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
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo isset($pageTitle) ? htmlspecialchars($pageTitle) . ' - ' : ''; ?>MÂY MƠ BOOK - Online Book Rental</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <?php $basePath = strpos($_SERVER['PHP_SELF'], '/admin/') !== false ? '../../' : '../'; ?>
    <link rel="stylesheet" href="<?php echo $basePath; ?>assets/css/style.css?v=<?php echo time(); ?>">
    <link rel="stylesheet" href="<?php echo $basePath; ?>assets/css/impeccable.css?v=<?php echo time(); ?>">
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
                    <input type="text" placeholder="Search books..." onkeypress="if(event.key==='Enter') window.location.href='books.php?search='+this.value">
                </div>
                
                <div class="nav">
                    <a href="index.php" class="nav-link <?php echo $currentPage == 'index.php' ? 'active' : ''; ?>">
                        <i class="fas fa-home"></i> Home
                    </a>
                    <a href="books.php" class="nav-link <?php echo $currentPage == 'books.php' ? 'active' : ''; ?>">
                        <i class="fas fa-book-reader"></i> Rent Books
                    </a>
                    <a href="about.php" class="nav-link <?php echo $currentPage == 'about.php' ? 'active' : ''; ?>">
                        <i class="fas fa-info-circle"></i> About
                    </a>
                    <a href="contact.php" class="nav-link <?php echo $currentPage == 'contact.php' ? 'active' : ''; ?>">
                        <i class="fas fa-envelope"></i> Contact
                    </a>
                    
                    <?php if ($isLoggedIn): ?>
                        <a href="cart.php" class="nav-link <?php echo $currentPage == 'cart.php' ? 'active' : ''; ?>">
                            <i class="fas fa-shopping-cart"></i> Cart
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
                        
                        <div class="user-menu">
                            <div class="user-avatar">
                                <?php echo strtoupper(substr($user['full_name'] ?? 'U', 0, 1)); ?>
                            </div>
                            <div class="user-dropdown">
                                <div class="dropdown-content">
                                    <div class="dropdown-profile">
                                        <div class="dropdown-name"><?php echo htmlspecialchars($user['full_name'] ?? 'User'); ?></div>
                                        <div class="dropdown-email"><?php echo htmlspecialchars($user['email'] ?? ''); ?></div>
                                    </div>
                                    <div class="dropdown-divider"></div>
                                    <a href="notifications.php" class="dropdown-item">
                                        <i class="fas fa-bell"></i> Thông Báo
                                        <?php if (isset($unreadMessages) && $unreadMessages > 0): ?>
                                        <span class="dropdown-badge"><?php echo $unreadMessages; ?></span>
                                        <?php endif; ?>
                                    </a>
                                    <a href="<?php echo $dashboardPath; ?>" class="dropdown-item">
                                        <i class="fas fa-th-large"></i> Dashboard
                                    </a>
                                    <a href="<?php echo $dashboardPath; ?>?tab=rentals" class="dropdown-item">
                                        <i class="fas fa-book"></i> Rented Books
                                    </a>
                                    <?php if ($isAdmin): ?>
                                    <div class="dropdown-divider"></div>
                                    <a href="<?php echo strpos($_SERVER['PHP_SELF'], '/admin/') !== false ? 'index.php' : 'admin/index.php'; ?>" class="dropdown-item">
                                        <i class="fas fa-shield-alt"></i> Admin
                                    </a>
                                    <?php endif; ?>
                                    <div class="dropdown-divider"></div>
                                    <a href="<?php echo $logoutPath; ?>" class="dropdown-item" style="color: var(--danger);">
                                        <i class="fas fa-sign-out-alt"></i> Logout
                                    </a>
                                </div>
                            </div>
                        </div>
                    <?php else: ?>
                        <a href="<?php echo $loginPath; ?>" class="nav-link">
                            <i class="fas fa-sign-in-alt"></i> Login
                        </a>
                        <a href="<?php echo $registerPath; ?>" class="nav-link btn">
                            <i class="fas fa-user-plus"></i> Register
                        </a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </nav>
