<?php
require_once __DIR__ . '/../templates/header.php';
require_once __DIR__ . '/../../backend/controllers/BookController.php';

$bookController = new BookController();
$categories = $bookController->categories();

$search = isset($_GET['search']) ? trim($_GET['search']) : '';
$category = isset($_GET['category']) ? $_GET['category'] : '';

if (!empty($search)) {
    $books = $bookController->search($search);
    $pageTitle = 'Tìm Kiếm: ' . htmlspecialchars($search);
} elseif (!empty($category)) {
    $books = $bookController->category($category);
    $pageTitle = 'Sách ' . htmlspecialchars($category);
} else {
    $books = $bookController->index();
    $pageTitle = 'Tất Cả Sách';
}

function getBookCover($title) {
    return "https://covers.openlibrary.org/b/title/" . urlencode($title) . "-M.jpg";
}

// New function using local covers
function getBookCoverLocal($book) {
    return getBookCoverImage($book);
}
?>

<!-- Page Header -->
<section class="page-header">
    <div class="container">
        <div class="section-header" style="margin-bottom: 0;">
            <div class="section-header-left">
                <div class="section-icon">
                    <i class="fas fa-book-open"></i>
                </div>
                <div>
                    <h1 style="font-size: 2rem; margin-bottom: 4px;"><?php echo $pageTitle; ?></h1>
                    <p style="margin: 0; color: var(--text-muted);"><?php echo count($books); ?> sách được tìm thấy</p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Filter & Search -->
<section style="padding: 24px 0; background: var(--bg-secondary); border-bottom: 1px solid var(--border-light);">
    <div class="container">
        <form action="books.php" method="GET" class="filter-bar">
            <input type="text" name="search" placeholder="Tìm kiếm theo tên sách, tác giả..." 
                   value="<?php echo htmlspecialchars($search); ?>"
                   style="flex: 1; padding: 14px 20px; background: white; border: 2px solid var(--border-light); border-radius: var(--radius); color: var(--text-primary); font-family: inherit;">
            <button type="submit" class="btn btn-primary">
                <i class="fas fa-search"></i> Tìm Kiếm
            </button>
            <?php if (!empty($search) || !empty($category)): ?>
            <a href="books.php" class="btn btn-outline">
                <i class="fas fa-times"></i> Xóa
            </a>
            <?php endif; ?>
        </form>
        
        <div class="categories-filter" style="margin-top: 16px;">
            <a href="books.php" class="category-pill <?php echo empty($category) ? 'active' : ''; ?>">
                <i class="fas fa-border-all"></i> Tất Cả
            </a>
            <?php foreach ($categories as $cat): ?>
            <a href="books.php?category=<?php echo urlencode($cat); ?>" 
               class="category-pill <?php echo $category === $cat ? 'active' : ''; ?>">
                <?php echo htmlspecialchars($cat); ?>
            </a>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!-- Books Grid -->
<section class="section">
    <div class="container">
        <?php if (count($books) > 0): ?>
        <div class="books-grid">
            <?php foreach ($books as $book): ?>
            <div class="book-card">
                <?php if ($book['quantity'] > 0): ?>
                <span class="book-discount">
                    <i class="fas fa-check"></i> Còn hàng
                </span>
                <?php else: ?>
                <span class="book-discount" style="background: linear-gradient(145deg, #6b7280, #4b5563);">
                    <i class="fas fa-times"></i> Hết hàng
                </span>
                <?php endif; ?>
                
                <button class="book-wishlist" title="Yêu Thích">
                    <i class="far fa-heart"></i>
                </button>
                
                <div class="book-image">
                    <img src="<?php echo getBookCoverLocal($book); ?>"
                         alt="<?php echo htmlspecialchars($book['title']); ?>"
                         loading="lazy">
                </div>
                
                <div class="book-info">
                    <span class="book-category"><?php echo htmlspecialchars($book['category']); ?></span>
                    <h3 class="book-title"><?php echo htmlspecialchars($book['title']); ?></h3>
                    <p class="book-author">
                        <i class="fas fa-user-edit"></i> <?php echo htmlspecialchars($book['author']); ?>
                    </p>
                    
                    <div class="book-footer">
                        <div class="book-price">
                            <span class="book-price-current"><?php echo number_format($book['price_per_day'], 0); ?>đ</span>
                            <span class="book-price-original">/ngày</span>
                        </div>
                        <?php if ($book['quantity'] > 0): ?>
                        <a href="book-detail.php?id=<?php echo $book['id']; ?>" class="btn btn-sm">Thuê</a>
                        <?php else: ?>
                        <span style="font-size: 0.75rem; color: var(--text-muted);">Hết Hàng</span>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
        <?php else: ?>
        <div style="text-align: center; padding: 80px 20px;">
            <div style="width: 100px; height: 100px; background: linear-gradient(145deg, var(--primary-bg), var(--bg-warm)); border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 24px; color: var(--primary); font-size: 2.5rem;">
                <i class="fas fa-search"></i>
            </div>
            <h3 style="margin-bottom: 12px; font-size: 1.5rem;">Không Tìm Thấy Sách</h3>
            <p style="color: var(--text-muted); margin-bottom: 24px;">Thử điều chỉnh từ khóa tìm kiếm hoặc bộ lọc.</p>
            <a href="books.php" class="btn btn-primary">
                <i class="fas fa-book"></i> Xem Tất Cả Sách
            </a>
        </div>
        <?php endif; ?>
    </div>
</section>

<!-- Related Categories -->
<section class="section section-alt">
    <div class="container">
        <div class="section-header">
            <div class="section-header-left">
                <div class="section-icon">
                    <i class="fas fa-compass"></i>
                </div>
                <div>
                    <h2 class="section-title">Khám Phá Thêm</h2>
                    <p class="section-subtitle">Tìm sách theo thể loại</p>
                </div>
            </div>
        </div>
        
        <div class="categories-scroll">
            <?php 
            $categoryIcons = [
                'Tiểu thuyết' => 'fa-book-open',
                'Self-help' => 'fa-heart',
                'Khoa học' => 'fa-atom',
                'Kỹ năng' => 'fa-lightbulb',
                'Truyện ngắn' => 'fa-pen-fancy',
                'Triết học' => 'fa-brain',
                'Phi hư cấu' => 'fa-hat-wizard',
            ];
            foreach ($categories as $category): 
            ?>
            <a href="books.php?category=<?php echo urlencode($category); ?>" class="category-card">
                <div class="category-icon">
                    <i class="fas <?php echo $categoryIcons[$category] ?? 'fa-book'; ?>"></i>
                </div>
                <h4><?php echo htmlspecialchars($category); ?></h4>
            </a>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<?php require_once __DIR__ . '/../templates/footer.php'; ?>
