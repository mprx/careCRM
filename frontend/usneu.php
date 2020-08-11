<?php
$siteTitle = "Neuer User";
$siteCategory = "Administration";

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

    $us_vorname = mysqli_real_escape_string($dbc, trim($_POST['us_vorname']));
    $us_nachname = mysqli_real_escape_string($dbc, trim($_POST['us_nachname']));
    $us_mail = mysqli_real_escape_string($dbc, trim($_POST['us_mail']));
    $us_pw = passwordgen();

    $us_name = substr(strtolower($us_vorname),0,1) . substr(strtolower($us_nachname),0,5);

    if(isset($_POST["us_admin"]))
    {
        $us_admin = 1;
    }
    else
    {
        $us_admin = 0;
    }

    //Prüfen, ob alle notwendigen Werte vorhanden sind
    if (!empty($us_vorname) && !empty($us_nachname) && !empty($us_mail))
    {
      // Sicher gehen, dass der user nicht schon existiert
      $query = "SELECT us_id FROM `user` WHERE us_vorname = '$us_vorname' AND us_nachname = '$us_nachname' LIMIT 1";
      $data = mysqli_query($dbc, $query)
        or die(errorlog($dbc,$query));


      if (mysqli_num_rows($data) != 0)
      {
          // Da es den User mit Vor- und Nachnamen bereits gibt, dem usernamen die nächste ID zuweisen:
          $query2 = "SELECT Auto_increment FROM information_schema.tables WHERE table_name='user' LIMIT 1, 1";
          $data2 = mysqli_query($dbc, $query2)
          or die(errorlog($dbc,$query2));
          $row2 = mysqli_fetch_array($data2);

          $us_name = $us_name . $row2["Auto_increment"];
      }
          //sichergehen, dass E-Mail Adresse korrektes Format hat, wenn angegeben
          if (($us_mail != "" && filter_var($us_mail, FILTER_VALIDATE_EMAIL)) || $us_mail == "")
          {
              //in User Tabelle eintragen
            $query = "INSERT INTO `user` " .
                     "(us_name, us_pw, us_mail, us_vorname, us_nachname, us_status, us_admin) " .
                     "VALUES" .
                     "('$us_name', SHA('$us_pw'), '$us_mail', '$us_vorname', '$us_nachname', '1', '$us_admin')";

              mysqli_query($dbc, $query)
              or die(errorlog($dbc, $query));

            $us_id = mysqli_insert_id($dbc);

            // Dem User den Erfolg mitteilen
            echo '<div class="alert alert-success" role="alert">';
            echo 'Der neue User wurde erfolgreich angelegt<br/><br/>';
            echo 'Userdaten:<br/>';
            echo 'Username: ' . $us_name . '<br/>';
            echo 'Passwort: ' . $us_pw . '<br/>';
            echo '</div>';
            echo '<p><a href="usdetails.php?us_id=' . $us_id . '">Userdetails</a></p>';
            echo '<p><a href="usneu.php">Neuen User anlegen</a></p>';


            mysqli_close($dbc);
            exit();
          }
          else
          {
              //E-Mail Adresse ist im falschen Format
              echo '<div class="alert alert-warning" role="alert">';
              echo 'Bitte die E-Mail Adresse im Format "name@beispiel.at" angeben!';
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
      Userdaten:
      </div>
    </div>
    <div class="row">
      <div class="col">

        <div class="form-group row m-1">
          <label for="us_vorname" class="col-sm-4 col-form-label col-form-label-sm">Vorname</label>
          <div class="col-sm-5">
            <input type="text" class="form-control form-control-sm" name="us_vorname" id="us_vorname" placeholder="Vorname" value="<?php keeppostvalue('us_vorname'); ?>" required />
          </div>
        </div>
        <div class="form-group row m-1">
          <label for="us_nachname" class="col-sm-4 col-form-label col-form-label-sm">Nachname</label>
          <div class="col-sm-5">
            <input type="text" class="form-control form-control-sm" name="us_nachname" id="us_nachname" placeholder="Nachname" value="<?php keeppostvalue('us_nachname'); ?>" required />
          </div>
        </div>
          <div class="form-group row m-1">
              <label for="us_mail" class="col-sm-4 col-form-label col-form-label-sm">E-Mail</label>
              <div class="col-sm-5">
                  <input type="text" class="form-control form-control-sm" name="us_mail" id="us_mail" placeholder="name@beispiel.at" value="<?php keeppostvalue('us_mail'); ?>" required autocomplete="off" />
              </div>
          </div>
          <div class="form-group row m-1">
              <label for="us_admin" class="col-sm-4 col-form-label col-form-label-sm">Admin:</label>
              <div class="col-sm-5">
                  <input class="form-check-input mt-2 ml-0" type="checkbox" value="1" name="us_admin" id="us_admin" <?php if(isset($_POST["us_admin"])) echo 'checked="checked"'; ?> />
              </div>
          </div>
      </div>
      <div class="col">
          <br/>
          Das Passwort für den User wird automatisch generiert!
   <!--
        <div class="form-group row m-1">
          <label for="us_pw1" class="col-sm-4 col-form-label col-form-label-sm m-0">Passwort:</label>
          <div class="col-sm-5">
            <input type="text" class="form-control form-control-sm" name="us_pw1" id="us_pw1" required autocomplete="off" />
          </div>
        </div>
          <div class="form-group row m-1">
              <label for="us_pw2" class="col-sm-4 col-form-label col-form-label-sm m-0">Passwort wiederholen:</label>
              <div class="col-sm-5">
                  <input type="text" class="form-control form-control-sm" name="us_pw2" id="us_pw2" required autocomplete="off" />
              </div>
          </div>
          -->
      </div>


    </div>

      <input type="submit" name="submit" id="submit" value="Datensatz anlegen" class="btn btn-primary mt-5 ml-4" />







  </div>
</form>
<?php
}
?>
<!-- MAIN END -->

<?php
include_once('footer.php');
?>
