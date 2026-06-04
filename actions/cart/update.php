<?php
require_once "../../includes/auth.php";
requireLogin();
require_once "../../includes/db.php";

$userId = $_SESSION["user_id"];
$productId = (int)($_POST["product_id"] ?? 0);
$action = $_POST["action"] ?? "";

if ($productId <= 0) {
  header("Location: ../../pages/cart.php");
  exit;
}

if ($action === "increase") {
  $stmt = $pdo->prepare("
    UPDATE cart_items
    SET quantity = quantity + 1
    WHERE user_id = ? AND product_id = ?
  ");
  $stmt->execute([$userId, $productId]);
}

if ($action === "decrease") {
  $stmt = $pdo->prepare("
    UPDATE cart_items
    SET quantity = GREATEST(quantity - 1, 1)
    WHERE user_id = ? AND product_id = ?
  ");
  $stmt->execute([$userId, $productId]);
}

header("Location: ../../pages/cart.php");
exit;