<?php
if (session_status() === PHP_SESSION_NONE) session_start();
include_once 'config.php';
?>
<!DOCTYPE html>
<html lang="et">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>AutoRent</title>
    <!-- Bootstrap 5 CSS (from CDN) -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
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
                    <a class="btn btn-outline-light btn-sm me-2" href="my_reservations.php">Minu broneeringud</a>
                <a class="btn btn-outline-danger btn-sm" href="logout.php">Logi välja</a>
            <?php else: ?>
                <!-- Bootstrapi modal-akna nupud (Vastavalt meie uuele index.php loogikale) -->
                <button type="button" class="btn btn-outline-light btn-sm me-2" data-bs-toggle="modal" data-bs-target="#loginModal">Logi sisse</button>
                <button type="button" class="btn btn-primary btn-sm fw-bold" data-bs-toggle="modal" data-bs-target="#registerModal">Registreeru</button>
            <?php endif; ?>
        </div>
    </div>
</nav>
<!-- Login and Register modals -->
<!-- Login Modal -->
<div class="modal fade" id="loginModal" tabindex="-1" aria-labelledby="loginModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="loginModalLabel">Logi sisse</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form method="post" action="login.php" autocomplete="off">
            <div class="modal-body">
                <div class="mb-3">
                    <label for="loginUser" class="form-label">Kasutajanimi või e-post</label>
                    <input name="username" type="text" class="form-control" id="loginUser" required>
                </div>
                <div class="mb-3">
                    <label for="loginPass" class="form-label">Parool</label>
                    <input name="password" type="password" class="form-control" id="loginPass" required>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tühista</button>
                <button type="submit" class="btn btn-primary">Logi sisse</button>
            </div>
            </form>
        </div>
    </div>
</div>

<!-- Register Modal -->
<div class="modal fade" id="registerModal" tabindex="-1" aria-labelledby="registerModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="registerModalLabel">Registreeru</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form method="post" action="register.php" autocomplete="off">
            <div class="modal-body">
                <div class="mb-3">
                    <label for="regUser" class="form-label">Kasutajanimi</label>
                    <input name="username" type="text" class="form-control" id="regUser" required>
                </div>
                <div class="mb-3">
                    <label for="regEmail" class="form-label">E-post</label>
                    <input name="email" type="email" class="form-control" id="regEmail" required>
                </div>
                <div class="mb-3">
                    <label for="regPass" class="form-label">Parool</label>
                    <input name="password" type="password" class="form-control" id="regPass" required>
                </div>
                <div class="mb-3">
                    <label for="regPass2" class="form-label">Korda parooli</label>
                    <input name="password2" type="password" class="form-control" id="regPass2" required>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tühista</button>
                <button type="submit" class="btn btn-primary">Registreeru</button>
            </div>
            </form>
        </div>
    </div>
</div>

<?php
// show flash messages (from login/register)
if (!empty($_SESSION['login_error'])) {
        echo '<div class="container"><div class="alert alert-danger">'.htmlspecialchars($_SESSION['login_error']).'</div></div>';
        unset($_SESSION['login_error']);
}
if (!empty($_SESSION['register_error'])) {
        echo '<div class="container"><div class="alert alert-danger">'.htmlspecialchars($_SESSION['register_error']).'</div></div>';
        unset($_SESSION['register_error']);
}
if (!empty($_SESSION['register_success'])) {
        echo '<div class="container"><div class="alert alert-success">'.htmlspecialchars($_SESSION['register_success']).'</div></div>';
        unset($_SESSION['register_success']);
}
?>
<div class="container">
