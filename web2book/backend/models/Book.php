<?php
/**
 * Book Model
 * Xử lý dữ liệu sách
 */

require_once __DIR__ . '/../config/database.php';

class Book {
    private $db;
    
    public function __construct() {
        $this->db = getDB();
    }

    public function findById($id) {
        $stmt = $this->db->prepare("SELECT * FROM books WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch();
    }

    public function getAll($limit = 100, $offset = 0) {
        $stmt = $this->db->prepare("SELECT * FROM books ORDER BY created_at DESC LIMIT ? OFFSET ?");
        $stmt->execute([$limit, $offset]);
        return $stmt->fetchAll();
    }

    public function getAvailable($limit = 100, $offset = 0) {
        $stmt = $this->db->prepare("SELECT * FROM books WHERE quantity > 0 AND is_available = 1 ORDER BY created_at DESC LIMIT ? OFFSET ?");
        $stmt->execute([$limit, $offset]);
        return $stmt->fetchAll();
    }

    public function search($keyword, $limit = 100) {
        $keyword = '%' . $keyword . '%';
        $stmt = $this->db->prepare("
            SELECT * FROM books 
            WHERE (title LIKE ? OR author LIKE ? OR category LIKE ?) 
            ORDER BY created_at DESC 
            LIMIT ?
        ");
        $stmt->execute([$keyword, $keyword, $keyword, $limit]);
        return $stmt->fetchAll();
    }

    public function getByCategory($category, $limit = 100) {
        $stmt = $this->db->prepare("SELECT * FROM books WHERE category = ? ORDER BY created_at DESC LIMIT ?");
        $stmt->execute([$category, $limit]);
        return $stmt->fetchAll();
    }

    public function getCategories() {
        $stmt = $this->db->query("SELECT DISTINCT category FROM books ORDER BY category");
        return $stmt->fetchAll(PDO::FETCH_COLUMN);
    }

    public function create($data) {
        $stmt = $this->db->prepare("
            INSERT INTO books (title, author, description, category, cover_image, quantity, price_per_day)
            VALUES (?, ?, ?, ?, ?, ?, ?)
        ");
        $stmt->execute([
            $data['title'],
            $data['author'],
            $data['description'] ?? '',
            $data['category'],
            $data['cover_image'] ?? 'default_book.png',
            $data['quantity'] ?? 1,
            $data['price_per_day'] ?? 1.00
        ]);
        return $this->db->lastInsertId();
    }

    public function update($id, $data) {
        $fields = [];
        $values = [];
        
        $allowed = ['title', 'author', 'description', 'category', 'cover_image', 'quantity', 'price_per_day', 'is_available'];
        foreach ($allowed as $field) {
            if (isset($data[$field])) {
                $fields[] = "$field = ?";
                $values[] = $data[$field];
            }
        }
        
        if (empty($fields)) return false;
        
        $values[] = $id;
        $sql = "UPDATE books SET " . implode(', ', $fields) . " WHERE id = ?";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute($values);
    }

    public function updateQuantity($id, $change) {
        $stmt = $this->db->prepare("UPDATE books SET quantity = quantity + ? WHERE id = ? AND quantity + ? >= 0");
        return $stmt->execute([$change, $id, $change]);
    }

    public function delete($id) {
        $stmt = $this->db->prepare("DELETE FROM books WHERE id = ?");
        return $stmt->execute([$id]);
    }

    public function countAll() {
        $stmt = $this->db->query("SELECT COUNT(*) as total FROM books");
        return $stmt->fetch()['total'];
    }

    public function countAvailable() {
        $stmt = $this->db->query("SELECT COUNT(*) as total FROM books WHERE quantity > 0");
        return $stmt->fetch()['total'];
    }

    public function getStats() {
        $stmt = $this->db->query("
            SELECT 
                COUNT(*) as total_books,
                SUM(quantity) as total_copies,
                SUM(CASE WHEN quantity > 0 THEN 1 ELSE 0 END) as available_books
            FROM books
        ");
        return $stmt->fetch();
    }
}
