<?php
// Configuration des identifiants locaux de XAMPP
$host = 'localhost';
$dbname = 'ellusion_db'; // Le nom de la base de données que tu as créée ou vas créer
$username = 'root';     // Identifiant par défaut sur XAMPP
$password = '';         // Mot de passe par défaut (vide) sur XAMPP

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