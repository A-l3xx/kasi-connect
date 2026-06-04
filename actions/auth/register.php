<?php
session_start();
require_once "../../includes/db.php";

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
  header("Location: ../../pages/signup.php");
  exit;
}

$full_name = trim($_POST["full_name"] ?? "");
$email = trim($_POST["email"] ?? "");
$phone = trim($_POST["phone"] ?? "");
$password = $_POST["password"] ?? "";
$role = $_POST["role"] ?? "customer";

$business_name = trim($_POST["business_name"] ?? "");
$business_category = trim($_POST["business_category"] ?? "");
$township_area = trim($_POST["township_area"] ?? "");
$fulfilment_option = trim($_POST["fulfilment_option"] ?? "");

if ($full_name === "" || $email === "" || $phone === "" || $password === "") {
  header("Location: ../../pages/signup.php?error=missing_fields");
  exit;
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
  header("Location: ../../pages/signup.php?error=invalid_email");
  exit;
}

if (!in_array($role, ["customer", "seller"], true)) {
  $role = "customer";
}

if ($role === "seller") {
  if (
    $business_name === "" ||
    $business_category === "" ||
    $township_area === "" ||
    $fulfilment_option === ""
  ) {
    header("Location: ../../pages/signup.php?error=missing_fields");
    exit;
  }
}

$status = $role === "seller" ? "pending" : "active";
$password_hash = password_hash($password, PASSWORD_DEFAULT);

try {
  $pdo->beginTransaction();

  $check = $pdo->prepare("SELECT id FROM users WHERE email = ?");
  $check->execute([$email]);

  if ($check->fetch()) {
    $pdo->rollBack();
    header("Location: ../../pages/signup.php?error=email_exists");
    exit;
  }

  $stmt = $pdo->prepare("
    INSERT INTO users (full_name, email, phone, password_hash, role, status)
    VALUES (?, ?, ?, ?, ?, ?)
  ");

  $stmt->execute([
    $full_name,
    $email,
    $phone,
    $password_hash,
    $role,
    $status
  ]);

  $user_id = $pdo->lastInsertId();

  if ($role === "seller") {
    $sellerStmt = $pdo->prepare("
      INSERT INTO seller_profiles (
        user_id,
        business_name,
        business_category,
        township_area,
        fulfilment_option,
        approval_status
      )
      VALUES (?, ?, ?, ?, ?, 'pending')
    ");

    $sellerStmt->execute([
      $user_id,
      $business_name,
      $business_category,
      $township_area,
      $fulfilment_option
    ]);
  }

  $pdo->commit();

  header("Location: ../../pages/login.php?success=registered");
  exit;

} catch (PDOException $e) {
  if ($pdo->inTransaction()) {
    $pdo->rollBack();
  }

  header("Location: ../../pages/signup.php?error=server_error");
  exit;
}