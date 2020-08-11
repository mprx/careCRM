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
//Pflegernummer definieren
if (isset($_GET["pg_id"]))
{
    $pg_id = mysqli_real_escape_string($dbc, trim($_GET['pg_id']));
}
else
{
    $pg_id = 0;
}

//Wenn kein GET gesetzt ist, kd_id auf 0 setzen und error anzeigen (unten im file)
if ($pg_id == "")
{
    $pg_id = 0;
    $error = 1;
}

//prüfen, ob kundennummer existiert

$query = "SELECT pg_id FROM pfleger WHERE pg_id = '$pg_id' LIMIT 1";
$data = mysqli_query($dbc, $query)
    or die(errorlog($dbc,query));

//wenn pg nicht existiert
if (empty(mysqli_fetch_array($data)))
{
    $error = 1;
    $pg_exist = 0;
}

if ((isset($error) && $error == 0) || !isset($error))
{

    //kundendaten holen
    $query = "SELECT * FROM pfleger WHERE pg_id = '$pg_id'";
    $data = mysqli_query($dbc, $query)
        or die(errorlog($dbc,query));
    $row = mysqli_fetch_array($data);
?>
    <ul class="nav nav-tabs">
        <li class="nav-item">
            <a class="nav-link <?php kdTab("pgdetails"); ?>" href="pgdetails.php?pg_id=<?php echo $pg_id ?>">Allgemein</a>
        </li>
        <li class="nav-item">
            <a class="nav-link <?php kdTab("pgdetails2"); ?>" href="pgdetails2.php?pg_id=<?php echo $pg_id ?>">Vermerke</a>
        </li>
        <li class="nav-item">
            <a class="nav-link <?php kdTab("pgdetails3"); ?>" href="pgdetails3.php?pg_id=<?php echo $pg_id ?>">Pflegeplan</a>
        </li>
    </ul>
    <br>
<form action="<?php echo $_SERVER["PHP_SELF"] ?>" method="POST">
  <div class="container">
    <div class="row">
      <div class="col border border-primary">
      Allgemeine Daten
      </div>
    </div>
    <div class="row">
      <div class="col">
        
        <div class="form-group row m-1">
          <label for="pg_anrede" class="col-sm-4 col-form-label col-form-label-sm">Anrede:</label>
          <div class="col-sm-7 mt-1">
            <?php echo "<b>" . $row["pg_anrede"] . "</b>"; ?>
          </div>
        </div>
        <div class="form-group row m-1">
          <label for="pg_vorname" class="col-sm-4 col-form-label col-form-label-sm">Vorname</label>
          <div class="col-sm-7 mt-1">
              <?php echo "<b>" . $row["pg_vorname"] . "</b>"; ?>
          </div>
        </div>
        <div class="form-group row m-1">
          <label for="pg_nachname" class="col-sm-4 col-form-label col-form-label-sm">Nachname</label>
          <div class="col-sm-7 mt-1">
              <?php echo "<b>" . $row["pg_nachname"] . "</b>"; ?>
          </div>
        </div>
        <div class="form-group row m-1">
          <label for="pg_gebdatum" class="col-sm-4 col-form-label col-form-label-sm">Geburtsdatum</label>
          <div class="col-sm-7 mt-1">
              <?php
                  list($pg_gebdatum_jahr,$pg_gebdatum_monat,$pg_gebdatum_tag)=explode("-", $row['pg_gebdatum']);
                  echo "<b>" . $pg_gebdatum_tag . '.' . $pg_gebdatum_monat . '.' . $pg_gebdatum_jahr . "</b>";
              ?>
          </div>
        </div>
        
      </div>
      <div class="col">
        <div class="form-group row m-1">
          <label for="pg_tel1" class="col-sm-4 col-form-label col-form-label-sm">Festnetz:</label>
          <div class="col-sm-7 mt-1">
              <?php echo "<b>" . $row["pg_tel1"] . "</b>"; ?>
          </div>
        </div>
        <div class="form-group row m-1">
          <label for="pg_tel2" class="col-sm-4 col-form-label col-form-label-sm">Mobil:</label>
            <div class="col-sm-7 mt-1">
                <?php echo "<b>" . $row["pg_tel2"] . "</b>"; ?>
            </div>
        </div>
        <div class="form-group row m-1">
          <label for="pg_mail" class="col-sm-4 col-form-label col-form-label-sm m-0">E-Mail:</label>
          <div class="col-sm-7 mt-1">
              <?php echo "<b>" . $row["pg_mail"] . "</b>"; ?>
          </div>
        </div>
        <div class="form-group row m-1">
          <label for="pg_art" class="col-sm-4 col-form-label col-form-label-sm">Art:</label>
          <div class="col-sm-7 mt-1">
              <?php
                if($row["pg_art"] == "24h") echo "<b>24h Pflege</b>";
                if($row["pg_art"] == "sw") echo "<b>Stundenweise</b>";
                if($row["pg_art"] == "dgks") echo "<b>DGKS/P</b>";
              ?>
          </div>
        </div>
        
      </div>
    </div>
    


     <div class="row">
      <div class="col border border-primary mt-4">
      Adresse
      </div>
    </div>
    <div class="row" id="RgAdresseNeu">
        <div class="form-group row m-1">
            <div class="col">
        
        <?php

            $query6 = "SELECT ad_id, ad_strasse, ad_nr, ad_stiege, ad_stock, ad_tuer, ad_plz FROM adressen WHERE ad_typ IN('r', 'rb') AND ad_aktiv = '1' AND pg_id = '$pg_id'";
            $data6 = mysqli_query($dbc, $query6)
                or die(errorlog($dbc, $query6));
            $row6 = mysqli_fetch_array($data6);

            echo "<b>";
            echo  $row6["ad_plz"] . ", " . $row6["ad_strasse"] . " " . $row6["ad_nr"] . addressseperation($row6["ad_stiege"]) . addressseperation($row6["ad_stock"]) . addressseperation($row6["ad_tuer"]);
            echo "</b>";
        ?>
            </div>
        </div>
    </div>

      <div class="row mt-4">
          <a href="pgaendern.php?pg_id=<?php echo $pg_id ?>" name="kd_aendern" id="kd_aendern"  class="btn btn-primary">Datensatz ändern</a>
      </div>
  </div>
</form>
<?php
}

if(isset($error) && $error == 1)
{
    //keine Pfleger ID in GET
    if ($pg_id == 0)
    {
        ?>
        <div class="alert alert-warning" role="alert">
            Ungültiger Link. Sie werden zur Übersicht weitergeleitet.
        </div>
        <script>
            window.setTimeout(function(){

                window.location.href = "pgalle.php";

            }, 5000);
        </script>
        <?php
    }

    //kdnr existiert nicht
     if (isset($pg_exist) && $pg_exist == 0 && $pg_id != 0)
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
