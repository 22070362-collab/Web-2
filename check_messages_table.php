<?php
try {
    $pdo = new PDO('mysql:host=localhost;dbname=web2book;charset=utf8mb4', 'root', '123456');
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $stmt = $pdo->query("SHOW TABLES LIKE 'messages'");
    $res = $stmt->fetchAll(PDO::FETCH_COLUMN);
    if (empty($res)) {
        echo 'NOT_FOUND';
    } else {
        echo 'FOUND';
    }
} catch (Exception $e) {
    echo 'ERROR: ' . $e->getMessage();
}
