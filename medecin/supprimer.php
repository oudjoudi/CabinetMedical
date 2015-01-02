<?php

/*
 * Permet de supprimer un médecin
 */

include('../config/config.inc.php');

if (!empty($_GET['id'])) {
    try {
        $linkpdo = new PDO("mysql:host=$server;dbname=$db", $login, $mdp);
    } catch (PDOException $e) {
        include('../header.php');
        echo "<div class='alert alert-danger' role='alert'>Erreur lors de la connexion à la BDD. Erreur : " . $e->getMessage() . "</div>";
        include('../footer.php');
        exit;
    }
    $queryDelete = $linkpdo->prepare("DELETE FROM `MEDECINS` WHERE id = :id");
    try {
        if (!$queryDelete->execute(array('id' => $_GET['id']))) {
            $message = "Le médecin n'existe pas";
            header("Location: $siteurl/medecin/rechercher.php?warning=$message");
            exit;
        }
    } catch (Exception $e) {
        $message = "Erreur lors de l'execution de la requête. Erreur : " . $e->getMessage();
        header("Location: $siteurl/medecin/rechercher.php?error=$message");
        exit;
    }
    $querySearch = $linkpdo->prepare('SELECT count(*) AS nb FROM MEDECINS WHERE id = :id');
    try {
        $querySearch->execute(array('id' => $_GET['id']));
    } catch (Exception $e) {
        $message = "Erreur lors de l'execution de la requête. Erreur : " . $e->getMessage();
        header("Location: $siteurl/medecin/rechercher.php?error=$message");
        exit;
    }
    $resultats = $querySearch->fetchAll();
    if ($resultats['0']['nb'] == 0) {
        $message = "Le médecin a bien été supprimé";
        header("Location: $siteurl/medecin/rechercher.php?success=$message");
        exit;
    } else {
        $message = "Un problème est survenu lors de la suppression. Le médecin n'a pas été supprimé";
        header("Location: $siteurl/medecin/rechercher.php?error=$message");
        exit;
    }
} else {
    $message = "Vous ne pouvez pas accéder à cette page directement";
    header("Location: $siteurl/medecin/rechercher.php?warning=$message");
    exit;
}