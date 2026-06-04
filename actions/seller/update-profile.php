<?php
require_once "../../includes/auth.php";
requireSeller();
require_once "../../includes/db.php";

$sellerId = (int)$_SESSION["user_id"];

$businessName = trim($_POST["business_name"] ?? "");
$businessCategory = trim($_POST["business_category"] ?? "");
$townshipArea = trim($_POST["township_area"] ?? "");
$phone = trim($_POST["phone"] ?? "");
$fulfilmentOption = trim($_POST["fulfilment_option"] ?? "");

if (
  $businessName === "" ||
  $businessCategory === "" ||
  $townshipArea === "" ||
  $phone === "" ||
  $fulfilmentOption === ""
) {
  header("Location: ../../pages/seller-profile.php?error=missing_fields");
  exit;
}

try {
  $pdo->beginTransaction();

  $userStmt = $pdo->prepare("
    UPDATE users
    SET phone = ?
    WHERE id = ?
    AND role = 'seller'
  ");

  $userStmt->execute([$phone, $sellerId]);

  $profileStmt = $pdo->prepare("
    UPDATE seller_profiles
    SET 
      business_name = ?,
      business_category = ?,
      township_area = ?,
      fulfilment_option = ?
    WHERE user_id = ?
  ");

  $profileStmt->execute([
    $businessName,
    $businessCategory,
    $townshipArea,
    $fulfilmentOption,
    $sellerId
  ]);

  $pdo->commit();

  header("Location: ../../pages/seller-profile.php?success=updated");
  exit;

} catch (PDOException $e) {
  if ($pdo->inTransaction()) {
    $pdo->rollBack();
  }

  header("Location: ../../pages/seller-profile.php?error=server_error");
  exit;
}