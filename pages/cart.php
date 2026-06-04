<?php
require_once "../includes/auth.php";
requireLogin();

if (($_SESSION["role"] ?? "") === "admin") {
  header("Location: admin-dashboard.php");
  exit;
}

require_once "../includes/db.php";

$fullName = $_SESSION["full_name"] ?? "User";
$initial = strtoupper(substr($fullName, 0, 1));
$userId = (int)$_SESSION["user_id"];
$role = $_SESSION["role"] ?? "customer";

$stmt = $pdo->prepare("
  SELECT
    cart_items.quantity,
    products.*,
    seller_profiles.business_name,
    users.full_name AS seller_full_name
  FROM cart_items
  JOIN products ON cart_items.product_id = products.id
  JOIN users ON products.seller_id = users.id
  LEFT JOIN seller_profiles ON seller_profiles.user_id = users.id
  WHERE cart_items.user_id = ?
  AND products.status = 'active'
  ORDER BY cart_items.updated_at DESC
");

$stmt->execute([$userId]);
$cartItems = $stmt->fetchAll(PDO::FETCH_ASSOC);

$subtotal = 0;
$totalItems = 0;

foreach ($cartItems as $item) {
  $itemPrice = isset($item["price"]) ? (float)$item["price"] : 0;
  $itemQty = isset($item["quantity"]) ? (int)$item["quantity"] : 1;

  $subtotal += $itemPrice * $itemQty;
  $totalItems += $itemQty;
}

$deliveryFee = $totalItems > 0 ? 40 : 0;
$serviceFee = $totalItems > 0 ? 10 : 0;
$total = $subtotal + $deliveryFee + $serviceFee;

function iconForCategory($category) {
  $category = strtolower($category);

  if ($category === "food") return "fa-burger";
  if ($category === "fashion") return "fa-shirt";
  if ($category === "repairs") return "fa-screwdriver-wrench";
  if ($category === "beauty") return "fa-scissors";
  if ($category === "groceries") return "fa-basket-shopping";
  if ($category === "transport") return "fa-car-side";

  return "fa-store";
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width,initial-scale=1.0">
  <title>Kasi Connect | Cart</title>

  <link rel="stylesheet" href="../assets/css/style.css?v=750">
  <link rel="stylesheet" href="../assets/css/cart.css?v=10">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
</head>
<body>

<header class="site-header">
  <div class="container nav-row">

    <div class="logo">
      <div class="logo-mark">K</div>
      <span><?= $role === "seller" ? "Kasi-Connect Seller" : "Kasi-Connect" ?></span>
    </div>

    <nav class="main-nav logged-nav">

      <?php if ($role === "seller"): ?>

        <a href="seller-dashboard.php"><i class="fa-solid fa-table-columns"></i> Seller Dashboard</a>
        <a href="add-listing.php"><i class="fa-solid fa-plus"></i> Add Listing</a>
        <a href="manage-listings.php"><i class="fa-solid fa-list-check"></i> Listings</a>

      <?php else: ?>

        <a href="home.php"><i class="fa-solid fa-house"></i> Home</a>
        <a href="dashboard.php"><i class="fa-solid fa-table-columns"></i> Dashboard</a>
        <a href="marketplace.php"><i class="fa-solid fa-store"></i> Marketplace</a>

      <?php endif; ?>

      <div class="profile-menu">
        <button class="profile-toggle" type="button">
          <span class="profile-avatar-sm"><?= htmlspecialchars($initial) ?></span>
          <span class="profile-name"><?= htmlspecialchars($fullName) ?></span>
          <i class="fa-solid fa-chevron-down"></i>
        </button>

        <div class="profile-dropdown">

          <?php if ($role === "seller"): ?>

            <a href="seller-profile.php"><i class="fa-solid fa-store"></i> Seller Profile</a>
            <a href="seller-orders.php"><i class="fa-solid fa-box"></i> Orders Received</a>
            <a href="marketplace.php"><i class="fa-solid fa-store"></i> Marketplace</a>
            <a href="orders.php"><i class="fa-solid fa-bag-shopping"></i> My Purchases</a>
            <a href="wishlist.php"><i class="fa-solid fa-heart"></i> Wishlist</a>
            <a href="cart.php"><i class="fa-solid fa-cart-shopping"></i> Cart</a>

          <?php else: ?>

            <a href="marketplace.php"><i class="fa-solid fa-store"></i> Marketplace</a>
            <a href="orders.php"><i class="fa-solid fa-box"></i> Orders</a>
            <a href="wishlist.php"><i class="fa-solid fa-heart"></i> Wishlist</a>
            <a href="cart.php"><i class="fa-solid fa-cart-shopping"></i> Cart</a>
            <a href="profile.php"><i class="fa-solid fa-user"></i> Profile</a>

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

<main class="cart-page">
  <div class="cart-container">

    <section class="cart-hero">
      <div>
        <p class="eyebrow">SHOPPING CART</p>
        <h1>Your cart</h1>
        <p>Review your selected products and continue to secure checkout.</p>
      </div>

      <a href="marketplace.php" class="btn btn-primary">
        <i class="fa-solid fa-store"></i>
        Continue shopping
      </a>
    </section>

    <?php if (count($cartItems) === 0): ?>

      <section class="empty-cart" style="display:block;">
        <i class="fa-solid fa-cart-shopping"></i>
        <h2>Your cart is empty</h2>
        <p>Browse the marketplace and add products or services to your cart.</p>

        <a href="marketplace.php" class="btn btn-primary">
          Browse Marketplace
        </a>
      </section>

    <?php else: ?>

      <section class="cart-layout">

        <div class="cart-items">

          <?php foreach ($cartItems as $item): ?>

            <?php
              $sellerName = $item["business_name"] ?: $item["seller_full_name"];
              $categoryIcon = iconForCategory($item["category"]);
              $imageClass = $item["image_class"] ?: "peach";

              $itemPrice = isset($item["price"]) ? (float)$item["price"] : 0;
              $itemQty = isset($item["quantity"]) ? (int)$item["quantity"] : 1;
              $itemTotal = $itemPrice * $itemQty;
            ?>

            <article class="cart-item">

              <?php if (!empty($item["image_path"])): ?>
                <div class="cart-img real-img">
                  <img src="../<?= htmlspecialchars($item["image_path"]) ?>" alt="<?= htmlspecialchars($item["title"]) ?>">
                </div>
              <?php else: ?>
                <div class="cart-img <?= htmlspecialchars($imageClass) ?>"></div>
              <?php endif; ?>

              <div class="cart-info">
                <span class="cart-badge">
                  <i class="fa-solid <?= htmlspecialchars($categoryIcon) ?>"></i>
                  <?= htmlspecialchars($item["category"]) ?>
                </span>

                <h3><?= htmlspecialchars($item["title"]) ?></h3>

                <p><?= htmlspecialchars($sellerName) ?> • <?= htmlspecialchars($item["location_area"]) ?></p>

                <div class="cart-meta">
                  <span><i class="fa-solid fa-circle-check"></i> Available</span>
                  <span><i class="fa-solid fa-truck-fast"></i> Pickup / delivery</span>
                </div>

                <div class="qty-row">
                  <form action="../actions/cart/update.php" method="POST">
                    <input type="hidden" name="product_id" value="<?= (int)$item["id"] ?>">
                    <input type="hidden" name="action" value="decrease">
                    <button class="qty-minus" type="submit">−</button>
                  </form>

                  <span class="qty-number"><?= (int)$itemQty ?></span>

                  <form action="../actions/cart/update.php" method="POST">
                    <input type="hidden" name="product_id" value="<?= (int)$item["id"] ?>">
                    <input type="hidden" name="action" value="increase">
                    <button class="qty-plus" type="submit">+</button>
                  </form>
                </div>
              </div>

              <div class="cart-price">
                <strong class="item-total">R<?= number_format((float)$itemTotal, 2) ?></strong>

                <form action="../actions/wishlist/add.php" method="POST">
                  <input type="hidden" name="product_id" value="<?= (int)$item["id"] ?>">

                  <button class="wishlist-btn" type="submit">
                    <i class="fa-regular fa-heart"></i>
                    Move to wishlist
                  </button>
                </form>

                <form action="../actions/cart/remove.php" method="POST">
                  <input type="hidden" name="product_id" value="<?= (int)$item["id"] ?>">

                  <button class="remove-btn" type="submit">
                    <i class="fa-regular fa-trash-can"></i>
                    Remove
                  </button>
                </form>
              </div>

            </article>

          <?php endforeach; ?>

        </div>

        <aside class="cart-summary">
          <h2>Order summary</h2>

          <div class="summary-list">
            <div class="summary-row">
              <span>Items</span>
              <strong><?= (int)$totalItems ?></strong>
            </div>

            <div class="summary-row">
              <span>Subtotal</span>
              <strong>R<?= number_format((float)$subtotal, 2) ?></strong>
            </div>

            <div class="summary-row">
              <span>Estimated delivery</span>
              <strong>R<?= number_format((float)$deliveryFee, 2) ?></strong>
            </div>

            <div class="summary-row">
              <span>Service fee</span>
              <strong>R<?= number_format((float)$serviceFee, 2) ?></strong>
            </div>
          </div>

          <div class="summary-total">
            <span>Total</span>
            <strong>R<?= number_format((float)$total, 2) ?></strong>
          </div>

          <a href="checkout.php" class="btn btn-primary full-btn">
            <i class="fa-solid fa-lock"></i>
            Secure checkout
          </a>
        </aside>

      </section>

    <?php endif; ?>

  </div>
</main>

<script src="../js/app.js"></script>
<script src="../js/logout.js"></script>

</body>
</html>