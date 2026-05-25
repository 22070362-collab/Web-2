# Online Book Rental Website - Website Cho Thuê Sách Trực Tuyến

## Giới thiệu
Website cho thuê sách trực tuyến (Online Book Rental) là nền tảng thương mại điện tử cho phép người dùng tìm kiếm, mượn sách trong khoảng thời gian nhất định và quản lý quá trình trả sách.

## Công nghệ sử dụng
- **Backend**: PHP 7.4+
- **Database**: MySQL
- **Frontend**: HTML5, CSS3, Vanilla JavaScript
- **Server**: XAMPP (Apache + MySQL)

## Cấu trúc thư mục
```
web2book/
├── database/
│   └── schema.sql          # Cấu trúc database
├── backend/
│   ├── config/
│   │   └── database.php    # Kết nối database
│   ├── models/
│   │   ├── User.php
│   │   ├── Book.php
│   │   └── Rental.php
│   ├── controllers/
│   │   ├── AuthController.php
│   │   ├── BookController.php
│   │   └── RentalController.php
│   └── api/
│       └── api.php         # API endpoints
├── frontend/
│   ├── assets/
│   │   ├── css/
│   │   │   └── style.css
│   │   └── js/
│   │       └── main.js
│   ├── pages/
│   │   ├── index.php        # Trang chủ
│   │   ├── books.php        # Danh sách sách
│   │   ├── book-detail.php  # Chi tiết sách
│   │   ├── cart.php         # Giỏ hàng
│   │   ├── checkout.php     # Thanh toán/Đặt thuê
│   │   ├── dashboard.php     # Dashboard người dùng
│   │   ├── login.php        # Đăng nhập
│   │   ├── register.php     # Đăng ký
│   │   ├── logout.php       # Đăng xuất
│   │   └── admin/
│   │       ├── index.php    # Admin Dashboard
│   │       ├── books.php    # Quản lý sách
│   │       ├── rentals.php  # Quản lý thuê/trả
│   │       └── users.php    # Quản lý người dùng
│   ├── templates/
│   │   ├── header.php       # Header template
│   │   └── footer.php       # Footer template
│   └── functions/
│       └── helpers.php      # Các hàm helper
└── config/
    └── config.php           # Cấu hình chung
```

## Tính năng

### Dành cho khách hàng
- [x] Duyệt sách theo thể loại, tác giả
- [x] Tìm kiếm sách theo tên
- [x] Kiểm tra tình trạng sách (còn/hết)
- [x] Đặt đơn thuê với giỏ hàng
- [x] Chọn thời hạn thuê (7 ngày, 14 ngày, 30 ngày)
- [x] Xem lịch sử thuê
- [x] Yêu cầu trả sách

### Dành cho Admin
- [x] Dashboard thống kê
- [x] Quản lý sách (CRUD)
- [x] Quản lý thuê/trả sách
- [x] Quản lý người dùng

## Cách cài đặt

### 1. Import Database
```bash
# Truy cập phpMyAdmin và import file:
database/schema.sql
```

### 2. Cấu hình Database
Chỉnh sửa file `backend/config/database.php`:
```php
$db_host = 'localhost';
$db_name = 'web2book';
$db_user = 'root';
$db_pass = ''; // Mật khẩu MySQL của bạn
```

### 3. Truy cập Website
- Frontend: http://localhost/web2book/frontend/pages/index.php
- Admin: http://localhost/web2book/frontend/pages/admin/

### Tài khoản Demo
- **Admin**: admin / admin123
- **User**: user1 / user123

## Luồng hoạt động

1. **Đăng nhập** -> Tìm sách -> Chọn thời gian thuê -> Xác nhận đặt đơn
2. Hệ thống tự động trừ số lượng trong kho (Quantity - 1)
3. Đến hạn, người dùng trả sách -> Admin xác nhận -> Hệ thống cộng lại kho (Quantity + 1)

## Database Schema

### Bảng Users
| Column | Type | Description |
|--------|------|-------------|
| id | INT (PK) | ID người dùng |
| username | VARCHAR(50) | Tên đăng nhập |
| email | VARCHAR(100) | Email |
| password | VARCHAR(255) | Mật khẩu (hash) |
| full_name | VARCHAR(100) | Họ tên |
| phone | VARCHAR(20) | Số điện thoại |
| address | TEXT | Địa chỉ |
| role | ENUM('user','admin') | Vai trò |
| created_at | TIMESTAMP | Ngày tạo |

### Bảng Books
| Column | Type | Description |
|--------|------|-------------|
| id | INT (PK) | ID sách |
| title | VARCHAR(200) | Tên sách |
| author | VARCHAR(100) | Tác giả |
| description | TEXT | Mô tả |
| category | VARCHAR(50) | Thể loại |
| cover_image | VARCHAR(255) | Đường dẫn ảnh bìa |
| quantity | INT | Số lượng trong kho |
| price_per_day | DECIMAL(10,2) | Giá thuê/ngày |
| created_at | TIMESTAMP | Ngày thêm |

### Bảng Rentals
| Column | Type | Description |
|--------|------|-------------|
| id | INT (PK) | ID thuê |
| user_id | INT (FK) | ID người mượn |
| book_id | INT (FK) | ID sách |
| rental_date | DATE | Ngày mượn |
| due_date | DATE | Ngày phải trả |
| return_date | DATE | Ngày trả thực tế |
| status | ENUM | Trạng thái |
| total_price | DECIMAL(10,2) | Tổng tiền |
| created_at | TIMESTAMP | Ngày tạo |

## Phí thuê
- 7 ngày: Giá cơ bản
- 14 ngày: Giá cơ bản × 1.5
- 30 ngày: Giá cơ bản × 2.5

## License
MIT License
