<?php
/**
 * Application Configuration
 */

// Site Settings
define('SITE_NAME', 'Web2Book - Cho Thuê Sách Trực Tuyến');
define('SITE_URL', 'http://localhost/web2book');
define('SITE_EMAIL', 'support@web2book.com');

// Currency
define('CURRENCY_SYMBOL', 'đ');
define('CURRENCY_CODE', 'VND');

// Rental Duration Options (in days)
define('RENTAL_DURATIONS', [7, 14, 30]);

// Date Format
define('DATE_FORMAT', 'd/m/Y');
define('DATETIME_FORMAT', 'd/m/Y H:i');

// Pagination
define('ITEMS_PER_PAGE', 20);

// Session
define('SESSION_NAME', 'WEB2BOOK_SESSION');
define('SESSION_LIFETIME', 86400); // 24 hours
$sessionPath = __DIR__ . '/../sessions';
if (!is_dir($sessionPath)) {
    mkdir($sessionPath, 0777, true);
}
session_save_path($sessionPath);
ini_set('session.gc_maxlifetime', SESSION_LIFETIME);
ini_set('session.cookie_lifetime', SESSION_LIFETIME);

// Error Reporting (disable in production)
ini_set('display_errors', 1);
error_reporting(E_ALL);

// Timezone
date_default_timezone_set('Asia/Ho_Chi_Minh');
