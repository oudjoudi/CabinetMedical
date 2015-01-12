<?php

/*
 * Permet d'enregistrer et vérifier la configuration interne du programme
 */

if (!empty($_POST)) {

    // Enregistrement de la configuration
    $xmlDebut = '<?xml version="1.0" encoding="UTF-8" ?><config></config>';
    $docXML = new SimpleXMLElement($xmlDebut);

    $serveurSQL = $docXML->addChild('serveurSQL');
    $serveurSQL->addChild("server", $_POST['server']);
    $serveurSQL->addChild("database", $_POST['database']);
    $serveurSQL->addChild("login", $_POST['login']);
    $serveurSQL->addChild("password", $_POST['pass']);

    $site = $docXML->addChild("site");
    $site->addChild("url", $_POST['url']);
    $site->addChild("username", $_POST['username']);
    $site->addChild("password", $_POST['password']);

    // On utilise DOM pour formater le texte et avoir un beau fichier lisible, on pourrait utiliser SimpleXML mais le fichier est illible ...
    // De plus, ça nous permet de valider le fichier XML avec un schéma XSD
    $dom = new DOMDocument();
    $dom->preserveWhiteSpace = false;
    $dom->formatOutput = true;
    $dom->loadXML($docXML->asXML());
    if (!$dom->schemaValidate(dirname(__FILE__) . "/../config/configuration.xsd")) {
        throw new Exception("Erreur lors de la validation du fichier de configuration");
    }
    if (!$dom->save(dirname(__FILE__) . "/../config/configuration.xml")) {
        throw new Exception("Erreur lors de l'écriture du fichier de configuration");
    };

    // Installation de la base de données sur le serveur SQL
    try {
        $server = $_POST['server'];
        $db = $_POST['database'];
        $login = $_POST['login'];
        $mdp = $_POST['pass'];
        $linkpdo = new PDO("mysql:host=$server;dbname=$db", $login, $mdp);
    } catch (PDOException $e) {
        throw new Exception("Erreur lors de la connexion à la BDD. Erreur : " . $e->getMessage());
    }

    $temp = '';
    $lignes = file(dirname(__FILE__) . "/CabinetMedical.sql");
    foreach ($lignes as $ligne) {
        // On supprime les commentaires
        if (substr($ligne, 0, 2) == '--' || $ligne == '' || substr($ligne, 0, 1) == '#') {
            continue;
        }
        $temp .= $ligne;

        // On cherche les point-virgules pour les fins de requêtes
        if (substr(trim($ligne), -1, 1) == ';') {
            if (!$linkpdo->query($temp)) {
                throw new Exception("Erreur lors de l'importation de la base de données");
            }
            $temp = '';
        }
    }
    header('Location: ../index.php?success=Le cabinet médical est bien installé');

} else {
    throw new Exception("Vous ne pouvez pas accéder à cette page directement");
}
