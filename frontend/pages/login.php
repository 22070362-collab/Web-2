<?php
require_once __DIR__ . '/../templates/header.php';

if ($isLoggedIn) {
    if ($isAdmin) {
        header('Location: admin/index.php');
    } else {
        header('Location: dashboard.php');
    }
    exit;
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';
    $remember = isset($_POST['remember']);
    
    if (empty($username) || empty($password)) {
        $error = 'Vui lòng điền đầy đủ thông tin.';
    } else {
        require_once __DIR__ . '/../../backend/models/User.php';
        $userModel = new User();
        $user = $userModel->findByUsername($username);
        
        if ($user && $userModel->verifyPassword($user, $password)) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['role'] = $user['role'];
            $_SESSION['full_name'] = $user['full_name'];
            
            // Remember Me functionality
            if ($remember) {
                $token = bin2hex(random_bytes(32));
                $userModel->setRememberToken($user['id'], $token);
                setcookie('remember_token', $token, time() + (86400 * 30), '/', '', false, true);
            }
            
            header('Location: ' . ($user['role'] === 'admin' ? 'admin/index.php' : 'dashboard.php'));
            exit;
        } else {
            $error = 'Tên đăng nhập hoặc mật khẩu không đúng.';
        }
    }
}
?>

<section class="auth-page">
    <div class="auth-card">
        <div class="auth-icon">
            <i class="fas fa-sign-in-alt"></i>
        </div>
        
        <div class="auth-header">
            <h2>Chào Mừng Trở Lại</h2>
            <p>Đăng nhập vào tài khoản của bạn</p>
        </div>
        
        <?php if ($error): ?>
        <div class="alert alert-danger">
            <i class="fas fa-exclamation-circle"></i>
            <?php echo htmlspecialchars($error); ?>
        </div>
        <?php endif; ?>
        
        <form method="POST">
            <div class="form-group">
                <label>Tên Đăng Nhập</label>
                <input type="text" name="username" class="form-control" placeholder="Nhập tên đăng nhập" required autofocus>
            </div>
            
            <div class="form-group">
                <label>Mật Khẩu</label>
                <input type="password" name="password" class="form-control" placeholder="Nhập mật khẩu" required>
            </div>
            
            <div class="form-group-remember">
                <label class="checkbox-wrapper">
                    <input type="checkbox" name="remember" id="remember">
                    <span class="checkmark"></span>
                    <span class="remember-text">Ghi nhớ đăng nhập</span>
                </label>
            </div>
            
            <button type="submit" class="btn btn-primary btn-lg" style="width: 100%;">
                <i class="fas fa-sign-in-alt"></i> Đăng Nhập
            </button>
        </form>
        
        <div class="auth-footer">
            <p>Chưa có tài khoản? <a href="register.php">Đăng Ký Ngay</a></p>
        </div>
        
        <div style="margin-top: 24px; padding-top: 20px; border-top: 1px solid var(--border-color); text-align: center; font-size: 0.85rem; color: var(--text-muted);">
            <p style="margin-bottom: 8px;">Tài Khoản Demo:</p>
            <p style="margin: 0;">Admin: admin / admin123<br>Customer: hung / password123</p>
        </div>
    </div>
</section>

<style>
.form-group-remember {
    margin-bottom: 20px;
}

.checkbox-wrapper {
    display: flex;
    align-items: center;
    cursor: pointer;
    user-select: none;
    gap: 10px;
}

.checkbox-wrapper input {
    position: absolute;
    opacity: 0;
    cursor: pointer;
    height: 0;
    width: 0;
}

.checkmark {
    height: 20px;
    width: 20px;
    background-color: var(--bg-secondary);
    border: 2px solid var(--border-color);
    border-radius: 4px;
    display: flex;
    align-items: center;
    justify-content: center;
    transition: all 0.2s ease;
}

.checkbox-wrapper:hover input ~ .checkmark {
    border-color: var(--primary);
}

.checkbox-wrapper input:checked ~ .checkmark {
    background-color: var(--primary);
    border-color: var(--primary);
}

.checkmark:after {
    content: "";
    display: none;
}

.checkbox-wrapper input:checked ~ .checkmark:after {
    display: block;
}

.checkbox-wrapper .checkmark:after {
    width: 5px;
    height: 10px;
    border: solid white;
    border-width: 0 2px 2px 0;
    transform: rotate(45deg);
    margin-bottom: 2px;
}

.remember-text {
    color: var(--text-secondary);
    font-size: 0.9rem;
}
</style>

<?php require_once __DIR__ . '/../templates/footer.php'; ?>
