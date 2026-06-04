<?php
session_start();

require_once "../../includes/db.php";

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
  header("Location: ../../pages/login.php");
  exit;
}

$email = trim($_POST["email"] ?? "");
$password = $_POST["password"] ?? "";

if ($email === "" || $password === "") {
  header("Location: ../../pages/login.php?error=missing_fields");
  exit;
}

try {

  $stmt = $pdo->prepare("
    SELECT *
    FROM users
    WHERE email = ?
    LIMIT 1
  ");

  $stmt->execute([$email]);

  $user = $stmt->fetch(PDO::FETCH_ASSOC);

  /* USER NOT FOUND */

  if (!$user) {
    header("Location: ../../pages/login.php?error=invalid_credentials");
    exit;
  }

  /* INVALID PASSWORD */

  if (!password_verify($password, $user["password_hash"])) {
    header("Location: ../../pages/login.php?error=invalid_credentials");
    exit;
  }

  /* BLOCKED ACCOUNT */

  if (
    isset($user["status"]) &&
    $user["status"] === "blocked"
  ) {
    header("Location: ../../pages/login.php?error=account_blocked");
    exit;
  }

  /* CREATE SESSION */

  $_SESSION["user_id"] = $user["id"];
  $_SESSION["full_name"] = $user["full_name"];
  $_SESSION["email"] = $user["email"];
  $_SESSION["role"] = $user["role"];

  /* ADMIN */

  if ($user["role"] === "admin") {

    header("Location: ../../pages/admin-dashboard.php");
    exit;
  }

  /* SELLER */

  if ($user["role"] === "seller") {

    if (
      isset($user["status"]) &&
      $user["status"] === "pending"
    ) {

      header("Location: ../../pages/login.php?error=seller_pending");
      exit;
    }

    header("Location: ../../pages/seller-dashboard.php");
    exit;
  }

  /* CUSTOMER */

  if ($user["role"] === "customer") {

    header("Location: ../../pages/home.php");
    exit;
  }

  /* INVALID ROLE */

  header("Location: ../../pages/login.php?error=invalid_role");
  exit;

} catch (PDOException $e) {

  header("Location: ../../pages/login.php?error=server_error");
  exit;
}