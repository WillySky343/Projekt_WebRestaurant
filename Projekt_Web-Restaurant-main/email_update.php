<?php
session_start();
require_once "db.php";

if (!isset($_SESSION["user_id"])) {
    header("Location: index.php?error=no_login");
    exit;
}

$user_id = $_SESSION["user_id"];
$old_email_input = trim($_POST["old_email"] ?? "");
$new_email = trim($_POST["new_email"] ?? "");

// Aktuelle E-Mail
$stmt = $pdo->prepare("SELECT email FROM users WHERE id = ?");
$stmt->execute([$user_id]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$user || $old_email_input !== $user["email"]) {
    header("Location: dashboard.php?error=old_email_wrong");
    exit;
}

// Validierung der neuen E-Mail-Adresse
if (!filter_var($new_email, FILTER_VALIDATE_EMAIL)) {
    header("Location: dashboard.php?error=invalid_email");
    exit;
}

// Update ausführen
$stmt = $pdo->prepare("UPDATE users SET email = ? WHERE id = ?");
$stmt->execute([$new_email, $user_id]);

header("Location: dashboard.php?success=email_updated");
exit;
?>