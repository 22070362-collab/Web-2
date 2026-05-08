<?php
require_once __DIR__ . '/../templates/header.php';
$pageTitle = 'Về Chúng Tôi';
?>

<!-- Page Header -->
<section class="page-header">
    <div class="container">
        <div class="section-header" style="margin-bottom: 0;">
            <div class="section-header-left">
                <div class="section-icon">
                    <i class="fas fa-info-circle"></i>
                </div>
                <div>
                    <h1 style="font-size: 2rem; margin-bottom: 4px;">Về MÂY MƠ BOOK</h1>
                    <p style="margin: 0; color: var(--text-muted);">Câu chuyện của chúng tôi</p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- About Content -->
<section class="section">
    <div class="container">
        <div class="about-grid">
            <div class="about-content animate-on-scroll">
                <h2>Chào mừng đến với MÂY MƠ BOOK</h2>
                <p>MÂY MƠ BOOK là nền tảng thuê sách trực tuyến hàng đầu Việt Nam, được thành lập với sứ mệnh mang đến cho người yêu sách cơ hội tiếp cận với kho tàng tri thức khổng lồ một cách dễ dàng và tiết kiệm.</p>
                
                <p>Với hơn <strong>15,000+ đầu sách</strong> thuộc nhiều thể loại đa dạng, từ tiểu thuyết, sách kỹ năng, đến các tác phẩm văn học kinh điển, chúng tôi cam kết mang đến trải nghiệm đọc sách tốt nhất cho bạn.</p>

                <h3>Tại sao chọn MÂY MƠ BOOK?</h3>
                <ul class="about-features">
                    <li>
                        <i class="fas fa-check-circle"></i>
                        <div>
                            <strong>Kho sách đa dạng</strong>
                            <p>Hơn 15,000 đầu sách thuộc nhiều thể loại khác nhau</p>
                        </div>
                    </li>
                    <li>
                        <i class="fas fa-check-circle"></i>
                        <div>
                            <strong>Chi phí hợp lý</strong>
                            <p>Chỉ trả tiền cho những ngày bạn thực sự đọc sách</p>
                        </div>
                    </li>
                    <li>
                        <i class="fas fa-check-circle"></i>
                        <div>
                            <strong>Giao hàng nhanh chóng</strong>
                            <p>Miễn phí giao hàng cho đơn từ 3 cuốn trở lên</p>
                        </div>
                    </li>
                    <li>
                        <i class="fas fa-check-circle"></i>
                        <div>
                            <strong>Đổi trả dễ dàng</strong>
                            <p>Chính sách đổi trả trong 7 ngày không phí</p>
                        </div>
                    </li>
                </ul>
            </div>
            
            <div class="about-image animate-on-scroll animate-delay-2">
                <img src="https://images.unsplash.com/photo-1507003211169-0a1dd7228f2d?w=600&q=80" alt="About Us">
            </div>
        </div>
    </div>
</section>

<!-- Stats Section -->
<section class="section section-alt">
    <div class="container">
        <div class="stats-grid">
            <div class="stat-item animate-on-scroll animate-delay-1">
                <div class="stat-number">15K+</div>
                <div class="stat-label">Đầu sách</div>
            </div>
            <div class="stat-item animate-on-scroll animate-delay-2">
                <div class="stat-number">50K+</div>
                <div class="stat-label">Khách hàng</div>
            </div>
            <div class="stat-item animate-on-scroll animate-delay-3">
                <div class="stat-number">100K+</div>
                <div class="stat-label">Đơn hàng</div>
            </div>
            <div class="stat-item animate-on-scroll animate-delay-4">
                <div class="stat-number">4.9/5</div>
                <div class="stat-label">Đánh giá</div>
            </div>
        </div>
    </div>
</section>

<!-- Team Section -->
<section class="section">
    <div class="container">
        <div class="section-header" style="justify-content: center; text-align: center; flex-direction: column; align-items: center;">
            <div class="section-icon">
                <i class="fas fa-users"></i>
            </div>
            <div style="text-align: center; margin-top: 16px;">
                <h2 class="section-title">Đội Ngũ Của Chúng Tôi</h2>
                <p class="section-subtitle">Những người đam mê sách</p>
            </div>
        </div>
        
        <div class="team-grid">
            <div class="team-card animate-on-scroll animate-delay-1">
                <div class="team-avatar">
                    <i class="fas fa-user"></i>
                </div>
                <h3>Nguyễn Văn A</h3>
                <p class="team-role">Founder & CEO</p>
                <p class="team-desc">Đam mê sách từ nhỏ, mong muốn lan tỏa văn hóa đọc đến mọi người.</p>
            </div>
            <div class="team-card animate-on-scroll animate-delay-2">
                <div class="team-avatar">
                    <i class="fas fa-user"></i>
                </div>
                <h3>Trần Thị B</h3>
                <p class="team-role">Head of Operations</p>
                <p class="team-desc">Đảm bảo mọi đơn hàng được xử lý nhanh chóng và chính xác.</p>
            </div>
            <div class="team-card animate-on-scroll animate-delay-3">
                <div class="team-avatar">
                    <i class="fas fa-user"></i>
                </div>
                <h3>Lê Văn C</h3>
                <p class="team-role">Head of Technology</p>
                <p class="team-desc">Xây dựng nền tảng công nghệ hiện đại, phục vụ khách hàng tốt nhất.</p>
            </div>
        </div>
    </div>
</section>

<style>
.about-grid {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 60px;
    align-items: center;
}

.about-content h2 {
    font-size: 2rem;
    margin-bottom: 20px;
}

.about-content p {
    color: var(--text-secondary);
    line-height: 1.8;
    margin-bottom: 20px;
}

.about-content h3 {
    margin: 32px 0 20px;
    font-size: 1.25rem;
}

.about-features {
    list-style: none;
}

.about-features li {
    display: flex;
    gap: 16px;
    margin-bottom: 20px;
}

.about-features li i {
    color: var(--success);
    font-size: 1.25rem;
    margin-top: 4px;
}

.about-features li strong {
    display: block;
    color: var(--text-primary);
    margin-bottom: 4px;
}

.about-features li p {
    margin: 0;
    font-size: 0.9rem;
}

.about-image img {
    width: 100%;
    border-radius: var(--radius-xl);
    box-shadow: var(--shadow-lg);
}

.stats-grid {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: 32px;
}

.stat-item {
    text-align: center;
    padding: 32px;
    background: white;
    border-radius: var(--radius-lg);
    border: 1px solid var(--border-color);
}

.stat-number {
    font-size: 2.5rem;
    font-weight: 800;
    color: var(--blue-primary);
    margin-bottom: 8px;
}

.stat-label {
    color: var(--text-muted);
    font-size: 0.95rem;
}

.team-grid {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 32px;
    margin-top: 40px;
}

.team-card {
    background: white;
    padding: 40px 32px;
    border-radius: var(--radius-xl);
    text-align: center;
    border: 1px solid var(--border-color);
    transition: var(--transition);
}

.team-card:hover {
    transform: translateY(-8px);
    box-shadow: var(--shadow-lg);
}

.team-avatar {
    width: 100px;
    height: 100px;
    background: var(--blue-bg);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 0 auto 20px;
    font-size: 2.5rem;
    color: var(--blue-primary);
}

.team-card h3 {
    margin-bottom: 4px;
}

.team-role {
    color: var(--blue-primary);
    font-weight: 600;
    margin-bottom: 12px;
}

.team-desc {
    color: var(--text-secondary);
    font-size: 0.9rem;
}

@media (max-width: 1024px) {
    .about-grid {
        grid-template-columns: 1fr;
        gap: 40px;
    }
    
    .stats-grid {
        grid-template-columns: repeat(2, 1fr);
    }
    
    .team-grid {
        grid-template-columns: repeat(2, 1fr);
    }
}

@media (max-width: 768px) {
    .stats-grid {
        grid-template-columns: 1fr;
    }
    
    .team-grid {
        grid-template-columns: 1fr;
    }
}
</style>

<?php require_once __DIR__ . '/../templates/footer.php'; ?>
