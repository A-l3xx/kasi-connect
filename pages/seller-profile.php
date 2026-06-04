<?php
require_once "../includes/auth.php";
requireSeller();
require_once "../includes/db.php";

$fullName = $_SESSION["full_name"] ?? "Seller";
$email = $_SESSION["email"] ?? "";
$initial = strtoupper(substr($fullName, 0, 1));
$sellerId = (int)$_SESSION["user_id"];

$success = $_GET["success"] ?? "";
$error = $_GET["error"] ?? "";

$stmt = $pdo->prepare("
  SELECT 
    users.full_name,
    users.email,
    users.phone,
    users.status,
    seller_profiles.business_name,
    seller_profiles.business_category,
    seller_profiles.township_area,
    seller_profiles.fulfilment_option,
    seller_profiles.approval_status
  FROM users
  LEFT JOIN seller_profiles ON seller_profiles.user_id = users.id
  WHERE users.id = ?
  LIMIT 1
");

$stmt->execute([$sellerId]);
$seller = $stmt->fetch(PDO::FETCH_ASSOC);

$businessName = $seller["business_name"] ?? "";
$businessCategory = $seller["business_category"] ?? "";
$townshipArea = $seller["township_area"] ?? "";
$phone = $seller["phone"] ?? "";
$fulfilmentOption = $seller["fulfilment_option"] ?? "";
$approvalStatus = $seller["approval_status"] ?? "pending";

$isApproved =
  ($seller["status"] ?? "") === "active" ||
  $approvalStatus === "approved";

function fulfilmentLabel($value) {
  if ($value === "pickup") return "Pickup only";
  if ($value === "delivery") return "Delivery only";
  if ($value === "pickup_delivery") return "Pickup & delivery";
  return "Not selected";
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width,initial-scale=1.0">
  <title>Kasi Connect | Seller Profile</title>

  <link rel="stylesheet" href="../assets/css/style.css?v=780">
  <link rel="stylesheet" href="../assets/css/seller-profile.css?v=1">
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
          <span class="profile-avatar-sm"><?= htmlspecialchars($initial) ?></span>
          <span class="profile-name"><?= htmlspecialchars($fullName) ?></span>
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

          <a href="marketplace.php">
            <i class="fa-solid fa-store"></i>
            Marketplace
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

<main class="seller-profile-page">
  <div class="seller-profile-container">

    <section class="seller-profile-hero">
      <div>
        <p class="eyebrow">SELLER PROFILE</p>
        <h1>Business profile</h1>
        <p>Manage your store information, contact details, fulfilment options and approval status.</p>
      </div>

      <a href="seller-dashboard.php" class="btn btn-light">
        <i class="fa-solid fa-arrow-left"></i>
        Back to dashboard
      </a>
    </section>

    <?php if ($success === "updated"): ?>
      <div class="auth-success">Seller profile updated successfully.</div>
    <?php elseif ($error === "missing_fields"): ?>
      <div class="auth-alert">Please fill in all required fields.</div>
    <?php elseif ($error === "server_error"): ?>
      <div class="auth-alert">Something went wrong. Please try again.</div>
    <?php endif; ?>

    <section class="profile-status-card <?= $isApproved ? 'approved' : 'pending' ?>">
      <i class="fa-solid <?= $isApproved ? 'fa-circle-check' : 'fa-clock' ?>"></i>

      <div>
        <strong><?= $isApproved ? "Seller account approved" : "Seller approval pending" ?></strong>
        <p>
          <?= $isApproved
            ? "Your seller account is active. Listings you publish will go to admin review before appearing on the marketplace."
            : "Your seller account is waiting for admin approval before your products can go live." ?>
        </p>
      </div>
    </section>

    <section class="seller-profile-grid">

      <article class="seller-profile-card">
        <div class="section-head">
          <div>
            <p class="eyebrow">BUSINESS DETAILS</p>
            <h2>Update store information</h2>
          </div>
        </div>

        <form action="../actions/seller/update-profile.php" method="POST">

          <div class="form-grid">
            <div class="form-group">
              <label>Business/store name</label>
              <input
                class="form-control"
                type="text"
                name="business_name"
                value="<?= htmlspecialchars($businessName) ?>"
                required
              >
            </div>

            <div class="form-group">
              <label>Business category</label>
              <select class="form-control" name="business_category" required>
                <option value="">Select category</option>
                <option value="Food" <?= $businessCategory === "Food" ? "selected" : "" ?>>Food</option>
                <option value="Fashion" <?= $businessCategory === "Fashion" ? "selected" : "" ?>>Fashion</option>
                <option value="Repairs" <?= $businessCategory === "Repairs" ? "selected" : "" ?>>Repairs</option>
                <option value="Beauty" <?= $businessCategory === "Beauty" ? "selected" : "" ?>>Beauty</option>
                <option value="Groceries" <?= $businessCategory === "Groceries" ? "selected" : "" ?>>Groceries</option>
                <option value="Transport" <?= $businessCategory === "Transport" ? "selected" : "" ?>>Transport</option>
                <option value="Services" <?= $businessCategory === "Services" ? "selected" : "" ?>>Services</option>
              </select>
            </div>

            <div class="form-group">
              <label>Township/area</label>
              <input
                class="form-control"
                type="text"
                name="township_area"
                value="<?= htmlspecialchars($townshipArea) ?>"
                placeholder="Example: Soweto / Tembisa"
                required
              >
            </div>

            <div class="form-group">
              <label>Phone number</label>
              <input
                class="form-control"
                type="tel"
                name="phone"
                value="<?= htmlspecialchars($phone) ?>"
                required
              >
            </div>

            <div class="form-group full">
              <label>Pickup or delivery option</label>
              <select class="form-control" name="fulfilment_option" required>
                <option value="">Select option</option>
                <option value="pickup" <?= $fulfilmentOption === "pickup" ? "selected" : "" ?>>Pickup only</option>
                <option value="delivery" <?= $fulfilmentOption === "delivery" ? "selected" : "" ?>>Delivery only</option>
                <option value="pickup_delivery" <?= $fulfilmentOption === "pickup_delivery" ? "selected" : "" ?>>Pickup & delivery</option>
              </select>
            </div>
          </div>

          <button class="btn btn-primary" type="submit">
            <i class="fa-solid fa-floppy-disk"></i>
            Save profile
          </button>

        </form>
      </article>

      <aside class="seller-profile-card summary-card">
        <div class="seller-avatar-large">
          <?= htmlspecialchars($initial) ?>
        </div>

        <h2><?= htmlspecialchars($businessName ?: $fullName) ?></h2>
        <p><?= htmlspecialchars($email) ?></p>

        <div class="summary-list">
          <div>
            <span>Owner</span>
            <strong><?= htmlspecialchars($fullName) ?></strong>
          </div>

          <div>
            <span>Category</span>
            <strong><?= htmlspecialchars($businessCategory ?: "Not selected") ?></strong>
          </div>

          <div>
            <span>Area</span>
            <strong><?= htmlspecialchars($townshipArea ?: "Not provided") ?></strong>
          </div>

          <div>
            <span>Fulfilment</span>
            <strong><?= htmlspecialchars(fulfilmentLabel($fulfilmentOption)) ?></strong>
          </div>

          <div>
            <span>Status</span>
            <strong><?= $isApproved ? "Approved" : "Pending" ?></strong>
          </div>
        </div>
      </aside>

    </section>

  </div>
</main>

<script src="../js/app.js"></script>
<script src="../js/logout.js"></script>

</body>
</html>