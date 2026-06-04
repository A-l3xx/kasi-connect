<?php
require_once "../../includes/auth.php";
requireSeller();
require_once "../../includes/db.php";

$sellerId = (int)$_SESSION["user_id"];
$orderId = (int)($_POST["order_id"] ?? 0);

if ($orderId <= 0) {
  header("Location: ../../pages/seller-orders.php");
  exit;
}

$stmt = $pdo->prepare("
  UPDATE orders
  SET order_status = 'completed'
  WHERE id = ?
  AND id IN (
    SELECT order_id
    FROM order_items
    WHERE seller_id = ?
  )
");

$stmt->execute([$orderId, $sellerId]);

header("Location: ../../pages/seller-orders.php?success=completed");
exit;