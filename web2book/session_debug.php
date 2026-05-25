<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
session_start();

echo "<h1>Session Debug</h1>";
echo "<pre>";
print_r($_SESSION);
echo "</pre>";

echo "<h2>Session Variables:</h2>";
echo "user_id: " . ($_SESSION['user_id'] ?? 'NOT SET') . "<br>";
echo "user_role: " . ($_SESSION['user_role'] ?? 'NOT SET') . "<br>";
echo "username: " . ($_SESSION['username'] ?? 'NOT SET') . "<br>";

if (isset($_SESSION['user_id'])) {
    echo "<p><a href='frontend/pages/messages.php'>Go to Messages</a></p>";
} else {
    echo "<p><a href='frontend/pages/login.php'>Go to Login</a></p>";
}
?>
