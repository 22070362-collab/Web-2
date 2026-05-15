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
            <p class="page-header-subtitle">Quản lý cài đặt hệ thống</p>
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
                    <h4 class="settings-section-title">Thông Tin Cửa Hàng</h4>
                    <div class="form-row">
                        <div class="form-group">
                            <label>Tên Cửa Hàng</label>
                            <input type="text" class="form-control" value="MÂY MƠ BOOK" placeholder="Tên cửa hàng">
                        </div>
                        <div class="form-group">
                            <label>Email Liên Hệ</label>
                            <input type="email" class="form-control" value="contact@maymobook.vn" placeholder="Email">
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group">
                            <label>Số Điện Thoại</label>
                            <input type="tel" class="form-control" value="0123-456-789" placeholder="Số điện thoại">
                        </div>
                        <div class="form-group">
                            <label>Địa Chỉ</label>
                            <input type="text" class="form-control" value="123 Đường ABC, TP.HCM" placeholder="Địa chỉ">
                        </div>
                    </div>
                </div>

                <div class="settings-section">
                    <h4 class="settings-section-title">Rental Settings</h4>
                    <div class="form-row">
                        <div class="form-group">
                            <label>Số Ngày Thuê Tối Đa</label>
                            <input type="number" class="form-control" value="30" min="1" max="365">
                            <small class="form-text">Số ngày tối đa cho một lần thuê sách</small>
                        </div>
                        <div class="form-group">
                            <label>Số Sách Thuê Tối Đa</label>
                            <input type="number" class="form-control" value="5" min="1" max="20">
                            <small class="form-text">Số sách tối đa một user có thể thuê</small>
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group">
                            <label>Phí Trễ Hạn (VNĐ/ngày)</label>
                            <input type="number" class="form-control" value="5000" min="0">
                            <small class="form-text">Phí phạt khi trả sách trễ hạn</small>
                        </div>
                        <div class="form-group">
                            <label>Tiền Đặt Cọc (VNĐ)</label>
                            <input type="number" class="form-control" value="50000" min="0">
                            <small class="form-text">Tiền đặt cọc khi thuê sách</small>
                        </div>
                    </div>
                </div>

                <div class="settings-section">
                    <h4 class="settings-section-title">Shipping Settings</h4>
                    <div class="form-row">
                        <div class="form-group">
                            <label>Phí Ship Miễn Phí (VNĐ)</label>
                            <input type="number" class="form-control" value="100000" min="0">
                            <small class="form-text">Miễn phí ship cho đơn từ giá trị này</small>
                        </div>
                        <div class="form-group">
                            <label>Phí Ship Mặc Định (VNĐ)</label>
                            <input type="number" class="form-control" value="20000" min="0">
                            <small class="form-text">Phí ship cho đơn dưới giá trị miễn phí</small>
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
                    <i class="fas fa-user-cog"></i> Tài Khoản Admin
                </h3>
            </div>
            <div class="dashboard-card-body">
                <form>
                    <div class="form-group">
                        <label>Tên Đăng Nhập</label>
                        <input type="text" class="form-control" value="<?php echo htmlspecialchars($user['username'] ?? 'admin'); ?>" disabled>
                    </div>
                    <div class="form-group">
                        <label>Email</label>
                        <input type="email" class="form-control" value="<?php echo htmlspecialchars($user['email'] ?? 'admin@example.com'); ?>">
                    </div>
                    <div class="form-group">
                        <label>Họ Tên</label>
                        <input type="text" class="form-control" value="<?php echo htmlspecialchars($user['full_name'] ?? 'Admin'); ?>">
                    </div>
                    <div class="form-divider"></div>
                    <div class="form-group">
                        <label>Đổi Mật Khẩu</label>
                        <input type="password" class="form-control" placeholder="Mật khẩu mới">
                    </div>
                    <div class="form-group">
                        <label>Xác Nhận Mật Khẩu</label>
                        <input type="password" class="form-control" placeholder="Xác nhận mật khẩu">
                    </div>
                    <button type="submit" class="btn btn-primary w-full">
                        <i class="fas fa-key"></i> Cập Nhật Tài Khoản
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
