<?php

/*
 * Permet d'afficher le formulaire d'ajout d'un médecin
 */

require('../config/config.inc.php');

try {
    $linkpdo = new PDO("mysql:host=$server;dbname=$db", $login, $mdp);
} catch (PDOException $e) {
    include('../header.php');
    echo "<div class='alert alert-danger' role='alert'>Erreur lors de la connexion à la BDD. Erreur : " . $e->getMessage() . "</div>";
    include('../footer.php');
    exit;
}

$form = new Form("Ajouter un médecin", "post", "ajouter.php");

foreach ($parametresMedecin as $cle => $parametre) {
    $form->setInput($cle, $parametre, "text", "", "required");
}
$form->setButton("Envoyer", "envoyer", "submit", "btn btn-primary");
$form->setButton("Vider", "vider", "reset", "btn btn-warning");

echo $form->getForm();

include('../footer.php');