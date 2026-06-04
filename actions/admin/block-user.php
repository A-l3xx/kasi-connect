<?php
require_once "../../includes/auth.php";
requireAdmin();
require_once "../../includes/db.php";

$userId = (int)($_POST["user_id"] ?? 0);

if ($userId <= 0 || $userId === (int)$_SESSION["user_id"]) {
  header("Location: ../../pages/manage-users.php");
  exit;
}

$stmt = $pdo->prepare("
  UPDATE users
  SET status = 'blocked'
  WHERE id = ?
");

$stmt->execute([$userId]);

header("Location: ../../pages/manage-users.php?success=blocked");
exit;