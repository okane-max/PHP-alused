<?php include('config.php'); ?>
<?php include('header.php'); ?>

<!-- Filter Form -->
<div class="container my-4">
    <div class="card">
        <div class="card-header bg-dark text-white">
            <h5 class="mb-0">Filtreeri autosid</h5>
        </div>
        <div class="card-body">
            <form method="GET" action="index.php">
                <div class="row g-3">
                    <div class="col-md-2">
                        <label class="form-label">Mark</label>
                        <input type="text" name="mark" class="form-control" value="<?php echo htmlspecialchars($_GET['mark'] ?? '', ENT_QUOTES, 'UTF-8'); ?>" placeholder="nt Audi">
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">Mudel</label>
                        <input type="text" name="model" class="form-control" value="<?php echo htmlspecialchars($_GET['model'] ?? '', ENT_QUOTES, 'UTF-8'); ?>" placeholder="nt A4">
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">Kütus</label>
                        <select name="fuel" class="form-select">
                            <option value="">Kõik</option>
                            <option value="bensiin" <?php echo ($_GET['fuel'] ?? '') === 'bensiin' ? 'selected' : ''; ?>>Bensiin</option>
                            <option value="diesel" <?php echo ($_GET['fuel'] ?? '') === 'diesel' ? 'selected' : ''; ?>>Diisel</option>
                            <option value="hybrid" <?php echo ($_GET['fuel'] ?? '') === 'hybrid' ? 'selected' : ''; ?>>Hübriid</option>
                            <option value="electric" <?php echo ($_GET['fuel'] ?? '') === 'electric' ? 'selected' : ''; ?>>Elekter</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">Mootor</label>
                        <input type="text" name="engine" class="form-control" value="<?php echo htmlspecialchars($_GET['engine'] ?? '', ENT_QUOTES, 'UTF-8'); ?>" placeholder="nt V6">
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">Max hind (€/päev)</label>
                        <input type="number" name="max_price" class="form-control" value="<?php echo htmlspecialchars($_GET['max_price'] ?? '', ENT_QUOTES, 'UTF-8'); ?>" placeholder="nt 500">
                    </div>
                    <div class="col-md-2 d-flex align-items-end">
                        <button type="submit" class="btn btn-primary w-100">Otsi</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Cars Grid -->
<div class="container">
<?php
    // Only show available cars that are not currently rented
    $paring = "SELECT * FROM cars WHERE status = 'vaba' AND id NOT IN (
        SELECT car_id FROM reservations WHERE status = 'confirmed' AND start_date <= CURDATE() AND end_date >= CURDATE()
    )";
    
    // Filter by mark
    if (!empty($_GET["mark"])) {
        $mark = mysqli_real_escape_string($yhendus, $_GET["mark"]);
        $paring .= " AND mark LIKE '%".$mark."%'";
    }
    
    // Filter by model
    if (!empty($_GET["model"])) {
        $model = mysqli_real_escape_string($yhendus, $_GET["model"]);
        $paring .= " AND model LIKE '%".$model."%'";
    }
    
    // Filter by fuel
    if (!empty($_GET["fuel"])) {
        $fuel = mysqli_real_escape_string($yhendus, $_GET["fuel"]);
        $paring .= " AND LOWER(fuel) LIKE LOWER('%".$fuel."%')";
    }
    
    // Filter by engine
    if (!empty($_GET["engine"])) {
        $engine = mysqli_real_escape_string($yhendus, $_GET["engine"]);
        $paring .= " AND engine LIKE '%".$engine."%'";
    }
    
    // Filter by max price
    if (!empty($_GET["max_price"])) {
        $max_price = floatval($_GET["max_price"]);
        $paring .= " AND price <= ".$max_price;
    }

    $valjund = mysqli_query($yhendus, $paring);
    $count = mysqli_num_rows($valjund);
?>
    <?php if ($count === 0): ?>
        <div class="alert alert-info w-100">Nende filtritega autosid ei leitud.</div>
    <?php else: ?>
        <p class="text-muted mb-3">Leiti <?php echo $count; ?> autot</p>
        <div class="row row-cols-1 row-cols-md-4 g-4">
        <?php while($rida = mysqli_fetch_assoc($valjund)): ?>
            <div class="col">
                <div class="card">
                <img src="https://placehold.co/800x500/1f2937/f8fafc?text=<?php echo rawurlencode($rida["mark"] . ' ' . $rida["model"]); ?>" class="card-img-top" alt="<?php echo htmlspecialchars($rida["mark"], ENT_QUOTES, 'UTF-8'); ?>">
                <div class="card-body">
                    <h5 class="card-title"><?php echo htmlspecialchars($rida["mark"] . ' ' . $rida["model"], ENT_QUOTES, 'UTF-8'); ?></h5>
                    <p class="card-text">
                        <small>Mootor: <?php echo htmlspecialchars($rida["engine"], ENT_QUOTES, 'UTF-8'); ?> <br>
                        Kütus: <?php echo htmlspecialchars($rida["fuel"], ENT_QUOTES, 'UTF-8'); ?><br>
                        Hind: <?php echo htmlspecialchars($rida["price"], ENT_QUOTES, 'UTF-8'); ?>€/päev</small>
                    </p>
                    <a href="single_car.php?id=<?php echo $rida["id"]; ?>" class="btn btn-dark w-100">Rendi</a>
                </div>
                </div>
            </div>
        <?php endwhile; ?>
    </div>
    <?php endif; ?>
</div>
<!-- /Cars Grid -->

    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js" integrity="sha384-FKyoEForCGlyvwx9Hj09JcYn3nv7wiPVlz7YYwJrWVcXK/BmnVDxM+D2scQbITxI" crossorigin="anonymous"></script>
  </body>
</html>
