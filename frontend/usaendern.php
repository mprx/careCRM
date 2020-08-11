<?php
$siteTitle = "User Ändern";
$siteCategory = "Administration";

include_once('header.php');
include_once('nav.php');
?>

<!-- MAIN START -->
<?php

$dbc = mysqli_connect(DB_HOST,DB_USER,DB_PASSWORD, DB_NAME)
    or die(errrorlog($dbc,0));
mysqli_set_charset($dbc,"utf8");

//user id definieren
if (isset($_GET["us_id"]))
{
    $us_id = mysqli_real_escape_string($dbc, trim($_GET['us_id']));
}
else
{
    $us_id = -1;
    $error = 1;
}

//Wenn kein GET gesetzt ist, us_id auf -1 setzen und error anzeigen (unten im file)
if ($us_id == "" || $us_id == 0)
{
    $us_id = -1;
    $error = 1;
}

//prüfen, ob user existiert
$query = "SELECT us_id FROM `user` WHERE us_id = '$us_id' LIMIT 1";
$data = mysqli_query($dbc, $query)
or die(errorlog($dbc,$query));

//wenn user nicht existiert
if (empty(mysqli_fetch_array($data)))
{
    $error = 1;
    $us_exist = 0;
}


if (isset($_POST["submit"]) && ((isset($error) && $error != 1) || !isset($error)))
{
  //form handling
    //variablen für insert vorbereiten

    $us_vorname = mysqli_real_escape_string($dbc, trim($_POST['us_vorname']));
    $us_nachname = mysqli_real_escape_string($dbc, trim($_POST['us_nachname']));
    $us_mail = mysqli_real_escape_string($dbc, trim($_POST['us_mail']));


    if(isset($_POST["us_admin"]))
    {
        $us_admin = 1;
    }
    else
    {
        $us_admin = 0;
    }

    if(isset($_POST["us_status"]))
    {
        $us_status = 1;
    }
    else
    {
        $us_status = 0;
    }


    //Prüfen, ob alle notwendigen Werte vorhanden sind
    if (!empty($us_vorname) && !empty($us_nachname) && !empty($us_mail))
    {
          //sichergehen, dass E-Mail Adresse korrektes Format hat, wenn angegeben
          if (($us_mail != "" && filter_var($us_mail, FILTER_VALIDATE_EMAIL)) || $us_mail == "")
          {
              //in User Tabelle eintragen
            $query = "UPDATE `user` SET " .
                     "us_nachname = '$us_nachname', " .
                     "us_vorname = '$us_vorname', " .
                     "us_mail = '$us_mail', " .
                     "us_admin = '$us_admin', " .
                     "us_status = '$us_status' " .
                     "WHERE us_id = '$us_id'";

              mysqli_query($dbc, $query)
                or die(errorlog($dbc, $query));

            // Dem User den Erfolg mitteilen
            echo '<div class="alert alert-success" role="alert">';
            echo 'Der neue User wurde erfolgreich geändert<br/>';
            echo 'Sie werden zum Akt weitergeleitet...';
            echo '</div>';
            ?>
              <script>
                  redirect('usdetails.php?us_id=<?php echo $us_id; ?>',2000);
              </script>
            <?php

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

if ((isset($error) && $error == 1) || !isset($error))
{
    $dbc = mysqli_connect(DB_HOST,DB_USER,DB_PASSWORD, DB_NAME);
    $query = "SELECT us_name, us_nachname, us_vorname, us_mail, us_admin, us_status FROM `user` WHERE us_id = '$us_id' LIMIT 1";
    $data = mysqli_query($dbc, $query)
        or die(errorlog($dbc,$query));
    $row = mysqli_fetch_array($data);
?>
<form action="<?php echo $_SERVER["PHP_SELF"] . "?us_id=$us_id"; ?>" method="POST" novalidate>
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
            <input type="text" class="form-control form-control-sm" name="us_vorname" id="us_vorname" placeholder="Vorname" value="<?php switchpostvalue('us_vorname'); ?>" required />
          </div>
        </div>
        <div class="form-group row m-1">
          <label for="us_nachname" class="col-sm-4 col-form-label col-form-label-sm">Nachname</label>
          <div class="col-sm-5">
            <input type="text" class="form-control form-control-sm" name="us_nachname" id="us_nachname" placeholder="Nachname" value="<?php switchpostvalue('us_nachname'); ?>" required />
          </div>
        </div>
          <div class="form-group row m-1">
              <label for="us_mail" class="col-sm-4 col-form-label col-form-label-sm">E-Mail</label>
              <div class="col-sm-5">
                  <input type="text" class="form-control form-control-sm" name="us_mail" id="us_mail" placeholder="name@beispiel.at" value="<?php switchpostvalue('us_mail'); ?>" required autocomplete="off" />
              </div>
          </div>
          <div class="form-group row m-1">
              <label for="us_admin" class="col-sm-4 col-form-label col-form-label-sm">Admin:</label>
              <div class="col-sm-5">
                  <input class="form-check-input mt-2 ml-0" type="checkbox" value="1" name="us_admin" id="us_admin" <?php switchpostvaluecheck('us_admin'); ?> />
              </div>
          </div>
      </div>
        <div class="col">

            <div class="form-group row m-1">
                <label for="us_pw1" class="col-sm-4 col-form-label col-form-label-sm m-0">Username:</label>
                <div class="col-sm-5 mt-1">
                    <?php
                    echo "<b>" . $row["us_name"] . "</b>";
                    ?>
                </div>
            </div>
            <div class="form-group row m-1">
                <label for="us_status" class="col-sm-4 col-form-label col-form-label-sm m-0">Aktiv:</label>
                <div class="col-sm-5 mt-1">
                    <input class="form-check-input mt-2  ml-0" type="checkbox" value="1" name="us_status" id="us_status" <?php switchpostvaluecheck('us_status'); ?> />
                </div>
            </div>
        </div>


    </div>

      <input type="submit" name="submit" id="submit" value="Übernehmen" class="btn btn-primary mt-5 ml-4" />
      <a href="usdetails.php?us_id=<?php echo $us_id; ?>" class="btn btn-primary mt-5 ml-4">Verwerfen</a>
  </div>
</form>
<?php
}
?>
<!-- MAIN END -->

<?php
include_once('footer.php');
?>
