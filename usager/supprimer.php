<?php

/*
 * Permet de supprimer un usager
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
    $queryDelete = $linkpdo->prepare("DELETE FROM `USAGERS` WHERE id = :id");
    try {
        if ($queryDelete->execute(array('id' => $_GET['id']))) {
            $message = "L'usager n'existe pas";
            header("Location: $siteurl/usager/rechercher.php?warning=$message");
            exit;
        }
    } catch (Exception $e) {
        $message = "Erreur lors de l'execution de la reqûete. Erreur : " . $e->getMessage();
        header("Location: $siteurl/usager/rechercher.php?error=$message");
        exit;
    }
    $querySearch = $linkpdo->prepare('SELECT count(*) AS nb FROM USAGERS WHERE id = :id');
    try {
        $querySearch->execute(array('id' => $_GET['id']));
    } catch (Exception $e) {
        $message = "Erreur lors de l'execution de la reqûete. Erreur : " . $e->getMessage();
        header("Location: $siteurl/usager/rechercher.php?error=$message");
        exit;
    }
    $resultats = $querySearch->fetchAll();
    if ($resultats['0']['nb'] == 0) {
        $message = "L'usager a bien été supprimé";
        header("Location: $siteurl/usager/rechercher.php?success=$message");
        exit;
    } else {
        $message = "Un problème est survenu lors de la suppression. L'usager n'a pas été supprimé";
        header("Location: $siteurl/usager/rechercher.php?error=$message");
        exit;
    }
} else {
    $message = "Vous ne pouvez pas accéder à cette page directement";
    header("Location: $siteurl/usager/rechercher.php?warning=$message");
    exit;
}
