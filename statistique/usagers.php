<?php

/*
 * Permet d'afficher les statistiques par usager
 */

include_once('../header.php');

try {
    $linkpdo = new PDO("mysql:host=$server;dbname=$db", $login, $mdp);
} catch (PDOException $e) {
    echo "<div class='alert alert-danger' role='alert'>Erreur lors de la connexion à la BDD. Erreur : " . $e->getMessage() . "</div>";
    include('../footer.php');
    exit;
}

$querySearch = $linkpdo->prepare("SELECT
(
    SELECT ROUND(COUNT(*)/(SELECT COUNT(*) FROM USAGERS)*100, 1)
    FROM USAGERS
    WHERE civilite = 'Mr'
    AND DATEDIFF(CURDATE(), dateNaissance) < 25*365
) as HM25,
(
    SELECT ROUND(COUNT(*)/(SELECT COUNT(*) FROM USAGERS)*100, 1)
    FROM USAGERS
    WHERE civilite = 'Mr'
    AND DATEDIFF(CURDATE(), dateNaissance) >= 25*365
    AND DATEDIFF(CURDATE(), dateNaissance) < 50*365
) as HE25ET50,
(
    SELECT ROUND(COUNT(*)/(SELECT COUNT(*) FROM USAGERS)*100, 1)
    FROM USAGERS
    WHERE civilite = 'Mr'
    AND DATEDIFF(CURDATE(), dateNaissance) >= 50*365
) as HP50,
(
    SELECT ROUND(COUNT(*)/(SELECT COUNT(*) FROM USAGERS)*100, 1)
    FROM USAGERS
    WHERE (civilite = 'Mme' OR civilite = 'Mlle')
    AND DATEDIFF(CURDATE(), dateNaissance) < 25*365
) as FM25,
(
    SELECT ROUND(COUNT(*)/(SELECT COUNT(*) FROM USAGERS)*100, 1)
    FROM USAGERS
    WHERE (civilite = 'Mme' OR civilite = 'Mlle')
    AND DATEDIFF(CURDATE(), dateNaissance) >= 25*365
    AND DATEDIFF(CURDATE(), dateNaissance) < 50*365
) as FE25ET50,
(
    SELECT ROUND(COUNT(*)/(SELECT COUNT(*) FROM USAGERS)*100, 1)
    FROM USAGERS
    WHERE (civilite = 'Mme' OR civilite = 'Mlle')
    AND DATEDIFF(CURDATE(), dateNaissance) >= 50*365
) as FP50");

try {
    $querySearch->execute(null);
} catch (Exception $e) {
    echo "<p class='bg-error'>Erreur lors de l'execution de la requete : " . $e->getMessage() . "</p>";
    include('../footer.php');
    exit;
}
$resultats = $querySearch->fetchAll()['0'];

?>
<p>Répartition des usagers selon leur sexe et leur âge</p>

<table class="table table-bordered">
    <thead>
        <tr>
            <td>Tranche d'âge</td>
            <td>Nombre d'hommes</td>
            <td>Nombre de femmes</td>
        </tr>
    </thead>
    <tbody>
        <tr>
            <td>Moins de 25 ans</td>
            <td><?php echo($resultats['HM25']); ?>%</td>
            <td><?php echo($resultats['FM25']); ?>%</td>
        </tr>
        <tr>
            <td>Entre 25 et 50 ans</td>
            <td><?php echo($resultats['HE25ET50']); ?>%</td>
            <td><?php echo($resultats['FE25ET50']); ?>%</td>
        </tr>
        <tr>
            <td>Plus de 50 ans</td>
            <td><?php echo($resultats['HP50']); ?>%</td>
            <td><?php echo($resultats['FP50']); ?>%</td>
        </tr>
    </tbody>
</table>

<?php

    include('../footer.php');