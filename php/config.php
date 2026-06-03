<?php
$host     = 'ijtebowcompte12.mysql.db';
$dbname   = 'ijtebowcompte12';
$username = 'ijtebowcompte12';
$password = 'xg8CvR452026';

try {
    $db = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password);
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Erreur de connexion : " . $e->getMessage());
}
?>
