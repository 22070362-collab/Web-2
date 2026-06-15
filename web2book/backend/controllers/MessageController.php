<?php
/**
 * Message Controller
 * Xử lý các chức năng tin nhắn
 */

require_once __DIR__ . '/../models/Message.php';

class MessageController {
    private $messageModel;

    public function __construct() {
        $this->messageModel = new Message();
    }

    /**
     * Lấy dữ liệu cho dropdown notification (admin)
     */
    public function getNotificationsForAdmin($limit = 5) {
        $messages = $this->messageModel->getForAdmin($limit);
        $unreadCount = $this->messageModel->countUnreadAdmin();
        
        return [
            'messages' => $messages,
            'unread_count' => $unreadCount
        ];
    }

    /**
     * Lấy dữ liệu cho dropdown notification (user)
     */
    public function getNotificationsForUser($userId, $limit = 5) {
        $messages = $this->messageModel->getForUser($userId, $limit);
        $unreadCount = $this->messageModel->countUnread($userId);
        
        return [
            'messages' => $messages,
            'unread_count' => $unreadCount
        ];
    }

    /**
     * Đếm tin nhắn chưa đọc (admin)
     */
    public function countUnreadAdmin() {
        return $this->messageModel->countUnreadAdmin();
    }

    /**
     * Đếm tin nhắn chưa đọc (user)
     */
    public function countUnread($userId) {
        return $this->messageModel->countUnread($userId);
    }

    /**
     * Gửi tin nhắn cho admin (từ user)
     */
    public function sendToAdmin($userId) {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            return ['success' => false, 'message' => 'Phương thức không hợp lệ'];
        }

        $subject = trim($_POST['subject'] ?? '');
        $content = trim($_POST['content'] ?? '');

        if (empty($subject) || empty($content)) {
            return ['success' => false, 'message' => 'Vui lòng nhập đầy đủ tiêu đề và nội dung'];
        }

        $id = $this->messageModel->sendToAdmin($userId, $subject, $content);

        if ($id) {
            return ['success' => true, 'message' => 'Tin nhắn đã được gửi thành công'];
        }

        return ['success' => false, 'message' => 'Gửi tin nhắn thất bại'];
    }

    /**
     * Admin gửi tin nhắn cho user
     */
    public function sendToUser($adminId) {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            return ['success' => false, 'message' => 'Phương thức không hợp lệ'];
        }

        $userId = $_POST['user_id'] ?? null;
        $subject = trim($_POST['subject'] ?? '');
        $content = trim($_POST['content'] ?? '');

        if (!$userId || empty($subject) || empty($content)) {
            return ['success' => false, 'message' => 'Vui lòng nhập đầy đủ thông tin'];
        }

        $id = $this->messageModel->sendFromAdmin($userId, $subject, $content);

        if ($id) {
            return ['success' => true, 'message' => 'Tin nhắn đã được gửi thành công'];
        }

        return ['success' => false, 'message' => 'Gửi tin nhắn thất bại'];
    }

    /**
     * Đánh dấu đã đọc
     */
    public function markAsRead($messageId, $userId = null) {
        $this->messageModel->markAsRead($messageId, $userId);
        return ['success' => true];
    }

    /**
     * Đánh dấu tất cả đã đọc (admin)
     */
    public function markAllAsReadAdmin() {
        $this->messageModel->markAllAsReadAdmin();
        return ['success' => true];
    }

    /**
     * Đánh dấu tất cả đã đọc (user)
     */
    public function markAllAsRead($userId) {
        $this->messageModel->markAllAsRead($userId);
        return ['success' => true];
    }

    /**
     * Xóa tin nhắn
     */
    public function delete($messageId, $userId) {
        if ($this->messageModel->delete($messageId, $userId)) {
            return ['success' => true, 'message' => 'Xóa tin nhắn thành công'];
        }
        return ['success' => false, 'message' => 'Không thể xóa tin nhắn'];
    }

    /**
     * Lấy tất cả tin nhắn cho admin
     */
    public function getAllForAdmin() {
        return $this->messageModel->getAdminMessages(100);
    }

    /**
     * Lấy tất cả tin nhắn cho user
     */
    public function getAllForUser($userId) {
        return $this->messageModel->getByUser($userId, 100);
    }

    /**
     * Lấy conversation
     */
    public function getConversation($userId, $withUserId = null) {
        return $this->messageModel->getConversation($userId, $withUserId);
    }

    /**
     * Lấy chi tiết tin nhắn
     */
    public function getById($id) {
        return $this->messageModel->findById($id);
    }
}
