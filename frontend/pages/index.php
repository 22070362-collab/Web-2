<?php
require_once __DIR__ . '/../templates/header.php';
require_once __DIR__ . '/../../backend/controllers/BookController.php';

$bookController = new BookController();
$featuredBooks = $bookController->available(12);
$categories = $bookController->categories();

function getRandomRating() {
    return mt_rand(38, 50) / 10;
}

function getRandomReviews() {
    return mt_rand(50, 500);
}
?>

<?php if ($isAdmin): ?>
<div class="container admin-banner">
    <div class="admin-banner-inner">
        <div style="display: flex; align-items: center; gap: 20px;">
            <div style="width: 50px; height: 50px; background: rgba(255,255,255,0.1); border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 1.5rem;">
                <i class="fas fa-user-shield"></i>
            </div>
            <div>
                <h3 style="margin: 0; font-size: 1.1rem;">Chế độ Quản trị viên</h3>
                <p style="margin: 0; font-size: 0.9rem; opacity: 0.8;">Bạn đang đăng nhập với quyền Admin. Truy cập bảng điều khiển để quản lý hệ thống.</p>
            </div>
        </div>
        <a href="admin/index.php" class="btn btn-primary">
            <i class="fas fa-tachometer-alt"></i> Vào Trang Quản Trị
        </a>
    </div>
</div>
<?php endif; ?>

<main>

<!-- Hero Section -->
<section class="hero">
    <div class="hero-background"></div>
    <div class="hero-overlay"></div>
    <img src="https://images.unsplash.com/photo-1507842217343-583bb7270b66?w=1200&q=80" alt="Books" class="hero-image">
    <div class="container">
        <div class="hero-content-wrapper">
            <div class="hero-content">
                <div class="hero-badge">
                    <i class="fas fa-sparkles"></i> Nền tảng thuê sách hàng đầu
                </div>
                <h1>Đọc sách <span>hay</span>, sống trọn <span>đam mê</span></h1>
                <p>Khám phá kho tàng sách phong phú với hàng ngàn đầu sách thuộc nhiều thể loại. Thuê sách online dễ dàng, linh hoạt theo ngày.</p>
                
                <div class="hero-buttons">
                    <a href="books.php" class="btn btn-primary btn-lg">
                        <i class="fas fa-book-open"></i> Khám Phá Ngay
                    </a>
                    <a href="books.php" class="btn btn-outline btn-lg">
                        <i class="fas fa-search"></i> Tìm Kiếm
                    </a>
                </div>

                <div class="hero-stats">
                    <div class="hero-stat">
                        <span class="hero-stat-number">15K+</span>
                        <span class="hero-stat-label">Đầu sách</span>
                    </div>
                    <div class="hero-stat">
                        <span class="hero-stat-number">50K+</span>
                        <span class="hero-stat-label">Độc giả</span>
                    </div>
                    <div class="hero-stat">
                        <span class="hero-stat-number">4.9</span>
                        <span class="hero-stat-label">Đánh giá</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Trust Badges -->
<section class="trust-badges-section">
    <div class="container">
        <div class="trust-badges">
            <div class="trust-badge">
                <div class="trust-badge-icon">
                    <i class="fas fa-truck"></i>
                </div>
                <div class="trust-badge-text">
                    <h4>Miễn phí giao hàng</h4>
                    <p>Cho đơn từ 3 cuốn</p>
                </div>
            </div>
            <div class="trust-badge">
                <div class="trust-badge-icon">
                    <i class="fas fa-shield-alt"></i>
                </div>
                <div class="trust-badge-text">
                    <h4>Bảo đảm chất lượng</h4>
                    <p>Sách mới 98%</p>
                </div>
            </div>
            <div class="trust-badge">
                <div class="trust-badge-icon">
                    <i class="fas fa-undo"></i>
                </div>
                <div class="trust-badge-text">
                    <h4>Đổi trả dễ dàng</h4>
                    <p>Trong 7 ngày</p>
                </div>
            </div>
            <div class="trust-badge">
                <div class="trust-badge-icon">
                    <i class="fas fa-headset"></i>
                </div>
                <div class="trust-badge-text">
                    <h4>Hỗ trợ 24/7</h4>
                    <p>Luôn sẵn sàng</p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Promo Banner -->
<section class="section">
    <div class="container">
        <div class="promo-banner animate-on-scroll">
            <h2>Lễ Hội Đọc Sách Mùa Hè</h2>
            <p>Giảm đến 30% cho tất cả sách Self-help. Chỉ áp dụng đến hết tháng này!</p>
            <a href="books.php?category=Self-help" class="btn btn-lg btn-outline">
                <i class="fas fa-fire"></i> Khám Phá Ngay
            </a>
        </div>
    </div>
</section>

<!-- Categories -->
<section class="section">
    <div class="container">
        <div class="section-header">
            <div class="section-header-left">
                <div class="section-icon">
                    <i class="fas fa-layer-group"></i>
                </div>
                <div>
                    <h2 class="section-title">Danh Mục Sách</h2>
                    <p class="section-subtitle">Khám phá sách theo sở thích của bạn</p>
                </div>
            </div>
            <a href="books.php" class="btn btn-outline btn-sm">Xem Tất Cả <i class="fas fa-arrow-right"></i></a>
        </div>
        
        <div class="categories-grid">
            <a href="books.php?category=Tiểu%20thuyết" class="category-card animate-on-scroll animate-delay-1">
                <div class="category-card-bg"></div>
                <div class="category-card-content">
                    <div class="category-icon">
                        <i class="fas fa-heart"></i>
                    </div>
                    <h4>Tiểu Thuyết</h4>
                    <p>Ngôn tình & Đời thường</p>
                </div>
            </a>
            
            <a href="books.php?category=Self-help" class="category-card animate-on-scroll animate-delay-2">
                <div class="category-card-bg"></div>
                <div class="category-card-content">
                    <div class="category-icon">
                        <i class="fas fa-brain"></i>
                    </div>
                    <h4>Self-Help</h4>
                    <p>Phát triển bản thân</p>
                </div>
            </a>
            
            <a href="books.php?category=Khoa%20học" class="category-card animate-on-scroll animate-delay-3">
                <div class="category-card-bg"></div>
                <div class="category-card-content">
                    <div class="category-icon">
                        <i class="fas fa-flask"></i>
                    </div>
                    <h4>Khoa Học</h4>
                    <p>Khoa học & Khám phá</p>
                </div>
            </a>
            
            <a href="books.php?category=Kỹ%20năng" class="category-card animate-on-scroll animate-delay-4">
                <div class="category-card-bg"></div>
                <div class="category-card-content">
                    <div class="category-icon">
                        <i class="fas fa-rocket"></i>
                    </div>
                    <h4>Kỹ Năng</h4>
                    <p>Kinh doanh & Nghề nghiệp</p>
                </div>
            </a>
        </div>
    </div>
</section>

<!-- Featured Books - Carousel -->
<section class="section section-alt">
    <div class="container">
        <div class="section-header">
            <div class="section-header-left">
                <div class="section-icon">
                    <i class="fas fa-fire"></i>
                </div>
                <div>
                    <h2 class="section-title">Sách Nổi Bật</h2>
                    <p class="section-subtitle">Những cuốn sách được thuê nhiều nhất tháng này</p>
                </div>
            </div>
            <a href="books.php" class="btn btn-outline btn-sm">Xem Tất Cả <i class="fas fa-arrow-right"></i></a>
        </div>
        
        <div class="books-carousel">
            <button class="carousel-nav-btn carousel-prev"><i class="fas fa-chevron-left"></i></button>
            <div class="books-carousel-track">
                <?php if (count($featuredBooks) > 0): ?>
                    <?php foreach ($featuredBooks as $book): 
                        $rating = getRandomRating();
                        $reviews = getRandomReviews();
                    ?>
                    <div class="book-card">
                        <?php if (($book['quantity'] ?? 0) > 0): ?>
                        <span class="book-discount">
                            <i class="fas fa-star"></i> Nổi bật
                        </span>
                        <?php else: ?>
                        <span class="book-discount" style="background: linear-gradient(145deg, #6b7280, #4b5563);">Hết hàng</span>
                        <?php endif; ?>
                        
                        <button class="book-wishlist" data-book-id="<?php echo $book['id']; ?>" title="Yêu thích">
                            <i class="far fa-heart"></i>
                        </button>
                        
                        <a href="book-detail.php?id=<?php echo $book['id']; ?>">
                            <div class="book-image">
                                <img src="<?php echo getBookCoverImage($book); ?>"
                                     alt="<?php echo htmlspecialchars($book['title']); ?>"
                                     loading="lazy">
                            </div>
                        </a>
                        
                        <div class="book-info">
                            <span class="book-category"><?php echo htmlspecialchars((string)($book['category'] ?? 'Sách')); ?></span>
                            <h3 class="book-title"><a href="book-detail.php?id=<?php echo $book['id']; ?>"><?php echo htmlspecialchars((string)($book['title'] ?? '')); ?></a></h3>
                            <p class="book-author">
                                <i class="fas fa-user-edit"></i> <?php echo htmlspecialchars((string)($book['author'] ?? 'Tác giả')); ?>
                            </p>
                            
                            <div class="book-rating">
                                <?php for($i = 0; $i < 5; $i++): ?>
                                    <i class="fas fa-star<?php echo $i < floor($rating) ? '' : '-half-alt'; ?>"></i>
                                <?php endfor; ?>
                                <span><?php echo number_format($rating, 1); ?> (<?php echo $reviews; ?>)</span>
                            </div>
                            
                            <div class="book-footer">
                                <div class="book-price">
                                    <span class="book-price-current"><?php echo number_format($book['price_per_day'] ?? 5000, 0); ?>đ</span>
                                    <span class="book-price-original">/ngày</span>
                                </div>
                                <?php if (($book['quantity'] ?? 0) > 0): ?>
                                <a href="book-detail.php?id=<?php echo $book['id']; ?>" class="btn btn-sm">
                                    <i class="fas fa-hand-holding-heart"></i> Thuê
                                </a>
                                <?php else: ?>
                                <span style="font-size: 0.75rem; color: var(--danger); font-weight: 600;">Hết hàng</span>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
            <button class="carousel-nav-btn carousel-next"><i class="fas fa-chevron-right"></i></button>
        </div>
    </div>
</section>

<!-- New Books -->
<section class="section">
    <div class="container">
        <div class="section-header">
            <div class="section-header-left">
                <div class="section-icon">
                    <i class="fas fa-sparkles"></i>
                </div>
                <div>
                    <h2 class="section-title">Sách Mới Nhất</h2>
                    <p class="section-subtitle">Những cuốn sách mới được thêm vào bộ sưu tập</p>
                </div>
            </div>
            <a href="books.php" class="btn btn-outline btn-sm">Xem Tất Cả <i class="fas fa-arrow-right"></i></a>
        </div>
        
        <div class="books-grid-4">
            <?php 
            $newBooks = array_slice($featuredBooks, 0, 4);
            foreach ($newBooks as $book): 
                $rating = getRandomRating();
                $reviews = getRandomReviews();
            ?>
            <div class="book-card animate-on-scroll">
                <?php if (($book['quantity'] ?? 0) > 0): ?>
                <span class="book-discount" style="background: linear-gradient(145deg, #16a34a, #15803d);">
                    <i class="fas fa-bolt"></i> Mới
                </span>
                <?php else: ?>
                <span class="book-discount" style="background: linear-gradient(145deg, #6b7280, #4b5563);">Hết hàng</span>
                <?php endif; ?>
                
                <button class="book-wishlist" data-book-id="<?php echo $book['id']; ?>" title="Yêu thích">
                    <i class="far fa-heart"></i>
                </button>
                
                <a href="book-detail.php?id=<?php echo $book['id']; ?>">
                    <div class="book-image">
                        <img src="<?php echo getBookCoverImage($book); ?>"
                             alt="<?php echo htmlspecialchars($book['title']); ?>"
                             loading="lazy">
                    </div>
                </a>
                
                <div class="book-info">
                    <span class="book-category"><?php echo htmlspecialchars($book['category'] ?? 'Sách'); ?></span>
                    <h3 class="book-title"><a href="book-detail.php?id=<?php echo $book['id']; ?>"><?php echo htmlspecialchars($book['title']); ?></a></h3>
                    <p class="book-author">
                        <i class="fas fa-user-edit"></i> <?php echo htmlspecialchars($book['author'] ?? 'Tác giả'); ?>
                    </p>
                    
                    <div class="book-rating">
                        <?php for($i = 0; $i < 5; $i++): ?>
                            <i class="fas fa-star<?php echo $i < floor($rating) ? '' : '-half-alt'; ?>"></i>
                        <?php endfor; ?>
                        <span><?php echo number_format($rating, 1); ?> (<?php echo $reviews; ?>)</span>
                    </div>
                    
                    <div class="book-footer">
                        <div class="book-price">
                            <span class="book-price-current"><?php echo number_format($book['price_per_day'] ?? 5000, 0); ?>đ</span>
                            <span class="book-price-original">/ngày</span>
                        </div>
                        <?php if (($book['quantity'] ?? 0) > 0): ?>
                        <a href="book-detail.php?id=<?php echo $book['id']; ?>" class="btn btn-sm">
                            <i class="fas fa-hand-holding-heart"></i> Thuê
                        </a>
                        <?php else: ?>
                        <span style="font-size: 0.75rem; color: var(--danger); font-weight: 600;">Hết hàng</span>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!-- Why Choose Us -->
<section class="section section-alt">
    <div class="container">
        <div class="section-header" style="justify-content: center; text-align: center; flex-direction: column; align-items: center;">
            <div class="section-icon">
                <i class="fas fa-award"></i>
            </div>
            <div style="text-align: center; margin-top: 16px;">
                <h2 class="section-title">Tại Sao Chọn MÂY MƠ BOOK</h2>
                <p class="section-subtitle">Trải nghiệm thuê sách tốt nhất dành cho người yêu sách</p>
            </div>
        </div>
        
        <div class="why-choose-grid">
            <div class="why-choose-card animate-on-scroll animate-delay-1">
                <div class="why-choose-icon">
                    <i class="fas fa-book-open"></i>
                </div>
                <h3>Kho Sách Đa Dạng</h3>
                <p>Hàng ngàn đầu sách thuộc nhiều thể loại từ tiểu thuyết đến sách học thuật.</p>
            </div>
            
            <div class="why-choose-card animate-on-scroll animate-delay-2">
                <div class="why-choose-icon">
                    <i class="fas fa-piggy-bank"></i>
                </div>
                <h3>Chi Phí Tiết Kiệm</h3>
                <p>Chỉ trả tiền cho những ngày bạn thuê. Tiết kiệm hơn so với việc mua sách.</p>
            </div>
            
            <div class="why-choose-card animate-on-scroll animate-delay-3">
                <div class="why-choose-icon">
                    <i class="fas fa-calendar-alt"></i>
                </div>
                <h3>Linh Hoạt</h3>
                <p>Dễ dàng gia hạn thời gian thuê online bất kỳ lúc nào bạn muốn.</p>
            </div>
        </div>
    </div>
</section>

<!-- CTA Section -->
<section class="section">
    <div class="container">
        <div class="cta-section animate-on-scroll">
            <div class="cta-content">
                <h2>Sẵn sàng bắt đầu đọc sách?</h2>
                <p>Đăng ký ngay hôm nay và nhận ưu đãi giảm 10% cho lần thuê đầu tiên!</p>
                <div class="cta-buttons">
                    <a href="register.php" class="btn btn-primary btn-lg">
                        <i class="fas fa-user-plus"></i> Đăng Ký Ngay
                    </a>
                    <a href="books.php" class="btn btn-outline btn-lg">
                        <i class="fas fa-book"></i> Khám Phá Sách
                    </a>
                </div>
            </div>
        </div>
    </div>
</section>

</main>

<style>
/* Trust Badges Section */
.trust-badges-section {
    background: var(--bg-secondary);
    padding: 32px 0;
    border-top: 1px solid var(--border-light);
    border-bottom: 1px solid var(--border-light);
}

.trust-badges {
    display: flex;
    justify-content: space-around;
    gap: 24px;
}

.trust-badge {
    display: flex;
    align-items: center;
    gap: 16px;
    padding: 16px 24px;
    background: white;
    border-radius: var(--radius-lg);
    transition: var(--transition);
    border: 1px solid var(--border-light);
}

.trust-badge:hover {
    border-color: var(--primary-border);
    box-shadow: var(--shadow);
    transform: translateY(-4px);
}

.trust-badge-icon {
    width: 52px;
    height: 52px;
    background: linear-gradient(145deg, var(--primary-bg), var(--bg-warm));
    border-radius: var(--radius-md);
    display: flex;
    align-items: center;
    justify-content: center;
    color: var(--primary);
    font-size: 1.3rem;
    border: 1px solid var(--primary-border);
}

.trust-badge-text h4 {
    font-size: 0.95rem;
    margin-bottom: 2px;
    color: var(--text-primary);
    font-weight: 700;
}

.trust-badge-text p {
    font-size: 0.8rem;
    color: var(--text-muted);
    margin: 0;
}

.hero-badge {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    padding: 10px 20px;
    background: white;
    border-radius: var(--radius-full);
    color: var(--primary);
    font-size: 0.85rem;
    margin-bottom: 24px;
    font-weight: 600;
    box-shadow: var(--shadow);
    border: 1px solid var(--border-light);
}

.hero-badge i {
    color: var(--accent);
}

.hero-stats {
    display: flex;
    gap: 40px;
    margin-top: 40px;
    padding-top: 28px;
    border-top: 1px solid var(--border-light);
}

.hero-stat {
    display: flex;
    flex-direction: column;
}

.hero-stat-number {
    font-size: 1.6rem;
    font-weight: 800;
    color: var(--primary);
    font-family: 'Playfair Display', serif;
}

.hero-stat-label {
    font-size: 0.85rem;
    color: var(--text-muted);
}

.why-choose-grid {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 32px;
    margin-top: 48px;
}

.why-choose-card {
    background: white;
    padding: 40px 32px;
    border-radius: var(--radius-xl);
    text-align: center;
    border: 1px solid var(--border-light);
    transition: var(--transition);
    box-shadow: var(--shadow-sm);
}

.why-choose-card:hover {
    transform: translateY(-10px);
    box-shadow: var(--shadow-lg);
    border-color: var(--primary-border);
}

.why-choose-icon {
    width: 80px;
    height: 80px;
    background: linear-gradient(145deg, var(--primary-bg), var(--bg-warm));
    border-radius: var(--radius-xl);
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 0 auto 24px;
    color: var(--primary);
    font-size: 2rem;
    border: 1px solid var(--primary-border);
}

.why-choose-card h3 {
    font-family: 'Playfair Display', serif;
    margin-bottom: 12px;
    font-size: 1.2rem;
}

.why-choose-card p {
    color: var(--text-secondary);
    font-size: 0.95rem;
    line-height: 1.6;
}

.cta-section {
    background: linear-gradient(145deg, var(--primary) 0%, var(--primary-dark) 100%);
    border-radius: var(--radius-xl);
    padding: 64px;
    text-align: center;
    position: relative;
    overflow: hidden;
    box-shadow: 0 12px 40px rgba(194, 65, 12, 0.25);
}

.cta-section::before {
    content: '';
    position: absolute;
    top: -50%;
    right: -10%;
    width: 400px;
    height: 400px;
    background: rgba(255,255,255,0.1);
    border-radius: 50%;
}

.cta-section::after {
    content: '';
    position: absolute;
    bottom: -30%;
    left: -5%;
    width: 300px;
    height: 300px;
    background: rgba(255,255,255,0.05);
    border-radius: 50%;
}

.cta-content {
    position: relative;
    z-index: 1;
}

.cta-content h2 {
    color: white;
    font-family: 'Playfair Display', serif;
    font-size: 2.2rem;
    margin-bottom: 16px;
}

.cta-content p {
    color: rgba(255,255,255,0.9);
    margin-bottom: 32px;
    font-size: 1.1rem;
}

.cta-buttons {
    display: flex;
    justify-content: center;
    gap: 16px;
}

.cta-buttons .btn-outline {
    border-color: white;
    color: white;
}

.cta-buttons .btn-outline:hover {
    background: white;
    color: var(--primary);
}

@media (max-width: 1024px) {
    .why-choose-grid {
        grid-template-columns: repeat(2, 1fr);
    }
}

@media (max-width: 768px) {
    .trust-badges {
        flex-direction: column;
        gap: 12px;
    }
    
    .hero-stats {
        gap: 24px;
    }
    
    .why-choose-grid {
        grid-template-columns: 1fr;
    }
    
    .cta-section {
        padding: 48px 24px;
    }
    
    .cta-content h2 {
        font-size: 1.75rem;
    }
    
    .cta-buttons {
        flex-direction: column;
    }
}
</style>

<?php require_once __DIR__ . '/../templates/footer.php'; ?>
