<?php
$siteTitle = "Alle User";
$siteCategory = "Administration";

include_once('header.php');
include_once('nav.php');
?>
<!-- MAIN START -->

<?php

$dbc = mysqli_connect(DB_HOST,DB_USER,DB_PASSWORD, DB_NAME)
or die(errrorlog($dbc,0));
mysqli_set_charset($dbc,"utf8");

//Daten holen
$query = "SELECT * FROM `user` WHERE us_id > 0 ORDER BY us_nachname;";
$data = mysqli_query($dbc, $query)
    or die(errorlog($dbc, $query));

?>
<table class="table table-striped table-sm">
  <thead>
    <tr>
      <th scope="col">Nachname</th>
      <th scope="col">Vorname</th>
      <th scope="col">Username</th>
      <th scope="col">Art</th>
      <th scope="col">Status</th>
      <th scope="col">Details</th>
    </tr>
  </thead>
  <tbody>

  <?php
    while($row = mysqli_fetch_array($data))
    {
        echo '<tr>';
        echo '<td>' . $row["us_nachname"] . '</td>';
        echo '<td>' . $row["us_vorname"] . '</td>';
        echo '<td>' . $row["us_name"] . '</td>';
        echo '<td>';
        if($row["us_admin"] == 1)
        {
            echo '<span class="text-success" >Admin</span>';
        }
        else
        {
            echo '<span class="text-info">User</span>';
        }
        echo '</td>';
        echo '<td>';
        if($row["us_status"] == 1)
        {
            echo '<span class="text-success">aktiv</span>';
        }
        else
        {
            echo '<span class="text-danger">inaktiv</span>';
        }
        echo '</td>';
        echo '<td><a href="usdetails.php?us_id=' . $row["us_id"] . '">Details</a></td>';
    }
  ?>

  </tbody>
</table>

<!-- MAIN END -->
<?php
include_once('footer.php');
?>