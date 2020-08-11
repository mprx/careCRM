<html>
<?php
if(isset($_POST["file_inhalt"]))
{
    $titel = "FEHLER";
    $file_inhalt = $_POST["file_inhalt"];
    $file = "csv/" . $_POST["von"] . "-" . $_POST["bis"] . "_RECHNUNGEN" . "_" . mt_rand() . ".csv";
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
