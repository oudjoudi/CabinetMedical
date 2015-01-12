<?php

/*
 * Permet de modifier un usager
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

    foreach ($parametresUsager as $parametre) {
        $usager[$parametre] = (!empty($_POST[$parametre])) ? $_POST[$parametre] : null;
    }

    $usager['id'] = $_GET['id'];
    $usager['dateNaissance'] = DateTime::createFromFormat('j/m/Y', $usager['dateNaissance'])->format('Y-m-d');
    $usager['medecin'] = $_POST['medecin'];

    $queryInsert = $linkpdo->prepare("UPDATE `USAGERS` SET civilite = :civilite, nom = :nom, prenom = :prenom, adresse = :adresse, dateNaissance = :dateNaissance, lieuNaissance = :lieuNaissance, numSecu = :numSecu, id_referent = :medecin WHERE id = :id");
    try {
        if (!$queryInsert->execute($usager)) {
            $message = "Impossible de modifier l'usager. Erreur : " . $queryInsert->errorInfo()[1] . " : " . $queryInsert->errorInfo()[2];
            header("Location: $siteurl/usager/rechercher.php?error=$message");
            exit;
        }
    } catch (Exception $e) {
        $message = "Une erreur s'est produite lors de la modification de l'usager. Erreur : " . $e->getMessage();
        header("Location: $siteurl/usager/rechercher.php?error=$message");
        exit;
    }

    $message = 'Le patient ' . $usager['nom'] . ' ' . $usager['prenom'] . ' a bien été modifié';
    header("Location: $siteurl/usager/rechercher.php?success=$message");
    exit;
}
if (!empty($_GET['id'])) {

    $querySearch = $linkpdo->prepare('SELECT * FROM USAGERS WHERE id = :id');
    try {
        $querySearch->execute(array('id' => $_GET['id']));
    } catch (Exception $e) {
        $message = "Impossible de récupérer les informations de l'usager. Erreur : " . $e->getMessage();
        header("Location: $siteurl/usager/rechercher.php?error=$message");
        exit;
    }
    $resultats = $querySearch->fetchAll();
    if (empty($resultats)) {
        $message = "Le patient n'existe pas";
        header("Location: $siteurl/usager/rechercher.php?error=$message");
        exit;
    }
    $ancienContact = $resultats[0];

    $querySearch = $linkpdo->prepare('SELECT * FROM MEDECINS');
    try {
        $querySearch->execute();
    } catch (Exception $e) {
        $message = "Impossible de récupérer les informations des médecins. Erreur : " . $e->getMessage();
        header("Location: $siteurl/usager/rechercher.php?error=$message");
        exit;
    }
    $medecins = $querySearch->fetchAll();
    $tableauMedecins = array();
    foreach ($medecins as $medecin) {
        $tableauMedecins[$medecin['id']] = $medecin['civilite'] . ' ' . $medecin['nom'] . ' ' . $medecin['prenom'];
    }

    include('../header.php');

    $form = new Form("Modifier un usager", "post", "");
    foreach ($parametresUsager as $cle => $parametre) {
        if ($parametre == "dateNaissance") {
            $date = DateTime::createFromFormat('Y-m-d', $ancienContact['dateNaissance'])->format('j/m/Y');
            $form->setInput($cle . ' (Format jj/mm/aaaa)', $parametre, "text", $date, "id='dateNaissance'");
        } elseif ($parametre == "civilite") {
            $form->setSelect($cle, $parametre, array('Mr' => 'Mr', 'Mme' => 'Mme', 'Mlle' => 'Mlle'), $ancienContact[$parametre]);
        } else {
            $form->setInput($cle, $parametre, "text", $ancienContact[$parametre], "required");
        }
    }
    $form->setSelect("Médecin traitant", "medecin", $tableauMedecins, $ancienContact['id_referent']);
    $form->setButton("Envoyer", "envoyer", "submit", "btn btn-primary");

    echo $form->getForm();

    include('../footer.php');

} else {
    $message = "Vous ne pouvez pas accéder à la page de modification directement";
    header("Location: $siteurl/usager/rechercher.php?warning=$message");
    exit;
}
