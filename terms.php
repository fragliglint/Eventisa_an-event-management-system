<?php
session_start();
require_once 'config/database.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Terms and Conditions - Eventisa</title>
    <link rel="stylesheet" href="css/user-styles.css">
    <style>
        /* Terms Page Specific Styles */
        .page-header {
            background: linear-gradient(135deg, #8b5cf6, #6366f1);
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
            animation: slideInUp 0.8s ease-out 0.2s both;
        }

        .page-subtitle {
            font-size: 1.25rem;
            opacity: 0.9;
            position: relative;
            z-index: 1;
            animation: slideInUp 0.8s ease-out 0.4s both;
        }

        /* Terms Content Section */
        .terms-section {
            padding: 60px 0;
            animation: fadeIn 0.8s ease-out both;
        }

        .terms-container {
            max-width: 900px;
            margin: 0 auto;
            background: white;
            border-radius: 12px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            border: 1px solid #e5e7eb;
            overflow: hidden;
        }

        .terms-header {
            background: linear-gradient(135deg, #f8fafc, #f1f5f9);
            padding: 40px;
            border-bottom: 1px solid #e5e7eb;
        }

        .terms-header h2 {
            font-size: 2rem;
            font-weight: 700;
            margin-bottom: 16px;
            color: #1f2937;
            animation: slideInUp 0.8s ease-out 0.3s both;
        }

        .last-updated {
            color: #6b7280;
            font-size: 1rem;
            animation: slideInUp 0.8s ease-out 0.4s both;
        }

        .terms-content {
            padding: 40px;
            max-height: 600px;
            overflow-y: auto;
        }

        .terms-section-item {
            margin-bottom: 40px;
            animation: slideInUp 0.8s ease-out both;
        }

        .terms-section-item:nth-child(1) { animation-delay: 0.3s; }
        .terms-section-item:nth-child(2) { animation-delay: 0.4s; }
        .terms-section-item:nth-child(3) { animation-delay: 0.5s; }
        .terms-section-item:nth-child(4) { animation-delay: 0.6s; }
        .terms-section-item:nth-child(5) { animation-delay: 0.7s; }
        .terms-section-item:nth-child(6) { animation-delay: 0.8s; }

        .section-title {
            font-size: 1.5rem;
            font-weight: 700;
            margin-bottom: 16px;
            color: #1f2937;
            position: relative;
            padding-bottom: 12px;
        }

        .section-title::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 0;
            width: 60px;
            height: 4px;
            background: linear-gradient(135deg, #8b5cf6, #6366f1);
            border-radius: 2px;
        }

        .section-content {
            color: #4b5563;
            line-height: 1.7;
        }

        .section-content p {
            margin-bottom: 16px;
        }

        .section-content ul, .section-content ol {
            margin: 16px 0;
            padding-left: 24px;
        }

        .section-content li {
            margin-bottom: 8px;
        }

        .highlight-box {
            background: #f8fafc;
            border-left: 4px solid #8b5cf6;
            padding: 20px;
            margin: 20px 0;
            border-radius: 0 8px 8px 0;
            animation: slideInLeft 0.6s ease-out;
        }

        .highlight-box strong {
            color: #1f2937;
            font-weight: 600;
        }

        /* Quick Navigation */
        .terms-nav {
            background: white;
            padding: 24px;
            border-radius: 12px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            border: 1px solid #e5e7eb;
            margin-bottom: 30px;
            animation: slideInDown 0.8s ease-out 0.3s both;
        }

        .terms-nav h3 {
            font-size: 1.25rem;
            font-weight: 600;
            margin-bottom: 16px;
            color: #1f2937;
        }

        .nav-links {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 12px;
        }

        .nav-link {
            display: flex;
            align-items: center;
            padding: 12px 16px;
            background: #f8fafc;
            border-radius: 8px;
            text-decoration: none;
            color: #4b5563;
            transition: all 0.3s ease-out;
            font-weight: 500;
        }

        .nav-link:hover {
            background: #8b5cf6;
            color: white;
            transform: translateY(-2px);
        }

        .nav-link::before {
            content: '📄';
            margin-right: 8px;
            font-size: 1.1rem;
        }

        /* Acceptance Section */
        .acceptance-section {
            background: linear-gradient(135deg, #f8fafc, #f1f5f9);
            padding: 40px;
            border-radius: 12px;
            margin-top: 40px;
            text-align: center;
            animation: slideInUp 0.8s ease-out 0.6s both;
        }

        .acceptance-section h3 {
            font-size: 1.5rem;
            font-weight: 700;
            margin-bottom: 16px;
            color: #1f2937;
        }

        .acceptance-section p {
            color: #6b7280;
            margin-bottom: 24px;
            max-width: 600px;
            margin-left: auto;
            margin-right: auto;
        }

        .contact-support {
            display: inline-block;
            padding: 12px 24px;
            background: linear-gradient(135deg, #8b5cf6, #6366f1);
            color: white;
            text-decoration: none;
            border-radius: 8px;
            font-weight: 600;
            transition: all 0.3s ease-out;
            position: relative;
            overflow: hidden;
        }

        .contact-support::before {
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

        .contact-support:hover::before {
            width: 300px;
            height: 300px;
        }

        .contact-support:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 25px rgba(139, 92, 246, 0.4);
        }

        /* Scrollbar Styling */
        .terms-content::-webkit-scrollbar {
            width: 6px;
        }

        .terms-content::-webkit-scrollbar-track {
            background: #f1f5f9;
            border-radius: 3px;
        }

        .terms-content::-webkit-scrollbar-thumb {
            background: #8b5cf6;
            border-radius: 3px;
        }

        .terms-content::-webkit-scrollbar-thumb:hover {
            background: #7c3aed;
        }

        /* Responsive Design */
        @media (max-width: 768px) {
            .page-title {
                font-size: 2.5rem;
            }

            .terms-header,
            .terms-content {
                padding: 30px 24px;
            }

            .nav-links {
                grid-template-columns: 1fr;
            }

            .page-header {
                padding: 60px 0 30px;
            }
        }

        @media (max-width: 480px) {
            .page-title {
                font-size: 2rem;
            }

            .terms-header,
            .terms-content {
                padding: 24px 20px;
            }

            .section-title {
                font-size: 1.25rem;
            }
        }

        /* Animation Keyframes */
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

        @keyframes fadeIn {
            from {
                opacity: 0;
            }
            to {
                opacity: 1;
            }
        }

        /* Scroll Reveal Animation */
        .scroll-reveal {
            opacity: 0;
            transform: translateY(30px);
            transition: all 0.6s ease-out;
        }

        .scroll-reveal.visible {
            opacity: 1;
            transform: translateY(0);
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
                <a href="contact.php" class="nav-link">Contact</a>
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

    <!-- Terms Header -->
    <section class="page-header">
        <div class="container">
            <h1 class="page-title">Terms and Conditions</h1>
            <p class="page-subtitle">Please read these terms carefully before using our services</p>
        </div>
    </section>

    <!-- Terms Section -->
    <section class="section terms-section">
        <div class="container">
            <!-- Quick Navigation -->
            <div class="terms-nav scroll-reveal">
                <h3>Quick Navigation</h3>
                <div class="nav-links">
                    <a href="#acceptance" class="nav-link">Acceptance of Terms</a>
                    <a href="#account" class="nav-link">User Accounts</a>
                    <a href="#booking" class="nav-link">Event Booking</a>
                    <a href="#payments" class="nav-link">Payments & Fees</a>
                    <a href="#cancellation" class="nav-link">Cancellation Policy</a>
                    <a href="#conduct" class="nav-link">User Conduct</a>
                    <a href="#intellectual" class="nav-link">Intellectual Property</a>
                    <a href="#liability" class="nav-link">Liability</a>
                </div>
            </div>

            <div class="terms-container scroll-reveal">
                <div class="terms-header">
                    <h2>Eventisa Terms of Service</h2>
                    <p class="last-updated">Last updated: <?php echo date('F j, Y'); ?></p>
                </div>

                <div class="terms-content">
                    <!-- Acceptance of Terms -->
                    <div class="terms-section-item" id="acceptance">
                        <h3 class="section-title">1. Acceptance of Terms</h3>
                        <div class="section-content">
                            <p>By accessing and using Eventisa ("the Platform"), you accept and agree to be bound by the terms and provision of this agreement. These Terms and Conditions govern your use of the Eventisa platform, including all events, services, and content available through the platform.</p>
                            
                            <div class="highlight-box">
                                <strong>Important:</strong> If you do not agree to these terms, please do not use our platform. Your continued use of Eventisa constitutes your acceptance of these terms and any future modifications.
                            </div>
                        </div>
                    </div>

                    <!-- User Accounts -->
                    <div class="terms-section-item" id="account">
                        <h3 class="section-title">2. User Accounts</h3>
                        <div class="section-content">
                            <p>To access certain features of the Platform, you must register for an account. You agree to:</p>
                            <ul>
                                <li>Provide accurate, current, and complete information during registration</li>
                                <li>Maintain and promptly update your account information</li>
                                <li>Maintain the security of your password and accept all risks of unauthorized access</li>
                                <li>Notify us immediately of any breach of security or unauthorized use of your account</li>
                                <li>Take responsibility for all activities that occur under your account</li>
                            </ul>
                            
                            <p>Eventisa reserves the right to disable any user account at any time in its sole discretion for conduct that violates these Terms or is otherwise harmful to other users, the Platform, or third parties.</p>
                        </div>
                    </div>

                    <!-- Event Booking -->
                    <div class="terms-section-item" id="booking">
                        <h3 class="section-title">3. Event Booking and Tickets</h3>
                        <div class="section-content">
                            <p>Eventisa acts as an intermediary between event organizers and attendees. When you book tickets through our platform:</p>
                            
                            <ul>
                                <li>All ticket sales are final unless otherwise stated in the event's specific refund policy</li>
                                <li>Ticket prices are set by event organizers and may include service fees</li>
                                <li>You must present a valid ticket for admission to events</li>
                                <li>Event organizers reserve the right to refuse admission</li>
                                <li>Event dates, times, and locations are subject to change by organizers</li>
                            </ul>

                            <div class="highlight-box">
                                <strong>Note:</strong> Eventisa is not responsible for events that are canceled, postponed, or modified by organizers. Please contact the event organizer directly for such matters.
                            </div>
                        </div>
                    </div>

                    <!-- Payments and Fees -->
                    <div class="terms-section-item" id="payments">
                        <h3 class="section-title">4. Payments and Service Fees</h3>
                        <div class="section-content">
                            <p>All payments processed through Eventisa are subject to the following terms:</p>
                            
                            <ul>
                                <li>Payment must be made in full at the time of booking</li>
                                <li>We accept major credit cards, debit cards, and other payment methods as indicated</li>
                                <li>Service fees may apply and are non-refundable</li>
                                <li>All prices are displayed in the local currency of the event</li>
                                <li>You authorize us to charge the total amount to your selected payment method</li>
                            </ul>

                            <p>Eventisa uses secure third-party payment processors. We do not store your complete payment card information on our servers.</p>
                        </div>
                    </div>

                    <!-- Cancellation Policy -->
                    <div class="terms-section-item" id="cancellation">
                        <h3 class="section-title">5. Cancellation and Refund Policy</h3>
                        <div class="section-content">
                            <p>Cancellation and refund policies vary by event organizer. Generally:</p>
                            
                            <ul>
                                <li>Refunds are subject to the event organizer's specific policy</li>
                                <li>Service fees are typically non-refundable</li>
                                <li>Requests for refunds must be made through the Platform</li>
                                <li>Eventisa may charge administrative fees for processing refunds</li>
                                <li>Force majeure events may affect cancellation policies</li>
                            </ul>

                            <p>Please review the specific refund policy for each event before making a purchase.</p>
                        </div>
                    </div>

                    <!-- User Conduct -->
                    <div class="terms-section-item" id="conduct">
                        <h3 class="section-title">6. User Conduct and Responsibilities</h3>
                        <div class="section-content">
                            <p>You agree not to use the Platform to:</p>
                            
                            <ul>
                               
                            <p>You agree not to use the Platform to:</p>
                            
                            <ul>
                               
                                <li>Infringe upon the rights of others</li>
                                <li>Post false, misleading, or fraudulent information</li>
                                <li>Transmit spam, chain letters, or other unsolicited communications</li>
                                <li>Distribute viruses or other harmful computer code</li>
                                <li>Harass, abuse, or harm another person</li>
                                <li>Impersonate any person or entity</li>
                            </ul>

                            <p>Eventisa reserves the right to investigate and take appropriate legal action against anyone who violates this provision.</p>
                        </div>
                    </div>

                    <!-- Intellectual Property -->
                    <div class="terms-section-item" id="intellectual">
                        <h3 class="section-title">7. Intellectual Property</h3>
                        <div class="section-content">
                            <p>All content on the Eventisa platform, including but not limited to:</p>
                            
                            <ul>
                                <li>Text, graphics, logos, and images</li>
                                <li>Software and source code</li>
                                <li>Event descriptions and content</li>
                                <li>User interface design</li>
                            </ul>

                            <p>Is the property of Eventisa or its content suppliers and protected by international copyright laws. You may not reproduce, distribute, or create derivative works without explicit permission.</p>

                            <div class="highlight-box">
                                <strong>User Content:</strong> By posting content on Eventisa, you grant us a worldwide, non-exclusive, royalty-free license to use, display, and distribute your content in connection with our services.
                            </div>
                        </div>
                    </div>

                    <!-- Liability -->
                    <div class="terms-section-item" id="liability">
                        <h3 class="section-title">8. Limitation of Liability</h3>
                        <div class="section-content">
                            <p>To the fullest extent permitted by law, Eventisa shall not be liable for:</p>
                            
                            <ul>
                                <li>Any indirect, incidental, or consequential damages</li>
                                <li>Loss of profits, data, or use</li>
                                <li>Events canceled, postponed, or modified by organizers</li>
                                <li>User interactions and disputes</li>
                                <li>Third-party actions or content</li>
                            </ul>

                            <p>Our total liability to you for all claims arising from or related to the Platform shall not exceed the amount you paid to Eventisa in the past six months.</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Acceptance Section -->
            <div class="acceptance-section scroll-reveal">
                <h3>Questions About Our Terms?</h3>
                <p>If you have any questions about these Terms and Conditions, please don't hesitate to contact our support team.</p>
                <a href="contact.php" class="contact-support">Contact Support</a>
            </div>
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
                    <a href="faq.php">FAQ</a>
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
        // Enhanced Terms Page JavaScript
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

            // Smooth scrolling for navigation links
            const navLinks = document.querySelectorAll('.nav-link');
            navLinks.forEach(link => {
                link.addEventListener('click', function(e) {
                    e.preventDefault();
                    const targetId = this.getAttribute('href');
                    const targetElement = document.querySelector(targetId);
                    
                    if (targetElement) {
                        const offsetTop = targetElement.offsetTop - 100;
                        window.scrollTo({
                            top: offsetTop,
                            behavior: 'smooth'
                        });
                    }
                });
            });

            // Highlight current section in view
            const sectionItems = document.querySelectorAll('.terms-section-item');
            const observerOptions = {
                threshold: 0.3,
                rootMargin: '-100px 0px -100px 0px'
            };

            const sectionObserver = new IntersectionObserver((entries) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        navLinks.forEach(link => {
                            link.classList.remove('active');
                            if (link.getAttribute('href') === `#${entry.target.id}`) {
                                link.classList.add('active');
                                link.style.background = '#8b5cf6';
                                link.style.color = 'white';
                            }
                        });
                    }
                });
            }, observerOptions);

            sectionItems.forEach(section => {
                sectionObserver.observe(section);
            });

            // Add reading progress indicator
            const createProgressBar = () => {
                const progressBar = document.createElement('div');
                progressBar.style.cssText = `
                    position: fixed;
                    top: 0;
                    left: 0;
                    width: 0;
                    height: 4px;
                    background: linear-gradient(135deg, #8b5cf6, #6366f1);
                    z-index: 1000;
                    transition: width 0.3s ease;
                `;
                document.body.appendChild(progressBar);

                window.addEventListener('scroll', () => {
                    const winHeight = window.innerHeight;
                    const docHeight = document.documentElement.scrollHeight;
                    const scrollTop = window.pageYOffset;
                    const scrollPercent = (scrollTop / (docHeight - winHeight)) * 100;
                    progressBar.style.width = `${scrollPercent}%`;
                });
            };

            createProgressBar();

            console.log('Terms and Conditions page enhanced with animations and interactions!');
        });
    </script>
</body>
</html>