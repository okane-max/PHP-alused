<?php
// Usage: php create_admin.php username email password
require_once __DIR__ . '/config.php';

if (PHP_SAPI !== 'cli') {
    echo "This script must be run from CLI.\n";
    exit(1);
}

if ($argc < 4) {
    echo "Usage: php create_admin.php <username> <email> <password>\n";
    exit(1);
}

$username = $argv[1];
$email = $argv[2];
$password = $argv[3];

// Check existing
$stmt = mysqli_prepare($yhendus, 'SELECT id FROM users WHERE username = ? OR email = ? LIMIT 1');
mysqli_stmt_bind_param($stmt, 'ss', $username, $email);
mysqli_stmt_execute($stmt);
$res = mysqli_stmt_get_result($stmt);
if (mysqli_fetch_assoc($res)) {
    echo "A user with that username or email already exists.\n";
    exit(1);
}
mysqli_stmt_close($stmt);

$hash = password_hash($password, PASSWORD_DEFAULT);
$role = 'admin';
$stmt = mysqli_prepare($yhendus, 'INSERT INTO users (username, email, password, role, created_at) VALUES (?, ?, ?, ?, NOW())');
mysqli_stmt_bind_param($stmt, 'ssss', $username, $email, $hash, $role);
$ok = mysqli_stmt_execute($stmt);
if ($ok) {
    echo "Admin user created successfully.\n";
    exit(0);
} else {
    echo "Failed to create admin user.\n";
    exit(1);
}
