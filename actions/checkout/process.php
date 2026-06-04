<?php
require_once "../../includes/auth.php";
requireLogin();
require_once "../../includes/db.php";

$userId = $_SESSION["user_id"];

$customerName = trim($_POST["customer_name"] ?? "");
$customerPhone = trim($_POST["customer_phone"] ?? "");
$deliveryAddress = trim($_POST["delivery_address"] ?? "");
$paymentMethod = $_POST["payment_method"] ?? "card";

if ($customerName === "" || $customerPhone === "" || $deliveryAddress === "") {
  header("Location: ../../pages/checkout.php?error=missing_fields");
  exit;
}

try {
  $pdo->beginTransaction();

  $stmt = $pdo->prepare("
    SELECT 
      cart_items.quantity,
      products.id AS product_id,
      products.seller_id,
      products.price
    FROM cart_items
    JOIN products ON cart_items.product_id = products.id
    WHERE cart_items.user_id = ?
    AND products.status = 'active'
  ");

  $stmt->execute([$userId]);
  $cartItems = $stmt->fetchAll(PDO::FETCH_ASSOC);

  if (count($cartItems) === 0) {
    $pdo->rollBack();
    header("Location: ../../pages/cart.php");
    exit;
  }

  $subtotal = 0;

  foreach ($cartItems as $item) {
    $subtotal += (float)$item["price"] * (int)$item["quantity"];
  }

  $deliveryFee = 40;
  $serviceFee = 10;
  $totalAmount = $subtotal + $deliveryFee + $serviceFee;

  $orderNumber = "KC" . date("YmdHis") . rand(100, 999);

  $orderStmt = $pdo->prepare("
    INSERT INTO orders (
      user_id,
      order_number,
      subtotal,
      delivery_fee,
      service_fee,
      total_amount,
      payment_method,
      payment_status,
      order_status,
      customer_name,
      customer_phone,
      delivery_address
    )
    VALUES (?, ?, ?, ?, ?, ?, ?, 'paid', 'processing', ?, ?, ?)
  ");

  $orderStmt->execute([
    $userId,
    $orderNumber,
    $subtotal,
    $deliveryFee,
    $serviceFee,
    $totalAmount,
    $paymentMethod,
    $customerName,
    $customerPhone,
    $deliveryAddress
  ]);

  $orderId = $pdo->lastInsertId();

  $itemStmt = $pdo->prepare("
    INSERT INTO order_items (
      order_id,
      product_id,
      seller_id,
      quantity,
      price
    )
    VALUES (?, ?, ?, ?, ?)
  ");

  foreach ($cartItems as $item) {
    $itemStmt->execute([
      $orderId,
      $item["product_id"],
      $item["seller_id"],
      $item["quantity"],
      $item["price"]
    ]);
  }

  $clearCart = $pdo->prepare("
    DELETE FROM cart_items
    WHERE user_id = ?
  ");

  $clearCart->execute([$userId]);

  $pdo->commit();

  header("Location: ../../pages/orders.php?success=order_created");
  exit;

} catch (PDOException $e) {
  if ($pdo->inTransaction()) {
    $pdo->rollBack();
  }

  header("Location: ../../pages/checkout.php?error=checkout_failed");
  exit;
}