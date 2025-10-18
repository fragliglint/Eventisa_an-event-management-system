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

// Get filter parameters
$category = $_GET['category'] ?? 'all';
$status_filter = $_GET['status'] ?? 'active';
$search = $_GET['search'] ?? '';

// Build query
$query = "SELECT * FROM events WHERE 1=1";
$params = [];

if ($status_filter !== 'all') {
    $query .= " AND status = ?";
    $params[] = ucfirst($status_filter);
}

if ($category !== 'all') {
    $query .= " AND category = ?";
    $params[] = $category;
}

if (!empty($search)) {
    $query .= " AND (title LIKE ? OR location LIKE ? OR description LIKE ?)";
    $search_term = "%$search%";
    $params[] = $search_term;
    $params[] = $search_term;
    $params[] = $search_term;
}

$query .= " ORDER BY event_date ASC";

$stmt = $db->prepare($query);
$stmt->execute($params);
$events = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Get counts for tabs
$active_count = $db->query("SELECT COUNT(*) FROM events WHERE status='Active'")->fetchColumn();
$draft_count = $db->query("SELECT COUNT(*) FROM events WHERE status='Draft'")->fetchColumn();
$past_count = $db->query("SELECT COUNT(*) FROM events WHERE status='Past'")->fetchColumn();
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width,initial-scale=1" />
  <title>Eventisa | Events</title>
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
  <link rel="stylesheet" href="css/styles.css" />
  <link rel="stylesheet" href="css/events.css" />
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
          <a class="menu-item active" href="Event.php"><span class="mi-icon">★</span> Events</a>
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
          <h1>Events</h1>
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

      <!-- Success/Error Messages -->
      <?php if (isset($_SESSION['success_message'])): ?>
        <div class="alert alert-success">
            <?php echo $_SESSION['success_message']; unset($_SESSION['success_message']); ?>
        </div>
      <?php endif; ?>

      <?php if (isset($_SESSION['error_message'])): ?>
        <div class="alert alert-error">
            <?php echo $_SESSION['error_message']; unset($_SESSION['error_message']); ?>
        </div>
      <?php endif; ?>

      <!-- Content -->
      <main class="content">
        <!-- Tabs -->
        <div class="tabs">
          <a href="Event.php?status=active" class="tab <?php echo $status_filter === 'active' ? 'active' : ''; ?>" data-tab="active">Active (<?php echo $active_count; ?>)</a>
          <a href="Event.php?status=draft" class="tab <?php echo $status_filter === 'draft' ? 'active' : ''; ?>" data-tab="draft">Draft (<?php echo $draft_count; ?>)</a>
          <a href="Event.php?status=past" class="tab <?php echo $status_filter === 'past' ? 'active' : ''; ?>" data-tab="past">Past (<?php echo $past_count; ?>)</a>
        </div>

        <!-- Filters and Add Event Button -->
        <div class="filters-add-section">
          <form method="GET" class="filters" id="filterForm">
            <input type="text" name="search" id="search" placeholder="Search event, location, etc" value="<?php echo htmlspecialchars($search); ?>">
            <select name="category" id="categoryFilter">
              <option value="all" <?php echo $category === 'all' ? 'selected' : ''; ?>>All Category</option>
              <option value="Music" <?php echo $category === 'Music' ? 'selected' : ''; ?>>Music</option>
              <option value="Fashion" <?php echo $category === 'Fashion' ? 'selected' : ''; ?>>Fashion</option>
              <option value="Technology" <?php echo $category === 'Technology' ? 'selected' : ''; ?>>Technology</option>
              <option value="Food & Culinary" <?php echo $category === 'Food & Culinary' ? 'selected' : ''; ?>>Food</option>
              <option value="Art & Design" <?php echo $category === 'Art & Design' ? 'selected' : ''; ?>>Art</option>
              <option value="Health & Wellness" <?php echo $category === 'Health & Wellness' ? 'selected' : ''; ?>>Health</option>
              <option value="Outdoor & Adventure" <?php echo $category === 'Outdoor & Adventure' ? 'selected' : ''; ?>>Outdoor</option>
            </select>
            <input type="hidden" name="status" id="statusFilter" value="<?php echo $status_filter; ?>">
            <button type="submit" class="filter-btn">Apply Filters</button>
            <div class="view-toggle">
              <button type="button" id="gridView" class="active">⬜</button>
              <button type="button" id="listView">📃</button>
            </div>
          </form>

          <!-- Add Event Button -->
          <div class="add-event-section">
            <button class="add-event-btn" onclick="openAddEventModal()">+ Add Event</button>
          </div>
        </div>

        <!-- Add Event Modal -->
        <div id="addEventModal" class="modal">
          <div class="modal-content">
            <div class="modal-header">
              <h2>Add New Event</h2>
              <span class="close" onclick="closeAddEventModal()">&times;</span>
            </div>
            <form id="addEventForm" method="POST" action="add_event.php" enctype="multipart/form-data">
              <div class="form-row">
                <div class="form-group">
                  <label for="eventTitle">Event Title *</label>
                  <input type="text" id="eventTitle" name="title" required>
                  <div class="form-error" id="titleError"></div>
                </div>
                <div class="form-group">
                  <label for="eventCategory">Category *</label>
                  <select id="eventCategory" name="category" required>
                    <option value="">Select Category</option>
                    <option value="Music">Music</option>
                    <option value="Fashion">Fashion</option>
                    <option value="Technology">Technology</option>
                    <option value="Food & Culinary">Food & Culinary</option>
                    <option value="Art & Design">Art & Design</option>
                    <option value="Health & Wellness">Health & Wellness</option>
                    <option value="Outdoor & Adventure">Outdoor & Adventure</option>
                  </select>
                  <div class="form-error" id="categoryError"></div>
                </div>
              </div>
              
              <div class="form-row">
                <div class="form-group">
                  <label for="eventDate">Event Date *</label>
                  <input type="date" id="eventDate" name="event_date" required>
                  <div class="form-error" id="dateError"></div>
                </div>
                <div class="form-group">
                  <label for="eventTime">Event Time *</label>
                  <input type="time" id="eventTime" name="event_time" required>
                  <div class="form-error" id="timeError"></div>
                </div>
              </div>
              
              <div class="form-group">
                <label for="eventLocation">Location *</label>
                <input type="text" id="eventLocation" name="location" required>
                <div class="form-error" id="locationError"></div>
              </div>
              
              <div class="form-group">
                <label for="eventDescription">Description</label>
                <textarea id="eventDescription" name="description" rows="3"></textarea>
              </div>
              
              <div class="form-row">
                <div class="form-group">
                  <label for="ticketPrice">Ticket Price ($) *</label>
                  <input type="number" id="ticketPrice" name="ticket_price" min="0" step="0.01" required>
                  <div class="form-error" id="priceError"></div>
                </div>
                <div class="form-group">
                  <label for="totalTickets">Total Tickets *</label>
                  <input type="number" id="totalTickets" name="total_tickets" min="1" required>
                  <div class="form-error" id="ticketsError"></div>
                </div>
              </div>
              
              <div class="form-group">
                <label for="eventImage">Event Image</label>
                <input type="file" id="eventImage" name="event_image" accept="image/*">
                <div class="file-info" id="fileInfo"></div>
              </div>
              
              <div class="form-group">
                <label for="eventStatus">Status *</label>
                <select id="eventStatus" name="status" required>
                  <option value="Active">Active</option>
                  <option value="Draft">Draft</option>
                </select>
              </div>
              
              <div class="form-actions">
                <button type="button" class="cancel-btn" onclick="closeAddEventModal()">Cancel</button>
                <button type="submit" class="submit-btn">Create Event</button>
              </div>
            </form>
          </div>
        </div>

        <!-- Events Grid -->
        <section id="eventsContainer" class="grid-view">
          <?php if (empty($events)): ?>
            <div class="no-events">
              <div class="no-events-icon">📅</div>
              <h3>No events found</h3>
              <p>Try adjusting your search criteria or create a new event</p>
              <button class="add-event-btn" onclick="openAddEventModal()">+ Add Event</button>
            </div>
          <?php else: ?>
            <?php foreach($events as $event): 
              $sold_percentage = $event['total_tickets'] > 0 ? ($event['tickets_sold'] / $event['total_tickets']) * 100 : 0;
              $status_class = strtolower($event['status']);
            ?>
            <div class="event-card" data-id="<?php echo $event['id']; ?>">
              <div class="event-image">
                <img src="<?php echo $event['image_url']; ?>" alt="<?php echo htmlspecialchars($event['title']); ?>">
                <div class="event-overlay">
                  <button class="event-action-btn" onclick="editEvent(<?php echo $event['id']; ?>)">Edit</button>
                  <button class="event-action-btn" onclick="viewEvent(<?php echo $event['id']; ?>)">View</button>
                </div>
              </div>
              <div class="event-tag"><?php echo $event['category']; ?></div>
              <span class="event-status <?php echo $status_class; ?>"><?php echo $event['status']; ?></span>
              <div class="event-info">
                <p class="event-date"><?php echo date('M j, Y — g:i A', strtotime($event['event_date'] . ' ' . $event['event_time'])); ?></p>
                <h3><?php echo htmlspecialchars($event['title']); ?></h3>
                <p class="muted"><?php echo htmlspecialchars($event['location']); ?></p>
                <div class="progress-section">
                  <div class="progress-bar">
                    <span style="width:<?php echo $sold_percentage; ?>%"></span>
                  </div>
                  <div class="progress-text"><?php echo $event['tickets_sold']; ?> / <?php echo $event['total_tickets']; ?> sold</div>
                </div>
                <div class="event-meta">
                  <span class="price">$<?php echo $event['ticket_price']; ?></span>
                  <div class="event-actions">
                    <button class="icon-action" title="Share">
                      <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M4 12v8a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2v-8"></path>
                        <polyline points="16 6 12 2 8 6"></polyline>
                        <line x1="12" y1="2" x2="12" y2="15"></line>
                      </svg>
                    </button>
                    <button class="icon-action" title="More options">
                      <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <circle cx="12" cy="12" r="1"></circle>
                        <circle cx="12" cy="5" r="1"></circle>
                        <circle cx="12" cy="19" r="1"></circle>
                      </svg>
                    </button>
                  </div>
                </div>
              </div>
            </div>
            <?php endforeach; ?>
          <?php endif; ?>
        </section>

        <!-- Pagination -->
        <div class="pagination">
          <button class="pagination-btn">«</button>
          <button class="pagination-btn active">1</button>
          <button class="pagination-btn">2</button>
          <button class="pagination-btn">3</button>
          <button class="pagination-btn">»</button>
        </div>
      </main>
    </div>
  </div>

  <script>
    // Simple direct functions for button operations
    function handleSearch() {
        const searchTerm = document.getElementById('searchInput').value;
        if (searchTerm.trim()) {
            alert('Search functionality would search for: ' + searchTerm);
            // In a real implementation, this would filter events
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

    function logout() {
        if (confirm('Are you sure you want to logout?')) {
            window.location.href = 'logout.php';
        }
    }

    // Event functions
    function editEvent(id) {
      alert('Edit event with ID: ' + id);
      // In a real implementation, this would open an edit modal
    }

    function viewEvent(id) {
      alert('View event with ID: ' + id);
      // In a real implementation, this would navigate to event details
    }

    // Modal Functions
    function openAddEventModal() {
      document.getElementById('addEventModal').style.display = 'block';
      document.body.style.overflow = 'hidden';
    }

    function closeAddEventModal() {
      document.getElementById('addEventModal').style.display = 'none';
      document.body.style.overflow = 'auto';
      document.getElementById('addEventForm').reset();
      clearFormErrors();
    }

    function clearFormErrors() {
      const errorElements = document.querySelectorAll('.form-error');
      errorElements.forEach(el => el.textContent = '');
    }

    // Close modal when clicking outside
    window.onclick = function(event) {
      const modal = document.getElementById('addEventModal');
      if (event.target === modal) {
        closeAddEventModal();
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

        // Set minimum date to today for event date
        const today = new Date().toISOString().split('T')[0];
        document.getElementById('eventDate').min = today;

        // File input change handler
        const fileInput = document.getElementById('eventImage');
        const fileInfo = document.getElementById('fileInfo');
        
        fileInput.addEventListener('change', function() {
          if (this.files && this.files[0]) {
            const file = this.files[0];
            const fileSize = (file.size / 1024 / 1024).toFixed(2); // MB
            fileInfo.textContent = `Selected: ${file.name} (${fileSize} MB)`;
            
            // Validate file type
            const allowedTypes = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif'];
            if (!allowedTypes.includes(file.type)) {
              fileInfo.textContent = 'Please select a valid image file (JPEG, PNG, GIF)';
              this.value = '';
            }
          } else {
            fileInfo.textContent = '';
          }
        });

        // Form submission handling
        document.getElementById('addEventForm').addEventListener('submit', function(e) {
          e.preventDefault();
          clearFormErrors();
          
          // Basic validation
          const formData = new FormData(this);
          let isValid = true;
          
          // Title validation
          if (!formData.get('title')?.trim()) {
            document.getElementById('titleError').textContent = 'Event title is required';
            isValid = false;
          }
          
          // Category validation
          if (!formData.get('category')) {
            document.getElementById('categoryError').textContent = 'Category is required';
            isValid = false;
          }
          
          // Date validation
          if (!formData.get('event_date')) {
            document.getElementById('dateError').textContent = 'Event date is required';
            isValid = false;
          }
          
          // Time validation
          if (!formData.get('event_time')) {
            document.getElementById('timeError').textContent = 'Event time is required';
            isValid = false;
          }
          
          // Location validation
          if (!formData.get('location')?.trim()) {
            document.getElementById('locationError').textContent = 'Location is required';
            isValid = false;
          }
          
          // Price validation
          const ticketPrice = parseFloat(formData.get('ticket_price'));
          if (isNaN(ticketPrice) || ticketPrice < 0) {
            document.getElementById('priceError').textContent = 'Valid ticket price is required';
            isValid = false;
          }
          
          // Tickets validation
          const totalTickets = parseInt(formData.get('total_tickets'));
          if (isNaN(totalTickets) || totalTickets < 1) {
            document.getElementById('ticketsError').textContent = 'Valid total tickets number is required';
            isValid = false;
          }
          
          if (isValid) {
            // Submit the form
            this.submit();
          }
        });
    });
  </script>
  <script src="js/events.js"></script>
</body>
</html>