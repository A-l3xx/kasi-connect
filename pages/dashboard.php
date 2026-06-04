<?php
require_once "../includes/auth.php";
requireLogin();

if (($_SESSION["role"] ?? "") === "seller") {
  header("Location: seller-dashboard.php");
  exit;
}

require_once "../includes/db.php";

$fullName = $_SESSION["full_name"] ?? "User";
$initial = strtoupper(substr($fullName, 0, 1));
$userId = (int)$_SESSION["user_id"];

/* CART COUNT */
$cartStmt = $pdo->prepare("
  SELECT COALESCE(SUM(quantity), 0)
  FROM cart_items
  WHERE user_id = ?
");
$cartStmt->execute([$userId]);
$cartCount = (int)$cartStmt->fetchColumn();

/* WISHLIST COUNT */
$wishlistStmt = $pdo->prepare("
  SELECT COUNT(*)
  FROM wishlist
  WHERE user_id = ?
");
$wishlistStmt->execute([$userId]);
$wishlistCount = (int)$wishlistStmt->fetchColumn();

/* TOTAL ORDERS */
$ordersStmt = $pdo->prepare("
  SELECT COUNT(*)
  FROM orders
  WHERE user_id = ?
");
$ordersStmt->execute([$userId]);
$orderCount = (int)$ordersStmt->fetchColumn();

/* ACTIVE ORDERS */
$activeOrdersStmt = $pdo->prepare("
  SELECT COUNT(*)
  FROM orders
  WHERE user_id = ?
  AND order_status NOT IN ('completed', 'cancelled')
");
$activeOrdersStmt->execute([$userId]);
$activeOrders = (int)$activeOrdersStmt->fetchColumn();

/* LATEST ORDER */
$latestOrderStmt = $pdo->prepare("
  SELECT *
  FROM orders
  WHERE user_id = ?
  ORDER BY created_at DESC
  LIMIT 1
");
$latestOrderStmt->execute([$userId]);
$latestOrder = $latestOrderStmt->fetch(PDO::FETCH_ASSOC);

/* RECENT ORDERS */
$recentOrdersStmt = $pdo->prepare("
  SELECT *
  FROM orders
  WHERE user_id = ?
  ORDER BY created_at DESC
  LIMIT 3
");
$recentOrdersStmt->execute([$userId]);
$recentOrders = $recentOrdersStmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width,initial-scale=1.0">
  <title>Kasi Connect | Dashboard</title>

  <link rel="stylesheet" href="../assets/css/style.css?v=740">
  <link rel="stylesheet" href="../assets/css/customer-dashboard.css?v=740">
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
          <a href="marketplace.php"><i class="fa-solid fa-store"></i> Marketplace</a>
          <a href="orders.php"><i class="fa-solid fa-box"></i> Orders</a>
          <a href="wishlist.php"><i class="fa-solid fa-heart"></i> Wishlist</a>
          <a href="cart.php"><i class="fa-solid fa-cart-shopping"></i> Cart</a>
          <a href="profile.php"><i class="fa-solid fa-user"></i> Profile</a>

          <a href="../actions/auth/logout.php" class="logout-link">
            <i class="fa-solid fa-right-from-bracket"></i>
            Logout
          </a>
        </div>
      </div>
    </nav>

  </div>
</header>

<main class="dashboard-page">
  <div class="dashboard-container">

    <section class="dashboard-hero">
      <div>
        <p class="eyebrow">CUSTOMER DASHBOARD</p>
        <h1>Hello, <?= htmlspecialchars($fullName) ?> 👋</h1>
        <p>Track orders, view your cart, manage wishlist items, and continue shopping locally.</p>
      </div>

      <a href="marketplace.php" class="btn btn-primary">
        <i class="fa-solid fa-store"></i>
        Continue shopping
      </a>
    </section>

    <section class="dashboard-stats">
      <a href="orders.php" class="dash-stat-card">
        <i class="fa-solid fa-box"></i>
        <span>Active Orders</span>
        <strong><?= (int)$activeOrders ?></strong>
        <p>Orders in progress</p>
      </a>

      <a href="wishlist.php" class="dash-stat-card">
        <i class="fa-solid fa-heart"></i>
        <span>Wishlist</span>
        <strong><?= (int)$wishlistCount ?></strong>
        <p>Saved products</p>
      </a>

      <a href="cart.php" class="dash-stat-card">
        <i class="fa-solid fa-cart-shopping"></i>
        <span>Cart</span>
        <strong><?= (int)$cartCount ?></strong>
        <p>Items ready for checkout</p>
      </a>

      <a href="orders.php" class="dash-stat-card">
        <i class="fa-solid fa-receipt"></i>
        <span>Total Orders</span>
        <strong><?= (int)$orderCount ?></strong>
        <p>All orders placed</p>
      </a>
    </section>

    <section class="dashboard-grid">

      <article class="dashboard-card large">
        <div class="section-head">
          <div>
            <p class="eyebrow">ORDER TRACKING</p>
            <h2>Current order status</h2>
          </div>

          <a href="orders.php">View all</a>
        </div>

        <?php if ($latestOrder): ?>

          <div class="tracking-card">
            <div class="tracking-top">
              <div>
                <h3>Order #<?= htmlspecialchars($latestOrder["order_number"]) ?></h3>
                <p>
                  Status: <?= ucfirst(htmlspecialchars($latestOrder["order_status"])) ?>
                  • Total: R<?= number_format((float)$latestOrder["total_amount"], 2) ?>
                </p>
              </div>

              <span class="status-pill orange">
                <i class="fa-solid fa-truck-fast"></i>
                <?= ucfirst(htmlspecialchars($latestOrder["order_status"])) ?>
              </span>
            </div>

            <div class="tracking-steps">
              <div class="track-step done">
                <span></span>
                <p>Placed</p>
              </div>

              <div class="track-step done">
                <span></span>
                <p>Paid</p>
              </div>

              <div class="track-step <?= $latestOrder["order_status"] === "pending" ? "active" : "done" ?>">
                <span></span>
                <p>Processing</p>
              </div>

              <div class="track-step <?= $latestOrder["order_status"] === "processing" ? "active" : ($latestOrder["order_status"] === "completed" ? "done" : "") ?>">
                <span></span>
                <p>Ready / Delivery</p>
              </div>

              <div class="track-step <?= $latestOrder["order_status"] === "completed" ? "done" : "" ?>">
                <span></span>
                <p>Completed</p>
              </div>
            </div>

            <div class="tracking-actions">
              <a href="orders.php" class="btn btn-light">
                <i class="fa-solid fa-location-dot"></i>
                Track order
              </a>

              <a href="orders.php" class="btn btn-primary">
                <i class="fa-solid fa-box"></i>
                View orders
              </a>
            </div>
          </div>

        <?php else: ?>

          <div class="tracking-card">
            <div class="tracking-top">
              <div>
                <h3>No orders yet</h3>
                <p>Your latest order will appear here after checkout.</p>
              </div>
            </div>

            <a href="marketplace.php" class="btn btn-primary">
              <i class="fa-solid fa-store"></i>
              Browse Marketplace
            </a>
          </div>

        <?php endif; ?>

      </article>

      <article class="dashboard-card">
        <div class="section-head">
          <div>
            <p class="eyebrow">QUICK ACCESS</p>
            <h2>Customer actions</h2>
          </div>
        </div>

        <div class="message-list">
          <a href="cart.php" class="message-item">
            <div class="message-avatar">C</div>
            <div>
              <strong>Cart</strong>
              <p><?= (int)$cartCount ?> item(s) waiting for checkout.</p>
            </div>
          </a>

          <a href="wishlist.php" class="message-item">
            <div class="message-avatar">W</div>
            <div>
              <strong>Wishlist</strong>
              <p><?= (int)$wishlistCount ?> saved product(s).</p>
            </div>
          </a>

          <a href="orders.php" class="message-item">
            <div class="message-avatar">O</div>
            <div>
              <strong>Orders</strong>
              <p><?= (int)$orderCount ?> order(s) placed.</p>
            </div>
          </a>
        </div>
      </article>

    </section>

    <section class="dashboard-card recent-orders">
      <div class="section-head">
        <div>
          <p class="eyebrow">RECENT ACTIVITY</p>
          <h2>Recent orders</h2>
        </div>

        <a href="orders.php">View orders</a>
      </div>

      <?php if (count($recentOrders) === 0): ?>

        <div class="tracking-card">
          <h3>No recent orders</h3>
          <p>Your recent orders will show here after checkout.</p>

          <a href="marketplace.php" class="btn btn-primary">
            <i class="fa-solid fa-store"></i>
            Start shopping
          </a>
        </div>

      <?php else: ?>

        <div class="recent-order-list">

          <?php foreach ($recentOrders as $order): ?>

            <div class="recent-order-row">
              <div class="recent-order-img peach"></div>

              <div>
                <h3>Order #<?= htmlspecialchars($order["order_number"]) ?></h3>
                <p>
                  <?= ucfirst(htmlspecialchars($order["payment_status"])) ?>
                  •
                  <?= ucfirst(htmlspecialchars($order["order_status"])) ?>
                </p>
              </div>

              <strong>R<?= number_format((float)$order["total_amount"], 2) ?></strong>
            </div>

          <?php endforeach; ?>

        </div>

      <?php endif; ?>

    </section>

  </div>
</main>

<script src="../js/app.js"></script>
<script src="../js/logout.js"></script>

</body>
</html>