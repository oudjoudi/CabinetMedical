<?php

/*
 * Permet d'afficher les statistiques par médecin
 */

include_once('../header.php');

try {
    $linkpdo = new PDO("mysql:host=$server;dbname=$db", $login, $mdp);
} catch (PDOException $e) {
    echo "<div class='alert alert-danger' role='alert'>Erreur lors de la connexion à la BDD. Erreur : " . $e->getMessage() . "</div>";
    include('../footer.php');
    exit;
}

$querySearch = $linkpdo->prepare("SELECT m.civilite, m.nom, m.prenom, SEC_TO_TIME(SUM(c.dureeConsult)*60) as TotalConsult
FROM MEDECINS m, CONSULTATION c
WHERE c.medecin = m.id
GROUP BY m.civilite, m.nom, m.prenom");

try {
    $querySearch->execute(null);
} catch (Exception $e) {
    echo "<p class='bg-error'>Erreur lors de l'execution de la requete : " . $e->getMessage() . "</p>";
    include('../footer.php');
    exit;
}
$resultats = $querySearch->fetchAll();

?>
    <p>Durée totale des consultations effectuées par chaque médecin</p>

    <table class="table table-bordered">
        <thead>
        <tr>
            <td>Médecins</td>
            <td>Nombre d'heures effectuées</td>
        </tr>
        </thead>
        <tbody>
        <?php
            foreach ($resultats as $res) {
                $heures = DateTime::createFromFormat("H:i:s", $res['TotalConsult'])->format('H\hi');
                echo '<tr>';
                echo "<td>" . $res['civilite'] . " " . $res['nom'] . " " . $res['prenom'] . "</td>";
                echo "<td>" . $heures . "</td>";
                echo '</tr>';
            }
        ?>
        </tbody>
    </table>

<?php

include('../footer.php');
