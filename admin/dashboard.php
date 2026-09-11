<?php
include_once '../config.php';

// 🛡️ Turvakontroll: ainult admin saab siseneda
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header('Location: ../login.php');
    exit;
}

// Autode lisamine (C - Create)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_car'])) {
    $brand = sanitize($_POST['brand']);
    $model = sanitize($_POST['model']);
    $price = (float)$_POST['price_per_day'];

    $stmt = $pdo->prepare("INSERT INTO cars (brand, model, price_per_day) VALUES (?, ?, ?)");
    $stmt->execute([$brand, $model, $price]);
    header("Location: dashboard.php");
    exit;
}

// Autode kustutamine (D - Delete)
if (isset($_GET['delete_car'])) {
    $id = (int)$_GET['delete_car'];
    $stmt = $pdo->prepare("DELETE FROM cars WHERE id = ?");
    $stmt->execute([$id]);
    header("Location: dashboard.php");
    exit;
}

// Andmete pärimine
$cars = $pdo->query("SELECT * FROM cars")->fetchAll();
$reservations = $pdo->query("
    SELECT r.*, c.brand, c.model, u.username 
    FROM reservations r 
    JOIN cars c ON r.car_id = c.id 
    JOIN users u ON r.user_id = u.id
    ORDER BY r.start_date DESC
")->fetchAll();
?>
<!DOCTYPE html>
<html lang="et">
<head>
    <meta charset="UTF-8">
    <link href="https://jsdelivr.net" rel="stylesheet">
    <title>Admin Paneel</title>
</head>
<body class="bg-light">
<div class="container my-5">
    <div class="d-flex justify-content-between align-items-center mb-5">
        <h1 class="fw-bold text-danger">🛡️ Administraatori paneel</h1>
        <a href="../index.php" class="btn btn-outline-dark">Tagasi avalehele</a>
    </div>

    <div class="row">
        <!-- Autode halduse vorm -->
        <div class="col-lg-4 mb-4">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-dark text-white fw-bold">Lisa autoparki sõiduk</div>
                <div class="card-body">
                    <form method="POST" action="">
                        <div class="mb-3">
                            <label class="form-label">Automark</label>
                            <input type="text" name="brand" class="form-control" placeholder="nt Audi" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Mudel</label>
                            <input type="text" name="model" class="form-control" placeholder="nt A6" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Päevahind (€)</label>
                            <input type="number" step="0.01" name="price_per_day" class="form-control" required>
                        </div>
                        <button type="submit" name="add_car" class="btn btn-primary w-100">Lisa auto</button>
                    </form>
                </div>
            </div>
        </div>

        <!-- Autode nimekiri -->
        <div class="col-lg-8 mb-4">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-dark text-white fw-bold">Autopargi nimekiri</div>
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr><th>Sõiduk</th><th>Päevahind</th><th>Tegevus</th></tr>
                        </thead>
                        <tbody>
                            <?php foreach ($cars as $car): ?>
                            <tr>
                                <td><strong><?= sanitize($car['brand']) ?></strong> <?= sanitize($car['model']) ?></td>
                                <td><?= sanitize($car['price_per_day']) ?> €</td>
                                <td>
                                    <a href="dashboard.php?delete_car=<?= $car['id'] ?>" class="btn btn-danger btn-sm text-white" onclick="return confirm('Kas oled kindel, et soovid auto kustutada?')">Kustuta</a>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Broneeringute haldamise tabel -->
    <div class="card shadow-sm border-0 mt-4">
        <div class="card-header bg-dark text-white fw-bold">Klientide broneeringud</div>
        <div class="table-responsive">
            <table class="table table-striped table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr><th>Kasutaja</th><th>Rendiauto</th><th>Rendiperiood</th><th>Koguhind</th><th>Staatus</th></tr>
                </thead>
                <tbody>
                    <?php foreach ($reservations as $res): ?>
                    <tr>
                        <td><?= sanitize($res['username']) ?></td>
                        <td><?= sanitize($res['brand']) . ' ' . sanitize($res['model']) ?></td>
                        <td><?= sanitize($res['start_date']) ?> kuni <?= sanitize($res['end_date']) ?></td>
                        <td class="text-primary fw-bold"><?= sanitize($res['total_price']) ?> €</td>
                        <td>
                            <!-- Omapoolne täiendus: Visuaalsed Bootstrapi staatusmärgendid -->
                            <span class="badge bg-success"><?= sanitize($res['status']) ?></span>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
</body>
</html>
