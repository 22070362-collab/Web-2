<?php
/**
 * Project health check script
 * Verifies PHP, MySQL, and project setup
 */
echo "=== Web-2 Project Health Check ===\n\n";

// 1. PHP Check
echo "[1/3] PHP Check:\n";
echo "  PHP Version: " . phpversion() . "\n";
echo "  Extensions: ";
$required = ['pdo', 'pdo_mysql', 'json'];
$missing = [];
foreach ($required as $ext) {
    if (!extension_loaded($ext)) {
        $missing[] = $ext;
    }
}
if (empty($missing)) {
    echo "✓ All required extensions loaded\n";
} else {
    echo "✗ Missing: " . implode(', ', $missing) . "\n";
}

// 2. DB Check
echo "\n[2/3] Database Check:\n";
require_once __DIR__ . '/../backend/config/database.php';
try {
    $db = getDB();
    if ($db) {
        echo "  ✓ Connected to MySQL\n";
        echo "  Database: " . DB_NAME . " @ " . DB_HOST . "\n";
        try {
            $row = $db->query("SELECT VERSION() AS v")->fetch();
            echo "  MySQL: " . ($row['v'] ?? 'unknown') . "\n";
        } catch (Exception $e) {
            echo "  MySQL version query failed: " . $e->getMessage() . "\n";
        }
    } else {
        echo "  ✗ Database connection failed\n";
        exit(1);
    }
} catch (Throwable $e) {
    echo "  ✗ Error: " . $e->getMessage() . "\n";
    exit(1);
}

// 3. Files Check
echo "\n[3/3] Project Files Check:\n";
$checks = [
    'frontend/pages/index.php' => 'Home page',
    'frontend/pages/books.php' => 'Books page',
    'frontend/pages/book-detail.php' => 'Book detail page',
    'frontend/assets/css/style.css' => 'Main CSS',
    'frontend/assets/css/impeccable.css' => 'Theme CSS',
    'frontend/assets/js/main.js' => 'Main JS',
    'backend/api/api.php' => 'API endpoint',
    'database/web2book_complete.sql' => 'SQL dump',
];

$repoRoot = realpath(__DIR__ . '/..');
$missing = [];
foreach ($checks as $file => $label) {
    $path = $repoRoot . '/' . str_replace('/', '\\', $file);
    if (file_exists($path)) {
        echo "  ✓ $label\n";
    } else {
        echo "  ✗ $label (not found)\n";
        $missing[] = $file;
    }
}

echo "\n=== Summary ===\n";
if (empty($missing)) {
    echo "✓ All checks passed! Project is ready to run.\n";
    echo "\nStart the server with:\n";
    echo "  cd " . $repoRoot . "\n";
    echo "  php -S localhost:8000 -t frontend\n";
    echo "\nThen visit: http://localhost:8000/pages/index.php\n";
    exit(0);
} else {
    echo "✗ Some checks failed. Please verify the project setup.\n";
    exit(1);
}
