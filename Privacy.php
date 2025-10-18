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
    <title>Privacy Policy - Eventisa</title>
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

        .policy-note {
            background: #f0f9ff;
            border-left: 4px solid #667eea;
            padding: 1.5rem;
            margin: 1.5rem 0;
            border-radius: 0 8px 8px 0;
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

        .policy-table {
            width: 100%;
            border-collapse: collapse;
            margin: 1.5rem 0;
            background: white;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        .policy-table th {
            background: #667eea;
            color: white;
            padding: 1rem;
            text-align: left;
            font-weight: 600;
        }

        .policy-table td {
            padding: 1rem;
            border-bottom: 1px solid #e5e7eb;
            color: #4b5563;
        }

        .policy-table tr:last-child td {
            border-bottom: none;
        }

        .policy-table tr:nth-child(even) {
            background: #f8fafc;
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

            .policy-table {
                font-size: 0.9rem;
            }

            .policy-table th,
            .policy-table td {
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

    <!-- Privacy Policy Section -->
    <section class="policy-section">
        <div class="policy-container">
            <div class="policy-header">
                <div class="policy-icon">🔒</div>
                <h1>Privacy Policy</h1>
                <p>Your privacy is important to us. Learn how we collect, use, and protect your personal information.</p>
            </div>

            <div class="policy-card">
                <div class="last-updated">
                    Last Updated: <?php echo date('F j, Y'); ?>
                </div>

                <div class="quick-links">
                    <h3>Quick Navigation</h3>
                    <div class="quick-links-list">
                        <a href="#information-collection">Information We Collect</a>
                        <a href="#data-usage">How We Use Your Data</a>
                        <a href="#data-sharing">Data Sharing</a>
                        <a href="#data-security">Data Security</a>
                        <a href="#your-rights">Your Rights</a>
                        <a href="#cookies">Cookies</a>
                        <a href="#contact">Contact Us</a>
                    </div>
                </div>

                <div class="policy-content">
                    <h2 class="policy-section-title" id="introduction">Introduction</h2>
                    <p>Welcome to Eventisa. We are committed to protecting your privacy and ensuring the security of your personal information. This Privacy Policy explains how we collect, use, disclose, and safeguard your information when you use our website and services.</p>

                    <h2 class="policy-section-title" id="information-collection">Information We Collect</h2>
                    
                    <h3 class="policy-subtitle">Personal Information</h3>
                    <p>We collect personal information that you voluntarily provide to us when you:</p>
                    <ul class="policy-list">
                        <li>Create an account on our platform</li>
                        <li>Purchase tickets for events</li>
                        <li>Contact our customer support</li>
                        <li>Subscribe to our newsletter</li>
                        <li>Participate in surveys or promotions</li>
                    </ul>

                    <h3 class="policy-subtitle">Information Collected Automatically</h3>
                    <p>When you visit our website, we may automatically collect certain information, including:</p>
                    <ul class="policy-list">
                        <li>IP address and location data</li>
                        <li>Browser type and version</li>
                        <li>Device information</li>
                        <li>Pages visited and time spent on pages</li>
                        <li>Referring website addresses</li>
                    </ul>

                    <div class="policy-table">
                        <table>
                            <thead>
                                <tr>
                                    <th>Type of Information</th>
                                    <th>Purpose of Collection</th>
                                    <th>Legal Basis</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>Name, Email, Phone</td>
                                    <td>Account creation, ticket delivery, customer support</td>
                                    <td>Contractual necessity</td>
                                </tr>
                                <tr>
                                    <td>Payment Information</td>
                                    <td>Processing ticket purchases</td>
                                    <td>Contractual necessity</td>
                                </tr>
                                <tr>
                                    <td>Event Preferences</td>
                                    <td>Personalized recommendations</td>
                                    <td>Legitimate interest</td>
                                </tr>
                                <tr>
                                    <td>Usage Data</td>
                                    <td>Website improvement and analytics</td>
                                    <td>Legitimate interest</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <h2 class="policy-section-title" id="data-usage">How We Use Your Information</h2>
                    <p>We use the information we collect for various purposes, including:</p>
                    <ul class="policy-list">
                        <li>Processing and managing your ticket purchases</li>
                        <li>Providing customer support and responding to inquiries</li>
                        <li>Sending important updates about your bookings</li>
                        <li>Personalizing your experience on our platform</li>
                        <li>Improving our website and services</li>
                        <li>Sending marketing communications (with your consent)</li>
                        <li>Preventing fraud and ensuring security</li>
                    </ul>

                    <div class="policy-note">
                        <strong>Note:</strong> We will never sell your personal information to third parties. Your data is used solely for providing and improving our services.
                    </div>

                    <h2 class="policy-section-title" id="data-sharing">Data Sharing and Disclosure</h2>
                    <p>We may share your information in the following circumstances:</p>
                    <ul class="policy-list">
                        <li><span class="highlight">Event Organizers:</span> Necessary information for event access and management</li>
                        <li><span class="highlight">Payment Processors:</span> To complete financial transactions</li>
                        <li><span class="highlight">Legal Requirements:</span> When required by law or to protect our rights</li>
                        <li><span class="highlight">Service Providers:</span> Trusted partners who assist in our operations</li>
                    </ul>

                    <h2 class="policy-section-title" id="data-security">Data Security</h2>
                    <p>We implement appropriate technical and organizational security measures to protect your personal information, including:</p>
                    <ul class="policy-list">
                        <li>SSL encryption for data transmission</li>
                        <li>Secure servers with regular security updates</li>
                        <li>Access controls and authentication procedures</li>
                        <li>Regular security assessments and monitoring</li>
                    </ul>

                    <h2 class="policy-section-title" id="your-rights">Your Rights</h2>
                    <p>You have the following rights regarding your personal information:</p>
                    <ul class="policy-list">
                        <li><span class="highlight">Right to Access:</span> Request copies of your personal data</li>
                        <li><span class="highlight">Right to Rectification:</span> Correct inaccurate or incomplete data</li>
                        <li><span class="highlight">Right to Erasure:</span> Request deletion of your personal data</li>
                        <li><span class="highlight">Right to Restrict Processing:</span> Limit how we use your data</li>
                        <li><span class="highlight">Right to Data Portability:</span> Receive your data in a readable format</li>
                        <li><span class="highlight">Right to Object:</span> Object to certain types of processing</li>
                    </ul>

                    <h2 class="policy-section-title" id="cookies">Cookies and Tracking Technologies</h2>
                    <p>We use cookies and similar tracking technologies to enhance your experience on our website:</p>
                    <ul class="policy-list">
                        <li><span class="highlight">Essential Cookies:</span> Required for basic website functionality</li>
                        <li><span class="highlight">Analytics Cookies:</span> Help us understand how visitors use our site</li>
                        <li><span class="highlight">Preference Cookies:</span> Remember your settings and preferences</li>
                        <li><span class="highlight">Marketing Cookies:</span> Used for targeted advertising (with consent)</li>
                    </ul>

                    <h2 class="policy-section-title" id="data-retention">Data Retention</h2>
                    <p>We retain your personal information only for as long as necessary to fulfill the purposes outlined in this policy, unless a longer retention period is required or permitted by law.</p>

                    <h2 class="policy-section-title" id="children-privacy">Children's Privacy</h2>
                    <p>Our services are not directed to individuals under the age of 16. We do not knowingly collect personal information from children under 16. If you become aware that a child has provided us with personal information, please contact us.</p>

                    <h2 class="policy-section-title" id="changes">Changes to This Policy</h2>
                    <p>We may update this Privacy Policy from time to time. We will notify you of any changes by posting the new Privacy Policy on this page and updating the "Last Updated" date.</p>

                    <h2 class="policy-section-title" id="contact">Contact Us</h2>
                    <div class="contact-info">
                        <h3>Privacy Concerns and Questions</h3>
                        <p>If you have any questions about this Privacy Policy or our data practices, please contact us:</p>
                        <p>📧 Email: privacy@eventisa.com</p>
                        <p>📞 Phone: +88 018 35099 555</p>
                        <p>🏠 Address: House 6, Road 16, Block D, Mirpur 6, Dhaka 1216</p>
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
                    <a href="privacy.php" class="active">Privacy Policy</a>
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