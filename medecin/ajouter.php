<?php

/*
 * Permet de traiter le formulaire d'ajout d'un médecin
 */

require("../config/config.inc.php");

session_start();
if ($_SESSION['connecté'] !== true) {
    header("Location: $siteurl/login.php?warning=Veuillez vous connecter pour accéder à cette page");
    exit;
}

try {
    $linkpdo = new PDO("mysql:host=$server;dbname=$db", $login, $mdp);
} catch (PDOException $e) {
    include('../header.php');
    echo "<div class='alert alert-danger' role='alert'>Erreur lors de la connexion à la BDD. Erreur : " . $e->getMessage() . "</div>";
    include('../footer.php');
    exit;
}

$medecin = array();

foreach ($parametresMedecin as $parametre) {
    $medecin[$parametre] = (!empty($_POST[$parametre])) ? $_POST[$parametre] : null;
}

$querySearch = $linkpdo->prepare('SELECT * FROM MEDECINS WHERE civilite = :civilite AND nom = :nom AND prenom = :prenom');
try {
    if (!$querySearch->execute($medecin)) {
        $message = 'Impossible de vérifier si le nouveau médecin ' . $medecin['nom'] . ' ' . $medecin['prenom'] . ' existe. Erreur : ' . $querySearch->errorInfo()[1] . ' : ' . $querySearch->errorInfo()[2];
        header("Location: $siteurl/medecin/saisir.php?error=$message");
        exit;
    }
} catch (Exception $e) {
    $message = 'Une erreur s\'est produite lors de la vérification du médecin ' . $medecin['nom'] . ' ' . $medecin['prenom'] . '. Erreur : ' . $e->getMessage();
    header("Location: $siteurl/medecin/saisir.php?error=$message");
    exit;
}
$resultat = $querySearch->fetchAll();

if (!empty($resultat)) {
    $message = 'Le médecin ' . $medecin['nom'] . ' ' . $medecin['prenom'] . ' existe déjà';
    header("Location: $siteurl/medecin/saisir.php?error=$message");
    exit;
}

$queryInsert = $linkpdo->prepare("INSERT INTO `MEDECINS` (`civilite`, `nom`, `prenom`) VALUES (:civilite, :nom, :prenom)");
try {
    if (!$queryInsert->execute($medecin)) {
        $message = 'Impossible de créer le nouveau médecin ' . $medecin['nom'] . ' ' . $medecin['prenom'] . ' Erreur : ' . $queryInsert->errorInfo()[1] . ' : ' .$queryInsert->errorInfo()[2];
        header("Location: $siteurl/medecin/saisir.php?error=$message");
        exit;
    }
} catch (Exception $e) {
    $message  = 'Une erreur s\'est produite lors de l\'ajout du médecin ' . $medecin['nom'] . ' ' . $medecin['prenom'] . ' Erreur : ' .$e->getMessage();
    header("Location: $siteurl/medecin/saisir.php?error=$message");
    exit;
}

$message = 'Le médecin ' . $medecin['nom'] . ' ' . $medecin['prenom'] . ' a bien été ajouté';
header("Location: $siteurl/medecin/saisir.php?success=$message");
exit;
