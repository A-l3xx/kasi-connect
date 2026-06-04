<?php
require_once "../../includes/auth.php";
requireAdmin();
require_once "../../includes/db.php";

$userId = (int)($_POST["user_id"] ?? 0);

if ($userId <= 0) {
  header("Location: ../../pages/approve-sellers.php");
  exit;
}

$stmt = $pdo->prepare("
  UPDATE users
  SET status = 'active'
  WHERE id = ?
  AND role = 'seller'
");
$stmt->execute([$userId]);

$profileStmt = $pdo->prepare("
  UPDATE seller_profiles
  SET approval_status = 'approved'
  WHERE user_id = ?
");
$profileStmt->execute([$userId]);

header("Location: ../../pages/approve-sellers.php?success=approved");
exit;