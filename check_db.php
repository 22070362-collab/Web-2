<?php
require_once __DIR__ . '/backend/config/database.php';
try {
    $db = getDB();
    $stmt = $db->query("SELECT username, role FROM users");
    $users = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo "Kết nối thành công!\nDanh sách người dùng:\n";
    print_r($users);
} catch (Exception $e) {
    echo "Lỗi: " . $e->getMessage();
}
?>
