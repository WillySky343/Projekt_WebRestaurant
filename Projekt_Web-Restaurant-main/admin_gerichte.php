<?php
session_start();
require_once "db.php";

if (!isset($_SESSION["user_id"]) || $_SESSION["role"] !== "admin") {
    header("Location: index.php?error=no_admin");
    exit;
}

$stmt = $pdo->query("
    SELECT dishes.*, menu_categories.name AS cat_name 
    FROM dishes 
    LEFT JOIN menu_categories ON dishes.category_id = menu_categories.id 
    ORDER BY dishes.category_id, dishes.title
");
$dishes = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="de">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Speisekarte verwalten – Willy's Admin</title>
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

        .table thead {
            background-color: #212529;
            color: white;
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
            <h2 class="h3 mb-0"><i class="bi bi-egg-fried text-muted me-2"></i> Speisekarte verwalten</h2>
            <a href="admin_gerichte_add.php" class="btn btn-brand">
                <i class="bi bi-plus-lg"></i> Neues Gericht
            </a>
        </div>

        <?php if (isset($_GET['success'])): ?>
            <div class="alert alert-success alert-dismissible fade show shadow-sm mb-4" role="alert">
                <i class="bi bi-check-circle-fill me-2"></i>
                Aktion erfolgreich ausgeführt!
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>

        <div class="card shadow-sm border-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead>
                        <tr>
                            <th class="ps-3">Bild</th>
                            <th>Gericht</th>
                            <th>Kategorie</th>
                            <th>Preis</th>
                            <th class="text-end pe-3">Aktionen</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($dishes)): ?>
                            <tr>
                                <td colspan="5" class="text-center py-5 text-muted">Noch keine Gerichte in der Speisekarte.
                                </td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($dishes as $dish): ?>
                                <tr>
                                    <td class="ps-3">
                                        <img src="<?= htmlspecialchars($dish['image_url']) ?>" class="rounded shadow-sm"
                                            style="width: 55px; height: 55px; object-fit: cover;">
                                    </td>
                                    <td>
                                        <div class="fw-bold"><?= htmlspecialchars($dish['title']) ?></div>
                                        <div class="small text-muted"
                                            style="max-width: 250px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">
                                            <?= htmlspecialchars($dish['description']) ?>
                                        </div>
                                    </td>
                                    <td>
                                        <span
                                            class="badge bg-light text-dark border"><?= htmlspecialchars($dish['cat_name'] ?? 'Keine') ?></span>
                                    </td>
                                    <td class="fw-bold"><?= number_format($dish['price'], 2, ',', '.') ?> €</td>
                                    <td class="text-end pe-3">
                                        <div class="btn-group">
                                            <a href="admin_gerichte_edit.php?id=<?= $dish['id'] ?>"
                                                class="btn btn-outline-primary btn-sm" title="Bearbeiten">
                                                <i class="bi bi-pencil"></i>
                                            </a>
                                            <a href="admin_gerichte_delete.php?id=<?= $dish['id'] ?>"
                                                class="btn btn-outline-danger btn-sm"
                                                onclick="return confirm('Möchtest du dieses Gericht wirklich löschen?')"
                                                title="Löschen">
                                                <i class="bi bi-trash"></i>
                                            </a>
                                        </div>
                                    </td>
                                </tr>
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