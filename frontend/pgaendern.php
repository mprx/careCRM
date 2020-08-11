<?php
$siteTitle = "Pfleger ändern";
$siteCategory = "Pfleger";

include_once('header.php');
include_once('nav.php');
?>

<!-- MAIN START -->
<?php

$dbc = mysqli_connect(DB_HOST,DB_USER,DB_PASSWORD, DB_NAME)
    or die(errrorlog($dbc,0));
mysqli_set_charset($dbc,"utf8");

//pg id definieren
if (isset($_GET["pg_id"]))
{
    $pg_id = mysqli_real_escape_string($dbc, trim($_GET['pg_id']));
}
else
{
    $pg_id = 0;
}

//Wenn kein GET gesetzt ist, pg_id auf 0 setzen und error anzeigen (unten im file)
if ($pg_id == "")
{
    $pg_id = 0;
    $error = 1;
}

if(!isset($error)) {
    $query = "SELECT * FROM pfleger WHERE pg_id = '$pg_id'";
    $data = mysqli_query($dbc, $query)
    or die(errorlog($dbc, $query));
    $row = mysqli_fetch_array($data);

    //Name
    $pg_anrede = $row["pg_anrede"];
    $pg_vorname = $row["pg_vorname"];
    $pg_nachname = $row["pg_nachname"];

    //Geburtsdatum
    $pg_gebdatum = $row["pg_gebdatum"];

    //Kontakt
    $pg_tel1 = $row["pg_tel1"];
    $pg_tel2 = $row["pg_tel2"];
    $pg_mail = $row["pg_mail"];

    $pg_art = $row["pg_art"];

    //Daten
    $pg_beginn = $row["pg_beginn"];
    $pg_ende = $row["pg_ende"];
    $pg_aktiv = $row["pg_aktiv"];

    //Adresse
    $query2 = "SELECT ad_id, ad_typ, ad_strasse, ad_nr, ad_stiege, ad_stock, ad_tuer, ad_plz FROM adressen WHERE pg_id = '$pg_id' AND ad_aktiv = '1' AND ad_typ IN('r', 'rb')";
    $data2 = mysqli_query($dbc, $query2)
    or die(errorlog($dbc,$query2));
    $row2 = mysqli_fetch_array($data2);

    $pg_ad = $row2["ad_id"];
    $pg_strasse	= $row2["ad_strasse"];
    $pg_nr = $row2["ad_nr"];
    $pg_stg	= $row2["ad_stiege"];
    $pg_stck = $row2["ad_stock"];
    $pg_tuer = $row2["ad_tuer"];
    $pg_plz = $row2["ad_plz"];

}

if (isset($_POST["submit"]))
{
  //form handling
    //variablen für insert vorbereiten
    //Name
    $pg_anrede_neu = mysqli_real_escape_string($dbc, trim($_POST['pg_anrede']));
    $pg_vorname_neu = mysqli_real_escape_string($dbc, trim($_POST['pg_vorname']));
    $pg_nachname_neu = mysqli_real_escape_string($dbc, trim($_POST['pg_nachname']));

    //Geburtsdatum
    $pg_gebdatum_neu = mysqli_real_escape_string($dbc, trim($_POST['pg_gebdatum']));

    //Kontakt
    $pg_tel1_neu = mysqli_real_escape_string($dbc, trim($_POST['pg_tel1']));
    $pg_tel2_neu = mysqli_real_escape_string($dbc, trim($_POST['pg_tel2']));
    $pg_mail_neu = mysqli_real_escape_string($dbc, trim($_POST['pg_mail']));

    //Rechnungsadresse
    $pg_strasse_neu	= mysqli_real_escape_string($dbc, trim($_POST['pg_strasse']));
    $pg_nr_neu = mysqli_real_escape_string($dbc, trim($_POST['pg_nr']));
    $pg_stg_neu	= mysqli_real_escape_string($dbc, trim($_POST['pg_stg']));
    $pg_stck_neu = mysqli_real_escape_string($dbc, trim($_POST['pg_stck']));
    $pg_tuer_neu = mysqli_real_escape_string($dbc, trim($_POST['pg_tuer']));
    $pg_plz_neu = mysqli_real_escape_string($dbc, trim($_POST['pg_plz']));

    //art
    $pg_art_neu = mysqli_real_escape_string($dbc, trim($_POST['pg_art']));

    //Daten
    $pg_beginn_neu = "'" . mysqli_real_escape_string($dbc, trim($_POST['pg_beginn'])) . "'";
    $pg_ende_neu = "'" . mysqli_real_escape_string($dbc, trim($_POST['pg_ende'])) . "'";
    if($pg_beginn_neu == "''") $pg_beginn_neu = "NULL";
    if($pg_ende_neu == "''") $pg_ende_neu = "NULL";

    $pg_aktiv_neu = 0;
    if(isset($_POST["pg_aktiv"])) $pg_aktiv_neu = 1;

    //adresse
    if(isset($_POST["pg_RgAendern"]))
    {
        $pg_strasse_neu	= mysqli_real_escape_string($dbc, trim($_POST['pg_strasse']));
        $pg_nr_neu = mysqli_real_escape_string($dbc, trim($_POST['pg_nr']));
        $pg_stg_neu	= mysqli_real_escape_string($dbc, trim($_POST['pg_stg']));
        $pg_stck_neu = mysqli_real_escape_string($dbc, trim($_POST['pg_stck']));
        $pg_tuer_neu = mysqli_real_escape_string($dbc, trim($_POST['pg_tuer']));
        $pg_plz_neu = mysqli_real_escape_string($dbc, trim($_POST['pg_plz']));
    }

    //Prüfen, ob alle notwendigen Werte vorhanden sind
    if (!empty($pg_anrede_neu) && !empty($pg_vorname_neu) && !empty($pg_nachname_neu) && ((isset($_POST["pg_RgAendern"]) && !empty($pg_strasse_neu) && !empty($pg_nr_neu) && !empty($pg_plz_neu)) || !isset($_POST["pg_RgAendern"])))
    {
          //sichergehen, dass E-Mail Adresse korrektes Format hat, wenn angegeben
          if (($pg_mail_neu != "" && filter_var($pg_mail_neu, FILTER_VALIDATE_EMAIL)) || $pg_mail_neu == "")
          {

              //in Pfleger Tabelle eintragen
              $query = "UPDATE pfleger SET " .
                  "pg_anrede = '$pg_anrede_neu', ".
                  "pg_vorname = '$pg_vorname_neu', " .
                  "pg_nachname = '$pg_nachname_neu', " .
                  "pg_gebdatum = '$pg_gebdatum_neu', " .
                  "pg_tel1 = '$pg_tel1_neu', " .
                  "pg_tel2 = '$pg_tel2_neu', " .
                  "pg_mail = '$pg_mail_neu', " .
                  "pg_art = '$pg_art_neu', " .
                  "pg_beginn = $pg_beginn_neu, " .
                  "pg_ende = $pg_ende_neu, " .
                  "pg_aktiv = '$pg_aktiv_neu' " .
                  "WHERE pg_id = '$pg_id'";
              mysqli_query($dbc, $query)
              or die(errorlog($dbc, $query));

              //in Adressen Tabelle eintragen
              if(isset($_POST["pg_RgAendern"]))
              {

                  $query = "INSERT INTO adressen " .
                      "(pg_id, ad_aktiv, ad_typ, ad_strasse, ad_nr, ad_stiege, ad_stock, ad_tuer, ad_plz) " .
                      "VALUES" .
                      "('$pg_id', '1', 'rb', '$pg_strasse_neu', '$pg_nr_neu', '$pg_stg_neu', '$pg_stck_neu', '$pg_tuer_neu', '$pg_plz_neu')";

                  mysqli_query($dbc, $query)
                  or die(errorlog($dbc, $query));

                  $query2 = "UPDATE adressen SET " .
                      "ad_aktiv = '0' WHERE ad_id = '$pg_ad'";

                  mysqli_query($dbc, $query2)
                  or die(errorlog($dbc, $query2));

              }
              //Pg Änderung als Vermerk eintragen
              $query2 = "INSERT INTO vermerke (pg_id, ve_art, ve_flag, ve_text, us_id, ve_datum) " .
                  "VALUES ('$pg_id', 'System', 'n', 'Änderung: Pfleger geändert / User: " . $_SESSION['username'] . "', 0, NOW())";
              mysqli_query($dbc, $query2)
              or die(errorlog($dbc, $query2));

            // Dem User den Erfolg mitteilen
            echo '<div class="alert alert-success" role="alert">';
            echo 'Der Pfleger wurde erfolgreich geändert';
            echo '</div>';
            echo '<p><a href="pgdetails.php?pg_id=' . $pg_id . '">Pflegerdetails</a></p>';


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
<form action="<?php echo $_SERVER["PHP_SELF"] ?>?pg_id=<?php echo $pg_id; ?>" method="POST">
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
                <option <?php if((isset($_POST["submit"]) && $pg_anrede_neu == "Herr") || (!isset($_POST["submit"]) && $pg_anrede == "Herr")) echo 'selected="selected"';?>>Herr</option>
                <option <?php if((isset($_POST["submit"]) && $pg_anrede_neu == "Frau") || (!isset($_POST["submit"]) && $pg_anrede == "Frau")) echo 'selected="selected"';?>>Frau</option>
            </select>
          </div>
        </div>
        <div class="form-group row m-1">
          <label for="pg_vorname" class="col-sm-4 col-form-label col-form-label-sm">Vorname</label>
          <div class="col-sm-5">
            <input type="text" class="form-control form-control-sm" name="pg_vorname" id="pg_vorname" placeholder="Vorname" required <?php switchpostvariable("pg_vorname", $pg_vorname); ?> />
          </div>
        </div>
        <div class="form-group row m-1">
          <label for="pg_nachname" class="col-sm-4 col-form-label col-form-label-sm">Nachname</label>
          <div class="col-sm-5">
            <input type="text" class="form-control form-control-sm" name="pg_nachname" id="pg_nachname" placeholder="Nachname" required  <?php switchpostvariable("pg_nachname", $pg_nachname); ?>/>
          </div>
        </div>
        <div class="form-group row m-1">
          <label for="pg_gebdatum" class="col-sm-4 col-form-label col-form-label-sm">Geburtsdatum</label>
          <div class="col-sm-5">
            <input type="date" min="2000-01-01" max="2200-12-31" class="form-control form-control-sm" name="pg_gebdatum" id="pg_gebdatum" placeholder="TT.MM.JJJJ" required  <?php switchpostvariable("pg_gebdatum", $pg_gebdatum); ?>/>
          </div>
        </div>

          <div class="form-group row m-1">
              <label for="pg_beginn" class="col-sm-4 col-form-label col-form-label-sm">Beginn</label>
              <div class="col-sm-5">
                  <input type="date" min="2000-01-01" max="2200-12-31" class="form-control form-control-sm" name="pg_beginn" id="pg_beginn" placeholder="TT.MM.JJJJ" <?php switchpostvariable("pg_beginn", $pg_beginn); ?> />
              </div>
          </div>
          <div class="form-group row m-1">
              <label for="pg_ende" class="col-sm-4 col-form-label col-form-label-sm">Ende</label>
              <div class="col-sm-5">
                  <input type="date" min="2000-01-01" max="2200-12-31" class="form-control form-control-sm" name="pg_ende" id="pg_ende" placeholder="TT.MM.JJJJ" <?php switchpostvariable("pg_ende", $pg_ende); ?> />
              </div>
          </div>

          <div class="form-group row m-1">
              <label for="pg_aktiv" class="col-sm-4 col-form-label col-form-label-sm">aktiv</label>
              <div class="col-sm-5">
                  <input type="checkbox" <?php if((isset($_POST["submit"]) && $pg_aktiv_neu == 1) || (!isset($_POST["submit"]) && $pg_aktiv == 1)) echo ' checked '?> name="pg_aktiv" id="pg_aktiv" />
              </div>
          </div>
        
      </div>
      <div class="col">
        <div class="form-group row m-1">
          <label for="pg_tel1" class="col-sm-4 col-form-label col-form-label-sm">Festnetz:</label>
          <div class="col-sm-5">
            <input type="text" class="form-control form-control-sm" name="pg_tel1" id="pg_tel1" placeholder="+43 1 2345678" <?php switchpostvariable("pg_tel1", $pg_tel1); ?> />
          </div>
        </div>
        <div class="form-group row m-1">
          <label for="pg_tel2" class="col-sm-4 col-form-label col-form-label-sm">Mobil:</label>
            <div class="col-sm-5">
                <input type="text" class="form-control form-control-sm"  name="pg_tel2" id="pg_tel2" placeholder="+43 660 2345678" <?php switchpostvariable("pg_tel2", $pg_tel2); ?>  />
            </div>
        </div>
        <div class="form-group row m-1">
          <label for="pg_mail" class="col-sm-4 col-form-label col-form-label-sm m-0">E-Mail:</label>
          <div class="col-sm-5">
            <input type="text" class="form-control form-control-sm" name="pg_mail" id="pg_mail" placeholder="mail@beispiel.at" <?php switchpostvariable("pg_mail", $pg_mail); ?>  />
          </div>
        </div>
        <div class="form-group row m-1">
          <label class="col-sm-4 col-form-label col-form-label-sm">Art:</label>
          <div class="col-sm-5">
              <div class="form-check">
                  <input class="form-check-input" type="radio" name="pg_art" id="pg_art_24h" value="24h" <?php if((isset($_POST["submit"]) && $pg_art_neu == "24h") || (!isset($_POST["submit"]) && $pg_art == "24h")) echo 'checked';?>>
                  <label class="form-check-label" for="pg_art_24h">
                      24h Pflege
                  </label>
              </div>
              <div class="form-check">
                  <input class="form-check-input" type="radio" name="pg_art" id="pg_art_sw" value="sw" <?php if((isset($_POST["submit"]) && $pg_art_neu == "sw") || (!isset($_POST["submit"]) && $pg_art == "sw")) echo 'checked';?>>
                  <label class="form-check-label" for="pg_art_sw">
                      Stundenweise
                  </label>
              </div>
              <div class="form-check disabled">
                  <input class="form-check-input" type="radio" name="pg_art" id="pg_art_dgks" value="dgks" <?php if((isset($_POST["submit"]) && $pg_art_neu == "dgks") || (!isset($_POST["submit"]) && $pg_art == "dgks")) echo 'checked';?>>
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
      <div class="col-md-10 mt-1">
          <div class="form-check">
              <input class="form-check-input" type="checkbox" value="1" name="pg_RgAendern" id="pg_RgAendern" onclick="toggleField('pg_RgAendern','RgAdresseNeu')" />
              <label class="form-check-label" for="pg_RgAendern">
                  Rechnungsadresse ändern
              </label>
          </div>
      </div>
    <div class="row" id="RgAdresseNeu" style="display: none;">
      <div class="col col-sm-6 col-lg-4">
        <div class="form-group row m-1">
          <label for="pg_strasse" class="m-0">Straße:</label>
          <input type="text" class="form-control form-control-sm" name="pg_strasse" id="pg_strasse" placeholder="Straße" required <?php switchpostvariable("pg_strasse", $pg_strasse); ?> />
        </div>
        
      </div>
      <div class="col col-md-2">
        
        <div class="form-group row m-1">
          <label for="pg_nr" class="m-0">Nr:</label>
          <input type="text" class="form-control form-control-sm" name="pg_nr" id="pg_nr" placeholder="" required <?php switchpostvariable("pg_nr", $pg_nr); ?> />
        </div>
        
      </div>
      <div class="col col-md-1">
        
        <div class="form-group row m-1">
          <label for="pg_stg" class="m-0">Stg:</label>
          <input type="text" class="form-control form-control-sm" name="pg_stg"  id="pg_stg" placeholder="" <?php switchpostvariable("pg_stg", $pg_stg); ?>/>
        </div>
        
      </div>
      <div class="col col-md-1">
        
        <div class="form-group row m-1">
          <label for="pg_stck" class="m-0">Stock:</label>
          <input type="text" class="form-control form-control-sm" name="pg_stck" id="pg_stck" placeholder="" <?php switchpostvariable("pg_stck", $pg_stck); ?> />
        </div>
        
      </div>
      <div class="col col-md-1">
        
        <div class="form-group row m-1">
          <label for="pg_tuer" class="m-0">Tür:</label>
          <input type="text" class="form-control form-control-sm" name="pg_tuer" id="pg_tuer" placeholder="" <?php switchpostvariable("pg_tuer", $pg_tuer); ?> />
        </div>
        
      </div>
       <div class="col col-sm-2">
        
        <div class="form-group row m-1">
          <label for="pg_plz" class="m-0">PLZ:</label>
          <input type="text" size="4" class="form-control form-control-sm" name="pg_plz" id="pg_plz" placeholder="1234" required <?php switchpostvariable("pg_plz", $pg_plz); ?> />
        </div>
        
      </div>

    </div>
    
    <input type="submit" name="submit" id="submit" value="Datensatz ändern" class="btn btn-primary mt-5" />
      <a href="pgdetails.php?pg_id=<?php echo $pg_id ?>" class="btn btn-primary mt-5">Verwerfen</a>
            
  </div>
</form>
<?php
}
?>
<!-- MAIN END -->

<?php
include_once('footer.php');
?>
