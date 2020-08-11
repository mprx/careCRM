<?php
$siteTitle = "ToDo";
$siteCategory = "Rechnungen";

include_once('header.php');
include_once('nav.php');
?>
<!-- MAIN START -->
<?php

$dbc = mysqli_connect(DB_HOST,DB_USER,DB_PASSWORD, DB_NAME)
or die(errrorlog($dbc,0));
mysqli_set_charset($dbc,"utf8");

if(isset($_GET["rg_jahr"]))
{
    $rg_jahr = mysqli_real_escape_string($dbc, trim($_GET['rg_jahr']));
}
else
{
    $rg_jahr = date("Y", time());
}

if(isset($_GET["rg_monat"]))
{
    $rg_monat = mysqli_real_escape_string($dbc, trim($_GET['rg_monat']));
}
else
{
    $rg_monat = date("m", time());
}
if ((isset($error) && $error == 0) || !isset($error))
{

    ?>
    <div class="col border border-primary mb-3">
       Zu erledigende Verrechnungen anzeigen
    </div>
    <form method="GET" action="<?php echo $_SERVER["PHP_SELF"]; ?>" id="rg_jahr_select">
        <div class="form-group row">

                <label for="lg_jahr" class="col-1 col-form-label col-form-label-sm">Jahr/Monat:</label>

            <div class="text-right">
                <select class="form-control form-control-sm" name="rg_jahr" id="rg_jahr" onchange="document.getElementById('rg_jahr_select').submit()">
                    <?php
                    for($i=2020;$i<=(date("Y",time())+1);$i++)
                    {
                        echo '<option ';
                        if($i == $rg_jahr) echo 'selected';
                        echo '>' . $i;
                        echo '</option>';
                    }
                    ?>
                </select>
            </div>
            <div class="text-left">
                <select class="form-control form-control-sm" name="rg_monat" id="rg_monat" onchange="document.getElementById('rg_jahr_select').submit()">
                    <?php
                    for($i=1;$i<=12;$i++)
                    {
                        echo '<option ';
                        if($i == $rg_monat) echo 'selected';
                        echo '>' . sprintf('%02d', $i);
                        echo '</option>';
                    }
                    ?>
                </select>
            </div>
        </div>

    </form>
    <?php
    $rg_datum_start = $rg_jahr."-".$rg_monat."-1";
    $rg_datum_ende = $rg_jahr."-".$rg_monat."-".date('t',strtotime($rg_datum_start));
}
//Daten holen
$query = "SELECT kunde.kd_id, kunde.kd_anrede, kunde.kd_vorname, kunde.kd_nachname, adressen.ad_strasse, adressen.ad_nr, adressen.ad_stiege, adressen.ad_stock, adressen.ad_tuer, adressen.ad_plz FROM kunde, adressen WHERE kunde.kd_id = adressen.kd_id AND adressen.ad_aktiv = 1 AND adressen.ad_typ != 'r' AND kunde.kd_aktiv = 1 AND kunde.kd_ableben = 0";
$data = mysqli_query($dbc, $query)
    or die(errorlog($dbc, $query))
?>


<table class="table table-striped table-sm">
  <thead>
    <tr>
      <th scope="col">Kunde</th>
      <th scope="col">Adresse</th>
      <th scope="col">verr. bis</th>
      <th scope="col">verrechnen</th>
    </tr>
  </thead>
  <tbody>

  <?php
    while($row = mysqli_fetch_array($data))
    {
        $query2 = "SELECT rg_zeitraum_ende, DATE_FORMAT(rg_zeitraum_ende,'%d.%m.%Y') AS rg_zeitraum_bis FROM rechnung WHERE kd_id = " . $row["kd_id"] . " ORDER BY rg_zeitraum_ende DESC LIMIT 1";
        $data2 = mysqli_query($dbc, $query2)
            or die(errorlog($dbc,$query2));

        $offene = 0;
        $row2 = mysqli_fetch_array($data2);

        if($row2["rg_zeitraum_ende"] < $rg_datum_ende || empty($row2["rg_zeitraum_ende"]))
        {
            $offene = 1;
        }
        else
        {
            $offene = 0;
        }

        if($offene == 1)
        {

            echo '<tr';
            echo '>';
            echo '<td scope="row">';
            echo '<a href="kddetails.php?kd_id=' . $row["kd_id"] . '">';
            echo $row["kd_anrede"] . " ";
            echo $row["kd_nachname"] . " ";
            echo $row["kd_vorname"];
            echo '</a></td>';
            echo '<td>' . $row["ad_plz"] . ', ';
            echo $row["ad_strasse"] . " " . $row["ad_nr"] . addressseperation($row["ad_stiege"]) . addressseperation($row["ad_stock"]) . addressseperation($row["ad_tuer"]) . '</td>';
            echo '<td>' . $row2["rg_zeitraum_bis"];
            if(empty($row2["rg_zeitraum_bis"])) echo "nie";
            echo '</td>';
            echo '<td><a href="rgneukd.php?kd_id=' . $row["kd_id"] . '">Neue Rg</a></td></tr>';

        }

    }
  ?>

  </tbody>
</table>

<!-- MAIN END -->
<?php
include_once('footer.php');
?>