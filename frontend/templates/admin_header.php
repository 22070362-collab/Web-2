<?php
ob_start();
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/../../backend/config/database.php';
require_once __DIR__ . '/../../frontend/functions/helpers.php';
require_once __DIR__ . '/../../backend/controllers/AuthController.php';
require_once __DIR__ . '/../../backend/controllers/MessageController.php';

$auth = new AuthController();
$isLoggedIn = $auth->isLoggedIn();
$isAdmin = $auth->isAdmin();
$user = $auth->getCurrentUser();

// Remember Me: Auto-login from cookie if no session
if (!$isLoggedIn && isset($_COOKIE['remember_token'])) {
    require_once __DIR__ . '/../../backend/models/User.php';
    $userModel = new User();
    $rememberUser = $userModel->findByRememberToken($_COOKIE['remember_token']);
    
    if ($rememberUser) {
        $_SESSION['user_id'] = $rememberUser['id'];
        $_SESSION['username'] = $rememberUser['username'];
        $_SESSION['role'] = $rememberUser['role'];
        $_SESSION['full_name'] = $rememberUser['full_name'];
        
        $isLoggedIn = true;
        $isAdmin = ($rememberUser['role'] === 'admin');
        $user = $rememberUser;
    }
}

if (!$isLoggedIn || !$isAdmin) {
    if (!$isLoggedIn) {
        header('Location: ../login.php');
    } else {
        header('Location: ../index.php');
    }
    exit;
}

// Get message notifications for admin
$messageController = new MessageController();
$messageData = $messageController->getNotificationsForAdmin(5);
$unreadMessagesList = $messageData['messages'];
$unreadCount = $messageData['unread_count'];

// Get overdue rentals count for notifications
require_once __DIR__ . '/../../backend/controllers/RentalController.php';
$rentalController = new RentalController();
$overdueRentals = $rentalController->getOverdue();
$overdueCount = count($overdueRentals);

$currentPage = basename($_SERVER['PHP_SELF']);
$currentDir = basename(dirname($_SERVER['PHP_SELF']));

function adminUiMessage($message) {
    $messages = [
        'Vui lòng nhập đầy đủ thông tin bắt buộc' => 'Please fill in all required information.',
        'Thêm sách thành công' => 'Book added successfully.',
        'Thêm sách thất bại' => 'Failed to add book.',
        'Cập nhật sách thành công' => 'Book updated successfully.',
        'Cập nhật sách thất bại' => 'Failed to update book.',
        'Xóa sách thành công' => 'Book deleted successfully.',
        'Xóa sách thất bại' => 'Failed to delete book.',
        'Xác nhận trả sách thành công' => 'Book return confirmed successfully.',
        'Không thể xác nhận trả sách' => 'Unable to confirm this return.',
        'Không tìm thấy đơn thuê' => 'Rental order not found.',
        'Đã xác nhận giao sách cho khách' => 'Book pickup confirmed successfully.',
        'Không thể xác nhận giao sách' => 'Unable to confirm book pickup.',
        'Hủy đơn thuê thành công' => 'Rental order cancelled successfully.',
        'Không thể hủy đơn thuê' => 'Unable to cancel this rental order.',
        'Tin nhắn đã được gửi thành công' => 'Message sent successfully.',
        'Gửi tin nhắn thất bại' => 'Failed to send message.',
        'Xóa tin nhắn thành công' => 'Message deleted successfully.',
        'Không thể xóa tin nhắn' => 'Unable to delete this message.',
        'Phương thức không hợp lệ' => 'Invalid request method.',
        'Vui lòng nhập đầy đủ tiêu đề và nội dung' => 'Please enter both subject and content.',
        'Vui lòng nhập đầy đủ thông tin' => 'Please fill in all required information.',
    ];

    return $messages[$message] ?? $message;
}

function adminTimeAgo($datetime) {
    $time = strtotime($datetime);
    $diff = time() - $time;

    if ($diff < 60) {
        return 'Just now';
    }

    if ($diff < 3600) {
        $mins = floor($diff / 60);
        return $mins . ' minute' . ($mins === 1 ? '' : 's') . ' ago';
    }

    if ($diff < 86400) {
        $hours = floor($diff / 3600);
        return $hours . ' hour' . ($hours === 1 ? '' : 's') . ' ago';
    }

    if ($diff < 604800) {
        $days = floor($diff / 86400);
        return $days . ' day' . ($days === 1 ? '' : 's') . ' ago';
    }

    return date('M d, Y', $time);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo isset($pageTitle) ? htmlspecialchars($pageTitle) . ' - ' : ''; ?>Admin - BookRent</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="../../assets/css/admin.css?v=20260515-beige-ui">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body>
    <!-- Sidebar -->
    <aside class="sidebar">
        <div class="sidebar-header">
            <a href="index.php" class="sidebar-logo">
                <div class="sidebar-logo-icon">
                    <i class="fas fa-book-open"></i>
                </div>
                <span class="sidebar-logo-text">MAY MO <span>BOOK</span></span>
            </a>
        </div>
        
        <nav class="sidebar-nav">
            <div class="sidebar-nav-section">
                <span class="sidebar-nav-label">Management</span>
                <a href="index.php" class="sidebar-nav-item <?php echo $currentPage == 'index.php' ? 'active' : ''; ?>">
                    <i class="fas fa-chart-pie"></i>
                    <span>Dashboard</span>
                </a>
                <a href="books.php" class="sidebar-nav-item <?php echo $currentPage == 'books.php' ? 'active' : ''; ?>">
                    <i class="fas fa-book"></i>
                    <span>Books</span>
                </a>
                <a href="rentals.php" class="sidebar-nav-item <?php echo $currentPage == 'rentals.php' ? 'active' : ''; ?>">
                    <i class="fas fa-exchange-alt"></i>
                    <span>Rentals</span>
                </a>
                <a href="users.php" class="sidebar-nav-item <?php echo $currentPage == 'users.php' ? 'active' : ''; ?>">
                    <i class="fas fa-users"></i>
                    <span>Users</span>
                </a>
                <a href="messages.php" class="sidebar-nav-item <?php echo $currentPage == 'messages.php' ? 'active' : ''; ?>">
                    <i class="fas fa-envelope"></i>
                    <span>Messages</span>
                    <?php if ($unreadCount > 0): ?>
                    <span class="sidebar-badge"><?php echo $unreadCount; ?></span>
                    <?php endif; ?>
                </a>
            </div>
            
            <div class="sidebar-nav-section">
                <span class="sidebar-nav-label">Settings</span>
                <a href="settings.php" class="sidebar-nav-item <?php echo $currentPage == 'settings.php' ? 'active' : ''; ?>">
                    <i class="fas fa-cog"></i>
                    <span>Settings</span>
                </a>
                <a href="../logout.php" class="sidebar-nav-item">
                    <i class="fas fa-sign-out-alt"></i>
                    <span>Logout</span>
                </a>
            </div>
        </nav>
        
        <div class="sidebar-footer">
            <div class="sidebar-admin-card">
                <div class="sidebar-admin-avatar">
                    <?php echo strtoupper(substr($user['full_name'] ?? 'A', 0, 1)); ?>
                </div>
                <div class="sidebar-admin-info">
                    <div class="sidebar-admin-name"><?php echo htmlspecialchars($user['full_name'] ?? 'Admin'); ?></div>
                    <div class="sidebar-admin-role">Administrator</div>
                </div>
            </div>
        </div>
    </aside>
    
    <!-- Main Content -->
    <div class="main-content">
        <!-- Top Bar -->
        <header class="topbar">
            <div class="topbar-left">
                <button class="topbar-toggle" id="sidebarToggle">
                    <i class="fas fa-bars"></i>
                </button>
                <div class="topbar-search">
                    <i class="fas fa-search"></i>
                    <input type="text" placeholder="Search...">
                </div>
            </div>
            
            <div class="topbar-right">
                <!-- Notifications Dropdown -->
                <div class="topbar-dropdown">
                    <button class="topbar-icon-btn" id="notificationBtn">
                        <i class="fas fa-bell"></i>
                        <?php if ($overdueCount > 0): ?>
                        <span class="topbar-badge"><?php echo $overdueCount; ?></span>
                        <?php endif; ?>
                    </button>
                    <div class="topbar-dropdown-menu" id="notificationMenu">
                        <div class="dropdown-header">
                            <span>Notifications</span>
                            <a href="#" onclick="markAllNotificationsRead(); return false;">Mark all as read</a>
                        </div>
                        <div class="dropdown-list">
                            <?php if ($overdueCount > 0): ?>
                            <a href="rentals.php?status=overdue" class="dropdown-item-notif unread">
                                <div class="notif-icon warning"><i class="fas fa-exclamation-circle"></i></div>
                                <div class="notif-content">
                                    <div class="notif-text"><?php echo $overdueCount; ?> rentals are overdue</div>
                                    <div class="notif-time">Needs action now</div>
                                </div>
                            </a>
                            <?php endif; ?>
                            <?php if ($unreadCount > 0): ?>
                            <a href="messages.php" class="dropdown-item-notif unread">
                                <div class="notif-icon info"><i class="fas fa-envelope"></i></div>
                                <div class="notif-content">
                                    <div class="notif-text"><?php echo $unreadCount; ?> new messages from users</div>
                                    <div class="notif-time">Unread</div>
                                </div>
                            </a>
                            <?php endif; ?>
                            <?php if ($overdueCount == 0 && $unreadCount == 0): ?>
                            <div class="dropdown-empty">
                                <i class="fas fa-check-circle"></i>
                                <span>No new notifications</span>
                            </div>
                            <?php endif; ?>
                        </div>
                        <div class="dropdown-footer">
                            <a href="rentals.php?status=overdue">View overdue rentals</a>
                        </div>
                    </div>
                </div>
                
                <!-- Messages Dropdown -->
                <div class="topbar-dropdown">
                    <button class="topbar-icon-btn" id="messageBtn">
                        <i class="fas fa-envelope"></i>
                        <?php if ($unreadCount > 0): ?>
                        <span class="topbar-badge"><?php echo $unreadCount; ?></span>
                        <?php endif; ?>
                    </button>
                    <div class="topbar-dropdown-menu" id="messageMenu">
                        <div class="dropdown-header">
                            <span>Messages</span>
                            <a href="messages.php">View all</a>
                        </div>
                        <div class="dropdown-list">
                            <?php if (count($unreadMessagesList) > 0): ?>
                                <?php foreach (array_slice($unreadMessagesList, 0, 5) as $msg): ?>
                                <a href="messages.php?view=<?php echo $msg['id']; ?>" class="dropdown-item-message <?php echo $msg['is_read'] ? '' : 'unread'; ?>">
                                    <div class="message-avatar">
                                        <?php if ($msg['type'] === 'system'): ?>
                                        <i class="fas fa-cog"></i>
                                        <?php else: ?>
                                        <?php echo strtoupper(substr($msg['sender_name'] ?? 'A', 0, 1)); ?>
                                        <?php endif; ?>
                                    </div>
                                    <div class="message-content">
                                        <div class="message-sender">
                                            <?php 
                                            if ($msg['type'] === 'system') echo 'System';
                                            else echo htmlspecialchars($msg['sender_name'] ?? 'Unknown');
                                            ?>
                                        </div>
                                        <div class="message-text"><?php echo htmlspecialchars($msg['subject'] ?: mb_substr($msg['content'], 0, 30) . '...'); ?></div>
                                        <div class="message-time"><?php echo adminTimeAgo($msg['created_at']); ?></div>
                                    </div>
                                </a>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <div class="dropdown-empty">
                                    <i class="fas fa-inbox"></i>
                                    <span>No messages</span>
                                </div>
                            <?php endif; ?>
                        </div>
                        <div class="dropdown-footer">
                            <a href="messages.php">Open inbox</a>
                        </div>
                    </div>
                </div>
                
                <div class="topbar-divider"></div>
                <div class="topbar-profile">
                    <div class="topbar-avatar">
                        <?php echo strtoupper(substr($user['full_name'] ?? 'A', 0, 1)); ?>
                    </div>
                    <div class="topbar-profile-info">
                        <span class="topbar-profile-name"><?php echo htmlspecialchars($user['full_name'] ?? 'Admin'); ?></span>
                        <span class="topbar-profile-role">Admin</span>
                    </div>
                </div>
            </div>
        </header>
        
        <!-- Page Content -->
        <main class="page-content">
