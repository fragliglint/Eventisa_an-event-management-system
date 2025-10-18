<?php
session_start();
require_once 'config/database.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>About Us - Eventisa</title>
    <link rel="stylesheet" href="css/user-styles.css">
    <style>
        /* About Page Specific Styles */
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

        /* About Content Section */
        .about-section {
            padding: 60px 0;
            animation: fadeIn 0.8s var(--ease-out) both;
        }

        .about-content {
            max-width: 800px;
            margin: 0 auto;
            animation: slideInUp 0.8s var(--ease-out) 0.3s both;
        }

        .about-text h2 {
            font-size: 2.5rem;
            font-weight: 800;
            margin: 60px 0 24px 0;
            color: var(--eventisa-text-dark);
            position: relative;
            animation: slideInUp 0.8s var(--ease-out) 0.4s both;
        }

        .about-text h2::after {
            content: '';
            position: absolute;
            bottom: -8px;
            left: 0;
            width: 60px;
            height: 4px;
            background: linear-gradient(135deg, var(--eventisa-primary), var(--eventisa-secondary));
            border-radius: 2px;
            animation: slideInLeft 0.8s var(--ease-out) 0.6s both;
        }

        .about-text p {
            font-size: 1.125rem;
            line-height: 1.8;
            color: var(--eventisa-text-muted);
            margin-bottom: 30px;
            animation: slideInUp 0.8s var(--ease-out) 0.5s both;
        }

        /* Features Grid */
        .features-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 30px;
            margin: 60px 0;
        }

        .feature-item {
            display: flex;
            align-items: flex-start;
            gap: 20px;
            padding: 32px;
            background: white;
            border-radius: var(--eventisa-radius-lg);
            box-shadow: var(--eventisa-shadow);
            border: 1px solid var(--eventisa-border);
            transition: all 0.4s var(--ease-out);
            position: relative;
            overflow: hidden;
            animation: scaleIn 0.6s var(--ease-out) both;
        }

        .feature-item:nth-child(1) { animation-delay: 0.3s; }
        .feature-item:nth-child(2) { animation-delay: 0.4s; }
        .feature-item:nth-child(3) { animation-delay: 0.5s; }
        .feature-item:nth-child(4) { animation-delay: 0.6s; }

        .feature-item::before {
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

        .feature-item:hover::before {
            height: 4px;
        }

        .feature-item:hover {
            transform: translateY(-8px) scale(1.02);
            box-shadow: 0 20px 40px rgba(16, 24, 40, 0.15);
        }

        .feature-icon {
            font-size: 2.5rem;
            flex-shrink: 0;
            animation: bounceIn 0.8s var(--bounce) 0.7s both;
            transition: all 0.4s var(--ease-out);
        }

        .feature-item:hover .feature-icon {
            transform: scale(1.2) rotate(5deg);
        }

        .feature-item-content {
            flex: 1;
        }

        .feature-item h4 {
            font-size: 1.375rem;
            font-weight: 700;
            margin-bottom: 12px;
            color: var(--eventisa-text-dark);
            animation: slideInUp 0.6s var(--ease-out) 0.8s both;
        }

        .feature-item p {
            margin: 0;
            color: var(--eventisa-text-muted);
            font-size: 1rem;
            line-height: 1.6;
            animation: slideInUp 0.6s var(--ease-out) 0.9s both;
        }

        /* Stats Section */
        .stats-section {
            background: linear-gradient(135deg, var(--eventisa-card-bg), #f8fafc);
            padding: 80px 0;
            position: relative;
            overflow: hidden;
            animation: fadeIn 0.8s var(--ease-out) both;
        }

        .stats-section::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1000 1000"><circle fill="rgba(139,92,246,0.03)" cx="100" cy="100" r="80"/><circle fill="rgba(139,92,246,0.05)" cx="800" cy="200" r="120"/><circle fill="rgba(139,92,246,0.02)" cx="400" cy="500" r="150"/></svg>');
            animation: float 10s ease-in-out infinite;
        }

        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 40px;
            position: relative;
            z-index: 1;
        }

        .stat-card {
            text-align: center;
            padding: 40px 20px;
            background: white;
            border-radius: var(--eventisa-radius-lg);
            box-shadow: var(--eventisa-shadow);
            border: 1px solid var(--eventisa-border);
            transition: all 0.4s var(--ease-out);
            animation: scaleIn 0.6s var(--ease-out) both;
            position: relative;
            overflow: hidden;
        }

        .stat-card:nth-child(1) { animation-delay: 0.2s; }
        .stat-card:nth-child(2) { animation-delay: 0.3s; }
        .stat-card:nth-child(3) { animation-delay: 0.4s; }
        .stat-card:nth-child(4) { animation-delay: 0.5s; }

        .stat-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 0;
            background: linear-gradient(135deg, var(--eventisa-primary), var(--eventisa-secondary));
            transition: height 0.4s var(--ease-out);
        }

        .stat-card:hover::before {
            height: 4px;
        }

        .stat-card:hover {
            transform: translateY(-8px) scale(1.05);
            box-shadow: 0 25px 50px rgba(16, 24, 40, 0.15);
        }

        .stat-number {
            font-size: 3rem;
            font-weight: 800;
            color: var(--eventisa-primary);
            margin-bottom: 12px;
            display: block;
            animation: countUp 2s var(--ease-out) 0.6s both;
            position: relative;
        }

        .stat-label {
            font-size: 1.125rem;
            color: var(--eventisa-text-muted);
            font-weight: 600;
            animation: slideInUp 0.6s var(--ease-out) 0.8s both;
        }

        /* Team Section */
        .team-section {
            padding: 80px 0;
            animation: fadeIn 0.8s var(--ease-out) both;
        }

        .team-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 30px;
            margin-top: 40px;
        }

        .team-member {
            text-align: center;
            padding: 30px;
            background: white;
            border-radius: var(--eventisa-radius-lg);
            box-shadow: var(--eventisa-shadow);
            border: 1px solid var(--eventisa-border);
            transition: all 0.4s var(--ease-out);
            animation: scaleIn 0.6s var(--ease-out) both;
            position: relative;
            overflow: hidden;
        }

        .team-member:nth-child(1) { animation-delay: 0.2s; }
        .team-member:nth-child(2) { animation-delay: 0.3s; }
        .team-member:nth-child(3) { animation-delay: 0.4s; }

        .team-member::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 0;
            background: linear-gradient(135deg, var(--eventisa-primary), var(--eventisa-secondary));
            transition: height 0.4s var(--ease-out);
        }

        .team-member:hover::before {
            height: 4px;
        }

        .team-member:hover {
            transform: translateY(-8px);
            box-shadow: 0 20px 40px rgba(16, 24, 40, 0.15);
        }

        .member-avatar {
            width: 100px;
            height: 100px;
            border-radius: 50%;
            background: linear-gradient(135deg, var(--eventisa-primary), var(--eventisa-secondary));
            margin: 0 auto 20px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 2.5rem;
            color: white;
            font-weight: 700;
            animation: bounceIn 0.8s var(--bounce) 0.5s both;
        }

        .member-name {
            font-size: 1.375rem;
            font-weight: 700;
            margin-bottom: 8px;
            color: var(--eventisa-text-dark);
            animation: slideInUp 0.6s var(--ease-out) 0.6s both;
        }

        .member-role {
            font-size: 1rem;
            color: var(--eventisa-primary);
            font-weight: 600;
            margin-bottom: 16px;
            animation: slideInUp 0.6s var(--ease-out) 0.7s both;
        }

        .member-bio {
            font-size: 0.9rem;
            color: var(--eventisa-text-muted);
            line-height: 1.6;
            animation: slideInUp 0.6s var(--ease-out) 0.8s both;
        }

        /* Responsive Design */
        @media (max-width: 768px) {
            .page-title {
                font-size: 2.5rem;
            }

            .features-grid {
                grid-template-columns: 1fr;
            }

            .stats-grid {
                grid-template-columns: repeat(2, 1fr);
            }

            .feature-item, .stat-card, .team-member {
                padding: 24px;
            }

            .page-header {
                padding: 60px 0 30px;
            }
        }

        @media (max-width: 480px) {
            .page-title {
                font-size: 2rem;
            }

            .stats-grid {
                grid-template-columns: 1fr;
            }

            .about-text h2 {
                font-size: 2rem;
            }

            .stat-number {
                font-size: 2.5rem;
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

        @keyframes countUp {
            from {
                opacity: 0;
                transform: translateY(20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
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
                <a href="about.php" class="nav-link active">About</a>
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

    <!-- About Header -->
    <section class="page-header">
        <div class="container">
            <h1 class="page-title">About Eventisa</h1>
            <p class="page-subtitle">Connecting people with amazing experiences</p>
        </div>
    </section>

    <!-- About Content -->
    <section class="section about-section">
        <div class="container">
            <div class="about-content">
                <div class="about-text">
                    <h2>Our Story</h2>
                    <p>Eventisa was founded with a simple mission: to make event discovery and booking seamless and enjoyable. We believe that everyone deserves access to amazing experiences that create lasting memories.</p>
                    
                    <h2>What We Do</h2>
                    <p>We partner with event organizers across the globe to bring you the best selection of concerts, festivals, workshops, sports events, and much more. Our platform makes it easy to discover, book, and enjoy events that match your interests.</p>
                    
                    <h2>Why Choose Eventisa?</h2>
                    <div class="features-grid">
                        <div class="feature-item scroll-reveal">
                            <span class="feature-icon">🔒</span>
                            <div class="feature-item-content">
                                <h4>Secure Booking</h4>
                                <p>Your transactions are safe and secure with our advanced encryption technology and payment protection.</p>
                            </div>
                        </div>
                        <div class="feature-item scroll-reveal">
                            <span class="feature-icon">⚡</span>
                            <div class="feature-item-content">
                                <h4>Instant Confirmation</h4>
                                <p>Get immediate confirmation and digital tickets delivered straight to your device upon booking.</p>
                            </div>
                        </div>
                        <div class="feature-item scroll-reveal">
                            <span class="feature-icon">📱</span>
                            <div class="feature-item-content">
                                <h4>Mobile Friendly</h4>
                                <p>Book events on the go with our fully responsive, mobile-optimized platform that works seamlessly.</p>
                            </div>
                        </div>
                        <div class="feature-item scroll-reveal">
                            <span class="feature-icon">🎯</span>
                            <div class="feature-item-content">
                                <h4>Personalized Recommendations</h4>
                                <p>Discover events perfectly tailored to your unique interests, preferences, and past experiences.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Stats Section -->
    <section class="section stats-section">
        <div class="container">
            <div class="stats-grid">
                <div class="stat-card scroll-reveal">
                    <div class="stat-number">50,000+</div>
                    <div class="stat-label">Happy Customers</div>
                </div>
                <div class="stat-card scroll-reveal">
                    <div class="stat-number">5,000+</div>
                    <div class="stat-label">Events Hosted</div>
                </div>
                <div class="stat-card scroll-reveal">
                    <div class="stat-number">100+</div>
                    <div class="stat-label">Cities Worldwide</div>
                </div>
                <div class="stat-card scroll-reveal">
                    <div class="stat-number">98%</div>
                    <div class="stat-label">Customer Satisfaction</div>
                </div>
            </div>
        </div>
    </section>

    <!-- Team Section -->
    <section class="section team-section">
        <div class="container">
            <h2 style="text-align: center; font-size: 2.5rem; font-weight: 800; margin-bottom: 20px; color: var(--eventisa-text-dark); animation: slideInUp 0.8s var(--ease-out) 0.3s both;">Our Team</h2>
            <p style="text-align: center; font-size: 1.125rem; color: var(--eventisa-text-muted); max-width: 600px; margin: 0 auto 40px; animation: slideInUp 0.8s var(--ease-out) 0.4s both;">Meet the passionate team behind Eventisa, dedicated to creating unforgettable experiences.</p>
            
            <div class="team-grid">
                <div class="team-member scroll-reveal">
                    <div class="member-avatar">SJ</div>
                    <h3 class="member-name">Sarah Johnson</h3>
                    <p class="member-role">CEO & Founder</p>
                    <p class="member-bio">With over 10 years in event management, Sarah founded Eventisa to revolutionize how people discover and experience events.</p>
                </div>
                <div class="team-member scroll-reveal">
                    <div class="member-avatar">MR</div>
                    <h3 class="member-name">Michael Rodriguez</h3>
                    <p class="member-role">CTO</p>
                    <p class="member-bio">Michael leads our technical team, ensuring our platform delivers a seamless and secure experience for all users.</p>
                </div>
                <div class="team-member scroll-reveal">
                    <div class="member-avatar">EC</div>
                    <h3 class="member-name">Emily Chen</h3>
                    <p class="member-role">Head of Partnerships</p>
                    <p class="member-bio">Emily builds relationships with event organizers worldwide to bring you the most diverse and exciting event portfolio.</p>
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
        // Enhanced About Page JavaScript
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

            // Number counting animation for stats
            const statNumbers = document.querySelectorAll('.stat-number');
            const observerOptions = {
                threshold: 0.5,
                rootMargin: '0px 0px -100px 0px'
            };

            const observer = new IntersectionObserver((entries) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        const statNumber = entry.target;
                        const targetNumber = parseInt(statNumber.textContent.replace(/,/g, ''));
                        const duration = 2000; // 2 seconds
                        const step = targetNumber / (duration / 16); // 60fps
                        let currentNumber = 0;
                        
                        const timer = setInterval(() => {
                            currentNumber += step;
                            if (currentNumber >= targetNumber) {
                                clearInterval(timer);
                                currentNumber = targetNumber;
                            }
                            statNumber.textContent = Math.floor(currentNumber).toLocaleString() + 
                                (statNumber.textContent.includes('%') ? '%' : 
                                 statNumber.textContent.includes('+') ? '+' : '');
                        }, 16);
                        
                        observer.unobserve(statNumber);
                    }
                });
            }, observerOptions);

            statNumbers.forEach(statNumber => {
                observer.observe(statNumber);
            });

            console.log('About page enhanced with animations and interactions!');
        });
    </script>
</body>
</html>