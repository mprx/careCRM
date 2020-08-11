<?php
session_start();
// Wenn die Session Variablen nicht gesetzt sind, die Session aus den Cookies kreieren
if (!isset($_SESSION['us_id']))
{
    if (isset($_COOKIE['us_id']) && isset($_COOKIE['us_name']))
    {
        $_SESSION['us_id'] = $_COOKIE['us_id'];
        $_SESSION['us_name'] = $_COOKIE['us_name'];
    }
}
?>