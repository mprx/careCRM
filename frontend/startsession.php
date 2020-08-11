<?php

session_start();

$dbc = mysqli_connect(DB_HOST,DB_USER,DB_PASSWORD, DB_NAME)
or die(errrorlog($dbc,0));
mysqli_set_charset($dbc,"utf8");

// Wenn die Session Variablen nicht gesetzt sind, die Session aus den Cookies kreieren
if (!isset($_SESSION['userid']))
{
    if (isset($_COOKIE['userid']) && isset($_COOKIE['username']))
    {
        $us_id = $_COOKIE['userid'];
        $query = "SELECT us_status FROM `user` WHERE us_id = '$us_id' LIMIT 1";
        $data = mysqli_query($dbc, $query)
            or die(errorlog($dbc, $query));

        $row = mysqli_fetch_array($data);

        if($row["us_status"] != 0)
        {
            $_SESSION['userid'] = $_COOKIE['userid'];
            $_SESSION['username'] = $_COOKIE['username'];
        }
        else
        {
            setcookie('userid', '', time() - 3600);
            setcookie('username', '', time() - 3600);

            if (isset($_COOKIE[session_name()]))
            {
                setcookie(session_name(), '', time() - 3600);
            }
            session_destroy();
        }
    }
}
else
{
    $us_id = $_SESSION['userid'];
    $query = "SELECT us_status FROM `user` WHERE us_id = '$us_id' LIMIT 1";
    $data = mysqli_query($dbc, $query)
    or die(errorlog($dbc, $query));

    $row = mysqli_fetch_array($data);

    if($row["us_status"] == 0)
    {
        setcookie('userid', '', time() - 3600);
        setcookie('username', '', time() - 3600);

        if (isset($_COOKIE[session_name()]))
        {
            setcookie(session_name(), '', time() - 3600);
        }
        session_destroy();
    }
}
?>
