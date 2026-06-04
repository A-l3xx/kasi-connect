<?php
require_once "../includes/auth.php";
requireAdmin();
require_once "../includes/db.php";

$fullName = $_SESSION["full_name"] ?? "Admin";
$initial = strtoupper(substr($fullName, 0, 1));

$stmt = $pdo->query("
  SELECT 
    orders.*,
    users.full_name AS customer_name_db,
    users.email AS customer_email
  FROM orders
  JOIN users ON orders.user_id = users.id
  ORDER BY orders.created_at DESC
");

$orders = $stmt->fetchAll(PDO::FETCH_ASSOC);

$totalOrders = count($orders);
$paidOrders = 0;
$cancelledOrders = 0;
$revenue = 0;

foreach ($orders as $order) {
  if ($order["payment_status"] === "paid") {
    $paidOrders++;
    $revenue += (float)$order["total_amount"];
  }

  if ($order["order_status"] === "cancelled") {
    $cancelledOrders++;
  }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width,initial-scale=1.0">
  <title>Kasi Connect | Order Monitoring</title>

  <link rel="stylesheet" href="../assets/css/style.css?v=640">
  <link rel="stylesheet" href="../assets/css/manage-orders.css?v=2">
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
      <a href="admin-dashboard.php"><i class="fa-solid fa-table-columns"></i> Dashboard</a>
      <a href="approve-sellers.php"><i class="fa-solid fa-user-check"></i> Sellers</a>
      <a href="approve-listings.php"><i class="fa-solid fa-box-open"></i> Listings</a>

      <div class="profile-menu">
        <button class="profile-toggle" type="button">
          <span class="profile-avatar-sm"><?= htmlspecialchars($initial) ?></span>
          <span class="profile-name"><?= htmlspecialchars($fullName) ?></span>
          <i class="fa-solid fa-chevron-down"></i>
        </button>

        <div class="profile-dropdown">
          <a href="manage-orders.php"><i class="fa-solid fa-box"></i> Orders</a>
          <a href="manage-users.php"><i class="fa-solid fa-users"></i> Users</a>
          <a href="../actions/auth/logout.php" class="logout-link">
            <i class="fa-solid fa-right-from-bracket"></i> Logout
          </a>
        </div>
      </div>
    </nav>

  </div>
</header>

<main class="orders-admin-page">
  <div class="orders-admin-container">

    <section class="orders-admin-hero">
      <div>
        <p class="eyebrow">ORDER MONITORING</p>
        <h1>Platform orders</h1>
        <p>Monitor platform orders, view issues, identify suspicious activity, and support dispute handling.</p>
      </div>

      <a href="admin-dashboard.php" class="btn btn-light">
        <i class="fa-solid fa-arrow-left"></i>
        Back to dashboard
      </a>
    </section>

    <section class="orders-admin-stats">
      <div class="order-stat-card">
        <i class="fa-solid fa-box"></i>
        <span>Total Orders</span>
        <strong><?= (int)$totalOrders ?></strong>
      </div>

      <div class="order-stat-card">
        <i class="fa-solid fa-credit-card"></i>
        <span>Paid Orders</span>
        <strong><?= (int)$paidOrders ?></strong>
      </div>

      <div class="order-stat-card">
        <i class="fa-solid fa-triangle-exclamation"></i>
        <span>Cancelled</span>
        <strong><?= (int)$cancelledOrders ?></strong>
      </div>

      <div class="order-stat-card">
        <i class="fa-solid fa-wallet"></i>
        <span>Revenue</span>
        <strong>R<?= number_format($revenue, 2) ?></strong>
      </div>
    </section>

    <section class="orders-toolbar">
      <div class="orders-search">
        <i class="fa-solid fa-magnifying-glass"></i>
        <input type="text" placeholder="Search order number, customer, status...">
      </div>

      <select>
        <option>All orders</option>
        <option>Paid</option>
        <option>Pending</option>
        <option>Processing</option>
        <option>Cancelled</option>
      </select>
    </section>

    <section class="orders-table-card">
      <div class="table-head">
        <div>
          <p class="eyebrow">PLATFORM MONITORING</p>
          <h2>Order activity</h2>
        </div>
      </div>

      <?php if (count($orders) === 0): ?>

        <div class="empty-admin-state">
          <i class="fa-solid fa-box-open"></i>
          <h3>No platform orders yet</h3>
          <p>Customer orders will appear here after checkout.</p>
        </div>

      <?php else: ?>

        <div class="orders-table-wrap">
          <table class="orders-table">
            <thead>
              <tr>
                <th>Order</th>
                <th>Customer</th>
                <th>Total</th>
                <th>Payment</th>
                <th>Status</th>
                <th>Date</th>
                <th>Admin Action</th>
              </tr>
            </thead>

            <tbody>
              <?php foreach ($orders as $order): ?>
                <tr>
                  <td>
                    <strong>#<?= htmlspecialchars($order["order_number"]) ?></strong>
                    <span>ID: <?= (int)$order["id"] ?></span>
                  </td>

                  <td>
                    <strong><?= htmlspecialchars($order["customer_name_db"]) ?></strong>
                    <span><?= htmlspecialchars($order["customer_email"]) ?></span>
                  </td>

                  <td>
                    <strong>R<?= number_format((float)$order["total_amount"], 2) ?></strong>
                  </td>

                  <td>
                    <span class="payment-pill <?= htmlspecialchars($order["payment_status"]) ?>">
                      <?= ucfirst(htmlspecialchars($order["payment_status"])) ?>
                    </span>
                  </td>

                  <td>
                    <span class="order-status <?= htmlspecialchars($order["order_status"]) ?>">
                      <?= ucfirst(htmlspecialchars($order["order_status"])) ?>
                    </span>
                  </td>

                  <td>
                    <?= htmlspecialchars(date("d M Y", strtotime($order["created_at"]))) ?>
                  </td>

                  <td>
                    <div class="table-actions">
                      <button class="icon-btn" type="button" title="View order">
                        <i class="fa-solid fa-eye"></i>
                      </button>

                      <button class="icon-btn warning" type="button" title="Flag issue">
                        <i class="fa-solid fa-flag"></i>
                      </button>

                      <button class="icon-btn danger" type="button" title="Emergency cancel">
                        <i class="fa-solid fa-ban"></i>
                      </button>
                    </div>
                  </td>
                </tr>
              <?php endforeach; ?>
            </tbody>
          </table>
        </div>

      <?php endif; ?>
    </section>

  </div>
</main>

<script src="../js/app.js"></script>

</body>
</html>