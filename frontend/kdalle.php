<?php
$siteTitle = "Alle Kunden";
$siteCategory = "Kunden";

include_once('header.php');
include_once('nav.php');
?>
<!-- MAIN START -->
<?php

$dbc = mysqli_connect(DB_HOST,DB_USER,DB_PASSWORD, DB_NAME)
or die(errrorlog($dbc,0));
mysqli_set_charset($dbc,"utf8");

$kd_ende = 0;
$kd_tot = 0;
if(isset($_GET["kd_ende"])) $kd_ende = 1;
if(isset($_GET["kd_tot"])) $kd_tot = 1;

$endequery = " AND (kd_ende IS NULL OR kd_ende >= DATE(NOW()))";
if($kd_ende == 1)
{
    $endequery = "";
}

$totquery = " AND kd_ableben = '0'";
if($kd_tot == 1)
{
    $totquery = "";
}


//Daten holen
$query = "SELECT kunde.kd_id, kunde.kd_ableben, kunde.kd_ende, kunde.kd_anrede, kunde.kd_vorname, kunde.kd_nachname, kunde.kd_tel1, adressen.ad_strasse, adressen.ad_nr, adressen.ad_stiege, adressen.ad_stock, adressen.ad_tuer, adressen.ad_plz FROM kunde, adressen WHERE kunde.kd_id = adressen.kd_id AND adressen.ad_aktiv = 1 AND adressen.ad_typ != 'r'" . $endequery . $totquery;
$data = mysqli_query($dbc, $query)
    or die(errorlog($dbc, $query));

?>

<form method="GET" action="<?php echo $_SERVER["PHP_SELF"]; ?>" id="kd_anzeige">
    Anzeigen:
    <input class="ml-4" type="checkbox" id="kd_ende" value="1" name="kd_ende" <?php if(isset($_GET["kd_ende"])) echo "checked" ?> onchange="document.getElementById('kd_anzeige').submit()">
    <label for="kd_ende">beendete</label>

    <input type="checkbox" id="kd_tot" value="1" name="kd_tot" <?php if(isset($_GET["kd_tot"])) echo "checked" ?> onchange="document.getElementById('kd_anzeige').submit()">
    <label for="kd_tot">verstorbene</label>
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
        if($row["kd_ableben"] == 1 || $row["kd_ende"] > 0) echo ' class="table-danger"';
        echo '>';
        echo '<td scope="row">' . $row["kd_anrede"] . '</td>';
        echo '<td>' . $row["kd_nachname"] . '</td>';
        echo '<td>' . $row["kd_vorname"] . '</td>';
        echo '<td>' . $row["ad_plz"] . '</td>';
        echo '<td>' . $row["ad_strasse"] . " " . $row["ad_nr"] . addressseperation($row["ad_stiege"]) . addressseperation($row["ad_stock"]) . addressseperation($row["ad_tuer"]) . '</td>';
        echo '<td>' . $row["kd_tel1"] . '</td>';
        echo '<td><a href="kddetails.php?kd_id=' . $row["kd_id"] . '">Details</a></td></tr>';
    }
  ?>

  </tbody>
</table>

<!-- MAIN END -->
<?php
include_once('footer.php');
?>