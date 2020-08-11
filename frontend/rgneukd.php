<?php
$siteTitle = "Rechnung für Kunde";
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
    if($_POST["rg_ad"] == "kd")
    {
        $rg_ad = mysqli_real_escape_string($dbc, trim($_POST['rg_ad_kd']));
        $rg_ad_id = mysqli_real_escape_string($dbc, trim($_POST['rg_ad_id_kd']));
    }
    elseif($_POST["rg_ad"] == "sw")
    {
        $rg_ad = mysqli_real_escape_string($dbc, trim($_POST['rg_ad_sw']));
        $rg_ad_id = mysqli_real_escape_string($dbc, trim($_POST['rg_ad_id_sw']));
    }
    else
    {
         ?>
        <div class="alert alert-danger">
        Bitte eine Anschrift auswählen!
        </div>
        <?php
       $error_step2 = 1;
    }

    $lg_gesamt = mysqli_real_escape_string($dbc, trim($_POST['lg_gesamtzahl']));

    $ad_leistungen = array();
    if($lg_gesamt == 0)
    {
        ?>
        <div class="alert alert-danger">
        Der Kunde hat keine Leistungen!
        </div>
        <?php
        $error_step2 = 1;
    }
    else
    {
        $rg_leistungen = array();
        for($i=1; $i <= $lg_gesamt; $i++)
        {
            if(isset($_POST["$i"]))
            {
                $rg_leistungen[$_POST["$i"]] = $_POST["anzahl_$i"];
                if(!isset($_POST["anzahl_$i"]) || $_POST["anzahl_$i"] == "" || $_POST["anzahl_$i"] == 0) $rg_leistung_leer = 1;
            }

        }

        if(sizeof($rg_leistungen) <= 0)
        {
            ?>
            <div class="alert alert-danger">
            Bitte mindestens eine Leistung zur Verrechnung auswählen!
            </div>
            <?php
            $error_step2 = 1;
        }

        if(isset($rg_leistung_leer))
        {
            ?>
            <div class="alert alert-danger">
            Ungültige Anzahl bei mindestesns einer Leistung!
            </div>
            <?php
            $error_step2 = 1;
        }
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
            mysqli_begin_transaction($dbc);
            $query = "INSERT INTO rechnung" .
                     "(kd_id, us_id, ad_id, rg_jahr, rg_nr, rg_anschrift, rg_zeitraum_start, rg_zeitraum_ende, rg_datum, rg_art) " .
                     "SELECT '$kd_id', '" . $_SESSION["userid"] . "','$rg_ad_id'" . ", '$rg_jahr', case when min(abs(rg_nr)) IS null then 1 else max(rg_nr)+1 end, '$rg_ad', '$rg_zeitraum_start', '$rg_zeitraum_ende', '$rg_datum', '$rg_art' FROM rechnung WHERE rg_jahr = '$rg_jahr'";
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
        if(!isset($error_step2))
        {
            ?>
            <div class="alert alert-danger">
            Bitte alle notwendigen Felder ausfüllen!
            </div>
            <?php
            $error_step2 = 1;
        }
    }




}


//Zweiter Schritt:
if(isset($_POST["submit"]) || isset($error_step2) || isset($_GET["kd_id"]))
{
    if(isset($_GET["kd_id"])) $kd_id = mysqli_real_escape_string($dbc, trim($_GET['kd_id']));
    if(isset($_POST["kd_id"])) $kd_id = mysqli_real_escape_string($dbc, trim($_POST['kd_id']));

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
                $kd_name = $kd_row["kd_anrede"] . " ";
                if(!empty($kd_row["kd_titel"])) $kd_name = $kd_row["kd_titel"] . " ";
                $kd_name = $kd_name . $kd_row["kd_vorname"] . " " . $kd_row["kd_nachname"];

                echo $kd_name;
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

                <div class="col-auto mr-5 ml-4">
                    <input class="form-check-input" type="radio" name="rg_ad" id="rg_ad_kd" value="kd" checked >
                    <input type="number" name="rg_ad_id_kd" id="rg_ad_id_kd" value="<?php echo $kd_row["ad_id"]; ?>" hidden />
                    <textarea name="rg_ad_kd" hidden><?php echo $kd_name . "\n" . $kd_ad ?></textarea>
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
                ?>
    </div>


    <div class="row">
        <div class="col border border-primary">
            Leistung
        </div>
    </div>
    <?php

    $lg_query = "SELECT * FROM leistung, bezug WHERE leistung.lg_id = bezug.lg_id AND bezug.kd_id = '$kd_id'";
    $lg_data = mysqli_query($dbc,$lg_query)
        or die(errorlog($dbc,$lg_query));

    ?>

        <?php
        if(mysqli_num_rows($lg_data) == 0)
        {
            $no_leistungen = 1;
            ?>
            <div class="alert alert-danger">
                Keine Leistungen für diesen Kunden vorhanden!<br>
                Bitte <a href="kddetails4.php?kd_id=<?php echo $kd_id; ?>">fügen Sie Leistungen hinzu</a>.
            </div>
            <?php
        }
        else
        {
            ?>
                <table class="table table-striped table-sm">
                <thead>
                <tr>
                    <th scope="col">Bezeichnung</th>
                    <th scope="col">Einheit</th>
                    <th scope="col">Einh. Preis</th>

                    <th scope="col">verrechnet<br>bis</th>
                    <th scope="col">verr.</th>
                    <th scope="col">Anzahl</th>
                </tr>
                </thead>
                <tbody>

                <?php
                $i = 0;
            while($lg_row = mysqli_fetch_array($lg_data))
            {
                $i++;
                $lg_ids[] = $lg_row["lg_id"];
                switch($lg_row["lg_einheit"])
                {
                    case "h":
                        $lg_einheit = "Stunde";
                        break;
                    case "m":
                        $lg_einheit = "Monat";
                        break;
                    case "d":
                        $lg_einheit = "Tag";
                        break;
                    case "w":
                        $lg_einheit = "Woche";
                        break;
                    case "b":
                        $lg_einheit = "Besuch";
                        break;
                    case "s":
                        $lg_einheit = "Stück";
                        break;
                    case "e":
                        $lg_einheit = "Einheit";
                        break;
                    default:
                        $lg_einheit = "Einheit";
                }
                echo '<tr>';
                echo '<td scope="row">' . $lg_row["lg_bezeichnung"] . ' ('. $lg_row["lg_jahr"] . ')</td>';
                echo '<td scope="row">' . $lg_einheit . '</td>';
                echo '<td>';
                if (!empty($lg_row["lg_einzeltarif"]))
                {
                    echo $lg_row["lg_einzeltarif"] . " €";
                }
                else
                {
                    echo "-";
                }
                echo '</td>';
                echo '<td>';
                $po_query = "SELECT max(DATE_FORMAT(rg_zeitraum_ende,'%d.%m.%Y')) AS datum FROM rechnung, positionen WHERE positionen.lg_id = '" . $lg_row["lg_id"] . "' AND rechnung.rg_id = positionen.rg_id AND rechnung.rg_art = 'r' AND rechnung.kd_id = '$kd_id'";
                $po_data = mysqli_query($dbc,$po_query)
                    or die(errorlog($dbc,$po_query));
                $po_row = mysqli_fetch_array($po_data);

                if($po_row["datum"] == "")
                {
                    echo "nie";
                }
                else
                {

                    echo $po_row["datum"];
                }
                echo '</td>';
                echo '<td>';
                ?>
                <input type="checkbox" checked id="<?php echo $i; ?>" name="<?php echo $i; ?>" value="<?php echo $lg_row["lg_id"]; ?>" onchange="toggleRequired('<?php echo $i; ?>','anzahl_<?php echo $i; ?>')" />
                <?php
                echo '</td>';
                echo '<td>';
                ?>
                <input type="number" class="form-control form-control-sm" step="1" min="1" max="99" id="anzahl_<?php echo $i; ?>" name="anzahl_<?php echo $i; ?>" value="1" />
                <?php
                echo '</td>';
                echo '<tr>';


            }
            ?>
            </tbody>
            </table>
            <input type="number" value="<?php echo $i; ?>" name="lg_gesamtzahl" hidden />
            <?php
        }
        ?>
    <div class="row">
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
else
{


//Daten holen
$query = "SELECT kunde.kd_id, kunde.kd_ende, kunde.kd_anrede, kunde.kd_vorname, kunde.kd_nachname, adressen.ad_strasse, adressen.ad_nr, adressen.ad_stiege, adressen.ad_stock, adressen.ad_tuer, adressen.ad_plz FROM kunde, adressen WHERE kunde.kd_id = adressen.kd_id AND adressen.ad_aktiv = 1 AND adressen.ad_typ IN('rb', 'r')";
$data = mysqli_query($dbc, $query)
    or die(errorlog($dbc, $query));

?>

    <form method="POST" action="<?php echo $_SERVER["PHP_SELF"]; ?>" id="kd_auswahl">
        <div class="form-group row">
            <div class="col-sm-2 text-left">
                <label for="kd_id" class="col-3 col-form-label col-form-label-sm">Kunde:</label>
            </div>
            <div class="text-right">
                <select class="form-control form-control-sm" name="kd_id" id="kd_id">
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
        <div class="col-sm-1 text-left">
        <input type="submit" name="submit" class="btn btn-primary" id="submit" value="Weiter" />
        </div>
    </form>
<!-- MAIN END -->
<?php
}
include_once('footer.php');
?>