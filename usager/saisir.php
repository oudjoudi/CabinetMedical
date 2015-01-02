<?php

/*
 * Permet d'afficher le formulaire d'ajout d'un médecin
 */

include_once('../header.php');

try {
    $linkpdo = new PDO("mysql:host=$server;dbname=$db", $login, $mdp);
} catch (PDOException $e) {
    echo "<div class='alert alert-danger' role='alert'>Erreur lors de la connexion à la BDD. Erreur : " . $e->getMessage() . "</div>";
    include('../footer.php');
    exit;
}

$querySearch = $linkpdo->prepare('SELECT * FROM MEDECINS');
try {
    $querySearch->execute();
} catch (Exception $e) {
    $message = "Erreur lors de l'execution de la requete : " . $e->getMessage();
    header("Location: $siteurl/consultation/rechercher.php?error=$message");
    exit;
}
$medecins = $querySearch->fetchAll();
$tableauMedecins = array();
foreach ($medecins as $medecin) {
    $tableauMedecins[$medecin['id']] = $medecin['civilite'] . ' ' . $medecin['nom'] . ' ' . $medecin['prenom'];
}

$form = new Form("Ajouter un usager", "post", "ajouter.php");

foreach ($parametresUsager as $cle => $parametre) {
    if ($parametre == "dateNaissance") {
        $form->setInput($cle . ' (Format jj/mm/aaaa)', $parametre, "text", "", "id='dateNaissance'");
    } elseif ($parametre == "civilite") {
        $form->setSelect($cle, $parametre, array('Mr' => 'Mr', 'Mme' => 'Mme', 'Mlle' => 'Mlle'));
    } else {
        $form->setInput($cle, $parametre, "text", "", "required");
    }
}
$form->setSelect("Médecin traitant", "medecin", $tableauMedecins);
$form->setButton("Envoyer", "envoyer", "submit", "btn btn-primary");
$form->setButton("Vider", "vider", "reset", "btn btn-warning");

echo $form->getForm();

include('../footer.php');