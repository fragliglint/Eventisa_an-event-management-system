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

// Get calendar events
$calendar_events = $db->query("SELECT * FROM calendar_events ORDER BY event_date, event_time")->fetchAll(PDO::FETCH_ASSOC);

// Get stats
$all_schedules = $db->query("SELECT COUNT(*) FROM calendar_events")->fetchColumn();
$event_count = $db->query("SELECT COUNT(*) FROM calendar_events WHERE event_type='event'")->fetchColumn();
$meeting_count = $db->query("SELECT COUNT(*) FROM calendar_events WHERE event_type='meeting'")->fetchColumn();
$setup_count = $db->query("SELECT COUNT(*) FROM calendar_events WHERE event_type='setup'")->fetchColumn();
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width,initial-scale=1" />
  <title>Eventisa | Calendar</title>
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
  <link rel="stylesheet" href="css/styles.css" />
  <link rel="stylesheet" href="css/calendar.css" />
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
          <a class="menu-item active" href="Calendar.php"><span class="mi-icon">🗓️</span> Calendar</a>
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
          <h1>Calendar</h1>
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
      <main class="content content-grid">
        <!-- Left column -->
        <section class="col-left">
          <!-- Stats -->
          <div class="stats-row">
            <div class="stat-card">
              <div class="stat-label">All Schedules</div>
              <div class="stat-value"><?php echo $all_schedules; ?> <small>Agenda</small></div>
            </div>
            <div class="stat-card">
              <div class="stat-label">Event</div>
              <div class="stat-value"><?php echo $event_count; ?> <small>Agenda</small></div>
            </div>
            <div class="stat-card">
              <div class="stat-label">Meeting</div>
              <div class="stat-value"><?php echo $meeting_count; ?> <small>Agenda</small></div>
            </div>
            <div class="stat-card">
              <div class="stat-label">Setup & Rehearsal</div>
              <div class="stat-value"><?php echo $setup_count; ?> <small>Agenda</small></div>
            </div>
          </div>

          <!-- Calendar Card -->
          <div class="card calendar">
            <div class="calendar-header">
              <h3 id="monthYear">May 2029</h3>
              <div class="calendar-controls">
                <button id="prevMonth" aria-label="Previous month">◀</button>
                <button id="nextMonth" aria-label="Next month">▶</button>
                <button id="filterBtn" class="small-select" onclick="handleFilter()">Filter</button>
                <button id="viewBtn" class="small-select" onclick="handleView()">Month ▼</button>
                <button class="new-agenda" onclick="handleNewAgenda()">+ New Agenda</button>
              </div>
            </div>

            <div class="calendar-days">
              <div>Sun</div><div>Mon</div><div>Tue</div><div>Wed</div>
              <div>Thu</div><div>Fri</div><div>Sat</div>
            </div>

            <div class="calendar-dates" id="calendarDates"></div>
          </div>
        </section>

        <!-- Right column: schedule details -->
        <aside class="col-right">
          <div class="card schedule-details" id="scheduleDetails">
            <img src="https://images.unsplash.com/photo-1546484959-f4b709f1d85a?auto=format&fit=crop&w=800&q=60" alt="Event hero image">
            <h3 id="detailTitle">Select a date</h3>
            <p class="tag">Event</p>
            <p id="detailDate"></p>
            <p>📍 Location info here</p>

            <div class="contact">
              <p><strong>Contact Name</strong></p>
              <p>Role</p>
              <p>+1-800-000-0000</p>
              <p>email@example.com</p>
            </div>

            <div class="team">
              <p style="margin:8px 0 6px 0;font-weight:700">Team</p>
              <div class="avatars">
                <img src="https://i.pravatar.cc/30?img=1" alt="team">
                <img src="https://i.pravatar.cc/30?img=2" alt="team">
                <img src="https://i.pravatar.cc/30?img=3" alt="team">
                <img src="https://i.pravatar.cc/30?img=4" alt="team">
              </div>
            </div>

            <div class="note">
              <ul>
                <li>Click a date in the calendar to see event details here.</li>
              </ul>
            </div>
          </div>
        </aside>
      </main>
    </div>
  </div>

  <!-- Calendar script -->
  <script>
    const calendarEvents = <?php echo json_encode($calendar_events); ?>;
  </script>
  <script>
    // Simple direct functions for button operations
    function handleSearch() {
        const searchTerm = document.getElementById('searchInput').value;
        if (searchTerm.trim()) {
            alert('Search functionality would search for: ' + searchTerm);
            // In a real implementation, this would filter calendar events
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

    function handleFilter() {
        alert('Filter options would appear here');
    }

    function handleView() {
        alert('View options (Month/Week/Day) would appear here');
    }

    function handleNewAgenda() {
        alert('Create new agenda functionality would go here');
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
    });
  </script>
  <script>
    // Calendar JavaScript
    document.addEventListener("DOMContentLoaded", () => {
      const calendarDates = document.getElementById("calendarDates");
      const calendarTitle = document.getElementById("monthYear");
      const detailTitle = document.getElementById("detailTitle");
      const detailDate = document.getElementById("detailDate");
      const prevBtn = document.getElementById("prevMonth");
      const nextBtn = document.getElementById("nextMonth");
      const scheduleDetails = document.getElementById("scheduleDetails");

      let year = 2029;
      let month = 4; // May

      // Sample event data
      const events = {
        "2029-5-1": [
          { type: "meeting", title: "Team Meeting", time: "10:00 AM", location: "Conference Room A", contact: "John Smith", role: "Project Manager", phone: "+1-800-123-4567", email: "john@example.com" }
        ],
        "2029-5-5": [
          { type: "event", title: "Product Launch", time: "2:00 PM", location: "Grand Hall", contact: "Sarah Johnson", role: "Event Coordinator", phone: "+1-800-765-4321", email: "sarah@example.com" }
        ],
        "2029-5-10": [
          { type: "setup", title: "Venue Setup", time: "9:00 AM", location: "Exhibition Center", contact: "Mike Wilson", role: "Logistics Manager", phone: "+1-800-555-1234", email: "mike@example.com" }
        ],
        "2029-5-15": [
          { type: "task", title: "Deadline: Project X", time: "5:00 PM", location: "Office", contact: "Emily Davis", role: "Team Lead", phone: "+1-800-987-6543", email: "emily@example.com" }
        ],
        "2029-5-20": [
          { type: "meeting", title: "Client Presentation", time: "11:00 AM", location: "Board Room", contact: "David Brown", role: "Account Executive", phone: "+1-800-246-8101", email: "david@example.com" }
        ],
        "2029-5-25": [
          { type: "event", title: "Company Anniversary", time: "6:00 PM", location: "Garden Venue", contact: "Lisa Taylor", role: "HR Director", phone: "+1-800-135-7924", email: "lisa@example.com" }
        ]
      };

      function monthLabel(y, m) {
        return new Date(y, m).toLocaleString("default", { month: "long", year: "numeric" });
      }

      function formatDate(date) {
        return new Date(date).toLocaleDateString("en-US", { weekday: 'long', month: 'long', day: 'numeric', year: 'numeric' });
      }

      function renderCalendar() {
        const firstDay = new Date(year, month, 1).getDay();
        const daysInMonth = new Date(year, month + 1, 0).getDate();
        const today = new Date();
        const isCurrentMonth = today.getMonth() === month && today.getFullYear() === year;

        calendarTitle.textContent = monthLabel(year, month);
        calendarDates.innerHTML = "";

        // empty slots
        for (let i = 0; i < firstDay; i++) {
          const empty = document.createElement("div");
          empty.className = "date-cell empty";
          calendarDates.appendChild(empty);
        }

        // dates
        for (let d = 1; d <= daysInMonth; d++) {
          const cell = document.createElement("div");
          const dateKey = `${year}-${month + 1}-${d}`;
          const dayEvents = events[dateKey] || [];
          
          cell.className = "date-cell";
          if (isCurrentMonth && d === today.getDate()) {
            cell.classList.add("today");
          }
          
          cell.innerHTML = `<div class="date-num">${d}</div>`;
          
          // Add events to the cell
          dayEvents.forEach(event => {
            const eventEl = document.createElement("button");
            eventEl.className = `event ${event.type}`;
            eventEl.textContent = event.title;
            eventEl.addEventListener("click", (e) => {
              e.stopPropagation();
              showEventDetails(event, dateKey);
            });
            cell.appendChild(eventEl);
          });
          
          cell.addEventListener("click", () => {
            // Remove selected class from all cells
            document.querySelectorAll('.date-cell').forEach(c => c.classList.remove('selected-day'));
            // Add selected class to clicked cell
            cell.classList.add('selected-day');
            
            if (dayEvents.length > 0) {
              showEventDetails(dayEvents[0], dateKey);
            } else {
              showEmptyDate(d);
            }
          });
          
          calendarDates.appendChild(cell);
        }
      }

      function showEventDetails(event, dateKey) {
        detailTitle.textContent = event.title;
        detailDate.textContent = formatDate(dateKey);
        
        // Update event details
        scheduleDetails.querySelector('.tag').textContent = event.type.charAt(0).toUpperCase() + event.type.slice(1);
        scheduleDetails.querySelector('p:nth-of-type(3)').textContent = `📍 ${event.location}`;
        
        // Update contact info
        const contactSection = scheduleDetails.querySelector('.contact');
        contactSection.innerHTML = `
          <p><strong>${event.contact}</strong></p>
          <p>${event.role}</p>
          <p>${event.phone}</p>
          <p>${event.email}</p>
        `;
        
        // Add time information
        if (!scheduleDetails.querySelector('.event-time')) {
          const timeEl = document.createElement('div');
          timeEl.className = 'event-time';
          scheduleDetails.insertBefore(timeEl, scheduleDetails.querySelector('.tag').nextSibling);
        }
        scheduleDetails.querySelector('.event-time').textContent = event.time;
      }

      function showEmptyDate(day) {
        detailTitle.textContent = `No events on ${day} ${monthLabel(year, month)}`;
        detailDate.textContent = "";
        scheduleDetails.querySelector('.tag').textContent = "No events";
        scheduleDetails.querySelector('p:nth-of-type(3)').textContent = "📍 No location specified";
        
        // Clear time if it exists
        if (scheduleDetails.querySelector('.event-time')) {
          scheduleDetails.querySelector('.event-time').remove();
        }
        
        // Reset contact info
        const contactSection = scheduleDetails.querySelector('.contact');
        contactSection.innerHTML = `
          <p><strong>Contact Name</strong></p>
          <p>Role</p>
          <p>+1-800-000-0000</p>
          <p>email@example.com</p>
        `;
      }

      prevBtn.addEventListener("click", () => {
        month--;
        if (month < 0) { month = 11; year--; }
        renderCalendar();
      });

      nextBtn.addEventListener("click", () => {
        month++;
        if (month > 11) { month = 0; year++; }
        renderCalendar();
      });

      renderCalendar();
    });
  </script>
</body>
</html>