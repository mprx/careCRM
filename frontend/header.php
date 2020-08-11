<?php
include_once('connectvars.php');
if($siteTitle != "Logout") include_once('startsession.php');
?>
<!DOCTYPE HTML>
<html>
<head>
	<meta http-equiv="content-type" content="text/html" />
	<meta name="author" content="Mario Praxmarer" />
    <meta charset="utf-8" />
    <link rel="stylesheet" href="bootstrap/dist/css/bootstrap.css" />
    <link rel="stylesheet" href="style1.css" />
    <title><?php echo $siteTitle; ?> - CareCRM</title>
    <link rel="icon" href="images/CareCRM_logo.png">
</head>
<?php
include_once('functions.php');
?>
<body style="background-color: #95a1b7;">
    <script src="https://code.jquery.com/jquery-3.4.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.0/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/js/bootstrap.min.js"></script>
    <script src="functions.js"></script>
   
    <div class="container container-fluid">
      <!-- HEAD START -->
      <div class="row">
        <div class="col border border-primary rounded mb-2 p-1 d-none d-xl-block bg-light col-xl">
          <div class="container p-0 m-0">
            <div class="row p-0 m-0">
              <div class="col-2 p-0 m-0 ">
                  <img src="images/CareCRM_logo.png" height="100" style="margin-left: 40px;margin-top: 10px; margin-bottom: -20px;">
              </div>
              <div class="col p-0 m-0">

              </div>
              <div class="col p-0 m-0">
              </div>
            </div>
            <div class="row p-0 m-0">
              <div class="col p-0 m-0 text-primary pt-4">
                <?php echo $siteCategory . " &rarr; <b>" . $siteTitle . "</b>"; ?></b>
              </div>
              <div class="col p-0 m-0">
              </div>
              <div class="col p-0 m-0 text-right">
                  <?php
                  if (!isset($_SESSION['userid']))
                  {
                      echo '<b>Sie sind nicht eingeloggt.</b><br /><a href="login.php">Einloggen</a>';
                  }
                  else
                  {
                      $user_userid = $_SESSION['userid'];
                      $dbc = mysqli_connect(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME)
                        or die(errorlog($dbc,$query));
                      $query = "SELECT us_vorname, us_nachname, us_admin, us_status FROM `user` WHERE us_id = '$user_userid'";
                      $data = mysqli_query($dbc, $query)
                        or die(errorlog($dbc,$query));
                      $row = mysqli_fetch_array($data);

                      echo 'Eingeloggt als: <b>' . $row['us_vorname'] . ' ' . $row['us_nachname'] .
                          '</b><br /><a href="logout.php">Ausloggen</a>';

                      mysqli_close($dbc);
                  }
                  ?>
              </div>
            </div>
          </div>
        
        </div>
      </div>
      <!-- HEAD END -->