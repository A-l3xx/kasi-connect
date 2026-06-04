<?php
$error = $_GET["error"] ?? "";
$success = $_GET["success"] ?? "";
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width,initial-scale=1.0">

  <title>Kasi Connect | Login</title>

  <link rel="stylesheet" href="../assets/css/style.css?v=300">
  <link rel="stylesheet" href="../assets/css/auth.css?v=5">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
</head>
<body>

<main class="auth-page">

  <section class="auth-container">

    <!-- LEFT SIDE -->

    <div class="auth-brand">

      <div class="logo">
        <div class="logo-mark">K</div>
        <span>Kasi-Connect</span>
      </div>

      <p class="eyebrow">WELCOME BACK</p>

      <h1>Login to your account.</h1>

      <p class="auth-description">
        Continue shopping local products and services,
        manage your orders, wishlist, cart,
        or access seller tools if approved.
      </p>

      <div class="auth-features">

        <div class="feature-item">
          <i class="fa-solid fa-store"></i>
          <span>Support township businesses</span>
        </div>

        <div class="feature-item">
          <i class="fa-solid fa-shield-halved"></i>
          <span>Secure customer accounts</span>
        </div>

        <div class="feature-item">
          <i class="fa-solid fa-cart-shopping"></i>
          <span>Track orders in real-time</span>
        </div>

      </div>

      <a href="signup.php" class="text-link">
        Don’t have an account?
        <strong>Create account</strong>
      </a>

    </div>

    <!-- RIGHT SIDE -->

    <form class="auth-panel"
          action="../actions/auth/login.php"
          method="POST">

      <div class="panel-head">
        <h2>Login</h2>
        <p>Enter your details below</p>
      </div>

      <?php if ($success === "registered"): ?>
        <div class="auth-success">
          Account created successfully. You can now login.
        </div>
      <?php endif; ?>

      <?php if ($error === "missing_fields"): ?>
        <div class="auth-alert">
          Please enter your email and password.
        </div>

      <?php elseif ($error === "invalid_credentials"): ?>
        <div class="auth-alert">
          Invalid email or password.
        </div>

      <?php elseif ($error === "account_blocked"): ?>
        <div class="auth-alert">
          This account has been blocked.
        </div>

      <?php elseif ($error === "seller_pending"): ?>
        <div class="auth-alert">
          Your seller account is still pending admin approval.
        </div>

      <?php elseif ($error === "server_error"): ?>
        <div class="auth-alert">
          Something went wrong. Please try again.
        </div>

      <?php elseif ($error === "login_required"): ?>
        <div class="auth-alert">
          Please login to continue.
        </div>
      <?php endif; ?>

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
                 placeholder="Enter password"
                 required>
        </div>
      </div>

      <!-- OPTIONS -->

      <div class="auth-options">

        <label class="remember-option">
          <input type="checkbox">
          <span>Remember me</span>
        </label>

        <a href="#" class="forgot-link">
          Forgot password?
        </a>

      </div>

      <!-- BUTTON -->

      <button class="btn btn-primary full-btn" type="submit">
        <i class="fa-solid fa-right-to-bracket"></i>
        Login
      </button>

      <p class="auth-note">
        Sellers can only access seller tools after approval.
      </p>

    </form>

  </section>

</main>

</body>
</html>