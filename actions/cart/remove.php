<?php
require_once "../../includes/auth.php";
requireLogin();
require_once "../../includes/db.php";

$userId = $_SESSION["user_id"];
$productId = (int)($_POST["product_id"] ?? 0);

$stmt = $pdo->prepare("
  DELETE FROM cart_items
  WHERE user_id = ? AND product_id = ?
");

$stmt->execute([$userId, $productId]);

header("Location: ../../pages/cart.php");
exit;