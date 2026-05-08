# MÂY MƠ BOOK - Online Book Rental Platform

## Giới thiệu
Website cho thuê sách trực tuyến - cho phép người dùng tìm kiếm, thuê sách theo ngày và quản lý quá trình trả sách.

## Công nghệ
- **Backend**: PHP 7.4+ (MVC)
- **Database**: MySQL
- **Frontend**: HTML5, CSS3, Vanilla JavaScript
- **Server**: XAMPP (Apache + MySQL)

## Cấu trúc dự án

```
web2book/
├── backend/
│   ├── api/
│   │   └── api.php           # REST API endpoint
│   ├── config/
│   │   └── database.php      # Kết nối MySQL (PDO)
│   ├── controllers/
│   │   ├── AuthController.php    # Đăng nhập/đăng ký
│   │   ├── BookController.php   # CRUD sách
│   │   ├── RentalController.php # Quản lý thuê/trả
│   │   └── MessageController.php # Tin nhắn user↔admin
│   ├── models/
│   │   ├── User.php          # Người dùng
│   │   ├── Book.php          # Sách
│   │   ├── Rental.php        # Đơn thuê
│   │   ├── Cart.php          # Giỏ hàng
│   │   ├── Message.php       # Tin nhắn
│   │   └── Review.php        # Đánh giá sách
│   └── cron/
│       └── send_reminders.php # Cron nhắc hạn/thông báo
├── database/
│   └── web2book_complete.sql # Schema + dữ liệu mẫu
├── frontend/
│   ├── assets/
│   │   ├── css/
│   │   │   ├── style.css    # Giao diện người dùng
│   │   │   └── admin.css    # Giao diện admin
│   │   ├── images/          # Ảnh bìa sách
│   │   └── js/
│   │       └── main.js      # JavaScript tương tác
│   ├── functions/
│   │   └── helpers.php      # Hàm tiện ích
│   ├── pages/
│   │   ├── index.php        # Trang chủ
│   │   ├── books.php        # Danh sách sách
│   │   ├── book-detail.php  # Chi tiết sách
│   │   ├── cart.php         # Giỏ hàng
│   │   ├── dashboard.php    # Dashboard người dùng
│   │   ├── messages.php     # Tin nhắn user
│   │   ├── notifications.php # Thông báo
│   │   ├── login.php        # Đăng nhập
│   │   ├── register.php     # Đăng ký
│   │   ├── logout.php       # Đăng xuất
│   │   ├── about.php        # Giới thiệu
│   │   ├── contact.php      # Liên hệ
│   │   ├── faq.php          # FAQ
│   │   └── admin/
│   │       ├── index.php    # Admin dashboard
│   │       ├── books.php    # Quản lý sách
│   │       ├── rentals.php  # Quản lý thuê/trả
│   │       ├── users.php    # Quản lý khách
│   │       ├── messages.php # Quản lý tin nhắn
│   │       └── settings.php # Cài đặt
│   └── templates/
│       ├── header.php       # Header người dùng
│       ├── footer.php       # Footer người dùng
│       ├── admin_header.php # Header admin
│       └── admin_footer.php # Footer admin
└── config/
    └── config.php           # Cấu hình ứng dụng
```

## Cách cài đặt

### 1. Import Database
Truy cập phpMyAdmin và import file:
```
database/web2book_complete.sql
```

### 2. Cấu hình Database
File: `backend/config/database.php`
```php
define('DB_HOST', 'localhost');
define('DB_NAME', 'web2book');
define('DB_USER', 'root');
define('DB_PASS', ''); // mật khẩu MySQL
```

### 3. Truy cập Website
- Frontend: `http://localhost/web2book/frontend/pages/index.php`
- Admin: `http://localhost/web2book/frontend/pages/admin/`

### Tài khoản Demo
| Vai trò | Username | Password |
|---------|----------|----------|
| Admin | admin | password123 |
| User | hung | password123 |

## Tính năng chính

### Người dùng
- Duyệt sách theo thể loại, tìm kiếm
- Giỏ hàng + đặt thuê
- Chọn thời hạn (7/14/30 ngày)
- Xem lịch sử thuê, trả sách
- Tin nhắn liên hệ admin
- Đánh giá sách sau thuê
- Wishlist (yêu thích)
- Mã giảm giá (coupon)

### Admin
- Dashboard thống kê + biểu đồ doanh thu
- CRUD sách
- Duyệt đơn thuê (xác nhận giao/trả, hủy)
- Tìm kiếm đơn theo mã
- Quản lý khách hàng
- Quản lý tin nhắn
- Cài đặt hệ thống

## Luồng hoạt động

1. User đăng nhập → chọn sách → thêm giỏ hàng → đặt thuê
2. Hệ thống tạo đơn (status: pending) → trừ số lượng trong kho
3. Admin xác nhận giao sách → status: active
4. Đến hạn → user trả sách → admin xác nhận → status: returned → cộng lại kho
5. Cron job tự động gửi nhắc hạn (2 ngày trước) và cảnh báo quá hạn

## Cron Job (nhắc nhở tự động)
```bash
# Chạy mỗi ngày lúc 9:00 sáng
0 9 * * * /usr/bin/php /Applications/XAMPP/xamppfiles/htdocs/web2book/backend/cron/send_reminders.php
```
# WEB-2
