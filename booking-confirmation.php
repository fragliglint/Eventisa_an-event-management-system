<?php
session_start();
require_once 'config/database.php';

if (!isset($_GET['invoice_id']) || empty($_GET['invoice_id'])) {
    header("Location: events.php");
    exit;
}

$invoice_id = trim($_GET['invoice_id']);

// Validate invoice ID format
if (!preg_match('/^INV\d{17}$/', $invoice_id)) {
    header("Location: events.php");
    exit;
}

try {
    $database = new Database();
    $db = $database->getConnection();
    
    // Get booking and invoice details with proper error handling
    $stmt = $db->prepare("
        SELECT b.*, i.*, e.title, e.location, e.event_date, e.event_time, e.image_url
        FROM bookings b 
        JOIN invoices i ON b.invoice_id = i.invoice_number 
        JOIN events e ON b.event_id = e.id 
        WHERE b.invoice_id = ?
    ");
    $stmt->execute([$invoice_id]);
    $booking = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$booking) {
        header("Location: events.php");
        exit;
    }
} catch (Exception $e) {
    error_log("Database error in booking confirmation: " . $e->getMessage());
    header("Location: events.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Booking Confirmed - Eventisa</title>
    <link rel="stylesheet" href="css/user-styles.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600;700;800&display=swap" rel="stylesheet">
    <style>
        .confirmation-section {
            padding: 4rem 0;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
        }

        .confirmation-container {
            max-width: 1000px;
            margin: 0 auto;
            padding: 0 1rem;
        }

        .confirmation-card {
            background: white;
            border-radius: 20px;
            padding: 3rem;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.1);
            position: relative;
            overflow: hidden;
        }

        .confirmation-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 4px;
            background: linear-gradient(135deg, #10b981 0%, #059669 100%);
        }

        .success-header {
            text-align: center;
            margin-bottom: 3rem;
        }

        .success-icon {
            width: 80px;
            height: 80px;
            background: linear-gradient(135deg, #10b981 0%, #059669 100%);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 2rem;
            font-size: 2.5rem;
            color: white;
        }

        .success-header h1 {
            font-size: 2.5rem;
            font-weight: 700;
            color: #1f2937;
            margin-bottom: 1rem;
        }

        .success-header p {
            font-size: 1.2rem;
            color: #6b7280;
            max-width: 600px;
            margin: 0 auto;
        }

        .booking-content {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 3rem;
            margin-bottom: 3rem;
        }

        .event-preview {
            text-align: center;
        }

        .event-image {
            width: 100%;
            height: 250px;
            object-fit: cover;
            border-radius: 12px;
            margin-bottom: 1.5rem;
        }

        .event-title {
            font-size: 1.5rem;
            font-weight: 600;
            color: #1f2937;
            margin-bottom: 0.5rem;
        }

        .event-date {
            color: #667eea;
            font-weight: 600;
            font-size: 1.1rem;
        }

        .booking-details {
            background: #f8fafc;
            border-radius: 12px;
            padding: 2rem;
        }

        .detail-section {
            margin-bottom: 2rem;
        }

        .detail-section:last-child {
            margin-bottom: 0;
        }

        .detail-section h3 {
            font-size: 1.25rem;
            font-weight: 600;
            color: #1f2937;
            margin-bottom: 1rem;
            padding-bottom: 0.5rem;
            border-bottom: 2px solid #e5e7eb;
        }

        .detail-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 0.75rem 0;
            border-bottom: 1px solid #e5e7eb;
        }

        .detail-row:last-child {
            border-bottom: none;
        }

        .detail-label {
            font-weight: 500;
            color: #6b7280;
        }

        .detail-value {
            font-weight: 600;
            color: #1f2937;
            text-align: right;
        }

        .status-badge {
            background: linear-gradient(135deg, #10b981 0%, #059669 100%);
            color: white;
            padding: 0.5rem 1rem;
            border-radius: 20px;
            font-weight: 600;
            font-size: 0.875rem;
        }

        .price-summary {
            background: #f8fafc;
            border-radius: 12px;
            padding: 2rem;
            margin-top: 2rem;
        }

        .price-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 0.75rem 0;
            border-bottom: 1px solid #e5e7eb;
        }

        .price-row:last-child {
            border-bottom: none;
        }

        .price-row.total {
            border-top: 2px solid #e5e7eb;
            margin-top: 0.5rem;
            padding-top: 1rem;
            font-weight: 700;
            font-size: 1.2rem;
            color: #1f2937;
        }

        .ticket-info {
            background: linear-gradient(135deg, #d1fae5 0%, #a7f3d0 100%);
            border: 1px solid #10b981;
            border-radius: 12px;
            padding: 2rem;
            margin: 2rem 0;
            text-align: center;
        }

        .ticket-info h4 {
            color: #065f46;
            font-size: 1.25rem;
            margin-bottom: 1rem;
            font-weight: 600;
        }

        .ticket-info p {
            color: #047857;
            margin: 0.5rem 0;
            font-size: 1rem;
        }

        .confirmation-actions {
            display: flex;
            gap: 1rem;
            justify-content: center;
            flex-wrap: wrap;
            margin-top: 3rem;
        }

        .btn {
            padding: 1rem 2rem;
            border-radius: 10px;
            text-decoration: none;
            font-weight: 600;
            font-size: 1rem;
            transition: all 0.3s ease;
            text-align: center;
            border: none;
            cursor: pointer;
            font-family: 'Poppins', sans-serif;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            min-width: 160px;
            justify-content: center;
        }

        .btn-primary {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
        }

        .btn-secondary {
            background: #f3f4f6;
            color: #374151;
            border: 2px solid #e5e7eb;
        }

        .btn-success {
            background: linear-gradient(135deg, #10b981 0%, #059669 100%);
            color: white;
        }

        .btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.15);
        }

        .invoice-number {
            text-align: center;
            margin-bottom: 2rem;
            padding: 1rem;
            background: #f8fafc;
            border-radius: 10px;
            border: 2px dashed #e5e7eb;
        }

        .invoice-number h3 {
            color: #6b7280;
            font-size: 1rem;
            margin-bottom: 0.5rem;
            font-weight: 500;
        }

        .invoice-number .number {
            font-size: 1.5rem;
            font-weight: 700;
            color: #667eea;
            letter-spacing: 1px;
        }

        @media (max-width: 768px) {
            .booking-content {
                grid-template-columns: 1fr;
                gap: 2rem;
            }

            .confirmation-card {
                padding: 2rem 1.5rem;
            }

            .confirmation-actions {
                flex-direction: column;
            }

            .btn {
                width: 100%;
            }

            .success-header h1 {
                font-size: 2rem;
            }

            .success-header p {
                font-size: 1.1rem;
            }
        }

        @media print {
            .eventisa-navbar,
            .eventisa-footer,
            .confirmation-actions {
                display: none !important;
            }

            .confirmation-section {
                background: white !important;
                padding: 0 !important;
            }

            .confirmation-card {
                box-shadow: none !important;
                border: 2px solid #e5e7eb !important;
            }

            .btn {
                display: none !important;
            }
        }

        .confirmation-badge {
            position: absolute;
            top: 2rem;
            right: 2rem;
            background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);
            color: white;
            padding: 0.5rem 1rem;
            border-radius: 20px;
            font-weight: 600;
            font-size: 0.875rem;
            transform: rotate(5deg);
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

    <!-- Confirmation Section -->
    <section class="confirmation-section">
        <div class="confirmation-container">
            <div class="confirmation-card">
                <div class="confirmation-badge">
                    🎉 Booking Confirmed!
                </div>
                
                <div class="success-header">
                    <div class="success-icon">✓</div>
                    <h1>Booking Successful!</h1>
                    <p>Thank you for your booking. Your tickets have been reserved successfully and confirmation has been sent to your email.</p>
                </div>

                <div class="invoice-number">
                    <h3>INVOICE NUMBER</h3>
                    <div class="number"><?php echo htmlspecialchars($booking['invoice_id']); ?></div>
                </div>

                <div class="booking-content">
                    <div class="event-preview">
                        <img src="<?php echo htmlspecialchars($booking['image_url']); ?>" alt="<?php echo htmlspecialchars($booking['title']); ?>" class="event-image">
                        <h2 class="event-title"><?php echo htmlspecialchars($booking['title']); ?></h2>
                        <div class="event-date">
                            📅 <?php echo date('F j, Y', strtotime($booking['event_date'])); ?> at <?php echo date('g:i A', strtotime($booking['event_time'])); ?>
                        </div>
                        <p style="color: #6b7280; margin-top: 0.5rem;">
                            📍 <?php echo htmlspecialchars($booking['location']); ?>
                        </p>
                    </div>

                    <div class="booking-details">
                        <div class="detail-section">
                            <h3>Booking Information</h3>
                            <div class="detail-row">
                                <span class="detail-label">Customer Name</span>
                                <span class="detail-value"><?php echo htmlspecialchars($booking['customer_name']); ?></span>
                            </div>
                            <div class="detail-row">
                                <span class="detail-label">Email Address</span>
                                <span class="detail-value"><?php echo htmlspecialchars($booking['customer_email']); ?></span>
                            </div>
                            <div class="detail-row">
                                <span class="detail-label">Phone Number</span>
                                <span class="detail-value"><?php echo htmlspecialchars($booking['customer_phone']); ?></span>
                            </div>
                            <div class="detail-row">
                                <span class="detail-label">Tickets Quantity</span>
                                <span class="detail-value"><?php echo htmlspecialchars($booking['tickets_qty']); ?> tickets</span>
                            </div>
                        </div>

                        <div class="detail-section">
                            <h3>Booking Status</h3>
                            <div class="detail-row">
                                <span class="detail-label">Status</span>
                                <span class="status-badge"><?php echo htmlspecialchars($booking['status']); ?></span>
                            </div>
                            <div class="detail-row">
                                <span class="detail-label">Booking Date</span>
                                <span class="detail-value"><?php echo date('M j, Y g:i A', strtotime($booking['booking_date'])); ?></span>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="price-summary">
                    <h3 style="margin-top: 0; margin-bottom: 1.5rem;">Payment Summary</h3>
                    <div class="price-row">
                        <span>Subtotal (<?php echo htmlspecialchars($booking['tickets_qty']); ?> tickets)</span>
                        <span>৳<?php echo number_format($booking['subtotal'], 2); ?></span>
                    </div>
                    <div class="price-row">
                        <span>Tax (10%)</span>
                        <span>৳<?php echo number_format($booking['tax_amount'], 2); ?></span>
                    </div>
                    <div class="price-row">
                        <span>Service Fee</span>
                        <span>৳<?php echo number_format($booking['fee_amount'], 2); ?></span>
                    </div>
                    <div class="price-row total">
                        <span>Total Amount Paid</span>
                        <span>৳<?php echo number_format($booking['total_amount'], 2); ?></span>
                    </div>
                </div>

                <div class="ticket-info">
                    <h4>📧 Confirmation Sent</h4>
                    <p>A confirmation email has been sent to <strong><?php echo htmlspecialchars($booking['customer_email']); ?></strong></p>
                    <p>Please present this confirmation or your e-tickets at the event entrance.</p>
                    <p><small>Your e-tickets will be emailed to you within 24 hours.</small></p>
                </div>

                <div class="confirmation-actions">
                    <a href="events.php" class="btn btn-secondary">
                        🔍 Browse More Events
                    </a>
                    <?php if (isset($_SESSION['user_id'])): ?>
                        <a href="<?php echo ($_SESSION['user_role'] === 'admin' || $_SESSION['user_role'] === 'manager' || $_SESSION['user_role'] === 'staff') ? 'Dashboard.php' : 'user-dashboard.php'; ?>" class="btn btn-primary">
                            📊 Go to Dashboard
                        </a>
                    <?php else: ?>
                        <a href="index.php" class="btn btn-primary">
                            🏠 Back to Home
                        </a>
                    <?php endif; ?>
                    <button onclick="window.print()" class="btn btn-success">
                        🖨️ Print Confirmation
                    </button>
                </div>
            </div>
        </div>
    </section>

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
        // Auto-scroll to top when page loads
        window.onload = function() {
            window.scrollTo(0, 0);
        };

        // Add confetti effect for celebration
        function createConfetti() {
            const colors = ['#667eea', '#764ba2', '#10b981', '#f59e0b', '#ef4444'];
            const confettiCount = 100;
            
            for (let i = 0; i < confettiCount; i++) {
                const confetti = document.createElement('div');
                confetti.style.position = 'fixed';
                confetti.style.width = '10px';
                confetti.style.height = '10px';
                confetti.style.background = colors[Math.floor(Math.random() * colors.length)];
                confetti.style.borderRadius = '50%';
                confetti.style.left = Math.random() * 100 + 'vw';
                confetti.style.top = '-10px';
                confetti.style.opacity = '0.8';
                confetti.style.zIndex = '9999';
                confetti.style.pointerEvents = 'none';
                document.body.appendChild(confetti);
                
                const animation = confetti.animate([
                    { transform: 'translateY(0) rotate(0deg)', opacity: 1 },
                    { transform: `translateY(${window.innerHeight + 100}px) rotate(${Math.random() * 360}deg)`, opacity: 0 }
                ], {
                    duration: 3000 + Math.random() * 2000,
                    easing: 'cubic-bezier(0.1, 0.8, 0.3, 1)'
                });
                
                animation.onfinish = () => confetti.remove();
            }
        }

        // Trigger confetti on page load
        setTimeout(createConfetti, 500);

        // Print functionality
        window.onbeforeprint = function() {
            document.querySelector('.eventisa-navbar').style.display = 'none';
            document.querySelector('.eventisa-footer').style.display = 'none';
            document.querySelector('.confirmation-actions').style.display = 'none';
            document.querySelector('.confirmation-badge').style.display = 'none';
        };
        
        window.onafterprint = function() {
            document.querySelector('.eventisa-navbar').style.display = '';
            document.querySelector('.eventisa-footer').style.display = '';
            document.querySelector('.confirmation-actions').style.display = '';
            document.querySelector('.confirmation-badge').style.display = '';
        };
    </script>
</body>
</html>