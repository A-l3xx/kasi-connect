<?php
require_once "../includes/auth.php";
requireAdmin();
require_once "../includes/db.php";

$fullName = $_SESSION["full_name"] ?? "Admin";
$initial = strtoupper(substr($fullName, 0, 1));

$success = $_GET["success"] ?? "";
$error = $_GET["error"] ?? "";

$stmt = $pdo->query("
  SELECT
    products.*,
    users.full_name AS seller_full_name,
    seller_profiles.business_name
  FROM products
  JOIN users ON products.seller_id = users.id
  LEFT JOIN seller_profiles ON seller_profiles.user_id = users.id
  WHERE users.role = 'seller'
  ORDER BY products.created_at DESC
");

$listings = $stmt->fetchAll(PDO::FETCH_ASSOC);

$activeCount = 0;
$removedCount = 0;

foreach ($listings as $listing) {
  if ($listing["status"] === "active") {
    $activeCount++;
  } elseif ($listing["status"] === "rejected") {
    $removedCount++;
  }
}

function listingStatusClass($status) {
  return $status === "active" ? "approved" : "rejected";
}

function listingStatusLabel($status) {
  return $status === "active" ? "Live" : "Removed";
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width,initial-scale=1.0">
  <title>Kasi Connect | Manage Listings</title>

  <link rel="stylesheet" href="../assets/css/style.css?v=910">
  <link rel="stylesheet" href="../assets/css/approve-listings.css?v=910">
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

<main class="listings-page">
  <div class="listings-container">

    <section class="listings-hero">
      <div>
        <p class="eyebrow">LISTING MANAGEMENT</p>
        <h1>Manage listings</h1>
        <p>View all seller products and services. Remove bad listings or restore removed ones.</p>
      </div>

      <a href="admin-dashboard.php" class="btn btn-light">
        <i class="fa-solid fa-arrow-left"></i>
        Back to dashboard
      </a>
    </section>

    <?php if ($success === "removed"): ?>
      <div class="auth-alert">Listing removed from marketplace.</div>
    <?php elseif ($success === "restored"): ?>
      <div class="auth-success">Listing restored to marketplace.</div>
    <?php elseif ($error): ?>
      <div class="auth-alert">Something went wrong.</div>
    <?php endif; ?>

    <section class="listing-stats">
      <div class="listing-stat-card">
        <i class="fa-solid fa-circle-check"></i>
        <span>Live Listings</span>
        <strong><?= (int)$activeCount ?></strong>
      </div>

      <div class="listing-stat-card">
        <i class="fa-solid fa-circle-xmark"></i>
        <span>Removed</span>
        <strong><?= (int)$removedCount ?></strong>
      </div>

      <div class="listing-stat-card">
        <i class="fa-solid fa-box-open"></i>
        <span>Total Listings</span>
        <strong><?= count($listings) ?></strong>
      </div>
    </section>

    <?php if (count($listings) === 0): ?>

      <section class="empty-admin-state">
        <i class="fa-solid fa-box-open"></i>
        <h3>No listings yet</h3>
        <p>Seller products and services will appear here.</p>
      </section>

    <?php else: ?>

      <section class="listing-review-grid">

        <?php foreach ($listings as $listing): ?>
          <?php
            $sellerName = $listing["business_name"] ?: $listing["seller_full_name"];
            $imageClass = $listing["image_class"] ?: "peach";
            $statusClass = listingStatusClass($listing["status"]);
            $statusLabel = listingStatusLabel($listing["status"]);
          ?>

          <article class="listing-review-card">

            <?php if (!empty($listing["image_path"])): ?>
              <div class="listing-img real-img">
                <img src="../<?= htmlspecialchars($listing["image_path"]) ?>" alt="<?= htmlspecialchars($listing["title"]) ?>">
              </div>
            <?php else: ?>
              <div class="listing-img <?= htmlspecialchars($imageClass) ?>"></div>
            <?php endif; ?>

            <div class="listing-review-content">
              <span class="status-pill <?= htmlspecialchars($statusClass) ?>">
                <i class="fa-solid fa-circle-info"></i>
                <?= htmlspecialchars($statusLabel) ?>
              </span>

              <h3><?= htmlspecialchars($listing["title"]) ?></h3>
              <p><?= htmlspecialchars($listing["description"]) ?></p>

              <div class="listing-meta">
                <span><i class="fa-solid fa-store"></i> <?= htmlspecialchars($sellerName) ?></span>
                <span><i class="fa-solid fa-layer-group"></i> <?= htmlspecialchars($listing["category"]) ?></span>
                <span><i class="fa-solid fa-location-dot"></i> <?= htmlspecialchars($listing["location_area"]) ?></span>
                <span><i class="fa-solid fa-tag"></i> <?= htmlspecialchars($listing["price_label"]) ?></span>
              </div>

              <div class="listing-actions">
                <?php if ($listing["status"] === "active"): ?>
                  <form action="../actions/admin/remove-listing.php" method="POST">
                    <input type="hidden" name="product_id" value="<?= (int)$listing["id"] ?>">
                    <button class="btn btn-light reject-btn" type="submit">
                      <i class="fa-solid fa-ban"></i>
                      Remove
                    </button>
                  </form>
                <?php else: ?>
                  <form action="../actions/admin/restore-listing.php" method="POST">
                    <input type="hidden" name="product_id" value="<?= (int)$listing["id"] ?>">
                    <button class="btn btn-primary" type="submit">
                      <i class="fa-solid fa-rotate-left"></i>
                      Restore
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
<script src="../js/logout.js"></script>

</body>
</html>