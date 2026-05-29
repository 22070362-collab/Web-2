<?php
require_once __DIR__ . '/../db/db.php';
require_once __DIR__ . '/../templates/header.php';
$pdo = getDB();
$stm = $pdo->query('SELECT * FROM products ORDER BY created_at DESC LIMIT 4');
$products = $stm->fetchAll();
?>
<section class="py-12 bg-gradient-to-r from-black via-[#111] to-black text-white rounded-lg">
  <div class="flex flex-col md:flex-row items-center gap-8">
    <div class="flex-1">
      <h1 class="text-4xl md:text-6xl font-bold tracking-tight">Elevated Performance. Minimal Design.</h1>
      <p class="mt-4 text-gray-300 max-w-xl">Premium footwear inspired by athletic heritage — clean silhouettes, bold details.</p>
      <div class="mt-6">
        <a href="products.php" class="btn-primary">Shop New</a>
      </div>
    </div>
    <div class="flex-1">
      <div class="w-full h-64 bg-gradient-to-br from-gray-800 to-gray-900 rounded-lg"></div>
    </div>
  </div>
</section>

<section class="mt-12">
  <div class="flex items-center justify-between mb-6">
    <h2 class="text-2xl font-bold">Featured</h2>
    <a href="products.php" class="text-sm text-gray-400">View all</a>
  </div>
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
</section>

<?php require_once __DIR__ . '/../templates/footer.php'; ?>
