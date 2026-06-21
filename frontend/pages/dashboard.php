<?php
require_once __DIR__ . '/../templates/header.php';

if (!$isLoggedIn) {
    header('Location: login.php?redirect=dashboard.php');
    exit;
}

require_once __DIR__ . '/../../backend/controllers/RentalController.php';
require_once __DIR__ . '/../../backend/models/User.php';

$rentalController = new RentalController();
$userModel = new User();

$user = $userModel->findById($_SESSION['user_id']);
$activeRentals = $rentalController->getActiveRentals($_SESSION['user_id']);
$historyRentals = $rentalController->getHistory($_SESSION['user_id']);

$stats = [
    'active' => count($activeRentals),
    'returned' => count($historyRentals),
    'total_spent' => array_sum(array_column($historyRentals, 'total_price'))
];

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    if ($_POST['action'] === 'return_book' && isset($_POST['rental_id'])) {
        $result = $rentalController->returnBook(intval($_POST['rental_id']));

        if ($result['success']) {
            header('Location: dashboard.php?success=1');
            exit;
        }
    }
}

if (isset($_GET['success'])) {
    $successMessage = 'Trả sách thành công!';
}

if (!function_exists('dashText')) {
    function dashText($value, $fallback = '') {
        return htmlspecialchars((string)($value ?? $fallback), ENT_QUOTES, 'UTF-8');
    }
}

if (!function_exists('dashMoney')) {
    function dashMoney($value) {
        return number_format((float)$value, 0) . 'đ';
    }
}

$fullName = (string)($user['full_name'] ?? 'Bạn đọc');
$email = (string)($user['email'] ?? '');
$initial = mb_strtoupper(mb_substr($fullName, 0, 1, 'UTF-8'), 'UTF-8');
$latestHistory = array_slice($historyRentals, 0, 4);
?>

<main class="dash-page">

    <section class="dash-hero">
        <div class="dash-hero-bg"></div>

        <div class="container">
            <div class="dash-hero-grid">
                <div class="dash-hero-content">
                    <span class="dash-eyebrow">
                        <i class="fas fa-chart-line"></i>
                        Dashboard
                    </span>

                    <h1>
                        Chào mừng trở lại,
                        <span><?php echo dashText($fullName); ?></span>
                    </h1>

                    <p>
                        Theo dõi sách đang thuê, hạn trả và lịch sử đọc của bạn tại Mây Mơ Book.
                    </p>

                    <div class="dash-hero-actions">
                        <a href="books.php" class="dash-btn dash-btn-primary">
                            Khám phá sách
                            <i class="fas fa-arrow-right"></i>
                        </a>

                        <a href="cart.php" class="dash-btn dash-btn-light">
                            Xem giỏ hàng
                        </a>
                    </div>
                </div>

                <div class="dash-hero-card">
                    <div class="dash-hero-avatar">
                        <?php echo dashText($initial); ?>
                    </div>

                    <div>
                        <strong><?php echo dashText($fullName); ?></strong>
                        <span><?php echo dashText($email); ?></span>
                    </div>

                    <div class="dash-hero-mini-grid">
                        <div>
                            <strong><?php echo number_format($stats['active']); ?></strong>
                            <span>Đang thuê</span>
                        </div>

                        <div>
                            <strong><?php echo number_format($stats['returned']); ?></strong>
                            <span>Đã trả</span>
                        </div>
                    </div>
                </div>
            </div>

            <?php if (isset($successMessage)): ?>
                <div class="dash-alert">
                    <i class="fas fa-check-circle"></i>
                    <span><?php echo dashText($successMessage); ?></span>
                </div>
            <?php endif; ?>
        </div>
    </section>

    <section class="dash-main-section">
        <div class="container">
            <div class="dash-layout">

                <aside class="dash-sidebar">
                    <div class="dash-user-card">
                        <div class="dash-avatar">
                            <?php echo dashText($initial); ?>
                        </div>

                        <h3><?php echo dashText($fullName); ?></h3>
                        <p><?php echo dashText($email); ?></p>
                    </div>

                    <nav class="dash-nav">
                        <a href="#" class="is-active">
                            <i class="fas fa-chart-pie"></i>
                            Tổng quan
                        </a>

                        <a href="#active-rentals">
                            <i class="fas fa-book-open"></i>
                            Sách đang thuê
                        </a>

                        <a href="#history-rentals">
                            <i class="fas fa-history"></i>
                            Lịch sử thuê
                        </a>

                        <a href="books.php">
                            <i class="fas fa-search"></i>
                            Khám phá sách
                        </a>
                    </nav>

                    <div class="dash-sidebar-note">
                        <i class="fas fa-leaf"></i>
                        <p>Đọc xong một cuốn, chọn tiếp một cuốn mới.</p>
                    </div>
                </aside>

                <section class="dash-content">

                    <div class="dash-section-head">
                        <div>
                            <span>Overview</span>
                            <h2>Tổng quan thuê sách</h2>
                        </div>
                    </div>

                    <div class="dash-stats-grid">
                        <div class="dash-stat-card">
                            <div class="dash-stat-icon">
                                <i class="fas fa-book"></i>
                            </div>

                            <strong><?php echo number_format($stats['active']); ?></strong>
                            <span>Sách đang thuê</span>
                        </div>

                        <div class="dash-stat-card">
                            <div class="dash-stat-icon is-green">
                                <i class="fas fa-check-circle"></i>
                            </div>

                            <strong><?php echo number_format($stats['returned']); ?></strong>
                            <span>Sách đã trả</span>
                        </div>

                        <div class="dash-stat-card">
                            <div class="dash-stat-icon is-gold">
                                <i class="fas fa-coins"></i>
                            </div>

                            <strong><?php echo dashMoney($stats['total_spent']); ?></strong>
                            <span>Tổng chi tiêu</span>
                        </div>
                    </div>

                    <section class="dash-panel" id="active-rentals">
                        <div class="dash-panel-head">
                            <div>
                                <span>Đang thuê</span>
                                <h2>Sách cần theo dõi</h2>
                            </div>

                            <a href="books.php" class="dash-panel-link">
                                Thuê thêm
                                <i class="fas fa-arrow-right"></i>
                            </a>
                        </div>

                        <?php if (count($activeRentals) > 0): ?>
                            <div class="dash-rental-list">
                                <?php foreach ($activeRentals as $rental): ?>
                                    <?php
                                    $daysLeft = (strtotime($rental['due_date']) - time()) / (60 * 60 * 24);
                                    $daysLeftDisplay = (int)ceil($daysLeft);
                                    $isOverdue = $daysLeft < 0;
                                    ?>
                                    <article class="dash-rental-card">
                                        <a href="book-detail.php?id=<?php echo (int)($rental['book_id'] ?? 0); ?>" class="dash-rental-cover">
                                            <img
                                                src="<?php echo dashText(getBookCoverImage($rental)); ?>"
                                                alt="<?php echo dashText($rental['title'] ?? 'Sách'); ?>"
                                                loading="lazy"
                                            >
                                        </a>

                                        <div class="dash-rental-info">
                                            <span class="dash-rental-status <?php echo $isOverdue ? 'is-overdue' : 'is-active'; ?>">
                                                <i class="fas fa-<?php echo $isOverdue ? 'exclamation-circle' : 'clock'; ?>"></i>
                                                <?php echo $isOverdue ? 'Quá hạn' : 'Đang thuê'; ?>
                                            </span>

                                            <h3>
                                                <a href="book-detail.php?id=<?php echo (int)($rental['book_id'] ?? 0); ?>">
                                                    <?php echo dashText($rental['title'] ?? ''); ?>
                                                </a>
                                            </h3>

                                            <p>
                                                <i class="fas fa-user-edit"></i>
                                                <?php echo dashText($rental['author'] ?? 'Tác giả'); ?>
                                            </p>

                                            <div class="dash-rental-meta">
                                                <div>
                                                    <span>Hạn trả</span>
                                                    <strong><?php echo date('d/m/Y', strtotime($rental['due_date'])); ?></strong>
                                                </div>

                                                <div>
                                                    <span><?php echo $isOverdue ? 'Trễ hạn' : 'Còn lại'; ?></span>
                                                    <strong class="<?php echo $isOverdue ? 'is-danger' : ''; ?>">
                                                        <?php
                                                        echo $isOverdue
                                                            ? abs($daysLeftDisplay) . ' ngày'
                                                            : max($daysLeftDisplay, 0) . ' ngày';
                                                        ?>
                                                    </strong>
                                                </div>

                                                <div>
                                                    <span>Tổng phí</span>
                                                    <strong><?php echo dashMoney($rental['total_price'] ?? 0); ?></strong>
                                                </div>
                                            </div>
                                        </div>

                                        <form method="POST" class="dash-return-form">
                                            <input type="hidden" name="action" value="return_book">
                                            <input type="hidden" name="rental_id" value="<?php echo (int)$rental['id']; ?>">

                                            <button type="submit" class="dash-return-btn">
                                                <i class="fas fa-undo"></i>
                                                Trả sách
                                            </button>
                                        </form>
                                    </article>
                                <?php endforeach; ?>
                            </div>
                        <?php else: ?>
                            <div class="dash-empty-card">
                                <div class="dash-empty-icon">
                                    <i class="fas fa-check-circle"></i>
                                </div>

                                <h3>Không có sách đang thuê</h3>
                                <p>Bạn đang không thuê cuốn sách nào. Hãy chọn một cuốn mới để bắt đầu đọc.</p>

                                <a href="books.php" class="dash-btn dash-btn-primary">
                                    Khám phá sách
                                    <i class="fas fa-arrow-right"></i>
                                </a>
                            </div>
                        <?php endif; ?>
                    </section>

                    <section class="dash-panel" id="history-rentals">
                        <div class="dash-panel-head">
                            <div>
                                <span>Lịch sử</span>
                                <h2>Sách đã thuê gần đây</h2>
                            </div>
                        </div>

                        <?php if (count($latestHistory) > 0): ?>
                            <div class="dash-history-list">
                                <?php foreach ($latestHistory as $rental): ?>
                                    <article class="dash-history-item">
                                        <div class="dash-history-cover">
                                            <img
                                                src="<?php echo dashText(getBookCoverImage($rental)); ?>"
                                                alt="<?php echo dashText($rental['title'] ?? 'Sách'); ?>"
                                                loading="lazy"
                                            >
                                        </div>

                                        <div>
                                            <h3><?php echo dashText($rental['title'] ?? ''); ?></h3>
                                            <p><?php echo dashText($rental['author'] ?? 'Tác giả'); ?></p>
                                        </div>

                                        <div class="dash-history-price">
                                            <span>Chi phí</span>
                                            <strong><?php echo dashMoney($rental['total_price'] ?? 0); ?></strong>
                                        </div>
                                    </article>
                                <?php endforeach; ?>
                            </div>
                        <?php else: ?>
                            <div class="dash-history-empty">
                                <i class="fas fa-book-reader"></i>
                                <span>Chưa có lịch sử thuê sách.</span>
                            </div>
                        <?php endif; ?>
                    </section>

                </section>
            </div>
        </div>
    </section>

</main>

<style>
.dash-page {
    --dash-primary: #8b5a2b;
    --dash-primary-dark: #5a3518;
    --dash-accent: #d89a45;
    --dash-cream: #fff8ed;
    --dash-cream-2: #f0dec4;
    --dash-ink: #1f1711;
    --dash-muted: #74685f;
    --dash-line: rgba(92, 57, 24, 0.13);
    --dash-card: rgba(255, 255, 255, 0.82);
    --dash-shadow: 0 24px 70px rgba(55, 34, 18, 0.13);
    --dash-shadow-soft: 0 14px 34px rgba(55, 34, 18, 0.08);
    min-height: 100vh;
    background:
        radial-gradient(circle at 8% 0%, rgba(216, 154, 69, 0.2), transparent 30%),
        radial-gradient(circle at 92% 8%, rgba(139, 90, 43, 0.11), transparent 25%),
        linear-gradient(180deg, #fffaf3 0%, #fff 46%, #fff8ed 100%);
    color: var(--dash-ink);
    overflow: hidden;
}

.dash-page * {
    box-sizing: border-box;
}

.dash-hero {
    position: relative;
    padding: 78px 0 34px;
}

.dash-hero-bg {
    position: absolute;
    inset: 0;
    background-image:
        linear-gradient(rgba(139, 90, 43, 0.045) 1px, transparent 1px),
        linear-gradient(90deg, rgba(139, 90, 43, 0.045) 1px, transparent 1px);
    background-size: 44px 44px;
    mask-image: linear-gradient(to bottom, #000, transparent 78%);
    pointer-events: none;
}

.dash-hero-grid {
    position: relative;
    display: grid;
    grid-template-columns: minmax(0, 1fr) minmax(320px, 0.5fr);
    gap: 32px;
    align-items: end;
}

.dash-eyebrow,
.dash-section-head span,
.dash-panel-head span {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    width: fit-content;
    padding: 8px 13px;
    border: 1px solid var(--dash-line);
    border-radius: 999px;
    background: rgba(255, 255, 255, 0.78);
    color: var(--dash-primary);
    font-weight: 950;
    font-size: 0.78rem;
    letter-spacing: 0.02em;
}

.dash-hero-content h1 {
    max-width: 860px;
    margin: 20px 0 18px;
    font-family: 'Playfair Display', Georgia, serif;
    font-size: clamp(3rem, 6vw, 5.45rem);
    line-height: 0.96;
    letter-spacing: -0.06em;
    color: var(--dash-ink);
}

.dash-hero-content h1 span {
    color: var(--dash-primary);
}

.dash-hero-content p {
    max-width: 620px;
    margin: 0;
    color: var(--dash-muted);
    font-size: 1.06rem;
    line-height: 1.75;
}

.dash-hero-actions {
    display: flex;
    align-items: center;
    gap: 12px;
    margin-top: 26px;
    flex-wrap: wrap;
}

.dash-btn {
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

.dash-btn:hover {
    transform: translateY(-2px);
    text-decoration: none;
}

.dash-btn-primary {
    background: linear-gradient(135deg, var(--dash-primary), var(--dash-primary-dark));
    color: #fff;
    box-shadow: 0 14px 28px rgba(95, 53, 20, 0.22);
}

.dash-btn-primary:hover {
    color: #fff;
}

.dash-btn-light {
    background: rgba(255, 255, 255, 0.78);
    color: var(--dash-ink);
    border-color: var(--dash-line);
}

.dash-btn-light:hover {
    color: var(--dash-primary);
    background: #fff;
}

.dash-hero-card {
    padding: 24px;
    border: 1px solid var(--dash-line);
    border-radius: 34px;
    background:
        radial-gradient(circle at 100% 0%, rgba(216, 154, 69, 0.18), transparent 34%),
        rgba(255, 255, 255, 0.82);
    box-shadow: var(--dash-shadow-soft);
    backdrop-filter: blur(18px);
}

.dash-hero-avatar,
.dash-avatar {
    display: grid;
    place-items: center;
    color: #fff;
    background: linear-gradient(135deg, var(--dash-primary), var(--dash-primary-dark));
    font-weight: 950;
}

.dash-hero-avatar {
    width: 68px;
    height: 68px;
    margin-bottom: 16px;
    border-radius: 24px;
    font-size: 1.7rem;
}

.dash-hero-card > div:not(.dash-hero-avatar):not(.dash-hero-mini-grid) strong,
.dash-hero-card > div:not(.dash-hero-avatar):not(.dash-hero-mini-grid) span {
    display: block;
}

.dash-hero-card > div:not(.dash-hero-avatar):not(.dash-hero-mini-grid) strong {
    color: var(--dash-ink);
    font-size: 1.1rem;
}

.dash-hero-card > div:not(.dash-hero-avatar):not(.dash-hero-mini-grid) span {
    margin-top: 4px;
    color: var(--dash-muted);
    font-size: 0.86rem;
}

.dash-hero-mini-grid {
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    gap: 12px;
    margin-top: 18px;
}

.dash-hero-mini-grid div {
    padding: 14px;
    border: 1px solid var(--dash-line);
    border-radius: 22px;
    background: rgba(255, 248, 237, 0.72);
}

.dash-hero-mini-grid strong,
.dash-hero-mini-grid span {
    display: block;
}

.dash-hero-mini-grid strong {
    color: var(--dash-primary);
    font-size: 1.35rem;
    line-height: 1;
    font-weight: 950;
}

.dash-hero-mini-grid span {
    margin-top: 6px;
    color: var(--dash-muted);
    font-size: 0.78rem;
    font-weight: 850;
}

.dash-alert {
    position: relative;
    display: flex;
    align-items: center;
    gap: 12px;
    margin-top: 28px;
    padding: 16px 18px;
    border-radius: 22px;
    border: 1px solid rgba(22, 101, 52, 0.14);
    background: #f0fdf4;
    color: #166534;
    box-shadow: var(--dash-shadow-soft);
    font-weight: 850;
}

.dash-main-section {
    padding: 42px 0 88px;
}

.dash-layout {
    display: grid;
    grid-template-columns: 280px minmax(0, 1fr);
    gap: 26px;
    align-items: start;
}

.dash-sidebar {
    position: sticky;
    top: 96px;
    display: grid;
    gap: 16px;
}

.dash-user-card,
.dash-sidebar-note,
.dash-content,
.dash-panel,
.dash-stat-card,
.dash-rental-card,
.dash-empty-card,
.dash-history-item,
.dash-history-empty {
    border: 1px solid var(--dash-line);
    background: rgba(255, 255, 255, 0.78);
    box-shadow: var(--dash-shadow-soft);
    backdrop-filter: blur(18px);
}

.dash-user-card {
    padding: 24px;
    border-radius: 34px;
    text-align: center;
}

.dash-avatar {
    width: 76px;
    height: 76px;
    margin: 0 auto 16px;
    border-radius: 26px;
    font-size: 1.9rem;
}

.dash-user-card h3 {
    margin: 0 0 5px;
    color: var(--dash-ink);
    font-size: 1.05rem;
    font-weight: 950;
}

.dash-user-card p {
    margin: 0;
    color: var(--dash-muted);
    font-size: 0.84rem;
    word-break: break-word;
}

.dash-nav {
    display: grid;
    gap: 8px;
    padding: 12px;
    border: 1px solid var(--dash-line);
    border-radius: 28px;
    background: rgba(255, 255, 255, 0.78);
    box-shadow: var(--dash-shadow-soft);
}

.dash-nav a {
    display: flex;
    align-items: center;
    gap: 10px;
    min-height: 44px;
    padding: 0 13px;
    border-radius: 999px;
    color: var(--dash-muted);
    font-weight: 900;
    text-decoration: none;
    transition: 0.25s ease;
}

.dash-nav a:hover,
.dash-nav a.is-active {
    background: linear-gradient(135deg, var(--dash-primary), var(--dash-primary-dark));
    color: #fff;
    text-decoration: none;
}

.dash-sidebar-note {
    display: flex;
    gap: 12px;
    padding: 18px;
    border-radius: 26px;
    color: var(--dash-muted);
}

.dash-sidebar-note i {
    width: 42px;
    height: 42px;
    display: grid;
    place-items: center;
    flex: 0 0 auto;
    border-radius: 15px;
    background: var(--dash-cream);
    color: var(--dash-primary);
}

.dash-sidebar-note p {
    margin: 0;
    line-height: 1.6;
    font-size: 0.88rem;
    font-weight: 800;
}

.dash-content {
    padding: 24px;
    border-radius: 34px;
}

.dash-section-head {
    margin-bottom: 22px;
}

.dash-section-head h2,
.dash-panel-head h2 {
    margin: 10px 0 0;
    color: var(--dash-ink);
    font-size: 1.35rem;
    font-weight: 950;
    letter-spacing: -0.02em;
}

.dash-stats-grid {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 16px;
}

.dash-stat-card {
    padding: 22px;
    border-radius: 28px;
    transition: 0.25s ease;
}

.dash-stat-card:hover {
    transform: translateY(-5px);
    box-shadow: var(--dash-shadow);
}

.dash-stat-icon {
    width: 52px;
    height: 52px;
    display: grid;
    place-items: center;
    margin-bottom: 16px;
    border-radius: 18px;
    color: var(--dash-primary);
    background: var(--dash-cream);
    font-size: 1.15rem;
}

.dash-stat-icon.is-green {
    color: #15803d;
    background: #dcfce7;
}

.dash-stat-icon.is-gold {
    color: #b45309;
    background: #fef3c7;
}

.dash-stat-card strong,
.dash-stat-card span {
    display: block;
}

.dash-stat-card strong {
    color: var(--dash-ink);
    font-size: 1.7rem;
    line-height: 1;
    font-weight: 950;
}

.dash-stat-card span {
    margin-top: 8px;
    color: var(--dash-muted);
    font-size: 0.86rem;
    font-weight: 850;
}

.dash-panel {
    margin-top: 24px;
    padding: 22px;
    border-radius: 30px;
}

.dash-panel-head {
    display: flex;
    align-items: end;
    justify-content: space-between;
    gap: 18px;
    margin-bottom: 18px;
}

.dash-panel-link {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    color: var(--dash-primary);
    font-weight: 950;
    text-decoration: none;
    white-space: nowrap;
}

.dash-panel-link:hover {
    color: var(--dash-primary-dark);
    text-decoration: none;
}

.dash-rental-list {
    display: grid;
    gap: 14px;
}

.dash-rental-card {
    display: grid;
    grid-template-columns: 96px minmax(0, 1fr) auto;
    gap: 18px;
    align-items: center;
    padding: 16px;
    border-radius: 28px;
    transition: 0.25s ease;
}

.dash-rental-card:hover {
    transform: translateY(-4px);
    box-shadow: var(--dash-shadow);
}

.dash-rental-cover {
    display: grid;
    place-items: center;
    width: 96px;
    height: 136px;
    padding: 10px;
    border-radius: 22px;
    background:
        radial-gradient(circle at 50% 18%, rgba(216, 154, 69, 0.24), transparent 46%),
        linear-gradient(180deg, #fff8ed, #ead6ba);
}

.dash-rental-cover img {
    width: 100%;
    height: 100%;
    object-fit: contain;
    filter: drop-shadow(0 12px 14px rgba(54, 33, 16, 0.2));
}

.dash-rental-status {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    width: fit-content;
    padding: 6px 10px;
    border-radius: 999px;
    font-size: 0.72rem;
    font-weight: 950;
}

.dash-rental-status.is-active {
    color: #15803d;
    background: #dcfce7;
}

.dash-rental-status.is-overdue {
    color: #991b1b;
    background: #fff1f2;
}

.dash-rental-info h3 {
    margin: 10px 0 6px;
    color: var(--dash-ink);
    font-size: 1.05rem;
    line-height: 1.35;
    font-weight: 950;
}

.dash-rental-info h3 a {
    color: var(--dash-ink);
    text-decoration: none;
}

.dash-rental-info h3 a:hover {
    color: var(--dash-primary);
}

.dash-rental-info p {
    display: flex;
    align-items: center;
    gap: 7px;
    margin: 0;
    color: var(--dash-muted);
    font-size: 0.86rem;
}

.dash-rental-meta {
    display: flex;
    align-items: center;
    gap: 20px;
    flex-wrap: wrap;
    margin-top: 14px;
}

.dash-rental-meta span,
.dash-rental-meta strong {
    display: block;
}

.dash-rental-meta span {
    margin-bottom: 4px;
    color: var(--dash-muted);
    font-size: 0.74rem;
    font-weight: 800;
}

.dash-rental-meta strong {
    color: var(--dash-ink);
    font-size: 0.9rem;
    font-weight: 950;
}

.dash-rental-meta strong.is-danger {
    color: #991b1b;
}

.dash-return-form {
    margin: 0;
}

.dash-return-btn {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 8px;
    min-height: 42px;
    padding: 0 15px;
    border: 0;
    border-radius: 999px;
    background: linear-gradient(135deg, var(--dash-primary), var(--dash-primary-dark));
    color: #fff;
    font-weight: 950;
    cursor: pointer;
    transition: 0.25s ease;
    white-space: nowrap;
}

.dash-return-btn:hover {
    transform: translateY(-2px);
    box-shadow: 0 14px 24px rgba(95, 53, 20, 0.18);
}

.dash-empty-card {
    padding: 48px 24px;
    border-radius: 30px;
    text-align: center;
}

.dash-empty-icon {
    width: 76px;
    height: 76px;
    display: grid;
    place-items: center;
    margin: 0 auto 18px;
    border-radius: 26px;
    background: #dcfce7;
    color: #15803d;
    font-size: 2rem;
}

.dash-empty-card h3 {
    margin: 0 0 8px;
    color: var(--dash-ink);
    font-size: 1.3rem;
    font-weight: 950;
}

.dash-empty-card p {
    max-width: 460px;
    margin: 0 auto 22px;
    color: var(--dash-muted);
    line-height: 1.7;
}

.dash-history-list {
    display: grid;
    gap: 12px;
}

.dash-history-item {
    display: grid;
    grid-template-columns: 58px minmax(0, 1fr) auto;
    gap: 14px;
    align-items: center;
    padding: 12px;
    border-radius: 22px;
}

.dash-history-cover {
    display: grid;
    place-items: center;
    width: 58px;
    height: 78px;
    padding: 6px;
    border-radius: 16px;
    background: var(--dash-cream);
}

.dash-history-cover img {
    width: 100%;
    height: 100%;
    object-fit: contain;
}

.dash-history-item h3 {
    margin: 0 0 4px;
    color: var(--dash-ink);
    font-size: 0.95rem;
    font-weight: 950;
}

.dash-history-item p {
    margin: 0;
    color: var(--dash-muted);
    font-size: 0.82rem;
}

.dash-history-price {
    text-align: right;
}

.dash-history-price span,
.dash-history-price strong {
    display: block;
}

.dash-history-price span {
    color: var(--dash-muted);
    font-size: 0.74rem;
    font-weight: 800;
}

.dash-history-price strong {
    margin-top: 4px;
    color: var(--dash-primary);
    font-weight: 950;
    white-space: nowrap;
}

.dash-history-empty {
    display: flex;
    align-items: center;
    gap: 10px;
    padding: 18px;
    border-radius: 24px;
    color: var(--dash-muted);
    font-weight: 850;
}

.dash-history-empty i {
    color: var(--dash-primary);
}

@media (max-width: 1180px) {
    .dash-layout,
    .dash-hero-grid {
        grid-template-columns: 1fr;
    }

    .dash-sidebar {
        position: static;
    }

    .dash-nav {
        display: flex;
        overflow-x: auto;
    }

    .dash-nav a {
        white-space: nowrap;
        flex: 0 0 auto;
    }
}

@media (max-width: 900px) {
    .dash-stats-grid {
        grid-template-columns: 1fr;
    }

    .dash-rental-card {
        grid-template-columns: 88px minmax(0, 1fr);
    }

    .dash-return-form {
        grid-column: 1 / -1;
    }

    .dash-return-btn {
        width: 100%;
    }
}

@media (max-width: 768px) {
    .dash-hero {
        padding: 58px 0 28px;
    }

    .dash-hero-content h1 {
        font-size: clamp(2.65rem, 13vw, 4rem);
    }

    .dash-main-section {
        padding: 34px 0 62px;
    }

    .dash-content,
    .dash-panel,
    .dash-user-card {
        border-radius: 28px;
    }

    .dash-content {
        padding: 18px;
    }

    .dash-panel {
        padding: 18px;
    }

    .dash-panel-head {
        align-items: flex-start;
        flex-direction: column;
    }

    .dash-hero-actions,
    .dash-hero-actions .dash-btn {
        width: 100%;
    }

    .dash-history-item {
        grid-template-columns: 52px minmax(0, 1fr);
    }

    .dash-history-price {
        grid-column: 1 / -1;
        text-align: left;
    }
}

@media (max-width: 480px) {
    .dash-rental-card {
        grid-template-columns: 76px minmax(0, 1fr);
        gap: 12px;
        padding: 12px;
    }

    .dash-rental-cover {
        width: 76px;
        height: 108px;
        border-radius: 18px;
    }

    .dash-rental-meta {
        gap: 12px;
    }
}
</style>

<?php require_once __DIR__ . '/../templates/footer.php'; ?>