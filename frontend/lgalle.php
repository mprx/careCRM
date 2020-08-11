<?php
$siteTitle = "Alle Leistungen";
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
$scopequery = "SELECT MAX(lg_jahr) as lg_jahr_max, MIN(lg_jahr) as lg_jahr_min FROM leistung ORDER BY lg_jahr";
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
$selectquery = "SELECT DISTINCT lg_jahr FROM leistung WHERE lg_jahr NOT IN('0000') ORDER BY lg_jahr";
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

?>
<form method="GET" action="<?php echo $_SERVER["PHP_SELF"]; ?>" id="lg_jahr_select">
    <div class="form-group row">

            <label for="lg_jahr" class="col-sm-1 col-form-label col-form-label-sm">Jahr:</label>

            <div class="col-sm-3 col-lg-2">
                <select class="form-control form-control-sm" name="lg_jahr" id="lg_jahr" onchange="document.getElementById('lg_jahr_select').submit()">
                    <?php
                    while($selectrow = mysqli_fetch_array($selectdata))
                    {
                        echo '<option ';
                        if($selectrow["lg_jahr"] == $lg_jahr) echo 'selected';
                        echo '>' . $selectrow["lg_jahr"];
                        echo '</option>';
                    }
                    ?>
                </select>
            </div>
    </div>

</form>
<table class="table table-striped table-sm">
  <thead>
    <tr>
      <th scope="col">Bezeichnung</th>
      <th scope="col">Einheit</th>
      <th scope="col">Preis/Einheit</th>
      <th scope="col">Mengenpreis</th>
      <th scope="col">Mengenpreis ab</th>
    </tr>
  </thead>
  <tbody>

  <?php
    while($row = mysqli_fetch_array($data))
    {
        switch($row["lg_einheit"])
        {
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
            default:
                $lg_einheit = "Einheit";
        }
        echo '<tr>';
        echo '<td scope="row">' . $row["lg_bezeichnung"] . '</td>';
        echo '<td scope="row">' . $lg_einheit . '</td>';
        echo '<td>';
        if (!empty($row["lg_einzeltarif"]))
        {
            echo $row["lg_einzeltarif"] . " €";
        }
        else
        {
            echo "-";
        }
        echo '</td>';
        echo '<td>';
        if (!empty($row["lg_mengentarif"]))
        {
            echo $row["lg_mengentarif"] . " €";
        }
        else
        {
            echo "-";
        }
        echo '</td>';
        echo '<td>';
        if (!empty($row["lg_rabattgrenze"]))
        {
            echo $row["lg_rabattgrenze"];
        }
        else
        {
            echo "-";
        }
        echo '</td>';
        echo '<tr>';
    }
  ?>

  </tbody>
</table>

<!-- MAIN END -->
<?php
include_once('footer.php');
?>