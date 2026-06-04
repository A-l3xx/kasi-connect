<?php
require_once "../../includes/auth.php";
requireAdmin();
require_once "../../includes/db.php";

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
  header("Location: ../../pages/approve-listings.php?error=invalid_request");
  exit;
}

$productId = (int)($_POST["product_id"] ?? 0);

if ($productId <= 0) {
  header("Location: ../../pages/approve-listings.php?error=invalid_listing");
  exit;
}

$stmt = $pdo->prepare("
  UPDATE products
  SET status = 'active'
  WHERE id = ?
");

$stmt->execute([$productId]);

header("Location: ../../pages/approve-listings.php?success=restored");
exit;