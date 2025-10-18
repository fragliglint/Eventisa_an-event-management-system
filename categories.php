<?php
session_start();
require_once 'config/database.php';

$database = new Database();
$db = $database->getConnection();

// Get all categories with event counts
$categories = $db->query("SELECT category, COUNT(*) as event_count FROM events WHERE status='Active' GROUP BY category")->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Event Categories - Eventisa</title>
    <link rel="stylesheet" href="css/user-styles.css">
    <style>
        /* Categories Page Specific Styles */
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

        /* Categories Section */
        .categories-section {
            padding: 60px 0;
            animation: fadeIn 0.8s var(--ease-out) both;
        }

        .categories-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
            gap: 30px;
            margin-top: 40px;
        }

        .category-card {
            background: white;
            border-radius: var(--eventisa-radius-lg);
            padding: 2.5rem 2rem;
            text-align: center;
            text-decoration: none;
            color: inherit;
            box-shadow: var(--eventisa-shadow);
            transition: all 0.4s var(--ease-out);
            border: 1px solid var(--eventisa-border);
            position: relative;
            overflow: hidden;
            animation: scaleIn 0.6s var(--ease-out) both;
        }

        .category-card:nth-child(1) { animation-delay: 0.1s; }
        .category-card:nth-child(2) { animation-delay: 0.2s; }
        .category-card:nth-child(3) { animation-delay: 0.3s; }
        .category-card:nth-child(4) { animation-delay: 0.4s; }
        .category-card:nth-child(5) { animation-delay: 0.5s; }
        .category-card:nth-child(6) { animation-delay: 0.6s; }
        .category-card:nth-child(7) { animation-delay: 0.7s; }
        .category-card:nth-child(8) { animation-delay: 0.8s; }

        .category-card::before {
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

        .category-card:hover::before {
            height: 4px;
        }

        .category-card:hover {
            transform: translateY(-12px) scale(1.02);
            box-shadow: 0 25px 50px rgba(16, 24, 40, 0.15);
            color: inherit;
        }

        .category-icon {
            font-size: 4rem;
            margin-bottom: 1.5rem;
            display: block;
            animation: bounceIn 0.8s var(--bounce) 0.5s both;
            filter: grayscale(0.2);
            transition: all 0.4s var(--ease-out);
        }

        .category-card:hover .category-icon {
            transform: scale(1.2) rotate(5deg);
            filter: grayscale(0);
        }

        .category-card h3 {
            margin: 0.5rem 0;
            color: var(--eventisa-text-dark);
            font-size: 1.5rem;
            font-weight: 700;
            animation: slideInUp 0.5s var(--ease-out) 0.6s both;
        }

        .category-card p {
            color: #6b7280;
            margin: 0;
            font-size: 1rem;
            font-weight: 500;
            animation: slideInUp 0.5s var(--ease-out) 0.7s both;
        }

        .category-count {
            display: inline-block;
            background: linear-gradient(135deg, var(--eventisa-primary), var(--eventisa-secondary));
            color: white;
            padding: 6px 14px;
            border-radius: 20px;
            font-size: 0.9rem;
            font-weight: 700;
            margin-top: 10px;
            animation: slideInUp 0.5s var(--ease-out) 0.8s both;
            position: relative;
            overflow: hidden;
        }

        .category-count::before {
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

        .category-card:hover .category-count::before {
            width: 100px;
            height: 100px;
        }

        /* No Categories State */
        .no-categories {
            text-align: center;
            padding: 80px 20px;
            animation: fadeIn 0.8s var(--ease-out);
        }

        .no-categories h3 {
            font-size: 2rem;
            margin-bottom: 16px;
            color: var(--eventisa-text-dark);
            animation: bounceIn 0.8s var(--bounce);
        }

        .no-categories p {
            color: #666;
            font-size: 1.1rem;
            animation: slideInUp 0.6s var(--ease-out) 0.3s both;
        }

        /* Responsive Design */
        @media (max-width: 768px) {
            .page-title {
                font-size: 2.5rem;
            }

            .categories-grid {
                grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
            }

            .category-card:hover {
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

            .categories-grid {
                grid-template-columns: 1fr;
            }

            .category-card {
                padding: 2rem 1.5rem;
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
                <a href="events.php" class="nav-link">Events</a>
                <a href="categories.php" class="nav-link active">Categories</a>
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

    <!-- Categories Header -->
    <section class="page-header">
        <div class="container">
            <h1 class="page-title">Event Categories</h1>
            <p class="page-subtitle">Browse events by your interests</p>
        </div>
    </section>

    <!-- Categories Grid -->
    <section class="section categories-section">
        <div class="container">
            <?php if (empty($categories)): ?>
                <div class="no-categories">
                    <h3>No categories found</h3>
                    <p>There are currently no active event categories available.</p>
                    <div style="margin-top: 30px;">
                        <a href="events.php" class="btn-book" style="display: inline-block;">Browse All Events</a>
                    </div>
                </div>
            <?php else: ?>
                <div class="categories-grid">
                    <?php foreach($categories as $category): 
                        $icons = [
                            'Music' => '🎵',
                            'Sport' => '⚽',
                            'Fashion' => '👗',
                            'Art & Design' => '🎨',
                            'Food & Culinary' => '🍕',
                            'Technology' => '💻',
                            'Health & Wellness' => '💊',
                            'Outdoor & Adventure' => '🏕️',
                            'Business' => '💼',
                            'Education' => '📚',
                            'Entertainment' => '🎭',
                            'Community' => '👥'
                        ];
                        $icon = $icons[$category['category']] ?? '📅';
                    ?>
                    <a href="events.php?category=<?php echo urlencode($category['category']); ?>" class="category-card scroll-reveal">
                        <span class="category-icon"><?php echo $icon; ?></span>
                        <h3><?php echo $category['category']; ?></h3>
                        <p>Explore <?php echo $category['category']; ?> events</p>
                        <span class="category-count"><?php echo $category['event_count']; ?> Event<?php echo $category['event_count'] !== 1 ? 's' : ''; ?></span>
                    </a>
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
        // Enhanced Categories Page JavaScript
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

            // Add to cart functionality
            const cartCount = document.querySelector('.cart-count');
            let cartItems = 0;

            // Category card hover effects
            const categoryCards = document.querySelectorAll('.category-card');
            categoryCards.forEach(card => {
                card.addEventListener('mouseenter', function() {
                    this.style.zIndex = '10';
                });
                
                card.addEventListener('mouseleave', function() {
                    this.style.zIndex = '1';
                });
            });

            console.log('Categories page enhanced with animations and interactions!');
        });
    </script>
</body>
</html>