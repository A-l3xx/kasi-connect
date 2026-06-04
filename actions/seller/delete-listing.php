<?php
require_once "../../includes/auth.php";
requireSeller();
require_once "../../includes/db.php";

$sellerId = (int)$_SESSION["user_id"];
$productId = (int)($_POST["product_id"] ?? 0);

if ($productId <= 0) {
  header("Location: ../../pages/manage-listings.php");
  exit;
}

$stmt = $pdo->prepare("
  DELETE FROM products
  WHERE id = ?
  AND seller_id = ?
");

$stmt->execute([$productId, $sellerId]);

header("Location: ../../pages/manage-listings.php?success=deleted");
exit;