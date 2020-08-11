<?php
$siteTitle = "Rechnung Manuell";
$siteCategory = "Neue Rechnung";

include_once('header.php');
include_once('nav.php');
?>
<!-- MAIN START -->
<?php

$dbc = mysqli_connect(DB_HOST,DB_USER,DB_PASSWORD, DB_NAME)
or die(errrorlog($dbc,0));
mysqli_set_charset($dbc,"utf8");

//Dritter Schritt:
if(isset($_POST["submit2"]))
{
    $kd_id = mysqli_real_escape_string($dbc, trim($_POST['kd_id']));
    if(isset($_POST["rg_ad"]) && $_POST["rg_ad"] == "kd")
    {
        $rg_ad = mysqli_real_escape_string($dbc, trim($_POST['rg_ad_kd']));
        $rg_ad_id = mysqli_real_escape_string($dbc, trim($_POST['rg_ad_id_kd']));
    }
    elseif(isset($_POST["rg_ad"]) && $_POST["rg_ad"] == "sw")
    {
        $rg_ad = mysqli_real_escape_string($dbc, trim($_POST['rg_ad_sw']));
        $rg_ad_id = mysqli_real_escape_string($dbc, trim($_POST['rg_ad_id_sw']));
    }
    else
    {
        $rg_ad = mysqli_real_escape_string($dbc, trim($_POST['rg_ad_kd']));
        $rg_ad_id = "NULL";
        $kd_id = "NULL";

        if(empty($rg_ad))
        {
            ?>
            <div class="alert alert-danger">
            Bitte eine Anschrift auswählen!
            </div>
            <?php
            $error_step2 = 1;
        }
    }

    if(!isset($error_step2) && !empty($_POST["lg_bezeichnung_1"]) && !empty($_POST["lg_preis_1"]) && $_POST["lg_preis_1"] !=0 && !empty($_POST["lg_anzahl_1"]) && $_POST["lg_anzahl_1"] !=0)
    {
        mysqli_begin_transaction($dbc);
        $rg_leistungen = array();
        for($i = 1;$i <= 10; $i++)
        {
            if(!empty($_POST["lg_bezeichnung_$i"]) && !empty($_POST["lg_preis_$i"]) && $_POST["lg_preis_$i"] !=0 && !empty($_POST["lg_anzahl_$i"]) && $_POST["lg_anzahl_$i"] !=0)
            {

                $query = "INSERT INTO leistung " .
                        "(lg_bezeichnung, lg_einheit, lg_einzeltarif, lg_jahr) ".
                        "VALUES('" . $_POST["lg_bezeichnung_$i"] . "', 'e', '" . $_POST["lg_preis_$i"] . "', '0000')";
                mysqli_query($dbc,$query)
                    or die(errorlog($dbc,$query));

                $lg_id_last = mysqli_insert_id($dbc);

                $rg_leistungen[$lg_id_last] = $_POST["lg_anzahl_$i"];
            }
        }
    }
    else
    {
         ?>
        <div class="alert alert-danger">
        Bitte zumindest die erste Leistung vollständig eintragen!
        </div>
        <?php
        $error_step2 = 1;
    }



    $rg_zeitraum_start = mysqli_real_escape_string($dbc, trim($_POST['rg_zeitraum_start']));
    $rg_zeitraum_ende = mysqli_real_escape_string($dbc, trim($_POST['rg_zeitraum_ende']));

    $rg_jahr = date("Y",time());
    $rg_datum = date("Y-m-d", time());
    $rg_art = "r";

    if(!empty($kd_id) && !empty($rg_zeitraum_start) && !empty($rg_zeitraum_ende) && !isset($error_step2))
    {
        if($rg_zeitraum_start <= $rg_zeitraum_ende)
        {

            $query = "INSERT INTO rechnung" .
                     "(kd_id, us_id, ad_id, rg_jahr, rg_nr, rg_anschrift, rg_zeitraum_start, rg_zeitraum_ende, rg_datum, rg_art) " .
                     "SELECT $kd_id, '" . $_SESSION["userid"] . "',$rg_ad_id" . ", '$rg_jahr', case when min(abs(rg_nr)) IS null then 1 else max(rg_nr)+1 end, '$rg_ad', '$rg_zeitraum_start', '$rg_zeitraum_ende', '$rg_datum', '$rg_art' FROM rechnung WHERE rg_jahr = '$rg_jahr'";
            mysqli_query($dbc,$query)
                or die(errorlog($dbc,$query));

            $rg_id = mysqli_insert_id($dbc);

            $i = 1;
            foreach($rg_leistungen as $lg_id => $po_anzahl)
            {
                $query = "INSERT INTO positionen" .
                        "(lg_id, rg_id, po_nr, po_anzahl) " .
                        "VALUES('$lg_id', '$rg_id', '$i', '$po_anzahl')";
                mysqli_query($dbc, $query)
                    or die(errorlog($dbc,$query));

                $i++;
            }

            mysqli_commit($dbc);



            //Erfolg
            ?>
            <div class="alert alert-success">
            Die Rechnung wurde erfolgreich erstellt.
            </div>
            <a href="rgstorno.php?rg_id=<?php echo $rg_id; ?>">Details</a><br>
            <a target="_blank" href="rechnung.php?rg_id=<?php echo $rg_id; ?>">als PDF öffnen</a>
            <?php
            exit();
        }
        else
        {
             ?>
            <div class="alert alert-danger">
            Leistung-Ende kann nicht vor Leistung-Beginn liegen!
            </div>
            <?php
            $error_step2 = 1;
        }
    }
    else
    {
        ?>
        <div class="alert alert-danger">
        Bitte alle notwendigen Felder ausfüllen!
        </div>
        <?php
        $error_step2 = 1;
    }




}


//Zweiter Schritt:
if(isset($_POST["submit"]) || isset($error_step2))
{
    $kd_id = mysqli_real_escape_string($dbc, trim($_POST['kd_id']));

    if($kd_id == 0)
    {
        $rg_name = mysqli_real_escape_string($dbc, trim($_POST['rg_name']));
        $rg_anschrift = mysqli_real_escape_string($dbc, trim($_POST['rg_anschrift']));
        $rg_plz = mysqli_real_escape_string($dbc, trim($_POST['rg_plz']));


        //Kein Kunde ausgewählt aber Adresse manuell ausgefüllt
        if(!empty($rg_name) && !empty($rg_anschrift) && !empty($rg_plz))
        {
            $rg_anschrift = $rg_anschrift . "\n" . $rg_plz;
        }
        else
        {
            ?>
             <div class="alert alert-danger">
            Bitte entweder einen Kunden auswählen oder manuell Name und Adresse eingeben!
            </div>
            <?php
            $error_step1 = 1;
        }

    }
    else
    {
        $kd_query = "SELECT * FROM kunde, adressen, plz WHERE kunde.kd_id = '$kd_id' AND adressen.kd_id = '$kd_id' AND adressen.ad_aktiv = 1 AND adressen.ad_typ IN ('rb', 'r') AND plz.plz = adressen.ad_plz";
        $kd_data = mysqli_query($dbc, $kd_query)
            or die(errorlog($dbc, $kd_query));
        $kd_row = mysqli_fetch_array($kd_data);
        $sw_query = "SELECT * FROM sachwalter, angehoerige, adressen, plz WHERE angehoerige.ag_aktiv = 1 AND angehoerige.kd_id = '$kd_id' AND sachwalter.sw_id = angehoerige.sw_id AND sachwalter.sw_typ = 'sw' AND adressen.ad_aktiv = 1 AND adressen.ad_typ IN ('rb', 'r') AND adressen.sw_id = sachwalter.sw_id AND adressen.sw_id = angehoerige.sw_id AND plz.plz = adressen.ad_plz";
        $sw_data = mysqli_query($dbc, $sw_query)
            or die(errorlog($dbc, $sw_query));
        $kd_sw = 1;
        if(mysqli_num_rows($sw_data) == 0) $kd_sw = 0;
        $sw_row = mysqli_fetch_array($sw_data);

        $kd_ad = $kd_row["ad_strasse"] . " " . $kd_row["ad_nr"] . addressseperation($kd_row["ad_stiege"]) . addressseperation($kd_row["ad_stock"]) . addressseperation($kd_row["ad_tuer"]) . "\n" . $kd_row["ad_plz"] . " " . $kd_row["ort"];
        if($kd_sw == 1) $sw_ad = $sw_row["ad_strasse"] . " " . $sw_row["ad_nr"] . addressseperation($sw_row["ad_stiege"]) . addressseperation($sw_row["ad_stock"]) . addressseperation($sw_row["ad_tuer"]) . "\n" . $sw_row["ad_plz"] . " " . $sw_row["ort"];

    }

    if(!isset($error_step1))
    {
        ?>
    <form action="<?php echo $_SERVER["PHP_SELF"]; ?>" method="post" >
         <div class="row">
          <div class="col border border-primary">
          Kundendaten
          </div>
        </div>
        <div class="row">
              <div class="col-sm-5">
                <?php
                if($kd_id == 0)
                {
                     echo $rg_name;
                     ?>

                     <textarea name="rg_ad_kd" hidden><?php echo $rg_name . "\n" . $rg_anschrift ?></textarea>
                     <?php
                }
                else
                {
                    $kd_name = $kd_row["kd_anrede"] . " ";
                    if(!empty($kd_row["kd_titel"])) $kd_name = $kd_row["kd_titel"] . " ";
                    $kd_name = $kd_name . $kd_row["kd_vorname"] . " " . $kd_row["kd_nachname"];

                    echo $kd_name;
                    ?>
                    <textarea name="rg_ad_kd" hidden><?php echo $kd_name . "\n" . $kd_ad ?></textarea>
                    <?php
                }

                ?>
              </div>
        </div>
        <input type="number" value="<?php echo $kd_id; ?>" name="kd_id" id="kd_id" hidden />

        <div class="row mt-3">
            <div class="col border border-primary">
                Rechnungsadresse
            </div>
        </div>
        <div class="row">
            <?php
                if($kd_id != 0)
                {
            ?>
                    <div class="col-auto mr-5 ml-4">
                        <input class="form-check-input" type="radio" name="rg_ad" id="rg_ad_kd" value="kd" checked >
                        <input type="number" name="rg_ad_id_kd" id="rg_ad_id_kd" value="<?php echo $kd_row["ad_id"]; ?>" hidden />

                        <label for="rg_ad_kd">
                            (Kunde)<br> <?php echo $kd_name . "<br>" . str_replace("\n","<br>",$kd_ad)  ?>
                        </label>
                    </div>

                    <?php
                    if($kd_sw == 1)
                    {
                        $sw_name = $sw_row["sw_anrede"] . " ";
                        if(!empty($sw_row["sw_titel"])) $sw_name = $sw_row["sw_titel"] . " ";
                        $sw_name = $sw_name . $sw_row["sw_vorname"] . " " . $sw_row["sw_nachname"];
                        ?>

                            <div class="col">
                                <input class="form-check-input" type="radio" name="rg_ad" id="rg_ad_sw" value="sw" >
                                <input type="number" name="rg_ad_id_sw" id="rg_ad_id_sw" value="<?php echo $sw_row["ad_id"]; ?>" hidden />
                                <textarea name="rg_ad_sw" hidden><?php echo $kd_name . "\np.A. " . $sw_name . "\n" . $sw_ad ?></textarea>
                                <label for="rg_ad_sw">
                                    (Sachwalter)<br> <?php echo $kd_name . "<br>p.A. " . $sw_name . "<br>" . str_replace("\n","<br>",$sw_ad) ?>
                                </label>

                            </div>

                        <?php
                    }
                }
                else
                {
                    ?>

                        <div class="col">
                            <?php echo str_replace("\n","<br>",$rg_anschrift) ; ?>
                        </div>

                    <?php
                }
                    ?>
        </div>


        <div class="row mt-3">
            <div class="col border border-primary">
                Positionen
            </div>
        </div>
        <?php
        for($i = 1; $i <= 10; $i++)
        {
            $required = "";
            if($i == 1) $required = " required ";
        ?>
        <div class="row mt-1" id="lg_bez_<?php echo $i; ?>" <?php if($i != 1) echo "style='display: none;'" ?> >
            <div class="col-1">
            <?php echo $i; ?>
            </div>
            <div class="col-5">
                <input type="text" placeholder="Bezeichnung <?php if($i != 1) echo "(optional)" ?>" class="form-control form-control-sm" <?php echo $required; ?> name="lg_bezeichnung_<?php echo $i; ?>" id="lg_bezeichnung_<?php echo $i; ?>" value="<?php if(isset($_POST["lg_bezeichnung_$i"])) echo $_POST["lg_bezeichnung_$i"]; ?>" />
            </div>
            <div class="col-3">
                <input type="number" step="1" placeholder="Anzahl <?php if($i != 1) echo "(optional)" ?>" class="form-control form-control-sm" <?php echo $required; ?> name="lg_anzahl_<?php echo $i; ?>" id="lg_anzahl_<?php echo $i; ?>" value="<?php if(isset($_POST["lg_preis_$i"])) echo $_POST["lg_preis_$i"]; ?>" />
            </div>
            <div class="col-3">
                <input type="number" step="0.01" placeholder="Einzelpreis <?php if($i != 1) echo "(optional)" ?>" class="form-control form-control-sm" <?php echo $required; ?> name="lg_preis_<?php echo $i; ?>" id="lg_preis_<?php echo $i; ?>" value="<?php if(isset($_POST["lg_preis_$i"])) echo $_POST["lg_preis_$i"]; ?>" onkeypress="toggleField('lg_preis_<?php echo $i; ?>','lg_bez_<?php echo $i+1; ?>')" onchange="toggleField('lg_preis_<?php echo $i; ?>','lg_bez_<?php echo $i+1; ?>')" oninput="toggleField('lg_preis_<?php echo $i; ?>','lg_bez_<?php echo $i+1; ?>')" />
            </div>
        </div>
        <?php
        }
        ?>
        <div class="row mt-3">
            <div class="col border border-primary">
                Leistungszeitraum
            </div>
        </div>
        <div class="row mt-1">
            <div class="col-auto">
                <input type="date" min="2000-01-01" max="2200-12-31" class="form-control form-control-sm" required name="rg_zeitraum_start" id="rg_zeitraum_start" value="<?php echo date("Y-m-d", strtotime("first day of previous month")) ?>" />
            </div>
            <div class="col-auto">
                bis
            </div>
            <div class="col-auto">
                <input type="date" min="2000-01-01" max="2200-12-31" class="form-control form-control-sm" required name="rg_zeitraum_ende" id="rg_zeitraum_ende" value="<?php echo date("Y-m-d", strtotime("last day of previous month")) ?>" />
            </div>
        </div>
        <?php
        if(!isset($no_leistungen))
        {
            ?>
            <input type="submit" name="submit2" id="submit2" value="Rechnung erstellen" class="btn btn-primary mt-3" />
            <?php
        }
    }
}

if((!isset($_POST["submit"]) && !isset($_POST["submit2"])) || isset($error_step1))
{


//Daten holen
$query = "SELECT kunde.kd_id, kunde.kd_ende, kunde.kd_anrede, kunde.kd_vorname, kunde.kd_nachname, adressen.ad_strasse, adressen.ad_nr, adressen.ad_stiege, adressen.ad_stock, adressen.ad_tuer, adressen.ad_plz FROM kunde, adressen WHERE kunde.kd_id = adressen.kd_id AND adressen.ad_aktiv = 1 AND adressen.ad_typ IN('rb', 'r')";
$data = mysqli_query($dbc, $query)
    or die(errorlog($dbc, $query));

?>


    <form method="POST" action="<?php echo $_SERVER["PHP_SELF"]; ?>" id="kd_auswahl">
    <div class="row">
      <div class="col border border-primary m-2">
      Name und Anschrift Rechnungsempfänger
      </div>
    </div>
        <div class="form-group row">

                <label for="kd_id" class="col-lg-2 col-sm-3  col-form-label col-form-label-sm">Kunde:</label>

            <div class="text-right col-lg-5 col-sm-9">
                <select class="form-control form-control-sm" name="kd_id" id="kd_id" onchange="toggleFieldInversed('kd_id','rg_anschrift_manuell')">
                    <option value="0">Daten manuell eingeben</option>
                    <?php

                    $query = "SELECT kunde.kd_id, kunde.kd_anrede, kunde.kd_vorname, kunde.kd_nachname, adressen.ad_strasse, adressen.ad_nr, adressen.ad_stiege, adressen.ad_stock, adressen.ad_tuer, adressen.ad_plz FROM kunde, adressen WHERE kunde.kd_id = adressen.kd_id AND adressen.ad_aktiv = 1 AND adressen.ad_typ != 'r' AND kunde.kd_aktiv = 1 ORDER BY kunde.kd_nachname";
                    $data = mysqli_query($dbc, $query)
                    or die(errorlog($dbc, $query));

                    $kd_daten = "";
                    while($row = mysqli_fetch_array($data))
                    {
                        echo '<option value="' . $row["kd_id"] . '"';
                        if(isset($_POST["kd_id"]) && $_POST["kd_id"] == $row["kd_id"])
                        {
                            echo " selected";
                            $kd_daten = $row["kd_id"] . ", ". $row["kd_nachname"] . " " . $row["kd_vorname"];
                        }
                        echo  '>';
                        echo $row["kd_nachname"] . " " . $row["kd_vorname"] . ", " . $row["ad_strasse"] . " " . $row["ad_nr"] . addressseperation($row["ad_stiege"]) . addressseperation($row["ad_stock"]) . addressseperation($row["ad_tuer"]) . ", " . $row["ad_plz"];
                        echo '</option>';
                    }
                    ?>
                </select>
            </div>
        </div>
        <div  id="rg_anschrift_manuell">
            <div class="form-group row mb-n1">
                <label for="rg_name" class="col-lg-2 col-sm-3 col-form-label col-form-label-sm">Name:</label>
                <div class="text-right col-sm-5">
                    <input class="form-control form-control-sm" type="text" name="rg_name" id="rg_name" placeholder="Frau Maria Musterfrau" value="<?php keeppostvalue("rg_name"); ?>" />
                </div>

                    <!--<textarea name="rg_anschrift" id="rg_anschrift" class="form-control form-control-sm" placeholder="Musterstraße 1/2/3&#10;1100 Wien"><?php keeppostvalue("rg_anschrift"); ?></textarea>-->

            </div>
            <div class="form-group row mb-n1">
                <label for="rg_anschrift" class="col-lg-2 col-sm-3 col-form-label col-form-label-sm">Straße + Nr:</label>
                <div class="text-right col-sm-5">
                   <input class="form-control form-control-sm" type="text" name="rg_anschrift" id="rg_anschrift" placeholder="Musterstraße 1/2/3" value="<?php keeppostvalue("rg_anschrift"); ?>" />
                </div>
            </div>
            <div class="form-group row">
                <label for="rg_plz" class="col-lg-2 col-sm-3 col-form-label col-form-label-sm">PLZ Ort:</label>
                <div class="text-right col-sm-5">
                       <input class="form-control form-control-sm" type="text" name="rg_plz" id="rg_plz" placeholder="1110 Wien" value="<?php keeppostvalue("rg_plz"); ?>" />
                </div>
             </div>
        </div>

        <div class="col-sm-1 text-left">
        <input type="submit" name="submit" class="btn btn-primary" id="submit" value="Weiter" />
        </div>
    </form>
<!-- MAIN END -->
<?php
}
include_once('footer.php');
?>