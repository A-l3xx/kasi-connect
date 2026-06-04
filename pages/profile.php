<?php
require_once "../includes/auth.php";
requireLogin();

$fullName = $_SESSION["full_name"] ?? "User";
$email = $_SESSION["email"] ?? "";
$role = $_SESSION["role"] ?? "customer";
$initial = strtoupper(substr($fullName, 0, 1));
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width,initial-scale=1.0">
  <title>Kasi Connect | Profile</title>

  <link rel="stylesheet" href="../assets/css/style.css?v=390">
  <link rel="stylesheet" href="../assets/css/profile.css?v=3">
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
      <a href="home.php"><i class="fa-solid fa-house"></i> Home</a>
      <a href="dashboard.php"><i class="fa-solid fa-table-columns"></i> Dashboard</a>
      <a href="marketplace.php"><i class="fa-solid fa-store"></i> Marketplace</a>

      <div class="profile-menu">
        <button class="profile-toggle" type="button">
          <span class="profile-avatar-sm"><?= htmlspecialchars($initial) ?></span>
          <span class="profile-name"><?= htmlspecialchars($fullName) ?></span>
          <i class="fa-solid fa-chevron-down"></i>
        </button>

        <div class="profile-dropdown">
          <a href="orders.php"><i class="fa-solid fa-box"></i> Orders</a>
          <a href="wishlist.php"><i class="fa-solid fa-heart"></i> Wishlist</a>
          <a href="cart.php"><i class="fa-solid fa-cart-shopping"></i> Cart</a>
          <a href="profile.php"><i class="fa-solid fa-user"></i> Profile</a>

          <a href="login.php" class="logout-link logout-btn">
            <i class="fa-solid fa-right-from-bracket"></i>
            Logout
          </a>
        </div>
      </div>
    </nav>

  </div>
</header>

<main class="profile-page">
  <div class="profile-container">

    <section class="profile-hero">
      <div>
        <p class="eyebrow">MY ACCOUNT</p>
        <h1>Profile settings</h1>
        <p>Manage your personal details, delivery address and account security.</p>
      </div>

      <a href="orders.php" class="btn btn-primary">
        <i class="fa-solid fa-box"></i>
        View orders
      </a>
    </section>

    <section class="profile-layout">

      <aside class="profile-sidebar">
        <div class="profile-avatar"><?= htmlspecialchars($initial) ?></div>

        <h3><?= htmlspecialchars($fullName) ?></h3>
        <p><?= ucfirst(htmlspecialchars($role)) ?> Account</p>

        <a class="active" href="#personal">
          <i class="fa-solid fa-user"></i>
          Personal Info
        </a>

        <a href="#address">
          <i class="fa-solid fa-location-dot"></i>
          Address
        </a>

        <a href="#security">
          <i class="fa-solid fa-lock"></i>
          Security
        </a>

        <a href="#preferences">
          <i class="fa-solid fa-gear"></i>
          Preferences
        </a>
      </aside>

      <div class="profile-content">

        <section class="profile-card" id="personal">
          <div class="card-head">
            <div>
              <p class="eyebrow">ACCOUNT DETAILS</p>
              <h2>Personal information</h2>
            </div>
          </div>

          <div class="form-grid">
            <div class="form-group">
              <label>Full name</label>
              <input class="form-control" type="text" name="full_name" value="<?= htmlspecialchars($fullName) ?>">
            </div>

            <div class="form-group">
              <label>Email address</label>
              <input class="form-control" type="email" name="email" value="<?= htmlspecialchars($email) ?>">
            </div>

            <div class="form-group">
              <label>Phone number</label>
              <input class="form-control" type="tel" name="phone" placeholder="+27 82 000 0000">
            </div>

            <div class="form-group">
              <label>Account type</label>
              <input class="form-control" type="text" value="<?= ucfirst(htmlspecialchars($role)) ?>" disabled>
            </div>
          </div>

          <button class="btn btn-primary" type="button">
            Save changes
          </button>
        </section>

        <section class="profile-card" id="address">
          <div class="card-head">
            <div>
              <p class="eyebrow">DELIVERY DETAILS</p>
              <h2>Address management</h2>
            </div>
          </div>

          <div class="form-group">
            <label>Street / landmark</label>
            <input class="form-control" type="text" name="street" placeholder="Example: Near Shoprite, main road">
          </div>

          <div class="form-grid">
            <div class="form-group">
              <label>Area</label>
              <input class="form-control" type="text" name="area" placeholder="Soweto">
            </div>

            <div class="form-group">
              <label>City</label>
              <input class="form-control" type="text" name="city" placeholder="Johannesburg">
            </div>
          </div>

          <div class="form-group">
            <label>Delivery note</label>
            <textarea class="form-control textarea" name="delivery_note" placeholder="Example: Call me when outside"></textarea>
          </div>

          <button class="btn btn-primary" type="button">
            Save address
          </button>
        </section>

        <section class="profile-card" id="security">
          <div class="card-head">
            <div>
              <p class="eyebrow">SECURITY</p>
              <h2>Password & security</h2>
            </div>
          </div>

          <div class="form-group">
            <label>Current password</label>
            <input class="form-control" type="password" name="current_password" placeholder="Enter current password">
          </div>

          <div class="form-grid">
            <div class="form-group">
              <label>New password</label>
              <input class="form-control" type="password" name="new_password" placeholder="New password">
            </div>

            <div class="form-group">
              <label>Confirm password</label>
              <input class="form-control" type="password" name="confirm_password" placeholder="Confirm password">
            </div>
          </div>

          <div class="security-note">
            <i class="fa-solid fa-shield-halved"></i>
            <p>Use a strong password with letters, numbers and symbols.</p>
          </div>

          <button class="btn btn-dark" type="button">
            Update password
          </button>
        </section>

        <section class="profile-card" id="preferences">
          <div class="card-head">
            <div>
              <p class="eyebrow">PREFERENCES</p>
              <h2>Shopping preferences</h2>
            </div>
          </div>

          <div class="form-grid">
            <div class="form-group">
              <label>Preferred language</label>
              <select class="form-control" name="language">
                <option>English</option>
                <option>Zulu</option>
                <option>Xhosa</option>
                <option>Sotho</option>
              </select>
            </div>

            <div class="form-group">
              <label>Preferred fulfilment</label>
              <select class="form-control" name="fulfilment">
                <option>Pickup</option>
                <option>Local delivery</option>
                <option>Either pickup or delivery</option>
              </select>
            </div>
          </div>

          <div class="preference-toggle">
            <div>
              <strong>Order updates</strong>
              <p>Receive updates from sellers about your orders.</p>
            </div>

            <label class="switch">
              <input type="checkbox" checked>
              <span></span>
            </label>
          </div>

          <div class="preference-toggle">
            <div>
              <strong>Local deals</strong>
              <p>Receive marketplace discounts and local offers.</p>
            </div>

            <label class="switch">
              <input type="checkbox">
              <span></span>
            </label>
          </div>

          <button class="btn btn-primary" type="button">
            Save preferences
          </button>
        </section>

      </div>

    </section>

       <section class="profile-card">
         <div class="card-head">
    <div>
      <p class="eyebrow">DISPLAY</p>
      <h2>Theme preference</h2>
    </div>
  </div>

  <div class="form-group">
    <label>Choose theme</label>

    <select id="themePreference" class="form-control">
      <option value="light">Light mode</option>
      <option value="dark">Dark mode</option>
    </select>

    <small class="form-help">
      This changes the appearance of the whole Kasi Connect system.
    </small>
  </div>
</section>

  </div>
</main>

<script src="../js/app.js"></script>
<script src="../js/logout.js"></script>

</body>
</html>