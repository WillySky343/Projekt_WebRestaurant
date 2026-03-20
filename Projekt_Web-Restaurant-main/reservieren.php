<?php
session_start();
require_once "db.php";

// Nutzer muss eingeloggt sein
if (!isset($_SESSION["user_id"])) {
    header("Location: index.php?error=no_login");
    exit; 
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $datum     = $_POST["datum"] ?? "";
    $uhrzeit   = $_POST["uhrzeit"] ?? "";
    $personen  = (int)($_POST["personen"] ?? 0);
    $bemerkung = trim($_POST["bemerkung"] ?? "");

    if ($datum === "" || $uhrzeit === "" || $personen < 1) {
        die("Bitte alle Pflichtfelder ausfüllen.");
    }

    // Öffnungszeiten
    $minZeit = "11:30";
    $maxZeit = "23:30";

    if ($uhrzeit < $minZeit || $uhrzeit > $maxZeit) {
        header("Location: index.php?error=zeit");
        exit;
    }

    // Personenlimit
    if ($personen < 1 || $personen > 11) {
        header("Location: index.php?error=personen");
        exit;
    }

    // --- In DB speichern (PDO) ---
    $stmt = $pdo->prepare("
        INSERT INTO reservierungen (user_id, datum, uhrzeit, personen, bemerkung)
        VALUES (:user_id, :datum, :uhrzeit, :personen, :bemerkung)
    ");

    $stmt->execute([
        "user_id"  => $_SESSION["user_id"],
        "datum"    => $datum,
        "uhrzeit"  => $uhrzeit,
        "personen" => $personen,
        "bemerkung"=> $bemerkung
    ]);

    header("Location: index.php#reservierungen");
    exit;
}
?>

