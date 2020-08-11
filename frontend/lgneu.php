<?php
$siteTitle = "Neue Leistung";
$siteCategory = "Leistungen";

include_once('header.php');
include_once('nav.php');
?>

<!-- MAIN START -->
<?php

$dbc = mysqli_connect(DB_HOST,DB_USER,DB_PASSWORD, DB_NAME)
    or die(errrorlog($dbc,0));
mysqli_set_charset($dbc,"utf8");


if (isset($_POST["submit"]))
{
  //form handling
    //variablen für insert vorbereiten
    //Name
    $lg_bezeichnung = mysqli_real_escape_string($dbc, trim($_POST['lg_bezeichnung']));
    $lg_einheit = mysqli_real_escape_string($dbc, trim($_POST['lg_einheit']));
    $lg_rabattgrenze = mysqli_real_escape_string($dbc, trim($_POST['lg_rabattgrenze']));
    $lg_einzeltarif = mysqli_real_escape_string($dbc, trim($_POST['lg_einzeltarif']));
    $lg_mengentarif = mysqli_real_escape_string($dbc, trim($_POST['lg_mengentarif']));
    $lg_jahr = mysqli_real_escape_string($dbc, trim($_POST['lg_jahr']));

    if($lg_rabattgrenze == '' || $lg_rabattgrenze == 0)
    {
        $lg_rabattgrenze = "NULL";
    }

    if($lg_mengentarif == '' || $lg_mengentarif == 0)
    {
        $lg_mengentarif = "NULL";
    }


    //Prüfen, ob alle notwendigen Werte vorhanden sind
    if (!empty($lg_bezeichnung) && !empty($lg_einheit) && !empty($lg_einzeltarif) && !empty($lg_jahr))
    {
      // Sicher gehen, dass die leistung nicht schon existiert
      $query = "SELECT lg_id FROM leistung WHERE lg_bezeichnung = '$lg_bezeichnung' AND lg_jahr = '$lg_jahr' AND lg_einheit = '$lg_einheit' AND lg_einzeltarif = '$lg_einzeltarif' AND lg_mengentarif = '$lg_mengentarif' AND lg_rabattgrenze = '$lg_rabattgrenze' LIMIT 1";
      $data = mysqli_query($dbc, $query)
        or die(errorlog($dbc,$query));

      //Wenn leistung nicht existiert:
      if (mysqli_num_rows($data) == 0)
      {
          //strings umwandeln (Kommas in Punkte)
          str_replace(",", ".", $lg_einzeltarif);
          str_replace(",", ".", $lg_mengentarif);

              //in leistung Tabelle eintragen
            $query = "INSERT INTO leistung " .
                     "(lg_bezeichnung, lg_einheit, lg_rabattgrenze, lg_einzeltarif, lg_mengentarif, lg_jahr) " .
                     "VALUES" .
                     "('$lg_bezeichnung', '$lg_einheit', $lg_rabattgrenze, '$lg_einzeltarif', $lg_mengentarif, '$lg_jahr')";

              mysqli_query($dbc, $query)
              or die(errorlog($dbc, $query));


            // Dem User den Erfolg mitteilen
            echo '<div class="alert alert-success" role="alert">';
            echo 'Die neue Leistung wurde erfolgreich angelegt';
            echo '</div>';
            echo '<p><a href="lgneu.php">Neue Leistung anlegen</a></p><br>';
            echo '<p><a href="lgalle.php?lg_jahr=' . $lg_jahr . '">Leistungen für ' . $lg_jahr . ' anzeigen</a></p>';


            mysqli_close($dbc);
            exit();
      }
      else
      {
          // Leistung existiert bereits
          echo '<div class="alert alert-warning" role="alert">';
          echo 'Diese Leistung existiert bereits.';
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

    mysqli_close($dbc);
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
      Leistung
      </div>
    </div>
    <div class="row">
      <div class="col">
        
        <div class="form-group row m-1">
          <label for="lg_bezeichnung" class="col-sm-4 col-form-label col-form-label-sm">Bezeichnung:</label>
          <div class="col-sm-8">
              <input type="text" class="form-control form-control-sm" name="lg_bezeichnung" id="lg_bezeichnung" placeholder="Leistungsbezeichnung" required value="<?php keeppostvalue('lg_bezeichnung'); ?>" />
          </div>
        </div>
          <div class="form-group row m-1">
              <label for="lg_jahr" class="col-sm-4 col-form-label col-form-label-sm">Jahr (Preisbasis):</label>
              <div class="col-sm-5">
                  <input type="number" class="form-control form-control-sm" name="lg_jahr" id="lg_jahr" step="1" min="2000" max="3000"  value="<?php if(isset($_POST["submit"])){keeppostvalue('lg_jahr');}else{echo date("Y");} ?>" required />
              </div>
          </div>
          <div class="form-group row m-1">
              <label for="lg_einheit" class="col-sm-4 col-form-label col-form-label-sm">Einheit:</label>
              <div class="col-sm-5">
                <select id="lg_einheit" name="lg_einheit" class="form-control form-control-sm">
                    <option value="h" <?php if(isset($_POST["lg_einheit"]) && $_POST["lg_einheit"] == "h") echo 'selected="selected"'; ?>>Stunde</option>
                    <option value="d" <?php if(isset($_POST["lg_einheit"]) && $_POST["lg_einheit"] == "d") echo 'selected="selected"'; ?>>Tag</option>
                    <option value="w" <?php if(isset($_POST["lg_einheit"]) && $_POST["lg_einheit"] == "w") echo 'selected="selected"'; ?>>Woche</option>
                    <option value="m" <?php if(isset($_POST["lg_einheit"]) && $_POST["lg_einheit"] == "m") echo 'selected="selected"'; ?>>Monat</option>
                    <option value="s" <?php if(isset($_POST["lg_einheit"]) && $_POST["lg_einheit"] == "s") echo 'selected="selected"'; ?>>Stück</option>
                    <option value="b" <?php if(isset($_POST["lg_einheit"]) && $_POST["lg_einheit"] == "b") echo 'selected="selected"'; ?>>Besuch</option>
                    <option value="e" <?php if(isset($_POST["lg_einheit"]) && $_POST["lg_einheit"] == "e") echo 'selected="selected"'; ?>>Einheit</option>
                </select>
              </div>
          </div>
        
      </div>
      <div class="col">
        <div class="form-group row m-1">
          <label for="lg_einzeltarif" class="col-sm-4 col-form-label col-form-label-sm">Einzeltarif:</label>
          <div class="col-sm-5">
            <input type="number" min="0.01" step="0.01" class="form-control form-control-sm" name="lg_einzeltarif" id="lg_einzeltarif" placeholder="99,99" value="<?php keeppostvalue('lg_einzeltarif'); ?>" />
          </div>
        </div>
          <div class="form-group row m-1 mt-4">
              <label for="lg_mengentarif" class="col-sm-4 col-form-label col-form-label-sm">Mengentarif:</label>
              <div class="col-sm-5">
                  <input type="number" min="0.01" step="0.01" class="form-control form-control-sm" name="lg_mengentarif" id="lg_mengentarif" placeholder="99,99" value="<?php keeppostvalue('lg_mengentarif'); ?>" />
              </div>
          </div>
          <div class="form-group row m-1">
              <label for="lg_rabattgrenze" class="col-sm-4 col-form-label col-form-label-sm">Ab (Anzahl):</label>
              <div class="col-sm-5">
                  <input type="number" min="0" step="1" class="form-control form-control-sm" name="lg_rabattgrenze" id="lg_rabattgrenze" placeholder="0" value="<?php keeppostvalue('lg_rabattgrenze'); ?>" />
              </div>
          </div>
        
      </div>
    </div>
    



    
    <input type="submit" name="submit" id="submit" value="Leistung anlegen" class="btn btn-primary mt-0" />
            
  </div>
</form>
<?php
}
?>
<!-- MAIN END -->

<?php
include_once('footer.php');
?>
