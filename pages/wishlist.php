<?php
require_once "../includes/auth.php";
requireLogin();
require_once "../includes/db.php";

$fullName = $_SESSION["full_name"] ?? "User";
$initial = strtoupper(substr($fullName, 0, 1));
$userId = $_SESSION["user_id"];

$search = trim($_GET["q"] ?? "");
$sort = $_GET["sort"] ?? "newest";

$sql = "
  SELECT 
    wishlist.created_at AS saved_at,
    products.*,
    seller_profiles.business_name,
    users.full_name AS seller_full_name
  FROM wishlist
  JOIN products ON wishlist.product_id = products.id
  JOIN users ON products.seller_id = users.id
  LEFT JOIN seller_profiles ON seller_profiles.user_id = users.id
  WHERE wishlist.user_id = ?
  AND products.status = 'active'
";

$params = [$userId];

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

  $params[] = $like;
  $params[] = $like;
  $params[] = $like;
  $params[] = $like;
  $params[] = $like;
  $params[] = $like;
}

if ($sort === "low") {
  $sql .= " ORDER BY products.price ASC";
} elseif ($sort === "high") {
  $sql .= " ORDER BY products.price DESC";
} else {
  $sql .= " ORDER BY wishlist.created_at DESC";
}

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$wishlistItems = $stmt->fetchAll(PDO::FETCH_ASSOC);

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
  <title>Kasi Connect | Wishlist</title>

  <link rel="stylesheet" href="../assets/css/style.css?v=460">
  <link rel="stylesheet" href="../assets/css/wishlist.css?v=7">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
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

          <a href="login.php" class="logout-link logout-btn">
            <i class="fa-solid fa-right-from-bracket"></i>
            Logout
          </a>

        </div>

      </div>

    </nav>

  </div>
</header>

<main class="wishlist-page">
  <div class="wishlist-container">

    <section class="wishlist-hero">
      <div>
        <p class="eyebrow">SAVED PRODUCTS</p>

        <h1>Your wishlist</h1>

        <p>
          Save products and services you like, compare them later,
          and move them to your cart when you are ready.
        </p>
      </div>

      <a href="marketplace.php" class="btn btn-primary">
        <i class="fa-solid fa-store"></i>
        Browse Marketplace
      </a>
    </section>

    <section class="wishlist-toolbar">

      <form class="wishlist-search" action="wishlist.php" method="GET">

        <i class="fa-solid fa-magnifying-glass"></i>

        <input
          type="text"
          name="q"
          value="<?= htmlspecialchars($search) ?>"
          placeholder="Search your wishlist..."
        >

        <input
          type="hidden"
          name="sort"
          value="<?= htmlspecialchars($sort) ?>"
        >

      </form>

      <form action="wishlist.php" method="GET">

        <input
          type="hidden"
          name="q"
          value="<?= htmlspecialchars($search) ?>"
        >

        <select name="sort" onchange="this.form.submit()">

          <option value="newest" <?= $sort === "newest" ? "selected" : "" ?>>
            Newest saved
          </option>

          <option value="low" <?= $sort === "low" ? "selected" : "" ?>>
            Lowest price
          </option>

          <option value="high" <?= $sort === "high" ? "selected" : "" ?>>
            Highest price
          </option>

        </select>

      </form>

    </section>

    <?php if (count($wishlistItems) === 0): ?>

      <section class="empty-wishlist" id="emptyWishlist" style="display:block;">

        <div class="empty-icon">
          <i class="fa-regular fa-heart"></i>
        </div>

        <h2>Your wishlist is empty</h2>

        <p>
          Browse the marketplace and save products or services you like.
        </p>

        <a href="marketplace.php" class="btn btn-primary">
          <i class="fa-solid fa-store"></i>
          Browse Marketplace
        </a>

      </section>

    <?php else: ?>

      <section class="wishlist-grid" id="wishlistGrid">

        <?php foreach ($wishlistItems as $item): ?>

          <?php
            $sellerName =
              $item["business_name"] ?: $item["seller_full_name"];

            $categoryIcon =
              iconForCategory($item["category"]);

            $imageClass =
              $item["image_class"] ?: "peach";
          ?>

          <article
            class="wishlist-card"
            data-name="<?= htmlspecialchars($item["title"]) ?>"
            data-type="product"
            data-price="<?= htmlspecialchars($item["price"]) ?>"
          >

            <a href="product-details.php?id=<?= (int)$item["id"] ?>" class="wishlist-link">

              <?php if (!empty($item["image_path"])): ?>

                <div class="wishlist-img real-img">

                  <img
                    src="../<?= htmlspecialchars($item["image_path"]) ?>"
                    alt="<?= htmlspecialchars($item["title"]) ?>"
                  >

                  <span class="wish-badge">
                    <i class="fa-solid <?= htmlspecialchars($categoryIcon) ?>"></i>
                    <?= htmlspecialchars($item["category"]) ?>
                  </span>

                </div>

              <?php else: ?>

                <div class="wishlist-img <?= htmlspecialchars($imageClass) ?>">

                  <span class="wish-badge">
                    <i class="fa-solid <?= htmlspecialchars($categoryIcon) ?>"></i>
                    <?= htmlspecialchars($item["category"]) ?>
                  </span>

                </div>

              <?php endif; ?>

              <div class="wishlist-info">

                <h3>
                  <?= htmlspecialchars($item["title"]) ?>
                </h3>

                <p>
                  <?= htmlspecialchars($sellerName) ?>
                  •
                  <?= htmlspecialchars($item["location_area"]) ?>
                </p>

                <div class="wish-meta">

                  <span>
                    <i class="fa-solid fa-circle-check"></i>
                    Saved
                  </span>

                  <span>
                    <i class="fa-solid fa-location-dot"></i>
                    Local seller
                  </span>

                </div>

                <strong>
                  <?= htmlspecialchars($item["price_label"]) ?>
                </strong>

              </div>

            </a>

            <div class="wishlist-actions">

              <form action="../actions/cart/add.php" method="POST">

                <input
                  type="hidden"
                  name="product_id"
                  value="<?= (int)$item["id"] ?>"
                >

                <button class="btn btn-primary add-cart-btn" type="submit">
                  <i class="fa-solid fa-cart-shopping"></i>
                  Add to cart
                </button>

              </form>

              <form action="../actions/wishlist/remove.php" method="POST">

                <input
                  type="hidden"
                  name="product_id"
                  value="<?= (int)$item["id"] ?>"
                >

                <button class="remove-btn" type="submit">
                  <i class="fa-regular fa-trash-can"></i>
                  Remove
                </button>

              </form>

            </div>

          </article>

        <?php endforeach; ?>

      </section>

    <?php endif; ?>

  </div>
</main>

<script src="../js/app.js"></script>
<script src="../js/logout.js"></script>
<script src="../js/wishlist.js"></script>

</body>
</html>