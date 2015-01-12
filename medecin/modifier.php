<?php

/*
 * Permet de modifier un médecin
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

if (!empty($_POST)) {

    foreach ($parametresMedecin as $parametre) {
        $medecin[$parametre] = (!empty($_POST[$parametre])) ? $_POST[$parametre] : null;
    }

    $medecin['id'] = $_GET['id'];

    $queryInsert = $linkpdo->prepare("UPDATE `MEDECINS` SET civilite = :civilite, nom = :nom, prenom = :prenom WHERE id = :id");
    try {
        if (!$queryInsert->execute($medecin)) {
            $message = "Impossible de modifier le médecin. Erreur : " . $queryInsert->errorInfo()[1] . " : " . $queryInsert->errorInfo()[2];
            header("Location: $siteurl/medecin/rechercher.php?error=$message");
            exit;
        }
    } catch (Exception $e) {
        $message = "Une erreur s'est produite lors de la modification du médecin. Erreur : " . $e->getMessage();
        header("Location: $siteurl/medecin/rechercher.php?error=$message");
        exit;
    }

    $message = 'Le médecin ' . $medecin['nom'] . ' ' . $medecin['prenom'] . ' a bien été modifié';
    header("Location: $siteurl/medecin/rechercher.php?success=$message");
    exit;
}

if (!empty($_GET['id'])) {

    $querySearch = $linkpdo->prepare('SELECT * FROM MEDECINS WHERE id = :id');
    try {
        $querySearch->execute(array('id' => $_GET['id']));
    } catch (Exception $e) {
        $message = "Impossible de récupérer les informations du médecin. Erreur : " . $e->getMessage();
        header("Location: $siteurl/medecin/rechercher.php?error=$message");
        exit;
    }
    $resultats = $querySearch->fetchAll();
    if (empty($resultats)) {
        $message = "Le médecin n'existe pas";
        header("Location: $siteurl/medecin/rechercher.php?error=$message");
        exit;
    }
    $ancienContact = $resultats[0];

    include('../header.php');

    $form = new Form("Modifier un médecin", "post", "");
    foreach ($parametresMedecin as $cle => $parametre) {
        $form->setInput($cle, $parametre, "text", $ancienContact[$parametre], "required");
    }
    $form->setButton("Envoyer", "envoyer", "submit", "btn btn-primary");

    echo $form->getForm();

    include('../footer.php');

} else {
    $message = "Vous ne pouvez pas accéder à la page de modification directement";
    header("Location: $siteurl/medecin/rechercher.php?warning=$message");
    exit;
}