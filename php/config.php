<?php
// Configuration OVH
$host = 'ijtebowcompte12.mysql.db';
$dbname = 'ijtebowcompte12';
$username = 'ijtebowcompte12';
$password = 'xg8CvR452026';

try {
    // Connexion sécurisée via PDO (on utilise $db comme dans ton fichier reservation.php)
    $db = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password);
    
    // Activation des alertes en cas d'erreur SQL pour t'aider à débugger
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
} catch (PDOException $e) {
    // Si la connexion échoue, on arrête le script et on affiche l'erreur
    die("Erreur de connexion à la base de données : " . $e->getMessage());
}
?>