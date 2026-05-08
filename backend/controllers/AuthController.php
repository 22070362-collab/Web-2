<?php
/**
 * Auth Controller
 * Xử lý đăng nhập, đăng ký, đăng xuất
 */

require_once __DIR__ . '/../models/User.php';
require_once __DIR__ . '/../models/Cart.php';

class AuthController {
    private $userModel;
    private $cartModel;

    public function __construct() {
        $this->userModel = new User();
        $this->cartModel = new Cart();
    }

    public function login() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            return ['success' => false, 'message' => 'Phương thức không hợp lệ'];
        }

        $username = trim($_POST['username'] ?? '');
        $password = $_POST['password'] ?? '';

        if (empty($username) || empty($password)) {
            return ['success' => false, 'message' => 'Vui lòng nhập đầy đủ thông tin'];
        }

        $user = $this->userModel->findByUsername($username);

        if (!$user || !$this->userModel->verifyPassword($user, $password)) {
            return ['success' => false, 'message' => 'Tên đăng nhập hoặc mật khẩu không đúng'];
        }

        $_SESSION['user_id'] = $user['id'];
        $_SESSION['username'] = $user['username'];
        $_SESSION['role'] = $user['role'];
        $_SESSION['full_name'] = $user['full_name'];

        $this->cartModel->assignToUser($user['id']);

        return ['success' => true, 'message' => 'Đăng nhập thành công', 'user' => $user];
    }

    public function register() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            return ['success' => false, 'message' => 'Phương thức không hợp lệ'];
        }

        $username = trim($_POST['username'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';
        $confirm_password = $_POST['confirm_password'] ?? '';
        $full_name = trim($_POST['full_name'] ?? '');
        $phone = trim($_POST['phone'] ?? '');
        $address = trim($_POST['address'] ?? '');

        if (empty($username) || empty($email) || empty($password) || empty($full_name)) {
            return ['success' => false, 'message' => 'Vui lòng nhập đầy đủ thông tin bắt buộc'];
        }

        if ($password !== $confirm_password) {
            return ['success' => false, 'message' => 'Mật khẩu xác nhận không khớp'];
        }

        if (strlen($password) < 6) {
            return ['success' => false, 'message' => 'Mật khẩu phải có ít nhất 6 ký tự'];
        }

        if ($this->userModel->findByUsername($username)) {
            return ['success' => false, 'message' => 'Tên đăng nhập đã tồn tại'];
        }

        if ($this->userModel->findByEmail($email)) {
            return ['success' => false, 'message' => 'Email đã được sử dụng'];
        }

        $userId = $this->userModel->create([
            'username' => $username,
            'email' => $email,
            'password' => $this->userModel->hashPassword($password),
            'full_name' => $full_name,
            'phone' => $phone,
            'address' => $address,
            'role' => 'user'
        ]);

        if ($userId) {
            return ['success' => true, 'message' => 'Đăng ký thành công'];
        }

        return ['success' => false, 'message' => 'Đăng ký thất bại'];
    }

    public function logout() {
        session_destroy();
        return ['success' => true, 'message' => 'Đăng xuất thành công'];
    }

    public function isLoggedIn() {
        return isset($_SESSION['user_id']);
    }

    public function isAdmin() {
        return isset($_SESSION['role']) && $_SESSION['role'] === 'admin';
    }

    public function requireLogin() {
        if (!$this->isLoggedIn()) {
            $path = strpos($_SERVER['PHP_SELF'], '/admin/') !== false ? '../login.php' : 'login.php';
            header('Location: ' . $path);
            exit;
        }
    }

    public function requireAdmin() {
        if (!$this->isAdmin()) {
            $path = strpos($_SERVER['PHP_SELF'], '/admin/') !== false ? '../index.php' : 'index.php';
            header('Location: ' . $path);
            exit;
        }
    }

    public function getCurrentUser() {
        if (!$this->isLoggedIn()) {
            return null;
        }
        return $this->userModel->findById($_SESSION['user_id']);
    }
}
