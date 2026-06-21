<?php
ob_start();
require_once __DIR__ . '/../../config/config.php';
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
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo isset($pageTitle) ? htmlspecialchars($pageTitle) . ' - ' : ''; ?>Admin - MÂY MƠ BOOK</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="../../assets/css/style.css?v=<?php echo time(); ?>">
    <link rel="stylesheet" href="../../assets/css/admin.css?v=<?php echo time(); ?>">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body class="admin-body">
    <!-- Admin Navbar -->
    <nav class="navbar admin-navbar">
        <div class="container">
            <div class="navbar-inner">
                <a href="index.php" class="logo">
                    <div class="logo-icon">
                        <i class="fas fa-book-open"></i>
                    </div>
                    MÂY MƠ <span>BOOK</span>
                </a>
                
                <div class="nav-main admin-nav-main">
                    <a href="index.php" class="nav-link <?php echo $currentPage == 'index.php' ? 'active' : ''; ?>">
                        <i class="fas fa-chart-pie"></i> <span class="nav-text">Dashboard</span>
                    </a>
                    <a href="books.php" class="nav-link <?php echo $currentPage == 'books.php' ? 'active' : ''; ?>">
                        <i class="fas fa-book"></i> <span class="nav-text">Quản lý Sách</span>
                    </a>
                    <a href="rentals.php" class="nav-link <?php echo $currentPage == 'rentals.php' ? 'active' : ''; ?>">
                        <i class="fas fa-exchange-alt"></i> <span class="nav-text">Quản lý Thuê</span>
                    </a>
                    <a href="users.php" class="nav-link <?php echo $currentPage == 'users.php' ? 'active' : ''; ?>">
                        <i class="fas fa-users"></i> <span class="nav-text">Quản lý Khách</span>
                    </a>
                    <a href="messages.php" class="nav-link <?php echo $currentPage == 'messages.php' ? 'active' : ''; ?>">
                        <i class="fas fa-envelope"></i> <span class="nav-text">Tin Nhắn</span>
                        <?php if ($unreadCount > 0): ?>
                        <span class="cart-count"><?php echo $unreadCount; ?></span>
                        <?php endif; ?>
                    </a>
                    <a href="settings.php" class="nav-link <?php echo $currentPage == 'settings.php' ? 'active' : ''; ?>">
                        <i class="fas fa-cog"></i> <span class="nav-text">Cài đặt</span>
                    </a>
                </div>
                
                <div class="nav-actions">
                    <div class="user-menu" style="position: relative;">
                        <div class="user-avatar">
                            <?php echo strtoupper(substr($user['full_name'] ?? 'A', 0, 1)); ?>
                        </div>
                        <div class="user-dropdown">
                            <div class="dropdown-content">
                                <div style="padding: 12px 14px; background: var(--bg-secondary); border-radius: var(--radius); margin-bottom: 8px;">
                                    <div style="font-weight: 600; color: var(--text-primary);"><?php echo htmlspecialchars($user['full_name'] ?? 'Admin'); ?></div>
                                    <div style="font-size: 0.8rem; color: var(--text-muted);"><?php echo htmlspecialchars($user['email'] ?? ''); ?></div>
                                </div>
                                <div class="dropdown-divider"></div>
                                <?php if ($overdueCount > 0): ?>
                                <a href="rentals.php?status=overdue" class="dropdown-item" style="color: var(--warning);">
                                    <i class="fas fa-exclamation-circle"></i> <?php echo $overdueCount; ?> Đơn Quá Hạn
                                </a>
                                <?php endif; ?>
                                <a href="messages.php" class="dropdown-item">
                                    <i class="fas fa-envelope"></i> Tin Nhắn
                                    <?php if ($unreadCount > 0): ?>
                                    <span class="dropdown-badge"><?php echo $unreadCount; ?></span>
                                    <?php endif; ?>
                                </a>
                                <div class="dropdown-divider"></div>
                                <a href="../index.php" class="dropdown-item">
                                    <i class="fas fa-home"></i> Về Trang Chủ
                                </a>
                                <a href="../logout.php" class="dropdown-item" style="color: var(--danger);">
                                    <i class="fas fa-sign-out-alt"></i> Đăng Xuất
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </nav>

    <!-- Admin Content -->
    <div class="admin-wrapper">
        <main class="page-content">
