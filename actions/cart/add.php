<?php
require_once "../../includes/auth.php";
requireLogin();
require_once "../../includes/db.php";

$userId = $_SESSION["user_id"];
$productId = (int)($_POST["product_id"] ?? 0);

if ($productId <= 0) {
  header("Location: ../../pages/marketplace.php");
  exit;
}

$stmt = $pdo->prepare("
  INSERT INTO cart_items (user_id, product_id, quantity)
  VALUES (?, ?, 1)
  ON DUPLICATE KEY UPDATE quantity = quantity + 1
");

$stmt->execute([$userId, $productId]);

header("Location: ../../pages/cart.php");
exit;