<?php
$siteTitle = "Neuer Pfleger";
$siteCategory = "Pfleger";

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
    $pg_anrede = mysqli_real_escape_string($dbc, trim($_POST['pg_anrede']));
    $pg_vorname = mysqli_real_escape_string($dbc, trim($_POST['pg_vorname']));
    $pg_nachname = mysqli_real_escape_string($dbc, trim($_POST['pg_nachname']));

    //Geburtsdatum
    $pg_gebdatum = mysqli_real_escape_string($dbc, trim($_POST['pg_gebdatum']));

    //Kontakt
    $pg_tel1 = mysqli_real_escape_string($dbc, trim($_POST['pg_tel1']));
    $pg_tel2 = mysqli_real_escape_string($dbc, trim($_POST['pg_tel2']));
    $pg_mail = mysqli_real_escape_string($dbc, trim($_POST['pg_mail']));

    //Rechnungsadresse
    $pg_strasse	= mysqli_real_escape_string($dbc, trim($_POST['pg_strasse']));
    $pg_nr = mysqli_real_escape_string($dbc, trim($_POST['pg_nr']));
    $pg_stg	= mysqli_real_escape_string($dbc, trim($_POST['pg_stg']));
    $pg_stck = mysqli_real_escape_string($dbc, trim($_POST['pg_stck']));
    $pg_tuer = mysqli_real_escape_string($dbc, trim($_POST['pg_tuer']));
    $pg_plz = mysqli_real_escape_string($dbc, trim($_POST['pg_plz']));

   //art
    $pg_art = mysqli_real_escape_string($dbc, trim($_POST['pg_art']));

    //Prüfen, ob alle notwendigen Werte vorhanden sind
    if (!empty($pg_anrede) && !empty($pg_vorname) && !empty($pg_nachname) && !empty($pg_strasse) && !empty($pg_nr) && !empty($pg_plz))
    {
      // Sicher gehen, dass der Pg nicht schon existiert
      $query = "SELECT pg_id FROM pfleger WHERE pg_vorname = '$pg_vorname' AND pg_nachname = '$pg_nachname' AND pg_gebdatum = '$pg_gebdatum' LIMIT 1";
      $data = mysqli_query($dbc, $query)
        or die(errorlog($dbc,$query));

      //Wenn Pg nicht existiert:
      if (mysqli_num_rows($data) == 0)
      {
          //sichergehen, dass E-Mail Adresse korrektes Format hat, wenn angegeben
          if (($pg_mail != "" && filter_var($pg_mail, FILTER_VALIDATE_EMAIL)) || $pg_mail == "")
          {


              //in Pfleger Tabelle eintragen
            $query = "INSERT INTO pfleger " .
                     "(pg_anrede, pg_vorname, pg_nachname, pg_gebdatum, pg_tel1, pg_tel2, pg_mail, pg_art) " .
                     "VALUES" .
                     "('$pg_anrede', '$pg_vorname', '$pg_nachname', '$pg_gebdatum', '$pg_tel1', '$pg_tel2', '$pg_mail', '$pg_art')";

              mysqli_query($dbc, $query)
              or die(errorlog($dbc, $query));

            $pg_id = mysqli_insert_id($dbc);

            //in Adressen Tabelle eintragen



              $query = "INSERT INTO adressen " .
                  "(pg_id, ad_aktiv, ad_typ, ad_strasse, ad_nr, ad_stiege, ad_stock, ad_tuer, ad_plz) " .
                  "VALUES" .
                  "('$pg_id', '1', 'rb', '$pg_strasse', '$pg_nr', '$pg_stg', '$pg_stck', '$pg_tuer', '$pg_plz')";

              mysqli_query($dbc, $query)
              or die(errorlog($dbc, $query));

            //Pg Erstellung als Vermerk eintragen

            $query2 = "INSERT INTO vermerke (pg_id, ve_art, ve_flag, ve_text, us_id, ve_datum) " .
                      "VALUES ('$pg_id', 'System', 'n', 'Änderung: Pfleger hinzugefügt / User: " . $_SESSION['username'] . "', 0, NOW())";
            mysqli_query($dbc, $query2)
              or die(errorlog($dbc, $query2));


            // Dem User den Erfolg mitteilen
            echo '<div class="alert alert-success" role="alert">';
            echo 'Der neue Pfleger wurde erfolgreich angelegt';
            echo '</div>';
            echo '<p><a href="pgdetails.php?pg_id=' . $pg_id . '">Pflegerdetails</a></p>';
            echo '<p><a href="pgneu.php">Neuen Pfleger anlegen</a></p>';


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
          echo 'Es existiert bereits ein Pfleger mit diesen Personendaten!';
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
          <label for="pg_anrede" class="col-sm-4 col-form-label col-form-label-sm">Anrede:</label>
          <div class="col-sm-5">
            <select class="form-control form-control-sm" name="pg_anrede" id="pg_anrede" required>
              <option value="0">Anrede</option>
              <option <?php if(isset($_POST["pg_anrede"]) && $pg_anrede = "Herr") echo 'selected="selected"'; ?>>Herr</option>
              <option <?php if(isset($_POST["pg_anrede"]) && $pg_anrede = "Frau") echo 'selected="selected"'; ?>>Frau</option>
            </select>
          </div>
        </div>
        <div class="form-group row m-1">
          <label for="pg_vorname" class="col-sm-4 col-form-label col-form-label-sm">Vorname</label>
          <div class="col-sm-5">
            <input type="text" class="form-control form-control-sm" name="pg_vorname" id="pg_vorname" placeholder="Vorname" required <?php keeppostvalue('pg_vorname'); ?> />
          </div>
        </div>
        <div class="form-group row m-1">
          <label for="pg_nachname" class="col-sm-4 col-form-label col-form-label-sm">Nachname</label>
          <div class="col-sm-5">
            <input type="text" class="form-control form-control-sm" name="pg_nachname" id="pg_nachname" placeholder="Nachname" required  <?php keeppostvalue('pg_nachname'); ?>/>
          </div>
        </div>
        <div class="form-group row m-1">
          <label for="pg_gebdatum" class="col-sm-4 col-form-label col-form-label-sm">Geburtsdatum</label>
          <div class="col-sm-5">
            <input type="date" min="2000-01-01" max="2200-12-31" class="form-control form-control-sm" name="pg_gebdatum" id="pg_gebdatum" placeholder="TT.MM.JJJJ" required  <?php keeppostvalue('pg_gebdatum'); ?>/>
          </div>
        </div>
        
      </div>
      <div class="col">
        <div class="form-group row m-1">
          <label for="pg_tel1" class="col-sm-4 col-form-label col-form-label-sm">Festnetz:</label>
          <div class="col-sm-5">
            <input type="text" class="form-control form-control-sm" name="pg_tel1" id="pg_tel1" placeholder="+43 1 2345678" <?php keeppostvalue('pg_tel1'); ?> />
          </div>
        </div>
        <div class="form-group row m-1">
          <label for="pg_tel2" class="col-sm-4 col-form-label col-form-label-sm">Mobil:</label>
            <div class="col-sm-5">
                <input type="text" class="form-control form-control-sm"  name="pg_tel2" id="kd_tel2" placeholder="+43 660 2345678" <?php keeppostvalue('pg_tel2'); ?> />
            </div>
        </div>
        <div class="form-group row m-1">
          <label for="pg_mail" class="col-sm-4 col-form-label col-form-label-sm m-0">E-Mail:</label>
          <div class="col-sm-5">
            <input type="text" class="form-control form-control-sm" name="pg_mail" id="pg_mail" placeholder="mail@beispiel.at" <?php keeppostvalue('pg_mail'); ?> />
          </div>
        </div>
        <div class="form-group row m-1">
          <label class="col-sm-4 col-form-label col-form-label-sm">Art:</label>
          <div class="col-sm-5">
              <div class="form-check">
                  <input class="form-check-input" type="radio" name="pg_art" id="pg_art_24h" value="24h" <?php if(!isset($_POST["submit"]) || (isset($pg_art) && $pg_art == "24h")) echo "checked"; ?>>
                  <label class="form-check-label" for="pg_art_24h">
                      24h Pflege
                  </label>
              </div>
              <div class="form-check">
                  <input class="form-check-input" type="radio" name="pg_art" id="pg_art_sw" value="sw" <?php if(isset($pg_art) && $pg_art == "sw") echo "checked"; ?>>
                  <label class="form-check-label" for="pg_art_sw">
                      Stundenweise
                  </label>
              </div>
              <div class="form-check disabled">
                  <input class="form-check-input" type="radio" name="pg_art" id="pg_art_dgks" value="dgks" <?php if(isset($pg_art) && $pg_art == "dgks") echo "checked"; ?>>
                  <label class="form-check-label" for="pg_art_dgks">
                      DGKS/P
                  </label>
              </div>
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
          <input type="text" class="form-control form-control-sm" name="pg_strasse" id="pg_strasse" placeholder="Straße" required <?php keeppostvalue('pg_strasse'); ?> />
        </div>
        
      </div>
      <div class="col col-md-2">
        
        <div class="form-group row m-1">
          <label for="pg_nr" class="m-0">Nr:</label>
          <input type="text" class="form-control form-control-sm" name="pg_nr" id="pg_nr" placeholder="" required <?php keeppostvalue('pg_nr'); ?> />
        </div>
        
      </div>
      <div class="col col-md-1">
        
        <div class="form-group row m-1">
          <label for="pg_stg" class="m-0">Stg:</label>
          <input type="text" class="form-control form-control-sm" name="pg_stg"  id="pg_stg" placeholder="" <?php keeppostvalue('pg_stg'); ?> />
        </div>
        
      </div>
      <div class="col col-md-1">
        
        <div class="form-group row m-1">
          <label for="pg_stck" class="m-0">Stock:</label>
          <input type="text" class="form-control form-control-sm" name="pg_stck" id="pg_stck" placeholder="" <?php keeppostvalue('pg_stck'); ?> />
        </div>
        
      </div>
      <div class="col col-md-1">
        
        <div class="form-group row m-1">
          <label for="pg_tuer" class="m-0">Tür:</label>
          <input type="text" class="form-control form-control-sm" name="pg_tuer" id="pg_tuer" placeholder="" <?php keeppostvalue('pg_tuer'); ?> />
        </div>
        
      </div>
       <div class="col col-sm-2">
        
        <div class="form-group row m-1">
          <label for="pg_plz" class="m-0">PLZ:</label>
          <input type="text" size="4" class="form-control form-control-sm" name="pg_plz" id="pg_plz" placeholder="1234" required <?php keeppostvalue('pg_plz'); ?> />
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
