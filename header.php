<?php include_once 'config.php'; ?>
<!DOCTYPE html>
<html lang="et">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>AutoRent</title>
    <!-- Bootstrap 5 CSS (PARANDATUD TÄIELIK LINK) -->
    <link href="https://jsdelivr.net" rel="stylesheet">
</head>
<body class="bg-light">
<nav class="navbar navbar-expand-lg navbar-dark bg-dark mb-4 shadow-sm">
    <div class="container">
        <a class="navbar-brand fw-bold" href="index.php">🚗 AutoRent Pro</a>
        <div class="navbar-nav ms-auto">
            <?php if (isset($_SESSION['user_id'])): ?>
                <span class="navbar-text me-3 text-white">Tere, <strong><?= htmlspecialchars($_SESSION['username'], ENT_QUOTES, 'UTF-8') ?></strong>!</span>
                <?php if ($_SESSION['role'] === 'admin'): ?>
                    <a class="btn btn-warning btn-sm me-2 fw-bold" href="admin/index.php">Halduspaneel</a>
                <?php endif; ?>
                <a class="btn btn-outline-danger btn-sm" href="logout.php">Logi välja</a>
            <?php else: ?>
                <!-- Bootstrapi modal-akna nupud (Vastavalt meie uuele index.php loogikale) -->
                <button type="button" class="btn btn-outline-light btn-sm me-2" data-bs-toggle="modal" data-bs-target="#loginModal">Logi sisse</button>
                <button type="button" class="btn btn-primary btn-sm fw-bold" data-bs-toggle="modal" data-bs-target="#registerModal">Registreeru</button>
            <?php endif; ?>
        </div>
    </div>
</nav>
<div class="container">
