<?php
if(session_status()===PHP_SESSION_NONE) session_start();
if($_SERVER['REQUEST_METHOD']==='POST'){
  $id = isset($_POST['id'])? (int)$_POST['id']:0;
  if($id && isset($_SESSION['cart'][$id])) unset($_SESSION['cart'][$id]);
}
header('Location: /frontend-php/pages/cart.php');
