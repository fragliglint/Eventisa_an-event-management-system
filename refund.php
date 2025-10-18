<?php
session_start();
require_once 'config/database.php';

$database = new Database();
$db = $database->getConnection();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Refund Policy - Eventisa</title>
    <link rel="stylesheet" href="css/user-styles.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600;700;800&display=swap" rel="stylesheet">
    <style>
        .policy-section {
            padding: 4rem 0;
            background: #f8fafc;
            min-height: 100vh;
        }

        .policy-container {
            max-width: 1000px;
            margin: 0 auto;
            padding: 0 1rem;
        }

        .policy-header {
            text-align: center;
            margin-bottom: 3rem;
        }

        .policy-icon {
            font-size: 4rem;
            margin-bottom: 1rem;
            color: #667eea;
        }

        .policy-header h1 {
            font-size: 3rem;
            font-weight: 700;
            color: #1f2937;
            margin-bottom: 1rem;
        }

        .policy-header p {
            font-size: 1.2rem;
            color: #6b7280;
            max-width: 600px;
            margin: 0 auto;
        }

        .policy-card {
            background: white;
            border-radius: 20px;
            padding: 3rem;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
        }

        .last-updated {
            text-align: center;
            background: #f3f4f6;
            padding: 1rem;
            border-radius: 10px;
            margin-bottom: 2rem;
            color: #6b7280;
            font-weight: 500;
        }

        .policy-content {
            line-height: 1.8;
            color: #4b5563;
        }

        .policy-section-title {
            font-size: 1.8rem;
            font-weight: 600;
            color: #1f2937;
            margin: 2.5rem 0 1rem 0;
            padding-bottom: 0.5rem;
            border-bottom: 2px solid #e5e7eb;
        }

        .policy-section-title:first-child {
            margin-top: 0;
        }

        .policy-subtitle {
            font-size: 1.3rem;
            font-weight: 600;
            color: #374151;
            margin: 2rem 0 1rem 0;
        }

        .policy-list {
            margin: 1rem 0;
            padding-left: 1.5rem;
        }

        .policy-list li {
            margin-bottom: 0.75rem;
            color: #4b5563;
        }

        .refund-table {
            width: 100%;
            border-collapse: collapse;
            margin: 1.5rem 0;
            background: white;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        .refund-table th {
            background: #667eea;
            color: white;
            padding: 1rem;
            text-align: left;
            font-weight: 600;
        }

        .refund-table td {
            padding: 1rem;
            border-bottom: 1px solid #e5e7eb;
            color: #4b5563;
        }

        .refund-table tr:last-child td {
            border-bottom: none;
        }

        .refund-table tr:nth-child(even) {
            background: #f8fafc;
        }

        .refund-status {
            display: inline-block;
            padding: 0.25rem 0.75rem;
            border-radius: 20px;
            font-size: 0.875rem;
            font-weight: 600;
        }

        .status-eligible {
            background: #d1fae5;
            color: #065f46;
        }

        .status-not-eligible {
            background: #fee2e2;
            color: #dc2626;
        }

        .status-partial {
            background: #fef3c7;
            color: #d97706;
        }

        .refund-timeline {
            background: #f0f9ff;
            border-left: 4px solid #667eea;
            padding: 1.5rem;
            margin: 1.5rem 0;
            border-radius: 0 8px 8px 0;
        }

        .timeline-item {
            display: flex;
            align-items: flex-start;
            margin-bottom: 1rem;
        }

        .timeline-item:last-child {
            margin-bottom: 0;
        }

        .timeline-marker {
            width: 20px;
            height: 20px;
            background: #667eea;
            border-radius: 50%;
            margin-right: 1rem;
            margin-top: 0.25rem;
            flex-shrink: 0;
        }

        .timeline-content {
            flex: 1;
        }

        .contact-info {
            background: #f8fafc;
            padding: 2rem;
            border-radius: 12px;
            margin: 2rem 0;
            border: 1px solid #e5e7eb;
        }

        .contact-info h3 {
            color: #1f2937;
            margin-bottom: 1rem;
        }

        .back-to-top {
            position: fixed;
            bottom: 2rem;
            right: 2rem;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            width: 50px;
            height: 50px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            text-decoration: none;
            font-size: 1.5rem;
            box-shadow: 0 4px 15px rgba(102, 126, 234, 0.4);
            transition: all 0.3s ease;
            z-index: 1000;
        }

        .back-to-top:hover {
            transform: translateY(-3px);
            box-shadow: 0 6px 20px rgba(102, 126, 234, 0.6);
        }

        .quick-links {
            background: #f8fafc;
            padding: 2rem;
            border-radius: 12px;
            margin-bottom: 2rem;
        }

        .quick-links h3 {
            color: #1f2937;
            margin-bottom: 1rem;
        }

        .quick-links-list {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 1rem;
        }

        .quick-links-list a {
            color: #667eea;
            text-decoration: none;
            font-weight: 500;
            padding: 0.5rem;
            border-radius: 6px;
            transition: all 0.3s ease;
        }

        .quick-links-list a:hover {
            background: #e5e7eb;
            color: #5a67d8;
        }

        .important-note {
            background: linear-gradient(135deg, #fef3c7 0%, #fde68a 100%);
            border: 1px solid #f59e0b;
            padding: 1.5rem;
            border-radius: 8px;
            margin: 1.5rem 0;
        }

        .important-note h4 {
            color: #d97706;
            margin-bottom: 0.5rem;
        }

        @media (max-width: 768px) {
            .policy-card {
                padding: 2rem 1.5rem;
            }

            .policy-header h1 {
                font-size: 2.2rem;
            }

            .policy-section-title {
                font-size: 1.5rem;
            }

            .refund-table {
                font-size: 0.9rem;
            }

            .refund-table th,
            .refund-table td {
                padding: 0.75rem 0.5rem;
            }

            .back-to-top {
                bottom: 1rem;
                right: 1rem;
            }
        }

        .highlight {
            background: linear-gradient(135deg, #667eea15 0%, #764ba215 100%);
            padding: 0.2rem 0.5rem;
            border-radius: 4px;
            font-weight: 600;
            color: #667eea;
        }
    </style>
</head>
<body>
    <!-- Navigation -->
    <nav class="eventisa-navbar">
        <div class="container nav-container">
            <div class="nav-brand">
                <a href="index.php" aria-label="Eventisa home" class="brand-link">
                    <img src="asstes/logo1.png" alt="Eventisa logo" style="width:140px; height:auto; display:block; object-fit:contain;">
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

    <!-- Refund Policy Section -->
    <section class="policy-section">
        <div class="policy-container">
            <div class="policy-header">
                <div class="policy-icon">💸</div>
                <h1>Refund Policy</h1>
                <p>Learn about our refund procedures, eligibility criteria, and cancellation policies.</p>
            </div>

            <div class="policy-card">
                <div class="last-updated">
                    Last Updated: <?php echo date('F j, Y'); ?>
                </div>

                <div class="quick-links">
                    <h3>Quick Navigation</h3>
                    <div class="quick-links-list">
                        <a href="#general-policy">General Policy</a>
                        <a href="#cancellation">Event Cancellation</a>
                        <a href="#refund-eligibility">Refund Eligibility</a>
                        <a href="#processing">Refund Processing</a>
                        <a href="#exceptions">Exceptions</a>
                        <a href="#contact">Contact Support</a>
                    </div>
                </div>

                <div class="policy-content">
                    <div class="important-note">
                        <h4>⚠️ Important Notice</h4>
                        <p>All refund requests must be submitted through our official channels. Refund eligibility depends on the event organizer's policy and the timing of your request.</p>
                    </div>

                    <h2 class="policy-section-title" id="general-policy">General Refund Policy</h2>
                    <p>At Eventisa, we strive to provide fair and transparent refund policies. However, please note that refund availability is primarily determined by the individual event organizers. We act as an intermediary platform and facilitate refunds based on the organizer's policies.</p>

                    <h2 class="policy-section-title" id="cancellation">Event Cancellation & Postponement</h2>
                    
                    <h3 class="policy-subtitle">Event Cancellation</h3>
                    <p>If an event is canceled by the organizer, you are entitled to a full refund including all fees. The refund will be automatically processed to your original payment method within 7-14 business days.</p>

                    <h3 class="policy-subtitle">Event Postponement</h3>
                    <p>If an event is postponed:</p>
                    <ul class="policy-list">
                        <li>Your tickets will be valid for the new date</li>
                        <li>If you cannot attend the new date, you may be eligible for a refund</li>
                        <li>Refund requests for postponed events must be made within 14 days of the announcement</li>
                    </ul>

                    <h2 class="policy-section-title" id="refund-eligibility">Refund Eligibility Criteria</h2>
                    
                    <div class="refund-table">
                        <table>
                            <thead>
                                <tr>
                                    <th>Situation</th>
                                    <th>Refund Eligibility</th>
                                    <th>Refund Amount</th>
                                    <th>Timeframe</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>Event Cancelled by Organizer</td>
                                    <td><span class="refund-status status-eligible">Full Refund</span></td>
                                    <td>100% of ticket price + fees</td>
                                    <td>Within 14 business days</td>
                                </tr>
                                <tr>
                                    <td>Event Postponed (Cannot attend)</td>
                                    <td><span class="refund-status status-eligible">Full Refund</span></td>
                                    <td>100% of ticket price</td>
                                    <td>Request within 14 days of postponement</td>
                                </tr>
                                <tr>
                                    <td>Customer Cancellation (7+ days before event)</td>
                                    <td><span class="refund-status status-partial">Partial Refund</span></td>
                                    <td>80% of ticket price*</td>
                                    <td>Within 10 business days</td>
                                </tr>
                                <tr>
                                    <td>Customer Cancellation (3-7 days before event)</td>
                                    <td><span class="refund-status status-partial">Partial Refund</span></td>
                                    <td>50% of ticket price*</td>
                                    <td>Within 10 business days</td>
                                </tr>
                                <tr>
                                    <td>Customer Cancellation (Less than 3 days)</td>
                                    <td><span class="refund-status status-not-eligible">No Refund</span></td>
                                    <td>Not applicable</td>
                                    <td>Not applicable</td>
                                </tr>
                                <tr>
                                    <td>No Show</td>
                                    <td><span class="refund-status status-not-eligible">No Refund</span></td>
                                    <td>Not applicable</td>
                                    <td>Not applicable</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <p><small>* Service fees are non-refundable in customer-initiated cancellations.</small></p>

                    <h2 class="policy-section-title" id="processing">Refund Processing</h2>
                    
                    <div class="refund-timeline">
                        <h3>Refund Processing Timeline</h3>
                        <div class="timeline-item">
                            <div class="timeline-marker"></div>
                            <div class="timeline-content">
                                <strong>Step 1: Refund Request</strong>
                                <p>Submit your refund request through our website or contact support</p>
                            </div>
                        </div>
                        <div class="timeline-item">
                            <div class="timeline-marker"></div>
                            <div class="timeline-content">
                                <strong>Step 2: Verification</strong>
                                <p>We verify your eligibility and process the request (1-3 business days)</p>
                            </div>
                        </div>
                        <div class="timeline-item">
                            <div class="timeline-marker"></div>
                            <div class="timeline-content">
                                <strong>Step 3: Approval & Processing</strong>
                                <p>Refund is approved and sent to payment processor (1-2 business days)</p>
                            </div>
                        </div>
                        <div class="timeline-item">
                            <div class="timeline-marker"></div>
                            <div class="timeline-content">
                                <strong>Step 4: Bank Processing</strong>
                                <p>Your bank processes the refund (5-10 business days)</p>
                            </div>
                        </div>
                    </div>

                    <h3 class="policy-subtitle">Refund Methods</h3>
                    <p>Refunds are processed through the original payment method:</p>
                    <ul class="policy-list">
                        <li><span class="highlight">Credit/Debit Cards:</span> 5-10 business days</li>
                        <li><span class="highlight">bKash/Nagad:</span> 2-5 business days</li>
                        <li><span class="highlight">Bank Transfer:</span> 3-7 business days</li>
                    </ul>

                    <h2 class="policy-section-title" id="exceptions">Special Circumstances & Exceptions</h2>
                    
                    <h3 class="policy-subtitle">Medical Emergencies</h3>
                    <p>We understand that emergencies happen. In case of medical emergencies:</p>
                    <ul class="policy-list">
                        <li>Provide valid medical documentation</li>
                        <li>Request must be made within 48 hours of the event</li>
                        <li>Subject to approval by event organizer</li>
                        <li>May qualify for partial or full refund</li>
                    </ul>

                    <h3 class="policy-subtitle">Event Quality Issues</h3>
                    <p>If the event significantly differs from what was advertised:</p>
                    <ul class="policy-list">
                        <li>Submit detailed feedback and evidence</li>
                        <li>Request must be made within 24 hours of event completion</li>
                        <li>Case-by-case evaluation by our support team</li>
                    </ul>

                    <h2 class="policy-section-title" id="non-refundable">Non-Refundable Items</h2>
                    <p>The following are typically non-refundable:</p>
                    <ul class="policy-list">
                        <li>Service fees and processing charges</li>
                        <li>Donations or charity contributions</li>
                        <li>Digital products or downloaded content</li>
                        <li>Gift cards and voucher purchases</li>
                        <li>Special package deals marked "Non-refundable"</li>
                    </ul>

                    <h2 class="policy-section-title" id="contact">Contact & Support</h2>
                    <div class="contact-info">
                        <h3>Refund Request Process</h3>
                        <p>To request a refund, please contact our support team with the following information:</p>
                        <ul class="policy-list">
                            <li>Your invoice number</li>
                            <li>Event name and date</li>
                            <li>Reason for refund request</li>
                            <li>Supporting documentation (if applicable)</li>
                        </ul>
                        
                        <h3>Contact Information</h3>
                        <p>📧 Email: refunds@eventisa.com</p>
                        <p>📞 Phone: +88 018 35099 555 (Refund Department)</p>
                        <p>🕒 Support Hours: 9:00 AM - 6:00 PM (BST), Monday - Friday</p>
                        <p>🏠 Address: House 6, Road 16, Block D, Mirpur 6, Dhaka 1216</p>
                    </div>

                    <div class="important-note">
                        <h4>📝 Important Reminder</h4>
                        <p>Refund policies may vary by event organizer. Always check the specific refund policy mentioned on the event page before purchasing tickets. This general policy serves as a guideline, but the event organizer's policy takes precedence.</p>
                    </div>
                </div>
            </div>
        </div>

        <a href="#" class="back-to-top" title="Back to Top">↑</a>
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
                    <a href="refund.php" class="active">Refund Policy</a>
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
        // Smooth scrolling for anchor links
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function (e) {
                e.preventDefault();
                const target = document.querySelector(this.getAttribute('href'));
                if (target) {
                    target.scrollIntoView({
                        behavior: 'smooth',
                        block: 'start'
                    });
                }
            });
        });

        // Back to top button visibility
        window.addEventListener('scroll', function() {
            const backToTop = document.querySelector('.back-to-top');
            if (window.pageYOffset > 300) {
                backToTop.style.display = 'flex';
            } else {
                backToTop.style.display = 'none';
            }
        });

        // Print functionality
        function printPolicy() {
            window.print();
        }
    </script>
</body>
</html>