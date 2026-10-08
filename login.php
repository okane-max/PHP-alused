<?php
session_start();
include_once 'config.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: index.php');
    exit;
}

$username = trim($_POST['username'] ?? $_POST['user'] ?? '');
$password = $_POST['password'] ?? '';

if (empty($username) || empty($password)) {
    $_SESSION['login_error'] = 'Palun täida kõik väljad.';
    header('Location: index.php');
    exit;
}

// prepared statement: allow login by username or email
$stmt = mysqli_prepare($yhendus, 'SELECT id, username, password, role FROM users WHERE username = ? OR email = ? LIMIT 1');
if (!$stmt) {
    $_SESSION['login_error'] = 'Serveri viga.';
    header('Location: index.php');
    exit;
}
mysqli_stmt_bind_param($stmt, 'ss', $username, $username);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$user = mysqli_fetch_assoc($result);

if ($user && password_verify($password, $user['password'])) {
    // success
    $_SESSION['user_id'] = $user['id'];
    $_SESSION['username'] = $user['username'];
    $_SESSION['role'] = $user['role'];
    header('Location: index.php');
    exit;
}

$_SESSION['login_error'] = 'Vale kasutajanimi või parool.';
header('Location: index.php');
exit;
