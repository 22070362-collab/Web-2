-- =====================================================
-- MÂY MƠ BOOK - Complete Database Setup
-- Import vào phpMyAdmin hoặc MySQL CLI
-- =====================================================

-- Tạo database
DROP DATABASE IF EXISTS web2book;
CREATE DATABASE web2book CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE web2book;

-- =====================================================
-- Bảng Users
-- =====================================================
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    email VARCHAR(100) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    full_name VARCHAR(100) NOT NULL,
    phone VARCHAR(20) DEFAULT '',
    address TEXT DEFAULT '',
    avatar VARCHAR(255) DEFAULT NULL,
    role ENUM('user', 'admin') DEFAULT 'user',
    is_verified TINYINT(1) DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_username (username),
    INDEX idx_email (email),
    INDEX idx_role (role)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- Bảng Books
-- =====================================================
CREATE TABLE books (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(200) NOT NULL,
    author VARCHAR(100) NOT NULL,
    description TEXT,
    category VARCHAR(50) NOT NULL,
    cover_image VARCHAR(255) DEFAULT 'default_book.jpg',
    quantity INT DEFAULT 1,
    price_per_day DECIMAL(10,2) DEFAULT 1.00,
    is_available TINYINT(1) DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_title (title),
    INDEX idx_author (author),
    INDEX idx_category (category),
    INDEX idx_available (is_available)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- Bảng Rentals (Thuê sách)
-- =====================================================
CREATE TABLE rentals (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    book_id INT NOT NULL,
    rental_date DATE NOT NULL,
    due_date DATE NOT NULL,
    return_date DATE DEFAULT NULL,
    status ENUM('pending', 'active', 'returned', 'overdue', 'cancelled') DEFAULT 'pending',
    total_price DECIMAL(10,2) DEFAULT 0.00,
    notes TEXT DEFAULT '',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (book_id) REFERENCES books(id) ON DELETE CASCADE,
    INDEX idx_user_id (user_id),
    INDEX idx_book_id (book_id),
    INDEX idx_status (status),
    INDEX idx_rental_date (rental_date),
    INDEX idx_due_date (due_date)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- Bảng Cart (Giỏ hàng)
-- =====================================================
CREATE TABLE cart (
    id INT AUTO_INCREMENT PRIMARY KEY,
    session_id VARCHAR(100) NOT NULL,
    user_id INT DEFAULT NULL,
    book_id INT NOT NULL,
    quantity INT DEFAULT 1,
    rental_days INT DEFAULT 7,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (book_id) REFERENCES books(id) ON DELETE CASCADE,
    INDEX idx_session_id (session_id),
    INDEX idx_user_id (user_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- Bảng Coupons (Mã giảm giá)
-- =====================================================
CREATE TABLE coupons (
    id INT AUTO_INCREMENT PRIMARY KEY,
    code VARCHAR(50) NOT NULL UNIQUE,
    discount_percent INT DEFAULT 10,
    discount_amount DECIMAL(10,2) DEFAULT 0,
    min_order_amount DECIMAL(10,2) DEFAULT 0,
    max_uses INT DEFAULT 100,
    used_count INT DEFAULT 0,
    active TINYINT(1) DEFAULT 1,
    starts_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    expires_at DATETIME NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_code (code),
    INDEX idx_active (active)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- Bảng Wishlist (Sách yêu thích)
-- =====================================================
CREATE TABLE wishlist (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    book_id INT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (book_id) REFERENCES books(id) ON DELETE CASCADE,
    UNIQUE KEY unique_wishlist (user_id, book_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- Bảng Reviews (Đánh giá sách)
-- =====================================================
CREATE TABLE reviews (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    book_id INT NOT NULL,
    rating TINYINT NOT NULL,
    comment TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (book_id) REFERENCES books(id) ON DELETE CASCADE,
    INDEX idx_book_id (book_id),
    INDEX idx_rating (rating)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- Bảng Contacts (Liên hệ)
-- =====================================================
CREATE TABLE contacts (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL,
    subject VARCHAR(100) DEFAULT '',
    message TEXT NOT NULL,
    status ENUM('new', 'read', 'replied') DEFAULT 'new',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_status (status)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- Bảng Messages (Tin nhắn) - BẢNG MỚI
-- =====================================================
CREATE TABLE messages (
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- Dữ liệu mẫu - Users
-- Password: password123 (hash bcrypt)
-- =====================================================
INSERT INTO users (username, email, password, full_name, phone, address, role) VALUES
('admin', 'admin@maymobook.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Administrator', '0901234567', '123 Admin Street, HCMC', 'admin'),
('hung', 'hung@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Nguyễn Văn Hùng', '0902345678', '456 User Street, Hanoi', 'user'),
('user2', 'user2@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Trần Thị B', '0903456789', '789 User Street, Da Nang', 'user');

-- =====================================================
-- Dữ liệu mẫu - Books
-- =====================================================
INSERT INTO books (title, author, description, category, cover_image, quantity, price_per_day) VALUES
-- Tiểu thuyết
('Harry Potter và Hòn đá Phù thủy', 'J.K. Rowling', 'Cuốn sách đầu tiên trong series Harry Potter nổi tiếng. Harry Potter nhận được thư mời từ Hogwarts và khám phá ra mình là một phù thủy.', 'Tiểu thuyết', 'harry_potter_1.jpg', 5, 2.00),
('Harry Potter và Phòng chứa Bí mật', 'J.K. Rowling', 'Năm học thứ hai tại Hogwarts bắt đầu với những bí ẩn đáng sợ khi học sinh bị đá ngạch.', 'Tiểu thuyết', 'harry_potter_2.jpg', 4, 2.00),
('Nhà Giả Kim', 'Paulo Coelho', 'Câu chuyện cảm động về chàng trai Santiago theo dõi giấc mơ của mình.', 'Tiểu thuyết', 'nha_gia_kim.jpg', 6, 1.80),
('Tristan và Isolde', 'Joseph Bédier', 'Tiểu thuyết lãng mạn cổ điển về tình yêu bị ngăn cấm.', 'Tiểu thuyết', 'tristan.jpg', 3, 1.80),

-- Self-help
('Đắc Nhân Tâm', 'Dale Carnegie', 'Cuốn sách kinh điển về nghệ thuật giao tiếp và thuyết phục người khác.', 'Self-help', 'dac_nhan_tam.jpg', 8, 1.50),

-- Truyện ngắn
('Cho Tôi Xin Một Vé Đi Tuổi Thơ', 'Nguyễn Nhật Ánh', 'Hành trình về tuổi thơ qua những kỷ niệm đẹp của chàng trai trưởng thành.', 'Truyện ngắn', 'cho_toi_xin.jpg', 7, 1.50),
('Tôi Thấy Hoa Vàng Trên Cỏ Xanh', 'Nguyễn Nhật Ánh', 'Câu chuyện về tuổi thơ, tình bạn và tình yêu đầu đời.', 'Truyện ngắn', 'hoa_vang.jpg', 8, 1.50),
('Mắt Biếc', 'Nguyễn Nhật Ánh', 'Câu chuyện tình yêu tuổi mới lớn đầy cảm xúc.', 'Truyện ngắn', 'mat_biec.jpg', 7, 1.50),

-- Khoa học
('Sapiens: Lược sử loài người', 'Yuval Noah Harari', 'Một cái nhìn tổng quan về lịch sử loài người từ thuở hồng hoang đến hiện đại.', 'Khoa học', 'sapiens.jpg', 4, 2.50),
('Vũ trụ trong vỏ hạt', 'Stephen Hawking', 'Giới thiệu về vũ trụ, các lỗ đen và bí ẩn của không gian vũ trụ.', 'Khoa học', 'universe.jpg', 3, 2.20),

-- Kỹ năng
('Think and Grow Rich', 'Napoleon Hill', 'Nguyên tắc thành công và làm giàu từ các doanh nhân hàng đầu.', 'Kỹ năng', 'think_grow.jpg', 6, 2.00),
('7 Thói Quen Hiệu Quả', 'Stephen Covey', 'Bảy nguyên tắc giúp bạn sống và làm việc hiệu quả hơn.', 'Kỹ năng', '7_habits.jpg', 5, 1.70),

-- Triết học
('Không Diệt Không Sinh', 'Thích Nhất Hạnh', 'Sách về thiền định và cách sống trong hiện tại.', 'Triết học', 'khong_sinh.jpg', 5, 1.80),

-- Phi hư cấu
('Cuốn Theo Từng Ngày', 'Hành', 'Nhật ký hành trình sống và trải nghiệm.', 'Phi hư cấu', 'cuon_theo.jpg', 4, 1.60),
('Thiên Tài và Sự Nghiệp', 'Nhiều tác giả', 'Tập hợp các câu chuyện về những người thành công.', 'Phi hư cấu', 'thien_tu.jpg', 5, 1.90);

-- =====================================================
-- Dữ liệu mẫu - Rentals
-- =====================================================
INSERT INTO rentals (user_id, book_id, rental_date, due_date, status, total_price, notes) VALUES
(2, 1, DATE_SUB(CURDATE(), INTERVAL 5 DAY), DATE_ADD(CURDATE(), INTERVAL 2 DAY), 'active', 14.00, 'Đang thuê'),
(2, 4, DATE_SUB(CURDATE(), INTERVAL 10 DAY), DATE_ADD(CURDATE(), INTERVAL 4 DAY), 'active', 16.20, 'Đang thuê'),
(3, 2, DATE_SUB(CURDATE(), INTERVAL 15 DAY), DATE_SUB(CURDATE(), INTERVAL 1 DAY), 'overdue', 28.00, 'Quá hạn 1 ngày'),
(3, 3, DATE_SUB(CURDATE(), INTERVAL 30 DAY), DATE_SUB(CURDATE(), INTERVAL 16 DAY), 'returned', 22.50, 'Đã trả đúng hạn'),
(2, 5, DATE_SUB(CURDATE(), INTERVAL 20 DAY), DATE_SUB(CURDATE(), INTERVAL 6 DAY), 'returned', 21.00, 'Đã trả muộn 6 ngày');

-- =====================================================
-- Dữ liệu mẫu - Coupons
-- =====================================================
INSERT INTO coupons (code, discount_percent, min_order_amount, max_uses, expires_at) VALUES
('WELCOME10', 10, 0, 1000, DATE_ADD(CURDATE(), INTERVAL 365 DAY)),
('SUMMER20', 20, 50000, 500, DATE_ADD(CURDATE(), INTERVAL 90 DAY)),
('VIP30', 30, 100000, 100, DATE_ADD(CURDATE(), INTERVAL 180 DAY)),
('FREESHIP', 0, 100000, 1000, DATE_ADD(CURDATE(), INTERVAL 365 DAY));

-- =====================================================
-- Dữ liệu mẫu - Messages (TIN NHẮN)
-- =====================================================
INSERT INTO messages (sender_id, receiver_id, subject, content, type, is_read, created_at) VALUES
-- Tin nhắn user gửi cho admin (user_id = 2 là Hùng)
(2, NULL, 'Xin chào Admin', 'Tôi muốn hỏi về việc thuê sách. Sách Harry Potter có sẵn không?', 'user_to_admin', 0, NOW() - INTERVAL 10 MINUTE),
(2, NULL, 'Gia hạn thuê', 'Tôi muốn gia hạn thêm 7 ngày cho đơn thuê hiện tại. Làm thế nào để thực hiện?', 'user_to_admin', 0, NOW() - INTERVAL 30 MINUTE),
(3, NULL, 'Phản hồi về dịch vụ', 'Cảm ơn cửa hàng đã hỗ trợ nhiệt tình. Tôi rất hài lòng với dịch vụ!', 'user_to_admin', 1, NOW() - INTERVAL 3 DAY),

-- Tin nhắn hệ thống gửi cho user
(NULL, 2, 'Chào mừng đến với MÂY MƠ BOOK', 'Cảm ơn bạn đã đăng ký tài khoản! Chúc bạn có những trải nghiệm thuê sách tuyệt vời.', 'system', 1, NOW() - INTERVAL 1 DAY),
(NULL, 2, 'Đơn thuê đã được xác nhận', 'Đơn thuê sách Harry Potter và Nhà Giả Kim của bạn đã được xác nhận. Vui lòng đến nhận sách trong vòng 24h.', 'system', 1, NOW() - INTERVAL 2 DAY),

-- Tin nhắn admin gửi cho user
(NULL, 2, 'Thông báo: Sách mới', 'Chúng tôi vừa cập nhật thêm nhiều sách mới. Hãy khám phá ngay!', 'admin_to_user', 1, NOW() - INTERVAL 5 DAY),
(NULL, 3, 'Nhắc nhở: Sách quá hạn', 'Bạn có sách đang quá hạn thuê. Vui lòng liên hệ để giải quyết.', 'admin_to_user', 0, NOW() - INTERVAL 1 DAY);

-- =====================================================
-- Cập nhật số lượng sách sau khi thuê
-- =====================================================
UPDATE books SET quantity = quantity - 1 WHERE id IN (1, 2, 3, 4, 5);

-- =====================================================
-- Lưu ý:
-- Password cho tất cả users: password123
-- User admin: admin / admin@maymobook.com
-- User Hùng: hung / hung@example.com
-- User B: user2 / user2@example.com
-- =====================================================
