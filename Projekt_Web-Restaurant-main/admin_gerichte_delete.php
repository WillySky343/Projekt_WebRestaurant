<?php
session_start();
require "db.php";

if (!isset($_SESSION["user_id"]) || $_SESSION["role"] !== "admin") {
    die("Kein Zugriff 🚫");
}

$id = $_GET["id"] ?? null;

if (!$id) {
    die("Keine ID angegeben.");
}

$stmt = $pdo->prepare("DELETE FROM dishes WHERE id = ?");
$stmt->execute([$id]);

header("Location: admin_gerichte.php");
exit;
?>