<?php
$siteTitle = "Pflegerdetails";
$siteCategory = "Pfleger";

include_once('header.php');
include_once('nav.php');
?>

<!-- MAIN START -->
<?php
$dbc = mysqli_connect(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);
mysqli_set_charset($dbc,"utf8");
//pg id definieren
if (isset($_GET["pg_id"]))
{
    $pg_id = mysqli_real_escape_string($dbc, trim($_GET['pg_id']));
}
else
{
    $pg_id = 0;
}

//Wenn kein GET gesetzt ist, pg_id auf 0 setzen und error anzeigen (unten im file)
if ($pg_id == "")
{
    $pg_id = 0;
    $error = 1;
}

//prüfen, ob pg id existiert


$query = "SELECT pg_id FROM pfleger WHERE pg_id = '$pg_id' LIMIT 1";
$data = mysqli_query($dbc, $query)
or die(errorlog($dbc,query));

//wenn pg_nr nicht existiert
if (empty(mysqli_fetch_array($data)))
{
    $error = 1;
    $pg_exist = 0;
}

if ((isset($error) && $error == 0) || !isset($error)) {
    ?>
    <ul class="nav nav-tabs">
        <li class="nav-item">
            <a class="nav-link <?php kdTab("pgdetails"); ?>"
               href="pgdetails.php?pg_id=<?php echo $pg_id ?>">Allgemein</a>
        </li>
        <li class="nav-item">
            <a class="nav-link <?php kdTab("pgdetails2"); ?>"
               href="pgdetails2.php?pg_id=<?php echo $pg_id ?>">Vermerke</a>
        </li>
        <li class="nav-item">
            <a class="nav-link <?php kdTab("pgdetails3"); ?>" href="pgdetails3.php?pg_id=<?php echo $pg_id ?>">Pflegeplan</a>
        </li>
    </ul>
    <br>

    <div class="row">
        <div class="col-sm-8 col-lg-5">

                <table id="vermerke" class="table table-responsive"  style="height: 50vh;">

                    <tr>
                        <td>
                            <div>
                                <!--<div class="p-4" style="height: 50vh; overflow-y: scroll; margin-right: -15px;">-->
                                <table id="vermerktable" class="table table-hover table-sm">
                                    <tr>
                                        <th>Datum</th>
                                        <th>Typ</th>
                                        <th></th>
                                        <th>Von</th>
                                    </tr>
                                    <?php


                                    $query = "SELECT ve_id, ve_datum, ve_flag, ve_art, ve_text, us_id, ve_datei FROM vermerke WHERE pg_id ='$pg_id' ORDER BY ve_datum DESC, ve_id DESC";
                                    $data = mysqli_query($dbc, $query)
                                    or die(errorlog($dbc, $query));

                                    // Schleife holt jeden Eintrag aus der Vermerk DB, der den pg betrifft
                                    $x = 1;
                                    while ($row = mysqli_fetch_array($data)) {

                                        list($ve_datum_jahr, $ve_datum_monat, $ve_datum_tag) = explode("-", $row['ve_datum']);

                                        $ve_datum = $ve_datum_tag . '.' . $ve_datum_monat . '.' . $ve_datum_jahr;

                                        echo '<tr id="tr' . $x . '" onclick="display(' . $x . ')"><td>' . $ve_datum . '</td>';
                                        /*if ($_GET["ve_id_del"] == $row["ve_id"] && $_GET["delete"] != "y")
                                        {
                                            echo "<td colspan='3' style='background-color: #FFA3A3'>";
                                            echo "Wirklich löschen? ";
                                            echo ' <a href="' . $_SERVER["PHP_SELF"] . '?kd_id=' . $_GET['kd_id'] . "&ve_id_del=" . $row["ve_id"] . '&delete=y">JA</a>' ;
                                            echo ' <a href="' . $_SERVER["PHP_SELF"] . '?kd_id=' . $_GET['kd_id'] . '">NEIN</a>' ;
                                        }*/
                                        /* else
                                         {*/
                                        echo '<td>' . $row['ve_art'];
                                        echo '</td>';
                                        echo '<td>';
                                        if ($row['ve_flag'] == 'w') echo ' <img class="vermerkwichtig" src="images/achtung.png" title="Wichtig!" />';
                                        if ($row['ve_datei'] !== NULL) echo ' <a href="dokumente/' . $row['ve_datei'] . '" target="_blank"><img class="vermerkwichtig" src="images/printed.png" title="Dokument" /></a>';
                                        //if ($row['ve_flag'] != '1') echo ' <a href="' . $_SERVER["PHP_SELF"] . '?kd_id=' . $_GET['kd_id'] . "&ve_id_del=" . $row["ve_id"] . '"><img class="vermerkwichtig" src="images/nein.png" title="Diesen Vermerk löschen" /></a>';
                                        echo '</td>';
                                        echo '<input id="div' . $x . '" type="hidden" value="' . str_replace('\\"', '-', $row['ve_text']) . '" />';

                                        $query2 = "SELECT us_name FROM `user` WHERE us_id = '" . $row["us_id"] . "'";
                                        $data2 = mysqli_query($dbc,$query2)
                                            or die(errorlog($dbc,$query2));
                                        $row2 = mysqli_fetch_array($data2);

                                        echo '<td>' . $row2['us_name'] . '</td>';
                                        echo '</tr>';
                                        //}


                                        $x++;
                                    }
                                    ?>

                                </table>

                            </div>
                        </td>
                    </tr>
                </table>
        </div>
        <div class="col-sm-9 col-lg-6">

                <textarea id="vermerk" readonly="readonly" cols="50" rows="6">Vermerk:</textarea>
        </div>
    </div>


    <a href="veneu.php?from=pg&id=<?php echo $_GET['pg_id']; ?>">Neuen Vermerk hinzufügen</a>


    <?php
}
if(isset($error) && $error == 1) {
//keine pg id in GET
    if ($pg_id == 0)
    {
        ?>
        <div class="alert alert-warning" role="alert">
            Ungültiger Link. Sie werden zur Übersicht weitergeleitet.
        </div>
        <script>
            window.setTimeout(function () {

                window.location.href = "pgalle.php";

            }, 5000);
        </script>
        <?php
    }

//pgnr existiert nicht
    if (isset($pg_exist) && $pg_exist == 0 && isset($pg_id) && $pg_id != 0)
    {
        ?>
        <div class="alert alert-warning" role="alert">
            Es existiert kein Pfleger mit der Nummer "<?php echo $pg_id ?>";
        </div>
        <?php
    }
}
?>
<!-- MAIN END -->
<?php
include_once('footer.php');
?>