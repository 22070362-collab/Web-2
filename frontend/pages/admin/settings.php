<?php
require_once __DIR__ . '/../../templates/admin_header.php';

$pageTitle = 'Settings';
?>

<!-- Page Header -->
<div class="page-header">
    <div class="page-header-left">
        <div class="page-header-icon">
            <i class="fas fa-cog"></i>
        </div>
        <div>
            <h1 class="page-header-title">Settings</h1>
            <p class="page-header-subtitle">Manage system settings</p>
        </div>
    </div>
</div>

<!-- Settings Grid -->
<div class="dashboard-grid dashboard-grid-full">
    
    <!-- General Settings -->
    <div class="dashboard-card">
        <div class="dashboard-card-header">
            <h3 class="dashboard-card-title">
                <i class="fas fa-cog"></i> General Settings
            </h3>
        </div>
        <div class="dashboard-card-body">
            <form>
                <div class="settings-section">
                    <h4 class="settings-section-title">Store Information</h4>
                    <div class="form-row">
                        <div class="form-group">
                            <label>Store Name</label>
                            <input type="text" class="form-control" value="MAY MO BOOK" placeholder="Store name">
                        </div>
                        <div class="form-group">
                            <label>Contact Email</label>
                            <input type="email" class="form-control" value="contact@maymobook.vn" placeholder="Email">
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group">
                            <label>Phone Number</label>
                            <input type="tel" class="form-control" value="0123-456-789" placeholder="Phone number">
                        </div>
                        <div class="form-group">
                            <label>Address</label>
                            <input type="text" class="form-control" value="123 ABC Street, Ho Chi Minh City" placeholder="Address">
                        </div>
                    </div>
                </div>

                <div class="settings-section">
                    <h4 class="settings-section-title">Rental Settings</h4>
                    <div class="form-row">
                        <div class="form-group">
                            <label>Maximum Rental Days</label>
                            <input type="number" class="form-control" value="30" min="1" max="365">
                            <small class="form-text">Maximum days allowed for one rental</small>
                        </div>
                        <div class="form-group">
                            <label>Maximum Books per Rental</label>
                            <input type="number" class="form-control" value="5" min="1" max="20">
                            <small class="form-text">Maximum books a user can rent</small>
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group">
                            <label>Late Fee (VND/day)</label>
                            <input type="number" class="form-control" value="5000" min="0">
                            <small class="form-text">Penalty fee for late returns</small>
                        </div>
                        <div class="form-group">
                            <label>Deposit (VND)</label>
                            <input type="number" class="form-control" value="50000" min="0">
                            <small class="form-text">Deposit required when renting books</small>
                        </div>
                    </div>
                </div>

                <div class="settings-section">
                    <h4 class="settings-section-title">Shipping Settings</h4>
                    <div class="form-row">
                        <div class="form-group">
                            <label>Free Shipping Threshold (VND)</label>
                            <input type="number" class="form-control" value="100000" min="0">
                            <small class="form-text">Free shipping for orders from this value</small>
                        </div>
                        <div class="form-group">
                            <label>Default Shipping Fee (VND)</label>
                            <input type="number" class="form-control" value="20000" min="0">
                            <small class="form-text">Shipping fee below the free-shipping threshold</small>
                        </div>
                    </div>
                </div>

                <div class="form-actions">
                    <button type="button" class="btn btn-outline">
                        <i class="fas fa-undo"></i> Reset
                    </button>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i> Save Changes
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Sidebar Settings -->
    <div style="display: flex; flex-direction: column; gap: 24px;">
        
        <!-- Account Settings -->
        <div class="dashboard-card">
            <div class="dashboard-card-header">
                <h3 class="dashboard-card-title">
                    <i class="fas fa-user-cog"></i> Admin Account
                </h3>
            </div>
            <div class="dashboard-card-body">
                <form>
                    <div class="form-group">
                        <label>Username</label>
                        <input type="text" class="form-control" value="<?php echo htmlspecialchars($user['username'] ?? 'admin'); ?>" disabled>
                    </div>
                    <div class="form-group">
                        <label>Email</label>
                        <input type="email" class="form-control" value="<?php echo htmlspecialchars($user['email'] ?? 'admin@example.com'); ?>">
                    </div>
                    <div class="form-group">
                        <label>Full Name</label>
                        <input type="text" class="form-control" value="<?php echo htmlspecialchars($user['full_name'] ?? 'Admin'); ?>">
                    </div>
                    <div class="form-divider"></div>
                    <div class="form-group">
                        <label>Change Password</label>
                        <input type="password" class="form-control" placeholder="New password">
                    </div>
                    <div class="form-group">
                        <label>Confirm Password</label>
                        <input type="password" class="form-control" placeholder="Confirm password">
                    </div>
                    <button type="submit" class="btn btn-primary w-full">
                        <i class="fas fa-key"></i> Update Account
                    </button>
                </form>
            </div>
        </div>

        <!-- System Status -->
        <div class="dashboard-card">
            <div class="dashboard-card-header">
                <h3 class="dashboard-card-title">
                    <i class="fas fa-server"></i> System Status
                </h3>
            </div>
            <div class="dashboard-card-body">
                <div class="status-list">
                    <div class="status-item">
                        <div class="status-info">
                            <i class="fas fa-database"></i>
                            <span>Database</span>
                        </div>
                        <span class="badge badge-success">
                            <i class="fas fa-check-circle"></i> Online
                        </span>
                    </div>
                    <div class="status-item">
                        <div class="status-info">
                            <i class="fas fa-server"></i>
                            <span>Server</span>
                        </div>
                        <span class="badge badge-success">
                            <i class="fas fa-check-circle"></i> Running
                        </span>
                    </div>
                    <div class="status-item">
                        <div class="status-info">
                            <i class="fas fa-shield-alt"></i>
                            <span>SSL</span>
                        </div>
                        <span class="badge badge-success">
                            <i class="fas fa-check-circle"></i> Active
                        </span>
                    </div>
                    <div class="status-item">
                        <div class="status-info">
                            <i class="fas fa-clock"></i>
                            <span>Last Backup</span>
                        </div>
                        <span class="badge badge-info">Just now</span>
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>

<style>
.settings-section {
    margin-bottom: 32px;
    padding-bottom: 24px;
    border-bottom: 1px solid var(--border-color);
}

.settings-section:last-of-type {
    border-bottom: none;
    margin-bottom: 24px;
}

.settings-section-title {
    font-size: 1rem;
    font-weight: 600;
    color: var(--text-primary);
    margin-bottom: 20px;
    display: flex;
    align-items: center;
    gap: 10px;
}

.settings-section-title::before {
    content: '';
    width: 4px;
    height: 16px;
    background: var(--primary);
    border-radius: 2px;
}

.form-row {
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    gap: 20px;
}

.form-group {
    margin-bottom: 16px;
}

.form-group label {
    display: block;
    font-weight: 500;
    margin-bottom: 8px;
    color: var(--text-primary);
    font-size: 0.9rem;
}

.form-text {
    display: block;
    font-size: 0.8rem;
    color: var(--text-muted);
    margin-top: 6px;
}

.form-divider {
    height: 1px;
    background: var(--border-color);
    margin: 20px 0;
}

.form-actions {
    display: flex;
    justify-content: flex-end;
    gap: 12px;
    padding-top: 20px;
    border-top: 1px solid var(--border-color);
}

.w-full {
    width: 100%;
}

.status-list {
    display: flex;
    flex-direction: column;
    gap: 12px;
}

.status-item {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 12px;
    background: var(--bg-secondary);
    border-radius: var(--radius);
}

.status-info {
    display: flex;
    align-items: center;
    gap: 10px;
    color: var(--text-secondary);
}

.status-info i {
    width: 20px;
    color: var(--primary);
}

@media (max-width: 768px) {
    .form-row {
        grid-template-columns: 1fr;
    }
}
</style>

<?php require_once __DIR__ . '/../../templates/admin_footer.php'; ?>
