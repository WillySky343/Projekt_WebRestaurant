<?php
session_start();
require "db.php";

if (!isset($_SESSION["user_id"]) || $_SESSION["role"] !== "admin") {
    header("Location: index.php?error=no_admin");
    exit;
}

// Alle Reservierungen mit Kundennamen abrufen
$stmt = $pdo->query("
    SELECT r.*, u.name, u.email
    FROM reservierungen r
    JOIN users u ON r.user_id = u.id
    ORDER BY r.datum DESC, r.uhrzeit DESC
");

$reservierungen = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="de">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin – Reservierungen | Willy's Nudelhaus</title>
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

        .badge-status {
            min-width: 80px;
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

    <div class="container">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2 class="h3 mb-0">📋 Alle Reservierungen</h2>
            <span class="badge bg-dark"><?= count($reservierungen) ?> Buchungen gesamt</span>
        </div>

        <div class="card shadow-sm border-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead>
                        <tr>
                            <th class="ps-3">Kunde</th>
                            <th>Datum & Uhrzeit</th>
                            <th>Personen</th>
                            <th>Status</th>
                            <th class="text-end pe-3">Aktionen</th>
                        </tr>
                    </thead>
                    <tbody>

                        <?php if (empty($reservierungen)): ?>
                            <tr>
                                <td colspan="5" class="text-center py-5 text-muted">
                                    Es liegen aktuell keine Reservierungen vor.
                                </td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($reservierungen as $r): ?>
                                <tr>
                                    <td class="ps-3">
                                        <div class="fw-bold"><?= htmlspecialchars($r["name"]) ?></div>
                                        <div class="small text-muted"><?= htmlspecialchars($r["email"]) ?></div>
                                    </td>
                                    <td>
                                        <div><i class="bi bi-calendar3 me-1"></i> <?= date("d.m.Y", strtotime($r["datum"])) ?>
                                        </div>
                                        <div class="small text-muted"><i class="bi bi-clock me-1"></i>
                                            <?= substr($r["uhrzeit"], 0, 5) ?> Uhr</div>
                                    </td>
                                    <td>
                                        <i class="bi bi-people me-1"></i> <?= $r["personen"] ?>
                                    </td>
                                    <td>
                                        <?php
                                        $statusClass = 'bg-secondary';
                                        if ($r["status"] == 'bestätigt')
                                            $statusClass = 'bg-success';
                                        if ($r["status"] == 'storniert')
                                            $statusClass = 'bg-danger';
                                        ?>
                                        <span class="badge <?= $statusClass ?> badge-status">
                                            <?= htmlspecialchars($r["status"] ?? 'offen') ?>
                                        </span>
                                    </td>
                                    <td class="text-end pe-3">
                                        <div class="btn-group">
                                            <a href="admin_res_edit.php?id=<?= $r["id"] ?>"
                                                class="btn btn-outline-primary btn-sm" title="Bearbeiten">
                                                <i class="bi bi-pencil"></i>
                                            </a>
                                            <a href="admin_res_delete.php?id=<?= $r["id"] ?>"
                                                class="btn btn-outline-danger btn-sm"
                                                onclick="return confirm('Möchten Sie diese Reservierung wirklich löschen?')"
                                                title="Löschen">
                                                <i class="bi bi-trash"></i>
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                                <?php if (!empty($r["bemerkung"])): ?>
                                    <tr class="table-light">
                                        <td colspan="5" class="small px-3 py-1">
                                            <span class="text-muted small text-uppercase fw-bold">Bemerkung:</span>
                                            <span class="fst-italic text-secondary"><?= htmlspecialchars($r["bemerkung"]) ?></span>
                                        </td>
                                    </tr>
                                <?php endif; ?>
                            <?php endforeach; ?>
                        <?php endif; ?>

                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>