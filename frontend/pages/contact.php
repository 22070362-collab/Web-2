<?php
require_once __DIR__ . '/../templates/header.php';
$pageTitle = 'Liên Hệ';

$messageSent = false;
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $subject = trim($_POST['subject'] ?? '');
    $message = trim($_POST['message'] ?? '');
    
    if ($name && $email && $subject && $message) {
        $messageSent = true;
    }
}
?>

<style>
@import url('https://fonts.googleapis.com/css2?family=Nunito:wght@300;400;500;600;700;800&display=swap');
@import url('https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,400;0,500;0,600;0,700;1,400&display=swap');

/* Contact Page Styles - Match Vintage Theme */
.contact-page {
    --contact-primary: #8b5a2b;
    --contact-primary-light: #d89a45;
    --contact-primary-dark: #5a3518;
    --contact-cream: #fff8ed;
    --contact-ink: #2c241a;
    --contact-muted: #7a6b5c;
    --contact-border: rgba(139, 90, 43, 0.12);
    --contact-shadow: 0 20px 40px rgba(55, 34, 18, 0.08);
    font-family: 'Nunito', sans-serif;
}

.contact-grid {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 60px;
}

.contact-form-wrapper {
    background: white;
    border-radius: 28px;
    padding: 32px;
    border: 1px solid var(--contact-border);
    box-shadow: var(--contact-shadow);
}

.contact-form-wrapper h2,
.contact-info h2 {
    font-family: 'Playfair Display', Georgia, serif;
    font-size: 1.75rem;
    font-weight: 700;
    color: var(--contact-ink);
    margin-bottom: 12px;
}

.form-row {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 20px;
}

.form-group {
    margin-bottom: 20px;
}

.form-group label {
    display: block;
    font-weight: 700;
    font-size: 0.85rem;
    color: var(--contact-ink);
    margin-bottom: 8px;
}

.form-control {
    width: 100%;
    padding: 12px 16px;
    border: 1px solid var(--contact-border);
    border-radius: 16px;
    font-size: 0.9rem;
    font-family: 'Nunito', sans-serif;
    background: white;
    color: var(--contact-ink);
    transition: all 0.2s ease;
}

.form-control:focus {
    outline: none;
    border-color: var(--contact-primary);
    box-shadow: 0 0 0 3px rgba(139, 90, 43, 0.1);
}

textarea.form-control {
    resize: vertical;
    min-height: 120px;
}

.btn-primary {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 10px;
    padding: 12px 28px;
    background: linear-gradient(135deg, var(--contact-primary), var(--contact-primary-dark));
    border: none;
    border-radius: 40px;
    color: white;
    font-weight: 700;
    font-size: 0.9rem;
    font-family: 'Nunito', sans-serif;
    cursor: pointer;
    transition: all 0.25s ease;
    width: 100%;
}

.btn-primary:hover {
    background: linear-gradient(135deg, var(--contact-primary-light), var(--contact-primary));
    transform: translateY(-2px);
    box-shadow: 0 8px 20px rgba(139, 90, 43, 0.25);
}

.btn-primary i {
    font-size: 0.9rem;
}

/* Contact Info */
.contact-info {
    background: white;
    border-radius: 28px;
    padding: 32px;
    border: 1px solid var(--contact-border);
    box-shadow: var(--contact-shadow);
}

.contact-methods {
    display: flex;
    flex-direction: column;
    gap: 28px;
    margin-bottom: 36px;
}

.contact-method {
    display: flex;
    gap: 18px;
    align-items: flex-start;
}

.contact-method-icon {
    width: 52px;
    height: 52px;
    background: rgba(139, 90, 43, 0.1);
    border-radius: 18px;
    display: flex;
    align-items: center;
    justify-content: center;
    color: var(--contact-primary);
    font-size: 1.3rem;
    flex-shrink: 0;
    transition: all 0.2s ease;
}

.contact-method:hover .contact-method-icon {
    background: var(--contact-primary);
    color: white;
    transform: translateY(-3px);
}

.contact-method-content h4 {
    font-size: 1rem;
    font-weight: 700;
    color: var(--contact-ink);
    margin-bottom: 6px;
    font-family: 'Nunito', sans-serif;
}

.contact-method-content p {
    color: var(--contact-muted);
    font-size: 0.85rem;
    line-height: 1.5;
    margin: 0;
    font-family: 'Nunito', sans-serif;
}

/* Social Links */
.contact-social h4 {
    font-size: 1rem;
    font-weight: 700;
    color: var(--contact-ink);
    margin-bottom: 16px;
    font-family: 'Nunito', sans-serif;
}

.social-links {
    display: flex;
    gap: 12px;
    flex-wrap: wrap;
}

.social-link {
    width: 44px;
    height: 44px;
    background: rgba(139, 90, 43, 0.1);
    border-radius: 14px;
    display: flex;
    align-items: center;
    justify-content: center;
    color: var(--contact-primary);
    font-size: 1.1rem;
    transition: all 0.2s ease;
}

.social-link:hover {
    background: var(--contact-primary);
    color: white;
    transform: translateY(-4px);
}

/* Map Section */
.map-placeholder {
    background: white;
    border-radius: 28px;
    border: 1px solid var(--contact-border);
    overflow: hidden;
    text-align: center;
    padding: 60px 40px;
}

.map-placeholder i {
    font-size: 3.5rem;
    color: var(--contact-primary);
    margin-bottom: 20px;
    opacity: 0.7;
}

.map-placeholder h3 {
    font-family: 'Playfair Display', Georgia, serif;
    font-size: 1.3rem;
    color: var(--contact-ink);
    margin-bottom: 8px;
}

.map-placeholder p {
    color: var(--contact-muted);
    font-family: 'Nunito', sans-serif;
}

.map-placeholder a {
    font-family: 'Nunito', sans-serif;
}

/* Alert */
.alert-success {
    background: rgba(46, 125, 50, 0.08);
    border: 1px solid rgba(46, 125, 50, 0.2);
    border-radius: 20px;
    padding: 18px 24px;
    display: flex;
    align-items: center;
    gap: 16px;
    color: #2e7d32;
}

.alert-success i {
    font-size: 1.5rem;
}

.alert-success strong {
    font-weight: 700;
    font-family: 'Nunito', sans-serif;
}

/* Responsive */
@media (max-width: 1024px) {
    .contact-grid {
        grid-template-columns: 1fr;
        gap: 32px;
    }
}

@media (max-width: 768px) {
    .contact-form-wrapper,
    .contact-info {
        padding: 24px;
    }
    
    .form-row {
        grid-template-columns: 1fr;
        gap: 0;
    }
    
    .contact-method {
        gap: 14px;
    }
    
    .contact-method-icon {
        width: 44px;
        height: 44px;
        font-size: 1.1rem;
    }
    
    .map-placeholder {
        padding: 40px 20px;
    }
}
</style>

<main class="contact-page">
    <!-- Page Header -->
    <section class="page-header" style="background: linear-gradient(135deg, #fffaf3 0%, #fef5e8 100%); padding: 48px 0 32px;">
        <div class="container">
            <div class="section-header" style="margin-bottom: 0;">
                <div class="section-header-left">
                    <div class="section-icon" style="background: rgba(139, 90, 43, 0.1); border-radius: 20px; width: 64px; height: 64px; display: flex; align-items: center; justify-content: center;">
                        <i class="fas fa-envelope" style="font-size: 1.8rem; color: #8b5a2b;"></i>
                    </div>
                    <div>
                        <h1 style="font-family: 'Playfair Display', Georgia, serif; font-size: 2.2rem; margin-bottom: 8px; color: #2c241a;">Liên Hệ</h1>
                        <p style="margin: 0; color: #7a6b5c; font-family: 'Nunito', sans-serif;">Chúng tôi luôn sẵn sàng lắng nghe bạn</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Contact Content -->
    <section class="section" style="padding: 60px 0;">
        <div class="container">
            <?php if ($messageSent): ?>
            <div class="alert-success" style="max-width: 600px; margin: 0 auto 40px;">
                <i class="fas fa-check-circle"></i>
                <div>
                    <strong>Cảm ơn bạn đã liên hệ!</strong>
                    <p style="margin: 6px 0 0; font-size: 0.85rem;">Chúng tôi sẽ phản hồi trong vòng 24 giờ.</p>
                </div>
            </div>
            <?php endif; ?>
            
            <div class="contact-grid">
                <!-- Contact Form -->
                <div class="contact-form-wrapper animate-on-scroll">
                    <h2>Gửi Tin Nhắn</h2>
                    <p style="color: #7a6b5c; margin-bottom: 28px; font-family: 'Nunito', sans-serif;">Điền thông tin bên dưới và chúng tôi sẽ liên hệ lại với bạn sớm nhất có thể.</p>
                    
                    <form method="POST" class="contact-form">
                        <div class="form-row">
                            <div class="form-group">
                                <label>Họ và Tên *</label>
                                <input type="text" name="name" class="form-control" placeholder="Nhập họ và tên của bạn" required>
                            </div>
                            <div class="form-group">
                                <label>Email *</label>
                                <input type="email" name="email" class="form-control" placeholder="email@example.com" required>
                            </div>
                        </div>
                        
                        <div class="form-group">
                            <label>Chủ đề *</label>
                            <select name="subject" class="form-control" required>
                                <option value="">Chọn chủ đề</option>
                                <option value="order">Hỗ trợ đơn hàng</option>
                                <option value="return">Đổi trả sách</option>
                                <option value="membership">Gói hội viên</option>
                                <option value="partnership">Hợp tác kinh doanh</option>
                                <option value="feedback">Góp ý dịch vụ</option>
                                <option value="other">Khác</option>
                            </select>
                        </div>
                        
                        <div class="form-group">
                            <label>Nội dung *</label>
                            <textarea name="message" class="form-control" rows="5" placeholder="Mô tả chi tiết vấn đề của bạn..." required></textarea>
                        </div>
                        
                        <button type="submit" class="btn-primary">
                            <i class="fas fa-paper-plane"></i> Gửi Tin Nhắn
                        </button>
                    </form>
                </div>
                
                <!-- Contact Info -->
                <div class="contact-info animate-on-scroll">
                    <h2>Thông Tin Liên Hệ</h2>
                    <p style="color: #7a6b5c; margin-bottom: 32px; font-family: 'Nunito', sans-serif;">Bạn cũng có thể liên hệ trực tiếp với chúng tôi qua các kênh sau:</p>
                    
                    <div class="contact-methods">
                        <div class="contact-method">
                            <div class="contact-method-icon">
                                <i class="fas fa-map-marker-alt"></i>
                            </div>
                            <div class="contact-method-content">
                                <h4>Địa Chỉ</h4>
                                <p>123 Đường ABC, Quận 1<br>TP. Hồ Chí Minh, Việt Nam</p>
                            </div>
                        </div>
                        
                        <div class="contact-method">
                            <div class="contact-method-icon">
                                <i class="fas fa-phone-alt"></i>
                            </div>
                            <div class="contact-method-content">
                                <h4>Điện Thoại</h4>
                                <p>Hotline: 1900 1234<br>Zalo: 0901 234 567</p>
                            </div>
                        </div>
                        
                        <div class="contact-method">
                            <div class="contact-method-icon">
                                <i class="fas fa-envelope"></i>
                            </div>
                            <div class="contact-method-content">
                                <h4>Email</h4>
                                <p>support@maymobook.com<br>business@maymobook.com</p>
                            </div>
                        </div>
                        
                        <div class="contact-method">
                            <div class="contact-method-icon">
                                <i class="fas fa-clock"></i>
                            </div>
                            <div class="contact-method-content">
                                <h4>Giờ Làm Việc</h4>
                                <p>Thứ 2 - Thứ 6: 8:00 - 18:00<br>Thứ 7: 9:00 - 15:00</p>
                            </div>
                        </div>
                    </div>
                    
                    <div class="contact-social">
                        <h4>Kết Nối Với Chúng Tôi</h4>
                        <div class="social-links">
                            <a href="#" class="social-link"><i class="fab fa-facebook-f"></i></a>
                            <a href="#" class="social-link"><i class="fab fa-instagram"></i></a>
                            <a href="#" class="social-link"><i class="fab fa-youtube"></i></a>
                            <a href="#" class="social-link"><i class="fab fa-tiktok"></i></a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Map Section -->
    <section class="section" style="background: rgba(139, 90, 43, 0.03); padding: 60px 0;">
        <div class="container">
            <div class="map-placeholder">
                <i class="fas fa-map-marked-alt"></i>
                <h3>Bản Đồ</h3>
                <p>TP. Hồ Chí Minh, Việt Nam</p>
                <div style="margin-top: 24px;">
                    <a href="https://maps.google.com" target="_blank" style="color: #8b5a2b; text-decoration: none; font-weight: 600; font-family: 'Nunito', sans-serif;">
                        Xem trên Google Maps <i class="fas fa-arrow-right"></i>
                    </a>
                </div>
            </div>
        </div>
    </section>
</main>

<?php require_once __DIR__ . '/../templates/footer.php'; ?>