<?php
$host     = '192.168.135.113';
$port     = '3306';
$dbname   = 'oriola';
$username = 'user';
$password = 'rQUSxP2xUCxnzU45';

try {
    $db = new PDO("mysql:host=$host;port=$port;dbname=$dbname;charset=utf8mb4", $username, $password);
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Erreur de connexion : " . $e->getMessage());
}
?>