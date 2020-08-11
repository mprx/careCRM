<?php
$siteTitle = "Details";
$siteCategory = "Pflegeplan";

include_once('header.php');
include_once('nav.php');
?>

<!-- MAIN START -->
<?php

$dbc = mysqli_connect(DB_HOST,DB_USER,DB_PASSWORD, DB_NAME)
    or die(errrorlog($dbc,0));
mysqli_set_charset($dbc,"utf8");

if (isset($_GET["pp_id"]))
{
    $pp_id = mysqli_real_escape_string($dbc, trim($_GET['pp_id']));
}
else
{
    $pp_id = 0;
    $error = 1;
}
if ($pp_id == "")
{
    $pp_id = 0;
    $error = 1;
}

if($pp_id != 0)
{
    $query = "SELECT pp_id FROM pflegeplan WHERE pp_id = '$pp_id' LIMIT 1";
    $data = mysqli_query($dbc, $query)
    or die(errorlog($dbc,query));

//wenn pp id nicht existiert
    if (empty(mysqli_fetch_array($data)))
    {
        $error = 1;
        $pp_exist = 0;
    }
}

if(!isset($error) && !isset($_GET["delete2"]))
{
    $query = "SELECT kunde.kd_id, pfleger.pg_id, pflegeplan.pp_beginn, pflegeplan.pp_ende FROM kunde, pfleger, pflegeplan WHERE pflegeplan.pp_id = '$pp_id' AND kunde.kd_id = pflegeplan.kd_id AND pfleger.pg_id = pflegeplan.pg_id";
    $data = mysqli_query($dbc, $query)
        or die(errorlog($dbc,$query));
    $row = mysqli_fetch_array($data);


    $kd_id = $row["kd_id"];
    $pg_id = $row["pg_id"];
    $pp_beginn = $row["pp_beginn"];
    $pp_ende = $row["pp_ende"];
}
else
{
    if (($pp_id == 0) || (isset($pp_exist) && $pp_exist == 0 && $pp_id != 0))
    {
        ?>
        <div class="alert alert-warning" role="alert">
            Ungültiger Link. Sie werden zur Übersicht weitergeleitet.
        </div>
        <script>
            redirect("pphb.php", 5000);
        </script>
        <?php
        exit();
    }
}

if (isset($_POST["submit"]))
{
  //form handling
    //variablen für insert vorbereiten
    //Name
    $kd_id_neu = mysqli_real_escape_string($dbc, trim($_POST['kd_id']));
    $pg_id_neu = mysqli_real_escape_string($dbc, trim($_POST['pg_id']));
    $pp_beginn_neu =  mysqli_real_escape_string($dbc, trim($_POST['pp_beginn']));
    $pp_ende_neu = mysqli_real_escape_string($dbc, trim($_POST['pp_ende']));


    if(empty($pp_ende_neu))
    {
        $pp_ende_neu = "NULL";
    }
    else
    {
        $pp_ende_neu = "'" . $pp_ende_neu . "'";
    }


    $pp_verrechnung = 0;

    //Prüfen, ob alle notwendigen Werte vorhanden sind
    if (!empty($kd_id_neu) && !empty($pg_id_neu) && !empty($pp_beginn_neu))
    {
        //prüfen ob enddatum größer als startdatum ist
        if(($pp_ende_neu != "NULL" && $pp_ende_neu >= $pp_beginn_neu) || $pp_ende_neu == "NULL")
        {
        //in PP Tabelle eintragen
            $query = "UPDATE pflegeplan SET " .
                     "kd_id = '$kd_id_neu', ".
                     "pg_id = '$pg_id_neu', ".
                     "pp_beginn = '$pp_beginn_neu', ".
                     "pp_ende = $pp_ende_neu, ".
                     "pp_verrechnung = '$pp_verrechnung' " .
                     "WHERE pp_id = '$pp_id'";

              mysqli_query($dbc, $query)
              or die(errorlog($dbc, $query));


            // Dem User den Erfolg mitteilen
            echo '<div class="alert alert-success" role="alert">';
            echo 'Die Tätigkeit wurde erfolgreich geändert';
            echo '</div>';
            echo '<p><a href="ppneu.php">Neue Tätigkeit anlegen</a></p>';




        }
        else
        {
            //Enddatum kleiner als Startdatum
            echo '<div class="alert alert-warning" role="alert">';
            echo 'Das Enddatum kann nicht vor dem Beginndatum liegen.';
            echo '</div>';
            $error = 1;
        }
    }
    else
    {
        //Es wurden nicht alle notwendigen Felder ausgefüllt
        echo '<div class="alert alert-warning" role="alert">';
        echo 'Bitte alle Pflichtfelder ausfüllen!';
        echo '</div>';
        $error = 1;
    }
}

if (isset($_GET["delete"]) && !isset($error))
{
    if(!isset($_GET["delete2"]))
    {
        echo '<div class="alert alert-warning" role="alert">';
        echo 'Wollen Sie diesen Eintrag wirklich löschen?</br>';
        echo '<a href="ppdetails.php?pp_id=' . $pp_id . '&delete=1&delete2=1" class="btn btn-danger">Löschen</a>';
        echo ' <a href="ppdetails.php?pp_id=' . $pp_id . '" class="btn btn-primary">Abbrechen</a>';
        echo '</div>';
    }
    else
    {
        $query = "DELETE FROM pflegeplan WHERE pp_id = '$pp_id'";
        mysqli_query($dbc,$query)
            or die(errorlog($dbc,$query));

        echo '<div class="alert alert-success" role="alert">';
        echo 'Der Eintrag wurde erfolgreich gelöscht.';
        echo '</div>';
        exit();
    }
}

//Ausgabe des $_POST Arrays für debugzwecke
/*
  echo '<div class="container">';
  echo '<table>';
    foreach ($_POST as $key => $value)
    {
        echo "<tr>";
        echo "<td>";
        echo $key;
        echo "</td>";
        echo "<td>";
        echo $value;
        echo "</td>";
        echo "</tr>";
    }
  echo '</table></div>';
*/


?>
<form action="<?php echo $_SERVER["PHP_SELF"] . "?pp_id=" . $pp_id; ?>" method="POST">
  <div class="container">
    <div class="row">
      <div class="col border border-primary">
      Tätigkeit Nr. <?php echo $pp_id; ?>
      </div>
    </div>
    <div class="row">
      <div class="col">
        
        <div class="form-group row m-1">
          <label for="kd_id" class="col-sm-2 col-form-label col-form-label-sm">Kunde:</label>
          <div class="col-sm-8">
            <select name="kd_id" id="kd_id" disabled>
                <?php
                $query2 = "SELECT kd_id, kd_vorname, kd_nachname FROM kunde WHERE kd_ableben = '0' ORDER BY kd_nachname";
                $data2 = mysqli_query($dbc, $query2)
                    or die(errorlog($dbc,$query2));
                while($row2 = mysqli_fetch_array($data2))
                {
                    echo '<option value="' . $row["kd_id"] . '"';
                    if((isset($_POST["submit"]) && $kd_id_neu == $row2["kd_id"]) || (!isset($_POST["submit"]) && $kd_id == $row2["kd_id"])) echo ' selected';
                    echo '>' . $row2["kd_nachname"] . ' ' . $row2["kd_vorname"] . '</option>';
                }
                ?>
            </select>
              <a class="btn btn-link btn-sm mb-1" href="kddetails.php?kd_id=<?php if(isset($kd_id_neu)){echo $kd_id_neu;}else{echo $kd_id;} ?>">zum Kundenakt</a>
          </div>
        </div>
          <div class="form-group row m-1">
              <label for="pg_id" class="col-sm-2 col-form-label col-form-label-sm">Pfleger:</label>
              <div class="col-sm-5">
                  <select name="pg_id" id="pg_id" disabled>
                      <optgroup label="24h Pfleger:">
                      <?php
                      $query3 = "SELECT pg_id, pg_vorname, pg_nachname FROM pfleger WHERE pg_art = '24h' ORDER BY pg_nachname";
                      $data3 = mysqli_query($dbc, $query3)
                      or die(errorlog($dbc,$query3));
                      while($row3 = mysqli_fetch_array($data3))
                      {
                          echo '<option value="' . $row3["pg_id"] . '"';
                          if((isset($_POST["submit"]) && $pg_id_neu == $row3["pg_id"]) || (!isset($_POST["submit"]) && $pg_id == $row3["pg_id"])) echo ' selected';
                          echo '>' . $row3["pg_nachname"] . ' ' . $row3["pg_vorname"] . '</option>';
                      }
                      ?>
                      </optgroup>
                      <optgroup label="Stundenweise:">
                          <?php
                          $query4 = "SELECT pg_id, pg_vorname, pg_nachname FROM pfleger WHERE pg_art = 'sw' ORDER BY pg_nachname";
                          $data4 = mysqli_query($dbc, $query4)
                          or die(errorlog($dbc,$query4));
                          while($row4 = mysqli_fetch_array($data4))
                          {
                              echo '<option value="' . $row4["pg_id"] . '"';
                              if((isset($_POST["submit"]) && $pg_id_neu == $row4["pg_id"]) || (!isset($_POST["submit"]) && $pg_id == $row4["pg_id"])) echo ' selected';
                              echo '>' . $row4["pg_nachname"] . ' ' . $row4["pg_vorname"] . '</option>';
                          }
                          ?>
                      </optgroup>
                      <optgroup label="DGKS/P:">
                          <?php
                          $query5 = "SELECT pg_id, pg_vorname, pg_nachname FROM pfleger WHERE pg_art = 'dgks' ORDER BY pg_nachname";
                          $data5 = mysqli_query($dbc, $query5)
                          or die(errorlog($dbc,$query5));
                          while($row5 = mysqli_fetch_array($data5))
                          {
                              echo '<option value="' . $row5["pg_id"] . '"';
                              if((isset($_POST["submit"]) && $pg_id_neu == $row5["pg_id"]) || (!isset($_POST["submit"]) && $pg_id == $row5["pg_id"])) echo ' selected';
                              echo '>' . $row5["pg_nachname"] . ' ' . $row5["pg_vorname"] . '</option>';
                          }
                          ?>
                      </optgroup>
                  </select>
                  <a class="btn btn-link btn-sm mb-1" href="pgdetails.php?pg_id=<?php if(isset($pg_id_neu)){echo $pg_id_neu;}else{echo $pg_id;} ?>">zum Pflegerakt</a>
              </div>
          </div>
          <div class="form-group row m-1">
              <label for="pp_beginn" class="col-sm-2 col-form-label col-form-label-sm">Beginn:</label>
              <div class="col-sm-5">
                <input type="date" min="2000-01-01" max="2200-12-31" name="pp_beginn" id="pp_beginn" value="<?php switchpostvalue("pp_beginn", $pp_beginn); ?>" />
              </div>
          </div>
          <div class="form-group row m-1 mt-2">
              <label for="pp_ende_offen" class="col-sm-2 col-form-label col-form-label-sm">Ende:</label>
              <div class="col-sm-5">
                  <input type="checkbox" name="pp_ende_offen" id="pp_ende_offen" value="1" <?php if((isset($_POST["submit"]) && $pp_ende_neu == "NULL") || (!isset($_POST["submit"]) && $pp_ende == "")) echo 'checked'; ?> onclick="toggleFieldInversed('pp_ende_offen','pp_ende_div'); clearfield('pp_ende')" /> offen
              </div>
          </div>
          <div class="form-group row m-1" id="pp_ende_div" style="<?php if((isset($_POST["submit"]) && $pp_ende_neu == "NULL") || (!isset($_POST["submit"]) && $pp_ende == "")) echo 'display: none;'; ?> ">
              <label for="pp_ende" class="col-sm-2 col-form-label col-form-label-sm"></label>
              <div class="col-sm-5">
                  <input type="date" min="2000-01-01" max="2200-12-31" name="pp_ende" id="pp_ende" value="<?php switchpostvalue("pp_ende", $pp_ende); ?>"/>
              </div>
          </div>

        


    



          <?php
          if(!isset($_GET["delete"]))
          {
              ?>
              <input type="submit" name="submit" id="submit" value="Ändern" class="btn btn-primary mt-2 mb-3" />
              <a href="ppdetails.php?pp_id=<?php echo $pp_id ?>&delete=1" class="btn btn-danger mt-2 mb-3">Löschen</a>
              </br>
          <?php
              if(isset($_GET["kd_id"]))
              {
                  echo '<a href="kddetails.php?kd_id=' . $_GET["kd_id"] . '">zurück zum Kundenakt</a>';
              }
              if(isset($_GET["pg_id"]))
              {
                  echo '<a href="pgdetails.php?pg_id=' . $_GET["pg_id"] . '">zurück zum Pflegerakt</a>';
              }
          }
          ?>

            
  </div>
</form>
<?php

mysqli_close($dbc);
?>
<!-- MAIN END -->

<?php
include_once('footer.php');
?>
