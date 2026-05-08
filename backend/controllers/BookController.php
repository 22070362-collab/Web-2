<?php
/**
 * Book Controller
 * Xử lý các thao tác với sách
 */

require_once __DIR__ . '/../models/Book.php';

class BookController {
    private $bookModel;

    public function __construct() {
        $this->bookModel = new Book();
    }

    public function index($limit = 100, $offset = 0) {
        return $this->bookModel->getAll($limit, $offset);
    }

    public function available($limit = 100, $offset = 0) {
        return $this->bookModel->getAvailable($limit, $offset);
    }

    public function show($id) {
        return $this->bookModel->findById($id);
    }

    public function search($keyword, $limit = 100) {
        if (empty($keyword)) {
            return [];
        }
        return $this->bookModel->search($keyword, $limit);
    }

    public function category($category, $limit = 100) {
        return $this->bookModel->getByCategory($category, $limit);
    }

    public function categories() {
        return $this->bookModel->getCategories();
    }

    public function create($data) {
        if (empty($data['title']) || empty($data['author']) || empty($data['category'])) {
            return ['success' => false, 'message' => 'Vui lòng nhập đầy đủ thông tin bắt buộc'];
        }

        $id = $this->bookModel->create([
            'title' => $data['title'],
            'author' => $data['author'],
            'description' => $data['description'] ?? '',
            'category' => $data['category'],
            'cover_image' => $data['cover_image'] ?? 'default_book.jpg',
            'quantity' => intval($data['quantity'] ?? 1),
            'price_per_day' => floatval($data['price_per_day'] ?? 1.00)
        ]);

        if ($id) {
            return ['success' => true, 'message' => 'Thêm sách thành công', 'id' => $id];
        }

        return ['success' => false, 'message' => 'Thêm sách thất bại'];
    }

    public function update($id, $data) {
        if (empty($data['title']) || empty($data['author']) || empty($data['category'])) {
            return ['success' => false, 'message' => 'Vui lòng nhập đầy đủ thông tin bắt buộc'];
        }

        $result = $this->bookModel->update($id, [
            'title' => $data['title'],
            'author' => $data['author'],
            'description' => $data['description'] ?? '',
            'category' => $data['category'],
            'cover_image' => $data['cover_image'] ?? 'default_book.jpg',
            'quantity' => intval($data['quantity'] ?? 1),
            'price_per_day' => floatval($data['price_per_day'] ?? 1.00),
            'is_available' => isset($data['is_available']) ? 1 : 0
        ]);

        if ($result) {
            return ['success' => true, 'message' => 'Cập nhật sách thành công'];
        }

        return ['success' => false, 'message' => 'Cập nhật sách thất bại'];
    }

    public function delete($id) {
        if ($this->bookModel->delete($id)) {
            return ['success' => true, 'message' => 'Xóa sách thành công'];
        }
        return ['success' => false, 'message' => 'Xóa sách thất bại'];
    }

    public function stats() {
        return $this->bookModel->getStats();
    }
}
