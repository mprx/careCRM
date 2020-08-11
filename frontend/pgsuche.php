<?php
$siteTitle = "Pfleger suchen";
$siteCategory = "Pfleger";

include_once('header.php');
include_once('nav.php');
?>

    <!-- MAIN START -->
<?php
if (isset($_POST["submit"]))
{
    // Zur DB verbinden
    $dbc = mysqli_connect(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME)
    or die(errorlog($dbc,0));
    mysqli_set_charset($dbc,"utf8");

    $suchbegriff = trim($_POST["suchbegriff"]);
    if (strlen($suchbegriff) < 3)
    {
        echo '<div class="alert alert-warning" role="alert">';
        echo 'Die Suchanfrage muss mindestens 3 Zeichen lang sein.';
        echo '</div>';
        $error = 1;
    }
    elseif (!isset($_POST["suche_vn"]) && !isset($_POST["suche_nn"]) && !isset($_POST["suche_ad"]))
    {
        echo '<div class="alert alert-warning" role="alert">';
        echo 'Es muss mindestens ein Suchkriterium ausgewählt sein.';
        echo '</div>';
        $error = 1;
    }
    else
    {
        $suchbegriffe = explode(" ",$suchbegriff);
        $query_start = "SELECT pfleger.pg_id, pfleger.pg_vorname, pfleger.pg_nachname, adressen.ad_strasse, adressen.ad_plz FROM pfleger, adressen";
        $query_where_list = array();

        {
            foreach ($suchbegriffe as $wort)
            {
                if (isset($_POST["suche_vn"])) $query_where_list[] = "(pfleger.pg_vorname LIKE '%$wort%' AND adressen.pg_id = pfleger.pg_id AND adressen.ad_aktiv = 1 AND adressen.ad_typ IN('rb','r'))";
                if (isset($_POST["suche_nn"])) $query_where_list[] = "(pfleger.pg_nachname LIKE '%$wort%' AND adressen.pg_id = pfleger.pg_id AND adressen.ad_aktiv = 1 AND adressen.ad_typ IN('rb','r'))";
                if (isset($_POST["suche_ad"])) $query_where_list[] = "(adressen.ad_strasse LIKE '%$wort%' AND adressen.pg_id = pfleger.pg_id)";
            }
        }


        $query_where = implode(" OR ", $query_where_list);

        $query = "$query_start WHERE $query_where";
        $data = mysqli_query($dbc, $query)
        or die(errorlog($dbc,$query));

        if (mysqli_num_rows($data) != 0)
        {
            echo '<div class="alert alert-success" role="alert">';
            echo 'Es wurden folgende Datensätze gefunden:';
            echo '</div>';

            while ($row = mysqli_fetch_array($data))
            {
                echo "<a href='pgdetails.php?pg_id=" . $row["pg_id"] . "'> &bull; " . $row["pg_vorname"] . " " . $row["pg_nachname"] .", " . $row["ad_strasse"] .", " . $row["ad_plz"] . "</a>";
                echo "<br />";
            }

            echo "<br />";
            echo "<a href='pgsuche.php'>Neue Suche</a>";
        }
        else
        {
            echo '<div class="alert alert-info" role="alert">';
            echo "Keine Treffer für \"$suchbegriff\". in Pfleger";
            echo '</div>';
            $error = 1;
        }





    }

}

if ((isset($error) && $error == 1) || (!isset($error) && !isset($_POST["submit"])))
{
    ?>
    <form action="<?php echo $_SERVER["PHP_SELF"] ?>" method="POST">
        <div class="form-group row">
            <label for="inputsearch" class="col-sm-2 col-form-label">Suchbegriff:</label>
            <div class="col-sm-5">
                <input type="text" class="form-control" id="suchbegriff" name="suchbegriff" placeholder="Suchbegriff">
            </div>
        </div>
        <div class="form-group row">
            <div class="col-sm-2">Suchen in</div>
            <div class="col-sm-5">
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" name="suche_nn" id="suche_nn" checked>
                    <label class="form-check-label" for="gridCheck1">
                        Nachname
                    </label>
                </div>
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" name="suche_vn" id="suche_vn" checked>
                    <label class="form-check-label" for="gridCheck2">
                        Vorname
                    </label>
                </div>
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" name="suche_ad" id="suche_ad">
                    <label class="form-check-label" for="gridCheck3">
                        Adresse
                    </label>
                </div>
            </div>
        </div>
        <div class="form-group row">
            <div class="col-sm-2"></div>
            <div class="col-sm-5">
                <input type="submit" name="submit" id="submit" value="suchen" class="btn btn-primary" />
            </div>
        </div>
    </form>
    <?php
}
?>
    <!-- MAIN END -->
<?php
include_once('footer.php');
?>