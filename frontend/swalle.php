<?php
$siteTitle = "Alle Sachwalter";
$siteCategory = "Sachwalter";

include_once('header.php');
include_once('nav.php');
?>
    <!-- MAIN START -->
<?php

$dbc = mysqli_connect(DB_HOST,DB_USER,DB_PASSWORD, DB_NAME)
or die(errrorlog($dbc,0));
mysqli_set_charset($dbc,"utf8");

//Daten holen
$query = "SELECT sachwalter.sw_id, sachwalter.sw_anrede, sachwalter.sw_vorname, sachwalter.sw_nachname, sachwalter.sw_tel1, adressen.ad_strasse, adressen.ad_nr, adressen.ad_stiege, adressen.ad_stock, adressen.ad_tuer, adressen.ad_plz FROM sachwalter, adressen WHERE sachwalter.sw_id = adressen.sw_id AND sachwalter.sw_typ = 'sw' AND adressen.ad_aktiv = 1";
$data = mysqli_query($dbc, $query)
or die(errorlog($dbc, $query));

?>
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
            echo '<tr>';
            echo '<td scope="row">' . $row["sw_anrede"] . '</td>';
            echo '<td>' . $row["sw_nachname"] . '</td>';
            echo '<td>' . $row["sw_vorname"] . '</td>';
            echo '<td>' . $row["ad_plz"] . '</td>';
            echo '<td>' . $row["ad_strasse"] . " " . $row["ad_nr"] . addressseperation($row["ad_stiege"]) . addressseperation($row["ad_stock"]) . addressseperation($row["ad_tuer"]) . '</td>';
            echo '<td>' . $row["sw_tel1"] . '</td>';
            echo '<td><a href="swdetails.php?sw_id=' . $row["sw_id"] . '">Details</a></td>';
        }
        ?>

        </tbody>
    </table>

    <!-- MAIN END -->
<?php
include_once('footer.php');
?>