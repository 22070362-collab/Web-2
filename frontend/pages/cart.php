<?php
require_once __DIR__ . '/../templates/header.php';

if (!$isLoggedIn) {
    header('Location: login.php?redirect=cart.php');
    exit;
}

$message = '';
$messageType = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    require_once __DIR__ . '/../../backend/models/Cart.php';
    $cartModel = new Cart();

    if ($_POST['action'] === 'remove') {
        $cartModel->removeItem(intval($_POST['cart_id']));
        $message = 'Đã xóa sách khỏi giỏ hàng.';
        $messageType = 'success';
    } elseif ($_POST['action'] === 'clear') {
        $cartModel->clear();
        $message = 'Đã xóa toàn bộ giỏ hàng.';
        $messageType = 'success';
    } elseif ($_POST['action'] === 'checkout') {
        require_once __DIR__ . '/../../backend/controllers/RentalController.php';

        $rentalController = new RentalController();
        $result = $rentalController->checkout($_SESSION['user_id']);

        if ($result['success']) {
            $message = 'Thuê sách thành công! Xem sách đã thuê trong mục Tổng Quan.';
            $messageType = 'success';
        } else {
            $message = $result['message'];
            $messageType = 'danger';
        }
    }
}

require_once __DIR__ . '/../../backend/models/Cart.php';

$cartModel = new Cart();
$cartItems = $cartModel->getItems();
$cartTotal = $cartModel->getTotal();

if (!function_exists('cartText')) {
    function cartText($value, $fallback = '') {
        return htmlspecialchars((string)($value ?? $fallback), ENT_QUOTES, 'UTF-8');
    }
}

if (!function_exists('cartMoney')) {
    function cartMoney($value) {
        return number_format((float)$value, 0) . 'đ';
    }
}

$itemCount = count($cartItems);
$totalRentalDays = 0;

foreach ($cartItems as $cartItem) {
    $totalRentalDays += (int)($cartItem['rental_days'] ?? 0);
}
?>

<main class="cart-page">

    <section class="cart-hero">
        <div class="cart-hero-bg"></div>

        <div class="container">
            <div class="cart-hero-grid">
                <div class="cart-hero-content">
                    <span class="cart-eyebrow">
                        <i class="fas fa-shopping-cart"></i>
                        Giỏ hàng
                    </span>

                    <h1>
                        Xem lại sách
                        <span>trước khi thuê.</span>
                    </h1>

                    <p>
                        Kiểm tra danh sách sách, số ngày thuê và tổng chi phí trước khi xác nhận đơn thuê.
                    </p>
                </div>

                <div class="cart-hero-summary">
                    <div class="cart-summary-mini">
                        <i class="fas fa-book-open"></i>
                        <div>
                            <strong><?php echo number_format($itemCount); ?></strong>
                            <span>Cuốn sách</span>
                        </div>
                    </div>

                    <div class="cart-summary-mini">
                        <i class="fas fa-calendar-check"></i>
                        <div>
                            <strong><?php echo number_format($totalRentalDays); ?></strong>
                            <span>Tổng ngày thuê</span>
                        </div>
                    </div>

                    <div class="cart-summary-mini">
                        <i class="fas fa-wallet"></i>
                        <div>
                            <strong><?php echo cartMoney($cartTotal); ?></strong>
                            <span>Tạm tính</span>
                        </div>
                    </div>
                </div>
            </div>

            <?php if ($message): ?>
                <div class="cart-alert cart-alert-<?php echo cartText($messageType); ?>">
                    <i class="fas fa-<?php echo $messageType === 'success' ? 'check-circle' : 'exclamation-circle'; ?>"></i>
                    <span><?php echo cartText($message); ?></span>
                </div>
            <?php endif; ?>
        </div>
    </section>

    <section class="cart-main-section">
        <div class="container">

            <?php if (empty($cartItems)): ?>
                <div class="cart-empty-card">
                    <div class="cart-empty-icon">
                        <i class="fas fa-shopping-cart"></i>
                    </div>

                    <h2>Giỏ hàng đang trống</h2>
                    <p>Hãy chọn vài cuốn sách bạn muốn đọc và thêm vào giỏ hàng.</p>

                    <a href="books.php" class="cart-btn cart-btn-primary">
                        Khám phá sách
                        <i class="fas fa-arrow-right"></i>
                    </a>
                </div>
            <?php else: ?>

                <div class="cart-layout">
                    <section class="cart-items-panel">
                        <div class="cart-panel-head">
                            <div>
                                <span>Danh sách</span>
                                <h2>Sách trong giỏ</h2>
                            </div>

                            <form method="POST">
                                <input type="hidden" name="action" value="clear">
                                <button
                                    type="submit"
                                    class="cart-clear-btn"
                                    onclick="return confirm('Xóa toàn bộ giỏ hàng?')"
                                >
                                    <i class="fas fa-trash"></i>
                                    Xóa tất cả
                                </button>
                            </form>
                        </div>

                        <div class="cart-items-list">
                            <?php foreach ($cartItems as $item): ?>
                                <?php
                                $itemId = (int)($item['id'] ?? 0);
                                $itemPrice = (float)($item['price_per_day'] ?? 0);
                                $itemDays = (int)($item['rental_days'] ?? 0);
                                $itemTotal = $itemPrice * $itemDays;
                                ?>
                                <article class="cart-item-card">
                                    <a href="book-detail.php?id=<?php echo (int)($item['book_id'] ?? 0); ?>" class="cart-book-cover">
                                        <img
                                            src="<?php echo cartText(getBookCoverImage($item)); ?>"
                                            alt="<?php echo cartText($item['title'] ?? 'Sách'); ?>"
                                            loading="lazy"
                                        >
                                    </a>

                                    <div class="cart-book-info">
                                        <span class="cart-book-category">
                                            <?php echo cartText($item['category'] ?? 'Sách'); ?>
                                        </span>

                                        <h3>
                                            <a href="book-detail.php?id=<?php echo (int)($item['book_id'] ?? 0); ?>">
                                                <?php echo cartText($item['title'] ?? ''); ?>
                                            </a>
                                        </h3>

                                        <p>
                                            <i class="fas fa-user-edit"></i>
                                            <?php echo cartText($item['author'] ?? 'Tác giả'); ?>
                                        </p>

                                        <div class="cart-mobile-total">
                                            <?php echo cartMoney($itemTotal); ?>
                                        </div>
                                    </div>

                                    <div class="cart-book-meta">
                                        <div>
                                            <span>Giá/ngày</span>
                                            <strong><?php echo cartMoney($itemPrice); ?></strong>
                                        </div>

                                        <div>
                                            <span>Số ngày</span>
                                            <strong><?php echo number_format($itemDays); ?> ngày</strong>
                                        </div>

                                        <div>
                                            <span>Tổng</span>
                                            <strong class="cart-item-total"><?php echo cartMoney($itemTotal); ?></strong>
                                        </div>
                                    </div>

                                    <form method="POST" class="cart-remove-form">
                                        <input type="hidden" name="action" value="remove">
                                        <input type="hidden" name="cart_id" value="<?php echo $itemId; ?>">
                                        <button type="submit" class="cart-remove-btn" title="Xóa sách">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </article>
                            <?php endforeach; ?>
                        </div>
                    </section>

                    <aside class="cart-checkout-panel">
                        <div class="cart-checkout-card">
                            <div class="cart-checkout-head">
                                <span>Thanh toán</span>
                                <h2>Tóm tắt đơn thuê</h2>
                            </div>

                            <div class="cart-coupon-box">
                                <label for="couponCode">
                                    <i class="fas fa-tag"></i>
                                    Mã ưu đãi
                                </label>

                                <div class="cart-coupon-row">
                                    <input
                                        type="text"
                                        id="couponCode"
                                        placeholder="WELCOME10"
                                        autocomplete="off"
                                    >
                                    <button type="button" onclick="applyCoupon()">
                                        Áp dụng
                                    </button>
                                </div>

                                <small id="couponMessage">Nhập mã nếu bạn có ưu đãi.</small>
                            </div>

                            <div class="cart-total-box">
                                <div class="cart-total-row">
                                    <span>Tạm tính</span>
                                    <strong id="cartSubtotal"><?php echo cartMoney($cartTotal); ?></strong>
                                </div>

                                <div class="cart-total-row discount-row" id="discountRow" style="display: none;">
                                    <span>Giảm giá</span>
                                    <strong id="cartDiscount">-0đ</strong>
                                </div>

                                <div class="cart-total-row">
                                    <span>Số sách</span>
                                    <strong><?php echo number_format($itemCount); ?> cuốn</strong>
                                </div>

                                <div class="cart-total-row">
                                    <span>Tổng ngày thuê</span>
                                    <strong><?php echo number_format($totalRentalDays); ?> ngày</strong>
                                </div>

                                <div class="cart-grand-total">
                                    <span>Tổng cộng</span>
                                    <strong id="cartGrandTotal"><?php echo cartMoney($cartTotal); ?></strong>
                                </div>
                            </div>

                            <form method="POST" class="cart-checkout-form">
                                <button type="submit" name="action" value="checkout" class="cart-checkout-btn">
                                    <i class="fas fa-check"></i>
                                    Xác nhận thuê
                                </button>
                            </form>

                            <a href="books.php" class="cart-continue-link">
                                <i class="fas fa-arrow-left"></i>
                                Tiếp tục chọn sách
                            </a>

                            <div class="cart-trust-list">
                                <div>
                                    <i class="fas fa-shield-alt"></i>
                                    Sách được kiểm tra trước khi giao
                                </div>
                                <div>
                                    <i class="fas fa-truck"></i>
                                    Giao sách tiện lợi
                                </div>
                                <div>
                                    <i class="fas fa-sync-alt"></i>
                                    Dễ thuê thêm hoặc đổi sách
                                </div>
                            </div>
                        </div>
                    </aside>
                </div>

            <?php endif; ?>

        </div>
    </section>

</main>

<style>
.cart-page {
    --cart-primary: #8b5a2b;
    --cart-primary-dark: #5a3518;
    --cart-accent: #d89a45;
    --cart-cream: #fff8ed;
    --cart-cream-2: #f0dec4;
    --cart-ink: #1f1711;
    --cart-muted: #74685f;
    --cart-line: rgba(92, 57, 24, 0.13);
    --cart-card: rgba(255, 255, 255, 0.82);
    --cart-shadow: 0 24px 70px rgba(55, 34, 18, 0.13);
    --cart-shadow-soft: 0 14px 34px rgba(55, 34, 18, 0.08);
    min-height: 100vh;
    background:
        radial-gradient(circle at 8% 0%, rgba(216, 154, 69, 0.2), transparent 30%),
        radial-gradient(circle at 92% 8%, rgba(139, 90, 43, 0.11), transparent 25%),
        linear-gradient(180deg, #fffaf3 0%, #fff 46%, #fff8ed 100%);
    color: var(--cart-ink);
    overflow: hidden;
}

.cart-page * {
    box-sizing: border-box;
}

.cart-hero {
    position: relative;
    padding: 78px 0 34px;
}

.cart-hero-bg {
    position: absolute;
    inset: 0;
    background-image:
        linear-gradient(rgba(139, 90, 43, 0.045) 1px, transparent 1px),
        linear-gradient(90deg, rgba(139, 90, 43, 0.045) 1px, transparent 1px);
    background-size: 44px 44px;
    mask-image: linear-gradient(to bottom, #000, transparent 78%);
    pointer-events: none;
}

.cart-hero-grid {
    position: relative;
    display: grid;
    grid-template-columns: minmax(0, 1fr) minmax(340px, 0.62fr);
    gap: 32px;
    align-items: end;
}

.cart-eyebrow {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    width: fit-content;
    padding: 8px 13px;
    border: 1px solid var(--cart-line);
    border-radius: 999px;
    background: rgba(255, 255, 255, 0.78);
    color: var(--cart-primary);
    font-weight: 950;
    font-size: 0.78rem;
    letter-spacing: 0.02em;
}

.cart-hero-content h1 {
    max-width: 760px;
    margin: 20px 0 18px;
    font-family: 'Playfair Display', Georgia, serif;
    font-size: clamp(3rem, 6vw, 5.45rem);
    line-height: 0.96;
    letter-spacing: -0.06em;
    color: var(--cart-ink);
}

.cart-hero-content h1 span {
    color: var(--cart-primary);
}

.cart-hero-content p {
    max-width: 620px;
    margin: 0;
    color: var(--cart-muted);
    font-size: 1.06rem;
    line-height: 1.75;
}

.cart-hero-summary {
    display: grid;
    gap: 12px;
}

.cart-summary-mini {
    display: flex;
    align-items: center;
    gap: 14px;
    padding: 16px;
    border: 1px solid var(--cart-line);
    border-radius: 24px;
    background: rgba(255, 255, 255, 0.78);
    box-shadow: var(--cart-shadow-soft);
    backdrop-filter: blur(16px);
}

.cart-summary-mini i {
    width: 46px;
    height: 46px;
    display: grid;
    place-items: center;
    flex: 0 0 auto;
    border-radius: 16px;
    background: var(--cart-cream);
    color: var(--cart-primary);
}

.cart-summary-mini strong,
.cart-summary-mini span {
    display: block;
}

.cart-summary-mini strong {
    color: var(--cart-ink);
    font-size: 1.2rem;
    font-weight: 950;
}

.cart-summary-mini span {
    margin-top: 3px;
    color: var(--cart-muted);
    font-size: 0.82rem;
    font-weight: 800;
}

.cart-alert {
    position: relative;
    display: flex;
    align-items: center;
    gap: 12px;
    margin-top: 28px;
    padding: 16px 18px;
    border-radius: 22px;
    border: 1px solid var(--cart-line);
    background: #fff;
    box-shadow: var(--cart-shadow-soft);
    font-weight: 850;
}

.cart-alert-success {
    color: #166534;
    background: #f0fdf4;
    border-color: rgba(22, 101, 52, 0.14);
}

.cart-alert-danger {
    color: #991b1b;
    background: #fff1f2;
    border-color: rgba(153, 27, 27, 0.14);
}

.cart-main-section {
    padding: 42px 0 88px;
}

.cart-empty-card {
    max-width: 660px;
    margin: 0 auto;
    padding: 58px 28px;
    border: 1px dashed rgba(139, 90, 43, 0.26);
    border-radius: 36px;
    background: rgba(255, 255, 255, 0.8);
    box-shadow: var(--cart-shadow-soft);
    text-align: center;
}

.cart-empty-icon {
    width: 82px;
    height: 82px;
    display: grid;
    place-items: center;
    margin: 0 auto 20px;
    border-radius: 28px;
    background: var(--cart-cream);
    color: var(--cart-primary);
    font-size: 2rem;
}

.cart-empty-card h2 {
    margin: 0 0 10px;
    font-family: 'Playfair Display', Georgia, serif;
    color: var(--cart-ink);
    font-size: clamp(2rem, 4vw, 3.1rem);
    line-height: 1;
    letter-spacing: -0.04em;
}

.cart-empty-card p {
    max-width: 430px;
    margin: 0 auto 24px;
    color: var(--cart-muted);
    line-height: 1.7;
}

.cart-layout {
    display: grid;
    grid-template-columns: minmax(0, 1fr) minmax(340px, 0.42fr);
    gap: 26px;
    align-items: start;
}

.cart-items-panel,
.cart-checkout-card {
    border: 1px solid var(--cart-line);
    border-radius: 34px;
    background: rgba(255, 255, 255, 0.78);
    box-shadow: var(--cart-shadow-soft);
    backdrop-filter: blur(18px);
}

.cart-items-panel {
    padding: 24px;
}

.cart-panel-head {
    display: flex;
    align-items: end;
    justify-content: space-between;
    gap: 18px;
    margin-bottom: 20px;
}

.cart-panel-head span,
.cart-checkout-head span {
    display: inline-flex;
    width: fit-content;
    padding: 7px 11px;
    border-radius: 999px;
    background: var(--cart-cream);
    color: var(--cart-primary);
    font-size: 0.76rem;
    font-weight: 950;
}

.cart-panel-head h2,
.cart-checkout-head h2 {
    margin: 10px 0 0;
    color: var(--cart-ink);
    font-size: 1.35rem;
    font-weight: 950;
    letter-spacing: -0.02em;
}

.cart-clear-btn {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    min-height: 40px;
    padding: 0 13px;
    border: 1px solid rgba(185, 28, 28, 0.14);
    border-radius: 999px;
    background: #fff1f2;
    color: #991b1b;
    font-size: 0.84rem;
    font-weight: 950;
    cursor: pointer;
    transition: 0.25s ease;
}

.cart-clear-btn:hover {
    transform: translateY(-2px);
    background: #991b1b;
    color: #fff;
}

.cart-items-list {
    display: grid;
    gap: 14px;
}

.cart-item-card {
    display: grid;
    grid-template-columns: 104px minmax(0, 1fr) auto 44px;
    gap: 18px;
    align-items: center;
    padding: 16px;
    border: 1px solid var(--cart-line);
    border-radius: 28px;
    background:
        radial-gradient(circle at 100% 0%, rgba(216, 154, 69, 0.12), transparent 32%),
        rgba(255, 255, 255, 0.78);
    transition: 0.25s ease;
}

.cart-item-card:hover {
    transform: translateY(-4px);
    box-shadow: var(--cart-shadow-soft);
}

.cart-book-cover {
    display: grid;
    place-items: center;
    width: 104px;
    height: 142px;
    padding: 10px;
    border-radius: 22px;
    background:
        radial-gradient(circle at 50% 18%, rgba(216, 154, 69, 0.24), transparent 46%),
        linear-gradient(180deg, #fff8ed, #ead6ba);
    overflow: hidden;
}

.cart-book-cover img {
    width: 100%;
    height: 100%;
    object-fit: contain;
    filter: drop-shadow(0 12px 14px rgba(54, 33, 16, 0.2));
    transition: 0.25s ease;
}

.cart-item-card:hover .cart-book-cover img {
    transform: translateY(-3px) scale(1.02);
}

.cart-book-category {
    display: inline-flex;
    width: fit-content;
    padding: 6px 10px;
    border-radius: 999px;
    background: var(--cart-cream);
    color: var(--cart-primary);
    font-size: 0.72rem;
    font-weight: 950;
}

.cart-book-info h3 {
    margin: 10px 0 6px;
    color: var(--cart-ink);
    font-size: 1.05rem;
    line-height: 1.35;
    font-weight: 950;
}

.cart-book-info h3 a {
    color: var(--cart-ink);
    text-decoration: none;
}

.cart-book-info h3 a:hover {
    color: var(--cart-primary);
}

.cart-book-info p {
    display: flex;
    align-items: center;
    gap: 7px;
    margin: 0;
    color: var(--cart-muted);
    font-size: 0.86rem;
}

.cart-mobile-total {
    display: none;
    margin-top: 10px;
    color: var(--cart-primary);
    font-weight: 950;
}

.cart-book-meta {
    display: grid;
    grid-template-columns: repeat(3, max-content);
    gap: 18px;
    align-items: center;
}

.cart-book-meta div {
    min-width: 82px;
    text-align: right;
}

.cart-book-meta span {
    display: block;
    margin-bottom: 5px;
    color: var(--cart-muted);
    font-size: 0.74rem;
    font-weight: 800;
}

.cart-book-meta strong {
    display: block;
    color: var(--cart-ink);
    font-size: 0.92rem;
    font-weight: 950;
    white-space: nowrap;
}

.cart-book-meta .cart-item-total {
    color: var(--cart-primary);
    font-size: 1.15rem;
}

.cart-remove-form {
    display: flex;
    align-items: center;
    justify-content: center;
}

.cart-remove-btn {
    width: 42px;
    height: 42px;
    display: grid;
    place-items: center;
    border: 1px solid rgba(185, 28, 28, 0.14);
    border-radius: 16px;
    background: #fff1f2;
    color: #991b1b;
    cursor: pointer;
    transition: 0.25s ease;
}

.cart-remove-btn:hover {
    background: #991b1b;
    color: #fff;
    transform: translateY(-2px);
}

.cart-checkout-panel {
    position: sticky;
    top: 96px;
}

.cart-checkout-card {
    padding: 24px;
}

.cart-checkout-head {
    margin-bottom: 18px;
}

.cart-coupon-box {
    padding: 18px;
    border: 1px solid var(--cart-line);
    border-radius: 24px;
    background: rgba(255, 248, 237, 0.72);
    margin-bottom: 16px;
}

.cart-coupon-box label {
    display: flex;
    align-items: center;
    gap: 8px;
    margin-bottom: 10px;
    color: var(--cart-ink);
    font-size: 0.9rem;
    font-weight: 950;
}

.cart-coupon-box label i {
    color: var(--cart-primary);
}

.cart-coupon-row {
    display: flex;
    gap: 8px;
}

.cart-coupon-row input {
    flex: 1;
    min-width: 0;
    height: 44px;
    padding: 0 13px;
    border: 1px solid var(--cart-line);
    border-radius: 999px;
    background: #fff;
    color: var(--cart-ink);
    outline: none;
    font-weight: 800;
}

.cart-coupon-row input:focus {
    border-color: rgba(139, 90, 43, 0.42);
    box-shadow: 0 0 0 4px rgba(139, 90, 43, 0.08);
}

.cart-coupon-row button {
    height: 44px;
    padding: 0 14px;
    border: 0;
    border-radius: 999px;
    background: var(--cart-ink);
    color: #fff;
    font-weight: 950;
    cursor: pointer;
    transition: 0.25s ease;
}

.cart-coupon-row button:hover {
    background: var(--cart-primary);
    transform: translateY(-1px);
}

.cart-coupon-box small {
    display: block;
    margin-top: 9px;
    color: var(--cart-muted);
    font-size: 0.78rem;
    line-height: 1.5;
}

.cart-total-box {
    padding: 18px;
    border: 1px solid var(--cart-line);
    border-radius: 24px;
    background: #fff;
    margin-bottom: 16px;
}

.cart-total-row {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 14px;
    padding: 9px 0;
    color: var(--cart-muted);
    font-size: 0.92rem;
    font-weight: 850;
}

.cart-total-row strong {
    color: var(--cart-ink);
    font-weight: 950;
}

.discount-row {
    color: #15803d;
}

.discount-row strong {
    color: #15803d;
}

.cart-grand-total {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 14px;
    margin-top: 12px;
    padding-top: 16px;
    border-top: 1px solid var(--cart-line);
}

.cart-grand-total span {
    color: var(--cart-ink);
    font-weight: 950;
}

.cart-grand-total strong {
    color: var(--cart-primary);
    font-size: 1.6rem;
    font-weight: 950;
    white-space: nowrap;
}

.cart-checkout-form {
    margin: 0;
}

.cart-checkout-btn,
.cart-btn {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 9px;
    min-height: 50px;
    border: 0;
    border-radius: 999px;
    font-weight: 950;
    cursor: pointer;
    text-decoration: none;
    transition: 0.25s ease;
}

.cart-checkout-btn {
    width: 100%;
    background: linear-gradient(135deg, var(--cart-primary), var(--cart-primary-dark));
    color: #fff;
    box-shadow: 0 16px 30px rgba(95, 53, 20, 0.2);
}

.cart-checkout-btn:hover,
.cart-btn:hover {
    transform: translateY(-2px);
    text-decoration: none;
}

.cart-btn-primary {
    padding: 0 22px;
    background: linear-gradient(135deg, var(--cart-primary), var(--cart-primary-dark));
    color: #fff;
    box-shadow: 0 16px 30px rgba(95, 53, 20, 0.2);
}

.cart-btn-primary:hover {
    color: #fff;
}

.cart-continue-link {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 9px;
    width: 100%;
    margin-top: 14px;
    color: var(--cart-muted);
    font-weight: 950;
    text-decoration: none;
}

.cart-continue-link:hover {
    color: var(--cart-primary);
    text-decoration: none;
}

.cart-trust-list {
    display: grid;
    gap: 10px;
    margin-top: 18px;
    padding-top: 18px;
    border-top: 1px solid var(--cart-line);
}

.cart-trust-list div {
    display: flex;
    align-items: center;
    gap: 9px;
    color: var(--cart-muted);
    font-size: 0.86rem;
    font-weight: 850;
}

.cart-trust-list i {
    color: var(--cart-primary);
}

@media (max-width: 1180px) {
    .cart-layout,
    .cart-hero-grid {
        grid-template-columns: 1fr;
    }

    .cart-checkout-panel {
        position: static;
    }
}

@media (max-width: 900px) {
    .cart-item-card {
        grid-template-columns: 94px minmax(0, 1fr) 42px;
    }

    .cart-book-meta {
        display: none;
    }

    .cart-mobile-total {
        display: block;
    }
}

@media (max-width: 768px) {
    .cart-hero {
        padding: 58px 0 28px;
    }

    .cart-hero-content h1 {
        font-size: clamp(2.65rem, 13vw, 4rem);
    }

    .cart-main-section {
        padding: 34px 0 62px;
    }

    .cart-items-panel,
    .cart-checkout-card,
    .cart-empty-card {
        border-radius: 28px;
    }

    .cart-items-panel,
    .cart-checkout-card {
        padding: 18px;
    }

    .cart-panel-head {
        align-items: flex-start;
        flex-direction: column;
    }

    .cart-clear-btn {
        width: 100%;
        justify-content: center;
    }

    .cart-item-card {
        grid-template-columns: 82px minmax(0, 1fr) 40px;
        gap: 12px;
        padding: 12px;
        border-radius: 24px;
    }

    .cart-book-cover {
        width: 82px;
        height: 116px;
        border-radius: 18px;
    }

    .cart-book-info h3 {
        font-size: 0.95rem;
    }

    .cart-coupon-row {
        flex-direction: column;
    }

    .cart-coupon-row button {
        width: 100%;
    }
}

@media (max-width: 480px) {
    .cart-item-card {
        grid-template-columns: 74px minmax(0, 1fr);
    }

    .cart-remove-form {
        grid-column: 1 / -1;
    }

    .cart-remove-btn {
        width: 100%;
        border-radius: 999px;
    }

    .cart-book-cover {
        width: 74px;
        height: 106px;
    }

    .cart-grand-total {
        align-items: flex-start;
        flex-direction: column;
    }
}
</style>

<script>
const cartOriginalTotal = <?php echo json_encode((float)$cartTotal); ?>;

function formatVnd(value) {
    return Math.round(value).toLocaleString('vi-VN') + 'đ';
}

function applyCoupon() {
    const input = document.getElementById('couponCode');
    const message = document.getElementById('couponMessage');
    const discountRow = document.getElementById('discountRow');
    const discountText = document.getElementById('cartDiscount');
    const grandTotal = document.getElementById('cartGrandTotal');

    if (!input || !message || !discountRow || !discountText || !grandTotal) {
        return;
    }

    const code = input.value.trim().toUpperCase();

    if (code === '') {
        message.textContent = 'Nhập mã nếu bạn có ưu đãi.';
        message.style.color = '';
        discountRow.style.display = 'none';
        discountText.textContent = '-0đ';
        grandTotal.textContent = formatVnd(cartOriginalTotal);
        return;
    }

    if (code === 'WELCOME10') {
        const discount = cartOriginalTotal * 0.1;
        const finalTotal = Math.max(cartOriginalTotal - discount, 0);

        discountRow.style.display = 'flex';
        discountText.textContent = '-' + formatVnd(discount);
        grandTotal.textContent = formatVnd(finalTotal);

        message.textContent = 'Đã áp dụng mã WELCOME10.';
        message.style.color = '#15803d';
    } else {
        discountRow.style.display = 'none';
        discountText.textContent = '-0đ';
        grandTotal.textContent = formatVnd(cartOriginalTotal);

        message.textContent = 'Mã ưu đãi chưa hợp lệ.';
        message.style.color = '#991b1b';
    }
}
</script>

<?php require_once __DIR__ . '/../templates/footer.php'; ?>