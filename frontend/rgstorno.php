<?php
$siteTitle = "Details";
$siteCategory = "Rechnungen";

include_once('header.php');
include_once('nav.php');
?>

<!-- MAIN START -->
<?php
$dbc = mysqli_connect(DB_HOST,DB_USER,DB_PASSWORD, DB_NAME)
or die(errrorlog($dbc,0));
mysqli_set_charset($dbc,"utf8");


if (isset($_GET["rg_id"]))
{
    $rg_id = mysqli_real_escape_string($dbc, trim($_GET['rg_id']));
}
else
{
    $rg_id = 0;
    $error = 1;
}

//Wenn kein GET gesetzt ist, sw_id auf 0 setzen und error anzeigen (unten im file)
if ($rg_id == "")
{
    $rg_id = 0;
    $error = 1;
}

//prüfen, ob rg existiert
$query = "SELECT rg_id FROM rechnung WHERE rg_id = '$rg_id' LIMIT 1";
$data = mysqli_query($dbc, $query)
or die(errorlog($dbc,query));

//wenn rg nicht existiert
if (empty(mysqli_fetch_array($data)))
{
    $error = 1;
    $rg_exist = 0;
}

if (((isset($error) && $error == 0) || !isset($error)) && isset($_POST["submit"]))
{
    mysqli_begin_transaction($dbc);

    //Rg auf bezahlt setzen
    $query = "UPDATE rechnung SET rg_bezahlt = 1 WHERE rg_id = $rg_id";
    mysqli_query($dbc,$query)
        or die(errorlog($dbc,$query));

    //Rechnung kopieren
    $query = "INSERT INTO rechnung (kd_id, pg_id, ad_id, us_id, rg_jahr, rg_nr, rg_anschrift, rg_zeitraum_start, rg_zeitraum_ende, rg_datum, rg_art, rg_bezahlt, rg_druck)
              SELECT kd_id, pg_id, ad_id, us_id, rg_jahr, rg_nr, rg_anschrift, rg_zeitraum_start, rg_zeitraum_ende, CURRENT_DATE(), 'g', rg_bezahlt, rg_druck FROM rechnung WHERE rg_id = $rg_id";
    mysqli_query($dbc,$query)
        or die(errorlog($dbc,$query));

    $rg_id_neu = mysqli_insert_id($dbc);

    //Positionen kopieren mit anzahl negativ:
    $query = "INSERT INTO positionen(lg_id, rg_id, po_nr, po_anzahl) 
                              SELECT lg_id, $rg_id_neu, po_nr, (-1 * po_anzahl) FROM positionen WHERE rg_id = '$rg_id'";
    mysqli_query($dbc,$query)
        or die(errorlog($dbc, $query));

    mysqli_commit($dbc);

    //Erfolg mitteilen
    ?>
    <div class="alert alert-success">
        Die Gutschrift wurde erstellt.
    </div>
    <a target="_blank" href="rechnung.php?rg_id=<?php echo $rg_id; ?>">Rechnung ansehen (PDF)</a>
    <a target="_blank" href="rechnung.php?rg_id=<?php echo $rg_id_neu; ?>">Gutschrift ansehen (PDF)</a>
    <?php

}

if (((isset($error) && $error == 0) || !isset($error)) && !isset($_POST["submit"]))
{
    //Daten der Rechnung holen
    $query = "SELECT kd_id, rg_jahr, rg_nr, rg_anschrift, DATE_FORMAT(rg_zeitraum_start,'%d.%m.%Y') AS rg_zeitraum_start, DATE_FORMAT(rg_zeitraum_ende,'%d.%m.%Y') AS rg_zeitraum_ende, DATE_FORMAT(rg_datum,'%d.%m.%Y') AS rg_datum, rg_art FROM rechnung WHERE rechnung.rg_id = '$rg_id'";
    $data = mysqli_query($dbc,$query)
    or die(erroglo($dbc,$query));
    $row = mysqli_fetch_array($data);


    $kd_id = $row["kd_id"];
    $rg_jahr = $row["rg_jahr"];
    $rg_nr = str_pad($row["rg_nr"], 4, '0', STR_PAD_LEFT);
    if($row["rg_art"] == 'g') $rg_nr = "GS-". $rg_nr;
    $rg_anschrift = $row["rg_anschrift"];
    $rg_zeitraum_start = $row["rg_zeitraum_start"];
    $rg_zeitraum_ende = $row["rg_zeitraum_ende"];
    $rg_zeitraum = $rg_zeitraum_start;
    if($rg_zeitraum_ende != $rg_zeitraum_start) $rg_zeitraum = $rg_zeitraum_start . " bis " . $rg_zeitraum_ende;

    $rg_datum = $row["rg_datum"];
    $rg_anschrift_explode = explode("\n", $rg_anschrift);
    $rg_anschrift_html = "";
    foreach($rg_anschrift_explode  as $value)
    {
        $rg_anschrift_html = $rg_anschrift_html . $value . "<br>";
    }

    $query2 = "SELECT * FROM positionen, leistung WHERE positionen.rg_id = '$rg_id' AND leistung.lg_id = positionen.lg_id ORDER BY positionen.po_nr";
    $data2 = mysqli_query($dbc,$query2)
    or die(erroglo($dbc,$query2));

    $leistung_html = "";
    $rg_gesamtsumme = 0;
    while($row2 = mysqli_fetch_array($data2))
    {
        $lg_bezeichnung = $row2["lg_bezeichnung"];
        $lg_einheit = $row2["lg_einheit"];
        $lg_rabattgrenze = $row2["lg_rabattgrenze"];
        $lg_einzeltarif = $row2["lg_einzeltarif"];
        $lg_mengentarif = $row2["lg_mengentarif"];
        $po_anzahl = $row2["po_anzahl"];

        $preis = $lg_einzeltarif;
        if($po_anzahl >= $lg_rabattgrenze && !empty($lg_rabattgrenze)) $preis = $lg_mengentarif;



        $gesamt = round($preis*$po_anzahl,2);
        $preis = number_format($preis, 2, ',', '.');
        $rg_gesamtsumme = $rg_gesamtsumme + $gesamt;
        $gesamt = number_format($gesamt, 2, ',', '.');


        $leistung_html = $leistung_html . "
                                <tr>
                                <td>$lg_bezeichnung</td>
                                <td align=\"right\">$po_anzahl</td>
                                <td align=\"right\">$lg_einheit</td>
                                <td align=\"right\">$preis</td>
                                <td align=\"right\">$gesamt</td>
                                </tr>
                                ";
    }
    $rg_gesamtsumme = number_format($rg_gesamtsumme, 2, ',', '.');


    $query4 = "SELECT rg_id FROM rechnung WHERE rg_nr = '$rg_nr' AND rg_jahr = '$rg_jahr' AND rg_art = 'g' AND rg_id NOT IN ('$rg_id')";
    $data4 = mysqli_query($dbc,$query4)
    or die(errorlog($dbc,$query4));

    if(mysqli_num_rows($data4) > 0)
    {
        $row4 = mysqli_fetch_array($data4);
        $rg_gutschrift = $row4["rg_id"];
    }


    ?>
    <div class="col border border-primary mt-4">
        Rechnungsdaten:
    </div>
    <div class="row">
        <div class="col-auto">
            Rechnungsnummer:<br>
            <?php
            if($row["rg_art"] == 'g') echo "Gutschrift für:<br>";
            if(isset($rg_gutschrift)) echo "Gutgeschrieben mit:<br>";
            ?>
            Rechnungsdatum:<br>
            Leistungszeitraum:
        </div>
        <div class="col">
            <b><?php echo $rg_nr . "-" . $rg_jahr ?><br>
                <?php
                if($row["rg_art"] == 'g')
                {
                    $query3 = "SELECT rg_id FROM rechnung WHERE rg_nr = '" . substr($rg_nr,3) . "' AND rg_jahr = '$rg_jahr' AND rg_art NOT IN ('g')";
                    $data3 = mysqli_query($dbc,$query3)
                        or die(errorlog($dbc,$query3));
                    $row3 = mysqli_fetch_array($data3);
                    echo "<a href='rgstorno.php?rg_id=" . $row3["rg_id"] . "'>" . substr($rg_nr,3)  . "-$rg_jahr</a><br>";
                }

                if(isset($rg_gutschrift))
                {
                    echo "<a href='rgstorno.php?rg_id=" . $rg_gutschrift . "'>GS-$rg_nr-$rg_jahr</a><br>";
                }

                ?>
            <?php echo $rg_datum ?><br>
            <?php echo $rg_zeitraum ?></b>
        </div>
    </div>
    <div class="col border border-primary mt-4">
        Kunde:
    </div>
    <div class="row">
        <div class="col-auto">
            Kundennummer:<br>
            Rechnungsanschrift:
        </div>
        <div class="col">
            <b>
                <?php

                if(!empty($kd_id))
                {
                    echo "  <a href='kddetails.php?kd_id=" . $kd_id . "'>$kd_id</a>";
                }

                ?>
            <br>
            <?php echo $rg_anschrift_html ?></b>
        </div>
    </div>
    <div class="col border border-primary mt-4">
        Positionen:
    </div>
    <div class="row">
        <div class="col">
            <table class="table table-striped table-sm">
                <tr>
                    <th scope="col" >Bezeichnung</th>
                    <th scope="col" class="text-right">Anzahl</th>
                    <th scope="col" class="text-right">Einheit</th>
                    <th scope="col" class="text-right">Einzelpreis (€)</th>
                    <th scope="col" class="text-right">Gesamt (€)</th>
                </tr>
                <?php echo $leistung_html ?>
                <tr>
                    <td colspan="4" align="right"><b>Gesamt:</b></td>
                    <td align="right"><b><?php echo $rg_gesamtsumme ?></b></td>
                </tr>
            </table>
        </div>
    </div>
<div class="col text-right">

    <form method="POST" action="<?php echo $_SERVER["PHP_SELF"] . "?rg_id=" . $rg_id ?>">
        <a href="rechnung.php?rg_id=<?php echo $rg_id ?>" class="btn btn-primary mt-1">PDF öffnen</a>
        <?php
        if ($rg_gesamtsumme < 0 || isset($rg_gutschrift))
        {
            ?>
            <input type="button" disabled value="Rechnung stornieren" class="btn btn-secondary mt-1" />
            <?php
        }
        else
        {
            ?>
            <input type="submit" name="submit" id="submit" value="Rechnung stornieren" class="btn btn-primary mt-1" />
            <?php
        }
        ?>
        </form>
</div>

<?php

}

?>
<!-- MAIN END -->

<?php
include_once('footer.php');
?>