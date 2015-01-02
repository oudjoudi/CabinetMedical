<?php

/*
 * Permet de rechercher un médecin
 */

include_once('../header.php');

if (!empty($_GET)) {
    try {
        $linkpdo = new PDO("mysql:host=$server;dbname=$db", $login, $mdp);
    } catch (PDOException $e) {
        echo "<div class='alert alert-danger' role='alert'>Erreur lors de la connexion à la BDD. Erreur : " . $e->getMessage() . "</div>";
        include('../footer.php');
        exit;
    }

    $usager = array();

    foreach ($parametresUsager as $parametre) {
        $usager[$parametre] = (!empty($_GET[$parametre])) ? $_GET[$parametre] : '%';
    }
    if (!empty($_GET['dateNaissance'])) {
        $consultation['dateNaissance'] = DateTime::createFromFormat('j/m/Y', $_GET['dateNaissance'])->format('Y-m-d');
    }


    $querySearch = $linkpdo->prepare('SELECT * FROM USAGERS WHERE civilite LIKE :civilite AND nom LIKE :nom AND prenom LIKE :prenom AND adresse LIKE :adresse AND dateNaissance LIKE :dateNaissance AND lieuNaissance LIKE :lieuNaissance AND numSecu LIKE :numSecu');
    try {
        $querySearch->execute($usager);
    } catch (Exception $e) {
        $message = "Erreur lors de l'execution de la requete : " . $e->getMessage();
        header("Location: $siteurl/usager/rechercher.php?error=$message");
        exit;
    }
    $resultats = $querySearch->fetchAll();
    if (empty($resultats)) {
        echo '<p class="bg-info">Aucun résultat <br /></p>';
    }
} else {
    $resultats = null;
}

?>

<p><i> Pour obtenir tous les résultats, laisser les cases vides et cliquer sur Envoyer </i></p>

<?php

$form = new Form("Rechercher un usager", "get", null);

foreach ($parametresUsager as $cle => $parametre) {
    if ($parametre == "dateNaissance") {
        $form->setInput($cle, $parametre, "text", (!empty($_GET[$parametre])) ? $_GET[$parametre] : '', "id='dateNaissance'");
    } elseif ($parametre == "civilite") {
        $form->setSelect($cle, $parametre, array('Mr' => 'Mr', 'Mme' => 'Mme', 'Mlle' => 'Mlle'));
    } else {
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
    foreach ($parametresUsager as $cle => $parametre) {
        echo "<th>$cle</th>";
    }
    echo'<th>Options</th></thead><tbody>';

    foreach ($resultats as $res) {
        $date = DateTime::createFromFormat('Y-m-d', $res['dateNaissance'])->format('j/m/Y');
        echo '<tr>';
        foreach ($parametresUsager as $parametre) {
            if ($parametre == "dateNaissance") {
                echo "<td> $date </td>";
            } else {
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
