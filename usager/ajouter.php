<?php

include("../config/config.inc.php");

session_start();
if ($_SESSION['connecté'] != true) {
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

$usager = array();

foreach ($parametresUsager as $parametre) {
    $usager[$parametre] = (!empty($_POST[$parametre])) ? $_POST[$parametre] : null;
}

$usager['dateNaissance'] = DateTime::createFromFormat('j/m/Y', $_POST['dateNaissance'])->format('Y-m-d');

$querySearch = $linkpdo->prepare('SELECT * FROM USAGERS WHERE civilite = :civilite AND nom = :nom AND prenom = :prenom AND adresse = :adresse AND dateNaissance = :dateNaissance AND lieuNaissance = :lieuNaissance AND numSecu = :numSecu');
try {
    if (!$querySearch->execute($usager)) {
        $message = 'Impossible de vérifier si le nouveau patient ' . $usager['nom'] . ' ' . $usager['prenom'] . ' existe. Erreur : ' . $querySearch->errorInfo()[1] . ' : ' . $querySearch->errorInfo()[2];
        header("Location: $siteurl/usager/saisir.php?error=$message");
        exit;
    }
} catch (Exception $e) {
    $message = 'Une erreur s\'est produite lors de la vérification du patient ' . $usager['nom'] . ' ' . $usager['prenom'] . '. Erreur : ' . $e->getMessage();
    header("Location: $siteurl/usager/saisir.php?error=$message");
    exit;
}
$resultat = $querySearch->fetchAll();

if (!empty($resultat)) {
    $message = 'Le patient ' . $usager['nom'] . ' ' . $usager['prenom'] . ' existe déjà';
    header("Location: $siteurl/usager/saisir.php?error=$message");
    exit;
}

$usager['idReferent'] = $_POST['medecin'];
$queryInsert = $linkpdo->prepare("INSERT INTO `USAGERS` (`civilite`, `nom`, `prenom`, `adresse`, `dateNaissance`, `lieuNaissance`, `numSecu`, `id_referent`) VALUES (:civilite, :nom, :prenom, :adresse, :dateNaissance, :lieuNaissance, :numSecu, :idReferent)");
try {
    if (!$queryInsert->execute($usager)) {
        $message = 'Impossible de créer le nouveau patient ' . $usager['nom'] . ' ' . $usager['prenom'] . ' Erreur : ' . $queryInsert->errorInfo()[1] . ' : ' .$queryInsert->errorInfo()[2];
        header("Location: $siteurl/usager/saisir.php?error=$message");
        exit;
    }
} catch (Exception $e) {
    $message  = 'Une erreur s\'est produite lors de l\'ajout du patient ' . $usager['nom'] . ' ' . $usager['prenom'] . ' Erreur : ' .$e->getMessage();
    header("Location: $siteurl/usager/saisir.php?error=$message");
    exit;
}

$message = 'Le patient ' . $usager['nom'] . ' ' . $usager['prenom'] . ' a bien été ajouté';
header("Location: $siteurl/usager/saisir.php?success=$message");
exit;