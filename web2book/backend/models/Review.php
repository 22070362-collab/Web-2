<?php
/**
 * Review Model
 * Xử lý dữ liệu đánh giá sách
 */

require_once __DIR__ . '/../config/database.php';

class Review {
    private $db;

    public function __construct() {
        $this->db = getDB();
    }

    public function getBookReviewSummary($bookId) {
        $stmt = $this->db->prepare("
            SELECT COUNT(*) as review_count, AVG(rating) as avg_rating
            FROM reviews
            WHERE book_id = ?
        ");
        $stmt->execute([$bookId]);
        $row = $stmt->fetch();

        return [
            'review_count' => (int)($row['review_count'] ?? 0),
            'avg_rating' => $row['avg_rating'] !== null ? (float)$row['avg_rating'] : null
        ];
    }

    public function getBookReviews($bookId, $limit = 20) {
        $stmt = $this->db->prepare("
            SELECT r.id, r.user_id, r.book_id, r.rating, r.comment, r.created_at,
                   u.full_name, u.username
            FROM reviews r
            JOIN users u ON r.user_id = u.id
            WHERE r.book_id = ?
            ORDER BY r.created_at DESC
            LIMIT ?
        ");
        $stmt->execute([$bookId, $limit]);
        return $stmt->fetchAll();
    }

    public function getUserReview($userId, $bookId) {
        $stmt = $this->db->prepare("
            SELECT *
            FROM reviews
            WHERE user_id = ? AND book_id = ?
            ORDER BY created_at DESC
            LIMIT 1
        ");
        $stmt->execute([$userId, $bookId]);
        return $stmt->fetch();
    }

    public function hasUserRentedBook($userId, $bookId) {
        $stmt = $this->db->prepare("
            SELECT id
            FROM rentals
            WHERE user_id = ?
              AND book_id = ?
              AND status IN ('active', 'returned', 'overdue')
            LIMIT 1
        ");
        $stmt->execute([$userId, $bookId]);
        return (bool)$stmt->fetch();
    }

    public function saveUserReview($userId, $bookId, $rating, $comment) {
        $existing = $this->getUserReview($userId, $bookId);

        if ($existing) {
            $stmt = $this->db->prepare("
                UPDATE reviews
                SET rating = ?, comment = ?
                WHERE id = ?
            ");
            return $stmt->execute([$rating, $comment, $existing['id']]);
        }

        $stmt = $this->db->prepare("
            INSERT INTO reviews (user_id, book_id, rating, comment)
            VALUES (?, ?, ?, ?)
        ");
        return $stmt->execute([$userId, $bookId, $rating, $comment]);
    }
}
