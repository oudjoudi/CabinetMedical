<?php

/*
 * Permet de traiter le formulaire d'ajout d'une consultation
 */

require("../config/config.inc.php");

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

$consultation = array();

foreach ($parametresConsultation as $parametre) {
    $consultation[$parametre] = (!empty($_POST[$parametre])) ? $_POST[$parametre] : null;
}

$consultation['dateConsult'] = DateTime::createFromFormat('j/m/Y', $_POST['dateConsult'])->format('Y-m-d');
$consultation['heureConsult'] = DateTime::createFromFormat('H:i', $_POST['heureConsult'])->format('H:i:s');

$querySearch = $linkpdo->prepare("
    SELECT COUNT(*) as NbRDV
    FROM `CONSULTATION`
    WHERE medecin = :medecin
    AND dateConsult = :dateConsult
    AND (
        (:heureConsult BETWEEN heureConsult AND DATE_ADD(heureConsult, INTERVAL dureeConsult - 1 MINUTE))
        OR
        (heureConsult BETWEEN :heureConsult AND DATE_ADD(CAST(:heureConsult as TIME), INTERVAL :dureeConsult - 1 MINUTE))
    )
");
try {
    $querySearch->execute($consultation);
} catch (Exception $e) {
    $message = "Erreur lors de l'execution de la requete : " . $e->getMessage();
    header("Location: $siteurl/consultation/rechercher.php?error=$message");
    exit;
}
$nbRDV = $querySearch->fetchAll()[0]['NbRDV'];
if ($nbRDV != 0) {
    $message  = 'Vous ne pouvez pas insérer cette consultation car le médecin sera déjà en consultation à ce moment-là';
    header("Location: $siteurl/consultation/saisir.php?warning=$message");
    exit;
}



$queryInsert = $linkpdo->prepare("INSERT INTO `CONSULTATION` (`usager`, `medecin`, `dateConsult`, `heureConsult`, `dureeConsult`) VALUES (:usager, :medecin, :dateConsult, :heureConsult, :dureeConsult)");
try {
    if (!$queryInsert->execute($consultation)) {
        $message = 'Impossible de créer la nouvelle consultation. Erreur : ' . $queryInsert->errorInfo()[1] . ' : ' .$queryInsert->errorInfo()[2];
        header("Location: $siteurl/consultation/saisir.php?error=$message");
        exit;
    }
} catch (Exception $e) {
    $message  = 'Une erreur s\'est produite lors de l\'ajout de la consultation. Erreur : ' .$e->getMessage();
    header("Location: $siteurl/consultation/saisir.php?error=$message");
    exit;
}

$message = 'La consultation a bien été ajoutée';
header("Location: $siteurl/consultation/saisir.php?success=$message");
exit;