<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

$host = "127.0.0.1";
$db   = "Kunde_Restaurant";
$user = "root";
$pass = "root";   // MAMP Standard
$port = 8889;     // benutz das 

try {
    $pdo = new PDO(
        "mysql:host=$host;port=$port;dbname=$db;charset=utf8mb4",
        $user,
        $pass
    );
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION); //Nicht silent sondern exception
} catch (PDOException $e) {
    die("DB-Verbindung fehlgeschlagen 😭: " . $e->getMessage());
}