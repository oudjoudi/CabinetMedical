<?php

/*
 * Permet de rechercher une consultation
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

if (!empty($_GET)) {

    $consultation = array();

    foreach ($parametresConsultation as $parametre) {
        $consultation[$parametre] = (!empty($_GET[$parametre])) ? $_GET[$parametre] : '%';
    }
    if (!empty($_GET['dateConsult'])) {
        $consultation['dateConsult'] = DateTime::createFromFormat('j/m/Y', $_GET['dateConsult'])->format('Y-m-d');
    }

    $querySearch = $linkpdo->prepare('SELECT * FROM CONSULTATION WHERE usager LIKE :usager AND medecin LIKE :medecin AND dateConsult LIKE :dateConsult AND heureConsult LIKE :heureConsult AND dureeConsult LIKE :dureeConsult ORDER BY dateConsult DESC, heureConsult DESC');
    try {
        $querySearch->execute($consultation);
    } catch (Exception $e) {
        $message = "Erreur lors de l'execution de la requete : " . $e->getMessage();
        header("Location: $siteurl/consultation/rechercher.php?error=$message");
        exit;
    }
    $resultats = $querySearch->fetchAll();
    if (empty($resultats)) {
        echo '<p class="bg-info">Aucun résultat <br /></p>';
    }
} else {
    $resultats = null;
}

include('../header.php');

?>

    <p><i> Pour obtenir tous les résultats, laisser les cases vides et cliquer sur Envoyer </i></p>

<?php

$querySearch = $linkpdo->prepare('SELECT id, civilite, nom, prenom FROM USAGERS');
try {
    $querySearch->execute(null);
} catch (Exception $e) {
    $message = "Erreur lors de l'execution de la requete : " . $e->getMessage();
    header("Location: $siteurl/consultation/rechercher.php?error=$message");
    exit;
}
$resultatsUsagers = $querySearch->fetchAll();
$usagers = array();

$usagers[''] = 'Tous les usagers';
foreach ($resultatsUsagers as $resultat) {
    $usagers[$resultat['id']] = $resultat['civilite'] . ' ' . $resultat['nom'] . ' ' . $resultat['prenom'];
}

$querySearch = $linkpdo->prepare('SELECT id, civilite, nom, prenom FROM MEDECINS');
try {
    $querySearch->execute(null);
} catch (Exception $e) {
    $message = "Erreur lors de l'execution de la requete : " . $e->getMessage();
    header("Location: $siteurl/consultation/rechercher.php?error=$message");
    exit;
}
$resultatsMedecins = $querySearch->fetchAll();
$medecins = array();

$medecins[''] = 'Tous les médecins';
foreach ($resultatsMedecins as $resultat) {
    $medecins[$resultat['id']] = $resultat['civilite'] . ' ' . $resultat['nom'] . ' ' . $resultat['prenom'];
}

$form = new Form("Rechercher une consultation ", "get", null);

foreach ($parametresConsultation as $cle => $parametre) {
    switch ($parametre) {
        case "usager":
            $form->setSelect("Nom du patient", "usager", $usagers, (!empty($_GET[$parametre])) ? $_GET[$parametre] : null);
            break;
        case "medecin":
            $form->setSelect("Médecin traitant", "medecin", $medecins);
            break;
        case "dateConsult":
            $form->setInput($cle, $parametre, "text", (!empty($_GET[$parametre])) ? $_GET[$parametre] : '', "id='dateConsult'");
            break;
        case "heureConsult":
            $form->setInput($cle, $parametre, "text", (!empty($_GET[$parametre])) ? $_GET[$parametre] : '', "class='datetimepicker'");
            break;
        default:
            $form->setInput($cle, $parametre, "text", (!empty($_GET[$parametre])) ? $_GET[$parametre] : '', null);
    }
}
$form->setButton("Envoyer", "envoyer", "submit", "btn btn-primary");
$form->setButton("Vider", "vider", "reset", "btn btn-warning");

echo $form->getForm();

?>



<?php

if (!empty($resultats)) {
    echo '<div class="table-responsive">
            <table class="table table-striped table-bordered">
                <caption> RÉSULTATS DE VOTRE RECHERCHE</caption>
                <thead>';
    foreach ($parametresConsultation as $cle => $parametre) {
        echo "<th>$cle</th>";
    }
    echo'<th>Options</th></thead><tbody>';

    foreach ($resultats as $res) {
        $date = DateTime::createFromFormat('Y-m-d', $res['dateConsult'])->format('j/m/Y');
        $heure = DateTime::createFromFormat('H:i:s', $res['heureConsult'])->format('H:i');
        echo '<tr>';
        foreach ($parametresConsultation as $parametre) {
            switch ($parametre) {
                case "usager":
                    $id = $res[$parametre];
                    echo "<td> $usagers[$id] </td>";
                    break;
                case "medecin":
                    $id = $res[$parametre];
                    echo "<td> $medecins[$id] </td>";
                    break;
                case "dateConsult":
                    echo "<td> $date </td>";
                    break;
                case "heureConsult":
                    echo "<td> $heure </td>";
                    break;
                default:
                    echo "<td> $res[$parametre] </td>";
            }
        }
        echo '<td>
                <a href="modifier.php?id=' . $res['id'] . '">Modifier</a>
                <a href="supprimer.php?id=' . $res['id'] . '">Supprimer</a>
            </td></tr>';
    }
    echo '</tbody></table></div>';
}

?>

<?php

include_once('../footer.php');
