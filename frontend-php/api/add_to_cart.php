<?php
if(session_status()===PHP_SESSION_NONE) session_start();
header('Content-Type: application/json');
$input = json_decode(file_get_contents('php://input'), true);
if(!$input || !isset($input['id'])){ echo json_encode(['success'=>false]); exit; }
$id = (int)$input['id']; $qty = isset($input['qty'])? (int)$input['qty'] : 1;
if($id<=0){ echo json_encode(['success'=>false]); exit; }
if(!isset($_SESSION['cart'])) $_SESSION['cart'] = [];
if(isset($_SESSION['cart'][$id])) $_SESSION['cart'][$id] += $qty; else $_SESSION['cart'][$id] = $qty;
echo json_encode(['success'=>true,'count'=>count($_SESSION['cart'])]);
