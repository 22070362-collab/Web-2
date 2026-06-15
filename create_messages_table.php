<?php
try {
    $pdo = new PDO('mysql:host=localhost;dbname=web2book;charset=utf8mb4', 'root', '123456');
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $sql = "CREATE TABLE IF NOT EXISTS messages (
        id INT AUTO_INCREMENT PRIMARY KEY,
        sender_id INT NULL,
        receiver_id INT NULL,
        subject VARCHAR(255) NOT NULL DEFAULT '',
        content TEXT NOT NULL,
        type ENUM('user_to_admin', 'admin_to_user', 'system') DEFAULT 'user_to_admin',
        is_read TINYINT(1) DEFAULT 0,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        read_at TIMESTAMP NULL,
        INDEX idx_sender (sender_id),
        INDEX idx_receiver (receiver_id),
        INDEX idx_is_read (is_read),
        INDEX idx_type (type),
        INDEX idx_created (created_at)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;";
    $pdo->exec($sql);
    echo "CREATE_OK\n";
    $dataSql = "INSERT INTO messages (sender_id, receiver_id, subject, content, type, is_read, created_at) VALUES
        (2, NULL, 'Xin chào Admin', 'Tôi muốn hỏi về việc thuê sách. Sách Harry Potter có sẵn không?', 'user_to_admin', 0, NOW() - INTERVAL 10 MINUTE),
        (2, NULL, 'Gia hạn thuê', 'Tôi muốn gia hạn thêm 7 ngày cho đơn thuê hiện tại. Làm thế nào để thực hiện?', 'user_to_admin', 0, NOW() - INTERVAL 30 MINUTE),
        (3, NULL, 'Phản hồi về dịch vụ', 'Cảm ơn cửa hàng đã hỗ trợ nhiệt tình. Tôi rất hài lòng với dịch vụ!', 'user_to_admin', 1, NOW() - INTERVAL 3 DAY),
        (NULL, 2, 'Chào mừng đến với MÂY MƠ BOOK', 'Cảm ơn bạn đã đăng ký tài khoản! Chúc bạn có những trải nghiệm thuê sách tuyệt vời.', 'system', 1, NOW() - INTERVAL 1 DAY),
        (NULL, 2, 'Đơn thuê đã được xác nhận', 'Đơn thuê sách Harry Potter và Nhà Giả Kim của bạn đã được xác nhận. Vui lòng đến nhận sách trong vòng 24h.', 'system', 1, NOW() - INTERVAL 2 DAY),
        (NULL, 2, 'Thông báo: Sách mới', 'Chúng tôi vừa cập nhật thêm nhiều sách mới. Hãy khám phá ngay!', 'admin_to_user', 1, NOW() - INTERVAL 5 DAY),
        (NULL, 3, 'Nhắc nhở: Sách quá hạn', 'Bạn có sách đang quá hạn thuê. Vui lòng liên hệ để giải quyết.', 'admin_to_user', 0, NOW() - INTERVAL 1 DAY);";
    $pdo->exec($dataSql);
    echo "INSERT_OK\n";
} catch (Exception $e) {
    echo 'ERROR: ' . $e->getMessage() . "\n";
}
