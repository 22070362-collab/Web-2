<?php
require_once __DIR__ . '/../../templates/admin_header.php';

$pageTitle = 'Tin Nhắn';
$adminUserId = $_SESSION['user_id'] ?? null;

// Handle actions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action'])) {
        switch ($_POST['action']) {
            case 'reply':
                $result = $messageController->sendToUser($adminUserId);
                if ($result['success']) {
                    $_SESSION['success'] = $result['message'];
                } else {
                    $_SESSION['error'] = $result['message'];
                }
                header('Location: messages.php');
                exit;
                break;
            case 'mark_read':
                $messageController->markAllAsReadAdmin();
                header('Location: messages.php');
                exit;
                break;
            case 'delete':
                if (isset($_POST['message_id'])) {
                    $messageController->delete($_POST['message_id'], $adminUserId);
                }
                header('Location: messages.php');
                exit;
                break;
        }
    }
}

// Get all messages for admin
$allMessages = $messageController->getAllForAdmin();
$unreadCount = $messageController->countUnreadAdmin();

// Separate messages by type
$userMessages = array_filter($allMessages, fn($m) => $m['type'] === 'user_to_admin');
$adminSentMessages = array_filter($allMessages, fn($m) => $m['type'] === 'admin_to_user');
$systemMessages = array_filter($allMessages, fn($m) => $m['type'] === 'system');

// View single message
$viewMessage = null;
if (isset($_GET['view'])) {
    $viewMessage = $messageController->getById($_GET['view']);
    if ($viewMessage && in_array($viewMessage['type'], ['user_to_admin', 'system', 'admin_to_user'])) {
        $messageController->markAsRead($viewMessage['id']);
        $unreadCount = $messageController->countUnreadAdmin();
    }
}

// Get users list for reply (exclude admins)
require_once __DIR__ . '/../../../backend/models/User.php';
$userModel = new User();
$allUsers = $userModel->getAll(100);
$users = array_filter($allUsers, function($u) { return ($u['role'] ?? 'user') !== 'admin'; });
$selectedUserId = intval($_POST['user_id'] ?? 0);

// Current tab
$currentTab = $_GET['tab'] ?? 'inbox';
?>

<!-- Admin Messages - Modern Dashboard Style -->
<div class="admin-messages-app">
    
    <!-- Top Stats Bar -->
    <div class="stats-bar">
        <div class="stat-card">
            <div class="stat-icon" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
                <i class="fas fa-envelope"></i>
            </div>
            <div class="stat-info">
                <span class="stat-value"><?php echo count($allMessages); ?></span>
                <span class="stat-label">Tổng tin nhắn</span>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon" style="background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);">
                <i class="fas fa-envelope-open"></i>
            </div>
            <div class="stat-info">
                <span class="stat-value"><?php echo $unreadCount; ?></span>
                <span class="stat-label">Chưa đọc</span>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon" style="background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);">
                <i class="fas fa-user"></i>
            </div>
            <div class="stat-info">
                <span class="stat-value"><?php echo count($userMessages); ?></span>
                <span class="stat-label">Tin nhắn từ user</span>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon" style="background: linear-gradient(135deg, #43e97b 0%, #38f9d7 100%);">
                <i class="fas fa-paper-plane"></i>
            </div>
            <div class="stat-info">
                <span class="stat-value"><?php echo count($adminSentMessages); ?></span>
                <span class="stat-label">Đã gửi</span>
            </div>
        </div>
    </div>
    
    <div class="messages-main-layout">
        
        <!-- Messages List Section -->
        <div class="messages-list-section <?php echo $viewMessage ? 'hidden-mobile' : ''; ?>">
            
            <!-- Tabs -->
            <div class="messages-tabs">
                <a href="messages.php?tab=inbox" class="tab-btn <?php echo $currentTab === 'inbox' ? 'active' : ''; ?>">
                    <i class="fas fa-inbox"></i>
                    <span>Hộp thư đến</span>
                    <?php if ($unreadCount > 0): ?>
                    <span class="tab-badge"><?php echo $unreadCount; ?></span>
                    <?php endif; ?>
                </a>
                <a href="messages.php?tab=sent" class="tab-btn <?php echo $currentTab === 'sent' ? 'active' : ''; ?>">
                    <i class="fas fa-paper-plane"></i>
                    <span>Đã gửi</span>
                </a>
                <a href="messages.php?tab=system" class="tab-btn <?php echo $currentTab === 'system' ? 'active' : ''; ?>">
                    <i class="fas fa-bell"></i>
                    <span>Hệ thống</span>
                </a>
            </div>
            
            <!-- Toolbar -->
            <div class="messages-toolbar">
                <?php if ($currentTab === 'inbox' && $unreadCount > 0): ?>
                <form method="POST" class="mark-all-form">
                    <input type="hidden" name="action" value="mark_read">
                    <button type="submit" class="toolbar-btn">
                        <i class="fas fa-check-double"></i>
                        <span>Đánh dấu tất cả đã đọc</span>
                    </button>
                </form>
                <?php endif; ?>
            </div>
            
            <!-- Messages List -->
            <div class="messages-list-container">
                <?php 
                $displayMessages = [];
                if ($currentTab === 'inbox') {
                    $displayMessages = $userMessages;
                } elseif ($currentTab === 'sent') {
                    $displayMessages = $adminSentMessages;
                } else {
                    $displayMessages = $systemMessages;
                }
                ?>
                
                <?php if (count($displayMessages) > 0): ?>
                    <?php foreach ($displayMessages as $msg): ?>
                    <a href="messages.php?view=<?php echo $msg['id']; ?>&tab=<?php echo $currentTab; ?>" 
                       class="message-item <?php echo $msg['is_read'] ? '' : 'unread'; ?>">
                        <div class="message-avatar-wrapper">
                            <?php if ($msg['type'] === 'system'): ?>
                            <div class="message-avatar-sm system">
                                <i class="fas fa-bell"></i>
                            </div>
                            <?php elseif ($msg['type'] === 'admin_to_user'): ?>
                            <div class="message-avatar-sm sent">
                                <i class="fas fa-paper-plane"></i>
                            </div>
                            <?php else: ?>
                            <div class="message-avatar-sm user">
                                <?php echo strtoupper(substr($msg['sender_name'] ?? 'U', 0, 1)); ?>
                            </div>
                            <?php endif; ?>
                        </div>
                        <div class="message-content-wrapper">
                            <div class="message-top-row">
                                <span class="message-sender-name">
                                    <?php 
                                    if ($msg['type'] === 'system') echo 'Hệ thống';
                                    elseif ($msg['type'] === 'admin_to_user') echo 'Gửi: ' . htmlspecialchars($msg['receiver_name'] ?? 'User');
                                    else echo htmlspecialchars($msg['sender_name'] ?? 'Người dùng');
                                    ?>
                                </span>
                                <span class="message-time"><?php echo timeAgo($msg['created_at']); ?></span>
                            </div>
                            <div class="message-subject">
                                <?php echo htmlspecialchars($msg['subject'] ?: '(Không có tiêu đề)'); ?>
                            </div>
                            <div class="message-preview">
                                <?php echo htmlspecialchars(mb_substr($msg['content'], 0, 100)); ?>...
                            </div>
                        </div>
                        <?php if (!$msg['is_read']): ?>
                        <div class="unread-dot"></div>
                        <?php endif; ?>
                    </a>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div class="empty-messages">
                        <div class="empty-icon">
                            <i class="fas fa-inbox"></i>
                        </div>
                        <h3>Không có tin nhắn</h3>
                        <p>Không có tin nhắn nào trong mục này.</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
        
        <!-- Message Detail Section -->
        <div class="message-detail-section <?php echo !$viewMessage ? 'hidden' : ''; ?>">
            <?php if ($viewMessage): ?>
            <div class="detail-header-bar">
                <button class="back-button" onclick="window.location.href='messages.php?tab=<?php echo $currentTab; ?>'">
                    <i class="fas fa-arrow-left"></i>
                    <span>Quay lại</span>
                </button>
                <div class="detail-actions">
                    <form method="POST" class="delete-form-inline">
                        <input type="hidden" name="action" value="delete">
                        <input type="hidden" name="message_id" value="<?php echo $viewMessage['id']; ?>">
                        <button type="submit" class="action-btn-delete" onclick="return confirm('Xóa tin nhắn này?')">
                            <i class="fas fa-trash"></i>
                        </button>
                    </form>
                </div>
            </div>
            
            <div class="detail-content-area">
                <div class="detail-subject-bar">
                    <h2><?php echo htmlspecialchars($viewMessage['subject'] ?: '(Không có tiêu đề)'); ?></h2>
                </div>
                
                <div class="detail-meta-bar">
                    <div class="sender-details">
                        <?php if ($viewMessage['type'] === 'admin_to_user'): ?>
                        <div class="sender-avatar-lg sent">
                            <i class="fas fa-paper-plane"></i>
                        </div>
                        <div class="sender-info-text">
                            <span class="sender-name-lg">Gửi đến: <?php echo htmlspecialchars($viewMessage['receiver_name'] ?? 'Người dùng'); ?></span>
                            <span class="sender-email-lg">Tin nhắn đã gửi</span>
                        </div>
                        <?php elseif ($viewMessage['sender_name']): ?>
                        <div class="sender-avatar-lg user">
                            <?php echo strtoupper(substr($viewMessage['sender_name'], 0, 1)); ?>
                        </div>
                        <div class="sender-info-text">
                            <span class="sender-name-lg"><?php echo htmlspecialchars($viewMessage['sender_name']); ?></span>
                            <span class="sender-email-lg"><?php echo htmlspecialchars($viewMessage['sender_email'] ?? ''); ?></span>
                        </div>
                        <?php else: ?>
                        <div class="sender-avatar-lg system">
                            <i class="fas fa-bell"></i>
                        </div>
                        <div class="sender-info-text">
                            <span class="sender-name-lg">Hệ thống</span>
                            <span class="sender-email-lg">Tin tự động</span>
                        </div>
                        <?php endif; ?>
                    </div>
                    <div class="message-datetime">
                        <i class="fas fa-clock"></i>
                        <?php echo date('H:i - d/m/Y', strtotime($viewMessage['created_at'])); ?>
                    </div>
                </div>
                
                <div class="detail-message-body">
                    <?php echo nl2br(htmlspecialchars($viewMessage['content'])); ?>
                </div>
                
                <?php if ($viewMessage['type'] === 'user_to_admin'): ?>
                <div class="reply-section">
                    <button class="reply-btn" onclick='showReplyForm(<?php echo intval($viewMessage["sender_id"] ?? 0); ?>, <?php echo json_encode((string)($viewMessage["sender_name"] ?? "Người dùng"), JSON_UNESCAPED_UNICODE); ?>)'>
                        <i class="fas fa-reply"></i>
                        Trả lời <?php echo htmlspecialchars($viewMessage['sender_name']); ?>
                    </button>
                </div>
                
                <!-- Reply Form (Hidden by default) -->
                <div class="reply-form-panel" id="replyFormPanel">
                    <h4><i class="fas fa-reply"></i> Trả lời tin nhắn</h4>
                    <form method="POST">
                        <input type="hidden" name="action" value="reply">
                        <input type="hidden" name="user_id" id="replyUserIdField">
                        
                        <div class="form-group">
                            <label><i class="fas fa-user"></i> Người nhận</label>
                            <input type="text" id="replyUserNameField" class="form-control" readonly>
                        </div>
                        
                        <div class="form-group">
                            <label><i class="fas fa-heading"></i> Tiêu đề</label>
                            <input type="text" name="subject" class="form-control" 
                                   placeholder="Nhập tiêu đề..." required>
                        </div>
                        
                        <div class="form-group">
                            <label><i class="fas fa-comment"></i> Nội dung</label>
                            <textarea name="content" class="form-control" rows="5" 
                                      placeholder="Nhập nội dung trả lời..." required></textarea>
                        </div>
                        
                        <div class="form-actions">
                            <button type="button" class="btn-cancel" onclick="hideReplyForm()">
                                <i class="fas fa-times"></i> Hủy
                            </button>
                            <button type="submit" class="btn-send">
                                <i class="fas fa-paper-plane"></i> Gửi
                            </button>
                        </div>
                    </form>
                </div>
                <?php endif; ?>
            </div>
            <?php endif; ?>
        </div>
        
        <!-- Quick Send Sidebar -->
        <div class="quick-send-sidebar">
            <div class="sidebar-card">
                <h3 class="card-title">
                    <i class="fas fa-paper-plane"></i>
                    Gửi Thông Báo
                </h3>
                
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
                
                <form method="POST" class="quick-send-form">
                    <input type="hidden" name="action" value="reply">
                    
                    <div class="form-group">
                        <label><i class="fas fa-user"></i> Người dùng</label>
                        <input type="text" id="userSearch" class="form-control" placeholder="Tìm theo tên hoặc username..." style="margin-bottom:8px;">
                        <select name="user_id" class="form-control" required>
                            <option value="">-- Chọn người dùng --</option>
                            <?php foreach ($users as $u): ?>
                            <option value="<?php echo $u['id']; ?>" data-search="<?php echo htmlspecialchars(strtolower(($u['full_name'] ?? '') . ' ' . ($u['username'] ?? ''))); ?>" <?php echo $selectedUserId === intval($u['id']) ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($u['full_name']); ?> (<?php echo htmlspecialchars($u['username']); ?>)
                            </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label><i class="fas fa-heading"></i> Tiêu đề</label>
                        <input type="text" name="subject" class="form-control" placeholder="Nhập tiêu đề..." required>
                    </div>
                    
                    <div class="form-group">
                        <label><i class="fas fa-comment"></i> Nội dung</label>
                        <textarea name="content" class="form-control" rows="4" placeholder="Nhập nội dung..." required></textarea>
                    </div>
                    
                    <button type="submit" class="btn-send-full">
                        <i class="fas fa-paper-plane"></i> Gửi tin nhắn
                    </button>
                </form>
            </div>
            
            <div class="sidebar-card tips-card">
                <h3 class="card-title">
                    <i class="fas fa-lightbulb"></i>
                    Mẹo
                </h3>
                <ul class="tips-list">
                    <li>Sử dụng tin nhắn để thông báo cho người dùng về đơn hàng</li>
                    <li>Có thể gửi thông báo hệ thống tự động</li>
                    <li>Tin nhắn chưa đọc sẽ hiển thị badge đỏ</li>
                </ul>
            </div>
        </div>
    </div>
</div>

<script>
function showReplyForm(userId, userName) {
    document.getElementById('replyFormPanel').style.display = 'block';
    document.getElementById('replyUserIdField').value = userId;
    document.getElementById('replyUserNameField').value = userName;
}

function hideReplyForm() {
    document.getElementById('replyFormPanel').style.display = 'none';
}

const userSearchInput = document.getElementById('userSearch');
const userSelect = document.querySelector('select[name="user_id"]');
if (userSearchInput && userSelect) {
    userSearchInput.addEventListener('input', function () {
        const q = (this.value || '').toLowerCase().trim();
        Array.from(userSelect.options).forEach((opt, idx) => {
            if (idx === 0) return;
            const hay = opt.getAttribute('data-search') || '';
            opt.hidden = q !== '' && !hay.includes(q);
        });
    });
}
</script>

<style>
/* Admin Messages App */
.admin-messages-app {
    padding: 0;
}

/* Stats Bar */
.stats-bar {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: 20px;
    margin-bottom: 24px;
}

.stat-card {
    background: white;
    border-radius: 16px;
    padding: 20px;
    display: flex;
    align-items: center;
    gap: 16px;
    box-shadow: 0 2px 12px rgba(0,0,0,0.05);
    border: 1px solid var(--border-color);
}

.stat-icon {
    width: 56px;
    height: 56px;
    border-radius: 14px;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-size: 1.3rem;
}

.stat-info {
    display: flex;
    flex-direction: column;
}

.stat-value {
    font-size: 1.75rem;
    font-weight: 800;
    color: var(--text-primary);
    line-height: 1;
}

.stat-label {
    font-size: 0.85rem;
    color: var(--text-muted);
    margin-top: 4px;
}

/* Main Layout */
.messages-main-layout {
    display: grid;
    grid-template-columns: 1fr 380px;
    gap: 24px;
}

/* Messages List Section */
.messages-list-section {
    background: white;
    border-radius: 16px;
    box-shadow: 0 2px 12px rgba(0,0,0,0.05);
    border: 1px solid var(--border-color);
    overflow: hidden;
}

/* Tabs */
.messages-tabs {
    display: flex;
    border-bottom: 1px solid var(--border-color);
    background: var(--bg-secondary);
}

.tab-btn {
    flex: 1;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 10px;
    padding: 16px;
    color: var(--text-secondary);
    text-decoration: none;
    font-weight: 500;
    transition: all 0.2s ease;
    position: relative;
}

.tab-btn:hover {
    background: white;
    color: var(--primary);
}

.tab-btn.active {
    background: white;
    color: #667eea;
    font-weight: 600;
}

.tab-btn.active::after {
    content: '';
    position: absolute;
    bottom: 0;
    left: 0;
    right: 0;
    height: 3px;
    background: linear-gradient(90deg, #667eea 0%, #764ba2 100%);
}

.tab-btn i {
    font-size: 1.1rem;
}

.tab-badge {
    background: #ff6b6b;
    color: white;
    padding: 2px 8px;
    border-radius: 12px;
    font-size: 0.75rem;
    font-weight: 600;
}

/* Toolbar */
.messages-toolbar {
    padding: 12px 16px;
    border-bottom: 1px solid var(--border-color);
    display: flex;
    gap: 12px;
}

.toolbar-btn {
    display: flex;
    align-items: center;
    gap: 8px;
    padding: 8px 16px;
    background: var(--primary-bg);
    color: var(--primary);
    border: 1px solid var(--primary);
    border-radius: 8px;
    font-size: 0.9rem;
    font-weight: 500;
    cursor: pointer;
    transition: all 0.2s ease;
}

.toolbar-btn:hover {
    background: var(--primary);
    color: white;
}

/* Messages List */
.messages-list-container {
    padding: 12px;
    max-height: calc(100vh - 380px);
    overflow-y: auto;
}

.message-item {
    display: flex;
    align-items: center;
    gap: 16px;
    padding: 16px;
    border-radius: 12px;
    margin-bottom: 8px;
    text-decoration: none;
    color: inherit;
    transition: all 0.2s ease;
    border: 1px solid transparent;
    background: var(--bg-secondary);
}

.message-item:hover {
    background: white;
    border-color: var(--border-color);
    transform: translateX(4px);
    box-shadow: 0 2px 8px rgba(0,0,0,0.05);
}

.message-item.unread {
    background: linear-gradient(90deg, rgba(102,126,234,0.06) 0%, var(--bg-secondary) 100%);
    border-left: 3px solid #667eea;
}

.message-avatar-wrapper {
    flex-shrink: 0;
}

.message-avatar-sm {
    width: 48px;
    height: 48px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: 700;
    font-size: 1rem;
    color: white;
}

.message-avatar-sm.user {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
}

.message-avatar-sm.system {
    background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
}

.message-avatar-sm.sent {
    background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
}

.message-content-wrapper {
    flex: 1;
    min-width: 0;
}

.message-top-row {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 6px;
}

.message-sender-name {
    font-weight: 600;
    color: var(--text-primary);
    font-size: 0.95rem;
}

.message-time {
    font-size: 0.8rem;
    color: var(--text-muted);
}

.message-subject {
    font-weight: 600;
    color: var(--text-primary);
    margin-bottom: 4px;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
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
    background: #667eea;
    border-radius: 50%;
    flex-shrink: 0;
    animation: pulse 2s infinite;
}

@keyframes pulse {
    0%, 100% { opacity: 1; }
    50% { opacity: 0.5; }
}

/* Empty State */
.empty-messages {
    text-align: center;
    padding: 60px 20px;
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

.empty-messages h3 {
    color: var(--text-primary);
    margin-bottom: 8px;
}

.empty-messages p {
    color: var(--text-muted);
}

/* Message Detail Section */
.message-detail-section {
    background: white;
    border-radius: 16px;
    box-shadow: 0 2px 12px rgba(0,0,0,0.05);
    border: 1px solid var(--border-color);
    display: flex;
    flex-direction: column;
    overflow: hidden;
}

.detail-header-bar {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 16px 20px;
    border-bottom: 1px solid var(--border-color);
    background: var(--bg-secondary);
}

.back-button {
    display: flex;
    align-items: center;
    gap: 8px;
    padding: 10px 16px;
    background: white;
    border: 1px solid var(--border-color);
    border-radius: 8px;
    color: var(--text-secondary);
    text-decoration: none;
    font-weight: 500;
    cursor: pointer;
    transition: all 0.2s ease;
}

.back-button:hover {
    background: var(--primary-bg);
    color: var(--primary);
    border-color: var(--primary);
}

.action-btn-delete {
    width: 40px;
    height: 40px;
    border-radius: 8px;
    border: 1px solid var(--border-color);
    background: white;
    color: var(--text-secondary);
    cursor: pointer;
    transition: all 0.2s ease;
}

.action-btn-delete:hover {
    background: var(--danger-bg);
    color: var(--danger);
    border-color: var(--danger);
}

.detail-content-area {
    flex: 1;
    padding: 24px;
    overflow-y: auto;
}

.detail-subject-bar {
    margin-bottom: 20px;
}

.detail-subject-bar h2 {
    font-size: 1.3rem;
    font-weight: 700;
    color: var(--text-primary);
    line-height: 1.4;
}

.detail-meta-bar {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding-bottom: 20px;
    border-bottom: 1px solid var(--border-color);
    margin-bottom: 20px;
}

.sender-details {
    display: flex;
    align-items: center;
    gap: 14px;
}

.sender-avatar-lg {
    width: 52px;
    height: 52px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: 700;
    font-size: 1.2rem;
    color: white;
}

.sender-avatar-lg.user {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
}

.sender-avatar-lg.system {
    background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
}

.sender-avatar-lg.sent {
    background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
}

.sender-info-text {
    display: flex;
    flex-direction: column;
}

.sender-name-lg {
    font-weight: 700;
    font-size: 1rem;
    color: var(--text-primary);
}

.sender-email-lg {
    font-size: 0.85rem;
    color: var(--text-muted);
}

.message-datetime {
    display: flex;
    align-items: center;
    gap: 6px;
    color: var(--text-muted);
    font-size: 0.9rem;
}

.detail-message-body {
    font-size: 1rem;
    line-height: 1.8;
    color: var(--text-secondary);
    padding: 20px;
    background: var(--bg-secondary);
    border-radius: 12px;
    margin-bottom: 20px;
}

.reply-section {
    margin-bottom: 20px;
}

.reply-btn {
    display: inline-flex;
    align-items: center;
    gap: 10px;
    padding: 12px 24px;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    border: none;
    border-radius: 10px;
    font-size: 1rem;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.2s ease;
}

.reply-btn:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 15px rgba(102,126,234,0.4);
}

/* Reply Form Panel */
.reply-form-panel {
    background: var(--bg-secondary);
    border-radius: 12px;
    padding: 20px;
    border: 1px solid var(--border-color);
}

.reply-form-panel h4 {
    display: flex;
    align-items: center;
    gap: 10px;
    margin-bottom: 16px;
    color: var(--text-primary);
}

.reply-form-panel h4 i {
    color: #667eea;
}

/* Quick Send Sidebar */
.quick-send-sidebar {
    display: flex;
    flex-direction: column;
    gap: 20px;
}

.sidebar-card {
    background: white;
    border-radius: 16px;
    padding: 20px;
    box-shadow: 0 2px 12px rgba(0,0,0,0.05);
    border: 1px solid var(--border-color);
}

.card-title {
    display: flex;
    align-items: center;
    gap: 10px;
    font-size: 1rem;
    font-weight: 700;
    color: var(--text-primary);
    margin-bottom: 16px;
    padding-bottom: 12px;
    border-bottom: 1px solid var(--border-color);
}

.card-title i {
    color: #667eea;
}

.tips-card .card-title i {
    color: #f59e0b;
}

.quick-send-form .form-group {
    margin-bottom: 14px;
}

.quick-send-form label {
    display: block;
    font-weight: 600;
    color: var(--text-primary);
    margin-bottom: 6px;
    font-size: 0.9rem;
}

.quick-send-form label i {
    color: #667eea;
    margin-right: 6px;
}

.btn-send-full {
    width: 100%;
    padding: 14px;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    border: none;
    border-radius: 10px;
    font-size: 1rem;
    font-weight: 600;
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 10px;
    transition: all 0.2s ease;
}

.btn-send-full:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 15px rgba(102,126,234,0.4);
}

/* Tips List */
.tips-list {
    list-style: none;
    padding: 0;
    margin: 0;
}

.tips-list li {
    padding: 10px 0;
    border-bottom: 1px solid var(--border-color);
    font-size: 0.9rem;
    color: var(--text-secondary);
    display: flex;
    align-items: flex-start;
    gap: 10px;
}

.tips-list li:last-child {
    border-bottom: none;
}

.tips-list li::before {
    content: '•';
    color: #667eea;
    font-weight: bold;
}

/* Form Elements */
.form-group {
    margin-bottom: 14px;
}

.form-group label {
    display: block;
    font-weight: 600;
    color: var(--text-primary);
    margin-bottom: 6px;
    font-size: 0.9rem;
}

.form-group label i {
    color: #667eea;
    margin-right: 6px;
}

.form-actions {
    display: flex;
    gap: 12px;
    justify-content: flex-end;
}

.btn-cancel {
    padding: 10px 20px;
    background: var(--bg-secondary);
    color: var(--text-secondary);
    border: 1px solid var(--border-color);
    border-radius: 8px;
    font-weight: 500;
    cursor: pointer;
    display: flex;
    align-items: center;
    gap: 8px;
    transition: all 0.2s ease;
}

.btn-cancel:hover {
    background: var(--bg-card);
}

.btn-send {
    padding: 10px 20px;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    border: none;
    border-radius: 8px;
    font-weight: 600;
    cursor: pointer;
    display: flex;
    align-items: center;
    gap: 8px;
    transition: all 0.2s ease;
}

.btn-send:hover {
    transform: translateY(-1px);
    box-shadow: 0 4px 12px rgba(102,126,234,0.3);
}

/* Alerts */
.alert {
    padding: 12px 16px;
    border-radius: 10px;
    margin-bottom: 16px;
    display: flex;
    align-items: center;
    gap: 10px;
    font-size: 0.9rem;
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

/* Utility */
.hidden {
    display: none;
}

.hidden-mobile {
    display: flex;
}

/* Responsive */
@media (max-width: 1200px) {
    .messages-main-layout {
        grid-template-columns: 1fr;
    }
    
    .quick-send-sidebar {
        display: none;
    }
}

@media (max-width: 768px) {
    .stats-bar {
        grid-template-columns: repeat(2, 1fr);
    }
    
    .messages-tabs {
        overflow-x: auto;
    }
    
    .tab-btn span {
        display: none;
    }
    
    .hidden-mobile {
        display: none;
    }
    
    .message-detail-section {
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        z-index: 100;
        border-radius: 0;
    }
}

@media (max-width: 480px) {
    .stats-bar {
        grid-template-columns: 1fr;
    }
    
    .stat-card {
        padding: 16px;
    }
    
    .stat-value {
        font-size: 1.5rem;
    }
}
</style>

<?php require_once __DIR__ . '/../../templates/admin_footer.php'; ?>
