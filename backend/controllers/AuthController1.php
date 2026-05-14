<?php

require_once __DIR__ . '/../models/User.php';
require_once __DIR__ . '/../models/Cart.php';

class AuthController {

    private $userModel;
    private $cartModel;

    public function __construct() {
        $this->userModel = new User();
        $this->cartModel = new Cart();
    }

    /**
     * LOGIN
     */
    public function login() {

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            return [
                'success' => false,
                'message' => 'Invalid request method'
            ];
        }

        if (
            !isset($_POST['csrf_token']) ||
            $_POST['csrf_token'] !== $_SESSION['csrf_token']
        ) {
            die('Invalid CSRF token');
        }

        $username = trim($_POST['username'] ?? '');
        $password = trim($_POST['password'] ?? '');

        if (empty($username) || empty($password)) {
            return [
                'success' => false,
                'message' => 'Please fill in all required fields'
            ];
        }
         $user = $this->userModel->findByUsername($username);

        if (!$user) {
            return [
                'success' => false,
                'message' => 'User does not exist'
            ];
        }

        if (!$this->userModel->verifyPassword($user, $password)) {
            return [
                'success' => false,
                'message' => 'Incorrect username or password'
            ];
        }
 session_regenerate_id(true);

        $_SESSION['user_id'] = $user['id'];
        $_SESSION['username'] = $user['username'];
        $_SESSION['role'] = $user['role'];
        $_SESSION['full_name'] = $user['full_name'];

        $this->cartModel->assignToUser($user['id']);

        return [
            'success' => true,
            'message' => 'Login successful',
            'user' => $user
        ];
    }

    /**
     * REGISTER
     */
    public function register() {

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            return [
                'success' => false,
                'message' => 'Invalid request method'
            ];
        }

        if (
            !isset($_POST['csrf_token']) ||
            $_POST['csrf_token'] !== $_SESSION['csrf_token']
        ) {
            die('Invalid CSRF token');
        }

        $username = trim($_POST['username'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $password = trim($_POST['password'] ?? '');
        $confirm_password = trim($_POST['confirm_password'] ?? '');
        $full_name = trim($_POST['full_name'] ?? '');
        $phone = trim($_POST['phone'] ?? '');
        $address = trim($_POST['address'] ?? '');

        if (
            empty($username) ||
            empty($email) ||
            empty($password) ||
            empty($full_name)
        ) {
            return [
                'success' => false,
                'message' => 'Please fill in all required fields'
            ];
        }
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return [
                'success' => false,
                'message' => 'Invalid email address'
            ];
        }

        if (!preg_match('/^[a-zA-Z0-9_]{4,20}$/', $username)) {
            return [
                'success' => false,
                'message' => 'Invalid username format'
            ];
        }

        if (!empty($phone) &&
            !preg_match('/^[0-9]{9,11}$/', $phone)) {

            return [
                'success' => false,
                'message' => 'Invalid phone number'
            ];
        }
         if ($password !== $confirm_password) {
            return [
                'success' => false,
                'message' => 'Passwords do not match'
            ];
        }

        if (strlen($password) < 6) {
            return [
                'success' => false,
                'message' => 'Password must contain at least 6 characters'
            ];
        }

        if ($this->userModel->findByUsername($username)) {
            return [
                'success' => false,
                'message' => 'Username already exists'
            ];
        }
         if ($this->userModel->findByEmail($email)) {
            return [
                'success' => false,
                'message' => 'Email already exists'
            ];
        }

        $userId = $this->userModel->create([
            'username' => htmlspecialchars($username),
            'email' => htmlspecialchars($email),
            'password' => password_hash($password, PASSWORD_DEFAULT),
            'full_name' => htmlspecialchars($full_name),
            'phone' => htmlspecialchars($phone),
            'address' => htmlspecialchars($address),
            'role' => 'user'
        ]);

        if ($userId) {
            return [
                'success' => true,
                'message' => 'Registration successful'
            ];
        }

        return [
            'success' => false,
            'message' => 'Registration failed'
        ];
    }
 /**
     * LOGOUT
     */
    public function logout() {

        $_SESSION = [];

        if (ini_get('session.use_cookies')) {

            $params = session_get_cookie_params();

            setcookie(
                session_name(),
                '',
                time() - 42000,
                $params['path'],
                $params['domain'],
                $params['secure'],
                $params['httponly']
            );
        }

        session_destroy();

        return [
            'success' => true,
            'message' => 'Logout successful'
        ];
    }
}