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
        <div class="section-header section-header--no-margin">
            <div class="section-header-left">
                <div class="section-icon">
                    <i class="fas fa-book-open"></i>
                </div>
                <div>
                    <h1 class="page-title"><?php echo $pageTitle; ?></h1>
                    <p class="page-subtitle"><?php echo count($books); ?> sách được tìm thấy</p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Filter & Search -->
<section class="filter-section">
    <div class="container">
        <form action="books.php" method="GET" class="filter-bar">
            <input type="text" name="search" placeholder="Tìm kiếm theo tên sách, tác giả..." 
                   value="<?php echo htmlspecialchars($search); ?>"
                   class="filter-input">
            <button type="submit" class="btn btn-primary">
                <i class="fas fa-search"></i> Tìm Kiếm
            </button>
            <?php if (!empty($search) || !empty($category)): ?>
            <a href="books.php" class="btn btn-outline">
                <i class="fas fa-times"></i> Xóa
            </a>
            <?php endif; ?>
        </form>
        
        <div class="categories-filter">
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
                <span class="book-discount available">
                    <i class="fas fa-check"></i> Còn hàng
                </span>
                <?php else: ?>
                <span class="book-discount unavailable">
                    <i class="fas fa-times"></i> Hết hàng
                </span>
                <?php endif; ?>
                
                <button class="book-wishlist" data-book-id="<?php echo $book['id']; ?>" title="Yêu Thích">
                    <i class="far fa-heart"></i>
                </button>
                
                <div class="book-image">
                    <img src="<?php echo getBookCoverLocal($book); ?>"
                         alt="<?php echo htmlspecialchars($book['title']); ?>"
                         loading="lazy">
                </div>
                
                <div class="book-info">
                    <span class="book-category"><?php echo htmlspecialchars((string)($book['category'] ?? 'Sách')); ?></span>
                    <h3 class="book-title"><?php echo htmlspecialchars((string)($book['title'] ?? '')); ?></h3>
                    <p class="book-author">
                        <i class="fas fa-user-edit"></i> <?php echo htmlspecialchars((string)($book['author'] ?? 'Tác giả')); ?>
                    </p>
                    
                    <div class="book-footer">
                        <div class="book-price">
                            <span class="book-price-current"><?php echo number_format($book['price_per_day'], 0); ?>đ</span>
                            <span class="book-price-original">/ngày</span>
                        </div>
                        <?php if ($book['quantity'] > 0): ?>
                        <a href="book-detail.php?id=<?php echo $book['id']; ?>" class="btn btn-sm">Thuê</a>
                        <?php else: ?>
                        <span class="text-muted small">Hết Hàng</span>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
        <?php else: ?>
        <div class="empty-state">
            <div class="empty-state-icon">
                <i class="fas fa-search"></i>
            </div>
            <h3>Không Tìm Thấy Sách</h3>
            <p class="section-subtitle">Thử điều chỉnh từ khóa tìm kiếm hoặc bộ lọc.</p>
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
