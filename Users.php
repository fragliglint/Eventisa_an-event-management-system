<?php
session_start();
require_once 'config/database.php';

// Check if user is logged in and is admin
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}
if ($_SESSION['user_role'] !== 'admin') {
    header('Location: Dashboard.php');
    exit();
}

$database = new Database();
$db = $database->getConnection();

// Handle delete request
if (isset($_GET['delete'])) {
    $id = intval($_GET['delete']);
    $stmt = $db->prepare("DELETE FROM users WHERE id = ?");
    $stmt->execute([$id]);
    header('Location: Users.php?msg=deleted');
    exit();
}

// Fetch all users
$query = $db->query("SELECT id, first_name, last_name, email, phone, role, status, created_at FROM users ORDER BY id DESC");
$users = $query->fetchAll(PDO::FETCH_ASSOC);
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width,initial-scale=1" />
  <title>Eventisa | Users</title>
  <link rel="stylesheet" href="css/styles.css" />
  <style>
    .btn { padding: 6px 12px; border-radius: 6px; text-decoration: none; color: white; font-weight: 600; }
    .btn-add { background: #3b82f6; }
    .btn-edit { background: #10b981; }
    .btn-del { background: #ef4444; }
    .btn:hover { opacity: 0.9; }
    table { width: 100%; border-collapse: collapse; margin-top: 15px; }
    th, td { padding: 10px; border-bottom: 1px solid #ddd; text-align: left; }
    th { background: #f3f4f6; }
    .status-active { color: #10b981; font-weight: bold; }
    .status-inactive { color: #f59e0b; font-weight: bold; }
    .status-suspended { color: #ef4444; font-weight: bold; }
    .actions a { margin-right: 8px; }
  </style>
</head>
<body>
  <div class="app">
    <!-- Sidebar -->
    <aside class="sidebar">
      <div class="brand">
        <div class="brand-logo">E</div>
        <div class="brand-text">Eventisa</div>
      </div>
      <nav class="menu">
        <a class="menu-item" href="Dashboard.php">▦ Dashboard</a>
        <a class="menu-item" href="Bookings.php">☑ Bookings</a>
        <a class="menu-item" href="Invoices.php">🧾 Invoices</a>
        <a class="menu-item" href="Calendar.php">🗓️ Calendar</a>
        <a class="menu-item" href="Event.php">★ Events</a>
        <a class="menu-item" href="Financial.php">💹 Financials</a>
        <a class="menu-item active" href="Users.php">👥 Users</a>
      </nav>
      <div class="sidebar-bottom">
        <div class="logout-section">
          <div class="user-info">
            <small>Logged in as <strong><?php echo $_SESSION['user_name']; ?></strong></small>
            <span class="user-role"><?php echo ucfirst($_SESSION['user_role']); ?></span>
          </div>
          <button class="logout" onclick="logout()">Sign Out</button>
        </div>
      </div>
    </aside>

    <!-- Main -->
    <div class="main">
      <header class="topbar">
        <div class="top-left">
          <h1>Manage Users</h1>
          <div class="top-sub">Admin access only</div>
        </div>
        <div class="top-right">
          <button class="icon-btn" onclick="window.location.href='Dashboard.php'">🏠</button>
        </div>
      </header>

      <main class="content">
        <div class="card">
          <div style="display:flex; justify-content:space-between; align-items:center;">
            <h3>User List</h3>
            <a href="add_user.php" class="btn btn-add">+ Add New User</a>
          </div>

          <?php if (isset($_GET['msg']) && $_GET['msg'] === 'deleted'): ?>
            <p style="color:#10b981; font-weight:600;">✅ User deleted successfully!</p>
          <?php endif; ?>

          <table>
            <thead>
              <tr>
                <th>#</th>
                <th>Name</th>
                <th>Email</th>
                <th>Phone</th>
                <th>Role</th>
                <th>Status</th>
                <th>Joined</th>
                <th>Actions</th>
              </tr>
            </thead>
            <tbody>
              <?php if (count($users) > 0): ?>
                <?php foreach($users as $user): ?>
                  <tr>
                    <td><?php echo $user['id']; ?></td>
                    <td><?php echo $user['first_name'] . ' ' . $user['last_name']; ?></td>
                    <td><?php echo $user['email']; ?></td>
                    <td><?php echo $user['phone'] ?: '-'; ?></td>
                    <td><?php echo ucfirst($user['role']); ?></td>
                    <td class="status-<?php echo strtolower($user['status']); ?>"><?php echo ucfirst($user['status']); ?></td>
                    <td><?php echo date('M j, Y', strtotime($user['created_at'])); ?></td>
                    <td class="actions">
                      <a href="edit_user.php?id=<?php echo $user['id']; ?>" class="btn btn-edit">Edit</a>
                      <a href="Users.php?delete=<?php echo $user['id']; ?>" class="btn btn-del" onclick="return confirm('Are you sure to delete this user?')">Delete</a>
                    </td>
                  </tr>
                <?php endforeach; ?>
              <?php else: ?>
                <tr><td colspan="8" style="text-align:center;">No users found</td></tr>
              <?php endif; ?>
            </tbody>
          </table>
        </div>
      </main>

      <footer class="footer">
        <div>© 2025 Eventisa</div>
      </footer>
    </div>
  </div>

  <script>
    function logout() {
        if (confirm('Are you sure you want to logout?')) {
            window.location.href = 'logout.php';
        }
    }
  </script>
</body>
</html>
