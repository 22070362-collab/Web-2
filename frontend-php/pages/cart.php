<?php
if(session_status()===PHP_SESSION_NONE) session_start();
require_once __DIR__ . '/../db/db.php';
require_once __DIR__ . '/../templates/header.php';
$cart = isset($_SESSION['cart'])? $_SESSION['cart'] : [];
$ids = array_map('intval', array_keys($cart));
$products = [];
if(count($ids)){
  $in = implode(',', array_fill(0,count($ids),'?'));
  $pdo = getDB();
  $stm = $pdo->prepare("SELECT * FROM products WHERE id IN ($in)");
  $stm->execute($ids);
  $rows = $stm->fetchAll();
  foreach($rows as $r) $products[$r['id']] = $r;
}
?>
<h1 class="text-2xl font-bold mb-4">Your Cart</h1>
<?php if(empty($cart)): ?>
  <div class="text-gray-400">Your cart is empty.</div>
<?php else: ?>
  <div class="space-y-4">
    <?php $total=0; foreach($cart as $id=>$qty): $p=$products[$id]; $line = $p['price']*$qty; $total += $line; ?>
      <div class="flex items-center justify-between bg-[#0b0b0b] p-4 rounded-md">
        <div>
          <div class="font-semibold"><?= htmlspecialchars($p['name']) ?></div>
          <div class="text-sm text-gray-400">Qty: <?= $qty ?></div>
        </div>
        <div class="text-right">
          <div class="font-bold">$<?= number_format($line,2) ?></div>
          <form method="post" action="/frontend-php/api/remove_from_cart.php" class="mt-2">
            <input type="hidden" name="id" value="<?= $id ?>">
            <button type="submit" class="text-sm text-red-500">Remove</button>
          </form>
        </div>
      </div>
    <?php endforeach; ?>
    <div class="flex items-center justify-between mt-4">
      <div class="text-lg font-semibold">Total</div>
      <div class="text-xl font-bold">$<?= number_format($total,2) ?></div>
    </div>
    <div class="flex gap-3 mt-4">
      <a href="#" class="btn-primary">Checkout</a>
      <form method="post" action="/frontend-php/api/clear_cart.php"><button class="px-4 py-2 border border-gray-700 rounded-md">Clear</button></form>
    </div>
  </div>
<?php endif; ?>

<?php require_once __DIR__ . '/../templates/footer.php'; ?>