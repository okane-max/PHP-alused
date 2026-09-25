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

// --- BRONEERINGU STAATUSE MUUTMINE ---
if (isset($_GET['action']) && isset($_GET['id'])) {
    $reservation_id = intval($_GET['id']);
    $action = $_GET['action'];
    
    if ($action === 'confirm') {
        $new_status = 'confirmed';
    } elseif ($action === 'cancel') {
        $new_status = 'cancelled';
    } else {
        $new_status = null;
    }

    if ($new_status !== null) {
        $update_query = "UPDATE reservations SET status = ? WHERE id = ?";
        $stmt = mysqli_prepare($yhendus, $update_query);
        mysqli_stmt_bind_param($stmt, "si", $new_status, $reservation_id);
        
        if (mysqli_stmt_execute($stmt)) {
            $success_message = "Broneeringu staatus on edukalt uuendatud!";
        } else {
            $error_message = "Staatuse muutmisel tekkis viga.";
        }
    }
}

// --- ANDMETE PÄRIMINE TEMAATILISE LIITMISEGA (JOIN) ---
// Pärime broneeringud koos kasutaja nime ja auto margiga/mudeliga
$res_query = "SELECT r.id, r.start_date, r.end_date, r.total_price, r.status, 
                     u.username, u.email, 
                     c.mark, c.model 
              FROM reservations r
              JOIN users u ON r.user_id = u.id
              JOIN cars c ON r.car_id = c.id
              ORDER BY r.id DESC";

$res_result = mysqli_query($yhendus, $res_query);
?>
<!DOCTYPE html>
<html lang="et">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admini Paneel - Broneeringute Haldus</title>
    <link href="https://jsdelivr.net" rel="stylesheet">
</head>
<body class="bg-light">

<nav class="navbar navbar-expand-lg navbar-dark bg-dark mb-4">
    <div class="container">
        <a class="navbar-brand" href="#">⚙️ AutoRent Admin</a>
        <div class="navbar-nav ms-auto">
            <a class="nav-link" href="index.php">Autode haldus</a>
            <a class="nav-link active" href="reservations.php">Broneeringute haldus</a>
            <a class="btn btn-outline-light btn-sm ms-3" href="../index.php">Tagasi avalehele</a>
        </div>
    </div>
</nav>

<div class="container">
    <div class="row mb-4">
        <div class="col">
            <h2>Klientide broneeringud</h2>
            <p class="text-muted">Siin näete reaalajas kõiki tellimusi ning saate neid kinnitada või tühistada.</p>
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

    <!-- Broneeringute tabel -->
    <div class="card shadow-sm">
        <div class="card-body p-0">
            <table class="table table-striped table-hover mb-0 align-middle">
                <thead class="table-dark">
                    <tr>
                        <th>ID</th>
                        <th>Klient</th>
                        <th>Sõiduk</th>
                        <th>Periood</th>
                        <th>Summa</th>
                        <th>Staatus</th>
                        <th class="text-end px-4">Tegevused</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (mysqli_num_rows($res_result) > 0): ?>
                        <?php while($res = mysqli_fetch_assoc($res_result)): ?>
                            <tr>
                                <td><?php echo $res['id']; ?></td>
                                <td>
                                    <strong><?php echo htmlspecialchars($res['username'], ENT_QUOTES, 'UTF-8'); ?></strong><br>
                                    <small class="text-muted"><?php echo htmlspecialchars($res['email'], ENT_QUOTES, 'UTF-8'); ?></small>
                                </td>
                                <td>
                                    <?php echo htmlspecialchars($res['mark'] . ' ' . $res['model'], ENT_QUOTES, 'UTF-8'); ?>
                                </td>
                                <td>
                                    <span class="badge bg-secondary"><?php echo $res['start_date']; ?></span> 
                                    kuni 
                                    <span class="badge bg-secondary"><?php echo $res['end_date']; ?></span>
                                </td>
                                <td><strong><?php echo htmlspecialchars($res['total_price'], ENT_QUOTES, 'UTF-8'); ?> €</strong></td>
                                <td>
                                    <?php 
                                    if ($res['status'] === 'confirmed') {
                                        echo '<span class="badge bg-success">Kinnitatud</span>';
                                    } elseif ($res['status'] === 'cancelled') {
                                        echo '<span class="badge bg-danger">Tühistatud</span>';
                                    } else {
                                        echo '<span class="badge bg-warning text-dark">' . htmlspecialchars($res['status'], ENT_QUOTES, 'UTF-8') . '</span>';
                                    }
                                    ?>
                                </td>
                                <td class="text-end px-4">
                                    <?php if ($res['status'] !== 'confirmed'): ?>
                                        <a href="reservations.php?action=confirm&id=<?php echo $res['id']; ?>" 
                                           class="btn btn-sm btn-outline-success me-1">Kinnita</a>
                                    <?php endif; ?>
                                    
                                    <?php if ($res['status'] !== 'cancelled'): ?>
                                        <a href="reservations.php?action=cancel&id=<?php echo $res['id']; ?>" 
                                           class="btn btn-sm btn-outline-danger"
                                           onclick="return confirm('Oled kindel, et soovid selle broneeringu tühistada?')">Tühista</a>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="7" class="text-center py-4 text-muted">Ühtegi broneeringut ei ole veel tehtud.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script src="https://jsdelivr.net"></script>
</body>
</html>
