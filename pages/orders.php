<?php
require_once "../includes/auth.php";
requireLogin();
require_once "../includes/db.php";

$fullName = $_SESSION["full_name"] ?? "User";
$initial = strtoupper(substr($fullName, 0, 1));
$userId = $_SESSION["user_id"];

$success = $_GET["success"] ?? "";

$stmt = $pdo->prepare("
  SELECT *
  FROM orders
  WHERE user_id = ?
  ORDER BY created_at DESC
");

$stmt->execute([$userId]);
$orders = $stmt->fetchAll(PDO::FETCH_ASSOC);

$totalOrders = count($orders);
$activeOrders = 0;
$completedOrders = 0;
$paidOrders = 0;

foreach ($orders as $order) {
  if ($order["order_status"] !== "completed" && $order["order_status"] !== "cancelled") {
    $activeOrders++;
  }

  if ($order["order_status"] === "completed") {
    $completedOrders++;
  }

  if ($order["payment_status"] === "paid") {
    $paidOrders++;
  }
}

function statusClass($status) {
  if ($status === "completed") return "dark";
  if ($status === "processing") return "orange";
  if ($status === "pending") return "green";
  return "dark";
}

function statusIcon($status) {
  if ($status === "completed") return "fa-circle-check";
  if ($status === "processing") return "fa-truck-fast";
  if ($status === "pending") return "fa-clock";
  return "fa-box";
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width,initial-scale=1.0">
  <title>Kasi Connect | Orders</title>

  <link rel="stylesheet" href="../assets/css/style.css?v=500">
  <link rel="stylesheet" href="../assets/css/orders.css?v=6">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
</head>
<body>

<header class="site-header">
  <div class="container nav-row">

    <div class="logo">
      <div class="logo-mark">K</div>
      <span>Kasi-Connect</span>
    </div>

    <nav class="main-nav logged-nav">
      <a href="home.php"><i class="fa-solid fa-house"></i> Home</a>
      <a href="dashboard.php"><i class="fa-solid fa-table-columns"></i> Dashboard</a>
      <a href="marketplace.php"><i class="fa-solid fa-store"></i> Marketplace</a>

      <div class="profile-menu">
        <button class="profile-toggle" type="button">
          <span class="profile-avatar-sm"><?= htmlspecialchars($initial) ?></span>
          <span class="profile-name"><?= htmlspecialchars($fullName) ?></span>
          <i class="fa-solid fa-chevron-down"></i>
        </button>

        <div class="profile-dropdown">
          <a href="orders.php"><i class="fa-solid fa-box"></i> Orders</a>
          <a href="wishlist.php"><i class="fa-solid fa-heart"></i> Wishlist</a>
          <a href="cart.php"><i class="fa-solid fa-cart-shopping"></i> Cart</a>
          <a href="profile.php"><i class="fa-solid fa-user"></i> Profile</a>

          <a href="login.php" class="logout-link logout-btn">
            <i class="fa-solid fa-right-from-bracket"></i>
            Logout
          </a>
        </div>
      </div>
    </nav>

  </div>
</header>

<main class="orders-page">
  <div class="orders-container">

    <section class="orders-head">
      <div>
        <p class="eyebrow">CUSTOMER ORDERS</p>
        <h1>My orders</h1>
        <p>Track purchases, message sellers, confirm pickup and review completed orders.</p>
      </div>

      <a href="marketplace.php" class="btn btn-primary">
        <i class="fa-solid fa-store"></i>
        Continue shopping
      </a>
    </section>

    <?php if ($success === "order_created"): ?>
      <div class="auth-success">
        Order placed successfully. Your cart has been cleared.
      </div>
    <?php endif; ?>

    <section class="orders-summary">
      <div class="summary-card">
        <i class="fa-solid fa-box-open"></i>
        <span>Total Orders</span>
        <strong><?= (int)$totalOrders ?></strong>
      </div>

      <div class="summary-card">
        <i class="fa-solid fa-truck-fast"></i>
        <span>Active</span>
        <strong><?= (int)$activeOrders ?></strong>
      </div>

      <div class="summary-card">
        <i class="fa-solid fa-circle-check"></i>
        <span>Completed</span>
        <strong><?= (int)$completedOrders ?></strong>
      </div>

      <div class="summary-card">
        <i class="fa-solid fa-credit-card"></i>
        <span>Paid</span>
        <strong><?= (int)$paidOrders ?></strong>
      </div>
    </section>

    <?php if (count($orders) === 0): ?>

      <section class="empty-orders">
        <i class="fa-solid fa-box-open"></i>
        <h2>No orders yet</h2>
        <p>Your completed checkout orders will appear here.</p>

        <a href="marketplace.php" class="btn btn-primary">
          Browse Marketplace
        </a>
      </section>

    <?php else: ?>

      <section class="orders-list">

        <?php foreach ($orders as $order): ?>

          <?php
            $itemsStmt = $pdo->prepare("
              SELECT 
                order_items.quantity,
                order_items.price,
                products.title,
                products.image_class,
                products.image_path,
                seller_profiles.business_name,
                users.full_name AS seller_full_name
              FROM order_items
              JOIN products ON order_items.product_id = products.id
              JOIN users ON order_items.seller_id = users.id
              LEFT JOIN seller_profiles ON seller_profiles.user_id = users.id
              WHERE order_items.order_id = ?
            ");

            $itemsStmt->execute([$order["id"]]);
            $items = $itemsStmt->fetchAll(PDO::FETCH_ASSOC);

            $firstItem = $items[0] ?? null;
            $sellerName = $firstItem
              ? ($firstItem["business_name"] ?: $firstItem["seller_full_name"])
              : "Seller";

            $imageClass = $firstItem["image_class"] ?? "peach";
            $status = $order["order_status"];
          ?>

          <article class="order-card">
            <div class="order-top">
              <div>
                <h3>Order #<?= htmlspecialchars($order["order_number"]) ?></h3>
                <p>
                  Placed <?= htmlspecialchars(date("d M Y", strtotime($order["created_at"]))) ?>
                  • Seller: <?= htmlspecialchars($sellerName) ?>
                </p>
              </div>

              <span class="order-status <?= htmlspecialchars(statusClass($status)) ?>">
                <i class="fa-solid <?= htmlspecialchars(statusIcon($status)) ?>"></i>
                <?= ucfirst(htmlspecialchars($status)) ?>
              </span>
            </div>

            <div class="order-body">

              <?php if ($firstItem && !empty($firstItem["image_path"])): ?>
                <div class="order-img real-img">
                  <img src="../<?= htmlspecialchars($firstItem["image_path"]) ?>" alt="<?= htmlspecialchars($firstItem["title"]) ?>">
                </div>
              <?php else: ?>
                <div class="order-img <?= htmlspecialchars($imageClass) ?>"></div>
              <?php endif; ?>

              <div class="order-info">
                <h4>
                  <?= $firstItem ? htmlspecialchars($firstItem["title"]) : "Order item" ?>
                </h4>

                <p>
                  <?= count($items) ?> item(s)
                  • <?= ucfirst(htmlspecialchars($order["payment_method"])) ?>
                  • Total: R<?= number_format((float)$order["total_amount"], 2) ?>
                </p>

                <div class="mini-progress">
                  <span class="done"></span>
                  <span class="done"></span>
                  <span class="<?= $status === "pending" ? "active" : "done" ?>"></span>
                  <span class="<?= $status === "processing" ? "active" : ($status === "completed" ? "done" : "") ?>"></span>
                  <span class="<?= $status === "completed" ? "done" : "" ?>"></span>
                </div>

                <small>
                  Payment: <?= ucfirst(htmlspecialchars($order["payment_status"])) ?>
                </small>
              </div>
            </div>

            <div class="order-actions">
              <button class="btn btn-primary open-track" type="button">
                <i class="fa-solid fa-location-dot"></i>
                Track order
              </button>

              <button class="btn btn-light open-message" type="button">
                <i class="fa-solid fa-message"></i>
                Message seller
              </button>

              <?php if ($status === "completed"): ?>
                <button class="btn btn-light open-review" type="button">
                  <i class="fa-solid fa-star"></i>
                  Leave review
                </button>
              <?php endif; ?>
            </div>
          </article>

        <?php endforeach; ?>

      </section>

    <?php endif; ?>

  </div>
</main>

<div class="modal" id="trackModal">
  <div class="modal-box">
    <button class="modal-close" type="button">×</button>
    <h2>Track Order</h2>

    <div class="track-steps">
      <div class="step done">✓ Order placed</div>
      <div class="step done">✓ Payment confirmed</div>
      <div class="step active">● Seller processing</div>
      <div class="step">○ Out for delivery / Ready for pickup</div>
      <div class="step">○ Completed</div>
    </div>
  </div>
</div>

<div class="modal" id="messageModal">
  <div class="modal-box">
    <button class="modal-close" type="button">×</button>
    <h2>Message Seller</h2>

    <div class="chat-box">
      <div class="chat-msg seller">Hi! Your order update will appear here.</div>
    </div>

    <div class="chat-input">
      <input type="text" placeholder="Type your message...">
      <button class="btn btn-primary" type="button">Send</button>
    </div>
  </div>
</div>

<div class="modal" id="reviewModal">
  <div class="modal-box">
    <button class="modal-close" type="button">×</button>
    <h2>Leave a Review</h2>

    <form>
      <label>Rating</label>
      <select class="form-control">
        <option>5 Stars</option>
        <option>4 Stars</option>
        <option>3 Stars</option>
        <option>2 Stars</option>
        <option>1 Star</option>
      </select>

      <label>Review</label>
      <textarea class="form-control textarea" placeholder="Write your experience..."></textarea>

      <button type="button" class="btn btn-primary full-btn">
        Submit Review
      </button>
    </form>
  </div>
</div>

<script src="../js/app.js"></script>
<script src="../js/logout.js"></script>
<script src="../js/orders.js"></script>

</body>
</html>