<?php
require_once __DIR__ . '/backend/config/database.php';
try {
    $db = getDB();
    $password = password_hash('123456', PASSWORD_DEFAULT);
    $stmt = $db->prepare("UPDATE users SET password = ? WHERE username = 'admin'");
    $stmt->execute([$password]);
    echo "Đã đổi mật khẩu tài khoản admin thành: 123456";
} catch (Exception $e) {
    echo "Lỗi: " . $e->getMessage();
}
?>
