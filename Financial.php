<?php
session_start();
require_once 'config/database.php';

$database = new Database();
$db = $database->getConnection();

// Get financial stats
$balance = $db->query("SELECT 
                      (SELECT COALESCE(SUM(amount), 0) FROM financial_transactions WHERE transaction_type='income' AND status='Completed') -
                      (SELECT COALESCE(SUM(amount), 0) FROM financial_transactions WHERE transaction_type='expense' AND status='Completed') as balance")->fetch()['balance'];

$income = $db->query("SELECT COALESCE(SUM(amount), 0) as total FROM financial_transactions WHERE transaction_type='income' AND status='Completed'")->fetch()['total'];
$expenses = $db->query("SELECT COALESCE(SUM(amount), 0) as total FROM financial_transactions WHERE transaction_type='expense' AND status='Completed'")->fetch()['total'];

// Get recent transactions
$transactions = $db->query("SELECT * FROM financial_transactions ORDER BY transaction_date DESC LIMIT 10")->fetchAll(PDO::FETCH_ASSOC);
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width,initial-scale=1" />
  <title>Eventisa | Dashboard</title>
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
  <link rel="stylesheet" href="css/styles.css" />
  <link rel="stylesheet" href="css/financial.css" />
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
          <a class="menu-item" href="Invoices.php"><span class="mi-icon">🧾</span> Invoices</a>
          <a class="menu-item" href="Calendar.php"><span class="mi-icon">🗓️</span> Calendar</a>
          <a class="menu-item" href="Event.php"><span class="mi-icon">★</span> Events</a>
          <a class="menu-item active" href="Financial.php"><span class="mi-icon">💹</span> Financials</a>
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
    <div class="main">
      <!-- Topbar -->
      <header class="topbar">
        <div class="top-left">
          <h1>Dashboard</h1>
          <div class="top-sub">Hello <?php echo $_SESSION['user_name']; ?>, welcome back!</div>
        </div>
        <div class="top-center">
          <div class="search">
            <input placeholder="Search anything" id="searchInput" />
            <button class="search-btn" onclick="handleSearch()">🔎</button>
          </div>
        </div>
        <div class="top-right">
          <button class="icon-btn" onclick="handleNotifications()">🔔</button>
          <button class="icon-btn" onclick="handleSettings()">⚙️</button>
          <div class="profile">
            <img class="avatar" src="assets/zahid.jpg" alt="user" />
            <div class="profile-name">
              <div><?php echo $_SESSION['user_name']; ?></div>
              <small><?php echo ucfirst($_SESSION['user_role']); ?></small>
            </div>
          </div>
        </div>


</header>

      <!-- Content -->
      <main class="content">
        <!-- Top Stats -->
        <section class="stats">
          <div class="stat-card">
            <h3>$<?php echo number_format($balance, 2); ?></h3>
            <p>Balance</p>
            <span class="trend up">+3.65%</span>
          </div>
          <div class="stat-card">
            <h3>$<?php echo number_format($income, 2); ?></h3>
            <p>Income</p>
            <span class="trend up">+2.08%</span>
          </div>
          <div class="stat-card">
            <h3>$<?php echo number_format($expenses, 2); ?></h3>
            <p>Expenses</p>
            <span class="trend down">-0.84%</span>
          </div>
        </section>

        <!-- Charts -->
        <section class="charts">
          <!-- Cashflow -->
          <div class="chart-box large">
            <h3>Cashflow</h3>
            <canvas id="cashflowChart"></canvas>
          </div>

          <!-- Sales Revenue -->
          <div class="chart-box">
            <h3>Sales Revenue</h3>
            <canvas id="revenueChart"></canvas>
            <div class="rev-total">Total Revenue $<?php echo number_format($income, 2); ?></div>
          </div>

          <!-- Expense Breakdown -->
          <div class="chart-box">
            <h3>Expense Breakdown</h3>
            <canvas id="expenseChart"></canvas>
            <div class="exp-total">Total Expenses $<?php echo number_format($expenses, 2); ?></div>
          </div>
        </section>

        <!-- Transactions Table -->
        <section class="transactions">
          <h3>Recent Transactions</h3>
          <table>
            <thead>
              <tr>
                <th>Date</th>
                <th>Description</th>
                <th>Amount</th>
                <th>Category</th>
                <th>Status</th>
              </tr>
            </thead>
            <tbody>
              <?php foreach($transactions as $transaction): ?>
              <tr>
                <td><?php echo $transaction['transaction_date']; ?></td>
                <td><?php echo $transaction['description']; ?></td>
                <td class="<?php echo $transaction['transaction_type'] === 'income' ? 'positive' : 'negative'; ?>">
                  <?php echo $transaction['transaction_type'] === 'income' ? '+' : '-'; ?> $<?php echo number_format($transaction['amount'], 2); ?>
                </td>
                <td><?php echo $transaction['category']; ?></td>
                <td class="status <?php echo strtolower($transaction['status']); ?>"><?php echo $transaction['status']; ?></td>
              </tr>
              <?php endforeach; ?>
            </tbody>
          </table>
        </section>
      </main>
    </div>
  </div>

  <!-- Financial JS -->
  <script src="js/financial.js"></script>
</body>
</html>