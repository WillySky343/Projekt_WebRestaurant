<?php
session_start();
require_once "db.php";

if (!isset($_SESSION["user_id"])) {
    header("Location: index.php?error=no_login");
    exit;
}

$user_id = $_SESSION["user_id"];
$old_pw = $_POST["old_pw"] ?? "";
$new_pw = $_POST["new_pw"] ?? "";
$new_pw2 = $_POST["new_pw2"] ?? "";

// Altes Pw
$stmt = $pdo->prepare("SELECT passwort FROM users WHERE id = ?");
$stmt->execute([$user_id]);
$row = $stmt->fetch(PDO::FETCH_ASSOC);

// Neues Pw 
if (!$row || !password_verify($old_pw, $row["passwort"])) {
    header("Location: dashboard.php?pw=old_wrong");
    exit;
}

if ($new_pw !== $new_pw2 || empty($new_pw)) {
    header("Location: dashboard.php?pw=mismatch");
    exit;
}

$hash = password_hash($new_pw, PASSWORD_DEFAULT);
$stmt = $pdo->prepare("UPDATE users SET passwort = ? WHERE id = ?");
$stmt->execute([$hash, $user_id]);

header("Location: dashboard.php?pw=success");
exit;
?>