<?php
require_once "../includes/auth.php";
requireLogin();

if (($_SESSION["role"] ?? "") === "seller") {
  header("Location: seller-dashboard.php");
  exit;
}

if (($_SESSION["role"] ?? "") === "admin") {
  header("Location: admin-dashboard.php");
  exit;
}

require_once "../includes/db.php";

$fullName = $_SESSION["full_name"] ?? "User";
$initial = strtoupper(substr($fullName, 0, 1));

$stmt = $pdo->query("
  SELECT 
    products.*,
    seller_profiles.business_name,
    users.full_name AS seller_full_name
  FROM products
  JOIN users ON products.seller_id = users.id
  LEFT JOIN seller_profiles
    ON seller_profiles.user_id = users.id
  WHERE products.status = 'active'
  ORDER BY products.created_at DESC
  LIMIT 6
");

$products = $stmt->fetchAll(PDO::FETCH_ASSOC);

function iconForCategory($category) {
  $category = strtolower($category);

  if ($category === "food") return "fa-burger";
  if ($category === "fashion") return "fa-shirt";
  if ($category === "repairs") return "fa-screwdriver-wrench";
  if ($category === "beauty") return "fa-scissors";
  if ($category === "services") return "fa-briefcase";
  if ($category === "groceries") return "fa-basket-shopping";

  return "fa-store";
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

  <title>Kasi Connect | Home</title>

  <link
    rel="stylesheet"
    href="../assets/css/style.css?v=1300"
  >

  <link
    rel="stylesheet"
    href="../assets/css/home.css?v=1300"
  >

  <link
    rel="stylesheet"
    href="../assets/css/marketplace.css?v=1300"
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

      <a href="home.php" class="active">
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

          <a href="../actions/auth/logout.php" class="logout-link">
            <i class="fa-solid fa-right-from-bracket"></i>
            Logout
          </a>

        </div>
      </div>

    </nav>

  </div>
</header>

<main class="home-page">

  <!-- HERO FULL BACKGROUND -->

  <section class="home-hero hero-bg">

    <div class="hero-overlay"></div>

     <div class="container hero-content">

      <p class="eyebrow">
        TOWNSHIP MARKETPLACE
      </p>

      <h1>
        Shop local products<br>
        and services near you.
      </h1>

      <p>
        Discover trusted township sellers, local food, fashion,
        repairs, <br> services and everyday essentials in
        one simple marketplace.
      </p>

      <div class="hero-actions">

        <a href="marketplace.php" class="btn btn-primary">
          <i class="fa-solid fa-store"></i>
          Browse Marketplace
        </a>
      </div>

    </div>

  </div>

</section>

  <!-- CATEGORIES -->

  <section class="container home-categories">

    <div class="section-head">

      <div>

        <p class="eyebrow">
          <i class="fa-solid fa-location-dot"></i>
          EXPLORE
        </p>

        <h2>
          Popular categories
        </h2>

        <p class="section-subtitle">
          Find sellers around your area and buy from businesses in your community.
        </p>

      </div>

      <a href="marketplace.php" class="section-link">
        View all
        <i class="fa-solid fa-arrow-right"></i>
      </a>

    </div>

    <div class="category-grid">

      <a href="marketplace.php?q=Food" class="category-card">

        <div class="category-icon">
          <i class="fa-solid fa-burger"></i>
        </div>

        <strong>Food</strong>

        <span>
          Kota, meals, snacks
        </span>

        <div class="category-image">
          <img
            src="../images/category-food.png"
            alt="Food"
          >
        </div>

      </a>

      <a href="marketplace.php?q=Fashion" class="category-card">

        <div class="category-icon">
          <i class="fa-solid fa-shirt"></i>
        </div>

        <strong>Fashion</strong>

        <span>
          Clothing and style
        </span>

        <div class="category-image">
          <img
            src="../images/category-fashion.png"
            alt="Fashion"
          >
        </div>

      </a>

      <a href="marketplace.php?q=Repairs" class="category-card">

        <div class="category-icon">
          <i class="fa-solid fa-screwdriver-wrench"></i>
        </div>

        <strong>Repairs</strong>

        <span>
          Phones and services
        </span>

        <div class="category-image">
          <img
            src="../images/category-repairs.png"
            alt="Repairs"
          >
        </div>

      </a>

      <a href="marketplace.php?q=Beauty" class="category-card">

        <div class="category-icon">
          <i class="fa-solid fa-scissors"></i>
        </div>

        <strong>Beauty</strong>

        <span>
          Hair and grooming
        </span>

        <div class="category-image">
          <img
            src="../images/category-beauty.png"
            alt="Beauty"
          >
        </div>

      </a>

    </div>

  </section>

  <!-- MARKETPLACE PRODUCTS -->

  <section class="container featured-section">

    <div class="section-head">

      <div>

        <p class="eyebrow">
          <i class="fa-solid fa-seedling"></i>
          LATEST LISTINGS
        </p>

        <h2>
          Fresh from the marketplace
        </h2>

      </div>

      <a href="marketplace.php" class="section-link">
        Shop more
        <i class="fa-solid fa-arrow-right"></i>
      </a>

    </div>

    <?php if (count($products) === 0): ?>

      <div class="empty-market">

        <i class="fa-solid fa-store"></i>

        <h2>
          No active listings yet
        </h2>

        <p>
          Seller listings will appear here once they are posted.
        </p>

      </div>

    <?php else: ?>

      <div class="browse-grid">

        <?php foreach ($products as $product): ?>

          <?php
            $sellerName =
              $product["business_name"]
              ?: $product["seller_full_name"];

            $categoryIcon =
              iconForCategory($product["category"]);

            $imageClass =
              $product["image_class"]
              ?: "peach";
          ?>

          <article class="shop-card">

            <a href="product-details.php?id=<?= (int)$product["id"] ?>">

              <?php if (!empty($product["image_path"])): ?>

                <div class="product-img real-img">

                  <img
                    src="../<?= htmlspecialchars($product["image_path"]) ?>"
                    alt="<?= htmlspecialchars($product["title"]) ?>"
                  >

                </div>

              <?php else: ?>

                <div class="product-img <?= htmlspecialchars($imageClass) ?>"></div>

              <?php endif; ?>

              <div class="shop-info">

                <span class="tag">
                  <i class="fa-solid <?= htmlspecialchars($categoryIcon) ?>"></i>
                  <?= htmlspecialchars($product["category"]) ?>
                </span>

                <h3>
                  <?= htmlspecialchars($product["title"]) ?>
                </h3>

                <p class="seller">
                  <?= htmlspecialchars($sellerName) ?>
                  •
                  <?= htmlspecialchars($product["location_area"]) ?>
                </p>

                <p class="description">
                  <?= htmlspecialchars($product["description"]) ?>
                </p>

                <strong>
                  <?= htmlspecialchars($product["price_label"]) ?>
                </strong>

              </div>

            </a>

          </article>

        <?php endforeach; ?>

      </div>

    <?php endif; ?>

  </section>

</main>

<script src="../js/app.js"></script>
<script src="../js/logout.js"></script>

</body>
</html>