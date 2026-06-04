<?php
require_once "../includes/auth.php";
requireAdmin();
require_once "../includes/db.php";

$fullName = $_SESSION["full_name"] ?? "Admin";
$initial = strtoupper(substr($fullName, 0, 1));

/* TOTAL USERS */
$totalUsersStmt = $pdo->query("
  SELECT COUNT(*)
  FROM users
");
$totalUsers = (int)$totalUsersStmt->fetchColumn();

/* PENDING SELLERS */
$pendingSellersStmt = $pdo->query("
  SELECT COUNT(*)
  FROM users
  WHERE role = 'seller'
  AND status = 'pending'
");
$pendingSellers = (int)$pendingSellersStmt->fetchColumn();

/* PENDING LISTINGS */
$pendingListingsStmt = $pdo->query("
  SELECT COUNT(*)
  FROM products
  WHERE status = 'pending'
");
$pendingListings = (int)$pendingListingsStmt->fetchColumn();

/* TOTAL REVENUE */
$revenueStmt = $pdo->query("
  SELECT COALESCE(SUM(total_amount), 0)
  FROM orders
  WHERE payment_status = 'paid'
");
$totalRevenue = (float)$revenueStmt->fetchColumn();

/* TOTAL ORDERS */
$ordersStmt = $pdo->query("
  SELECT COUNT(*)
  FROM orders
");
$totalOrders = (int)$ordersStmt->fetchColumn();

/* RECENT ORDERS */
$recentOrdersStmt = $pdo->query("
  SELECT order_number, total_amount, order_status, created_at
  FROM orders
  ORDER BY created_at DESC
  LIMIT 3
");
$recentOrders = $recentOrdersStmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width,initial-scale=1.0">

  <title>Kasi Connect | Admin Dashboard</title>

  <link rel="stylesheet" href="../assets/css/style.css?v=600">
  <link rel="stylesheet" href="../assets/css/admin.dashboard.css?v=600">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
</head>
<body>

<header class="site-header">
  <div class="container nav-row">

    <div class="logo">
      <div class="logo-mark">K</div>
      <span>Kasi Admin</span>
    </div>

    <nav class="main-nav logged-nav">
      <a href="admin-dashboard.php">
        <i class="fa-solid fa-table-columns"></i>
        Dashboard
      </a>

      <a href="approve-sellers.php">
        <i class="fa-solid fa-user-check"></i>
        Sellers
      </a>

      <a href="approve-listings.php">
        <i class="fa-solid fa-box-open"></i>
        Listings
      </a>

      <div class="profile-menu">
        <button class="profile-toggle" type="button">
          <span class="profile-avatar-sm"><?= htmlspecialchars($initial) ?></span>
          <span class="profile-name"><?= htmlspecialchars($fullName) ?></span>
          <i class="fa-solid fa-chevron-down"></i>
        </button>

        <div class="profile-dropdown">
          <a href="manage-orders.php">
            <i class="fa-solid fa-box"></i>
            Orders
          </a>

          <a href="manage-users.php">
            <i class="fa-solid fa-users"></i>
            Users
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

<main class="admin-page">
  <div class="admin-container">

    <section class="admin-hero">
      <div>
        <p class="eyebrow">ADMIN CONTROL PANEL</p>

        <h1>Welcome back, <?= htmlspecialchars($fullName) ?> 👋</h1>

        <p>
          Monitor the marketplace, manage users, approve sellers,
          moderate listings, and oversee the Kasi Connect ecosystem.
        </p>
      </div>
    </section>

    <section class="admin-stats">
      <div class="admin-stat-card">
        <i class="fa-solid fa-users"></i>
        <span>Total Users</span>
        <strong><?= (int)$totalUsers ?></strong>
        <p>Registered accounts</p>
      </div>

      <div class="admin-stat-card">
        <i class="fa-solid fa-user-check"></i>
        <span>Pending Sellers</span>
        <strong><?= (int)$pendingSellers ?></strong>
        <p>Awaiting approval</p>
      </div>

      <div class="admin-stat-card">
        <i class="fa-solid fa-box-open"></i>
        <span>Pending Listings</span>
        <strong><?= (int)$pendingListings ?></strong>
        <p>Awaiting moderation</p>
      </div>

      <div class="admin-stat-card">
        <i class="fa-solid fa-wallet"></i>
        <span>Revenue</span>
        <strong>R<?= number_format($totalRevenue, 2) ?></strong>
        <p>Total paid orders</p>
      </div>
    </section>

    <section class="admin-grid">

      <article class="admin-card large">
        <div class="section-head">
          <div>
            <p class="eyebrow">ADMIN ACTIONS</p>
            <h2>Management tools</h2>
          </div>
        </div>

        <div class="admin-actions">

          <a href="approve-sellers.php" class="admin-action">
            <i class="fa-solid fa-user-check"></i>
            <div>
              <strong>Approve sellers</strong>
              <p><?= (int)$pendingSellers ?> seller application(s) waiting.</p>
            </div>
          </a>

          <a href="approve-listings.php" class="admin-action">
            <i class="fa-solid fa-box-open"></i>
            <div>
              <strong>Approve listings</strong>
              <p><?= (int)$pendingListings ?> listing(s) waiting for review.</p>
            </div>
          </a>

          <a href="manage-orders.php" class="admin-action">
            <i class="fa-solid fa-box"></i>
            <div>
              <strong>Manage orders</strong>
              <p><?= (int)$totalOrders ?> order(s) created on the platform.</p>
            </div>
          </a>

          <a href="manage-users.php" class="admin-action">
            <i class="fa-solid fa-users"></i>
            <div>
              <strong>Manage users</strong>
              <p><?= (int)$totalUsers ?> user account(s) registered.</p>
            </div>
          </a>

        </div>
      </article>

      <article class="admin-card">
        <div class="section-head">
          <div>
            <p class="eyebrow">MODERATION</p>
            <h2>Platform status</h2>
          </div>
        </div>

        <div class="status-list">
          <div class="status-item">
            <span class="dot green"></span>
            <div>
              <strong>Marketplace</strong>
              <p>Operational</p>
            </div>
          </div>

          <div class="status-item">
            <span class="dot orange"></span>
            <div>
              <strong>Seller approvals</strong>
              <p><?= (int)$pendingSellers ?> pending request(s)</p>
            </div>
          </div>

          <div class="status-item">
            <span class="dot dark"></span>
            <div>
              <strong>Listings queue</strong>
              <p><?= (int)$pendingListings ?> pending review(s)</p>
            </div>
          </div>
        </div>
      </article>

    </section>

    <section class="admin-card">
      <div class="section-head">
        <div>
          <p class="eyebrow">ACTIVITY</p>
          <h2>Recent orders</h2>
        </div>

        <a href="manage-orders.php">View all</a>
      </div>

      <?php if (count($recentOrders) === 0): ?>

        <div class="empty-admin-state">
          <i class="fa-solid fa-chart-line"></i>
          <h3>No recent activity yet</h3>
          <p>
            Seller approvals, listing moderation, and customer orders
            will appear here.
          </p>
        </div>

      <?php else: ?>

        <div class="status-list">
          <?php foreach ($recentOrders as $order): ?>
            <div class="status-item">
              <span class="dot green"></span>

              <div>
                <strong>Order #<?= htmlspecialchars($order["order_number"]) ?></strong>
                <p>
                  <?= ucfirst(htmlspecialchars($order["order_status"])) ?>
                  • R<?= number_format((float)$order["total_amount"], 2) ?>
                  • <?= htmlspecialchars(date("d M Y", strtotime($order["created_at"]))) ?>
                </p>
              </div>
            </div>
          <?php endforeach; ?>
        </div>

      <?php endif; ?>
    </section>

  </div>
</main>

<script src="../js/app.js"></script>

</body>
</html>