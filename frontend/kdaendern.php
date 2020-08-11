<?php
$siteTitle = "Kunde ändern";
$siteCategory = "Kunden";

include_once('header.php');
include_once('nav.php');
?>

<!-- MAIN START -->
<?php

$dbc = mysqli_connect(DB_HOST,DB_USER,DB_PASSWORD, DB_NAME)
    or die(errrorlog($dbc,0));
mysqli_set_charset($dbc,"utf8");

//Kundennummer definieren
if (isset($_GET["kd_id"]))
{
    $kd_id = mysqli_real_escape_string($dbc, trim($_GET['kd_id']));
}
else
{
    $kd_id = 0;
}

//Wenn kein GET gesetzt ist, kd_id auf 0 setzen und error anzeigen (unten im file)
if ($kd_id == "")
{
    $kd_id = 0;
    $error = 1;
}

if(!isset($error))
{
    $query = "SELECT * FROM kunde WHERE kd_id = '$kd_id'";
    $data = mysqli_query($dbc, $query)
        or die(errorlog($dbc,$query));
    $row = mysqli_fetch_array($data);

    $kd_anrede = $row["kd_anrede"];
    $kd_titel = $row["kd_titel"];
    $kd_vorname = $row["kd_vorname"];
    $kd_nachname = $row["kd_nachname"];

    //Geburtsdatum
    $kd_gebdatum = $row["kd_gebdatum"];

    //Kontakt
    $kd_tel1 = $row["kd_tel1"];
    $kd_tel2 = $row["kd_tel2"];
    $kd_mail = $row["kd_mail"];

    //Zustellung p oder m
    $kd_rg = $row["kd_rg_art"];

    //Daten
    $kd_beginn = $row["kd_beginn"];
    $kd_ende = $row["kd_ende"];
    $kd_ableben = $row["kd_ableben"];
    $kd_aktiv = $row["kd_aktiv"];

    //Sachwalter
    $query2 = "SELECT angehoerige.sw_id, sachwalter.sw_typ FROM angehoerige, sachwalter WHERE angehoerige.kd_id = '$kd_id' AND sachwalter.sw_id = angehoerige.sw_id AND angehoerige.ag_aktiv = '1' AND sachwalter.sw_typ = 'sw'";
    $data2 = mysqli_query($dbc, $query2)
    or die(errorlog($dbc,$query2));
    $row2 = mysqli_fetch_array($data2);

    if(isset($row2["sw_id"]))
    {

        $kd_sachwalter = $row2["sw_id"];
    }
    else
    {
        $kd_sachwalter = 0;
    }

    //Angehörige
    $query3 = "SELECT angehoerige.sw_id, sachwalter.sw_typ FROM angehoerige, sachwalter WHERE angehoerige.kd_id = '$kd_id'AND sachwalter.sw_id = angehoerige.sw_id AND angehoerige.ag_aktiv = '1' AND sachwalter.sw_typ = 'ag'";
    $data3 = mysqli_query($dbc, $query3)
    or die(errorlog($dbc,$query3));

    $kd_ag1 = 0;
    $kd_ag2 = 0;
    $kd_ag3 = 0;

    if(mysqli_num_rows($data3) != 0)
    {
        $i = 1;
        while ($row3 = mysqli_fetch_array($data3))
        {
            ${"kd_ag" . $i} = $row3["sw_id"];
            $i++;
        }
    }

    //Adressen
    $query4 = "SELECT ad_id, ad_typ, ad_strasse, ad_nr, ad_stiege, ad_stock, ad_tuer, ad_plz FROM adressen WHERE kd_id = '$kd_id' AND ad_aktiv = '1' AND ad_typ IN('r', 'rb')";
    $data4 = mysqli_query($dbc, $query4)
        or die(errorlog($dbc,$query4));
    $row4 = mysqli_fetch_array($data4);

    $kd_ad = $row4["ad_id"];
    $kd_strasse	= $row4["ad_strasse"];
    $kd_nr = $row4["ad_nr"];
    $kd_stg	= $row4["ad_stiege"];
    $kd_stck = $row4["ad_stock"];
    $kd_tuer = $row4["ad_tuer"];
    $kd_plz = $row4["ad_plz"];

    if($row4["ad_typ"] == "r")
    {
        $query5 = "SELECT ad_id, ad_typ, ad_strasse, ad_nr, ad_stiege, ad_stock, ad_tuer, ad_plz FROM adressen WHERE kd_id = '$kd_id' AND ad_aktiv = '1' AND ad_typ IN('b', 'rb')";
        $data5 = mysqli_query($dbc, $query5)
        or die(errorlog($dbc,$query5));
        $row5 = mysqli_fetch_array($data5);

        $kd_b_ad = $row5["ad_id"];
        $kd_b_strasse	= $row5["ad_strasse"];
        $kd_b_nr = $row5["ad_nr"];
        $kd_b_stg	= $row5["ad_stiege"];
        $kd_b_stck = $row5["ad_stock"];
        $kd_b_tuer = $row5["ad_tuer"];
        $kd_b_plz = $row5["ad_plz"];
    }

    $query6 = "SELECT ve_id, ve_text FROM vermerke WHERE kd_id = '$kd_id' AND ve_flag = '1'";
    $data6 = mysqli_query($dbc, $query6)
        or die(errorlog($dbc,$query6));
    $row6 = mysqli_fetch_array($data6);

    $kd_anmerkung_id = $row6["ve_id"];
    $kd_anmerkung = $row6["ve_text"];
}


if (isset($_POST["submit"]))
{
  //form handling
    //variablen für insert vorbereiten
    //Name
    $kd_anrede_neu = mysqli_real_escape_string($dbc, trim($_POST['kd_anrede']));
    $kd_titel_neu = mysqli_real_escape_string($dbc, trim($_POST['kd_titel']));
    $kd_vorname_neu = mysqli_real_escape_string($dbc, trim($_POST['kd_vorname']));
    $kd_nachname_neu = mysqli_real_escape_string($dbc, trim($_POST['kd_nachname']));

    //Geburtsdatum
    $kd_gebdatum_neu = mysqli_real_escape_string($dbc, trim($_POST['kd_gebdatum']));


    $kd_sachwalter_neu = mysqli_real_escape_string($dbc, trim($_POST['kd_sachwalter']));

    //Kontakt
    $kd_tel1_neu = mysqli_real_escape_string($dbc, trim($_POST['kd_tel1']));
    $kd_tel2_neu = mysqli_real_escape_string($dbc, trim($_POST['kd_tel2']));
    $kd_mail_neu = mysqli_real_escape_string($dbc, trim($_POST['kd_mail']));

    //Zustellung p oder m
    $kd_rg_neu = mysqli_real_escape_string($dbc, trim($_POST['kd_rg']));

    //Angehörge
    $kd_ag1_neu = mysqli_real_escape_string($dbc, trim($_POST['kd_ag1']));
    $kd_ag2_neu = mysqli_real_escape_string($dbc, trim($_POST['kd_ag2']));
    $kd_ag3_neu = mysqli_real_escape_string($dbc, trim($_POST['kd_ag3']));

    //Rechnungsadresse
    if(isset($_POST["kd_RgAendern"]))
    {
        $kd_strasse_neu	= mysqli_real_escape_string($dbc, trim($_POST['kd_strasse']));
        $kd_nr_neu = mysqli_real_escape_string($dbc, trim($_POST['kd_nr']));
        $kd_stg_neu	= mysqli_real_escape_string($dbc, trim($_POST['kd_stg']));
        $kd_stck_neu = mysqli_real_escape_string($dbc, trim($_POST['kd_stck']));
        $kd_tuer_neu = mysqli_real_escape_string($dbc, trim($_POST['kd_tuer']));
        $kd_plz_neu = mysqli_real_escape_string($dbc, trim($_POST['kd_plz']));
    }


    //Betreuungsadresse
    if(isset($_POST["kd_RgIsNB"]))
    {
        $kd_b_strasse_neu	= mysqli_real_escape_string($dbc, trim($_POST['kd_b_strasse']));
        $kd_b_nr_neu = mysqli_real_escape_string($dbc, trim($_POST['kd_b_nr']));
        $kd_b_stg_neu	= mysqli_real_escape_string($dbc, trim($_POST['kd_b_stg']));
        $kd_b_stck_neu = mysqli_real_escape_string($dbc, trim($_POST['kd_b_stck']));
        $kd_b_tuer_neu = mysqli_real_escape_string($dbc, trim($_POST['kd_b_tuer']));
        $kd_b_plz_neu = mysqli_real_escape_string($dbc, trim($_POST['kd_b_plz']));
    }

    //Anmerkung
    $kd_anmerkung_neu = mysqli_real_escape_string($dbc, trim($_POST['kd_anmerkung']));

    //Beginn und Ende
    $kd_beginn_neu = "'" . mysqli_real_escape_string($dbc, trim($_POST['kd_beginn'])) . "'";
    $kd_ende_neu = "'" . mysqli_real_escape_string($dbc, trim($_POST['kd_ende'])) . "'";

    if($kd_beginn_neu == "''") $kd_beginn_neu = "NULL";
    if($kd_ende_neu == "''") $kd_ende_neu = "NULL";

    $kd_ableben_neu = 0;
    if(isset($_POST["kd_ableben"]))
    {
        $kd_ableben_neu = 1;
    }

    $kd_aktiv_neu = 0;
    if(isset($_POST["kd_aktiv"]))
    {
        $kd_aktiv_neu = 1;
    }


    //Prüfen, ob alle notwendigen Werte vorhanden sind
    if (!empty($kd_anrede_neu) && !empty($kd_vorname_neu) && !empty($kd_nachname_neu) &&
        ((isset($kd_b_strasse_neu) && !empty($kd_b_strasse_neu) && !empty($kd_b_nr_neu) && !empty($kd_b_plz_neu)) || (!isset($kd_b_strasse_neu)))
    )
    {
          //sichergehen, dass E-Mail Adresse korrektes Format hat, wenn angegeben
          if (($kd_mail_neu != "" && filter_var($kd_mail_neu, FILTER_VALIDATE_EMAIL)) || $kd_mail_neu == "")
          {
              //in Kunde Tabelle eintragen
            $query = "UPDATE kunde SET " .
                     "kd_anrede = '$kd_anrede_neu', ".
                     "kd_titel = '$kd_titel_neu', " .
                     "kd_vorname = '$kd_vorname_neu', " .
                     "kd_nachname = '$kd_nachname_neu', " .
                     "kd_gebdatum = '$kd_gebdatum_neu', " .
                     "kd_tel1 = '$kd_tel1_neu', " .
                     "kd_tel2 = '$kd_tel2_neu', " .
                     "kd_mail = '$kd_mail_neu', " .
                     "kd_rg_art = '$kd_rg_neu', " .
                     "kd_beginn = $kd_beginn_neu, " .
                     "kd_ende = $kd_ende_neu, " .
                     "kd_ableben = '$kd_ableben_neu', " .
                     "kd_aktiv = '$kd_aktiv_neu' " .
                     "WHERE kd_id = '$kd_id'";
              mysqli_query($dbc, $query)
              or die(errorlog($dbc, $query));

            //in Adressen Tabelle eintragen
              if(isset($_POST["kd_RgAendern"]))
              {
                  if(!isset($_POST["kd_RgIsNB"]))
                  {
                      $ad_typ1 = "rb";
                  }
                  else
                  {
                      $ad_typ1 = "r";
                  }

                  $query = "INSERT INTO adressen " .
                      "(kd_id, ad_aktiv, ad_typ, ad_strasse, ad_nr, ad_stiege, ad_stock, ad_tuer, ad_plz) " .
                      "VALUES" .
                      "('$kd_id', '1', '$ad_typ1', '$kd_strasse_neu', '$kd_nr_neu', '$kd_stg_neu', '$kd_stck_neu', '$kd_tuer_neu', '$kd_plz_neu')";

                  mysqli_query($dbc, $query)
                  or die(errorlog($dbc, $query));

                  $query2 = "UPDATE adressen SET " .
                      "ad_aktiv = '0' WHERE ad_id = '$kd_ad'";

                  mysqli_query($dbc, $query2)
                  or die(errorlog($dbc, $query2));

                  if($ad_typ1 == "r")
                  {
                      $query = "INSERT INTO adressen " .
                          "(kd_id, ad_aktiv, ad_typ, ad_strasse, ad_nr, ad_stiege, ad_stock, ad_tuer, ad_plz) " .
                          "VALUES" .
                          "('$kd_id', '1', 'b', '$kd_b_strasse_neu', '$kd_b_nr_neu', '$kd_b_stg_neu', '$kd_b_stck_neu', '$kd_b_tuer_neu', '$kd_b_plz_neu')";

                      mysqli_query($dbc, $query)
                      or die(errorlog($dbc, $query));

                      $query2 = "UPDATE adressen SET " .
                          "ad_aktiv = '0' WHERE ad_id = '$kd_b_ad'";

                      mysqli_query($dbc, $query2)
                      or die(errorlog($dbc, $query2));
                  }

              }

              //Sachwalter/Angehörige

              if($kd_sachwalter != $kd_sachwalter_neu)
              {
                  if($kd_sachwalter != 0)
                  {
                      $query = "UPDATE angehoerige SET ag_aktiv = 0 WHERE kd_id = $kd_id AND sw_id = $kd_sachwalter";
                      mysqli_query($dbc,$query)
                      or die(errorlog($dbc,$query));
                  }

                  if($kd_sachwalter_neu != 0)
                  {
                      $query = "INSERT INTO angehoerige " .
                          "(sw_id, kd_id, ag_aktiv) " .
                          "VALUES($kd_sachwalter_neu, $kd_id, 1)";
                      mysqli_query($dbc, $query)
                      or die(errorlog($dbc, $query));
                  }
              }

              if($kd_ag1 != $kd_ag1_neu)
              {

                  if($kd_ag1 != 0)
                  {
                      $query = "UPDATE angehoerige SET ag_aktiv = 0 WHERE kd_id = $kd_id AND sw_id = $kd_ag1";
                      mysqli_query($dbc,$query)
                      or die(errorlog($dbc,$query));
                  }

                  if($kd_ag1_neu != 0)
                  {
                      $query = "INSERT INTO angehoerige " .
                          "(sw_id, kd_id, ag_aktiv) " .
                          "VALUES($kd_ag1_neu, $kd_id, 1)";
                      mysqli_query($dbc,$query)
                        or die(errorlog($dbc,$query));
                  }
              }

              if($kd_ag2 != $kd_ag2_neu)
              {

                  if($kd_ag2 != 0)
                  {
                      $query = "UPDATE angehoerige SET ag_aktiv = 0 WHERE kd_id = $kd_id AND sw_id = $kd_ag2";
                      mysqli_query($dbc,$query)
                      or die(errorlog($dbc,$query));
                  }

                  if($kd_ag2_neu != 0)
                  {
                      $query = "INSERT INTO angehoerige " .
                          "(sw_id, kd_id, ag_aktiv) " .
                          "VALUES($kd_ag2_neu, $kd_id, 1)";
                      mysqli_query($dbc,$query)
                        or die(errorlog($dbc,$query));
                  }
              }

              if($kd_ag3 != $kd_ag3_neu)
              {

                  if($kd_ag3 != 0)
                  {
                      $query = "UPDATE angehoerige SET ag_aktiv = 0 WHERE kd_id = $kd_id AND sw_id = $kd_ag3";
                      mysqli_query($dbc,$query)
                      or die(errorlog($dbc,$query));
                  }

                  if($kd_ag3_neu != 0)
                  {
                      $query = "INSERT INTO angehoerige " .
                          "(sw_id, kd_id, ag_aktiv) " .
                          "VALUES($kd_ag3_neu, $kd_id, 1)";
                      mysqli_query($dbc,$query)
                      or die(errorlog($dbc,$query));
                  }

              }

            //Kd Änderung als Vermerk eintragen
            $query2 = "INSERT INTO vermerke (kd_id, ve_art, ve_flag, ve_text, us_id, ve_datum) " .
                      "VALUES ('$kd_id', 'System', 'n', 'Änderung: Kunde geändert / User: " . $_SESSION['username'] . "', 0, NOW())";
            mysqli_query($dbc, $query2)
              or die(errorlog($dbc, $query2));

            //Wenn Anmerkung geändert, ebenso in Vermerke:
            if($kd_anmerkung != $kd_anmerkung_neu)
            {
                $query4 = "UPDATE vermerke SET ve_flag = 'n' WHERE ve_id = '$kd_anmerkung_id'";
                mysqli_query($dbc, $query4)
                    or die(errorlog($dbc, $query4));

                $query3 = "INSERT INTO vermerke (kd_id, ve_art, ve_flag, ve_text, us_id, ve_datum) " .
                          "VALUES ('$kd_id', 'Information', '1', '$kd_anmerkung_neu', 0, NOW())";
                mysqli_query($dbc, $query3)
                  or die(errorlog($dbc, $query3));

            }

            // Dem User den Erfolg mitteilen
            echo '<div class="alert alert-success" role="alert">';
            echo 'Der Kunde wurde erfolgreich geändert';
            echo '</div>';
            echo '<p><a href="kddetails.php?kd_id=' . $kd_id . '">Kundendetails</a></p>';


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
<form action="<?php echo $_SERVER["PHP_SELF"] ?>?kd_id=<?php echo $kd_id; ?>" method="POST">
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
              <option <?php if((isset($_POST["submit"]) && $kd_anrede_neu == "Herr") || (!isset($_POST["submit"]) && $kd_anrede == "Herr")) echo 'selected="selected"';?>>Herr</option>
              <option <?php if((isset($_POST["submit"]) && $kd_anrede_neu == "Frau") || (!isset($_POST["submit"]) && $kd_anrede == "Frau")) echo 'selected="selected"';?>>Frau</option>
            </select>
          </div>
        </div>
        <div class="form-group row m-1">
          <label for="kd_titel" class="col-sm-4 col-form-label col-form-label-sm">Titel:</label>
          <div class="col-sm-5">
            <select class="form-control form-control-sm" name="kd_titel" id="kd_titel">
              <option value="0">Titel</option>
              <option <?php if((isset($_POST["submit"]) && $kd_titel_neu == "Dr.") || (!isset($_POST["submit"]) && $kd_titel == "Dr.")) echo 'selected="selected"';?>>Dr.</option>
              <option <?php if((isset($_POST["submit"]) && $kd_titel_neu == "Mag.") || (!isset($_POST["submit"]) && $kd_titel == "Mag.")) echo 'selected="selected"';?>>Mag.</option>
            </select>
          </div>
        </div>
        <div class="form-group row m-1">
          <label for="kd_vorname" class="col-sm-4 col-form-label col-form-label-sm">Vorname</label>
          <div class="col-sm-5">
            <input type="text" class="form-control form-control-sm" name="kd_vorname" id="kd_vorname" placeholder="Vorname" required <?php switchpostvariable("kd_vorname", $kd_vorname); ?> />
          </div>
        </div>
        <div class="form-group row m-1">
          <label for="kd_nachname" class="col-sm-4 col-form-label col-form-label-sm">Nachname</label>
          <div class="col-sm-5">
            <input type="text" class="form-control form-control-sm" name="kd_nachname" id="kd_nachname" placeholder="Nachname" required <?php switchpostvariable("kd_nachname", $kd_nachname); ?> />
          </div>
        </div>
        <div class="form-group row m-1">
          <label for="kd_gebdatum" class="col-sm-4 col-form-label col-form-label-sm">Geburtsdatum</label>
          <div class="col-sm-5">
            <input type="date" min="2000-01-01" max="2200-12-31" class="form-control form-control-sm" name="kd_gebdatum" id="kd_gebdatum" placeholder="TT.MM.JJJJ" required <?php switchpostvariable("kd_gebdatum", $kd_gebdatum); ?> />
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
               

                    $sw_query = "SELECT sw_id, sw_nachname, sw_vorname FROM sachwalter WHERE sw_typ = 'sw' ORDER BY sw_nachname ASC";
                    $sw_data = mysqli_query($dbc, $sw_query)
                        or die(errorlog($dbc,$sw_query));
                        
                while ($sw_row = mysqli_fetch_array($sw_data)) 
                    {
                        echo '<option ';
                        if ((isset($_POST["kd_sachwalter"]) && $kd_sachwalter_neu == $sw_row["sw_id"]) || (!isset($_POST["submit"]) && $kd_sachwalter == $sw_row["sw_id"])) echo " selected='selected' ";
                        echo 'value="' . $sw_row['sw_id'] . '">' . $sw_row['sw_nachname'] . ' ' . $sw_row['sw_vorname'] . '</option>';  
                    }
               
               
              ?>
            </select>
          </div>
        </div>
        <div class="form-group row m-1">
          <label for="kd_tel1" class="col-sm-4 col-form-label col-form-label-sm">Festnetz:</label>
          <div class="col-sm-5">
            <input type="text" class="form-control form-control-sm" name="kd_tel1" id="kd_tel1" placeholder="+43 1 2345678" <?php switchpostvariable("kd_tel1", $kd_tel1); ?> />
          </div>
        </div>
        <div class="form-group row m-1">
          <label for="kd_tel2" class="col-sm-4 col-form-label col-form-label-sm">Mobil:</label>
            <div class="col-sm-5">
                <input type="text" class="form-control form-control-sm"  name="kd_tel2" id="kd_tel2" placeholder="+43 660 2345678"<?php switchpostvariable("kd_tel2", $kd_tel2); ?> />
            </div>
        </div>
        <div class="form-group row m-1">
          <label for="kd_mail" class="col-sm-4 col-form-label col-form-label-sm m-0">E-Mail:</label>
          <div class="col-sm-5">
            <input type="text" class="form-control form-control-sm" name="kd_mail" id="kd_mail" placeholder="mail@beispiel.at"<?php switchpostvariable("kd_mail", $kd_mail); ?> />
          </div>
        </div>
        <div class="form-group row m-1">
          <label for="kd_rg" class="col-sm-4 col-form-label col-form-label-sm">Rechnung:</label>
          <div class="col-sm-5">
            <select class="form-control form-control-sm" name="kd_rg" id="kd_rg">
              <option value="m"<?php  if ((isset($_POST["submit"]) && $kd_rg_neu == "m") || (!isset($_POST["submit"]) && $kd_rg == "m")) echo "selected='selected' "; ?>>E-Mail</option>
              <option value="p"<?php  if ((isset($_POST["submit"]) && $kd_rg_neu == "p") || (!isset($_POST["submit"]) && $kd_rg == "p")) echo "selected='selected' "; ?>>Post</option>
            </select>
          </div>
        </div>
        
      </div>
    </div>

      <div class="row">
          <div class="col border border-primary mt-4">
              Vertragsdaten
          </div>
      </div>
      <div class="row">
          <div class="col">

      <div class="form-group row m-1">
          <label for="kd_aktiv" class="col-sm-4 col-form-label col-form-label-sm">Aktiv:</label>
          <div class="col-sm-5">
              <input type="checkbox" <?php if((isset($_POST["submit"]) && $kd_aktiv_neu == 1) || (!isset($_POST["submit"]) && $kd_aktiv == 1)) echo ' checked '?> name="kd_aktiv" id="kd_aktiv" />
          </div>
      </div>

      <div class="form-group row m-1">
          <label for="kd_beginn" class="col-sm-4 col-form-label col-form-label-sm">Beginn</label>
          <div class="col-sm-5">
              <input type="date" min="2000-01-01" max="2200-12-31" class="form-control form-control-sm" name="kd_beginn" id="kd_beginn" placeholder="TT.MM.JJJJ" <?php switchpostvariable("kd_beginn", $kd_beginn); ?> />
          </div>
      </div>
      <div class="form-group row m-1">
          <label for="kd_ende" class="col-sm-4 col-form-label col-form-label-sm">Ende</label>
          <div class="col-sm-5">
              <input type="date" min="2000-01-01" max="2200-12-31" class="form-control form-control-sm" name="kd_ende" id="kd_ende" placeholder="TT.MM.JJJJ" <?php switchpostvariable("kd_ende", $kd_ende); ?> />
          </div>
      </div>

      <div class="form-group row m-1">
          <label for="kd_ableben" class="col-sm-4 col-form-label col-form-label-sm">Verstorben</label>
          <div class="col-sm-5">
              <input type="checkbox" <?php if((isset($_POST["submit"]) && $kd_ableben_neu == 1) || (!isset($_POST["submit"]) && $kd_ableben == 1)) echo ' checked '?> name="kd_ableben" id="kd_ableben" />
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
              <?php
               

                    $ag_query = "SELECT sw_id, sw_nachname, sw_vorname FROM sachwalter WHERE sw_typ = 'ag' ORDER BY sw_nachname ASC";
                    $ag_data = mysqli_query($dbc, $ag_query)
                        or die(errorlog($dbc,$ag_query));
                        
                while ($ag_row = mysqli_fetch_array($ag_data)) 
                    {
                        echo '<option ';
                        if ((isset($_POST["kd_ag1"]) && $kd_ag1_neu == $ag_row["sw_id"]) || (!isset($_POST["submit"]) && isset($kd_ag1) && $kd_ag1 == $ag_row["sw_id"])) echo " selected='selected' ";
                        echo 'value="' . $ag_row['sw_id'] . '">' . $ag_row['sw_nachname'] . ' ' . $ag_row['sw_vorname'] . '</option>';  
                    }
               
               
              ?>
            </select>
          </div>
        </div>
        <div class="form-group row m-1" id="kd_ag2_div" <?php if(!isset($kd_ag1_neu) && !isset($kd_ag1)) echo ' style="display: none;"'; ?>>
          <label for="kd_ag2" class="col col-form-label col-form-label-sm">Angehöriger 2:</label>
          <div class="col-sm-5">
            <select class="form-control form-control-sm" name="kd_ag2" id="kd_ag2" onchange="toggleField('kd_ag2','kd_ag3_div')">
              <option value="0">keiner</option>
               <?php
              $ag_data = mysqli_query($dbc, $ag_query)
                        or die(errorlog($dbc,$ag_query));
             while ($ag_row = mysqli_fetch_array($ag_data)) 
                    {
                        echo '<option ';
                        if ((isset($_POST["kd_ag2"]) && $kd_ag2_neu == $ag_row["sw_id"]) || (!isset($_POST["submit"]) && isset($kd_ag2) && $kd_ag2 == $ag_row["sw_id"])) echo " selected='selected' ";
                        echo 'value="' . $ag_row['sw_id'] . '">' . $ag_row['sw_nachname'] . ' ' . $ag_row['sw_vorname'] . '</option>';  
                    }
              ?>
            </select>
          </div>
        </div>
        <div class="form-group row m-1" id="kd_ag3_div"  <?php if(!isset($kd_ag2_neu) && !isset($kd_ag2)) echo ' style="display: none;"'; ?>>
          <label for="kd_ag3" class="col col-form-label col-form-label-sm">Angehöriger 3:</label>
          <div class="col-sm-5">
            <select class="form-control form-control-sm" name="kd_ag3" id="kd_ag3">
              <option value="0">keiner</option>
              <?php
              $ag_data = mysqli_query($dbc, $ag_query)
                        or die(errorlog($dbc,$ag_query));
             while ($ag_row = mysqli_fetch_array($ag_data)) 
                    {
                        echo '<option ';
                        if ((isset($_POST["kd_ag3"]) && $kd_ag3_neu == $ag_row["sw_id"]) || (!isset($_POST["submit"]) && isset($kd_ag3) && $kd_ag3 == $ag_row["sw_id"])) echo " selected='selected' ";
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
      <div class="col-md-10 mt-1">
          <div class="form-check">
              <input class="form-check-input" type="checkbox" value="1" name="kd_RgAendern" id="kd_RgAendern" onclick="toggleField('kd_RgAendern','RgAdresseNeu')" />
              <label class="form-check-label" for="kd_RgAendern">
                  Rechnungsadresse ändern
              </label>
          </div>
      </div>
    <div class="row" id="RgAdresseNeu" style="display: none;">
        
      <div class="col col-sm-6 col-lg-4">
        
        <div class="form-group row m-1">
          <label for="kd_strasse" class="m-0">Straße:</label>
          <input type="text" class="form-control form-control-sm" name="kd_strasse" id="kd_strasse" placeholder="Straße" required <?php switchpostvariable("kd_strasse", $kd_strasse); ?> />
        </div>
        
      </div>
      <div class="col col-md-2">
        
        <div class="form-group row m-1">
          <label for="kd_nr" class="m-0">Nr:</label>
          <input type="text" class="form-control form-control-sm" name="kd_nr" id="kd_nr" placeholder="" required <?php switchpostvariable("kd_nr", $kd_nr); ?> />
        </div>
        
      </div>
      <div class="col col-md-1">
        
        <div class="form-group row m-1">
          <label for="kd_stg" class="m-0">Stg:</label>
          <input type="text" class="form-control form-control-sm" name="kd_stg"  id="kd_stg" placeholder=""  <?php switchpostvariable("kd_stg", $kd_stg); ?>/>
        </div>
        
      </div>
      <div class="col col-md-1">
        
        <div class="form-group row m-1">
          <label for="kd_stck" class="m-0">Stock:</label>
          <input type="text" class="form-control form-control-sm" name="kd_stck" id="kd_stck" placeholder="" <?php switchpostvariable("kd_stck", $kd_stck); ?> />
        </div>
        
      </div>
      <div class="col col-md-1">
        
        <div class="form-group row m-1">
          <label for="kd_tuer" class="m-0">Tür:</label>
          <input type="text" class="form-control form-control-sm" name="kd_tuer" id="kd_tuer" placeholder="" <?php switchpostvariable("kd_tuer", $kd_tuer); ?> />
        </div>
        
      </div>
       <div class="col col-sm-2">
        
        <div class="form-group row m-1">
          <label for="kd_plz" class="m-0">PLZ:</label>
          <input type="text" size="4" class="form-control form-control-sm" name="kd_plz" id="kd_plz" placeholder="1234" required <?php switchpostvariable("kd_plz", $kd_plz); ?> />
        </div>
        
      </div>
    </div>

    
    <div class="row">
      <div class="col border border-primary mt-4">
      Betreuungsadresse
      </div>
    </div>
    <div class="col-md-10 mt-1">
            <div class="form-check">
              <input class="form-check-input" type="checkbox" value="1" name="kd_RgIsNB" id="kd_RgIsNB" onclick="toggleField('kd_RgIsNB','BAdresseNeu')" />
              <label class="form-check-label" for="kd_RgIsNB">
                Abweichende Betreuungsadresse
              </label>
            </div>
          </div>
    <div class="row" id="BAdresseNeu" style="display: none;">
      <div class="col">
        
        <div class="row">
      <div class="col col-sm-6 col-lg-4">
        
        <div class="form-group row m-1">
          <label for="kd_b_strasse" class="m-0">Straße:</label>
          <input type="text" class="form-control form-control-sm" name="kd_b_strasse" id="kd_b_strasse" placeholder="Straße" <?php switchpostvariable("kd_b_strasse", $kd_b_strasse); ?> />
        </div>
        
      </div>
      <div class="col col-md-2">
        
        <div class="form-group row m-1">
          <label for="kd_b_nr" class="m-0">Nr:</label>
          <input type="text" class="form-control form-control-sm" name="kd_b_nr" id="kd_b_nr" placeholder="" <?php switchpostvariable("kd_b_nr", $kd_b_nr); ?> />
        </div>
        
      </div>
      <div class="col col-md-1">
        
        <div class="form-group row m-1">
          <label for="kd_b_stg" class="m-0">Stg:</label>
          <input type="text" class="form-control form-control-sm" name="kd_b_stg" id="kd_b_stg" placeholder="" <?php switchpostvariable("kd_b_stg", $kd_b_stg); ?> />
        </div>
        
      </div>
      <div class="col col-md-1">
        
        <div class="form-group row m-1">
          <label for="kd_b_stck" class="m-0">Stock:</label>
          <input type="text" class="form-control form-control-sm" name="kd_b_stck" id="kd_b_stck" placeholder="" <?php switchpostvariable("kd_b_stck", $kd_b_stck); ?> />
        </div>
        
      </div>
      <div class="col col-md-1">
        
        <div class="form-group row m-1">
          <label for="kd_b_tuer" class="m-0">Tür:</label>
          <input type="text" class="form-control form-control-sm" name="kd_b_tuer" id="kd_b_tuer" placeholder="" <?php switchpostvariable("kd_b_tuer", $kd_b_tuer); ?> />
        </div>
        
      </div>
       <div class="col col-sm-2">
        
        <div class="form-group row m-1">
          <label for="kd_b_plz" class="m-0">PLZ:</label>
          <input type="text" size="4" class="form-control form-control-sm" name="kd_b_plz" id="kd_b_plz" placeholder=""  <?php switchpostvariable("kd_b_plz", $kd_b_plz); ?>/>
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
              <input type="text" class="form-control form-control-sm" name="kd_anmerkung" id="kd_anmerkung" placeholder="Anmerkung" <?php switchpostvariable("kd_anmerkung", $kd_anmerkung); ?> />
            </div>
          
          </div>
        </div>
        
      </div>
    </div>
    
    
    <input type="submit" name="submit" id="submit" value="Datensatz ändern" class="btn btn-primary" />
      <a href="kddetails.php?kd_id=<?php echo $kd_id ?>" class="btn btn-primary">Verwerfen</a>
  </div>
</form>
<?php
}
mysqli_close($dbc);
?>
<!-- MAIN END -->

<?php
include_once('footer.php');
?>
