<html>
<?php
if(isset($_POST["file_inhalt"]))
{
    $titel = "FEHLER";
    if(isset($_POST["kd_daten"])) $titel = $_POST["kd_daten"];
    if(isset($_POST["pg_daten"])) $titel = $_POST["pg_daten"];
    $file_inhalt = $_POST["file_inhalt"];
    $file = "csv/" . $_POST["von"] . "-" . $_POST["bis"] . "_plan_$titel" . "_" . mt_rand() . ".csv";
    $handle = fopen($file, "w");

   // fwrite($handle,$file_inhalt);

    file_put_contents($file, "\xEF\xBB\xBF".  $file_inhalt);
    fclose($handle);
    //readfile($file);

    header("Location: $file");
}
?>
<header>

</header>
</html>
