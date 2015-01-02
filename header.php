<?php

// Chargement des paramètres de configuration
include_once('config/config.inc.php');
include_once('class/form.class.php');


session_start();

if (isset($_GET['logout'])) {
    session_destroy();
    header("Location: $siteurl/login.php?success=Vous êtes déconnecté");
    exit;
}

if (basename($_SERVER['PHP_SELF']) != 'login.php' && $_SESSION['connecté'] != true) {
    header("Location: $siteurl/login.php?warning=Veuillez vous connecter pour accéder à cette page");
    exit;
}

?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Cabinet Médical</title>

    <!-- Bootstrap -->
    <link href="<?php echo $siteurl; ?>/public/css/bootstrap.min.css" rel="stylesheet">
    <link href="<?php echo $siteurl; ?>/public/css/style.css" rel="stylesheet">

    <link href="<?php echo $siteurl; ?>/public/css/bootstrap-datetimepicker.min.css" rel="stylesheet">
    <link href="<?php echo $siteurl; ?>/public/css/datepicker3.standalone.min.css" rel="stylesheet">

</head>
<body role="document">
<!-- Fixed navbar -->
<nav class="navbar navbar-inverse navbar-fixed-top" role="navigation">
    <div class="container">
        <div class="navbar-header">
            <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#navbar"
                    aria-expanded="false" aria-controls="navbar">
                <span class="sr-only">Barre de navigation</span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
            </button>
            <a class="navbar-brand" href="<?php echo $siteurl; ?>/index.php">Cabinet Médical</a>
        </div>
        <div id="navbar" class="navbar-collapse collapse">
            <ul class="nav navbar-nav navbar-left">
                <li><a href="<?php echo $siteurl; ?>/index.php">Accueil</a></li>
                <li class="dropdown">
                    <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false">Usagers
                        <span class="caret"></span></a>
                    <ul class="dropdown-menu" role="menu">
                        <li><a href="<?php echo $siteurl; ?>/usager/rechercher.php">Visualiser</a></li>
                        <li class="divider"></li>
                        <li class="dropdown-header">Saisie</li>
                        <li><a href="<?php echo $siteurl; ?>/usager/saisir.php">Ajouter</a></li>
                    </ul>
                </li>
                <li class="dropdown">
                    <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false">Médecins
                        <span class="caret"></span></a>
                    <ul class="dropdown-menu" role="menu">
                        <li><a href="<?php echo $siteurl; ?>/medecin/rechercher.php">Visualiser</a></li>
                        <li class="divider"></li>
                        <li class="dropdown-header">Saisie</li>
                        <li><a href="<?php echo $siteurl; ?>/medecin/saisir.php">Ajouter</a></li>
                    </ul>
                </li>
                <li class="dropdown">
                    <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false">Consultations
                        <span class="caret"></span></a>
                    <ul class="dropdown-menu" role="menu">
                        <li><a href="<?php echo $siteurl; ?>/consultation/rechercher.php">Visualiser</a></li>
                        <li class="divider"></li>
                        <li class="dropdown-header">Saisie</li>
                        <li><a href="<?php echo $siteurl; ?>/consultation/saisir.php">Ajouter</a></li>
                    </ul>
                </li>
                <li class="dropdown">
                    <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false">Statistiques
                        <span class="caret"></span></a>
                    <ul class="dropdown-menu" role="menu">
                        <li><a href="<?php echo $siteurl; ?>/statistique/usagers.php">Usagers</a></li>
                        <li><a href="<?php echo $siteurl; ?>/statistique/medecins.php">Médecins</a></li>
                    </ul>
                </li>
                <li><a href="<?php echo $siteurl; ?>/assistant/assistant.php">Configuration</a></li>
            </ul>
            <ul class="nav navbar-nav navbar-right">
                <?php
                if (isset($_SESSION['connecté']) && $_SESSION['connecté'] == true) {
                    echo "<li><button type='button' class='btn btn-default navbar-btn navbar-inverse'><a href='$siteurl/index.php?logout'><span class='glyphicon glyphicon-off' aria-hidden='true'></span>  Se déconnecter</a></button></li>";
                }
                ?>
            </ul>
        </div>
    </div>
</nav>
<!-- /container -->
<div class="container theme-showcase" role="main">

<?php

if (isset($_GET)) {
    foreach ($_GET as $cle => $element) {
        switch ($cle) {
            case 'error':
                echo "<div class='alert alert-danger' role='alert'>$element</div>";
                break;
            case 'success':
                echo "<div class='alert alert-success' role='alert'>$element</div>";
                break;
            case 'info':
                echo "<div class='alert alert-info' role='alert'>$element</div>";
                break;
            case 'warning':
                echo "<div class='alert alert-warning' role='alert'>$element</div>";
                break;
        }
    }
}