<?php
// Quick database check
error_reporting(E_ALL);
ini_set('display_errors', 1);

try {
    $pdo = new PDO("mysql:host=localhost;dbname=web2book;charset=utf8", "root", "", [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
    ]);
    
    echo "✅ Database connected<br>";
    
    // Check users
    $stmt = $pdo->query("SELECT id, username, full_name, role FROM users");
    $users = $stmt->fetchAll();
    echo "<h3>Users in database:</h3>";
    foreach ($users as $u) {
        echo "- ID: {$u['id']}, Username: {$u['username']}, Name: {$u['full_name']}, Role: {$u['role']}<br>";
    }
    
    // Check messages
    $stmt = $pdo->query("SELECT * FROM messages ORDER BY created_at DESC");
    $messages = $stmt->fetchAll();
    echo "<h3>All messages in database:</h3>";
    if (count($messages) > 0) {
        foreach ($messages as $m) {
            echo "- ID: {$m['id']}, From: {$m['sender_id']}, To: {$m['receiver_id']}, Type: {$m['type']}, Subject: {$m['subject']}<br>";
        }
    } else {
        echo "No messages found!<br>";
    }
    
} catch (PDOException $e) {
    echo "❌ Database error: " . $e->getMessage();
}
?>
