<?php
include_once('header.php');
include_once('nav.php');
?>

<!-- MAIN START -->
<table class="table table-striped table-sm">
  <thead>
    <tr>
      <th scope="col">Anrede</th>
      <th scope="col">Nachname</th>
      <th scope="col">Vorname</th>
      <th scope="col">PLZ</th>
      <th scope="col">Adresse</th>
      <th scope="col">Telefon</th>
      <th scope="col">Details</th>
    </tr>
  </thead>
  <tbody>
    <tr>
      <th scope="row">Herr</th>
      <td>Ochse</td>
      <td>Otto</td>
      <td>1130</td>
      <td>Anton-Langer-Gasse 34/8</td>
      <td>01 234 56 78</td>
      <td><a href="#">Details</a></td>
    </tr>
    <tr>
     <th scope="row">Frau</th>
      <td>Musterfrau</td>
      <td>Maria</td>
      <td>1110</td>
      <td>Simmeringer Hauptstraße 123</td>
      <td>01 876 54 32</td>
      <td><a href="#">Details</a></td>
    </tr>
    <tr>
      <th scope="row">Herr</th>
      <td>Muster</td>
      <td>Franz</td>
      <td>1120</td>
      <td>Lange Gasse 12/8/9</td>
      <td>01 234 56 78</td>
      <td><a href="#">Details</a></td>
    </tr>
  </tbody>
</table>
        
<!-- MAIN END -->
<?php
include_once('footer.php');
?>