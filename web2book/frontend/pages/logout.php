<?php
session_start();

// Clear remember cookie
if (isset($_COOKIE['remember_token'])) {
    setcookie('remember_token', '', time() - 3600, '/');
}

session_destroy();
header('Location: ../pages/login.php?message=logged_out');
exit;
