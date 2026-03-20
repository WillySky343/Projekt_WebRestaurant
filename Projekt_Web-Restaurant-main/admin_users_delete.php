<?php
session_start();
require "db.php";

// nur admins
if (!isset($_SESSION["user_id"]) || $_SESSION["role"] !== "admin") {
    die("Kein Zugriff 🚫");
}

$id = $_GET["id"] ?? null;

if ($id) {
    // Erst Reservierung dann user löschen
    $stmt1 = $pdo->prepare("DELETE FROM reservierungen WHERE user_id = ?");
    $stmt1->execute([$id]);

    $stmt2 = $pdo->prepare("DELETE FROM users WHERE id = ?");
    $stmt2->execute([$id]);
}

header("Location: admin_users.php?status=deleted");
exit;
?>