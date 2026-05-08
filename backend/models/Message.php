<?php
/**
 * Message Model
 * Xử lý dữ liệu tin nhắn
 */

require_once __DIR__ . '/../config/database.php';

class Message {
    private $db;
    
    public function __construct() {
        $this->db = getDB();
    }

    /**
     * Tạo tin nhắn mới
     */
    public function create($data) {
        $subject = $this->sanitizeForDb($data['subject'] ?? '');
        $content = $this->sanitizeForDb($data['content'] ?? '');

        $stmt = $this->db->prepare("
            INSERT INTO messages (sender_id, receiver_id, subject, content, type)
            VALUES (?, ?, ?, ?, ?)
        ");
        $stmt->execute([
            $data['sender_id'] ?? null,
            $data['receiver_id'] ?? null,
            $subject,
            $content,
            $data['type'] ?? 'user_to_admin'
        ]);
        return $this->db->lastInsertId();
    }

    /**
     * Loại bỏ ký tự Unicode 4-byte (emoji) để tránh lỗi với DB charset cũ.
     */
    private function sanitizeForDb($text) {
        $text = (string)$text;
        return preg_replace('/[\x{10000}-\x{10FFFF}]/u', '', $text);
    }

    /**
     * Lấy tin nhắn theo ID
     */
    public function findById($id) {
        $stmt = $this->db->prepare("SELECT * FROM messages WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch();
    }

    /**
     * Lấy tin nhắn của user (nhận + gửi)
     * Dùng cho user page
     */
    public function getByUser($userId, $limit = 50) {
        $stmt = $this->db->prepare("
            SELECT m.*,
                   s.username as sender_username, s.full_name as sender_name,
                   r.username as receiver_username, r.full_name as receiver_name
            FROM messages m
            LEFT JOIN users s ON m.sender_id = s.id
            LEFT JOIN users r ON m.receiver_id = r.id
            WHERE (
                (m.receiver_id = ? AND m.type IN ('admin_to_user', 'system'))
                OR (m.sender_id = ? AND m.type = 'user_to_admin')
            )
            ORDER BY m.created_at DESC
            LIMIT ?
        ");
        $stmt->execute([$userId, $userId, $limit]);
        return $stmt->fetchAll();
    }

    /**
     * Lấy tin nhắn gửi cho admin (chỉ tin nhắn từ user - dùng cho dropdown notification)
     */
    public function getForAdmin($limit = 50) {
        $stmt = $this->db->prepare("
            SELECT m.*, 
                   s.username as sender_username, s.full_name as sender_name,
                   s.email as sender_email
            FROM messages m
            LEFT JOIN users s ON m.sender_id = s.id
            WHERE m.type IN ('user_to_admin', 'system')
            ORDER BY m.created_at DESC
            LIMIT ?
        ");
        $stmt->execute([$limit]);
        return $stmt->fetchAll();
    }

    /**
     * Lấy tin nhắn gửi cho user cụ thể (bao gồm cả tin nhắn user đã gửi đi)
     */
    public function getForUser($userId, $limit = 50) {
        $stmt = $this->db->prepare("
            SELECT m.*,
                   s.username as sender_username, s.full_name as sender_name,
                   r.username as receiver_username, r.full_name as receiver_name
            FROM messages m
            LEFT JOIN users s ON m.sender_id = s.id
            LEFT JOIN users r ON m.receiver_id = r.id
            WHERE (
                (m.receiver_id = ? AND m.type IN ('admin_to_user', 'system'))
                OR (m.sender_id = ? AND m.type = 'user_to_admin')
            )
            ORDER BY m.created_at DESC
            LIMIT ?
        ");
        $stmt->execute([$userId, $userId, $limit]);
        return $stmt->fetchAll();
    }

    /**
     * Đếm tin nhắn chưa đọc của user
     */
    public function countUnread($userId) {
        $stmt = $this->db->prepare("
            SELECT COUNT(*) as count 
            FROM messages 
            WHERE receiver_id = ? AND is_read = 0
        ");
        $stmt->execute([$userId]);
        return (int)$stmt->fetch()['count'];
    }

    /**
     * Đếm tin nhắn chưa đọc cho admin
     */
    public function countUnreadAdmin() {
        $stmt = $this->db->query("
            SELECT COUNT(*) as count 
            FROM messages 
            WHERE type IN ('user_to_admin', 'system') AND is_read = 0
        ");
        return $stmt->fetch()['count'];
    }

    /**
     * Lấy tất cả tin nhắn của admin (gửi + nhận)
     */
    public function getAdminMessages($limit = 100) {
        $stmt = $this->db->prepare("
            SELECT m.*, 
                   s.username as sender_username, s.full_name as sender_name,
                   s.email as sender_email,
                   r.username as receiver_username, r.full_name as receiver_name
            FROM messages m
            LEFT JOIN users s ON m.sender_id = s.id
            LEFT JOIN users r ON m.receiver_id = r.id
            WHERE m.type IN ('user_to_admin', 'admin_to_user', 'system')
            ORDER BY m.created_at DESC
            LIMIT ?
        ");
        $stmt->execute([$limit]);
        return $stmt->fetchAll();
    }

    /**
     * Đánh dấu đã đọc
     */
    public function markAsRead($id, $userId = null) {
        if ($userId !== null) {
            $stmt = $this->db->prepare("
                UPDATE messages
                SET is_read = 1, read_at = NOW()
                WHERE id = ? AND receiver_id = ?
            ");
            return $stmt->execute([$id, $userId]);
        } else {
            $stmt = $this->db->prepare("
                UPDATE messages
                SET is_read = 1, read_at = NOW()
                WHERE id = ?
            ");
            return $stmt->execute([$id]);
        }
    }

    /**
     * Đánh dấu tất cả đã đọc cho user
     */
    public function markAllAsRead($userId) {
        $stmt = $this->db->prepare("
            UPDATE messages 
            SET is_read = 1, read_at = NOW() 
            WHERE receiver_id = ? AND is_read = 0
        ");
        return $stmt->execute([$userId]);
    }

    /**
     * Đánh dấu tất cả đã đọc cho admin
     */
    public function markAllAsReadAdmin() {
        $stmt = $this->db->prepare("
            UPDATE messages
            SET is_read = 1, read_at = NOW()
            WHERE type IN ('user_to_admin', 'system') AND is_read = 0
        ");
        return $stmt->execute();
    }

    /**
     * Xóa tin nhắn
     */
    public function delete($id, $userId) {
        $stmt = $this->db->prepare("
            DELETE FROM messages 
            WHERE id = ? AND (sender_id = ? OR receiver_id = ?)
        ");
        return $stmt->execute([$id, $userId, $userId]);
    }

    /**
     * Lấy conversation giữa 2 user (hoặc user với admin)
     */
    public function getConversation($userId, $withUserId = null, $limit = 50) {
        if ($withUserId) {
            $stmt = $this->db->prepare("
                SELECT m.*, 
                       s.username as sender_username, s.full_name as sender_name
                FROM messages m
                LEFT JOIN users s ON m.sender_id = s.id
                WHERE (m.sender_id = ? AND m.receiver_id = ?) 
                   OR (m.sender_id = ? AND m.receiver_id = ?)
                ORDER BY m.created_at DESC
                LIMIT ?
            ");
            $stmt->execute([$userId, $withUserId, $withUserId, $userId, $limit]);
        } else {
            // Conversation với admin
            $stmt = $this->db->prepare("
                SELECT m.*, 
                       s.username as sender_username, s.full_name as sender_name
                FROM messages m
                LEFT JOIN users s ON m.sender_id = s.id
                WHERE m.type IN ('admin_to_user', 'user_to_admin', 'system') 
                  AND (m.receiver_id = ? OR m.sender_id = ?)
                ORDER BY m.created_at DESC
                LIMIT ?
            ");
            $stmt->execute([$userId, $userId, $limit]);
        }
        return $stmt->fetchAll();
    }

    /**
     * Kiểm tra đã gửi thông báo hệ thống cùng tiêu đề trong hôm nay hay chưa
     */
    public function hasSystemMessageToday($userId, $subject) {
        $stmt = $this->db->prepare("
            SELECT id
            FROM messages
            WHERE receiver_id = ?
              AND type = 'system'
              AND subject = ?
              AND DATE(created_at) = CURDATE()
            LIMIT 1
        ");
        $stmt->execute([$userId, $subject]);
        return (bool)$stmt->fetch();
    }

    /**
     * Gửi tin nhắn cho admin
     */
    public function sendToAdmin($userId, $subject, $content) {
        return $this->create([
            'sender_id' => $userId,
            'receiver_id' => null,
            'subject' => $subject,
            'content' => $content,
            'type' => 'user_to_admin'
        ]);
    }

    /**
     * Admin gửi tin nhắn cho user
     */
    public function sendFromAdmin($adminId, $userId, $subject, $content) {
        return $this->create([
            'sender_id' => $adminId,
            'receiver_id' => $userId,
            'subject' => $subject,
            'content' => $content,
            'type' => 'admin_to_user'
        ]);
    }

    /**
     * Gửi tin nhắn hệ thống
     */
    public function sendSystem($userId, $subject, $content) {
        return $this->create([
            'sender_id' => null,
            'receiver_id' => $userId,
            'subject' => $subject,
            'content' => $content,
            'type' => 'system'
        ]);
    }
}
