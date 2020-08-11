<?php
$siteTitle = "Neuer Sachwalter";
$siteCategory = "Sachwalter";

include_once('header.php');
include_once('nav.php');
?>

<!-- MAIN START -->
<?php

$dbc = mysqli_connect(DB_HOST,DB_USER,DB_PASSWORD, DB_NAME)
    or die(errrorlog($dbc,0));
mysqli_set_charset($dbc,"utf8");


if (isset($_POST["submit"]))
{
  //form handling
    //variablen für insert vorbereiten
    //Name
    $sw_anrede = mysqli_real_escape_string($dbc, trim($_POST['sw_anrede']));
    $sw_titel = mysqli_real_escape_string($dbc, trim($_POST['sw_titel']));
    $sw_vorname = mysqli_real_escape_string($dbc, trim($_POST['sw_vorname']));
    $sw_nachname = mysqli_real_escape_string($dbc, trim($_POST['sw_nachname']));

    //Geburtsdatum
    $sw_gebdatum = mysqli_real_escape_string($dbc, trim($_POST['sw_gebdatum']));

    //Kontakt
    $sw_tel1 = mysqli_real_escape_string($dbc, trim($_POST['sw_tel1']));
    $sw_tel2 = mysqli_real_escape_string($dbc, trim($_POST['sw_tel2']));
    $sw_mail = mysqli_real_escape_string($dbc, trim($_POST['sw_mail']));

    //Adresse
    $sw_strasse	= mysqli_real_escape_string($dbc, trim($_POST['sw_strasse']));
    $sw_nr = mysqli_real_escape_string($dbc, trim($_POST['sw_nr']));
    $sw_stg	= mysqli_real_escape_string($dbc, trim($_POST['sw_stg']));
    $sw_stck = mysqli_real_escape_string($dbc, trim($_POST['sw_stck']));
    $sw_tuer = mysqli_real_escape_string($dbc, trim($_POST['sw_tuer']));
    $sw_plz = mysqli_real_escape_string($dbc, trim($_POST['sw_plz']));

    //rg art
    $sw_rg = mysqli_real_escape_string($dbc, trim($_POST['sw_rg']));

    //Kunde
    $sw_kd = mysqli_real_escape_string($dbc, trim($_POST['sw_kd']));

    //Prüfen, ob alle notwendigen Werte vorhanden sind
    if (!empty($sw_anrede) && !empty($sw_vorname) && !empty($sw_nachname) && !empty($sw_strasse) && !empty($sw_nr) && !empty($sw_plz))
    {
      // Sicher gehen, dass der sw nicht schon existiert
      $query = "SELECT sw_id FROM sachwalter WHERE sw_vorname = '$sw_vorname' AND sw_nachname = '$sw_nachname' AND sw_gebdatum = '$sw_gebdatum' AND sw_typ = 'sw' LIMIT 1";
      $data = mysqli_query($dbc, $query)
        or die(errorlog($dbc,$query));

      //Wenn sw nicht existiert:
      if (mysqli_num_rows($data) == 0)
      {
          //sichergehen, dass E-Mail Adresse korrektes Format hat, wenn angegeben
          if (($sw_mail != "" && filter_var($sw_mail, FILTER_VALIDATE_EMAIL)) || $sw_mail == "")
          {


              //in Pfleger Tabelle eintragen
            $query = "INSERT INTO sachwalter " .
                     "(sw_anrede, sw_titel, sw_vorname, sw_nachname, sw_gebdatum, sw_tel1, sw_tel2, sw_mail, sw_rg_art, sw_typ) " .
                     "VALUES" .
                     "('$sw_anrede', '$sw_titel', '$sw_vorname', '$sw_nachname', '$sw_gebdatum', '$sw_tel1', '$sw_tel2', '$sw_mail', '$sw_rg', 'sw')";

              mysqli_query($dbc, $query)
              or die(errorlog($dbc, $query));

            $sw_id = mysqli_insert_id($dbc);

            //in Adressen Tabelle eintragen



              $query = "INSERT INTO adressen " .
                  "(sw_id, ad_aktiv, ad_typ, ad_strasse, ad_nr, ad_stiege, ad_stock, ad_tuer, ad_plz) " .
                  "VALUES" .
                  "('$sw_id', '1', 'rb', '$sw_strasse', '$sw_nr', '$sw_stg', '$sw_stck', '$sw_tuer', '$sw_plz')";

              mysqli_query($dbc, $query)
              or die(errorlog($dbc, $query));

            //sw Erstellung als Vermerk eintragen

            $query2 = "INSERT INTO vermerke (sw_id, ve_art, ve_flag, ve_text, us_id, ve_datum) " .
                      "VALUES ('$sw_id', 'System', 'n', 'Änderung: Sachwalter hinzugefügt / User: " . $_SESSION['username'] . "', 0, NOW())";
            mysqli_query($dbc, $query2)
              or die(errorlog($dbc, $query2));

            if($sw_kd != 0)
            {
                $query = "INSERT INTO angehoerige " .
                         "(sw_id, kd_id, ag_aktiv) " .
                         "VALUES" .
                         "('$sw_id', '$sw_kd', '1')";
                mysqli_query($dbc, $query)
                    or die(errorlog($dbc, $query));
            }


            // Dem User den Erfolg mitteilen
            echo '<div class="alert alert-success" role="alert">';
            echo 'Der neue Pfleger wurde erfolgreich angelegt';
            echo '</div>';
            echo '<p><a href="swdetails.php?sw_id=' . $sw_id . '">Sachwalterdetails</a></p>';
            echo '<p><a href="swneu.php">Neuen Sachwalter anlegen</a></p>';


            mysqli_close($dbc);
            exit();
          }
          else
          {
              //E-Mail Adresse ist im falschen Format
              echo '<div class="alert alert-warning" role="alert">';
              echo 'Bitte die E-Mail Adresse im Format "name@beispiel.at" angeben!';
              echo '</div>';
              $error = 1;
          }
      }
      else
      {
          // SW existiert bereits
          echo '<div class="alert alert-warning" role="alert">';
          echo 'Es existiert bereits ein Sachwalter mit diesen Personendaten!';
          echo '</div>';
          $error = 1;
      }
    }
    else
    {
        //Es wurden nicht alle notwendigen Felder ausgefüllt
        echo '<div class="alert alert-warning" role="alert">';
        echo 'Bitte alle Pflichtfelder ausfüllen!';
        echo '</div>';
        $error = 1;
    }

    mysqli_close($dbc);
  }

//Ausgabe des $_POST Arrays für debugzwecke
/*
  echo '<div class="container">';
  echo '<table>';
    foreach ($_POST as $key => $value)
    {
        echo "<tr>";
        echo "<td>";
        echo $key;
        echo "</td>";
        echo "<td>";
        echo $value;
        echo "</td>";
        echo "</tr>";
    }
  echo '</table></div>';
*/


if ((isset($error) && $error == 1) || !isset($error))
{
?>
<form action="<?php echo $_SERVER["PHP_SELF"] ?>" method="POST">
  <div class="container">
    <div class="row">
      <div class="col border border-primary">
      Allgemeine Daten
      </div>
    </div>
    <div class="row">
      <div class="col">
        
        <div class="form-group row m-1">
          <label for="sw_anrede" class="col-sm-4 col-form-label col-form-label-sm">Anrede:</label>
          <div class="col-sm-5">
            <select class="form-control form-control-sm" name="sw_anrede" id="sw_anrede" required>
              <option value="0">Anrede</option>
              <option <?php if(isset($_POST["sw_anrede"]) && $sw_anrede = "Herr") echo 'selected="selected"'; ?>>Herr</option>
              <option <?php if(isset($_POST["sw_anrede"]) && $sw_anrede = "Frau") echo 'selected="selected"'; ?>>Frau</option>
            </select>
          </div>
        </div>
          <div class="form-group row m-1">
              <label for="sw_titel" class="col-sm-4 col-form-label col-form-label-sm">Titel:</label>
              <div class="col-sm-5">
                  <select class="form-control form-control-sm" name="sw_titel" id="sw_titel" required>
                      <option value="0">Titel</option>
                      <option <?php if(isset($_POST["sw_titel"]) && $sw_titel = "Dr.") echo 'selected="selected"'; ?>>Dr.</option>
                      <option <?php if(isset($_POST["sw_titel"]) && $sw_titel = "Mag.") echo 'selected="selected"'; ?>>Mag.</option>
                  </select>
              </div>
          </div>
        <div class="form-group row m-1">
          <label for="sw_vorname" class="col-sm-4 col-form-label col-form-label-sm">Vorname</label>
          <div class="col-sm-5">
            <input type="text" class="form-control form-control-sm" name="sw_vorname" id="sw_vorname" placeholder="Vorname" required <?php keeppostvalue('sw_vorname'); ?> />
          </div>
        </div>
        <div class="form-group row m-1">
          <label for="sw_nachname" class="col-sm-4 col-form-label col-form-label-sm">Nachname</label>
          <div class="col-sm-5">
            <input type="text" class="form-control form-control-sm" name="sw_nachname" id="sw_nachname" placeholder="Nachname" required  <?php keeppostvalue('sw_nachname'); ?>/>
          </div>
        </div>
        <div class="form-group row m-1">
          <label for="sw_gebdatum" class="col-sm-4 col-form-label col-form-label-sm">Geburtsdatum</label>
          <div class="col-sm-5">
            <input type="date" min="2000-01-01" max="2200-12-31" class="form-control form-control-sm" name="sw_gebdatum" id="sw_gebdatum" placeholder="TT.MM.JJJJ" required  <?php keeppostvalue('sw_gebdatum'); ?>/>
          </div>
        </div>
        
      </div>
      <div class="col">
        <div class="form-group row m-1">
          <label for="sw_tel1" class="col-sm-4 col-form-label col-form-label-sm">Festnetz:</label>
          <div class="col-sm-5">
            <input type="text" class="form-control form-control-sm" name="sw_tel1" id="sw_tel1" placeholder="+43 1 2345678" <?php keeppostvalue('sw_tel1'); ?> />
          </div>
        </div>
        <div class="form-group row m-1">
          <label for="sw_tel2" class="col-sm-4 col-form-label col-form-label-sm">Mobil:</label>
            <div class="col-sm-5">
                <input type="text" class="form-control form-control-sm"  name="sw_tel2" id="kd_tel2" placeholder="+43 660 2345678" <?php keeppostvalue('sw_tel2'); ?> />
            </div>
        </div>
        <div class="form-group row m-1">
          <label for="sw_mail" class="col-sm-4 col-form-label col-form-label-sm m-0">E-Mail:</label>
          <div class="col-sm-5">
            <input type="text" class="form-control form-control-sm" name="sw_mail" id="sw_mail" placeholder="mail@beispiel.at" <?php keeppostvalue('sw_mail'); ?> />
          </div>
        </div>
          <div class="form-group row m-1">
              <label for="sw_rg" class="col-sm-4 col-form-label col-form-label-sm">Rechnung:</label>
              <div class="col-sm-5">
                  <select class="form-control form-control-sm" name="sw_rg" id="sw_rg" required>
                      <option value="m" <?php if(isset($_POST["sw_rg"]) && $sw_rg = "m") echo 'selected="selected"'; ?>>E-Mail</option>
                      <option value="p" <?php if(isset($_POST["sw_rg"]) && $sw_rg = "p") echo 'selected="selected"'; ?>>Post</option>
                  </select>
              </div>
          </div>
        
      </div>
    </div>
    
     <div class="row">
      <div class="col border border-primary mt-4">
      Adresse
      </div>
    </div>
    <div class="row" id="RgAdresseNeu">
      <div class="col col-sm-6 col-lg-4">
        <div class="form-group row m-1">
          <label for="kd_strasse" class="m-0">Straße:</label>
          <input type="text" class="form-control form-control-sm" name="sw_strasse" id="sw_strasse" placeholder="Straße" required <?php keeppostvalue('sw_strasse'); ?> />
        </div>
        
      </div>
      <div class="col col-md-2">
        
        <div class="form-group row m-1">
          <label for="sw_nr" class="m-0">Nr:</label>
          <input type="text" class="form-control form-control-sm" name="sw_nr" id="sw_nr" placeholder="" required <?php keeppostvalue('sw_nr'); ?> />
        </div>
        
      </div>
      <div class="col col-md-1">
        
        <div class="form-group row m-1">
          <label for="sw_stg" class="m-0">Stg:</label>
          <input type="text" class="form-control form-control-sm" name="sw_stg"  id="sw_stg" placeholder="" <?php keeppostvalue('sw_stg'); ?> />
        </div>
        
      </div>
      <div class="col col-md-1">
        
        <div class="form-group row m-1">
          <label for="sw_stck" class="m-0">Stock:</label>
          <input type="text" class="form-control form-control-sm" name="sw_stck" id="sw_stck" placeholder="" <?php keeppostvalue('sw_stck'); ?> />
        </div>
        
      </div>
      <div class="col col-md-1">
        
        <div class="form-group row m-1">
          <label for="sw_tuer" class="m-0">Tür:</label>
          <input type="text" class="form-control form-control-sm" name="sw_tuer" id="sw_tuer" placeholder="" <?php keeppostvalue('sw_tuer'); ?> />
        </div>
        
      </div>
       <div class="col col-sm-2">
        
        <div class="form-group row m-1">
          <label for="sw_plz" class="m-0">PLZ:</label>
          <input type="text" size="4" class="form-control form-control-sm" name="sw_plz" id="sw_plz" placeholder="1234" required <?php keeppostvalue('sw_plz'); ?> />
        </div>
        
      </div>

    </div>
      <div class="row">
          <div class="col border border-primary mt-4">
              Kunde
          </div>
      </div>
      <div class="row">
          <div class="col-sm-5">
              <div class="form-group row m-1">
              <select class="form-control form-control-sm" name="sw_kd" id="sw_kd">
                  <option value="0">später angeben</option>
                  <?php
                  $kd_query = "SELECT kd_id, kd_anrede, kd_vorname, kd_nachname FROM kunde WHERE kd_ableben = '0' AND (kd_ende IS NULL OR kd_ende > NOW()) ORDER BY kd_nachname";
                  $kd_data = mysqli_query($dbc, $kd_query)
                  or die(errorlog($dbc,$kd_query));
                  while ($kd_row = mysqli_fetch_array($kd_data))
                  {
                      $kd_query2 = "SELECT kd_id FROM kunde WHERE kd_id = '" . $kd_row["kd_id"] . "'";
                      $kd_data2 = mysqli_query($dbc, $kd_query2)
                      or die(errorlog($dbc, $query));

                      echo '<option ';
                      if(mysqli_num_rows($kd_data2) >= 3)
                      {
                          echo "disabled ";
                      }
                      elseif(isset($_POST["sw_kd"]) && $_POST["sw_kd"] == $kd_row["kd_id"])
                      {
                          echo "selected='selected' ";
                      }
                      echo 'value="' . $kd_row['kd_id'] . '">' . $kd_row["kd_anrede"] . ' ' . $kd_row['kd_nachname'] . ' ' . $kd_row['kd_vorname'] . '</option>';
                  }
                  ?>
              </select>
              </div>
          </div>
      </div>


    
    <input type="submit" name="submit" id="submit" value="Datensatz anlegen" class="btn btn-primary mt-5" />
            
  </div>
</form>
<?php
}
?>
<!-- MAIN END -->

<?php
include_once('footer.php');
?>
