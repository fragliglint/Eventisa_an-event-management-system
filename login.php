<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
session_start();

require_once 'config/database.php';

// Initialize variables to prevent undefined variable warnings
$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $database = new Database();
    $db = $database->getConnection();
    
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    
    // Validate inputs
    if (empty($email) || empty($password)) {
        $error = 'Please fill in all fields.';
    } else {
        // Check if user exists
        $stmt = $db->prepare("SELECT * FROM users WHERE email = ? AND status = 'active'");
        $stmt->execute([$email]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($user && password_verify($password, $user['password_hash'])) {
            // Login successful
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user_email'] = $user['email'];
            $_SESSION['user_name'] = $user['first_name'] . ' ' . $user['last_name'];
            $_SESSION['user_role'] = $user['role'];
            
            // Update last login
            $updateStmt = $db->prepare("UPDATE users SET last_login = NOW() WHERE id = ?");
            $updateStmt->execute([$user['id']]);
            
            // Log the activity
            $activityStmt = $db->prepare("INSERT INTO activity_logs (user_id, activity_type, description, ip_address) VALUES (?, 'login', 'User logged into the system', ?)");
            $activityStmt->execute([$user['id'], $_SERVER['REMOTE_ADDR']]);
            
            // Redirect to appropriate page
            if ($user['role'] === 'admin' || $user['role'] === 'manager' || $user['role'] === 'staff') {
                header('Location: Dashboard.php');
                exit();
            } else {
                header('Location: index.php');
                exit();
            }
        } else {
            $error = 'Invalid email or password.';
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Eventisa</title>
    <link rel="stylesheet" href="css/user-styles.css">
    <style>
        :root {
            --primary: #8B5CF6;
            --primary-dark: #7C3AED;
            --secondary: #10B981;
            --accent1: #8B5CF6;
            --accent2: #6366F1;
            --text: #1F2937;
            --text-light: #6B7280;
            --card-bg: #FFFFFF;
            --soft-border: #E5E7EB;
            --muted: #9CA3AF;
            --shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
            --shadow-lg: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
            --shadow-xl: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
            --small-radius: 8px;
            --large-radius: 16px;
            --ease-out: cubic-bezier(0.25, 0.46, 0.45, 0.94);
            --ease-in-out: cubic-bezier(0.42, 0, 0.58, 1);
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, Cantarell, sans-serif;
        }

        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
            overflow-x: hidden;
        }

        .floating-shapes {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            z-index: -1;
            overflow: hidden;
        }

        .shape {
            position: absolute;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.1);
            animation: float 20s infinite linear;
        }

        .shape:nth-child(1) {
            width: 80px;
            height: 80px;
            top: 10%;
            left: 10%;
            animation-delay: 0s;
        }

        .shape:nth-child(2) {
            width: 120px;
            height: 120px;
            top: 70%;
            left: 80%;
            animation-delay: -5s;
        }

        .shape:nth-child(3) {
            width: 60px;
            height: 60px;
            top: 40%;
            left: 85%;
            animation-delay: -10s;
        }

        .shape:nth-child(4) {
            width: 100px;
            height: 100px;
            top: 80%;
            left: 15%;
            animation-delay: -15s;
        }

        @keyframes float {
            0% {
                transform: translateY(0) rotate(0deg);
                opacity: 1;
            }
            33% {
                transform: translateY(-30px) rotate(120deg);
            }
            66% {
                transform: translateY(20px) rotate(240deg);
            }
            100% {
                transform: translateY(0) rotate(360deg);
                opacity: 1;
            }
        }

        /* Make container wider and add entrance animation */
        .auth-container {
            width: 100%;
            max-width: 620px; /* Increased from 440px */
            perspective: 1200px;
            margin: 0 auto;
        }

        .auth-card {
            background: var(--card-bg);
            border-radius: var(--large-radius);
            padding: 56px 56px 40px 56px; /* More padding for wider card */
            box-shadow: var(--shadow-xl);
            animation: cardEntrance 1.1s cubic-bezier(0.23, 1, 0.32, 1);
            transform-style: preserve-3d;
            transition: transform 0.5s var(--ease-out);
            position: relative;
            overflow: hidden;
        }

        @keyframes cardEntrance {
            0% {
                opacity: 0;
                transform: scale(0.92) rotateY(18deg) translateY(60px);
                filter: blur(8px);
            }
            60% {
                opacity: 1;
                transform: scale(1.03) rotateY(-2deg) translateY(-8px);
                filter: blur(0.5px);
            }
            100% {
                opacity: 1;
                transform: scale(1) rotateY(0deg) translateY(0);
                filter: blur(0);
            }
        }

        /* Add floating gradient glow behind card */
        .auth-card::after {
            content: '';
            position: absolute;
            top: -60px;
            left: 50%;
            transform: translateX(-50%);
            width: 340px;
            height: 140px;
            background: radial-gradient(ellipse at center, rgba(139,92,246,0.18) 0%, rgba(99,102,241,0.10) 80%, transparent 100%);
            z-index: 0;
            filter: blur(8px);
            pointer-events: none;
            animation: glowMove 5s ease-in-out infinite alternate;
        }
        @keyframes glowMove {
            0% { left: 48%; }
            100% { left: 52%; }
        }

        .auth-header {
            text-align: center;
            margin-bottom: 30px;
        }

        .auth-logo {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 12px;
            margin-bottom: 20px;
            text-decoration: none;
            color: inherit;
            transition: transform 0.3s ease;
        }

        .auth-logo:hover {
            transform: translateY(-2px);
        }

        .brand-logo {
            width: 40px;
            height: 40px;
            background: linear-gradient(135deg, var(--accent1), var(--accent2));
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: 800;
            font-size: 20px;
            box-shadow: 0 4px 10px rgba(139, 92, 246, 0.3);
        }

        .brand-name {
            font-size: 24px;
            font-weight: 800;
            color: var(--text);
        }

        .auth-title {
            font-size: 2rem;
            font-weight: 800;
            color: var(--text);
            margin-bottom: 8px;
            position: relative;
            display: inline-block;
        }

        .auth-title::after {
            content: '';
            position: absolute;
            bottom: -5px;
            left: 50%;
            transform: translateX(-50%);
            width: 40px;
            height: 3px;
            background: linear-gradient(90deg, var(--accent1), var(--accent2));
            border-radius: 2px;
        }

        .auth-subtitle {
            color: var(--muted);
            font-size: 1rem;
            margin-top: 15px;
        }

        .auth-form {
            display: flex;
            flex-direction: column;
            gap: 20px;
        }

        .form-group {
            display: flex;
            flex-direction: column;
            gap: 8px;
            position: relative;
        }

        .form-label {
            font-weight: 600;
            color: var(--text);
            font-size: 14px;
            transition: all 0.3s ease;
        }

        .form-input {
            padding: 16px;
            border: 2px solid var(--soft-border);
            border-radius: var(--small-radius);
            font-size: 16px;
            transition: all 0.3s var(--ease-out);
            background: var(--card-bg);
            position: relative;
            z-index: 1;
        }

        .form-input:focus {
            outline: none;
            border-color: var(--accent2);
            box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.1);
            transform: translateY(-2px);
        }

        .form-input:focus + .input-highlight {
            width: 100%;
            opacity: 1;
        }

        .input-highlight {
            position: absolute;
            bottom: 0;
            left: 0;
            height: 2px;
            width: 0;
            background: linear-gradient(90deg, var(--accent1), var(--accent2));
            transition: all 0.3s var(--ease-out);
            opacity: 0;
            z-index: 2;
        }

        .password-toggle {
            position: relative;
        }

        .toggle-password {
            position: absolute;
            right: 12px;
            top: 50%;
            transform: translateY(-50%);
            background: none;
            border: none;
            color: var(--muted);
            cursor: pointer;
            padding: 4px;
            transition: all 0.3s ease;
            z-index: 2;
        }

        .toggle-password:hover {
            color: var(--accent2);
            transform: translateY(-50%) scale(1.1);
        }

        .auth-options {
            display: flex;
            justify-content: space-between;
            align-items: center;
            font-size: 14px;
        }

        .remember-me {
            display: flex;
            align-items: center;
            gap: 8px;
            cursor: pointer;
        }

        .remember-me input {
            accent-color: var(--accent2);
        }

        .forgot-password {
            color: var(--accent2);
            text-decoration: none;
            font-weight: 500;
            transition: all 0.3s ease;
            position: relative;
        }

        .forgot-password::after {
            content: '';
            position: absolute;
            bottom: -2px;
            left: 0;
            width: 0;
            height: 1px;
            background: var(--accent2);
            transition: width 0.3s ease;
        }

        .forgot-password:hover::after {
            width: 100%;
        }

        .btn-auth {
            padding: 16px;
            background: linear-gradient(135deg, var(--accent1), var(--accent2));
            color: white;
            border: none;
            border-radius: var(--small-radius);
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s var(--ease-out);
            position: relative;
            overflow: hidden;
            box-shadow: 0 4px 15px rgba(139, 92, 246, 0.3);
        }

        .btn-auth:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 25px rgba(139, 92, 246, 0.4);
        }

        .btn-auth:active {
            transform: translateY(-1px);
        }

        .btn-auth::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.2), transparent);
            transition: left 0.5s;
        }

        .btn-auth:hover::before {
            left: 100%;
        }

        .auth-footer {
            text-align: center;
            margin-top: 30px;
            padding-top: 20px;
            border-top: 1px solid var(--soft-border);
        }

        .auth-footer a {
            color: var(--accent2);
            text-decoration: none;
            font-weight: 600;
            transition: all 0.3s ease;
            position: relative;
        }

        .auth-footer a::after {
            content: '';
            position: absolute;
            bottom: -2px;
            left: 0;
            width: 0;
            height: 1px;
            background: var(--accent2);
            transition: width 0.3s ease;
        }

        .auth-footer a:hover::after {
            width: 100%;
        }

        .alert {
            padding: 12px 16px;
            border-radius: var(--small-radius);
            margin-bottom: 20px;
            font-size: 14px;
            animation: shake 0.5s var(--ease-out);
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .alert-error {
            background: #fef2f2;
            border: 1px solid #fecaca;
            color: #dc2626;
        }

        .alert-success {
            background: #f0fdf4;
            border: 1px solid #bbf7d0;
            color: #16a34a;
        }

        .alert-icon {
            font-size: 18px;
        }

        .home-button {
            position: fixed;
            top: 20px;
            left: 20px;
            background: rgba(255, 255, 255, 0.9);
            border: none;
            border-radius: var(--small-radius);
            padding: 12px 18px;
            font-size: 14px;
            font-weight: 600;
            color: var(--text);
            text-decoration: none;
            display: flex;
            align-items: center;
            gap: 8px;
            box-shadow: var(--shadow);
            transition: all 0.3s ease;
            cursor: pointer;
            backdrop-filter: blur(10px);
            z-index: 100;
        }

        .home-button:hover {
            background: white;
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(0, 0, 0, 0.15);
        }

        .ripple {
            position: absolute;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.6);
            transform: scale(0);
            animation: ripple-animation 0.6s linear;
            pointer-events: none;
        }

        @keyframes ripple-animation {
            to {
                transform: scale(4);
                opacity: 0;
            }
        }

        @keyframes slideInUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        @keyframes shake {
            0%, 100% {
                transform: translateX(0);
            }
            10%, 30%, 50%, 70%, 90% {
                transform: translateX(-5px);
            }
            20%, 40%, 60%, 80% {
                transform: translateX(5px);
            }
        }

        .particles {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            pointer-events: none;
            z-index: -1;
        }

        .particle {
            position: absolute;
            background: rgba(255, 255, 255, 0.5);
            border-radius: 50%;
            animation: float-particle 15s infinite linear;
        }

        @keyframes float-particle {
            0% {
                transform: translateY(100vh) rotate(0deg);
                opacity: 0;
            }
            10% {
                opacity: 1;
            }
            90% {
                opacity: 1;
            }
            100% {
                transform: translateY(-100px) rotate(360deg);
                opacity: 0;
            }
        }

        /* Responsive for mobile */
        @media (max-width: 700px) {
            .auth-container {
                max-width: 98vw;
            }
            .auth-card {
                padding: 32px 8vw 28px 8vw;
            }
        }
        @media (max-width: 480px) {
            .auth-card {
                padding: 24px 6vw 18px 6vw;
            }
        }
    </style>
</head>
<body>
    <!-- Floating Background Shapes -->
    <div class="floating-shapes">
        <div class="shape"></div>
        <div class="shape"></div>
        <div class="shape"></div>
        <div class="shape"></div>
    </div>

    <!-- Particles -->
    <div class="particles" id="particles"></div>

    <!-- Home Button -->
    <a href="index.php" class="home-button">
        🏠 Back to Home
    </a>

    <div class="auth-container">
        <div class="auth-card">
            <div class="auth-header">
                <!-- Logo linking to index.php -->
                <a href="index.php" class="auth-logo">
                    <div class="brand-logo">E</div>
                    <span class="brand-name">Eventisa</span>
                </a>
                <h1 class="auth-title">Welcome Back</h1>
                <p class="auth-subtitle">Sign in to your account to continue</p>
            </div>
            
            <?php if (!empty($error)): ?>
                <div class="alert alert-error">
                    <span class="alert-icon">⚠️</span>
                    <?php echo htmlspecialchars($error); ?>
                </div>
            <?php endif; ?>

            <?php if (!empty($success)): ?>
                <div class="alert alert-success">
                    <span class="alert-icon">✅</span>
                    <?php echo htmlspecialchars($success); ?>
                </div>
            <?php endif; ?>

            <form class="auth-form" method="POST" action="">
                <div class="form-group">
                    <label for="email" class="form-label">Email Address</label>
                    <input 
                        type="email" 
                        id="email" 
                        name="email" 
                        class="form-input" 
                        placeholder="Enter your email"
                        value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>"
                        required
                    >
                    <div class="input-highlight"></div>
                </div>

                <div class="form-group">
                    <label for="password" class="form-label">Password</label>
                    <div class="password-toggle">
                        <input 
                            type="password" 
                            id="password" 
                            name="password" 
                            class="form-input" 
                            placeholder="Enter your password"
                            required
                        >
                        <div class="input-highlight"></div>
                        <button type="button" class="toggle-password" onclick="togglePassword()">
                            👁️
                        </button>
                    </div>
                </div>

                <div class="auth-options">
                    <label class="remember-me">
                        <input type="checkbox" name="remember">
                        <span>Remember me</span>
                    </label>
                    <a href="forgot-password.php" class="forgot-password">Forgot password?</a>
                </div>

                <button type="submit" class="btn-auth">Sign In</button>
            </form>

            <div class="auth-footer">
                <p>Don't have an account? <a href="register.php">Sign up here</a></p>
            </div>
        </div>
    </div>

    <script>
        function togglePassword() {
            const passwordInput = document.getElementById('password');
            const toggleButton = document.querySelector('.toggle-password');
            
            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                toggleButton.textContent = '🙈';
            } else {
                passwordInput.type = 'password';
                toggleButton.textContent = '👁️';
            }
        }

        // Add ripple effect to auth button
        document.querySelector('.btn-auth').addEventListener('click', function(e) {
            createRipple(e);
        });

        function createRipple(event) {
            const button = event.currentTarget;
            const circle = document.createElement('span');
            const diameter = Math.max(button.clientWidth, button.clientHeight);
            const radius = diameter / 2;

            circle.style.width = circle.style.height = `${diameter}px`;
            circle.style.left = `${event.clientX - button.getBoundingClientRect().left - radius}px`;
            circle.style.top = `${event.clientY - button.getBoundingClientRect().top - radius}px`;
            circle.classList.add('ripple');

            const ripple = button.getElementsByClassName('ripple')[0];
            if (ripple) {
                ripple.remove();
            }

            button.appendChild(circle);
        }

        // Create floating particles
        function createParticles() {
            const particlesContainer = document.getElementById('particles');
            const particleCount = 30;
            
            for (let i = 0; i < particleCount; i++) {
                const particle = document.createElement('div');
                particle.classList.add('particle');
                
                // Random size between 2px and 6px
                const size = Math.random() * 4 + 2;
                particle.style.width = `${size}px`;
                particle.style.height = `${size}px`;
                
                // Random position
                particle.style.left = `${Math.random() * 100}%`;
                
                // Random animation delay and duration
                const delay = Math.random() * 15;
                const duration = Math.random() * 10 + 15;
                particle.style.animationDelay = `${delay}s`;
                particle.style.animationDuration = `${duration}s`;
                
                particlesContainer.appendChild(particle);
            }
        }

        // Auto-fill demo credentials for testing
        document.addEventListener('DOMContentLoaded', function() {
            createParticles();
            
            const urlParams = new URLSearchParams(window.location.search);
            if (urlParams.get('demo') === 'admin') {
                document.getElementById('email').value = 'zahid@eventisa.com';
                document.getElementById('password').value = 'admin123';
            } else if (urlParams.get('demo') === 'user') {
                document.getElementById('email').value = 'john@example.com';
                document.getElementById('password').value = 'admin123';
            }

            // Add ripple effect to home button
            const homeButton = document.querySelector('.home-button');
            homeButton.addEventListener('click', function(e) {
                createRipple(e);
            });
            
            // Add focus effects to form inputs
            const inputs = document.querySelectorAll('.form-input');
            inputs.forEach(input => {
                input.addEventListener('focus', function() {
                    this.parentElement.querySelector('.input-highlight').style.width = '100%';
                    this.parentElement.querySelector('.input-highlight').style.opacity = '1';
                });
                
                input.addEventListener('blur', function() {
                    if (!this.value) {
                        this.parentElement.querySelector('.input-highlight').style.width = '0';
                        this.parentElement.querySelector('.input-highlight').style.opacity = '0';
                    }
                });
            });
        });
    </script>
</body>
</html>