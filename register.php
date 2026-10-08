<?php
session_start();
include_once 'config.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: index.php');
    exit;
}

$username = trim($_POST['username'] ?? '');
$email = trim($_POST['email'] ?? '');
$password = $_POST['password'] ?? '';
$password2 = $_POST['password2'] ?? '';

if (empty($username) || empty($email) || empty($password) || empty($password2)) {
    $_SESSION['register_error'] = 'Palun täida kõik väljad.';
    header('Location: index.php');
    exit;
}

if ($password !== $password2) {
    $_SESSION['register_error'] = 'Paroolid ei kattu.';
    header('Location: index.php');
    exit;
}

// check existing username or email
$stmt = mysqli_prepare($yhendus, 'SELECT id FROM users WHERE username = ? OR email = ? LIMIT 1');
mysqli_stmt_bind_param($stmt, 'ss', $username, $email);
mysqli_stmt_execute($stmt);
$res = mysqli_stmt_get_result($stmt);
if (mysqli_fetch_assoc($res)) {
    $_SESSION['register_error'] = 'Kasutajanimi või e-post juba kasutusel.';
    header('Location: index.php');
    exit;
}

$hash = password_hash($password, PASSWORD_DEFAULT);
$stmt = mysqli_prepare($yhendus, 'INSERT INTO users (username, email, password, role, created_at) VALUES (?, ?, ?, ?, NOW())');
$role = 'client';
mysqli_stmt_bind_param($stmt, 'ssss', $username, $email, $hash, $role);
$ok = mysqli_stmt_execute($stmt);
if (!$ok) {
    $_SESSION['register_error'] = 'Registreerimisel viga.';
    header('Location: index.php');
    exit;
}

// log in the new user
$uid = mysqli_insert_id($yhendus);
$_SESSION['user_id'] = $uid;
$_SESSION['username'] = $username;
$_SESSION['role'] = $role;
$_SESSION['register_success'] = 'Registreerimine õnnestus. Tere tulemast!';
header('Location: index.php');
exit;
