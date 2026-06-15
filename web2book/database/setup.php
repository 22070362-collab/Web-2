<?php
/**
 * Database Setup Script - MÂY MƠ BOOK
 */

$dbHost = 'localhost';
$dbUser = 'root';
$dbPass = '123456';
$dbName = 'web2book';

echo "=== MÂY MƠ BOOK - Database Setup ===\n\n";

try {
    $pdo = new PDO("mysql:host=$dbHost", $dbUser, $dbPass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    echo "[1/5] Kết nối MySQL thành công\n";
    
    $pdo->exec("DROP DATABASE IF EXISTS $dbName");
    echo "[2/5] Xóa database cũ (nếu có)\n";
    
    $pdo->exec("CREATE DATABASE $dbName CHARACTER SET utf8 COLLATE utf8_unicode_ci");
    $pdo->exec("USE $dbName");
    echo "[3/5] Tạo database '$dbName' thành công\n";
    
    // Tạo bảng
    $pdo->exec("
        CREATE TABLE users (
            id INT AUTO_INCREMENT PRIMARY KEY,
            username VARCHAR(50) NOT NULL UNIQUE,
            email VARCHAR(100) NOT NULL UNIQUE,
            password VARCHAR(255) NOT NULL,
            full_name VARCHAR(100) NOT NULL,
            phone VARCHAR(20) DEFAULT '',
            address VARCHAR(255) DEFAULT '',
            avatar VARCHAR(255) DEFAULT NULL,
            role ENUM('user', 'admin') DEFAULT 'user',
            is_verified TINYINT(1) DEFAULT 0,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            INDEX idx_username (username),
            INDEX idx_email (email),
            INDEX idx_role (role)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci
    ");
    echo "[4/5] Tạo bảng thành công\n";
    
    $pdo->exec("
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
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci
    ");
    
    $pdo->exec("
        CREATE TABLE rentals (
            id INT AUTO_INCREMENT PRIMARY KEY,
            user_id INT NOT NULL,
            book_id INT NOT NULL,
            rental_date DATE NOT NULL,
            due_date DATE NOT NULL,
            return_date DATE DEFAULT NULL,
            status ENUM('pending', 'active', 'returned', 'overdue', 'cancelled') DEFAULT 'pending',
            total_price DECIMAL(10,2) DEFAULT 0.00,
            notes VARCHAR(500) DEFAULT '',
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
            FOREIGN KEY (book_id) REFERENCES books(id) ON DELETE CASCADE,
            INDEX idx_user_id (user_id),
            INDEX idx_book_id (book_id),
            INDEX idx_status (status)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci
    ");
    
    $pdo->exec("
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
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci
    ");
    
    $pdo->exec("
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
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci
    ");
    
    $pdo->exec("
        CREATE TABLE wishlist (
            id INT AUTO_INCREMENT PRIMARY KEY,
            user_id INT NOT NULL,
            book_id INT NOT NULL,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
            FOREIGN KEY (book_id) REFERENCES books(id) ON DELETE CASCADE,
            UNIQUE KEY unique_wishlist (user_id, book_id)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci
    ");
    
    $pdo->exec("
        CREATE TABLE reviews (
            id INT AUTO_INCREMENT PRIMARY KEY,
            user_id INT NOT NULL,
            book_id INT NOT NULL,
            rating TINYINT NOT NULL,
            comment TEXT,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
            FOREIGN KEY (book_id) REFERENCES books(id) ON DELETE CASCADE,
            UNIQUE KEY unique_user_book_review (user_id, book_id),
            INDEX idx_book_id (book_id),
            INDEX idx_rating (rating)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci
    ");
    
    $pdo->exec("
        CREATE TABLE contacts (
            id INT AUTO_INCREMENT PRIMARY KEY,
            name VARCHAR(100) NOT NULL,
            email VARCHAR(100) NOT NULL,
            subject VARCHAR(100) DEFAULT '',
            message TEXT NOT NULL,
            status ENUM('new', 'read', 'replied') DEFAULT 'new',
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            INDEX idx_status (status)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci
    ");
    
    echo "\n[5/5] Import dữ liệu mẫu...\n";
    
    // Insert Users
    $password = password_hash('password123', PASSWORD_DEFAULT);
    $pdo->prepare("INSERT INTO users (username, email, password, full_name, phone, address, role) VALUES (?, ?, ?, ?, ?, ?, ?)")->execute(
        ['admin', 'admin@maymobook.com', $password, 'Administrator', '0901234567', '123 Admin Street, HCMC', 'admin']
    );
    $pdo->prepare("INSERT INTO users (username, email, password, full_name, phone, address, role) VALUES (?, ?, ?, ?, ?, ?, ?)")->execute(
        ['user1', 'user1@example.com', $password, 'Nguyễn Văn A', '0902345678', '456 User Street, Hanoi', 'user']
    );
    $pdo->prepare("INSERT INTO users (username, email, password, full_name, phone, address, role) VALUES (?, ?, ?, ?, ?, ?, ?)")->execute(
        ['user2', 'user2@example.com', $password, 'Trần Thị B', '0903456789', '789 User Street, Da Nang', 'user']
    );
    echo "     - Đã thêm 3 users\n";
    
    // Insert Books
    $books = [
        ['Harry Potter và Hòn đá Phù thủy', 'J.K. Rowling', 'Cuốn sách đầu tiên trong series Harry Potter nổi tiếng.', 'Tiểu thuyết', 'harry_potter_1.jpg', 5, 2.00],
        ['Harry Potter và Phòng chứa Bí mật', 'J.K. Rowling', 'Năm học thứ hai tại Hogwarts với những bí ẩn đáng sợ.', 'Tiểu thuyết', 'harry_potter_2.jpg', 4, 2.00],
        ['Đắc Nhân Tâm', 'Dale Carnegie', 'Cuốn sách kinh điển về nghệ thuật giao tiếp.', 'Self-help', 'dac_nhan_tam.jpg', 8, 1.50],
        ['Nhà Giả Kim', 'Paulo Coelho', 'Câu chuyện cảm động về chàng trai Santiago.', 'Tiểu thuyết', 'nha_gia_kim.jpg', 6, 1.80],
        ['Cho Tôi xin Một Vé Đi Tuổi Thơ', 'Nguyễn Nhật Ánh', 'Hành trình về tuổi thơ qua những kỷ niệm đẹp.', 'Truyện ngắn', 'cho_toi_xin.jpg', 7, 1.50],
        ['Sapiens: Lược sử loài người', 'Yuval Noah Harari', 'Cái nhìn tổng quan về lịch sử loài người.', 'Khoa học', 'sapiens.jpg', 4, 2.50],
        ['Vũ trụ trong vỏ hạt', 'Stephen Hawking', 'Giới thiệu về vũ trụ và các lỗ đen.', 'Khoa học', 'universe.jpg', 3, 2.20],
        ['Không Diệt Không Sinh', 'Thích Nhất Hạnh', 'Sách về thiền định và cách sống trong hiện tại.', 'Triết học', 'khong_sinh.jpg', 5, 1.80],
        ['Think and Grow Rich', 'Napoleon Hill', 'Nguyên tắc thành công và làm giàu.', 'Kỹ năng', 'think_grow.jpg', 6, 2.00],
        ['7 Thói Quen Hiệu Quả', 'Stephen Covey', 'Bảy nguyên tắc giúp bạn sống hiệu quả hơn.', 'Kỹ năng', '7_habits.jpg', 5, 1.70],
        ['Tôi Thấy Hoa Vàng Trên Cỏ Xanh', 'Nguyễn Nhật Ánh', 'Câu chuyện về tuổi thơ, tình bạn và tình yêu.', 'Truyện ngắn', 'hoa_vang.jpg', 8, 1.50],
        ['Mắt Biếc', 'Nguyễn Nhật Ánh', 'Câu chuyện tình yêu tuổi mới lớn.', 'Truyện ngắn', 'mat_biec.jpg', 7, 1.50],
        ['Tristan và Isolde', 'Joseph Bédier', 'Tiểu thuyết lãng mạn cổ điển.', 'Tiểu thuyết', 'tristan.jpg', 3, 1.80],
        ['Cuốn Theo Từng Ngày', 'Hành', 'Nhật ký hành trình sống và trải nghiệm.', 'Phi hư cấu', 'cuon_theo.jpg', 4, 1.60],
        ['Thiên Tài và Sự Nghiệp', 'Nhiều tác giả', 'Tập hợp các câu chuyện về những người thành công.', 'Phi hư cấu', 'thien_tu.jpg', 5, 1.90],
    ];
    
    $stmt = $pdo->prepare("INSERT INTO books (title, author, description, category, cover_image, quantity, price_per_day) VALUES (?, ?, ?, ?, ?, ?, ?)");
    foreach ($books as $book) {
        $stmt->execute($book);
    }
    echo "     - Đã thêm " . count($books) . " books\n";
    
    // Insert Rentals
    $pdo->prepare("INSERT INTO rentals (user_id, book_id, rental_date, due_date, status, total_price, notes) VALUES (?, ?, ?, ?, ?, ?, ?)")->execute(
        [2, 1, date('Y-m-d', strtotime('-5 days')), date('Y-m-d', strtotime('+2 days')), 'active', 14.00, 'Đang thuê']
    );
    $pdo->prepare("INSERT INTO rentals (user_id, book_id, rental_date, due_date, status, total_price, notes) VALUES (?, ?, ?, ?, ?, ?, ?)")->execute(
        [2, 4, date('Y-m-d', strtotime('-10 days')), date('Y-m-d', strtotime('+4 days')), 'active', 16.20, 'Đang thuê']
    );
    $pdo->prepare("INSERT INTO rentals (user_id, book_id, rental_date, due_date, status, total_price, notes) VALUES (?, ?, ?, ?, ?, ?, ?)")->execute(
        [3, 2, date('Y-m-d', strtotime('-15 days')), date('Y-m-d', strtotime('-1 day')), 'overdue', 28.00, 'Quá hạn']
    );
    echo "     - Đã thêm 3 rentals\n";
    
    // Insert Coupons
    $coupons = [
        ['WELCOME10', 10, 365],
        ['SUMMER20', 20, 90],
        ['VIP30', 30, 180],
        ['FREESHIP', 0, 365],
    ];
    $stmt = $pdo->prepare("INSERT INTO coupons (code, discount_percent, max_uses, expires_at) VALUES (?, ?, 1000, DATE_ADD(NOW(), INTERVAL ? DAY))");
    foreach ($coupons as $c) {
        $stmt->execute($c);
    }
    echo "     - Đã thêm " . count($coupons) . " coupons\n";
    
    echo "\n========================================\n";
    echo "✅ SETUP THÀNH CÔNG!\n";
    echo "========================================\n\n";
    echo "📋 Thông tin đăng nhập:\n";
    echo "   Admin: admin / password123\n";
    echo "   User:  user1 / password123\n";
    echo "   User:  user2 / password123\n\n";
    echo "🌐 Truy cập website:\n";
    echo "   http://localhost/web2book/frontend/\n\n";
    echo "📊 Database: $dbName\n";
    echo "   - users (3)\n";
    echo "   - books (" . count($books) . ")\n";
    echo "   - rentals (3)\n";
    echo "   - coupons (" . count($coupons) . ")\n";
    echo "   - cart, wishlist, reviews, contacts\n";
    echo "========================================\n";
    
} catch (PDOException $e) {
    echo "❌ LỖI: " . $e->getMessage() . "\n";
}
