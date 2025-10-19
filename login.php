<?php
require_once 'config.php';

$error = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = trim($_POST['uname']);
    $password = $_POST['pass'];
    
    if (!empty($username) && !empty($password)) {
        try {
            $stmt = $pdo->prepare("SELECT * FROM users WHERE username = ? AND status = 1");
            $stmt->execute([$username]);
            $user = $stmt->fetch();
            
            if ($user && password_verify($password, $user['password'])) {
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['username'] = $user['username'];
                $_SESSION['role'] = $user['role'];
                
                redirectByRole($user['role']);
            } else {
                $error = "Invalid username or password!";
            }
        } catch (PDOException $e) {
            $error = "Database error: " . $e->getMessage();
        }
    } else {
        $error = "Please fill all fields!";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Dr. Hilla Lumann Technical University</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css">
    <link rel="icon" href="logo.png">
    <style>
        :root {
            --primary-blue: #002B5C;
            --accent-gold: #FFB81C;
            --pure-white: #ffffff;
            --light-gray: #f5f5f5;
            --dark-gray: #333333;
        }
        
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, var(--primary-blue) 0%, #003a7a 100%);
            height: 100vh;
            overflow-x: hidden;
            margin: 0;
            padding: 0;
        }
        
        .login-container {
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            padding: 20px;
        }
        
        .login-card {
            background: var(--pure-white);
            border-radius: 12px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.15);
            width: 100%;
            max-width: 440px;
            overflow: hidden;
            transform: translateY(0);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }
        
        .login-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.2);
        }
        
        .login-header {
            background: var(--primary-blue);
            padding: 25px 20px;
            text-align: center;
            position: relative;
        }
        
        .login-header::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 0;
            width: 100%;
            height: 4px;
            background: var(--accent-gold);
        }
        
        .logo-container {
            background: var(--pure-white);
            width: 90px;
            height: 90px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 15px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
        }
        
        .logo-container img {
            width: 70px;
            height: 70px;
            object-fit: contain;
        }
        
        .login-title {
            color: var(--pure-white);
            font-weight: 600;
            font-size: 1.5rem;
            margin: 0;
        }
        
        .login-subtitle {
            color: rgba(255, 255, 255, 0.8);
            font-size: 0.9rem;
            margin-top: 5px;
        }
        
        .login-body {
            padding: 25px;
        }
        
        .form-label {
            font-weight: 500;
            color: var(--dark-gray);
            margin-bottom: 8px;
            display: flex;
            align-items: center;
        }
        
        .form-label i {
            margin-right: 8px;
            color: var(--primary-blue);
        }
        
        .form-control {
            padding: 12px 15px;
            border: 1px solid #ddd;
            border-radius: 6px;
            transition: all 0.3s;
        }
        
        .form-control:focus {
            border-color: var(--primary-blue);
            box-shadow: 0 0 0 0.25rem rgba(0, 43, 92, 0.15);
        }
        
        .input-group-text {
            background: var(--pure-white);
            border: 1px solid #ddd;
            cursor: pointer;
            transition: all 0.3s;
        }
        
        .input-group-text:hover {
            background: var(--light-gray);
        }
        
        .alert-danger {
            background: rgba(220, 53, 69, 0.1);
            color: #dc3545;
            border: none;
            border-radius: 6px;
            padding: 10px 15px;
            font-size: 0.9rem;
        }
        
        .btn-login {
            background: var(--primary-blue);
            border: none;
            color: white;
            padding: 12px;
            font-weight: 600;
            border-radius: 6px;
            width: 100%;
            transition: all 0.3s;
            margin-top: 10px;
        }
        
        .btn-login:hover {
            background: #001a36;
            transform: translateY(-2px);
        }
        
        .btn-login:active {
            transform: translateY(0);
        }
        
        .home-link {
            color: var(--primary-blue);
            text-decoration: none;
            display: inline-block;
            margin-top: 15px;
            font-weight: 500;
            transition: all 0.3s;
        }
        
        .home-link:hover {
            color: #001a36;
            text-decoration: underline;
        }
        
        .login-footer {
            text-align: center;
            padding: 20px;
            background: var(--light-gray);
            color: var(--dark-gray);
            font-size: 0.8rem;
        }
        
        /* Animation for form elements */
        .animate-form {
            animation: fadeIn 0.6s ease forwards;
        }
        
        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(10px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        
        /* Responsive adjustments */
        @media (max-width: 576px) {
            .login-card {
                margin: 0 15px;
            }
            
            .login-body {
                padding: 20px;
            }
        }
        
        /* Password strength indicator */
        .password-strength {
            height: 5px;
            margin-top: 5px;
            border-radius: 3px;
            background: #eee;
            overflow: hidden;
        }
        
        .password-strength-bar {
            height: 100%;
            width: 0;
            transition: width 0.3s;
        }
        
        /* Error message styling */
        .error-message {
            background: rgba(220, 53, 69, 0.1);
            color: #dc3545;
            border: none;
            border-radius: 6px;
            padding: 10px 15px;
            font-size: 0.9rem;
            margin-bottom: 20px;
        }
    </style>
</head>
<body>
    <div class="login-container">
        <div class="login-card animate__animated animate__fadeInUp">
            <div class="login-header">
                <div class="logo-container">
                    <img src="logo.png" alt="University Logo">
                </div>
                <h1 class="login-title">DR. HILLA LUMANN TECHNICAL UNIVERSITY</h1>
                <p class="login-subtitle">School Management System</p>
            </div>
            
            <div class="login-body">
                <form method="POST" action="" class="needs-validation" novalidate>
                    <?php if (!empty($error)): ?>
                        <div class="alert alert-danger mb-4 animate__animated animate__shakeX" role="alert">
                            <i class="fas fa-exclamation-circle me-2"></i>
                            <?php echo htmlspecialchars($error); ?>
                        </div>
                    <?php endif; ?>
                    
                    <div class="mb-4 animate-form" style="animation-delay: 0.1s">
                        <label for="username" class="form-label">
                            <i class="fas fa-user"></i>Username
                        </label>
                        <input type="text" 
                               class="form-control"
                               id="username"
                               name="uname"
                               required
                               placeholder="Enter your username">
                        <div class="invalid-feedback">
                            Please enter your username.
                        </div>
                    </div>
                    
                    <div class="mb-4 animate-form" style="animation-delay: 0.2s">
                        <label for="password" class="form-label">
                            <i class="fas fa-lock"></i>Password
                        </label>
                        <div class="input-group">
                            <input type="password" 
                                   class="form-control"
                                   id="password"
                                   name="pass"
                                   required
                                   placeholder="Enter your password">
                            <span class="input-group-text" id="togglePassword">
                                <i class="fas fa-eye"></i>
                            </span>
                            <div class="invalid-feedback">
                                Please enter your password.
                            </div>
                        </div>
                        <div class="password-strength mt-2">
                            <div class="password-strength-bar"></div>
                        </div>
                    </div>
                    
                    <button type="submit" class="btn btn-login animate-form" style="animation-delay: 0.3s">
                        <i class="fas fa-sign-in-alt me-2"></i>Login
                    </button>
                    
                    <div class="text-center mt-3 animate-form" style="animation-delay: 0.4s">
                        <a href="index.php" class="home-link">
                            <i class="fas fa-home me-1"></i>Back to Home
                        </a>
                    </div>
                </form>
            </div>
            
            <div class="login-footer">
                Copyright &copy; 2023 Dr. Hilla Lumann Technical University. All rights reserved.
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Password visibility toggle
        document.addEventListener('DOMContentLoaded', function() {
            const togglePassword = document.querySelector('#togglePassword');
            const password = document.querySelector('#password');
            
            if (togglePassword && password) {
                togglePassword.addEventListener('click', function() {
                    // Toggle the type attribute
                    const type = password.getAttribute('type') === 'password' ? 'text' : 'password';
                    password.setAttribute('type', type);
                    
                    // Toggle the eye icon
                    this.querySelector('i').classList.toggle('fa-eye');
                    this.querySelector('i').classList.toggle('fa-eye-slash');
                });
            }
            
            // Form validation
            const forms = document.querySelector('.needs-validation');
            if (forms) {
                forms.addEventListener('submit', function(event) {
                    if (!forms.checkValidity()) {
                        event.preventDefault();
                        event.stopPropagation();
                    }
                    forms.classList.add('was-validated');
                }, false);
            }
            
            // Simple password strength indicator
            if (password) {
                password.addEventListener('input', function() {
                    const strengthBar = document.querySelector('.password-strength-bar');
                    const value = password.value;
                    let strength = 0;
                    
                    if (value.length > 0) strength += 20;
                    if (value.length >= 8) strength += 20;
                    if (/[A-Z]/.test(value)) strength += 20;
                    if (/[0-9]/.test(value)) strength += 20;
                    if (/[^A-Za-z0-9]/.test(value)) strength += 20;
                    
                    if (strengthBar) {
                        strengthBar.style.width = strength + '%';
                        
                        // Color coding
                        if (strength < 40) {
                            strengthBar.style.background = '#dc3545'; // Weak - red
                        } else if (strength < 80) {
                            strengthBar.style.background = '#ffc107'; // Medium - yellow
                        } else {
                            strengthBar.style.background = '#198754'; // Strong - green
                        }
                    }
                });
            }
        });
    </script>
</body>
</html>