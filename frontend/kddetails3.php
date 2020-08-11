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

if(isset($_GET["pp_jahr"]))
{
    $pp_jahr = mysqli_real_escape_string($dbc, trim($_GET['pp_jahr']));
}
else
{
    $pp_jahr = date("Y", time());
}

if(isset($_GET["pp_monat"]))
{
    $pp_monat = mysqli_real_escape_string($dbc, trim($_GET['pp_monat']));
}
else
{
    $pp_monat = date("m", time());
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

<form method="GET" action="<?php echo $_SERVER["PHP_SELF"]; ?>" id="pp_jahr_select">
    <input type="number" name="kd_id" id="kd_id" style="display: none;" value="<?php echo $kd_id; ?>">
    <div class="form-group row">
        <div class="col-sm-2 text-left">
            <label for="lg_jahr" class="col-5 col-form-label col-form-label-sm">Jahr/Monat:</label>
        </div>
            <div class="text-right">
                <select class="form-control form-control-sm" name="pp_jahr" id="pp_jahr" onchange="document.getElementById('pp_jahr_select').submit()">
                    <?php
                    for($i=2010;$i<=2030;$i++)
                    {
                        echo '<option ';
                        if($i == $pp_jahr) echo 'selected';
                        echo '>' . $i;
                        echo '</option>';
                    }
                    ?>
                </select>
            </div>
        <div class="text-left">
            <select class="form-control form-control-sm" name="pp_monat" id="pp_monat" onchange="document.getElementById('pp_jahr_select').submit()">
                <?php
                for($i=1;$i<=12;$i++)
                {
                    echo '<option ';
                    if($i == $pp_monat) echo 'selected';
                    echo '>' . sprintf('%02d', $i);
                    echo '</option>';
                }
                ?>
            </select>
        </div>
    </div>

</form>
    <?php
    $pp_monat1 = $pp_monat + 1;
    $query = "SELECT pflegeplan.pp_id, pfleger.pg_id, CONCAT(CASE DATE_FORMAT(pflegeplan.pp_beginn,'%w') WHEN 0 THEN 'So' WHEN 1 THEN 'Mo' WHEN 2 THEN 'Di' WHEN 3 THEN 'Mi' WHEN 4 THEN 'Do' WHEN 5 THEN 'Fr' WHEN 6 THEN 'Sa' ELSE 'nA' END, DATE_FORMAT(pflegeplan.pp_beginn,' %d.%m.%Y')) AS pp_beginn, CONCAT(CASE DATE_FORMAT(pflegeplan.pp_ende,'%w') WHEN 0 THEN 'So' WHEN 1 THEN 'Mo' WHEN 2 THEN 'Di' WHEN 3 THEN 'Mi' WHEN 4 THEN 'Do' WHEN 5 THEN 'Fr' WHEN 6 THEN 'Sa' ELSE 'nA' END, DATE_FORMAT(pflegeplan.pp_ende,' %d.%m.%Y')) AS pp_ende, pfleger.pg_vorname, pfleger.pg_nachname, pfleger.pg_art FROM pflegeplan, pfleger WHERE kd_id='$kd_id' AND ((YEAR(pflegeplan.pp_beginn) = '$pp_jahr' AND MONTH(pflegeplan.pp_beginn) = '$pp_monat') OR (pflegeplan.pp_beginn < '" . $pp_jahr . "-" . $pp_monat . "-01' AND (pflegeplan.pp_ende IS NULL OR pflegeplan.pp_ende >= '" . $pp_jahr . "-" . $pp_monat . "-01')) OR (MONTH(pflegeplan.pp_ende) = '$pp_monat' AND YEAR(pflegeplan.pp_ende) = '$pp_jahr')) AND pflegeplan.pg_id = pfleger.pg_id ORDER BY pflegeplan.pp_beginn";
    $data = mysqli_query($dbc,$query)
        or die(errorlog($dbc,$query));
    ?>

<table class="table table-striped table-sm">
    <thead>
    <tr>
        <th scope="col">Start</th>
        <th scope="col">Ende</th>
        <th scope="col">Pfleger</th>
        <th scope="col">Art</th>
        <th scope="col">Details</th>
    </tr>
    </thead>
    <tbody>
    <?php
    while($row = mysqli_fetch_array($data))
    {
        switch($row["pg_art"])
        {
            case "sw":
                $pg_art = "Stundenweise";
                break;
            case "24h":
                $pg_art = "24h Betreuung";
                break;
            case "dgks":
                $pg_art = "DGKS/P";
                break;
        }

        echo '<tr>';
        echo '<td scope="row">' . $row["pp_beginn"] . '</td>';
        echo '<td scope="row">' . $row["pp_ende"] . '</td>';
        echo '<td scope="row"><a href="pgdetails.php?pg_id=' . $row["pg_id"] . '">' . $row["pg_vorname"] . " " . $row["pg_nachname"] . '</a></td>';
        echo '<td scope="row">' . $pg_art . '</td>';
        echo '<td scope="row"><a href="ppdetails.php?pp_id=' . $row["pp_id"] . '&kd_id=' . $kd_id . '">Details</a></td>';
        echo '<tr>';
    }
    ?>

    </tbody>
</table>
    <a href="ppneu.php?kd_id=<?php echo $kd_id ?>" class="btn btn-primary mt-0">Neue Tätigkeit hinzufügen</a>

<?php
}
?>
<!-- MAIN END -->

<?php
include_once('footer.php');
?>
