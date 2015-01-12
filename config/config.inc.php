<?php

/*
 * Fichier de configuration
 */

/*
   Chargement de la configuration depuis XML
   Utilisation de la bibliothèque SimpleXML, plus légère que DOM et qui n'est pas pertinante pour l'usage ici
*/
$nomFichier = dirname(__FILE__) . "/configuration.xml";
$xml = simplexml_load_file($nomFichier) or header('Location: assistant/assistant.php');

// On cast toutes les variables car les données sont de types SimpleXMLElement, or lors du login, on met un ===,
// ce qui pose problème lors de la vérification du typage

// Connexion au serveur MySQL
$serveurSQL = $xml->serveurSQL;
$server = (string) $serveurSQL->server;
$db = (string) $serveurSQL->database;
$login = (string) $serveurSQL->login;
$mdp = (string) $serveurSQL->password;

// Configuration du site
$site = $xml->site;
$siteurl = (string) $site->url;
$username = (string) $site->username;
$password = (string) $site->password;

$parametresUsager = array(
    'Civilité' => 'civilite',
    'Nom' => 'nom',
    'Prénom' => 'prenom',
    'Adresse' => 'adresse',
    'Date de naissance' => 'dateNaissance',
    'Lieu de naissance' => 'lieuNaissance',
    'Numéro de sécurité sociale' => 'numSecu'
);

$parametresMedecin = array(
    'Civilité' => 'civilite',
    'Nom' => 'nom',
    'Prénom' => 'prenom'
);

$parametresConsultation = array(
    'Nom du patient' => 'usager',
    'Médecin traitant' => 'medecin',
    'Date de consultation' => 'dateConsult',
    'Heure de consultation' => 'heureConsult',
    'Duree de consultation (en min)' => 'dureeConsult'
);
