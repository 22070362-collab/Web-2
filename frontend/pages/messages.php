<?php
require_once __DIR__ . '/../templates/header.php';

$pageTitle = 'Tin Nhắn';

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
                header('Location: messages.php');
                exit;
                break;
            case 'mark_read':
                $messageController->markAllAsRead($userId);
                header('Location: messages.php');
                exit;
                break;
            case 'delete':
                if (isset($_POST['message_id'])) {
                    $messageController->delete($_POST['message_id'], $userId);
                }
                header('Location: messages.php');
                exit;
                break;
        }
    }
}

// Get messages for user
$allMessages = $messageController->getAllForUser($userId);
$unreadCount = $messageController->countUnread($userId);

// Separate messages by type for display
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

// Get notifications for dropdown
$notifications = $messageController->getNotificationsForUser($userId, 5);

// Current tab
$currentTab = $_GET['tab'] ?? 'received';
?>

<!-- Messages Page - Modern Email Style -->
<div class="messages-app">
    
    <!-- Sidebar Navigation -->
    <aside class="messages-sidebar">
        <div class="sidebar-header">
            <button class="compose-btn" onclick="window.location.href='messages.php?compose=1'">
                <i class="fas fa-plus"></i>
                <span>Soạn tin nhắn</span>
            </button>
        </div>
        
        <nav class="sidebar-nav">
            <a href="messages.php?tab=received" class="nav-item <?php echo $currentTab === 'received' ? 'active' : ''; ?>">
                <i class="fas fa-inbox"></i>
                <span>Hộp thư đến</span>
                <?php if ($unreadCount > 0): ?>
                <span class="badge"><?php echo $unreadCount; ?></span>
                <?php endif; ?>
            </a>
            <a href="messages.php?tab=sent" class="nav-item <?php echo $currentTab === 'sent' ? 'active' : ''; ?>">
                <i class="fas fa-paper-plane"></i>
                <span>Đã gửi</span>
            </a>
        </nav>
        
        <div class="sidebar-footer">
            <div class="user-info">
                <div class="user-avatar-small">
                    <?php echo strtoupper(substr($user['full_name'] ?? 'U', 0, 1)); ?>
                </div>
                <div class="user-details">
                    <span class="user-name"><?php echo htmlspecialchars($user['full_name'] ?? 'User'); ?></span>
                    <span class="user-role">Thành viên</span>
                </div>
            </div>
        </div>
    </aside>
    
    <!-- Messages List Panel -->
    <section class="messages-list-panel <?php echo $viewMessage ? 'hidden-mobile' : ''; ?>">
        <div class="panel-header">
            <h1 class="panel-title">
                <?php if ($currentTab === 'received'): ?>
                <i class="fas fa-inbox"></i> Hộp thư đến
                <?php else: ?>
                <i class="fas fa-paper-plane"></i> Tin nhắn đã gửi
                <?php endif; ?>
            </h1>
            <div class="panel-actions">
                <?php if ($currentTab === 'received' && $unreadCount > 0): ?>
                <form method="POST" class="action-form">
                    <input type="hidden" name="action" value="mark_read">
                    <button type="submit" class="action-btn" title="Đánh dấu tất cả đã đọc">
                        <i class="fas fa-check-double"></i>
                    </button>
                </form>
                <?php endif; ?>
                <button class="action-btn" onclick="window.location.href='messages.php?compose=1'" title="Soạn tin nhắn mới">
                    <i class="fas fa-edit"></i>
                </button>
            </div>
        </div>
        
        <div class="messages-filter">
            <span class="filter-count">
                <?php 
                $displayMessages = $currentTab === 'received' ? $receivedMessages : $sentMessages;
                echo count($displayMessages); ?> tin nhắn
            </span>
        </div>
        
        <div class="messages-container">
            <?php if (count($displayMessages) > 0): ?>
                <?php foreach ($displayMessages as $msg): ?>
                <a href="messages.php?view=<?php echo $msg['id']; ?>&tab=<?php echo $currentTab; ?>" 
                   class="message-row <?php echo $msg['is_read'] ? '' : 'unread'; ?>">
                    <div class="message-icon-wrapper">
                        <?php if ($msg['type'] === 'admin_to_user'): ?>
                        <div class="message-avatar admin">
                            <i class="fas fa-headset"></i>
                        </div>
                        <?php elseif ($msg['type'] === 'system'): ?>
                        <div class="message-avatar system">
                            <i class="fas fa-bell"></i>
                        </div>
                        <?php else: ?>
                        <div class="message-avatar sent">
                            <i class="fas fa-check"></i>
                        </div>
                        <?php endif; ?>
                    </div>
                    <div class="message-info">
                        <div class="message-sender-row">
                            <span class="message-sender">
                                <?php 
                                if ($msg['type'] === 'admin_to_user') echo 'Admin';
                                elseif ($msg['type'] === 'system') echo 'Hệ thống';
                                else echo 'Admin';
                                ?>
                            </span>
                            <span class="message-date"><?php echo timeAgo($msg['created_at']); ?></span>
                        </div>
                        <div class="message-subject-row">
                            <span class="message-subject-text"><?php echo htmlspecialchars($msg['subject'] ?: '(Không có tiêu đề)'); ?></span>
                        </div>
                        <div class="message-preview-text">
                            <?php echo htmlspecialchars(mb_substr($msg['content'], 0, 80)); ?>...
                        </div>
                    </div>
                    <?php if (!$msg['is_read']): ?>
                    <div class="unread-indicator"></div>
                    <?php endif; ?>
                </a>
                <?php endforeach; ?>
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
                        Bạn chưa có tin nhắn nào trong hộp thư đến.
                        <?php else: ?>
                        Bạn chưa gửi tin nhắn nào cho admin.
                        <?php endif; ?>
                    </p>
                    <?php if ($currentTab === 'received'): ?>
                    <a href="messages.php?compose=1" class="btn btn-primary">
                        <i class="fas fa-paper-plane"></i> Gửi tin nhắn cho Admin
                    </a>
                    <?php endif; ?>
                </div>
            <?php endif; ?>
        </div>
    </section>
    
    <!-- Message Detail Panel -->
    <section class="message-detail-panel <?php echo !$viewMessage ? 'hidden' : ''; ?>">
        <?php if ($viewMessage): ?>
        <div class="detail-header">
            <button class="back-btn" onclick="window.location.href='messages.php?tab=<?php echo $currentTab; ?>'">
                <i class="fas fa-arrow-left"></i>
                <span>Quay lại</span>
            </button>
            <div class="detail-actions">
                <form method="POST" class="delete-form">
                    <input type="hidden" name="action" value="delete">
                    <input type="hidden" name="message_id" value="<?php echo $viewMessage['id']; ?>">
                    <button type="submit" class="action-btn danger" onclick="return confirm('Bạn có chắc muốn xóa tin nhắn này?')">
                        <i class="fas fa-trash"></i>
                    </button>
                </form>
            </div>
        </div>
        
        <div class="detail-content">
            <div class="detail-subject">
                <h2><?php echo htmlspecialchars($viewMessage['subject'] ?: '(Không có tiêu đề)'); ?></h2>
            </div>
            
            <div class="detail-meta">
                <div class="sender-info">
                    <?php if ($viewMessage['type'] === 'admin_to_user'): ?>
                    <div class="sender-avatar-large admin">
                        <i class="fas fa-headset"></i>
                    </div>
                    <div class="sender-text">
                        <span class="sender-name">Admin - MÂY MƠ BOOK</span>
                        <span class="sender-email">admin@maymobook.com</span>
                    </div>
                    <?php elseif ($viewMessage['type'] === 'system'): ?>
                    <div class="sender-avatar-large system">
                        <i class="fas fa-bell"></i>
                    </div>
                    <div class="sender-text">
                        <span class="sender-name">Hệ thống</span>
                        <span class="sender-email">system@maymobook.com</span>
                    </div>
                    <?php else: ?>
                    <div class="sender-avatar-large user">
                        <i class="fas fa-user"></i>
                    </div>
                    <div class="sender-text">
                        <span class="sender-name"><?php echo htmlspecialchars($viewMessage['sender_name'] ?? 'Người gửi'); ?></span>
                        <span class="sender-email">Đã gửi cho Admin</span>
                    </div>
                    <?php endif; ?>
                </div>
                <div class="message-time-detail">
                    <i class="fas fa-clock"></i>
                    <?php echo date('H:i - d/m/Y', strtotime($viewMessage['created_at'])); ?>
                </div>
            </div>
            
            <div class="detail-body">
                <?php echo nl2br(htmlspecialchars($viewMessage['content'])); ?>
            </div>
            
            <div class="detail-footer">
                <?php if ($viewMessage['type'] !== 'user_to_admin' || $viewMessage['sender_id'] != $userId): ?>
                <a href="messages.php?compose=1" class="btn btn-primary">
                    <i class="fas fa-reply"></i> Trả lời
                </a>
                <?php else: ?>
                <span class="badge badge-success">
                    <i class="fas fa-check"></i> Đã gửi cho Admin
                </span>
                <?php endif; ?>
            </div>
        </div>
        <?php endif; ?>
    </section>
    
    <!-- Compose Modal/Panel -->
    <?php if (isset($_GET['compose'])): ?>
    <section class="compose-panel">
        <div class="compose-header">
            <h2><i class="fas fa-paper-plane"></i> Soạn tin nhắn mới</h2>
            <a href="messages.php" class="close-btn"><i class="fas fa-times"></i></a>
        </div>
        
        <?php if (isset($_SESSION['error'])): ?>
        <div class="alert alert-danger">
            <i class="fas fa-exclamation-circle"></i>
            <?php echo $_SESSION['error']; unset($_SESSION['error']); ?>
        </div>
        <?php endif; ?>
        
        <?php if (isset($_SESSION['success'])): ?>
        <div class="alert alert-success">
            <i class="fas fa-check-circle"></i>
            <?php echo $_SESSION['success']; unset($_SESSION['success']); ?>
        </div>
        <?php endif; ?>
        
        <form method="POST" class="compose-form">
            <input type="hidden" name="action" value="send">
            
            <div class="compose-field">
                <label><i class="fas fa-user-shield"></i> Người nhận</label>
                <input type="text" class="form-control" value="Admin - MÂY MƠ BOOK" readonly>
            </div>
            
            <div class="compose-field">
                <label for="subject"><i class="fas fa-heading"></i> Tiêu đề</label>
                <input type="text" id="subject" name="subject" class="form-control" 
                       placeholder="Nhập tiêu đề tin nhắn..." required>
            </div>
            
            <div class="compose-field">
                <label for="content"><i class="fas fa-comment-dots"></i> Nội dung</label>
                <textarea id="content" name="content" class="form-control" rows="10" 
                          placeholder="Nhập nội dung tin nhắn của bạn..." required></textarea>
            </div>
            
            <div class="compose-actions">
                <a href="messages.php" class="btn btn-outline">
                    <i class="fas fa-times"></i> Hủy
                </a>
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-paper-plane"></i> Gửi tin nhắn
                </button>
            </div>
        </form>
    </section>
    <?php endif; ?>
</div>

<style>
/* Messages App Container */
.messages-app {
    display: grid;
    grid-template-columns: 280px 1fr;
    min-height: calc(100vh - 80px);
    background: var(--bg-secondary);
}

/* Sidebar */
.messages-sidebar {
    background: linear-gradient(180deg, #667eea 0%, #764ba2 100%);
    color: white;
    display: flex;
    flex-direction: column;
    padding: 20px 0;
}

.sidebar-header {
    padding: 0 16px;
    margin-bottom: 24px;
}

.compose-btn {
    width: 100%;
    padding: 14px 20px;
    background: white;
    color: #667eea;
    border: none;
    border-radius: 12px;
    font-size: 1rem;
    font-weight: 600;
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 10px;
    transition: all 0.3s ease;
    box-shadow: 0 4px 15px rgba(0,0,0,0.2);
}

.compose-btn:hover {
    transform: translateY(-2px);
    box-shadow: 0 6px 20px rgba(0,0,0,0.3);
}

.sidebar-nav {
    flex: 1;
    padding: 0 12px;
}

.sidebar-nav .nav-item {
    display: flex;
    align-items: center;
    gap: 14px;
    padding: 14px 16px;
    color: rgba(255,255,255,0.8);
    text-decoration: none;
    border-radius: 10px;
    margin-bottom: 4px;
    transition: all 0.2s ease;
    font-weight: 500;
}

.sidebar-nav .nav-item:hover {
    background: rgba(255,255,255,0.15);
    color: white;
}

.sidebar-nav .nav-item.active {
    background: rgba(255,255,255,0.25);
    color: white;
    font-weight: 600;
}

.sidebar-nav .nav-item i {
    width: 20px;
    text-align: center;
    font-size: 1.1rem;
}

.sidebar-nav .nav-item .badge {
    margin-left: auto;
    background: #ff6b6b;
    color: white;
    padding: 4px 10px;
    border-radius: 20px;
    font-size: 0.75rem;
    font-weight: 600;
}

.sidebar-footer {
    padding: 16px;
    border-top: 1px solid rgba(255,255,255,0.1);
}

.user-info {
    display: flex;
    align-items: center;
    gap: 12px;
}

.user-avatar-small {
    width: 40px;
    height: 40px;
    background: rgba(255,255,255,0.2);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: 700;
    font-size: 1rem;
}

.user-details {
    display: flex;
    flex-direction: column;
}

.user-name {
    font-weight: 600;
    font-size: 0.95rem;
}

.user-role {
    font-size: 0.8rem;
    opacity: 0.7;
}

/* Messages List Panel */
.messages-list-panel {
    background: white;
    border-left: 1px solid var(--border-color);
    display: flex;
    flex-direction: column;
    overflow: hidden;
}

.panel-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 20px 24px;
    border-bottom: 1px solid var(--border-color);
    background: white;
}

.panel-title {
    font-size: 1.25rem;
    font-weight: 700;
    color: var(--text-primary);
    display: flex;
    align-items: center;
    gap: 12px;
}

.panel-title i {
    color: #667eea;
}

.panel-actions {
    display: flex;
    gap: 8px;
}

.action-btn {
    width: 40px;
    height: 40px;
    border: 1px solid var(--border-color);
    background: white;
    border-radius: 10px;
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    color: var(--text-secondary);
    transition: all 0.2s ease;
}

.action-btn:hover {
    background: var(--bg-secondary);
    color: var(--primary);
    border-color: var(--primary);
}

.action-btn.danger:hover {
    background: var(--danger-bg);
    color: var(--danger);
    border-color: var(--danger);
}

.messages-filter {
    padding: 12px 24px;
    border-bottom: 1px solid var(--border-color);
    background: var(--bg-secondary);
}

.filter-count {
    font-size: 0.85rem;
    color: var(--text-muted);
    font-weight: 500;
}

.messages-container {
    flex: 1;
    overflow-y: auto;
    padding: 12px;
}

/* Message Row */
.message-row {
    display: flex;
    align-items: center;
    gap: 16px;
    padding: 16px 20px;
    background: white;
    border-radius: 12px;
    margin-bottom: 8px;
    text-decoration: none;
    color: inherit;
    transition: all 0.2s ease;
    border: 1px solid transparent;
    position: relative;
}

.message-row:hover {
    background: var(--bg-secondary);
    border-color: var(--border-color);
    transform: translateX(4px);
    box-shadow: 0 2px 8px rgba(0,0,0,0.05);
}

.message-row.unread {
    background: linear-gradient(90deg, rgba(102,126,234,0.08) 0%, white 100%);
    border-left: 3px solid #667eea;
}

.message-icon-wrapper {
    flex-shrink: 0;
}

.message-avatar {
    width: 48px;
    height: 48px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.1rem;
}

.message-avatar.admin {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
}

.message-avatar.system {
    background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
    color: white;
}

.message-avatar.sent {
    background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
    color: white;
}

.message-info {
    flex: 1;
    min-width: 0;
}

.message-sender-row {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 4px;
}

.message-sender {
    font-weight: 600;
    color: var(--text-primary);
    font-size: 0.95rem;
}

.message-date {
    font-size: 0.8rem;
    color: var(--text-muted);
}

.message-subject-row {
    margin-bottom: 4px;
}

.message-subject-text {
    font-weight: 600;
    color: var(--text-primary);
    font-size: 0.95rem;
}

.unread .message-subject-text {
    font-weight: 700;
}

.message-preview-text {
    font-size: 0.85rem;
    color: var(--text-muted);
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}

.unread-indicator {
    width: 10px;
    height: 10px;
    background: #667eea;
    border-radius: 50%;
    flex-shrink: 0;
    animation: pulse 2s infinite;
}

@keyframes pulse {
    0%, 100% { opacity: 1; transform: scale(1); }
    50% { opacity: 0.7; transform: scale(1.1); }
}

/* Empty State */
.empty-state {
    text-align: center;
    padding: 80px 40px;
}

.empty-icon {
    width: 100px;
    height: 100px;
    margin: 0 auto 24px;
    background: var(--bg-secondary);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 3rem;
    color: var(--text-muted);
}

.empty-state h3 {
    font-size: 1.5rem;
    color: var(--text-primary);
    margin-bottom: 12px;
}

.empty-state p {
    color: var(--text-muted);
    margin-bottom: 24px;
    max-width: 300px;
    margin-left: auto;
    margin-right: auto;
}

/* Message Detail Panel */
.message-detail-panel {
    background: white;
    border-left: 1px solid var(--border-color);
    display: flex;
    flex-direction: column;
}

.detail-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 16px 24px;
    border-bottom: 1px solid var(--border-color);
    background: white;
}

.back-btn {
    display: flex;
    align-items: center;
    gap: 8px;
    padding: 10px 16px;
    background: var(--bg-secondary);
    border: 1px solid var(--border-color);
    border-radius: 8px;
    color: var(--text-secondary);
    text-decoration: none;
    font-weight: 500;
    transition: all 0.2s ease;
}

.back-btn:hover {
    background: var(--primary-bg);
    color: var(--primary);
    border-color: var(--primary);
}

.detail-actions {
    display: flex;
    gap: 8px;
}

.detail-content {
    flex: 1;
    padding: 32px;
    overflow-y: auto;
}

.detail-subject {
    margin-bottom: 24px;
}

.detail-subject h2 {
    font-size: 1.5rem;
    font-weight: 700;
    color: var(--text-primary);
    line-height: 1.4;
}

.detail-meta {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    padding-bottom: 24px;
    border-bottom: 1px solid var(--border-color);
    margin-bottom: 24px;
}

.sender-info {
    display: flex;
    align-items: center;
    gap: 16px;
}

.sender-avatar-large {
    width: 56px;
    height: 56px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.3rem;
}

.sender-avatar-large.admin {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
}

.sender-avatar-large.system {
    background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
    color: white;
}

.sender-avatar-large.user {
    background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
    color: white;
}

.sender-text {
    display: flex;
    flex-direction: column;
}

.sender-name {
    font-weight: 700;
    font-size: 1.1rem;
    color: var(--text-primary);
}

.sender-email {
    font-size: 0.85rem;
    color: var(--text-muted);
}

.message-time-detail {
    display: flex;
    align-items: center;
    gap: 8px;
    color: var(--text-muted);
    font-size: 0.9rem;
}

.detail-body {
    font-size: 1rem;
    line-height: 1.8;
    color: var(--text-secondary);
    padding: 24px;
    background: var(--bg-secondary);
    border-radius: 12px;
    margin-bottom: 24px;
}

.detail-footer {
    padding-top: 20px;
    border-top: 1px solid var(--border-color);
}

/* Compose Panel */
.compose-panel {
    position: fixed;
    top: 0;
    right: 0;
    width: 600px;
    height: 100vh;
    background: white;
    box-shadow: -4px 0 30px rgba(0,0,0,0.15);
    z-index: 1000;
    display: flex;
    flex-direction: column;
    animation: slideIn 0.3s ease;
}

@keyframes slideIn {
    from {
        transform: translateX(100%);
        opacity: 0;
    }
    to {
        transform: translateX(0);
        opacity: 1;
    }
}

.compose-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 20px 24px;
    border-bottom: 1px solid var(--border-color);
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
}

.compose-header h2 {
    font-size: 1.25rem;
    font-weight: 600;
    display: flex;
    align-items: center;
    gap: 12px;
}

.close-btn {
    width: 36px;
    height: 36px;
    border-radius: 50%;
    background: rgba(255,255,255,0.2);
    border: none;
    color: white;
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    transition: all 0.2s ease;
}

.close-btn:hover {
    background: rgba(255,255,255,0.3);
}

.compose-form {
    flex: 1;
    padding: 24px;
    overflow-y: auto;
}

.compose-field {
    margin-bottom: 20px;
}

.compose-field label {
    display: flex;
    align-items: center;
    gap: 8px;
    font-weight: 600;
    color: var(--text-primary);
    margin-bottom: 8px;
}

.compose-field label i {
    color: #667eea;
}

.compose-actions {
    display: flex;
    justify-content: flex-end;
    gap: 12px;
    padding-top: 20px;
    border-top: 1px solid var(--border-color);
}

/* Alerts */
.alert {
    padding: 14px 20px;
    border-radius: 10px;
    margin-bottom: 20px;
    display: flex;
    align-items: center;
    gap: 12px;
    font-weight: 500;
}

.alert-danger {
    background: var(--danger-bg);
    color: var(--danger);
    border: 1px solid var(--danger);
}

.alert-success {
    background: var(--success-bg);
    color: var(--success);
    border: 1px solid var(--success);
}

/* Badge */
.badge {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    padding: 8px 16px;
    border-radius: 8px;
    font-weight: 500;
}

.badge-success {
    background: var(--success-bg);
    color: var(--success);
}

/* Utility Classes */
.hidden {
    display: none;
}

.hidden-mobile {
    display: flex;
}

/* Responsive */
@media (max-width: 1024px) {
    .messages-app {
        grid-template-columns: 1fr;
    }
    
    .messages-sidebar {
        flex-direction: row;
        padding: 12px;
        overflow-x: auto;
    }
    
    .sidebar-header {
        margin-bottom: 0;
        margin-right: 16px;
    }
    
    .compose-btn {
        width: auto;
        padding: 12px 16px;
    }
    
    .compose-btn span {
        display: none;
    }
    
    .sidebar-nav {
        display: flex;
        padding: 0;
        gap: 8px;
    }
    
    .sidebar-nav .nav-item {
        padding: 12px 16px;
    }
    
    .sidebar-nav .nav-item span {
        display: none;
    }
    
    .sidebar-footer {
        display: none;
    }
    
    .message-detail-panel {
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        z-index: 100;
    }
    
    .hidden-mobile {
        display: none;
    }
    
    .compose-panel {
        width: 100%;
    }
}

@media (max-width: 768px) {
    .panel-header {
        padding: 16px;
    }
    
    .panel-title {
        font-size: 1.1rem;
    }
    
    .message-row {
        padding: 14px 16px;
    }
    
    .message-avatar {
        width: 42px;
        height: 42px;
    }
    
    .detail-content {
        padding: 20px;
    }
}
</style>

<?php require_once __DIR__ . '/../templates/footer.php'; ?>
