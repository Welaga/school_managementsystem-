<?php
require_once 'config.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$error = '';
$success = '';
$redirect = false;

// Process form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $current_password = $_POST['current_password'] ?? '';
    $new_password = $_POST['new_password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';
    
    // Validate inputs
    if (empty($current_password) || empty($new_password) || empty($confirm_password)) {
        $error = "All fields are required.";
    } elseif ($new_password !== $confirm_password) {
        $error = "New passwords do not match.";
    } elseif (strlen($new_password) < 6) {
        $error = "New password must be at least 6 characters long.";
    } else {
        // Get user information
        $user_id = $_SESSION['user_id'];
        $user_role = $_SESSION['role'] ?? '';
        
        // Determine which table to query based on user role
        $table = '';
        $id_field = '';
        
        switch ($user_role) {
            case 'Admin':
                $table = 'admin';
                $id_field = 'admin_id';
                break;
            case 'Teacher':
                $table = 'teachers';
                $id_field = 'teacher_id';
                break;
            case 'Student':
                $table = 'students';
                $id_field = 'student_id';
                break;
            default:
                $table = 'users';
                $id_field = 'id';
        }
        
        try {
            // Verify current password
            $stmt = $pdo->prepare("SELECT password FROM $table WHERE $id_field = ?");
            $stmt->execute([$user_id]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if ($user && password_verify($current_password, $user['password'])) {
                // Update password
                $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
                $update_stmt = $pdo->prepare("UPDATE $table SET password = ? WHERE $id_field = ?");
                
                if ($update_stmt->execute([$hashed_password, $user_id])) {
                    $success = "Password updated successfully! Redirecting to dashboard...";
                    $redirect = true;
                } else {
                    $error = "Failed to update password. Please try again.";
                }
            } else {
                $error = "Current password is incorrect.";
            }
        } catch (PDOException $e) {
            $error = "Database error: " . $e->getMessage();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Change Password</title>
    <style>
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        
        body {
            background: linear-gradient(135deg, #6a11cb 0%, #2575fc 100%);
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            padding: 20px;
        }
        
        .password-container {
            background-color: white;
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
            width: 100%;
            max-width: 450px;
            padding: 40px;
            position: relative;
            overflow: hidden;
        }
        
        .password-container::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 5px;
            background: linear-gradient(90deg, #6a11cb, #2575fc);
        }
        
        h2 {
            text-align: center;
            margin-bottom: 25px;
            color: #333;
            font-size: 28px;
            position: relative;
        }
        
        h2::after {
            content: '';
            display: block;
            width: 50px;
            height: 3px;
            background: linear-gradient(90deg, #6a11cb, #2575fc);
            margin: 10px auto 0;
            border-radius: 2px;
        }
        
        .form-group {
            margin-bottom: 25px;
            position: relative;
        }
        
        label {
            display: block;
            margin-bottom: 10px;
            font-weight: 600;
            color: #444;
            font-size: 15px;
        }
        
        input[type="password"] {
            width: 100%;
            padding: 14px 15px;
            border: 2px solid #e1e5eb;
            border-radius: 8px;
            font-size: 16px;
            transition: all 0.3s;
        }
        
        input[type="password"]:focus {
            border-color: #4a90e2;
            outline: none;
            box-shadow: 0 0 0 3px rgba(74, 144, 226, 0.2);
            transform: translateY(-2px);
        }
        
        .btn {
            background: linear-gradient(90deg, #6a11cb, #2575fc);
            color: white;
            border: none;
            padding: 15px 20px;
            border-radius: 8px;
            cursor: pointer;
            font-size: 17px;
            font-weight: 600;
            width: 100%;
            transition: all 0.3s;
            box-shadow: 0 4px 10px rgba(106, 17, 203, 0.3);
        }
        
        .btn:hover {
            transform: translateY(-3px);
            box-shadow: 0 6px 15px rgba(106, 17, 203, 0.4);
        }
        
        .btn:active {
            transform: translateY(0);
        }
        
        .message {
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 25px;
            text-align: center;
            font-weight: 500;
        }
        
        .error {
            background-color: #ffebee;
            color: #d32f2f;
            border: 1px solid #ffcdd2;
            animation: shake 0.5s;
        }
        
        .success {
            background-color: #e8f5e9;
            color: #388e3c;
            border: 1px solid #c8e6c9;
        }
        
        .password-strength {
            margin-top: 8px;
            height: 6px;
            border-radius: 3px;
            background-color: #eee;
            overflow: hidden;
        }
        
        .strength-meter {
            height: 100%;
            width: 0;
            transition: width 0.4s, background-color 0.4s;
        }
        
        .countdown {
            text-align: center;
            margin-top: 15px;
            font-size: 14px;
            color: #666;
            font-weight: 500;
        }
        
        @keyframes shake {
            0%, 100% { transform: translateX(0); }
            25% { transform: translateX(-5px); }
            75% { transform: translateX(5px); }
        }
        
        .password-toggle {
            position: absolute;
            right: 12px;
            top: 40px;
            cursor: pointer;
            color: #777;
            font-size: 18px;
        }
        
        @media (max-width: 480px) {
            .password-container {
                padding: 25px;
            }
            
            h2 {
                font-size: 24px;
            }
        }
    </style>
</head>
<body>
    <div class="password-container">
        <h2>Change Password</h2>
        
        <?php if ($error): ?>
            <div class="message error"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>
        
        <?php if ($success): ?>
            <div class="message success"><?php echo htmlspecialchars($success); ?></div>
            <div class="countdown" id="countdown">Redirecting in 3 seconds...</div>
        <?php endif; ?>
        
        <form id="passwordForm" method="POST">
            <div class="form-group">
                <label for="current_password">Current Password</label>
                <input type="password" id="current_password" name="current_password" required>
                <span class="password-toggle" id="toggleCurrent">👁️</span>
            </div>
            
            <div class="form-group">
                <label for="new_password">New Password</label>
                <input type="password" id="new_password" name="new_password" required>
                <span class="password-toggle" id="toggleNew">👁️</span>
                <div class="password-strength">
                    <div class="strength-meter" id="strengthMeter"></div>
                </div>
            </div>
            
            <div class="form-group">
                <label for="confirm_password">Confirm New Password</label>
                <input type="password" id="confirm_password" name="confirm_password" required>
                <span class="password-toggle" id="toggleConfirm">👁️</span>
            </div>
            
            <button type="submit" class="btn">Change Password</button>
        </form>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const newPasswordInput = document.getElementById('new_password');
            const confirmPasswordInput = document.getElementById('confirm_password');
            const strengthMeter = document.getElementById('strengthMeter');
            const form = document.getElementById('passwordForm');
            const toggleCurrent = document.getElementById('toggleCurrent');
            const toggleNew = document.getElementById('toggleNew');
            const toggleConfirm = document.getElementById('toggleConfirm');
            const currentPasswordInput = document.getElementById('current_password');
            
            // Toggle password visibility
            toggleCurrent.addEventListener('click', function() {
                const type = currentPasswordInput.getAttribute('type') === 'password' ? 'text' : 'password';
                currentPasswordInput.setAttribute('type', type);
                this.textContent = type === 'password' ? '👁️' : '🔒';
            });
            
            toggleNew.addEventListener('click', function() {
                const type = newPasswordInput.getAttribute('type') === 'password' ? 'text' : 'password';
                newPasswordInput.setAttribute('type', type);
                this.textContent = type === 'password' ? '👁️' : '🔒';
            });
            
            toggleConfirm.addEventListener('click', function() {
                const type = confirmPasswordInput.getAttribute('type') === 'password' ? 'text' : 'password';
                confirmPasswordInput.setAttribute('type', type);
                this.textContent = type === 'password' ? '👁️' : '🔒';
            });
            
            // Password strength indicator
            newPasswordInput.addEventListener('input', function() {
                const password = this.value;
                let strength = 0;
                
                if (password.length >= 8) strength += 25;
                if (/[A-Z]/.test(password)) strength += 25;
                if (/[0-9]/.test(password)) strength += 25;
                if (/[^A-Za-z0-9]/.test(password)) strength += 25;
                
                strengthMeter.style.width = strength + '%';
                
                if (strength < 50) {
                    strengthMeter.style.backgroundColor = '#ff5252';
                } else if (strength < 75) {
                    strengthMeter.style.backgroundColor = '#ffb142';
                } else {
                    strengthMeter.style.backgroundColor = '#2ed573';
                }
            });
            
            // Form validation
            form.addEventListener('submit', function(e) {
                const newPassword = newPasswordInput.value;
                const confirmPassword = confirmPasswordInput.value;
                
                if (newPassword !== confirmPassword) {
                    e.preventDefault();
                    alert('New passwords do not match.');
                    confirmPasswordInput.focus();
                }
                
                if (newPassword.length < 6) {
                    e.preventDefault();
                    alert('Password must be at least 6 characters long.');
                    newPasswordInput.focus();
                }
            });
            
            // Redirect after success
            <?php if ($redirect): ?>
                let seconds = 3;
                const countdownElement = document.getElementById('countdown');
                const countdownInterval = setInterval(function() {
                    seconds--;
                    if (countdownElement) {
                        countdownElement.textContent = `Redirecting in ${seconds} seconds...`;
                    }
                    if (seconds <= 0) {
                        clearInterval(countdownInterval);
                        window.location.href = 'index.php';
                    }
                }, 1000);
            <?php endif; ?>
        });
    </script>
</body>
</html>