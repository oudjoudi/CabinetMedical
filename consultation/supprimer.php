<?php

/*
 * Permet de supprimer une consultation
 */

require('../config/config.inc.php');

if (!empty($_GET['id'])) {
    try {
        $linkpdo = new PDO("mysql:host=$server;dbname=$db", $login, $mdp);
    } catch (PDOException $e) {
        include('../header.php');
        echo "<div class='alert alert-danger' role='alert'>Erreur lors de la connexion à la BDD. Erreur : " . $e->getMessage() . "</div>";
        include('../footer.php');
        exit;
    }
    $queryDelete = $linkpdo->prepare("DELETE FROM `CONSULTATION` WHERE id = :id");
    try {
        if (!$queryDelete->execute(array('id' => $_GET['id']))) {
            $message = "La consultation n'existe pas";
            header("Location: $siteurl/consultation/rechercher.php?warning=$message");
            exit;
        }
    } catch (Exception $e) {
        $message = "Erreur lors de l'execution de la requête. Erreur : " . $e->getMessage();
        header("Location: $siteurl/consultation/rechercher.php?error=$message");
        exit;
    }
    $querySearch = $linkpdo->prepare('SELECT count(*) AS nb FROM CONSULTATION WHERE id = :id');
    try {
        $querySearch->execute(array('id' => $_GET['id']));
    } catch (Exception $e) {
        $message = "Erreur lors de l'execution de la requête. Erreur : " . $e->getMessage();
        header("Location: $siteurl/consultation/rechercher.php?error=$message");
        exit;
    }
    $resultats = $querySearch->fetchAll();
    if ($resultats['0']['nb'] == 0) {
        $message = "La consultation a bien été supprimée";
        header("Location: $siteurl/consultation/rechercher.php?success=$message");
        exit;
    } else {
        $message = "Un problème est survenu lors de la suppression. La consultation n'a pas été supprimée";
        header("Location: $siteurl/consultation/rechercher.php?error=$message");
        exit;
    }
} else {
    $message = "Vous ne pouvez pas accéder à cette page directement";
    header("Location: $siteurl/consultation/rechercher.php?warning=$message");
    exit;
}
