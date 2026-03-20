<?php
session_start();
require_once "db.php";

if (!isset($_SESSION["user_id"])) {
    die("Nicht eingeloggt.");
}

$id = (int) ($_GET["id"] ?? 0);

$stmt = $pdo->prepare("
  DELETE FROM reservierungen 
  WHERE id = ? AND user_id = ?
  ");
$stmt->execute([$id, $_SESSION["user_id"]]);

header("Location: dashboard.php");
exit; 
?>