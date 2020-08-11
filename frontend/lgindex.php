<?php
$siteTitle = "Indexieren";
$siteCategory = "Leistungen";

include_once('header.php');
include_once('nav.php');
?>
<!-- MAIN START -->
<?php
$dbc = mysqli_connect(DB_HOST,DB_USER,DB_PASSWORD, DB_NAME)
or die(errrorlog($dbc,0));
mysqli_set_charset($dbc,"utf8");


//aus der db die min und max werte der vorhandenen Jahre holen
$scopequery = "SELECT MAX(lg_jahr) as lg_jahr_max, MIN(lg_jahr) as lg_jahr_min FROM leistung";
$scopedata = mysqli_query($dbc, $scopequery)
    or die(errrorlog($dbc, $scopequery));
$scoperow = mysqli_fetch_array($scopedata);

if(!empty($scoperow["lg_jahr_min"]))
{
    $lg_jahr_min = $scoperow["lg_jahr_min"];
    $lg_jahr_max = $scoperow["lg_jahr_max"];
    $lg_keine_daten = 0;
}
else
{
    $lg_jahr_min = date("Y",time());
    $lg_jahr_max = date("Y",time());
    $lg_keine_daten = 1;
}


//Jahr definieren
if (isset($_GET["lg_jahr"]))
{
    $lg_jahr = mysqli_real_escape_string($dbc, trim($_GET['lg_jahr']));
    //Wenn datum keine Zahl ist und nicht 4 stellen lang ist auf max jahr setzen
    if(!is_numeric($lg_jahr) || (strlen($lg_jahr) != 4))
    {
        $lg_jahr = $lg_jahr_max;
    }
}
else
{
    $lg_jahr = $lg_jahr_max;
}

//verfübare Jahre für das Select holen:
$selectquery = "SELECT DISTINCT lg_jahr FROM leistung";
$selectdata = mysqli_query($dbc, $selectquery)
    or die(errorlog($dbc, $selectquery));

if(mysqli_num_rows($selectdata) > 0)
{
    //Daten holen
    $query = "SELECT * FROM leistung WHERE lg_jahr = '$lg_jahr'";
    $data = mysqli_query($dbc, $query)
    or die(errorlog($dbc, $query));

    $lg_keine_daten_jahr = 0;
}
else
{
    $lg_keine_daten_jahr = 1;
}

if(isset($_POST["submit"]))
{
    $lg_index = mysqli_real_escape_string($dbc, trim($_POST['lg_index']));
    $lg_index_jahr = mysqli_real_escape_string($dbc, trim($_POST['lg_index_jahr']));

    $lg_index_i = mysqli_real_escape_string($dbc, trim($_POST['lg_index_i']));

    $i = 0;
    $lg_index_ids = array();
    while($i<$lg_index_i)
    {
        if(isset($_POST["lg_index_id_$i"]))
        {
            $lg_index_ids[] = $_POST["lg_index_id_$i"];
        }

        $i++;
    }
    //prüfen, ob array gefüllt ist
    if(!empty($lg_index_ids))
    {
        //prüfen, ob die anderen felder ausgefüllt sind
        if(!empty($lg_index) && !empty($lg_index_jahr))
        {
            //prüfen, ob Jahr korrekt und im Rahmen ist:
            if($lg_index_jahr < 3000 && $lg_index_jahr > 2000)
            {
                //index wert ins korrekte Format bringen
                str_replace(",", ".", $lg_index);

                mysqli_begin_transaction($dbc);

                foreach($lg_index_ids as $lg_id)
                {
                    $query = "INSERT INTO `leistung`(`lg_bezeichnung`, `lg_einheit`, `lg_rabattgrenze`, `lg_einzeltarif`, `lg_mengentarif`, `lg_jahr`) " .
                             "SELECT `lg_bezeichnung`, `lg_einheit`, `lg_rabattgrenze`, `lg_einzeltarif`*" . $lg_index . ", `lg_mengentarif`*" . $lg_index . ", " . $lg_index_jahr . " FROM `leistung` WHERE lg_id = '$lg_id'";
                    mysqli_query($dbc, $query)
                        or die(errorlog($dbc, $query));
                    $lg_id_neu = mysqli_insert_id($dbc);

                    $query = "INSERT INTO bezug(kd_id, lg_id) " .
                             "SELECT kd_id, $lg_id_neu FROM bezug WHERE lg_id = $lg_id";
                    mysqli_query($dbc, $query)
                        or die(errorlog($dbc, $query));

                    $query = "DELETE FROM bezug WHERE lg_id = $lg_id";
                    mysqli_query($dbc, $query)
                        or die(errorlog($dbc, $query));

                }

                mysqli_commit($dbc)
                    or die(errorlog($dbc,$query));

                //erfolg mitteilen
                echo '<div class="alert alert-success" role="alert">';
                echo 'Die Leistungen wurden erfolgreich indexiert';
                echo '</div>';
                echo '<p><a href="lgalle.php?lg_jahr=' . $lg_index_jahr . '">Indexierte Leistungen anzeigen</a></p>';


                mysqli_close($dbc);
                exit();

            }
            else
            {
                //Jahr ungültig
                echo '<div class="alert alert-warning" role="alert">';
                echo 'Das eingegebene Ziel-Jahr ist ungültig.';
                echo '</div>';
                $error = 1;
            }



        }
        else
        {
            //nicht alle Felder ausgefüllt
            //Jahr ungültig
            echo '<div class="alert alert-warning" role="alert">';
            echo 'Bitte alle Felder ausfüllen.';
            echo '</div>';
            $error = 1;
        }
    }
    else
    {
        //keine Leistung zur Indexierung ausgewählt
        //Jahr ungültig
        echo '<div class="alert alert-warning" role="alert">';
        echo 'Bitte mindestens eine Leistung zum Indexieren auswählen.';
        echo '</div>';
        $error = 1;
    }


}

if ((isset($error) && $error == 1) || !isset($error)) {
    ?>
    <form method="GET" action="<?php echo $_SERVER["PHP_SELF"]; ?>" id="lg_jahr_select">
        <div class="container">

            <div class="row">
                <div class="col">
                    <div class="form-group row m-1">

                        <label for="lg_jahr" class="col-sm-5 col-lg-3 col-form-label col-form-label-sm">Jahr anzeigen:</label>

                        <div class="col-sm-3 col-lg-2">
                            <select class="form-control form-control-sm" name="lg_jahr" id="lg_jahr"
                                    onchange="document.getElementById('lg_jahr_select').submit()">
                                <?php
                                while ($selectrow = mysqli_fetch_array($selectdata)) {
                                    echo '<option ';
                                    if ($selectrow["lg_jahr"] == $lg_jahr) echo 'selected';
                                    echo '>' . $selectrow["lg_jahr"];
                                    echo '</option>';
                                }
                                ?>
                            </select>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </form>
    <form action="<?php echo $_SERVER["PHP_SELF"]; ?>" method="POST">
        <div class="container">

            <div class="row">
                <div class="col">
                    <div class="form-group row m-1">
                        <label for="lg_index" class="col-sm-5 col-lg-3 col-form-label col-form-label-sm">Faktor für
                            Indexierung:</label>
                        <div class="col-sm-3 col-lg-2">
                            <input type="number" min="0.0001" step="0.0001" class="form-control form-control-sm"
                                   name="lg_index" id="lg_index" placeholder="1" required
                                   value="<?php keeppostvalue('lg_index'); ?>"/>
                        </div>
                    </div>
                    <div class="form-group row m-1">
                        <label for="lg_index_jahr" class="col-sm-5 col-lg-3 col-form-label col-form-label-sm">Neues Jahr:</label>
                        <div class="col-sm-3 col-lg-2">
                            <input type="number" class="form-control form-control-sm" name="lg_index_jahr"
                                   id="lg_index_jahr" step="1" min="2000" max="3000"
                                   value="<?php if (isset($_POST["submit"])) {
                                       keeppostvalue('lg_jahr');
                                   } else {
                                       echo date("Y") + 1;
                                   } ?>" required/>
                        </div>
                    </div>
                </div>
            </div>


        </div>
        <table class="table table-striped table-sm">
            <thead>
            <tr>
                <th scope="col">Bezeichnung</th>
                <th scope="col">Einheit</th>
                <th scope="col">Preis/Einheit</th>
                <th scope="col">Mengenpreis</th>
                <th scope="col">Mengenpreis ab</th>
                <th scope="col">Indexieren</th>
            </tr>
            </thead>
            <tbody>

            <?php
            $i = 0;
            while ($row = mysqli_fetch_array($data)) {
                switch ($row["lg_einheit"]) {
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
                }
                echo '<tr>';
                echo '<td scope="row">' . $row["lg_bezeichnung"] . '</td>';
                echo '<td scope="row">' . $lg_einheit . '</td>';
                echo '<td>';
                if (!empty($row["lg_einzeltarif"])) {
                    echo $row["lg_einzeltarif"] . " €";
                } else {
                    echo "-";
                }
                echo '</td>';
                echo '<td>';
                if (!empty($row["lg_mengentarif"])) {
                    echo $row["lg_mengentarif"] . " €";
                } else {
                    echo "-";
                }
                echo '</td>';
                echo '<td>';
                if (!empty($row["lg_rabattgrenze"])) {
                    echo $row["lg_rabattgrenze"];
                } else {
                    echo "-";
                }
                echo '</td>';
                echo '<td>';
                $lg_id = $row["lg_id"];
                echo '<input type="checkbox" name="lg_index_id_' . $i . '" id="lg_index_id_' . $i . '"  value="' . $lg_id . '"';
                if ((isset($_POST["lg_index_id_" . $i]) && isset($_POST["submit"])) || !isset($_POST["submit"])) echo ' checked';
                echo '/>';
                echo '</td>';
                echo '<tr>';
                $i++;
            }
            ?>

            </tbody>
        </table>
        <input type="text" name="lg_index_i" id="lg_index_i" value="<?php echo $i; ?>" style="display: none;"/>
        <input type="submit" name="submit" id="submit" value="indexieren" class="btn btn-primary mt-0"/>
    </form>

    <!-- MAIN END -->
    <?php
}
include_once('footer.php');
?>