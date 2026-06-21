# USER FLOW - MÂY MƠ BOOK

## MỤC LỤC
1. [Khách vãng lai (Chưa đăng nhập)](#1-khách-vãng-lai-chưa-đăng-nhập)
2. [Người dùng đã đăng nhập](#2-người-dùng-đã-đăng-nhập)
3. [Admin](#3-admin)
4. [Luồng thuê sách chi tiết](#4-luồng-thuê-sách-chi-tiết)
5. [Cron Job tự động](#5-cron-job-tự-động)

---

## 1. KHÁCH VÃNG LAI (Chưa đăng nhập)

```
┌─────────────────────────────────────────────────────────────┐
│  TRANG CHỦ (index.php)                                     │
│                                                             │
│  ┌─────────┐  ┌─────────┐  ┌─────────┐  ┌─────────┐      │
│  │ Xem sách │→ │ Tìm kiếm│→ │ Xem chi │→ │ Đăng ký │      │
│  │  theo   │  │  sách   │  │  tiết   │  │ /Đăng  │      │
│  │danh mục │  │         │  │  sách   │  │  nhập   │      │
│  └─────────┘  └─────────┘  └─────────┘  └─────────┘      │
└─────────────────────────────────────────────────────────────┘
```

### 1.1 Xem sách theo danh mục
```
Trang chủ
  └── Click "Danh Mục" (Tiểu thuyết, Self-help, Khoa học...)
        └── Chuyển đến books.php?category=XYZ
              └── Hiển thị danh sách sách theo thể loại
```

### 1.2 Tìm kiếm sách
```
Trang chủ / Trang sách
  └── Nhập từ khóa vào thanh tìm kiếm
        └── Chuyển đến books.php?search=XYZ
              └── Hiển thị kết quả tìm kiếm
```

### 1.3 Xem chi tiết sách
```
Danh sách sách / Tìm kiếm
  └── Click vào sách
        └── Chuyển đến book-detail.php?id=X
              ├── Xem thông tin sách (tên, tác giả, giá, mô tả)
              ├── Xem đánh giá từ người dùng khác
              └── ⚠️ Muốn thuê phải ĐĂNG NHẬP
                    └── Redirect đến login.php
```

### 1.4 Đăng ký / Đăng nhập
```
Trang đăng ký (register.php)
  └── Điền: username, email, password, full_name, phone, address
        └── Submit → Tạo tài khoản → Redirect login.php

Trang đăng nhập (login.php)
  └── Điền: username, password
        └── Submit → Kiểm tra đăng nhập
              ├── ✅ Thành công → Redirect index.php (đã đăng nhập)
              └── ❌ Thất bại → Hiển thị lỗi
```

---

## 2. NGƯỜI DÙNG ĐÃ ĐĂNG NHẬP

```
┌────────────────────────────────────────────────────────────────────┐
│  MENU SAU KHI ĐĂNG NHẬP                                           │
│                                                                     │
│  🏠 Trang chủ   📚 Sách   🛒 Giỏ hàng   🔔 Thông báo   👤 User    │
│                          (N)                (N)        Menu         │
└────────────────────────────────────────────────────────────────────┘
```

### 2.1 Luồng thuê sách (Chi tiết - xem phần 4)

### 2.2 Xem thông báo
```
Notifications (notifications.php)
  ├── Tab "Hộp thư đến"
  │     ├── Tin nhắn từ Admin
  │     ├── Tin nhắn hệ thống (thuê thành công, nhắc hạn...)
  │     └── Tin chưa đọc → Đánh dấu đã đọc
  │
  └── Tab "Đã gửi"
        └── Tin nhắn đã gửi cho Admin
```

### 2.3 Gửi tin nhắn cho Admin
```
Notifications → Tab "Đã gửi" → Soạn tin mới
  └── Nhập: Tiêu đề, Nội dung
        └── Submit → Lưu vào database (type: user_to_admin)
              └── Admin nhận được trong admin/messages.php
```

### 2.4 Xem Dashboard
```
Dashboard (dashboard.php)
  ├── Tab "Tổng quan"
  │     ├── Số sách đang thuê
  │     ├── Số sách quá hạn
  │     └── Số tin nhắn chưa đọc
  │
  ├── Tab "Đang thuê"
  │     └── Danh sách đơn thuê đang active/pending
  │           ├── Xem mã đơn
  │           ├── Xem ngày hết hạn
  │           └── Yêu cầu trả sách
  │
  └── Tab "Lịch sử"
        └── Danh sách đơn đã trả / đã hủy
```

### 2.5 Đăng xuất
```
Header Menu → Logout
  └── Xóa session → Redirect index.php (khách vãng lai)
```

---

## 3. ADMIN

### 3.1 Đăng nhập Admin
```
login.php
  └── Đăng nhập với tài khoản role='admin'
        └── Redirect đến admin/index.php
```

### 3.2 Admin Dashboard
```
Admin Dashboard (admin/index.php)
  ├── Thống kê tổng quan
  │     ├── Tổng số sách / Tổng sách đang thuê
  │     ├── Tổng người dùng
  │     ├── Đơn thuê theo trạng thái (chờ/active/quá hạn/đã trả)
  │     └── Biểu đồ doanh thu theo tháng
  │
  ├── Quick actions
  │     ├── Đơn chờ xác nhận
  │     ├── Đơn quá hạn
  │     └── Tin nhắn chưa đọc
  │
  └── Thông báo trên topbar
        ├── 🔔 Đơn quá hạn
        └── 📧 Tin nhắn mới
```

### 3.3 Quản lý sách (Admin)
```
Admin → Quản lý sách (admin/books.php)
  ├── Danh sách tất cả sách (bảng)
  │     ├── ID, Tên, Tác giả, Thể loại, Giá, Tồn kho
  │     └── Actions: Sửa | Xóa
  │
  ├── Thêm sách mới
  │     └── Form: Tên, Tác giả, Mô tả, Thể loại, Ảnh bìa, Số lượng, Giá/ngày
  │
  └── Sửa / Xóa sách
        └── Xác nhận trước khi xóa
```

### 3.4 Quản lý thuê sách (Admin) ⭐
```
Admin → Quản lý thuê sách (admin/rentals.php)

┌──────────────────────────────────────────────────────────────┐
│  TÌM KIẾM THEO MÃ ĐƠN                                       │
│  ┌──────────────────────────┐                                │
│  │ Nhập mã đơn (VD: WB9F2A1C) │ 🔍 Tìm kiếm │            │
│  └──────────────────────────┘                                │
│                                                              │
│  Kết quả: Thông tin chi tiết đơn + Actions                  │
└──────────────────────────────────────────────────────────────┘

  ├── Tabs lọc theo trạng thái
  │     ├── Tất cả
  │     ├── Chờ lấy (pending)
  │     ├── Đang thuê (active)
  │     ├── Quá hạn (overdue)
  │     └── Đã trả (returned)
  │
  ├── Actions cho từng đơn
  │     ├── pending → Xác nhận giao sách | Hủy đơn
  │     ├── active  → Xác nhận trả sách
  │     ├── overdue → Xác nhận trả sách
  │     └── returned/cancelled → Xem chi tiết
  │
  └── Thông tin trong bảng
        ├── Mã đơn (rental_code)
        ├── Khách hàng (avatar, tên, username)
        ├── Sách (bìa, tên, tác giả)
        ├── Ngày thuê / Hạn trả
        ├── Tổng tiền
        └── Trạng thái (badge)
```

### 3.5 Quản lý khách hàng (Admin)
```
Admin → Quản lý khách (admin/users.php)
  ├── Danh sách tất cả người dùng
  │     ├── ID, Username, Email, Họ tên, Số điện thoại
  │     ├── Vai trò (user/admin)
  │     └── Actions: Xem chi tiết | Xóa (không xóa được admin)
  │
  └── Xem chi tiết khách hàng
        ├── Thông tin cá nhân
        ├── Lịch sử thuê sách
        └── Gửi tin nhắn cho khách
```

### 3.6 Quản lý tin nhắn (Admin)
```
Admin → Tin nhắn (admin/messages.php)

  ├── Tabs
  │     ├── Hộp thư đến (tin từ user gửi)
  │     ├── Đã gửi (admin gửi cho user)
  │     └── Hệ thống (tin tự động)
  │
  ├── Danh sách tin nhắn
  │     ├── Người gửi, Tiêu đề, Thời gian
  │     └── Badge: Chưa đọc / Đã đọc
  │
  ├── Chi tiết tin nhắn
  │     ├── Nội dung đầy đủ
  │     └── Reply → Soạn tin gửi lại cho user
  │
  └── Actions
        ├── Đánh dấu đã đọc
        ├── Trả lời (gửi cho user)
        └── Xóa tin nhắn
```

---

## 4. LUỒNG THUÊ SÁCH CHI TIẾT

```
╔════════════════════════════════════════════════════════════════════════╗
║                    LUỒNG THUÊ SÁCH HOÀN CHỈNH                        ║
╚════════════════════════════════════════════════════════════════════════╝

 KHÁCH                                    HỆ THỐNG                         ADMIN
   │                                         │                             │
   │  1. Đăng nhập                          │                             │
   │──────────→ Kiểm tra credentials         │                             │
   │            ←─────────── Đăng nhập OK   │                             │
   │                                         │                             │
   │  2. Duyệt / Tìm kiếm sách              │                             │
   │──────────→ SELECT books WHERE ...       │                             │
   │            ←─────────── Danh sách sách  │                             │
   │                                         │                             │
   │  3. Xem chi tiết sách                   │                             │
   │──────────→ SELECT * FROM books WHERE id=X                             │
   │            ←─────────── Thông tin sách   │                             │
   │                                         │                             │
   │  4. Thêm vào giỏ hàng                  │                             │
   │──────────→ INSERT INTO cart            │                             │
   │            ←─────────── Thành công      │                             │
   │                                         │                             │
   │  5. Xem giỏ hàng (cart.php)            │                             │
   │──────────→ SELECT * FROM cart          │                             │
   │            ←─────────── Items + Tổng tiền                             │
   │                                         │                             │
   │  6. Đặt thuê (Checkout)                │                             │
   │──────────→                            │                             │
   │                ┌────────────────────────┼─────────────────────────┐    │
   │                │ 7. Tạo đơn thuê      │                         │    │
   │                │ INSERT rentals (status='pending')                │    │
   │                │ Trừ số lượng sách    │                         │    │
   │                │ UPDATE books SET quantity = quantity - 1         │    │
   │                │ Xóa khỏi giỏ hàng   │                         │    │
   │                │ Tạo mã đơn tự động  │                         │    │
   │                │ (rental_code = WBXXXXXX)                        │    │
   │                │                       │                         │    │
   │                │ 8. Gửi thông báo     │                         │    │
   │                │ INSERT messages (type='system')                 │    │
   │                │ Tiêu đề: "Đặt thuê thành công"               │    │
   │                └────────────────────────┼─────────────────────────┘    │
   │            ←─────────── Đặt thuê thành công! Mã: WBXXXXXX        │
   │            ←─────────── Email thông báo                          │
   │                                         │                             │
   │  9. Mang mã đơn đến cửa hàng          │                             │
   │                                         │  10. Admin xem đơn chờ     │
   │                                         │────────→ SELECT pending     │
   │                                         │         rentals            │
   │                                         │←───────── Danh sách đơn    │
   │                                         │                             │
   │                                         │  11. Xác nhận giao sách    │
   │                                         │←─────── Admin click confirm │
   │                                         │         UPDATE status='active'│
   │                                         │         picked_up_at=NOW()  │
   │            ←─────────── Thông báo:      │                             │
   │               "Thuê sách thành công!"   │                             │
   │            ←─────────── Email thông báo  │                             │
   │                                         │                             │
   │  12. Đọc sách trong thời gian thuê     │                             │
   │                                         │                             │
   │  13. Hết hạn → Mang sách + mã đơn đến  │                             │
   │                                         │                             │
   │                                         │  14. Admin xác nhận trả     │
   │                                         │←─────── Admin click "Trả"   │
   │                                         │         UPDATE status='returned'│
   │                                         │         return_date=NOW()    │
   │                                         │         UPDATE books SET    │
   │                                         │         quantity = quantity + 1│
   │            ←─────────── Thông báo:       │                             │
   │               "Đã xác nhận trả sách!"   │                             │
   │            ←─────────── Email thông báo  │                             │
   │                                         │                             │
   ╰═════════════════════════════════════════════════════════════════════╯

```

### Các trạng thái đơn thuê:
```
┌──────────┬────────────────────────────────────────────────────────┐
│ Trạng thái │ Mô tả                                                │
├──────────┼────────────────────────────────────────────────────────┤
│ pending   │ Đã đặt, chờ admin giao sách                          │
│ active    │ Đang thuê (đã nhận sách)                              │
│ returned  │ Đã trả sách                                           │
│ overdue   │ Quá hạn (vẫn đang thuê nhưng đã hết hạn)             │
│ cancelled │ Đã hủy (admin hủy hoặc khách hủy trước khi nhận sách)│
└──────────┴────────────────────────────────────────────────────────┘
```

---

## 5. CRON JOB TỰ ĐỘNG

```
backend/cron/send_reminders.php (Chạy mỗi ngày 9:00 sáng)

  ┌─────────────────────────────────────────────────────────┐
  │  CRON JOB EXECUTION                                     │
  │                                                          │
  │  1. Cập nhật trạng thái quá hạn                        │
  │     UPDATE rentals SET status='overdue'                  │
  │     WHERE status='active' AND due_date < CURDATE()       │
  │                                                          │
  │  2. Gửi nhắc nhở sắp đến hạn (trong 2 ngày)          │
  │     SELECT rentals WHERE status='active'                 │
  │       AND due_date BETWEEN CURDATE()                     │
  │       AND CURDATE() + 2 days                            │
  │     → INSERT messages (type='system')                   │
  │       Subject: "⏰ Nhắc nhở: Sách sắp đến hạn trả"    │
  │                                                          │
  │  3. Gửi cảnh báo quá hạn                               │
  │     SELECT rentals WHERE status='overdue'                 │
  │     → INSERT messages (type='system')                   │
  │       Subject: "⚠️ Cảnh báo: Sách quá hạn"             │
  │                                                          │
  │  4. Gửi email thông báo cho user                        │
  └─────────────────────────────────────────────────────────┘
```

---

## SƠ ĐỒ QUAN HỆ TRANG

```
                        ┌─────────────┐
                        │  INDEX.PHP  │ ← Trang chủ
                        └──────┬──────┘
                               │
          ┌────────────────────┼────────────────────┐
          │                    │                    │
          ▼                    ▼                    ▼
    ┌──────────┐       ┌──────────┐         ┌──────────┐
    │ BOOKS.PHP │       │BOOK-DETAIL│        │ ABOUT.PHP│
    │(Danh sách)│       │  .PHP    │         └──────────┘
    └─────┬────┘       └─────┬────┘
          │                   │
          │                   ▼
          │            ┌───────────┐
          │            │  CART.PHP │ (cần đăng nhập)
          │            └─────┬─────┘
          │                  │
          ▼                  ▼
    ┌─────────────────────────────┐
    │     BOOK-DETAIL.PHP         │
    │  (Nếu chưa đăng nhập)      │──→ LOGIN.PHP
    └─────────────────────────────┘
                    │
                    ▼
            ┌───────────────┐
            │ REGISTER.PHP  │──→ LOGIN.PHP
            └───────────────┘

    ┌─────────────────────────────────────┐
    │         SAU KHI ĐĂNG NHẬP           │
    └──────┬──────────────────────────────┘
           │
    ┌──────┴──────┐     ┌──────────────┐
    │  INDEX.PHP  │────→│ DASHBOARD.PHP│
    └──────┬──────┘     ├──────────────┤
           │             │  CART.PHP   │
           │             ├──────────────┤
           │             │ NOTIFICATIONS│
           │             │  .PHP       │
           │             ├──────────────┤
           │             │ MESSAGES.PHP │
           │             └──────────────┘
           │
           ▼
    ┌─────────────────────────────┐
    │       ADMIN PANEL          │
    │  (chỉ user có role=admin)  │
    ├─────────────────────────────┤
    │ admin/index.php  - Dashboard│
    │ admin/books.php - QL Sách  │
    │ admin/rentals.php - QL Thuê│
    │ admin/users.php - QL Khách │
    │ admin/messages.php - QL Tin │
    │ admin/settings.php - Cài đặt│
    └─────────────────────────────┘
```

---

## QUY TRÌNH THANH TOÁN

```
1. KHÁCH THÊM SÁCH VÀO GIỎ
   └── Session/Cookie lưu cart items

2. KHÁCH XEM GIỎ HÀNG
   └── Tính tổng tiền tự động
       Tổng = Σ (price_per_day × rental_days × quantity)

3. KHÁCH CHỌN THỜI GIAN THUÊ
   └── 7 ngày / 14 ngày / 30 ngày
       Giá tự động cập nhật theo số ngày

4. KHÁCH NHẬP MÃ GIẢM GIÁ (tùy chọn)
   └── API: apply_coupon
       Kiểm tra: code tồn tại, chưa hết hạn, còn lượt dùng
       Áp dụng: giảm % theo coupon

5. KHÁCH XÁC NHẬN ĐẶT THUÊ
   └── Checkout → Tạo đơn thuê (pending)
       Trừ số lượng sách trong kho
       Xóa items khỏi cart
       Gửi thông báo cho khách

6. ADMIN XÁC NHẬN GIAO SÁCH
   └── pending → active
       Gửi thông báo: "Thuê thành công"

7. KHÁCH TRẢ SÁCH
   └── Admin xác nhận trả: active → returned
       Cộng số lượng sách vào kho
       Gửi thông báo hoàn tất
```
