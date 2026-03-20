<?php
session_start();
require_once "db.php";

// Prüfen, ob das Formular per POST abgeschickt wurde
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    
    $email = trim($_POST["email"] ?? "");
    $passwort = $_POST["passwort"] ?? "";

    if (empty($email) || empty($passwort)) {
        header("Location: index.php?error=empty_fields");
        exit;
    }

    $stmt = $pdo->prepare("SELECT id, passwort, role, name FROM users WHERE email = ?");
    $stmt->execute([$email]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    // ob Passwort gleich ist
    if ($user && password_verify($passwort, $user["passwort"])) {
        $_SESSION["user_id"]   = $user["id"];
        $_SESSION["user_name"] = $user["name"];
        $_SESSION["role"]      = $user["role"];

        header("Location: index.php?login=success");
        exit;
    } else {
        header("Location: index.php?error=invalid_login");
        exit;
    }
} else {
    // Falls die Datei direkt aufgerufen wird (ohne Formular)
    header("Location: index.php");
    exit;
}