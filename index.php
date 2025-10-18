<?php
session_start();
require_once 'config/database.php';

$database = new Database();
$db = $database->getConnection();

// Get featured events with error handling
try {
    $featured_events = $db->query("SELECT * FROM events WHERE status='Active' ORDER BY event_date ASC LIMIT 8")->fetchAll(PDO::FETCH_ASSOC);
    $upcoming_events = $db->query("SELECT * FROM events WHERE status='Active' AND event_date > NOW() ORDER BY event_date ASC LIMIT 8")->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $featured_events = [];
    $upcoming_events = [];
    error_log("Database error: " . $e->getMessage());
}

// Activity list (icon + name)
$activities = [
    ['icon' => '🏆', 'name' => 'Competitions'],
    ['icon' => '👠', 'name' => 'Fashion Shows'],
    ['icon' => '🎙️', 'name' => 'Conferences'],
    ['icon' => '👨‍🏫', 'name' => 'Seminars'],
    ['icon' => '🤝', 'name' => 'Reunions'],
    ['icon' => '🖼️', 'name' => 'Exhibitions'],
    ['icon' => '🚀', 'name' => 'Launching'],
    ['icon' => '🎤', 'name' => 'Stand-up'],
];

// Promo slides
$promo_slides = [
    [
        'title' => '4TH LFB LEADERSHIP EXCELLENCE SUMMIT 2025',
        'subtitle' => 'ON 6 NOVEMBER 2025, THURSDAY AT SHERATON DHAKA',
        'image_url' => 'https://images.unsplash.com/photo-1540575467063-178a50c2df87?auto=format&fit=crop&w=1200&q=80',
        'cta_url' => '#'
    ],
    [
        'title' => 'Get Your Desired Event Pass!',
        'subtitle' => 'Explore the universe of events.',
        'image_url' => 'https://images.unsplash.com/photo-1501281668745-f7f57925c3b4?auto=format&fit=crop&w=1200&q=80',
        'cta_url' => 'events.php'
    ],
    [
        'title' => 'Summer Music Festival 2024',
        'subtitle' => 'Biggest music event of the year',
        'image_url' => 'https://images.unsplash.com/photo-1493225457124-a3eb161ffa5f?auto=format&fit=crop&w=1200&q=80',
        'cta_url' => 'events.php'
    ],
];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Eventisa - Buy Tickets Online</title>
    <link rel="stylesheet" href="css/user-styles.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600;700;800&display=swap" rel="stylesheet">
    <script src="js/user-animations.js" defer></script>
    <style>
        .event-card {
            position: relative;
            overflow: hidden;
        }
        
        .book-now-btn {
            position: absolute;
            bottom: 20px;
            right: 20px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 25px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            opacity: 0;
            transform: translateY(10px);
            z-index: 10;
            font-family: 'Poppins', sans-serif;
        }
        
        .event-card:hover .book-now-btn {
            opacity: 1;
            transform: translateY(0);
        }
        
        .book-now-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(102, 126, 234, 0.4);
        }
        
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

        @media (max-width: 640px) {
            .quick-book-modal {
                width: 95%;
                margin: 1rem;
            }
            
            .quick-book-header,
            .booking-steps,
            .booking-step {
                padding: 1rem;
            }
            
            .step-actions {
                flex-direction: column;
            }
        }
        .features-grid {
            display: flex;
            flex-direction: column;
            gap: 2rem;
        }

        .feature-row {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 2rem;
        }

        .feature-card {
            background: white;
            padding: 2rem;
            border-radius: 12px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            text-align: center;
            transition: transform 0.3s ease;
        }

        .feature-card:hover {
            transform: translateY(-5px);
        }

        .feature-icon {
            font-size: 3rem;
            margin-bottom: 1rem;
        }

        .feature-card h3 {
            margin-bottom: 1rem;
            color: #333;
        }

        .feature-card p {
            color: #666;
            line-height: 1.6;
        }

        /* Responsive design */
        @media (max-width: 768px) {
            .feature-row {
                grid-template-columns: 1fr;
                gap: 1.5rem;
            }
        }
    </style>
</head>
<body>
    <!-- Navigation -->
    <nav class="eventisa-navbar">
        <div class="container nav-container">
            <div class="nav-brand">
                <a href="index.php" class="brand-link" aria-label="Eventisa home">
                    <!-- replace text brand with a larger logo that uses the full brand area -->
                    <img src="asstes/logo1.png" alt="Eventisa logo" style="width:140px; height:auto; display:block; object-fit:contain;">
                </a>
            </div>

            <div class="nav-menu">
                <a href="index.php" class="nav-link active">Home</a>
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

    <!-- Banner Slider -->
    <section class="eventisa-banner-slider">
        <div class="slider-wrapper">
            <?php foreach ($promo_slides as $index => $slide): ?>
                <div class="slide <?php echo $index === 0 ? 'active' : ''; ?>" data-slide="<?php echo $index; ?>">
                    <div class="slide-background" style="background-image: url('<?php echo htmlspecialchars($slide['image_url']); ?>');"></div>
                    <div class="slide-overlay"></div>
                    
                    <div class="slide-content container">
                        <?php if ($index === 0): ?>
                            <div class="event-details-box">
                                <div class="logo-sm">E</div>
                                <h1 class="slide-title"><?php echo htmlspecialchars($slide['title']); ?></h1>
                                <p class="slide-subtitle"><?php echo htmlspecialchars($slide['subtitle']); ?></p>
                                <div class="registration-details">
                                    <div class="detail-box">
                                        <p>REGISTRATION FEE: BDT 5000/-</p>
                                        <p>LAST DATE FOR REGISTRATION</p>
                                        <p class="date-highlight">OCTOBER 20, 2025</p>
                                    </div>
                                </div>
                            </div>
                        <?php else: ?>
                            <div class="generic-cta-box">
                                <h2 class="cta-title"><?php echo htmlspecialchars($slide['title']); ?></h2>
                                <p class="cta-subtitle"><?php echo htmlspecialchars($slide['subtitle']); ?></p>
                                <a href="<?php echo htmlspecialchars($slide['cta_url']); ?>" class="btn-explore">Explore <span class="arrow-right">→</span></a>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endforeach; ?>

            <button class="slider-prev" aria-label="Previous slide">←</button>
            <button class="slider-next" aria-label="Next slide">→</button>
        </div>

        <div class="slider-dots">
            <?php for ($i = 0; $i < count($promo_slides); $i++): ?>
                <button class="dot <?php echo $i === 0 ? 'active' : ''; ?>" data-slide="<?php echo $i; ?>"></button>
            <?php endfor; ?>
        </div>
    </section>

    <!-- Activities Section -->
    <section class="section activity-icons-section">
        <div class="container activity-slider-wrapper">
            <button class="activity-prev-btn" aria-hidden="true">←</button>
            <div class="activity-list">
                <?php foreach ($activities as $act): ?>
                    <a href="events.php?category=<?php echo urlencode($act['name']); ?>" class="activity-card scroll-reveal">
                        <div class="activity-icon"><?php echo $act['icon']; ?></div>
                        <h3 class="activity-name"><?php echo htmlspecialchars($act['name']); ?></h3>
                    </a>
                <?php endforeach; ?>
            </div>
            <button class="activity-next-btn" aria-hidden="true">→</button>
        </div>
    </section>

    <!-- Upcoming Events Section -->
    <section class="section upcoming-events-section">
        <div class="container">
            <div class="section-header">
                <h2 class="section-title">Explore Upcomings!</h2>
                <p class="section-subtitle">Explore the Universe of Events at Your Fingertips.</p>
            </div>

            <div class="events-grid upcoming-grid">
                <?php foreach ($upcoming_events as $event): 
                    $available_tickets = $event['total_tickets'] - $event['tickets_sold'];
                ?>
                    <div class="event-card eventisa-card scroll-reveal">
                        <a href="event-details.php?id=<?php echo $event['id']; ?>">
                            <div class="card-image-wrapper">
                                <img src="<?php echo htmlspecialchars($event['image_url']); ?>" alt="<?php echo htmlspecialchars($event['title']); ?>">
                                <span class="event-tag category-tag"><?php echo htmlspecialchars($event['category']); ?></span>
                                <span class="event-tag live-tag">Live Now</span>
                            </div>
                            <div class="card-content">
                                <div class="event-date-time"><?php echo date('d M', strtotime($event['event_date'])); ?> | <?php echo date('g:i A', strtotime($event['event_time'])); ?></div>
                                <h3 class="event-title"><?php echo htmlspecialchars($event['title']); ?></h3>
                                <p class="event-location">📍 <?php echo htmlspecialchars($event['location']); ?></p>
                                <p class="event-price-from">Price starts from ৳<?php echo number_format($event['ticket_price']); ?></p>
                                <p class="ticket-availability"><?php echo $available_tickets; ?> tickets available</p>
                            </div>
                        </a>
                        <button class="book-now-btn" 
                                onclick="openQuickBook(<?php echo $event['id']; ?>, '<?php echo htmlspecialchars(addslashes($event['title'])); ?>', <?php echo $event['ticket_price']; ?>, <?php echo $available_tickets; ?>)">
                            Book Now
                        </button>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>

    <!-- Featured Events Section -->
    <section class="section flagship-events-section">
        <div class="container">
            <div class="section-header">
                <h2 class="section-title">Featured Events: Made Easy with Eventisa Ticketing</h2>
                <p class="section-subtitle">We're proud to showcase the success of our previous flagship events.</p>
            </div>
            <div class="events-grid flagship-grid">
                <?php foreach ($featured_events as $event): 
                    $available_tickets = $event['total_tickets'] - $event['tickets_sold'];
                ?>
                    <div class="event-card review-card scroll-reveal">
                        <a href="event-details.php?id=<?php echo $event['id']; ?>">
                            <img src="<?php echo htmlspecialchars($event['image_url']); ?>" alt="<?php echo htmlspecialchars($event['title']); ?>">
                        </a>
                        <button class="book-now-btn" 
                                onclick="openQuickBook(<?php echo $event['id']; ?>, '<?php echo htmlspecialchars(addslashes($event['title'])); ?>', <?php echo $event['ticket_price']; ?>, <?php echo $available_tickets; ?>)">
                            Book Now
                        </button>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>

    <!-- Offerings Section -->
    <section class="section offerings-section">
        <div class="container">
            <div class="section-header">
                <h2 class="section-title">Our Offerings</h2>
                <p class="section-subtitle">Explore the key features that make Eventisa the perfect choice for event organizers.</p>
            </div>
            <div class="features-grid">
                <div class="feature-row">
                    <div class="feature-card scroll-reveal">
                        <div class="feature-icon">🎫</div>
                        <h3>Streamlined Event Discovery</h3>
                        <p>Explore and secure tickets for diverse events seamlessly from any device.</p>
                    </div>
                    <div class="feature-card scroll-reveal">
                        <div class="feature-icon">⚡</div>
                        <h3>Instant Digital Delivery</h3>
                        <p>Get your tickets immediately via email or WhatsApp after purchase.</p>
                    </div>
                    <div class="feature-card scroll-reveal">
                        <div class="feature-icon">💳</div>
                        <h3>Flexible Payment Options</h3>
                        <p>Choose from bKash, Nagad, Upay, Visa, Mastercard and other payment methods.</p>
                    </div>
                </div>
                <div class="feature-row">
                    <div class="feature-card scroll-reveal">
                        <div class="feature-icon">📊</div>
                        <h3>Comprehensive Event Analytics</h3>
                        <p>Gain insights with detailed event management and attendance analytics.</p>
                    </div>
                    <div class="feature-card scroll-reveal">
                        <div class="feature-icon">📱</div>
                        <h3>Intuitive Event Management</h3>
                        <p>Manage all your event details and ticket sales through our comprehensive dashboard.</p>
                    </div>
                    <div class="feature-card scroll-reveal">
                        <div class="feature-icon">🔍</div>
                        <h3>Efficient Entry Verification</h3>
                        <p>Quick and reliable ticket scanning for smooth event entry experiences.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

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
                        <!-- use full logo image in footer instead of text -->
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
                <p>&copy; <?php echo date('Y'); ?> Eventisa | All rights reserved.</p>
            </div>
        </div>
    </footer>

    <script>
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