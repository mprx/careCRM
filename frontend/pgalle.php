<?php
$siteTitle = "Alle Pfleger";
$siteCategory = "Pfleger";

include_once('header.php');
include_once('nav.php');
?>
    <!-- MAIN START -->
<?php

$dbc = mysqli_connect(DB_HOST,DB_USER,DB_PASSWORD, DB_NAME)
or die(errrorlog($dbc,0));
mysqli_set_charset($dbc,"utf8");

$pg_ende = 0;
$pg_inaktiv = 0;
if(isset($_GET["pg_ende"])) $pg_ende = 1;
if(isset($_GET["pg_inaktiv"])) $pg_inaktiv = 1;

$endequery = " AND (pfleger.pg_ende IS NULL OR pfleger.pg_ende >= DATE(NOW()))";
if($pg_ende == 1)
{
    $endequery = "";
}

$inaktivquery = " AND pfleger.pg_aktiv = '1'";
if($pg_inaktiv == 1)
{
    $inaktivquery = "";
}

//Daten holen
$query = "SELECT pfleger.pg_id, pfleger.pg_anrede, pfleger.pg_vorname, pfleger.pg_nachname, pfleger.pg_tel1, pfleger.pg_aktiv, pfleger.pg_beginn, pfleger.pg_ende, adressen.ad_strasse, adressen.ad_nr, adressen.ad_stiege, adressen.ad_stock, adressen.ad_tuer, adressen.ad_plz FROM pfleger, adressen WHERE pfleger.pg_id = adressen.pg_id AND adressen.ad_aktiv = 1 AND adressen.ad_typ != 'r'" . $endequery . $inaktivquery;
$data = mysqli_query($dbc, $query)
or die(errorlog($dbc, $query));

?>
    <form method="GET" action="<?php echo $_SERVER["PHP_SELF"]; ?>" id="pg_anzeige">
        Anzeigen:
        <input class="ml-4" type="checkbox" id="pg_ende" value="1" name="pg_ende" <?php if(isset($_GET["pg_ende"])) echo "checked" ?> onchange="document.getElementById('pg_anzeige').submit()">
        <label for="pg_ende">beendete</label>

        <input type="checkbox" id="pg_inaktiv" value="1" name="pg_inaktiv" <?php if(isset($_GET["pg_inaktiv"])) echo "checked" ?> onchange="document.getElementById('pg_anzeige').submit()">
        <label for="pg_inaktiv">inaktive</label>
    </form>
    <table class="table table-striped table-sm">
        <thead>
        <tr>
            <th scope="col">Anrede</th>
            <th scope="col">Nachname</th>
            <th scope="col">Vorname</th>
            <th scope="col">PLZ</th>
            <th scope="col">Adresse</th>
            <th scope="col">Telefon</th>
            <th scope="col">Details</th>
        </tr>
        </thead>
        <tbody>

        <?php
        while($row = mysqli_fetch_array($data))
        {
            echo '<tr';
            if($row["pg_aktiv"] == 0 || $row["pg_ende"] > 0) echo ' class="table-danger"';
            echo '>';
            echo '<td scope="row">' . $row["pg_anrede"] . '</td>';
            echo '<td>' . $row["pg_nachname"] . '</td>';
            echo '<td>' . $row["pg_vorname"] . '</td>';
            echo '<td>' . $row["ad_plz"] . '</td>';
            echo '<td>' . $row["ad_strasse"] . " " . $row["ad_nr"] . addressseperation($row["ad_stiege"]) . addressseperation($row["ad_stock"]) . addressseperation($row["ad_tuer"]) . '</td>';
            echo '<td>' . $row["pg_tel1"] . '</td>';
            echo '<td><a href="pgdetails.php?pg_id=' . $row["pg_id"] . '">Details</a></td>';
        }
        ?>

        </tbody>
    </table>

    <!-- MAIN END -->
<?php
include_once('footer.php');
?>