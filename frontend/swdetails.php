<?php
$siteTitle = "Sachwalterdetails";
$siteCategory = "Sachwalter";

include_once('header.php');
include_once('nav.php');
?>

<!-- MAIN START -->
<?php
$dbc = mysqli_connect(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);
mysqli_set_charset($dbc,"utf8");
//sw nr definieren
if (isset($_GET["sw_id"]))
{
    $sw_id = mysqli_real_escape_string($dbc, trim($_GET['sw_id']));
}
else
{
    $sw_id = 0;
}

//Wenn kein GET gesetzt ist, sw_id auf 0 setzen und error anzeigen (unten im file)
if ($sw_id == "")
{
    $sw_id = 0;
    $error = 1;
}

//prüfen, ob sw nr existiert
$query = "SELECT sw_id FROM sachwalter WHERE sw_id = '$sw_id' LIMIT 1";
$data = mysqli_query($dbc, $query)
    or die(errorlog($dbc,query));

//wenn sw nicht existiert
if (empty(mysqli_fetch_array($data)))
{
    $error = 1;
    $sw_exist = 0;
}

if ((isset($error) && $error == 0) || !isset($error))
{

    //sw dateb holen
    $query = "SELECT * FROM sachwalter WHERE sw_id = '$sw_id'";
    $data = mysqli_query($dbc, $query)
        or die(errorlog($dbc,$query));
    $row = mysqli_fetch_array($data);
?>
    <ul class="nav nav-tabs">
        <li class="nav-item">
            <a class="nav-link <?php kdTab("swdetails"); ?>" href="swdetails.php?sw_id=<?php echo $sw_id ?>">Allgemein</a>
        </li>
        <li class="nav-item">
            <a class="nav-link <?php kdTab("swdetails2"); ?>" href="swdetails2.php?sw_id=<?php echo $sw_id ?>">Vermerke</a>
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
          <label for="sw_anrede" class="col-sm-3 col-form-label col-form-label-sm">Anrede:</label>
          <div class="col-sm-5 mt-1">
            <?php echo "<b>" . $row["sw_anrede"] . "</b>"; ?>
          </div>
        </div>
          <div class="form-group row m-1">
              <label for="sw_titel" class="col-sm-3 col-form-label col-form-label-sm">Titel:</label>
              <div class="col-sm-5 mt-1">
                  <?php echo "<b>" . $row["sw_titel"] . "</b>"; ?>
              </div>
          </div>
        <div class="form-group row m-1">
          <label for="sw_vorname" class="col-sm-3 col-form-label col-form-label-sm">Vorname</label>
          <div class="col-sm-5 mt-1">
              <?php echo "<b>" . $row["sw_vorname"] . "</b>"; ?>
          </div>
        </div>
        <div class="form-group row m-1">
          <label for="sw_nachname" class="col-sm-3 col-form-label col-form-label-sm">Nachname</label>
          <div class="col-sm-5 mt-1">
              <?php echo "<b>" . $row["sw_nachname"] . "</b>"; ?>
          </div>
        </div>
        <div class="form-group row m-1">
          <label for="sw_gebdatum" class="col-sm-3 col-form-label col-form-label-sm">Geburtsdatum</label>
          <div class="col-sm-5 mt-1">
              <?php
                  list($sw_gebdatum_jahr,$sw_gebdatum_monat,$sw_gebdatum_tag)=explode("-", $row['sw_gebdatum']);
                  echo "<b>" . $sw_gebdatum_tag . '.' . $sw_gebdatum_monat . '.' . $sw_gebdatum_jahr . "</b>";
              ?>
          </div>
        </div>
        
      </div>
      <div class="col">
        <div class="form-group row m-1">
          <label for="sw_tel1" class="col-sm-3 col-form-label col-form-label-sm">Festnetz:</label>
          <div class="col-sm-5 mt-1">
              <?php echo "<b>" . $row["sw_tel1"] . "</b>"; ?>
          </div>
        </div>
        <div class="form-group row m-1">
          <label for="sw_tel2" class="col-sm-3 col-form-label col-form-label-sm">Mobil:</label>
            <div class="col-sm-5 mt-1">
                <?php echo "<b>" . $row["sw_tel2"] . "</b>"; ?>
            </div>
        </div>
        <div class="form-group row m-1">
          <label for="sw_mail" class="col-sm-3 col-form-label col-form-label-sm m-0">E-Mail:</label>
          <div class="col-sm-5 mt-1">
              <?php echo "<b>" . $row["sw_mail"] . "</b>"; ?>
          </div>
        </div>
      </div>
    </div>


      <div class="row">
          <div class="col border border-primary mt-4">
              Vertretungen
          </div>
      </div>

      <div class="row">
          <div class="col">

              <div class="form-group row m-1">
                  <div class="col-sm-5">
                      <?php

                      $query4 = "SELECT kunde.kd_id, kunde.kd_anrede, kunde.kd_vorname, kunde.kd_nachname FROM angehoerige, kunde WHERE angehoerige.ag_aktiv = 1 AND angehoerige.sw_id = '$sw_id' AND kunde.kd_id = angehoerige.kd_id AND kunde.kd_ableben = 0";
                      $data4 = mysqli_query($dbc, $query4)
                      or die(errorlog($dbc,$query4));

                      if(mysqli_num_rows($data4) != 0)
                      {
                          while ($row4 = mysqli_fetch_array($data4))
                          {
                              echo "<b><a href='kddetails.php?kd_id=" . $row4["kd_id"] . "'>" . $row4["kd_anrede"] . " " . $row4["kd_vorname"] . " " . $row4["kd_nachname"] . " " . "</a></b>";
                              echo "</br>";
                          }

                      }
                      else
                      {

                          echo "<b>keine</b>";
                      }
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
        <div class="col">

            <div class="form-group row m-1">
                <div class="col-sm-5">
        <?php

            $query6 = "SELECT ad_id, ad_strasse, ad_nr, ad_stiege, ad_stock, ad_tuer, ad_plz FROM adressen WHERE ad_aktiv = '1' AND sw_id = '$sw_id'";
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
    </div>

      <div class="row mt-4">
          <a href="swaendern.php?sw_id=<?php echo $sw_id ?>" class="btn btn-primary">Datensatz ändern</a>
      </div>
  </div>
</form>
<?php
}

if(isset($error) && $error == 1)
{
    //keine sw ID in GET
    if ($sw_id == 0)
    {
        ?>
        <div class="alert alert-warning" role="alert">
            Ungültiger Link. Sie werden zur Übersicht weitergeleitet.
        </div>
        <script>
            window.setTimeout(function(){

                window.location.href = "swalle.php";

            }, 5000);
        </script>
        <?php
    }

    //kdnr existiert nicht
     if (isset($sw_exist) && $sw_exist == 0 && $sw_id != 0)
     {
         ?>
         <div class="alert alert-warning" role="alert">
             Es existiert kein Sachwalter mit der Nummer "<?php echo $sw_id ?>";
         </div>
        <?php
     }


}
?>
<!-- MAIN END -->

<?php
include_once('footer.php');
?>
