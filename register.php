<?php
session_start();
require_once 'config/database.php';

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $database = new Database();
    $db = $database->getConnection();
    
    $firstName = trim($_POST['first_name']);
    $lastName = trim($_POST['last_name']);
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    $confirmPassword = $_POST['confirm_password'];
    $phone = trim($_POST['phone']);
    
    // Validate inputs
    if (empty($firstName) || empty($lastName) || empty($email) || empty($password) || empty($confirmPassword)) {
        $error = 'Please fill in all required fields.';
    } elseif ($password !== $confirmPassword) {
        $error = 'Passwords do not match.';
    } elseif (strlen($password) < 6) {
        $error = 'Password must be at least 6 characters long.';
    } else {
        // Check if email already exists
        $stmt = $db->prepare("SELECT id FROM users WHERE email = ?");
        $stmt->execute([$email]);
        
        if ($stmt->fetch()) {
            $error = 'Email address is already registered.';
        } else {
            // Hash password
            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
            
            // Insert new user
            $stmt = $db->prepare("INSERT INTO users (first_name, last_name, email, password_hash, phone, role, status, created_at) VALUES (?, ?, ?, ?, ?, 'user', 'active', NOW())");
            
            if ($stmt->execute([$firstName, $lastName, $email, $hashedPassword, $phone])) {
                $success = 'Account created successfully! You can now login.';
                
                // Clear form
                $_POST = array();
            } else {
                $error = 'Something went wrong. Please try again.';
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign Up - Eventisa</title>
    <link rel="stylesheet" href="css/user-styles.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        :root {
            --primary: #8b5cf6;
            --primary-dark: #7c3aed;
            --secondary: #ec4899;
            --accent1: #e79df6;
            --accent2: #b08bff;
            --text: #1f2937;
            --text-light: #6b7280;
            --bg: #f8fafc;
            --card-bg: #ffffff;
            --border: #e5e7eb;
            --success: #10b981;
            --warning: #f59e0b;
            --error: #ef4444;
            --shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
            --shadow-lg: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
            --radius: 12px;
            --radius-sm: 8px;
        }

        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
            line-height: 1.6;
        }

        .auth-container {
            width: 100%;
            max-width: 620px; /* Increased width for a more spacious form */
            margin: 0 auto;
            perspective: 1200px;
        }

        .auth-card {
            background: var(--card-bg);
            border-radius: var(--radius);
            padding: 56px 56px 40px 56px; /* More padding for wider card */
            box-shadow: var(--shadow-lg);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.2);
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
            margin-bottom: 32px;
        }

        .auth-logo {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 12px;
            margin-bottom: 24px;
            text-decoration: none;
            color: inherit;
        }

        .logo-icon {
            width: 40px;
            height: 40px;
            background: linear-gradient(135deg, var(--accent1), var(--accent2));
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: 800;
            font-size: 18px;
            box-shadow: var(--shadow);
        }

        .brand-name {
            font-size: 24px;
            font-weight: 800;
            color: var(--text);
        }

        .auth-title {
            font-size: 28px;
            font-weight: 700;
            color: var(--text);
            margin-bottom: 8px;
            background: linear-gradient(135deg, var(--primary), var(--secondary));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .auth-subtitle {
            color: var(--text-light);
            font-size: 16px;
        }

        .auth-form {
            display: flex;
            flex-direction: column;
            gap: 20px;
        }

        .form-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 16px;
        }

        .form-group {
            display: flex;
            flex-direction: column;
            gap: 8px;
        }

        .form-label {
            font-weight: 600;
            color: var(--text);
            font-size: 14px;
            display: flex;
            align-items: center;
            gap: 4px;
        }

        .required {
            color: var(--error);
            font-size: 12px;
        }

        .form-input {
            padding: 14px 16px;
            border: 2px solid var(--border);
            border-radius: var(--radius-sm);
            font-size: 16px;
            transition: all 0.3s ease;
            background: var(--bg);
            font-family: inherit;
        }

        .form-input:focus {
            outline: none;
            border-color: var(--primary);
            box-shadow: 0 0 0 3px rgba(139, 92, 246, 0.1);
            background: white;
        }

        .form-input::placeholder {
            color: #9ca3af;
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
            color: var(--text-light);
            cursor: pointer;
            padding: 4px;
            border-radius: 4px;
            transition: all 0.2s ease;
        }

        .toggle-password:hover {
            background: var(--bg);
            color: var(--text);
        }

        .password-strength {
            margin-top: 8px;
        }

        .strength-info {
            display: flex;
            justify-content: space-between;
            align-items: center;
            font-size: 12px;
            margin-bottom: 4px;
        }

        .strength-meter {
            height: 6px;
            background: var(--border);
            border-radius: 3px;
            overflow: hidden;
        }

        .strength-fill {
            height: 100%;
            width: 0%;
            transition: all 0.3s ease;
            border-radius: 3px;
        }

        .terms {
            display: flex;
            align-items: flex-start;
            gap: 12px;
            padding: 16px;
            background: var(--bg);
            border-radius: var(--radius-sm);
            margin: 8px 0;
        }

        .terms input[type="checkbox"] {
            margin-top: 2px;
            accent-color: var(--primary);
        }

        .terms label {
            font-size: 14px;
            color: var(--text);
            line-height: 1.5;
        }

        .terms a {
            color: var(--primary);
            text-decoration: none;
            font-weight: 500;
        }

        .terms a:hover {
            text-decoration: underline;
        }

        .btn-auth {
            padding: 16px;
            background: linear-gradient(135deg, var(--primary), var(--secondary));
            color: white;
            border: none;
            border-radius: var(--radius-sm);
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
            font-family: inherit;
            margin-top: 8px;
        }

        .btn-auth:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(139, 92, 246, 0.4);
        }

        .btn-auth:active {
            transform: translateY(0);
        }

        .btn-auth:disabled {
            opacity: 0.6;
            cursor: not-allowed;
            transform: none;
            box-shadow: none;
        }

        .auth-footer {
            text-align: center;
            margin-top: 32px;
            padding-top: 24px;
            border-top: 1px solid var(--border);
        }

        .auth-footer p {
            color: var(--text-light);
            font-size: 14px;
        }

        .auth-footer a {
            color: var(--primary);
            text-decoration: none;
            font-weight: 600;
            margin-left: 4px;
        }

        .auth-footer a:hover {
            text-decoration: underline;
        }

        .alert {
            padding: 16px;
            border-radius: var(--radius-sm);
            margin-bottom: 24px;
            font-size: 14px;
            display: flex;
            align-items: center;
            gap: 12px;
            animation: slideDown 0.3s ease-out;
        }

        @keyframes slideDown {
            from {
                opacity: 0;
                transform: translateY(-10px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .alert-error {
            background: #fef2f2;
            border: 1px solid #fecaca;
            color: var(--error);
        }

        .alert-success {
            background: #f0fdf4;
            border: 1px solid #bbf7d0;
            color: var(--success);
        }

        .alert-icon {
            font-size: 18px;
            flex-shrink: 0;
        }

        .home-button {
            position: fixed;
            top: 24px;
            left: 24px;
            background: rgba(255, 255, 255, 0.9);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.2);
            border-radius: var(--radius-sm);
            padding: 12px 20px;
            font-size: 14px;
            font-weight: 600;
            color: var(--text);
            text-decoration: none;
            display: flex;
            align-items: center;
            gap: 8px;
            box-shadow: var(--shadow);
            transition: all 0.3s ease;
            z-index: 1000;
        }

        .home-button:hover {
            background: white;
            transform: translateY(-2px);
            box-shadow: var(--shadow-lg);
            color: var(--primary);
        }

        .ripple {
            position: absolute;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.6);
            transform: scale(0);
            animation: ripple 0.6s linear;
            pointer-events: none;
        }

        @keyframes ripple {
            to {
                transform: scale(4);
                opacity: 0;
            }
        }

        @media (max-width: 640px) {
            .auth-card {
                padding: 32px 24px;
            }
            
            .form-row {
                grid-template-columns: 1fr;
            }
            
            .home-button {
                top: 16px;
                left: 16px;
                padding: 10px 16px;
                font-size: 13px;
            }
            
            .auth-title {
                font-size: 24px;
            }
        }

        @media (max-width: 480px) {
            body {
                padding: 16px;
            }
            
            .auth-card {
                padding: 24px 20px;
            }
            
            .home-button {
                position: relative;
                top: auto;
                left: auto;
                margin-bottom: 20px;
                align-self: flex-start;
            }
            
            .auth-container {
                display: flex;
                flex-direction: column;
            }
        }

        /* Password match indicator */
        .password-match {
            font-size: 12px;
            margin-top: 4px;
            display: flex;
            align-items: center;
            gap: 4px;
        }

        .match-valid {
            color: var(--success);
        }

        .match-invalid {
            color: var(--error);
        }
    </style>
</head>
<body>
    <!-- Home Button -->
    <a href="index.php" class="home-button">
        <span>🏠</span>
        Back to Home
    </a>

    <div class="auth-container">
        <div class="auth-card">
            <div class="auth-header">
                <!-- Clickable Logo -->
                <a href="index.php" class="auth-logo">
                    <div class="logo-icon">E</div>
                    <span class="brand-name">Eventisa</span>
                </a>
                <h1 class="auth-title">Join Eventisa</h1>
                <p class="auth-subtitle">Create your account and start exploring amazing events</p>
            </div>

            <?php if ($error): ?>
                <div class="alert alert-error">
                    <span class="alert-icon">⚠️</span>
                    <?php echo htmlspecialchars($error); ?>
                </div>
            <?php endif; ?>

            <?php if ($success): ?>
                <div class="alert alert-success">
                    <span class="alert-icon">✅</span>
                    <?php echo htmlspecialchars($success); ?>
                </div>
            <?php endif; ?>

            <form class="auth-form" method="POST" action="">
                <div class="form-row">
                    <div class="form-group">
                        <label for="first_name" class="form-label">
                            First Name <span class="required">*</span>
                        </label>
                        <input 
                            type="text" 
                            id="first_name" 
                            name="first_name" 
                            class="form-input" 
                            placeholder="John"
                            value="<?php echo isset($_POST['first_name']) ? htmlspecialchars($_POST['first_name']) : ''; ?>"
                            required
                        >
                    </div>
                    
                    <div class="form-group">
                        <label for="last_name" class="form-label">
                            Last Name <span class="required">*</span>
                        </label>
                        <input 
                            type="text" 
                            id="last_name" 
                            name="last_name" 
                            class="form-input" 
                            placeholder="Doe"
                            value="<?php echo isset($_POST['last_name']) ? htmlspecialchars($_POST['last_name']) : ''; ?>"
                            required
                        >
                    </div>
                </div>

                <div class="form-group">
                    <label for="email" class="form-label">
                        Email Address <span class="required">*</span>
                    </label>
                    <input 
                        type="email" 
                        id="email" 
                        name="email" 
                        class="form-input" 
                        placeholder="john@example.com"
                        value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>"
                        required
                    >
                </div>

                <div class="form-group">
                    <label for="phone" class="form-label">Phone Number</label>
                    <input 
                        type="tel" 
                        id="phone" 
                        name="phone" 
                        class="form-input" 
                        placeholder="+1 (555) 123-4567"
                        value="<?php echo isset($_POST['phone']) ? htmlspecialchars($_POST['phone']) : ''; ?>"
                    >
                </div>

                <div class="form-group">
                    <label for="password" class="form-label">
                        Password <span class="required">*</span>
                    </label>
                    <div class="password-toggle">
                        <input 
                            type="password" 
                            id="password" 
                            name="password" 
                            class="form-input" 
                            placeholder="Create a strong password"
                            required
                            oninput="checkPasswordStrength()"
                        >
                        <button type="button" class="toggle-password" onclick="togglePassword('password')">
                            👁️
                        </button>
                    </div>
                    <div class="password-strength">
                        <div class="strength-info">
                            <span>Password strength</span>
                            <span id="strength-text">None</span>
                        </div>
                        <div class="strength-meter">
                            <div class="strength-fill" id="strength-fill"></div>
                        </div>
                    </div>
                </div>

                <div class="form-group">
                    <label for="confirm_password" class="form-label">
                        Confirm Password <span class="required">*</span>
                    </label>
                    <div class="password-toggle">
                        <input 
                            type="password" 
                            id="confirm_password" 
                            name="confirm_password" 
                            class="form-input" 
                            placeholder="Confirm your password"
                            required
                            oninput="checkPasswordMatch()"
                        >
                        <button type="button" class="toggle-password" onclick="togglePassword('confirm_password')">
                            👁️
                        </button>
                    </div>
                    <div id="password-match" class="password-match"></div>
                </div>

                <div class="terms">
                    <input type="checkbox" id="terms" name="terms" required>
                    <label for="terms">
                        I agree to the <a href="terms.php">Terms of Service</a> and <a href="privacy.php">Privacy Policy</a>
                    </label>
                </div>

                <button type="submit" class="btn-auth" id="submit-btn">
                    Create Account
                </button>
            </form>

            <div class="auth-footer">
                <p>Already have an account? <a href="login.php">Sign in here</a></p>
            </div>
        </div>
    </div>

    <script>
        function togglePassword(fieldId) {
            const passwordInput = document.getElementById(fieldId);
            const toggleButton = passwordInput.parentNode.querySelector('.toggle-password');
            
            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                toggleButton.textContent = '🙈';
            } else {
                passwordInput.type = 'password';
                toggleButton.textContent = '👁️';
            }
        }

        function checkPasswordStrength() {
            const password = document.getElementById('password').value;
            const strengthText = document.getElementById('strength-text');
            const strengthFill = document.getElementById('strength-fill');
            
            let strength = 0;
            let color = '#ef4444';
            let text = 'None';
            
            if (password.length >= 6) strength += 25;
            if (password.match(/[a-z]/) && password.match(/[A-Z]/)) strength += 25;
            if (password.match(/\d/)) strength += 25;
            if (password.match(/[^a-zA-Z\d]/)) strength += 25;
            
            if (strength >= 75) {
                color = '#10b981';
                text = 'Strong';
            } else if (strength >= 50) {
                color = '#f59e0b';
                text = 'Medium';
            } else if (strength >= 25) {
                color = '#ea580c';
                text = 'Weak';
            } else if (password.length > 0) {
                color = '#ef4444';
                text = 'Very Weak';
            } else {
                color = '#e5e7eb';
                text = 'None';
            }
            
            strengthText.textContent = text;
            strengthText.style.color = color;
            strengthFill.style.width = strength + '%';
            strengthFill.style.background = color;
        }

        function checkPasswordMatch() {
            const password = document.getElementById('password').value;
            const confirmPassword = document.getElementById('confirm_password').value;
            const matchText = document.getElementById('password-match');
            const submitBtn = document.getElementById('submit-btn');
            
            if (confirmPassword === '') {
                matchText.textContent = '';
                matchText.className = 'password-match';
                submitBtn.disabled = false;
            } else if (password === confirmPassword) {
                matchText.textContent = '✓ Passwords match';
                matchText.className = 'password-match match-valid';
                submitBtn.disabled = false;
            } else {
                matchText.textContent = '✗ Passwords do not match';
                matchText.className = 'password-match match-invalid';
                submitBtn.disabled = true;
            }
        }

        // Add ripple effect to auth button
        document.querySelector('.btn-auth').addEventListener('click', function(e) {
            if (!this.disabled) {
                createRipple(e);
            }
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

        // Initialize checks
        checkPasswordStrength();
        checkPasswordMatch();

        // Add ripple to home button
        document.querySelector('.home-button').addEventListener('click', function(e) {
            createRipple(e);
        });
    </script>
</body>
</html>