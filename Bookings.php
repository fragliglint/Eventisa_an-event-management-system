<?php
session_start();
require_once 'config/database.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

$database = new Database();
$db = $database->getConnection();

// Get event filter from URL
$event_filter = $_GET['event'] ?? 'all';

// Build query based on event filter
if ($event_filter === 'all') {
    $bookings_query = "SELECT b.*, e.title as event_name, e.category as event_category 
                      FROM bookings b 
                      LEFT JOIN events e ON b.event_id = e.id 
                      ORDER BY b.booking_date DESC";
    $bookings_stmt = $db->query($bookings_query);
} else {
    $bookings_query = "SELECT b.*, e.title as event_name, e.category as event_category 
                      FROM bookings b 
                      LEFT JOIN events e ON b.event_id = e.id 
                      WHERE e.id = ? 
                      ORDER BY b.booking_date DESC";
    $bookings_stmt = $db->prepare($bookings_query);
    $bookings_stmt->execute([$event_filter]);
}

$bookings = $bookings_stmt->fetchAll(PDO::FETCH_ASSOC);

// Get stats based on event filter
if ($event_filter === 'all') {
    $total_bookings = $db->query("SELECT COUNT(*) as count FROM bookings")->fetch()['count'];
    $total_tickets = $db->query("SELECT SUM(tickets_qty) as total FROM bookings WHERE status='Confirmed'")->fetch()['total'];
    $total_earnings = $db->query("SELECT SUM(total_amount) as total FROM bookings WHERE status='Confirmed'")->fetch()['total'];
} else {
    $stmt1 = $db->prepare("SELECT COUNT(*) as count FROM bookings b 
                           JOIN events e ON b.event_id = e.id 
                           WHERE e.id = ?");
    $stmt1->execute([$event_filter]);
    $total_bookings = $stmt1->fetch()['count'];

    $stmt2 = $db->prepare("SELECT SUM(tickets_qty) as total FROM bookings b 
                           JOIN events e ON b.event_id = e.id 
                           WHERE e.id = ? AND b.status='Confirmed'");
    $stmt2->execute([$event_filter]);
    $total_tickets = $stmt2->fetch()['total'];

    $stmt3 = $db->prepare("SELECT SUM(total_amount) as total FROM bookings b 
                           JOIN events e ON b.event_id = e.id 
                           WHERE e.id = ? AND b.status='Confirmed'");
    $stmt3->execute([$event_filter]);
    $total_earnings = $stmt3->fetch()['total'];
}

// Get events with ticket sales data
$events = $db->query("SELECT e.id, e.title, e.category, e.total_tickets, 
                             COALESCE(SUM(CASE WHEN b.status = 'Confirmed' THEN b.tickets_qty ELSE 0 END), 0) as tickets_sold,
                             COALESCE(SUM(CASE WHEN b.status = 'Confirmed' THEN b.total_amount ELSE 0 END), 0) as total_earnings
                      FROM events e 
                      LEFT JOIN bookings b ON e.id = b.event_id 
                      GROUP BY e.id 
                      ORDER BY tickets_sold DESC")->fetchAll(PDO::FETCH_ASSOC);

$total_all_bookings = $db->query("SELECT COUNT(*) as count FROM bookings")->fetch()['count'];

// Get current event name for display
$current_event_name = 'All Events';
if ($event_filter !== 'all') {
    $event_stmt = $db->prepare("SELECT title FROM events WHERE id = ?");
    $event_stmt->execute([$event_filter]);
    $event_data = $event_stmt->fetch();
    $current_event_name = $event_data ? $event_data['title'] : 'Unknown Event';
}
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width,initial-scale=1" />
  <title>Eventisa | Bookings</title>
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
  <link rel="stylesheet" href="css/styles.css" />
  <link rel="stylesheet" href="css/bookings.css" />
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
          <a class="menu-item active" href="Bookings.php"><span class="mi-icon">☑</span> Bookings</a>
          <a class="menu-item" href="Invoices.php"><span class="mi-icon">🧾</span> Invoices</a>
          <a class="menu-item" href="Calendar.php"><span class="mi-icon">🗓️</span> Calendar</a>
          <a class="menu-item" href="Events.php"><span class="mi-icon">★</span> Events</a>
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
        <div class="top-left">
          <h1>Bookings <?php echo $event_filter !== 'all' ? ' - ' . htmlspecialchars($current_event_name) : ''; ?></h1>
          <div class="top-sub">Hello <?php echo $_SESSION['user_name']; ?>, welcome back!</div>
        </div>
        <div class="top-center">
          <div class="search">
            <input placeholder="Search bookings..." id="searchInput" />
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
        <section class="dashboard-page">
          <div class="content-grid bookings-layout">

            <!-- Left column - Events -->
            <div class="col-left">
              <!-- Stats row -->
              <div class="stats-row">
                <div class="stat-card pink">
                  <div class="stat-label">Total Bookings</div>
                  <div class="stat-value"><?php echo $total_bookings; ?></div>
                </div>
                <div class="stat-card violet">
                  <div class="stat-label">Total Tickets Sold</div>
                  <div class="stat-value"><?php echo $total_tickets ?: '0'; ?></div>
                </div>
                <div class="stat-card purple">
                  <div class="stat-label">Total Earnings</div>
                  <div class="stat-value">$<?php echo number_format($total_earnings ?: '0', 2); ?></div>
                </div>
              </div>

              <!-- Events Card -->
              <div class="card events-card">
                <div class="card-head">
                  <h3>Events Performance</h3>
                  <a href="Bookings.php?event=all" class="small-select <?php echo $event_filter === 'all' ? 'active' : ''; ?>">View All</a>
                </div>
                <div class="events-list">
                  <?php foreach($events as $event): 
                    $is_active = $event_filter == $event['id'];
                    $sold_percentage = $event['total_tickets'] > 0 ? round(($event['tickets_sold'] / $event['total_tickets']) * 100) : 0;
                    $available_tickets = $event['total_tickets'] - $event['tickets_sold'];
                  ?>
                  <a href="Bookings.php?event=<?php echo $event['id']; ?>" 
                     class="event-item <?php echo $is_active ? 'active' : ''; ?>">
                    <div class="event-info">
                      <div class="event-icon">
                        <?php 
                        $icons = [
                          'Music' => '🎵',
                          'Sport' => '⚽', 
                          'Fashion' => '👗',
                          'Art & Design' => '🎨',
                          'Food & Culinary' => '🍕',
                          'Technology' => '💻',
                          'Health & Wellness' => '💊',
                          'Outdoor & Adventure' => '🏕️',
                          'Business' => '💼',
                          'Education' => '📚',
                          'Entertainment' => '🎭',
                          'Charity' => '🤝'
                        ];
                        echo $icons[$event['category']] ?? '📅';
                        ?>
                      </div>
                      <div class="event-details">
                        <span class="event-name"><?php echo htmlspecialchars($event['title']); ?></span>
                        <span class="event-category"><?php echo htmlspecialchars($event['category']); ?></span>
                      </div>
                    </div>
                    <div class="event-stats">
                      <div class="ticket-sales">
                        <div class="sales-figures">
                          <span class="sold"><?php echo $event['tickets_sold']; ?></span>
                          <span class="separator">/</span>
                          <span class="total"><?php echo $event['total_tickets']; ?></span>
                          <span class="tickets-label">tickets sold</span>
                        </div>
                        <div class="sales-amount">$<?php echo number_format($event['total_earnings'], 2); ?></div>
                      </div>
                      <div class="sales-progress">
                        <div class="progress-bar">
                          <div class="progress-fill" data-width="<?php echo $sold_percentage; ?>%" style="width:0"></div>
                        </div>
                        <div class="progress-percentage"><?php echo $sold_percentage; ?>%</div>
                      </div>
                    </div>
                  </a>
                  <?php endforeach; ?>
                </div>
              </div>
            </div>

            <!-- Right column - Donut Chart -->
            <div class="col-right">
              <div class="card">
                <div class="card-head">
                  <h3>Bookings Distribution</h3>
                  <div class="small-select">All Events ▾</div>
                </div>
                <div class="donut-wrap">
                  <canvas id="donutBookings" height="200"></canvas>
                  <div class="donut-center">
                    <div class="donut-total"><?php echo $total_bookings; ?></div>
                    <div class="donut-label">Total Bookings</div>
                  </div>
                </div>
              </div>
            </div>

            <!-- Full-width table -->
            <div class="col-full">
              <div class="card table-card">
                <div class="table-header">
                  <div class="tabs">
                    <button class="tab-btn active" data-filter="all">All</button>
                    <button class="tab-btn" data-filter="confirmed">Confirmed</button>
                    <button class="tab-btn" data-filter="pending">Pending</button>
                    <button class="tab-btn" data-filter="cancelled">Cancelled</button>
                  </div>
                  <div class="table-actions">
                    <button class="export-btn" onclick="exportBookings()">📥 Export</button>
                  </div>
                </div>
                <div class="table-wrap">
                  <table class="table">
                    <thead>
                      <tr>
                        <th>Invoice ID</th>
                        <th>Date</th>
                        <th>Customer</th>
                        <th>Event</th>
                        <th>Category</th>
                        <th>Email</th>
                        <th>Phone</th>
                        <th>Qty</th>
                        <th>Amount</th>
                        <th>Status</th>
                      </tr>
                    </thead>
                    <tbody id="bookingPageRows">
                      <?php foreach($bookings as $booking): ?>
                      <tr>
                        <td><strong><?php echo $booking['invoice_id']; ?></strong></td>
                        <td><?php echo date('M j, Y', strtotime($booking['booking_date'])); ?></td>
                        <td><?php echo htmlspecialchars($booking['customer_name']); ?></td>
                        <td><?php echo htmlspecialchars($booking['event_name']); ?></td>
                        <td>
                          <span class="category-badge">
                            <?php 
                            $cat_icons = [
                              'Music' => '🎵',
                              'Sport' => '⚽',
                              'Fashion' => '👗',
                              'Art & Design' => '🎨',
                              'Food & Culinary' => '🍕',
                              'Technology' => '💻',
                              'Health & Wellness' => '💊',
                              'Outdoor & Adventure' => '🏕️'
                            ];
                            echo ($cat_icons[$booking['event_category']] ?? '📅') . ' ' . $booking['event_category'];
                            ?>
                          </span>
                        </td>
                        <td><?php echo $booking['customer_email']; ?></td>
                        <td><?php echo $booking['customer_phone']; ?></td>
                        <td><span class="quantity"><?php echo $booking['tickets_qty']; ?></span></td>
                        <td><strong>$<?php echo number_format($booking['total_amount'], 2); ?></strong></td>
                        <td><span class="badge <?php echo strtolower($booking['status']); ?>"><?php echo $booking['status']; ?></span></td>
                      </tr>
                      <?php endforeach; ?>
                      <?php if (empty($bookings)): ?>
                      <tr>
                        <td colspan="10" class="no-data">
                          <div class="no-data-content">
                            <div class="no-data-icon">📭</div>
                            <h3>No bookings found</h3>
                            <p>No bookings available for the selected event.</p>
                          </div>
                        </td>
                      </tr>
                      <?php endif; ?>
                    </tbody>
                  </table>
                </div>
              </div>
            </div>

          </div>
        </section>
      </main>

      <footer class="footer">
        <div>© 2025 Eventisa</div>
        <div class="foot-links"><a href="privacy.php">Privacy</a> · <a href="contact.php">Contact</a></div>
      </footer>
    </div>
  </div>

  <script src="js/bookings.js"></script>
  <script>
    // Simple direct functions for button operations
    function handleSearch() {
        const searchTerm = document.getElementById('searchInput').value;
        if (searchTerm.trim()) {
            alert('Search functionality would search for: ' + searchTerm);
            // In a real implementation, this would filter the bookings table
        } else {
            alert('Please enter a search term');
        }
    }

    function handleNotifications() {
        alert('Notifications panel would open here');
    }

    function handleSettings() {
        alert('Settings panel would open here');
    }

    function exportBookings() {
        alert('Export functionality would download bookings data');
    }

    function logout() {
        if (confirm('Are you sure you want to logout?')) {
            window.location.href = 'logout.php';
        }
    }

    // Make search work with Enter key
    document.addEventListener('DOMContentLoaded', function() {
        const searchInput = document.getElementById('searchInput');
        if (searchInput) {
            searchInput.addEventListener('keypress', function(e) {
                if (e.key === 'Enter') {
                    handleSearch();
                }
            });
        }

        // Add active state to current event
        const currentEvent = '<?php echo $event_filter; ?>';
        if (currentEvent !== 'all') {
            document.querySelectorAll('.event-item').forEach(item => {
                if (item.href.includes('event=' + currentEvent)) {
                    item.classList.add('active');
                }
            });
        }

        // Animate progress bars
        document.querySelectorAll('.progress-fill').forEach(function(bar) {
            const width = bar.getAttribute('data-width');
            setTimeout(() => { bar.style.width = width; }, 200);
        });
    });
  </script>
</body>
</html>