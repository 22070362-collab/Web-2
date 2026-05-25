-- =====================================================
-- Upgrade Database - Add new features
-- =====================================================

-- Bảng Coupons - Mã giảm giá
CREATE TABLE IF NOT EXISTS coupons (
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

-- Bảng Wishlist - Sách yêu thích
CREATE TABLE IF NOT EXISTS wishlist (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    book_id INT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (book_id) REFERENCES books(id) ON DELETE CASCADE,
    UNIQUE KEY unique_wishlist (user_id, book_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Bảng Reviews - Đánh giá sách
CREATE TABLE IF NOT EXISTS reviews (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    book_id INT NOT NULL,
    rating TINYINT NOT NULL CHECK (rating >= 1 AND rating <= 5),
    comment TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (book_id) REFERENCES books(id) ON DELETE CASCADE,
    INDEX idx_book_id (book_id),
    INDEX idx_rating (rating)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dọn dữ liệu review trùng (giữ bản ghi mới nhất) trước khi thêm unique key cho DB cũ
DELETE r1
FROM reviews r1
INNER JOIN reviews r2
    ON r1.user_id = r2.user_id
   AND r1.book_id = r2.book_id
   AND r1.id < r2.id;

-- Đảm bảo mỗi user chỉ có 1 đánh giá trên mỗi sách
ALTER TABLE reviews
ADD UNIQUE KEY unique_user_book_review (user_id, book_id);

-- Thêm cột phone và address cho bảng users nếu chưa có
ALTER TABLE users 
ADD COLUMN IF NOT EXISTS avatar VARCHAR(255) DEFAULT NULL,
ADD COLUMN IF NOT EXISTS is_verified TINYINT(1) DEFAULT 0;

-- Thêm các trường mới cho bảng rentals nếu chưa có
ALTER TABLE rentals
ADD COLUMN IF NOT EXISTS rental_code VARCHAR(50) DEFAULT NULL,
ADD COLUMN IF NOT EXISTS pickup_deadline DATE DEFAULT NULL,
ADD COLUMN IF NOT EXISTS picked_up_at DATE DEFAULT NULL,
ADD UNIQUE KEY IF NOT EXISTS unique_rental_code (rental_code);

-- Dữ liệu mẫu - Coupons
INSERT INTO coupons (code, discount_percent, min_order_amount, max_uses, expires_at) VALUES
('WELCOME10', 10, 0, 1000, DATE_ADD(CURDATE(), INTERVAL 365 DAY)),
('SUMMER20', 20, 50000, 500, DATE_ADD(CURDATE(), INTERVAL 90 DAY)),
('VIP30', 30, 100000, 100, DATE_ADD(CURDATE(), INTERVAL 180 DAY)),
('FREESHIP', 0, 100000, 1000, DATE_ADD(CURDATE(), INTERVAL 365 DAY))
ON DUPLICATE KEY UPDATE code = VALUES(code);
