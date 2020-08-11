<html>
<?php
if(isset($_GET["export"]) && isset($_POST["file_inhalt"]))
{
    $file_inhalt = $_POST["file_inhalt"];
    $file = "csv/" . date("Y-m") . "_hausbesuche_To-Do_" . mt_rand() . ".csv";
    $handle = fopen($file, "w");
    //fwrite($handle,$file_inhalt);

    file_put_contents($file, "\xEF\xBB\xBF".  $file_inhalt);
    fclose($handle);
    //readfile($file);

    header("Location: $file");
}
?>
<header>

</header>
</html>
