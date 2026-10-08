<?php
include('config.php');
session_start();

// Kontrollime, kas auto ID on päringus olemas
if (!isset($_GET['id']) || empty($_GET['id'])) {
    header("Location: index.php");
    exit();
}

$car_id = intval($_GET['id']);

// Kuvatava auto andmete päring (SQL Injection kaitstud)
$car_query = "SELECT * FROM cars WHERE id = ?";
$stmt = mysqli_prepare($yhendus, $car_query);
mysqli_stmt_bind_param($stmt, "i", $car_id);
mysqli_stmt_execute($stmt);
$car_result = mysqli_stmt_get_result($stmt);
$car = mysqli_fetch_assoc($car_result);

if (!$car) {
    die("Autot ei leitud.");
}

$error_message = "";
$success_message = "";

// Fetch existing non-cancelled reservations for this car to show booked periods
$res_q = "SELECT start_date, end_date FROM reservations WHERE car_id = ? AND status != 'cancelled' ORDER BY start_date";
$res_stmt = mysqli_prepare($yhendus, $res_q);
mysqli_stmt_bind_param($res_stmt, 'i', $car_id);
mysqli_stmt_execute($res_stmt);
$res_result = mysqli_stmt_get_result($res_stmt);
$bookedRanges = [];
while ($br = mysqli_fetch_assoc($res_result)) {
    $bookedRanges[] = $br;
}
mysqli_stmt_close($res_stmt);

// --- BRONEERIMISE TÖÖTLUS ---
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['rent_car'])) {
    // Kontrollime, kas kasutaja on üldse sisse loginud
    if (!isset($_SESSION['user_id'])) {
        $error_message = "Auto rentimiseks peate olema sisse logitud!";
    } else {
        $user_id = $_SESSION['user_id'];
        $start_date = $_POST['start_date'];
        $end_date = $_POST['end_date'];

        if (empty($start_date) || empty($end_date)) {
            $error_message = "Palun vali nii algus- kui ka lõppkuupäev!";
        } elseif ($start_date < date('Y-m-d')) {
            $error_message = "Alguskuupäev ei saa olla minevikus!";
        } elseif ($start_date > $end_date) {
            $error_message = "Lõppkuupäev ei saa olla enne alguskuupäeva!";
        } else {
            // NÕUE: Sama autot ei tohi olla võimalik samaks perioodiks mitu korda rentida
            // Teeme selle atomaarseks: alustame transaktsiooni ja lukustame auto rea, seejärel kontrollime kattuvusi ja salvestame
            mysqli_begin_transaction($yhendus);
            try {
                // Lukustame auto rea, et vältida samal ajal tehtavaid paralleelseid broneeringuid
                $lock_stmt = mysqli_prepare($yhendus, "SELECT id FROM cars WHERE id = ? FOR UPDATE");
                mysqli_stmt_bind_param($lock_stmt, 'i', $car_id);
                mysqli_stmt_execute($lock_stmt);
                mysqli_stmt_close($lock_stmt);

                // Kontrollime kattuvaid broneeringuid (mitte tühistatud)
                $check_query = "SELECT COUNT(*) as kokku FROM reservations 
                                WHERE car_id = ? 
                                AND status != 'cancelled'
                                AND (start_date <= ? AND end_date >= ?)";
                $stmt = mysqli_prepare($yhendus, $check_query);
                mysqli_stmt_bind_param($stmt, "iss", $car_id, $end_date, $start_date);
                mysqli_stmt_execute($stmt);
                $check_result = mysqli_stmt_get_result($stmt);
                $row = mysqli_fetch_assoc($check_result);
                mysqli_stmt_close($stmt);

                if ($row['kokku'] > 0) {
                    mysqli_rollback($yhendus);
                    $error_message = "Sellel perioodil on see auto juba broneeritud! Palun vali teised kuupäevad.";
                } else {
                    // Arvutame rendipäevade arvu ja koguhinna
                    $datetime1 = new DateTime($start_date);
                    $datetime2 = new DateTime($end_date);
                    $interval = $datetime1->diff($datetime2);
                    $days = $interval->days + 1; // Kaasa arvatud alguspäev
                    $total_price = $days * $car['price'];

                    // Salvestame broneeringu (olek alguses 'pending', admin kinnitab)
                    $insert_query = "INSERT INTO reservations (user_id, car_id, start_date, end_date, total_price, status) VALUES (?, ?, ?, ?, ?, 'pending')";
                    $stmt = mysqli_prepare($yhendus, $insert_query);
                    mysqli_stmt_bind_param($stmt, "iissd", $user_id, $car_id, $start_date, $end_date, $total_price);

                    if (mysqli_stmt_execute($stmt)) {
                        mysqli_commit($yhendus);
                        $success_message = "Broneering esitatud! Administraator vaatab teie palvet käsile. Kokku: " . $total_price . " € (" . $days . " päeva).";
                    } else {
                        mysqli_rollback($yhendus);
                        $error_message = "Broneerimisel tekkis viga. Proovi uuesti.";
                    }
                    mysqli_stmt_close($stmt);
                }
            } catch (Exception $e) {
                mysqli_rollback($yhendus);
                $error_message = "Broneerimisel tekkis viga. Proovi uuesti.";
            }
        }
    }
}
?>

<?php include('header.php'); ?>

<div class="container my-5">
    <a href="index.php" class="btn btn-outline-secondary mb-4">← Tagasi avalehele</a>

    <!-- Veateated ja õnnestumised -->
    <?php if (!empty($error_message)): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <?php echo $error_message; ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>
    <?php if (!empty($success_message)): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <?php echo $success_message; ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>

    <!-- Display booked periods for this car -->
    <?php if (!empty($bookedRanges)): ?>
        <div class="mb-3">
            <h5>Broneeritud ajavahemikud</h5>
            <?php foreach ($bookedRanges as $br): ?>
                <span class="badge bg-secondary me-1"><?php echo htmlspecialchars($br['start_date']); ?> → <?php echo htmlspecialchars($br['end_date']); ?></span>
            <?php endforeach; ?>
        </div>
    <?php else: ?>
        <div class="mb-3">
            <div class="alert alert-info">Sellel autol pole hetkel broneeringuid.</div>
        </div>
    <?php endif; ?>

    <div class="row bg-white p-4 rounded shadow-sm">
        <!-- Auto pilt ja detailid -->
        <div class="col-md-6">
            <img src="https://loremflickr.com<?php echo str_replace(" ", "", $car['mark']); ?>" class="img-fluid rounded shadow-sm mb-3" alt="<?php echo htmlspecialchars($car['mark'], ENT_QUOTES, 'UTF-8'); ?>">
            <h2><?php echo htmlspecialchars($car['mark'] . ' ' . $car['model'], ENT_QUOTES, 'UTF-8'); ?></h2>
            <hr>
            <p><strong>Tehnilised andmed:</strong></p>
            <ul>
                <li>Mootor: <?php echo htmlspecialchars($car['engine'], ENT_QUOTES, 'UTF-8'); ?></li>
                <li>Kütus: <?php echo htmlspecialchars($car['fuel'], ENT_QUOTES, 'UTF-8'); ?></li>
                <li>Päeva rent: <span class="badge bg-dark fs-6"><?php echo htmlspecialchars($car['price'], ENT_QUOTES, 'UTF-8'); ?> €</span></li>
            </ul>
        </div>

        <!-- Rentimise vorm -->
        <div class="col-md-6 bg-light p-4 rounded border">
            <h3 class="mb-4">Broneeri see auto</h3>
            
            <?php if (isset($_SESSION['user_id'])): ?>
                <form action="single_car.php?id=<?php echo $car_id; ?>" method="POST">
                    <div class="mb-3">
                        <label class="form-label">Rendi alguskuupäev</label>
                        <input type="date" name="start_date" min="<?php echo date('Y-m-d'); ?>" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Rendi lõppkuupäev</label>
                        <input type="date" name="end_date" min="<?php echo date('Y-m-d'); ?>" class="form-control" required>
                    </div>
                    <button type="submit" name="rent_car" class="btn btn-dark w-100 py-2 fs-5">Kinnita broneering</button>
                </form>
            <?php else: ?>
                <div class="text-center py-4">
                    <p class="text-muted">Auto rentimiseks peate esmalt avalehel sisse logima või konto registreerima.</p>
                    <a href="index.php" class="btn btn-outline-dark">Mine avalehele sisse logima</a>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<script>
    const bookedRanges = <?php echo json_encode($bookedRanges); ?>;

    function dateInRange(dateStr) {
        if (!dateStr) return false;
        for (const r of bookedRanges) {
            if (dateStr >= r.start_date && dateStr <= r.end_date) return true;
        }
        return false;
    }

    function rangesOverlap(s, e) {
        if (!s || !e) return false;
        for (const r of bookedRanges) {
            // overlap if not (selected end < booked start OR selected start > booked end)
            if (!(e < r.start_date || s > r.end_date)) return true;
        }
        return false;
    }

    document.addEventListener('DOMContentLoaded', function() {
        const form = document.querySelector('form[action^="single_car.php"]');
        if (!form) return;
        const startInput = form.querySelector('input[name="start_date"]');
        const endInput = form.querySelector('input[name="end_date"]');

        function checkInputDates() {
            const s = startInput.value;
            const e = endInput.value;
            let msgs = [];
            if (s && dateInRange(s)) msgs.push('Valitud alguskuupäev kattub olemasoleva broneeringuga.');
            if (e && dateInRange(e)) msgs.push('Valitud lõppkuupäev kattub olemasoleva broneeringuga.');
            if (msgs.length) {
                // show a non-blocking alert; server-side will also enforce
                alert(msgs.join('\n'));
            }
        }

        startInput && startInput.addEventListener('change', checkInputDates);
        endInput && endInput.addEventListener('change', checkInputDates);

        form.addEventListener('submit', function(ev) {
            const s = startInput.value;
            const e = endInput.value;
            if (rangesOverlap(s, e)) {
                ev.preventDefault();
                alert('Valitud kuupäevad kattuvad juba broneeringuga. Vaata "Broneeritud ajavahemikud" ala ja vali teised kuupäevad.');
            }
        });
    });
</script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js" integrity="sha384-FKyoEForCGlyvwx9Hj09JcYn3nv7wiPVlz7YYwJrWVcXK/BmnVDxM+D2scQbITxI" crossorigin="anonymous"></script>
</body>
</html>
