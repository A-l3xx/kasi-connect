<?php
require_once "../includes/auth.php";
requireSeller();
require_once "../includes/db.php";

$fullName = $_SESSION["full_name"] ?? "Seller";
$initial = strtoupper(substr($fullName, 0, 1));
$sellerId = (int)$_SESSION["user_id"];

/* ACTIVE LISTINGS */

$activeStmt = $pdo->prepare("
  SELECT COUNT(*)
  FROM products
  WHERE seller_id = ?
  AND status = 'active'
");

$activeStmt->execute([$sellerId]);
$activeListings = (int)$activeStmt->fetchColumn();

/* REMOVED LISTINGS */

$removedStmt = $pdo->prepare("
  SELECT COUNT(*)
  FROM products
  WHERE seller_id = ?
  AND status = 'rejected'
");

$removedStmt->execute([$sellerId]);
$removedListings = (int)$removedStmt->fetchColumn();

$totalListings =
  $activeListings +
  $removedListings;

/* SELLER ORDERS */

$orderStmt = $pdo->prepare("
  SELECT DISTINCT orders.id,
  orders.total_amount,
  orders.payment_status
  FROM orders
  JOIN order_items
    ON orders.id = order_items.order_id
  WHERE order_items.seller_id = ?
");

$orderStmt->execute([$sellerId]);
$sellerOrders = $orderStmt->fetchAll(PDO::FETCH_ASSOC);

$totalOrders = count($sellerOrders);

$totalRevenue = 0;

foreach ($sellerOrders as $order) {

  if ($order["payment_status"] === "paid") {
    $totalRevenue += (float)$order["total_amount"];
  }

}
?>
<!DOCTYPE html>
<html lang="en">
<head>

  <meta charset="UTF-8">

  <meta
    name="viewport"
    content="width=device-width,initial-scale=1.0"
  >

  <title>Kasi Connect | Seller Dashboard</title>

  <link
    rel="stylesheet"
    href="../assets/css/style.css?v=950"
  >

  <link
    rel="stylesheet"
    href="../assets/css/seller-dashboard.css?v=950"
  >

  <link
    rel="stylesheet"
    href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css"
  >

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

      <div class="profile-menu">

        <button class="profile-toggle" type="button">

          <span class="profile-avatar-sm">
            <?= htmlspecialchars($initial) ?>
          </span>

          <span class="profile-name">
            <?= htmlspecialchars($fullName) ?>
          </span>

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
            Marketplace
          </a>

          <a
            href="../actions/auth/logout.php"
            class="logout-link"
          >
            <i class="fa-solid fa-right-from-bracket"></i>
            Logout
          </a>

        </div>

      </div>

    </nav>

  </div>

</header>

<main class="seller-page">

  <div class="seller-container">

    <section class="seller-hero">

      <div>

        <p class="eyebrow">
          SELLER DASHBOARD
        </p>

        <h1>
          Welcome back,
          <?= htmlspecialchars($fullName) ?> 👋
        </h1>

        <p>
          Manage your products, track orders,
          monitor sales performance and grow
          your local business.
        </p>

      </div>

      <div class="seller-hero-actions">

        <a
          href="add-listing.php"
          class="btn btn-primary"
        >
          <i class="fa-solid fa-plus"></i>
          <span>Add product/service</span>
        </a>

        <a
          href="marketplace.php"
          class="btn btn-light"
        >
          <i class="fa-solid fa-store"></i>
          <span>View marketplace</span>
        </a>

      </div>

    </section>

    <section class="seller-stats">

      <a
        href="manage-listings.php"
        class="seller-stat-card"
      >

        <i class="fa-solid fa-store"></i>

        <span>Live Listings</span>

        <strong>
          <?= (int)$activeListings ?>
        </strong>

        <p>
          Active marketplace listings
        </p>

      </a>

      <a
        href="seller-orders.php"
        class="seller-stat-card"
      >

        <i class="fa-solid fa-box-open"></i>

        <span>Orders Received</span>

        <strong>
          <?= (int)$totalOrders ?>
        </strong>

        <p>
          Customer purchases
        </p>

      </a>

      <div class="seller-stat-card">

        <i class="fa-solid fa-wallet"></i>

        <span>Sales Summary</span>

        <strong>
          R<?= number_format($totalRevenue, 2) ?>
        </strong>

        <p>
          Total seller revenue
        </p>

      </div>

      <div class="seller-stat-card">

        <i class="fa-solid fa-chart-line"></i>

        <span>Total Listings</span>

        <strong>
          <?= (int)$totalListings ?>
        </strong>

        <p>
          Active + removed listings
        </p>

      </div>

    </section>

    <section class="seller-grid">

      <article class="seller-card large">

        <div class="section-head">

          <div>

            <p class="eyebrow">
              SELLER TOOLS
            </p>

            <h2>Quick actions</h2>

          </div>

        </div>

        <div class="quick-actions">

          <a
            href="add-listing.php"
            class="quick-action"
          >

            <i class="fa-solid fa-plus"></i>

            <div>

              <strong>
                Add product/service
              </strong>

              <p>
                Create a new listing for
                the marketplace.
              </p>

            </div>

          </a>

          <a
            href="manage-listings.php"
            class="quick-action"
          >

            <i class="fa-solid fa-list-check"></i>

            <div>

              <strong>
                Manage listings
              </strong>

              <p>
                Edit products, prices,
                descriptions and stock.
              </p>

            </div>

          </a>

          <a
            href="seller-orders.php"
            class="quick-action"
          >

            <i class="fa-solid fa-box"></i>

            <div>

              <strong>
                Orders received
              </strong>

              <p>
                View customer orders
                and fulfilment status.
              </p>

            </div>

          </a>

          <a
            href="seller-profile.php"
            class="quick-action"
          >

            <i class="fa-solid fa-store"></i>

            <div>

              <strong>
                Business profile
              </strong>

              <p>
                Update your store and
                business information.
              </p>

            </div>

          </a>

          <a
            href="marketplace.php"
            class="quick-action"
          >

            <i class="fa-solid fa-shop"></i>

            <div>

              <strong>
                View marketplace
              </strong>

              <p>
                Browse listings as
                customers see them.
              </p>

            </div>

          </a>

        </div>

      </article>

      <article class="seller-card">

        <div class="section-head">

          <div>

            <p class="eyebrow">
              LISTING STATUS
            </p>

            <h2>Inventory</h2>

          </div>

        </div>

        <div class="inventory-list">

          <div class="inventory-item">

            <span class="dot active"></span>

            <div>

              <strong>
                Live listings
              </strong>

              <p>
                <?= (int)$activeListings ?>
                listing(s)
              </p>

            </div>

          </div>

          <div class="inventory-item">

            <span class="dot inactive"></span>

            <div>

              <strong>
                Removed listings
              </strong>

              <p>
                <?= (int)$removedListings ?>
                listing(s)
              </p>

            </div>

          </div>

        </div>

      </article>

    </section>

    <section class="seller-card">

      <div class="section-head">

        <div>

          <p class="eyebrow">
            PRODUCT PERFORMANCE
          </p>

          <h2>
            Listing overview
          </h2>

        </div>

      </div>

      <?php if ($totalListings === 0): ?>

        <div class="empty-seller-state">

          <i class="fa-solid fa-chart-simple"></i>

          <h3>
            No listings yet
          </h3>

          <p>
            Add your first product or
            service to start selling on
            the marketplace.
          </p>

          <a
            href="add-listing.php"
            class="btn btn-primary"
          >

            <i class="fa-solid fa-plus"></i>

            <span>
              Add first listing
            </span>

          </a>

        </div>

      <?php else: ?>

        <div class="inventory-list">

          <div class="inventory-item">

            <span class="dot active"></span>

            <div>

              <strong>
                <?= (int)$activeListings ?>
                live listing(s)
              </strong>

              <p>
                These listings are visible
                on the marketplace.
              </p>

            </div>

          </div>

          <div class="inventory-item">

            <span class="dot inactive"></span>

            <div>

              <strong>
                <?= (int)$removedListings ?>
                removed listing(s)
              </strong>

              <p>
                These listings were removed
                by admin moderation.
              </p>

            </div>

          </div>

        </div>

      <?php endif; ?>

    </section>

  </div>

</main>

<script src="../js/app.js"></script>
<script src="../js/logout.js"></script>

</body>
</html>