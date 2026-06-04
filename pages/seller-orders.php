<?php
require_once "../includes/auth.php";
requireSeller();
require_once "../includes/db.php";

$fullName = $_SESSION["full_name"] ?? "Seller";
$initial = strtoupper(substr($fullName, 0, 1));
$sellerId = (int)$_SESSION["user_id"];

$success = $_GET["success"] ?? "";

$stmt = $pdo->prepare("
  SELECT DISTINCT
    orders.id,
    orders.order_number,
    orders.customer_name,
    orders.customer_phone,
    orders.delivery_address,
    orders.total_amount,
    orders.payment_method,
    orders.payment_status,
    orders.order_status,
    orders.created_at,
    users.email AS customer_email
  FROM orders
  JOIN order_items ON orders.id = order_items.order_id
  JOIN users ON orders.user_id = users.id
  WHERE order_items.seller_id = ?
  ORDER BY orders.created_at DESC
");

$stmt->execute([$sellerId]);
$orders = $stmt->fetchAll(PDO::FETCH_ASSOC);

$totalOrders = count($orders);
$processingOrders = 0;
$completedOrders = 0;
$cancelledOrders = 0;
$totalRevenue = 0;

foreach ($orders as $order) {
  if ($order["order_status"] === "processing") {
    $processingOrders++;
  }

  if ($order["order_status"] === "completed") {
    $completedOrders++;
  }

  if ($order["order_status"] === "cancelled") {
    $cancelledOrders++;
  }

  if ($order["payment_status"] === "paid") {
    $totalRevenue += (float)$order["total_amount"];
  }
}

function statusClass($status) {
  if ($status === "completed") return "completed";
  if ($status === "processing") return "processing";
  if ($status === "cancelled") return "cancelled";
  return "pending";
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width,initial-scale=1.0">
  <title>Kasi Connect | Seller Orders</title>

  <link rel="stylesheet" href="../assets/css/style.css?v=660">
  <link rel="stylesheet" href="../assets/css/seller-orders.css?v=2">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
</head>
<body>

<header class="site-header">
  <div class="container nav-row">

    <div class="logo">
      <div class="logo-mark">K</div>
      <span>Kasi-Connect Seller</span>
    </div>

    <nav class="main-nav logged-nav">
      <a href="seller-dashboard.php">
        <i class="fa-solid fa-table-columns"></i>
        Dashboard
      </a>

      <a href="add-listing.php">
        <i class="fa-solid fa-plus"></i>
        Add Listing
      </a>

      <a href="manage-listings.php">
        <i class="fa-solid fa-list-check"></i>
        Listings
      </a>

      <a href="seller-orders.php">
        <i class="fa-solid fa-box"></i>
        Orders
      </a>

      <a href="marketplace.php">
        <i class="fa-solid fa-store"></i>
        Marketplace
      </a>

      <div class="profile-menu">
        <button class="profile-toggle" type="button">
          <span class="profile-avatar-sm"><?= htmlspecialchars($initial) ?></span>
          <span class="profile-name"><?= htmlspecialchars($fullName) ?></span>
          <i class="fa-solid fa-chevron-down"></i>
        </button>

        <div class="profile-dropdown">
          <a href="seller-profile.php">
            <i class="fa-solid fa-store"></i>
            Seller Profile
          </a>

          <a href="seller-orders.php">
            <i class="fa-solid fa-box"></i>
            Orders Received
          </a>

          <a href="manage-listings.php">
            <i class="fa-solid fa-list-check"></i>
            Manage Listings
          </a>

          <a href="marketplace.php">
            <i class="fa-solid fa-shop"></i>
            View Marketplace
          </a>

          <a href="../actions/auth/logout.php" class="logout-link">
            <i class="fa-solid fa-right-from-bracket"></i>
            Logout
          </a>
        </div>
      </div>
    </nav>

  </div>
</header>

<main class="seller-orders-page">
  <div class="seller-orders-container">

    <section class="seller-orders-hero">
      <div>
        <p class="eyebrow">ORDER FULFILMENT</p>
        <h1>Seller orders</h1>
        <p>View customer orders, update fulfilment status, and manage pickup or delivery progress.</p>
      </div>

      <a href="seller-dashboard.php" class="btn btn-light">
        <i class="fa-solid fa-arrow-left"></i>
        Back to dashboard
      </a>
    </section>

    <?php if ($success === "processing"): ?>
      <div class="auth-success">Order marked as processing.</div>
    <?php elseif ($success === "completed"): ?>
      <div class="auth-success">Order marked as completed.</div>
    <?php elseif ($success === "cancelled"): ?>
      <div class="auth-alert">Order has been cancelled.</div>
    <?php endif; ?>

    <section class="seller-order-stats">
      <div class="seller-order-stat">
        <i class="fa-solid fa-box"></i>
        <span>Total Orders</span>
        <strong><?= (int)$totalOrders ?></strong>
      </div>

      <div class="seller-order-stat">
        <i class="fa-solid fa-clock"></i>
        <span>Processing</span>
        <strong><?= (int)$processingOrders ?></strong>
      </div>

      <div class="seller-order-stat">
        <i class="fa-solid fa-circle-check"></i>
        <span>Completed</span>
        <strong><?= (int)$completedOrders ?></strong>
      </div>

      <div class="seller-order-stat">
        <i class="fa-solid fa-wallet"></i>
        <span>Revenue</span>
        <strong>R<?= number_format($totalRevenue, 2) ?></strong>
      </div>
    </section>

    <section class="seller-orders-toolbar">
      <div class="seller-orders-search">
        <i class="fa-solid fa-magnifying-glass"></i>
        <input type="text" placeholder="Search order number, customer, or status...">
      </div>

      <select>
        <option>All orders</option>
        <option>Pending</option>
        <option>Processing</option>
        <option>Completed</option>
        <option>Cancelled</option>
      </select>
    </section>

    <?php if (count($orders) === 0): ?>

      <section class="empty-seller-orders">
        <i class="fa-solid fa-box-open"></i>
        <h2>No seller orders yet</h2>
        <p>Orders for your products and services will appear here after customers checkout.</p>
      </section>

    <?php else: ?>

      <section class="seller-order-list">

        <?php foreach ($orders as $order): ?>

          <?php
            $itemsStmt = $pdo->prepare("
              SELECT
                order_items.quantity,
                order_items.price,
                products.title,
                products.category,
                products.image_class,
                products.image_path
              FROM order_items
              JOIN products ON order_items.product_id = products.id
              WHERE order_items.order_id = ?
              AND order_items.seller_id = ?
            ");

            $itemsStmt->execute([$order["id"], $sellerId]);
            $items = $itemsStmt->fetchAll(PDO::FETCH_ASSOC);
          ?>

          <article class="seller-order-card">

            <div class="seller-order-top">
              <div>
                <h3>Order #<?= htmlspecialchars($order["order_number"]) ?></h3>
                <p>
                  <?= htmlspecialchars(date("d M Y", strtotime($order["created_at"]))) ?>
                  • <?= count($items) ?> item(s)
                </p>
              </div>

              <span class="seller-order-status <?= htmlspecialchars(statusClass($order["order_status"])) ?>">
                <?= ucfirst(htmlspecialchars($order["order_status"])) ?>
              </span>
            </div>

            <div class="customer-box">
              <div>
                <span>Customer</span>
                <strong><?= htmlspecialchars($order["customer_name"]) ?></strong>
                <p><?= htmlspecialchars($order["customer_email"]) ?></p>
              </div>

              <div>
                <span>Phone</span>
                <strong><?= htmlspecialchars($order["customer_phone"]) ?></strong>
              </div>

              <div>
                <span>Address / note</span>
                <strong><?= htmlspecialchars($order["delivery_address"]) ?></strong>
              </div>
            </div>

            <div class="seller-order-items">

              <?php foreach ($items as $item): ?>
                <?php
                  $imageClass = $item["image_class"] ?: "peach";
                  $itemTotal = (float)$item["price"] * (int)$item["quantity"];
                ?>

                <div class="seller-order-item">

                  <?php if (!empty($item["image_path"])): ?>
                    <div class="seller-item-img real-img">
                      <img
                        src="../<?= htmlspecialchars($item["image_path"]) ?>"
                        alt="<?= htmlspecialchars($item["title"]) ?>"
                      >
                    </div>
                  <?php else: ?>
                    <div class="seller-item-img <?= htmlspecialchars($imageClass) ?>"></div>
                  <?php endif; ?>

                  <div>
                    <strong><?= htmlspecialchars($item["title"]) ?></strong>
                    <p><?= htmlspecialchars($item["category"]) ?> • Qty <?= (int)$item["quantity"] ?></p>
                  </div>

                  <span>R<?= number_format($itemTotal, 2) ?></span>
                </div>
              <?php endforeach; ?>

            </div>

            <div class="seller-order-footer">
              <div>
                <span>Payment</span>
                <strong>
                  <?= ucfirst(htmlspecialchars($order["payment_status"])) ?>
                  •
                  <?= ucfirst(htmlspecialchars($order["payment_method"])) ?>
                </strong>
              </div>

              <div>
                <span>Total Order</span>
                <strong>R<?= number_format((float)$order["total_amount"], 2) ?></strong>
              </div>

              <div class="seller-order-actions">

                <?php if (
                  $order["order_status"] !== "processing" &&
                  $order["order_status"] !== "completed" &&
                  $order["order_status"] !== "cancelled"
                ): ?>
                  <form action="../actions/seller/order-processing.php" method="POST">
                    <input type="hidden" name="order_id" value="<?= (int)$order["id"] ?>">

                    <button class="btn btn-primary" type="submit">
                      <i class="fa-solid fa-truck-fast"></i>
                      Mark processing
                    </button>
                  </form>
                <?php endif; ?>

                <?php if (
                  $order["order_status"] !== "completed" &&
                  $order["order_status"] !== "cancelled"
                ): ?>
                  <form action="../actions/seller/order-completed.php" method="POST">
                    <input type="hidden" name="order_id" value="<?= (int)$order["id"] ?>">

                    <button class="btn btn-light complete-btn" type="submit">
                      <i class="fa-solid fa-check"></i>
                      Complete
                    </button>
                  </form>
                <?php endif; ?>

                <?php if (
                  $order["order_status"] !== "completed" &&
                  $order["order_status"] !== "cancelled"
                ): ?>
                  <form action="../actions/seller/order-cancelled.php" method="POST">
                    <input type="hidden" name="order_id" value="<?= (int)$order["id"] ?>">

                    <button class="btn btn-light cancel-btn" type="submit">
                      <i class="fa-solid fa-xmark"></i>
                      Cancel
                    </button>
                  </form>
                <?php endif; ?>

              </div>
            </div>

          </article>

        <?php endforeach; ?>

      </section>

    <?php endif; ?>

  </div>
</main>

<script src="../js/app.js"></script>

</body>
</html>