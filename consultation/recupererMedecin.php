<?php

include('../config/config.inc.php');

session_start();
if ($_SESSION['connecté'] != true) {
    header("Location: $siteurl/login.php?warning=Veuillez vous connecter pour accéder à cette page");
    exit;
}

// Test pour savoir si c'est bien une requête AJAX, sinon on affiche un message d'erreur
if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
    header('Content-Type: application/json');
    try {
        $linkpdo = new PDO("mysql:host=$server;dbname=$db", $login, $mdp);
    } catch (PDOException $e) {
        echo json_encode(array(
            'statut' => 'error',
            'error' => "Erreur lors de la connexion à la BDD. Erreur : " . $e->getMessage()
        ));
        exit;
    }

    $querySearch = $linkpdo->prepare('SELECT id_referent FROM USAGERS WHERE id = :idUsager');
    try {
        $querySearch->execute($_POST);
    } catch (Exception $e) {
        echo json_encode(array(
            'statut' => 'error',
            'error' => "Erreur lors de l'execution de la requete : " . $e->getMessage()
        ));
        exit;
    }
    $idReferent = $querySearch->fetchAll()[0]['id_referent'];

    $querySearch = $linkpdo->prepare('SELECT * FROM MEDECINS');
    try {
        $querySearch->execute(null);
    } catch (Exception $e) {
        echo json_encode(array(
            'statut' => 'error',
            'error' => "Erreur lors de l'execution de la requete : " . $e->getMessage()
        ));
        exit;
    }
    $medecins = $querySearch->fetchAll();

    echo json_encode(array(
        'statut' => 'success',
        'medecins' => $medecins,
        'traitant' => $idReferent
    ));

} else {
    die("Vous ne pouvez pas accéder à cette page. Elle est utilisable uniquement via un appel de script");
}