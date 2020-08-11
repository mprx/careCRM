<?php
$siteTitle = "Hausbesuche";
$siteCategory = "Pflegeplan";

include_once('header.php');
include_once('nav.php');
?>
<!-- MAIN START -->
<?php
$dbc = mysqli_connect(DB_HOST,DB_USER,DB_PASSWORD, DB_NAME)
or die(errrorlog($dbc,0));
mysqli_set_charset($dbc,"utf8");




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
    <form method="GET" action="<?php echo $_SERVER["PHP_SELF"]; ?>" id="pp_jahr_select">
        <div class="form-group row">
            <div class="col-sm-2 text-left">
                <label for="lg_jahr" class="col-5 col-form-label col-form-label-sm">Jahr/Monat:</label>
            </div>
            <div class="text-right">
                <select class="form-control form-control-sm" name="pp_jahr" id="pp_jahr" onchange="document.getElementById('pp_jahr_select').submit()">
                    <?php
                    for($i=2020;$i<=(date("Y",time())+1);$i++)
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

    $query = "SELECT kunde.kd_id, kunde.kd_vorname, kunde.kd_nachname, adressen.ad_strasse, adressen.ad_nr, adressen.ad_stiege, adressen.ad_stock, adressen.ad_tuer, adressen.ad_plz FROM kunde, adressen WHERE kunde.kd_aktiv = 1 AND adressen.kd_id = kunde.kd_id AND adressen.ad_typ IN('rb','b') AND  adressen.ad_aktiv = 1";
    $data = mysqli_query($dbc,$query)
    or die(errorlog($dbc,$query));

    ?>

    <table class="table table-striped table-sm">
        <thead>
        <tr>
            <th scope="col">Kunde</th>
            <th scope="col">Hausbesuche</th>
            <th scope="col" class="text-center">Neuer HB</th>
        </tr>
        </thead>
        <tbody>
        <?php
        while($row = mysqli_fetch_array($data))
        {
            $query2 = "SELECT pflegeplan.pp_id, DATE_FORMAT(pflegeplan.pp_beginn,'%d.%m.%Y') AS pp_beginn, pfleger.pg_id FROM pfleger, pflegeplan WHERE pflegeplan.kd_id = " . $row["kd_id"] . " AND pfleger.pg_art = 'dgks' AND pfleger.pg_id = pflegeplan.pg_id AND YEAR(pflegeplan.pp_beginn) = $pp_jahr AND MONTH(pflegeplan.pp_beginn) = $pp_monat ORDER BY pflegeplan.pp_beginn LIMIT 3";
            $data2 = mysqli_query($dbc, $query2)
                or die(errorlog($dbc, $query2));


            $trclass = "";
            $trtext = "";
            if(mysqli_num_rows($data2) == 0)
            {
                $trclass = "class='table-danger'";
                $trtext = "keine";
            }

            echo "<tr $trclass>";
            echo '<td scope="row"><a href="kddetails.php?kd_id=' . $row["kd_id"] . '">' . $row["kd_vorname"] . " " . $row["kd_nachname"] . ", " . $row["ad_strasse"] . " " . $row["ad_nr"] . addressseperation($row["ad_stiege"]) . addressseperation($row["ad_stock"]) . addressseperation($row["ad_tuer"]) . ", " . $row["ad_plz"] . '</a></td>';


            echo '<td scope="row">' . $trtext;

            while($row2 = mysqli_fetch_array($data2))
            {
                echo '<a class="mr-3" href="ppdetails.php?pp_id=' . $row2["pp_id"] . '">'. $row2["pp_beginn"] . '</a>';
            }

            echo '</td>';
            echo '<td scope="row" class="text-center"><a href="ppneu.php?kd_id=' . $row["kd_id"] . '&hb=1">' . '<img src="images/plus.png">' . '</a></td>';
            echo '<tr>';
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