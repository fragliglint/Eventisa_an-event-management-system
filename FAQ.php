<?php
session_start();
require_once 'config/database.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>FAQ - Eventisa</title>
    <link rel="stylesheet" href="css/user-styles.css">
    <style>
        /* FAQ Page Specific Styles */
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

        /* FAQ Section */
        .faq-section {
            padding: 60px 0;
            animation: fadeIn 0.8s var(--ease-out) both;
        }

        .faq-categories {
            display: flex;
            gap: 16px;
            margin-bottom: 40px;
            flex-wrap: wrap;
            animation: slideInDown 0.8s var(--ease-out) 0.3s both;
        }

        .category-btn {
            padding: 12px 24px;
            background: white;
            border: 2px solid var(--eventisa-border);
            border-radius: var(--eventisa-radius-md);
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s var(--ease-out);
            font-family: 'Poppins', sans-serif;
        }

        .category-btn.active,
        .category-btn:hover {
            background: linear-gradient(135deg, var(--eventisa-primary), var(--eventisa-secondary));
            color: white;
            border-color: transparent;
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(139, 92, 246, 0.3);
        }

        .faq-container {
            max-width: 800px;
            margin: 0 auto;
        }

        .faq-category {
            margin-bottom: 50px;
            animation: slideInUp 0.8s var(--ease-out) both;
        }

        .faq-category h2 {
            font-size: 2rem;
            font-weight: 700;
            margin-bottom: 24px;
            color: var(--eventisa-text-dark);
            position: relative;
            padding-bottom: 12px;
        }

        .faq-category h2::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 0;
            width: 60px;
            height: 4px;
            background: linear-gradient(135deg, var(--eventisa-primary), var(--eventisa-secondary));
            border-radius: 2px;
        }

        .faq-item {
            background: white;
            border-radius: var(--eventisa-radius-lg);
            box-shadow: var(--eventisa-shadow);
            border: 1px solid var(--eventisa-border);
            margin-bottom: 16px;
            overflow: hidden;
            transition: all 0.4s var(--ease-out);
            animation: scaleIn 0.6s var(--ease-out) both;
        }

        .faq-item:nth-child(1) { animation-delay: 0.3s; }
        .faq-item:nth-child(2) { animation-delay: 0.4s; }
        .faq-item:nth-child(3) { animation-delay: 0.5s; }
        .faq-item:nth-child(4) { animation-delay: 0.6s; }
        .faq-item:nth-child(5) { animation-delay: 0.7s; }

        .faq-item:hover {
            transform: translateY(-4px);
            box-shadow: 0 12px 30px rgba(16, 24, 40, 0.15);
        }

        .faq-question {
            padding: 24px;
            font-size: 1.125rem;
            font-weight: 600;
            color: var(--eventisa-text-dark);
            cursor: pointer;
            display: flex;
            justify-content: space-between;
            align-items: center;
            transition: all 0.3s var(--ease-out);
            position: relative;
        }

        .faq-question::after {
            content: '+';
            font-size: 1.5rem;
            font-weight: 300;
            transition: all 0.3s var(--ease-out);
            color: var(--eventisa-primary);
        }

        .faq-item.active .faq-question::after {
            content: '−';
            transform: rotate(180deg);
        }

        .faq-question:hover {
            background: var(--eventisa-card-bg);
        }

        .faq-answer {
            padding: 0 24px;
            max-height: 0;
            overflow: hidden;
            transition: all 0.4s var(--ease-out);
            color: var(--eventisa-text-muted);
            line-height: 1.7;
        }

        .faq-item.active .faq-answer {
            padding: 0 24px 24px 24px;
            max-height: 500px;
        }

        /* Search Section */
        .faq-search {
            max-width: 600px;
            margin: 0 auto 40px;
            animation: slideInDown 0.8s var(--ease-out) 0.3s both;
        }

        .search-container {
            position: relative;
        }

        .search-input {
            width: 100%;
            padding: 16px 50px 16px 20px;
            border: 2px solid var(--eventisa-border);
            border-radius: var(--eventisa-radius-lg);
            background: white;
            font-size: 1rem;
            outline: none;
            transition: all 0.3s var(--ease-out);
            font-family: 'Poppins', sans-serif;
            box-shadow: var(--eventisa-shadow);
        }

        .search-input:focus {
            border-color: var(--eventisa-primary);
            box-shadow: 0 0 0 3px rgba(139, 92, 246, 0.1);
        }

        .search-icon {
            position: absolute;
            right: 20px;
            top: 50%;
            transform: translateY(-50%);
            font-size: 1.25rem;
            color: var(--eventisa-text-muted);
        }

        /* Contact CTA */
        .contact-cta {
            background: linear-gradient(135deg, var(--eventisa-card-bg), #f8fafc);
            padding: 60px 0;
            text-align: center;
            animation: fadeIn 0.8s var(--ease-out) both;
        }

        .cta-content {
            max-width: 600px;
            margin: 0 auto;
        }

        .cta-content h2 {
            font-size: 2.5rem;
            font-weight: 700;
            margin-bottom: 16px;
            color: var(--eventisa-text-dark);
            animation: slideInUp 0.8s var(--ease-out) 0.3s both;
        }

        .cta-content p {
            font-size: 1.125rem;
            color: var(--eventisa-text-muted);
            margin-bottom: 32px;
            animation: slideInUp 0.8s var(--ease-out) 0.4s both;
        }

        .cta-button {
            display: inline-block;
            padding: 16px 32px;
            background: linear-gradient(135deg, var(--eventisa-primary), var(--eventisa-secondary));
            color: white;
            text-decoration: none;
            border-radius: var(--eventisa-radius-md);
            font-weight: 600;
            transition: all 0.3s var(--ease-out);
            position: relative;
            overflow: hidden;
            animation: slideInUp 0.8s var(--ease-out) 0.5s both;
        }

        .cta-button::before {
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

        .cta-button:hover::before {
            width: 300px;
            height: 300px;
        }

        .cta-button:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 25px rgba(139, 92, 246, 0.4);
        }

        /* No Results */
        .no-results {
            text-align: center;
            padding: 60px 20px;
            display: none;
            animation: fadeIn 0.8s var(--ease-out);
        }

        .no-results h3 {
            font-size: 1.5rem;
            margin-bottom: 16px;
            color: var(--eventisa-text-dark);
        }

        .no-results p {
            color: var(--eventisa-text-muted);
        }

        /* Responsive Design */
        @media (max-width: 768px) {
            .page-title {
                font-size: 2.5rem;
            }

            .faq-categories {
                justify-content: center;
            }

            .faq-question {
                padding: 20px;
                font-size: 1rem;
            }

            .page-header {
                padding: 60px 0 30px;
            }
        }

        @media (max-width: 480px) {
            .page-title {
                font-size: 2rem;
            }

            .faq-category h2 {
                font-size: 1.75rem;
            }

            .cta-content h2 {
                font-size: 2rem;
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
                <a href="FAQ.php" class="nav-link active">FAQ</a>
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

    <!-- FAQ Header -->
    <section class="page-header">
        <div class="container">
            <h1 class="page-title">Frequently Asked Questions</h1>
            <p class="page-subtitle">Find quick answers to common questions</p>
        </div>
    </section>

    <!-- FAQ Section -->
    <section class="section faq-section">
        <div class="container">
            <!-- Search -->
            <div class="faq-search scroll-reveal">
                <div class="search-container">
                    <input type="text" class="search-input" placeholder="Search for questions...">
                    <span class="search-icon">🔍</span>
                </div>
            </div>

            <!-- Categories -->
            <div class="faq-categories scroll-reveal">
                <button class="category-btn active" data-category="all">All Questions</button>
                <button class="category-btn" data-category="booking">Booking & Tickets</button>
                <button class="category-btn" data-category="events">Events</button>
                <button class="category-btn" data-category="account">Account & Profile</button>
                <button class="category-btn" data-category="payment">Payment & Refunds</button>
            </div>

            <!-- FAQ Container -->
            <div class="faq-container">
                <!-- Booking & Tickets -->
                <div class="faq-category scroll-reveal" data-category="booking">
                    <h2>Booking & Tickets</h2>
                    
                    <div class="faq-item">
                        <div class="faq-question">How do I book tickets for an event?</div>
                        <div class="faq-answer">
                            <p>Booking tickets is easy! Simply browse our events, select the one you want to attend, choose your preferred ticket type and quantity, and proceed to checkout. You'll need to create an account or log in to complete your booking.</p>
                        </div>
                    </div>

                    <div class="faq-item">
                        <div class="faq-question">Can I cancel or reschedule my booking?</div>
                        <div class="faq-answer">
                            <p>Cancellation and rescheduling policies vary by event organizer. Generally, you can cancel your booking up to 24 hours before the event for a full refund. Some events may have different policies, which will be clearly stated during the booking process.</p>
                        </div>
                    </div>

                    <div class="faq-item">
                        <div class="faq-question">How will I receive my tickets?</div>
                        <div class="faq-answer">
                            <p>After successful booking, you'll receive an email confirmation with your e-tickets attached. You can also access your tickets anytime by logging into your account and visiting the "My Tickets" section.</p>
                        </div>
                    </div>

                    <div class="faq-item">
                        <div class="faq-question">What if I lose my tickets?</div>
                        <div class="faq-answer">
                            <p>Don't worry! You can always re-download your tickets from your account dashboard. If you're having trouble accessing your tickets, contact our support team and we'll help you retrieve them.</p>
                        </div>
                    </div>
                </div>

                <!-- Events -->
                <div class="faq-category scroll-reveal" data-category="events">
                    <h2>Events</h2>
                    
                    <div class="faq-item">
                        <div class="faq-question">How do I find events near me?</div>
                        <div class="faq-answer">
                            <p>You can use our location-based search feature to find events in your area. Simply enter your city or allow location access, and we'll show you relevant events. You can also filter by category, date, and price to find exactly what you're looking for.</p>
                        </div>
                    </div>

                    <div class="faq-item">
                        <div class="faq-question">Can I get notifications for upcoming events?</div>
                        <div class="faq-answer">
                            <p>Yes! You can enable notifications in your account settings. We'll send you alerts about new events matching your interests, events from your favorite organizers, and reminders for events you've bookmarked.</p>
                        </div>
                    </div>

                    <div class="faq-item">
                        <div class="faq-question">How do I create an event?</div>
                        <div class="faq-answer">
                            <p>If you're an event organizer, you can create events by signing up for an organizer account. Once approved, you'll have access to our event creation tools where you can set up event details, ticket types, pricing, and more.</p>
                        </div>
                    </div>
                </div>

                <!-- Account & Profile -->
                <div class="faq-category scroll-reveal" data-category="account">
                    <h2>Account & Profile</h2>
                    
                    <div class="faq-item">
                        <div class="faq-question">How do I create an account?</div>
                        <div class="faq-answer">
                            <p>Click the "Sign Up" button in the top navigation and fill out the registration form. You'll need to provide your email address, create a password, and verify your email. Once verified, you can start booking events immediately!</p>
                        </div>
                    </div>

                    <div class="faq-item">
                        <div class="faq-question">I forgot my password. How can I reset it?</div>
                        <div class="faq-answer">
                            <p>Click "Sign In" and then "Forgot Password." Enter your email address, and we'll send you a password reset link. Follow the instructions in the email to create a new password.</p>
                        </div>
                    </div>

                    <div class="faq-item">
                        <div class="faq-question">Can I update my profile information?</div>
                        <div class="faq-answer">
                            <p>Yes! Log into your account and go to "My Profile" where you can update your personal information, preferences, notification settings, and more.</p>
                        </div>
                    </div>
                </div>

                <!-- Payment & Refunds -->
                <div class="faq-category scroll-reveal" data-category="payment">
                    <h2>Payment & Refunds</h2>
                    
                    <div class="faq-item">
                        <div class="faq-question">What payment methods do you accept?</div>
                        <div class="faq-answer">
                            <p>We accept all major credit cards (Visa, MasterCard, American Express), debit cards, and popular digital wallets. Some events may also offer additional payment options specific to your region.</p>
                        </div>
                    </div>

                    <div class="faq-item">
                        <div class="faq-question">Is my payment information secure?</div>
                        <div class="faq-answer">
                            <p>Absolutely! We use industry-standard SSL encryption to protect your payment information. We never store your full credit card details on our servers, and all transactions are processed through PCI-compliant payment gateways.</p>
                        </div>
                    </div>

                    <div class="faq-item">
                        <div class="faq-question">How long do refunds take to process?</div>
                        <div class="faq-answer">
                            <p>Refunds are typically processed within 5-7 business days. The time it takes for the refund to appear in your account depends on your bank or payment provider, but usually appears within 10 business days.</p>
                        </div>
                    </div>
                </div>

                <!-- No Results Message -->
                <div class="no-results scroll-reveal">
                    <h3>No results found</h3>
                    <p>Try adjusting your search terms or browse different categories.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Contact CTA -->
    <section class="contact-cta">
        <div class="container">
            <div class="cta-content scroll-reveal">
                <h2>Still have questions?</h2>
                <p>Can't find the answer you're looking for? Our support team is here to help you.</p>
                <a href="contact.php" class="cta-button">Contact Support</a>
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
        // Enhanced FAQ Page JavaScript
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

            // FAQ Accordion Functionality
            const faqItems = document.querySelectorAll('.faq-item');
            
            faqItems.forEach(item => {
                const question = item.querySelector('.faq-question');
                
                question.addEventListener('click', () => {
                    // Close all other items
                    faqItems.forEach(otherItem => {
                        if (otherItem !== item && otherItem.classList.contains('active')) {
                            otherItem.classList.remove('active');
                        }
                    });
                    
                    // Toggle current item
                    item.classList.toggle('active');
                });
            });

            // Category Filtering
            const categoryBtns = document.querySelectorAll('.category-btn');
            const faqCategories = document.querySelectorAll('.faq-category');
            const noResults = document.querySelector('.no-results');

            categoryBtns.forEach(btn => {
                btn.addEventListener('click', () => {
                    // Update active button
                    categoryBtns.forEach(b => b.classList.remove('active'));
                    btn.classList.add('active');
                    
                    const category = btn.dataset.category;
                    
                    // Show/hide categories
                    let hasVisibleItems = false;
                    
                    faqCategories.forEach(cat => {
                        if (category === 'all' || cat.dataset.category === category) {
                            cat.style.display = 'block';
                            hasVisibleItems = true;
                            
                            // Animate in
                            cat.style.opacity = '0';
                            cat.style.transform = 'translateY(20px)';
                            setTimeout(() => {
                                cat.style.opacity = '1';
                                cat.style.transform = 'translateY(0)';
                                cat.style.transition = 'all 0.4s var(--ease-out)';
                            }, 100);
                        } else {
                            cat.style.display = 'none';
                        }
                    });
                    
                    // Show no results message if needed
                    noResults.style.display = hasVisibleItems ? 'none' : 'block';
                });
            });

            // Search Functionality
            const searchInput = document.querySelector('.search-input');
            
            searchInput.addEventListener('input', function() {
                const searchTerm = this.value.toLowerCase().trim();
                let hasResults = false;
                
                if (searchTerm === '') {
                    // Show all items when search is empty
                    faqItems.forEach(item => {
                        item.style.display = 'block';
                        hasResults = true;
                    });
                    noResults.style.display = 'none';
                    return;
                }
                
                faqItems.forEach(item => {
                    const question = item.querySelector('.faq-question').textContent.toLowerCase();
                    const answer = item.querySelector('.faq-answer').textContent.toLowerCase();
                    
                    if (question.includes(searchTerm) || answer.includes(searchTerm)) {
                        item.style.display = 'block';
                        hasResults = true;
                        
                        // Highlight matching text
                        const questionElement = item.querySelector('.faq-question');
                        const originalText = questionElement.textContent;
                        const regex = new RegExp(`(${searchTerm})`, 'gi');
                        questionElement.innerHTML = originalText.replace(regex, '<mark style="background: yellow;">$1</mark>');
                    } else {
                        item.style.display = 'none';
                    }
                });
                
                noResults.style.display = hasResults ? 'none' : 'block';
            });

            // Auto-open FAQ item if URL has hash
            if (window.location.hash) {
                const targetItem = document.querySelector(window.location.hash);
                if (targetItem && targetItem.classList.contains('faq-item')) {
                    targetItem.classList.add('active');
                    targetItem.scrollIntoView({ behavior: 'smooth', block: 'center' });
                }
            }

            console.log('FAQ page enhanced with animations and interactions!');
        });
    </script>
</body>
</html>