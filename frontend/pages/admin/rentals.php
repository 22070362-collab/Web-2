<?php
require_once __DIR__ . '/../../templates/admin_header.php';

$auth->requireAdmin();

require_once __DIR__ . '/../../../backend/controllers/RentalController.php';

$rentalController = new RentalController();
$message = '';
$messageType = '';
$searchResult = null;
$searchCode = '';

// Handle form
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    
    if ($action === 'return') {
        $result = $rentalController->returnBook(intval($_POST['id']));
        $message = $result['message'];
        $messageType = $result['success'] ? 'success' : 'danger';
    } elseif ($action === 'cancel') {
        $result = $rentalController->cancel(intval($_POST['id']));
        $message = $result['message'];
        $messageType = $result['success'] ? 'success' : 'danger';
    } elseif ($action === 'confirm_pickup') {
        $result = $rentalController->confirmPickup(intval($_POST['id']));
        $message = $result['message'];
        $messageType = $result['success'] ? 'success' : 'danger';
    } elseif ($action === 'search_code') {
        $searchCode = trim($_POST['rental_code'] ?? '');
        if ($searchCode) {
            $searchResult = $rentalController->getRentalByCode($searchCode);
            if (!$searchResult) {
                $message = 'No order found for code: ' . htmlspecialchars($searchCode);
                $messageType = 'warning';
            }
        }
    }
}

$status = $_GET['status'] ?? '';
$rentals = $rentalController->getAll(100, 0, $status ?: null);
$stats = $rentalController->stats();
?>

<!-- Page Header -->
<div class="page-header">
    <div class="page-header-left">
        <div class="page-header-icon" style="background: var(--green-bg); color: var(--green-primary);">
            <i class="fas fa-exchange-alt"></i>
        </div>
        <div>
            <h1 class="page-header-title">Rentals</h1>
            <p class="page-header-subtitle">Review orders and confirm pickups or returns</p>
        </div>
    </div>
    <div>
        <a href="index.php" class="btn btn-outline btn-sm">
            <i class="fas fa-arrow-left"></i> Back
        </a>
    </div>
</div>

<!-- Stats -->
<div class="dashboard-stats-grid" style="grid-template-columns: repeat(4, 1fr);">
    <div class="stat-card-admin">
        <div class="stat-card-header">
            <div class="stat-card-icon orange">
                <i class="fas fa-clock"></i>
            </div>
        </div>
        <div class="stat-card-value"><?php echo $stats['pending_rentals']; ?></div>
        <div class="stat-card-label">Pending Pickup</div>
    </div>
    
    <div class="stat-card-admin">
        <div class="stat-card-header">
            <div class="stat-card-icon green">
                <i class="fas fa-book"></i>
            </div>
        </div>
        <div class="stat-card-value"><?php echo $stats['active_rentals']; ?></div>
        <div class="stat-card-label">Active Rentals</div>
    </div>
    
    <div class="stat-card-admin">
        <div class="stat-card-header">
            <div class="stat-card-icon" style="background: rgba(239, 68, 68, 0.15); color: var(--danger);">
                <i class="fas fa-exclamation-triangle"></i>
            </div>
        </div>
        <div class="stat-card-value"><?php echo $stats['overdue_rentals']; ?></div>
        <div class="stat-card-label">Overdue</div>
    </div>
    
    <div class="stat-card-admin">
        <div class="stat-card-header">
            <div class="stat-card-icon purple">
                <i class="fas fa-check-circle"></i>
            </div>
        </div>
        <div class="stat-card-value"><?php echo $stats['returned_rentals']; ?></div>
        <div class="stat-card-label">Returned</div>
    </div>
</div>

<!-- Messages -->
<?php if ($message): ?>
<div class="alert alert-<?php echo $messageType; ?>">
    <i class="fas fa-<?php echo $messageType === 'success' ? 'check-circle' : ($messageType === 'warning' ? 'exclamation-circle' : 'exclamation-circle'); ?>"></i>
    <?php echo htmlspecialchars(adminUiMessage($message)); ?>
</div>
<?php endif; ?>

<!-- Search by Rental Code -->
<div class="dashboard-card" style="margin-bottom: 20px;">
    <div class="dashboard-card-header">
        <h3 class="dashboard-card-title"><i class="fas fa-search"></i> Search Order by Code</h3>
    </div>
    <div class="dashboard-card-body">
        <form method="POST" style="display: flex; gap: 10px; align-items: flex-end;">
            <input type="hidden" name="action" value="search_code">
            <div style="flex: 1;">
                <label style="display: block; margin-bottom: 8px; font-weight: 500; color: var(--text-primary);">Order Code</label>
                <input type="text" name="rental_code" placeholder="Enter order code (example: WB9F2A1C)" 
                       value="<?php echo htmlspecialchars($searchCode); ?>"
                       style="width: 100%; padding: 10px 12px; border: 1px solid var(--border-color); border-radius: 6px; background: var(--bg-secondary); color: var(--text-primary);">
            </div>
            <button type="submit" class="btn btn-primary" style="height: 40px; padding: 0 20px;">
                <i class="fas fa-search"></i> Search
            </button>
            <a href="rentals.php" class="btn btn-outline" style="height: 40px; padding: 0 20px;">
                <i class="fas fa-redo"></i> Reset
            </a>
        </form>
    </div>
</div>

<!-- Search Result -->
<?php if ($searchResult && !$message): ?>
<div class="dashboard-card" style="margin-bottom: 20px; border: 2px solid var(--green-primary);">
    <div class="dashboard-card-header" style="background: rgba(34, 197, 94, 0.1);">
        <h3 class="dashboard-card-title"><i class="fas fa-check-circle"></i> Search Result: <?php echo htmlspecialchars($searchCode); ?></h3>
    </div>
    <div class="dashboard-card-body">
        <table class="data-table">
            <thead>
                <tr>
                    <th>Customer</th>
                    <th>Book</th>
                    <th>Rental Date</th>
                    <th>Due Date</th>
                    <th>Total</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php 
                $daysLeft = (strtotime($searchResult['due_date']) - time()) / (60 * 60 * 24);
                $isOverdue = $daysLeft < 0 && $searchResult['status'] === 'active';
                ?>
                <tr <?php echo $isOverdue ? 'style="background: rgba(239,68,68,0.05);"' : ''; ?>>
                    <td>
                        <div class="table-user">
                            <div class="table-user-avatar">
                                <?php echo strtoupper(substr($searchResult['full_name'], 0, 1)); ?>
                            </div>
                            <div class="table-user-info">
                                <div class="table-user-name"><?php echo htmlspecialchars($searchResult['full_name']); ?></div>
                                <div class="table-user-email">@<?php echo htmlspecialchars($searchResult['username']); ?></div>
                            </div>
                        </div>
                    </td>
                    <td>
                        <div class="table-book">
                            <img src="../../../assets/images/<?php echo htmlspecialchars($searchResult['cover_image'] ?? 'default_book.jpg'); ?>"
                                 alt="" class="table-book-cover"
                                 onerror="this.src='https://via.placeholder.com/36x48/242424/22c55e?text=B'">
                            <div class="table-book-info">
                                <div class="table-book-title"><?php echo htmlspecialchars($searchResult['title']); ?></div>
                                <div class="table-book-author"><?php echo htmlspecialchars($searchResult['author']); ?></div>
                            </div>
                        </div>
                    </td>
                    <td><?php echo date('d M, Y', strtotime($searchResult['rental_date'])); ?></td>
                    <td>
                        <?php echo date('d M, Y', strtotime($searchResult['due_date'])); ?>
                        <?php if ($searchResult['status'] === 'active'): ?>
                        <div style="font-size: 0.75rem; margin-top: 4px;">
                            <?php if ($isOverdue): ?>
                            <span style="color: var(--danger);"><?php echo abs(round($daysLeft)); ?> days overdue</span>
                            <?php else: ?>
                            <span style="color: var(--green-primary);"><?php echo round($daysLeft); ?> days left</span>
                            <?php endif; ?>
                        </div>
                        <?php endif; ?>
                    </td>
                    <td style="font-weight: 700; color: var(--green-primary);">
                        <?php echo number_format($searchResult['total_price'], 0, ',', '.'); ?> VND
                    </td>
                    <td>
                        <?php 
                        $statusMap = [
                            'pending' => ['label' => 'Pending Pickup', 'class' => 'badge-warning'],
                            'active' => ['label' => 'Active', 'class' => 'badge-success'],
                            'returned' => ['label' => 'Returned', 'class' => 'badge-secondary'],
                            'overdue' => ['label' => 'Overdue', 'class' => 'badge-danger'],
                            'cancelled' => ['label' => 'Cancelled', 'class' => 'badge-secondary']
                        ];
                        $st = $statusMap[$searchResult['status']] ?? ['label' => $searchResult['status'], 'class' => 'badge-secondary'];
                        ?>
                        <span class="badge <?php echo $st['class']; ?>">
                            <?php echo $st['label']; ?>
                        </span>
                    </td>
                    <td>
                        <?php if ($searchResult['status'] === 'active' || $searchResult['status'] === 'overdue'): ?>
                        <form method="POST" style="display: inline;">
                            <input type="hidden" name="action" value="return">
                            <input type="hidden" name="id" value="<?php echo $searchResult['id']; ?>">
                            <button type="submit" class="btn btn-sm btn-success" onclick="return confirm('Confirm this customer returned the book?')">
                                <i class="fas fa-check"></i> Return Book
                            </button>
                        </form>
                        <?php elseif ($searchResult['status'] === 'pending'): ?>
                        <form method="POST" style="display: inline; gap: 8px;">
                            <input type="hidden" name="action" value="confirm_pickup">
                            <input type="hidden" name="id" value="<?php echo $searchResult['id']; ?>">
                            <button type="submit" class="btn btn-sm btn-primary" onclick="return confirm('Confirm this customer picked up the book?')">
                                <i class="fas fa-handshake"></i> Picked Up
                            </button>
                        </form>
                        <form method="POST" style="display: inline;">
                            <input type="hidden" name="action" value="cancel">
                            <input type="hidden" name="id" value="<?php echo $searchResult['id']; ?>">
                            <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Cancel this order?')">
                                <i class="fas fa-times"></i> Cancel
                            </button>
                        </form>
                        <?php else: ?>
                        <span style="color: var(--text-muted);">-</span>
                        <?php endif; ?>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
</div>
<?php endif; ?>

<!-- Filters & Table -->
<div class="dashboard-card">
    <div class="dashboard-card-header">
        <h3 class="dashboard-card-title">All Orders</h3>
        <div style="display: flex; gap: 8px;">
            <a href="rentals.php" class="dashboard-card-btn <?php echo empty($status) ? 'active' : ''; ?>">All</a>
            <a href="rentals.php?status=pending" class="dashboard-card-btn <?php echo $status === 'pending' ? 'active' : ''; ?>">Pending Pickup</a>
            <a href="rentals.php?status=active" class="dashboard-card-btn <?php echo $status === 'active' ? 'active' : ''; ?>">Active</a>
            <a href="rentals.php?status=overdue" class="dashboard-card-btn <?php echo $status === 'overdue' ? 'active' : ''; ?>">Overdue</a>
            <a href="rentals.php?status=returned" class="dashboard-card-btn <?php echo $status === 'returned' ? 'active' : ''; ?>">Returned</a>
        </div>
    </div>
    
    <div class="dashboard-card-body no-padding">
        <?php if (count($rentals) > 0): ?>
        <table class="data-table">
            <thead>
                <tr>
                    <th>Order Code</th>
                    <th>Customer</th>
                    <th>Book</th>
                    <th>Rental Date</th>
                    <th>Due Date</th>
                    <th>Total</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($rentals as $rental): 
                    $daysLeft = (strtotime($rental['due_date']) - time()) / (60 * 60 * 24);
                    $isOverdue = $daysLeft < 0 && $rental['status'] === 'active';
                ?>
                <tr <?php echo $isOverdue ? 'style="background: rgba(239,68,68,0.05);"' : ''; ?>>
                    <td style="font-weight: 600; color: var(--green-primary);">
                        <?php echo htmlspecialchars($rental['rental_code'] ?? 'N/A'); ?>
                    </td>
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
                            <img src="../../../assets/images/<?php echo htmlspecialchars($rental['cover_image'] ?? 'default_book.jpg'); ?>"
                                 alt="" class="table-book-cover"
                                 onerror="this.src='https://via.placeholder.com/36x48/242424/22c55e?text=B'">
                            <div class="table-book-info">
                                <div class="table-book-title"><?php echo htmlspecialchars($rental['title']); ?></div>
                                <div class="table-book-author"><?php echo htmlspecialchars($rental['author']); ?></div>
                            </div>
                        </div>
                    </td>
                    <td><?php echo date('d M, Y', strtotime($rental['rental_date'])); ?></td>
                    <td>
                        <?php echo date('d M, Y', strtotime($rental['due_date'])); ?>
                        <?php if ($rental['status'] === 'active'): ?>
                        <div style="font-size: 0.75rem; margin-top: 4px;">
                            <?php if ($isOverdue): ?>
                            <span style="color: var(--danger);"><?php echo abs(round($daysLeft)); ?> days overdue</span>
                            <?php else: ?>
                            <span style="color: var(--green-primary);"><?php echo round($daysLeft); ?> days left</span>
                            <?php endif; ?>
                        </div>
                        <?php endif; ?>
                    </td>
                    <td style="font-weight: 700; color: var(--green-primary);">
                        <?php echo number_format($rental['total_price'], 0, ',', '.'); ?> VND
                    </td>
                    <td>
                        <?php 
                        $statusMap = [
                            'pending' => ['label' => 'Pending Pickup', 'class' => 'badge-warning'],
                            'active' => ['label' => 'Active', 'class' => 'badge-success'],
                            'returned' => ['label' => 'Returned', 'class' => 'badge-secondary'],
                            'overdue' => ['label' => 'Overdue', 'class' => 'badge-danger'],
                            'cancelled' => ['label' => 'Cancelled', 'class' => 'badge-secondary']
                        ];
                        $st = $statusMap[$rental['status']] ?? ['label' => $rental['status'], 'class' => 'badge-secondary'];
                        ?>
                        <span class="badge <?php echo $st['class']; ?>">
                            <?php echo $st['label']; ?>
                        </span>
                    </td>
                    <td>
                        <?php if ($rental['status'] === 'active' || $rental['status'] === 'overdue'): ?>
                        <form method="POST" style="display: inline;">
                            <input type="hidden" name="action" value="return">
                            <input type="hidden" name="id" value="<?php echo $rental['id']; ?>">
                            <button type="submit" class="btn btn-sm btn-success" onclick="return confirm('Confirm this customer returned the book?')">
                                <i class="fas fa-check"></i> Return Book
                            </button>
                        </form>
                        <?php elseif ($rental['status'] === 'pending'): ?>
                        <form method="POST" style="display: inline; gap: 8px;">
                            <input type="hidden" name="action" value="confirm_pickup">
                            <input type="hidden" name="id" value="<?php echo $rental['id']; ?>">
                            <button type="submit" class="btn btn-sm btn-primary" onclick="return confirm('Confirm this customer picked up the book?')">
                                <i class="fas fa-handshake"></i> Picked Up
                            </button>
                        </form>
                        <form method="POST" style="display: inline;">
                            <input type="hidden" name="action" value="cancel">
                            <input type="hidden" name="id" value="<?php echo $rental['id']; ?>">
                            <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Cancel this order?')">
                                <i class="fas fa-times"></i> Cancel
                            </button>
                        </form>
                        <?php else: ?>
                        <span style="color: var(--text-muted);">-</span>
                        <?php endif; ?>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        <?php else: ?>
        <div style="text-align: center; padding: 60px 20px;">
            <i class="fas fa-inbox" style="font-size: 3rem; color: var(--text-muted); margin-bottom: 16px;"></i>
            <p style="color: var(--text-muted);">No orders found.</p>
        </div>
        <?php endif; ?>
    </div>
</div>

<?php require_once __DIR__ . '/../../templates/admin_footer.php'; ?>
