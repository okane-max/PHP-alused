<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$host = 'db'; 
$db   = 'autorent';
$user = 'root';
$pass = 'Par00l';
$charset = 'utf8mb4';

$dsn = "mysql:host=$host;dbname=$db;charset=$charset";
$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
];

try {
     $pdo = new PDO($dsn, $user, $pass, $options);
} catch (\PDOException $e) {
     die("Andmebaasi viga: " . $e->getMessage());
}

// Turvalisus: XSS puhastusfunktsioon väljunditele
function sanitize($data) {
    return htmlspecialchars(trim($data), ENT_QUOTES, 'UTF-8');
}
?>
