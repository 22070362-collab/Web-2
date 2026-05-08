<?php
require_once __DIR__ . '/../templates/header.php';

if ($isLoggedIn) {
    header('Location: index.php');
    exit;
}

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirmPassword = $_POST['confirm_password'] ?? '';
    $fullName = trim($_POST['full_name'] ?? '');
    
    if (empty($username) || empty($email) || empty($password) || empty($fullName)) {
        $error = 'Vui lòng điền đầy đủ thông tin bắt buộc.';
    } elseif ($password !== $confirmPassword) {
        $error = 'Mật khẩu không khớp.';
    } elseif (strlen($password) < 6) {
        $error = 'Mật khẩu phải có ít nhất 6 ký tự.';
    } else {
        require_once __DIR__ . '/../../backend/models/User.php';
        $userModel = new User();
        
        if ($userModel->findByUsername($username)) {
            $error = 'Tên đăng nhập đã tồn tại.';
        } elseif ($userModel->findByEmail($email)) {
            $error = 'Email đã được sử dụng.';
        } else {
            $userId = $userModel->create([
                'username' => $username,
                'email' => $email,
                'password' => $userModel->hashPassword($password),
                'full_name' => $fullName,
                'phone' => '',
                'address' => '',
                'role' => 'user'
            ]);
            
            if ($userId) {
                $success = 'Đăng ký thành công! Vui lòng đăng nhập.';
            } else {
                $error = 'Đăng ký thất bại. Vui lòng thử lại.';
            }
        }
    }
}
?>

<section class="auth-page">
    <div class="auth-card">
        <div class="auth-icon">
            <i class="fas fa-user-plus"></i>
        </div>
        
        <div class="auth-header">
            <h2>Tạo Tài Khoản</h2>
            <p>Tham gia BookRent ngay hôm nay</p>
        </div>
        
        <?php if ($error): ?>
        <div class="alert alert-danger">
            <i class="fas fa-exclamation-circle"></i>
            <?php echo htmlspecialchars($error); ?>
        </div>
        <?php endif; ?>
        
        <?php if ($success): ?>
        <div class="alert alert-success">
            <i class="fas fa-check-circle"></i>
            <?php echo htmlspecialchars($success); ?>
        </div>
        <div style="text-align: center; margin-top: 20px;">
            <a href="login.php" class="btn btn-primary">
                <i class="fas fa-sign-in-alt"></i> Đăng Nhập Ngay
            </a>
        </div>
        <?php else: ?>
        
        <form method="POST">
            <div class="form-group">
                <label>Họ và Tên *</label>
                <input type="text" name="full_name" class="form-control" placeholder="Nguyễn Văn A" required>
            </div>
            
            <div class="form-group">
                <label>Tên Đăng Nhập *</label>
                <input type="text" name="username" class="form-control" placeholder="nguyenvana" required>
            </div>
            
            <div class="form-group">
                <label>Email *</label>
                <input type="email" name="email" class="form-control" placeholder="nguyenvana@email.com" required>
            </div>
            
            <div class="form-group">
                <label>Mật Khẩu *</label>
                <input type="password" name="password" class="form-control" placeholder="Ít nhất 6 ký tự" required>
            </div>
            
            <div class="form-group">
                <label>Xác Nhận Mật Khẩu *</label>
                <input type="password" name="confirm_password" class="form-control" placeholder="Nhập lại mật khẩu" required>
            </div>
            
            <button type="submit" class="btn btn-primary btn-lg" style="width: 100%;">
                <i class="fas fa-user-plus"></i> Đăng Ký
            </button>
        </form>
        
        <?php endif; ?>
        
        <div class="auth-footer">
            <p>Đã có tài khoản? <a href="login.php">Đăng Nhập</a></p>
        </div>
    </div>
</section>

<?php require_once __DIR__ . '/../templates/footer.php'; ?>
