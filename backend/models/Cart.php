<?php
/**
 * Cart Model
 * Xử lý giỏ hàng thuê sách
 */

require_once __DIR__ . '/../config/database.php';

class Cart {
    private $db;
    
    public function __construct() {
        $this->db = getDB();
    }

    private function getSessionId() {
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }
        return session_id();
    }

    public function addItem($bookId, $quantity = 1, $rentalDays = 7, $userId = null) {
        $sessionId = $this->getSessionId();
        
        $checkStmt = $this->db->prepare("SELECT * FROM cart WHERE book_id = ? AND session_id = ?");
        $checkStmt->execute([$bookId, $sessionId]);
        $existing = $checkStmt->fetch();
        
        if ($existing) {
            $stmt = $this->db->prepare("UPDATE cart SET quantity = quantity + ?, rental_days = ? WHERE id = ?");
            return $stmt->execute([$quantity, $rentalDays, $existing['id']]);
        } else {
            $stmt = $this->db->prepare("
                INSERT INTO cart (session_id, user_id, book_id, quantity, rental_days)
                VALUES (?, ?, ?, ?, ?)
            ");
            return $stmt->execute([$sessionId, $userId, $bookId, $quantity, $rentalDays]);
        }
    }

    public function updateItem($id, $quantity, $rentalDays) {
        $stmt = $this->db->prepare("UPDATE cart SET quantity = ?, rental_days = ? WHERE id = ?");
        return $stmt->execute([$quantity, $rentalDays, $id]);
    }

    public function removeItem($id) {
        $stmt = $this->db->prepare("DELETE FROM cart WHERE id = ?");
        return $stmt->execute([$id]);
    }

    public function clear($sessionId = null) {
        $sessionId = $sessionId ?? $this->getSessionId();
        $stmt = $this->db->prepare("DELETE FROM cart WHERE session_id = ?");
        return $stmt->execute([$sessionId]);
    }

    public function getItems($sessionId = null) {
        $sessionId = $sessionId ?? $this->getSessionId();
        $stmt = $this->db->prepare("
            SELECT c.*, b.title, b.author, b.category, b.cover_image, b.price_per_day, b.quantity as available_quantity
            FROM cart c
            JOIN books b ON c.book_id = b.id
            WHERE c.session_id = ?
        ");
        $stmt->execute([$sessionId]);
        return $stmt->fetchAll();
    }

    public function getItemCount($sessionId = null) {
        $sessionId = $sessionId ?? $this->getSessionId();
        $stmt = $this->db->prepare("SELECT COUNT(*) as count FROM cart WHERE session_id = ?");
        $stmt->execute([$sessionId]);
        return $stmt->fetch()['count'];
    }

    public function getTotal($sessionId = null) {
        $items = $this->getItems($sessionId);
        $total = 0;
        foreach ($items as $item) {
            $total += $item['price_per_day'] * $item['rental_days'] * $item['quantity'];
        }
        return $total;
    }

    public function assignToUser($userId) {
        $sessionId = $this->getSessionId();
        $stmt = $this->db->prepare("UPDATE cart SET user_id = ? WHERE session_id = ?");
        return $stmt->execute([$userId, $sessionId]);
    }

    public function convertToRentals($userId) {
        $items = $this->getItems();
        $rentalModel = new Rental();
        $bookModel = new Book();
        $results = [];
        
        foreach ($items as $item) {
            if ($item['available_quantity'] < $item['quantity']) {
                $results[] = [
                    'book_id' => $item['book_id'],
                    'title' => $item['title'],
                    'success' => false,
                    'message' => 'Sách "' . $item['title'] . '" không đủ số lượng trong kho'
                ];
                continue;
            }
            
            $rentalDate = date('Y-m-d');
            $dueDate = date('Y-m-d', strtotime("+{$item['rental_days']} days"));
            $pickupDeadline = date('Y-m-d', strtotime('+2 days'));
            $totalPrice = $item['price_per_day'] * $item['rental_days'] * $item['quantity'];
            $rentalCode = $rentalModel->generateUniqueRentalCode();
            
            $rentalId = $rentalModel->create([
                'user_id' => $userId,
                'book_id' => $item['book_id'],
                'rental_date' => $rentalDate,
                'due_date' => $dueDate,
                'status' => 'pending',
                'total_price' => $totalPrice,
                'rental_code' => $rentalCode,
                'pickup_deadline' => $pickupDeadline
            ]);
            
            if ($rentalId) {
                $bookModel->updateQuantity($item['book_id'], -$item['quantity']);
                $this->removeItem($item['id']);
                $results[] = [
                    'rental_id' => $rentalId,
                    'rental_code' => $rentalCode,
                    'pickup_deadline' => $pickupDeadline,
                    'book_title' => $item['title'],
                    'title' => $item['title'],
                    'success' => true
                ];
            } else {
                $results[] = [
                    'book_id' => $item['book_id'],
                    'title' => $item['title'],
                    'success' => false,
                    'message' => 'Không thể tạo đơn thuê cho "' . $item['title'] . '"'
                ];
            }
        }
        
        return $results;
    }
}
