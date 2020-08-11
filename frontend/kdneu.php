<?php
$siteTitle = "Neuer Kunde";
$siteCategory = "Kunden";

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
    $kd_anrede = mysqli_real_escape_string($dbc, trim($_POST['kd_anrede']));
    $kd_titel = mysqli_real_escape_string($dbc, trim($_POST['kd_titel']));
    $kd_vorname = mysqli_real_escape_string($dbc, trim($_POST['kd_vorname']));
    $kd_nachname = mysqli_real_escape_string($dbc, trim($_POST['kd_nachname']));

    //Geburtsdatum
    $kd_gebdatum = mysqli_real_escape_string($dbc, trim($_POST['kd_gebdatum']));

    //Sachwalter-ID
    $kd_sachwalter = mysqli_real_escape_string($dbc, trim($_POST['kd_sachwalter']));

    //Kontakt
    $kd_tel1 = mysqli_real_escape_string($dbc, trim($_POST['kd_tel1']));
    $kd_tel2 = mysqli_real_escape_string($dbc, trim($_POST['kd_tel2']));
    $kd_mail = mysqli_real_escape_string($dbc, trim($_POST['kd_mail']));

    //Zustellung p oder m
    $kd_rg = mysqli_real_escape_string($dbc, trim($_POST['kd_rg']));

    //Angehörge
    $kd_ag1 = mysqli_real_escape_string($dbc, trim($_POST['kd_ag1']));
    $kd_ag2 = mysqli_real_escape_string($dbc, trim($_POST['kd_ag2']));
    $kd_ag3 = mysqli_real_escape_string($dbc, trim($_POST['kd_ag3']));

    //Rechnungsadresse
    $kd_strasse	= mysqli_real_escape_string($dbc, trim($_POST['kd_strasse']));
    $kd_nr = mysqli_real_escape_string($dbc, trim($_POST['kd_nr']));
    $kd_stg	= mysqli_real_escape_string($dbc, trim($_POST['kd_stg']));
    $kd_stck = mysqli_real_escape_string($dbc, trim($_POST['kd_stck']));
    $kd_tuer = mysqli_real_escape_string($dbc, trim($_POST['kd_tuer']));
    $kd_plz = mysqli_real_escape_string($dbc, trim($_POST['kd_plz']));

    //Betreuungsadresse
    if(isset($_POST["kd_RgIsNB"]))
    {
        $kd_b_strasse	= mysqli_real_escape_string($dbc, trim($_POST['kd_b_strasse']));
        $kd_b_nr = mysqli_real_escape_string($dbc, trim($_POST['kd_b_nr']));
        $kd_b_stg	= mysqli_real_escape_string($dbc, trim($_POST['kd_b_stg']));
        $kd_b_stck = mysqli_real_escape_string($dbc, trim($_POST['kd_b_stck']));
        $kd_b_tuer = mysqli_real_escape_string($dbc, trim($_POST['kd_b_tuer']));
        $kd_b_plz = mysqli_real_escape_string($dbc, trim($_POST['kd_b_plz']));
    }

    //Anmerkung
    $kd_anmerkung = mysqli_real_escape_string($dbc, trim($_POST['kd_anmerkung']));

    //Daten außerhalb Formular
    $kd_ableben = 0;


    //Prüfen, ob alle notwendigen Werte vorhanden sind
    if (!empty($kd_anrede) && !empty($kd_vorname) && !empty($kd_nachname) && !empty($kd_strasse) && !empty($kd_nr) && !empty($kd_plz) &&
        ((isset($kd_b_strasse) && !empty($kd_b_strasse) && !empty($kd_b_nr) && !empty($kd_b_plz)) || (!isset($kd_b_strasse))))
    {
      // Sicher gehen, dass der Kd nicht schon existiert
        mysqli_begin_transaction($dbc);
      $query = "SELECT kd_id FROM kunde WHERE kd_vorname = '$kd_vorname' AND kd_nachname = '$kd_nachname' AND kd_gebdatum = '$kd_gebdatum' LIMIT 1";
      $data = mysqli_query($dbc, $query)
        or die(errorlog($dbc,$query));

      //Wenn Kd nicht existiert:
      if (mysqli_num_rows($data) == 0)
      {
          //sichergehen, dass E-Mail Adresse korrektes Format hat, wenn angegeben
          if (($kd_mail != "" && filter_var($kd_mail, FILTER_VALIDATE_EMAIL)) || $kd_mail == "")
          {


              //in Kunde Tabelle eintragen
            $query = "INSERT INTO kunde " .
                     "(kd_anrede, kd_titel, kd_vorname, kd_nachname, kd_gebdatum, kd_tel1, kd_tel2, kd_mail, kd_rg_art, kd_ableben) " .
                     "VALUES" .
                     "('$kd_anrede', '$kd_titel', '$kd_vorname', '$kd_nachname', '$kd_gebdatum', '$kd_tel1', '$kd_tel2', '$kd_mail', '$kd_rg', '$kd_ableben')";

              mysqli_query($dbc, $query)
              or die(errorlog($dbc, $query));

            $kd_id = mysqli_insert_id($dbc);

            //in SW Tabelle eintragen
            if($kd_sachwalter != 0)
            {
                $query = "INSERT INTO angehoerige " .
                    "(sw_id, kd_id, ag_aktiv) " .
                    "VALUES('$kd_sachwalter', '$kd_id', '1')";
                mysqli_query($dbc,$query)
                    or die(errorlog($dbc,$query));
            }

              if($kd_ag1 != 0)
              {
                  $query = "INSERT INTO angehoerige " .
                      "(sw_id, kd_id, ag_aktiv) " .
                      "VALUES($kd_ag1, $kd_id, 1)";
                  mysqli_query($dbc,$query)
                  or die(errorlog($dbc,$query));
              }

              if($kd_ag2 != 0)
              {
                  $query = "INSERT INTO angehoerige " .
                      "(sw_id, kd_id, ag_aktiv) " .
                      "VALUES($kd_ag2, $kd_id, 1)";
                  mysqli_query($dbc,$query)
                  or die(errorlog($dbc,$query));
              }

              if($kd_ag3 != 0)
              {
                  $query = "INSERT INTO angehoerige " .
                      "(sw_id, kd_id, ag_aktiv) " .
                      "VALUES($kd_ag3, $kd_id, 1)";
                  mysqli_query($dbc,$query)
                  or die(errorlog($dbc,$query));
              }

            //in Adressen Tabelle eintragen

              if (isset($kd_b_strasse)){ $ad_typ1 = "r";} else {$ad_typ1 = "rb";}

              $query = "INSERT INTO adressen " .
                  "(kd_id, ad_aktiv, ad_typ, ad_strasse, ad_nr, ad_stiege, ad_stock, ad_tuer, ad_plz) " .
                  "VALUES" .
                  "('$kd_id', '1', '$ad_typ1', '$kd_strasse', '$kd_nr', '$kd_stg', '$kd_stck', '$kd_tuer', '$kd_plz')";

              mysqli_query($dbc, $query)
              or die(errorlog($dbc, $query));

              if($ad_typ1 == "r")
              {
                  $query = "INSERT INTO adressen " .
                      "(kd_id, ad_aktiv, ad_typ, ad_strasse, ad_nr, ad_stiege, ad_stock, ad_tuer, ad_plz) " .
                      "VALUES" .
                      "('$kd_id', '1', 'b', '$kd_b_strasse', '$kd_b_nr', '$kd_b_stg', '$kd_b_stck', '$kd_b_tuer', '$kd_b_plz')";

                  mysqli_query($dbc, $query)
                  or die(errorlog($dbc, $query));
              }

            //Kd Erstellung als Vermerk eintragen
            //$_SESSION['username'] statt USERNAME
            $query2 = "INSERT INTO vermerke (kd_id, ve_art, ve_flag, ve_text, us_id, ve_datum) " .
                      "VALUES ('$kd_id', 'System', 'n', 'Änderung: Kunde hinzugefügt / User: " . $_SESSION['username'] . "', 0, NOW())";
            mysqli_query($dbc, $query2)
              or die(errorlog($dbc, $query2));

            //Wenn Anmerkung eingetragen, ebenso in Vermerke:
            if($kd_anmerkung != "")
            {
                $query3 = "INSERT INTO vermerke (kd_id, ve_art, ve_flag, ve_text, us_id, ve_datum) " .
                          "VALUES ('$kd_id', 'Information', '1', '$kd_anmerkung', 0, NOW())";
                mysqli_query($dbc, $query3)
                  or die(errorlog($dbc, $query3));
            }

            mysqli_commit($dbc);

            // Dem User den Erfolg mitteilen
            echo '<div class="alert alert-success" role="alert">';
            echo 'Der neue Kunde wurde erfolgreich angelegt';
            echo '</div>';
            echo '<p><a href="kddetails.php?kd_id=' . $kd_id . '">Kundendetails</a></p>';
            echo '<p><a href="kdneu.php">Neuen Kunden anlegen</a></p>';


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
          // Kunde existiert bereits
          echo '<div class="alert alert-warning" role="alert">';
          echo 'Es existiert bereits ein Kunde mit diesen Personendaten!';
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
          <label for="kd_anrede" class="col-sm-4 col-form-label col-form-label-sm">Anrede:</label>
          <div class="col-sm-5">
            <select class="form-control form-control-sm" name="kd_anrede" id="kd_anrede" required>
              <option value="0">Anrede</option>
              <option>Herr</option>
              <option>Frau</option>
            </select>
          </div>
        </div>
        <div class="form-group row m-1">
          <label for="kd_titel" class="col-sm-4 col-form-label col-form-label-sm">Titel:</label>
          <div class="col-sm-5">
            <select class="form-control form-control-sm" name="kd_titel" id="kd_titel">
              <option value="0">Titel</option>
              <option>Dr.</option>
              <option>Mag.</option>
            </select>
          </div>
        </div>
        <div class="form-group row m-1">
          <label for="kd_vorname" class="col-sm-4 col-form-label col-form-label-sm">Vorname</label>
          <div class="col-sm-5">
            <input type="text" class="form-control form-control-sm" name="kd_vorname" id="kd_vorname" placeholder="Vorname" required />
          </div>
        </div>
        <div class="form-group row m-1">
          <label for="kd_nachname" class="col-sm-4 col-form-label col-form-label-sm">Nachname</label>
          <div class="col-sm-5">
            <input type="text" class="form-control form-control-sm" name="kd_nachname" id="kd_nachname" placeholder="Nachname" required />
          </div>
        </div>
        <div class="form-group row m-1">
          <label for="kd_gebdatum" class="col-sm-4 col-form-label col-form-label-sm">Geburtsdatum</label>
          <div class="col-sm-5">
            <input type="date" min="2000-01-01" max="2200-12-31" class="form-control form-control-sm" name="kd_gebdatum" id="kd_gebdatum" placeholder="TT.MM.JJJJ" required />
          </div>
        </div>
        
      </div>
      <div class="col">


        <div class="form-group row m-1">
          <label for="kd_sachwalter" class="col-sm-4 col-form-label col-form-label-sm">Sachwalter:</label>
          <div class="col-sm-5">
            <select class="form-control form-control-sm" name="kd_sachwalter" id="kd_sachwalter">
              <option value="0">Keiner</option>
              <?php
               
                $sw_dbc = mysqli_connect(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME)
                        or die(errorlog($sw_dbc,""));
              mysqli_set_charset($sw_query,"utf8");
                    $sw_query = "SELECT sw_id, sw_nachname, sw_vorname FROM sachwalter WHERE sw_typ = 'sw' ORDER BY sw_nachname ASC";
                    $sw_data = mysqli_query($sw_dbc, $sw_query)
                        or die(errorlog($sw_dbc,$sw_query));
                        
                while ($sw_row = mysqli_fetch_array($sw_data)) 
                    {
                        echo '<option ';
                        //if ($_POST["kd_sw_id"] == $sw_row['sw_id']) echo "selected='selected' ";
                        echo 'value="' . $sw_row['sw_id'] . '">' . $sw_row['sw_nachname'] . ' ' . $sw_row['sw_vorname'] . '</option>';  
                    }
               
               
              ?>
            </select>
          </div>
        </div>
        <div class="form-group row m-1">
          <label for="kd_tel1" class="col-sm-4 col-form-label col-form-label-sm">Festnetz:</label>
          <div class="col-sm-5">
            <input type="text" class="form-control form-control-sm" name="kd_tel1" id="kd_tel1" placeholder="+43 1 2345678" />
          </div>
        </div>
        <div class="form-group row m-1">
          <label for="kd_tel2" class="col-sm-4 col-form-label col-form-label-sm">Mobil:</label>
            <div class="col-sm-5">
                <input type="text" class="form-control form-control-sm"  name="kd_tel2" id="kd_tel2" placeholder="+43 660 2345678" />
            </div>
        </div>
        <div class="form-group row m-1">
          <label for="kd_mail" class="col-sm-4 col-form-label col-form-label-sm m-0">E-Mail:</label>
          <div class="col-sm-5">
            <input type="text" class="form-control form-control-sm" name="kd_mail" id="kd_mail" placeholder="mail@beispiel.at" />
          </div>
        </div>
        <div class="form-group row m-1">
          <label for="kd_rg" class="col-sm-4 col-form-label col-form-label-sm">Rechnung:</label>
          <div class="col-sm-5">
            <select class="form-control form-control-sm" name="kd_rg" id="kd_rg">
              <option value="m">E-Mail</option>
              <option value="p">Post</option>
            </select>
          </div>
        </div>
        
      </div>
    </div>
    
     <div class="row">
      <div class="col border border-primary mt-4">
      Angehörige
      </div>
    </div>
    
    <div class="row">
      <div class="col">
      
         <div class="form-group row m-1">
          <label for="kd_ag1" class="col col-form-label col-form-label-sm">Angehöriger 1:</label>
          <div class="col-sm-5">
            <select class="form-control form-control-sm" name="kd_ag1" id="kd_ag1" onchange="toggleField('kd_ag1','kd_ag2_div')">
              <option value="0">keiner</option>
              <option value="3">irgendwas</option>
              <?php
               
                $ag_dbc = mysqli_connect(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME)
                        or die(errorlog("",""));
              mysqli_set_charset($ag_dbc,"utf8");
                    $ag_query = "SELECT sw_id, sw_nachname, sw_vorname FROM sachwalter WHERE sw_typ = 'ag' ORDER BY sw_nachname ASC";
                    $ag_data = mysqli_query($ag_dbc, $ag_query)
                        or die(errorlog($ag_dbc,$ag_query));
                        
                while ($ag_row = mysqli_fetch_array($ag_data)) 
                    {
                        echo '<option ';
                        //if ($_POST["kd_ag_id"] == $ag_row['ag_id']) echo "selected='selected' ";
                        echo 'value="' . $ag_row['sw_id'] . '">' . $ag_row['sw_nachname'] . ' ' . $ag_row['sw_vorname'] . '</option>';  
                    }
               
               
              ?>
            </select>
          </div>
        </div>
        <div class="form-group row m-1" id="kd_ag2_div" style="display: none;">
          <label for="kd_ag2" class="col col-form-label col-form-label-sm">Angehöriger 2:</label>
          <div class="col-sm-5">
            <select class="form-control form-control-sm" name="kd_ag2" id="kd_ag2" onchange="toggleField('kd_ag2','kd_ag3_div')">
              <option value="0">keiner</option>
               <?php
              $ag_data = mysqli_query($ag_dbc, $ag_query)
                        or die(errorlog($ag_dbc,$ag_query));
             while ($ag_row = mysqli_fetch_array($ag_data)) 
                    {
                        echo '<option ';
                        //if ($_POST["kd_ag_id"] == $ag_row['ag_id']) echo "selected='selected' ";
                        echo 'value="' . $ag_row['sw_id'] . '">' . $ag_row['sw_nachname'] . ' ' . $ag_row['sw_vorname'] . '</option>';  
                    }
              ?>
            </select>
          </div>
        </div>
        <div class="form-group row m-1" id="kd_ag3_div" style="display: none;">
          <label for="kd_ag3" class="col col-form-label col-form-label-sm">Angehöriger 3:</label>
          <div class="col-sm-5">
            <select class="form-control form-control-sm" name="kd_ag3" id="kd_ag3">
              <option value="0">keiner</option>
              <?php
              $ag_data = mysqli_query($ag_dbc, $ag_query)
                        or die(errorlog($ag_dbc,$ag_query));
             while ($ag_row = mysqli_fetch_array($ag_data)) 
                    {
                        echo '<option ';
                        //if ($_POST["kd_ag_id"] == $ag_row['ag_id']) echo "selected='selected' ";
                        echo 'value="' . $ag_row['sw_id'] . '">' . $ag_row['sw_nachname'] . ' ' . $ag_row['sw_vorname'] . '</option>';  
                    }
              ?>
            </select>
          </div>
        </div>
        
      </div>
    </div>
    
     <div class="row">
      <div class="col border border-primary mt-4">
      Rechnungsadresse 
      </div>
    </div>
    <div class="row" id="RgAdresseNeu">
        
      <div class="col col-sm-6 col-lg-4">
        
        <div class="form-group row m-1">
          <label for="kd_strasse" class="m-0">Straße:</label>
          <input type="text" class="form-control form-control-sm" name="kd_strasse" id="kd_strasse" placeholder="Straße" required />
        </div>
        
      </div>
      <div class="col col-md-2">
        
        <div class="form-group row m-1">
          <label for="kd_nr" class="m-0">Nr:</label>
          <input type="text" class="form-control form-control-sm" name="kd_nr" id="kd_nr" placeholder="" required />
        </div>
        
      </div>
      <div class="col col-md-1">
        
        <div class="form-group row m-1">
          <label for="kd_stg" class="m-0">Stg:</label>
          <input type="text" class="form-control form-control-sm" name="kd_stg"  id="kd_stg" placeholder="" />
        </div>
        
      </div>
      <div class="col col-md-1">
        
        <div class="form-group row m-1">
          <label for="kd_stck" class="m-0">Stock:</label>
          <input type="text" class="form-control form-control-sm" name="kd_stck" id="kd_stck" placeholder="" />
        </div>
        
      </div>
      <div class="col col-md-1">
        
        <div class="form-group row m-1">
          <label for="kd_tuer" class="m-0">Tür:</label>
          <input type="text" class="form-control form-control-sm" name="kd_tuer" id="kd_tuer" placeholder="" />
        </div>
        
      </div>
       <div class="col col-sm-2">
        
        <div class="form-group row m-1">
          <label for="kd_plz" class="m-0">PLZ:</label>
          <input type="text" size="4" class="form-control form-control-sm" name="kd_plz" id="kd_plz" placeholder="1234" required />
        </div>
        
      </div>

    </div>
    
    <div class="row">
      <div class="col border border-primary mt-4">
      Betreuungsadresse (wenn von Rechnungsadresse abweichend)
      </div>
    </div>
    <div class="col-md-10 mt-1">
            <div class="form-check">
              <input class="form-check-input" type="checkbox" value="1" name="kd_RgIsNB" id="kd_RgIsNB" onclick="toggleField('kd_RgIsNB','BAdresseNeu')" />
              <label class="form-check-label" for="kd_RgIsNB">
                Betreuungsadresse abweichend von Rechnungsadresse
              </label>
            </div>
          </div>
    <div class="row" id="BAdresseNeu" style="display: none;">
      <div class="col">
        
        <div class="row">
      <div class="col col-sm-6 col-lg-4">
        
        <div class="form-group row m-1">
          <label for="kd_b_strasse" class="m-0">Straße:</label>
          <input type="text" class="form-control form-control-sm" name="kd_b_strasse" id="kd_b_strasse" placeholder="Straße" />
        </div>
        
      </div>
      <div class="col col-md-2">
        
        <div class="form-group row m-1">
          <label for="kd_b_nr" class="m-0">Nr:</label>
          <input type="text" class="form-control form-control-sm" name="kd_b_nr" id="kd_b_nr" placeholder="" />
        </div>
        
      </div>
      <div class="col col-md-1">
        
        <div class="form-group row m-1">
          <label for="kd_b_stg" class="m-0">Stg:</label>
          <input type="text" class="form-control form-control-sm" name="kd_b_stg" id="kd_b_stg" placeholder="" />
        </div>
        
      </div>
      <div class="col col-md-1">
        
        <div class="form-group row m-1">
          <label for="kd_b_stck" class="m-0">Stock:</label>
          <input type="text" class="form-control form-control-sm" name="kd_b_stck" id="kd_b_stck" placeholder="" />
        </div>
        
      </div>
      <div class="col col-md-1">
        
        <div class="form-group row m-1">
          <label for="kd_b_tuer" class="m-0">Tür:</label>
          <input type="text" class="form-control form-control-sm" name="kd_b_tuer" id="kd_b_tuer" placeholder="" />
        </div>
        
      </div>
       <div class="col col-sm-2">
        
        <div class="form-group row m-1">
          <label for="kd_b_plz" class="m-0">PLZ:</label>
          <input type="text" size="4" class="form-control form-control-sm" name="kd_b_plz" id="kd_b_plz" placeholder="" />
        </div>
        
      </div>
    </div>
        
      </div>
    </div>
    
    <div class="row">
      <div class="col border border-primary mt-4">
      Wichtige Anmerkung
      </div>
    </div>
    
    <div class="row">
      <div class="col">
        
        <div class="row">
          <div class="col">
          
            <div class="form-group row">
              <label for="anmerkung" class="m-0">Anmerkung:</label>
              <input type="text" class="form-control form-control-sm" name="kd_anmerkung" id="kd_anmerkung" placeholder="Anmerkung" />
            </div>
          
          </div>
        </div>
        
      </div>
    </div>
    
    
    <input type="submit" name="submit" id="submit" value="Datensatz anlegen" class="btn btn-primary" />
            
  </div>
</form>
<?php
}
?>
<!-- MAIN END -->

<?php
include_once('footer.php');
?>
