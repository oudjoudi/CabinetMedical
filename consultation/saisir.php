<?php

/*
 * Permet d'afficher le formulaire d'ajout d'une consultation
 */

require('../config/config.inc.php');
require_once('../class/form.class.php');

try {
    $linkpdo = new PDO("mysql:host=$server;dbname=$db", $login, $mdp);
} catch (PDOException $e) {
    include('../header.php');
    echo "<div class='alert alert-danger' role='alert'>Erreur lors de la connexion à la BDD. Erreur : " . $e->getMessage() . "</div>";
    include('../footer.php');
    exit;
}

$querySearch = $linkpdo->prepare('SELECT id, civilite, nom, prenom FROM USAGERS ORDER BY 3, 4 ASC');
try {
    $querySearch->execute(null);
} catch (Exception $e) {
    $message = "Erreur lors de l'execution de la requete : " . $e->getMessage();
    header("Location: $siteurl/consultation/rechercher.php?error=$message");
    exit;
}
$resultatsUsagers = $querySearch->fetchAll();

$usagers = array();
foreach ($resultatsUsagers as $resultat) {
    $usagers[$resultat['id']] = $resultat['civilite'] . ' ' . $resultat['nom'] . ' ' . $resultat['prenom'];
}

$form = new Form("Ajouter une consultation", "post", "ajouter.php");

foreach ($parametresConsultation as $cle => $parametre) {
    switch ($parametre) {
        case "usager":
            $form->setSelect("Nom du patient", "usager", $usagers, null, 'id="selectUsagers"');
            break;
        case "medecin":
            $form->setSelect("Médecin traitant", "medecin", array('0' => "Sélectionner un usager"), null, 'id="selectMedecins"');
            break;
        case "dateConsult":
            $date = new DateTime();
            $form->setInput($cle, $parametre, "text", $date->format('j/m/Y'), 'id="dateConsult"');
            break;
        case "heureConsult":
            $date = new DateTime();
            $form->setInput($cle, $parametre, "text", $date->format('H:i'), "class='datetimepicker'");
            break;
        default:
            $form->setInput($cle, $parametre, "text");
    }
}
$form->setButton("Envoyer", "envoyer", "submit", "btn btn-primary");
$form->setButton("Vider", "vider", "reset", "btn btn-warning");

include('../header.php');

echo $form->getForm();

include('../footer.php');