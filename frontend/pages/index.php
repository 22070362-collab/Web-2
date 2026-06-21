<?php
require_once __DIR__ . '/../templates/header.php';
require_once __DIR__ . '/../../backend/controllers/BookController.php';

$bookController = new BookController();
$featuredBooks = $bookController->available(12);
$categories = $bookController->categories();

function getStableRating($bookId) {
    $seed = abs((int)$bookId);
    return 4.2 + (($seed % 8) / 10);
}

function getStableReviews($bookId) {
    $seed = abs((int)$bookId);
    return 80 + (($seed * 37) % 420);
}

function safeText($value, $fallback = '') {
    return htmlspecialchars((string)($value ?? $fallback), ENT_QUOTES, 'UTF-8');
}

function renderBookCard($book, $badgeLabel = 'Nổi bật', $badgeIcon = 'fa-star') {
    $id = (int)($book['id'] ?? 0);
    $title = safeText($book['title'] ?? '');
    $author = safeText($book['author'] ?? 'Tác giả');
    $category = safeText($book['category'] ?? 'Sách');
    $quantity = (int)($book['quantity'] ?? 0);
    $price = number_format((float)($book['price_per_day'] ?? 5000), 0);
    $rating = getStableRating($id);
    $reviews = getStableReviews($id);
    $cover = getBookCoverImage($book);
    ob_start();
    ?>
    <article class="home-book-card">
        <?php if ($quantity > 0): ?>
            <span class="home-book-badge">
                <i class="fas <?php echo $badgeIcon; ?>"></i>
                <?php echo safeText($badgeLabel); ?>
            </span>
        <?php else: ?>
            <span class="home-book-badge is-muted">
                Hết hàng
            </span>
        <?php endif; ?>

        <button class="home-wishlist-btn" data-book-id="<?php echo $id; ?>" title="Yêu thích">
            <i class="far fa-heart"></i>
        </button>

        <a href="book-detail.php?id=<?php echo $id; ?>" class="home-book-cover" aria-label="<?php echo $title; ?>">
            <img
                src="<?php echo safeText($cover); ?>"
                alt="<?php echo $title; ?>"
                loading="lazy"
            >
        </a>

        <div class="home-book-body">
            <span class="home-book-category"><?php echo $category; ?></span>

            <h3 class="home-book-title">
                <a href="book-detail.php?id=<?php echo $id; ?>">
                    <?php echo $title; ?>
                </a>
            </h3>

            <p class="home-book-author">
                <i class="fas fa-user-edit"></i>
                <?php echo $author; ?>
            </p>

            <div class="home-book-rating" aria-label="Đánh giá <?php echo number_format($rating, 1); ?>">
                <span class="home-stars">
                    <?php for ($i = 1; $i <= 5; $i++): ?>
                        <?php if ($i <= floor($rating)): ?>
                            <i class="fas fa-star"></i>
                        <?php else: ?>
                            <i class="far fa-star"></i>
                        <?php endif; ?>
                    <?php endfor; ?>
                </span>
                <span><?php echo number_format($rating, 1); ?> · <?php echo $reviews; ?> lượt</span>
            </div>

            <div class="home-book-footer">
                <div class="home-price">
                    <strong><?php echo $price; ?>đ</strong>
                    <span>/ ngày</span>
                </div>

                <?php if ($quantity > 0): ?>
                    <a href="book-detail.php?id=<?php echo $id; ?>" class="home-rent-btn">
                        Thuê sách
                    </a>
                <?php else: ?>
                    <span class="home-sold-out">Hết hàng</span>
                <?php endif; ?>
            </div>
        </div>
    </article>
    <?php
    return ob_get_clean();
}

$categoryFallbacks = [
    [
        'name' => 'Tiểu thuyết',
        'desc' => 'Những câu chuyện đáng nhớ',
        'icon' => 'fa-feather-alt',
        'image' => 'https://images.unsplash.com/photo-1512820790803-83ca734da794?w=900&q=85&auto=format&fit=crop'
    ],
    [
        'name' => 'Self-help',
        'desc' => 'Phát triển bản thân',
        'icon' => 'fa-seedling',
        'image' => 'https://images.unsplash.com/photo-1456513080510-7bf3a84b82f8?w=900&q=85&auto=format&fit=crop'
    ],
    [
        'name' => 'Khoa học',
        'desc' => 'Tri thức và khám phá',
        'icon' => 'fa-flask',
        'image' => 'https://images.unsplash.com/photo-1532094349884-543bc11b234d?w=900&q=85&auto=format&fit=crop'
    ],
    [
        'name' => 'Kỹ năng',
        'desc' => 'Học tập và nghề nghiệp',
        'icon' => 'fa-briefcase',
        'image' => 'https://images.unsplash.com/photo-1497633762265-9d179a990aa6?w=900&q=85&auto=format&fit=crop'
    ],
];

$displayCategories = [];

if (!empty($categories)) {
    foreach (array_slice($categories, 0, 4) as $index => $category) {
        $fallback = $categoryFallbacks[$index] ?? $categoryFallbacks[0];

        if (is_array($category)) {
            $name = $category['name'] ?? $category['category'] ?? $category['title'] ?? $fallback['name'];
        } else {
            $name = $category;
        }

        $displayCategories[] = [
            'name' => (string)$name,
            'desc' => $fallback['desc'],
            'icon' => $fallback['icon'],
            'image' => $fallback['image']
        ];
    }
}

if (empty($displayCategories)) {
    $displayCategories = $categoryFallbacks;
}

$newBooks = array_slice($featuredBooks, 0, 4);
?>

<?php if ($isAdmin): ?>
<section class="home-admin-wrap">
    <div class="container">
        <div class="home-admin-banner">
            <div class="home-admin-left">
                <div class="home-admin-icon">
                    <i class="fas fa-user-shield"></i>
                </div>
                <div>
                    <h3>Chế độ quản trị</h3>
                    <p>Bạn đang đăng nhập với quyền Admin.</p>
                </div>
            </div>

            <a href="admin/index.php" class="home-admin-btn">
                Vào trang quản trị
                <i class="fas fa-arrow-right"></i>
            </a>
        </div>
    </div>
</section>
<?php endif; ?>

<main class="home-page">

    <section class="home-hero">
        <div class="home-hero-bg"></div>

        <div class="container">
            <div class="home-hero-grid">
                <div class="home-hero-content">
                    <span class="home-eyebrow">
                        <i class="fas fa-book-open"></i>
                        Mượn văn chương - Truyền cảm hứng
                    </span>

                    <h1>
                        Thuê sách dễ hơn.
                        <span>Đọc hay hơn.</span>
                    </h1>

                    <p>
                        Khám phá những cuốn sách đáng đọc, thuê linh hoạt theo ngày và nhận sách nhanh chóng ngay khi bạn cần.
                    </p>

                    <form class="home-search-box" action="books.php" method="get">
                        <i class="fas fa-search"></i>
                        <input type="text" name="q" placeholder="Tìm tên sách, tác giả hoặc thể loại...">
                        <button type="submit">Tìm kiếm</button>
                    </form>

                    <div class="home-hero-actions">
                        <a href="books.php" class="home-btn home-btn-primary">
                            Khám phá sách
                            <i class="fas fa-arrow-right"></i>
                        </a>
                        <a href="#featured-books" class="home-btn home-btn-light">
                            Sách nổi bật
                        </a>
                    </div>

                    <div class="home-hero-stats">
                        <div>
                            <strong>12K+</strong>
                            <span>Đầu sách</span>
                        </div>
                        <div>
                            <strong>4.9/5</strong>
                            <span>Đánh giá</span>
                        </div>
                        <div>
                            <strong>24h</strong>
                            <span>Hỗ trợ</span>
                        </div>
                    </div>
                </div>

                <div class="home-hero-visual">
                    <div class="home-hero-image-card">
                        <img
                            src="https://i.pinimg.com/736x/85/1c/95/851c95fa2cf7e44fbfbc1180456e5aff.jpg"
                            alt="Không gian đọc sách"
                        >
                    </div>

                    <div class="home-floating-card home-floating-card-top">
                        <i class="fas fa-truck"></i>
                        <div>
                            <strong>Giao nhanh</strong>
                            <span>Cho đơn từ 3 cuốn</span>
                        </div>
                    </div>

                    <div class="home-floating-card home-floating-card-bottom">
                        <i class="fas fa-leaf"></i>
                        <div>
                            <strong>Tiết kiệm hơn</strong>
                            <span>Đọc nhiều, mua ít</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="home-trust-section">
        <div class="container">
            <div class="home-trust-grid">
                <div class="home-trust-item">
                    <i class="fas fa-truck"></i>
                    <div>
                        <strong>Giao hàng tiện lợi</strong>
                        <span>Nhận sách tại nhà</span>
                    </div>
                </div>

                <div class="home-trust-item">
                    <i class="fas fa-shield-alt"></i>
                    <div>
                        <strong>Sách sạch đẹp</strong>
                        <span>Kiểm tra trước khi giao</span>
                    </div>
                </div>

                <div class="home-trust-item">
                    <i class="fas fa-calendar-check"></i>
                    <div>
                        <strong>Thuê linh hoạt</strong>
                        <span>Tính phí theo ngày</span>
                    </div>
                </div>

                <div class="home-trust-item">
                    <i class="fas fa-headset"></i>
                    <div>
                        <strong>Hỗ trợ nhanh</strong>
                        <span>Luôn sẵn sàng</span>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="home-section">
        <div class="container">
            <div class="home-promo-card reveal-on-scroll">
                <div>
                    <span>Ưu đãi mùa đọc sách</span>
                    <h2>Giảm 30% cho sách Self-help</h2>
                    <p>Chọn sách truyền cảm hứng, thuê nhanh và bắt đầu một thói quen đọc mới.</p>
                </div>

                <a href="books.php?category=Self-help" class="home-btn home-btn-primary">
                    Xem ưu đãi
                    <i class="fas fa-arrow-right"></i>
                </a>
            </div>
        </div>
    </section>

    <section class="home-section">
        <div class="container">
            <div class="home-section-head">
                <div>
                    <span class="home-section-kicker">Danh mục</span>
                    <h2>Chọn sách theo gu đọc</h2>
                </div>

                <a href="books.php" class="home-link">
                    Xem tất cả
                    <i class="fas fa-arrow-right"></i>
                </a>
            </div>

            <div class="home-category-grid">
                <?php foreach ($displayCategories as $index => $category): ?>
                    <a
                        href="books.php?category=<?php echo urlencode($category['name']); ?>"
                        class="home-category-card reveal-on-scroll"
                        style="--delay: <?php echo $index * 80; ?>ms;"
                    >
                        <img src="<?php echo safeText($category['image']); ?>" alt="<?php echo safeText($category['name']); ?>">
                        <div class="home-category-overlay"></div>
                        <div class="home-category-content">
                            <div class="home-category-icon">
                                <i class="fas <?php echo safeText($category['icon']); ?>"></i>
                            </div>
                            <h3><?php echo safeText($category['name']); ?></h3>
                            <p><?php echo safeText($category['desc']); ?></p>
                        </div>
                    </a>
                <?php endforeach; ?>
            </div>
        </div>
    </section>

    <section class="home-section home-soft-section" id="featured-books">
        <div class="container">
            <div class="home-section-head">
                <div>
                    <span class="home-section-kicker">Nổi bật</span>
                    <h2>Sách được thuê nhiều</h2>
                </div>

                <div class="home-carousel-actions">
                    <button class="home-carousel-btn" type="button" data-carousel-prev aria-label="Trước">
                        <i class="fas fa-chevron-left"></i>
                    </button>
                    <button class="home-carousel-btn" type="button" data-carousel-next aria-label="Sau">
                        <i class="fas fa-chevron-right"></i>
                    </button>
                    <a href="books.php" class="home-link">
                        Xem tất cả
                        <i class="fas fa-arrow-right"></i>
                    </a>
                </div>
            </div>

            <?php if (count($featuredBooks) > 0): ?>
                <div class="home-books-carousel" data-book-carousel>
                    <?php foreach ($featuredBooks as $book): ?>
                        <?php echo renderBookCard($book, 'Nổi bật', 'fa-star'); ?>
                    <?php endforeach; ?>
                </div>
            <?php else: ?>
                <div class="home-empty-state">
                    <i class="fas fa-book"></i>
                    <h3>Chưa có sách nổi bật</h3>
                    <p>Hệ thống sẽ hiển thị sách tại đây khi có dữ liệu.</p>
                </div>
            <?php endif; ?>
        </div>
    </section>

    <section class="home-section">
        <div class="container">
            <div class="home-section-head">
                <div>
                    <span class="home-section-kicker">Mới cập nhật</span>
                    <h2>Sách mới nhất</h2>
                </div>

                <a href="books.php" class="home-link">
                    Xem tất cả
                    <i class="fas fa-arrow-right"></i>
                </a>
            </div>

            <?php if (count($newBooks) > 0): ?>
                <div class="home-books-grid">
                    <?php foreach ($newBooks as $book): ?>
                        <?php echo renderBookCard($book, 'Mới', 'fa-bolt'); ?>
                    <?php endforeach; ?>
                </div>
            <?php else: ?>
                <div class="home-empty-state">
                    <i class="fas fa-book-reader"></i>
                    <h3>Chưa có sách mới</h3>
                    <p>Hãy thêm sách trong trang quản trị để hiển thị bộ sưu tập.</p>
                </div>
            <?php endif; ?>
        </div>
    </section>

    <section class="home-section home-soft-section">
        <div class="container">
            <div class="home-section-head is-centered">
                <div>
                    <span class="home-section-kicker">Vì sao chọn tụi mình?</span>
                    <h2>Một cách đọc sách nhẹ nhàng hơn</h2>
                </div>
            </div>

            <div class="home-benefit-grid">
                <div class="home-benefit-card reveal-on-scroll">
                    <div class="home-benefit-icon">
                        <i class="fas fa-book-open"></i>
                    </div>
                    <h3>Nhiều thể loại</h3>
                    <p>Từ tiểu thuyết, kỹ năng, self-help đến sách học thuật.</p>
                </div>

                <div class="home-benefit-card reveal-on-scroll">
                    <div class="home-benefit-icon">
                        <i class="fas fa-wallet"></i>
                    </div>
                    <h3>Tiết kiệm chi phí</h3>
                    <p>Không cần mua quá nhiều sách, chỉ thuê khi thật sự muốn đọc.</p>
                </div>

                <div class="home-benefit-card reveal-on-scroll">
                    <div class="home-benefit-icon">
                        <i class="fas fa-sync-alt"></i>
                    </div>
                    <h3>Dễ đổi gu đọc</h3>
                    <p>Đọc xong trả lại, tiếp tục chọn cuốn tiếp theo.</p>
                </div>
            </div>
        </div>
    </section>

    <section class="home-section">
        <div class="container">
            <div class="home-final-cta reveal-on-scroll">
                <div>
                    <span>Bắt đầu hôm nay</span>
                    <h2>Tìm cuốn sách tiếp theo của bạn.</h2>
                    <p>Đăng ký tài khoản để thuê sách nhanh hơn và theo dõi lịch sử đọc của bạn.</p>
                </div>

                <div class="home-final-actions">
                    <a href="register.php" class="home-btn home-btn-primary">
                        Đăng ký
                        <i class="fas fa-arrow-right"></i>
                    </a>
                    <a href="books.php" class="home-btn home-btn-light">
                        Xem sách
                    </a>
                </div>
            </div>
        </div>
    </section>

</main>

<style>
.home-page {
    --home-primary: #8b5a2b;
    --home-primary-dark: #5f3514;
    --home-accent: #d8a05d;
    --home-cream: #fff8ed;
    --home-cream-2: #f8efe1;
    --home-ink: #20160f;
    --home-muted: #75695f;
    --home-line: rgba(95, 53, 20, 0.12);
    --home-card: rgba(255, 255, 255, 0.82);
    --home-shadow: 0 24px 70px rgba(67, 43, 22, 0.13);
    --home-shadow-soft: 0 14px 34px rgba(67, 43, 22, 0.08);
    color: var(--home-ink);
    background:
        radial-gradient(circle at 10% 0%, rgba(216, 160, 93, 0.20), transparent 30%),
        radial-gradient(circle at 90% 10%, rgba(139, 90, 43, 0.12), transparent 26%),
        linear-gradient(180deg, #fffaf3 0%, #ffffff 45%, #fff8ed 100%);
    overflow: hidden;
}

.home-page * {
    box-sizing: border-box;
}

.home-admin-wrap {
    background: #fff8ed;
    padding: 18px 0 0;
}

.home-admin-banner {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 24px;
    padding: 18px 20px;
    background: linear-gradient(135deg, #2f2219, #6b3f1f);
    color: #fff;
    border-radius: 24px;
    box-shadow: var(--home-shadow-soft, 0 14px 34px rgba(67, 43, 22, 0.08));
}

.home-admin-left {
    display: flex;
    align-items: center;
    gap: 14px;
}

.home-admin-icon {
    width: 48px;
    height: 48px;
    border-radius: 16px;
    display: grid;
    place-items: center;
    background: rgba(255, 255, 255, 0.14);
}

.home-admin-banner h3 {
    margin: 0 0 3px;
    font-size: 1rem;
}

.home-admin-banner p {
    margin: 0;
    opacity: 0.78;
    font-size: 0.9rem;
}

.home-admin-btn {
    display: inline-flex;
    align-items: center;
    gap: 9px;
    padding: 11px 16px;
    border-radius: 999px;
    background: #fff;
    color: #4a2a13;
    font-weight: 800;
    text-decoration: none;
    transition: 0.25s ease;
}

.home-admin-btn:hover {
    transform: translateY(-2px);
    color: #4a2a13;
}

.home-hero {
    position: relative;
    padding: 84px 0 60px;
}

.home-hero-bg {
    position: absolute;
    inset: 0;
    background-image:
        linear-gradient(rgba(139, 90, 43, 0.045) 1px, transparent 1px),
        linear-gradient(90deg, rgba(139, 90, 43, 0.045) 1px, transparent 1px);
    background-size: 44px 44px;
    mask-image: linear-gradient(to bottom, #000, transparent 78%);
    pointer-events: none;
}

.home-hero-grid {
    position: relative;
    display: grid;
    grid-template-columns: minmax(0, 0.68fr) minmax(400px, 1fr);
    gap: 60px;
    align-items: center;
}

.home-eyebrow,
.home-section-kicker,
.home-final-cta span,
.home-promo-card span {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    width: fit-content;
    padding: 8px 13px;
    border: 1px solid var(--home-line);
    border-radius: 999px;
    background: rgba(255, 255, 255, 0.78);
    color: var(--home-primary);
    font-weight: 800;
    font-size: 0.78rem;
    letter-spacing: 0.02em;
}

.home-hero-content h1 {
    margin: 20px 0 18px;
    max-width: 760px;
    font-family: 'Inter', sans-serif;
    font-size: clamp(3rem, 6vw, 5.8rem);
    line-height: 0.96;
    letter-spacing: -0.06em;
    color: var(--home-ink);
}

.home-hero-content h1 span {
    color: var(--home-primary);
}

.home-hero-content p {
    max-width: 590px;
    margin: 0;
    color: var(--home-muted);
    font-size: 1.08rem;
    line-height: 1.75;
}

.home-search-box {
    display: flex;
    align-items: center;
    gap: 12px;
    max-width: 620px;
    margin-top: 30px;
    padding: 8px;
    background: rgba(255, 255, 255, 0.9);
    border: 1px solid var(--home-line);
    border-radius: 999px;
    box-shadow: var(--home-shadow-soft);
}

.home-search-box i {
    margin-left: 12px;
    color: var(--home-primary);
}

.home-search-box input {
    flex: 1;
    min-width: 0;
    border: 0;
    outline: 0;
    background: transparent;
    color: var(--home-ink);
    font-size: 0.95rem;
}

.home-search-box input::placeholder {
    color: #9b8c7d;
}

.home-search-box button {
    border: 0;
    cursor: pointer;
    padding: 12px 20px;
    border-radius: 999px;
    background: var(--home-ink);
    color: #fff;
    font-weight: 800;
    transition: 0.25s ease;
}

.home-search-box button:hover {
    background: var(--home-primary);
    transform: translateY(-1px);
}

.home-hero-actions {
    display: flex;
    align-items: center;
    gap: 12px;
    margin-top: 24px;
    flex-wrap: wrap;
}

.home-btn {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 9px;
    min-height: 46px;
    padding: 0 20px;
    border-radius: 999px;
    font-weight: 900;
    text-decoration: none;
    border: 1px solid transparent;
    transition: 0.25s ease;
    white-space: nowrap;
}

.home-btn:hover {
    transform: translateY(-2px);
    text-decoration: none;
}

.home-btn-primary {
    background: linear-gradient(135deg, var(--home-primary), var(--home-primary-dark));
    color: #fff;
    box-shadow: 0 14px 28px rgba(95, 53, 20, 0.22);
}

.home-btn-primary:hover {
    color: #fff;
}

.home-btn-light {
    background: rgba(255, 255, 255, 0.78);
    color: var(--home-ink);
    border-color: var(--home-line);
}

.home-btn-light:hover {
    color: var(--home-primary);
    background: #fff;
}

.home-hero-stats {
    display: flex;
    gap: 28px;
    margin-top: 34px;
    padding-top: 26px;
    border-top: 1px solid var(--home-line);
    flex-wrap: wrap;
}

.home-hero-stats div {
    min-width: 86px;
}

.home-hero-stats strong {
    display: block;
    font-family: 'Inter', sans-serif;
    font-size: 1.7rem;
    line-height: 1;
    color: var(--home-primary);
}

.home-hero-stats span {
    display: block;
    margin-top: 6px;
    color: var(--home-muted);
    font-size: 0.86rem;
}

.home-hero-visual {
    position: relative;
    min-height: 560px;
}

.home-hero-image-card {
    position: absolute;
    inset: 0 0 0 28px;
    border-radius: 42px;
    overflow: hidden;
    box-shadow: var(--home-shadow);
    border: 10px solid rgba(255, 255, 255, 0.72);
    transform: rotate(1.5deg);
}

.home-hero-image-card::after {
    content: '';
    position: absolute;
    inset: 0;
    background:
        linear-gradient(180deg, transparent 45%, rgba(32, 22, 15, 0.46)),
        radial-gradient(circle at 20% 10%, rgba(255, 255, 255, 0.4), transparent 38%);
}

.home-hero-image-card img {
    width: 100%;
    height: 100%;
    display: block;
    object-fit: cover;
}

.home-floating-card {
    position: absolute;
    z-index: 2;
    display: flex;
    align-items: center;
    gap: 12px;
    width: max-content;
    max-width: 260px;
    padding: 14px 16px;
    border: 1px solid var(--home-line);
    border-radius: 22px;
    background: rgba(255, 255, 255, 0.88);
    backdrop-filter: blur(16px);
    box-shadow: var(--home-shadow-soft);
}

.home-floating-card i {
    width: 42px;
    height: 42px;
    display: grid;
    place-items: center;
    border-radius: 15px;
    background: var(--home-cream);
    color: var(--home-primary);
}

.home-floating-card strong {
    display: block;
    color: var(--home-ink);
    font-size: 0.92rem;
}

.home-floating-card span {
    display: block;
    margin-top: 2px;
    color: var(--home-muted);
    font-size: 0.8rem;
}

.home-floating-card-top {
    top: 36px;
    left: 0;
}

.home-floating-card-bottom {
    right: -8px;
    bottom: 56px;
}

.home-trust-section {
    padding: 18px 0 12px;
}

.home-trust-grid {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: 14px;
}

.home-trust-item {
    display: flex;
    align-items: center;
    gap: 13px;
    padding: 18px;
    border: 1px solid var(--home-line);
    border-radius: 24px;
    background: rgba(255, 255, 255, 0.72);
    box-shadow: 0 12px 30px rgba(67, 43, 22, 0.045);
}

.home-trust-item i {
    width: 44px;
    height: 44px;
    display: grid;
    place-items: center;
    flex: 0 0 auto;
    border-radius: 16px;
    color: var(--home-primary);
    background: var(--home-cream);
}

.home-trust-item strong {
    display: block;
    color: var(--home-ink);
    font-size: 0.9rem;
}

.home-trust-item span {
    display: block;
    margin-top: 3px;
    color: var(--home-muted);
    font-size: 0.8rem;
}

.home-section {
    padding: 76px 0;
}

.home-soft-section {
    background:
        radial-gradient(circle at 18% 10%, rgba(216, 160, 93, 0.16), transparent 34%),
        linear-gradient(180deg, rgba(255, 248, 237, 0.82), rgba(255, 255, 255, 0.8));
}

.home-section-head {
    display: flex;
    align-items: end;
    justify-content: space-between;
    gap: 24px;
    margin-bottom: 30px;
}

.home-section-head.is-centered {
    justify-content: center;
    text-align: center;
}

.home-section-head h2 {
    margin: 12px 0 0;
    font-family: 'Inter', sans-serif;
    font-size: clamp(2rem, 4vw, 3.2rem);
    line-height: 1;
    letter-spacing: -0.04em;
    color: var(--home-ink);
}

.home-link {
    display: inline-flex;
    align-items: center;
    gap: 9px;
    color: var(--home-primary);
    font-weight: 900;
    text-decoration: none;
    white-space: nowrap;
}

.home-link:hover {
    color: var(--home-primary-dark);
    text-decoration: none;
}

.home-promo-card {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 28px;
    padding: 38px;
    min-height: 230px;
    border-radius: 34px;
    overflow: hidden;
    position: relative;
    background:
        linear-gradient(115deg, rgba(32, 22, 15, 0.88), rgba(95, 53, 20, 0.72)),
        url('https://images.unsplash.com/photo-1519682337058-a94d519337bc?w=1400&q=90&auto=format&fit=crop') center/cover;
    color: #fff;
    box-shadow: var(--home-shadow);
}

.home-promo-card::after {
    content: '';
    position: absolute;
    right: -90px;
    top: -110px;
    width: 260px;
    height: 260px;
    border-radius: 999px;
    background: rgba(255, 255, 255, 0.12);
}

.home-promo-card > * {
    position: relative;
    z-index: 1;
}

.home-promo-card span {
    background: rgba(255, 255, 255, 0.14);
    border-color: rgba(255, 255, 255, 0.18);
    color: #ffe6c6;
}

.home-promo-card h2 {
    margin: 14px 0 10px;
    font-family: 'Inter', sans-serif;
    font-size: clamp(2rem, 4vw, 3.2rem);
    letter-spacing: -0.04em;
    color: #fff;
}

.home-promo-card p {
    max-width: 620px;
    margin: 0;
    color: rgba(255, 255, 255, 0.82);
    line-height: 1.7;
}

.home-category-grid {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: 18px;
}

.home-category-card {
    position: relative;
    min-height: 250px;
    border-radius: 30px;
    overflow: hidden;
    color: #fff;
    text-decoration: none;
    box-shadow: var(--home-shadow-soft);
    isolation: isolate;
    transform: translateY(0);
    transition: 0.3s ease;
}

.home-category-card:hover {
    transform: translateY(-8px);
    color: #fff;
    text-decoration: none;
    box-shadow: var(--home-shadow);
}

.home-category-card img {
    position: absolute;
    inset: 0;
    width: 100%;
    height: 100%;
    object-fit: cover;
    transition: 0.5s ease;
    z-index: -2;
}

.home-category-card:hover img {
    transform: scale(1.08);
}

.home-category-overlay {
    position: absolute;
    inset: 0;
    background:
        linear-gradient(180deg, rgba(32, 22, 15, 0.08), rgba(32, 22, 15, 0.8)),
        linear-gradient(135deg, rgba(139, 90, 43, 0.42), transparent);
    z-index: -1;
}

.home-category-content {
    position: absolute;
    inset: auto 18px 18px;
}

.home-category-icon {
    width: 48px;
    height: 48px;
    display: grid;
    place-items: center;
    border-radius: 17px;
    margin-bottom: 16px;
    background: rgba(255, 255, 255, 0.18);
    backdrop-filter: blur(12px);
}

.home-category-content h3 {
    margin: 0 0 5px;
    font-size: 1.25rem;
    font-weight: 900;
}

.home-category-content p {
    margin: 0;
    color: rgba(255, 255, 255, 0.82);
    font-size: 0.9rem;
}

.home-carousel-actions {
    display: flex;
    align-items: center;
    gap: 10px;
}

.home-carousel-btn {
    width: 42px;
    height: 42px;
    display: grid;
    place-items: center;
    border-radius: 999px;
    border: 1px solid var(--home-line);
    background: #fff;
    color: var(--home-ink);
    cursor: pointer;
    transition: 0.25s ease;
}

.home-carousel-btn:hover {
    background: var(--home-ink);
    color: #fff;
    transform: translateY(-2px);
}

.home-books-carousel {
    display: grid;
    grid-auto-flow: column;
    grid-auto-columns: minmax(250px, 1fr);
    gap: 20px;
    overflow-x: auto;
    overscroll-behavior-inline: contain;
    scroll-snap-type: inline mandatory;
    scroll-behavior: smooth;
    padding: 4px 4px 18px;
    scrollbar-width: thin;
}

.home-books-carousel .home-book-card {
    scroll-snap-align: start;
}

.home-books-grid {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: 22px;
}

.home-book-card {
    position: relative;
    display: flex;
    flex-direction: column;
    min-height: 100%;
    border: 1px solid var(--home-line);
    border-radius: 30px;
    background: var(--home-card);
    overflow: hidden;
    box-shadow: 0 16px 42px rgba(67, 43, 22, 0.07);
    transition: 0.28s ease;
}

.home-book-card:hover {
    transform: translateY(-8px);
    box-shadow: var(--home-shadow);
    border-color: rgba(139, 90, 43, 0.22);
}

.home-book-badge {
    position: absolute;
    z-index: 3;
    top: 14px;
    left: 14px;
    display: inline-flex;
    align-items: center;
    gap: 6px;
    padding: 7px 11px;
    border-radius: 999px;
    background: rgba(32, 22, 15, 0.86);
    color: #fff;
    font-size: 0.74rem;
    font-weight: 900;
    backdrop-filter: blur(12px);
}

.home-book-badge.is-muted {
    background: rgba(95, 95, 95, 0.82);
}

.home-wishlist-btn {
    position: absolute;
    z-index: 3;
    top: 14px;
    right: 14px;
    width: 38px;
    height: 38px;
    display: grid;
    place-items: center;
    border-radius: 999px;
    border: 1px solid rgba(255, 255, 255, 0.42);
    background: rgba(255, 255, 255, 0.82);
    color: var(--home-ink);
    cursor: pointer;
    transition: 0.25s ease;
}

.home-wishlist-btn:hover {
    color: #c2410c;
    transform: scale(1.05);
}

.home-book-cover {
    display: block;
    height: 280px;
    padding: 24px 34px;
    background:
        radial-gradient(circle at 50% 25%, rgba(216, 160, 93, 0.25), transparent 48%),
        linear-gradient(180deg, #fff8ed, #f1e2cc);
    text-decoration: none;
}

.home-book-cover img {
    display: block;
    width: 100%;
    height: 100%;
    object-fit: contain;
    filter: drop-shadow(0 18px 20px rgba(58, 35, 18, 0.24));
    transition: 0.3s ease;
}

.home-book-card:hover .home-book-cover img {
    transform: translateY(-5px) scale(1.025);
}

.home-book-body {
    display: flex;
    flex-direction: column;
    flex: 1;
    padding: 20px;
}

.home-book-category {
    width: fit-content;
    padding: 6px 10px;
    border-radius: 999px;
    background: var(--home-cream);
    color: var(--home-primary);
    font-weight: 900;
    font-size: 0.72rem;
}

.home-book-title {
    margin: 13px 0 7px;
    font-size: 1.05rem;
    line-height: 1.35;
    font-weight: 900;
}

.home-book-title a {
    color: var(--home-ink);
    text-decoration: none;
}

.home-book-title a:hover {
    color: var(--home-primary);
}

.home-book-author {
    display: flex;
    align-items: center;
    gap: 7px;
    margin: 0 0 12px;
    color: var(--home-muted);
    font-size: 0.86rem;
}

.home-book-rating {
    display: flex;
    align-items: center;
    gap: 8px;
    margin-top: auto;
    color: var(--home-muted);
    font-size: 0.82rem;
}

.home-stars {
    display: inline-flex;
    gap: 2px;
    color: #d89a32;
}

.home-book-footer {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 14px;
    margin-top: 18px;
    padding-top: 16px;
    border-top: 1px solid var(--home-line);
}

.home-price strong {
    display: block;
    color: var(--home-primary);
    font-size: 1.14rem;
}

.home-price span {
    display: block;
    margin-top: 1px;
    color: var(--home-muted);
    font-size: 0.8rem;
}

.home-rent-btn {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    min-height: 38px;
    padding: 0 14px;
    border-radius: 999px;
    background: var(--home-ink);
    color: #fff;
    font-size: 0.82rem;
    font-weight: 900;
    text-decoration: none;
    transition: 0.25s ease;
}

.home-rent-btn:hover {
    color: #fff;
    background: var(--home-primary);
    text-decoration: none;
    transform: translateY(-2px);
}

.home-sold-out {
    color: #b91c1c;
    font-size: 0.8rem;
    font-weight: 900;
}

.home-empty-state {
    padding: 48px 22px;
    border: 1px dashed rgba(139, 90, 43, 0.24);
    border-radius: 28px;
    text-align: center;
    background: rgba(255, 255, 255, 0.7);
}

.home-empty-state i {
    font-size: 2rem;
    color: var(--home-primary);
    margin-bottom: 12px;
}

.home-empty-state h3 {
    margin: 0 0 6px;
    color: var(--home-ink);
}

.home-empty-state p {
    margin: 0;
    color: var(--home-muted);
}

.home-benefit-grid {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 22px;
    margin-top: 32px;
}

.home-benefit-card {
    padding: 34px 28px;
    border: 1px solid var(--home-line);
    border-radius: 30px;
    background: rgba(255, 255, 255, 0.76);
    box-shadow: var(--home-shadow-soft);
    text-align: center;
    transition: 0.28s ease;
}

.home-benefit-card:hover {
    transform: translateY(-8px);
    box-shadow: var(--home-shadow);
}

.home-benefit-icon {
    width: 66px;
    height: 66px;
    display: grid;
    place-items: center;
    margin: 0 auto 18px;
    border-radius: 22px;
    background: var(--home-cream);
    color: var(--home-primary);
    font-size: 1.45rem;
}

.home-benefit-card h3 {
    margin: 0 0 10px;
    font-size: 1.15rem;
    font-weight: 900;
    color: var(--home-ink);
}

.home-benefit-card p {
    margin: 0;
    color: var(--home-muted);
    line-height: 1.7;
}

.home-final-cta {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 28px;
    padding: 44px;
    border-radius: 36px;
    background:
        radial-gradient(circle at 90% 10%, rgba(255, 255, 255, 0.18), transparent 34%),
        linear-gradient(135deg, #27180f, #6e421f);
    color: #fff;
    box-shadow: var(--home-shadow);
    overflow: hidden;
    position: relative;
}

.home-final-cta span {
    background: rgba(255, 255, 255, 0.12);
    border-color: rgba(255, 255, 255, 0.16);
    color: #ffe3bf;
}

.home-final-cta h2 {
    margin: 14px 0 10px;
    max-width: 720px;
    font-family: 'Inter', sans-serif;
    font-size: clamp(2rem, 4vw, 3.35rem);
    letter-spacing: -0.04em;
    color: #fff;
}

.home-final-cta p {
    max-width: 620px;
    margin: 0;
    color: rgba(255, 255, 255, 0.78);
    line-height: 1.7;
}

.home-final-actions {
    display: flex;
    align-items: center;
    gap: 12px;
    flex-wrap: wrap;
}

.reveal-on-scroll {
    opacity: 0;
    transform: translateY(18px);
    transition: opacity 0.6s ease, transform 0.6s ease;
    transition-delay: var(--delay, 0ms);
}

.reveal-on-scroll.is-visible {
    opacity: 1;
    transform: translateY(0);
}

@media (max-width: 1180px) {
    .home-hero-grid {
        grid-template-columns: 1fr;
        gap: 42px;
    }

    .home-hero-visual {
        min-height: 460px;
    }

    .home-hero-image-card {
        inset: 0;
    }

    .home-floating-card-bottom {
        right: 18px;
    }

    .home-category-grid,
    .home-books-grid {
        grid-template-columns: repeat(2, 1fr);
    }

    .home-trust-grid {
        grid-template-columns: repeat(2, 1fr);
    }
}

@media (max-width: 768px) {
    .home-hero {
        padding: 58px 0 38px;
    }

    .home-hero-content h1 {
        font-size: clamp(2.65rem, 13vw, 4rem);
    }

    .home-hero-content p {
        font-size: 1rem;
    }

    .home-search-box {
        border-radius: 24px;
        align-items: stretch;
        flex-wrap: wrap;
        padding: 12px;
    }

    .home-search-box i {
        margin-left: 4px;
        align-self: center;
    }

    .home-search-box input {
        min-height: 42px;
    }

    .home-search-box button {
        width: 100%;
    }

    .home-hero-stats {
        gap: 20px;
    }

    .home-hero-visual {
        min-height: 390px;
    }

    .home-hero-image-card {
        border-radius: 32px;
        border-width: 7px;
        transform: none;
    }

    .home-floating-card {
        display: none;
    }

    .home-section {
        padding: 56px 0;
    }

    .home-section-head {
        align-items: flex-start;
        flex-direction: column;
        margin-bottom: 24px;
    }

    .home-carousel-actions {
        width: 100%;
        flex-wrap: wrap;
    }

    .home-category-grid,
    .home-books-grid,
    .home-benefit-grid,
    .home-trust-grid {
        grid-template-columns: 1fr;
    }

    .home-promo-card,
    .home-final-cta,
    .home-admin-banner {
        flex-direction: column;
        align-items: flex-start;
    }

    .home-promo-card,
    .home-final-cta {
        padding: 30px 24px;
        border-radius: 30px;
    }

    .home-final-actions,
    .home-final-actions .home-btn,
    .home-promo-card .home-btn {
        width: 100%;
    }

    .home-book-cover {
        height: 260px;
    }
}

@media (max-width: 520px) {
    .home-trust-item {
        padding: 15px;
        border-radius: 20px;
    }

    .home-books-carousel {
        grid-auto-columns: minmax(245px, 82vw);
    }

    .home-category-card {
        min-height: 220px;
    }

    .home-book-footer {
        align-items: flex-start;
        flex-direction: column;
    }

    .home-rent-btn {
        width: 100%;
    }
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function () {
    var carousel = document.querySelector('[data-book-carousel]');
    var prevBtn = document.querySelector('[data-carousel-prev]');
    var nextBtn = document.querySelector('[data-carousel-next]');

    if (carousel && prevBtn && nextBtn) {
        var getScrollAmount = function () {
            var card = carousel.querySelector('.home-book-card');
            return card ? card.offsetWidth + 20 : 280;
        };

        prevBtn.addEventListener('click', function () {
            carousel.scrollBy({
                left: -getScrollAmount(),
                behavior: 'smooth'
            });
        });

        nextBtn.addEventListener('click', function () {
            carousel.scrollBy({
                left: getScrollAmount(),
                behavior: 'smooth'
            });
        });
    }

    var revealItems = document.querySelectorAll('.reveal-on-scroll');

    if ('IntersectionObserver' in window) {
        var observer = new IntersectionObserver(function (entries) {
            entries.forEach(function (entry) {
                if (entry.isIntersecting) {
                    entry.target.classList.add('is-visible');
                    observer.unobserve(entry.target);
                }
            });
        }, {
            threshold: 0.14
        });

        revealItems.forEach(function (item) {
            observer.observe(item);
        });
    } else {
        revealItems.forEach(function (item) {
            item.classList.add('is-visible');
        });
    }
});
</script>

<?php require_once __DIR__ . '/../templates/footer.php'; ?>