<?php
require_once 'config.php';
checkRole(['admin', 'registrar', 'finance', 'teacher', 'student', 'cleaner', 'transport']);

$page_title = "My Profile";
$message = '';

// Get user details
$user = getUserDetails($_SESSION['user_id']);

// Get role-specific details
$role_details = [];
switch ($_SESSION['role']) {
    case 'student':
        $role_details = getStudentDetails($_SESSION['user_id']);
        break;
    case 'teacher':
        $role_details = getTeacherDetails($_SESSION['user_id']);
        break;
    case 'cleaner':
        $stmt = $pdo->prepare("SELECT * FROM cleaners WHERE user_id = ?");
        $stmt->execute([$_SESSION['user_id']]);
        $role_details = $stmt->fetch();
        break;
    case 'transport':
        $stmt = $pdo->prepare("SELECT * FROM transport WHERE user_id = ?");
        $stmt->execute([$_SESSION['user_id']]);
        $role_details = $stmt->fetch();
        break;
}

// Handle form actions
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['update_profile'])) {
        $username = trim($_POST['username']);
        $email = trim($_POST['email']);
        $phone = trim($_POST['phone']);
        
        try {
            $stmt = $pdo->prepare("UPDATE users SET username = ? WHERE id = ?");
            $stmt->execute([$username, $_SESSION['user_id']]);
            
            // Update role-specific details
            switch ($_SESSION['role']) {
                case 'student':
                    $stmt = $pdo->prepare("UPDATE students SET contact = ? WHERE user_id = ?");
                    $stmt->execute([$phone, $_SESSION['user_id']]);
                    break;
                case 'teacher':
                    $stmt = $pdo->prepare("UPDATE teachers SET contact = ? WHERE user_id = ?");
                    $stmt->execute([$phone, $_SESSION['user_id']]);
                    break;
            }
            
            $_SESSION['username'] = $username;
            $message = "Profile updated successfully!";
        } catch (PDOException $e) {
            $message = "Error updating profile: " . $e->getMessage();
        }
    } elseif (isset($_POST['change_password'])) {
        $current_password = $_POST['current_password'];
        $new_password = $_POST['new_password'];
        $confirm_password = $_POST['confirm_password'];
        
        if ($new_password !== $confirm_password) {
            $message = "New passwords do not match!";
        } else {
            // Verify current password
            $stmt = $pdo->prepare("SELECT password FROM users WHERE id = ?");
            $stmt->execute([$_SESSION['user_id']]);
            $db_password = $stmt->fetch()['password'];
            
            if (password_verify($current_password, $db_password)) {
                $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
                $stmt = $pdo->prepare("UPDATE users SET password = ? WHERE id = ?");
                $stmt->execute([$hashed_password, $_SESSION['user_id']]);
                $message = "Password changed successfully!";
            } else {
                $message = "Current password is incorrect!";
            }
        }
    }
}

// Refresh user details after update
$user = getUserDetails($_SESSION['user_id']);
if ($_SESSION['role'] == 'student') {
    $role_details = getStudentDetails($_SESSION['user_id']);
} elseif ($_SESSION['role'] == 'teacher') {
    $role_details = getTeacherDetails($_SESSION['user_id']);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="assets/css/dashboard.css">
</head>
<body>
   <?php include 'includes/header.php'; ?>
<?php include 'includes/sidebar.php'; ?>

  
<main class="main-content">
    <div class="content-header">
        <h1>My Profile</h1>
    </div>
    
    <?php if (!empty($message)): ?>
        <div class="alert <?php echo strpos($message, 'Error') !== false ? 'alert-error' : 'alert-success'; ?>">
            <?php echo $message; ?>
        </div>
    <?php endif; ?>
    
    <div class="card-grid">
        <div class="card">
            <h3>Personal Information</h3>
            <form method="POST">
                <div class="form-group">
                    <label for="username">Username</label>
                    <input type="text" id="username" name="username" required 
                           value="<?php echo $user['username']; ?>">
                </div>
                
                <div class="form-group">
                    <label for="email">Email</label>
                    <input type="email" id="email" name="email" 
                           value="<?php echo $role_details['email'] ?? ''; ?>">
                </div>
                
                <div class="form-group">
                    <label for="phone">Phone</label>
                    <input type="text" id="phone" name="phone" 
                           value="<?php echo $role_details['contact'] ?? $role_details['guardian_contact'] ?? ''; ?>">
                </div>
                
                <div class="form-group">
                    <label>Role</label>
                    <p class="readonly-field"><?php echo ucfirst($_SESSION['role']); ?></p>
                </div>
                
                <?php if ($_SESSION['role'] == 'student' && $role_details): ?>
                    <div class="form-group">
                        <label>Class</label>
                        <p class="readonly-field"><?php echo $role_details['class_name'] ?? 'Not assigned'; ?></p>
                    </div>
                    
                    <div class="form-group">
                        <label>Guardian</label>
                        <p class="readonly-field"><?php echo $role_details['guardian_name'] ?? 'N/A'; ?></p>
                    </div>
                <?php endif; ?>
                
                <?php if ($_SESSION['role'] == 'teacher' && $role_details): ?>
                    <div class="form-group">
                        <label>Specialization</label>
                        <p class="readonly-field"><?php echo $role_details['specialization'] ?? 'N/A'; ?></p>
                    </div>
                <?php endif; ?>
                
                <?php if ($_SESSION['role'] == 'cleaner' && $role_details): ?>
                    <div class="form-group">
                        <label>Assigned Area</label>
                        <p class="readonly-field"><?php echo $role_details['assigned_area'] ?? 'N/A'; ?></p>
                    </div>
                <?php endif; ?>
                
                <?php if ($_SESSION['role'] == 'transport' && $role_details): ?>
                    <div class="form-group">
                        <label>Route</label>
                        <p class="readonly-field"><?php echo $role_details['route'] ?? 'N/A'; ?></p>
                    </div>
                <?php endif; ?>
                
                <div class="form-group">
                    <button type="submit" name="update_profile" class="btn btn-primary">Update Profile</button>
                </div>
            </form>
        </div>
        
        <div class="card">
            <h3>Change Password</h3>
            <form method="POST">
                <div class="form-group">
                    <label for="current_password">Current Password</label>
                    <input type="password" id="current_password" name="current_password" required>
                </div>
                
                <div class="form-group">
                    <label for="new_password">New Password</label>
                    <input type="password" id="new_password" name="new_password" required 
                           minlength="6" placeholder="Minimum 6 characters">
                </div>
                
                <div class="form-group">
                    <label for="confirm_password">Confirm New Password</label>
                    <input type="password" id="confirm_password" name="confirm_password" required>
                </div>
                
                <div class="form-group">
                    <button type="submit" name="change_password" class="btn btn-primary">Change Password</button>
                </div>
            </form>
        </div>
    </div>
</main>

<?php include 'includes/footer.php'; ?>

<style>
.readonly-field {
    background: #f8f9fa;
    padding: 0.75rem;
    border-radius: 5px;
    border: 1px solid #e9ecef;
    margin: 0;
}
</style> 
</body>
</html>
