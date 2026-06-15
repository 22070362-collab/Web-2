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

<main>

<!-- Hero Section -->
<section class="hero">
    <div class="hero-background"></div>
    <div class="hero-overlay"></div>
    <img src="https://i.pinimg.com/1200x/ed/63/fc/ed63fce281111f021d46e5ba14551e8f.jpg" alt="Books" class="hero-image">
    <div class="container">
        <div class="hero-content-wrapper">
            <div class="hero-content">
                <div class="hero-badge">
                    <i class="fas fa-sparkles"></i> Leading Book Rental Platform in Vietnam
                </div>
                <h1>Read great <span> books</span>, live on <span> passion</span></h1>
                <p>Discover a vast treasure trove of thousands of titles across diverse genres. Rent books online easily, with flexible daily options.</p>
                
                <div class="hero-buttons">
                    <a href="books.php" class="btn btn-primary btn-lg">
                        <i class="fas fa-book-open"></i> Explore Now
                    </a>
                    <a href="books.php" class="btn btn-outline btn-lg">
                        <i class="fas fa-search"></i> Search
                    </a>
                </div>

                <div class="hero-stats">
                    <div class="hero-stat">
                        <span class="hero-stat-number">15K+</span>
                        <span class="hero-stat-label">Titles</span>
                    </div>
                    <div class="hero-stat">
                        <span class="hero-stat-number">50K+</span>
                        <span class="hero-stat-label">Readers</span>
                    </div>
                    <div class="hero-stat">
                        <span class="hero-stat-number">4.9</span>
                        <span class="hero-stat-label">Rating</span>
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
                    <h4>Free Shipping</h4>
                    <p>For orders from 3 books</p>
                </div>
            </div>
            <div class="trust-badge">
                <div class="trust-badge-icon">
                    <i class="fas fa-shield-alt"></i>
                </div>
                <div class="trust-badge-text">
                    <h4>Quality Assurance</h4>
                    <p>98% new books</p>
                </div>
            </div>
            <div class="trust-badge">
                <div class="trust-badge-icon">
                    <i class="fas fa-undo"></i>
                </div>
                <div class="trust-badge-text">
                    <h4>Easy Returns</h4>
                    <p>Within 7 days</p>
                </div>
            </div>
            <div class="trust-badge">
                <div class="trust-badge-icon">
                    <i class="fas fa-headset"></i>
                </div>
                <div class="trust-badge-text">
                    <h4>24/7 Support</h4>
                    <p>Always ready to help</p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Promo Banner -->
<section class="section">
    <div class="container">
        <div class="promo-banner animate-on-scroll">
            <h2>Summer Reading Festival</h2>
            <p>Get up to 30% off all Self-help books. Limited time offer!</p>
            <a href="books.php?category=Self-help" class="btn btn-lg" style="background: white; color: var(--primary-dark); border: 1px solid rgba(0,0,0,0.08);">
                <i class="fas fa-fire"></i> Explore Now
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
                    <h2 class="section-title">Book Categories</h2>
                    <p class="section-subtitle">Discover books by your favorite genres</p>
                </div>
            </div>
            <a href="books.php" class="btn btn-outline btn-sm">View All <i class="fas fa-arrow-right"></i></a>
        </div>
        
        <div class="categories-grid">
            <a href="books.php?category=Tiểu%20thuyết" class="category-card animate-on-scroll animate-delay-1">
                <div class="category-card-bg"></div>
                <div class="category-card-content">
                    <div class="category-icon">
                        <i class="fas fa-heart"></i>
                    </div>
                    <h4>Novel</h4>
                    <p>Romance & Everyday Life

</p>
                </div>
            </a>
            
            <a href="books.php?category=Self-help" class="category-card animate-on-scroll animate-delay-2">
                <div class="category-card-bg"></div>
                <div class="category-card-content">
                    <div class="category-icon">
                        <i class="fas fa-brain"></i>
                    </div>
                    <h4>Self-Help</h4>
                    <p>Personal Development</p>
                </div>
            </a>
            
            <a href="books.php?category=Khoa%20học" class="category-card animate-on-scroll animate-delay-3">
                <div class="category-card-bg"></div>
                <div class="category-card-content">
                    <div class="category-icon">
                        <i class="fas fa-flask"></i>
                    </div>
                    <h4>Science</h4>
                    <p>Science & Discovery</p>
                </div>
            </a>
            
            <a href="books.php?category=Kỹ%20năng" class="category-card animate-on-scroll animate-delay-4">
                <div class="category-card-bg"></div>
                <div class="category-card-content">
                    <div class="category-icon">
                        <i class="fas fa-rocket"></i>
                    </div>
                    <h4>Skills</h4>
                    <p>Business & Career</p>
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
                    <h2 class="section-title">Featured Books</h2>
                    <p class="section-subtitle">The most borrowed books this month</p>
                </div>
            </div>
            <a href="books.php" class="btn btn-outline btn-sm">View All <i class="fas fa-arrow-right"></i></a>
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
                            <i class="fas fa-star"></i> Featured
                        </span>
                        <?php else: ?>
                        <span class="book-discount" style="background: linear-gradient(145deg, #6b7280, #4b5563);">Out of Stock</span>
                        <?php endif; ?>
                        
                        <button class="book-wishlist" data-book-id="<?php echo $book['id']; ?>" title="Add to Wishlist">
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
                                    <i class="fas fa-hand-holding-heart"></i> Rent
                                </a>
                                <?php else: ?>
                                <span style="font-size: 0.75rem; color: var(--danger); font-weight: 600;">Out of Stock</span>
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
                    <h2 class="section-title">New Books</h2>
                    <p class="section-subtitle">The latest books added to our collection</p>
                </div>
            </div>
            <a href="books.php" class="btn btn-outline btn-sm">View All <i class="fas fa-arrow-right"></i></a>
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
                    <i class="fas fa-bolt"></i> New
                </span>
                <?php else: ?>
                <span class="book-discount" style="background: linear-gradient(145deg, #6b7280, #4b5563);">Out of Stock</span>
                <?php endif; ?>
                
                <button class="book-wishlist" data-book-id="<?php echo $book['id']; ?>" title="Add to Wishlist">
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
                            <i class="fas fa-hand-holding-heart"></i> Rent
                        </a>
                        <?php else: ?>
                        <span style="font-size: 0.75rem; color: var(--danger); font-weight: 600;">Out of Stock</span>
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
                <h2 class="section-title">Why Choose MÂY MƠ BOOK</h2>
                <p class="section-subtitle">The best book rental experience for book lovers</p>
            </div>
        </div>
        
        <div class="why-choose-grid">
            <div class="why-choose-card animate-on-scroll animate-delay-1">
                <div class="why-choose-icon">
                    <i class="fas fa-book-open"></i>
                </div>
                <h3>Diverse Book Collection</h3>
                <p>Thousands of books across many genres from novels to academic texts.</p>
            </div>
            
            <div class="why-choose-card animate-on-scroll animate-delay-2">
                <div class="why-choose-icon">
                    <i class="fas fa-piggy-bank"></i>
                </div>
                <h3>Cost-Effective</h3>
                <p>Only pay for the days you rent. Save more compared to buying books.</p>
            </div>
            
            <div class="why-choose-card animate-on-scroll animate-delay-3">
                <div class="why-choose-icon">
                    <i class="fas fa-calendar-alt"></i>
                </div>
                <h3>Flexible</h3>
                <p>Easy to extend your rental period online at any time you want.</p>
            </div>
        </div>
    </div>
</section>

<!-- CTA Section -->
<section class="section">
    <div class="container">
        <div class="cta-section animate-on-scroll">
            <div class="cta-content">
                <h2>Ready to start reading books?</h2>
                <p>Sign up today and get a 10% discount on your first rental!</p>
                <div class="cta-buttons">
                    <a href="register.php" class="btn btn-primary btn-lg">
                        <i class="fas fa-user-plus"></i> Sign Up Now
                    </a>
                    <a href="books.php" class="btn btn-outline btn-lg">
                        <i class="fas fa-book"></i> Explore Books
                    </a>
                </div>
            </div>
        </div>
    </div>
</section>

</main>

<style>
/* Trust Badges Section */
.hero-image {
    width: 2000x;       /* giữ nguyên chiều rộng gốc */
    height: 500px;      /* giữ nguyên chiều cao gốc */
    max-width: 100%;   /* đảm bảo không vượt quá khung */
    display: block;    /* tránh khoảng trắng dư thừa */
    margin: 0 auto;    /* căn giữa nếu cần */
}

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
    background: #c2410c;
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
    background: white;
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
    color:white;
    font-weight: 700;
}

.trust-badge-text p {
    font-size: 0.8rem;
    color: white;
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


/* Category chung */
.category-card {
    border-radius: var(--radius-lg);
    padding: 24px;
    text-align: center;
    transition: var(--transition);
    box-shadow: var(--shadow-sm);
    color: #fff; /* chữ trắng để nổi bật trên nền màu */
}

/* Màu nền đậm cho từng category */
.category-card:nth-child(1) {
    background: #e53935; /* đỏ đậm */
}

.category-card:nth-child(2) {
    background: #fbc02d; /* vàng đậm */
    color: #000; /* chữ đen để dễ đọc trên nền vàng */
}

.category-card:nth-child(3) {
    background: #1e88e5; /* xanh dương đậm */
}

.category-card:nth-child(4) {
    background: #6a1b9a; /* tím đậm */
}



.why-choose-grid {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 32px;
    margin-top: 48px;
}

.why-choose-card {
    background: #c2410c;
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
    background: white;
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
