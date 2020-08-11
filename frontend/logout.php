<?php
$siteTitle = "Logout";
$siteCategory = "Start";

?>

<!-- MAIN START -->
<?php
// Username und Userid Cookies löschen, indem man Ablaufdatum auf eine Stunde zurück setzt (3600)
setcookie('userid', '', time() - 3600);
setcookie('username', '', time() - 3600);
session_start();
if (isset($_SESSION['userid']))
{
    // Session Variablen löschen, indem man das Array leert
    $_SESSION = array();

    // Session Cookie löschen indem man Ablaufdatum auf eine Stunde zurück setzt (3600)
    if (isset($_COOKIE[session_name()]))
    {
        setcookie(session_name(), '', time() - 3600);
    }

    // Session beenden
    session_destroy();
}

include_once('header.php');
include_once('nav.php');
?>

<div class="alert alert-success" role="alert">Sie wurden erfolgreich ausgeloggt!</div>



<!-- MAIN END -->

<?php
include_once('footer.php');
?>
