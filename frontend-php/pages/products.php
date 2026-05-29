<?php
require_once __DIR__ . '/../db/db.php';
require_once __DIR__ . '/../templates/header.php';
$pdo = getDB();
$stm = $pdo->query('SELECT * FROM products ORDER BY name');
$products = $stm->fetchAll();
?>
<h1 class="text-3xl font-bold mb-6">All Products</h1>
<div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
  <?php foreach($products as $p): ?>
    <article class="card p-4">
      <a href="product.php?id=<?= $p['id'] ?>">
        <div class="w-full h-44 bg-gray-800 rounded-md mb-3 flex items-center justify-center text-gray-400">Image</div>
        <h3 class="font-semibold text-lg"><?= htmlspecialchars($p['name']) ?></h3>
        <p class="text-sm text-gray-400 mt-1"><?= htmlspecialchars($p['color']) ?></p>
        <div class="mt-3 flex items-center justify-between">
          <span class="font-bold">$<?= number_format($p['price'],2) ?></span>
        </div>
      </a>
    </article>
  <?php endforeach; ?>
</div>

<?php require_once __DIR__ . '/../templates/footer.php'; ?>