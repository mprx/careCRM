<?php
$siteTitle = "Kundendetails";
$siteCategory = "Kunden";

include_once('header.php');
include_once('nav.php');
?>

<!-- MAIN START -->
<?php
$dbc = mysqli_connect(DB_HOST,DB_USER,DB_PASSWORD, DB_NAME)
or die(errrorlog($dbc,0));
mysqli_set_charset($dbc,"utf8");

if (isset($_GET["kd_id"]))
{
    $kd_id = mysqli_real_escape_string($dbc, trim($_GET['kd_id']));
}
else
{
    $kd_id = 0;
}
if ($kd_id == "")
{
    $kd_id = 0;
    $error = 1;
}



if ((isset($error) && $error == 0) || !isset($error))
{
    ?>
    <ul class="nav nav-tabs">
        <li class="nav-item">
            <a class="nav-link <?php kdTab("kddetails"); ?>" href="kddetails.php?kd_id=<?php echo $kd_id ?>">Allgemein</a>
        </li>
        <li class="nav-item">
            <a class="nav-link <?php kdTab("kddetails2"); ?>" href="kddetails2.php?kd_id=<?php echo $kd_id ?>">Vermerke</a>
        </li>
        <li class="nav-item">
            <a class="nav-link <?php kdTab("kddetails3"); ?>" href="kddetails3.php?kd_id=<?php echo $kd_id ?>">Pflegeplan</a>
        </li>
        <li class="nav-item">
            <a class="nav-link <?php kdTab("kddetails4"); ?>" href="kddetails4.php?kd_id=<?php echo $kd_id ?>">Tarife</a>
        </li>
        <li class="nav-item">
            <a class="nav-link <?php kdTab("kddetails5"); ?>" href="kddetails5.php?kd_id=<?php echo $kd_id ?>">Rechnungen</a>
        </li>
    </ul>
    <br>
<?php
    //aus der db die min und max werte der vorhandenen Jahre holen
    $scopequery = "SELECT MAX(rg_jahr) as rg_jahr_max, MIN(rg_jahr) as rg_jahr_min FROM rechnung WHERE kd_id = '$kd_id' ORDER BY rg_jahr";
    $scopedata = mysqli_query($dbc, $scopequery)
    or die(errrorlog($dbc, $scopequery));
    $scoperow = mysqli_fetch_array($scopedata);

    if(!empty($scoperow["rg_jahr_min"]))
    {
        $rg_jahr_min = $scoperow["rg_jahr_min"];
        $rg_jahr_max = $scoperow["rg_jahr_max"];
        $rg_keine_daten = 0;
    }
    else
    {
        $rg_jahr_min = date("Y",time());
        $rg_jahr_max = date("Y",time());
        $rg_keine_daten = 1;
    }

    //Jahr definieren
    if (isset($_GET["rg_jahr"]))
    {
        $rg_jahr = mysqli_real_escape_string($dbc, trim($_GET['rg_jahr']));
        //Wenn datum keine Zahl ist und nicht 4 stellen lang ist auf max jahr setzen
        if(!is_numeric($rg_jahr) || (strlen($rg_jahr) != 4))
        {
            $rg_jahr = $rg_jahr_max;
        }
    }
    else
    {
        $rg_jahr = $rg_jahr_max;
    }

    //verfübare Jahre für das Select holen:
    $selectquery = "SELECT DISTINCT rg_jahr FROM rechnung WHERE kd_id = '$kd_id' ORDER BY rg_jahr";
    $selectdata = mysqli_query($dbc, $selectquery)
    or die(errorlog($dbc, $selectquery));

    if(mysqli_num_rows($selectdata) > 0)
    {
        //Daten holen
        $query = "SELECT *, DATE_FORMAT(rg_datum,'%d.%m.%Y') AS rg_datum1 FROM rechnung WHERE kd_id = '$kd_id' AND rg_jahr = '$rg_jahr' ORDER BY rg_nr DESC, rg_art DESC";
        $data = mysqli_query($dbc, $query)
        or die(errorlog($dbc, $query));

        $rg_keine_daten_jahr = 0;
    }
    else
    {
        $rg_keine_daten_jahr = 1;
    }

    ?>
    <form method="GET" action="<?php echo $_SERVER["PHP_SELF"]; ?>" id="rg_jahr_select">
        <div class="form-group row">

                <label for="rg_jahr" class="col-1 col-form-label col-form-label-sm">Jahr:</label>

            <div class="col-sm-3 col-lg-2">
                <select class="form-control form-control-sm" name="rg_jahr" id="rg_jahr" onchange="document.getElementById('rg_jahr_select').submit()">
                    <?php
                    while($selectrow = mysqli_fetch_array($selectdata))
                    {
                        echo '<option ';
                        if($selectrow["rg_jahr"] == $rg_jahr) echo 'selected';
                        echo '>' . $selectrow["rg_jahr"];
                        echo '</option>';
                    }
                    ?>
                </select>
            </div>
            <div class="col-auto">
                <a href="rgneukd.php?kd_id=<?php echo $kd_id ?>">Neue Rechnung für diesen Kunden erstellen</a>
            </div>
        </div>
<input type="number" name="kd_id" value="<?php echo $kd_id; ?>" hidden />
    </form>
    <div class="col border border-primary mt-4">
        Rechnungen:
    </div>
    <table class="table table-striped table-sm">
        <thead>
        <tr>
            <th scope="col">Nr</th>
            <th scope="col">Datum</th>
            <th scope="col">Pos</th>
            <th scope="col">Summe (€)</th>
            <th scope="col">PDF</th>
            <th scope="col">Details</th>
        </tr>
        </thead>
        <tbody>

        <?php
        while($row = mysqli_fetch_array($data))
        {
            $query2 = "SELECT * FROM leistung, positionen WHERE positionen.rg_id = '" . $row["rg_id"] . "' AND positionen.lg_id = leistung.lg_id ORDER BY positionen.po_nr";
            $data2 = mysqli_query($dbc, $query2)
            or die(errorlog($dbc,$query2));

            //Positionen und Summe
            $positionen = 0;
            $summe = 0;
            while($row2 = mysqli_fetch_array($data2))
            {
                $positionen = $row2["po_nr"];
                $lg_rabattgrenze = $row2["lg_rabattgrenze"];
                $lg_einzeltarif = $row2["lg_einzeltarif"];
                $lg_mengentarif = $row2["lg_mengentarif"];
                $po_anzahl = $row2["po_anzahl"];

                $preis = $lg_einzeltarif;
                if($po_anzahl >= $lg_rabattgrenze && !empty($lg_rabattgrenze)) $preis = $lg_mengentarif;
                $gesamt = round($preis*$po_anzahl,2);
                $summe = $summe + $gesamt;

            }
            echo "<tr>";

            //Rg Nr
            echo "<td>";
            if($summe < 0) echo "GS-";
            echo str_pad($row["rg_nr"], 4, '0', STR_PAD_LEFT) . "-" . $row["rg_jahr"];
            echo "</td>";

            //rg_datum
            echo "<td>";
            echo $row["rg_datum1"];
            echo "</td>";


            //positionen
            echo "<td>";
            echo $positionen;
            echo "</td>";

            //summe
            echo "<td>";
            echo number_format($summe, 2, ',', '.');
            echo "</td>";

            //pdf
            echo "<td>";
            ?><a target="_blank" href="rechnung.php?rg_id=<?php echo $row["rg_id"]; ?>">PDF</a><?php
            echo "</td>";

            //storno
            echo "<td>";
            ?><a href="rgstorno.php?rg_id=<?php echo $row["rg_id"]; ?>">details</a><?php
            echo "</td>";

            echo "</tr>";
        }

        ?>

        </tbody>
    </table>

    <?php
}
?>
<!-- MAIN END -->

<?php
include_once('footer.php');
?>
