<?php
session_start();
require_once "db.php";

// Alle Kategorien laden
$catStmt = $pdo->query("SELECT * FROM menu_categories");
$categories = $catStmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="de">

<head>
    <meta charset="UTF-8">
    <title>Speisekarte – Willy's Nudelhaus</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        :root {
            --brand: #8a2b2b;
        }

        .navbar-brand span {
            color: var(--brand);
        }

        .menu-card img {
            aspect-ratio: 4/3;
            object-fit: cover;
        }

        body {
            display: flex;
            flex-direction: column;
            min-height: 100vh;
        }

        .page-content {
            flex: 1;
        }
    </style>
</head>

<body>

    <nav class="navbar navbar-expand-lg bg-body-tertiary sticky-top shadow-sm">
        <div class="container">
            <a class="navbar-brand fw-bold" href="index.php"><span>Willy's</span> Nudelhaus</a>
        </div>
    </nav>

    <div class="page-content container py-5">
        <h1 class="mb-4 text-center">Unsere Speisekarte</h1>

        <div class="accordion" id="menu">
            <?php foreach ($categories as $index => $cat): ?>
                <div class="accordion-item">
                    <h2 class="accordion-header">
                        <button class="accordion-button <?= $index !== 0 ? 'collapsed' : '' ?>" data-bs-toggle="collapse"
                            data-bs-target="#cat-<?= $cat['id'] ?>">
                            <?= htmlspecialchars($cat['name']) ?>
                        </button>
                    </h2>
                    <div id="cat-<?= $cat['id'] ?>" class="accordion-collapse collapse <?= $index === 0 ? 'show' : '' ?>">
                        <div class="accordion-body">
                            <div class="row">
                                <?php
                                // Gerichte für diese Kategorie laden
                                $dishStmt = $pdo->prepare("SELECT * FROM dishes WHERE category_id = ?");
                                $dishStmt->execute([$cat['id']]);
                                $dishes = $dishStmt->fetchAll(PDO::FETCH_ASSOC);

                                foreach ($dishes as $dish): ?>
                                    <div class="col-md-6 mb-4">
                                        <div class="card h-100 menu-card">
                                            <img src="<?= htmlspecialchars($dish['image_url']) ?>" class="card-img-top"
                                                alt="<?= htmlspecialchars($dish['title']) ?>">
                                            <div class="card-body">
                                                <h5 class="card-title">
                                                    <?= htmlspecialchars($dish['title']) ?> –
                                                    <?= number_format($dish['price'], 2, ',', '.') ?>€
                                                </h5>
                                                <p class="card-text">
                                                    <?= htmlspecialchars($dish['description']) ?>
                                                </p>
                                                <p class="text-muted small"><strong>Allergene:</strong>
                                                    <?= htmlspecialchars($dish['allergens'] ?: 'Keine') ?>
                                                </p>
                                            </div>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
        <div class="mt-4 text-muted" style="font-size: 0.65rem; line-height: 1.1;">
            <strong>Allergen-Legende:</strong><br>
            A = Glutenhaltiges Getreide &nbsp;|&nbsp;
            B = Krebstiere &nbsp;|&nbsp;
            C = Ei &nbsp;|&nbsp;
            D = Fisch &nbsp;|&nbsp;
            E = Erdnüsse &nbsp;|&nbsp;
            F = Soja &nbsp;|&nbsp;
            G = Milch/Laktose &nbsp;|&nbsp;
            H = Schalenfrüchte (Nüsse) &nbsp;|&nbsp;
            L = Sellerie &nbsp;|&nbsp;
            M = Senf &nbsp;|&nbsp;
            N = Sesam &nbsp;|&nbsp;
            O = Sulfite &nbsp;|&nbsp;
            P = Lupinen &nbsp;|&nbsp;
            R = Weichtiere
        </div>
    </div>

    <footer class="bg-dark text-white text-center p-3 mt-4">
        © 2025 Willy's Nudelhaus
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>