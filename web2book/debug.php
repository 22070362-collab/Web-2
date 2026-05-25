<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
?>
<!DOCTYPE html>
<html>
<head>
    <title>Debug Messages</title>
    <style>

        body { font-family: Arial, sans-serif; padding: 20px; background: #1a1a2e; color: #eee; }
        h1, h2, h3 { color: #00d4ff; }
        .success { color: #00ff88; background: #0a3d2a; padding: 10px; border-radius: 5px; margin: 10px 0; }
        .error { color: #ff4444; background: #3d0a0a; padding: 10px; border-radius: 5px; margin: 10px 0; }
        .info { color: #ffcc00; }
        pre { background: #16213e; padding: 15px; border-radius: 5px; overflow-x: auto; }
        table { border-collapse: collapse; width: 100%; margin: 10px 0; }
        th, td { border: 1px solid #333; padding: 10px; text-align: left; }
        th { background: #0f3460; }
        a { color: #00d4ff; }
    </style>
</head>
<body>
<h1>🔍 Debug Messages Page</h1>

<?php
// Step 1: Check session
echo "<h2>1. Session Info</h2>";
session_start();
echo "<div class='info'>";
echo "Session ID: " . session_id() . "<br>";
echo "User ID: " . (isset($_SESSION['user_id']) ? $_SESSION['user_id'] : "<span class='error'>NOT SET</span>") . "<br>";
echo "User Role: " . (isset($_SESSION['user_role']) ? $_SESSION['user_role'] : "<span class='error'>NOT SET</span>") . "<br>";


echo "</div>";

if (!isset($_SESSION['user_id'])) {
    echo "<p class='error'>⚠️ User chưa đăng nhập! Vui lòng đăng nhập trước.</p>";
    echo "<p><a href='frontend/pages/login.php'>Đi đến trang đăng nhập</a></p>";
    exit;
}

$userId = $_SESSION['user_id'];
echo "<p class='success'>✅ User đã đăng nhập với ID: $userId</p>";

// Step 2: Connect to database
echo "<h2>2. Database Connection</h2>";
require_once __DIR__ . '/backend/config/database.php';
try {
    $db = getDB();
    echo "<p class='success'>✅ Kết nối database thành công!</p>";
} catch (Exception $e) {
    echo "<p class='error'>❌ Lỗi kết nối database: " . $e->getMessage() . "</p>";
    exit;
}

// Step 3: Check messages table
echo "<h2>3. Messages Table</h2>";
try {
    $stmt = $db->query("SELECT COUNT(*) as cnt FROM messages");
    $result = $stmt->fetch();
    echo "<p class='info'>Tổng số tin nhắn trong database: " . $result['cnt'] . "</p>";
} catch (Exception $e) {
    echo "<p class='error'>❌ Lỗi: " . $e->getMessage() . "</p>";
}

// Step 4: Test getByUser query
echo "<h2>4. Test getByUser Query (for user ID: $userId)</h2>";
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
    
    echo "<p class='info'>Số tin nhắn tìm thấy cho user này: " . count($messages) . "</p>";
    
    if (count($messages) > 0) {
        echo "<table>";
        echo "<tr><th>ID</th><th>Type</th><th>From</th><th>To</th><th>Subject</th><th>Read</th></tr>";
        foreach ($messages as $m) {
            echo "<tr>";
            echo "<td>{$m['id']}</td>";
            echo "<td>{$m['type']}</td>";
            echo "<td>{$m['sender_name']}</td>";
            echo "<td>{$m['receiver_name']}</td>";
            echo "<td>{$m['subject']}</td>";
            echo "<td>" . ($m['is_read'] ? '✅' : '❌') . "</td>";
            echo "</tr>";
        }
        echo "</table>";
    } else {
        echo "<p class='error'>⚠️ Không có tin nhắn nào cho user này!</p>";
    }
} catch (Exception $e) {
    echo "<p class='error'>❌ Lỗi query: " . $e->getMessage() . "</p>";
}

// Step 5: Count unread
echo "<h2>5. Unread Count</h2>";
try {
    $stmt = $db->prepare("
        SELECT COUNT(*) as cnt 
        FROM messages 
        WHERE (receiver_id = ? OR type = 'system') AND is_read = 0
    ");
    $stmt->execute([$userId]);
    $result = $stmt->fetch();
    echo "<p class='info'>Số tin nhắn chưa đọc: " . $result['cnt'] . "</p>";
} catch (Exception $e) {
    echo "<p class='error'>❌ Lỗi: " . $e->getMessage() . "</p>";
}

// Step 6: Test via Message Model
echo "<h2>6. Test via Message Model</h2>";
try {
    require_once __DIR__ . '/backend/models/Message.php';
    $msgModel = new Message();
    
    $allMsgs = $msgModel->getByUser($userId, 50);
    echo "<p class='info'>Message Model - getByUser(): " . count($allMsgs) . " tin nhắn</p>";
    
    $unread = $msgModel->countUnread($userId);
    echo "<p class='info'>Message Model - countUnread(): $unread tin nhắn chưa đọc</p>";
    
} catch (Exception $e) {
    echo "<p class='error'>❌ Lỗi: " . $e->getMessage() . "</p>";
}

echo "<hr>";
echo "<h2>🔗 Quick Links</h2>";
echo "<p><a href='frontend/pages/messages.php'>Truy cập trang Messages</a></p>";
echo "<p><a href='frontend/pages/login.php'>Đăng nhập lại</a></p>";
?>
</body>
</html>
