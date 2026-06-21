<?php
require_once __DIR__ . '/../templates/header.php';
require_once __DIR__ . '/../../backend/controllers/BookController.php';
require_once __DIR__ . '/../../backend/models/Review.php';

$bookController = new BookController();
$reviewModel = new Review();
$bookId = isset($_GET['id']) ? intval($_GET['id']) : 0;
$book = $bookController->show($bookId);

if (!$book) {
    header('Location: books.php');
    exit;
}

$pageTitle = $book['title'];
$isAvailable = ((int)($book['quantity'] ?? 0)) > 0;
$message = '';
$messageType = '';
$userCanReview = false;
$userReview = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    if (!$isLoggedIn) {
        header('Location: login.php?redirect=book-detail.php?id=' . $bookId);
        exit;
    }

    if ($_POST['action'] === 'rent_now') {
        $rentalDays = intval($_POST['rental_days']);
        require_once __DIR__ . '/../../backend/controllers/RentalController.php';

        $rentalController = new RentalController();
        $result = $rentalController->create($_SESSION['user_id'], $bookId, $rentalDays);

        if ($result['success']) {
            $message = 'Thuê sách thành công! Bạn có thể xem trong mục thuê của tôi.';
            $messageType = 'success';
        } else {
            $message = $result['message'];
            $messageType = 'danger';
        }
    } elseif ($_POST['action'] === 'add_to_cart') {
        require_once __DIR__ . '/../../backend/models/Cart.php';

        $cartModel = new Cart();
        $rentalDays = intval($_POST['rental_days']);

        if (isset($_SESSION['user_id'])) {
            $cartModel->addItem($bookId, 1, $rentalDays, $_SESSION['user_id']);
        } else {
            if (!isset($_SESSION['cart'])) {
                $_SESSION['cart'] = [];
            }

            $_SESSION['cart'][] = [
                'book_id' => $bookId,
                'rental_days' => $rentalDays,
                'quantity' => 1
            ];
        }

        $message = 'Đã thêm vào giỏ hàng!';
        $messageType = 'success';
    } elseif ($_POST['action'] === 'submit_review') {
        $userId = intval($_SESSION['user_id']);
        $ratingInput = intval($_POST['rating'] ?? 0);
        $comment = trim($_POST['comment'] ?? '');

        if (!$reviewModel->hasUserRentedBook($userId, $bookId)) {
            $message = 'Bạn cần thuê sách này trước khi đánh giá.';
            $messageType = 'danger';
        } elseif ($ratingInput < 1 || $ratingInput > 5) {
            $message = 'Vui lòng chọn số sao hợp lệ (1-5).';
            $messageType = 'danger';
        } elseif ($comment === '') {
            $message = 'Vui lòng nhập nội dung bình luận.';
            $messageType = 'danger';
        } else {
            $saved = $reviewModel->saveUserReview($userId, $bookId, $ratingInput, $comment);

            if ($saved) {
                $message = 'Đã gửi đánh giá thành công.';
                $messageType = 'success';
            } else {
                $message = 'Không thể gửi đánh giá, vui lòng thử lại.';
                $messageType = 'danger';
            }
        }
    }
}

$defaultDays = 7;
$pricePerDay = (float)($book['price_per_day'] ?? 0);
$totalPrice = $pricePerDay * $defaultDays;

$reviewSummary = $reviewModel->getBookReviewSummary($bookId);
$bookReviews = $reviewModel->getBookReviews($bookId, 20);
$rating = (float)($reviewSummary['avg_rating'] ?? 0);
$reviewCount = (int)($reviewSummary['review_count'] ?? 0);

if ($isLoggedIn) {
    $userId = intval($_SESSION['user_id']);
    $userCanReview = $reviewModel->hasUserRentedBook($userId, $bookId);
    $userReview = $reviewModel->getUserReview($userId, $bookId);
}

$relatedBooks = [];
$allBooks = $bookController->category($book['category']);

foreach ($allBooks as $rb) {
    if ((int)$rb['id'] !== $bookId && count($relatedBooks) < 4) {
        $relatedBooks[] = $rb;
    }
}

if (!function_exists('bdText')) {
    function bdText($value, $fallback = '') {
        return htmlspecialchars((string)($value ?? $fallback), ENT_QUOTES, 'UTF-8');
    }
}

if (!function_exists('bdMoney')) {
    function bdMoney($value) {
        return number_format((float)$value, 0) . 'đ';
    }
}

if (!function_exists('bdBookCover')) {
    function bdBookCover($book) {
        return getBookCoverImage($book);
    }
}

if (!function_exists('bdRenderStars')) {
    function bdRenderStars($rating) {
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

if (!function_exists('bdStableRating')) {
    function bdStableRating($id) {
        $seed = abs((int)$id);
        return 4.1 + (($seed % 9) / 10);
    }
}

$bookTitle = bdText($book['title'] ?? '');
$bookAuthor = bdText($book['author'] ?? 'Tác giả');
$bookCategory = bdText($book['category'] ?? 'Sách');
$bookDescription = trim((string)($book['description'] ?? ''));
$bookQuantity = (int)($book['quantity'] ?? 0);
?>

<main class="bd-page">

    <section class="bd-breadcrumb-section">
        <div class="container">
            <nav class="bd-breadcrumb">
                <a href="index.php">Trang chủ</a>
                <i class="fas fa-chevron-right"></i>
                <a href="books.php">Sách</a>
                <i class="fas fa-chevron-right"></i>
                <a href="books.php?category=<?php echo urlencode((string)($book['category'] ?? '')); ?>">
                    <?php echo $bookCategory; ?>
                </a>
                <i class="fas fa-chevron-right"></i>
                <span><?php echo $bookTitle; ?></span>
            </nav>
        </div>
    </section>

    <?php if ($message): ?>
        <section class="bd-message-section">
            <div class="container">
                <div class="bd-alert bd-alert-<?php echo bdText($messageType); ?>">
                    <i class="fas fa-<?php echo $messageType === 'success' ? 'check-circle' : 'exclamation-circle'; ?>"></i>
                    <span><?php echo bdText($message); ?></span>
                </div>
            </div>
        </section>
    <?php endif; ?>

    <section class="bd-hero-section">
        <div class="bd-hero-bg"></div>

        <div class="container">
            <div class="bd-detail-grid">

                <aside class="bd-cover-column">
                    <div class="bd-cover-card">
                        <span class="bd-status-badge <?php echo $isAvailable ? 'is-available' : 'is-unavailable'; ?>">
                            <i class="fas fa-<?php echo $isAvailable ? 'check' : 'times'; ?>"></i>
                            <?php echo $isAvailable ? 'Còn hàng' : 'Hết hàng'; ?>
                        </span>

                        <div class="bd-cover-frame">
                            <img
                                src="<?php echo bdText(bdBookCover($book)); ?>"
                                alt="<?php echo $bookTitle; ?>"
                                class="bd-cover-image"
                            >
                        </div>

                        <div class="bd-cover-note">
                            <i class="fas fa-shield-alt"></i>
                            <span>Sách được kiểm tra trước khi giao</span>
                        </div>
                    </div>
                </aside>

                <section class="bd-info-column">
                    <div class="bd-category-pill">
                        <i class="fas fa-layer-group"></i>
                        <?php echo $bookCategory; ?>
                    </div>

                    <h1><?php echo $bookTitle; ?></h1>

                    <p class="bd-author">
                        <i class="fas fa-user-edit"></i>
                        <?php echo $bookAuthor; ?>
                    </p>

                    <div class="bd-rating-row">
                        <div class="bd-stars">
                            <?php echo bdRenderStars($rating); ?>
                        </div>

                        <strong>
                            <?php echo $reviewCount > 0 ? number_format($rating, 1) : 'Chưa có'; ?>
                        </strong>

                        <span>
                            <?php echo number_format($reviewCount); ?> đánh giá
                        </span>
                    </div>

                    <div class="bd-price-row">
                        <div>
                            <span>Giá thuê</span>
                            <strong><?php echo bdMoney($pricePerDay); ?></strong>
                            <small>/ ngày</small>
                        </div>

                        <div>
                            <span>Tình trạng</span>
                            <strong><?php echo $bookQuantity; ?> cuốn</strong>
                            <small>có sẵn</small>
                        </div>
                    </div>

                    <div class="bd-description-card">
                        <h2>Giới thiệu sách</h2>
                        <p>
                            <?php
                            echo nl2br(bdText(
                                $bookDescription !== ''
                                    ? $bookDescription
                                    : 'Chưa có mô tả cho cuốn sách này. Thuê ngay để khám phá nội dung bên trong.'
                            ));
                            ?>
                        </p>
                    </div>

                    <div class="bd-meta-grid">
                        <div class="bd-meta-item">
                            <i class="fas fa-box"></i>
                            <div>
                                <span>Số lượng</span>
                                <strong><?php echo $bookQuantity; ?> cuốn</strong>
                            </div>
                        </div>

                        <div class="bd-meta-item">
                            <i class="fas fa-bookmark"></i>
                            <div>
                                <span>Thể loại</span>
                                <strong><?php echo $bookCategory; ?></strong>
                            </div>
                        </div>

                        <div class="bd-meta-item">
                            <i class="fas fa-pen-fancy"></i>
                            <div>
                                <span>Tác giả</span>
                                <strong><?php echo $bookAuthor; ?></strong>
                            </div>
                        </div>

                        <div class="bd-meta-item">
                            <i class="fas fa-calendar-check"></i>
                            <div>
                                <span>Thời gian</span>
                                <strong>7 - 30 ngày</strong>
                            </div>
                        </div>
                    </div>
                </section>

                <aside class="bd-rental-column">
                    <div class="bd-rental-card">
                        <div class="bd-rental-head">
                            <span>Thuê sách</span>
                            <h2>Chọn thời gian</h2>
                        </div>

                        <?php if ($isAvailable): ?>
                            <form method="POST" id="rentalForm" class="bd-rental-form">
                                <input type="hidden" name="action" value="rent_now">
                                <input type="hidden" name="rental_days" id="rental_days" value="7">

                                <label for="bdRentalDays">
                                    <i class="fas fa-calendar-alt"></i>
                                    Thời gian thuê
                                </label>

                                <select id="bdRentalDays" name="rental_days_select" onchange="updatePrice(this.value)">
                                    <option value="7">7 ngày - <?php echo bdMoney($pricePerDay * 7); ?></option>
                                    <option value="14">14 ngày - <?php echo bdMoney($pricePerDay * 14); ?></option>
                                    <option value="30">30 ngày - <?php echo bdMoney($pricePerDay * 30); ?></option>
                                </select>

                                <div class="bd-total-box">
                                    <div>
                                        <span>Tổng thanh toán</span>
                                        <small>
                                            <?php echo bdMoney($pricePerDay); ?> x
                                            <span id="daysDisplay">7</span> ngày
                                        </small>
                                    </div>

                                    <strong id="total-price">
                                        <?php echo bdMoney($totalPrice); ?>
                                    </strong>
                                </div>

                                <button type="submit" class="bd-primary-action">
                                    <i class="fas fa-book-open"></i>
                                    Thuê ngay
                                </button>
                            </form>

                            <form method="POST" class="bd-cart-form">
                                <input type="hidden" name="action" value="add_to_cart">
                                <input type="hidden" name="rental_days" id="rental_days_cart" value="7">

                                <button type="submit" class="bd-secondary-action">
                                    <i class="fas fa-shopping-cart"></i>
                                    Thêm vào giỏ
                                </button>
                            </form>

                            <div class="bd-rental-benefits">
                                <div>
                                    <i class="fas fa-truck"></i>
                                    Giao sách tiện lợi
                                </div>
                                <div>
                                    <i class="fas fa-sync-alt"></i>
                                    Dễ gia hạn
                                </div>
                                <div>
                                    <i class="fas fa-leaf"></i>
                                    Tiết kiệm hơn mua mới
                                </div>
                            </div>
                        <?php else: ?>
                            <div class="bd-unavailable-box">
                                <i class="fas fa-exclamation-circle"></i>
                                <h3>Sách đang hết hàng</h3>
                                <p>Vui lòng quay lại sau hoặc chọn một cuốn sách khác.</p>
                            </div>
                        <?php endif; ?>

                        <a href="books.php" class="bd-back-link">
                            <i class="fas fa-arrow-left"></i>
                            Quay lại danh sách
                        </a>
                    </div>
                </aside>

            </div>
        </div>
    </section>

    <section class="bd-review-section">
        <div class="container">
            <div class="bd-section-head">
                <div>
                    <span>Reviews</span>
                    <h2>Đánh giá & bình luận</h2>
                    <p>Xem cảm nhận của những người đã thuê cuốn sách này.</p>
                </div>

                <div class="bd-review-summary">
                    <strong><?php echo $reviewCount > 0 ? number_format($rating, 1) : '—'; ?></strong>
                    <div>
                        <div class="bd-stars"><?php echo bdRenderStars($rating); ?></div>
                        <span><?php echo number_format($reviewCount); ?> đánh giá</span>
                    </div>
                </div>
            </div>

            <div class="bd-review-layout">
                <div class="bd-review-form-area">
                    <?php if ($isLoggedIn && $userCanReview): ?>
                        <div class="bd-review-form-card">
                            <h3>
                                <?php echo $userReview ? 'Cập nhật đánh giá của bạn' : 'Viết đánh giá của bạn'; ?>
                            </h3>

                            <form method="POST">
                                <input type="hidden" name="action" value="submit_review">

                                <div class="bd-form-group">
                                    <label for="rating">Số sao</label>
                                    <select id="rating" name="rating" required>
                                        <?php $selectedRating = intval($userReview['rating'] ?? 5); ?>
                                        <option value="5" <?php echo $selectedRating === 5 ? 'selected' : ''; ?>>5 sao - Rất tốt</option>
                                        <option value="4" <?php echo $selectedRating === 4 ? 'selected' : ''; ?>>4 sao - Tốt</option>
                                        <option value="3" <?php echo $selectedRating === 3 ? 'selected' : ''; ?>>3 sao - Ổn</option>
                                        <option value="2" <?php echo $selectedRating === 2 ? 'selected' : ''; ?>>2 sao - Chưa tốt</option>
                                        <option value="1" <?php echo $selectedRating === 1 ? 'selected' : ''; ?>>1 sao - Kém</option>
                                    </select>
                                </div>

                                <div class="bd-form-group">
                                    <label for="comment">Bình luận</label>
                                    <textarea
                                        id="comment"
                                        name="comment"
                                        rows="4"
                                        placeholder="Chia sẻ cảm nhận của bạn..."
                                        required
                                    ><?php echo bdText($userReview['comment'] ?? ''); ?></textarea>
                                </div>

                                <button type="submit" class="bd-primary-action is-small">
                                    <i class="fas fa-paper-plane"></i>
                                    <?php echo $userReview ? 'Cập nhật đánh giá' : 'Gửi đánh giá'; ?>
                                </button>
                            </form>
                        </div>
                    <?php elseif ($isLoggedIn): ?>
                        <div class="bd-review-note">
                            <i class="fas fa-info-circle"></i>
                            <div>
                                <h3>Chưa thể đánh giá</h3>
                                <p>Bạn cần thuê sách này trước khi viết đánh giá.</p>
                            </div>
                        </div>
                    <?php else: ?>
                        <div class="bd-review-note">
                            <i class="fas fa-sign-in-alt"></i>
                            <div>
                                <h3>Đăng nhập để đánh giá</h3>
                                <p>Vui lòng đăng nhập và thuê sách để chia sẻ cảm nhận.</p>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>

                <div class="bd-review-list-area">
                    <?php if (!empty($bookReviews)): ?>
                        <div class="bd-review-list">
                            <?php foreach ($bookReviews as $review): ?>
                                <article class="bd-review-card">
                                    <div class="bd-review-top">
                                        <div class="bd-review-user">
                                            <div class="bd-avatar">
                                                <?php
                                                $displayName = (string)($review['full_name'] ?: $review['username'] ?: 'U');
                                                echo bdText(mb_substr($displayName, 0, 1, 'UTF-8'));
                                                ?>
                                            </div>

                                            <div>
                                                <strong>
                                                    <?php echo bdText($review['full_name'] ?: $review['username']); ?>
                                                </strong>
                                                <span>
                                                    <?php echo date('d/m/Y H:i', strtotime($review['created_at'])); ?>
                                                </span>
                                            </div>
                                        </div>

                                        <div class="bd-review-stars">
                                            <?php echo bdRenderStars((float)$review['rating']); ?>
                                        </div>
                                    </div>

                                    <p><?php echo nl2br(bdText($review['comment'])); ?></p>
                                </article>
                            <?php endforeach; ?>
                        </div>
                    <?php else: ?>
                        <div class="bd-empty-state">
                            <i class="fas fa-comment-dots"></i>
                            <h3>Chưa có đánh giá</h3>
                            <p>Hãy là người đầu tiên chia sẻ trải nghiệm về cuốn sách này.</p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </section>

    <?php if (count($relatedBooks) > 0): ?>
        <section class="bd-related-section">
            <div class="container">
                <div class="bd-section-head">
                    <div>
                        <span>Cùng thể loại</span>
                        <h2>Sách liên quan</h2>
                        <p>Thêm lựa chọn từ danh mục <?php echo $bookCategory; ?>.</p>
                    </div>

                    <a href="books.php?category=<?php echo urlencode((string)($book['category'] ?? '')); ?>" class="bd-view-more">
                        Xem thêm
                        <i class="fas fa-arrow-right"></i>
                    </a>
                </div>

                <div class="bd-related-grid">
                    <?php foreach ($relatedBooks as $related): ?>
                        <?php
                        $relatedId = (int)($related['id'] ?? 0);
                        $relatedAvailable = ((int)($related['quantity'] ?? 0)) > 0;
                        $relatedRating = bdStableRating($relatedId);
                        ?>
                        <article class="bd-related-card">
                            <span class="bd-related-status <?php echo $relatedAvailable ? 'is-available' : 'is-unavailable'; ?>">
                                <?php echo $relatedAvailable ? 'Còn hàng' : 'Hết hàng'; ?>
                            </span>

                            <a href="book-detail.php?id=<?php echo $relatedId; ?>" class="bd-related-image">
                                <img
                                    src="<?php echo bdText(bdBookCover($related)); ?>"
                                    alt="<?php echo bdText($related['title'] ?? 'Sách'); ?>"
                                    loading="lazy"
                                >
                            </a>

                            <div class="bd-related-body">
                                <span><?php echo bdText($related['category'] ?? 'Sách'); ?></span>

                                <h3>
                                    <a href="book-detail.php?id=<?php echo $relatedId; ?>">
                                        <?php echo bdText($related['title'] ?? ''); ?>
                                    </a>
                                </h3>

                                <p><?php echo bdText($related['author'] ?? 'Tác giả'); ?></p>

                                <div class="bd-related-rating">
                                    <div class="bd-stars"><?php echo bdRenderStars($relatedRating); ?></div>
                                    <small><?php echo number_format($relatedRating, 1); ?></small>
                                </div>

                                <div class="bd-related-footer">
                                    <strong><?php echo bdMoney($related['price_per_day'] ?? 0); ?><span>/ngày</span></strong>

                                    <?php if ($relatedAvailable): ?>
                                        <a href="book-detail.php?id=<?php echo $relatedId; ?>">Thuê</a>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </article>
                    <?php endforeach; ?>
                </div>
            </div>
        </section>
    <?php endif; ?>

</main>

<style>
.bd-page {
    --bd-primary: #8b5a2b;
    --bd-primary-dark: #5a3518;
    --bd-accent: #d89a45;
    --bd-cream: #fff8ed;
    --bd-cream-2: #f5e6d0;
    --bd-ink: #1f1711;
    --bd-muted: #74685f;
    --bd-soft: rgba(255, 255, 255, 0.82);
    --bd-line: rgba(92, 57, 24, 0.13);
    --bd-shadow: 0 24px 70px rgba(55, 34, 18, 0.13);
    --bd-shadow-soft: 0 14px 34px rgba(55, 34, 18, 0.08);
    background:
        radial-gradient(circle at 6% 0%, rgba(216, 154, 69, 0.2), transparent 30%),
        radial-gradient(circle at 92% 8%, rgba(139, 90, 43, 0.11), transparent 25%),
        linear-gradient(180deg, #fffaf3 0%, #fff 45%, #fff8ed 100%);
    color: var(--bd-ink);
    overflow: hidden;
}

.bd-page * {
    box-sizing: border-box;
}

.bd-breadcrumb-section {
    padding: 26px 0 8px;
}

.bd-breadcrumb {
    display: flex;
    align-items: center;
    gap: 10px;
    flex-wrap: wrap;
    color: var(--bd-muted);
    font-size: 0.9rem;
}

.bd-breadcrumb a {
    color: var(--bd-muted);
    text-decoration: none;
    font-weight: 700;
}

.bd-breadcrumb a:hover {
    color: var(--bd-primary);
}

.bd-breadcrumb i {
    font-size: 0.72rem;
    opacity: 0.55;
}

.bd-breadcrumb span {
    color: var(--bd-ink);
    font-weight: 900;
}

.bd-message-section {
    padding: 12px 0 0;
}

.bd-alert {
    display: flex;
    align-items: center;
    gap: 12px;
    padding: 16px 18px;
    border-radius: 22px;
    border: 1px solid var(--bd-line);
    background: #fff;
    box-shadow: var(--bd-shadow-soft);
    font-weight: 800;
}

.bd-alert-success {
    color: #166534;
    background: #f0fdf4;
    border-color: rgba(22, 101, 52, 0.14);
}

.bd-alert-danger {
    color: #991b1b;
    background: #fff1f2;
    border-color: rgba(153, 27, 27, 0.14);
}

.bd-hero-section {
    position: relative;
    padding: 42px 0 84px;
}

.bd-hero-bg {
    position: absolute;
    inset: 0;
    background-image:
        linear-gradient(rgba(139, 90, 43, 0.045) 1px, transparent 1px),
        linear-gradient(90deg, rgba(139, 90, 43, 0.045) 1px, transparent 1px);
    background-size: 44px 44px;
    mask-image: linear-gradient(to bottom, #000, transparent 78%);
    pointer-events: none;
}

.bd-detail-grid {
    position: relative;
    display: grid;
    grid-template-columns: minmax(280px, 0.82fr) minmax(0, 1.15fr) minmax(300px, 0.72fr);
    gap: 28px;
    align-items: start;
}

.bd-cover-column,
.bd-rental-column {
    position: sticky;
    top: 96px;
}

.bd-cover-card,
.bd-rental-card,
.bd-description-card,
.bd-review-form-card,
.bd-review-note,
.bd-review-card,
.bd-empty-state,
.bd-related-card {
    border: 1px solid var(--bd-line);
    background: var(--bd-soft);
    backdrop-filter: blur(18px);
    box-shadow: var(--bd-shadow-soft);
}

.bd-cover-card {
    position: relative;
    padding: 28px;
    border-radius: 36px;
}

.bd-status-badge {
    position: absolute;
    z-index: 3;
    top: 20px;
    left: 20px;
    display: inline-flex;
    align-items: center;
    gap: 7px;
    padding: 8px 12px;
    border-radius: 999px;
    color: #fff;
    font-size: 0.78rem;
    font-weight: 900;
    box-shadow: 0 10px 24px rgba(0, 0, 0, 0.12);
}

.bd-status-badge.is-available,
.bd-related-status.is-available {
    background: linear-gradient(135deg, #15803d, #16a34a);
}

.bd-status-badge.is-unavailable,
.bd-related-status.is-unavailable {
    background: linear-gradient(135deg, #6b7280, #4b5563);
}

.bd-cover-frame {
    display: grid;
    place-items: center;
    min-height: 520px;
    padding: 42px 34px;
    border-radius: 30px;
    background:
        radial-gradient(circle at 50% 20%, rgba(216, 154, 69, 0.28), transparent 42%),
        linear-gradient(180deg, #fff8ed, #ead6ba);
    overflow: hidden;
}

.bd-cover-image {
    width: 100%;
    max-height: 470px;
    object-fit: contain;
    filter: drop-shadow(0 24px 26px rgba(54, 33, 16, 0.28));
    transition: 0.32s ease;
}

.bd-cover-card:hover .bd-cover-image {
    transform: translateY(-8px) scale(1.025);
}

.bd-cover-note {
    display: flex;
    align-items: center;
    gap: 10px;
    margin-top: 18px;
    padding: 14px 16px;
    border-radius: 20px;
    background: rgba(255, 248, 237, 0.8);
    color: var(--bd-muted);
    font-size: 0.9rem;
    font-weight: 700;
}

.bd-cover-note i {
    color: var(--bd-primary);
}

.bd-info-column {
    padding: 4px 0;
}

.bd-category-pill {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    width: fit-content;
    padding: 9px 14px;
    border: 1px solid var(--bd-line);
    border-radius: 999px;
    background: rgba(255, 255, 255, 0.78);
    color: var(--bd-primary);
    font-size: 0.82rem;
    font-weight: 900;
}

.bd-info-column h1 {
    margin: 20px 0 12px;
    font-family: 'Playfair Display', Georgia, serif;
    font-size: clamp(2.8rem, 5.4vw, 5rem);
    line-height: 0.98;
    letter-spacing: -0.055em;
    color: var(--bd-ink);
}

.bd-author {
    display: flex;
    align-items: center;
    gap: 9px;
    margin: 0 0 18px;
    color: var(--bd-muted);
    font-size: 1rem;
    font-weight: 700;
}

.bd-author i {
    color: var(--bd-primary);
}

.bd-rating-row {
    display: flex;
    align-items: center;
    gap: 10px;
    flex-wrap: wrap;
    margin-bottom: 24px;
}

.bd-stars,
.bd-review-stars {
    display: inline-flex;
    gap: 3px;
    color: #d89a32;
}

.bd-rating-row strong {
    color: var(--bd-ink);
    font-size: 1rem;
}

.bd-rating-row span {
    color: var(--bd-muted);
    font-weight: 700;
}

.bd-price-row {
    display: grid;
    grid-template-columns: repeat(2, minmax(0, 1fr));
    gap: 14px;
    margin-bottom: 20px;
}

.bd-price-row > div {
    padding: 20px;
    border-radius: 26px;
    border: 1px solid var(--bd-line);
    background: rgba(255, 255, 255, 0.72);
    box-shadow: 0 10px 26px rgba(55, 34, 18, 0.055);
}

.bd-price-row span,
.bd-price-row small {
    display: block;
    color: var(--bd-muted);
    font-size: 0.82rem;
    font-weight: 700;
}

.bd-price-row strong {
    display: inline-block;
    margin-top: 6px;
    color: var(--bd-primary);
    font-size: 1.8rem;
    font-weight: 950;
    letter-spacing: -0.035em;
}

.bd-description-card {
    padding: 24px;
    border-radius: 30px;
    margin-bottom: 20px;
}

.bd-description-card h2 {
    margin: 0 0 10px;
    color: var(--bd-ink);
    font-size: 1.15rem;
    font-weight: 950;
}

.bd-description-card p {
    margin: 0;
    color: var(--bd-muted);
    line-height: 1.78;
}

.bd-meta-grid {
    display: grid;
    grid-template-columns: repeat(2, minmax(0, 1fr));
    gap: 14px;
}

.bd-meta-item {
    display: flex;
    align-items: center;
    gap: 13px;
    padding: 17px;
    border-radius: 24px;
    border: 1px solid var(--bd-line);
    background: rgba(255, 255, 255, 0.66);
}

.bd-meta-item i {
    width: 44px;
    height: 44px;
    display: grid;
    place-items: center;
    flex: 0 0 auto;
    border-radius: 16px;
    color: var(--bd-primary);
    background: var(--bd-cream);
}

.bd-meta-item span {
    display: block;
    color: var(--bd-muted);
    font-size: 0.78rem;
    font-weight: 800;
}

.bd-meta-item strong {
    display: block;
    margin-top: 3px;
    color: var(--bd-ink);
    font-size: 0.9rem;
    font-weight: 950;
}

.bd-rental-card {
    padding: 22px;
    border-radius: 34px;
}

.bd-rental-head {
    margin-bottom: 18px;
}

.bd-rental-head span {
    display: inline-flex;
    width: fit-content;
    padding: 7px 11px;
    border-radius: 999px;
    background: var(--bd-cream);
    color: var(--bd-primary);
    font-weight: 950;
    font-size: 0.75rem;
}

.bd-rental-head h2 {
    margin: 12px 0 0;
    color: var(--bd-ink);
    font-size: 1.35rem;
    font-weight: 950;
}

.bd-rental-form label,
.bd-form-group label {
    display: flex;
    align-items: center;
    gap: 8px;
    margin-bottom: 9px;
    color: var(--bd-ink);
    font-weight: 900;
    font-size: 0.9rem;
}

.bd-rental-form select,
.bd-form-group select,
.bd-form-group textarea {
    width: 100%;
    border: 1px solid var(--bd-line);
    outline: none;
    border-radius: 18px;
    background: #fff;
    color: var(--bd-ink);
    font-size: 0.95rem;
    font-weight: 750;
    transition: 0.22s ease;
}

.bd-rental-form select,
.bd-form-group select {
    height: 52px;
    padding: 0 15px;
}

.bd-form-group textarea {
    min-height: 118px;
    padding: 14px 15px;
    resize: vertical;
    line-height: 1.6;
}

.bd-rental-form select:focus,
.bd-form-group select:focus,
.bd-form-group textarea:focus {
    border-color: rgba(139, 90, 43, 0.42);
    box-shadow: 0 0 0 4px rgba(139, 90, 43, 0.08);
}

.bd-total-box {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 14px;
    margin: 16px 0;
    padding: 18px;
    border-radius: 24px;
    background:
        radial-gradient(circle at 90% 0%, rgba(216, 154, 69, 0.18), transparent 35%),
        linear-gradient(180deg, #fff8ed, #fff);
    border: 1px solid var(--bd-line);
}

.bd-total-box span,
.bd-total-box small {
    display: block;
}

.bd-total-box span {
    color: var(--bd-muted);
    font-size: 0.82rem;
    font-weight: 800;
}

.bd-total-box small {
    margin-top: 3px;
    color: var(--bd-muted);
    font-size: 0.78rem;
}

.bd-total-box strong {
    color: var(--bd-primary);
    font-size: 1.55rem;
    font-weight: 950;
    white-space: nowrap;
}

.bd-primary-action,
.bd-secondary-action {
    width: 100%;
    min-height: 52px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 9px;
    border: 0;
    border-radius: 999px;
    font-weight: 950;
    cursor: pointer;
    text-decoration: none;
    transition: 0.25s ease;
}

.bd-primary-action {
    background: linear-gradient(135deg, var(--bd-primary), var(--bd-primary-dark));
    color: #fff;
    box-shadow: 0 16px 30px rgba(95, 53, 20, 0.2);
}

.bd-primary-action:hover,
.bd-secondary-action:hover {
    transform: translateY(-2px);
}

.bd-primary-action.is-small {
    width: auto;
    min-height: 46px;
    padding: 0 20px;
}

.bd-cart-form {
    margin-top: 12px;
}

.bd-secondary-action {
    background: #fff;
    color: var(--bd-ink);
    border: 1px solid var(--bd-line);
}

.bd-rental-benefits {
    display: grid;
    gap: 10px;
    margin-top: 18px;
}

.bd-rental-benefits div {
    display: flex;
    align-items: center;
    gap: 9px;
    color: var(--bd-muted);
    font-size: 0.86rem;
    font-weight: 800;
}

.bd-rental-benefits i {
    color: var(--bd-primary);
}

.bd-unavailable-box {
    padding: 26px 18px;
    border-radius: 24px;
    background: #fff1f2;
    color: #991b1b;
    text-align: center;
}

.bd-unavailable-box i {
    font-size: 2rem;
    margin-bottom: 10px;
}

.bd-unavailable-box h3 {
    margin: 0 0 6px;
    font-size: 1.1rem;
}

.bd-unavailable-box p {
    margin: 0;
    line-height: 1.6;
}

.bd-back-link {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 9px;
    width: 100%;
    margin-top: 16px;
    color: var(--bd-muted);
    font-weight: 900;
    text-decoration: none;
}

.bd-back-link:hover {
    color: var(--bd-primary);
    text-decoration: none;
}

.bd-review-section,
.bd-related-section {
    padding: 82px 0;
}

.bd-review-section {
    background:
        radial-gradient(circle at 12% 15%, rgba(216, 154, 69, 0.15), transparent 32%),
        linear-gradient(180deg, rgba(255, 248, 237, 0.86), rgba(255, 255, 255, 0.84));
}

.bd-section-head {
    display: flex;
    align-items: end;
    justify-content: space-between;
    gap: 24px;
    margin-bottom: 28px;
}

.bd-section-head > div:first-child > span {
    display: inline-flex;
    width: fit-content;
    padding: 8px 12px;
    border-radius: 999px;
    background: rgba(255, 255, 255, 0.75);
    border: 1px solid var(--bd-line);
    color: var(--bd-primary);
    font-weight: 950;
    font-size: 0.78rem;
}

.bd-section-head h2 {
    margin: 12px 0 8px;
    font-family: 'Playfair Display', Georgia, serif;
    font-size: clamp(2rem, 4vw, 3.25rem);
    line-height: 1;
    letter-spacing: -0.045em;
    color: var(--bd-ink);
}

.bd-section-head p {
    margin: 0;
    color: var(--bd-muted);
    line-height: 1.7;
}

.bd-review-summary {
    display: flex;
    align-items: center;
    gap: 14px;
    min-width: 210px;
    padding: 16px 18px;
    border-radius: 24px;
    border: 1px solid var(--bd-line);
    background: rgba(255, 255, 255, 0.75);
}

.bd-review-summary > strong {
    font-size: 2rem;
    color: var(--bd-primary);
    font-weight: 950;
}

.bd-review-summary span {
    display: block;
    margin-top: 3px;
    color: var(--bd-muted);
    font-size: 0.82rem;
    font-weight: 800;
}

.bd-review-layout {
    display: grid;
    grid-template-columns: minmax(280px, 0.42fr) minmax(0, 0.58fr);
    gap: 24px;
    align-items: start;
}

.bd-review-form-card,
.bd-review-note {
    padding: 24px;
    border-radius: 30px;
}

.bd-review-form-card h3,
.bd-review-note h3 {
    margin: 0 0 18px;
    color: var(--bd-ink);
    font-size: 1.15rem;
    font-weight: 950;
}

.bd-form-group {
    margin-bottom: 16px;
}

.bd-review-note {
    display: flex;
    gap: 14px;
    align-items: flex-start;
}

.bd-review-note > i {
    width: 46px;
    height: 46px;
    display: grid;
    place-items: center;
    flex: 0 0 auto;
    border-radius: 16px;
    background: var(--bd-cream);
    color: var(--bd-primary);
}

.bd-review-note p {
    margin: 0;
    color: var(--bd-muted);
    line-height: 1.7;
}

.bd-review-list {
    display: grid;
    gap: 14px;
}

.bd-review-card {
    padding: 20px;
    border-radius: 28px;
}

.bd-review-top {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 14px;
    margin-bottom: 14px;
}

.bd-review-user {
    display: flex;
    align-items: center;
    gap: 12px;
}

.bd-avatar {
    width: 44px;
    height: 44px;
    display: grid;
    place-items: center;
    flex: 0 0 auto;
    border-radius: 16px;
    background: linear-gradient(135deg, var(--bd-primary), var(--bd-primary-dark));
    color: #fff;
    font-weight: 950;
}

.bd-review-user strong {
    display: block;
    color: var(--bd-ink);
    font-weight: 950;
}

.bd-review-user span {
    display: block;
    margin-top: 3px;
    color: var(--bd-muted);
    font-size: 0.8rem;
}

.bd-review-card p {
    margin: 0;
    color: var(--bd-muted);
    line-height: 1.75;
}

.bd-empty-state {
    padding: 46px 24px;
    border-radius: 30px;
    text-align: center;
}

.bd-empty-state i {
    width: 70px;
    height: 70px;
    display: grid;
    place-items: center;
    margin: 0 auto 16px;
    border-radius: 24px;
    background: var(--bd-cream);
    color: var(--bd-primary);
    font-size: 1.8rem;
}

.bd-empty-state h3 {
    margin: 0 0 8px;
    color: var(--bd-ink);
    font-weight: 950;
}

.bd-empty-state p {
    margin: 0;
    color: var(--bd-muted);
}

.bd-view-more {
    display: inline-flex;
    align-items: center;
    gap: 9px;
    color: var(--bd-primary);
    font-weight: 950;
    text-decoration: none;
    white-space: nowrap;
}

.bd-view-more:hover {
    color: var(--bd-primary-dark);
    text-decoration: none;
}

.bd-related-grid {
    display: grid;
    grid-template-columns: repeat(4, minmax(0, 1fr));
    gap: 22px;
}

.bd-related-card {
    position: relative;
    overflow: hidden;
    border-radius: 30px;
    transition: 0.28s ease;
}

.bd-related-card:hover {
    transform: translateY(-8px);
    box-shadow: var(--bd-shadow);
}

.bd-related-status {
    position: absolute;
    z-index: 3;
    top: 14px;
    left: 14px;
    display: inline-flex;
    padding: 7px 11px;
    border-radius: 999px;
    color: #fff;
    font-size: 0.72rem;
    font-weight: 950;
}

.bd-related-image {
    display: grid;
    place-items: center;
    height: 260px;
    padding: 26px 32px;
    background:
        radial-gradient(circle at 50% 20%, rgba(216, 154, 69, 0.24), transparent 46%),
        linear-gradient(180deg, #fff8ed, #ead6ba);
}

.bd-related-image img {
    width: 100%;
    height: 100%;
    object-fit: contain;
    filter: drop-shadow(0 17px 18px rgba(54, 33, 16, 0.22));
    transition: 0.3s ease;
}

.bd-related-card:hover .bd-related-image img {
    transform: translateY(-5px) scale(1.025);
}

.bd-related-body {
    padding: 18px;
}

.bd-related-body > span {
    display: inline-flex;
    width: fit-content;
    padding: 6px 10px;
    border-radius: 999px;
    background: var(--bd-cream);
    color: var(--bd-primary);
    font-size: 0.72rem;
    font-weight: 950;
}

.bd-related-body h3 {
    margin: 12px 0 6px;
    font-size: 1rem;
    line-height: 1.35;
    font-weight: 950;
}

.bd-related-body h3 a {
    color: var(--bd-ink);
    text-decoration: none;
}

.bd-related-body h3 a:hover {
    color: var(--bd-primary);
}

.bd-related-body p {
    margin: 0 0 10px;
    color: var(--bd-muted);
    font-size: 0.86rem;
}

.bd-related-rating {
    display: flex;
    align-items: center;
    gap: 7px;
    color: var(--bd-muted);
    font-size: 0.82rem;
}

.bd-related-footer {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 14px;
    margin-top: 16px;
    padding-top: 15px;
    border-top: 1px solid var(--bd-line);
}

.bd-related-footer strong {
    color: var(--bd-primary);
    font-size: 1.08rem;
    font-weight: 950;
}

.bd-related-footer strong span {
    color: var(--bd-muted);
    font-size: 0.78rem;
    font-weight: 800;
}

.bd-related-footer a {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    min-height: 36px;
    padding: 0 14px;
    border-radius: 999px;
    background: var(--bd-ink);
    color: #fff;
    font-size: 0.82rem;
    font-weight: 950;
    text-decoration: none;
    transition: 0.25s ease;
}

.bd-related-footer a:hover {
    background: var(--bd-primary);
    color: #fff;
    text-decoration: none;
    transform: translateY(-2px);
}

@media (max-width: 1200px) {
    .bd-detail-grid {
        grid-template-columns: minmax(280px, 0.85fr) minmax(0, 1.15fr);
    }

    .bd-rental-column {
        grid-column: 1 / -1;
        position: static;
    }

    .bd-rental-card {
        display: grid;
        grid-template-columns: minmax(0, 0.6fr) minmax(280px, 0.4fr);
        gap: 18px;
        align-items: start;
    }

    .bd-rental-head,
    .bd-rental-form,
    .bd-cart-form,
    .bd-rental-benefits,
    .bd-unavailable-box,
    .bd-back-link {
        grid-column: auto;
    }

    .bd-related-grid {
        grid-template-columns: repeat(2, minmax(0, 1fr));
    }
}

@media (max-width: 900px) {
    .bd-detail-grid,
    .bd-review-layout {
        grid-template-columns: 1fr;
    }

    .bd-cover-column {
        position: static;
    }

    .bd-cover-frame {
        min-height: 430px;
    }

    .bd-rental-card {
        display: block;
    }

    .bd-section-head {
        align-items: flex-start;
        flex-direction: column;
    }

    .bd-review-summary {
        width: 100%;
    }
}

@media (max-width: 640px) {
    .bd-breadcrumb-section {
        padding-top: 20px;
    }

    .bd-hero-section {
        padding: 30px 0 58px;
    }

    .bd-cover-card,
    .bd-rental-card,
    .bd-description-card,
    .bd-review-form-card,
    .bd-review-note,
    .bd-review-card,
    .bd-empty-state {
        border-radius: 24px;
    }

    .bd-cover-card {
        padding: 18px;
    }

    .bd-cover-frame {
        min-height: 360px;
        padding: 34px 28px;
        border-radius: 24px;
    }

    .bd-info-column h1 {
        font-size: clamp(2.35rem, 13vw, 3.5rem);
    }

    .bd-price-row,
    .bd-meta-grid,
    .bd-related-grid {
        grid-template-columns: 1fr;
    }

    .bd-review-top {
        align-items: flex-start;
        flex-direction: column;
    }

    .bd-review-section,
    .bd-related-section {
        padding: 58px 0;
    }

    .bd-total-box {
        align-items: flex-start;
        flex-direction: column;
    }
}
</style>

<script>
const pricePerDay = <?php echo json_encode($pricePerDay); ?>;

function updatePrice(days) {
    const selectedDays = parseInt(days, 10);
    const total = pricePerDay * selectedDays;

    const totalPrice = document.getElementById('total-price');
    const daysDisplay = document.getElementById('daysDisplay');
    const rentalDays = document.getElementById('rental_days');
    const rentalDaysCart = document.getElementById('rental_days_cart');

    if (totalPrice) {
        totalPrice.textContent = total.toLocaleString('vi-VN') + 'đ';
    }

    if (daysDisplay) {
        daysDisplay.textContent = selectedDays;
    }

    if (rentalDays) {
        rentalDays.value = selectedDays;
    }

    if (rentalDaysCart) {
        rentalDaysCart.value = selectedDays;
    }
}
</script>

<?php require_once __DIR__ . '/../templates/footer.php'; ?>