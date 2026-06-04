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

try {
  $stmt = $pdo->prepare("
    INSERT IGNORE INTO wishlist (user_id, product_id)
    VALUES (?, ?)
  ");

  $stmt->execute([$userId, $productId]);

  header("Location: ../../pages/wishlist.php?success=added");
  exit;

} catch (PDOException $e) {
  header("Location: ../../pages/marketplace.php?error=wishlist_failed");
  exit;
}