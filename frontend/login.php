<?php
$siteTitle = "Login";
$siteCategory = "Start";

?>

<!-- MAIN START -->
<?php
include_once('connectvars.php');
$dbc = mysqli_connect(DB_HOST,DB_USER,DB_PASSWORD, DB_NAME)
    or die(errrorlog($dbc,0));
mysqli_set_charset($dbc,"utf8");



if (!isset($_SESSION['userid']))
{
    if (isset($_POST['submit']))
    {
        // Die eingetragenen Daten holen
        $user_username = mysqli_real_escape_string($dbc, trim($_POST['user_username']));
        $user_password = mysqli_real_escape_string($dbc, trim($_POST['user_password']));

        $error_msg = "";

        if (!empty($user_username) && !empty($user_password))
        {

            // Username und Passwort in der DB suchen
            $query = "SELECT us_id, us_name, us_status FROM `user` WHERE us_name = '$user_username' LIMIT 1";
            $data = mysqli_query($dbc, $query)
                or die(errorlog($dbc,$query));
            $row = mysqli_fetch_array($data);

            if (!empty($row["us_id"]))
            {

                //user ist gesperrt
                if($row["us_status"] == 0)
                {

                    $error_msg = 'Ihr Benutzerkonto ist inaktiv. Bitte wenden Sie sich an einen Administrator.';
                }
                else
                {

                    $query2 = "SELECT us_id FROM `user` WHERE us_name = '$user_username' AND us_pw = SHA('$user_password') LIMIT 1";
                    $data2 = mysqli_query($dbc, $query2)
                        or die(errorlog($dbc,$query2));

                    //username existiert aber PW ist falsch
                    if(empty(mysqli_num_rows($data2)))
                    {
                        if($row["us_status"] >= 3)
                        {
                            //user hat das PW schon 3 mal oder öfters falsch eingegeben, Konto wird also gesperrt
                            $error_msg = 'Ihr Benutzerkonto wurde wegen zu vielen fehlgeschlagenen Login-Versuchen gesperrt. <br/>' .
                                'Bitte wenden Sie sich an einen Administrator.';

                            $query3 = "UPDATE `user` SET us_status = '0' WHERE us_id = '" . $row["us_id"] . "'";
                            mysqli_query($dbc,$query3)
                                or die(errorlog($dbc,$query3));
                        }
                        else
                        {
                            //user passwort-falsch counter wird um 1 erhöht
                            $error_msg = 'Die eingegeben Login-Daten sind nicht korrekt.';

                            $query3 = "UPDATE `user` SET us_status = us_status + 1 WHERE us_id = '" . $row["us_id"] . "'";
                            mysqli_query($dbc,$query3)
                                or die(errorlog($dbc,$query3));
                        }
                    }
                    else
                    {
                        //pw ist korrekt
                        //pw-falsch counter wird zurückgesetzt
                        $query3 = "UPDATE `user` SET us_status = 1 WHERE us_id = '" . $row["us_id"] . "'";
                        mysqli_query($dbc,$query3)
                            or die(errorlog($dbc,$query3));

                        //setze User ID und Username in die Session und Cookies

                        $_SESSION['userid'] = $row['us_id'];
                        $_SESSION['username'] = $row['us_name'];
                        setcookie('userid', $row['us_id'], time() + (60 * 60 * 24 * 30));    // lauft in 30 Tagen ab
                        setcookie('username', $row['us_name'], time() + (60 * 60 * 24 * 30));  // lauft in 30 Tagen ab

                        $login = 1;
                    }
                }
            }
            else
            {
                // Username nicht korrekt
                $error_msg = 'Die eingegeben Login-Daten sind nicht korrekt.';
            }
        }
        else
        {
            // Username und/oder PW wurden nicht eingegeben
            $error_msg = 'Bitte Benutzername UND Passwort eingeben.';
        }
    }
}

include_once('header.php');
include_once('nav.php');

if (!isset($login) && !isset($_SESSION['username']))
{
    if (!empty($error_msg))
    {
        echo '<div class="alert alert-danger" role="alert">' . $error_msg . '</div>';
    }
?>
<form action="<?php echo $_SERVER["PHP_SELF"] ?>" method="POST">
  <div class="container">

    <div class="row">
      <div class="col">

        <div class="form-group row m-1">
          <label for="user_username" class="col-sm-3 col-form-label col-form-label-sm">Username:</label>
          <div class="col-sm-5">
            <input type="text" class="form-control form-control-sm" name="user_username" id="user_username" placeholder="Username" value="<?php keeppostvalue('user_username'); ?>" required />
          </div>
        </div>
          <div class="form-group row m-1">
              <label for="user_password" class="col-sm-3 col-form-label col-form-label-sm m-0">Passwort:</label>
              <div class="col-sm-5">
                  <input type="password" class="form-control form-control-sm" name="user_password" id="user_password" required />
              </div>
          </div>
      </div>
      <div class="col">



      </div>


    </div>

      <input type="submit" name="submit" id="submit" value="Einloggen" class="btn btn-primary mt-5 ml-4" />







  </div>
</form>
<?php
}
else
{
    $username = "";
    if(isset($_SESSION['username'])) $username = $_SESSION['username'];
    if(isset($row['us_name'])) $username = $row['us_name'];
    echo '<div class="alert alert-success" role="alert"> Sie sind nun als <b>' . $username . '</b> eingeloggt.</div>';
    if(isset($login))
    {
        ?>
        <script>
            redirect("login.php",1);
        </script>
        <?php
    }

}
?>
<!-- MAIN END -->

<?php
include_once('footer.php');
?>
