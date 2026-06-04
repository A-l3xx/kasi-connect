<?php
require_once "../includes/auth.php";
requireLogin();
require_once "../includes/db.php";

$fullName = $_SESSION["full_name"] ?? "User";
$initial = strtoupper(substr($fullName, 0, 1));
$userId = (int)$_SESSION["user_id"];
$role = $_SESSION["role"] ?? "customer";

$search = trim($_GET["q"] ?? "");
$sort = $_GET["sort"] ?? "popular";

$sql = "
  SELECT 
    products.*,
    seller_profiles.business_name,
    users.full_name AS seller_full_name
  FROM products
  JOIN users ON products.seller_id = users.id
  LEFT JOIN seller_profiles ON seller_profiles.user_id = users.id
  WHERE products.status = 'active'
";

$params = [];

if ($search !== "") {
  $sql .= "
    AND (
      products.title LIKE ?
      OR products.description LIKE ?
      OR products.category LIKE ?
      OR products.location_area LIKE ?
      OR seller_profiles.business_name LIKE ?
      OR users.full_name LIKE ?
    )
  ";

  $like = "%" . $search . "%";
  $params = [$like, $like, $like, $like, $like, $like];
}

if ($sort === "low") {
  $sql .= " ORDER BY products.price ASC";
} elseif ($sort === "high") {
  $sql .= " ORDER BY products.price DESC";
} elseif ($sort === "newest") {
  $sql .= " ORDER BY products.created_at DESC";
} else {
  $sql .= " ORDER BY products.id DESC";
}

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$products = $stmt->fetchAll(PDO::FETCH_ASSOC);

$wishlistStmt = $pdo->prepare("
  SELECT product_id
  FROM wishlist
  WHERE user_id = ?
");

$wishlistStmt->execute([$userId]);
$wishlistIds = $wishlistStmt->fetchAll(PDO::FETCH_COLUMN);

function iconForCategory($category) {
  $category = strtolower($category);

  if ($category === "food") return "fa-burger";
  if ($category === "fashion") return "fa-shirt";
  if ($category === "repairs") return "fa-screwdriver-wrench";
  if ($category === "beauty") return "fa-scissors";
  if ($category === "groceries") return "fa-basket-shopping";
  if ($category === "transport") return "fa-car-side";
  if ($category === "services") return "fa-briefcase";

  return "fa-store";
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width,initial-scale=1.0">

  <title>Kasi Connect | Marketplace</title>

  <link rel="stylesheet" href="../assets/css/style.css?v=1010">
  <link rel="stylesheet" href="../assets/css/marketplace.css?v=1010">
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
          <span class="profile-avatar-sm"><?= htmlspecialchars($initial) ?></span>
          <span class="profile-name"><?= htmlspecialchars($fullName) ?></span>
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

<main class="marketplace-page">
  <div class="marketplace-layout">

    <aside class="filters-panel">
      <h3>
        <i class="fa-solid fa-sliders"></i>
        Filters
      </h3>

      <form action="marketplace.php" method="GET">

        <div class="filter-block">
          <h4>Category</h4>

          <label>
            <input type="radio" name="q" value="" <?= $search === "" ? "checked" : "" ?>>
            All
          </label>

          <label>
            <input type="radio" name="q" value="Food" <?= $search === "Food" ? "checked" : "" ?>>
            Food
          </label>

          <label>
            <input type="radio" name="q" value="Fashion" <?= $search === "Fashion" ? "checked" : "" ?>>
            Fashion
          </label>

          <label>
            <input type="radio" name="q" value="Repairs" <?= $search === "Repairs" ? "checked" : "" ?>>
            Repairs
          </label>

          <label>
            <input type="radio" name="q" value="Beauty" <?= $search === "Beauty" ? "checked" : "" ?>>
            Beauty
          </label>

          <label>
            <input type="radio" name="q" value="Services" <?= $search === "Services" ? "checked" : "" ?>>
            Services
          </label>
        </div>

        <div class="filter-block">
          <h4>Sort</h4>

          <select class="form-control" name="sort">
            <option value="popular" <?= $sort === "popular" ? "selected" : "" ?>>Popular</option>
            <option value="newest" <?= $sort === "newest" ? "selected" : "" ?>>Newest</option>
            <option value="low" <?= $sort === "low" ? "selected" : "" ?>>Price: Low to High</option>
            <option value="high" <?= $sort === "high" ? "selected" : "" ?>>Price: High to Low</option>
          </select>
        </div>

        <button class="btn btn-primary full-btn" type="submit">
          <i class="fa-solid fa-filter"></i>
          Apply Filters
        </button>

      </form>
    </aside>

    <section class="market-results">

      <div class="results-head">
        <div>
          <p class="eyebrow">SHOP LOCAL</p>

          <h1>Browse products & services</h1>

          <p class="results-subtitle">
            Support township businesses and discover local services near you.
          </p>
        </div>
      </div>

      <form class="market-search" action="marketplace.php" method="GET">
        <input
          type="text"
          name="q"
          value="<?= htmlspecialchars($search) ?>"
          placeholder="Search products, services or sellers..."
        >

        <input type="hidden" name="sort" value="<?= htmlspecialchars($sort) ?>">

        <button class="btn btn-primary" type="submit">
          <i class="fa-solid fa-magnifying-glass"></i>
          Search
        </button>
      </form>

      <?php if (count($products) === 0): ?>

        <div class="empty-market">
          <i class="fa-solid fa-store"></i>

          <h2>No products found</h2>

          <p>Try searching another category, seller or product.</p>

          <a href="marketplace.php" class="btn btn-primary">
            Reset Marketplace
          </a>
        </div>

      <?php else: ?>

        <div class="browse-grid">

          <?php foreach ($products as $product): ?>

            <?php
              $sellerName = $product["business_name"] ?: $product["seller_full_name"];
              $categoryIcon = iconForCategory($product["category"]);
              $imageClass = $product["image_class"] ?: "peach";
              $isWishlisted = in_array($product["id"], $wishlistIds);
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

                  <h3><?= htmlspecialchars($product["title"]) ?></h3>

                  <p class="seller">
                    <?= htmlspecialchars($sellerName) ?>
                    •
                    <?= htmlspecialchars($product["location_area"]) ?>
                  </p>

                  <p class="description">
                    <?= htmlspecialchars($product["description"]) ?>
                  </p>

                  <strong><?= htmlspecialchars($product["price_label"]) ?></strong>
                </div>
              </a>

              <div class="card-actions">

                <?php if ($isWishlisted): ?>

                  <form action="../actions/wishlist/remove.php" method="POST">
                    <input type="hidden" name="product_id" value="<?= (int)$product["id"] ?>">

                    <button class="wishlist-btn active" type="submit">
                      <i class="fa-solid fa-heart"></i>
                      Wishlisted
                    </button>
                  </form>

                <?php else: ?>

                  <form action="../actions/wishlist/add.php" method="POST">
                    <input type="hidden" name="product_id" value="<?= (int)$product["id"] ?>">

                    <button class="wishlist-btn" type="submit">
                      <i class="fa-regular fa-heart"></i>
                      Wishlist
                    </button>
                  </form>

                <?php endif; ?>

                <form action="../actions/cart/add.php" method="POST">
                  <input type="hidden" name="product_id" value="<?= (int)$product["id"] ?>">

                  <button class="cart-btn" type="submit">
                    <i class="fa-solid fa-cart-shopping"></i>
                    Add to cart
                  </button>
                </form>

              </div>

            </article>

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