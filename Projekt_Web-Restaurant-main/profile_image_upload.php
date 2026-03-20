<?php
session_start();
require "db.php";

if (!isset($_SESSION["user_id"])) {
    die("Nicht eingeloggt.");
}

if (!isset($_FILES["profilbild"])) {
    die("Keine Datei hochgeladen.");
}

$file = $_FILES["profilbild"];
$allowed = ["image/jpeg", "image/png", "image/jpg"];

if (!in_array($file["type"], $allowed)) {
    die("Nur JPG & PNG erlaubt.");
}

$ext = pathinfo($file["name"], PATHINFO_EXTENSION);
$newName = "user_" . $_SESSION["user_id"] . "." . $ext;
$target = "uploads/" . $newName;

move_uploaded_file($file["tmp_name"], $target);

$stmt = $pdo->prepare("UPDATE users SET profilbild = :img WHERE id = :id");
$stmt->execute([
    "img" => $newName,
    "id"  => $_SESSION["user_id"]
]);

header("Location: dashboard.php");
exit;
