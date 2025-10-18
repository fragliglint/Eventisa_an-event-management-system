<?php
session_start();
require_once 'config/database.php';

// Handle contact form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'] ?? '';
    $email = $_POST['email'] ?? '';
    $subject = $_POST['subject'] ?? '';
    $message = $_POST['message'] ?? '';
    
    // Basic validation
    $errors = [];
    if (empty($name)) $errors[] = 'Name is required';
    if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = 'Valid email is required';
    if (empty($subject)) $errors[] = 'Subject is required';
    if (empty($message)) $errors[] = 'Message is required';
    
    if (empty($errors)) {
        // In a real application, you would:
        // 1. Save to database
        // 2. Send email notification
        // 3. Handle the submission
        
        $success_message = "Thank you for your message, $name! We'll get back to you within 24 hours.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contact Us - Eventisa</title>
    <link rel="stylesheet" href="css/user-styles.css">
    <style>
        /* Contact Page Specific Styles */
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
            animation: slideInUp 0.8s ease-out 0.2s both;
        }

        .page-subtitle {
            font-size: 1.25rem;
            opacity: 0.9;
            position: relative;
            z-index: 1;
            animation: slideInUp 0.8s ease-out 0.4s both;
        }

        /* Contact Section */
        .contact-section {
            padding: 60px 0;
            animation: fadeIn 0.8s ease-out both;
        }

        .contact-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 60px;
            margin-top: 40px;
        }

        @media (max-width: 968px) {
            .contact-grid {
                grid-template-columns: 1fr;
                gap: 40px;
            }
        }

        /* Contact Form */
        .contact-form-container {
            animation: slideInLeft 0.8s ease-out 0.3s both;
        }

        .contact-form {
            background: white;
            padding: 40px;
            border-radius: 12px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            border: 1px solid #e5e7eb;
            position: relative;
            overflow: hidden;
        }

        .contact-form::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 4px;
            background: linear-gradient(135deg, #8b5cf6, #6366f1);
        }

        .form-group {
            margin-bottom: 24px;
            animation: slideInUp 0.6s ease-out both;
        }

        .form-group:nth-child(1) { animation-delay: 0.4s; }
        .form-group:nth-child(2) { animation-delay: 0.5s; }
        .form-group:nth-child(3) { animation-delay: 0.6s; }
        .form-group:nth-child(4) { animation-delay: 0.7s; }

        .form-label {
            display: block;
            margin-bottom: 8px;
            font-weight: 600;
            color: #1f2937;
            font-size: 1rem;
        }

        .form-input,
        .form-textarea,
        .form-select {
            width: 100%;
            padding: 14px 18px;
            border: 2px solid #e5e7eb;
            border-radius: 8px;
            background: white;
            font-size: 16px;
            outline: none;
            transition: all 0.3s ease-out;
            font-family: 'Poppins', sans-serif;
        }

        .form-input:focus,
        .form-textarea:focus,
        .form-select:focus {
            border-color: #8b5cf6;
            box-shadow: 0 0 0 3px rgba(139, 92, 246, 0.1);
            transform: translateY(-2px);
        }

        .form-textarea {
            resize: vertical;
            min-height: 120px;
        }

        .submit-btn {
            width: 100%;
            padding: 16px;
            background: linear-gradient(135deg, #8b5cf6, #6366f1);
            color: white;
            border: none;
            border-radius: 8px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease-out;
            position: relative;
            overflow: hidden;
            font-family: 'Poppins', sans-serif;
            font-size: 1.125rem;
            animation: slideInUp 0.6s ease-out 0.8s both;
        }

        .submit-btn::before {
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

        .submit-btn:hover::before {
            width: 300px;
            height: 300px;
        }

        .submit-btn:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 25px rgba(139, 92, 246, 0.4);
        }

        /* Contact Info */
        .contact-info {
            animation: slideInRight 0.8s ease-out 0.3s both;
        }

        .info-card {
            background: white;
            padding: 40px;
            border-radius: 12px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            border: 1px solid #e5e7eb;
            margin-bottom: 30px;
            position: relative;
            overflow: hidden;
            transition: all 0.4s ease-out;
        }

        .info-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 0;
            background: linear-gradient(135deg, #8b5cf6, #6366f1);
            transition: height 0.4s ease-out;
        }

        .info-card:hover::before {
            height: 4px;
        }

        .info-card:hover {
            transform: translateY(-8px);
            box-shadow: 0 20px 40px rgba(16, 24, 40, 0.15);
        }

        .info-card h3 {
            font-size: 1.5rem;
            font-weight: 700;
            margin-bottom: 20px;
            color: #1f2937;
            animation: slideInUp 0.6s ease-out 0.4s both;
        }

        .contact-details {
            animation: slideInUp 0.6s ease-out 0.5s both;
        }

        .contact-item {
            display: flex;
            align-items: flex-start;
            gap: 16px;
            margin-bottom: 20px;
            padding: 16px;
            border-radius: 8px;
            transition: all 0.3s ease-out;
        }

        .contact-item:hover {
            background: #f8fafc;
            transform: translateX(8px);
        }

        .contact-icon {
            font-size: 1.5rem;
            flex-shrink: 0;
            animation: bounceIn 0.8s cubic-bezier(0.68, -0.55, 0.265, 1.55) 0.6s both;
        }

        .contact-detail h4 {
            font-size: 1.125rem;
            font-weight: 600;
            margin-bottom: 4px;
            color: #1f2937;
        }

        .contact-detail p {
            color: #6b7280;
            margin: 0;
            line-height: 1.6;
        }

        /* Alert Messages */
        .alert {
            padding: 16px 20px;
            border-radius: 8px;
            margin-bottom: 24px;
            animation: slideInDown 0.6s ease-out;
        }

        .alert-success {
            background: #d1fae5;
            color: #065f46;
            border: 1px solid #a7f3d0;
        }

        .alert-error {
            background: #fee2e2;
            color: #991b1b;
            border: 1px solid #fecaca;
        }

        /* FAQ Preview */
        .faq-preview {
            background: linear-gradient(135deg, #f8fafc, #f1f5f9);
            padding: 40px;
            border-radius: 12px;
            margin-top: 40px;
            animation: slideInUp 0.8s ease-out 0.6s both;
        }

        .faq-preview h3 {
            font-size: 1.5rem;
            font-weight: 700;
            margin-bottom: 20px;
            color: #1f2937;
        }

        .faq-preview p {
            color: #6b7280;
            margin-bottom: 20px;
        }

        /* Responsive Design */
        @media (max-width: 768px) {
            .page-title {
                font-size: 2.5rem;
            }

            .contact-form,
            .info-card {
                padding: 30px 24px;
            }

            .page-header {
                padding: 60px 0 30px;
            }
        }

        @media (max-width: 480px) {
            .page-title {
                font-size: 2rem;
            }

            .contact-form,
            .info-card {
                padding: 24px 20px;
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

        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
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
                <a href="contact.php" class="nav-link active">Contact Us</a>
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

    <!-- Contact Header -->
    <section class="page-header">
        <div class="container">
            <h1 class="page-title">Contact Us</h1>
            <p class="page-subtitle">We're here to help! Get in touch with our team</p>
        </div>
    </section>

    <!-- Contact Section -->
    <section class="section contact-section">
        <div class="container">
            <!-- Alert Messages -->
            <?php if (isset($success_message)): ?>
                <div class="alert alert-success">
                    <?php echo $success_message; ?>
                </div>
            <?php endif; ?>

            <?php if (!empty($errors)): ?>
                <div class="alert alert-error">
                    <strong>Please fix the following errors:</strong>
                    <ul style="margin: 8px 0 0 20px;">
                        <?php foreach ($errors as $error): ?>
                            <li><?php echo $error; ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            <?php endif; ?>

            <div class="contact-grid">
                <!-- Contact Form -->
                <div class="contact-form-container">
                    <div class="contact-form scroll-reveal">
                        <h2 style="font-size: 2rem; font-weight: 700; margin-bottom: 24px; color: #1f2937; animation: slideInUp 0.6s ease-out 0.3s both;">Send us a Message</h2>
                        <form method="POST" action="contact.php">
                            <div class="form-group">
                                <label class="form-label" for="name">Full Name</label>
                                <input type="text" id="name" name="name" class="form-input" 
                                       value="<?php echo isset($_POST['name']) ? htmlspecialchars($_POST['name']) : ''; ?>" 
                                       placeholder="Enter your full name" required>
                            </div>

                            <div class="form-group">
                                <label class="form-label" for="email">Email Address</label>
                                <input type="email" id="email" name="email" class="form-input" 
                                       value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>" 
                                       placeholder="Enter your email address" required>
                            </div>

                            <div class="form-group">
                                <label class="form-label" for="subject">Subject</label>
                                <select id="subject" name="subject" class="form-select" required>
                                    <option value="">Select a subject</option>
                                    <option value="General Inquiry" <?php echo (isset($_POST['subject']) && $_POST['subject'] === 'General Inquiry') ? 'selected' : ''; ?>>General Inquiry</option>
                                    <option value="Technical Support" <?php echo (isset($_POST['subject']) && $_POST['subject'] === 'Technical Support') ? 'selected' : ''; ?>>Technical Support</option>
                                    <option value="Event Partnership" <?php echo (isset($_POST['subject']) && $_POST['subject'] === 'Event Partnership') ? 'selected' : ''; ?>>Event Partnership</option>
                                    <option value="Billing Issue" <?php echo (isset($_POST['subject']) && $_POST['subject'] === 'Billing Issue') ? 'selected' : ''; ?>>Billing Issue</option>
                                    <option value="Feedback" <?php echo (isset($_POST['subject']) && $_POST['subject'] === 'Feedback') ? 'selected' : ''; ?>>Feedback</option>
                                    <option value="Other" <?php echo (isset($_POST['subject']) && $_POST['subject'] === 'Other') ? 'selected' : ''; ?>>Other</option>
                                </select>
                            </div>

                            <div class="form-group">
                                <label class="form-label" for="message">Message</label>
                                <textarea id="message" name="message" class="form-textarea" 
                                          placeholder="Tell us how we can help you..." required><?php echo isset($_POST['message']) ? htmlspecialchars($_POST['message']) : ''; ?></textarea>
                            </div>

                            <button type="submit" class="submit-btn">Send Message</button>
                        </form>
                    </div>
                </div>

                <!-- Contact Information -->
                <div class="contact-info">
                    <div class="info-card scroll-reveal">
                        <h3>Get in Touch</h3>
                        <div class="contact-details">
                            <div class="contact-item">
                                <span class="contact-icon">📧</span>
                                <div class="contact-detail">
                                    <h4>Email Us</h4>
                                    <p>eventisa.live@gmail.com</p>
                                    <p>We'll respond within 24 hours</p>
                                </div>
                            </div>

                            <div class="contact-item">
                                <span class="contact-icon">📞</span>
                                <div class="contact-detail">
                                    <h4>Call Us</h4>
                                    <p>01858057515</p>
                                    <p>Mon-Fri from 9am to 6pm</p>
                                </div>
                            </div>

                            <div class="contact-item">
                                <span class="contact-icon">🏠</span>
                                <div class="contact-detail">
                                    <h4>Visit Us</h4>
                                    <p>LA-56,Post Office Road, Middle Badda</p>
                                    <p>Dhaka, Bangladesh</p>
                                </div>
                            </div>

                            <div class="contact-item">
                                <span class="contact-icon">🟢</span>
                                <div class="contact-detail">
                                    <h4>WhatsApp</h4>
                                    <p>
                                        <a href="https://wa.me/8801858057515" target="_blank" style="color:#1f2937;text-decoration:underline;">
                                            01858057515
                                        </a>
                                    </p>
                                    <p>Message us anytime on WhatsApp</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="faq-preview scroll-reveal">
                        <h3>Quick Help</h3>
                        <p>Check out our FAQ section for quick answers to common questions.</p>
                        <a href="faq.php" class="submit-btn" style="display: inline-block; width: auto; padding: 12px 24px; text-decoration: none;">View FAQ</a>
                    </div>
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
        // Enhanced Contact Page JavaScript
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

            // Form enhancement
            const contactForm = document.querySelector('.contact-form form');
            if (contactForm) {
                // Add real-time validation
                const inputs = contactForm.querySelectorAll('input, textarea, select');
                inputs.forEach(input => {
                    input.addEventListener('blur', function() {
                        if (this.value.trim() === '') {
                            this.style.borderColor = '#ef4444';
                        } else {
                            this.style.borderColor = '#e5e7eb';
                        }
                    });

                    input.addEventListener('focus', function() {
                        this.style.borderColor = '#8b5cf6';
                    });
                });

                // Form submission animation
                contactForm.addEventListener('submit', function(e) {
                    const submitBtn = this.querySelector('.submit-btn');
                    const originalText = submitBtn.textContent;
                    
                    submitBtn.innerHTML = '<span class="loading-spinner" style="width: 20px; height: 20px; border: 2px solid transparent; border-top: 2px solid white; border-radius: 50%; display: inline-block; animation: spin 1s linear infinite; margin-right: 8px;"></span> Sending...';
                    submitBtn.disabled = true;
                    
                    setTimeout(() => {
                        submitBtn.innerHTML = originalText;
                        submitBtn.disabled = false;
                    }, 3000);
                });
            }

            // Add typing effect to form placeholders
            const messageTextarea = document.getElementById('message');
            if (messageTextarea) {
                const placeholderTexts = [
                    "Tell us about your event needs...",
                    "How can we help you today?",
                    "Describe your inquiry in detail...",
                    "We're here to listen and help!"
                ];
                
                let currentIndex = 0;
                let charIndex = 0;
                let isDeleting = false;
                let currentText = '';
                
                function typeEffect() {
                    currentText = placeholderTexts[currentIndex];
                    
                    if (isDeleting) {
                        messageTextarea.placeholder = currentText.substring(0, charIndex - 1);
                        charIndex--;
                    } else {
                        messageTextarea.placeholder = currentText.substring(0, charIndex + 1);
                        charIndex++;
                    }
                    
                    if (!isDeleting && charIndex === currentText.length) {
                        setTimeout(() => isDeleting = true, 2000);
                    } else if (isDeleting && charIndex === 0) {
                        isDeleting = false;
                        currentIndex = (currentIndex + 1) % placeholderTexts.length;
                    }
                    
                    setTimeout(typeEffect, isDeleting ? 50 : 100);
                }
                
                // Start typing effect
                typeEffect();
            }

            console.log('Contact page enhanced with animations and interactions!');
        });
    </script>
</body>
</html>