#!/usr/bin/env php
<?php
/**
 * Cron Job: Gửi thông báo nhắc nhở thuê sách
 * Chạy mỗi ngày lúc 9:00 sáng
 * 
 * Hướng dẫn cài đặt cron:
 * 0 9 * * * /usr/bin/php /Applications/XAMPP/xamppfiles/htdocs/web2book/backend/cron/send_reminders.php
 * 
 * Hoặc chạy thủ công:
 * php /Applications/XAMPP/xamppfiles/htdocs/web2book/backend/cron/send_reminders.php
 */

// Load configuration
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../controllers/RentalController.php';

echo "=== Bắt đầu gửi thông báo nhắc nhở ===\n";
echo "Thời gian: " . date('Y-m-d H:i:s') . "\n\n";

$rentalController = new RentalController();

// 1. Cập nhật trạng thái quá hạn
echo "[1] Cập nhật trạng thái quá hạn...\n";
require_once __DIR__ . '/../models/Rental.php';
$rentalModel = new Rental();
$rentalModel->updateOverdueStatus();
echo "    ✓ Đã cập nhật\n";

// 2. Gửi thông báo sắp đến hạn
echo "\n[2] Gửi thông báo sắp đến hạn...\n";
$dueReminders = $rentalController->sendDueReminders();
echo "    ✓ Đã gửi {$dueReminders} thông báo nhắc nhở\n";

// 3. Gửi thông báo quá hạn
echo "\n[3] Gửi thông báo quá hạn...\n";
$overdueNotifications = $rentalController->sendOverdueNotifications();
echo "    ✓ Đã gửi {$overdueNotifications} thông báo quá hạn\n";

echo "\n=== Hoàn thành ===\n";
echo "Tổng thông báo đã gửi: " . ($dueReminders + $overdueNotifications) . "\n";
