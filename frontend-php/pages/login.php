<?php
require_once __DIR__ . '/../templates/header.php';
$msg = '';
if($_SERVER['REQUEST_METHOD']==='POST'){
  // Demo only
  $msg = 'Demo login — integrate real auth';
}
?>
<div class="max-w-md">
  <h1 class="text-2xl font-bold mb-4">Sign in</h1>
  <?php if($msg): ?><div class="mb-4 text-green-400"><?= htmlspecialchars($msg) ?></div><?php endif; ?>
  <form method="post" class="space-y-4">
    <input name="email" placeholder="Email" class="w-full p-3 bg-[#0b0b0b] rounded" />
    <input name="password" type="password" placeholder="Password" class="w-full p-3 bg-[#0b0b0b] rounded" />
    <div class="flex items-center justify-between">
      <button class="btn-primary">Sign in</button>
    </div>
  </form>
</div>
<?php require_once __DIR__ . '/../templates/footer.php'; ?>