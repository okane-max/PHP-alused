<?php
include('../config.php');
session_start();

// Admin-only access
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header('Location: ../index.php');
    exit();
}

$success_message = '';
$error_message = '';

// Handle status change
if (isset($_GET['action']) && isset($_GET['id'])) {
    $reservation_id = intval($_GET['id']);
    $action = $_GET['action'];
    
    $new_status = '';
    if ($action === 'confirm') {
        $new_status = 'confirmed';
    } elseif ($action === 'cancel') {
        $new_status = 'cancelled';
    }

    if ($new_status) {
        $stmt = mysqli_prepare($yhendus, 'UPDATE reservations SET status = ? WHERE id = ?');
        mysqli_stmt_bind_param($stmt, 'si', $new_status, $reservation_id);
        if (mysqli_stmt_execute($stmt)) {
            $success_message = 'Broneeringu staatus uuendatud.';
        } else {
            $error_message = 'Staatuse muutmisel tekkis viga.';
        }
        mysqli_stmt_close($stmt);
    }
}

// Fetch reservations grouped by status
$res_q = "SELECT r.id, r.start_date, r.end_date, r.total_price, r.status, r.user_id, r.car_id,
                 u.username, u.email, 
                 c.mark, c.model 
          FROM reservations r
          JOIN users u ON r.user_id = u.id
          JOIN cars c ON r.car_id = c.id
          ORDER BY 
            CASE r.status WHEN 'pending' THEN 0 WHEN 'confirmed' THEN 1 ELSE 2 END,
            r.id DESC";
$res_result = mysqli_query($yhendus, $res_q);
$reservations = [];
while ($row = mysqli_fetch_assoc($res_result)) {
    $reservations[] = $row;
}
?>
<!doctype html>
<html lang="et">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Admin - Broneeringute haldus</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<nav class="navbar navbar-dark bg-dark mb-4">
  <div class="container">
    <a class="navbar-brand" href="#">⚙️ AutoRent Admin</a>
    <div class="d-flex">
      <a class="btn btn-outline-light me-2" href="index.php">Autod</a>
      <a class="btn btn-outline-light" href="../index.php">Avalehele</a>
    </div>
  </div>
</nav>
<div class="container">
  <?php if ($success_message): ?>
    <div class="alert alert-success alert-dismissible fade show"><?php echo htmlspecialchars($success_message); ?><button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
  <?php endif; ?>
  <?php if ($error_message): ?>
    <div class="alert alert-danger alert-dismissible fade show"><?php echo htmlspecialchars($error_message); ?><button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
  <?php endif; ?>

  <h2 class="mb-4">Broneeringute haldus</h2>

  <!-- Filter tabs -->
  <ul class="nav nav-tabs mb-4" role="tablist">
    <li class="nav-item">
      <a class="nav-link active" data-bs-toggle="tab" href="#tab-pending">Ootel <?php echo count(array_filter($reservations, fn($r) => $r['status'] === 'pending')); ?></a>
    </li>
    <li class="nav-item">
      <a class="nav-link" data-bs-toggle="tab" href="#tab-confirmed">Kinnitatud <?php echo count(array_filter($reservations, fn($r) => $r['status'] === 'confirmed')); ?></a>
    </li>
    <li class="nav-item">
      <a class="nav-link" data-bs-toggle="tab" href="#tab-cancelled">Tühistatud <?php echo count(array_filter($reservations, fn($r) => $r['status'] === 'cancelled')); ?></a>
    </li>
  </ul>

  <div class="tab-content">
    <!-- Pending tab -->
    <div id="tab-pending" class="tab-pane fade show active">
      <div class="row g-3">
        <?php foreach ($reservations as $r): if ($r['status'] !== 'pending') continue; ?>
          <div class="col-lg-6">
            <div class="card border-warning shadow-sm">
              <div class="card-header bg-warning text-dark">
                <strong><?php echo htmlspecialchars($r['mark'] . ' ' . $r['model'], ENT_QUOTES, 'UTF-8'); ?></strong>
                <span class="badge bg-secondary float-end">ID: <?php echo $r['id']; ?></span>
              </div>
              <div class="card-body">
                <p class="mb-2"><strong>Klient:</strong> <?php echo htmlspecialchars($r['username'], ENT_QUOTES, 'UTF-8'); ?> <br><small class="text-muted"><?php echo htmlspecialchars($r['email'], ENT_QUOTES, 'UTF-8'); ?></small></p>
                <p class="mb-2"><strong>Periood:</strong> <?php echo htmlspecialchars($r['start_date']); ?> kuni <?php echo htmlspecialchars($r['end_date']); ?></p>
                <p class="mb-2"><strong>Summa:</strong> <span class="badge bg-success"><?php echo htmlspecialchars($r['total_price']); ?> €</span></p>
              </div>
              <div class="card-footer bg-white d-flex gap-2">
                <a href="reservations.php?action=confirm&id=<?php echo $r['id']; ?>" class="btn btn-success btn-sm flex-grow-1">Kinnita</a>
                <a href="reservations.php?action=cancel&id=<?php echo $r['id']; ?>" class="btn btn-danger btn-sm flex-grow-1" onclick="return confirm('Kindel?')">Tühista</a>
              </div>
            </div>
          </div>
        <?php endforeach; ?>
      </div>
      <?php if (!array_filter($reservations, fn($r) => $r['status'] === 'pending')): ?>
        <div class="text-center py-5 text-muted">Ootel broneeringuid pole.</div>
      <?php endif; ?>
    </div>

    <!-- Confirmed tab -->
    <div id="tab-confirmed" class="tab-pane fade">
      <div class="row g-3">
        <?php foreach ($reservations as $r): if ($r['status'] !== 'confirmed') continue; ?>
          <div class="col-lg-6">
            <div class="card border-success shadow-sm">
              <div class="card-header bg-success text-white">
                <strong><?php echo htmlspecialchars($r['mark'] . ' ' . $r['model'], ENT_QUOTES, 'UTF-8'); ?></strong>
                <span class="badge bg-secondary float-end">ID: <?php echo $r['id']; ?></span>
              </div>
              <div class="card-body">
                <p class="mb-2"><strong>Klient:</strong> <?php echo htmlspecialchars($r['username'], ENT_QUOTES, 'UTF-8'); ?> <br><small class="text-muted"><?php echo htmlspecialchars($r['email'], ENT_QUOTES, 'UTF-8'); ?></small></p>
                <p class="mb-2"><strong>Periood:</strong> <?php echo htmlspecialchars($r['start_date']); ?> kuni <?php echo htmlspecialchars($r['end_date']); ?></p>
                <p class="mb-2"><strong>Summa:</strong> <span class="badge bg-info"><?php echo htmlspecialchars($r['total_price']); ?> €</span></p>
              </div>
              <div class="card-footer bg-white">
                <a href="reservations.php?action=cancel&id=<?php echo $r['id']; ?>" class="btn btn-outline-danger btn-sm w-100" onclick="return confirm('Kindel?')">Tühista</a>
              </div>
            </div>
          </div>
        <?php endforeach; ?>
      </div>
      <?php if (!array_filter($reservations, fn($r) => $r['status'] === 'confirmed')): ?>
        <div class="text-center py-5 text-muted">Kinnitatud broneeringuid pole.</div>
      <?php endif; ?>
    </div>

    <!-- Cancelled tab -->
    <div id="tab-cancelled" class="tab-pane fade">
      <div class="row g-3">
        <?php foreach ($reservations as $r): if ($r['status'] !== 'cancelled') continue; ?>
          <div class="col-lg-6">
            <div class="card border-danger shadow-sm opacity-75">
              <div class="card-header bg-danger text-white">
                <strong><?php echo htmlspecialchars($r['mark'] . ' ' . $r['model'], ENT_QUOTES, 'UTF-8'); ?></strong>
                <span class="badge bg-secondary float-end">ID: <?php echo $r['id']; ?></span>
              </div>
              <div class="card-body">
                <p class="mb-2"><strong>Klient:</strong> <?php echo htmlspecialchars($r['username'], ENT_QUOTES, 'UTF-8'); ?> <br><small class="text-muted"><?php echo htmlspecialchars($r['email'], ENT_QUOTES, 'UTF-8'); ?></small></p>
                <p class="mb-2"><strong>Periood:</strong> <?php echo htmlspecialchars($r['start_date']); ?> kuni <?php echo htmlspecialchars($r['end_date']); ?></p>
                <p class="mb-2"><strong>Summa:</strong> <span class="badge bg-secondary"><?php echo htmlspecialchars($r['total_price']); ?> €</span></p>
              </div>
            </div>
          </div>
        <?php endforeach; ?>
      </div>
      <?php if (!array_filter($reservations, fn($r) => $r['status'] === 'cancelled')): ?>
        <div class="text-center py-5 text-muted">Tühistatud broneeringuid pole.</div>
      <?php endif; ?>
    </div>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
