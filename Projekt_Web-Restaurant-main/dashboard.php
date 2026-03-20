<?php
session_start();
require_once "db.php";

if (!isset($_SESSION["user_id"])) {
    header("Location: index.php");
    exit;
}

$user_id = $_SESSION["user_id"];

try {
    $stmt = $pdo->prepare("SELECT name, email, profilbild FROM users WHERE id = ?");
    $stmt->execute([$user_id]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    $profilbild = $user["profilbild"] ?? "default.png";

    $stmt = $pdo->prepare("SELECT id, datum, uhrzeit, personen, status, bemerkung FROM reservierungen WHERE user_id = ? ORDER BY datum DESC");
    $stmt->execute([$user_id]);
    $reservierungen = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Datenbankfehler: " . $e->getMessage());
}
?>

<!doctype html>
<html lang="de">

<head>
    <meta charset="utf-8">
    <title>Mein Dashboard – Willy's Nudelhaus</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        :root {
            --brand: #8a2b2b;
        }

        .navbar-brand span {
            color: var(--brand);
        }

        .card {
            border: none;
            transition: transform 0.2s;
        }

        .card:hover {
            transform: translateY(-5px);
        }
    </style>
</head>

<body class="bg-light">

    <nav class="navbar navbar-expand-lg bg-white shadow-sm mb-4">
        <div class="container">
            <a class="navbar-brand fw-bold" href="index.php"><span>Willy's</span> Nudelhaus</a>
            <div class="ms-auto">
                <a href="index.php" class="btn btn-outline-dark btn-sm me-2">Startseite</a>
                <a href="logout.php" class="btn btn-danger btn-sm">Logout</a>
            </div>
        </div>
    </nav>

    <div class="container py-4">
        <h2 class="h3 mb-4 text-dark">👤 Mein Dashboard</h2>

        <?php if (isset($_GET['error']) || isset($_GET['pw']) || isset($_GET['success'])): ?>
            <div class="row">
                <div class="col-12">
                    <?php if (isset($_GET['error'])): ?>
                        <div class="alert alert-danger alert-dismissible fade show shadow-sm mb-4">
                            <?php
                            if ($_GET['error'] == 'old_email_wrong')
                                echo "Die alte E-Mail Adresse ist nicht korrekt.";
                            if ($_GET['error'] == 'invalid_email')
                                echo "Die neue E-Mail Adresse ist ungültig.";
                            ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    <?php endif; ?>

                    <?php if (isset($_GET['pw'])): ?>
                        <div
                            class="alert <?= $_GET['pw'] == 'success' ? 'alert-success' : 'alert-danger' ?> alert-dismissible fade show shadow-sm mb-4">
                            <?php
                            if ($_GET['pw'] == 'old_wrong')
                                echo "Das alte Passwort war falsch.";
                            if ($_GET['pw'] == 'mismatch')
                                echo "Die neuen Passwörter stimmen nicht überein.";
                            if ($_GET['pw'] == 'success')
                                echo "Passwort wurde erfolgreich geändert.";
                            ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    <?php endif; ?>

                    <?php if (isset($_GET['success']) && $_GET['success'] == 'email_updated'): ?>
                        <div class="alert alert-success alert-dismissible fade show shadow-sm mb-4">
                            E-Mail Adresse wurde erfolgreich geändert.
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        <?php endif; ?>

        <div class="row g-4 mb-5">
            <div class="col-md-4">
                <div class="card h-100 shadow-sm">
                    <div class="card-body text-center">
                        <img src="uploads/<?= htmlspecialchars($profilbild) ?>" class="rounded-circle mb-3" width="100"
                            height="100" style="object-fit:cover; border: 3px solid #eee;">
                        <h5 class="mb-1"><?= htmlspecialchars($user["name"]) ?></h5>
                        <p class="small text-muted mb-3"><?= htmlspecialchars($user["email"]) ?></p>
                        <form action="profile_image_upload.php" method="POST" enctype="multipart/form-data">
                            <input type="file" name="profilbild" class="form-control form-control-sm mb-2" required>
                            <button class="btn btn-outline-primary btn-sm w-100">Bild ändern</button>
                        </form>
                    </div>
                </div>
            </div>

            <div class="col-md-4">
                <div class="card h-100 shadow-sm">
                    <div class="card-body">
                        <h6 class="text-uppercase small fw-bold text-muted mb-3">E-Mail Adresse ändern</h6>
                        <form action="email_update.php" method="POST">
                            <input type="email" name="old_email" class="form-control form-control-sm mb-2"
                                placeholder="Alte E-Mail" required>
                            <input type="email" name="new_email" class="form-control form-control-sm mb-2"
                                placeholder="Neue E-Mail" required>
                            <button class="btn btn-primary btn-sm w-100 mt-2">Speichern</button>
                        </form>
                    </div>
                </div>
            </div>

            <div class="col-md-4">
                <div class="card h-100 shadow-sm">
                    <div class="card-body">
                        <h6 class="text-uppercase small fw-bold text-muted mb-3">Sicherheit</h6>
                        <form action="pw_update.php" method="POST">
                            <input type="password" name="old_pw" class="form-control form-control-sm mb-2"
                                placeholder="Altes Passwort" required>
                            <input type="password" name="new_pw" class="form-control form-control-sm mb-2"
                                placeholder="Neues Passwort" required>
                            <input type="password" name="new_pw2" class="form-control form-control-sm mb-2"
                                placeholder="Neues Passwort bestätigen" required>
                            <button class="btn btn-secondary btn-sm w-100 mt-2">Passwort ändern</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <h3 class="h5 mb-3">📅 Deine Reservierungen</h3>
        <div class="card shadow-sm">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Datum</th>
                            <th>Zeit</th>
                            <th>Gäste</th>
                            <th>Status</th>
                            <th>Bemerkung</th>
                            <th class="text-end">Aktion</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($reservierungen as $r): ?>
                            <tr>
                                <td><?= date("d.m.Y", strtotime($r["datum"])) ?></td>
                                <td><?= substr($r["uhrzeit"], 0, 5) ?> Uhr</td>
                                <td><?= $r["personen"] ?></td>
                                <td><span class="badge bg-secondary"><?= $r["status"] ?></span></td>
                                <td><?= !empty($r['bemerkung']) ? htmlspecialchars($r['bemerkung']) : '-' ?></td>
                                <td class="text-end">
                                    <a href="reservierung_delete.php?id=<?= $r["id"] ?>"
                                        class="btn btn-link text-danger p-0" onclick="return confirm('Stornieren?')"><i
                                            class="bi bi-trash3"></i></a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>