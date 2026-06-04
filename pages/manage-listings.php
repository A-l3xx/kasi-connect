<?php
require_once "../includes/auth.php";
requireSeller();
require_once "../includes/db.php";

$fullName = $_SESSION["full_name"] ?? "Seller";
$initial = strtoupper(substr($fullName, 0, 1));
$sellerId = (int)$_SESSION["user_id"];

$success = $_GET["success"] ?? "";

$stmt = $pdo->prepare("
  SELECT *
  FROM products
  WHERE seller_id = ?
  ORDER BY created_at DESC
");

$stmt->execute([$sellerId]);
$listings = $stmt->fetchAll(PDO::FETCH_ASSOC);

$liveCount = 0;
$removedCount = 0;

foreach ($listings as $listing) {
  if ($listing["status"] === "active") {
    $liveCount++;
  } elseif ($listing["status"] === "rejected") {
    $removedCount++;
  }
}

function statusClass($status) {
  if ($status === "active") return "active";
  if ($status === "rejected") return "rejected";
  return "inactive";
}

function statusLabel($status) {
  if ($status === "active") return "Live";
  if ($status === "rejected") return "Removed by admin";
  return ucfirst($status);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width,initial-scale=1.0">
  <title>Kasi Connect | Manage Listings</title>

  <link rel="stylesheet" href="../assets/css/style.css?v=960">
  <link rel="stylesheet" href="../assets/css/manage-listings.css?v=960">
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
      <a href="seller-dashboard.php"><i class="fa-solid fa-table-columns"></i> Dashboard</a>
      <a href="add-listing.php"><i class="fa-solid fa-plus"></i> Add Listing</a>
      <a href="manage-listings.php"><i class="fa-solid fa-list-check"></i> Listings</a>

      <div class="profile-menu">
        <button class="profile-toggle" type="button">
          <span class="profile-avatar-sm"><?= htmlspecialchars($initial) ?></span>
          <span class="profile-name"><?= htmlspecialchars($fullName) ?></span>
          <i class="fa-solid fa-chevron-down"></i>
        </button>

        <div class="profile-dropdown">
          <a href="seller-profile.php"><i class="fa-solid fa-store"></i> Seller Profile</a>
          <a href="seller-orders.php"><i class="fa-solid fa-box"></i> Orders Received</a>
          <a href="marketplace.php"><i class="fa-solid fa-store"></i> Marketplace</a>
          <a href="../actions/auth/logout.php" class="logout-link">
            <i class="fa-solid fa-right-from-bracket"></i>
            Logout
          </a>
        </div>
      </div>
    </nav>

  </div>
</header>

<main class="manage-listings-page">
  <div class="manage-listings-container">

    <section class="listings-hero">
      <div>
        <p class="eyebrow">SELLER LISTINGS</p>
        <h1>Manage listings</h1>
        <p>View your live products/services, check removed listings, and manage what you publish.</p>
      </div>

      <a href="add-listing.php" class="btn btn-primary">
        <i class="fa-solid fa-plus"></i>
        Add listing
      </a>
    </section>

    <?php if ($success === "created"): ?>
      <div class="auth-success">Listing created successfully and is now live on the marketplace.</div>
    <?php elseif ($success === "deleted"): ?>
      <div class="auth-alert">Listing deleted successfully.</div>
    <?php endif; ?>

    <section class="listing-stats">
      <div class="listing-stat-card">
        <i class="fa-solid fa-circle-check"></i>
        <span>Live Listings</span>
        <strong><?= (int)$liveCount ?></strong>
      </div>

      <div class="listing-stat-card">
        <i class="fa-solid fa-circle-xmark"></i>
        <span>Removed</span>
        <strong><?= (int)$removedCount ?></strong>
      </div>

      <div class="listing-stat-card">
        <i class="fa-solid fa-box"></i>
        <span>Total</span>
        <strong><?= count($listings) ?></strong>
      </div>
    </section>

    <?php if (count($listings) === 0): ?>

      <section class="empty-listings">
        <i class="fa-solid fa-box-open"></i>
        <h2>No listings yet</h2>
        <p>Create your first product or service listing to start selling on Kasi Connect.</p>

        <a href="add-listing.php" class="btn btn-primary">
          <i class="fa-solid fa-plus"></i>
          Add first listing
        </a>
      </section>

    <?php else: ?>

      <section class="seller-listing-grid">

        <?php foreach ($listings as $listing): ?>
          <?php
            $imageClass = $listing["image_class"] ?: "peach";
            $currentStatusClass = statusClass($listing["status"]);
            $currentStatusLabel = statusLabel($listing["status"]);
          ?>

          <article class="seller-listing-card">

            <?php if (!empty($listing["image_path"])): ?>
              <div class="seller-listing-img real-img">
                <img src="../<?= htmlspecialchars($listing["image_path"]) ?>" alt="<?= htmlspecialchars($listing["title"]) ?>">
              </div>
            <?php else: ?>
              <div class="seller-listing-img <?= htmlspecialchars($imageClass) ?>"></div>
            <?php endif; ?>

            <div class="seller-listing-content">
              <span class="listing-status <?= htmlspecialchars($currentStatusClass) ?>">
                <?= htmlspecialchars($currentStatusLabel) ?>
              </span>

              <h3><?= htmlspecialchars($listing["title"]) ?></h3>

              <p><?= htmlspecialchars($listing["description"]) ?></p>

              <div class="listing-meta">
                <span><i class="fa-solid fa-store"></i> <?= htmlspecialchars($listing["category"]) ?></span>
                <span><i class="fa-solid fa-location-dot"></i> <?= htmlspecialchars($listing["location_area"]) ?></span>
                <span><i class="fa-solid fa-tag"></i> <?= htmlspecialchars($listing["price_label"]) ?></span>
              </div>

              <div class="listing-actions">

                <?php if ($listing["status"] === "active"): ?>
                  <a href="product-details.php?id=<?= (int)$listing["id"] ?>" class="btn btn-light">
                    <i class="fa-solid fa-eye"></i>
                    View
                  </a>
                <?php endif; ?>

                <form action="../actions/seller/delete-listing.php" method="POST">
                  <input type="hidden" name="product_id" value="<?= (int)$listing["id"] ?>">

                  <button class="btn btn-light delete-btn" type="submit">
                    <i class="fa-solid fa-trash"></i>
                    Delete
                  </button>
                </form>

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