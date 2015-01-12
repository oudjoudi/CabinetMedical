<?php

/*
 * Permet de modifier une consultation
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

    foreach ($parametresConsultation as $parametre) {
        $consultation[$parametre] = (!empty($_POST[$parametre])) ? $_POST[$parametre] : null;
    }

    $consultation['id'] = $_GET['id'];
    $consultation['dateConsult'] = DateTime::createFromFormat('j/m/Y', $_POST['dateConsult'])->format('Y-m-d');
    $consultation['heureConsult'] = DateTime::createFromFormat('H:i', $_POST['heureConsult'])->format('H:i:s');

    $queryInsert = $linkpdo->prepare("UPDATE `CONSULTATION` SET usager = :usager, medecin = :medecin, dateConsult = :dateConsult, heureConsult = :heureConsult, dureeConsult = :dureeConsult WHERE id = :id");
    try {
        if (!$queryInsert->execute($consultation)) {
            $message = "Impossible de modifier la consultation. Erreur : " . $queryInsert->errorInfo()[1] . " : " . $queryInsert->errorInfo()[2];
            header("Location: $siteurl/consultation/rechercher.php?error=$message");
            exit;
        }
    } catch (Exception $e) {
        $message = "Une erreur s'est produite lors de la modification de la consultation. Erreur : " . $e->getMessage();
        header("Location: $siteurl/consultation/rechercher.php?error=$message");
        exit;
    }

    $message = 'La consultation a bien été modifiée';
    header("Location: $siteurl/consultation/rechercher.php?success=$message");
    exit;
}

if (!empty($_GET['id'])) {

    $querySearch = $linkpdo->prepare('SELECT * FROM CONSULTATION WHERE id = :id');
    try {
        $querySearch->execute(array('id' => $_GET['id']));
    } catch (Exception $e) {
        $message = "Impossible de récupérer les informations de la consultation. Erreur : " . $e->getMessage();
        header("Location: $siteurl/consultation/rechercher.php?error=$message");
        exit;
    }
    $resultats = $querySearch->fetchAll();
    if (empty($resultats)) {
        $message = "La consultation n'existe pas";
        header("Location: $siteurl/consultation/rechercher.php?error=$message");
        exit;
    }
    $ancienneConsultation = $resultats[0];

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

    $querySearch = $linkpdo->prepare('SELECT * FROM MEDECINS');
    try {
        $querySearch->execute(null);
    } catch (Exception $e) {
        $message = "Erreur lors de l'execution de la requete : " . $e->getMessage();
        header("Location: $siteurl/consultation/rechercher.php?error=$message");
        exit;
    }
    $medecins = $querySearch->fetchAll();

    $listeMedecins = array();
    foreach ($medecins as $medecin) {
        $listeMedecins[$medecin['id']] = $medecin['civilite'] . ' ' . $medecin['nom'] . ' ' . $medecin['prenom'];
    }

    include('../header.php');

    $form = new Form("Modifier une consultation", "post", "");
    foreach ($parametresConsultation as $cle => $parametre) {
        switch ($parametre) {
            case "usager":
                $form->setSelect("Nom du patient", "usager", $usagers, $ancienneConsultation[$parametre], 'id="selectUsagers"');
                break;
            case "medecin":
                $form->setSelect("Médecin traitant", "medecin", $listeMedecins, $ancienneConsultation[$parametre], 'id="selectMedecins"');
                break;
            case "dateConsult":
                $date = DateTime::createFromFormat('Y-m-d', $ancienneConsultation[$parametre])->format('j/m/Y');
                $form->setInput($cle, $parametre, "text", $date, 'id="dateConsult"');
                break;
            case "heureConsult":
                $heure = DateTime::createFromFormat('H:i:s', $ancienneConsultation[$parametre])->format('H:i');
                $form->setInput($cle, $parametre, "text", $heure, "class='datetimepicker'");
                break;
            default:
                $form->setInput($cle, $parametre, "text", $ancienneConsultation[$parametre]);
        }
    }
    $form->setButton("Envoyer", "envoyer", "submit", "btn btn-primary");

    echo $form->getForm();

    include('../footer.php');

} else {
    $message = "Vous ne pouvez pas accéder à la page de modification directement";
    header("Location: $siteurl/consultation/rechercher.php?warning=$message");
    exit;
}
