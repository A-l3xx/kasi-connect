<?php
require_once "../includes/auth.php";
requireAdmin();
require_once "../includes/db.php";

$fullName = $_SESSION["full_name"] ?? "Admin";
$initial = strtoupper(substr($fullName, 0, 1));
$currentAdminId = (int)$_SESSION["user_id"];

$success = $_GET["success"] ?? "";
$filter = $_GET["role"] ?? "all";
$search = trim($_GET["q"] ?? "");

$sql = "
  SELECT id, full_name, email, phone, role, status, created_at
  FROM users
  WHERE 1 = 1
";

$params = [];

if ($filter !== "all") {
  $sql .= " AND role = ?";
  $params[] = $filter;
}

if ($search !== "") {
  $sql .= "
    AND (
      full_name LIKE ?
      OR email LIKE ?
      OR phone LIKE ?
      OR role LIKE ?
      OR status LIKE ?
    )
  ";

  $like = "%" . $search . "%";

  $params[] = $like;
  $params[] = $like;
  $params[] = $like;
  $params[] = $like;
  $params[] = $like;
}

$sql .= " ORDER BY created_at DESC";

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$users = $stmt->fetchAll(PDO::FETCH_ASSOC);

$totalUsers = (int)$pdo->query("SELECT COUNT(*) FROM users")->fetchColumn();
$totalCustomers = (int)$pdo->query("SELECT COUNT(*) FROM users WHERE role = 'customer'")->fetchColumn();
$totalSellers = (int)$pdo->query("SELECT COUNT(*) FROM users WHERE role = 'seller'")->fetchColumn();
$totalAdmins = (int)$pdo->query("SELECT COUNT(*) FROM users WHERE role = 'admin'")->fetchColumn();

function roleClass($role) {
  if ($role === "admin") return "admin";
  if ($role === "seller") return "seller";
  return "customer";
}

function statusClass($status) {
  if ($status === "active") return "active";
  if ($status === "pending") return "pending";
  return "blocked";
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width,initial-scale=1.0">

  <title>Kasi Connect | Manage Users</title>

  <link rel="stylesheet" href="../assets/css/style.css?v=630">
  <link rel="stylesheet" href="../assets/css/manage-users.css?v=3">

  <link rel="stylesheet"
  href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
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
          <span class="profile-avatar-sm">
            <?= htmlspecialchars($initial) ?>
          </span>

          <span class="profile-name">
            <?= htmlspecialchars($fullName) ?>
          </span>

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

<main class="users-page">

  <div class="users-container">

    <section class="users-hero">

      <div>
        <p class="eyebrow">USER MANAGEMENT</p>

        <h1>Manage users</h1>

        <p>
          View customers, sellers and admins.
          Manage account status, permissions,
          and user access across the platform.
        </p>
      </div>

      <a href="admin-dashboard.php" class="btn btn-light">
        <i class="fa-solid fa-arrow-left"></i>
        Back to dashboard
      </a>

    </section>

    <?php if ($success === "blocked"): ?>
      <div class="auth-alert">User blocked successfully.</div>
    <?php elseif ($success === "activated"): ?>
      <div class="auth-success">User activated successfully.</div>
    <?php elseif ($success === "deleted"): ?>
      <div class="auth-alert">User deleted successfully.</div>
    <?php endif; ?>

    <section class="users-stats">

      <div class="user-stat-card">
        <i class="fa-solid fa-users"></i>
        <span>Total Users</span>
        <strong><?= (int)$totalUsers ?></strong>
      </div>

      <div class="user-stat-card">
        <i class="fa-solid fa-user"></i>
        <span>Customers</span>
        <strong><?= (int)$totalCustomers ?></strong>
      </div>

      <div class="user-stat-card">
        <i class="fa-solid fa-store"></i>
        <span>Sellers</span>
        <strong><?= (int)$totalSellers ?></strong>
      </div>

      <div class="user-stat-card">
        <i class="fa-solid fa-shield-halved"></i>
        <span>Admins</span>
        <strong><?= (int)$totalAdmins ?></strong>
      </div>

    </section>

    <section class="users-toolbar">

      <form class="users-search" action="manage-users.php" method="GET">
        <i class="fa-solid fa-magnifying-glass"></i>

        <input
          type="text"
          name="q"
          value="<?= htmlspecialchars($search) ?>"
          placeholder="Search by name, email, phone, role or status..."
        >

        <input type="hidden" name="role" value="<?= htmlspecialchars($filter) ?>">
      </form>

      <form action="manage-users.php" method="GET">
        <input type="hidden" name="q" value="<?= htmlspecialchars($search) ?>">

        <select name="role" onchange="this.form.submit()">
          <option value="all" <?= $filter === "all" ? "selected" : "" ?>>All users</option>
          <option value="customer" <?= $filter === "customer" ? "selected" : "" ?>>Customers</option>
          <option value="seller" <?= $filter === "seller" ? "selected" : "" ?>>Sellers</option>
          <option value="admin" <?= $filter === "admin" ? "selected" : "" ?>>Admins</option>
        </select>
      </form>

    </section>

    <section class="users-table-card">

      <div class="table-head">
        <div>
          <p class="eyebrow">ACCOUNTS</p>
          <h2>User list</h2>
        </div>
      </div>

      <?php if (count($users) === 0): ?>

        <div class="empty-admin-state">
          <i class="fa-solid fa-users"></i>
          <h3>No users found</h3>
          <p>Try another search or role filter.</p>
        </div>

      <?php else: ?>

        <div class="users-table-wrap">

          <table class="users-table">

            <thead>
              <tr>
                <th>User</th>
                <th>Role</th>
                <th>Status</th>
                <th>Phone</th>
                <th>Joined</th>
                <th>Actions</th>
              </tr>
            </thead>

            <tbody>

              <?php foreach ($users as $user): ?>
                <?php
                  $userInitial = strtoupper(substr($user["full_name"], 0, 1));
                  $isCurrentAdmin = ((int)$user["id"] === $currentAdminId);
                ?>

                <tr>

                  <td>
                    <div class="user-cell">
                      <div class="user-avatar">
                        <?= htmlspecialchars($userInitial) ?>
                      </div>

                      <div>
                        <strong><?= htmlspecialchars($user["full_name"]) ?></strong>
                        <span><?= htmlspecialchars($user["email"]) ?></span>
                      </div>
                    </div>
                  </td>

                  <td>
                    <span class="role-pill <?= htmlspecialchars(roleClass($user["role"])) ?>">
                      <?= ucfirst(htmlspecialchars($user["role"])) ?>
                    </span>
                  </td>

                  <td>
                    <span class="status-pill <?= htmlspecialchars(statusClass($user["status"])) ?>">
                      <?= ucfirst(htmlspecialchars($user["status"])) ?>
                    </span>
                  </td>

                  <td><?= htmlspecialchars($user["phone"] ?: "N/A") ?></td>

                  <td>
                    <?= htmlspecialchars(date("d M Y", strtotime($user["created_at"]))) ?>
                  </td>

                  <td>
                    <div class="table-actions">

                      <button class="icon-btn" type="button" title="View user">
                        <i class="fa-solid fa-eye"></i>
                      </button>

                      <?php if (!$isCurrentAdmin): ?>

                        <?php if ($user["status"] === "blocked"): ?>

                          <form action="../actions/admin/activate-user.php" method="POST">
                            <input type="hidden" name="user_id" value="<?= (int)$user["id"] ?>">

                            <button class="icon-btn success" type="submit" title="Activate user">
                              <i class="fa-solid fa-check"></i>
                            </button>
                          </form>

                        <?php else: ?>

                          <form action="../actions/admin/block-user.php" method="POST">
                            <input type="hidden" name="user_id" value="<?= (int)$user["id"] ?>">

                            <button class="icon-btn warning" type="submit" title="Block user">
                              <i class="fa-solid fa-ban"></i>
                            </button>
                          </form>

                        <?php endif; ?>

                        <form action="../actions/admin/delete-user.php" method="POST">
                          <input type="hidden" name="user_id" value="<?= (int)$user["id"] ?>">

                          <button class="icon-btn danger" type="submit" title="Delete user">
                            <i class="fa-solid fa-trash"></i>
                          </button>
                        </form>

                      <?php endif; ?>

                    </div>
                  </td>

                </tr>

              <?php endforeach; ?>

            </tbody>

          </table>

        </div>

      <?php endif; ?>

    </section>

  </div>

</main>

<script src="../js/app.js"></script>

</body>
</html>