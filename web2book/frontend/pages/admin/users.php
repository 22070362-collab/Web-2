<?php
require_once __DIR__ . '/../../templates/admin_header.php';

$auth->requireAdmin();

require_once __DIR__ . '/../../../backend/models/User.php';

$userModel = new User();
$message = '';
$messageType = '';

// Handle delete user
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'delete') {
    if ($userModel->delete(intval($_POST['id']))) {
        $message = 'User deleted successfully.';
        $messageType = 'success';
    } else {
        $message = 'Cannot delete this user.';
        $messageType = 'danger';
    }
}

$users = $userModel->getAll();
$totalUsers = $userModel->countAll();
$adminCount = count(array_filter($users, fn($u) => $u['role'] === 'admin'));
$customerCount = count(array_filter($users, fn($u) => $u['role'] === 'user'));
?>

<!-- Page Header -->
<div class="page-header">
    <div class="page-header-left">
        <div class="page-header-icon" style="background: rgba(245, 158, 11, 0.15); color: var(--warning);">
            <i class="fas fa-users"></i>
        </div>
        <div>
            <h1 class="page-header-title">Users</h1>
            <p class="page-header-subtitle">Manage system users</p>
        </div>
    </div>
    <div>
        <a href="index.php" class="btn btn-outline btn-sm">
            <i class="fas fa-arrow-left"></i> Back to Dashboard
        </a>
    </div>
</div>

<!-- Stats -->
<div class="dashboard-stats-grid" style="grid-template-columns: repeat(3, 1fr);">
    <div class="stat-card-admin">
        <div class="stat-card-header">
            <div class="stat-card-icon green">
                <i class="fas fa-users"></i>
            </div>
        </div>
        <div class="stat-card-value"><?php echo $totalUsers; ?></div>
        <div class="stat-card-label">Total Users</div>
    </div>
    
    <div class="stat-card-admin">
        <div class="stat-card-header">
            <div class="stat-card-icon purple">
                <i class="fas fa-user"></i>
            </div>
        </div>
        <div class="stat-card-value"><?php echo $customerCount; ?></div>
        <div class="stat-card-label">Customers</div>
    </div>
    
    <div class="stat-card-admin">
        <div class="stat-card-header">
            <div class="stat-card-icon orange">
                <i class="fas fa-shield-alt"></i>
            </div>
        </div>
        <div class="stat-card-value"><?php echo $adminCount; ?></div>
        <div class="stat-card-label">Admins</div>
    </div>
</div>

<!-- Messages -->
<?php if ($message): ?>
<div class="alert alert-<?php echo $messageType; ?>">
    <i class="fas fa-<?php echo $messageType === 'success' ? 'check-circle' : 'exclamation-circle'; ?>"></i>
    <?php echo htmlspecialchars($message); ?>
</div>
<?php endif; ?>

<!-- Users Table -->
<div class="dashboard-card">
    <div class="dashboard-card-header">
        <h3 class="dashboard-card-title">All Users (<?php echo count($users); ?>)</h3>
    </div>
    
    <div class="dashboard-card-body no-padding">
        <?php if (count($users) > 0): ?>
        <table class="data-table">
            <thead>
                <tr>
                    <th>#</th>
                    <th>User</th>
                    <th>Email</th>
                    <th>Phone</th>
                    <th>Role</th>
                    <th>Joined</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($users as $index => $user): ?>
                <tr>
                    <td><?php echo $index + 1; ?></td>
                    <td>
                        <div class="table-user">
                            <div class="table-user-avatar" style="background: var(--green-bg); color: var(--green-primary);">
                                <?php echo strtoupper(substr($user['full_name'], 0, 1)); ?>
                            </div>
                            <div class="table-user-info">
                                <div class="table-user-name"><?php echo htmlspecialchars($user['full_name']); ?></div>
                                <div class="table-user-email">@<?php echo htmlspecialchars($user['username']); ?></div>
                            </div>
                        </div>
                    </td>
                    <td><?php echo htmlspecialchars($user['email']); ?></td>
                    <td><?php echo htmlspecialchars($user['phone'] ?: '-'); ?></td>
                    <td>
                        <?php if ($user['role'] === 'admin'): ?>
                        <span class="badge badge-warning">
                            <i class="fas fa-crown"></i> Admin
                        </span>
                        <?php else: ?>
                        <span class="badge badge-info">
                            <i class="fas fa-user"></i> Customer
                        </span>
                        <?php endif; ?>
                    </td>
                    <td><?php echo date('d M, Y', strtotime($user['created_at'])); ?></td>
                    <td>
                        <?php if ($user['role'] !== 'admin'): ?>
                        <form method="POST" style="display: inline;">
                            <input type="hidden" name="action" value="delete">
                            <input type="hidden" name="id" value="<?php echo $user['id']; ?>">
                            <button type="submit" class="btn btn-icon btn-danger" onclick="return confirm('Delete this user? This cannot be undone.')">
                                <i class="fas fa-trash"></i>
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
            <i class="fas fa-users" style="font-size: 3rem; color: var(--text-muted); margin-bottom: 16px;"></i>
            <p style="color: var(--text-muted);">No users found.</p>
        </div>
        <?php endif; ?>
    </div>
</div>

<?php require_once __DIR__ . '/../../templates/admin_footer.php'; ?>
