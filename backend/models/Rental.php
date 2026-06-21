<?php
/**
 * Rental Model
 * Xử lý dữ liệu thuê sách
 */

require_once __DIR__ . '/../config/database.php';

class Rental {
    private $db;
    
    public function __construct() {
        $this->db = getDB();
    }

    public function findById($id) {
        $stmt = $this->db->prepare("
            SELECT r.*, b.title, b.author, b.cover_image, b.price_per_day,
                   u.username, u.full_name, u.email, u.phone
            FROM rentals r
            JOIN books b ON r.book_id = b.id
            JOIN users u ON r.user_id = u.id
            WHERE r.id = ?
        ");
        $stmt->execute([$id]);
        return $stmt->fetch();
    }

    public function findByRentalCode($rentalCode) {
        $stmt = $this->db->prepare("
            SELECT r.*, b.title, b.author, b.cover_image, b.price_per_day,
                   u.username, u.full_name, u.email, u.phone
            FROM rentals r
            JOIN books b ON r.book_id = b.id
            JOIN users u ON r.user_id = u.id
            WHERE r.rental_code = ?
        ");
        $stmt->execute([$rentalCode]);
        return $stmt->fetch();
    }

    public function create($data) {
        $stmt = $this->db->prepare("
            INSERT INTO rentals (user_id, book_id, rental_date, due_date, status, total_price, notes, rental_code, pickup_deadline, picked_up_at)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
        ");
        $stmt->execute([
            $data['user_id'],
            $data['book_id'],
            $data['rental_date'],
            $data['due_date'],
            $data['status'] ?? 'pending',
            $data['total_price'] ?? 0,
            $data['notes'] ?? '',
            $data['rental_code'] ?? null,
            $data['pickup_deadline'] ?? null,
            $data['picked_up_at'] ?? null
        ]);
        return $this->db->lastInsertId();
    }

    public function rentalCodeExists($rentalCode) {
        $stmt = $this->db->prepare("SELECT id FROM rentals WHERE rental_code = ? LIMIT 1");
        $stmt->execute([$rentalCode]);
        return (bool)$stmt->fetch();
    }

    public function generateUniqueRentalCode() {
        do {
            $code = 'WB' . strtoupper(bin2hex(random_bytes(3))); // ví dụ: WB9F2A1C
        } while ($this->rentalCodeExists($code));

        return $code;
    }

    public function update($id, $data) {
        $fields = [];
        $values = [];
        
        $allowed = ['return_date', 'status', 'notes', 'total_price'];
        foreach ($allowed as $field) {
            if (isset($data[$field])) {
                $fields[] = "$field = ?";
                $values[] = $data[$field];
            }
        }
        
        if (empty($fields)) return false;
        
        $values[] = $id;
        $sql = "UPDATE rentals SET " . implode(', ', $fields) . " WHERE id = ?";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute($values);
    }

    public function getByUser($userId, $status = null) {
        if ($status) {
            $stmt = $this->db->prepare("
                SELECT r.*, b.title, b.author, b.cover_image, b.price_per_day
                FROM rentals r
                JOIN books b ON r.book_id = b.id
                WHERE r.user_id = ? AND r.status = ?
                ORDER BY r.created_at DESC
            ");
            $stmt->execute([$userId, $status]);
        } else {
            $stmt = $this->db->prepare("
                SELECT r.*, b.title, b.author, b.cover_image, b.price_per_day
                FROM rentals r
                JOIN books b ON r.book_id = b.id
                WHERE r.user_id = ?
                ORDER BY r.created_at DESC
            ");
            $stmt->execute([$userId]);
        }
        return $stmt->fetchAll();
    }

    public function getActiveByUser($userId) {
        return $this->getByUser($userId, 'active');
    }

    public function getPendingByUser($userId) {
        return $this->getByUser($userId, 'pending');
    }

    public function getHistoryByUser($userId) {
        return $this->getByUser($userId, 'returned');
    }

    public function getAll($limit = 100, $offset = 0, $status = null) {
        if ($status) {
            $stmt = $this->db->prepare("
                SELECT r.*, b.title, b.author, b.cover_image,
                       u.username, u.full_name, u.email
                FROM rentals r
                JOIN books b ON r.book_id = b.id
                JOIN users u ON r.user_id = u.id
                WHERE r.status = ?
                ORDER BY r.created_at DESC
                LIMIT ? OFFSET ?
            ");
            $stmt->execute([$status, $limit, $offset]);
        } else {
            $stmt = $this->db->prepare("
                SELECT r.*, b.title, b.author, b.cover_image,
                       u.username, u.full_name, u.email
                FROM rentals r
                JOIN books b ON r.book_id = b.id
                JOIN users u ON r.user_id = u.id
                ORDER BY r.created_at DESC
                LIMIT ? OFFSET ?
            ");
            $stmt->execute([$limit, $offset]);
        }
        return $stmt->fetchAll();
    }

    public function returnBook($id) {
        $rental = $this->findById($id);
        if (!$rental || $rental['status'] !== 'active') {
            return false;
        }

        $returnDate = date('Y-m-d');
        
        $stmt = $this->db->prepare("
            UPDATE rentals 
            SET return_date = ?, status = 'returned', updated_at = NOW()
            WHERE id = ?
        ");
        $result = $stmt->execute([$returnDate, $id]);
        
        if ($result) {
            $bookStmt = $this->db->prepare("UPDATE books SET quantity = quantity + 1 WHERE id = ?");
            $bookStmt->execute([$rental['book_id']]);
        }
        
        return $result;
    }

    public function confirmPickup($id) {
        $rental = $this->findById($id);
        if (!$rental || $rental['status'] !== 'pending') {
            return false;
        }

        $stmt = $this->db->prepare("
            UPDATE rentals
            SET status = 'active', picked_up_at = NOW(), updated_at = NOW()
            WHERE id = ?
        ");
        return $stmt->execute([$id]);
    }

    public function cancel($id) {
        $rental = $this->findById($id);
        if (!$rental || $rental['status'] !== 'pending') {
            return false;
        }

        $stmt = $this->db->prepare("UPDATE rentals SET status = 'cancelled' WHERE id = ?");
        $result = $stmt->execute([$id]);
        
        if ($result) {
            $bookStmt = $this->db->prepare("UPDATE books SET quantity = quantity + 1 WHERE id = ?");
            $bookStmt->execute([$rental['book_id']]);
        }
        
        return $result;
    }

    public function countAll($status = null) {
        if ($status) {
            $stmt = $this->db->query("SELECT COUNT(*) as total FROM rentals WHERE status = '$status'");
        } else {
            $stmt = $this->db->query("SELECT COUNT(*) as total FROM rentals");
        }
        return $stmt->fetch()['total'];
    }

    public function countByUser($userId, $status = null) {
        if ($status) {
            $stmt = $this->db->prepare("SELECT COUNT(*) as total FROM rentals WHERE user_id = ? AND status = ?");
            $stmt->execute([$userId, $status]);
        } else {
            $stmt = $this->db->prepare("SELECT COUNT(*) as total FROM rentals WHERE user_id = ?");
            $stmt->execute([$userId]);
        }
        return $stmt->fetch()['total'];
    }

    public function getStats() {
        $stmt = $this->db->query("
            SELECT 
                COUNT(*) as total_rentals,
                SUM(CASE WHEN status = 'active' THEN 1 ELSE 0 END) as active_rentals,
                SUM(CASE WHEN status = 'returned' THEN 1 ELSE 0 END) as returned_rentals,
                SUM(CASE WHEN status = 'overdue' THEN 1 ELSE 0 END) as overdue_rentals,
                SUM(CASE WHEN status = 'pending' THEN 1 ELSE 0 END) as pending_rentals,
                SUM(CASE 
                    WHEN status IN ('active','returned','overdue') THEN total_price 
                    ELSE 0 
                END) as total_revenue
            FROM rentals
        ");
        return $stmt->fetch();
    }

    /**
     * Report doanh thu theo tháng (12 tháng) cho admin dashboard
     * Rule: SUM(rentals.total_price) với status IN ('active','returned','overdue') và loại cancelled
     */
    public function getRevenueByMonth($year = null) {
        $year = $year ?? (int)date('Y');

        // Lấy doanh thu từng tháng
        $stmt = $this->db->prepare("
            SELECT 
                MONTH(rental_date) as month_num,
                SUM(total_price) as revenue
            FROM rentals
            WHERE status IN ('active','returned','overdue')
              AND rental_date IS NOT NULL
              AND YEAR(rental_date) = ?
            GROUP BY MONTH(rental_date)
        ");
        $stmt->execute([$year]);
        $rows = $stmt->fetchAll();

        // Map tháng -> revenue
        $revenueByMonthNum = [];
        foreach ($rows as $row) {
            $m = (int)$row['month_num'];
            $revenueByMonthNum[$m] = (float)$row['revenue'];
        }

        $labels = ['Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec'];
        $data = [];
        for ($m = 1; $m <= 12; $m++) {
            $data[] = isset($revenueByMonthNum[$m]) ? $revenueByMonthNum[$m] : 0;
        }

        return [
            'labels' => $labels,
            'data' => $data,
            'year' => $year
        ];
    }

    public function updateOverdueStatus() {
        $stmt = $this->db->prepare("
            UPDATE rentals 
            SET status = 'overdue' 
            WHERE status = 'active' AND due_date < CURDATE()
        ");
        return $stmt->execute();
    }

    public function getOverdueRentals() {
        $stmt = $this->db->query("
            SELECT r.*, b.title, b.author, u.username, u.full_name, u.email, u.phone
            FROM rentals r
            JOIN books b ON r.book_id = b.id
            JOIN users u ON r.user_id = u.id
            WHERE r.status = 'overdue'
            ORDER BY r.due_date ASC
        ");
        return $stmt->fetchAll();
    }

    /**
     * Lấy các đơn thuê sắp đến hạn (trong 2 ngày)
     */
    public function getDueReminders() {
        $stmt = $this->db->query("
            SELECT r.*, b.title, b.author, u.full_name, u.email
            FROM rentals r
            JOIN books b ON r.book_id = b.id
            JOIN users u ON r.user_id = u.id
            WHERE r.status = 'active'
              AND r.due_date BETWEEN CURDATE() AND DATE_ADD(CURDATE(), INTERVAL 2 DAY)
            ORDER BY r.due_date ASC
        ");
        return $stmt->fetchAll();
    }

    /**
     * Lấy các đơn thuê quá hạn
     */
    public function getAllOverdue() {
        $stmt = $this->db->query("
            SELECT r.*, b.title, b.author, u.full_name, u.email, u.phone
            FROM rentals r
            JOIN books b ON r.book_id = b.id
            JOIN users u ON r.user_id = u.id
            WHERE r.status = 'active' AND r.due_date < CURDATE()
            ORDER BY r.due_date ASC
        ");
        return $stmt->fetchAll();
    }
}
