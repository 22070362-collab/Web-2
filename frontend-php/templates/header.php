<?php if(session_status()===PHP_SESSION_NONE) session_start(); ?>
<!doctype html>
<html lang="vi">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <title>Brand — Premium</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <link rel="stylesheet" href="<?= (defined('BASE_URL')?BASE_URL:'') ?>/public/assets/css/styles.css">
</head>
<body class="bg-black text-white antialiased">
  <header class="w-full border-b border-gray-800">
    <div class="max-w-6xl mx-auto flex items-center justify-between p-4">
      <a href="<?= (defined('BASE_URL')?BASE_URL:'/frontend-php') ?>/index.php" class="text-2xl font-bold uppercase tracking-wider">Brand</a>
      <nav class="flex items-center gap-6">
        <a href="<?= (defined('BASE_URL')?BASE_URL:'/frontend-php') ?>/products.php" class="text-sm opacity-90 hover:opacity-100">Products</a>
        <a href="<?= (defined('BASE_URL')?BASE_URL:'/frontend-php') ?>/login.php" class="text-sm opacity-90 hover:opacity-100">Login</a>
        <a href="<?= (defined('BASE_URL')?BASE_URL:'/frontend-php') ?>/cart.php" class="px-3 py-1 border rounded-md border-gray-700">Cart <span id="cart-count" class="ml-2 text-sm bg-red-500 text-black rounded-full px-2"><?php echo isset($_SESSION['cart'])?count($_SESSION['cart']):0; ?></span></a>
      </nav>
    </div>
  </header>
  <main class="min-h-[60vh] max-w-6xl mx-auto w-full p-6">
