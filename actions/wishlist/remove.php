<?php
require_once "../../includes/auth.php";
requireLogin();
require_once "../../includes/db.php";

$userId = $_SESSION["user_id"];
$productId = (int)($_POST["product_id"] ?? 0);

if ($productId <= 0) {
  header("Location: ../../pages/wishlist.php");
  exit;
}

try {
  $stmt = $pdo->prepare("
    DELETE FROM wishlist
    WHERE user_id = ? AND product_id = ?
  ");

  $stmt->execute([$userId, $productId]);

  header("Location: ../../pages/wishlist.php?success=removed");
  exit;

} catch (PDOException $e) {
  header("Location: ../../pages/wishlist.php?error=remove_failed");
  exit;
}