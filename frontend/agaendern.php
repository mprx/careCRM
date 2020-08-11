<?php
$siteTitle = "Angehörigen ändern";
$siteCategory = "Angehörige";

include_once('header.php');
include_once('nav.php');
?>

<!-- MAIN START -->
<?php

$dbc = mysqli_connect(DB_HOST,DB_USER,DB_PASSWORD, DB_NAME)
    or die(errrorlog($dbc,0));
mysqli_set_charset($dbc,"utf8");

//sw id definieren
if (isset($_GET["sw_id"]))
{
    $sw_id = mysqli_real_escape_string($dbc, trim($_GET['sw_id']));
}
else
{
    $sw_id = 0;
}

//Wenn kein GET gesetzt ist, sw_id auf 0 setzen und error anzeigen (unten im file)
if ($sw_id == "")
{
    $sw_id = 0;
    $error = 1;
}

if(!isset($error)) {
    $query = "SELECT * FROM sachwalter WHERE sw_id = '$sw_id'";
    $data = mysqli_query($dbc, $query)
    or die(errorlog($dbc, $query));
    $row = mysqli_fetch_array($data);

    //Name
    $sw_anrede = $row["sw_anrede"];
    $sw_titel = $row["sw_titel"];
    $sw_vorname = $row["sw_vorname"];
    $sw_nachname = $row["sw_nachname"];

    //Geburtsdatum
    $sw_gebdatum = $row["sw_gebdatum"];

    //Kontakt
    $sw_tel1 = $row["sw_tel1"];
    $sw_tel2 = $row["sw_tel2"];
    $sw_mail = $row["sw_mail"];

    $sw_rg_art = $row["sw_rg_art"];



    //Adresse
    $query2 = "SELECT ad_id, ad_typ, ad_strasse, ad_nr, ad_stiege, ad_stock, ad_tuer, ad_plz FROM adressen WHERE sw_id = '$sw_id' AND ad_aktiv = '1' AND ad_typ IN('r', 'rb')";
    $data2 = mysqli_query($dbc, $query2)
    or die(errorlog($dbc,$query2));
    $row2 = mysqli_fetch_array($data2);

    $sw_ad = $row2["ad_id"];
    $sw_strasse	= $row2["ad_strasse"];
    $sw_nr = $row2["ad_nr"];
    $sw_stg	= $row2["ad_stiege"];
    $sw_stck = $row2["ad_stock"];
    $sw_tuer = $row2["ad_tuer"];
    $sw_plz = $row2["ad_plz"];

}

if (isset($_POST["submit"]))
{
  //form handling
    //variablen für insert vorbereiten
    //Name
    $sw_anrede_neu = mysqli_real_escape_string($dbc, trim($_POST['sw_anrede']));
    $sw_titel_neu = mysqli_real_escape_string($dbc, trim($_POST['sw_titel']));
    $sw_vorname_neu = mysqli_real_escape_string($dbc, trim($_POST['sw_vorname']));
    $sw_nachname_neu = mysqli_real_escape_string($dbc, trim($_POST['sw_nachname']));

    //Geburtsdatum
    $sw_gebdatum_neu = mysqli_real_escape_string($dbc, trim($_POST['sw_gebdatum']));

    //Kontakt
    $sw_tel1_neu = mysqli_real_escape_string($dbc, trim($_POST['sw_tel1']));
    $sw_tel2_neu = mysqli_real_escape_string($dbc, trim($_POST['sw_tel2']));
    $sw_mail_neu = mysqli_real_escape_string($dbc, trim($_POST['sw_mail']));

    //adresse
    if(isset($_POST["sw_RgAendern"]))
    {
        //Adresse
        $sw_strasse_neu = mysqli_real_escape_string($dbc, trim($_POST['sw_strasse']));
        $sw_nr_neu = mysqli_real_escape_string($dbc, trim($_POST['sw_nr']));
        $sw_stg_neu = mysqli_real_escape_string($dbc, trim($_POST['sw_stg']));
        $sw_stck_neu = mysqli_real_escape_string($dbc, trim($_POST['sw_stck']));
        $sw_tuer_neu = mysqli_real_escape_string($dbc, trim($_POST['sw_tuer']));
        $sw_plz_neu = mysqli_real_escape_string($dbc, trim($_POST['sw_plz']));
    }
    //rg art
    $sw_rg_neu = mysqli_real_escape_string($dbc, trim($_POST['sw_rg']));

    //Prüfen, ob alle notwendigen Werte vorhanden sind
    if (!empty($sw_anrede_neu) && !empty($sw_vorname_neu) && !empty($sw_nachname_neu) && !empty($sw_gebdatum_neu) && ((isset($_POST["sw_RgAendern"]) && !empty($sw_strasse_neu) && !empty($sw_nr_neu) && !empty($sw_plz_neu)) || !isset($_POST["sw_RgAendern"])))
    {
          //sichergehen, dass E-Mail Adresse korrektes Format hat, wenn angegeben
          if (($sw_mail_neu != "" && filter_var($sw_mail_neu, FILTER_VALIDATE_EMAIL)) || $sw_mail_neu == "")
          {

              $query = "UPDATE sachwalter SET " .
                  "sw_anrede = '$sw_anrede_neu', ".
                  "sw_titel = '$sw_titel_neu', ".
                  "sw_vorname = '$sw_vorname_neu', " .
                  "sw_nachname = '$sw_nachname_neu', " .
                  "sw_gebdatum = '$sw_gebdatum_neu', " .
                  "sw_tel1 = '$sw_tel1_neu', " .
                  "sw_tel2 = '$sw_tel2_neu', " .
                  "sw_mail = '$sw_mail_neu', " .
                  "sw_rg_art = '$sw_rg_neu' " .
                  "WHERE sw_id = '$sw_id'";
              mysqli_query($dbc, $query)
              or die(errorlog($dbc, $query));

              //in Adressen Tabelle eintragen
              if(isset($_POST["sw_RgAendern"]))
              {

                  $query = "INSERT INTO adressen " .
                      "(sw_id, ad_aktiv, ad_typ, ad_strasse, ad_nr, ad_stiege, ad_stock, ad_tuer, ad_plz) " .
                      "VALUES" .
                      "('$sw_id', '1', 'rb', '$sw_strasse_neu', '$sw_nr_neu', '$sw_stg_neu', '$sw_stck_neu', '$sw_tuer_neu', '$sw_plz_neu')";

                  mysqli_query($dbc, $query)
                  or die(errorlog($dbc, $query));

                  $query2 = "UPDATE adressen SET " .
                      "ad_aktiv = '0' WHERE ad_id = '$sw_ad'";

                  mysqli_query($dbc, $query2)
                  or die(errorlog($dbc, $query2));

              }
              //sw Änderung als Vermerk eintragen
              $query2 = "INSERT INTO vermerke (sw_id, ve_art, ve_flag, ve_text, us_id, ve_datum) " .
                  "VALUES ('$sw_id', 'System', 'n', 'Änderung: Sachwalter geändert / User: " . $_SESSION['username'] . "', 0, NOW())";
              mysqli_query($dbc, $query2)
              or die(errorlog($dbc, $query2));


              // Dem User den Erfolg mitteilen
              echo '<div class="alert alert-success" role="alert">';
              echo 'Der Sachwalter wurde erfolgreich geändert';
              echo '</div>';
              echo '<p><a href="swdetails.php?sw_id=' . $sw_id . '">Sachwalterdetails</a></p>';


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
<form action="<?php echo $_SERVER["PHP_SELF"] . "?sw_id=" . $sw_id ?>" method="POST">
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
                <option <?php if((isset($_POST["submit"]) && $sw_anrede_neu == "Herr") || (!isset($_POST["submit"]) && $sw_anrede == "Herr")) echo 'selected="selected"';?>>Herr</option>
                <option <?php if((isset($_POST["submit"]) && $sw_anrede_neu == "Frau") || (!isset($_POST["submit"]) && $sw_anrede == "Frau")) echo 'selected="selected"';?>>Frau</option>
            </select>
          </div>
        </div>
          <div class="form-group row m-1">
              <label for="sw_titel" class="col-sm-4 col-form-label col-form-label-sm">Titel:</label>
              <div class="col-sm-5">
                  <select class="form-control form-control-sm" name="sw_titel" id="sw_titel" required>
                      <option value="0">Titel</option>
                      <option <?php if((isset($_POST["submit"]) && $sw_titel_neu == "Mag.") || (!isset($_POST["submit"]) && $sw_titel == "Mag.")) echo 'selected="selected"';?>>Mag.</option>
                      <option <?php if((isset($_POST["submit"]) && $sw_titel_neu == "Dr.") || (!isset($_POST["submit"]) && $sw_titel== "Dr.")) echo 'selected="selected"';?>>Dr.</option>
                  </select>
              </div>
          </div>
        <div class="form-group row m-1">
          <label for="sw_vorname" class="col-sm-4 col-form-label col-form-label-sm">Vorname</label>
          <div class="col-sm-5">
            <input type="text" class="form-control form-control-sm" name="sw_vorname" id="sw_vorname" placeholder="Vorname" required <?php switchpostvariable("sw_vorname", $sw_vorname); ?> />
          </div>
        </div>
        <div class="form-group row m-1">
          <label for="sw_nachname" class="col-sm-4 col-form-label col-form-label-sm">Nachname</label>
          <div class="col-sm-5">
            <input type="text" class="form-control form-control-sm" name="sw_nachname" id="sw_nachname" placeholder="Nachname" required  <?php switchpostvariable("sw_nachname", $sw_nachname); ?> />
          </div>
        </div>
        <div class="form-group row m-1">
          <label for="sw_gebdatum" class="col-sm-4 col-form-label col-form-label-sm">Geburtsdatum</label>
          <div class="col-sm-5">
            <input type="date" min="2000-01-01" max="2200-12-31" class="form-control form-control-sm" name="sw_gebdatum" id="sw_gebdatum" placeholder="TT.MM.JJJJ" required <?php switchpostvariable("sw_gebdatum", $sw_gebdatum); ?> />
          </div>
        </div>
        
      </div>
      <div class="col">
        <div class="form-group row m-1">
          <label for="sw_tel1" class="col-sm-4 col-form-label col-form-label-sm">Festnetz:</label>
          <div class="col-sm-5">
            <input type="text" class="form-control form-control-sm" name="sw_tel1" id="sw_tel1" placeholder="+43 1 2345678" <?php switchpostvariable("sw_tel1", $sw_tel1); ?> />
          </div>
        </div>
        <div class="form-group row m-1">
          <label for="sw_tel2" class="col-sm-4 col-form-label col-form-label-sm">Mobil:</label>
            <div class="col-sm-5">
                <input type="text" class="form-control form-control-sm"  name="sw_tel2" id="kd_tel2" placeholder="+43 660 2345678" <?php switchpostvariable("sw_tel2", $sw_tel2); ?> />
            </div>
        </div>
        <div class="form-group row m-1">
          <label for="sw_mail" class="col-sm-4 col-form-label col-form-label-sm m-0">E-Mail:</label>
          <div class="col-sm-5">
            <input type="text" class="form-control form-control-sm" name="sw_mail" id="sw_mail" placeholder="mail@beispiel.at" <?php switchpostvariable("sw_mail", $sw_mail); ?> />
          </div>
        </div>
          <div class="form-group row m-1">
              <label for="sw_rg" class="col-sm-4 col-form-label col-form-label-sm">Rechnung:</label>
              <div class="col-sm-5">
                  <select class="form-control form-control-sm" name="sw_rg" id="sw_rg" required>
                      <option value="m" <?php if((isset($_POST["submit"]) && $sw_rg_neu == "m") || (!isset($_POST["submit"]) && $sw_rg_art == "m")) echo 'selected="selected"';?>>E-Mail</option>
                      <option value="p" <?php if((isset($_POST["submit"]) && $sw_rg_neu == "p") || (!isset($_POST["submit"]) && $sw_rg_art == "p")) echo 'selected="selected"';?>>Post</option>
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
      <div class="col-md-10 mt-1">
          <div class="form-check">
              <input class="form-check-input" type="checkbox" value="1" name="sw_RgAendern" id="sw_RgAendern" onclick="toggleField('sw_RgAendern','RgAdresseNeu')" />
              <label class="form-check-label" for="sw_RgAendern">
                  Rechnungsadresse ändern
              </label>
          </div>
      </div>
    <div class="row" id="RgAdresseNeu" style="display: none;">
      <div class="col col-sm-6 col-lg-4">
        <div class="form-group row m-1">
          <label for="kd_strasse" class="m-0">Straße:</label>
          <input type="text" class="form-control form-control-sm" name="sw_strasse" id="sw_strasse" placeholder="Straße" required <?php switchpostvariable("sw_strasse", $sw_strasse); ?> />
        </div>
        
      </div>
      <div class="col col-md-2">
        
        <div class="form-group row m-1">
          <label for="sw_nr" class="m-0">Nr:</label>
          <input type="text" class="form-control form-control-sm" name="sw_nr" id="sw_nr" placeholder="" required <?php switchpostvariable("sw_nr", $sw_nr); ?> />
        </div>
        
      </div>
      <div class="col col-md-1">
        
        <div class="form-group row m-1">
          <label for="sw_stg" class="m-0">Stg:</label>
          <input type="text" class="form-control form-control-sm" name="sw_stg"  id="sw_stg" placeholder="" <?php switchpostvariable("sw_stg", $sw_stg); ?> />
        </div>
        
      </div>
      <div class="col col-md-1">
        
        <div class="form-group row m-1">
          <label for="sw_stck" class="m-0">Stock:</label>
          <input type="text" class="form-control form-control-sm" name="sw_stck" id="sw_stck" placeholder="" <?php switchpostvariable("sw_stck", $sw_stck); ?> />
        </div>
        
      </div>
      <div class="col col-md-1">
        
        <div class="form-group row m-1">
          <label for="sw_tuer" class="m-0">Tür:</label>
          <input type="text" class="form-control form-control-sm" name="sw_tuer" id="sw_tuer" placeholder="" <?php switchpostvariable("sw_tuer", $sw_tuer); ?> />
        </div>
        
      </div>
       <div class="col col-sm-2">
        
        <div class="form-group row m-1">
          <label for="sw_plz" class="m-0">PLZ:</label>
          <input type="text" size="4" class="form-control form-control-sm" name="sw_plz" id="sw_plz" placeholder="1234" required <?php switchpostvariable("sw_plz", $sw_plz); ?> />
        </div>
        
      </div>

    </div>
      <div class="row">
          <div class="col border border-primary mt-4">
              Angehöriger Kunde
          </div>
      </div>
      <div class="row">

              <div class="form-group row m-1">
                <div class="alert alert-info">Kundenzuordnung bitte bei den einzelnen Kunden ändern.</div>
              </div>

      </div>


    
    <input type="submit" name="submit" id="submit" value="Datensatz ändern" class="btn btn-primary mt-5" />
      <a href="agdetails.php?sw_id=<?php echo $sw_id ?>" class="btn btn-primary mt-5">Verwerfen</a>
            
  </div>
</form>
<?php
}
?>
<!-- MAIN END -->

<?php
include_once('footer.php');
?>
