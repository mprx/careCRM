<?php
// Definiere Datenbank Variablen
//define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASSWORD', '');
define('DB_NAME', 'carecrm');


//Array mit Hosts definieren
$hosts = array(
    'localhost',
    'localhost',
    'host3'
);

//timeout in seKunden für den Verbindungsversuch definieren.
$timeout = 1;

//Durch hosts iterieren
foreach($hosts as $value)
{

    if($fp = @fsockopen($value, 80, $errno, $errstr, $timeout))
    {
        fclose($fp);
        $verbindung = 1;
        define('DB_HOST', $value);
        break;
    }
    else
    {
        $verbindung = 0;

    }
}

//wenn verbindung zu KEINEM host zustandekommt, DB_HOST auf empty setzen (wirft fehler aus)
if($verbindung == 0)
{
    define('DB_HOST', '');
}

?>