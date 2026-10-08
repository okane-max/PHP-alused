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

// Handle delete via GET (with confirmation in UI)
if (isset($_GET['action']) && $_GET['action'] === 'delete' && isset($_GET['id'])) {
    $delete_id = intval($_GET['id']);
    $stmt = mysqli_prepare($yhendus, 'DELETE FROM cars WHERE id = ?');
    mysqli_stmt_bind_param($stmt, 'i', $delete_id);
    if (mysqli_stmt_execute($stmt)) {
        $success_message = 'Auto on edukalt kustutatud.';
    } else {
        $error_message = 'Kustutamisel tekkis viga. Kontrolli seoseid broneeringutega.';
    }
    mysqli_stmt_close($stmt);
}

// Handle add car
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'add_car') {
    $mark = trim($_POST['mark'] ?? '');
    $model = trim($_POST['model'] ?? '');
    $engine = trim($_POST['engine'] ?? '');
    $fuel = trim($_POST['fuel'] ?? '');
    $price = floatval($_POST['price'] ?? 0);
    $image = trim($_POST['image'] ?? '');
    $year = intval($_POST['year'] ?? 0);
    $transmission = trim($_POST['transmission'] ?? '');
    $seats = intval($_POST['seats'] ?? 0);
    $description = trim($_POST['description'] ?? '');
    $status = trim($_POST['status'] ?? 'vaba');

    if ($mark === '' || $model === '' || $price <= 0) {
        $error_message = 'Vähemalt mark, mudel ja hind peavad olema täidetud.';
    } else {
        $stmt = mysqli_prepare($yhendus, 'INSERT INTO cars (mark, model, engine, fuel, price, image, year, transmission, seats, description, status) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)');
        mysqli_stmt_bind_param($stmt, 'ssssdsssiis', $mark, $model, $engine, $fuel, $price, $image, $year, $transmission, $seats, $description, $status);
        if (mysqli_stmt_execute($stmt)) {
            $success_message = 'Uus auto lisatud.';
        } else {
            $error_message = 'Auto lisamisel tekkis viga.';
        }
        mysqli_stmt_close($stmt);
    }
}

// Handle edit car
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'edit_car') {
    $id = intval($_POST['id'] ?? 0);
    $mark = trim($_POST['mark'] ?? '');
    $model = trim($_POST['model'] ?? '');
    $engine = trim($_POST['engine'] ?? '');
    $fuel = trim($_POST['fuel'] ?? '');
    $price = floatval($_POST['price'] ?? 0);
    $image = trim($_POST['image'] ?? '');
    $year = intval($_POST['year'] ?? 0);
    $transmission = trim($_POST['transmission'] ?? '');
    $seats = intval($_POST['seats'] ?? 0);
    $description = trim($_POST['description'] ?? '');
    $status = trim($_POST['status'] ?? 'vaba');

    if ($id <= 0 || $mark === '' || $model === '' || $price <= 0) {
        $error_message = 'Puuduvad või vigased andmed.';
    } else {
        $stmt = mysqli_prepare($yhendus, 'UPDATE cars SET mark = ?, model = ?, engine = ?, fuel = ?, price = ?, image = ?, year = ?, transmission = ?, seats = ?, description = ?, status = ? WHERE id = ?');
        mysqli_stmt_bind_param($stmt, 'ssssdsssiisi', $mark, $model, $engine, $fuel, $price, $image, $year, $transmission, $seats, $description, $status, $id);
        if (mysqli_stmt_execute($stmt)) {
            $success_message = 'Auto uuendatud.';
        } else {
            $error_message = 'Auto uuendamisel tekkis viga.';
        }
        mysqli_stmt_close($stmt);
    }
}

// Fetch cars list with filtering
$cars_q = 'SELECT id, mark, model, engine, fuel, price, image, year, transmission, seats, description, status FROM cars WHERE 1=1';

// Filter by mark
if (!empty($_GET["mark"])) {
    $mark = mysqli_real_escape_string($yhendus, $_GET["mark"]);
    $cars_q .= " AND mark LIKE '%".$mark."%'";
}

// Filter by model
if (!empty($_GET["model"])) {
    $model = mysqli_real_escape_string($yhendus, $_GET["model"]);
    $cars_q .= " AND model LIKE '%".$model."%'";
}

// Filter by fuel
if (!empty($_GET["fuel"])) {
    $fuel = mysqli_real_escape_string($yhendus, $_GET["fuel"]);
    $cars_q .= " AND LOWER(fuel) LIKE LOWER('%".$fuel."%')";
}

// Filter by engine
if (!empty($_GET["engine"])) {
    $engine = mysqli_real_escape_string($yhendus, $_GET["engine"]);
    $cars_q .= " AND engine LIKE '%".$engine."%'";
}

// Filter by max price
if (!empty($_GET["max_price"])) {
    $max_price = floatval($_GET["max_price"]);
    $cars_q .= " AND price <= ".$max_price;
}

// Filter by status
if (!empty($_GET["status"]) && $_GET["status"] !== 'all') {
    $status = mysqli_real_escape_string($yhendus, $_GET["status"]);
    $cars_q .= " AND status = '".$status."'";
}

$cars_q .= ' ORDER BY id DESC';
$cars_res = mysqli_query($yhendus, $cars_q);
$cars_count = mysqli_num_rows($cars_res);
?>
<!doctype html>
<html lang="et">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Admin - Autode haldus</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<nav class="navbar navbar-dark bg-dark mb-4">
  <div class="container">
    <a class="navbar-brand" href="#">⚙️ AutoRent Admin</a>
    <div class="d-flex">
      <a class="btn btn-outline-light me-2" href="reservations.php">Broneeringud</a>
      <a class="btn btn-outline-light" href="../index.php">Avalehele</a>
    </div>
  </div>
</nav>
<div class="container">
  <?php if ($success_message): ?>
    <div class="alert alert-success"><?php echo htmlspecialchars($success_message); ?></div>
  <?php endif; ?>
  <?php if ($error_message): ?>
    <div class="alert alert-danger"><?php echo htmlspecialchars($error_message); ?></div>
  <?php endif; ?>

  <div class="d-flex justify-content-between align-items-center mb-3">
    <h2>Autod</h2>
    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addModal">+ Lisa auto</button>
  </div>

  <!-- Filter Form -->
  <div class="card mb-4">
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
            <label class="form-label">Max hind (€)</label>
            <input type="number" name="max_price" class="form-control" value="<?php echo htmlspecialchars($_GET['max_price'] ?? '', ENT_QUOTES, 'UTF-8'); ?>" placeholder="nt 500">
          </div>
          <div class="col-md-2">
            <label class="form-label">Staatus</label>
            <select name="status" class="form-select">
              <option value="">Kõik</option>
              <option value="vaba" <?php echo ($_GET['status'] ?? '') === 'vaba' ? 'selected' : ''; ?>>Vaba</option>
              <option value="rendidud" <?php echo ($_GET['status'] ?? '') === 'rendidud' ? 'selected' : ''; ?>>Rendidud</option>
              <option value="hoolduses" <?php echo ($_GET['status'] ?? '') === 'hoolduses' ? 'selected' : ''; ?>>Hoolduses</option>
            </select>
          </div>
          <div class="col-md-12 d-flex gap-2">
            <button type="submit" class="btn btn-primary">Otsi</button>
            <a href="index.php" class="btn btn-outline-secondary">Tühista filtrid</a>
          </div>
        </div>
      </form>
    </div>
  </div>

  <?php if (!empty($_GET) && array_filter($_GET)): ?>
    <p class="text-muted mb-3">Leiti <?php echo $cars_count; ?> autot</p>
  <?php endif; ?>

  <div class="card shadow-sm">
    <div class="card-body p-0">
      <table class="table table-striped mb-0 align-middle">
        <thead class="table-dark">
          <tr>
            <th>ID</th>
            <th>Mark</th>
            <th>Mudel</th>
            <th>Mootor</th>
            <th>Kütus</th>
            <th>Hind</th>
            <th>Staatus</th>
            <th class="text-end">Tegevused</th>
          </tr>
        </thead>
        <tbody>
        <?php if ($cars_res && mysqli_num_rows($cars_res) > 0): ?>
          <?php while ($car = mysqli_fetch_assoc($cars_res)): ?>
            <tr>
              <td><?php echo $car['id']; ?></td>
              <td><?php echo htmlspecialchars($car['mark'], ENT_QUOTES, 'UTF-8'); ?></td>
              <td><?php echo htmlspecialchars($car['model'], ENT_QUOTES, 'UTF-8'); ?></td>
              <td><?php echo htmlspecialchars($car['engine'], ENT_QUOTES, 'UTF-8'); ?></td>
              <td><?php echo htmlspecialchars($car['fuel'], ENT_QUOTES, 'UTF-8'); ?></td>
              <td><?php echo htmlspecialchars($car['price'], ENT_QUOTES, 'UTF-8'); ?> €</td>
              <td><?php echo htmlspecialchars($car['status'], ENT_QUOTES, 'UTF-8'); ?></td>
              <td class="text-end">
                <button class="btn btn-sm btn-warning me-1" data-bs-toggle="modal" data-bs-target="#editModal"
                  data-id="<?php echo $car['id']; ?>"
                  data-mark="<?php echo htmlspecialchars($car['mark'], ENT_QUOTES, 'UTF-8'); ?>"
                  data-model="<?php echo htmlspecialchars($car['model'], ENT_QUOTES, 'UTF-8'); ?>"
                  data-engine="<?php echo htmlspecialchars($car['engine'], ENT_QUOTES, 'UTF-8'); ?>"
                  data-fuel="<?php echo htmlspecialchars($car['fuel'], ENT_QUOTES, 'UTF-8'); ?>"
                  data-price="<?php echo htmlspecialchars($car['price'], ENT_QUOTES, 'UTF-8'); ?>"
                  data-image="<?php echo htmlspecialchars($car['image'], ENT_QUOTES, 'UTF-8'); ?>"
                  data-year="<?php echo htmlspecialchars($car['year'], ENT_QUOTES, 'UTF-8'); ?>"
                  data-transmission="<?php echo htmlspecialchars($car['transmission'], ENT_QUOTES, 'UTF-8'); ?>"
                  data-seats="<?php echo htmlspecialchars($car['seats'], ENT_QUOTES, 'UTF-8'); ?>"
                  data-description="<?php echo htmlspecialchars($car['description'], ENT_QUOTES, 'UTF-8'); ?>"
                  data-status="<?php echo htmlspecialchars($car['status'], ENT_QUOTES, 'UTF-8'); ?>"
                >Muuda</button>
                <a href="?action=delete&id=<?php echo $car['id']; ?>" class="btn btn-sm btn-danger" onclick="return confirm('Oled kindel?');">Kustuta</a>
              </td>
            </tr>
          <?php endwhile; ?>
        <?php else: ?>
          <tr><td colspan="8" class="text-center py-4">Autopark on tühi</td></tr>
        <?php endif; ?>
        </tbody>
      </table>
    </div>
  </div>
</div>

<!-- Add Modal -->
<div class="modal fade" id="addModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <form method="post" action="index.php">
        <input type="hidden" name="action" value="add_car">
        <div class="modal-header">
          <h5 class="modal-title">Lisa auto</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
          <div class="row g-3">
            <div class="col-md-6">
              <label class="form-label">Mark</label>
              <input name="mark" class="form-control" required>
            </div>
            <div class="col-md-6">
              <label class="form-label">Mudel</label>
              <input name="model" class="form-control" required>
            </div>
            <div class="col-md-4">
              <label class="form-label">Mootor</label>
              <input name="engine" class="form-control">
            </div>
            <div class="col-md-4">
              <label class="form-label">Kütus</label>
              <input name="fuel" class="form-control">
            </div>
            <div class="col-md-4">
              <label class="form-label">Hind (€)</label>
              <input name="price" type="number" step="0.01" class="form-control" required>
            </div>
            <div class="col-md-6">
              <label class="form-label">Pilt (URL või /img/...)</label>
              <input name="image" class="form-control">
            </div>
            <div class="col-md-2">
              <label class="form-label">Aasta</label>
              <input name="year" type="number" class="form-control">
            </div>
            <div class="col-md-4">
              <label class="form-label">Käigukast</label>
              <input name="transmission" class="form-control">
            </div>
            <div class="col-md-2">
              <label class="form-label">Kohad</label>
              <input name="seats" type="number" class="form-control">
            </div>
            <div class="col-12">
              <label class="form-label">Kirjeldus</label>
              <textarea name="description" class="form-control" rows="2"></textarea>
            </div>
            <div class="col-md-3">
              <label class="form-label">Staatus</label>
              <select name="status" class="form-select">
                <option value="vaba">vaba</option>
                <option value="rendidud">rendidud</option>
                <option value="hoolduses">hoolduses</option>
              </select>
            </div>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tühista</button>
          <button type="submit" class="btn btn-primary">Salvesta</button>
        </div>
      </form>
    </div>
  </div>
</div>

<!-- Edit Modal -->
<div class="modal fade" id="editModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <form method="post" action="index.php">
        <input type="hidden" name="action" value="edit_car">
        <input type="hidden" name="id" id="edit_id">
        <div class="modal-header">
          <h5 class="modal-title">Muuda auto andmeid</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
          <div class="row g-3">
            <div class="col-md-6">
              <label class="form-label">Mark</label>
              <input id="edit_mark" name="mark" class="form-control" required>
            </div>
            <div class="col-md-6">
              <label class="form-label">Mudel</label>
              <input id="edit_model" name="model" class="form-control" required>
            </div>
            <div class="col-md-4">
              <label class="form-label">Mootor</label>
              <input id="edit_engine" name="engine" class="form-control">
            </div>
            <div class="col-md-4">
              <label class="form-label">Kütus</label>
              <input id="edit_fuel" name="fuel" class="form-control">
            </div>
            <div class="col-md-4">
              <label class="form-label">Hind (€)</label>
              <input id="edit_price" name="price" type="number" step="0.01" class="form-control" required>
            </div>
            <div class="col-md-6">
              <label class="form-label">Pilt (URL või /img/...)</label>
              <input id="edit_image" name="image" class="form-control">
            </div>
            <div class="col-md-2">
              <label class="form-label">Aasta</label>
              <input id="edit_year" name="year" type="number" class="form-control">
            </div>
            <div class="col-md-4">
              <label class="form-label">Käigukast</label>
              <input id="edit_transmission" name="transmission" class="form-control">
            </div>
            <div class="col-md-2">
              <label class="form-label">Kohad</label>
              <input id="edit_seats" name="seats" type="number" class="form-control">
            </div>
            <div class="col-12">
              <label class="form-label">Kirjeldus</label>
              <textarea id="edit_description" name="description" class="form-control" rows="2"></textarea>
            </div>
            <div class="col-md-3">
              <label class="form-label">Staatus</label>
              <select id="edit_status" name="status" class="form-select">
                <option value="vaba">vaba</option>
                <option value="rendidud">rendidud</option>
                <option value="hoolduses">hoolduses</option>
              </select>
            </div>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tühista</button>
          <button type="submit" class="btn btn-primary">Salvesta muudatused</button>
        </div>
      </form>
    </div>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>
<script>
  var editModal = document.getElementById('editModal');
  editModal && editModal.addEventListener('show.bs.modal', function (event) {
    var button = event.relatedTarget;
    document.getElementById('edit_id').value = button.getAttribute('data-id');
    document.getElementById('edit_mark').value = button.getAttribute('data-mark');
    document.getElementById('edit_model').value = button.getAttribute('data-model');
    document.getElementById('edit_engine').value = button.getAttribute('data-engine');
    document.getElementById('edit_fuel').value = button.getAttribute('data-fuel');
    document.getElementById('edit_price').value = button.getAttribute('data-price');
    document.getElementById('edit_image').value = button.getAttribute('data-image');
    document.getElementById('edit_year').value = button.getAttribute('data-year');
    document.getElementById('edit_transmission').value = button.getAttribute('data-transmission');
    document.getElementById('edit_seats').value = button.getAttribute('data-seats');
    document.getElementById('edit_description').value = button.getAttribute('data-description');
    document.getElementById('edit_status').value = button.getAttribute('data-status');
  });
</script>
</body>
</html>
