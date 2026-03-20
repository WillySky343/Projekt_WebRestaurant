<?php
session_start();
require "db.php";

// Nur admin
if (!isset($_SESSION["user_id"]) || $_SESSION["role"] !== "admin") {
    header("Location: index.php?error=no_admin");
    exit;
}

// Alle Benutzer laden (außer sich selbst)
$stmt = $pdo->prepare("SELECT id, name, email, role, created_at FROM users WHERE id != ? ORDER BY created_at DESC");
$stmt->execute([$_SESSION["user_id"]]);
$users = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="de">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin – Benutzerverwaltung | Willy's Nudelhaus</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        :root {
            --brand: #8a2b2b;
        }

        .navbar-brand span {
            color: var(--brand);
        }

        .table thead {
            background-color: #212529;
            color: white;
        }

        .badge-admin {
            background-color: var(--brand);
        }
    </style>
</head>

<body class="bg-light">

    <nav class="navbar navbar-expand-lg bg-white shadow-sm mb-4">
        <div class="container">
            <a class="navbar-brand fw-bold" href="index.php"><span>Willy's</span> Nudelhaus</a>
            <div class="ms-auto">
                <a href="index.php" class="btn btn-outline-dark btn-sm me-2">
                    <i class="bi bi-house"></i> Startseite
                </a>
                <a href="logout.php" class="btn btn-danger btn-sm">
                    <i class="bi bi-box-arrow-right"></i> Logout
                </a>
            </div>
        </div>
    </nav>

    <div class="container py-2">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2 class="h3 mb-0">👥 Benutzerverwaltung</h2>
            <span class="badge bg-dark"><?= count($users) ?> Registrierte Kunden</span>
        </div>

        <div class="card shadow-sm border-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead>
                        <tr>
                            <th class="ps-3">Name / E-Mail</th>
                            <th>Rolle</th>
                            <th>Registriert am</th>
                            <th class="text-end pe-3">Aktionen</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($users)): ?>
                            <tr>
                                <td colspan="4" class="text-center py-5 text-muted">Keine weiteren Benutzer gefunden.</td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($users as $u): ?>
                                <tr>
                                    <td class="ps-3">
                                        <div class="fw-bold"><?= htmlspecialchars($u["name"]) ?></div>
                                        <div class="small text-muted"><?= htmlspecialchars($u["email"]) ?></div>
                                    </td>
                                    <td>
                                        <?php if ($u["role"] === 'admin'): ?>
                                            <span class="badge badge-admin">Admin</span>
                                        <?php else: ?>
                                            <span class="badge bg-primary">Kunde</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <i class="bi bi-calendar-check me-1 text-muted"></i>
                                        <?= date("d.m.Y", strtotime($u["created_at"])) ?>
                                        <div class="small text-muted"><?= date("H:i", strtotime($u["created_at"])) ?> Uhr</div>
                                    </td>
                                    <td class="text-end pe-3">
                                        <a href="admin_users_delete.php?id=<?= $u["id"] ?>"
                                            class="btn btn-outline-danger btn-sm"
                                            onclick="return confirm('Möchtest du diesen Benutzer und alle seine Reservierungen wirklich löschen?')">
                                            <i class="bi bi-trash me-1"></i> Löschen
                                        </a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <div class="mt-4 small text-muted">
            <i class="bi bi-info-circle me-1"></i> Ihr eigenes Admin-Konto wird hier zum Schutz vor versehentlichem
            Löschen nicht angezeigt.
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>