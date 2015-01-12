<?php

/*
 * Permet de s'authentifier
 */

require('config/config.inc.php');


if (!empty($_POST['username']) && !empty($_POST['password'])) {
    if ($_POST['username'] === $username && $_POST['password'] === $password) {
        session_start();
        $_SESSION['connecté'] = true;
        header("Location: $siteurl/index.php?success=Vous êtes connecté !");
        exit;
    } else {
        header("Location: $siteurl/login.php?error=Nom d'utilisateur ou mot de passe incorrect");
        exit;
    }
}

session_start();
if (isset($_SESSION['connecté']) && $_SESSION['connecté'] == true) {
    header("Location: $siteurl/index.php?warning=Vous êtes déjà connecté !");
    exit;
}
session_destroy();

$form = new Form("Identification", "post", "");
$form->setInput("Nom d'utilisateur", "username", "text");
$form->setInput("Mot de passe", "password", "password");
$form->setButton("Connexion", "submit", "submit", "btn btn-primary");

include('header.php');

echo $form->getForm();

include('footer.php');