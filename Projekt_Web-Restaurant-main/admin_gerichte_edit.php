<?php
session_start();
require "db.php";

if (!isset($_SESSION["user_id"]) || $_SESSION["role"] !== "admin") {
    header("Location: index.php?error=no_admin");
    exit;
}

$id = $_GET["id"] ?? null;
if (!$id) {
    die("Keine ID angegeben.");
}

$stmt = $pdo->prepare("SELECT * FROM dishes WHERE id = ?");
$stmt->execute([$id]);
$dish = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$dish) {
    die("Gericht nicht gefunden.");
}

$catStmt = $pdo->query("SELECT * FROM menu_categories ORDER BY name");
$categories = $catStmt->fetchAll(PDO::FETCH_ASSOC);

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $title = $_POST["title"];
    $price = $_POST["price"];
    $description = $_POST["description"];
    $category_id = $_POST["category_id"];
    $allergens = $_POST["allergens"];

    // Altes Bild behalten, außer ein neues wird hochgeladen
    $imagePath = $dish["image_url"];
    if (!empty($_FILES["image"]["name"])) {
        $file = $_FILES["image"];
        $newName = time() . "." . pathinfo($file["name"], PATHINFO_EXTENSION);
        $uploadDir = "uploads/gerichte/";
        if (!is_dir($uploadDir))
            mkdir($uploadDir, 0777, true);
        move_uploaded_file($file["tmp_name"], $uploadDir . $newName);
        $imagePath = $uploadDir . $newName;
    }

    // Datenbank-Update
    $update = $pdo->prepare("
        UPDATE dishes 
        SET title = ?, price = ?, description = ?, category_id = ?, allergens = ?, image_url = ? 
        WHERE id = ?
    ");
    $update->execute([$title, $price, $description, $category_id, $allergens, $imagePath, $id]);

    header("Location: admin_gerichte.php?success=updated");
    exit;
}
?>

<!DOCTYPE html>
<html lang="de">

<head>
    <meta charset="UTF-8">
    <title>Gericht bearbeiten – Willy's Admin</title>
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
    </style>
</head>

<body class="bg-light">

    <nav class="navbar navbar-dark bg-dark shadow-sm mb-5">
        <div class="container">
            <a class="navbar-brand fw-bold" href="index.php"><span>Willy's</span> Nudelhaus</a>
            <a href="admin_gerichte.php" class="btn btn-outline-light btn-sm">Abbrechen</a>
        </div>
    </nav>

    <div class="container pb-5">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card shadow-sm border-0">
                    <div class="card-header bg-white py-3">
                        <h5 class="mb-0"><i class="bi bi-pencil-square me-2"></i> Gericht bearbeiten</h5>
                    </div>
                    <div class="card-body p-4">
                        <form method="POST" enctype="multipart/form-data">

                            <div class="mb-3">
                                <label class="form-label fw-bold">Name des Gerichts</label>
                                <input type="text" name="title" class="form-control"
                                    value="<?= htmlspecialchars($dish['title']) ?>" required>
                            </div>

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label fw-bold">Preis (€)</label>
                                    <input type="number" step="0.01" name="price" class="form-control"
                                        value="<?= $dish['price'] ?>" required>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label fw-bold">Kategorie</label>
                                    <select name="category_id" class="form-select" required>
                                        <?php foreach ($categories as $cat): ?>
                                            <option value="<?= $cat['id'] ?>" <?= $cat['id'] == $dish['category_id'] ? 'selected' : '' ?>>
                                                <?= htmlspecialchars($cat['name']) ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label class="form-label fw-bold">Beschreibung</label>
                                <textarea name="description" class="form-control"
                                    rows="3"><?= htmlspecialchars($dish['description']) ?></textarea>
                            </div>

                            <div class="mb-3">
                                <label class="form-label fw-bold">Allergene (optional)</label>
                                <input type="text" name="allergens" class="form-control"
                                    value="<?= htmlspecialchars($dish['allergens']) ?>" placeholder="z.B. A, C, G">
                            </div>

                            <div class="mb-4">
                                <label class="form-label fw-bold">Bild</label>
                                <div class="mb-2">
                                    <img src="<?= htmlspecialchars($dish['image_url']) ?>" class="rounded shadow-sm"
                                        style="width: 100px; height: 100px; object-fit: cover;">
                                    <small class="text-muted ms-2">Aktuelles Bild</small>
                                </div>
                                <input type="file" name="image" class="form-control" accept="image/*">
                                <div class="form-text">Nur ausfüllen, wenn du das Bild ändern möchtest.</div>
                            </div>

                            <hr>
                            <div class="d-flex justify-content-between">
                                <a href="admin_gerichte.php" class="btn btn-light">Zurück</a>
                                <button type="submit" class="btn btn-brand px-5">Speichern</button>
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