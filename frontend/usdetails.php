<?php
$siteTitle = "Userdetails";
$siteCategory = "Administration";

include_once('header.php');
include_once('nav.php');
?>

<!-- MAIN START -->
<?php
$dbc = mysqli_connect(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);

//user id definieren
if (isset($_GET["us_id"]))
{
    $us_id = mysqli_real_escape_string($dbc, trim($_GET['us_id']));
}
else
{
    $us_id = 0;
    $error = 1;
}

//Wenn kein GET gesetzt ist, us_id auf 0 setzen und error anzeigen (unten im file)
if ($us_id == "")
{
    $us_id = 0;
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

if ((isset($error) && $error == 0) || !isset($error))
{

    $query = "SELECT us_name, us_mail, us_nachname, us_vorname, us_admin, us_status FROM `user` WHERE us_id = '$us_id'";
    $data = mysqli_query($dbc, $query)
        or die(errorlog($dbc,$query));
    $row = mysqli_fetch_array($data);
?>

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
          <div class="col-sm-5 mt-1">
          <?php echo "<b>" . $row["us_vorname"] . "</b>"; ?>
          </div>
        </div>
        <div class="form-group row m-1">
          <label for="us_nachname" class="col-sm-4 col-form-label col-form-label-sm">Nachname</label>
          <div class="col-sm-5 mt-1">
              <?php echo "<b>" . $row["us_nachname"] . "</b>"; ?>
          </div>
        </div>
          <div class="form-group row m-1">
              <label for="us_mail" class="col-sm-4 col-form-label col-form-label-sm">E-Mail</label>
              <div class="col-sm-5 mt-1">
                  <?php echo "<b>" . $row["us_mail"] . "</b>"; ?>
              </div>
          </div>
          <div class="form-group row m-1">
              <label for="us_admin" class="col-sm-4 col-form-label col-form-label-sm">Art:</label>
              <div class="col-sm-5 mt-1">
                  <?php
                  if($row["us_admin"] == 1)
                  {
                      echo "<b>" . '<span class="text-success" >Admin</span>' . "</b>";
                  }
                  else
                  {
                      echo "<b>" . '<span class="text-info">User</span>' . "</b>";
                  }
                  ?>
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
              <label for="us_pw2" class="col-sm-4 col-form-label col-form-label-sm m-0">Status:</label>
              <div class="col-sm-5 mt-1">
                  <?php
                  if($row["us_status"] == 1)
                  {
                      echo "<b>" . '<span class="text-success">aktiv</span>' . "</b>";
                  }
                  else
                  {
                      echo "<b>" . '<span class="text-danger">inaktiv</span>' . "</b>";
                  }
                  ?>
              </div>
          </div>
          <div class="form-group row m-1">

              <?php
              if(isset($_POST["submit"]))
              {
                  $us_pw = passwordgen();
                  $query = "UPDATE `user` SET " .
                      "us_pw = SHA('$us_pw') " .
                      "WHERE us_id = '$us_id'";
                  mysqli_query($dbc, $query)
                    or die(errorlog($dbc,$query));

                  //input um clipboard copy via js zu ermöglichen ohne 7000 zeilen lange funktion erstellen zu müssen. Pfusch aber geht
                  echo '<input type="text" id="us_pw_n" value="' . $us_pw . '" style="width:1px; height:1px; border: none;">';
                  echo '<div class="alert alert-success" role="alert">';
                  echo 'Neues Passwort: ';
                  echo '<a href="" onclick="copytext(\'us_pw_n\')">' . $us_pw . '</a>';
                  echo '</div>';

              }
              else
              {
                  ?>

              <form action="<?php echo $_SERVER["PHP_SELF"] . "?us_id=" . $us_id; ?>" method="post">
                  <input type="submit" name="submit" id="submit" value="Neues Passwort generieren" class="btn btn-primary ml-3 mt-1">
              </form>
                  <?php
              }
              ?>
          </div>
      </div>


    </div>

      <a href="usaendern.php?us_id=<?php echo $us_id; ?>" class="btn btn-primary mt-5 ml-4">Datensatz ändern</a>









  </div>

<?php
}

if(isset($error) && $error == 1)
{
    //keine us_id in GET
    if ($us_id == 0)
    {
        ?>
        <div class="alert alert-warning" role="alert">
            Ungültiger Link. Sie werden zur Übersicht weitergeleitet.
        </div>
        <script>
            window.setTimeout(function(){

                window.location.href = "usalle.php";

            }, 5000);
        </script>
        <?php
    }

    //user existiert nicht
    if (isset($us_exist) && $us_exist == 0 && $us_id != 0)
    {
        ?>
        <div class="alert alert-warning" role="alert">
            Es existiert kein User mit der id "<?php echo $us_id ?>";
        </div>
        <?php
    }


}
?>
<!-- MAIN END -->

<?php
include_once('footer.php');
?>
