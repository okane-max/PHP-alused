<?php
include('../config.php');
session_start();

// --- ROLLIKONTROLL (RBAC VAHAWARA) ---
// Admini alale pääseb ainult administraator
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header('Location: ../index.php');
    exit();
}

$error_message = "";
$success_message = "";

// --- 1. AUTO KUSTUTAMINE (DELETE) ---
if (isset($_GET['action']) && $_GET['action'] === 'delete' && isset($_GET['id'])) {
    $delete_id = intval($_GET['id']);
    
    $delete_query = "DELETE FROM cars WHERE id = ?";
    $stmt = mysqli_prepare($yhendus, $delete_query);
    mysqli_stmt_bind_param($stmt, "i", $delete_id);
    
    if (mysqli_stmt_execute($stmt)) {
        $success_message = "Auto on edukalt süsteemist kustutatud!";
    } else {
        $error_message = "Kustutamisel tekkis viga! Auto võib olla seotud aktiivse broneeringuga.";
    }
}

// --- 2. UUE AUTO LISAMINE (CREATE) ---
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['add_car'])) {
    $mark = trim($_POST['mark']);
    $model = trim($_POST['model']);
    $engine = trim($_POST['engine']);
    $fuel = trim($_POST['fuel']);
    $price = floatval($_POST['price']);

    if (!empty($mark) && !empty($model) && !empty($engine) && !empty($fuel) && $price > 0) {
        $insert_query = "INSERT INTO cars (mark, model, engine, fuel, price) VALUES (?, ?, ?, ?, ?)";
        $stmt = mysqli_prepare($yhendus, $insert_query);
        mysqli_stmt_bind_param($stmt, "ssssd", $mark, $model, $engine, $fuel, $price);
        
        if (mysqli_stmt_execute($stmt)) {
            $success_message = "Uus auto on edukalt lisatud!";
        } else {
            $error_message = "Auto lisamisel tekkis viga.";
        }
    } else {
        $error_message = "Kõik väljad peavad olema korrektselt täidetud!";
    }
}

// --- 3. AUTO MUUTMISE TÖÖTLUS (UPDATE) ---
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['edit_car'])) {
    $edit_id = intval($_POST['id']);
    $mark = trim($_POST['mark']);
    $model = trim($_POST['model']);
    $engine = trim($_POST['engine']);
    $fuel = trim($_POST['fuel']);
    $price = floatval($_POST['price']);

    if (!empty($mark) && !empty($model) && !empty($engine) && !empty($fuel) && $price > 0) {
        $update_query = "UPDATE cars SET mark = ?, model = ?, engine = ?, fuel = ?, price = ? WHERE id = ?";
        $stmt = mysqli_prepare($yhendus, $update_query);
        mysqli_stmt_bind_param($stmt, "ssssdi", $mark, $model, $engine, $fuel, $price, $edit_id);
        
        if (mysqli_stmt_execute($stmt)) {
            $success_message = "Auto andmed on edukalt uuendatud!";
        } else {
            $error_message = "Andmete uuendamisel tekkis viga.";
        }
    } else {
        $error_message = "Muutmisel peavad kõik väljad olema korrektselt täidetud!";
    }
}

// Autode pärimine tabeli kuvamiseks
$cars_query = "SELECT * FROM cars ORDER BY id DESC";
$cars_result = mysqli_query($yhendus, $cars_query);
?>
<!DOCTYPE html>
<html lang="et">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admini Paneel - Autode Haldus</title>
    <link href="https://jsdelivr.net" rel="stylesheet">
</head>
<body class="bg-light">

<nav class="navbar navbar-expand-lg navbar-dark bg-dark mb-4">
    <div class="container">
        <a class="navbar-brand" href="#">⚙️ AutoRent Admin</a>
        <div class="navbar-nav ms-auto">
            <a class="nav-link active" href="index.php">Autode haldus</a>
            <a class="nav-link" href="reservations.php">Broneeringute haldus</a>
            <a class="btn btn-outline-light btn-sm ms-3" href="../index.php">Tagasi avalehele</a>
        </div>
    </div>
</nav>

<div class="container">
    <div class="row mb-4">
        <div class="col d-flex justify-content-between align-items-center">
            <h2>Autode autopark</h2>
            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addCarModal">+ Lisa uus auto</button>
        </div>
    </div>

    <!-- Teavitused -->
    <?php if (!empty($success_message)): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <?php echo $success_message; ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>
    <?php if (!empty($error_message)): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <?php echo $error_message; ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>

    <!-- Autode tabel -->
    <div class="card shadow-sm">
        <div class="card-body p-0">
            <table class="table table-striped table-hover mb-0 align-middle">
                <thead class="table-dark">
                    <tr>
                        <th>ID</th>
                        <th>Mark</th>
                        <th>Mudel</th>
                        <th>Mootor</th>
                        <th>Kütus</th>
                        <th>Rendipäev (€)</th>
                        <th class="text-end px-4">Tegevused</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (mysqli_num_rows($cars_result) > 0): ?>
                        <?php while($car = mysqli_fetch_assoc($cars_result)): ?>
                            <tr>
                                <td><?php echo $car['id']; ?></td>
                                <td><strong><?php echo htmlspecialchars($car['mark'], ENT_QUOTES, 'UTF-8'); ?></strong></td>
                                <td><?php echo htmlspecialchars($car['model'], ENT_QUOTES, 'UTF-8'); ?></td>
                                <td><?php echo htmlspecialchars($car['engine'], ENT_QUOTES, 'UTF-8'); ?></td>
                                <td><?php echo htmlspecialchars($car['fuel'], ENT_QUOTES, 'UTF-8'); ?></td>
                                <td><?php echo htmlspecialchars($car['price'], ENT_QUOTES, 'UTF-8'); ?> €</td>
                                <td class="text-end px-4">
                                    <button class="btn btn-sm btn-warning me-1" 
                                            data-bs-toggle="modal" 
                                            data-bs-target="#editCarModal"
                                            data-id="<?php echo $car['id']; ?>"
                                            data-mark="<?php echo htmlspecialchars($car['mark'], ENT_QUOTES, 'UTF-8'); ?>"
                                            data-model="<?php echo htmlspecialchars($car['model'], ENT_QUOTES, 'UTF-8'); ?>"
                                            data-engine="<?php echo htmlspecialchars($car['engine'], ENT_QUOTES, 'UTF-8'); ?>"
                                            data-fuel="<?php echo htmlspecialchars($car['fuel'], ENT_QUOTES, 'UTF-8'); ?>"
                                            data-price="<?php echo $car['price']; ?>">
                                        Muuda
                                    </button>
                                    <a href="index.php?action=delete&id=<?php echo $car['id']; ?>" 
                                       class="btn btn-sm btn-danger" 
                                       onclick="return confirm('Oled kindel, et soovid selle auto kustutada?')">Kustuta</a>
                                </td>
                            </tr>
                        <?php endphp; ?>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="7" class="text-center py-4 text-muted">Autopark on hetkel tühi.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- ================= MODALID MÕLEMA TEGEVUSE JAOKS ================= -->

<!-- Auto lisamise modal -->
<div class="modal fade" id="addCarModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Lisa uus sõiduk</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="index.php" method="POST">
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Mark</label>
                        <input type="text" name="mark" class="form-control" required placeholder="nt Audi">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Mudel</label>
                        <input type="text" name="model" class="form-control" required placeholder="nt A6">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Mootor</label>
                        <input type="text" name="engine" class="form-control" required placeholder="nt 2.0 TDI">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Kütusetüüp</label>
                        <select name="fuel" class="form-select" required>
                            <option value="Diisel">Diisel</option>
                            <option value="Bensiin">Bensiin</option>
                            <option value="Hübriid">Hübriid</option>
                            <option value="Elekter">Elekter</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Päevahind (€)</label>
