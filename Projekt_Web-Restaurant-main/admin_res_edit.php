<?php
session_start();
require "db.php";

//  Nur Admins
if (!isset($_SESSION["user_id"]) || $_SESSION["role"] !== "admin") {
  header("Location: index.php?error=no_admin");
  exit;
}

$id = $_GET["id"] ?? null;
if (!$id)
  die("Keine Reservierungs-ID angegeben.");

$stmt = $pdo->prepare("
    SELECT r.*, u.name as user_name 
    FROM reservierungen r 
    JOIN users u ON r.user_id = u.id 
    WHERE r.id = ?
");
$stmt->execute([$id]);
$res = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$res)
  die("Reservierung nicht gefunden.");

// 4. Speichern-Logik
if ($_SERVER["REQUEST_METHOD"] === "POST") {
  $datum = $_POST["datum"];
  $uhrzeit = $_POST["uhrzeit"];
  $personen = $_POST["personen"];
  $status = $_POST["status"];
  $bemerkung = $_POST["bemerkung"];

  $update = $pdo->prepare("
        UPDATE reservierungen 
        SET datum = ?, uhrzeit = ?, personen = ?, status = ?, bemerkung = ? 
        WHERE id = ?
    ");
  $update->execute([$datum, $uhrzeit, $personen, $status, $bemerkung, $id]);

  header("Location: admin_reservierungen.php?success=updated");
  exit;
}
?>

<!DOCTYPE html>
<html lang="de">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Reservierung bearbeiten – Willy's Admin</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
  <style>
    :root {
      --brand: #8a2b2b;
    }

    .navbar-brand span {
      color: var(--brand);
    }

    .btn-brand {
      background-color: var(--brand);
      color: white;
    }

    .btn-brand:hover {
      background-color: #6d2222;
      color: white;
    }

    .card-header-admin {
      background-color: #212529;
      color: white;
    }
  </style>
</head>

<body class="bg-light">

  <nav class="navbar navbar-expand-lg bg-body-tertiary sticky-top shadow-sm mb-5">
    <div class="container">
      <a class="navbar-brand fw-bold" href="index.php"><span>Willy's</span> Nudelhaus</a>
      <div class="ms-auto">
        <a href="admin_reservierungen.php" class="btn btn-outline-dark btn-sm">Abbrechen</a>
      </div>
    </div>
  </nav>

  <div class="container">
    <div class="row justify-content-center">
      <div class="col-md-6">
        <div class="card shadow-sm border-0">
          <div class="card-header card-header-admin p-3">
            <h5 class="mb-0"><i class="bi bi-calendar-event me-2"></i> Reservierung bearbeiten</h5>
            <small>Kunde: <?= htmlspecialchars($res['user_name']) ?></small>
          </div>
          <div class="card-body p-4">
            <form method="POST">

              <div class="mb-3">
                <label class="form-label fw-bold small text-muted text-uppercase">Datum</label>
                <input type="date" name="datum" class="form-control" value="<?= $res['datum'] ?>" required>
              </div>

              <div class="row mb-3">
                <div class="col-md-6">
                  <label class="form-label fw-bold small text-muted text-uppercase">Uhrzeit</label>
                  <input type="time" name="uhrzeit" class="form-control" value="<?= substr($res['uhrzeit'], 0, 5) ?>"
                    required>
                </div>
                <div class="col-md-6">
                  <label class="form-label fw-bold small text-muted text-uppercase">Personen</label>
                  <input type="number" name="personen" class="form-control" value="<?= $res['personen'] ?>" min="1"
                    max="11" required>
                </div>
              </div>

              <div class="mb-3">
                <label class="form-label fw-bold small text-muted text-uppercase">Status</label>
                <select name="status" class="form-select">
                  <option value="offen" <?= $res['status'] == 'offen' ? 'selected' : '' ?>>Offen</option>
                  <option value="bestätigt" <?= $res['status'] == 'bestätigt' ? 'selected' : '' ?>>Bestätigt</option>
                  <option value="storniert" <?= $res['status'] == 'storniert' ? 'selected' : '' ?>>Storniert</option>
                </select>
              </div>

              <div class="mb-4">
                <label class="form-label fw-bold small text-muted text-uppercase">Bemerkung</label>
                <textarea name="bemerkung" class="form-control"
                  rows="3"><?= htmlspecialchars($res['bemerkung']) ?></textarea>
              </div>

              <div class="d-grid">
                <button type="submit" class="btn btn-brand py-2 fw-bold text-uppercase">Änderungen speichern</button>
              </div>
            </form>
          </div>
        </div>
      </div>
    </div>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>