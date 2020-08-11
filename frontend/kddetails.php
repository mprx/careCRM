<?php
$siteTitle = "Kundendetails";
$siteCategory = "Kunden";

include_once('header.php');
include_once('nav.php');
?>

<!-- MAIN START -->
<?php
$dbc = mysqli_connect(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);
mysqli_set_charset($dbc,"utf8");

//Kundennummer definieren
if (isset($_GET["kd_id"]))
{
    $kd_id = mysqli_real_escape_string($dbc, trim($_GET['kd_id']));
}
else
{
    $kd_id = 0;
}

//Wenn kein GET gesetzt ist, kd_id auf 0 setzen und error anzeigen (unten im file)
if ($kd_id == "")
{
    $kd_id = 0;
    $error = 1;
}

//prüfen, ob kundennummer existiert

$query = "SELECT kd_id FROM kunde WHERE kd_id = '$kd_id' LIMIT 1";
$data = mysqli_query($dbc, $query)
    or die(errorlog($dbc,query));

//wenn kd_nr nicht existiert
if (empty(mysqli_fetch_array($data)))
{
    $error = 1;
    $kd_exist = 0;
}

if ((isset($error) && $error == 0) || !isset($error))
{

    //kundendaten holen
    $query = "SELECT * FROM kunde WHERE kd_id = '$kd_id'";
    $data = mysqli_query($dbc, $query)
        or die(errorlog($dbc,query));
    $row = mysqli_fetch_array($data);
?>
    <ul class="nav nav-tabs">
        <li class="nav-item">
            <a class="nav-link <?php kdTab("kddetails"); ?>" href="kddetails.php?kd_id=<?php echo $kd_id ?>">Allgemein</a>
        </li>
        <li class="nav-item">
            <a class="nav-link <?php kdTab("kddetails2"); ?>" href="kddetails2.php?kd_id=<?php echo $kd_id ?>">Vermerke</a>
        </li>
        <li class="nav-item">
            <a class="nav-link <?php kdTab("kddetails3"); ?>" href="kddetails3.php?kd_id=<?php echo $kd_id ?>">Pflegeplan</a>
        </li>
        <li class="nav-item">
            <a class="nav-link <?php kdTab("kddetails4"); ?>" href="kddetails4.php?kd_id=<?php echo $kd_id ?>">Tarife</a>
        </li>
        <li class="nav-item">
            <a class="nav-link <?php kdTab("kddetails5"); ?>" href="kddetails5.php?kd_id=<?php echo $kd_id ?>">Rechnungen</a>
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
          <label for="kd_anrede" class="col-sm-4 col-form-label col-form-label-sm">Anrede:</label>
          <div class="col-sm-7 mt-1">
            <?php echo "<b>" . $row["kd_anrede"] . "</b>"; ?>
          </div>
        </div>
        <div class="form-group row m-1">
          <label for="kd_titel" class="col-sm-4 col-form-label col-form-label-sm">Titel:</label>
          <div class="col-sm-7 mt-1">
              <?php echo "<b>" . $row["kd_titel"] . "</b>"; ?>
          </div>
        </div>
        <div class="form-group row m-1">
          <label for="kd_vorname" class="col-sm-4 col-form-label col-form-label-sm">Vorname:</label>
          <div class="col-sm-7 mt-1">
              <?php echo "<b>" . $row["kd_vorname"] . "</b>"; ?>
          </div>
        </div>
        <div class="form-group row m-1">
          <label for="kd_nachname" class="col-sm-4 col-form-label col-form-label-sm">Nachname:</label>
          <div class="col-sm-7 mt-1">
              <?php echo "<b>" . $row["kd_nachname"] . "</b>"; ?>
          </div>
        </div>
        <div class="form-group row m-1">
          <label for="kd_gebdatum" class="col-sm-4 col-form-label col-form-label-sm">Geburtsdatum:</label>
          <div class="col-sm-7 mt-1">
              <?php
                  list($kd_gebdatum_jahr,$kd_gebdatum_monat,$kd_gebdatum_tag)=explode("-", $row['kd_gebdatum']);
                  echo "<b>" . $kd_gebdatum_tag . '.' . $kd_gebdatum_monat . '.' . $kd_gebdatum_jahr . "</b>";
              ?>
          </div>
        </div>

          <div class="form-group row m-1">
              <label for="kd_verstorben" class="col-sm-4 col-form-label col-form-label-sm">Verstorben:</label>
              <div class="col-sm-7 mt-1">
                  <b>
                  <?php
                  if($row["kd_ableben"] == 1)
                  {
                      echo "ja";
                  }
                  else
                  {
                      echo "nein";
                  }
                  ?>
                  </b>
              </div>
          </div>
        
      </div>
      <div class="col">
        
        <div class="form-group row m-1">
          <label for="kd_besachwaltet" class="col-sm-4 col-form-label col-form-label-sm">Besachwaltet:</label>
          <div class="col-sm-6 mt-1">
              <?php
                $query2 = "SELECT angehoerige.sw_id, sachwalter.sw_typ FROM angehoerige, sachwalter WHERE angehoerige.kd_id = '$kd_id' AND sachwalter.sw_id = angehoerige.sw_id AND angehoerige.ag_aktiv = '1' AND sachwalter.sw_typ = 'sw'";
                $data2 = mysqli_query($dbc, $query2)
                    or die(errorlog($dbc,$query2));
                $row2 = mysqli_fetch_array($data2);

                if(isset($row2["sw_id"]))
                {
                    $kd_sw = 1;
                    echo "<b>ja</b>";
                }
                else
                {
                    $kd_sw = 0;
                    echo "<b>nein</b>";
                }
              ?>
          </div>
        </div>
        <div class="form-group row m-1">
          <label for="kd_sachwalter" class="col-sm-4 col-form-label col-form-label-sm">Sachwalter:</label>
          <div class="col-sm-6 mt-1">
              <?php
                if($kd_sw == 1)
                    {
                        $sw_id = $row2["sw_id"];
                        $query3 = "SELECT sw_id, sw_anrede, sw_vorname, sw_nachname FROM sachwalter WHERE sw_id = '$sw_id'";
                        $data3 = mysqli_query($dbc,$query3)
                            or die(errorlog($dbc,$query3));
                        $row3 = mysqli_fetch_array($data3);
                        echo "<b><a href='swdetails.php?sw_id=" . $row3["sw_id"] ."'>" . $row3["sw_anrede"] . " " . $row3["sw_vorname"] . " " . $row3["sw_nachname"] . "</a></b></br>" ;
                    }
                else
                {
                    echo "<b>keiner</b>";
                }
              ?>
          </div>
        </div>
        <div class="form-group row m-1">
          <label for="kd_tel1" class="col-sm-4 col-form-label col-form-label-sm">Festnetz:</label>
          <div class="col-sm-6 mt-1">
              <?php echo "<b>" . $row["kd_tel1"] . "</b>"; ?>
          </div>
        </div>
        <div class="form-group row m-1">
          <label for="kd_tel2" class="col-sm-4 col-form-label col-form-label-sm">Mobil:</label>
            <div class="col-sm-6 mt-1">
                <?php echo "<b>" . $row["kd_tel2"] . "</b>"; ?>
            </div>
        </div>
        <div class="form-group row m-1">
          <label for="kd_mail" class="col-sm-4 col-form-label col-form-label-sm m-0">E-Mail:</label>
          <div class="col-sm-6 mt-1">
              <?php echo "<b>" . $row["kd_mail"] . "</b>"; ?>
          </div>
        </div>
        <div class="form-group row m-1">
          <label for="kd_rg" class="col-sm-4 col-form-label col-form-label-sm">Rechnung:</label>
          <div class="col-sm-6 mt-1">
              <?php
                if($row["kd_rg_art"] == "p") echo "<b>Post</b>";
                if($row["kd_rg_art"] == "m") echo "<b>Mail</b>";
              ?>
          </div>
        </div>
        
      </div>
    </div>

      <div class="row">
          <div class="col border border-primary mt-4">
              Vertragsdaten:
          </div>
      </div>

      <div class="row">
          <div class="col">
              <div class="form-group row m-1">
                  <label for="kd_aktiv" class="col-sm-4 col-form-label col-form-label-sm">Aktiv:</label>
                  <div class="col-sm-6 mt-1">
                      <b>
                          <?php
                          if($row["kd_aktiv"] == 1)
                          {
                              echo "ja";
                          }
                          else
                          {
                              echo "nein";
                          }
                          ?>
                      </b>
                  </div>
              </div>
              <div class="form-group row m-1">
                  <label for="kd_beginn" class="col-sm-4 col-form-label col-form-label-sm">Beginn:</label>
                  <div class="col-sm-6 mt-1">
                      <b>
                      <?php
                      if($row["kd_beginn"] == NULL)
                      {
                          echo "-";
                      }
                      else
                      {
                          list($kd_beginn_jahr,$kd_beginn_monat,$kd_beginn_tag)=explode("-", $row['kd_beginn']);
                          echo "<b>" . $kd_beginn_tag . '.' . $kd_beginn_monat . '.' . $kd_beginn_jahr . "</b>";

                      }
                      ?>
                      </b>
                  </div>
              </div>
              <div class="form-group row m-1">
                  <label for="kd_ende" class="col-sm-4 col-form-label col-form-label-sm">Ende:</label>
                  <div class="col-sm-6 mt-1">
                      <b>
                          <?php
                          if($row["kd_ende"] == NULL)
                          {
                              echo "-";
                          }
                          else
                          {
                              list($kd_ende_jahr,$kd_ende_monat,$kd_ende_tag)=explode("-", $row['kd_ende']);
                              echo "<b>" . $kd_ende_tag . '.' . $kd_ende_monat . '.' . $kd_ende_jahr . "</b>";

                          }
                          ?>
                      </b>
                  </div>
              </div>
          </div>
      </div>
    
     <div class="row">
      <div class="col border border-primary mt-4">
      Angehörige
      </div>
    </div>
    
    <div class="row">
      <div class="col">
      
         <div class="form-group row m-1">
          <div class="col-sm-6">
              <?php

              $query4 = "SELECT sachwalter.sw_id, sachwalter.sw_anrede, sachwalter.sw_vorname, sachwalter.sw_nachname FROM angehoerige, sachwalter WHERE angehoerige.kd_id = '$kd_id' AND sachwalter.sw_id = angehoerige.sw_id AND angehoerige.ag_aktiv = '1' AND sachwalter.sw_typ = 'ag'";
              $data4 = mysqli_query($dbc, $query4)
                or die(errorlog($dbc,$query4));

              if(mysqli_num_rows($data4) != 0)
              {
                      while ($row4 = mysqli_fetch_array($data4))
                      {
                          echo "<b><a href='agdetails.php?sw_id=" . $row4["sw_id"] . "'>" . $row4["sw_anrede"] . " " . $row4["sw_vorname"] . " " . $row4["sw_nachname"] . " " . "</a></b>";
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
    
     <div class="row">
      <div class="col border border-primary mt-4">
      Rechnungsadresse 
      </div>
    </div>
    <div class="row" id="RgAdresseNeu">
        <div class="form-group row m-1">
            <div class="col">
            <?php

                $query6 = "SELECT ad_id, ad_strasse, ad_nr, ad_stiege, ad_stock, ad_tuer, ad_plz FROM adressen WHERE ad_typ IN('r', 'rb') AND ad_aktiv = '1' AND kd_id = '$kd_id'";
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
    
    <div class="row">
      <div class="col border border-primary mt-4">
      Betreuungsadresse
      </div>
    </div>

    <div class="row">
        <div class="form-group row m-1">
            <div class="col">

            <?php

            $query7 = "SELECT ad_id, ad_strasse, ad_nr, ad_stiege, ad_stock, ad_tuer, ad_plz FROM adressen WHERE ad_typ IN('b', 'rb') AND ad_aktiv = '1' AND kd_id = '$kd_id'";
            $data7 = mysqli_query($dbc, $query7)
            or die(errorlog($dbc, $query7));
            $row7 = mysqli_fetch_array($data7);

            echo "<b>";
            echo  $row7["ad_plz"] . ", " . $row7["ad_strasse"] . " " . $row7["ad_nr"] . addressseperation($row7["ad_stiege"]) . addressseperation($row7["ad_stock"]) . addressseperation($row7["ad_tuer"]);
            echo "</b>";
            ?>
             </div>
        </div>
    </div>

    
    <div class="row">
      <div class="col border border-primary mt-4">
      Wichtige Anmerkung
      </div>
    </div>
    
    <div class="row">
        <div class="form-group row m-1">
            <div class="col">
                <?php

                $query8 = "SELECT ve_text FROM vermerke WHERE kd_id = '$kd_id' AND ve_flag = '1'";
                $data8 = mysqli_query($dbc, $query8)
                or die(errorlog($dbc, $query8));
                $row8 = mysqli_fetch_array($data8);

                echo "<b>";
                echo  $row8["ve_text"];
                echo "</b>";
                ?>
            </div>
        </div>
    </div>

      <div class="row mt-4">
          <a href="kdaendern.php?kd_id=<?php echo $kd_id ?>" name="kd_aendern" id="kd_aendern"  class="btn btn-primary">Datensatz ändern</a>
      </div>
  </div>
</form>
<?php
}

if(isset($error) && $error == 1)
{
    //keine Kundennummer in GET
    if ($kd_id == 0)
    {
        ?>
        <div class="alert alert-warning" role="alert">
            Ungültiger Link. Sie werden zur Übersicht weitergeleitet.
        </div>
        <script>
            window.setTimeout(function(){

                window.location.href = "kdalle.php";

            }, 5000);
        </script>
        <?php
    }

    //kdnr existiert nicht
     if (isset($kd_exist) && $kd_exist == 0 && $kd_id != 0)
     {
         ?>
         <div class="alert alert-warning" role="alert">
             Es existiert kein Kunde mit der Nummer "<?php echo $kd_id ?>";
         </div>
        <?php
     }


}
?>
<!-- MAIN END -->

<?php
include_once('footer.php');
?>
