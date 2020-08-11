<?php
$siteTitle = "Pflegerdetails";
$siteCategory = "Pfleger";

include_once('header.php');
include_once('nav.php');
?>

<!-- MAIN START -->
<?php
$dbc = mysqli_connect(DB_HOST,DB_USER,DB_PASSWORD, DB_NAME)
or die(errrorlog($dbc,0));
mysqli_set_charset($dbc,"utf8");

if (isset($_GET["pg_id"]))
{
    $pg_id = mysqli_real_escape_string($dbc, trim($_GET['pg_id']));
}
else
{
    $pg_id = 0;
}
if ($pg_id == "")
{
    $pg_id = 0;
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
            <a class="nav-link <?php kdTab("pgdetails"); ?>" href="pgdetails.php?pg_id=<?php echo $pg_id ?>">Allgemein</a>
        </li>
        <li class="nav-item">
            <a class="nav-link <?php kdTab("pgdetails2"); ?>" href="pgdetails2.php?pg_id=<?php echo $pg_id ?>">Vermerke</a>
        </li>
        <li class="nav-item">
            <a class="nav-link <?php kdTab("pgdetails3"); ?>" href="pgdetails3.php?pg_id=<?php echo $pg_id ?>">Pflegeplan</a>
        </li>
    </ul>
    <br>

<form method="GET" action="<?php echo $_SERVER["PHP_SELF"]; ?>" id="pp_jahr_select">
    <input type="number" name="pg_id" id="pg_id" style="display: none;" value="<?php echo $pg_id; ?>">
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
    $query = "SELECT pflegeplan.pp_id, kunde.kd_id, CONCAT(CASE DATE_FORMAT(pflegeplan.pp_beginn,'%w') WHEN 0 THEN 'So' WHEN 1 THEN 'Mo' WHEN 2 THEN 'Di' WHEN 3 THEN 'Mi' WHEN 4 THEN 'Do' WHEN 5 THEN 'Fr' WHEN 6 THEN 'Sa' ELSE 'nA' END, DATE_FORMAT(pflegeplan.pp_beginn,' %d.%m.%Y')) AS pp_beginn, CONCAT(CASE DATE_FORMAT(pflegeplan.pp_ende,'%w') WHEN 0 THEN 'So' WHEN 1 THEN 'Mo' WHEN 2 THEN 'Di' WHEN 3 THEN 'Mi' WHEN 4 THEN 'Do' WHEN 5 THEN 'Fr' WHEN 6 THEN 'Sa' ELSE 'nA' END, DATE_FORMAT(pflegeplan.pp_ende,' %d.%m.%Y')) AS pp_ende, kunde.kd_vorname, kunde.kd_nachname FROM pflegeplan, kunde WHERE pg_id='$pg_id' AND ((YEAR(pflegeplan.pp_beginn) = '$pp_jahr' AND MONTH(pflegeplan.pp_beginn) = '$pp_monat') OR (pflegeplan.pp_beginn < '" . $pp_jahr . "-" . $pp_monat . "-01' AND (pflegeplan.pp_ende IS NULL OR pflegeplan.pp_ende >= '" . $pp_jahr . "-" . $pp_monat . "-01')) OR (MONTH(pflegeplan.pp_ende) = '$pp_monat' AND YEAR(pflegeplan.pp_ende) = '$pp_jahr')) AND pflegeplan.kd_id = kunde.kd_id ORDER BY pflegeplan.pp_beginn";
    $data = mysqli_query($dbc,$query)
        or die(errorlog($dbc,$query));

    ?>

<table class="table table-striped table-sm">
    <thead>
    <tr>
        <th scope="col">Start</th>
        <th scope="col">Ende</th>
        <th scope="col">Kunde</th>
        <th scope="col">Details</th>
    </tr>
    </thead>
    <tbody>
    <?php
    while($row = mysqli_fetch_array($data))
    {

        echo '<tr>';
        echo '<td scope="row">' . $row["pp_beginn"] . '</td>';
        echo '<td scope="row">' . $row["pp_ende"] . '</td>';
        echo '<td scope="row"><a href="kddetails.php?kd_id=' . $row["kd_id"] . '">' . $row["kd_vorname"] . " " . $row["kd_nachname"] . '</a></td>';
        echo '<td scope="row"><a href="ppdetails.php?pp_id=' . $row["pp_id"] . '&pg_id=' . $pg_id . '">Details</a></td>';
        echo '<tr>';
    }
    ?>

    </tbody>
</table>
    <a href="ppneu.php?pg_id=<?php echo $pg_id ?>" class="btn btn-primary mt-0">Neue Tätigkeit hinzufügen</a>

<?php
}
?>
<!-- MAIN END -->

<?php
include_once('footer.php');
?>
