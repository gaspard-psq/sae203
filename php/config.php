<?php
// Détection automatique de l'environnement selon le domaine
$host_actuel = strtolower($_SERVER['HTTP_HOST'] ?? $_SERVER['SERVER_NAME'] ?? '');

if ($host_actuel === 'localhost' || $host_actuel === '127.0.0.1') {

    // local (XAMPP)
    $host     = 'localhost';
    $dbname   = 'sae203';
    $username = 'root';
    $password = '';

} elseif (strpos($host_actuel, 'mmi-agences.univ-smb.fr') !== false) {

    // MMI Agence 
    $host     = '192.168.135.113';
    $dbname   = 'pasquieg';
    $username = 'user';
    $password = 'rQUSxP2xUCxnzU45';

} else {

    // OVH (production par défaut) 
    $host     = 'ijtebowcompte12.mysql.db';
    $dbname   = 'ijtebowcompte12';
    $username = 'ijtebowcompte12';
    $password = 'xg8CvR452026';

}

try {
    $db = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password);
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Erreur de connexion : " . $e->getMessage());
}
