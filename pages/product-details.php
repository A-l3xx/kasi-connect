<?php
require_once "../includes/auth.php";
requireLogin();
require_once "../includes/db.php";

$fullName = $_SESSION["full_name"] ?? "User";
$initial = strtoupper(substr($fullName, 0, 1));
$userId = (int)$_SESSION["user_id"];
$role = $_SESSION["role"] ?? "customer";

$productId = (int)($_GET["id"] ?? 0);

if ($productId <= 0) {
  header("Location: marketplace.php");
  exit;
}

$stmt = $pdo->prepare("
  SELECT 
    products.*,
    seller_profiles.business_name,
    users.full_name AS seller_full_name
  FROM products
  JOIN users ON products.seller_id = users.id
  LEFT JOIN seller_profiles ON seller_profiles.user_id = users.id
  WHERE products.id = ?
  AND products.status = 'active'
  LIMIT 1
");

$stmt->execute([$productId]);
$product = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$product) {
  header("Location: marketplace.php");
  exit;
}

$wishlistStmt = $pdo->prepare("
  SELECT COUNT(*)
  FROM wishlist
  WHERE user_id = ?
  AND product_id = ?
");

$wishlistStmt->execute([$userId, $productId]);
$isWishlisted = (int)$wishlistStmt->fetchColumn() > 0;

$sellerName = $product["business_name"] ?: $product["seller_full_name"];
$imageClass = $product["image_class"] ?: "peach";

$relatedStmt = $pdo->prepare("
  SELECT *
  FROM products
  WHERE category = ?
  AND id != ?
  AND status = 'active'
  ORDER BY created_at DESC
  LIMIT 3
");

$relatedStmt->execute([$product["category"], $productId]);
$relatedProducts = $relatedStmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">

  <meta
    name="viewport"
    content="width=device-width,initial-scale=1.0"
  >

  <title>Kasi Connect | <?= htmlspecialchars($product["title"]) ?></title>

  <link
    rel="stylesheet"
    href="../assets/css/style.css?v=1600"
  >

  <link
    rel="stylesheet"
    href="../assets/css/product-details.css?v=1600"
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
      <span>Kasi-Connect</span>
    </div>

    <nav class="main-nav logged-nav">

      <?php if ($role === "seller"): ?>

        <a href="seller-dashboard.php">
          <i class="fa-solid fa-table-columns"></i>
          Seller Dashboard
        </a>

        <a href="add-listing.php">
          <i class="fa-solid fa-plus"></i>
          Add Listing
        </a>

        <a href="manage-listings.php">
          <i class="fa-solid fa-list-check"></i>
          Listings
        </a>

      <?php else: ?>

        <a href="home.php">
          <i class="fa-solid fa-house"></i>
          Home
        </a>

        <a href="dashboard.php">
          <i class="fa-solid fa-table-columns"></i>
          Dashboard
        </a>

        <a href="marketplace.php">
          <i class="fa-solid fa-store"></i>
          Marketplace
        </a>

      <?php endif; ?>

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

          <?php if ($role === "seller"): ?>

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
              <i class="fa-solid fa-store"></i>
              Marketplace
            </a>

          <?php else: ?>

            <a href="marketplace.php">
              <i class="fa-solid fa-store"></i>
              Marketplace
            </a>

            <a href="orders.php">
              <i class="fa-solid fa-box"></i>
              Orders
            </a>

            <a href="wishlist.php">
              <i class="fa-solid fa-heart"></i>
              Wishlist
            </a>

            <a href="cart.php">
              <i class="fa-solid fa-cart-shopping"></i>
              Cart
            </a>

            <a href="profile.php">
              <i class="fa-solid fa-user"></i>
              Profile
            </a>

          <?php endif; ?>

          <a href="../actions/auth/logout.php" class="logout-link">
            <i class="fa-solid fa-right-from-bracket"></i>
            Logout
          </a>

        </div>
      </div>

    </nav>
  </div>
</header>

<main class="product-page">
  <div class="product-container">

    <a href="marketplace.php" class="back-link">
      <i class="fa-solid fa-arrow-left"></i>
      Back to marketplace
    </a>

    <section class="product-layout">

      <div class="product-gallery">

        <?php if (!empty($product["image_path"])): ?>

          <div class="product-main-img real-img">
            <img
              src="../<?= htmlspecialchars($product["image_path"]) ?>"
              alt="<?= htmlspecialchars($product["title"]) ?>"
            >
          </div>

        <?php else: ?>

          <div class="product-main-img <?= htmlspecialchars($imageClass) ?>">
            <span>
              <?= htmlspecialchars($product["category"]) ?>
            </span>
          </div>

        <?php endif; ?>

      </div>

      <aside class="product-info-panel">

        <div class="product-tags">

          <span class="tag verified">
            <i class="fa-solid fa-circle-check"></i>
            Verified seller
          </span>

          <span class="tag">
            <i class="fa-solid fa-store"></i>
            <?= htmlspecialchars($product["category"]) ?>
          </span>

          <span class="tag stock">
            <i class="fa-solid fa-box"></i>
            Available
          </span>

        </div>

        <h1>
          <?= htmlspecialchars($product["title"]) ?>
        </h1>

        <div class="rating-row">

          <span>
            <i class="fa-solid fa-star"></i>
            4.8
          </span>

          <span>
            Local seller
          </span>

          <span>
            <?= htmlspecialchars($product["location_area"]) ?>
          </span>

        </div>

        <div class="price-row">
          <strong>
            <?= htmlspecialchars($product["price_label"]) ?>
          </strong>
        </div>

        <p class="product-short-desc">
          <?= htmlspecialchars($product["description"]) ?>
        </p>

        <div class="seller-card">

          <div class="seller-avatar">
            <?= htmlspecialchars(strtoupper(substr($sellerName, 0, 1))) ?>
          </div>

          <div>
            <h3>
              <?= htmlspecialchars($sellerName) ?>
            </h3>

            <p>
              <i class="fa-solid fa-location-dot"></i>
              <?= htmlspecialchars($product["location_area"]) ?>
            </p>

            <small>
              <i class="fa-solid fa-shield-heart"></i>
              Kasi verified seller
            </small>
          </div>

        </div>

        <div class="product-actions">

          <?php if ($role !== "seller"): ?>

            <form action="../actions/cart/add.php" method="POST">

              <input
                type="hidden"
                name="product_id"
                value="<?= (int)$product["id"] ?>"
              >

              <button class="btn btn-primary" type="submit">
                <i class="fa-solid fa-cart-shopping"></i>
                Add to cart
              </button>

            </form>

            <?php if ($isWishlisted): ?>

              <form action="../actions/wishlist/remove.php" method="POST">

                <input
                  type="hidden"
                  name="product_id"
                  value="<?= (int)$product["id"] ?>"
                >

                <button class="btn btn-light" type="submit">
                  <i class="fa-solid fa-heart"></i>
                  Wishlisted
                </button>

              </form>

            <?php else: ?>

              <form action="../actions/wishlist/add.php" method="POST">

                <input
                  type="hidden"
                  name="product_id"
                  value="<?= (int)$product["id"] ?>"
                >

                <button class="btn btn-light" type="submit">
                  <i class="fa-regular fa-heart"></i>
                  Add to wishlist
                </button>

              </form>

            <?php endif; ?>

          <?php else: ?>

            <a href="manage-listings.php" class="btn btn-primary">
              <i class="fa-solid fa-list-check"></i>
              Manage Listings
            </a>

          <?php endif; ?>

        </div>

        <div class="trust-row">

          <div>
            <i class="fa-solid fa-shield-halved"></i>
            Kasi Protect
          </div>

          <div>
            <i class="fa-solid fa-truck-fast"></i>
            Pickup / Delivery
          </div>

          <div>
            <i class="fa-solid fa-phone"></i>
            Contact after order
          </div>

        </div>

      </aside>

    </section>

    <section class="details-grid">

      <article class="details-card">

        <h2>Description</h2>

        <p>
          <?= nl2br(htmlspecialchars($product["description"])) ?>
        </p>

        <div class="spec-grid">

          <div>
            <span>Category</span>
            <strong>
              <?= htmlspecialchars($product["category"]) ?>
            </strong>
          </div>

          <div>
            <span>Seller</span>
            <strong>
              <?= htmlspecialchars($sellerName) ?>
            </strong>
          </div>

          <div>
            <span>Location</span>
            <strong>
              <?= htmlspecialchars($product["location_area"]) ?>
            </strong>
          </div>

          <div>
            <span>Status</span>
            <strong>Available</strong>
          </div>

        </div>

      </article>

      <article class="details-card delivery-card">

        <h2>Pickup & delivery</h2>

        <div class="delivery-option">

          <i class="fa-solid fa-bag-shopping"></i>

          <div>
            <strong>Pickup</strong>
            <p>
              Collect directly from the seller in <?= htmlspecialchars($product["location_area"]) ?>.
            </p>
          </div>

        </div>

        <div class="delivery-option">

          <i class="fa-solid fa-truck-fast"></i>

          <div>
            <strong>Local delivery</strong>
            <p>
              Delivery can be arranged depending on the seller’s fulfilment option.
            </p>
          </div>

        </div>

      </article>

    </section>

    <?php if (count($relatedProducts) > 0): ?>

      <section class="related-section">

        <div class="section-head">
          <div>
            <p class="eyebrow">RELATED</p>
            <h2>Similar listings</h2>
          </div>
        </div>

        <div class="similar-listings">

          <?php foreach ($relatedProducts as $related): ?>

            <a
              href="product-details.php?id=<?= (int)$related["id"] ?>"
              class="similar-card"
            >

              <div class="similar-card-body">

                <h3 class="similar-card-title">
                  <?= htmlspecialchars($related["title"]) ?>
                </h3>

                <p>
                  <?= htmlspecialchars($related["location_area"]) ?>
                </p>

                <div class="similar-card-price">
                  <?= htmlspecialchars($related["price_label"]) ?>
                </div>

              </div>

            </a>

          <?php endforeach; ?>

        </div>

      </section>

    <?php endif; ?>

  </div>
</main>

<script src="../js/app.js"></script>


</body>
</html>