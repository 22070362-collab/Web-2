<?php
/**
 * Debug script for messages
 */
session_start();

require_once __DIR__ . '/../backend/config/database.php';
require_once __DIR__ . '/../backend/controllers/AuthController.php';
require_once __DIR__ . '/../backend/models/Message.php';

echo "<h2>Debug Messages</h2>";

// Check session
echo "<h3>1. Session Check</h3>";
echo "Session ID: " . session_id() . "<br>";
echo "User ID in session: " . ($_SESSION['user_id'] ?? 'NOT SET') . "<br>";
echo "User Role in session: " . ($_SESSION['user_role'] ?? 'NOT SET') . "<br>";

// Check database connection
echo "<h3>2. Database Connection</h3>";
try {
    $db = getDB();
    echo "Database: Connected OK<br>";
} catch (Exception $e) {
    echo "Database Error: " . $e->getMessage() . "<br>";
}

// Check messages table
echo "<h3>3. Messages Table</h3>";
try {
    $stmt = $db->query("SELECT COUNT(*) as count FROM messages");
    $count = $stmt->fetch();
    echo "Total messages: " . $count['count'] . "<br>";
    
    $stmt = $db->query("SELECT * FROM messages ORDER BY created_at DESC LIMIT 5");
    $messages = $stmt->fetchAll();
    echo "<pre>";
    print_r($messages);
    echo "</pre>";
} catch (Exception $e) {
    echo "Query Error: " . $e->getMessage() . "<br>";
}

// Check getByUser query
echo "<h3>4. Test getByUser Query</h3>";
if (isset($_SESSION['user_id'])) {
    $userId = $_SESSION['user_id'];
    echo "User ID: $userId<br>";
    
    try {
        $stmt = $db->prepare("
            SELECT m.*, 
                   s.username as sender_username, s.full_name as sender_name,
                   r.username as receiver_username, r.full_name as receiver_name
            FROM messages m
            LEFT JOIN users s ON m.sender_id = s.id
            LEFT JOIN users r ON m.receiver_id = r.id
            WHERE m.sender_id = ? OR m.receiver_id = ? OR m.type = 'system'
            ORDER BY m.created_at DESC
            LIMIT 50
        ");
        $stmt->execute([$userId, $userId]);
        $messages = $stmt->fetchAll();
        echo "Found messages: " . count($messages) . "<br>";
        echo "<pre>";
        print_r($messages);
        echo "</pre>";
    } catch (Exception $e) {
        echo "Query Error: " . $e->getMessage() . "<br>";
    }
} else {
    echo "User not logged in (no user_id in session)<br>";
}

// Check countUnread query
echo "<h3>5. Test countUnread Query</h3>";
if (isset($_SESSION['user_id'])) {
    $userId = $_SESSION['user_id'];
    try {
        $stmt = $db->prepare("
            SELECT COUNT(*) as count 
            FROM messages 
            WHERE (receiver_id = ? OR type = 'system') AND is_read = 0
        ");
        $stmt->execute([$userId]);
        $count = $stmt->fetch();
        echo "Unread count: " . $count['count'] . "<br>";
    } catch (Exception $e) {
        echo "Query Error: " . $e->getMessage() . "<br>";
    }
}
?>
