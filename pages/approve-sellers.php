<?php
require_once "../includes/auth.php";
requireAdmin();
require_once "../includes/db.php";

$fullName = $_SESSION["full_name"] ?? "Admin";
$initial = strtoupper(substr($fullName, 0, 1));

$success = $_GET["success"] ?? "";

$stmt = $pdo->query("
  SELECT 
    users.id,
    users.full_name,
    users.email,
    users.phone,
    users.status,
    users.created_at,
    seller_profiles.business_name,
    seller_profiles.business_category,
    seller_profiles.township_area,
    seller_profiles.fulfilment_option,
    seller_profiles.approval_status
  FROM users
  LEFT JOIN seller_profiles 
    ON seller_profiles.user_id = users.id
  WHERE users.role = 'seller'
  ORDER BY users.created_at DESC
");

$sellers = $stmt->fetchAll(PDO::FETCH_ASSOC);

$pendingCount = 0;
$approvedCount = 0;
$rejectedCount = 0;

foreach ($sellers as $seller) {
  if ($seller["approval_status"] === "approved" || $seller["status"] === "active") {
    $approvedCount++;
  } elseif ($seller["approval_status"] === "rejected" || $seller["status"] === "blocked") {
    $rejectedCount++;
  } else {
    $pendingCount++;
  }
}

function formatFulfilment($value) {
  if ($value === "pickup") return "Pickup only";
  if ($value === "delivery") return "Delivery only";
  if ($value === "pickup_delivery") return "Pickup & delivery";
  return "Not provided";
}

function sellerStatusLabel($seller) {
  if ($seller["approval_status"] === "approved" || $seller["status"] === "active") {
    return "Approved";
  }

  if ($seller["approval_status"] === "rejected" || $seller["status"] === "blocked") {
    return "Rejected";
  }

  return "Pending approval";
}

function sellerStatusClass($seller) {
  if ($seller["approval_status"] === "approved" || $seller["status"] === "active") {
    return "approved";
  }

  if ($seller["approval_status"] === "rejected" || $seller["status"] === "blocked") {
    return "rejected";
  }

  return "pending";
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width,initial-scale=1.0">
  <title>Kasi Connect | Approve Sellers</title>

  <link rel="stylesheet" href="../assets/css/style.css?v=610">
  <link rel="stylesheet" href="../assets/css/approve-sellers.css?v=2">
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
      <a href="admin-dashboard.php">
        <i class="fa-solid fa-table-columns"></i>
        Dashboard
      </a>

      <a href="approve-sellers.php">
        <i class="fa-solid fa-user-check"></i>
        Sellers
      </a>

      <a href="approve-listings.php">
        <i class="fa-solid fa-box-open"></i>
        Listings
      </a>

      <div class="profile-menu">
        <button class="profile-toggle" type="button">
          <span class="profile-avatar-sm"><?= htmlspecialchars($initial) ?></span>
          <span class="profile-name"><?= htmlspecialchars($fullName) ?></span>
          <i class="fa-solid fa-chevron-down"></i>
        </button>

        <div class="profile-dropdown">
          <a href="manage-orders.php">
            <i class="fa-solid fa-box"></i>
            Orders
          </a>

          <a href="manage-users.php">
            <i class="fa-solid fa-users"></i>
            Users
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

<main class="approve-page">
  <div class="approve-container">

    <section class="approve-hero">
      <div>
        <p class="eyebrow">SELLER APPROVALS</p>
        <h1>Approve sellers</h1>
        <p>Review seller applications before giving access to seller tools and marketplace selling features.</p>
      </div>

      <a href="admin-dashboard.php" class="btn btn-light">
        <i class="fa-solid fa-arrow-left"></i>
        Back to dashboard
      </a>
    </section>

    <?php if ($success === "approved"): ?>
      <div class="auth-success">
        Seller approved successfully.
      </div>
    <?php elseif ($success === "rejected"): ?>
      <div class="auth-alert">
        Seller rejected successfully.
      </div>
    <?php endif; ?>

    <section class="approval-stats">
      <div class="approval-stat-card">
        <i class="fa-solid fa-clock"></i>
        <span>Pending</span>
        <strong><?= (int)$pendingCount ?></strong>
      </div>

      <div class="approval-stat-card">
        <i class="fa-solid fa-circle-check"></i>
        <span>Approved</span>
        <strong><?= (int)$approvedCount ?></strong>
      </div>

      <div class="approval-stat-card">
        <i class="fa-solid fa-circle-xmark"></i>
        <span>Rejected</span>
        <strong><?= (int)$rejectedCount ?></strong>
      </div>
    </section>

    <section class="approval-toolbar">
      <div class="approval-search">
        <i class="fa-solid fa-magnifying-glass"></i>
        <input type="text" placeholder="Search seller, business, category or area...">
      </div>

      <select>
        <option>All sellers</option>
        <option>Pending sellers</option>
        <option>Approved sellers</option>
        <option>Rejected sellers</option>
      </select>
    </section>

    <?php if (count($sellers) === 0): ?>

      <section class="empty-admin-state">
        <i class="fa-solid fa-user-check"></i>
        <h3>No seller applications yet</h3>
        <p>Seller applications will appear here after users sign up as sellers.</p>
      </section>

    <?php else: ?>

      <section class="seller-approval-list">

        <?php foreach ($sellers as $seller): ?>
          <?php
            $businessName = $seller["business_name"] ?: "Business not provided";
            $ownerName = $seller["full_name"] ?: "Unknown seller";
            $category = $seller["business_category"] ?: "Not provided";
            $area = $seller["township_area"] ?: "Not provided";
            $phone = $seller["phone"] ?: "Not provided";
            $email = $seller["email"] ?: "Not provided";
            $fulfilment = formatFulfilment($seller["fulfilment_option"] ?? "");
            $statusLabel = sellerStatusLabel($seller);
            $statusClass = sellerStatusClass($seller);
            $avatar = strtoupper(substr($businessName, 0, 1));
          ?>

          <article class="seller-approval-card">
            <div class="seller-main">
              <div class="seller-avatar-lg">
                <?= htmlspecialchars($avatar) ?>
              </div>

              <div>
                <span class="status-pill <?= htmlspecialchars($statusClass) ?>">
                  <i class="fa-solid fa-circle-info"></i>
                  <?= htmlspecialchars($statusLabel) ?>
                </span>

                <h3><?= htmlspecialchars($businessName) ?></h3>

                <p>
                  Owner: <?= htmlspecialchars($ownerName) ?>
                  • <?= htmlspecialchars($category) ?>
                  • <?= htmlspecialchars($area) ?>
                </p>
              </div>
            </div>

            <div class="seller-details">
              <div>
                <span>Phone</span>
                <strong><?= htmlspecialchars($phone) ?></strong>
              </div>

              <div>
                <span>Email</span>
                <strong><?= htmlspecialchars($email) ?></strong>
              </div>

              <div>
                <span>Fulfilment</span>
                <strong><?= htmlspecialchars($fulfilment) ?></strong>
              </div>

              <div>
                <span>Submitted</span>
                <strong><?= htmlspecialchars(date("d M Y", strtotime($seller["created_at"]))) ?></strong>
              </div>
            </div>

            <div class="seller-actions">

              <?php if ($statusClass !== "approved"): ?>
                <form action="../actions/admin/approve-seller.php" method="POST">
                  <input type="hidden" name="user_id" value="<?= (int)$seller["id"] ?>">

                  <button class="btn btn-primary" type="submit">
                    <i class="fa-solid fa-check"></i>
                    Approve
                  </button>
                </form>
              <?php endif; ?>

              <?php if ($statusClass !== "rejected"): ?>
                <form action="../actions/admin/reject-seller.php" method="POST">
                  <input type="hidden" name="user_id" value="<?= (int)$seller["id"] ?>">

                  <button class="btn btn-light reject-btn" type="submit">
                    <i class="fa-solid fa-xmark"></i>
                    Reject
                  </button>
                </form>
              <?php endif; ?>

            </div>
          </article>

        <?php endforeach; ?>

      </section>

    <?php endif; ?>

  </div>
</main>

<script src="../js/app.js"></script>

</body>
</html>