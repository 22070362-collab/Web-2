<?php
require_once __DIR__ . '/../../templates/admin_header.php';

$auth->requireAdmin();

require_once __DIR__ . '/../../../backend/controllers/BookController.php';

$bookController = new BookController();
$categories = $bookController->categories();
$message = '';
$messageType = '';

// Handle form
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    
    if ($action === 'add') {
        $result = $bookController->create([
            'title' => $_POST['title'],
            'author' => $_POST['author'],
            'description' => $_POST['description'],
            'category' => $_POST['category'] === '__new__' ? $_POST['new_category'] : $_POST['category'],
            'quantity' => intval($_POST['quantity']),
            'price_per_day' => floatval($_POST['price_per_day'])
        ]);
        $message = $result['message'];
        $messageType = $result['success'] ? 'success' : 'danger';
    } elseif ($action === 'update') {
        $result = $bookController->update(intval($_POST['id']), [
            'title' => $_POST['title'],
            'author' => $_POST['author'],
            'description' => $_POST['description'],
            'category' => $_POST['category'],
            'quantity' => intval($_POST['quantity']),
            'price_per_day' => floatval($_POST['price_per_day'])
        ]);
        $message = $result['message'];
        $messageType = $result['success'] ? 'success' : 'danger';
    } elseif ($action === 'delete') {
        $result = $bookController->delete(intval($_POST['id']));
        $message = $result['message'];
        $messageType = $result['success'] ? 'success' : 'danger';
    }
}

$books = $bookController->index();
$stats = $bookController->stats();
?>

<!-- Page Header -->
<div class="page-header">
    <div class="page-header-left">
        <div class="page-header-icon" style="background: var(--accent-light); color: var(--accent);">
            <i class="fas fa-book"></i>
        </div>
        <div>
            <h1 class="page-header-title">Sách</h1>
            <p class="page-header-subtitle">Quản lý kho sách</p>
        </div>
    </div>
    <div>
        <a href="index.php" class="btn btn-outline btn-sm">
            <i class="fas fa-arrow-left"></i> Quay về Bảng Điều Khiển
        </a>
    </div>
</div>

<!-- Stats -->
<div class="dashboard-stats-grid" style="grid-template-columns: repeat(3, 1fr);">
    <div class="stat-card-admin">
        <div class="stat-card-header">
            <div class="stat-card-icon green">
                <i class="fas fa-book"></i>
            </div>
        </div>
        <div class="stat-card-value"><?php echo $stats['total_books']; ?></div>
        <div class="stat-card-label">Tổng Sách</div>
    </div>
    
    <div class="stat-card-admin">
        <div class="stat-card-header">
            <div class="stat-card-icon purple">
                <i class="fas fa-check-circle"></i>
            </div>
        </div>
        <div class="stat-card-value"><?php echo $stats['available_books']; ?></div>
        <div class="stat-card-label">Còn Hàng</div>
    </div>
    
    <div class="stat-card-admin">
        <div class="stat-card-header">
            <div class="stat-card-icon orange">
                <i class="fas fa-layer-group"></i>
            </div>
        </div>
        <div class="stat-card-value"><?php echo count($categories); ?></div>
        <div class="stat-card-label">Danh Mục</div>
    </div>
</div>

<!-- Messages -->
<?php if ($message): ?>
<div class="alert alert-<?php echo $messageType; ?>">
    <i class="fas fa-<?php echo $messageType === 'success' ? 'check-circle' : 'exclamation-circle'; ?>"></i>
    <?php echo htmlspecialchars($message); ?>
</div>
<?php endif; ?>

<!-- Books Table -->
<div class="dashboard-card">
    <div class="dashboard-card-header">
        <h3 class="dashboard-card-title">Tất Cả Sách (<?php echo count($books); ?>)</h3>
        <a href="?action=add" class="btn btn-primary btn-sm">
            <i class="fas fa-plus"></i> Thêm Sách Mới
        </a>
    </div>
    
    <?php if (isset($_GET['action']) && $_GET['action'] === 'add'): ?>
    <!-- Add Book Form -->
    <div class="dashboard-card-body" style="background: var(--bg-secondary);">
        <h4 style="margin-bottom: 20px; color: var(--text-primary);">
            <i class="fas fa-plus-circle" style="color: var(--green-primary);"></i> Thêm Sách Mới
        </h4>
        <form method="POST">
            <input type="hidden" name="action" value="add">
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px;">
                <div class="form-group">
                    <label>Tiêu đề *</label>
                    <input type="text" name="title" class="form-control" required placeholder="Nhập tiêu đề sách">
                </div>
                <div class="form-group">
                    <label>Tác giả *</label>
                    <input type="text" name="author" class="form-control" required placeholder="Nhập tên tác giả">
                </div>
            </div>
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px;">
                <div class="form-group">
                    <label>Thể loại *</label>
                    <select name="category" class="form-control" required>
                        <?php foreach ($categories as $cat): ?>
                        <option value="<?php echo htmlspecialchars($cat); ?>"><?php echo htmlspecialchars($cat); ?></option>
                        <?php endforeach; ?>
                        <option value="__new__">+ Thêm thể loại mới</option>
                    </select>
                </div>
                <div class="form-group">
                    <label>Thêm thể loại mới</label>
                    <input type="text" name="new_category" class="form-control" placeholder="Nhập tên thể loại mới">
                </div>
            </div>
            <div class="form-group">
                <label>Mô tả</label>
                <textarea name="description" class="form-control" rows="3" placeholder="Nhập mô tả sách"></textarea>
            </div>
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px;">
                <div class="form-group">
                    <label>Số lượng *</label>
                    <input type="number" name="quantity" class="form-control" min="0" value="1" required>
                </div>
                <div class="form-group">
                    <label>Giá/ngày (VND) *</label>
                    <input type="number" name="price_per_day" class="form-control" min="0" step="1000" value="10000" required>
                </div>
            </div>
                <div style="display: flex; gap: 12px; margin-top: 8px;">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save"></i> Lưu sách
                </button>
                <a href="books.php" class="btn btn-outline">Hủy</a>
            </div>
        </form>
    </div>
    <?php endif; ?>
    
    <?php if (isset($_GET['action']) && $_GET['action'] === 'edit' && isset($_GET['id'])): ?>
    <!-- Edit Book Form -->
    <?php 
    $editBook = $bookController->show(intval($_GET['id']));
    if ($editBook):
    ?>
    <div class="dashboard-card-body" style="background: var(--bg-secondary);">
        <h4 style="margin-bottom: 20px; color: var(--text-primary);">
            <i class="fas fa-edit" style="color: var(--accent);"></i> Chỉnh Sửa Sách
        </h4>
        <form method="POST">
            <input type="hidden" name="action" value="update">
            <input type="hidden" name="id" value="<?php echo $editBook['id']; ?>">
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px;">
                <div class="form-group">
                    <label>Title *</label>
                    <input type="text" name="title" class="form-control" value="<?php echo htmlspecialchars($editBook['title']); ?>" required>
                </div>
                <div class="form-group">
                    <label>Author *</label>
                    <input type="text" name="author" class="form-control" value="<?php echo htmlspecialchars($editBook['author']); ?>" required>
                </div>
            </div>
            <div class="form-group">
                <label>Category *</label>
                <select name="category" class="form-control" required>
                    <?php foreach ($categories as $cat): ?>
                    <option value="<?php echo htmlspecialchars($cat); ?>" <?php echo $editBook['category'] === $cat ? 'selected' : ''; ?>><?php echo htmlspecialchars($cat); ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="form-group">
                <label>Description</label>
                <textarea name="description" class="form-control" rows="3"><?php echo htmlspecialchars($editBook['description']); ?></textarea>
            </div>
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px;">
                <div class="form-group">
                    <label>Quantity *</label>
                    <input type="number" name="quantity" class="form-control" min="0" value="<?php echo $editBook['quantity']; ?>" required>
                </div>
                <div class="form-group">
                    <label>Price per Day (VND) *</label>
                    <input type="number" name="price_per_day" class="form-control" min="0" step="1000" value="<?php echo $editBook['price_per_day']; ?>" required>
                </div>
            </div>
            <div style="display: flex; gap: 12px; margin-top: 8px;">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save"></i> Lưu Thay Đổi
                </button>
                <a href="books.php" class="btn btn-outline">Hủy</a>
            </div>
        </form>
    </div>
    <?php endif; ?>
    <?php endif; ?>
    
    <div class="dashboard-card-body no-padding">
        <?php if (count($books) > 0): ?>
        <table class="data-table">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Sách</th>
                    <th>Thể loại</th>
                    <th>Giá/ngày</th>
                    <th>Số lượng</th>
                    <th>Hành động</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($books as $index => $book): ?>
                <tr>
                    <td><?php echo $index + 1; ?></td>
                    <td>
                        <div class="table-book">
                            <img src="../../assets/images/<?php echo htmlspecialchars($book['cover_image'] ?? 'default_book.jpg'); ?>"
                                 alt="" class="table-book-cover"
                                 onerror="this.src='https://via.placeholder.com/36x48/242424/22c55e?text=B'">
                            <div class="table-book-info">
                                <div class="table-book-title"><?php echo htmlspecialchars($book['title']); ?></div>
                                <div class="table-book-author"><?php echo htmlspecialchars($book['author']); ?></div>
                            </div>
                        </div>
                    </td>
                    <td>
                        <span class="badge badge-info"><?php echo htmlspecialchars($book['category']); ?></span>
                    </td>
                    <td style="font-weight: 600; color: var(--green-primary);">
                        <?php echo number_format($book['price_per_day'], 0, ',', '.'); ?>đ
                    </td>
                    <td>
                        <?php if ($book['quantity'] > 0): ?>
                        <span class="badge badge-success"><?php echo $book['quantity']; ?></span>
                        <?php else: ?>
                        <span class="badge badge-danger">Hết hàng</span>
                        <?php endif; ?>
                    </td>
                    <td>
                        <div style="display: flex; gap: 8px;">
                            <a href="?action=edit&id=<?php echo $book['id']; ?>" class="btn btn-icon btn-success">
                                <i class="fas fa-edit"></i>
                            </a>
                            <form method="POST" style="display: inline;">
                                <input type="hidden" name="action" value="delete">
                                <input type="hidden" name="id" value="<?php echo $book['id']; ?>">
                                <button type="submit" class="btn btn-icon btn-danger" onclick="return confirm('Bạn có chắc muốn xóa sách này?')">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        <?php else: ?>
        <div style="text-align: center; padding: 60px 20px;">
            <i class="fas fa-book-open" style="font-size: 3rem; color: var(--text-muted); margin-bottom: 16px;"></i>
            <p style="color: var(--text-muted);">Chưa tìm thấy sách. Thêm sách đầu tiên!</p>
        </div>
        <?php endif; ?>
    </div>
</div>

<?php require_once __DIR__ . '/../../templates/admin_footer.php'; ?>
