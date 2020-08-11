<?php
$siteTitle = "Neuer Vermerk";
$siteCategory = "Kunden";

include_once('header.php');
include_once('nav.php');
?>

<!-- MAIN START -->
<?php
$dbc = mysqli_connect(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);
mysqli_set_charset($dbc,"utf8");

//Nummer definieren

if (isset($_GET["id"]))
{
    $id = mysqli_real_escape_string($dbc, trim($_GET['id']));
}
else
{
    $id = 0;
}

//from definieren
if (isset($_GET["from"]))
{
    $from = mysqli_real_escape_string($dbc, trim($_GET['from']));
}
else
{
    $from = 0;
}

//Wenn kein GET gesetzt ist, kd_id auf 0 setzen und error anzeigen (unten im file)
if ($id == "")
{
    $id = 0;
    $error = 1;
}

if ($from == "")
{
    $from = 0;
    $error = 1;
}

switch($from)
{
    case "kd":
        $table = "kunde";
        $prefix = "kd";
        $siteTitle = "Kundendetails";
        $siteCategory = "Kunden";
        break;
    case "pg":
        $table = "pfleger";
        $prefix = "pg";
        $siteTitle = "Pflegerdetails";
        $siteCategory = "Pfleger";
        break;
    case "sw":
        $table = "sachwalter";
        $prefix = "sw";
        $siteTitle = "Sachwalterdetails";
        $siteCategory = "Sachwalter";
}


$query = "SELECT " . $prefix . "_id, " . $prefix . "_vorname, " . $prefix . "_nachname FROM $table WHERE " . $prefix . "_id = '$id' LIMIT 1";
$data = mysqli_query($dbc, $query)
or die(errorlog($dbc,query));

//wenn nr nicht existiert
if (empty(mysqli_fetch_array($data)))
{
    $error = 1;
    $id_exist = 0;
}

if ((isset($error) && $error == 0) || !isset($error))
{
    if(isset($_POST["submit"]))
    {
        $ve_text = mysqli_real_escape_string($dbc, trim($_POST['ve_text']));
        $ve_art = mysqli_real_escape_string($dbc, trim($_POST['ve_art']));
        if(isset($_POST["ve_flag"]))
        {
            $ve_flag = $_POST["ve_flag"];
        }
        else
        {
            $ve_flag = 'n';
        }

        //text muss ausgefüllt sein
        if(!empty($ve_text))
        {
            $query = "INSERT INTO vermerke" .
                "(" . $prefix . "_id, us_id, ve_art, ve_flag, ve_text, ve_datum) " .
                "VALUES" .
                "('$id','" . $_SESSION["userid"] . "', '$ve_art', '$ve_flag', '$ve_text', NOW())";

            mysqli_query($dbc, $query)
                or die(errorlog($dbc, $query));

            echo '<div class="alert alert-success" role="alert">';
            echo 'Der Vermerk wurde erfolgreich angelegt';
            echo '</div>';
            echo '<p><a href="' . $prefix . 'details2.php?' . $prefix . '_id=' . $id . '">Zurück zum Akt</a></p>';
            echo '<p><a href="veneu.php?id=' . $id . '&from=' . $from . '">Neuen Vermerk für ' . ucfirst($table) . ' hinzufügen</a></p>';

        }
        else
        {
            $incomplete = 1;
            ?>
            <div class="alert alert-warning" role="alert">
                Bitte alle notwendigen Felder ausfüllen!
            </div>
            <?php
        }

    }

    if(!isset($_POST["submit"]) || isset($incomplete))
    {
    mysqli_data_seek($data, 0);
    $row = mysqli_fetch_array($data);
    ?>
    <form action="<?php echo $_SERVER["PHP_SELF"] . "?id=" . $id . "&from=" . $from ?>" method="POST">
        <div class="container">
            <div class="row">
                <div class="col border border-primary">
                    Neuen Vermerk für <?php echo ucfirst($table) . " " . $row[$prefix . "_vorname"] . " " . $row[$prefix . "_nachname"]; ?>
                </div>
            </div>
            <div class="row">
                <div class="col">
                    <div class="form-group row m-1">
                        <label for="ve_art" class="col-sm-3 col-form-label col-form-label-sm">Art:</label>
                        <div class="col-sm-5">
                            <select class="form-control form-control-sm" name="ve_art" id="ve_art" required>
                                <option value="Information">Information</option>
                                <option value="Hausbesuch">Hausbesuch</option>
                                <option value="Telefonat">Telefonat</option>
                            </select>
                        </div>
                    </div>
                    <div class="form-group row m-1">
                        <label for="ve_flag" class="col-sm-3 col-form-label col-form-label-sm">Wichtig:</label>
                        <div class="col-sm-5">
                            <input type="checkbox" name="ve_flag" id="ve_flag" value="w" class="mt-3" />
                        </div>
                    </div>
                    <div class="form-group row m-1">
                        <label for="ve_text" class="col-sm-3 col-form-label col-form-label-sm">Vermerk:</label>
                        <div class="col-sm-5">
                            <textarea name="ve_text" id="ve_text" class="form-control" id="exampleFormControlTextarea1" rows="3" required></textarea>
                        </div>
                    </div>
                </div>
            </div>
            <input type="submit" name="submit" id="submit" value="Abschicken" class="btn btn-primary mt-5 ml-4" />
        </div>
    </form>
    <?php

    }
}
if(isset($error) && $error == 1) {
//keine nummer in GET
    if ($id == 0)
    {
        ?>
        <div class="alert alert-warning" role="alert">
            Ungültiger Link. Sie werden zur Übersicht weitergeleitet.
        </div>
        <script>
            window.setTimeout(function () {

                window.location.href = <?php echo $prefix; ?>"alle.php";

            }, 5000);
        </script>
        <?php
    }

//kdnr existiert nicht
    if (isset($id_exist) && $id_exist == 0 && isset($id) && $id != 0)
    {
        ?>
        <div class="alert alert-warning" role="alert">
            Es existiert kein Akt mit der Nummer "<?php echo $id ?>";
        </div>
        <?php
    }



}
?>
<!-- MAIN END -->

<?php
include_once('footer.php');
?>