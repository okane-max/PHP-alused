<?php
include('config.php');
session_start();

if (!isset($_SESSION['user_id'])) {
    header('Location: index.php');
    exit;
}

$user_id = $_SESSION['user_id'];

// Fetch user's reservations (excluding cancelled)
$query = "SELECT r.id, r.car_id, r.start_date, r.end_date, r.total_price, r.status, c.mark, c.model, c.price as price_per_day
          FROM reservations r
          JOIN cars c ON r.car_id = c.id
          WHERE r.user_id = ?
          AND r.status != 'cancelled'
          ORDER BY r.start_date DESC";

$stmt = mysqli_prepare($yhendus, $query);
mysqli_stmt_bind_param($stmt, 'i', $user_id);
mysqli_stmt_execute($stmt);
$res = mysqli_stmt_get_result($stmt);

$rows = [];
while ($row = mysqli_fetch_assoc($res)) {
    $rows[] = $row;
}

include('header.php');
?>
<div class="container my-5">
    <h2>Minu broneeringud</h2>
    <?php if (empty($rows)): ?>
        <div class="alert alert-info">Sul pole aktiivseid broneeringuid.</div>
    <?php else: ?>
        <div class="row row-cols-1 row-cols-md-2 g-4">
            <?php foreach ($rows as $r): ?>
                <div class="col">
                    <div class="card">
                        <div class="card-body">
                            <h5 class="card-title"><?php echo htmlspecialchars($r['mark'] . ' ' . $r['model'], ENT_QUOTES, 'UTF-8'); ?></h5>
                            <p class="card-text mb-1">Algus: <strong><?php echo htmlspecialchars($r['start_date']); ?></strong></p>
                            <p class="card-text mb-1">Lõpp: <strong><?php echo htmlspecialchars($r['end_date']); ?></strong></p>
                            <p class="card-text mb-1">Kokku: <strong><?php echo htmlspecialchars($r['total_price']); ?> €</strong></p>
                            <p class="card-text mb-2">Staatus: <span class="badge bg-secondary"><?php echo htmlspecialchars($r['status']); ?></span></p>
                            <a href="single_car.php?id=<?php echo intval($r['car_id']); ?>" class="btn btn-outline-dark">Vaata autot</a>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>

        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js" integrity="sha384-FKyoEForCGlyvwx9Hj09JcYn3nv7wiPVlz7YYwJrWVcXK/BmnVDxM+D2scQbITxI" crossorigin="anonymous"></script>
    </body>
    </html>
