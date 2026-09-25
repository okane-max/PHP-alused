<?php
// Algatame sessiooni, et süsteem teaks, millist sessiooni sulgeda
session_start();

// 1. Tühjendame kõik sessiooni muutujad (user_id, username, role jne)
$_SESSION = array();

// 2. Kustutame sessiooni küpsise brauserist (turvalisuse huvides kriitiline)
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000,
        $params["path"], $params["domain"],
        $params["secure"], $params["httponly"]
    );
}

// 3. Hävitame sessiooni täielikult serveri poolel
session_destroy();

// 4. Suuname kasutaja tagasi avalehele
header("Location: index.php");
exit();
?>
