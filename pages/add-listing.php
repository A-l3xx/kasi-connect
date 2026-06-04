<?php
require_once "../includes/auth.php";
requireSeller();

$fullName = $_SESSION["full_name"] ?? "Seller";
$initial = strtoupper(substr($fullName, 0, 1));

$error = $_GET["error"] ?? "";
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width,initial-scale=1.0">
  <title>Kasi Connect | Add Listing</title>

  <link rel="stylesheet" href="../assets/css/style.css?v=800">
  <link rel="stylesheet" href="../assets/css/add-listing.css?v=2">
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

          <a href="manage-listings.php">
            <i class="fa-solid fa-list-check"></i>
            Manage Listings
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

<main class="add-listing-page">
  <div class="add-listing-container">

    <section class="add-listing-hero">
      <div>
        <p class="eyebrow">NEW LISTING</p>

        <h1>Add product/service</h1>

        <p>
          Create a listing for your product or service. Add a clear image,
          price, location and description so customers understand what you offer.
        </p>
      </div>

      <a href="manage-listings.php" class="btn btn-light">
        <i class="fa-solid fa-list-check"></i>
        Manage listings
      </a>
    </section>

    <?php if ($error === "missing_fields"): ?>
      <div class="auth-alert">Please fill in all required fields.</div>
    <?php elseif ($error === "invalid_image"): ?>
      <div class="auth-alert">Invalid image type. Please upload JPG, JPEG, PNG or WEBP.</div>
    <?php elseif ($error === "image_too_large"): ?>
      <div class="auth-alert">Image is too large. Maximum allowed size is 5MB.</div>
    <?php elseif ($error === "image_upload_failed"): ?>
      <div class="auth-alert">Image upload failed. Please try again.</div>
    <?php elseif ($error === "image_required"): ?>
      <div class="auth-alert">Please upload a product or service image.</div>
    <?php elseif ($error === "server_error"): ?>
      <div class="auth-alert">Something went wrong. Please try again.</div>
    <?php endif; ?>

    <section class="add-listing-card">
      <form
        action="../actions/seller/add-listing.php"
        method="POST"
        enctype="multipart/form-data"
      >

        <div class="section-head">
          <div>
            <p class="eyebrow">LISTING DETAILS</p>
            <h2>Product/service information</h2>
          </div>
        </div>

        <div class="form-grid">

          <div class="form-group">
            <label>Title</label>
            <input
              class="form-control"
              type="text"
              name="title"
              placeholder="Example: Kota & Chips"
              required
            >
          </div>

          <div class="form-group">
            <label>Category</label>
            <select class="form-control" name="category" required>
              <option value="">Select category</option>
              <option value="Food">Food</option>
              <option value="Fashion">Fashion</option>
              <option value="Repairs">Repairs</option>
              <option value="Beauty">Beauty</option>
              <option value="Groceries">Groceries</option>
              <option value="Transport">Transport</option>
              <option value="Services">Services</option>
            </select>
          </div>

          <div class="form-group">
            <label>Price</label>
            <input
              class="form-control"
              type="number"
              name="price"
              min="1"
              step="0.01"
              placeholder="Example: 45.00"
              required
            >

            <small class="price-note">
              Enter the price in South African Rand.
            </small>
          </div>

          <div class="form-group">
            <label>Location area</label>
            <input
              class="form-control"
              type="text"
              name="location_area"
              placeholder="Example: Soweto / Tembisa"
              required
            >
          </div>

          <div class="form-group full">
            <label>Product/service image</label>

            <input
              class="form-control"
              type="file"
              name="product_image"
              accept="image/*"
              required
            >

            <small class="price-note">
              Upload a clear JPG, PNG or WEBP image. Maximum size: 5MB.
            </small>
          </div>

        </div>

        <div class="form-group">
          <label>Description</label>

          <textarea
            class="form-control textarea"
            name="description"
            placeholder="Describe your product or service clearly..."
            required
          ></textarea>
        </div>

        <div class="listing-tips">
          <h3>
            <i class="fa-solid fa-lightbulb"></i>
            Tips for a good listing
          </h3>

          <ul>
            <li>Use a clear product or service name.</li>
            <li>Upload a real image of what you sell.</li>
            <li>Add a fair price that customers can understand.</li>
            <li>Mention your area so nearby customers can find you.</li>
            <li>Write a short but useful description.</li>
          </ul>
        </div>

        <button class="btn btn-primary" type="submit">
          <i class="fa-solid fa-paper-plane"></i>
          Publish listing
        </button>

      </form>
    </section>

  </div>
</main>

<script src="../js/app.js"></script>
<script src="../js/add-listing.js"></script>
<script src="../js/logout.js"></script>


</body>
</html>