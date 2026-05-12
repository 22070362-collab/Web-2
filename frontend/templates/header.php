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

// Chống truy cập chéo: Nếu đang ở trang user mà là admin, hoặc ngược lại (tùy nhu cầu)
// Tuy nhiên thông thường admin vẫn có thể xem frontend. 
// Quan trọng nhất là bảo vệ trang admin đã có requireAdmin().

// Fix đường dẫn logout cho header
$logoutPath = strpos($_SERVER['PHP_SELF'], '/admin/') !== false ? '../logout.php' : 'logout.php';
$dashboardPath = strpos($_SERVER['PHP_SELF'], '/admin/') !== false ? '../dashboard.php' : 'dashboard.php';
$loginPath = strpos($_SERVER['PHP_SELF'], '/admin/') !== false ? '../login.php' : 'login.php';
$registerPath = strpos($_SERVER['PHP_SELF'], '/admin/') !== false ? '../register.php' : 'register.php';

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

$cartCount = 0;
if ($isLoggedIn) {
    require_once __DIR__ . '/../../backend/models/Cart.php';
    $cartModel = new Cart();
    $cartCount = $cartModel->getItemCount();
    
    // Get unread messages count
    $messageController = new MessageController();
    $unreadMessages = $messageController->countUnread($_SESSION['user_id']);
} elseif (isset($_SESSION['cart']) && is_array($_SESSION['cart'])) {
    $cartCount = count($_SESSION['cart']);
}

$currentPage = basename($_SERVER['PHP_SELF']);
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo isset($pageTitle) ? htmlspecialchars($pageTitle) . ' - ' : ''; ?>MÂY MƠ BOOK - Thuê Sách Trực Tuyến</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <?php $basePath = strpos($_SERVER['PHP_SELF'], '/admin/') !== false ? '../../' : '../'; ?>
    <link rel="stylesheet" href="<?php echo $basePath; ?>assets/css/style.css?v=<?php echo time(); ?>">
    <script src="<?php echo $basePath; ?>assets/js/main.js" defer></script>
</head>
<body>
    <nav class="navbar">
        <div class="container">
            <div class="navbar-inner">
                <a href="index.php" class="logo">
                    <div class="logo-icon">
                        <i class="fas fa-book-open"></i>
                    </div>
                    MÂY MƠ <span>BOOK</span>
                </a>
                
                <div class="navbar-search">
                    <i class="fas fa-search"></i>
                    <input type="text" placeholder="Tìm kiếm sách..." onkeypress="if(event.key==='Enter') window.location.href='books.php?search='+this.value">
                </div>
                
                <div class="nav">
                    <a href="index.php" class="nav-link <?php echo $currentPage == 'index.php' ? 'active' : ''; ?>">
                        <i class="fas fa-home"></i> Trang Chủ
                    </a>
                    <a href="books.php" class="nav-link <?php echo $currentPage == 'books.php' ? 'active' : ''; ?>">
                        <i class="fas fa-book-reader"></i> Thuê Sách
                    </a>
                    <a href="about.php" class="nav-link <?php echo $currentPage == 'about.php' ? 'active' : ''; ?>">
                        <i class="fas fa-info-circle"></i> Giới Thiệu
                    </a>
                    <a href="contact.php" class="nav-link <?php echo $currentPage == 'contact.php' ? 'active' : ''; ?>">
                        <i class="fas fa-envelope"></i> Liên Hệ
                    </a>
                    
                    <?php if ($isLoggedIn): ?>
                        <a href="cart.php" class="nav-link <?php echo $currentPage == 'cart.php' ? 'active' : ''; ?>">
                            <i class="fas fa-shopping-cart"></i> Giỏ Hàng
                            <?php if ($cartCount > 0): ?>
                            <span class="cart-count"><?php echo $cartCount; ?></span>
                            <?php endif; ?>
                        </a>
                        
                        <a href="notifications.php" class="nav-link <?php echo $currentPage == 'notifications.php' ? 'active' : ''; ?>">
                            <i class="fas fa-bell"></i> Thông Báo
                            <?php if (isset($unreadMessages) && $unreadMessages > 0): ?>
                            <span class="cart-count"><?php echo $unreadMessages; ?></span>
                            <?php endif; ?>
                        </a>
                        
                        <div class="user-menu" style="position: relative;">
                            <div class="user-avatar">
                                <?php echo strtoupper(substr($user['full_name'] ?? 'U', 0, 1)); ?>
                            </div>
                            <div class="user-dropdown">
                                <div class="dropdown-content">
                                    <div style="padding: 12px 14px; background: var(--bg-secondary); border-radius: var(--radius); margin-bottom: 8px;">
                                        <div style="font-weight: 600; color: var(--text-primary);"><?php echo htmlspecialchars($user['full_name'] ?? 'User'); ?></div>
                                        <div style="font-size: 0.8rem; color: var(--text-muted);"><?php echo htmlspecialchars($user['email'] ?? ''); ?></div>
                                    </div>
                                    <div class="dropdown-divider"></div>
                                    <a href="notifications.php" class="dropdown-item">
                                        <i class="fas fa-bell"></i> Thông Báo
                                        <?php if (isset($unreadMessages) && $unreadMessages > 0): ?>
                                        <span class="dropdown-badge"><?php echo $unreadMessages; ?></span>
                                        <?php endif; ?>
                                    </a>
                                    <a href="<?php echo $dashboardPath; ?>" class="dropdown-item">
                                        <i class="fas fa-th-large"></i> Tổng Quan
                                    </a>
                                    <a href="<?php echo $dashboardPath; ?>?tab=rentals" class="dropdown-item">
                                        <i class="fas fa-book"></i> Sách Đã Thuê
                                    </a>
                                    <?php if ($isAdmin): ?>
                                    <div class="dropdown-divider"></div>
                                    <a href="<?php echo strpos($_SERVER['PHP_SELF'], '/admin/') !== false ? 'index.php' : 'admin/index.php'; ?>" class="dropdown-item">
                                        <i class="fas fa-shield-alt"></i> Quản Trị
                                    </a>
                                    <?php endif; ?>
                                    <div class="dropdown-divider"></div>
                                    <a href="<?php echo $logoutPath; ?>" class="dropdown-item" style="color: var(--danger);">
                                        <i class="fas fa-sign-out-alt"></i> Đăng Xuất
                                    </a>
                                </div>
                            </div>
                        </div>
                    <?php else: ?>
                        <a href="<?php echo $loginPath; ?>" class="nav-link">
                            <i class="fas fa-sign-in-alt"></i> Đăng Nhập
                        </a>
                        <a href="<?php echo $registerPath; ?>" class="nav-link btn">
                            <i class="fas fa-user-plus"></i> Đăng Ký
                        </a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </nav>
