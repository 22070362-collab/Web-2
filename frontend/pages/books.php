<?php
require_once __DIR__ . '/../templates/header.php';
require_once __DIR__ . '/../../backend/controllers/BookController.php';

$bookController = new BookController();
$categories = $bookController->categories();

$search = isset($_GET['search']) ? trim($_GET['search']) : '';
$quickSearch = isset($_GET['q']) ? trim($_GET['q']) : '';

if ($search === '' && $quickSearch !== '') {
    $search = $quickSearch;
}

$category = isset($_GET['category']) ? trim((string)$_GET['category']) : '';

if (!empty($search)) {
    $books = $bookController->search($search);
    $pageTitle = 'Tìm kiếm: ' . htmlspecialchars($search, ENT_QUOTES, 'UTF-8');
} elseif (!empty($category)) {
    $books = $bookController->category($category);
    $pageTitle = 'Sách ' . htmlspecialchars($category, ENT_QUOTES, 'UTF-8');
} else {
    $books = $bookController->index();
    $pageTitle = 'Tất cả sách';
}

if (!function_exists('getBookCoverImage')) {
    /**
     * Lấy đường dẫn ảnh bìa sách an toàn
     * @param array $book Dữ liệu sách (có thể chứa 'cover_image' hoặc 'id')
     * @return string URL ảnh
     */
    function getBookCoverImage($book) {
        // Nếu có trường cover_image và không rỗng
        if (!empty($book['cover_image'])) {
            return htmlspecialchars($book['cover_image'], ENT_QUOTES, 'UTF-8');
        }
        
        // Fallback: ảnh placeholder dựa trên ID sách (Picsum)
        $bookId = isset($book['id']) ? (int)$book['id'] : rand(1, 100);
        return "https://picsum.photos/id/{$bookId}/300/400?grayscale";
    }
}

if (!function_exists('booksText')) {
    function booksText($value, $fallback = '') {
        return htmlspecialchars((string)($value ?? $fallback), ENT_QUOTES, 'UTF-8');
    }
}

if (!function_exists('booksMoney')) {
    function booksMoney($value) {
        return number_format((float)$value, 0) . 'đ';
    }
}

if (!function_exists('booksCover')) {
    function booksCover($book) {
        return getBookCoverImage($book);
    }
}

if (!function_exists('booksStableRating')) {
    function booksStableRating($bookId) {
        $seed = abs((int)$bookId);
        return 4.1 + (($seed % 9) / 10);
    }
}

if (!function_exists('booksStableReviews')) {
    function booksStableReviews($bookId) {
        $seed = abs((int)$bookId);
        return 42 + (($seed * 31) % 380);
    }
}

if (!function_exists('booksRenderStars')) {
    function booksRenderStars($rating) {
        $ratingValue = (float)$rating;
        $html = '';

        for ($i = 1; $i <= 5; $i++) {
            if ($ratingValue >= $i) {
                $html .= '<i class="fas fa-star"></i>';
            } elseif ($ratingValue >= ($i - 0.5)) {
                $html .= '<i class="fas fa-star-half-alt"></i>';
            } else {
                $html .= '<i class="far fa-star"></i>';
            }
        }

        return $html;
    }
}

$bookCount = count($books);
$availableCount = 0;

foreach ($books as $bookItem) {
    if (((int)($bookItem['quantity'] ?? 0)) > 0) {
        $availableCount++;
    }
}

$categoryIcons = [
    'Tiểu thuyết' => 'fa-feather-alt',
    'Self-help' => 'fa-seedling',
    'Khoa học' => 'fa-atom',
    'Kỹ năng' => 'fa-lightbulb',
    'Truyện ngắn' => 'fa-pen-fancy',
    'Triết học' => 'fa-brain',
    'Phi hư cấu' => 'fa-book-reader',
    'Văn học' => 'fa-book-open',
    'Kinh doanh' => 'fa-briefcase',
    'Tâm lý' => 'fa-heart',
];
?>

<main class="books-page">

    <!-- Hero -->
    <section class="books-hero">
        <div class="books-hero-bg"></div>

        <div class="container">
            <div class="books-hero-grid">
                <div class="books-hero-content">
                    <span class="books-eyebrow">
                        <i class="fas fa-book-open"></i>
                        Văn minh đọc sách - Văn minh chia sẻ
                    </span>

                    <h1>
                        Tìm cuốn sách
                        <span>đáng đọc hôm nay.</span>
                    </h1>

                    <p>
                        Khám phá kho sách của Mây Mơ Book, tìm theo tên sách, tác giả hoặc thể loại
                        và thuê linh hoạt theo số ngày bạn cần.
                    </p>

                    <form action="books.php" method="GET" class="books-search-card">
                        <div class="books-search-input">
                            <i class="fas fa-search"></i>
                            <input
                                type="text"
                                name="search"
                                placeholder="Tìm tên sách, tác giả hoặc thể loại..."
                                value="<?php echo booksText($search); ?>"
                            >
                        </div>

                        <?php if (!empty($category)): ?>
                            <input type="hidden" name="category" value="<?php echo booksText($category); ?>">
                        <?php endif; ?>

                        <button type="submit">
                            Tìm kiếm
                            <i class="fas fa-arrow-right"></i>
                        </button>
                    </form>

                    <div class="books-hero-actions">
                        <a href="#book-list" class="books-btn books-btn-primary">
                            Xem sách
                            <i class="fas fa-arrow-down"></i>
                        </a>

                        <?php if (!empty($search) || !empty($category)): ?>
                            <a href="books.php" class="books-btn books-btn-light">
                                Xóa bộ lọc
                            </a>
                        <?php else: ?>
                            <a href="#categories" class="books-btn books-btn-light">
                                Xem thể loại
                            </a>
                        <?php endif; ?>
                    </div>
                </div>

                <div class="books-hero-visual">
                    <div class="books-hero-image-card">
                        <img
                            src="https://i.pinimg.com/736x/ff/74/42/ff7442003a5575a2648e8eb94114cb85.jpg"
                            alt="Một cuốn sách tối giản"
                        >
                    </div>

                    <div class="books-floating-card books-floating-top">
                        <i class="fas fa-sparkles"></i>
                        <div>
                            <strong><?php echo number_format($bookCount); ?> sách</strong>
                            <span>đang hiển thị</span>
                        </div>
                    </div>

                    <div class="books-floating-card books-floating-bottom">
                        <i class="fas fa-check-circle"></i>
                        <div>
                            <strong><?php echo number_format($availableCount); ?> còn hàng</strong>
                            <span>sẵn sàng cho thuê</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Filter -->
    <section class="books-filter-section" id="categories">
        <div class="container">
            <div class="books-filter-card">
                <div class="books-filter-head">
                    <div>
                        <span>Bộ lọc</span>
                        <h2>Khám phá theo thể loại</h2>
                    </div>

                    <div class="books-result-chip">
                        <i class="fas fa-book"></i>
                        <?php echo number_format($bookCount); ?> sách được tìm thấy
                    </div>
                </div>

                <div class="books-category-pills">
                    <a href="books.php" class="books-category-pill <?php echo empty($category) && empty($search) ? 'is-active' : ''; ?>">
                        <i class="fas fa-border-all"></i>
                        Tất cả
                    </a>

                    <?php foreach ($categories as $cat): ?>
                        <?php
                        $catName = (string)$cat;
                        $catIcon = $categoryIcons[$catName] ?? 'fa-book';
                        ?>
                        <a
                            href="books.php?category=<?php echo urlencode($catName); ?>"
                            class="books-category-pill <?php echo $category === $catName ? 'is-active' : ''; ?>"
                        >
                            <i class="fas <?php echo booksText($catIcon); ?>"></i>
                            <?php echo booksText($catName); ?>
                        </a>
                    <?php endforeach; ?>
                </div>

                <?php if (!empty($search) || !empty($category)): ?>
                    <div class="books-active-filter">
                        <span>
                            <i class="fas fa-filter"></i>
                            Đang lọc:
                        </span>

                        <?php if (!empty($search)): ?>
                            <strong>“<?php echo booksText($search); ?>”</strong>
                        <?php endif; ?>

                        <?php if (!empty($category)): ?>
                            <strong><?php echo booksText($category); ?></strong>
                        <?php endif; ?>

                        <a href="books.php">
                            Xóa
                            <i class="fas fa-times"></i>
                        </a>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </section>

    <!-- Books Grid -->
    <section class="books-list-section" id="book-list">
        <div class="container">
            <div class="books-section-head">
                <div>
                    <span class="books-section-kicker">Danh sách sách</span>
                    <h2><?php echo booksText($pageTitle); ?></h2>
                    <p><?php echo number_format($bookCount); ?> kết quả phù hợp với lựa chọn của bạn.</p>
                </div>

                <div class="books-mini-stats">
                    <div>
                        <strong><?php echo number_format($bookCount); ?></strong>
                        <span>Tổng sách</span>
                    </div>
                    <div>
                        <strong><?php echo number_format($availableCount); ?></strong>
                        <span>Còn hàng</span>
                    </div>
                </div>
            </div>

            <?php if ($bookCount > 0): ?>
                <div class="books-grid">
                    <?php foreach ($books as $book): ?>
                        <?php
                        $bookId = (int)($book['id'] ?? 0);
                        $bookTitle = booksText($book['title'] ?? '');
                        $bookAuthor = booksText($book['author'] ?? 'Tác giả');
                        $bookCategory = booksText($book['category'] ?? 'Sách');
                        $bookQuantity = (int)($book['quantity'] ?? 0);
                        $bookAvailable = $bookQuantity > 0;
                        $bookPrice = booksMoney($book['price_per_day'] ?? 0);
                        $bookRating = booksStableRating($bookId);
                        $bookReviews = booksStableReviews($bookId);
                        $coverUrl = booksCover($book);
                        ?>

                        <article class="books-card reveal-on-scroll">
                            <span class="books-status <?php echo $bookAvailable ? 'is-available' : 'is-unavailable'; ?>">
                                <i class="fas fa-<?php echo $bookAvailable ? 'check' : 'times'; ?>"></i>
                                <?php echo $bookAvailable ? 'Còn hàng' : 'Hết hàng'; ?>
                            </span>

                            <button class="books-wishlist" data-book-id="<?php echo $bookId; ?>" title="Yêu thích">
                                <i class="far fa-heart"></i>
                            </button>

                            <a href="book-detail.php?id=<?php echo $bookId; ?>" class="books-cover">
                                <img
                                    src="<?php echo $coverUrl; ?>"
                                    alt="<?php echo $bookTitle; ?>"
                                    loading="lazy"
                                    onerror="this.onerror=null; this.src='https://placehold.co/400x500?text=No+Cover';"
                                >
                            </a>

                            <div class="books-card-body">
                                <span class="books-card-category">
                                    <?php echo $bookCategory; ?>
                                </span>

                                <h3>
                                    <a href="book-detail.php?id=<?php echo $bookId; ?>">
                                        <?php echo $bookTitle; ?>
                                    </a>
                                </h3>

                                <p class="books-author">
                                    <i class="fas fa-user-edit"></i>
                                    <?php echo $bookAuthor; ?>
                                </p>

                                <div class="books-rating">
                                    <div class="books-stars">
                                        <?php echo booksRenderStars($bookRating); ?>
                                    </div>
                                    <span>
                                        <?php echo number_format($bookRating, 1); ?> · <?php echo number_format($bookReviews); ?> lượt
                                    </span>
                                </div>

                                <div class="books-card-footer">
                                    <div class="books-price">
                                        <strong><?php echo $bookPrice; ?></strong>
                                        <span>/ ngày</span>
                                    </div>

                                    <?php if ($bookAvailable): ?>
                                        <a href="book-detail.php?id=<?php echo $bookId; ?>" class="books-rent-btn">
                                            Thuê
                                            <i class="fas fa-arrow-right"></i>
                                        </a>
                                    <?php else: ?>
                                        <span class="books-sold-out">Hết hàng</span>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </article>
                    <?php endforeach; ?>
                </div>
            <?php else: ?>
                <div class="books-empty-state">
                    <div class="books-empty-icon">
                        <i class="fas fa-search"></i>
                    </div>

                    <h3>Không tìm thấy sách</h3>
                    <p>Thử tìm bằng từ khóa khác hoặc quay lại toàn bộ kho sách.</p>

                    <a href="books.php" class="books-btn books-btn-primary">
                        Xem tất cả sách
                        <i class="fas fa-arrow-right"></i>
                    </a>
                </div>
            <?php endif; ?>
        </div>
    </section>

    <!-- Explore More -->
    <?php if (!empty($categories)): ?>
        <section class="books-explore-section">
            <div class="container">
                <div class="books-section-head">
                    <div>
                        <span class="books-section-kicker">Khám phá thêm</span>
                        <h2>Chọn nhanh theo thể loại</h2>
                        <p>Tiếp tục tìm những cuốn sách hợp với gu đọc của bạn.</p>
                    </div>
                </div>

                <div class="books-explore-grid">
                    <?php foreach ($categories as $cat): ?>
                        <?php
                        $catName = (string)$cat;
                        $catIcon = $categoryIcons[$catName] ?? 'fa-book';
                        ?>
                        <a href="books.php?category=<?php echo urlencode($catName); ?>" class="books-explore-card reveal-on-scroll">
                            <div class="books-explore-icon">
                                <i class="fas <?php echo booksText($catIcon); ?>"></i>
                            </div>

                            <div>
                                <h3><?php echo booksText($catName); ?></h3>
                                <p>Xem sách thuộc thể loại này</p>
                            </div>

                            <i class="fas fa-arrow-right books-explore-arrow"></i>
                        </a>
                    <?php endforeach; ?>
                </div>
            </div>
        </section>
    <?php endif; ?>

</main>

<style>
.books-page {
    --books-primary: #8b5a2b;
    --books-primary-dark: #5a3518;
    --books-accent: #d89a45;
    --books-cream: #fff8ed;
    --books-cream-2: #f0dec4;
    --books-ink: #1f1711;
    --books-muted: #74685f;
    --books-line: rgba(92, 57, 24, 0.13);
    --books-card: rgba(255, 255, 255, 0.82);
    --books-shadow: 0 24px 70px rgba(55, 34, 18, 0.13);
    --books-shadow-soft: 0 14px 34px rgba(55, 34, 18, 0.08);
    background:
        radial-gradient(circle at 8% 0%, rgba(216, 154, 69, 0.2), transparent 30%),
        radial-gradient(circle at 92% 8%, rgba(139, 90, 43, 0.11), transparent 25%),
        linear-gradient(180deg, #fffaf3 0%, #fff 46%, #fff8ed 100%);
    color: var(--books-ink);
    overflow: hidden;
}

.books-page * {
    box-sizing: border-box;
}

#book-list {
    scroll-margin-top: 80px;
}

.books-hero {
    position: relative;
    padding: 72px 0 52px;
}

.books-hero-bg {
    position: absolute;
    inset: 0;
    background-image:
        linear-gradient(rgba(139, 90, 43, 0.045) 1px, transparent 1px),
        linear-gradient(90deg, rgba(139, 90, 43, 0.045) 1px, transparent 1px);
    background-size: 44px 44px;
    mask-image: linear-gradient(to bottom, #000, transparent 78%);
    pointer-events: none;
}

.books-hero-grid {
    position: relative;
    display: grid;
    grid-template-columns: minmax(0, 0.68fr) minmax(380px, 1fr);
    gap: 56px;
    align-items: center;
}

.books-eyebrow,
.books-section-kicker {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    width: fit-content;
    padding: 8px 13px;
    border: 1px solid var(--books-line);
    border-radius: 999px;
    background: rgba(255, 255, 255, 0.78);
    color: var(--books-primary);
    font-weight: 950;
    font-size: 0.78rem;
    letter-spacing: 0.02em;
}

.books-hero-content h1 {
    max-width: 820px;
    margin: 20px 0 18px;
    font-family: 'Inter', sans-serif;
    font-size: clamp(3rem, 6vw, 5.6rem);
    line-height: 0.96;
    letter-spacing: -0.06em;
    color: var(--books-ink);
}

.books-hero-content h1 span {
    color: var(--books-primary);
}

.books-hero-content p {
    max-width: 660px;
    margin: 0;
    color: var(--books-muted);
    font-size: 1.07rem;
    line-height: 1.75;
}

.books-search-card {
    display: flex;
    align-items: center;
    gap: 12px;
    max-width: 760px;
    margin-top: 30px;
    padding: 8px;
    border: 1px solid var(--books-line);
    border-radius: 999px;
    background: rgba(255, 255, 255, 0.88);
    box-shadow: var(--books-shadow-soft);
}

.books-search-input {
    flex: 1;
    min-width: 0;
    display: flex;
    align-items: center;
    gap: 11px;
    padding-left: 12px;
}

.books-search-input i {
    color: var(--books-primary);
}

.books-search-input input {
    width: 100%;
    min-width: 0;
    height: 44px;
    border: 0;
    outline: 0;
    background: transparent;
    color: var(--books-ink);
    font-size: 0.96rem;
}

.books-search-input input::placeholder {
    color: #9b8c7d;
}

.books-search-card button {
    min-height: 46px;
    padding: 0 20px;
    border: 0;
    border-radius: 999px;
    background: var(--books-ink);
    color: #fff;
    font-weight: 950;
    cursor: pointer;
    transition: 0.25s ease;
}

.books-search-card button:hover {
    background: var(--books-primary);
    transform: translateY(-1px);
}

.books-hero-actions {
    display: flex;
    align-items: center;
    gap: 12px;
    margin-top: 24px;
    flex-wrap: wrap;
}

.books-btn {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 9px;
    min-height: 46px;
    padding: 0 20px;
    border-radius: 999px;
    font-weight: 950;
    text-decoration: none;
    border: 1px solid transparent;
    transition: 0.25s ease;
    white-space: nowrap;
}

.books-btn:hover {
    transform: translateY(-2px);
    text-decoration: none;
}

.books-btn-primary {
    background: linear-gradient(135deg, var(--books-primary), var(--books-primary-dark));
    color: #fff;
    box-shadow: 0 14px 28px rgba(95, 53, 20, 0.22);
}

.books-btn-primary:hover {
    color: #fff;
}

.books-btn-light {
    background: rgba(255, 255, 255, 0.78);
    color: var(--books-ink);
    border-color: var(--books-line);
}

.books-btn-light:hover {
    color: var(--books-primary);
    background: #fff;
}

.books-hero-visual {
    position: relative;
    min-height: 560px;
}

.books-hero-image-card {
    position: absolute;
    inset: 0 0 0 28px;
    border-radius: 42px;
    overflow: hidden;
    box-shadow: var(--books-shadow);
    border: 10px solid rgba(255, 255, 255, 0.72);
    transform: rotate(1.5deg);
    background: var(--books-cream);
}

.books-hero-image-card::after {
    content: '';
    position: absolute;
    inset: 0;
    background:
        linear-gradient(180deg, transparent 45%, rgba(32, 22, 15, 0.46)),
        radial-gradient(circle at 20% 10%, rgba(255, 255, 255, 0.4), transparent 38%);
}

.books-hero-image-card img {
    width: 100%;
    height: 100%;
    display: block;
    object-fit: cover;
}

.books-floating-card {
    position: absolute;
    z-index: 3;
    display: flex;
    align-items: center;
    gap: 12px;
    max-width: 260px;
    padding: 14px 16px;
    border: 1px solid var(--books-line);
    border-radius: 22px;
    background: rgba(255, 255, 255, 0.9);
    backdrop-filter: blur(16px);
    box-shadow: var(--books-shadow-soft);
}

.books-floating-card i {
    width: 42px;
    height: 42px;
    display: grid;
    place-items: center;
    flex: 0 0 auto;
    border-radius: 15px;
    background: var(--books-cream);
    color: var(--books-primary);
}

.books-floating-card strong,
.books-floating-card span {
    display: block;
}

.books-floating-card strong {
    color: var(--books-ink);
    font-size: 0.94rem;
}

.books-floating-card span {
    margin-top: 2px;
    color: var(--books-muted);
    font-size: 0.8rem;
}

.books-floating-top {
    top: 38px;
    left: 0;
}

.books-floating-bottom {
    right: -8px;
    bottom: 54px;
}

.books-filter-section {
    padding: 12px 0 52px;
}

.books-filter-card {
    padding: 24px;
    border: 1px solid var(--books-line);
    border-radius: 34px;
    background: rgba(255, 255, 255, 0.78);
    box-shadow: var(--books-shadow-soft);
    backdrop-filter: blur(18px);
}

.books-filter-head {
    display: flex;
    align-items: end;
    justify-content: space-between;
    gap: 24px;
    margin-bottom: 18px;
}

.books-filter-head span {
    display: inline-flex;
    width: fit-content;
    padding: 7px 11px;
    border-radius: 999px;
    background: var(--books-cream);
    color: var(--books-primary);
    font-size: 0.76rem;
    font-weight: 950;
}

.books-filter-head h2 {
    margin: 10px 0 0;
    color: var(--books-ink);
    font-size: 1.35rem;
    font-weight: 950;
    letter-spacing: -0.02em;
}

.books-result-chip {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    min-height: 42px;
    padding: 0 14px;
    border-radius: 999px;
    background: var(--books-ink);
    color: #fff;
    font-size: 0.86rem;
    font-weight: 900;
    white-space: nowrap;
}

.books-category-pills {
    display: flex;
    align-items: center;
    gap: 10px;
    flex-wrap: wrap;
}

.books-category-pill {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    min-height: 42px;
    padding: 0 14px;
    border: 1px solid var(--books-line);
    border-radius: 999px;
    background: #fff;
    color: var(--books-muted);
    font-size: 0.88rem;
    font-weight: 900;
    text-decoration: none;
    transition: 0.25s ease;
}

.books-category-pill:hover,
.books-category-pill.is-active {
    background: linear-gradient(135deg, var(--books-primary), var(--books-primary-dark));
    color: #fff;
    border-color: transparent;
    text-decoration: none;
    transform: translateY(-2px);
}

.books-active-filter {
    display: flex;
    align-items: center;
    gap: 10px;
    flex-wrap: wrap;
    margin-top: 18px;
    padding-top: 18px;
    border-top: 1px solid var(--books-line);
    color: var(--books-muted);
    font-size: 0.9rem;
    font-weight: 850;
}

.books-active-filter span,
.books-active-filter strong,
.books-active-filter a {
    display: inline-flex;
    align-items: center;
    gap: 7px;
    min-height: 34px;
    padding: 0 11px;
    border-radius: 999px;
}

.books-active-filter span {
    padding-left: 0;
}

.books-active-filter strong {
    background: var(--books-cream);
    color: var(--books-primary);
}

.books-active-filter a {
    background: #fff1f2;
    color: #9f1239;
    text-decoration: none;
    font-weight: 950;
}

.books-list-section,
.books-explore-section {
    padding: 76px 0;
}

.books-list-section {
    background:
        radial-gradient(circle at 15% 8%, rgba(216, 154, 69, 0.14), transparent 32%),
        linear-gradient(180deg, rgba(255, 248, 237, 0.82), rgba(255, 255, 255, 0.86));
}

.books-section-head {
    display: flex;
    align-items: end;
    justify-content: space-between;
    gap: 24px;
    margin-bottom: 30px;
}

.books-section-head h2 {
    margin: 12px 0 8px;
    font-family: 'Inter', sans-serif;
    color: var(--books-ink);
    font-size: clamp(2rem, 4vw, 3.35rem);
    line-height: 1;
    letter-spacing: -0.045em;
}

.books-section-head p {
    margin: 0;
    color: var(--books-muted);
    line-height: 1.7;
}

.books-mini-stats {
    display: flex;
    gap: 10px;
    flex-wrap: wrap;
    justify-content: flex-end;
}

.books-mini-stats div {
    min-width: 116px;
    padding: 14px 16px;
    border: 1px solid var(--books-line);
    border-radius: 22px;
    background: rgba(255, 255, 255, 0.78);
    box-shadow: var(--books-shadow-soft);
}

.books-mini-stats strong,
.books-mini-stats span {
    display: block;
}

.books-mini-stats strong {
    color: var(--books-primary);
    font-size: 1.4rem;
    line-height: 1;
    font-weight: 950;
}

.books-mini-stats span {
    margin-top: 6px;
    color: var(--books-muted);
    font-size: 0.78rem;
    font-weight: 850;
}

.books-grid {
    display: grid;
    grid-template-columns: repeat(4, minmax(0, 1fr));
    gap: 22px;
}

.books-card {
    position: relative;
    display: flex;
    flex-direction: column;
    min-height: 100%;
    border: 1px solid var(--books-line);
    border-radius: 30px;
    background: var(--books-card);
    box-shadow: 0 16px 42px rgba(67, 43, 22, 0.07);
    overflow: hidden;
    transition: 0.28s ease;
}

.books-card:hover {
    transform: translateY(-8px);
    box-shadow: var(--books-shadow);
    border-color: rgba(139, 90, 43, 0.22);
}

.books-status {
    position: absolute;
    z-index: 3;
    top: 14px;
    left: 14px;
    display: inline-flex;
    align-items: center;
    gap: 6px;
    padding: 7px 11px;
    border-radius: 999px;
    color: #fff;
    font-size: 0.72rem;
    font-weight: 950;
    backdrop-filter: blur(12px);
}

.books-status.is-available {
    background: linear-gradient(135deg, #15803d, #16a34a);
}

.books-status.is-unavailable {
    background: linear-gradient(135deg, #6b7280, #4b5563);
}

.books-wishlist {
    position: absolute;
    z-index: 3;
    top: 14px;
    right: 14px;
    width: 38px;
    height: 38px;
    display: grid;
    place-items: center;
    border-radius: 999px;
    border: 1px solid rgba(255, 255, 255, 0.46);
    background: rgba(255, 255, 255, 0.84);
    color: var(--books-ink);
    cursor: pointer;
    transition: 0.25s ease;
}

.books-wishlist:hover {
    color: #c2410c;
    transform: scale(1.05);
}

.books-cover {
    display: grid;
    place-items: center;
    height: 286px;
    padding: 26px 34px;
    background:
        radial-gradient(circle at 50% 22%, rgba(216, 154, 69, 0.25), transparent 44%),
        linear-gradient(180deg, #fff8ed, #ead6ba);
    text-decoration: none;
}

.books-cover img {
    width: 100%;
    height: 100%;
    object-fit: contain;
    filter: drop-shadow(0 18px 20px rgba(54, 33, 16, 0.24));
    transition: 0.3s ease;
}

.books-card:hover .books-cover img {
    transform: translateY(-5px) scale(1.025);
}

.books-card-body {
    display: flex;
    flex-direction: column;
    flex: 1;
    padding: 20px;
}

.books-card-category {
    width: fit-content;
    padding: 6px 10px;
    border-radius: 999px;
    background: var(--books-cream);
    color: var(--books-primary);
    font-weight: 950;
    font-size: 0.72rem;
}

.books-card-body h3 {
    margin: 13px 0 7px;
    color: var(--books-ink);
    font-size: 1.04rem;
    line-height: 1.35;
    font-weight: 950;
}

.books-card-body h3 a {
    color: var(--books-ink);
    text-decoration: none;
}

.books-card-body h3 a:hover {
    color: var(--books-primary);
}

.books-author {
    display: flex;
    align-items: center;
    gap: 7px;
    margin: 0 0 12px;
    color: var(--books-muted);
    font-size: 0.86rem;
}

.books-rating {
    display: flex;
    align-items: center;
    gap: 8px;
    margin-top: auto;
    color: var(--books-muted);
    font-size: 0.82rem;
}

.books-stars {
    display: inline-flex;
    gap: 2px;
    color: #d89a32;
}

.books-card-footer {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 14px;
    margin-top: 18px;
    padding-top: 16px;
    border-top: 1px solid var(--books-line);
}

.books-price strong,
.books-price span {
    display: block;
}

.books-price strong {
    color: var(--books-primary);
    font-size: 1.14rem;
    font-weight: 950;
}

.books-price span {
    margin-top: 1px;
    color: var(--books-muted);
    font-size: 0.78rem;
    font-weight: 800;
}

.books-rent-btn {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 7px;
    min-height: 38px;
    padding: 0 14px;
    border-radius: 999px;
    background: var(--books-ink);
    color: #fff;
    font-size: 0.82rem;
    font-weight: 950;
    text-decoration: none;
    transition: 0.25s ease;
}

.books-rent-btn:hover {
    background: var(--books-primary);
    color: #fff;
    text-decoration: none;
    transform: translateY(-2px);
}

.books-sold-out {
    color: #b91c1c;
    font-size: 0.8rem;
    font-weight: 950;
}

.books-empty-state {
    max-width: 620px;
    margin: 0 auto;
    padding: 54px 28px;
    border: 1px dashed rgba(139, 90, 43, 0.26);
    border-radius: 34px;
    background: rgba(255, 255, 255, 0.78);
    box-shadow: var(--books-shadow-soft);
    text-align: center;
}

.books-empty-icon {
    width: 76px;
    height: 76px;
    display: grid;
    place-items: center;
    margin: 0 auto 18px;
    border-radius: 26px;
    background: var(--books-cream);
    color: var(--books-primary);
    font-size: 2rem;
}

.books-empty-state h3 {
    margin: 0 0 8px;
    color: var(--books-ink);
    font-size: 1.35rem;
    font-weight: 950;
}

.books-empty-state p {
    margin: 0 auto 22px;
    max-width: 430px;
    color: var(--books-muted);
    line-height: 1.7;
}

.books-explore-section {
    background: #fff;
}

.books-explore-grid {
    display: grid;
    grid-template-columns: repeat(3, minmax(0, 1fr));
    gap: 16px;
}

.books-explore-card {
    display: flex;
    align-items: center;
    gap: 14px;
    min-height: 104px;
    padding: 18px;
    border: 1px solid var(--books-line);
    border-radius: 28px;
    background:
        radial-gradient(circle at 100% 0%, rgba(216, 154, 69, 0.12), transparent 35%),
        rgba(255, 255, 255, 0.82);
    color: var(--books-ink);
    box-shadow: var(--books-shadow-soft);
    text-decoration: none;
    transition: 0.25s ease;
}

.books-explore-card:hover {
    transform: translateY(-6px);
    color: var(--books-ink);
    text-decoration: none;
    box-shadow: var(--books-shadow);
}

.books-explore-icon {
    width: 52px;
    height: 52px;
    display: grid;
    place-items: center;
    flex: 0 0 auto;
    border-radius: 18px;
    background: var(--books-cream);
    color: var(--books-primary);
    font-size: 1.16rem;
}

.books-explore-card h3 {
    margin: 0 0 4px;
    color: var(--books-ink);
    font-size: 1rem;
    font-weight: 950;
}

.books-explore-card p {
    margin: 0;
    color: var(--books-muted);
    font-size: 0.86rem;
}

.books-explore-arrow {
    margin-left: auto;
    color: var(--books-primary);
}

.reveal-on-scroll {
    opacity: 0;
    transform: translateY(18px);
    transition: opacity 0.6s ease, transform 0.6s ease;
}

.reveal-on-scroll.is-visible {
    opacity: 1;
    transform: translateY(0);
}

@media (max-width: 1180px) {
    .books-hero-grid {
        grid-template-columns: 1fr;
        gap: 42px;
    }

    .books-hero-visual {
        min-height: 420px;
    }

    .books-hero-image-card {
        inset: 0;
    }

    .books-floating-bottom {
        right: 20px;
    }

    .books-grid {
        grid-template-columns: repeat(3, minmax(0, 1fr));
    }

    .books-explore-grid {
        grid-template-columns: repeat(2, minmax(0, 1fr));
    }
}

@media (max-width: 900px) {
    .books-grid {
        grid-template-columns: repeat(2, minmax(0, 1fr));
    }

    .books-section-head,
    .books-filter-head {
        align-items: flex-start;
        flex-direction: column;
    }

    .books-mini-stats {
        justify-content: flex-start;
        width: 100%;
    }

    .books-result-chip {
        width: fit-content;
    }
}

@media (max-width: 768px) {
    .books-hero {
        padding: 58px 0 38px;
    }

    .books-hero-content h1 {
        font-size: clamp(2.65rem, 13vw, 4rem);
    }

    .books-hero-content p {
        font-size: 1rem;
    }

    .books-search-card {
        align-items: stretch;
        flex-direction: column;
        border-radius: 26px;
        padding: 12px;
    }

    .books-search-input {
        width: 100%;
        min-height: 46px;
        padding: 0 8px;
    }

    .books-search-card button {
        width: 100%;
    }

    .books-hero-visual {
        min-height: 350px;
    }

    .books-hero-image-card {
        border-radius: 32px;
        border-width: 7px;
        transform: none;
    }

    .books-floating-card {
        display: none;
    }

    .books-filter-section {
        padding-bottom: 36px;
    }

    .books-filter-card {
        padding: 18px;
        border-radius: 28px;
    }

    .books-list-section,
    .books-explore-section {
        padding: 58px 0;
    }

    .books-grid,
    .books-explore-grid {
        grid-template-columns: 1fr;
    }

    .books-card-footer {
        align-items: flex-start;
        flex-direction: column;
    }

    .books-rent-btn {
        width: 100%;
    }

    .books-btn,
    .books-hero-actions,
    .books-hero-actions .books-btn {
        width: 100%;
    }
}

@media (max-width: 480px) {
    .books-cover {
        height: 260px;
    }

    .books-mini-stats {
        display: grid;
        grid-template-columns: 1fr 1fr;
    }

    .books-mini-stats div {
        min-width: 0;
    }
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function () {
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
            threshold: 0.12
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