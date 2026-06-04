<?php
require_once "../includes/auth.php";
requireLogin();
require_once "../includes/db.php";

$fullName = $_SESSION["full_name"] ?? "User";
$email = $_SESSION["email"] ?? "";
$initial = strtoupper(substr($fullName, 0, 1));
$userId = $_SESSION["user_id"];

$error = $_GET["error"] ?? "";

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
  $price = isset($item["price"]) ? (float)$item["price"] : 0;
  $qty = isset($item["quantity"]) ? (int)$item["quantity"] : 1;

  $subtotal += $price * $qty;
  $totalItems += $qty;
}

$deliveryFee = $totalItems > 0 ? 40 : 0;
$serviceFee = $totalItems > 0 ? 10 : 0;
$total = $subtotal + $deliveryFee + $serviceFee;
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">

  <meta
    name="viewport"
    content="width=device-width,initial-scale=1.0"
  >

  <title>Kasi Connect | Checkout</title>

  <link
    rel="stylesheet"
    href="../assets/css/style.css?v=1300"
  >

  <link
    rel="stylesheet"
    href="../assets/css/checkout.css?v=1300"
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

          <a href="../actions/auth/logout.php" class="logout-link logout-btn">
            <i class="fa-solid fa-right-from-bracket"></i>
            Logout
          </a>

        </div>

      </div>

    </nav>

  </div>
</header>

<main class="checkout-page">
  <div class="checkout-container">

    <a href="cart.php" class="back-link">
      <i class="fa-solid fa-arrow-left"></i>
      Back to cart
    </a>

    <section class="checkout-hero">

      <div>
        <p class="eyebrow">
          SECURE CHECKOUT
        </p>

        <h1>
          Complete your order
        </h1>

        <p>
          Confirm your details, choose pickup or delivery, and place your order securely.
        </p>
      </div>

      <div class="checkout-trust">
        <i class="fa-solid fa-lock"></i>
        <span>Protected checkout</span>
      </div>

    </section>

    <?php if ($error === "missing_fields"): ?>

      <div class="auth-alert">
        Please fill in your name, phone number and delivery address.
      </div>

    <?php elseif ($error === "checkout_failed"): ?>

      <div class="auth-alert">
        Checkout failed. Please try again.
      </div>

    <?php endif; ?>

    <?php if (count($cartItems) === 0): ?>

      <section class="empty-cart" style="display:block;">

        <i class="fa-solid fa-cart-shopping"></i>

        <h2>Your cart is empty</h2>

        <p>
          Add products or services to your cart before checkout.
        </p>

        <a href="marketplace.php" class="btn btn-primary">
          Browse Marketplace
        </a>

      </section>

    <?php else: ?>

      <section class="checkout-layout">

        <form
          class="checkout-form"
          action="../actions/checkout/process.php"
          method="POST"
        >

          <section class="checkout-card">

            <div class="card-head">
              <div>
                <p class="eyebrow">STEP 1</p>
                <h2>Fulfilment method</h2>
              </div>
            </div>

            <div class="choice-grid">

              <label class="choice-card active">

                <input
                  type="radio"
                  name="fulfilment_method"
                  value="pickup"
                  checked
                >

                <i class="fa-solid fa-bag-shopping"></i>

                <div>
                  <strong>Pickup</strong>
                  <p>Collect directly from the seller.</p>
                </div>

              </label>

              <label class="choice-card">

                <input
                  type="radio"
                  name="fulfilment_method"
                  value="delivery"
                >

                <i class="fa-solid fa-truck-fast"></i>

                <div>
                  <strong>Local delivery</strong>
                  <p>Get your order delivered by a local partner.</p>
                </div>

              </label>

            </div>

          </section>

          <section class="checkout-card">

            <div class="card-head">
              <div>
                <p class="eyebrow">STEP 2</p>
                <h2>Customer details</h2>
              </div>
            </div>

            <div class="form-grid">

              <div class="form-group">
                <label>Full name</label>

                <input
                  class="form-control"
                  type="text"
                  name="customer_name"
                  value="<?= htmlspecialchars($fullName) ?>"
                  required
                >
              </div>

              <div class="form-group">
                <label>Phone number</label>

                <input
                  class="form-control"
                  type="tel"
                  name="customer_phone"
                  placeholder="+27 82 000 0000"
                  required
                >
              </div>

              <div class="form-group">
                <label>Email address</label>

                <input
                  class="form-control"
                  type="email"
                  value="<?= htmlspecialchars($email) ?>"
                  disabled
                >
              </div>

              <div class="form-group">
                <label>Area</label>

                <input
                  class="form-control"
                  type="text"
                  name="area"
                  placeholder="Soweto / Alexandra / Tembisa"
                >
              </div>

            </div>

            <div class="form-group">
              <label>Delivery address / pickup note</label>

              <textarea
                class="form-control textarea"
                name="delivery_address"
                placeholder="Example: Call when outside, use main gate, or collect at seller stall..."
                required
              ></textarea>
            </div>

          </section>

          <section class="checkout-card">

            <div class="card-head">
              <div>
                <p class="eyebrow">STEP 3</p>
                <h2>Payment method</h2>
              </div>
            </div>

            <label class="payment-card active">

              <input
                type="radio"
                name="payment_method"
                value="card"
                checked
              >

              <div class="payment-icon">
                <i class="fa-solid fa-credit-card"></i>
              </div>

              <div>
                <strong>Card payment with Stripe</strong>
                <p>Stripe will be connected later. For now, this creates a test paid order.</p>
              </div>

              <span class="recommended">
                Recommended
              </span>

            </label>

            <label class="payment-card">

              <input
                type="radio"
                name="payment_method"
                value="cash"
              >

              <div class="payment-icon">
                <i class="fa-solid fa-money-bill-wave"></i>
              </div>

              <div>
                <strong>Cash on pickup / delivery</strong>
                <p>Pay the seller when collecting or receiving the order.</p>
              </div>

            </label>

            <button
              class="btn btn-primary full-btn checkout-submit"
              type="submit"
            >
              <i class="fa-solid fa-lock"></i>
              Place order
            </button>

          </section>

        </form>

        <aside class="checkout-summary">

          <h2>Order summary</h2>

          <div class="summary-products">

            <?php foreach ($cartItems as $item): ?>

              <?php
                $sellerName =
                  $item["business_name"]
                  ?: $item["seller_full_name"];

                $imageClass =
                  $item["image_class"]
                  ?: "peach";

                $qty =
                  isset($item["quantity"])
                  ? (int)$item["quantity"]
                  : 1;

                $price =
                  isset($item["price"])
                  ? (float)$item["price"]
                  : 0;

                $itemTotal = $price * $qty;

                $categoryInitial =
                  !empty($item["category"])
                  ? strtoupper(substr($item["category"], 0, 1))
                  : "K";
              ?>

              <div class="summary-product">

                <div class="summary-img <?= htmlspecialchars($imageClass) ?>">
                    <span>
                      <?= strtoupper(substr($item["category"], 0, 1)) ?>
                    </span>
                </div>

                <div class="summary-product-info">

                  <strong>
                    <?= htmlspecialchars($item["title"]) ?>
                  </strong>

                  <p>
                    Qty <?= $qty ?> • <?= htmlspecialchars($sellerName) ?>
                  </p>

                </div>

                <span class="summary-product-price">
                  R<?= number_format((float)$itemTotal, 2) ?>
                </span>

              </div>

            <?php endforeach; ?>

          </div>

          <div class="summary-list">

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

          <p class="checkout-note">
            After placing the order, your cart will be cleared and the order will appear under Orders.
          </p>

          <div class="safe-box">

            <i class="fa-solid fa-shield-halved"></i>

            <div>
              <strong>Kasi Protect</strong>
              <p>Your order details and payment flow are protected.</p>
            </div>

          </div>

        </aside>

      </section>

    <?php endif; ?>

  </div>
</main>

<script src="../js/app.js"></script>
<script src="../js/logout.js"></script>

</body>
</html>