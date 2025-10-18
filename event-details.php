<?php
session_start();
require_once 'config/database.php';

$database = new Database();
$db = $database->getConnection();

// Get event ID from URL
$event_id = $_GET['id'] ?? null;

if (!$event_id) {
    header('Location: events.php');
    exit;
}

// Get event details
$stmt = $db->prepare("SELECT * FROM events WHERE id = ? AND status='Active'");
$stmt->execute([$event_id]);
$event = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$event) {
    header('Location: events.php');
    exit;
}

$sold_percentage = $event['total_tickets'] > 0 ? ($event['tickets_sold'] / $event['total_tickets']) * 100 : 0;
$available_tickets = $event['total_tickets'] - $event['tickets_sold'];

// Get related events
$related_events = $db->prepare("SELECT * FROM events WHERE category = ? AND id != ? AND status='Active' ORDER BY event_date ASC LIMIT 3");
$related_events->execute([$event['category'], $event_id]);
$related_events = $related_events->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($event['title']); ?> - Eventisa</title>
    <link rel="stylesheet" href="css/user-styles.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600;700;800&display=swap" rel="stylesheet">
    <style>
        .event-details {
            padding: 2rem 0;
        }

        .event-details-content {
            display: grid;
            grid-template-columns: 2fr 1fr;
            gap: 3rem;
            align-items: start;
        }

        .event-hero {
            position: relative;
            border-radius: 16px;
            overflow: hidden;
            margin-bottom: 2rem;
        }

        .event-hero-image {
            width: 100%;
            height: 400px;
            object-fit: cover;
        }

        .event-hero-overlay {
            position: absolute;
            top: 1rem;
            left: 1rem;
            right: 1rem;
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
        }

        .event-tag {
            background: rgba(255, 255, 255, 0.9);
            padding: 0.5rem 1rem;
            border-radius: 20px;
            font-weight: 600;
            font-size: 0.875rem;
        }

        .event-status-badge {
            background: linear-gradient(135deg, #10b981 0%, #059669 100%);
            color: white;
            padding: 0.5rem 1rem;
            border-radius: 20px;
            font-weight: 600;
            font-size: 0.875rem;
        }

        .event-status-badge.sold-out {
            background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%);
        }

        .event-title {
            font-size: 2.5rem;
            font-weight: 700;
            margin-bottom: 1rem;
            color: #1f2937;
        }

        .event-meta {
            display: flex;
            flex-direction: column;
            gap: 1rem;
            margin-bottom: 2rem;
        }

        .meta-item {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            font-size: 1.1rem;
        }

        .meta-icon {
            font-size: 1.25rem;
        }

        .meta-text {
            color: #4b5563;
            font-weight: 500;
        }

        .event-description {
            margin-bottom: 2rem;
        }

        .event-description h3 {
            font-size: 1.5rem;
            margin-bottom: 1rem;
            color: #1f2937;
        }

        .event-description p {
            line-height: 1.7;
            color: #4b5563;
            font-size: 1.1rem;
        }

        .event-features {
            margin-bottom: 2rem;
        }

        .event-features h3 {
            font-size: 1.5rem;
            margin-bottom: 1rem;
            color: #1f2937;
        }

        .features-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 1rem;
        }

        .feature-item {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            padding: 1rem;
            background: #f8fafc;
            border-radius: 8px;
        }

        .feature-icon {
            font-size: 1.5rem;
        }

        .feature-text {
            font-weight: 500;
            color: #374151;
        }

        .booking-card {
            background: white;
            border-radius: 16px;
            padding: 2rem;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
            border: 1px solid #e5e7eb;
            position: sticky;
            top: 2rem;
        }

        .booking-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 1.5rem;
        }

        .booking-header h3 {
            font-size: 1.5rem;
            color: #1f2937;
            margin: 0;
        }

        .price {
            font-size: 2rem;
            font-weight: 700;
            color: #667eea;
        }

        .booking-progress {
            margin-bottom: 2rem;
        }

        .progress-text {
            font-weight: 600;
            margin-bottom: 0.5rem;
            color: #374151;
        }

        .progress-bar {
            width: 100%;
            height: 8px;
            background: #e5e7eb;
            border-radius: 4px;
            overflow: hidden;
            margin-bottom: 0.5rem;
        }

        .progress-fill {
            height: 100%;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border-radius: 4px;
            transition: width 0.3s ease;
        }

        .progress-stats {
            font-size: 0.875rem;
            color: #6b7280;
        }

        .btn-book-now {
            width: 100%;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border: none;
            padding: 1rem 2rem;
            border-radius: 8px;
            font-size: 1.1rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            margin-top: 1rem;
        }

        .btn-book-now:hover {
            opacity: 0.9;
            transform: translateY(-2px);
        }

        .btn-book-now:disabled {
            opacity: 0.6;
            cursor: not-allowed;
            transform: none;
        }

        .related-events {
            background: #f8fafc;
            padding: 4rem 0;
            margin-top: 4rem;
        }

        .section-title {
            font-size: 2rem;
            font-weight: 700;
            text-align: center;
            margin-bottom: 3rem;
            color: #1f2937;
        }

        .events-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 2rem;
        }

        .event-card {
            background: white;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            transition: transform 0.3s ease;
        }

        .event-card:hover {
            transform: translateY(-5px);
        }

        .event-image {
            position: relative;
            height: 200px;
        }

        .event-image img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .event-content {
            padding: 1.5rem;
        }

        .event-date {
            color: #667eea;
            font-weight: 600;
            margin-bottom: 0.5rem;
        }

        .event-card .event-title {
            font-size: 1.25rem;
            margin-bottom: 0.5rem;
        }

        .event-location {
            color: #6b7280;
            margin-bottom: 1rem;
        }

        .event-footer {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .event-price {
            font-size: 1.25rem;
            font-weight: 700;
            color: #667eea;
        }

        .btn-book {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 0.5rem 1rem;
            border-radius: 6px;
            text-decoration: none;
            font-weight: 600;
            transition: all 0.3s ease;
        }

        .btn-book:hover {
            opacity: 0.9;
            transform: translateY(-2px);
        }

        /* Booking Modal Styles (Same as index.php) */
        .quick-book-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.8);
            display: none;
            justify-content: center;
            align-items: center;
            z-index: 1000;
            backdrop-filter: blur(5px);
        }

        .quick-book-modal {
            background: white;
            border-radius: 16px;
            max-width: 500px;
            width: 90%;
            max-height: 90vh;
            overflow-y: auto;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
        }

        .quick-book-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 1.5rem;
            border-bottom: 1px solid #e5e7eb;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border-radius: 16px 16px 0 0;
        }

        .close-modal {
            background: none;
            border: none;
            font-size: 1.5rem;
            cursor: pointer;
            color: white;
            padding: 0;
            width: 30px;
            height: 30px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .booking-steps {
            padding: 1.5rem 1.5rem 0;
        }

        .step-indicator {
            display: flex;
            justify-content: space-between;
            position: relative;
        }

        .step-indicator::before {
            content: '';
            position: absolute;
            top: 15px;
            left: 25px;
            right: 25px;
            height: 2px;
            background: #e5e7eb;
            z-index: 1;
        }

        .step {
            display: flex;
            flex-direction: column;
            align-items: center;
            position: relative;
            z-index: 2;
        }

        .step-number {
            width: 30px;
            height: 30px;
            border-radius: 50%;
            background: #e5e7eb;
            color: #6b7280;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 600;
            margin-bottom: 0.5rem;
            transition: all 0.3s ease;
        }

        .step.active .step-number {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
        }

        .step-label {
            font-size: 0.75rem;
            color: #6b7280;
            font-weight: 500;
        }

        .step.active .step-label {
            color: #667eea;
        }

        .booking-step {
            display: none;
            padding: 1.5rem;
        }

        .booking-step.active {
            display: block;
        }

        .ticket-selection {
            background: #f8fafc;
            border-radius: 12px;
            padding: 1.5rem;
            margin-bottom: 1.5rem;
        }

        .ticket-type-header {
            display: flex;
            justify-content: space-between;
            align-items: start;
            margin-bottom: 0.5rem;
        }

        .ticket-price {
            font-size: 1.25rem;
            font-weight: 700;
            color: #667eea;
        }

        .ticket-description p {
            margin: 0;
            color: #6b7280;
            font-size: 0.9rem;
        }

        .ticket-quantity-selector {
            margin-bottom: 1.5rem;
        }

        .ticket-quantity-selector label {
            display: block;
            margin-bottom: 0.5rem;
            font-weight: 600;
            color: #374151;
        }

        .quantity-controls {
            display: flex;
            align-items: center;
            gap: 1rem;
        }

        .quantity-btn {
            width: 40px;
            height: 40px;
            border: 2px solid #e5e7eb;
            background: white;
            border-radius: 8px;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.2rem;
            font-weight: 600;
            transition: all 0.3s ease;
        }

        .quantity-btn:hover:not(:disabled) {
            border-color: #667eea;
            background: #667eea;
            color: white;
        }

        .quantity-btn:disabled {
            opacity: 0.5;
            cursor: not-allowed;
        }

        .quantity-display {
            font-size: 1.25rem;
            font-weight: 700;
            min-width: 40px;
            text-align: center;
        }

        .ticket-summary {
            border-top: 1px solid #e5e7eb;
            padding-top: 1rem;
        }

        .summary-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 0.5rem;
            font-size: 0.9rem;
        }

        .summary-row.total {
            border-top: 1px solid #e5e7eb;
            padding-top: 0.5rem;
            font-weight: 700;
            font-size: 1.1rem;
            color: #1f2937;
        }

        .form-section {
            margin-bottom: 1.5rem;
        }

        .form-group {
            margin-bottom: 1rem;
        }

        .form-group label {
            display: block;
            margin-bottom: 0.5rem;
            font-weight: 600;
            color: #374151;
            font-size: 0.9rem;
        }

        .form-group input,
        .form-group textarea {
            width: 100%;
            padding: 0.75rem;
            border: 2px solid #e5e7eb;
            border-radius: 8px;
            font-family: 'Poppins', sans-serif;
            box-sizing: border-box;
            transition: border-color 0.3s ease;
        }

        .form-group input:focus,
        .form-group textarea:focus {
            outline: none;
            border-color: #667eea;
            box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
        }

        .order-summary {
            background: #f8fafc;
            border-radius: 12px;
            padding: 1.5rem;
            margin-bottom: 1.5rem;
        }

        .order-item {
            display: flex;
            justify-content: space-between;
            margin-bottom: 1rem;
            font-weight: 600;
        }

        .order-breakdown {
            border-top: 1px solid #e5e7eb;
            padding-top: 1rem;
        }

        .breakdown-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 0.5rem;
            font-size: 0.9rem;
        }

        .breakdown-row.total {
            border-top: 1px solid #e5e7eb;
            padding-top: 0.5rem;
            font-weight: 700;
            font-size: 1.1rem;
        }

        .payment-options {
            display: grid;
            gap: 0.75rem;
            margin-bottom: 1.5rem;
        }

        .payment-option {
            display: flex;
            align-items: center;
            padding: 1rem;
            border: 2px solid #e5e7eb;
            border-radius: 8px;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .payment-option:hover {
            border-color: #667eea;
        }

        .payment-option input[type="radio"]:checked + .payment-icon {
            background: #667eea;
            color: white;
        }

        .payment-option input[type="radio"]:checked ~ span:last-child {
            color: #667eea;
            font-weight: 600;
        }

        .payment-icon {
            width: 40px;
            height: 40px;
            border-radius: 8px;
            background: #f3f4f6;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-right: 1rem;
            font-size: 1.2rem;
            transition: all 0.3s ease;
        }

        .terms-agreement {
            margin: 1.5rem 0;
        }

        .checkbox-label {
            display: flex;
            align-items: flex-start;
            gap: 0.75rem;
            cursor: pointer;
            font-size: 0.9rem;
        }

        .step-actions {
            display: flex;
            gap: 1rem;
            margin-top: 2rem;
        }

        .btn-back,
        .btn-next,
        .btn-pay-now {
            flex: 1;
            padding: 1rem 1.5rem;
            border: none;
            border-radius: 8px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            font-family: 'Poppins', sans-serif;
        }

        .btn-back {
            background: #e5e7eb;
            color: #374151;
        }

        .btn-next,
        .btn-pay-now {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
        }

        .btn-back:hover {
            background: #d1d5db;
        }

        .btn-next:hover,
        .btn-pay-now:hover {
            opacity: 0.9;
            transform: translateY(-2px);
        }

        .btn-pay-now {
            background: linear-gradient(135deg, #10b981 0%, #059669 100%);
        }

        @media (max-width: 768px) {
            .event-details-content {
                grid-template-columns: 1fr;
                gap: 2rem;
            }

            .event-title {
                font-size: 2rem;
            }

            .booking-card {
                position: static;
            }

            .quick-book-modal {
                width: 95%;
                margin: 1rem;
            }

            .step-actions {
                flex-direction: column;
            }
        }
    </style>
</head>
<body>
    <!-- Navigation -->
    <nav class="eventisa-navbar">
        <div class="container nav-container">
            <div class="nav-brand">
                <a href="index.php">
                    <svg width="28" height="28" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <rect width="24" height="24" rx="4" fill="#8b5cf6"/>
                        <path d="M8 12L12 12M12 12V6M12 12V18M12 12H16" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                    <span class="brand-name">Eventisa</span>
                </a>
            </div>

            <div class="nav-menu">
                <a href="index.php" class="nav-link">Home</a>
                <a href="events.php" class="nav-link">Events</a>
                <a href="categories.php" class="nav-link">Categories</a>
                <a href="about.php" class="nav-link">About</a>
                <a href="contact.php" class="nav-link">Contact Us</a>
                <a href="FAQ.php" class="nav-link">FAQ</a>
            </div>

            <div class="nav-actions">
                <?php if (isset($_SESSION['user_id'])): ?>
                    <div class="user-info-nav">
                        <a href="<?php echo ($_SESSION['user_role'] === 'admin' || $_SESSION['user_role'] === 'manager' || $_SESSION['user_role'] === 'staff') ? 'Dashboard.php' : 'user-dashboard.php'; ?>" class="btn-login">
                            👤 <?php echo htmlspecialchars($_SESSION['user_name']); ?>
                        </a>
                        <a href="logout.php" class="btn-sign-in">Logout</a>
                    </div>
                <?php else: ?>
                    <a href="login.php" class="btn-sign-in">Sign in</a>
                <?php endif; ?>
            </div>
        </div>
    </nav>

    <!-- Event Details -->
    <section class="event-details">
        <div class="container">
            <div class="event-details-content">
                <div class="event-details-main">
                    <div class="event-hero">
                        <img src="<?php echo htmlspecialchars($event['image_url']); ?>" alt="<?php echo htmlspecialchars($event['title']); ?>" class="event-hero-image">
                        <div class="event-hero-overlay">
                            <div class="event-tag"><?php echo htmlspecialchars($event['category']); ?></div>
                            <div class="event-status-badge <?php echo $available_tickets <= 0 ? 'sold-out' : ''; ?>">
                                <?php echo $available_tickets <= 0 ? 'Sold Out' : ($sold_percentage > 80 ? 'Almost Sold Out' : 'Available'); ?>
                            </div>
                        </div>
                    </div>
                    
                    <div class="event-info">
                        <h1 class="event-title"><?php echo htmlspecialchars($event['title']); ?></h1>
                        <div class="event-meta">
                            <div class="meta-item">
                                <span class="meta-icon">📅</span>
                                <span class="meta-text"><?php echo date('F j, Y', strtotime($event['event_date'])); ?> at <?php echo date('g:i A', strtotime($event['event_time'])); ?></span>
                            </div>
                            <div class="meta-item">
                                <span class="meta-icon">📍</span>
                                <span class="meta-text"><?php echo htmlspecialchars($event['location']); ?></span>
                            </div>
                            <div class="meta-item">
                                <span class="meta-icon">🎫</span>
                                <span class="meta-text"><?php echo $available_tickets; ?> tickets available</span>
                            </div>
                        </div>
                        
                        <div class="event-description">
                            <h3>About This Event</h3>
                            <p><?php echo nl2br(htmlspecialchars($event['description'])); ?></p>
                        </div>
                        
                        <div class="event-features">
                            <h3>Event Highlights</h3>
                            <div class="features-grid">
                                <div class="feature-item">
                                    <span class="feature-icon">🎵</span>
                                    <span class="feature-text">Live Music</span>
                                </div>
                                <div class="feature-item">
                                    <span class="feature-icon">🍹</span>
                                    <span class="feature-text">Food & Drinks</span>
                                </div>
                                <div class="feature-item">
                                    <span class="feature-icon">📸</span>
                                    <span class="feature-text">Photo Opportunities</span>
                                </div>
                                <div class="feature-item">
                                    <span class="feature-icon">🅿️</span>
                                    <span class="feature-text">Parking Available</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="event-booking">
                    <div class="booking-card">
                        <div class="booking-header">
                            <h3>Book Your Tickets</h3>
                            <div class="price">৳<?php echo number_format($event['ticket_price']); ?></div>
                        </div>
                        
                        <div class="booking-progress">
                            <div class="progress-text"><?php echo round($sold_percentage); ?>% Sold</div>
                            <div class="progress-bar">
                                <div class="progress-fill" style="width: <?php echo $sold_percentage; ?>%"></div>
                            </div>
                            <div class="progress-stats"><?php echo $available_tickets; ?> of <?php echo $event['total_tickets']; ?> tickets available</div>
                        </div>
                        
                        <button class="btn-book-now" 
                                onclick="openQuickBook(<?php echo $event['id']; ?>, '<?php echo htmlspecialchars(addslashes($event['title'])); ?>', <?php echo $event['ticket_price']; ?>, <?php echo $available_tickets; ?>)"
                                <?php echo $available_tickets <= 0 ? 'disabled' : ''; ?>>
                            <?php echo $available_tickets <= 0 ? 'Sold Out' : 'Book Now'; ?>
                        </button>
                        
                        <?php if ($available_tickets <= 0): ?>
                            <p style="text-align: center; color: #ef4444; margin-top: 1rem; font-weight: 600;">
                                This event is completely sold out!
                            </p>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Related Events -->
    <?php if (!empty($related_events)): ?>
    <section class="section related-events">
        <div class="container">
            <h2 class="section-title">Related Events</h2>
            <div class="events-grid">
                <?php foreach($related_events as $related_event): 
                    $related_available = $related_event['total_tickets'] - $related_event['tickets_sold'];
                    $related_sold_percentage = $related_event['total_tickets'] > 0 ? ($related_event['tickets_sold'] / $related_event['total_tickets']) * 100 : 0;
                ?>
                <div class="event-card">
                    <div class="event-image">
                        <img src="<?php echo htmlspecialchars($related_event['image_url']); ?>" alt="<?php echo htmlspecialchars($related_event['title']); ?>">
                        <div class="event-tag"><?php echo htmlspecialchars($related_event['category']); ?></div>
                        <div class="event-status-badge <?php echo $related_available <= 0 ? 'sold-out' : ''; ?>" style="position: absolute; top: 1rem; right: 1rem;">
                            <?php echo $related_available <= 0 ? 'Sold Out' : ($related_sold_percentage > 80 ? 'Almost Sold Out' : 'Available'); ?>
                        </div>
                    </div>
                    <div class="event-content">
                        <div class="event-date"><?php echo date('M j, Y', strtotime($related_event['event_date'])); ?></div>
                        <h3 class="event-title"><?php echo htmlspecialchars($related_event['title']); ?></h3>
                        <p class="event-location">📍 <?php echo htmlspecialchars($related_event['location']); ?></p>
                        <div class="event-footer">
                            <div class="event-price">৳<?php echo number_format($related_event['ticket_price']); ?></div>
                            <a href="event-details.php?id=<?php echo $related_event['id']; ?>" class="btn-book">View Details</a>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>
    <?php endif; ?>

    <!-- Quick Book Modal -->
    <div class="quick-book-overlay" id="quickBookOverlay">
        <div class="quick-book-modal">
            <div class="quick-book-header">
                <h3 id="modalEventTitle">Book Your Tickets</h3>
                <button class="close-modal" onclick="closeQuickBook()">×</button>
            </div>
            
            <div class="booking-steps">
                <div class="step-indicator">
                    <div class="step active" data-step="1">
                        <span class="step-number">1</span>
                        <span class="step-label">Tickets</span>
                    </div>
                    <div class="step" data-step="2">
                        <span class="step-number">2</span>
                        <span class="step-label">Information</span>
                    </div>
                    <div class="step" data-step="3">
                        <span class="step-number">3</span>
                        <span class="step-label">Payment</span>
                    </div>
                </div>
            </div>

            <!-- Step 1: Ticket Selection -->
            <div class="booking-step active" id="step1">
                <div class="ticket-selection">
                    <div class="ticket-type-header">
                        <h4>General Admission</h4>
                        <div class="ticket-price">৳<span id="ticketBasePrice">0</span></div>
                    </div>
                    <div class="ticket-description">
                        <p>Standard entry ticket for the event</p>
                    </div>
                    
                    <div class="ticket-quantity-selector">
                        <label>Quantity</label>
                        <div class="quantity-controls">
                            <button type="button" class="quantity-btn minus" onclick="adjustQuantity(-1)">-</button>
                            <span class="quantity-display" id="quantityDisplay">1</span>
                            <input type="hidden" name="ticket_qty" id="ticketQty" value="1">
                            <button type="button" class="quantity-btn plus" onclick="adjustQuantity(1)">+</button>
                        </div>
                    </div>

                    <div class="ticket-summary">
                        <div class="summary-row">
                            <span><span id="summaryQuantity">1</span> x General Admission</span>
                            <span>৳<span id="summarySubtotal">0</span></span>
                        </div>
                        <div class="summary-row fee">
                            <span>Service Fee</span>
                            <span>৳5.00</span>
                        </div>
                        <div class="summary-row total">
                            <span>Total</span>
                            <span>৳<span id="summaryTotal">0</span></span>
                        </div>
                    </div>
                </div>

                <div class="step-actions">
                    <button type="button" class="btn-next" onclick="nextStep(2)">Continue to Information</button>
                </div>
            </div>

            <!-- Step 2: Customer Information -->
            <div class="booking-step" id="step2">
                <form id="customerInfoForm">
                    <div class="form-section">
                        <h4>Contact Information</h4>
                        <div class="form-group">
                            <label for="customer_name">Full Name *</label>
                            <input type="text" id="customer_name" name="customer_name" required 
                                   value="<?php echo isset($_SESSION['user_name']) ? htmlspecialchars($_SESSION['user_name']) : ''; ?>">
                        </div>
                        
                        <div class="form-group">
                            <label for="customer_email">Email Address *</label>
                            <input type="email" id="customer_email" name="customer_email" required
                                   value="<?php echo isset($_SESSION['user_email']) ? htmlspecialchars($_SESSION['user_email']) : ''; ?>">
                        </div>
                        
                        <div class="form-group">
                            <label for="customer_phone">Phone Number *</label>
                            <input type="tel" id="customer_phone" name="customer_phone" required>
                        </div>
                    </div>

                    <div class="form-section">
                        <h4>Additional Information</h4>
                        <div class="form-group">
                            <label for="special_requests">Special Requests (Optional)</label>
                            <textarea id="special_requests" name="special_requests" rows="3" placeholder="Any special requirements or requests..."></textarea>
                        </div>
                    </div>
                </form>

                <div class="step-actions">
                    <button type="button" class="btn-back" onclick="prevStep(1)">Back</button>
                    <button type="button" class="btn-next" onclick="validateStep2()">Continue to Payment</button>
                </div>
            </div>

            <!-- Step 3: Payment -->
            <div class="booking-step" id="step3">
                <div class="payment-section">
                    <h4>Order Summary</h4>
                    <div class="order-summary">
                        <div class="order-item">
                            <span id="orderEventTitle">Event Title</span>
                            <span><span id="orderQuantity">1</span> ticket(s)</span>
                        </div>
                        <div class="order-breakdown">
                            <div class="breakdown-row">
                                <span>Subtotal:</span>
                                <span>৳<span id="orderSubtotal">0</span></span>
                            </div>
                            <div class="breakdown-row">
                                <span>Service Fee:</span>
                                <span>৳5.00</span>
                            </div>
                            <div class="breakdown-row total">
                                <span>Total Amount:</span>
                                <span>৳<span id="orderTotal">0</span></span>
                            </div>
                        </div>
                    </div>

                    <div class="payment-methods">
                        <h4>Select Payment Method</h4>
                        <div class="payment-options">
                            <label class="payment-option">
                                <input type="radio" name="payment_method" value="card" checked>
                                <span class="payment-icon">💳</span>
                                <span>Credit/Debit Card</span>
                            </label>
                            <label class="payment-option">
                                <input type="radio" name="payment_method" value="bkash">
                                <span class="payment-icon">💰</span>
                                <span>bKash</span>
                            </label>
                            <label class="payment-option">
                                <input type="radio" name="payment_method" value="nagad">
                                <span class="payment-icon">💸</span>
                                <span>Nagad</span>
                            </label>
                        </div>
                    </div>

                    <div class="terms-agreement">
                        <label class="checkbox-label">
                            <input type="checkbox" id="agree_terms" required>
                            <span>I agree to the <a href="terms.php" target="_blank">Terms & Conditions</a> and <a href="privacy.php" target="_blank">Privacy Policy</a></span>
                        </label>
                    </div>
                </div>

                <div class="step-actions">
                    <button type="button" class="btn-back" onclick="prevStep(2)">Back</button>
                    <button type="button" class="btn-pay-now" onclick="processPayment()">Pay Now</button>
                </div>
            </div>

            <!-- Hidden form for final submission -->
            <form id="quickBookForm" method="POST" action="booking-process.php" style="display: none;">
                <input type="hidden" name="event_id" id="modalEventId">
                <input type="hidden" name="ticket_qty" id="finalTicketQty">
                <input type="hidden" name="customer_name" id="finalCustomerName">
                <input type="hidden" name="customer_email" id="finalCustomerEmail">
                <input type="hidden" name="customer_phone" id="finalCustomerPhone">
                <input type="hidden" name="special_requests" id="finalSpecialRequests">
                <input type="hidden" name="payment_method" id="finalPaymentMethod">
            </form>
        </div>
    </div>

    <!-- Footer -->
    <footer class="eventisa-footer">
        <div class="container">
            <div class="footer-grid">
                <div class="footer-section brand-info">
                    <div class="footer-brand">
                        <div class="logo-wrapper">E</div>
                        <span class="brand-name">Eventisa</span>
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
                <p>&copy; <?php echo date('Y'); ?> Eventisa | All rights reserved.</p>
            </div>
        </div>
    </footer>

    <script>
        // Booking Process Variables
        let currentStep = 1;
        let currentTicketPrice = 0;
        let maxTickets = 10;
        let ticketQuantity = 1;

        function openQuickBook(eventId, eventTitle, ticketPrice, availableTickets) {
            currentTicketPrice = ticketPrice;
            maxTickets = Math.min(availableTickets, 10);
            
            document.getElementById('modalEventId').value = eventId;
            document.getElementById('modalEventTitle').textContent = eventTitle;
            document.getElementById('ticketBasePrice').textContent = ticketPrice.toLocaleString();
            
            resetToStep(1);
            updateTicketDisplay();
            updateOrderSummary();
            
            document.getElementById('quickBookOverlay').style.display = 'flex';
        }

        function closeQuickBook() {
            document.getElementById('quickBookOverlay').style.display = 'none';
            resetToStep(1);
        }

        function resetToStep(step) {
            currentStep = step;
            
            document.querySelectorAll('.booking-step').forEach(stepEl => {
                stepEl.classList.remove('active');
            });
            
            document.getElementById(`step${step}`).classList.add('active');
            
            document.querySelectorAll('.step').forEach(stepEl => {
                stepEl.classList.remove('active');
                if (parseInt(stepEl.dataset.step) <= step) {
                    stepEl.classList.add('active');
                }
            });
        }

        function nextStep(step) {
            if (step > currentStep) {
                resetToStep(step);
            }
        }

        function prevStep(step) {
            if (step < currentStep) {
                resetToStep(step);
            }
        }

        function adjustQuantity(change) {
            const newQuantity = ticketQuantity + change;
            if (newQuantity >= 1 && newQuantity <= maxTickets) {
                ticketQuantity = newQuantity;
                updateTicketDisplay();
                updateOrderSummary();
            }
        }

        function updateTicketDisplay() {
            document.getElementById('quantityDisplay').textContent = ticketQuantity;
            document.getElementById('ticketQty').value = ticketQuantity;
            document.getElementById('summaryQuantity').textContent = ticketQuantity;
            
            const subtotal = currentTicketPrice * ticketQuantity;
            const total = subtotal + 5;
            
            document.getElementById('summarySubtotal').textContent = subtotal.toLocaleString();
            document.getElementById('summaryTotal').textContent = total.toLocaleString();
            
            document.querySelector('.quantity-btn.minus').disabled = ticketQuantity <= 1;
            document.querySelector('.quantity-btn.plus').disabled = ticketQuantity >= maxTickets;
        }

        function updateOrderSummary() {
            const subtotal = currentTicketPrice * ticketQuantity;
            const total = subtotal + 5;
            
            document.getElementById('orderEventTitle').textContent = document.getElementById('modalEventTitle').textContent;
            document.getElementById('orderQuantity').textContent = ticketQuantity;
            document.getElementById('orderSubtotal').textContent = subtotal.toLocaleString();
            document.getElementById('orderTotal').textContent = total.toLocaleString();
        }

        function validateStep2() {
            const name = document.getElementById('customer_name').value.trim();
            const email = document.getElementById('customer_email').value.trim();
            const phone = document.getElementById('customer_phone').value.trim();
            
            if (!name) {
                alert('Please enter your full name');
                document.getElementById('customer_name').focus();
                return;
            }
            
            if (!email) {
                alert('Please enter your email address');
                document.getElementById('customer_email').focus();
                return;
            }
            
            const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            if (!emailRegex.test(email)) {
                alert('Please enter a valid email address');
                document.getElementById('customer_email').focus();
                return;
            }
            
            if (!phone) {
                alert('Please enter your phone number');
                document.getElementById('customer_phone').focus();
                return;
            }
            
            nextStep(3);
        }

        function processPayment() {
            if (!document.getElementById('agree_terms').checked) {
                alert('Please agree to the Terms & Conditions and Privacy Policy');
                return;
            }
            
            const paymentMethod = document.querySelector('input[name="payment_method"]:checked').value;
            
            document.getElementById('finalTicketQty').value = ticketQuantity;
            document.getElementById('finalCustomerName').value = document.getElementById('customer_name').value;
            document.getElementById('finalCustomerEmail').value = document.getElementById('customer_email').value;
            document.getElementById('finalCustomerPhone').value = document.getElementById('customer_phone').value;
            document.getElementById('finalSpecialRequests').value = document.getElementById('special_requests').value;
            document.getElementById('finalPaymentMethod').value = paymentMethod;
            
            const payButton = document.querySelector('.btn-pay-now');
            payButton.textContent = 'Processing...';
            payButton.disabled = true;
            
            document.getElementById('quickBookForm').submit();
        }

        document.getElementById('quickBookOverlay').addEventListener('click', function(e) {
            if (e.target === this) {
                closeQuickBook();
            }
        });

        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                closeQuickBook();
            }
        });

        updateTicketDisplay();
    </script>
</body>
</html>