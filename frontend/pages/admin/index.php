<?php
require_once __DIR__ . '/../../templates/admin_header.php';

$auth->requireAdmin();

require_once __DIR__ . '/../../../backend/controllers/BookController.php';
require_once __DIR__ . '/../../../backend/controllers/RentalController.php';
require_once __DIR__ . '/../../../backend/models/User.php';

$bookController = new BookController();
$rentalController = new RentalController();
$userModel = new User();

$bookStats = $bookController->stats();
$rentalStats = $rentalController->stats();
$userCount = $userModel->countAll();
$overdueRentals = $rentalController->getOverdue();

// Get recent rentals
$recentRentals = $rentalController->getAll(5);

/**
 * Revenue report (real data)
 * Rule: SUM(rentals.total_price) with status IN ('active','returned','overdue'), exclude cancelled
 */
$revenueByMonth = $rentalController->getRevenueByMonth(); // default: current year
$totalRevenue = $rentalStats['total_revenue'];

// Get category stats
$categories = $bookController->categories();
$categoryStats = [];
$totalBooksInCats = 0;
foreach ($categories as $cat) {
    $booksInCat = $bookController->available(100);
    $count = count(array_filter($booksInCat, fn($b) => $b['category'] === $cat));
    if ($count > 0) {
        $categoryStats[$cat] = $count;
        $totalBooksInCats += $count;
    }
}
?>

<!-- Page Header -->
<div class="page-header">
    <div class="page-header-left">
        <div class="page-header-icon">
            <i class="fas fa-chart-pie"></i>
        </div>
        <div>
            <h1 class="page-header-title">Bảng Điều Khiển</h1>
            <p class="page-header-subtitle">Chào mừng trở lại, <?php echo htmlspecialchars($_SESSION['full_name']); ?></p>
        </div>
    </div>
    <div>
        <a href="../index.php" class="btn btn-outline btn-sm">
            <i class="fas fa-external-link-alt"></i> Xem Trang
        </a>
    </div>
</div>

<!-- Dashboard Stats -->
<div class="dashboard-stats-grid">
    <div class="stat-card-admin">
        <div class="stat-card-header">
            <div class="stat-card-icon green">
                <i class="fas fa-book"></i>
            </div>
            <div class="stat-card-trend up">
                <i class="fas fa-arrow-up"></i> 12%
            </div>
        </div>
        <div class="stat-card-value"><?php echo $bookStats['total_books']; ?></div>
        <div class="stat-card-label">Tổng Sách</div>
    </div>
    
    <div class="stat-card-admin">
        <div class="stat-card-header">
            <div class="stat-card-icon purple">
                <i class="fas fa-exchange-alt"></i>
            </div>
            <div class="stat-card-trend up">
                <i class="fas fa-arrow-up"></i> 8%
            </div>
        </div>
        <div class="stat-card-value"><?php echo $rentalStats['active_rentals']; ?></div>
        <div class="stat-card-label">Đang Thuê</div>
    </div>
    
    <div class="stat-card-admin">
        <div class="stat-card-header">
            <div class="stat-card-icon orange">
                <i class="fas fa-users"></i>
            </div>
            <div class="stat-card-trend up">
                <i class="fas fa-arrow-up"></i> 5%
            </div>
        </div>
        <div class="stat-card-value"><?php echo $userCount; ?></div>
        <div class="stat-card-label">Tổng Người Dùng</div>
    </div>
    
    <div class="stat-card-admin">
        <div class="stat-card-header">
            <div class="stat-card-icon blue">
                <i class="fas fa-dollar-sign"></i>
            </div>
            <div class="stat-card-trend up">
                <i class="fas fa-arrow-up"></i> 15%
            </div>
        </div>
        <div class="stat-card-value"><?php echo number_format($totalRevenue ?? 0, 0, ',', '.'); ?></div>
        <div class="stat-card-label">Doanh Thu (VND)</div>
    </div>
</div>

<!-- Dashboard Grid -->
<div class="dashboard-grid">
    <!-- Chart Section -->
    <div class="dashboard-card">
        <div class="dashboard-card-header">
            <h3 class="dashboard-card-title">Doanh Thu Theo Tháng</h3>
            <div class="dashboard-card-actions">
                <button class="dashboard-card-btn active">Tháng</button>
                <button class="dashboard-card-btn">Tuần</button>
                <button class="dashboard-card-btn">Năm</button>
            </div>
        </div>
        <div class="dashboard-card-body">
            <div class="chart-container">
                <canvas id="revenueChart"></canvas>
            </div>
        </div>
    </div>
    
    <!-- Sidebar Cards -->
    <div style="display: flex; flex-direction: column; gap: 24px;">
        <!-- Quick Actions -->
        <div class="dashboard-card">
            <div class="dashboard-card-header">
                <h3 class="dashboard-card-title">Quick Actions</h3>
            </div>
            <div class="dashboard-card-body">
                <div class="quick-actions">
                    <a href="books.php?action=add" class="quick-action-btn">
                        <i class="fas fa-plus-circle"></i>
                        <span>Thêm Sách</span>
                    </a>
                    <a href="rentals.php?status=pending" class="quick-action-btn">
                        <i class="fas fa-clock"></i>
                        <span>Chờ Xử Lý</span>
                    </a>
                    <a href="rentals.php?status=overdue" class="quick-action-btn">
                        <i class="fas fa-exclamation-triangle"></i>
                        <span>Quá Hạn</span>
                    </a>
                    <a href="users.php" class="quick-action-btn">
                        <i class="fas fa-user-plus"></i>
                        <span>Người Dùng Mới</span>
                    </a>
                </div>
            </div>
        </div>
        
        <!-- Top Categories -->
        <div class="dashboard-card">
            <div class="dashboard-card-header">
                <h3 class="dashboard-card-title">Danh Mục Hàng Đầu</h3>
            </div>
            <div class="dashboard-card-body">
                <div class="category-list">
                    <?php 
                    $catIndex = 0;
                    foreach (array_slice($categoryStats, 0, 4) as $catName => $count): 
                        $percentage = $totalBooksInCats > 0 ? ($count / $totalBooksInCats * 100) : 0;
                        $catIndex++;
                    ?>
                    <div class="category-item">
                        <div class="category-info">
                            <div class="category-icon">
                                <i class="fas fa-<?php 
                                    echo match($catIndex) {
                                        1 => 'book-open',
                                        2 => 'heart',
                                        3 => 'atom',
                                        4 => 'lightbulb',
                                        default => 'book'
                                    };
                                ?>"></i>
                            </div>
                            <div>
                                <div class="category-name"><?php echo htmlspecialchars($catName); ?></div>
                                <div class="category-count"><?php echo $count; ?> cuốn</div>
                            </div>
                        </div>
                        <div class="category-bar">
                            <div class="category-bar-fill" style="width: <?php echo $percentage; ?>%"></div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Recent Orders -->
<div class="dashboard-card" style="margin-top: 24px;">
    <div class="dashboard-card-header">
        <h3 class="dashboard-card-title">Đơn Gần Đây</h3>
        <a href="rentals.php" class="btn btn-outline btn-sm">Xem Tất Cả</a>
    </div>
    <div class="dashboard-card-body no-padding">
        <?php if (count($recentRentals) > 0): ?>
        <table class="data-table">
            <thead>
                <tr>
                    <th>Khách Hàng</th>
                    <th>Sách</th>
                    <th>Ngày Thuê</th>
                    <th>Hạn Trả</th>
                    <th>Tổng</th>
                    <th>Trạng Thái</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($recentRentals as $rental): ?>
                <tr>
                    <td>
                        <div class="table-user">
                            <div class="table-user-avatar">
                                <?php echo strtoupper(substr($rental['full_name'], 0, 1)); ?>
                            </div>
                            <div class="table-user-info">
                                <div class="table-user-name"><?php echo htmlspecialchars($rental['full_name']); ?></div>
                                <div class="table-user-email">@<?php echo htmlspecialchars($rental['username']); ?></div>
                            </div>
                        </div>
                    </td>
                    <td>
                        <div class="table-book">
                            <img src="../../assets/images/<?php echo htmlspecialchars($rental['cover_image'] ?? 'default_book.jpg'); ?>"
                                 alt="" class="table-book-cover"
                                 onerror="this.src='https://via.placeholder.com/36x48/242424/22c55e?text=B'">
                            <div class="table-book-info">
                                <div class="table-book-title"><?php echo htmlspecialchars($rental['title']); ?></div>
                                <div class="table-book-author"><?php echo htmlspecialchars($rental['author']); ?></div>
                            </div>
                        </div>
                    </td>
                    <td><?php echo date('d M, Y', strtotime($rental['rental_date'])); ?></td>
                    <td><?php echo date('d M, Y', strtotime($rental['due_date'])); ?></td>
                    <td style="font-weight: 600; color: var(--green-primary);"><?php echo number_format($rental['total_price'], 0, ',', '.'); ?>đ</td>
                    <td>
                        <?php 
                        $statusMap = [
                            'pending' => ['label' => 'Chờ xử lý', 'class' => 'badge-warning'],
                                'active' => ['label' => 'Đang thuê', 'class' => 'badge-success'],
                                'returned' => ['label' => 'Đã trả', 'class' => 'badge-secondary'],
                                'overdue' => ['label' => 'Quá hạn', 'class' => 'badge-danger'],
                                'cancelled' => ['label' => 'Đã hủy', 'class' => 'badge-secondary']
                        ];
                        $status = $statusMap[$rental['status']] ?? ['label' => $rental['status'], 'class' => 'badge-secondary'];
                        ?>
                        <span class="badge <?php echo $status['class']; ?>">
                            <?php echo $status['label']; ?>
                        </span>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        <?php else: ?>
        <div style="text-align: center; padding: 60px 20px;">
            <i class="fas fa-inbox" style="font-size: 3rem; color: var(--text-muted); margin-bottom: 16px;"></i>
            <p style="color: var(--text-muted);">Chưa có đơn thuê.</p>
        </div>
        <?php endif; ?>
    </div>
</div>

<!-- Overdue Alert -->
<?php if (count($overdueRentals) > 0): ?>
    <div class="alert alert-danger" style="margin-top: 24px;">
    <i class="fas fa-exclamation-circle"></i>
    <div style="flex: 1;">
        <strong>Cảnh báo! Có <?php echo count($overdueRentals); ?> đơn quá hạn.</strong>
        <span style="color: var(--text-muted); margin-left: 8px;">
            Vui lòng liên hệ khách hàng để thu hồi sách.
        </span>
        <a href="rentals.php?status=overdue" class="btn btn-danger btn-sm" style="margin-left: 16px;">
            Xem đơn quá hạn
        </a>
    </div>
</div>
<?php endif; ?>

<script>
// Revenue Chart
document.addEventListener('DOMContentLoaded', function() {
    const ctx = document.getElementById('revenueChart').getContext('2d');
    
    // Revenue data from database
    const months = <?php echo json_encode($revenueByMonth['labels']); ?>;
    const revenueData = <?php echo json_encode($revenueByMonth['data']); ?>;
    
    new Chart(ctx, {
        type: 'line',
        data: {
            labels: months,
            datasets: [{
                label: 'Doanh thu',
                data: revenueData,
                borderColor: '#22c55e',
                backgroundColor: 'rgba(34, 197, 94, 0.1)',
                fill: true,
                tension: 0.4,
                pointBackgroundColor: '#22c55e',
                pointBorderColor: '#22c55e',
                pointRadius: 4,
                pointHoverRadius: 6
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    display: false
                },
                tooltip: {
                    backgroundColor: '#242424',
                    titleColor: '#ffffff',
                    bodyColor: '#a3a3a3',
                    borderColor: '#2a2a2a',
                    borderWidth: 1,
                    padding: 12,
                    displayColors: false,
                    callbacks: {
                        label: function(context) {
                            return context.parsed.y.toLocaleString('vi-VN') + 'đ';
                        }
                    }
                }
            },
            scales: {
                x: {
                    grid: {
                        display: false
                    },
                    ticks: {
                        color: '#737373'
                    }
                },
                y: {
                    grid: {
                        color: '#2a2a2a'
                    },
                    ticks: {
                        color: '#737373',
                        callback: function(value) {
                            return (value / 1000000).toFixed(1) + 'M';
                        }
                    }
                }
            }
        }
    });
});
</script>

<?php require_once __DIR__ . '/../../templates/admin_footer.php'; ?>
