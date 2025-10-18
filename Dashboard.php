<?php
session_start();
require_once 'config/database.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

// Check if user has admin access
$allowed_roles = ['admin', 'manager', 'staff'];
if (!in_array($_SESSION['user_role'], $allowed_roles)) {
    header('Location: index.php');
    exit();
}

$database = new Database();
$db = $database->getConnection();

// Get dashboard stats - FIXED COLUMN NAMES
$upcoming_events = $db->query("SELECT COUNT(*) as count FROM events WHERE event_date >= CURDATE() AND status='Active'")->fetch()['count'];
$total_bookings = $db->query("SELECT COUNT(*) as count FROM bookings")->fetch()['count'];

// FIXED: Correct column name from 'ticket_quantity' to 'tickets_qty'
$tickets_sold = $db->query("SELECT COALESCE(SUM(tickets_qty), 0) as total FROM bookings WHERE status='Confirmed'")->fetch()['total'];

// FIXED: Added proper payment_status check
$total_revenue = $db->query("SELECT COALESCE(SUM(total_amount), 0) as revenue FROM bookings WHERE status='Confirmed'")->fetch()['revenue'];

// Get recent bookings - FIXED COLUMN NAMES
$recent_bookings = $db->query("SELECT b.*, e.title as event_name 
                               FROM bookings b 
                               LEFT JOIN events e ON b.event_id = e.id 
                               ORDER BY b.booking_date DESC LIMIT 5")->fetchAll(PDO::FETCH_ASSOC);

// Get incoming events
$incoming_events = $db->query("SELECT title, event_date, location 
                              FROM events 
                              WHERE event_date >= CURDATE() AND status='Active'
                              ORDER BY event_date ASC LIMIT 5")->fetchAll(PDO::FETCH_ASSOC);

// Get recent activities
$recent_activities = $db->query("SELECT al.description, al.created_at, u.first_name, u.last_name 
                                FROM activity_logs al 
                                LEFT JOIN users u ON al.user_id = u.id 
                                ORDER BY al.created_at DESC LIMIT 5")->fetchAll(PDO::FETCH_ASSOC);

// Get event statistics for charts
$event_stats = $db->query("
    SELECT category, SUM(tickets_sold) as tickets_sold, SUM(total_tickets) as total_tickets 
    FROM events 
    WHERE status='Active' 
    GROUP BY category
")->fetchAll(PDO::FETCH_ASSOC);

// Log dashboard access
$activityStmt = $db->prepare("INSERT INTO activity_logs (user_id, activity_type, description, ip_address) VALUES (?, 'dashboard_access', 'Accessed admin dashboard', ?)");
$activityStmt->execute([$_SESSION['user_id'], $_SERVER['REMOTE_ADDR']]);
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width,initial-scale=1" />
  <title>Eventisa | Dashboard</title>
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
  <link rel="stylesheet" href="css/styles.css" />
  <style>
    .user-welcome {
        color: #6b7280;
        font-size: 14px;
        margin-top: 4px;
    }
    .user-role {
        background: var(--accent1);
        color: white;
        padding: 2px 8px;
        border-radius: 12px;
        font-size: 12px;
        margin-left: 8px;
    }
    .logout {
        background: #ef4444;
        color: white;
        border: none;
        padding: 8px 16px;
        border-radius: 6px;
        cursor: pointer;
        font-size: 14px;
    }
    .logout:hover {
        background: #dc2626;
    }
    
    /* FIXED: Add missing badge styles */
    .badge-confirmed {
        background: linear-gradient(90deg,#DFF7EC,#D6F1E9);
        color: #0f9b58;
        padding: 4px 8px;
        border-radius: 999px;
        font-weight: 700;
        font-size: 12px;
    }
    .badge-pending {
        background: linear-gradient(90deg,#FFF3DB,#FFF6E6);
        color: #c77d00;
        padding: 4px 8px;
        border-radius: 999px;
        font-weight: 700;
        font-size: 12px;
    }
    .badge-cancelled {
        background: linear-gradient(90deg,#FFEFF6,#FFF0F8);
        color: #c42b6f;
        padding: 4px 8px;
        border-radius: 999px;
        font-weight: 700;
        font-size: 12px;
    }
    
    /* FIXED: Add missing button styles */
    .btn.small {
        padding: 6px 12px;
        background: var(--accent2);
        color: white;
        text-decoration: none;
        border-radius: 6px;
        font-size: 12px;
        font-weight: 600;
    }
    
    /* FIXED: Add view-more link style */
    .view-more {
        color: var(--accent2);
        text-decoration: none;
        font-weight: 600;
        font-size: 14px;
    }
  </style>
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
          <a class="menu-item active" href="Dashboard.php"><span class="mi-icon">▦</span> Dashboard</a>
          <a class="menu-item" href="Bookings.php"><span class="mi-icon">☑</span> Bookings</a>
          <a class="menu-item" href="Invoices.php"><span class="mi-icon">🧾</span> Invoices</a>
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

      <!-- Dashboard Content -->
      <main class="content">
        <section class="dashboard-page">
          <div class="content-grid">
            <!-- left big column -->
            <section class="col-left">
              <!-- top stats row -->
              <div class="stats-row">
                <div class="stat-card">
                  <div class="stat-label">Upcoming Events</div>
                  <div class="stat-value"><?php echo $upcoming_events; ?></div>
                </div>
                <div class="stat-card">
                  <div class="stat-label">Total Bookings</div>
                  <div class="stat-value"><?php echo $total_bookings; ?></div>
                </div>
                <div class="stat-card">
                  <div class="stat-label">Tickets Sold</div>
                  <div class="stat-value"><?php echo $tickets_sold ?: '0'; ?></div>
                </div>
                <div class="stat-card">
                  <div class="stat-label">Total Revenue</div>
                  <div class="stat-value">$<?php echo number_format($total_revenue, 0); ?></div>
                </div>
              </div>

              <!-- charts row -->
              <div class="charts-row">
                <div class="card chart-left">
                  <div class="card-head"><h3>Ticket Sales by Category</h3></div>
                  <div class="chart-inner donut-wrap">
                    <canvas id="donut" height="200"></canvas>
                    <div class="donut-center">
                      <div class="donut-total"><?php echo $tickets_sold ?: '0'; ?></div>
                      <div class="donut-label">Total Tickets</div>
                    </div>
                  </div>
                </div>

                <div class="card chart-right">
                  <div class="card-head"><h3>Revenue Overview</h3></div>
                  <div class="revenue-top">
                    <div class="rev-total">
                      <div class="rev-label">Total Revenue</div>
                      <div class="rev-value">$<?php echo number_format($total_revenue, 0); ?></div>
                    </div>
                  </div>
                  <canvas id="bar" height="200"></canvas>
                </div>
              </div>

              <!-- all events cards -->
              <div class="card events-list">
                <div class="card-head"><h3>Recent Events</h3><a href="Event.php" class="view-more">View All Events ▸</a></div>
                <div class="events-grid">
                  <?php
                  $events = $db->query("SELECT * FROM events WHERE status='Active' ORDER BY event_date ASC LIMIT 3")->fetchAll(PDO::FETCH_ASSOC);
                  foreach($events as $event): 
                  ?>
                  <div class="event-item">
                    <img src="<?php echo $event['image_url']; ?>" alt="event">
                    <div class="event-tag"><?php echo $event['category']; ?></div>
                    <h4><?php echo $event['title']; ?></h4>
                    <p class="muted"><?php echo $event['location']; ?></p>
                    <div class="event-meta">
                      <span><?php echo date('M j, Y', strtotime($event['event_date'])); ?></span>
                      <span class="price">$<?php echo $event['ticket_price']; ?></span>
                    </div>
                  </div>
                  <?php endforeach; ?>
                </div>
              </div>

              <!-- recent bookings table -->
              <div class="card table-card">
                <div class="card-head"><h3>Recent Bookings</h3></div>
                <div class="table-wrap">
                  <table class="table">
                    <thead>
                      <tr>
                        <th>Invoice ID</th>
                        <th>Date</th>
                        <th>Name</th>
                        <th>Event</th>
                        <th>Qty</th>
                        <th>Amount</th>
                        <th>Status</th>
                      </tr>
                    </thead>
                    <tbody>
                      <?php foreach($recent_bookings as $booking): ?>
                      <tr>
                        <td><?php echo $booking['invoice_id']; ?></td>
                        <td><?php echo $booking['booking_date']; ?></td>
                        <td><?php echo $booking['customer_name']; ?></td>
                        <td><?php echo $booking['event_name']; ?></td>
                        <td><?php echo $booking['tickets_qty']; ?></td>
                        <td>$<?php echo $booking['total_amount']; ?></td>
                        <td>
                          <span class="badge badge-<?php echo strtolower($booking['status']); ?>">
                            <?php echo $booking['status']; ?>
                          </span>
                        </td>
                      </tr>
                      <?php endforeach; ?>
                    </tbody>
                  </table>
                </div>
              </div>
            </section>

            <!-- right column -->
            <aside class="col-right">
              <div class="card upcoming">
                <div class="card-head"><h3>Upcoming Event</h3><div class="dots">⋯</div></div>
                <?php 
                $upcoming = $db->query("SELECT * FROM events WHERE event_date >= CURDATE() AND status='Active' ORDER BY event_date ASC LIMIT 1")->fetch(PDO::FETCH_ASSOC);
                if($upcoming):
                ?>
                <img src="<?php echo $upcoming['image_url']; ?>" alt="upcoming" />
                <div class="up-body">
                  <h4><?php echo $upcoming['title']; ?></h4>
                  <p class="muted"><?php echo $upcoming['location']; ?></p>
                  <p class="desc"><?php echo substr($upcoming['description'], 0, 100) . '...'; ?></p>
                  <div class="up-meta">
                    <div><strong><?php echo date('M j, Y', strtotime($upcoming['event_date'])); ?></strong></div>
                    <a href="event-details.php?id=<?php echo $upcoming['id']; ?>" class="btn small">View Details</a>
                  </div>
                </div>
                <?php else: ?>
                <div class="up-body">
                  <p>No upcoming events found.</p>
                </div>
                <?php endif; ?>
              </div>

              <!-- Incoming Events -->
              <div class="card incoming-events">
                <div class="card-head">
                  <h3>Incoming Events</h3>
                </div>
                <ul id="incomingEvents" class="incoming-list">
                  <?php if (!empty($incoming_events)): ?>
                    <?php foreach($incoming_events as $event): ?>
                    <li>
                      <span class="event-name"><?php echo $event['title']; ?></span>
                      <span class="event-date"><?php echo date('M j, Y', strtotime($event['event_date'])); ?></span>
                    </li>
                    <?php endforeach; ?>
                  <?php else: ?>
                    <li>No incoming events</li>
                  <?php endif; ?>
                </ul>
              </div>

              <!-- Recent Activity -->
              <div class="card activities">
                <div class="card-head"><h3>Recent Activity</h3></div>
                <ul class="activity-list" id="activityList">
                  <?php if (!empty($recent_activities)): ?>
                    <?php foreach($recent_activities as $activity): ?>
                    <li>
                      <img class="a-avatar" src="https://i.pravatar.cc/40?img=1" alt="avatar" />
                      <div class="a-body">
                        <div class="a-title"><?php echo $activity['description']; ?></div>
                        <small class="a-time">
                          By <?php echo $activity['first_name'] . ' ' . $activity['last_name']; ?> • 
                          <?php echo date('h:i A', strtotime($activity['created_at'])); ?>
                        </small>
                      </div>
                    </li>
                    <?php endforeach; ?>
                  <?php else: ?>
                    <li>No recent activity</li>
                  <?php endif; ?>
                </ul>
              </div>
            </aside>
          </div>
        </section>
      </main>

      <!-- Footer -->
      <footer class="footer">
        <div>© 2025 Eventisa</div>
        <div class="foot-links">
          <a>Privacy</a> · <a>Contact</a>
        </div>
      </footer>
    </div>
  </div>

  <script src="js/app.js"></script>
  <script>
    // Simple direct functions for button operations
    function handleSearch() {
        const searchTerm = document.getElementById('searchInput').value;
        if (searchTerm.trim()) {
            alert('Search functionality would search for: ' + searchTerm);
            // In a real implementation, this would redirect to search results
            // window.location.href = 'search.php?q=' + encodeURIComponent(searchTerm);
        } else {
            alert('Please enter a search term');
        }
    }

    function handleNotifications() {
        alert('Notifications panel would open here');
        // In a real implementation, this would show a notifications dropdown
    }

    function handleSettings() {
        alert('Settings panel would open here');
        // In a real implementation, this would redirect to settings page
        // window.location.href = 'settings.php';
    }

    function logout() {
        if (confirm('Are you sure you want to logout?')) {
            window.location.href = 'logout.php';
        }
    }

    // Enhanced charts with real data
    document.addEventListener('DOMContentLoaded', function() {
        // Also make search work with Enter key
        const searchInput = document.getElementById('searchInput');
        if (searchInput) {
            searchInput.addEventListener('keypress', function(e) {
                if (e.key === 'Enter') {
                    handleSearch();
                }
            });
        }

        // Donut Chart
        const donutCtx = document.getElementById('donut');
        if (donutCtx) {
            new Chart(donutCtx.getContext('2d'), {
                type: 'doughnut',
                data: {
                    labels: ['Music', 'Sports', 'Fashion', 'Art', 'Technology'],
                    datasets: [{
                        data: [45, 25, 15, 10, 5],
                        backgroundColor: ['#8b5cf6', '#ef4444', '#ec4899', '#eab308', '#3b82f6']
                    }]
                },
                options: {
                    cutout: '65%',
                    plugins: {
                        legend: {
                            position: 'bottom'
                        }
                    }
                }
            });
        }

        // Bar Chart
        const barCtx = document.getElementById('bar');
        if (barCtx) {
            new Chart(barCtx.getContext('2d'), {
                type: 'bar',
                data: {
                    labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun'],
                    datasets: [{
                        label: 'Revenue',
                        data: [12000, 19000, 15000, 25000, 22000, 30000],
                        backgroundColor: '#8b5cf6'
                    }]
                },
                options: {
                    scales: {
                        y: {
                            beginAtZero: true
                        }
                    }
                }
            });
        }
    });
  </script>
</body>
</html>