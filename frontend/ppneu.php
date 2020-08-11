<?php
$siteTitle = "Neue Tätigkeit";
$siteCategory = "Pflegeplan";

include_once('header.php');
include_once('nav.php');
?>

<!-- MAIN START -->
<?php

$dbc = mysqli_connect(DB_HOST,DB_USER,DB_PASSWORD, DB_NAME)
    or die(errrorlog($dbc,0));
mysqli_set_charset($dbc,"utf8");

if (isset($_GET["hb"]))
{
    $hb = 1;
}
else
{
    $hb = 0;
}

if (isset($_GET["kd_id"]))
{
    $kd_id = mysqli_real_escape_string($dbc, trim($_GET['kd_id']));
}
else
{
    $kd_id = 0;
}
if ($kd_id == "")
{
    $kd_id = 0;
}

if (isset($_GET["pg_id"]))
{
    $pg_id = mysqli_real_escape_string($dbc, trim($_GET['pg_id']));
}
else
{
    $pg_id = 0;
}
if ($pg_id == "")
{
    $pg_id = 0;
}

if (isset($_POST["submit"]))
{
  //form handling
    //variablen für insert vorbereiten
    //Name
    $kd_id = mysqli_real_escape_string($dbc, trim($_POST['kd_id']));
    $pg_id = mysqli_real_escape_string($dbc, trim($_POST['pg_id']));
    $pp_beginn = mysqli_real_escape_string($dbc, trim($_POST['pp_beginn']));
    $pp_ende = mysqli_real_escape_string($dbc, trim($_POST['pp_ende']));

    if($pp_ende == "hb")
    {
        $pp_ende = $pp_beginn;
    }

    if(isset($_POST['pp_ende_offen']))
    {
        $pp_ende_value = "NULL";
    }
    else
    {
        $pp_ende_value = "'$pp_ende'";
    }


    $pp_verrechnung = 0;

    //Prüfen, ob alle notwendigen Werte vorhanden sind
    if (!empty($kd_id) && !empty($pg_id) && !empty($pp_beginn))
    {
        //prüfen ob enddatum größer als startdatum ist
        if((!isset($_POST['pp_ende_offen']) && $pp_ende >= $pp_beginn) || isset($_POST['pp_ende_offen']))
        {
        //in PP Tabelle eintragen
            $query = "INSERT INTO pflegeplan " .
                     "(kd_id, pg_id, pp_beginn, pp_ende, pp_verrechnung) " .
                     "VALUES" .
                     "('$kd_id', '$pg_id', '$pp_beginn', " . $pp_ende_value . ", '$pp_verrechnung')";

              mysqli_query($dbc, $query)
              or die(errorlog($dbc, $query));


            // Dem User den Erfolg mitteilen
            echo '<div class="alert alert-success" role="alert">';
            echo 'Die neue Tätigkeit wurde erfolgreich angelegt';
            echo '</div>';
            echo '<p><a href="kddetails.php?kd_id=' . $kd_id . '">Zum Kundenakt</a></p><br>';
            echo '<p><a href="pgdetails.php?pg_id=' . $pg_id . '">Zum Pflegerakt</a></p><br><br>';
            echo '<p><a href="ppneu.php">Neue Tätigkeit anlegen</a></p>';


            mysqli_close($dbc);
            exit();
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


if ((isset($error) && $error == 1) || !isset($error))
{
?>
<form action="<?php echo $_SERVER["PHP_SELF"] ?>" method="POST">
  <div class="container">
    <div class="row">
      <div class="col border border-primary">
          <?php
          if($hb == 1) echo "Hausbesuch";
          if($hb == 0) echo "Tätigkeit";
          ?>
      </div>
    </div>
    <div class="row">
      <div class="col">
        
        <div class="form-group row m-1">
          <label for="kd_id" class="col-sm-2 col-form-label col-form-label-sm">Kunde:</label>
          <div class="col-sm-6 col-lg-3">
            <select name="kd_id" id="kd_id" class="form-control form-control-sm">
                <?php
                $query = "SELECT kd_id, kd_vorname, kd_nachname FROM kunde WHERE kd_ableben = '0' ORDER BY kd_nachname";
                $data = mysqli_query($dbc, $query)
                    or die(errorlog($dbc,$query));
                while($row = mysqli_fetch_array($data))
                {
                    echo '<option value="' . $row["kd_id"] . '"';
                    if($kd_id == $row["kd_id"]) echo ' selected';
                    echo '>' . $row["kd_nachname"] . ' ' . $row["kd_vorname"] . '</option>';
                }
                ?>
            </select>
          </div>
        </div>
          <div class="form-group row m-1">
              <label for="pg_id" class="col-sm-2 col-form-label col-form-label-sm">
                  <?php
                  if($hb == 1) echo "DGKS/P";
                  if($hb == 0) echo "Pfleger";
                  ?>
              </label>
              <div class="col-sm-6 col-lg-3"">
                  <select name="pg_id" id="pg_id" class="form-control form-control-sm">
                      <?php
                      if($hb == 0)
                      {
                      ?>
                      <optgroup label="24h Pfleger:">
                      <?php
                      $query = "SELECT pg_id, pg_vorname, pg_nachname FROM pfleger WHERE pg_art = '24h' ORDER BY pg_nachname";
                      $data = mysqli_query($dbc, $query)
                      or die(errorlog($dbc,$query));
                      while($row = mysqli_fetch_array($data))
                      {
                          echo '<option value="' . $row["pg_id"] . '"';
                          if($pg_id == $row["pg_id"]) echo ' selected';
                          echo '>' . $row["pg_nachname"] . ' ' . $row["pg_vorname"] . '</option>';
                      }
                      ?>
                      </optgroup>
                      <optgroup label="Stundenweise:">
                          <?php
                          $query = "SELECT pg_id, pg_vorname, pg_nachname FROM pfleger WHERE pg_art = 'sw' ORDER BY pg_nachname";
                          $data = mysqli_query($dbc, $query)
                          or die(errorlog($dbc,$query));
                          while($row = mysqli_fetch_array($data))
                          {
                              echo '<option value="' . $row["pg_id"] . '"';
                              if($pg_id == $row["pg_id"]) echo ' selected';
                              echo '>' . $row["pg_nachname"] . ' ' . $row["pg_vorname"] . '</option>';
                          }
                          ?>
                      </optgroup>
                          <?php
                      }
                      ?>
                      <optgroup label="DGKS/P:">
                          <?php
                          $query = "SELECT pg_id, pg_vorname, pg_nachname FROM pfleger WHERE pg_art = 'dgks' ORDER BY pg_nachname";
                          $data = mysqli_query($dbc, $query)
                          or die(errorlog($dbc,$query));
                          while($row = mysqli_fetch_array($data))
                          {
                              echo '<option value="' . $row["pg_id"] . '"';
                              if($pg_id == $row["pg_id"]) echo ' selected';
                              echo '>' . $row["pg_nachname"] . ' ' . $row["pg_vorname"] . '</option>';
                          }
                          ?>
                      </optgroup>
                  </select>
              </div>
          </div>
          <div class="form-group row m-1">
              <label for="pp_beginn" class="col-sm-2 col-form-label col-form-label-sm">
                  <?php
                  if($hb == 1) echo "Datum:";
                  if($hb == 0) echo "Beginn:";
                  ?>
              </label>
              <div class="col-sm-6 col-lg-3"">
                <input type="date" class="form-control form-control-sm" name="pp_beginn" id="pp_beginn" min="2000-01-01" max="2200-12-31" value="<?php if(isset($_POST["submit"])){echo $_POST["pp_beginn"];}else{echo date('Y-m-d', time());} ?>" />
              </div>
          </div>
          <?php
          if($hb == 0)
          {
          ?>
          <div class="form-group row m-1 mt-4">
              <label for="pp_ende_offen" class="col-sm-2 col-form-label col-form-label-sm">Ende:</label>
              <div class="col-sm-5">
                  <input type="checkbox" name="pp_ende_offen" id="pp_ende_offen" value="1" checked onclick="toggleFieldInversed('pp_ende_offen','pp_ende_div')" /> offen
              </div>
          </div>
          <div class="form-group row m-1" id="pp_ende_div" style="display: none;">
              <label for="pp_ende_offen" class="col-sm-2 col-form-label col-form-label-sm"></label>
              <div class="col-sm-5">
                  <input type="date" name="pp_ende" id="pp_ende" min="2000-01-01" max="2200-12-31" value="<?php if(isset($_POST["submit"])){echo $_POST["pp_ende"];}else{echo date('Y-m-d', time());} ?>"/>
              </div>
          </div>
          <?php
          }
          else
          {
          ?>
          <input type="text" name="pp_ende" id="pp_ende" value="hb" hidden />
          <?php
          }
          ?>

        


    



    
    <input type="submit" name="submit" id="submit" value="

<?php
    if($hb == 1) echo "Hausbesuch anlegen";
    if($hb == 0) echo "Tätigkeit anlegen";
    ?>
" class="btn btn-primary mt-5" />
            
  </div>
</form>
<?php
}
mysqli_close($dbc);
?>
<!-- MAIN END -->

<?php
include_once('footer.php');
?>
