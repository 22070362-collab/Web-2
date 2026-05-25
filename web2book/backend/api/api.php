<?php
/**
 * API Endpoint
 * Xử lý các yêu cầu API từ frontend
 */

header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

session_start();

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../models/Book.php';
require_once __DIR__ . '/../models/Cart.php';
require_once __DIR__ . '/../models/Rental.php';
require_once __DIR__ . '/../models/User.php';
require_once __DIR__ . '/controllers/BookController.php';
require_once __DIR__ . '/controllers/AuthController.php';
require_once __DIR__ . '/controllers/RentalController.php';

$action = $_GET['action'] ?? $_POST['action'] ?? '';

$bookController = new BookController();
$authController = new AuthController();
$rentalController = new RentalController();

$result = ['success' => false, 'message' => 'Hành động không hợp lệ'];

switch ($action) {
    case 'get_books':
        $books = $bookController->index();
        $result = ['success' => true, 'data' => $books];
        break;

    case 'get_available_books':
        $books = $bookController->available();
        $result = ['success' => true, 'data' => $books];
        break;

    case 'get_book':
        $id = intval($_GET['id'] ?? 0);
        $book = $bookController->show($id);
        if ($book) {
            $result = ['success' => true, 'data' => $book];
        } else {
            $result = ['success' => false, 'message' => 'Không tìm thấy sách'];
        }
        break;

    case 'search_books':
        $keyword = $_GET['keyword'] ?? '';
        $books = $bookController->search($keyword);
        $result = ['success' => true, 'data' => $books];
        break;

    case 'get_categories':
        $categories = $bookController->categories();
        $result = ['success' => true, 'data' => $categories];
        break;

    case 'get_books_by_category':
        $category = $_GET['category'] ?? '';
        $books = $bookController->category($category);
        $result = ['success' => true, 'data' => $books];
        break;

    case 'add_to_cart':
        $bookId = intval($_POST['book_id'] ?? 0);
        $quantity = intval($_POST['quantity'] ?? 1);
        $rentalDays = intval($_POST['rental_days'] ?? 7);
        
        $cartModel = new Cart();
        if ($cartModel->addItem($bookId, $quantity, $rentalDays)) {
            $count = $cartModel->getItemCount();
            $result = ['success' => true, 'message' => 'Đã thêm vào giỏ hàng', 'count' => $count];
        } else {
            $result = ['success' => false, 'message' => 'Không thể thêm vào giỏ hàng'];
        }
        break;

    case 'get_cart':
        $cartModel = new Cart();
        $items = $cartModel->getItems();
        $total = $cartModel->getTotal();
        $result = ['success' => true, 'data' => $items, 'total' => $total];
        break;

    case 'update_cart':
        $cartId = intval($_POST['cart_id'] ?? 0);
        $quantity = intval($_POST['quantity'] ?? 1);
        $rentalDays = intval($_POST['rental_days'] ?? 7);
        
        $cartModel = new Cart();
        if ($cartModel->updateItem($cartId, $quantity, $rentalDays)) {
            $result = ['success' => true, 'message' => 'Cập nhật giỏ hàng thành công'];
        } else {
            $result = ['success' => false, 'message' => 'Không thể cập nhật giỏ hàng'];
        }
        break;

    case 'remove_from_cart':
        $cartId = intval($_POST['cart_id'] ?? 0);
        
        $cartModel = new Cart();
        if ($cartModel->removeItem($cartId)) {
            $result = ['success' => true, 'message' => 'Đã xóa khỏi giỏ hàng'];
        } else {
            $result = ['success' => false, 'message' => 'Không thể xóa khỏi giỏ hàng'];
        }
        break;

    case 'clear_cart':
        $cartModel = new Cart();
        $cartModel->clear();
        $result = ['success' => true, 'message' => 'Đã xóa giỏ hàng'];
        break;

    case 'checkout':
        if (!$authController->isLoggedIn()) {
            $result = ['success' => false, 'message' => 'Vui lòng đăng nhập để thuê sách'];
            break;
        }
        
        $result = $rentalController->checkout($_SESSION['user_id']);
        break;

    case 'rent_book':
        if (!$authController->isLoggedIn()) {
            $result = ['success' => false, 'message' => 'Vui lòng đăng nhập để thuê sách'];
            break;
        }
        
        $bookId = intval($_POST['book_id'] ?? 0);
        $rentalDays = intval($_POST['rental_days'] ?? 7);
        
        $result = $rentalController->create($_SESSION['user_id'], $bookId, $rentalDays);
        break;

    case 'get_my_rentals':
        if (!$authController->isLoggedIn()) {
            $result = ['success' => false, 'message' => 'Vui lòng đăng nhập'];
            break;
        }
        
        $status = $_GET['status'] ?? null;
        $rentals = $rentalController->getUserRentals($_SESSION['user_id'], $status);
        $result = ['success' => true, 'data' => $rentals];
        break;

    case 'get_active_rentals':
        if (!$authController->isLoggedIn()) {
            $result = ['success' => false, 'message' => 'Vui lòng đăng nhập'];
            break;
        }
        
        $rentals = $rentalController->getActiveRentals($_SESSION['user_id']);
        $result = ['success' => true, 'data' => $rentals];
        break;

    case 'return_book':
        if (!$authController->isLoggedIn()) {
            $result = ['success' => false, 'message' => 'Vui lòng đăng nhập'];
            break;
        }
        
        $rentalId = intval($_POST['rental_id'] ?? 0);
        $result = $rentalController->returnBook($rentalId);
        break;

    case 'cancel_rental':
        if (!$authController->isLoggedIn()) {
            $result = ['success' => false, 'message' => 'Vui lòng đăng nhập'];
            break;
        }
        
        $rentalId = intval($_POST['rental_id'] ?? 0);
        $result = $rentalController->cancel($rentalId);
        break;

    case 'login':
        $result = $authController->login();
        break;

    case 'register':
        $result = $authController->register();
        break;

    case 'logout':
        $result = $authController->logout();
        break;

    case 'check_auth':
        if ($authController->isLoggedIn()) {
            $user = $authController->getCurrentUser();
            $result = [
                'success' => true,
                'isLoggedIn' => true,
                'user' => [
                    'id' => $user['id'],
                    'username' => $user['username'],
                    'full_name' => $user['full_name'],
                    'role' => $user['role']
                ]
            ];
        } else {
            $result = ['success' => true, 'isLoggedIn' => false];
        }
        break;

    // ===== COUPON SYSTEM =====
    case 'apply_coupon':
        $code = $_GET['code'] ?? $_POST['code'] ?? '';
        $db = getDB();
        $stmt = $db->prepare("SELECT * FROM coupons WHERE code = ? AND active = 1 AND expires_at > NOW()");
        $stmt->execute([$code]);
        $coupon = $stmt->fetch();
        
        if ($coupon) {
            $result = [
                'success' => true, 
                'message' => 'Áp dụng mã giảm giá thành công!',
                'discount' => $coupon['discount_percent'],
                'coupon_id' => $coupon['id']
            ];
        } else {
            $result = ['success' => false, 'message' => 'Mã giảm giá không hợp lệ hoặc đã hết hạn'];
        }
        break;

    // ===== WISHLIST =====
    case 'add_to_wishlist':
        $bookId = intval($_POST['book_id'] ?? $_GET['book_id'] ?? 0);
        if (!$authController->isLoggedIn()) {
            $result = ['success' => false, 'message' => 'Vui lòng đăng nhập'];
            break;
        }
        $db = getDB();
        $stmt = $db->prepare("INSERT IGNORE INTO wishlist (user_id, book_id) VALUES (?, ?)");
        $stmt->execute([$_SESSION['user_id'], $bookId]);
        $result = ['success' => true, 'message' => 'Đã thêm vào yêu thích'];
        break;

    case 'remove_from_wishlist':
        $bookId = intval($_POST['book_id'] ?? $_GET['book_id'] ?? 0);
        if (!$authController->isLoggedIn()) {
            $result = ['success' => false, 'message' => 'Vui lòng đăng nhập'];
            break;
        }
        $db = getDB();
        $stmt = $db->prepare("DELETE FROM wishlist WHERE user_id = ? AND book_id = ?");
        $stmt->execute([$_SESSION['user_id'], $bookId]);
        $result = ['success' => true, 'message' => 'Đã xóa khỏi yêu thích'];
        break;

    case 'get_wishlist':
        if (!$authController->isLoggedIn()) {
            $result = ['success' => false, 'message' => 'Vui lòng đăng nhập'];
            break;
        }
        $db = getDB();
        $stmt = $db->prepare("SELECT b.* FROM wishlist w JOIN books b ON w.book_id = b.id WHERE w.user_id = ?");
        $stmt->execute([$_SESSION['user_id']]);
        $result = ['success' => true, 'data' => $stmt->fetchAll()];
        break;

    default:
        $result = ['success' => false, 'message' => 'API action not found'];
}

echo json_encode($result, JSON_UNESCAPED_UNICODE);
