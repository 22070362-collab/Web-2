<?php
require_once __DIR__ . '/../db/db.php';
require_once __DIR__ . '/../templates/header.php';
$pdo = getDB();
$id = isset($_GET['id'])? (int)$_GET['id'] : 0;
$stm = $pdo->prepare('SELECT * FROM products WHERE id = ?');
$stm->execute([$id]);
$p = $stm->fetch();
if(!$p){ echo '<div class="text-gray-400">Product not found</div>'; require_once __DIR__ . '/../templates/footer.php'; exit; }
?>
<div class="grid grid-cols-1 md:grid-cols-2 gap-8">
  <div class="w-full h-96 bg-gray-800 rounded-md"></div>
  <div>
    <h1 class="text-2xl font-bold"><?= htmlspecialchars($p['name']) ?></h1>
    <p class="text-gray-400 mt-2"><?= htmlspecialchars($p['description']) ?></p>
    <div class="mt-4 text-xl font-bold">$<?= number_format($p['price'],2) ?></div>
    <div class="mt-6 flex items-center gap-3">
      <button data-add-to-cart data-id="<?= $p['id'] ?>" class="btn-primary">Add to Cart</button>
      <a href="#" class="px-4 py-2 border border-gray-700 rounded-md">Wishlist</a>
    </div>
  </div>
</div>

<?php require_once __DIR__ . '/../templates/footer.php'; ?>