<?php
session_start();
require_once 'config/database.php';

$database = new Database();
$db = $database->getConnection();

// Get invoices data
$invoices = $db->query("SELECT i.*, b.customer_name, b.customer_email, b.customer_phone, b.tickets_qty, e.title as event_name 
                       FROM invoices i 
                       JOIN bookings b ON i.booking_id = b.id 
                       JOIN events e ON b.event_id = e.id 
                       ORDER BY i.issue_date DESC")->fetchAll(PDO::FETCH_ASSOC);

// Get stats
$paid_count = $db->query("SELECT COUNT(*) FROM invoices WHERE status='Paid'")->fetchColumn();
$unpaid_count = $db->query("SELECT COUNT(*) FROM invoices WHERE status='Unpaid'")->fetchColumn();

// Get selected invoice
$selected_invoice = null;
if (isset($_GET['invoice']) && !empty($_GET['invoice'])) {
    $invoice_stmt = $db->prepare("SELECT i.*, b.*, e.title as event_name 
                                 FROM invoices i 
                                 JOIN bookings b ON i.booking_id = b.id 
                                 JOIN events e ON b.event_id = e.id 
                                 WHERE i.invoice_number = ?");
    $invoice_stmt->execute([$_GET['invoice']]);
    $selected_invoice = $invoice_stmt->fetch(PDO::FETCH_ASSOC);
} else {
    $selected_invoice = $invoices[0] ?? null;
}
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width,initial-scale=1" />
  <title>Eventisa | Invoices</title>
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
  <link rel="stylesheet" href="css/styles.css" />
  <link rel="stylesheet" href="css/invoices.css" />
</head>
<body>
  <div class="app">
    <!-- Sidebar -->
    <aside class="sidebar">
      <div class="sidebar-top">
        <div class="brand">
          <div class="brand-logo">E</div>
          <div class="brand-text"><div class="brand-name">Eventisa</div></div>
        </div>
        <nav class="menu">
          <a class="menu-item" href="Dashboard.php"><span class="mi-icon">▦</span> Dashboard</a>
          <a class="menu-item" href="Bookings.php"><span class="mi-icon">☑</span> Bookings</a>
          <a class="menu-item active" href="Invoices.php"><span class="mi-icon">🧾</span> Invoices</a>
          <a class="menu-item" href="Calendar.php"><span class="mi-icon">🗓️</span> Calendar</a>
          <a class="menu-item" href="Event.php"><span class="mi-icon">★</span> Events</a>
          <a class="menu-item" href="Financial.php"><span class="mi-icon">💹</span> Financials</a>
          <?php if ($_SESSION['user_role'] === 'admin'): ?>
          <a class="menu-item" href="Users.php"><span class="mi-icon">👥</span> Users</a>
          <?php endif; ?>
        </nav>
      </div>
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
      <!-- Topbar -->
      <header class="topbar">
        <div class="top-left"><h1>Invoices</h1><div class="top-sub">Manage and view all invoices</div></div>
        <div class="top-center"><div class="search"><input placeholder="Search anything" /><button class="search-btn">🔎</button></div></div>
        <div class="top-right">
          <button class="icon-btn">🔔</button><button class="icon-btn">⚙️</button>
          <div class="profile">
            <img class="avatar" src="assets/zahid.jpg" alt="user" />
            <div class="profile-name"><div>Md. Zahid Hasan</div><small>Admin</small></div>
          </div>
        </div>
      </header>

      <!-- Page Content -->
      <main class="content">
        <h2>Invoices Overview</h2>

        <!-- Stats -->
        <div class="stats">
          <div class="stat-card">
            <div class="value"><?php echo $paid_count; ?></div>
            <div class="label">Paid</div>
          </div>
          <div class="stat-card">
            <div class="value"><?php echo $unpaid_count; ?></div>
            <div class="label">Unpaid</div>
          </div>
        </div>

        <div class="invoice-grid">
          <!-- Left Invoice List -->
          <div class="invoice-list">
            <h3>Invoice List</h3>
            <input type="text" placeholder="Search invoice" />
            <ul>
              <?php foreach($invoices as $invoice): ?>
              <li class="invoice <?php echo strtolower($invoice['status']); ?> <?php echo $selected_invoice && $selected_invoice['invoice_number'] === $invoice['invoice_number'] ? 'active' : ''; ?>"
                  onclick="window.location.href='Invoices.php?invoice=<?php echo $invoice['invoice_number']; ?>'">
                <?php echo $invoice['invoice_number']; ?> - $<?php echo number_format($invoice['total_amount'], 2); ?>
              </li>
              <?php endforeach; ?>
            </ul>
          </div>

          <!-- Right Invoice Details -->
          <?php if ($selected_invoice): ?>
          <div class="invoice-details">
            <div class="details-header">
              <h3>#<?php echo $selected_invoice['invoice_number']; ?></h3>
              <span class="status <?php echo strtolower($selected_invoice['status']); ?>"><?php echo $selected_invoice['status']; ?></span>
            </div>
            <div class="bill-info">
              <div>
                <h4>Bill From</h4>
                <p>Event Management Co.<br>123 Sunset Ave<br>Los Angeles, CA</p>
              </div>
              <div>
                <h4>Bill To</h4>
                <p><?php echo $selected_invoice['customer_name']; ?><br><?php echo $selected_invoice['customer_email']; ?><br><?php echo $selected_invoice['customer_phone']; ?></p>
              </div>
            </div>
            
            <?php
            // Calculate proper values
            $ticket_price = $selected_invoice['subtotal'] / $selected_invoice['tickets_qty'];
            $calculated_subtotal = $ticket_price * $selected_invoice['tickets_qty'];
            $calculated_total = $calculated_subtotal + $selected_invoice['tax_amount'] + $selected_invoice['fee_amount'];
            ?>
            
            <table class="ticket-table">
              <thead>
                <tr><th>Ticket Category</th><th>Price</th><th>Qty</th><th>Amount</th></tr>
              </thead>
              <tbody>
                <tr>
                  <td><?php echo $selected_invoice['event_name']; ?></td>
                  <td>$<?php echo number_format($ticket_price, 2); ?></td>
                  <td><?php echo $selected_invoice['tickets_qty']; ?></td>
                  <td>$<?php echo number_format($calculated_subtotal, 2); ?></td>
                </tr>
              </tbody>
            </table>
            <div class="totals">
              <div>Subtotal: $<?php echo number_format($calculated_subtotal, 2); ?></div>
              <div>Tax (10%): $<?php echo number_format($selected_invoice['tax_amount'], 2); ?></div>
              <div>Fee: $<?php echo number_format($selected_invoice['fee_amount'], 2); ?></div>
              <strong>Total: $<?php echo number_format($calculated_total, 2); ?></strong>
            </div>
            <div class="actions">
              <button>Edit Invoice</button>
              <button>Send Invoice</button>
              <button>Hold Invoice</button>
            </div>
          </div>
          <?php endif; ?>
        </div>
      </main>

      <footer class="footer">
        <div>© 2025 Eventisa</div>
        <div>Privacy Policy · Terms & Conditions · Contact</div>
      </footer>
    </div>
  </div>
  <script src="js/invoices.js"></script>
</body>
</html>