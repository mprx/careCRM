<?php
$siteTitle = "Export - Pflegerplan";
$siteCategory = "Pflegeplan";

include_once('header.php');
include_once('nav.php');
?>
<!-- MAIN START -->
    <ul class="nav nav-tabs">
        <li class="nav-item">
            <a class="nav-link <?php kdTab("ppexport"); ?>" href="ppexport.php">Kundenpläne</a>
        </li>
        <li class="nav-item">
            <a class="nav-link <?php kdTab("ppexport2"); ?>" href="ppexport2.php">Pflegerpläne</a>
        </li>
        <li class="nav-item">
            <a class="nav-link <?php kdTab("ppexport3"); ?>" href="ppexport3.php">Hausbesuche To-Do</a>
        </li>
    </ul>
    <br>

<?php
$dbc = mysqli_connect(DB_HOST,DB_USER,DB_PASSWORD, DB_NAME)
or die(errrorlog($dbc,0));
mysqli_set_charset($dbc,"utf8");

    ?>

    <form method="POST" action="<?php echo $_SERVER["PHP_SELF"]; ?>" id="pp_auswahl">
        <div class="form-group row">

                <label for="pg_id" class="col-1 col-form-label col-form-label-sm">Kunde:</label>

            <div class="text-right">
                <select class="form-control form-control-sm" name="pg_id" id="pg_id" onchange="document.getElementById('submit').click()">
                    <?php

                    $query = "SELECT pfleger.pg_id, pfleger.pg_anrede, pfleger.pg_vorname, pfleger.pg_nachname, adressen.ad_strasse, adressen.ad_nr, adressen.ad_stiege, adressen.ad_stock, adressen.ad_tuer, adressen.ad_plz FROM pfleger, adressen WHERE pfleger.pg_id = adressen.pg_id AND adressen.ad_aktiv = 1 AND adressen.ad_typ != 'r' ORDER BY pfleger.pg_nachname";
                    $data = mysqli_query($dbc, $query)
                    or die(errorlog($dbc, $query));

                    $pg_daten = "";
                    while($row = mysqli_fetch_array($data))
                    {
                        echo '<option value="' . $row["pg_id"] . '"';
                        if(isset($_POST["pg_id"]) && $_POST["pg_id"] == $row["pg_id"])
                        {
                            echo " selected";
                            $pg_daten = $row["pg_id"] . ", ". $row["pg_nachname"] . " " . $row["pg_vorname"];
                        }
                        echo  '>';
                        echo $row["pg_nachname"] . " " . $row["pg_vorname"] . ", " . $row["ad_strasse"] . " " . $row["ad_nr"] . addressseperation($row["ad_stiege"]) . addressseperation($row["ad_stock"]) . addressseperation($row["ad_tuer"]) . ", " . $row["ad_plz"];
                        echo '</option>';
                    }
                    ?>
                </select>
            </div>
        </div>
        <div class="form-group row">

                <label for="pp_von" class="col-1 col-form-label col-form-label-sm">von: </label>

            <div class="text-right">
                <input type="date" min="2000-01-01" max="2200-12-31" class="form-control form-control-sm" name="pp_von" id="pp_von" placeholder="TT.MM.JJJJ" <?php switchpostvariable("pp_von", date("Y-m-",time()) . "01"); ?> onchange="document.getElementById('submit').click()" />
            </div>


                <label for="pp_bis" class="col-1 col-form-label col-form-label-sm">bis:</label>

            <div class="text-right">
                <input type="date" min="2000-01-01" max="2200-12-31" class="form-control form-control-sm" name="pp_bis" id="pp_bis" placeholder="TT.MM.JJJJ" <?php switchpostvariable("pp_bis", date("Y-m-",time()) . cal_days_in_month(CAL_GREGORIAN,date("m",time()),date("Y",time()))); ?> onchange="document.getElementById('submit').click()" />
            </div>

        </div>
        <input type="submit" name="submit" id="submit" value="submit" hidden />
    </form>
    <?php

if(isset($_POST["pg_id"]))
{
    $pg_id = mysqli_real_escape_string($dbc, trim($_POST['pg_id']));
    $pp_von = mysqli_real_escape_string($dbc, trim($_POST['pp_von']));
    $pp_bis = mysqli_real_escape_string($dbc, trim($_POST['pp_bis']));

    $query = "SELECT pflegeplan.pp_id, kunde.kd_id, DATE_FORMAT(pflegeplan.pp_beginn,' %d.%m.%Y') AS pp_beginn, DATE_FORMAT(pflegeplan.pp_ende,' %d.%m.%Y') AS pp_ende, kunde.kd_vorname, kunde.kd_nachname FROM pflegeplan, kunde WHERE pflegeplan.pg_id='$pg_id' AND ((pflegeplan.pp_beginn BETWEEN '$pp_von' AND '$pp_bis') OR (pflegeplan.pp_beginn < '$pp_von' AND (pflegeplan.pp_ende IS NULL OR pflegeplan.pp_ende > '$pp_bis')) OR (pflegeplan.pp_ende BETWEEN '$pp_von' AND '$pp_bis')) AND pflegeplan.kd_id = kunde.kd_id ORDER BY pflegeplan.pp_beginn";
    $data = mysqli_query($dbc,$query)
    or die(errorlog($dbc,$query));
    ?>

    <table class="table table-striped table-sm">
        <thead>
        <tr>
            <th scope="col">Start</th>
            <th scope="col">Ende</th>
            <th scope="col">Kunde</th>

        </tr>
        </thead>
        <tbody>
        <?php
        list($pp_von_jahr,$pp_von_monat,$pp_von_tag)=explode("-", $pp_von);
        list($pp_bis_jahr,$pp_bis_monat,$pp_bis_tag)=explode("-", $pp_bis);
        $file_inhalt = "Pfleger:;$pg_daten;Einsätze von;$pp_von_tag.$pp_von_monat.$pp_von_jahr;bis;$pp_bis_tag.$pp_bis_monat.$pp_bis_jahr\nKdNr;Vorname;Nachname;Start;Ende;\n";
        while($row = mysqli_fetch_array($data))
        {

            $file_inhalt = $file_inhalt . $row["kd_id"] . ";" . $row["kd_vorname"] . ";" . $row["kd_nachname"] . ";" . $row["pp_beginn"] . ";" . $row["pp_ende"] . ";\n";


            $pp_ende = $row["pp_ende"];

            if(empty($row["pp_ende"])) $pp_ende = "offen";

            echo '<tr>';
            echo '<td scope="row">' . $row["pp_beginn"] . '</td>';
            echo '<td scope="row">' . $pp_ende . '</td>';
            echo '<td scope="row"><a href="pgdetails.php?pg_id=' . $row["kd_id"] . '">' . $row["kd_vorname"] . " " . $row["kd_nachname"] . '</a></td>';

            echo '<tr>';
        }
        ?>

        </tbody>
    </table>
    <form action="<?php echo "pp_export.php"; ?>" method="post">
        <input type="text" name="pg_daten" value="<?php echo $pg_daten; ?>" hidden>
        <input type="text" name="von" value="<?php echo $pp_von; ?>" hidden>
        <input type="text" name="bis" value="<?php echo $pp_bis; ?>" hidden>
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