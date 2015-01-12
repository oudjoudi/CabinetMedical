<?php

/*
 * Permet de gérer la configuration interne du programme
 */

include_once('../class/form.class.php');

if (file_exists(dirname(__FILE__) . '/../config/configuration.xml')) {
    session_start();
    if (!isset($_SESSION['connecté']) || $_SESSION['connecté'] !== true) {
        require('../config/config.inc.php');
        header("Location: $siteurl/login.php?warning=Veuillez vous connecter pour accéder à cette page");
        exit;
    }
}

?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Assistant - Cabinet Médical</title>

    <!-- Bootstrap -->
    <link href="../public/css/bootstrap.min.css" rel="stylesheet">
    <link href="../public/css/style.css" rel="stylesheet">

    <link href="../public/css/bootstrap-datetimepicker.min.css" rel="stylesheet">
    <link href="../public/css/datepicker3.standalone.min.css" rel="stylesheet">

</head>
<body role="document">
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
                <a class="navbar-brand" href="../index.php">Cabinet Médical</a>
            </div>
            <div id="navbar" class="navbar-collapse collapse">
                <ul class="nav navbar-nav navbar-left">
                    <li><a href="../index.php">Accueil</a></li>
                    <li><a href="assistant.php">Configuration</a></li>
                </ul>
            </div>
        </div>
    </nav>
    <div class="container theme-showcase" role="main">
        <?php
            // On initiale des variables à des valeurs par défaut ...
            $server = 'localhost';
            $db = '';
            $login = 'root';
            $mdp = '';
            $siteurl = str_replace('/assistant/assistant.php', '', "http://".$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI']);
            $username = '';
            $password = '';

            // ... puis on remplis si la configuration existe
            if (file_exists(dirname(__FILE__) . '/../config/configuration.xml')) {
                include('../config/config.inc.php');
            }

            $form = new Form("Configuration", "post", "configurateur.php");
            $form->setInput("Adresse du serveur SQL", "server", "text", "localhost", $server);
            $form->setInput("Nom de la base de données", "database", "text", $db);
            $form->setInput("Nom d'utilisateur SQL", "login", "text", $login);
            $form->setInput("Mot de passe du serveur SQL", "pass", "password");
            $form->setInput("URL du site web", "url", "text", $siteurl);
            $form->setInput("Nom d'utilisateur administrateur", "username", "text", $username);
            $form->setInput("Mot de passe administrateur", "password", "password");
            $form->setButton("Enregistrer", 'enregistrer', "submit", "btn btn-primary");
            echo $form->getForm();

         ?>

    </div>
    <script src="../public/js/jquery-2.1.3.min.js"></script>
    <script src="../public/js/bootstrap.min.js"></script>
</body>
</html>
