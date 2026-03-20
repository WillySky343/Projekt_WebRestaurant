<?php
session_start();
require_once "db.php";

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $name     = trim($_POST["name"] ?? "");
    $email    = trim($_POST["email"] ?? "");
    $passwort = $_POST["passwort"] ?? "";

    // Pflichtfeld
    if ($name === "" || $email === "" || $passwort === "") {
        die("Bitte alle Felder ausfüllen.");
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        die("Ungültige E-Mail.");
    }

    $check = $pdo->prepare("SELECT id FROM users WHERE email = :email");
    $check->execute(["email" => $email]);

    if ($check->fetch()) {
        die("Diese E-Mail ist bereits registriert.");
    }

    // Passwort hashen
    $hash = password_hash($passwort, PASSWORD_DEFAULT);

    $stmt = $pdo->prepare("
        INSERT INTO users (name, email, passwort)
        VALUES (:name, :email, :passwort)
    ");

    $stmt->execute([
        "name"     => $name,
        "email"    => $email,
        "passwort" => $hash
    ]);

    // automatisch einloggen
    $_SESSION["user_id"]   = $pdo->lastInsertId();
    $_SESSION["user_name"] = $name;

    header("Location: index.php");
    exit;
}
?>

