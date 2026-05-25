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
?>

<section style="padding: 120px 0 60px; min-height: 100vh;">
    <div class="container">
        <div style="margin-bottom: 32px;">
            <h1 style="font-size: 2rem; margin-bottom: 4px;">Giỏ Hàng Của Bạn</h1>
            <p style="color: var(--text-muted); margin: 0;">Xem lại sách bạn muốn thuê</p>
        </div>
        
        <?php if ($message): ?>
        <div class="alert alert-<?php echo $messageType; ?>" style="margin-bottom: 24px;">
            <i class="fas fa-<?php echo $messageType === 'success' ? 'check-circle' : 'exclamation-circle'; ?>"></i>
            <?php echo htmlspecialchars($message); ?>
        </div>
        <?php endif; ?>
        
        <div class="cart-box">
            <?php if (empty($cartItems)): ?>
            <div class="empty-state">
                <div class="empty-state-icon">
                    <i class="fas fa-shopping-cart"></i>
                </div>
                <h3>Giỏ Hàng Trống</h3>
                <p>Bắt đầu khám phá bộ sưu tập sách của chúng tôi</p>
                <a href="books.php" class="btn btn-primary">
                    <i class="fas fa-book"></i> Khám Phá Sách
                </a>
            </div>
            <?php else: ?>
            
            <div class="cart-items">
                <?php foreach ($cartItems as $item): ?>
                <?php $itemTotal = $item['price_per_day'] * $item['rental_days']; ?>
                <div class="cart-item">
                    <div class="cart-item-image">
                        <img src="<?php echo getBookCoverImage($item); ?>"
                             alt="<?php echo htmlspecialchars($item['title']); ?>"
                             style="width: 100px; height: 140px; object-fit: cover; border-radius: var(--radius);">
                    </div>
                    <div class="cart-item-details">
                        <h3 class="cart-item-title"><?php echo htmlspecialchars($item['title']); ?></h3>
                        <p class="cart-item-author">by <?php echo htmlspecialchars($item['author']); ?></p>
                        <span class="book-category"><?php echo htmlspecialchars($item['category']); ?></span>
                    </div>
                    <div class="cart-item-meta">
                        <div class="cart-item-price">
                            <span class="label">Giá/ngày</span>
                            <span class="value"><?php echo number_format($item['price_per_day'], 0); ?>đ</span>
                        </div>
                        <div class="cart-item-days">
                            <span class="label">Số ngày</span>
                            <span class="value"><?php echo $item['rental_days']; ?> ngày</span>
                        </div>
                    </div>
                    <div class="cart-item-total">
                        <span class="label">Tổng</span>
                        <span class="value"><?php echo number_format($itemTotal, 0); ?>đ</span>
                    </div>
                    <div class="cart-item-actions">
                        <form method="POST" style="display: inline;">
                            <input type="hidden" name="action" value="remove">
                            <input type="hidden" name="cart_id" value="<?php echo $item['id']; ?>">
                            <button type="submit" class="btn-remove" title="Xóa">
                                <i class="fas fa-trash"></i>
                            </button>
                        </form>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
            
            <div class="cart-footer">
                <div class="cart-footer-left">
                    <form method="POST" style="display: inline;">
                        <input type="hidden" name="action" value="clear">
                        <button type="submit" class="btn btn-outline btn-sm" onclick="return confirm('Xóa toàn bộ giỏ hàng?')">
                            <i class="fas fa-trash"></i> Xóa toàn bộ
                        </button>
                    </form>
                </div>
                
                <div class="cart-footer-right">
                    <!-- Coupon Section -->
                    <div class="coupon-section">
                        <input type="text" id="couponCode" class="coupon-input" placeholder="Nhập mã giảm giá (VD: WELCOME10)">
                        <button type="button" class="btn btn-outline btn-sm" onclick="applyCoupon()">
                            <i class="fas fa-tag"></i> Áp dụng
                        </button>
                    </div>
                    
                    <div class="cart-summary">
                        <div class="cart-summary-row">
                            <span>Tạm tính (<?php echo count($cartItems); ?> cuốn)</span>
                            <span class="cart-subtotal"><?php echo number_format($cartTotal, 0); ?>đ</span>
                        </div>
                        <div class="cart-summary-row discount-row" style="display: none;">
                            <span>Giảm giá</span>
                            <span class="cart-discount">-0đ</span>
                        </div>
                        <div class="cart-summary-row total-row">
                            <span>Tổng cộng</span>
                            <span class="cart-total"><?php echo number_format($cartTotal, 0); ?>đ</span>
                        </div>
                    </div>
                    
                    <div class="cart-actions">
                        <a href="books.php" class="btn btn-outline">
                            <i class="fas fa-arrow-left"></i> Tiếp Tục Mua Sắm
                        </a>
                        <form method="POST" style="display: inline;">
                            <button type="submit" name="action" value="checkout" class="btn btn-primary btn-lg">
                                <i class="fas fa-check"></i> Xác Nhận Thuê
                            </button>
                        </form>
                    </div>
                </div>
            </div>
            <?php endif; ?>
        </div>
    </div>
</section>

<style>
.cart-items {
    display: flex;
    flex-direction: column;
    gap: 18px;
}

.cart-item {
    display: grid;
    grid-template-columns: 120px minmax(220px, 1.8fr) 120px 120px auto;
    gap: 22px;
    align-items: center;
    padding: 22px;
    background: var(--bg-secondary);
    border-radius: var(--radius-lg);
    transition: var(--transition);
}

.cart-item:hover {
    box-shadow: var(--shadow);
}

.cart-item-image img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    border-radius: var(--radius);
}

.cart-item-details {
    display: flex;
    flex-direction: column;
    gap: 8px;
    min-width: 0;
}

.cart-item-title {
    font-size: 1.05rem;
    font-weight: 700;
    margin-bottom: 4px;
}

.cart-item-author {
    font-size: 0.9rem;
    color: var(--text-muted);
    margin-bottom: 4px;
}

.book-category {
    display: inline-block;
    padding: 4px 10px;
    border-radius: 999px;
    background: rgba(59, 130, 246, 0.12);
    color: var(--blue-primary);
    font-size: 0.78rem;
    font-weight: 600;
    max-width: fit-content;
}

.cart-item-meta {
    display: flex;
    flex-direction: column;
    gap: 12px;
    text-align: center;
    min-width: 120px;
}

.cart-item-price,
.cart-item-days {
    text-align: center;
}

.cart-item-price .label,
.cart-item-days .label,
.cart-item-total .label {
    display: block;
    font-size: 0.75rem;
    color: var(--text-muted);
    margin-bottom: 6px;
}

.cart-item-price .value,
.cart-item-days .value,
.cart-item-total .value {
    font-weight: 700;
    color: var(--text-primary);
}

.cart-item-total {
    text-align: center;
}

.cart-item-total .value {
    font-size: 1.25rem;
    color: var(--blue-primary);
}

.cart-item-actions {
    display: flex;
    justify-content: flex-end;
}

.btn-remove {
    background: rgba(239, 68, 68, 0.12);
    color: var(--danger);
    border: none;
    width: 44px;
    height: 44px;
    border-radius: var(--radius);
    cursor: pointer;
    transition: var(--transition);
}

.btn-remove:hover {
    background: var(--danger);
    color: white;
}

.cart-footer {
    display: flex;
    flex-wrap: wrap;
    justify-content: space-between;
    align-items: flex-start;
    margin-top: 28px;
    padding-top: 24px;
    border-top: 2px solid var(--border-color);
    gap: 20px;
}

.cart-summary {
    background: var(--bg-secondary);
    padding: 22px;
    border-radius: var(--radius-lg);
    margin-bottom: 0;
    min-width: 320px;
    width: 100%;
    max-width: 420px;
}

.cart-summary-row {
    display: flex;
    justify-content: space-between;
    padding: 10px 0;
}

.cart-summary-row.total-row {
    border-top: 2px solid var(--border-color);
    margin-top: 10px;
    padding-top: 18px;
    font-weight: 700;
    font-size: 1.05rem;
}

.cart-summary-row.total-row .cart-total {
    color: var(--blue-primary);
    font-size: 1.6rem;
}

.cart-actions {
    display: flex;
    gap: 14px;
    justify-content: flex-end;
    flex-wrap: wrap;
    min-width: 220px;
}

.discount-row {
    color: var(--success);
}

.discount-row .cart-discount {
    font-weight: 700;
}

@media (max-width: 900px) {
    .cart-item {
        grid-template-columns: 100px 1fr;
        gap: 18px;
    }

    .cart-item-details {
        min-width: 0;
    }

    .cart-item-meta,
    .cart-item-total,
    .cart-item-actions {
        width: 100%;
    }

    .cart-item-meta {
        flex-direction: row;
        flex-wrap: wrap;
        justify-content: space-between;
        gap: 12px;
        padding-top: 10px;
    }

    .cart-item-total {
        padding-top: 10px;
    }

    .cart-actions {
        width: 100%;
        justify-content: space-between;
    }

    .cart-actions .btn,
    .cart-actions button {
        flex: 1 1 48%;
    }

    .cart-footer {
        flex-direction: column;
        align-items: stretch;
    }

    .cart-summary {
        max-width: 100%;
    }
}
</style>

<?php require_once __DIR__ . '/../templates/footer.php'; ?>
