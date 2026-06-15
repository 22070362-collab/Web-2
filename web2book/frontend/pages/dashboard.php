<?php
require_once __DIR__ . '/../templates/header.php';

if (!$isLoggedIn) {
    header('Location: login.php?redirect=dashboard.php');
    exit;
}

require_once __DIR__ . '/../../backend/controllers/RentalController.php';
require_once __DIR__ . '/../../backend/models/User.php';

$rentalController = new RentalController();
$userModel = new User();

$user = $userModel->findById($_SESSION['user_id']);
$activeRentals = $rentalController->getActiveRentals($_SESSION['user_id']);
$historyRentals = $rentalController->getHistory($_SESSION['user_id']);

$stats = [
    'active' => count($activeRentals),
    'returned' => count($historyRentals),
    'total_spent' => array_sum(array_column($historyRentals, 'total_price'))
];

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    if ($_POST['action'] === 'return_book' && isset($_POST['rental_id'])) {
        $result = $rentalController->returnBook(intval($_POST['rental_id']));
        if ($result['success']) {
            header('Location: dashboard.php?success=1');
            exit;
        }
    }
}

if (isset($_GET['success'])) {
    $successMessage = 'Trả sách thành công!';
}
?>

<section style="padding: 120px 0 60px; min-height: 100vh;">
    <div class="container">
        <div style="margin-bottom: 32px;">
            <h1 style="font-size: 2rem; margin-bottom: 4px;">Tổng Quan</h1>
            <p style="color: var(--text-muted); margin: 0;">Chào mừng, <?php echo htmlspecialchars($user['full_name']); ?>!</p>
        </div>
        
        <?php if (isset($successMessage)): ?>
        <div class="alert alert-success" style="margin-bottom: 24px;">
            <i class="fas fa-check-circle"></i>
            <?php echo $successMessage; ?>
        </div>
        <?php endif; ?>
        
        <div class="dashboard-grid">
            <!-- Sidebar -->
            <aside class="dashboard-sidebar">
                <div class="dashboard-user">
                    <div class="dashboard-avatar"><?php echo strtoupper(substr($user['full_name'], 0, 1)); ?></div>
                    <h3 style="margin-bottom: 4px;"><?php echo htmlspecialchars($user['full_name']); ?></h3>
                    <p style="font-size: 0.85rem; color: var(--text-muted); margin: 0;"><?php echo htmlspecialchars($user['email']); ?></p>
                </div>
                
                <nav class="dashboard-nav">
                    <a href="#" class="active"><i class="fas fa-chart-line"></i> Tổng Quan</a>
                    <a href="#"><i class="fas fa-book"></i> Sách Đã Thuê</a>
                    <a href="books.php"><i class="fas fa-search"></i> Khám Phá Sách</a>
                </nav>
            </aside>
            
            <!-- Content -->
            <main class="dashboard-content">
                <h2 style="font-size: 1.25rem; margin-bottom: 24px;">Tổng Quan</h2>
                
                <div class="dashboard-stats">
                    <div class="stat-card">
                        <div class="stat-icon">
                            <i class="fas fa-book"></i>
                        </div>
                        <div class="stat-value"><?php echo $stats['active']; ?></div>
                        <div class="stat-label">Sách Đang Thuê</div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-icon" style="background: rgba(34, 197, 94, 0.15); color: #22c55e;">
                            <i class="fas fa-check-circle"></i>
                        </div>
                        <div class="stat-value"><?php echo $stats['returned']; ?></div>
                        <div class="stat-label">Sách Đã Trả</div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-icon" style="background: rgba(245, 158, 11, 0.15); color: #f59e0b;">
                            <i class="fas fa-coins"></i>
                        </div>
                        <div class="stat-value"><?php echo number_format($stats['total_spent'], 0); ?>đ</div>
                        <div class="stat-label">Tổng Chi Tiêu</div>
                    </div>
                </div>
                
                <?php if (count($activeRentals) > 0): ?>
                <h3 style="margin: 32px 0 16px; font-size: 1.1rem;">Sách Đang Thuê</h3>
                <div style="display: flex; flex-direction: column; gap: 16px;">
                    <?php foreach ($activeRentals as $rental): 
                        $daysLeft = (strtotime($rental['due_date']) - time()) / (60 * 60 * 24);
                        $isOverdue = $daysLeft < 0;
                    ?>
                    <div style="background: var(--bg-secondary); border: 1px solid var(--border-color); border-radius: var(--radius-lg); overflow: hidden;">
                        <div style="display: flex; gap: 16px; padding: 16px; border-bottom: 1px solid var(--border-color);">
                            <img src="<?php echo getBookCoverImage($rental); ?>"
                                 alt=""
                                 style="width: 60px; height: 85px; object-fit: cover; border-radius: var(--radius);"
                                 onerror="this.src='https://via.placeholder.com/60x85/1a1a1a/6366f1?text=Book'">
                            <div style="flex: 1;">
                                <h4 style="font-size: 1rem; margin-bottom: 4px;"><?php echo htmlspecialchars($rental['title']); ?></h4>
                                <p style="font-size: 0.85rem; color: var(--text-muted); margin: 0 0 8px;"><?php echo htmlspecialchars($rental['author']); ?></p>
                                <span style="display: inline-block; padding: 4px 10px; border-radius: var(--radius-full); font-size: 0.7rem; font-weight: 600; text-transform: uppercase; background: <?php echo $isOverdue ? 'rgba(239,68,68,0.15); color: #ef4444;' : 'rgba(34,197,94,0.15); color: #22c55e;'; ?>">
                                    <?php echo $isOverdue ? 'Quá Hạn' : 'Đang Thuê'; ?>
                                </span>
                            </div>
                        </div>
                        <div style="padding: 16px; display: flex; justify-content: space-between; align-items: center;">
                            <div style="display: flex; gap: 24px; font-size: 0.9rem;">
                                <span style="color: var(--text-muted);">Hạn trả: <strong style="color: var(--text-primary);"><?php echo date('d/m/Y', strtotime($rental['due_date'])); ?></strong></span>
                                <span style="color: var(--text-muted);">Tổng: <strong style="color: var(--accent);"><?php echo number_format($rental['total_price'], 0); ?>đ</strong></span>
                            </div>
                            <form method="POST">
                                <input type="hidden" name="action" value="return_book">
                                <input type="hidden" name="rental_id" value="<?php echo $rental['id']; ?>">
                                <button type="submit" class="btn btn-primary btn-sm">
                                    <i class="fas fa-undo"></i> Trả Sách
                                </button>
                            </form>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
                <?php else: ?>
                <div style="text-align: center; padding: 60px 20px; background: var(--bg-card); border: 1px solid var(--border-color); border-radius: var(--radius-lg); margin-top: 24px;">
                    <div style="width: 80px; height: 80px; background: rgba(34, 197, 94, 0.15); border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 16px;">
                        <i class="fas fa-check-circle" style="font-size: 2rem; color: #22c55e;"></i>
                    </div>
                    <h3 style="margin-bottom: 8px; font-size: 1.25rem;">Không Có Sách Đang Thuê</h3>
                    <p style="color: var(--text-muted); margin-bottom: 20px;">Bạn đang không thuê cuốn sách nào.</p>
                    <a href="books.php" class="btn btn-primary">
                        <i class="fas fa-book"></i> Khám Phá Sách
                    </a>
                </div>
                <?php endif; ?>
            </main>
        </div>
    </div>
</section>

<?php require_once __DIR__ . '/../templates/footer.php'; ?>
