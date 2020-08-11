<?php
//Functions

//highlightet den link in der Navigation der aktuell geöffneten Seite
function navlinkhighlight($sitename)
{
  if (basename($_SERVER['SCRIPT_FILENAME'],'.php') == $sitename) echo " bg-primary text-white"; 
}

//lässt die Navigation der aktuellen siteCategory offen
function navcategoryshow($category)
{
    global $siteCategory;
    if($siteCategory == $category) echo " show";
}

//gibt Stiege/Stock/Tür nur aus, wenn es ein brauchbarer Wert ist
function addressseperation($number)
{
  if ((is_int($number) && $number != 0) || (is_string($number) && $number != ""))
  {
    return "/".$number;
  }
}

//schreibt eine textdatei auf den Server mit genauen Infos zum Datenbankfehler
function errorlog($dbc, $query)
{
  //global $dbc;

  //Fehler im Verzeichnis /errorlogs in ein eigenes Textfile mit Datum, Uhrzeit und zufallszahl schreiben:
  $file = "errorlogs/error-" . date("Y-m-d-H-i-s-") . mt_rand() . ".txt";
  $handle = fopen($file, "w");
  $fehler = "mysqli_error: " . mysqli_error($dbc) . "\nmysqli_errorno: " . mysqli_errno($dbc) . "\nmysqli_connect_error: " . mysqli_connect_error() . "\nmysqli_connect_errorno: " . mysqli_connect_errno() . "\nQuery: " . $query;
  $inhalt = "Datum: " . date("d.m.Y") . "\nUhrzeit: " . date("H:i:s") . "\nSeite: " . $_SERVER["PHP_SELF"] . "\nUser: "  . $_SESSION["username"] . "\n\nFehler:\n" . $fehler;
  fwrite($handle,$inhalt);
  fclose($handle);


    //Fehler alert anzeigen (DEV Version):
    /*
    echo '<div class="alert alert-danger" role="alert">';
    echo str_replace("\n","<br/>",$inhalt);
    echo '</div>';
    */

    //Fehler alert anzeigen (USER Version):
    echo '<div class="alert alert-danger" role="alert">';
    echo "Datum: " . date("d.m.Y") . "<br>Uhrzeit: " . date("H:i:s") . "<br>Seite: " . $_SERVER["PHP_SELF"] . "<br>User: "  . $_SESSION["username"] . "<br><br>" .
        "Es ist ein unerwarteter Fehler aufgetreten.<br>Bitte wenden Sie sich an einen Serveradministrator.";
    echo '</div>';



    //Mail an Admin schicken:
/*
  $nachricht = "Genaue angaben zum aufgetretenem Fehler sind im Errorlog unter " . $_SERVER["DOCUMENT_ROOT"] . "/" . $file . " zu finden.";
  $nachricht = wordwrap($nachricht, 70,"\n");
  mail('m.praxm@gmail.com', 'Neuer Fehler', $nachricht);
*/
}

//setzt das aktuelle Tab in Kundendetails auf aktiv
function kdTab($site)
{
    if (basename($_SERVER['PHP_SELF']) == $site . ".php") echo " active";
}

//Generiert ein Passwort aus klein- großbuchstaben, zahlen und sonderzeichen, 16 zeichen lang

function passwordgen()
{
    $laenge = 16;
    $zeichen = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ!=%$;&/+#-_.:,?"}{/*';

    $str = '';
    $max = strlen($zeichen) - 1;

    for ($i = 0; $i < $laenge; ++$i) {
        $str .= $zeichen[random_int(0, $max)];
    }

    return $str;
}

//gibt $_POST values aus, wenn vorhanden
function keeppostvalue($index)
{
    if(isset($_POST["$index"])) echo $_POST["$index"];
}

//gibt values aus der $ROW aus, wenn vorhanden
function keeprowvalue($index)
{
    global $row;
    if(isset($row["$index"])) echo $row["$index"];
}

//gibt nur dann den $_POST value aus, wenn $_POST auch gesetzt ist
function switchpostvalue($index)
{
    global $row;
    if(isset($_POST["submit"]))
    {
        echo $_POST["$index"];
    }
    else
    {
        echo $row["$index"];
    }
}

function switchpostvariable($postIndex, $variable)
{
    if(isset($_POST["submit"]) && !empty($_POST[$postIndex]))
    {
        echo ' value="' . $_POST["$postIndex"] . '"';
    }
    else
    {
        echo ' value="' . $variable . '"';
    }
}

//selbes wie switchpostvalue nur für checkboxen
function switchpostvaluecheck($index)
{
    global $row;
    if(isset($_POST["submit"]))
    {
        if(isset($_POST["$index"]))
        {
            echo ' checked="checked" ';
        }
    }
    else
    {
        if($row["$index"] == 1)
        {
            echo ' checked="checked" ';
        }
    }
}

?>