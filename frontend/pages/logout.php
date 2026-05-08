<?php
require_once __DIR__ . '/../../config/config.php';
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Clear session variables
$_SESSION = array();

// Clear remember cookie
if (isset($_COOKIE['remember_token'])) {
    setcookie('remember_token', '', time() - 3600, '/');
}

// Destroy session cookie if it exists
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000,
        $params["path"], $params["domain"],
        $params["secure"], $params["httponly"]
    );
}

session_destroy();
header('Location: login.php?message=logged_out');
exit;
