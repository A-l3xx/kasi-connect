<?php
$error = $_GET["error"] ?? "";
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width,initial-scale=1.0">

  <title>Kasi Connect | Create Account</title>

  <link rel="stylesheet" href="../assets/css/style.css?v=310">
  <link rel="stylesheet" href="../assets/css/auth.css?v=6">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
</head>
<body>

<main class="auth-page">

  <section class="auth-container">

    <!-- LEFT -->

    <div class="auth-brand">

      <div class="logo">
        <div class="logo-mark">K</div>
        <span>Kasi-Connect</span>
      </div>

      <p class="eyebrow">JOIN KASI CONNECT</p>

      <h1>Create your account.</h1>

      <p class="auth-description">
        Join Kasi Connect as a customer or seller.
        Discover local products, support township businesses,
        and grow your community marketplace.
      </p>

      <div class="auth-features">

        <div class="feature-item">
          <i class="fa-solid fa-store"></i>
          <span>Sell products & services locally</span>
        </div>

        <div class="feature-item">
          <i class="fa-solid fa-bag-shopping"></i>
          <span>Shop trusted local businesses</span>
        </div>

        <div class="feature-item">
          <i class="fa-solid fa-shield-halved"></i>
          <span>Secure account management</span>
        </div>

      </div>

      <a href="login.php" class="text-link">
        Already have an account?
        <strong>Login</strong>
      </a>

    </div>

    <!-- RIGHT -->

    <form class="auth-panel"
          action="../actions/auth/register.php"
          method="POST">

      <div class="panel-head">
        <h2>Create account</h2>
        <p>Select your account type below</p>
      </div>

      <?php if ($error === "missing_fields"): ?>
        <div class="auth-alert">
          Please fill in all required fields.
        </div>

      <?php elseif ($error === "invalid_email"): ?>
        <div class="auth-alert">
          Please enter a valid email address.
        </div>

      <?php elseif ($error === "email_exists"): ?>
        <div class="auth-alert">
          This email is already registered.
        </div>

      <?php elseif ($error === "server_error"): ?>
        <div class="auth-alert">
          Something went wrong. Please try again.
        </div>
      <?php endif; ?>

      <!-- ROLE -->

      <div class="form-group">
        <label>Account type</label>

        <div class="input-icon">
          <i class="fa-solid fa-user-group"></i>

          <select class="form-control"
                  name="role"
                  id="roleSelect"
                  required>

            <option value="customer">Customer</option>
            <option value="seller">Seller</option>

          </select>
        </div>
      </div>

      <!-- FULL NAME -->

      <div class="form-group">
        <label>Full name</label>

        <div class="input-icon">
          <i class="fa-regular fa-user"></i>

          <input class="form-control"
                 type="text"
                 name="full_name"
                 placeholder="Enter full name"
                 required>
        </div>
      </div>

      <!-- SELLER FIELDS -->

      <div class="seller-fields" id="sellerFields">

        <div class="form-group">
          <label>Business / store name</label>

          <div class="input-icon">
            <i class="fa-solid fa-store"></i>

            <input class="form-control"
                   type="text"
                   name="business_name"
                   placeholder="Example: Nana’s Kitchen">
          </div>
        </div>

        <div class="form-group">
          <label>Business category</label>

          <div class="input-icon">
            <i class="fa-solid fa-layer-group"></i>

            <select class="form-control"
                    name="business_category">

              <option value="">Choose category</option>
              <option value="Food">Food</option>
              <option value="Fashion">Fashion</option>
              <option value="Beauty">Beauty</option>
              <option value="Repairs">Repairs</option>
              <option value="Groceries">Groceries</option>
              <option value="Transport">Transport</option>
              <option value="Services">Services</option>

            </select>
          </div>
        </div>

        <div class="form-group">
          <label>Township / area</label>

          <div class="input-icon">
            <i class="fa-solid fa-location-dot"></i>

            <input class="form-control"
                   type="text"
                   name="township_area"
                   placeholder="Example: Soweto">
          </div>
        </div>

        <div class="form-group">
          <label>Pickup or delivery option</label>

          <div class="input-icon">
            <i class="fa-solid fa-truck-fast"></i>

            <select class="form-control"
                    name="fulfilment_option">

              <option value="">Choose option</option>
              <option value="pickup">Pickup only</option>
              <option value="delivery">Delivery only</option>
              <option value="pickup_delivery">Pickup & delivery</option>

            </select>
          </div>
        </div>

      </div>

      <!-- PHONE -->

      <div class="form-group">
        <label>Phone number</label>

        <div class="input-icon">
          <i class="fa-solid fa-phone"></i>

          <input class="form-control"
                 type="tel"
                 name="phone"
                 placeholder="+27 82 000 0000"
                 required>
        </div>
      </div>

      <!-- EMAIL -->

      <div class="form-group">
        <label>Email address</label>

        <div class="input-icon">
          <i class="fa-regular fa-envelope"></i>

          <input class="form-control"
                 type="email"
                 name="email"
                 placeholder="you@example.com"
                 required>
        </div>
      </div>

      <!-- PASSWORD -->

      <div class="form-group">
        <label>Password</label>

        <div class="input-icon">
          <i class="fa-solid fa-lock"></i>

          <input class="form-control"
                 type="password"
                 name="password"
                 placeholder="Create password"
                 required>
        </div>
      </div>

      <!-- BUTTON -->

      <button class="btn btn-primary full-btn"
              type="submit">

        <i class="fa-solid fa-user-plus"></i>
        Create account

      </button>

      <p class="auth-note">
        Seller accounts require admin approval before accessing seller tools.
      </p>

    </form>

  </section>

</main>

<script>

const roleSelect = document.getElementById("roleSelect");
const sellerFields = document.getElementById("sellerFields");

function toggleSellerFields() {

  if (roleSelect.value === "seller") {
    sellerFields.style.display = "block";
  } else {
    sellerFields.style.display = "none";
  }

}

roleSelect.addEventListener("change", toggleSellerFields);

toggleSellerFields();

</script>

</body>
</html>