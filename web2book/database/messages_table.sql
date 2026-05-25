-- Messages Table for web2book
-- Run this SQL to create the messages table

CREATE TABLE IF NOT EXISTS `messages` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `sender_id` INT NULL,
    `receiver_id` INT NULL,
    `subject` VARCHAR(255) NOT NULL DEFAULT '',
    `content` TEXT NOT NULL,
    `type` ENUM('user_to_admin', 'admin_to_user', 'system') DEFAULT 'user_to_admin',
    `is_read` TINYINT(1) DEFAULT 0,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `read_at` TIMESTAMP NULL,
    INDEX `idx_sender` (`sender_id`),
    INDEX `idx_receiver` (`receiver_id`),
    INDEX `idx_is_read` (`is_read`),
    INDEX `idx_type` (`type`),
    INDEX `idx_created` (`created_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Insert sample messages for demo
INSERT INTO `messages` (`sender_id`, `receiver_id`, `subject`, `content`, `type`, `is_read`, `created_at`) VALUES
(1, NULL, 'Xin chào Admin', 'Tôi muốn hỏi về việc thuê sách. Sách Harry Potter có sẵn không?', 'user_to_admin', 0, NOW() - INTERVAL 10 MINUTE),
(2, NULL, 'Gia hạn thuê', 'Tôi muốn gia hạn thêm 7 ngày cho đơn thuê hiện tại. Làm thế nào để thực hiện?', 'user_to_admin', 0, NOW() - INTERVAL 30 MINUTE),
(NULL, 1, 'Chào mừng đến với MÂY MƠ BOOK', 'Cảm ơn bạn đã đăng ký tài khoản! Chúc bạn có những trải nghiệm thuê sách tuyệt vời.', 'system', 1, NOW() - INTERVAL 1 DAY),
(1, 3, 'Đơn thuê đã được xác nhận', 'Đơn thuê sách của bạn đã được xác nhận. Vui lòng đến nhận sách trong vòng 24h.', 'admin_to_user', 1, NOW() - INTERVAL 2 DAY),
(3, NULL, 'Phản hồi về dịch vụ', 'Cảm ơn cửa hàng đã hỗ trợ nhiệt tình. Tôi rất hài lòng với dịch vụ!', 'user_to_admin', 1, NOW() - INTERVAL 3 DAY);
