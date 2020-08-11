<?php
$siteTitle = "Export";
$siteCategory = "Rechnungen";

include_once('header.php');
include_once('nav.php');
?>
<!-- MAIN START -->

<?php
$dbc = mysqli_connect(DB_HOST,DB_USER,DB_PASSWORD, DB_NAME)
or die(errrorlog($dbc,0));
mysqli_set_charset($dbc,"utf8");

    ?>

    <form method="POST" action="<?php echo $_SERVER["PHP_SELF"]; ?>" id="rg_auswahl">
        <div class="col border border-primary mt-4">
            Rechnungsdatum:
        </div>
        <div class="form-group row mt-2">

                <label for="rg_von" class="col-1 col-form-label col-form-label-sm">von: </label>

            <div class="text-right">
                <input type="date" min="2000-01-01" max="2200-12-31" class="form-control form-control-sm" name="rg_von" id="rg_von" placeholder="TT.MM.JJJJ" <?php switchpostvariable("rg_von", date("Y-m-",time()) . "01"); ?> onchange="document.getElementById('submit').click()" />
            </div>


                <label for="rg_bis" class="col-1 col-form-label col-form-label-sm">bis:</label>

            <div class="text-right">
                <input type="date" min="2000-01-01" max="2200-12-31" class="form-control form-control-sm" name="rg_bis" id="rg_bis" placeholder="TT.MM.JJJJ" <?php switchpostvariable("rg_bis", date("Y-m-",time()) . cal_days_in_month(CAL_GREGORIAN,date("m",time()),date("Y",time()))); ?> onchange="document.getElementById('submit').click()" />
            </div>

        </div>
        <?php
        if(!isset($_POST["submit"]))
        {
            ?>
            <input type="submit" name="submit" id="submit" value="Weiter" class="btn btn-primary" />
            <?php
        }
        ?>

    </form>
    <?php

if(isset($_POST["submit"]))
{

    $rg_von = mysqli_real_escape_string($dbc, trim($_POST['rg_von']));
    $rg_bis = mysqli_real_escape_string($dbc, trim($_POST['rg_bis']));

    $query = "SELECT *, DATE_FORMAT(rechnung.rg_datum,'%d.%m.%Y') AS rg_datum1 FROM rechnung WHERE rg_datum BETWEEN '$rg_von' AND '$rg_bis' ORDER BY rg_nr ASC, rg_art ASC";
    $data = mysqli_query($dbc, $query)
    or die(errorlog($dbc, $query));


    $file_inhalt = "Rechnungsnummer;Rechnungsdatum;Kundennummer;Anschrift;Leistung Start;Leistung Ende;Pos Nr;Pos Bezeichnung;Pos Anzahl;Pos Einheit;Pos Einzel;Pos Gesamt;\n";
    while($row = mysqli_fetch_array($data))
    {
        $rg_id = $row["rg_id"];
        $rg_nr = str_pad($row["rg_nr"], 4, '0', STR_PAD_LEFT) . "-" . $row["rg_jahr"];
        if($row["rg_art"] == 'g') $rg_nr = "GS-" . $rg_nr;
        $rg_datum = $row["rg_datum1"];
        $kd_id = $row["kd_id"];
        $rg_anschrift = str_replace("\n",",", $row["rg_anschrift"]);
        $rg_anschrift = str_replace("\r","", $rg_anschrift);
        $rg_zeitraum_start = $row["rg_zeitraum_start"];
        $rg_zeitraum_ende = $row["rg_zeitraum_ende"];


        $query2 = "SELECT * FROM positionen, leistung WHERE positionen.rg_id = '$rg_id' AND leistung.lg_id = positionen.lg_id ORDER BY positionen.po_nr";
        $data2 = mysqli_query($dbc,$query2)
        or die(erroglo($dbc,$query2));

        while($row2 = mysqli_fetch_array($data2))
        {
            $po_nr = $row2["po_nr"];
            $po_bezeichnung = $row2["lg_bezeichnung"];
            $po_anzahl = $row2["po_anzahl"];
            $po_einheit = $row2["lg_einheit"];
            $lg_einzeltarif = $row2["lg_einzeltarif"];
            $lg_rabattgrenze = $row2["lg_rabattgrenze"];
            $lg_mengentarif = $row2["lg_mengentarif"];

            $po_einzel = $lg_einzeltarif;
            if($po_anzahl >= $lg_rabattgrenze && !empty($lg_rabattgrenze)) $po_einzel = $lg_mengentarif;
            $po_gesamt = round($po_einzel*$po_anzahl,2);
            $po_einzel = number_format($po_einzel, 2, ',', '.');
            $po_gesamt = number_format($po_gesamt, 2, ',', '.');

            $file_inhalt = $file_inhalt . "$rg_nr;$rg_datum;$kd_id;$rg_anschrift;$rg_zeitraum_start;$rg_zeitraum_ende;$po_nr;$po_bezeichnung;$po_anzahl;$po_einheit;$po_einzel;$po_gesamt;\n";
        }

    }
    ?>
    <form action="<?php echo "rg_export.php"; ?>" method="post">
        <input type="text" name="von" value="<?php echo $rg_von; ?>" hidden>
        <input type="text" name="bis" value="<?php echo $rg_bis; ?>" hidden>
        <textarea name="file_inhalt" hidden><?php echo $file_inhalt; ?></textarea>
        <input type="submit" class="btn btn-primary" value="Export CSV"/>
    </form>

    <?php
}
?>
<!-- MAIN END -->
<?php
include_once('footer.php');
?>