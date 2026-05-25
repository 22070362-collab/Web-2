<?php
/**
 * Database Test Script
 * Chạy để kiểm tra kết nối database
 */

require_once __DIR__ . '/../backend/config/database.php';

echo "=== Database Connection Test ===\n\n";

try {
    $db = getDB();
    
    // Test 1: Users
    $stmt = $db->query("SELECT COUNT(*) as count FROM users");
    $users = $stmt->fetch();
    echo "✅ Users: " . $users['count'] . " records\n";
    
    // Test 2: Books
    $stmt = $db->query("SELECT COUNT(*) as count FROM books");
    $books = $stmt->fetch();
    echo "✅ Books: " . $books['count'] . " records\n";
    
    // Test 3: Show books
    $stmt = $db->query("SELECT id, title, author, category, price_per_day FROM books LIMIT 5");
    echo "\n📚 Sample Books:\n";
    while ($row = $stmt->fetch()) {
        echo "   - [{$row['id']}] {$row['title']} - {$row['author']} ({$row['category']}) - {$row['price_per_day']}/day\n";
    }
    
    // Test 4: Coupons
    $stmt = $db->query("SELECT code, discount_percent FROM coupons");
    echo "\n🎟️ Active Coupons:\n";
    while ($row = $stmt->fetch()) {
        $discount = $row['discount_percent'] > 0 ? $row['discount_percent'] . '%' : 'Free ship';
        echo "   - {$row['code']}: $discount OFF\n";
    }
    
    echo "\n✅ Database connection is working!\n";
    
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
}
