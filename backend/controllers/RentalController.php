<?php
/**
 * Rental Controller
 * Xử lý các thao tác thuê sách
 */

require_once __DIR__ . '/../models/Rental.php';
require_once __DIR__ . '/../models/Book.php';
require_once __DIR__ . '/../models/Cart.php';
require_once __DIR__ . '/../models/Message.php';

class RentalController {
    private $rentalModel;
    private $bookModel;
    private $cartModel;
    private $messageModel;

    public function __construct() {
        $this->rentalModel = new Rental();
        $this->bookModel = new Book();
        $this->cartModel = new Cart();
        $this->messageModel = new Message();
    }

    /**
     * Gửi thông báo cho user
     */
    private function sendNotification($userId, $subject, $content) {
        return $this->messageModel->create([
            'receiver_id' => $userId,
            'subject' => $subject,
            'content' => $content,
            'type' => 'system'
        ]);
    }

    public function create($userId, $bookId, $rentalDays = 7) {
        $book = $this->bookModel->findById($bookId);

        if (!$book) {
            return ['success' => false, 'message' => 'Sách không tồn tại'];
        }

        if ($book['quantity'] < 1) {
            return ['success' => false, 'message' => 'Sách đã hết hàng'];
        }

        $rentalDate = date('Y-m-d');
        $dueDate = date('Y-m-d', strtotime("+{$rentalDays} days"));
        $pickupDeadline = date('Y-m-d', strtotime('+2 days'));
        $rentalCode = $this->rentalModel->generateUniqueRentalCode();
        $totalPrice = $book['price_per_day'] * $rentalDays;

        $rentalId = $this->rentalModel->create([
            'user_id' => $userId,
            'book_id' => $bookId,
            'rental_date' => $rentalDate,
            'due_date' => $dueDate,
            'status' => 'pending',
            'total_price' => $totalPrice,
            'rental_code' => $rentalCode,
            'pickup_deadline' => $pickupDeadline
        ]);

        if ($rentalId) {
            $this->bookModel->updateQuantity($bookId, -1);

            // Gửi thông báo đặt thuê thành công (chờ nhận sách)
            $this->sendNotification(
                $userId,
                "Đặt thuê thành công - {$book['title']}",
                "Bạn đã đặt thuê sách thành công!\n\n" .
                "Mã đơn hàng: {$rentalCode}\n" .
                "📖 Sách: {$book['title']}\n" .
                "✍️ Tác giả: {$book['author']}\n" .
                "📅 Ngày thuê: " . date('d/m/Y') . "\n" .
                "📅 Hạn đến nhận sách: " . date('d/m/Y', strtotime($pickupDeadline)) . "\n" .
                "📅 Ngày trả: " . date('d/m/Y', strtotime($dueDate)) . "\n" .
                "💰 Giá thuê: " . number_format($totalPrice, 0, ',', '.') . "đ\n\n" .
                "Vui lòng mang mã đơn đến cửa hàng trong vòng 2 ngày để nhận sách.\n" .
                "Sau khi admin xác nhận giao sách, trạng thái đơn sẽ chuyển sang thuê thành công."
            );

            return [
                'success' => true,
                'message' => 'Đặt thuê thành công, vui lòng đến nhận sách trong 2 ngày',
                'rental_id' => $rentalId,
                'rental_code' => $rentalCode
            ];
        }

        return ['success' => false, 'message' => 'Thuê sách thất bại'];
    }

    public function checkout($userId) {
        $items = $this->cartModel->getItems();

        if (empty($items)) {
            return ['success' => false, 'message' => 'Giỏ hàng trống'];
        }

        $results = $this->cartModel->convertToRentals($userId);

        $successCount = 0;
        $failCount = 0;
        $messages = [];
        $rentedBooks = [];

        foreach ($results as $result) {
            if ($result['success']) {
                $successCount++;
                $rentedBooks[] = $result['book_title'] ?? 'Sách';
            } else {
                $failCount++;
                $messages[] = $result['message'];
            }
        }

        // Gửi thông báo tổng hợp nếu có sách được thuê
        if ($successCount > 0) {
            $orderLines = [];
            foreach ($results as $result) {
                if (!empty($result['success'])) {
                    $title = $result['book_title'] ?? ($result['title'] ?? 'Sách');
                    $code = $result['rental_code'] ?? '';
                    $pickupDeadline = !empty($result['pickup_deadline']) ? date('d/m/Y', strtotime($result['pickup_deadline'])) : date('d/m/Y', strtotime('+2 days'));
                    $orderLines[] = "• {$title} - Mã: {$code} - Hạn nhận: {$pickupDeadline}";
                }
            }
            $booksList = implode("\n", $orderLines);
            $this->sendNotification(
                $userId,
                "Đặt thuê thành công - {$successCount} sách",
                "Bạn đã tạo {$successCount} đơn đặt thuê thành công!\n\n" .
                "📚 Danh sách đơn:\n{$booksList}\n\n" .
                "Vui lòng mang mã đơn đến cửa hàng trong vòng 2 ngày để nhận sách.\n" .
                "Sau khi admin xác nhận giao sách, trạng thái sẽ chuyển sang thuê thành công."
            );
        }

        return [
            'success' => $successCount > 0,
            'message' => "Đã tạo $successCount đơn đặt thuê" . ($failCount > 0 ? ". $failCount đơn thất bại" : ''),
            'details' => $results
        ];
    }

    public function getUserRentals($userId, $status = null) {
        return $this->rentalModel->getByUser($userId, $status);
    }

    public function getActiveRentals($userId) {
        return $this->rentalModel->getActiveByUser($userId);
    }

    public function getPendingRentals($userId) {
        return $this->rentalModel->getPendingByUser($userId);
    }

    public function getHistory($userId) {
        return $this->rentalModel->getHistoryByUser($userId);
    }

    public function getAll($limit = 100, $offset = 0, $status = null) {
        return $this->rentalModel->getAll($limit, $offset, $status);
    }

    public function show($id) {
        return $this->rentalModel->findById($id);
    }

    public function getRentalByCode($rentalCode) {
        return $this->rentalModel->findByRentalCode($rentalCode);
    }

    public function returnBook($id) {
        $rental = $this->rentalModel->findById($id);
        if ($this->rentalModel->returnBook($id)) {
            // Gửi thông báo trả sách thành công
            $this->sendNotification(
                $rental['user_id'],
                "✅ Đã xác nhận trả sách - {$rental['title']}",
                "Cảm ơn bạn đã trả sách!\n\n" .
                "📖 Sách: {$rental['title']}\n" .
                "📅 Ngày trả: " . date('d/m/Y') . "\n" .
                "💰 Số tiền đã thanh toán: " . number_format($rental['total_price'], 0, ',', '.') . "đ\n\n" .
                "Cảm ơn bạn đã sử dụng dịch vụ của MÂY MƠ BOOK!\n" .
                "Hẹn gặp lại bạn lần sau!"
            );
            return ['success' => true, 'message' => 'Xác nhận trả sách thành công'];
        }
        return ['success' => false, 'message' => 'Không thể xác nhận trả sách'];
    }

    public function confirmPickup($id) {
        $rental = $this->rentalModel->findById($id);
        if (!$rental) {
            return ['success' => false, 'message' => 'Không tìm thấy đơn thuê'];
        }

        if ($this->rentalModel->confirmPickup($id)) {
            $this->sendNotification(
                $rental['user_id'],
                "Thuê sách thành công - {$rental['title']}",
                "Đơn hàng của bạn đã được xác nhận giao sách thành công.\n\n" .
                "Mã đơn hàng: {$rental['rental_code']}\n" .
                "📖 Sách: {$rental['title']}\n" .
                "📅 Ngày nhận: " . date('d/m/Y') . "\n" .
                "📅 Ngày trả: " . date('d/m/Y', strtotime($rental['due_date'])) . "\n\n" .
                "Chúc bạn đọc sách vui vẻ!"
            );
            return ['success' => true, 'message' => 'Đã xác nhận giao sách cho khách'];
        }

        return ['success' => false, 'message' => 'Không thể xác nhận giao sách'];
    }

    /**
     * Gửi thông báo nhắc nhở trước hạn
     */
    public function sendDueReminders() {
        // Lấy các đơn thuê sắp đến hạn (trong 2 ngày tới)
        $reminders = $this->rentalModel->getDueReminders();

        $sent = 0;
        foreach ($reminders as $rental) {
            $daysLeft = ceil((strtotime($rental['due_date']) - time()) / (60 * 60 * 24));
            $subject = "⏰ Nhắc nhở: Sách sắp đến hạn trả - {$rental['title']}";

            // Tránh gửi lặp cùng một thông báo trong cùng ngày
            if ($this->messageModel->hasSystemMessageToday($rental['user_id'], $subject)) {
                continue;
            }

            $this->sendNotification(
                $rental['user_id'],
                $subject,
                "Xin chào {$rental['full_name']}!\n\n" .
                "Sách bạn đang thuê sắp đến hạn trả:\n\n" .
                "📖 Sách: {$rental['title']}\n" .
                "📅 Ngày trả: " . date('d/m/Y', strtotime($rental['due_date'])) . "\n" .
                "⏰ Còn {$daysLeft} ngày\n\n" .
                "Vui lòng chuẩn bị trả sách đúng hạn.\n" .
                "Nếu cần gia hạn, hãy liên hệ với chúng tôi!\n\n" .
                "Cảm ơn bạn! ❤️"
            );
            $sent++;
        }

        return $sent;
    }

    /**
     * Gửi thông báo sách quá hạn
     */
    public function sendOverdueNotifications() {
        $overdue = $this->rentalModel->getOverdueRentals();

        $sent = 0;
        foreach ($overdue as $rental) {
            $daysOverdue = ceil((time() - strtotime($rental['due_date'])) / (60 * 60 * 24));
            $subject = "⚠️ Cảnh báo: Sách quá hạn - {$rental['title']}";

            // Tránh gửi lặp cùng một thông báo trong cùng ngày
            if ($this->messageModel->hasSystemMessageToday($rental['user_id'], $subject)) {
                continue;
            }

            $this->sendNotification(
                $rental['user_id'],
                $subject,
                "Xin chào {$rental['full_name']}!\n\n" .
                "Sách bạn đang thuê đã quá hạn trả:\n\n" .
                "📖 Sách: {$rental['title']}\n" .
                "📅 Ngày hết hạn: " . date('d/m/Y', strtotime($rental['due_date'])) . "\n" .
                "⚠️ Quá hạn: {$daysOverdue} ngày\n\n" .
                "Vui lòng liên hệ với chúng tôi để giải quyết.\n" .
                "Cảm ơn bạn!"
            );
            $sent++;
        }

        return $sent;
    }

    public function cancel($id) {
        if ($this->rentalModel->cancel($id)) {
            return ['success' => true, 'message' => 'Hủy đơn thuê thành công'];
        }
        return ['success' => false, 'message' => 'Không thể hủy đơn thuê'];
    }

    public function stats() {
        return $this->rentalModel->getStats();
    }

    public function getOverdue() {
        return $this->rentalModel->getOverdueRentals();
    }

    /**
     * Report doanh thu theo tháng (12 tháng) cho admin dashboard
     */
    public function getRevenueByMonth($year = null) {
        return $this->rentalModel->getRevenueByMonth($year);
    }
}
