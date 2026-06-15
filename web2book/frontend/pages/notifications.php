<?php
require_once __DIR__ . '/../templates/header.php';

$pageTitle = 'Tin Nhắn & Thông Báo';

// Require login
$auth->requireLogin();
$userId = $_SESSION['user_id'];

// Initialize message controller
require_once __DIR__ . '/../../backend/controllers/MessageController.php';
$messageController = new MessageController();

// Handle actions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action'])) {
        switch ($_POST['action']) {
            case 'send':
                $result = $messageController->sendToAdmin($userId);
                if ($result['success']) {
                    $_SESSION['success'] = $result['message'];
                } else {
                    $_SESSION['error'] = $result['message'];
                }
                header('Location: notifications.php');
                exit;
            case 'mark_read':
                $messageController->markAllAsRead($userId);
                header('Location: notifications.php');
                exit;
            case 'delete':
                if (isset($_POST['message_id'])) {
                    $messageController->delete($_POST['message_id'], $userId);
                }
                header('Location: notifications.php');
                exit;
        }
    }
}

// Get messages for user
$allMessages = $messageController->getAllForUser($userId);
$unreadCount = $messageController->countUnread($userId);

// Separate by type
$receivedMessages = array_filter($allMessages, fn($m) => in_array($m['type'], ['admin_to_user', 'system']));
$sentMessages = array_filter($allMessages, fn($m) => $m['type'] === 'user_to_admin' && $m['sender_id'] == $userId);

// View single message
$viewMessage = null;
if (isset($_GET['view'])) {
    $viewMessage = $messageController->getById($_GET['view']);
    if ($viewMessage) {
        $messageController->markAsRead($viewMessage['id'], $userId);
        $unreadCount = $messageController->countUnread($userId);
    }
}

// Show compose form?
$showCompose = isset($_GET['compose']) || (isset($_SESSION['show_compose']) && ($_SESSION['show_compose'] = false));

// Current tab
$currentTab = $_GET['tab'] ?? 'received';
?>

<!-- Notifications Page - Simple & Easy to Use -->
<div class="notifications-page">
    
    <!-- Alerts -->
    <?php if (isset($_SESSION['success'])): ?>
    <div class="alert alert-success">
        <i class="fas fa-check-circle"></i>
        <?php echo $_SESSION['success']; unset($_SESSION['success']); ?>
    </div>
    <?php endif; ?>
    
    <?php if (isset($_SESSION['error'])): ?>
    <div class="alert alert-danger">
        <i class="fas fa-exclamation-circle"></i>
        <?php echo $_SESSION['error']; unset($_SESSION['error']); ?>
    </div>
    <?php endif; ?>
    
    <!-- Page Header -->
    <div class="page-header">
        <div class="header-left">
            <h1><i class="fas fa-bell"></i> Tin Nhắn & Thông Báo</h1>
            <p>Quản lý tin nhắn và thông báo của bạn</p>
        </div>
        <div class="header-right">
            <?php if ($unreadCount > 0): ?>
            <div class="unread-badge">
                <span class="badge-number"><?php echo $unreadCount; ?></span>
                <span>chưa đọc</span>
            </div>
            <?php endif; ?>
            <?php if ($showCompose): ?>
            <a href="notifications.php" class="btn btn-outline">
                <i class="fas fa-arrow-left"></i> Quay lại
            </a>
            <?php else: ?>
            <a href="notifications.php?compose=1" class="btn btn-primary">
                <i class="fas fa-paper-plane"></i> Gửi tin nhắn
            </a>
            <?php endif; ?>
        </div>
    </div>
    
    <!-- Compose Form (Inline) -->
    <?php if ($showCompose): ?>
    <div class="compose-section">
        <div class="section-card">
            <h2><i class="fas fa-paper-plane"></i> Gửi tin nhắn cho Admin</h2>
            
            <form method="POST" class="compose-form">
                <input type="hidden" name="action" value="send">
                
                <div class="form-group">
                    <label><i class="fas fa-heading"></i> Tiêu đề</label>
                    <input type="text" name="subject" class="form-control" placeholder="Nhập tiêu đề tin nhắn..." required>
                </div>
                
                <div class="form-group">
                    <label><i class="fas fa-comment"></i> Nội dung</label>
                    <textarea name="content" class="form-control" rows="6" placeholder="Nhập nội dung tin nhắn..." required></textarea>
                </div>
                
                <div class="form-actions">
                    <a href="notifications.php" class="btn btn-outline">
                        <i class="fas fa-times"></i> Hủy
                    </a>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-paper-plane"></i> Gửi tin nhắn
                    </button>
                </div>
            </form>
        </div>
    </div>
    <?php else: ?>
    
    <!-- Quick Stats -->
    <div class="quick-stats">
        <a href="notifications.php?tab=received" class="stat-item <?php echo $currentTab === 'received' ? 'active' : ''; ?>">
            <div class="stat-icon inbox">
                <i class="fas fa-inbox"></i>
            </div>
            <div class="stat-content">
                <span class="stat-title">Hộp thư đến</span>
                <span class="stat-count"><?php echo count($receivedMessages); ?> tin</span>
            </div>
            <?php if ($unreadCount > 0): ?>
            <div class="stat-badge"><?php echo $unreadCount; ?></div>
            <?php endif; ?>
        </a>
        
        <a href="notifications.php?tab=sent" class="stat-item <?php echo $currentTab === 'sent' ? 'active' : ''; ?>">
            <div class="stat-icon sent">
                <i class="fas fa-paper-plane"></i>
            </div>
            <div class="stat-content">
                <span class="stat-title">Đã gửi</span>
                <span class="stat-count"><?php echo count($sentMessages); ?> tin</span>
            </div>
        </a>
        
        <?php if ($unreadCount > 0): ?>
        <form method="POST" class="stat-item mark-all">
            <input type="hidden" name="action" value="mark_read">
            <div class="stat-icon mark">
                <i class="fas fa-check-double"></i>
            </div>
            <div class="stat-content">
                <span class="stat-title">Đánh dấu đã đọc</span>
                <span class="stat-count">Tất cả</span>
            </div>
        </form>
        <?php endif; ?>
    </div>
    
    <!-- Messages List -->
    <div class="messages-section">
        <?php 
        $displayMessages = $currentTab === 'received' ? $receivedMessages : $sentMessages;
        ?>
        
        <?php if (count($displayMessages) > 0): ?>
        <div class="messages-list-simple">
            <?php foreach ($displayMessages as $msg): ?>
            <a href="notifications.php?view=<?php echo $msg['id']; ?>&tab=<?php echo $currentTab; ?>" 
               class="message-card <?php echo $msg['is_read'] ? '' : 'unread'; ?>">
                
                <div class="message-icon">
                    <?php if ($msg['type'] === 'system'): ?>
                    <div class="icon-circle system">
                        <i class="fas fa-bell"></i>
                    </div>
                    <?php elseif ($msg['type'] === 'admin_to_user'): ?>
                    <div class="icon-circle admin">
                        <i class="fas fa-headset"></i>
                    </div>
                    <?php else: ?>
                    <div class="icon-circle user">
                        <i class="fas fa-paper-plane"></i>
                    </div>
                    <?php endif; ?>
                </div>
                
                <div class="message-info">
                    <div class="message-top">
                        <span class="message-type">
                            <?php 
                            if ($msg['type'] === 'system') echo '<i class="fas fa-cog"></i> Hệ thống';
                            elseif ($msg['type'] === 'admin_to_user') echo '<i class="fas fa-user-shield"></i> Admin';
                            else echo '<i class="fas fa-user"></i> Bạn';
                            ?>
                        </span>
                        <span class="message-time"><?php echo timeAgo($msg['created_at']); ?></span>
                    </div>
                    <div class="message-subject"><?php echo htmlspecialchars($msg['subject'] ?: '(Không có tiêu đề)'); ?></div>
                    <div class="message-preview"><?php echo htmlspecialchars(mb_substr($msg['content'], 0, 80)); ?>...</div>
                </div>
                
                <?php if (!$msg['is_read']): ?>
                <div class="unread-dot"></div>
                <?php endif; ?>
            </a>
            <?php endforeach; ?>
        </div>
        
        <?php else: ?>
        <div class="empty-state">
            <div class="empty-icon">
                <?php if ($currentTab === 'received'): ?>
                <i class="fas fa-inbox"></i>
                <?php else: ?>
                <i class="fas fa-paper-plane"></i>
                <?php endif; ?>
            </div>
            <h3>Không có tin nhắn</h3>
            <p>
                <?php if ($currentTab === 'received'): ?>
                Bạn chưa có thông báo nào.
                <?php else: ?>
                Bạn chưa gửi tin nhắn nào.
                <?php endif; ?>
            </p>
            <a href="notifications.php?compose=1" class="btn btn-primary">
                <i class="fas fa-paper-plane"></i> Gửi tin nhắn cho Admin
            </a>
        </div>
        <?php endif; ?>
    </div>
    <?php endif; ?>
    
    <!-- Message Detail View -->
    <?php if ($viewMessage): ?>
    <div class="message-detail-overlay">
        <div class="message-detail-card">
            <div class="detail-header">
                <a href="notifications.php?tab=<?php echo $currentTab; ?>" class="back-link">
                    <i class="fas fa-arrow-left"></i> Quay lại
                </a>
                <form method="POST" class="delete-form" onsubmit="return confirm('Xóa tin nhắn này?')">
                    <input type="hidden" name="action" value="delete">
                    <input type="hidden" name="message_id" value="<?php echo $viewMessage['id']; ?>">
                    <button type="submit" class="btn-icon-delete">
                        <i class="fas fa-trash"></i>
                    </button>
                </form>
            </div>
            
            <div class="detail-content">
                <div class="detail-sender">
                    <?php if ($viewMessage['type'] === 'system'): ?>
                    <div class="sender-avatar system">
                        <i class="fas fa-bell"></i>
                    </div>
                    <div class="sender-info">
                        <span class="sender-name">Hệ thống</span>
                        <span class="sender-time"><?php echo date('d/m/Y H:i', strtotime($viewMessage['created_at'])); ?></span>
                    </div>
                    <?php elseif ($viewMessage['type'] === 'admin_to_user'): ?>
                    <div class="sender-avatar admin">
                        <i class="fas fa-headset"></i>
                    </div>
                    <div class="sender-info">
                        <span class="sender-name">Admin - MÂY MƠ BOOK</span>
                        <span class="sender-time"><?php echo date('d/m/Y H:i', strtotime($viewMessage['created_at'])); ?></span>
                    </div>
                    <?php else: ?>
                    <div class="sender-avatar user">
                        <i class="fas fa-paper-plane"></i>
                    </div>
                    <div class="sender-info">
                        <span class="sender-name">Bạn đã gửi</span>
                        <span class="sender-time"><?php echo date('d/m/Y H:i', strtotime($viewMessage['created_at'])); ?></span>
                    </div>
                    <?php endif; ?>
                </div>
                
                <h2 class="detail-subject"><?php echo htmlspecialchars($viewMessage['subject'] ?: '(Không có tiêu đề)'); ?></h2>
                
                <div class="detail-body">
                    <?php echo nl2br(htmlspecialchars($viewMessage['content'])); ?>
                </div>
                
                <div class="detail-actions">
                    <?php if ($viewMessage['type'] !== 'user_to_admin'): ?>
                    <a href="notifications.php?compose=1&reply_to=<?php echo $viewMessage['id']; ?>" class="btn btn-primary">
                        <i class="fas fa-reply"></i> Trả lời
                    </a>
                    <?php else: ?>
                    <span class="badge-sent">
                        <i class="fas fa-check"></i> Đã gửi cho Admin
                    </span>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
    <?php endif; ?>
</div>

<style>
.notifications-page {
    max-width: 900px;
    margin: 0 auto;
    padding: 30px 20px;
    position: relative;
}

/* Page Header */
.page-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 30px;
    flex-wrap: wrap;
    gap: 20px;
}

.header-left h1 {
    font-size: 1.75rem;
    font-weight: 700;
    color: var(--text-primary);
    margin-bottom: 4px;
    display: flex;
    align-items: center;
    gap: 12px;
}

.header-left h1 i {
    color: var(--primary);
}

.header-left p {
    color: var(--text-muted);
    font-size: 0.95rem;
}

.header-right {
    display: flex;
    align-items: center;
    gap: 12px;
}

.unread-badge {
    display: flex;
    align-items: center;
    gap: 8px;
    padding: 8px 16px;
    background: var(--danger-bg);
    border-radius: 20px;
    color: var(--danger);
    font-size: 0.9rem;
    font-weight: 600;
}

.badge-number {
    background: var(--danger);
    color: white;
    padding: 2px 8px;
    border-radius: 10px;
    font-size: 0.85rem;
}

/* Compose Section */
.compose-section {
    margin-bottom: 30px;
}

.section-card {
    background: var(--bg-card);
    border: 1px solid var(--border-color);
    border-radius: 16px;
    padding: 30px;
}

.section-card h2 {
    font-size: 1.3rem;
    font-weight: 700;
    color: var(--text-primary);
    margin-bottom: 24px;
    display: flex;
    align-items: center;
    gap: 10px;
}

.section-card h2 i {
    color: var(--primary);
}

/* Quick Stats */
.quick-stats {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 16px;
    margin-bottom: 30px;
}

.stat-item {
    display: flex;
    align-items: center;
    gap: 14px;
    padding: 20px;
    background: var(--bg-card);
    border: 2px solid var(--border-color);
    border-radius: 16px;
    text-decoration: none;
    color: inherit;
    transition: all 0.2s ease;
    position: relative;
    cursor: pointer;
}

.stat-item:hover {
    border-color: var(--primary);
    transform: translateY(-2px);
    box-shadow: 0 4px 15px rgba(0,0,0,0.08);
}

.stat-item.active {
    border-color: var(--primary);
    background: var(--primary-bg);
}

.stat-icon {
    width: 50px;
    height: 50px;
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.3rem;
}

.stat-icon.inbox {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
}

.stat-icon.sent {
    background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
    color: white;
}

.stat-icon.mark {
    background: linear-gradient(135deg, #43e97b 0%, #38f9d7 100%);
    color: white;
}

.stat-content {
    flex: 1;
}

.stat-title {
    display: block;
    font-weight: 600;
    color: var(--text-primary);
    font-size: 0.95rem;
}

.stat-count {
    display: block;
    font-size: 0.85rem;
    color: var(--text-muted);
}

.stat-badge {
    position: absolute;
    top: -8px;
    right: -8px;
    background: var(--danger);
    color: white;
    width: 26px;
    height: 26px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 0.8rem;
    font-weight: 700;
    box-shadow: 0 2px 8px rgba(220,38,38,0.4);
}

/* Messages List */
.messages-list-simple {
    display: flex;
    flex-direction: column;
    gap: 12px;
}

.message-card {
    display: flex;
    align-items: center;
    gap: 16px;
    padding: 20px;
    background: var(--bg-card);
    border: 1px solid var(--border-color);
    border-radius: 16px;
    text-decoration: none;
    color: inherit;
    transition: all 0.2s ease;
    position: relative;
}

.message-card:hover {
    border-color: var(--primary);
    transform: translateX(4px);
    box-shadow: 0 4px 15px rgba(0,0,0,0.05);
}

.message-card.unread {
    background: linear-gradient(90deg, rgba(102,126,234,0.05) 0%, var(--bg-card) 100%);
    border-left: 4px solid var(--primary);
}

.message-icon {
    flex-shrink: 0;
}

.icon-circle {
    width: 50px;
    height: 50px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.2rem;
}

.icon-circle.admin {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
}

.icon-circle.system {
    background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
    color: white;
}

.icon-circle.user {
    background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
    color: white;
}

.message-info {
    flex: 1;
    min-width: 0;
}

.message-top {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 6px;
}

.message-type {
    font-size: 0.85rem;
    color: var(--text-muted);
    font-weight: 500;
}

.message-type i {
    margin-right: 4px;
}

.message-time {
    font-size: 0.8rem;
    color: var(--text-muted);
}

.message-subject {
    font-weight: 600;
    color: var(--text-primary);
    margin-bottom: 4px;
    font-size: 1rem;
}

.unread .message-subject {
    font-weight: 700;
}

.message-preview {
    font-size: 0.85rem;
    color: var(--text-muted);
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}

.unread-dot {
    width: 10px;
    height: 10px;
    background: var(--primary);
    border-radius: 50%;
    flex-shrink: 0;
    animation: pulse 2s infinite;
}

@keyframes pulse {
    0%, 100% { opacity: 1; }
    50% { opacity: 0.5; }
}

/* Empty State */
.empty-state {
    text-align: center;
    padding: 60px 40px;
    background: var(--bg-card);
    border-radius: 16px;
    border: 1px solid var(--border-color);
}

.empty-icon {
    width: 80px;
    height: 80px;
    margin: 0 auto 20px;
    background: var(--bg-secondary);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 2.5rem;
    color: var(--text-muted);
}

.empty-state h3 {
    color: var(--text-primary);
    margin-bottom: 8px;
}

.empty-state p {
    color: var(--text-muted);
    margin-bottom: 20px;
}

/* Message Detail Overlay */
.message-detail-overlay {
    position: fixed;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: rgba(0,0,0,0.5);
    display: flex;
    align-items: center;
    justify-content: center;
    z-index: 100;
    padding: 20px;
}

.message-detail-card {
    background: white;
    border-radius: 20px;
    width: 100%;
    max-width: 600px;
    max-height: 90vh;
    overflow-y: auto;
}

.detail-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 20px 24px;
    border-bottom: 1px solid var(--border-color);
}

.back-link {
    display: flex;
    align-items: center;
    gap: 8px;
    color: var(--text-secondary);
    text-decoration: none;
    font-weight: 500;
    transition: color 0.2s;
}

.back-link:hover {
    color: var(--primary);
}

.btn-icon-delete {
    width: 40px;
    height: 40px;
    border-radius: 8px;
    border: 1px solid var(--border-color);
    background: white;
    color: var(--text-secondary);
    cursor: pointer;
    transition: all 0.2s;
}

.btn-icon-delete:hover {
    background: var(--danger-bg);
    color: var(--danger);
    border-color: var(--danger);
}

.detail-content {
    padding: 24px;
}

.detail-sender {
    display: flex;
    align-items: center;
    gap: 14px;
    margin-bottom: 20px;
}

.sender-avatar {
    width: 52px;
    height: 52px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.3rem;
    color: white;
}

.sender-avatar.admin {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
}

.sender-avatar.system {
    background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
}

.sender-avatar.user {
    background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
}

.sender-info {
    display: flex;
    flex-direction: column;
}

.sender-name {
    font-weight: 700;
    font-size: 1.1rem;
    color: var(--text-primary);
}

.sender-time {
    font-size: 0.85rem;
    color: var(--text-muted);
}

.detail-subject {
    font-size: 1.3rem;
    font-weight: 700;
    color: var(--text-primary);
    margin-bottom: 16px;
    padding-bottom: 16px;
    border-bottom: 1px solid var(--border-color);
}

.detail-body {
    padding: 20px;
    background: var(--bg-secondary);
    border-radius: 12px;
    font-size: 1rem;
    line-height: 1.8;
    color: var(--text-secondary);
    margin-bottom: 24px;
}

.detail-actions {
    display: flex;
    gap: 12px;
}

.badge-sent {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    padding: 12px 20px;
    background: var(--success-bg);
    color: var(--success);
    border-radius: 8px;
    font-weight: 500;
}

/* Form */
.compose-form .form-group {
    margin-bottom: 20px;
}

.compose-form label {
    display: flex;
    align-items: center;
    gap: 8px;
    font-weight: 600;
    color: var(--text-primary);
    margin-bottom: 8px;
}

.compose-form label i {
    color: var(--primary);
}

.form-actions {
    display: flex;
    justify-content: flex-end;
    gap: 12px;
    margin-top: 20px;
}

/* Alert */
.alert {
    padding: 14px 20px;
    border-radius: 12px;
    margin-bottom: 20px;
    display: flex;
    align-items: center;
    gap: 12px;
    font-weight: 500;
}

.alert-success {
    background: var(--success-bg);
    color: var(--success);
    border: 1px solid var(--success);
}

.alert-danger {
    background: var(--danger-bg);
    color: var(--danger);
    border: 1px solid var(--danger);
}

/* Buttons */
.btn {
    padding: 12px 24px;
    border-radius: 10px;
    font-weight: 600;
    font-size: 0.95rem;
    cursor: pointer;
    display: inline-flex;
    align-items: center;
    gap: 8px;
    transition: all 0.2s ease;
    border: none;
    text-decoration: none;
}

.btn-primary {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
}

.btn-primary:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 15px rgba(102,126,234,0.4);
}

.btn-outline {
    background: transparent;
    color: var(--text-secondary);
    border: 1px solid var(--border-color);
}

.btn-outline:hover {
    background: var(--bg-secondary);
}

/* Responsive */
@media (max-width: 768px) {
    .page-header {
        flex-direction: column;
        align-items: flex-start;
    }
    
    .quick-stats {
        grid-template-columns: 1fr;
    }
    
    .stat-item {
        padding: 16px;
    }
    
    .message-card {
        padding: 16px;
    }
    
    .icon-circle {
        width: 44px;
        height: 44px;
    }
    
    .detail-actions {
        flex-direction: column;
    }
    
    .detail-actions .btn {
        width: 100%;
        justify-content: center;
    }
    
    .form-actions {
        flex-direction: column;
    }
    
    .form-actions .btn {
        width: 100%;
        justify-content: center;
    }
}
</style>

<?php require_once __DIR__ . '/../templates/footer.php'; ?>
