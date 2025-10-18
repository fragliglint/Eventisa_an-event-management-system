<?php
session_start();
require_once 'config/database.php';

$database = new Database();
$db = $database->getConnection();

// Get filter parameters
$category = $_GET['category'] ?? 'all';
$search = $_GET['search'] ?? '';
$sort = $_GET['sort'] ?? 'date';

// Build query
$query = "SELECT * FROM events WHERE status='Active'";
$params = [];

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

// Add sorting
if ($sort === 'price_low') {
    $query .= " ORDER BY ticket_price ASC";
} elseif ($sort === 'price_high') {
    $query .= " ORDER BY ticket_price DESC";
} else {
    $query .= " ORDER BY event_date ASC";
}

$stmt = $db->prepare($query);
$stmt->execute($params);
$events = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Get categories for filter
$categories = $db->query("SELECT DISTINCT category FROM events WHERE status='Active'")->fetchAll(PDO::FETCH_COLUMN);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>All Events - Eventisa</title>
    <link rel="stylesheet" href="css/user-styles.css">
    <style>
        /* Events Page Specific Styles */
        .page-header {
            background: linear-gradient(135deg, var(--eventisa-primary), var(--eventisa-secondary));
            background-size: 200% 200%;
            color: white;
            padding: 80px 0 40px;
            text-align: center;
            animation: gradientShift 8s ease infinite;
            position: relative;
            overflow: hidden;
        }

        .page-header::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1000 1000"><circle fill="rgba(255,255,255,0.1)" cx="200" cy="200" r="100"/><circle fill="rgba(255,255,255,0.05)" cx="800" cy="300" r="150"/><circle fill="rgba(255,255,255,0.08)" cx="400" cy="600" r="120"/></svg>');
            animation: float 8s ease-in-out infinite;
        }

        .page-title {
            font-size: 3.5rem;
            font-weight: 800;
            margin-bottom: 16px;
            position: relative;
            z-index: 1;
            animation: slideInUp 0.8s var(--ease-out) 0.2s both;
        }

        .page-subtitle {
            font-size: 1.25rem;
            opacity: 0.9;
            position: relative;
            z-index: 1;
            animation: slideInUp 0.8s var(--ease-out) 0.4s both;
        }

        /* Filters Section */
        .filters-section {
            background: var(--eventisa-card-bg);
            padding: 30px 0;
            border-bottom: 1px solid var(--eventisa-border);
            animation: slideInDown 0.6s var(--ease-out);
        }

        .filter-form {
            display: flex;
            gap: 16px;
            align-items: center;
            flex-wrap: wrap;
            animation: fadeIn 0.8s var(--ease-out) 0.3s both;
        }

        .filter-group {
            flex: 1;
            min-width: 200px;
            position: relative;
        }

        .filter-input,
        .filter-select {
            width: 100%;
            padding: 14px 18px;
            border: 2px solid var(--eventisa-border);
            border-radius: var(--eventisa-radius-md);
            background: white;
            font-size: 16px;
            outline: none;
            transition: all 0.3s var(--ease-out);
            font-family: 'Poppins', sans-serif;
            box-shadow: var(--eventisa-shadow);
        }

        .filter-input:focus,
        .filter-select:focus {
            border-color: var(--eventisa-primary);
            box-shadow: 0 0 0 3px rgba(139, 92, 246, 0.1);
            transform: translateY(-2px);
        }

        .filter-input::placeholder {
            color: #9ca3af;
        }

        .filter-btn {
            padding: 14px 28px;
            background: linear-gradient(135deg, var(--eventisa-primary), var(--eventisa-secondary));
            color: white;
            border: none;
            border-radius: var(--eventisa-radius-md);
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s var(--ease-out);
            position: relative;
            overflow: hidden;
            font-family: 'Poppins', sans-serif;
            box-shadow: var(--eventisa-shadow);
            white-space: nowrap;
        }

        .filter-btn::before {
            content: '';
            position: absolute;
            top: 50%;
            left: 50%;
            width: 0;
            height: 0;
            background: rgba(255, 255, 255, 0.2);
            border-radius: 50%;
            transform: translate(-50%, -50%);
            transition: width 0.6s, height 0.6s;
        }

        .filter-btn:hover::before {
            width: 300px;
            height: 300px;
        }

        .filter-btn:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 25px rgba(139, 92, 246, 0.4);
        }

        /* Events Grid Enhancements */
        .events-section {
            padding: 60px 0;
            animation: fadeIn 0.8s var(--ease-out) both;
        }

        .events-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(350px, 1fr));
            gap: 30px;
            margin-top: 40px;
        }

        .event-card {
            background: white;
            border-radius: var(--eventisa-radius-lg);
            overflow: hidden;
            box-shadow: var(--eventisa-shadow);
            border: 1px solid var(--eventisa-border);
            transition: all 0.4s var(--ease-out);
            animation: scaleIn 0.6s var(--ease-out) both;
            position: relative;
        }

        .event-card:nth-child(1) { animation-delay: 0.1s; }
        .event-card:nth-child(2) { animation-delay: 0.2s; }
        .event-card:nth-child(3) { animation-delay: 0.3s; }
        .event-card:nth-child(4) { animation-delay: 0.4s; }
        .event-card:nth-child(5) { animation-delay: 0.5s; }
        .event-card:nth-child(6) { animation-delay: 0.6s; }

        .event-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 0;
            background: linear-gradient(135deg, var(--eventisa-primary), var(--eventisa-secondary));
            transition: height 0.4s var(--ease-out);
            z-index: 1;
        }

        .event-card:hover::before {
            height: 4px;
        }

        .event-card:hover {
            transform: translateY(-12px) scale(1.02);
            box-shadow: 0 25px 50px rgba(16, 24, 40, 0.15);
        }

        .event-image {
            position: relative;
            height: 220px;
            overflow: hidden;
        }

        .event-image img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: all 0.6s var(--ease-out);
            filter: brightness(0.9);
        }

        .event-card:hover .event-image img {
            transform: scale(1.1) rotate(1deg);
            filter: brightness(1);
        }

        .event-tag {
            position: absolute;
            top: 15px;
            left: 15px;
            background: white;
            color: var(--eventisa-primary);
            padding: 8px 16px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 700;
            box-shadow: var(--eventisa-shadow);
            animation: slideInLeft 0.5s var(--ease-out) 0.3s both;
            z-index: 2;
        }

        .event-status {
            position: absolute;
            top: 15px;
            right: 15px;
            background: white;
            color: var(--eventisa-primary);
            padding: 8px 16px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 700;
            box-shadow: var(--eventisa-shadow);
            animation: slideInRight 0.5s var(--ease-out) 0.3s both;
            z-index: 2;
        }

        .event-content {
            padding: 24px;
            animation: fadeIn 0.6s var(--ease-out) 0.4s both;
        }

        .event-date {
            font-size: 14px;
            color: var(--eventisa-primary);
            font-weight: 700;
            margin-bottom: 10px;
            animation: slideInUp 0.5s var(--ease-out) 0.5s both;
        }

        .event-title {
            font-size: 18px;
            font-weight: 700;
            margin-bottom: 10px;
            color: var(--eventisa-text-dark);
            line-height: 1.3;
            animation: slideInUp 0.5s var(--ease-out) 0.6s both;
        }

        .event-location {
            font-size: 14px;
            color: #666;
            margin-bottom: 16px;
            animation: slideInUp 0.5s var(--ease-out) 0.7s both;
        }

        .event-progress {
            margin-bottom: 20px;
            animation: slideInUp 0.5s var(--ease-out) 0.8s both;
        }

        .progress-bar {
            width: 100%;
            height: 6px;
            background: #eee;
            border-radius: 6px;
            margin-bottom: 8px;
            overflow: hidden;
            position: relative;
        }

        .progress-bar::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            height: 100%;
            width: 100%;
            background: linear-gradient(90deg, var(--eventisa-primary), var(--eventisa-secondary));
            border-radius: 6px;
            transform: scaleX(0);
            transform-origin: left;
            animation: progressFill 1.5s var(--ease-out) 1s both;
        }

        .progress-fill {
            height: 100%;
            background: linear-gradient(90deg, var(--eventisa-primary), var(--eventisa-secondary));
            border-radius: 6px;
            transition: width 1.5s var(--ease-out);
            transform-origin: left;
        }

        .progress-text {
            font-size: 12px;
            color: #666;
            text-align: right;
            animation: fadeIn 0.5s var(--ease-out) 1.2s both;
        }

        .event-footer {
            display: flex;
            justify-content: space-between;
            align-items: center;
            animation: slideInUp 0.5s var(--ease-out) 0.9s both;
        }

        .event-price {
            font-size: 22px;
            font-weight: 800;
            color: var(--eventisa-primary);
            animation: pulse 2s infinite;
        }

        .btn-book {
            padding: 12px 24px;
            background: linear-gradient(135deg, var(--eventisa-primary), var(--eventisa-secondary));
            color: white;
            text-decoration: none;
            border-radius: var(--eventisa-radius-md);
            font-weight: 600;
            transition: all 0.3s var(--ease-out);
            position: relative;
            overflow: hidden;
            font-size: 14px;
        }

        .btn-book::before {
            content: '';
            position: absolute;
            top: 50%;
            left: 50%;
            width: 0;
            height: 0;
            background: rgba(255, 255, 255, 0.2);
            border-radius: 50%;
            transform: translate(-50%, -50%);
            transition: width 0.6s, height 0.6s;
        }

        .btn-book:hover::before {
            width: 300px;
            height: 300px;
        }

        .btn-book:hover {
            transform: translateY(-2px) scale(1.05);
            box-shadow: 0 8px 20px rgba(139, 92, 246, 0.4);
        }

        /* No Events State */
        .no-events {
            text-align: center;
            padding: 80px 20px;
            animation: fadeIn 0.8s var(--ease-out);
        }

        .no-events h3 {
            font-size: 2rem;
            margin-bottom: 16px;
            color: var(--eventisa-text-dark);
            animation: bounceIn 0.8s var(--bounce);
        }

        .no-events p {
            color: #666;
            font-size: 1.1rem;
            animation: slideInUp 0.6s var(--ease-out) 0.3s both;
        }

        /* Results Count */
        .results-count {
            text-align: center;
            margin-bottom: 30px;
            color: #666;
            font-size: 1.1rem;
            animation: fadeIn 0.6s var(--ease-out);
        }

        .results-count strong {
            color: var(--eventisa-primary);
            font-weight: 700;
        }

        /* Active Filter Tags */
        .active-filters {
            display: flex;
            gap: 10px;
            margin-bottom: 20px;
            flex-wrap: wrap;
            animation: slideInDown 0.6s var(--ease-out);
        }

        .filter-tag {
            background: linear-gradient(135deg, var(--eventisa-primary), var(--eventisa-secondary));
            color: white;
            padding: 8px 16px;
            border-radius: 20px;
            font-size: 14px;
            font-weight: 600;
            display: flex;
            align-items: center;
            gap: 8px;
            animation: scaleIn 0.3s var(--ease-out);
        }

        .filter-tag .remove {
            background: rgba(255, 255, 255, 0.3);
            border: none;
            color: white;
            width: 20px;
            height: 20px;
            border-radius: 50%;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 12px;
            transition: background 0.3s;
        }

        .filter-tag .remove:hover {
            background: rgba(255, 255, 255, 0.5);
        }

        /* Loading Animation */
        .events-loading {
            text-align: center;
            padding: 60px 20px;
        }

        .loading-spinner {
            width: 40px;
            height: 40px;
            border: 4px solid #f3f3f3;
            border-top: 4px solid var(--eventisa-primary);
            border-radius: 50%;
            animation: spin 1s linear infinite;
            margin: 0 auto 20px;
        }

        /* Responsive Design */
        @media (max-width: 768px) {
            .page-title {
                font-size: 2.5rem;
            }

            .filter-form {
                flex-direction: column;
            }

            .filter-group {
                min-width: 100%;
            }

            .events-grid {
                grid-template-columns: 1fr;
            }

            .event-card:hover {
                transform: translateY(-8px) scale(1.01);
            }

            .page-header {
                padding: 60px 0 30px;
            }
        }

        @media (max-width: 480px) {
            .page-title {
                font-size: 2rem;
            }

            .event-content {
                padding: 20px;
            }

            .event-footer {
                flex-direction: column;
                gap: 12px;
                align-items: flex-start;
            }

            .btn-book {
                width: 100%;
                text-align: center;
            }
        }

        /* Animation Keyframes */
        @keyframes progressFill {
            from {
                transform: scaleX(0);
            }
            to {
                transform: scaleX(1);
            }
        }

        @keyframes gradientShift {
            0% {
                background-position: 0% 50%;
            }
            50% {
                background-position: 100% 50%;
            }
            100% {
                background-position: 0% 50%;
            }
        }

        @keyframes float {
            0%, 100% {
                transform: translateY(0px);
            }
            50% {
                transform: translateY(-10px);
            }
        }

        @keyframes slideInUp {
            from {
                opacity: 0;
                transform: translateY(40px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        @keyframes slideInDown {
            from {
                opacity: 0;
                transform: translateY(-40px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        @keyframes slideInLeft {
            from {
                opacity: 0;
                transform: translateX(-40px);
            }
            to {
                opacity: 1;
                transform: translateX(0);
            }
        }

        @keyframes slideInRight {
            from {
                opacity: 0;
                transform: translateX(40px);
            }
            to {
                opacity: 1;
                transform: translateX(0);
            }
        }

        @keyframes scaleIn {
            from {
                opacity: 0;
                transform: scale(0.8);
            }
            to {
                opacity: 1;
                transform: scale(1);
            }
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
            }
            to {
                opacity: 1;
            }
        }

        @keyframes bounceIn {
            0% {
                opacity: 0;
                transform: scale(0.3);
            }
            50% {
                opacity: 1;
                transform: scale(1.05);
            }
            70% {
                transform: scale(0.9);
            }
            100% {
                opacity: 1;
                transform: scale(1);
            }
        }

        @keyframes pulse {
            0% {
                transform: scale(1);
            }
            50% {
                transform: scale(1.05);
            }
            100% {
                transform: scale(1);
            }
        }

        @keyframes spin {
            0% {
                transform: rotate(0deg);
            }
            100% {
                transform: rotate(360deg);
            }
        }
    </style>
</head>
<body>
    <!-- Navigation -->
    <nav class="eventisa-navbar">
        <div class="container nav-container">
            <div class="nav-brand">
                <a href="index.php" aria-label="Eventisa home">
                    <img src="asstes/logo1.png" alt="Eventisa logo" style="width:140px; height:auto; display:block; object-fit:contain;">
                </a>
            </div>

            <div class="nav-menu">
                <a href="index.php" class="nav-link">Home</a>
                <a href="events.php" class="nav-link active">Events</a>
                <a href="categories.php" class="nav-link">Categories</a>
                <a href="about.php" class="nav-link">About</a>
                <a href="contact.php" class="nav-link">Contact Us</a>
                <a href="FAQ.php" class="nav-link">FAQ</a>
            </div>

            <div class="nav-actions">
                <?php if (isset($_SESSION['user_id'])): ?>
                    <div class="user-info-nav">
                        <a href="<?php echo ($_SESSION['user_role'] === 'admin' || $_SESSION['user_role'] === 'manager' || $_SESSION['user_role'] === 'staff') ? 'Dashboard.php' : 'user-dashboard.php'; ?>" class="btn-login">
                            👤 <?php echo $_SESSION['user_name']; ?>
                        </a>
                        <a href="logout.php" class="btn-sign-in">Logout</a>
                    </div>
                <?php else: ?>
                    <button class="btn-cart" aria-label="Cart">
                        <span class="cart-count">0</span>
                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M6 2L3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4z"></path>
                            <line x1="3" y1="6" x2="21" y2="6"></line>
                            <path d="M16 10a4 4 0 0 1-8 0"></path>
                        </svg>
                    </button>
                    <a href="login.php" class="btn-sign-in">Sign in</a>
                <?php endif; ?>
            </div>
        </div>
    </nav>

    <!-- Events Header -->
    <section class="page-header">
        <div class="container">
            <h1 class="page-title">All Events</h1>
            <p class="page-subtitle">Discover amazing events happening around you</p>
        </div>
    </section>

    <!-- Filters Section -->
    <section class="filters-section">
        <div class="container">
            <!-- Active Filters -->
            <?php if ($category !== 'all' || !empty($search)): ?>
            <div class="active-filters">
                <?php if ($category !== 'all'): ?>
                <div class="filter-tag">
                    Category: <?php echo htmlspecialchars($category); ?>
                    <a href="?<?php echo http_build_query(array_merge($_GET, ['category' => 'all'])); ?>" class="remove">×</a>
                </div>
                <?php endif; ?>
                <?php if (!empty($search)): ?>
                <div class="filter-tag">
                    Search: "<?php echo htmlspecialchars($search); ?>"
                    <a href="?<?php echo http_build_query(array_merge($_GET, ['search' => ''])); ?>" class="remove">×</a>
                </div>
                <?php endif; ?>
            </div>
            <?php endif; ?>

            <!-- Results Count -->
            <div class="results-count scroll-reveal">
                Found <strong><?php echo count($events); ?></strong> event<?php echo count($events) !== 1 ? 's' : ''; ?> matching your criteria
            </div>

            <!-- Filter Form -->
            <div class="filters">
                <form method="GET" class="filter-form">
                    <div class="filter-group">
                        <input type="text" name="search" placeholder="Search events..." value="<?php echo htmlspecialchars($search); ?>" class="filter-input">
                    </div>
                    <div class="filter-group">
                        <select name="category" class="filter-select">
                            <option value="all" <?php echo $category === 'all' ? 'selected' : ''; ?>>All Categories</option>
                            <?php foreach($categories as $cat): ?>
                            <option value="<?php echo $cat; ?>" <?php echo $category === $cat ? 'selected' : ''; ?>><?php echo $cat; ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="filter-group">
                        <select name="sort" class="filter-select">
                            <option value="date" <?php echo $sort === 'date' ? 'selected' : ''; ?>>Sort by Date</option>
                            <option value="price_low" <?php echo $sort === 'price_low' ? 'selected' : ''; ?>>Price: Low to High</option>
                            <option value="price_high" <?php echo $sort === 'price_high' ? 'selected' : ''; ?>>Price: High to Low</option>
                        </select>
                    </div>
                    <button type="submit" class="filter-btn">Apply Filters</button>
                </form>
            </div>
        </div>
    </section>

    <!-- Events Grid -->
    <section class="section events-section">
        <div class="container">
            <?php if (empty($events)): ?>
                <div class="no-events">
                    <h3>No events found</h3>
                    <p>Try adjusting your search filters or browse all categories.</p>
                    <div style="margin-top: 30px;">
                        <a href="events.php" class="btn-book" style="display: inline-block;">Browse All Events</a>
                    </div>
                </div>
            <?php else: ?>
                <div class="events-grid">
                    <?php foreach($events as $event): 
                        $sold_percentage = $event['total_tickets'] > 0 ? ($event['tickets_sold'] / $event['total_tickets']) * 100 : 0;
                    ?>
                    <div class="event-card scroll-reveal">
                        <div class="event-image">
                            <img src="<?php echo $event['image_url']; ?>" alt="<?php echo htmlspecialchars($event['title']); ?>">
                            <div class="event-tag"><?php echo $event['category']; ?></div>
                            <div class="event-status"><?php echo $sold_percentage > 80 ? 'Almost Sold Out' : 'Available'; ?></div>
                        </div>
                        <div class="event-content">
                            <div class="event-date"><?php echo date('M j, Y', strtotime($event['event_date'])); ?> • <?php echo date('g:i A', strtotime($event['event_time'])); ?></div>
                            <h3 class="event-title"><?php echo htmlspecialchars($event['title']); ?></h3>
                            <p class="event-location">📍 <?php echo htmlspecialchars($event['location']); ?></p>
                            <div class="event-progress">
                                <div class="progress-bar">
                                    <div class="progress-fill" style="width: <?php echo $sold_percentage; ?>%"></div>
                                </div>
                                <div class="progress-text"><?php echo round($sold_percentage); ?>% Sold</div>
                            </div>
                            <div class="event-footer">
                                <div class="event-price">$<?php echo $event['ticket_price']; ?></div>
                                <a href="event-details.php?id=<?php echo $event['id']; ?>" class="btn-book">Book Now</a>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
    </section>

    <!-- Footer -->
    <footer class="eventisa-footer">
        <div class="container">
            <div class="footer-grid">
                <div class="footer-section brand-info">
                    <div class="footer-brand">
                        <img src="asstes/logo1.png" alt="Eventisa logo" style="width:120px; height:auto; display:block; object-fit:contain;">
                    </div>
                    <p class="tagline">Your Gateway to Amazing Events</p>
                    <p class="trade-license">TRADE LICENSE: TRAD/DNCC/141845/2022</p>
                    <h4>FOLLOW US</h4>
                    <div class="social-links">
                        <a href="#" aria-label="Facebook">📘</a>
                        <a href="#" aria-label="Instagram">📷</a>
                        <a href="#" aria-label="YouTube">📺</a>
                    </div>
                </div>

                <div class="footer-section">
                    <h4>MORE INFO</h4>
                    <a href="contact.php">Contact us</a>
                    <a href="FAQ.php">FAQ</a>
                </div>

                <div class="footer-section">
                    <h4>LEGALS</h4>
                    <a href="terms.php">Terms and Conditions</a>
                    <a href="privacy.php">Privacy Policy</a>
                    <a href="refund.php">Refund Policy</a>
                </div>

                <div class="footer-section">
                    <h4>CONTACTS</h4>
                    <div class="contact-item">
                        <span class="icon">🏠</span>
                        <p>LA-56,Post Office Road, Middle Badda, Dhaka, Bangladesh</p>
                    </div>
                    <div class="contact-item">
                        <span class="icon">📞</span>
                        <p>01858057515</p>
                    </div>
                    <div class="contact-item">
                        <span class="icon">✉️</span>
                        <p>eventisa.live@gmail.com</p>
                    </div>
                </div>
            </div>

            <div class="footer-bottom">
                <div class="payment-methods">
                    <span class="payment-icon">💰</span>
                    <span class="payment-icon">💸</span>
                    <span class="payment-icon">💳</span>
                    <span class="payment-icon">💳</span>
                </div>
                <p>&copy; <?php echo date('Y'); ?> Eventisa | All rights reserved.</p>
            </div>
        </div>
    </footer>

    <script>
        // Enhanced Events Page JavaScript
        document.addEventListener('DOMContentLoaded', function() {
            // Scroll Reveal Animation
            const scrollElements = document.querySelectorAll('.scroll-reveal');

            const elementInView = (el, fraction = 1.25) => {
                const top = el.getBoundingClientRect().top;
                return top <= (window.innerHeight || document.documentElement.clientHeight) / fraction;
            };

            const displayScrollElement = (element) => {
                element.classList.add('visible');
            };

            const handleScrollAnimation = () => {
                scrollElements.forEach((el) => {
                    if (elementInView(el, 1.25)) {
                        displayScrollElement(el);
                    }
                });
            };

            window.addEventListener('scroll', handleScrollAnimation);
            window.addEventListener('resize', handleScrollAnimation);
            handleScrollAnimation();

            // Filter Form Enhancements
            const filterForm = document.querySelector('.filter-form');
            if (filterForm) {
                // Real-time search suggestions (placeholder for future implementation)
                const searchInput = filterForm.querySelector('input[name="search"]');
                if (searchInput) {
                    searchInput.addEventListener('input', function() {
                        // Add debounced search suggestions here
                        console.log('Search query:', this.value);
                    });
                }

                // Auto-submit on select change
                const selects = filterForm.querySelectorAll('select');
                selects.forEach(select => {
                    select.addEventListener('change', function() {
                        filterForm.submit();
                    });
                });
            }

            // Add to cart functionality
            const cartButtons = document.querySelectorAll('.btn-book');
            const cartCount = document.querySelector('.cart-count');
            let cartItems = 0;

            cartButtons.forEach(button => {
                button.addEventListener('click', function(e) {
                    if (this.getAttribute('href').startsWith('event-details.php')) {
                        return; // Allow navigation to event details
                    }
                    
                    e.preventDefault();
                    cartItems++;
                    if (cartCount) {
                        cartCount.textContent = cartItems;
                        
                        // Add animation
                        cartCount.style.transform = 'scale(1.3)';
                        setTimeout(() => {
                            cartCount.style.transform = 'scale(1)';
                        }, 300);
                    }

                    // Show added to cart feedback
                    const originalText = this.textContent;
                    this.textContent = 'Added to Cart!';
                    this.style.background = 'linear-gradient(135deg, #10b981, #059669)';
                    
                    setTimeout(() => {
                        this.textContent = originalText;
                        this.style.background = '';
                    }, 2000);
                });
            });

            // Enhanced hover effects
            const eventCards = document.querySelectorAll('.event-card');
            eventCards.forEach(card => {
                card.addEventListener('mouseenter', function() {
                    this.style.zIndex = '10';
                });
                
                card.addEventListener('mouseleave', function() {
                    this.style.zIndex = '1';
                });
            });

            // Loading state for filters
            const filterBtn = document.querySelector('.filter-btn');
            if (filterBtn) {
                filterBtn.addEventListener('click', function() {
                    this.innerHTML = '<span class="loading-spinner" style="width: 16px; height: 16px; border: 2px solid transparent; border-top: 2px solid white; border-radius: 50%; display: inline-block; animation: spin 1s linear infinite; margin-right: 8px;"></span> Applying...';
                    setTimeout(() => {
                        this.innerHTML = 'Apply Filters';
                    }, 1000);
                });
            }

            // Price formatting
            const priceElements = document.querySelectorAll('.event-price');
            priceElements.forEach(priceEl => {
                const price = priceEl.textContent.replace('$', '');
                const formattedPrice = new Intl.NumberFormat('en-US', {
                    style: 'currency',
                    currency: 'USD'
                }).format(price);
                priceEl.textContent = formattedPrice;
            });

            console.log('Events page enhanced with animations and interactions!');
        });

        // Utility function for debouncing
        function debounce(func, wait, immediate) {
            let timeout;
            return function executedFunction(...args) {
                const later = () => {
                    timeout = null;
                    if (!immediate) func(...args);
                };
                const callNow = immediate && !timeout;
                clearTimeout(timeout);
                timeout = setTimeout(later, wait);
                if (callNow) func(...args);
            };
        }
    </script>
</body>
</html>