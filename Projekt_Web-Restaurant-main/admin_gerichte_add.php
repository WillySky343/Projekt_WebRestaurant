<?php
session_start();
require "db.php";

if (!isset($_SESSION["role"]) || $_SESSION["role"] !== "admin") {
    header("Location: index.php?error=no_admin");
    exit;
}

// Kategorien laden
$cats = $pdo->query("SELECT * FROM menu_categories")->fetchAll(PDO::FETCH_ASSOC);

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $title = $_POST["title"];
    $price = $_POST["price"];
    $desc = $_POST["description"];
    $all = $_POST["allergens"];
    $cat = $_POST["category_id"];

    $file = $_FILES["image"];
    $ext = pathinfo($file["name"], PATHINFO_EXTENSION);
    $newName = "dish_" . time() . "." . $ext;

    $uploadDir = "uploads/gerichte/";
    if (!is_dir($uploadDir))
        mkdir($uploadDir, 0777, true);

    move_uploaded_file($file["tmp_name"], $uploadDir . $newName);

    $stmt = $pdo->prepare("
        INSERT INTO dishes (title, price, description, allergens, image_url, category_id)
        VALUES (?, ?, ?, ?, ?, ?)
    ");
    $stmt->execute([$title, $price, $desc, $all, $uploadDir . $newName, $cat]);

    header("Location: admin_gerichte.php?success=added");
    exit;
}
?>

<!DOCTYPE html>
<html lang="de">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin – Gericht hinzufügen</title>
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

    <nav class="navbar navbar-expand-lg bg-white shadow-sm mb-5">
        <div class="container">
            <a class="navbar-brand fw-bold" href="index.php"><span>Willy's</span> Nudelhaus</a>
            <div class="ms-auto">
                <a href="admin_gerichte.php" class="btn btn-outline-dark btn-sm">Zurück zur Liste</a>
            </div>
        </div>
    </nav>

    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8 col-lg-6">

                <div class="card shadow-sm border-0">
                    <div class="card-header bg-dark text-white p-3">
                        <h5 class="mb-0"><i class="bi bi-plus-circle me-2"></i> Neues Gericht hinzufügen</h5>
                    </div>
                    <div class="card-body p-4">
                        <form method="POST" enctype="multipart/form-data">

                            <div class="mb-3">
                                <label class="form-label fw-bold small text-muted text-uppercase">Name des
                                    Gerichts</label>
                                <input name="title" class="form-control" placeholder="z.B. Spaghetti Carbonara"
                                    required>
                            </div>

                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label class="form-label fw-bold small text-muted text-uppercase">Preis (€)</label>
                                    <input name="price" type="number" step="0.01" class="form-control"
                                        placeholder="0.00" required>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-bold small text-muted text-uppercase">Kategorie</label>
                                    <select name="category_id" class="form-select" required>
                                        <option value="" disabled selected>Wählen...</option>
                                        <?php foreach ($cats as $c): ?>
                                            <option value="<?= $c["id"] ?>"><?= htmlspecialchars($c["name"]) ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label class="form-label fw-bold small text-muted text-uppercase">Beschreibung</label>
                                <textarea name="description" class="form-control" rows="3"
                                    placeholder="Zutaten, Zubereitung..."></textarea>
                            </div>

                            <div class="mb-3">
                                <label class="form-label fw-bold small text-muted text-uppercase">Allergene</label>
                                <input name="allergens" class="form-control" placeholder="z.B. A, C, G">
                            </div>

                            <div class="mb-4">
                                <label class="form-label fw-bold small text-muted text-uppercase">Gericht-Foto</label>
                                <input type="file" name="image" class="form-control" accept="image/*" required>
                            </div>

                            <div class="d-grid">
                                <button type="submit" class="btn btn-brand py-2 fw-bold text-uppercase">Gericht
                                    Speichern</button>
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