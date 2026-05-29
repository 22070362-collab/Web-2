<?php
/**
 * Helper Functions
 */

function isLoggedIn() {
    return isset($_SESSION['user_id']);
}

function isAdmin() {
    return isset($_SESSION['role']) && $_SESSION['role'] === 'admin';
}

function getCurrentUserId() {
    return $_SESSION['user_id'] ?? null;
}

function getCurrentUsername() {
    return $_SESSION['username'] ?? null;
}

function getCurrentUserRole() {
    return $_SESSION['role'] ?? null;
}

function formatCurrency($amount) {
    return number_format($amount, 0, ',', '.') . 'đ';
}

function formatDate($date, $format = 'd/m/Y') {
    return date($format, strtotime($date));
}

function formatDateTime($datetime) {
    return date('d/m/Y H:i', strtotime($datetime));
}

function getDaysRemaining($dueDate) {
    $due = strtotime($dueDate);
    $now = time();
    $diff = $due - $now;
    return ceil($diff / (60 * 60 * 24));
}

function getDaysOverdue($dueDate) {
    $due = strtotime($dueDate);
    $now = time();
    $diff = $now - $due;
    return $diff > 0 ? ceil($diff / (60 * 60 * 24)) : 0;
}

function sanitize($string) {
    return htmlspecialchars(trim($string), ENT_QUOTES, 'UTF-8');
}

function generateRandomString($length = 10) {
    return substr(str_shuffle('0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ'), 0, $length);
}

function redirect($url) {
    header('Location: ' . $url);
    exit;
}

function jsonResponse($data) {
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode($data, JSON_UNESCAPED_UNICODE);
    exit;
}

function getStatusLabel($status) {
    $labels = [
        'pending' => 'Chờ xác nhận',
        'active' => 'Đang thuê',
        'returned' => 'Đã trả',
        'overdue' => 'Quá hạn',
        'cancelled' => 'Đã hủy'
    ];
    return $labels[$status] ?? $status;
}

function getStatusClass($status) {
    $classes = [
        'pending' => 'warning',
        'active' => 'success',
        'returned' => 'secondary',
        'overdue' => 'danger',
        'cancelled' => 'secondary'
    ];
    return $classes[$status] ?? 'secondary';
}

function getRentalStatus($rental) {
    if ($rental['status'] === 'returned' || $rental['status'] === 'cancelled') {
        return $rental['status'];
    }
    
    $dueDate = strtotime($rental['due_date']);
    $now = time();
    
    if ($now > $dueDate) {
        return 'overdue';
    }
    
    return $rental['status'];
}

function calculateRentalPrice($pricePerDay, $days) {
    return $pricePerDay * $days;
}

function calculateLateFee($pricePerDay, $daysOverdue) {
    return $pricePerDay * $daysOverdue * 1.5; // 1.5x price per day for late fee
}

/**
 * Get book cover image URL
 * Returns local cover if exists, otherwise returns default
 */
function getBookCoverImage($book) {
    $coverImage = $book['cover_image'] ?? '';
    $bookId = $book['id'] ?? 0;
    $bookTitle = $book['title'] ?? 'No Cover';

    // If cover_image is set and file exists, use it
    if (!empty($coverImage)) {
        $checkPath = __DIR__ . '/../assets/images/' . $coverImage;
        if (file_exists($checkPath)) {
            return '../assets/images/' . htmlspecialchars($coverImage, ENT_QUOTES, 'UTF-8');
        }
    }

    // Fallback: placeholder with book title (works from any page depth)
    return "https://placehold.co/300x400/8B4513/FFF?text=" . urlencode($bookTitle);
}

/**
 * Time ago function - converts timestamp to relative time
 */
function timeAgo($datetime) {
    $time = strtotime($datetime);
    $diff = time() - $time;
    
    if ($diff < 60) {
        return 'Vừa xong';
    } elseif ($diff < 3600) {
        $mins = floor($diff / 60);
        return $mins . ' phút trước';
    } elseif ($diff < 86400) {
        $hours = floor($diff / 3600);
        return $hours . ' giờ trước';
    } elseif ($diff < 604800) {
        $days = floor($diff / 86400);
        return $days . ' ngày trước';
    } else {
        return date('d/m/Y', $time);
    }
}
