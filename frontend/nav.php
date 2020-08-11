<!-- BODY START -->

<!-- MOBILE NAVIGATION -->
<div class="row" style="font-size: 1vw;">
    <div class="col border border-primary rounded mb-2 p-1 d-xl-none d-sm-block bg-light">
        <div class="container p-0 m-0 col-auto container">
            <div class="row p-0 m-0">
                <div class="col p-0 m-0">
                    <img src="images/CareCRM_logo.png" height="100" class="ml-2">
                </div>
                <div class="col-6 p-0 m-0 float-right">
                    <nav class="navbar navbar-light float-right">
                        <!--<a class="dropdown-toggle navbar-toggler prxm" data-toggle="collapse" data-target="#mobilenav" aria-expanded="false">Menü</a>-->
                        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#mobilenav" aria-controls="navbarToggleExternalContent" aria-expanded="false" aria-label="Toggle navigation">
                            <span class="navbar-toggler-icon"></span>
                        </button>
                    </nav>
                </div>
            </div>
        </div>

    </div>
</div>
<!-- style="font-size: 3vw;" -->
<div class="row flex-column collapse border border-primary d-xl-none rounded prxm mb-2 p-1 bg-light text-right" id="mobilenav">
    <ul class="nav flex-column">
        <li class="nav-item">
            <a href="kunden.php" class="toggle-custom nav-link dropdown-toggle pt-0 pb-0 mb-1<?php navlinkhighlight("kunden"); ?>" id="nav-btn-1" data-toggle="collapse" data-target="#submenu1" aria-expanded="false">Kunden</a>
            <ul class="nav collapse flex-column <?php navcategoryshow("Kunden");?>" id="submenu1" role="menu" aria-labelledby="nav-btn-1">
                <li><a href="kdsuche.php" class="nav-link mr-3 pt-0 pb-0 mb-1<?php navlinkhighlight("kdsuche"); ?>">Suchen</a>
                </li>
                <li><a href="kdalle.php" class="nav-link mr-3 pt-0 pb-0 mb-1<?php navlinkhighlight("kdalle"); ?>">Alle</a>
                </li>
                <li><a href="kdneu.php" class="nav-link mr-3 pt-0 pb-0 mb-1<?php navlinkhighlight("kdneu"); ?>">Neuer</a>
                </li>
            </ul>
        </li>
        <div class="dropdown-divider"></div>
        <li class="nav-item">
            <a href="pfleger.php" class="toggle-custom nav-link dropdown-toggle pt-0 pb-0 mb-1<?php navlinkhighlight("pfleger"); ?>" id="nav-btn-2" data-toggle="collapse" data-target="#submenu2" aria-expanded="false">Pfleger</a>
            <ul class="nav collapse flex-column <?php navcategoryshow("Pfleger");?>" id="submenu2" role="menu" aria-labelledby="nav-btn-2">
                <li><a href="pgsuche.php" class="nav-link mr-3 pt-0 pb-0 mb-1<?php navlinkhighlight("pgsuche"); ?>">Suchen</a>
                </li>
                <li><a href="pgalle.php" class="nav-link mr-3 pt-0 pb-0 mb-1<?php navlinkhighlight("pgalle"); ?>">Alle</a>
                </li>
                <li><a href="pgneu.php" class="nav-link mr-3 pt-0 pb-0 mb-1<?php navlinkhighlight("pgneu"); ?>">Neuer</a>
                </li>
            </ul>
        </li>
        <div class="dropdown-divider"></div>
        <li class="nav-item">
            <a href="sachwalter.php" class="toggle-custom nav-link dropdown-toggle pt-0 pb-0 mb-1<?php navlinkhighlight("sachwalter"); ?>" id="nav-btn-3" data-toggle="collapse" data-target="#submenu3" aria-expanded="false">Sachwalter</a>
            <ul class="nav collapse flex-column <?php navcategoryshow("Sachwalter");?>" id="submenu3" role="menu" aria-labelledby="nav-btn-3">
                <li><a href="swsuche.php" class="nav-link mr-3 pt-0 pb-0 mb-1<?php navlinkhighlight("swsuche"); ?>">Suchen</a>
                </li>
                <li><a href="swalle.php" class="nav-link mr-3 pt-0 pb-0 mb-1<?php navlinkhighlight("swalle"); ?>">Alle</a>
                </li>
                <li><a href="swneu.php" class="nav-link mr-3 pt-0 pb-0 mb-1<?php navlinkhighlight("swneu"); ?>">Neuer</a>
                </li>
            </ul>
        </li>
        <div class="dropdown-divider"></div>
        <li class="nav-item">
            <a href="angehoerige.php" class="toggle-custom nav-link dropdown-toggle pt-0 pb-0 mb-1<?php navlinkhighlight("angehoerige"); ?>" id="nav-btn-4" data-toggle="collapse" data-target="#submenu4" aria-expanded="false">Angehörige</a>
            <ul class="nav collapse flex-column <?php navcategoryshow("Angehörige");?>" id="submenu4" role="menu" aria-labelledby="nav-btn-4">
                <li><a href="agsuche.php" class="nav-link mr-3 pt-0 pb-0 mb-1<?php navlinkhighlight("agsuche"); ?>">Suchen</a>
                </li>
                <li><a href="agalle.php" class="nav-link mr-3 pt-0 pb-0 mb-1<?php navlinkhighlight("agalle"); ?>">Alle</a>
                </li>
                <li><a href="agneu.php" class="nav-link mr-3 pt-0 pb-0 mb-1<?php navlinkhighlight("agneu"); ?>">Neuer</a>
                </li>
            </ul>
        </li>
        <div class="dropdown-divider"></div>
        <li class="nav-item">
            <a href="#" class="toggle-custom nav-link dropdown-toggle pt-0 pb-0 mb-1" id="nav-btn-5" data-toggle="collapse" data-target="#submenu5" aria-expanded="false">Pflegeplan</a>
            <ul class="nav collapse flex-column <?php navcategoryshow("Pflegeplan");?>" id="submenu5" role="menu" aria-labelledby="nav-btn-5">
                <li><a href="pphb.php" class="nav-link mr-3 pt-0 pb-0 mb-1<?php navlinkhighlight("pphb"); ?>">Hausbesuche</a>
                </li>
                <li><a href="ppneu.php" class="nav-link mr-3 pt-0 pb-0 mb-1<?php navlinkhighlight("ppneu"); ?>">Neu</a>
                </li>
                <li><a href="ppexport.php" class="nav-link mr-3 pt-0 pb-0 mb-1<?php navlinkhighlight("ppexport"); ?>">Export</a>
                </li>
            </ul>
        </li>
        <div class="dropdown-divider"></div>
        <li class="nav-item">
            <a href="#" class="toggle-custom nav-link dropdown-toggle pt-0 pb-0 mb-1" id="nav-btn-6" data-toggle="collapse" data-target="#submenu6" aria-expanded="false">Rechnungen</a>
            <ul class="nav collapse flex-column <?php navcategoryshow("Rechnungen");?>" id="submenu6" role="menu" aria-labelledby="nav-btn-6">
                <li><a href="rgtodo.php" class="nav-link mr-3 pt-0 pb-0 mb-1<?php navlinkhighlight("rgtodo"); ?>">To-Do</a>
                </li>
                <li><a href="rgoffen.php" class="nav-link mr-3 pt-0 pb-0 mb-1<?php navlinkhighlight("rgoffen"); ?>">Offene</a>
                </li>
                <li><a href="rgalle.php" class="nav-link mr-3 pt-0 pb-0 mb-1<?php navlinkhighlight("rgalle"); ?>">Alle</a>
                </li>
                <li><a href="rgexport.php" class="nav-link mr-3 pt-0 pb-0 mb-1<?php navlinkhighlight("rgexport"); ?>">Export Liste</a>
                </li>
            </ul>
        </li>
        <div class="dropdown-divider"></div>
        <li class="nav-item">
            <a href="#" class="toggle-custom nav-link dropdown-toggle pt-0 pb-0 mb-1" id="nav-btn-7" data-toggle="collapse" data-target="#submenu7" aria-expanded="false">Neue Rechnung</a>
            <ul class="nav collapse flex-column <?php navcategoryshow("Neue Rechnung");?>" id="submenu7" role="menu" aria-labelledby="nav-btn-7">
                <li><a href="rgneukd.php" class="nav-link mr-3 pt-0 pb-0 mb-1<?php navlinkhighlight("rgneukd"); ?>">Für Kd</a>
                </li>
                <li><a href="rgneupg.php" class="nav-link mr-3 pt-0 pb-0 mb-1<?php navlinkhighlight("rgneupg"); ?>">Für Pg</a>
                </li>
                <li><a href="rgneuman.php" class="nav-link mr-3 pt-0 pb-0 mb-1<?php navlinkhighlight("rgneuman"); ?>">manuell</a>
                </li>
            </ul>
        </li>
        <div class="dropdown-divider"></div>
        <?php
        if (isset($_SESSION['userid']))
        {
            $user_userid = $_SESSION['userid'];
            $dbc = mysqli_connect(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME)
            or die(errorlog($dbc,$query));
            $query = "SELECT us_admin FROM `user` WHERE us_id = '$user_userid'";
            $data = mysqli_query($dbc, $query)
            or die(errorlog($dbc,$query));
            $row = mysqli_fetch_array($data);

            $us_admin = 0;

            if($row["us_admin"] == 1)
            {
                $us_admin = 1;
                ?>
                <li class="nav-item">
                    <a href="#" class="toggle-custom nav-link dropdown-toggle pt-0 pb-0 mb-1" id="nav-btn-8" data-toggle="collapse" data-target="#submenu8" aria-expanded="false">Administration</a>
                    <ul class="nav collapse flex-column <?php navcategoryshow("Administration"); navcategoryshow("Leistungen");?>" id="submenu8" role="menu" aria-labelledby="nav-btn-8">
                        <li>
                        <li class="nav-item">
                            <a href="#" class="toggle-custom nav-link dropdown-toggle pt-0 pb-0 mb-1 mr-3<?php navlinkhighlight("Leistungen"); ?>" id="nav-btn-9" data-toggle="collapse" data-target="#submenu9" aria-expanded="false">Leistungen</a>
                            <ul class="nav collapse flex-column <?php navcategoryshow("Leistungen");?>" id="submenu9" role="menu" aria-labelledby="nav-btn-9">
                                <li><a href="lgalle.php?lg_jahr=<?php echo date("Y", time()); ?>" class="nav-link mr-5 pt-0 pb-0 mb-1<?php navlinkhighlight("lgalle"); ?>">Alle</a>
                                </li>
                                <li><a href="lgneu.php" class="nav-link mr-5 pt-0 pb-0 mb-1<?php navlinkhighlight("lgneu"); ?>">Neu</a>
                                </li>
                                <li><a href="lgindex.php" class="nav-link mr-5 pt-0 pb-0 mb-1<?php navlinkhighlight("lgindex"); ?>">Index</a>
                                </li>
                            </ul>
                        </li>
                        </li>
                        <li><a href="usalle.php" class="nav-link mr-3 pt-0 pb-0 mb-1<?php navlinkhighlight("usalle"); ?>">Alle User</a>
                        </li>
                        <li><a href="usneu.php" class="nav-link mr-3 pt-0 pb-0 mb-1<?php navlinkhighlight("usneu"); ?>">User hinzufügen</a>
                        </li>
                    </ul>
                </li>
                <?php
            }

            mysqli_close($dbc);
        }
        ?>
    </ul>
</div>
      <div class="row">
      
        <!-- NAV START -->
          <!-- DESKTOP NAVIGATION -->
        <div class="col-s-2 border border-primary rounded bg-light d-none d-xl-block mr-2">
        <div style="height: 75vh; overflow-y: scroll;">
        <nav class="mt-2">

<ul class="nav flex-column">

    <li class="nav-item">
      <a href="kunden.php" class="toggle-custom nav-link dropdown-toggle pt-0 pb-0 mb-1<?php navlinkhighlight("kunden"); ?>" id="nav-btn-1" data-toggle="collapse" data-target="#submenu1" aria-expanded="false">Kunden</a>
      <ul class="nav collapse flex-column <?php navcategoryshow("Kunden");?>" id="submenu1" role="menu" aria-labelledby="nav-btn-1">
        <li><a href="kdsuche.php" class="nav-link ml-2 pt-0 pb-0 mb-1<?php navlinkhighlight("kdsuche"); ?>">Suchen</a>
        </li>
        <li><a href="kdalle.php" class="nav-link ml-2 pt-0 pb-0 mb-1<?php navlinkhighlight("kdalle"); ?>">Alle</a>
        </li>
        <li><a href="kdneu.php" class="nav-link ml-2 pt-0 pb-0 mb-1<?php navlinkhighlight("kdneu"); ?>">Neuer</a>
        </li>
      </ul>
    </li>
    <li class="nav-item">
      <a href="pfleger.php" class="toggle-custom nav-link dropdown-toggle pt-0 pb-0 mb-1<?php navlinkhighlight("pfleger"); ?>" id="nav-btn-2" data-toggle="collapse" data-target="#submenu2" aria-expanded="false">Pfleger</a>
      <ul class="nav collapse flex-column <?php navcategoryshow("Pfleger");?>" id="submenu2" role="menu" aria-labelledby="nav-btn-2">
        <li><a href="pgsuche.php" class="nav-link ml-2 pt-0 pb-0 mb-1<?php navlinkhighlight("pgsuche"); ?>">Suchen</a>
        </li>
        <li><a href="pgalle.php" class="nav-link ml-2 pt-0 pb-0 mb-1<?php navlinkhighlight("pgalle"); ?>">Alle</a>
        </li>
        <li><a href="pgneu.php" class="nav-link ml-2 pt-0 pb-0 mb-1<?php navlinkhighlight("pgneu"); ?>">Neuer</a>
        </li>
      </ul>
    </li>
    <li class="nav-item">
      <a href="sachwalter.php" class="toggle-custom nav-link dropdown-toggle pt-0 pb-0 mb-1<?php navlinkhighlight("sachwalter"); ?>" id="nav-btn-3" data-toggle="collapse" data-target="#submenu3" aria-expanded="false">Sachwalter</a>
      <ul class="nav collapse flex-column <?php navcategoryshow("Sachwalter");?>" id="submenu3" role="menu" aria-labelledby="nav-btn-3">
        <li><a href="swsuche.php" class="nav-link ml-2 pt-0 pb-0 mb-1<?php navlinkhighlight("swsuche"); ?>">Suchen</a>
        </li>
        <li><a href="swalle.php" class="nav-link ml-2 pt-0 pb-0 mb-1<?php navlinkhighlight("swalle"); ?>">Alle</a>
        </li>
        <li><a href="swneu.php" class="nav-link ml-2 pt-0 pb-0 mb-1<?php navlinkhighlight("swneu"); ?>">Neuer</a>
        </li>
      </ul>
    </li>
    <li class="nav-item">
      <a href="angehoerige.php" class="toggle-custom nav-link dropdown-toggle pt-0 pb-0 mb-1<?php navlinkhighlight("angehoerige"); ?>" id="nav-btn-4" data-toggle="collapse" data-target="#submenu4" aria-expanded="false">Angehörige</a>
      <ul class="nav collapse flex-column <?php navcategoryshow("Angehörige");?>" id="submenu4" role="menu" aria-labelledby="nav-btn-4">
        <li><a href="agsuche.php" class="nav-link ml-2 pt-0 pb-0 mb-1<?php navlinkhighlight("agsuche"); ?>">Suchen</a>
        </li>
        <li><a href="agalle.php" class="nav-link ml-2 pt-0 pb-0 mb-1<?php navlinkhighlight("agalle"); ?>">Alle</a>
        </li>
        <li><a href="agneu.php" class="nav-link ml-2 pt-0 pb-0 mb-1<?php navlinkhighlight("agneu"); ?>">Neuer</a>
        </li>
      </ul>
    </li>
    <br />
    <li class="nav-item">
      <a href="#" class="toggle-custom nav-link dropdown-toggle pt-0 pb-0 mb-1" id="nav-btn-5" data-toggle="collapse" data-target="#submenu5" aria-expanded="false">Pflegeplan</a>
      <ul class="nav collapse flex-column <?php navcategoryshow("Pflegeplan");?>" id="submenu5" role="menu" aria-labelledby="nav-btn-5">
        <li><a href="pphb.php" class="nav-link ml-2 pt-0 pb-0 mb-1<?php navlinkhighlight("pphb"); ?>">Hausbesuche</a>
        </li>
        <li><a href="ppneu.php" class="nav-link ml-2 pt-0 pb-0 mb-1<?php navlinkhighlight("ppneu"); ?>">Neu</a>
        </li>
        <li><a href="ppexport.php" class="nav-link ml-2 pt-0 pb-0 mb-1<?php navlinkhighlight("ppexport"); ?>">Export</a>
        </li>
      </ul>
    </li>
    <br />
    <li class="nav-item">
      <a href="#" class="toggle-custom nav-link dropdown-toggle pt-0 pb-0 mb-1" id="nav-btn-6" data-toggle="collapse" data-target="#submenu6" aria-expanded="false">Rechnungen</a>
      <ul class="nav collapse flex-column <?php navcategoryshow("Rechnungen");?>" id="submenu6" role="menu" aria-labelledby="nav-btn-6">
        <li><a href="rgtodo.php" class="nav-link ml-2 pt-0 pb-0 mb-1<?php navlinkhighlight("rgtodo"); ?>">To-Do</a>
        </li>
        <li><a href="rgoffen.php" class="nav-link ml-2 pt-0 pb-0 mb-1<?php navlinkhighlight("rgoffen"); ?>">Offene</a>
        </li>
        <li><a href="rgalle.php" class="nav-link ml-2 pt-0 pb-0 mb-1<?php navlinkhighlight("rgalle"); ?>">Alle</a>
        </li>
        <li><a href="rgexport.php" class="nav-link ml-2 pt-0 pb-0 mb-1<?php navlinkhighlight("rgexport"); ?>">Export Liste</a>
        </li>
      </ul>
    </li>
    <li class="nav-item">
      <a href="#" class="toggle-custom nav-link dropdown-toggle pt-0 pb-0 mb-1" id="nav-btn-7" data-toggle="collapse" data-target="#submenu7" aria-expanded="false">Neue Rechnung</a>
      <ul class="nav collapse flex-column <?php navcategoryshow("Neue Rechnung");?>" id="submenu7" role="menu" aria-labelledby="nav-btn-7">
        <li><a href="rgneukd.php" class="nav-link ml-2 pt-0 pb-0 mb-1<?php navlinkhighlight("rgneukd"); ?>">Für Kd</a>
        </li>
        <li><a href="rgneuman.php" class="nav-link ml-2 pt-0 pb-0 mb-1<?php navlinkhighlight("rgneuman"); ?>">manuell</a>
        </li>
      </ul>
    </li>
    <br />
    <?php
    if (isset($_SESSION['userid']))
    {
        $user_userid = $_SESSION['userid'];
        $dbc = mysqli_connect(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME)
        or die(errorlog($dbc,$query));
        $query = "SELECT us_admin FROM `user` WHERE us_id = '$user_userid'";
        $data = mysqli_query($dbc, $query)
        or die(errorlog($dbc,$query));
        $row = mysqli_fetch_array($data);

        $us_admin = 0;

        if($row["us_admin"] == 1)
        {
            $us_admin = 1;
            ?>
            <li class="nav-item">
                <a href="#" class="toggle-custom nav-link dropdown-toggle pt-0 pb-0 mb-1" id="nav-btn-8" data-toggle="collapse" data-target="#submenu8" aria-expanded="false">Administration</a>
                <ul class="nav collapse flex-column <?php navcategoryshow("Administration"); navcategoryshow("Leistungen");?>" id="submenu8" role="menu" aria-labelledby="nav-btn-8">
                    <li>
                    <li class="nav-item">
                        <a href="#" class="toggle-custom nav-link dropdown-toggle pt-0 pb-0 mb-1 ml-2<?php navlinkhighlight("Leistungen"); ?>" id="nav-btn-9" data-toggle="collapse" data-target="#submenu9" aria-expanded="false">Leistungen</a>
                        <ul class="nav collapse flex-column <?php navcategoryshow("Leistungen");?>" id="submenu9" role="menu" aria-labelledby="nav-btn-9">
                            <li><a href="lgalle.php?lg_jahr=<?php echo date("Y", time()); ?>" class="nav-link ml-4 pt-0 pb-0 mb-1<?php navlinkhighlight("lgalle"); ?>">Alle</a>
                            </li>
                            <li><a href="lgneu.php" class="nav-link ml-4 pt-0 pb-0 mb-1<?php navlinkhighlight("lgneu"); ?>">Neu</a>
                            </li>
                            <li><a href="lgindex.php" class="nav-link ml-4 pt-0 pb-0 mb-1<?php navlinkhighlight("lgindex"); ?>">Index</a>
                            </li>
                        </ul>
                    </li>
                    </li>
                    <li><a href="usalle.php" class="nav-link ml-2 pt-0 pb-0 mb-1<?php navlinkhighlight("usalle"); ?>">Alle User</a>
                    </li>
                    <li><a href="usneu.php" class="nav-link ml-2 pt-0 pb-0 mb-1<?php navlinkhighlight("usneu"); ?>">User hinzufügen</a>
                    </li>
                </ul>
            </li>
            <?php
        }

        mysqli_close($dbc);
    }
    ?>


</ul>
  
</nav>
</div>
        </div>
        
        <!-- NAV END -->
       
        
        <div class="col-xl border border-primary rounded bg-light">
        <div class="p-4" style="height: 75vh; overflow-y: scroll; margin-right: -15px;">
<?php

if(!isset($_SESSION["userid"]) && $siteCategory != "Start")
{
    echo '<div class="alert alert-info" role="alert">Bitte <a href="login.php">loggen Sie sich ein</a>, um auf diese Seite zugreifen zu können.</div>';
    exit();
}
elseif(isset($_SESSION["userid"]) && $us_admin == 0 && ($siteCategory == "Administration" || $siteCategory == "Leistungen"))
{
    echo '<div class="alert alert-info" role="alert"> Sie besitzen nicht die notwendigen Berechtigungen, um diese Funktion zu nutzen.<br/>Bitte wenden Sie sich an einen Administrator.</div>';
    exit();
}

?>