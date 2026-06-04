<?php
require_once "../../includes/auth.php";
requireAdmin();
require_once "../../includes/db.php";

$userId = (int)($_POST["user_id"] ?? 0);

if ($userId <= 0) {
  header("Location: ../../pages/manage-users.php");
  exit;
}

$stmt = $pdo->prepare("
  UPDATE users
  SET status = 'active'
  WHERE id = ?
");

$stmt->execute([$userId]);

header("Location: ../../pages/manage-users.php?success=activated");
exit;