<?php 
include_once 'header.php';

$message = '';

// Broneerimise käsitlus
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['book_car'])) {
    if (!isset($_SESSION['user_id'])) {
        $message = '<div class="alert alert-danger">Broneerimiseks peate esmalt sisse logima!</div>';
    } else {
        $car_id = (int)$_POST['car_id'];
        $start_date = $_POST['start_date'];
        $end_date = $_POST['end_date'];
        $user_id = $_SESSION['user_id'];

        // Kontroll, et kuupäevad oleks loogilised
        if (strtotime($start_date) < strtotime(date('Y-m-d')) || strtotime($end_date) < strtotime($start_date)) {
            $message = '<div class="alert alert-danger">Viga: Valitud kuupäevad on vigased või minevikus!</div>';
        } else {
            // 🛡️ AI abiga loodud topeltbroneeringu vältimise kontroll (kattuvad perioodid)
            $check_stmt = $pdo->prepare("
                SELECT COUNT(*) FROM reservations 
                WHERE car_id = ? AND status != 'cancelled' 
                AND NOT (end_date < ? OR start_date > ?)
            ");
            $check_stmt->execute([$car_id, $start_date, $end_date]);
            
            if ($check_stmt->fetchColumn() > 0) {
                $message = '<div class="alert alert-danger">Kahjuks on see auto valitud kuupäevadel juba broneeritud!</div>';
            } else {
                // Omapoolne täiendus: Automaatne rendipäevade ja koguhinna arvutamine
                $days = (strtotime($end_date) - strtotime($start_date)) / (60 * 60 * 24) + 1;
                
                $car_stmt = $pdo->prepare("SELECT price_per_day FROM cars WHERE id = ?");
                $car_stmt->execute([$car_id]);
                $price_per_day = $car_stmt->fetchColumn();
                $total_price = $days * $price_per_day;

                // Salvestamine andmebaasi reservations tabelisse
                $book_stmt = $pdo->prepare("INSERT INTO reservations (user_id, car_id, start_date, end_date, total_price, status) VALUES (?, ?, ?, ?, ?, 'confirmed')");
                $book_stmt->execute([$user_id, $car_id, $start_date, $end_date, $total_price]);
                
                $message = '<div class="alert alert-success">🎉 Broneering õnnestus! Rendipäevi: ' . $days . '. Koguhind: <strong>' . $total_price . '€</strong></div>';
            }
        }
    }
}

$cars = $pdo->query("SELECT * FROM cars")->fetchAll();
?>

<div class="text-center my-4">
    <h1 class="fw-bold">Rendi unistuste auto kiirelt ja mugavalt</h1>
    <p class="text-muted">Kõik autod on täishooldusega ja valmis sõiduks.</p>
</div>

<?= $message ?>

<div class="row">
    <?php foreach ($cars as $car): ?>
        <div class="col-md-4 mb-4">
            <div class="card h-100 shadow-sm border-0">
                <div class="card-body d-flex flex-column">
                    <h5 class="card-title fw-bold text-dark mb-1"><?= sanitize($car['brand']) ?></h5>
                    <p class="text-muted mb-3"><?= sanitize($car['model']) ?></p>
                    <h4 class="text-primary fw-bold mb-4"><?= sanitize($car['price_per_day']) ?>€ <small class="text-muted fs-6">/ päev</small></h4>
                    
                    <form method="POST" action="" class="mt-auto">
                        <input type="hidden" name="car_id" value="<?= $car['id'] ?>">
                        <div class="mb-2">
                            <label class="form-label small fw-bold">Alates:</label>
                            <input type="date" name="start_date" class="form-control form-control-sm" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label small fw-bold">Kuni:</label>
                            <input type="date" name="end_date" class="form-control form-control-sm" required>
                        </div>
                        <button type="submit" name="book_car" class="btn btn-dark w-100">Broneeri auto</button>
                    </form>
                </div>
            </div>
        </div>
    <?php endforeach; ?>
</div>

</div>
<footer class="bg-dark text-white text-center py-3 mt-5">
    <p class="mb-0 small">&copy; 2026 AutoRent Süsteem. Kõik õigused kaitstud.</p>
</footer>
</body>
</html>
