<?php
require_once '../config.php';

// Initialize variables
$error = '';
$success = '';

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['register_teacher'])) {
        // Register teacher logic
        $username = $_POST['username'];
        $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
        $first_name = $_POST['first_name'];
        $last_name = $_POST['last_name'];
        $specialization = $_POST['specialization'];
        $contact = $_POST['contact'];
        $email = $_POST['email'];
        
        try {
            // Insert into users table
            $stmt = $pdo->prepare("INSERT INTO users (username, password, role, email, status, created_at) VALUES (?, ?, 'teacher', ?, 1, NOW())");
            $stmt->execute([$username, $password, $email]);
            $user_id = $pdo->lastInsertId();
            
            // Insert into teachers table
            $stmt = $pdo->prepare("INSERT INTO teachers (user_id, first_name, last_name, email, contact, specialization, created_at) VALUES (?, ?, ?, ?, ?, ?, NOW())");
            $stmt->execute([$user_id, $first_name, $last_name, $email, $contact, $specialization]);
            
            $teacher_id = $pdo->lastInsertId();
            $success = "Teacher registered successfully! Teacher ID: " . $teacher_id . " | Username: " . $username;
        } catch (PDOException $e) {
            $error = "Error registering teacher: " . $e->getMessage();
        }
    } 
    elseif (isset($_POST['assign_teacher_class'])) {
        // Assign teacher to class logic
        $teacher_id = $_POST['teacher_id'];
        $class_id = $_POST['class_id'];
        $subject_id = $_POST['subject_id'];
        
        try {
            $stmt = $pdo->prepare("INSERT INTO class_subjects (class_id, subject_id, teacher_id, created_at) VALUES (?, ?, ?, NOW())");
            $stmt->execute([$class_id, $subject_id, $teacher_id]);
            
            $success = "Teacher assigned to class successfully!";
        } catch (PDOException $e) {
            $error = "Error assigning teacher: " . $e->getMessage();
        }
    }
    elseif (isset($_POST['delete_teacher'])) {
        // Delete teacher logic
        $teacher_id = $_POST['teacher_id'];
        
        try {
            // First get the user_id from the teacher
            $stmt = $pdo->prepare("SELECT user_id FROM teachers WHERE id = ?");
            $stmt->execute([$teacher_id]);
            $teacher = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if ($teacher) {
                $user_id = $teacher['user_id'];
                
                // Delete from class_subjects table
                $stmt = $pdo->prepare("DELETE FROM class_subjects WHERE teacher_id = ?");
                $stmt->execute([$teacher_id]);
                
                // Delete from teachers table
                $stmt = $pdo->prepare("DELETE FROM teachers WHERE id = ?");
                $stmt->execute([$teacher_id]);
                
                // Delete from users table
                $stmt = $pdo->prepare("DELETE FROM users WHERE id = ?");
                $stmt->execute([$user_id]);
                
                $success = "Teacher deleted successfully!";
            } else {
                $error = "Teacher not found!";
            }
        } catch (PDOException $e) {
            $error = "Error deleting teacher: " . $e->getMessage();
        }
    }
    elseif (isset($_POST['update_teacher'])) {
        // Update teacher logic
        $teacher_id = $_POST['teacher_id'];
        $first_name = $_POST['first_name'];
        $last_name = $_POST['last_name'];
        $specialization = $_POST['specialization'];
        $contact = $_POST['contact'];
        $email = $_POST['email'];
        
        try {
            // Update teachers table
            $stmt = $pdo->prepare("UPDATE teachers SET first_name = ?, last_name = ?, email = ?, contact = ?, specialization = ? WHERE id = ?");
            $stmt->execute([$first_name, $last_name, $email, $contact, $specialization, $teacher_id]);
            
            // Also update the users table email
            $stmt = $pdo->prepare("UPDATE users SET email = ? WHERE id = (SELECT user_id FROM teachers WHERE id = ?)");
            $stmt->execute([$email, $teacher_id]);
            
            $success = "Teacher updated successfully!";
        } catch (PDOException $e) {
            $error = "Error updating teacher: " . $e->getMessage();
        }
    }
    elseif (isset($_POST['delete_assignment'])) {
        // Delete assignment logic
        $assignment_id = $_POST['assignment_id'];
        
        try {
            $stmt = $pdo->prepare("DELETE FROM class_subjects WHERE id = ?");
            $stmt->execute([$assignment_id]);
            
            $success = "Assignment deleted successfully!";
        } catch (PDOException $e) {
            $error = "Error deleting assignment: " . $e->getMessage();
        }
    }
}

// Fetch data for display
try {
    // Get all teachers
    $teachers = $pdo->query("SELECT * FROM teachers ORDER BY id DESC")->fetchAll(PDO::FETCH_ASSOC);
    
    // Get all classes
    $classes = $pdo->query("SELECT * FROM classes ORDER BY name")->fetchAll(PDO::FETCH_ASSOC);
    
    // Get all subjects
    $subjects = $pdo->query("SELECT * FROM subjects ORDER BY name")->fetchAll(PDO::FETCH_ASSOC);
    
    // Get teacher assignments with related info
    $assignments = $pdo->query("
        SELECT cs.id, t.first_name, t.last_name, c.name AS class_name, s.name AS subject_name, s.code AS subject_code
        FROM class_subjects cs
        JOIN teachers t ON cs.teacher_id = t.id
        JOIN classes c ON cs.class_id = c.id
        JOIN subjects s ON cs.subject_id = s.id
        ORDER BY cs.id DESC
    ")->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $error = "Error fetching data: " . $e->getMessage();
    // Initialize empty arrays to prevent errors
    $teachers = []; $classes = []; $subjects = []; $assignments = [];
}

// Check if we're editing a teacher
$editing_teacher = null;
if (isset($_GET['edit_teacher'])) {
    $teacher_id = $_GET['edit_teacher'];
    foreach ($teachers as $teacher) {
        if ($teacher['id'] == $teacher_id) {
            $editing_teacher = $teacher;
            break;
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Teacher Management System</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        /* Reset and Base Styles */
        * { 
            margin: 0; 
            padding: 0; 
            box-sizing: border-box; 
        }
        
        body { 
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; 
            line-height: 1.6; 
            color: #333; 
            background-color: #f4f6f9;
            display: flex;
            min-height: 100vh;
        }
        
        /* Sidebar Styles */
        .sidebar {
            width: 250px;
            background: linear-gradient(135deg, #2c3e50 0%, #34495e 100%);
            color: white;
            position: fixed;
            height: 100vh;
            overflow-y: auto;
            transition: all 0.3s ease;
            z-index: 1000;
            left: 0;
            top: 0;
        }
        
        .sidebar-header {
            padding: 20px;
            text-align: center;
            border-bottom: 1px solid #34495e;
        }
        
        .sidebar-menu {
            list-style: none;
            padding: 20px 0;
        }
        
        .sidebar-menu li {
            margin-bottom: 5px;
        }
        
        .sidebar-menu a {
            display: block;
            color: #ecf0f1;
            padding: 12px 20px;
            text-decoration: none;
            transition: all 0.3s ease;
            border-left: 4px solid transparent;
        }
        
        .sidebar-menu a:hover {
            background-color: #34495e;
            border-left-color: #667eea;
            padding-left: 25px;
        }
        
        .sidebar-menu a.active {
            background-color: #667eea;
            border-left-color: #fff;
        }
        
        .sidebar-menu i {
            margin-right: 10px;
            width: 20px;
            text-align: center;
        }
        
        /* Main Content Area */
        .main-content {
            flex: 1;
            margin-left: 250px;
            padding: 20px;
            transition: all 0.3s ease;
            min-height: 100vh;
        }
        
        /* Header Styles */
        .content-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 30px;
            border-radius: 10px;
            margin-bottom: 30px;
            text-align: center;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
            position: relative;
        }
        
        .content-header h1 {
            font-size: 2.5rem;
            margin-bottom: 10px;
            font-weight: 300;
        }
        
        .user-info {
            position: absolute;
            top: 20px;
            right: 20px;
            background: rgba(255, 255, 255, 0.2);
            padding: 10px 20px;
            border-radius: 25px;
            font-size: 0.9rem;
        }
        
        /* Tabs Navigation */
        .tabs { 
            display: flex; 
            background-color: #fff; 
            border-radius: 10px; 
            overflow: hidden; 
            margin-bottom: 30px; 
            flex-wrap: wrap;
            box-shadow: 0 2px 15px rgba(0, 0, 0, 0.1);
        }
        
        .tab { 
            padding: 20px 15px; 
            cursor: pointer; 
            font-weight: 600; 
            text-align: center; 
            flex: 1; 
            min-width: 150px; 
            transition: all 0.3s ease; 
            border-bottom: 3px solid transparent;
            color: #666;
        }
        
        .tab:hover {
            background-color: #f8f9fa;
            color: #667eea;
        }
        
        .tab.active { 
            border-bottom: 3px solid #667eea; 
            color: #667eea; 
            background-color: #f8f9fa; 
        }
        
        .tab i {
            margin-right: 10px;
        }
        
        .tab-content { 
            display: none; 
            animation: fadeIn 0.5s ease-in; 
        }
        
        .tab-content.active { 
            display: block; 
        }
        
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }
        
        /* Card Styles */
        .card { 
            background-color: #fff; 
            border-radius: 10px; 
            padding: 25px; 
            margin-bottom: 25px;
            box-shadow: 0 2px 15px rgba(0, 0, 0, 0.1);
            border: 1px solid #eaeaea;
        }
        
        .card-header {
            border-bottom: 2px solid #f0f0f0;
            padding-bottom: 15px;
            margin-bottom: 20px;
        }
        
        .card-header h2 {
            color: #2c3e50;
            font-size: 1.5rem;
            font-weight: 600;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        
        /* Form Styles */
        .form-row { 
            display: flex; 
            flex-wrap: wrap; 
            margin: 0 -10px; 
        }
        
        .form-group { 
            flex: 1 0 calc(33.333% - 20px); 
            margin: 0 10px 20px; 
            min-width: 250px; 
        }
        
        .form-group.full-width { 
            flex: 1 0 calc(100% - 20px); 
        }
        
        label { 
            display: block; 
            margin-bottom: 8px; 
            font-weight: 600; 
            color: #2c3e50; 
        }
        
        .form-control { 
            width: 100%; 
            padding: 12px; 
            border: 2px solid #eaeaea; 
            border-radius: 6px; 
            font-size: 1rem; 
            transition: all 0.3s ease;
            background-color: #fafafa;
        }
        
        .form-control:focus {
            border-color: #667eea;
            outline: none;
            box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
            background-color: #fff;
        }
        
        /* Button Styles */
        .btn { 
            padding: 12px 25px; 
            border: none; 
            border-radius: 6px; 
            cursor: pointer; 
            font-weight: 600; 
            transition: all 0.3s ease; 
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }
        
        .btn-primary { 
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); 
            color: white; 
        }
        
        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(102, 126, 234, 0.4);
        }
        
        .btn-success { 
            background: linear-gradient(135deg, #4CAF50 0%, #45a049 100%); 
            color: white; 
        }
        
        .btn-danger { 
            background: linear-gradient(135deg, #f44336 0%, #d32f2f 100%); 
            color: white; 
        }
        
        .btn-secondary { 
            background: #6c757d; 
            color: white; 
        }
        
        .btn-sm {
            padding: 8px 15px;
            font-size: 0.9rem;
        }
        
        /* Table Styles */
        .table-container { 
            overflow-x: auto; 
            margin-top: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }
        
        table { 
            width: 100%; 
            border-collapse: collapse; 
            background: white;
        }
        
        th, td { 
            padding: 12px 15px; 
            text-align: left; 
            border-bottom: 1px solid #eaeaea; 
        }
        
        th { 
            background-color: #667eea; 
            color: white; 
            font-weight: 600;
            position: sticky;
            top: 0;
        }
        
        tr:hover {
            background-color: #f8f9fa;
        }
        
        .action-buttons {
            display: flex;
            gap: 10px;
        }
        
        /* Status Badges */
        .status-badge { 
            padding: 6px 12px; 
            border-radius: 15px; 
            font-size: 0.8rem; 
            font-weight: 600; 
            display: inline-block;
        }
        
        .status-active { 
            background-color: #d4edda; 
            color: #155724; 
        }
        
        /* Stats Container */
        .stats-container { 
            display: grid; 
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); 
            gap: 20px; 
            margin-bottom: 30px; 
        }
        
        .stat-card { 
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); 
            border-radius: 10px; 
            padding: 20px; 
            color: white; 
            text-align: center;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
            transition: transform 0.3s ease;
        }
        
        .stat-card:hover {
            transform: translateY(-5px);
        }
        
        .stat-card h3 {
            font-size: 2rem;
            margin-bottom: 10px;
            font-weight: 300;
        }
        
        .stat-card i {
            font-size: 2.5rem;
            margin-bottom: 15px;
            opacity: 0.9;
        }
        
        /* Mobile Menu Toggle */
        .mobile-menu-toggle {
            display: none;
            position: fixed;
            top: 20px;
            left: 20px;
            z-index: 1001;
            background: #667eea;
            color: white;
            border: none;
            border-radius: 5px;
            padding: 10px;
            font-size: 1.2rem;
            cursor: pointer;
        }
        
        /* Alert Styles */
        .alert {
            padding: 15px 20px;
            border-radius: 8px;
            margin-bottom: 25px;
            display: flex;
            align-items: center;
            border-left: 5px solid;
        }
        
        .alert-success {
            background-color: #d4edda;
            color: #155724;
            border-left-color: #28a745;
        }
        
        .alert-error {
            background-color: #f8d7da;
            color: #721c24;
            border-left-color: #dc3545;
        }
        
        .alert i {
            margin-right: 10px;
            font-size: 1.2rem;
        }
        
        /* Responsive Design */
        @media (max-width: 768px) {
            .mobile-menu-toggle {
                display: block;
            }
            
            .sidebar {
                width: 280px;
                transform: translateX(-100%);
            }
            
            .sidebar.active {
                transform: translateX(0);
            }
            
            .main-content {
                margin-left: 0;
                width: 100%;
            }
            
            .main-content.sidebar-active {
                margin-left: 280px;
            }
            
            .form-group { 
                flex: 1 0 calc(100% - 20px); 
            }
            
            .tab { 
                flex: 1 0 100%; 
            }
            
            .tabs {
                flex-direction: column;
            }
            
            .user-info {
                position: relative;
                top: 0;
                right: 0;
                margin-bottom: 15px;
                text-align: center;
            }
            
            .stats-container {
                grid-template-columns: 1fr;
            }
            
            .action-buttons {
                flex-direction: column;
            }
            
            .content-header {
                padding: 20px;
            }
            
            .content-header h1 {
                font-size: 2rem;
            }
        }
    </style>
</head>
<body>
    <!-- Mobile Menu Toggle -->
    <button class="mobile-menu-toggle" id="mobileMenuToggle">
        <i class="fas fa-bars"></i>
    </button>

    <!-- Sidebar -->
    <div class="sidebar" id="sidebar">
        <div class="sidebar-header">
            <h3><i class="fas fa-chalkboard-teacher"></i> Teacher System</h3>
        </div>
        <ul class="sidebar-menu">
              <li><a href="../admin/" class="<?php echo basename($_SERVER['PHP_SELF']) == 'index.php' ? 'active' : ''; ?>">Dashboard</a></li>
            <li><a href="../admin/teacher_management.php">Teacher</a></li>
            <li><a href="../admin/manage_users.php">Manage Users</a></li>
            <li><a href="../admin/system_settings.php">System Settings</a></li>
            <li><a href="../admin/register_student.php">Students</a></li>
            <li><a href="../admin/manage_classes.php">Classes</a></li>
            <li><a href="../admin/finance/fee_management.php">Finance</a></li>
            <li><a href="../admin/registrar_cleaner.php">Cleaner</a></li>
            <li><a href="../admin/transport_management.php" class="<?php echo basename($_SERVER['PHP_SELF']) == 'transport_management.php' ? 'active' : ''; ?>"><i class="fas fa-bus"></i> Transport Management</a></li>
            <li><a href="../admin/profile.php">Profile</a></li>
            <li><a href="../admin/change_password.php">Change Password</a></li>
<li>
    <a href="transport_management.php" class="<?php echo basename($_SERVER['PHP_SELF']) == 'transport_management.php' ? 'active' : ''; ?>">
        <i class="fas fa-bus"></i> Transport Management
    </a>
</li>
        </ul>
    </div>

    <!-- Main Content -->
    <div class="main-content" id="mainContent">
        <div class="content-header">
            <div class="user-info">
                Welcome admin (Admin) | <a href="/school_managementsystem/logout.php" style="color: white; text-decoration: underline;">Logout</a>
            </div>
            <h1><i class="fas fa-chalkboard-teacher"></i> Teacher Management System</h1>
            <p>Manage teachers, class assignments, and subject allocations</p>
        </div>
        
        <?php if ($success): ?>
            <div class="alert alert-success">
                <i class="fas fa-check-circle"></i> <?php echo $success; ?>
            </div>
        <?php endif; ?>
        
        <?php if ($error): ?>
            <div class="alert alert-error">
                <i class="fas fa-exclamation-circle"></i> <?php echo $error; ?>
            </div>
        <?php endif; ?>
        
        <div class="stats-container">
            <div class="stat-card">
                <i class="fas fa-chalkboard-teacher"></i>
                <h3><?php echo count($teachers); ?></h3>
                <p>Total Teachers</p>
            </div>
            <div class="stat-card">
                <i class="fas fa-door-open"></i>
                <h3><?php echo count($classes); ?></h3>
                <p>Available Classes</p>
            </div>
            <div class="stat-card">
                <i class="fas fa-book"></i>
                <h3><?php echo count($subjects); ?></h3>
                <p>Subjects</p>
            </div>
            <div class="stat-card">
                <i class="fas fa-link"></i>
                <h3><?php echo count($assignments); ?></h3>
                <p>Current Assignments</p>
            </div>
        </div>
        
        <div class="tabs">
            <div class="tab <?php echo !$editing_teacher ? 'active' : ''; ?>" onclick="showTab('register')">
                <i class="fas fa-user-plus"></i> <?php echo $editing_teacher ? 'Edit Teacher' : 'Register Teacher'; ?>
            </div>
            <div class="tab" onclick="showTab('manage')">
                <i class="fas fa-users-cog"></i> Manage Teachers
            </div>
            <div class="tab" onclick="showTab('assign')">
                <i class="fas fa-tasks"></i> Class Assignments
            </div>
        </div>
        
        <!-- Register/Edit Teacher Tab -->
        <div class="tab-content <?php echo !$editing_teacher ? 'active' : ''; ?>" id="register">
            <div class="card">
                <div class="card-header">
                    <h2><i class="fas fa-user-plus"></i> <?php echo $editing_teacher ? 'Edit Teacher' : 'Register New Teacher'; ?></h2>
                </div>
                <form method="POST">
                    <?php if ($editing_teacher): ?>
                        <input type="hidden" name="teacher_id" value="<?php echo $editing_teacher['id']; ?>">
                    <?php endif; ?>
                    
                    <div class="form-row">
                        <?php if (!$editing_teacher): ?>
                        <div class="form-group">
                            <label>Username</label>
                            <input type="text" class="form-control" name="username" placeholder="Enter username" value="<?php echo $editing_teacher ? $editing_teacher['username'] : ''; ?>" required>
                        </div>
                        <div class="form-group">
                            <label>Password</label>
                            <input type="password" class="form-control" name="password" placeholder="Enter password" <?php echo $editing_teacher ? '' : 'required'; ?>>
                            <?php if ($editing_teacher): ?>
                                <small style="color: #666; font-size: 0.9rem;">Leave blank to keep current password</small>
                            <?php endif; ?>
                        </div>
                        <?php endif; ?>
                    </div>
                    
                    <div class="form-row">
                        <div class="form-group">
                            <label>First Name</label>
                            <input type="text" class="form-control" name="first_name" placeholder="Enter first name" value="<?php echo $editing_teacher ? $editing_teacher['first_name'] : ''; ?>" required>
                        </div>
                        <div class="form-group">
                            <label>Last Name</label>
                            <input type="text" class="form-control" name="last_name" placeholder="Enter last name" value="<?php echo $editing_teacher ? $editing_teacher['last_name'] : ''; ?>" required>
                        </div>
                        <div class="form-group">
                            <label>Email Address</label>
                            <input type="email" class="form-control" name="email" placeholder="Enter email address" value="<?php echo $editing_teacher ? $editing_teacher['email'] : ''; ?>" required>
                        </div>
                    </div>
                    
                    <div class="form-row">
                        <div class="form-group">
                            <label>Specialization</label>
                            <input type="text" class="form-control" name="specialization" placeholder="e.g., Mathematics, Science" value="<?php echo $editing_teacher ? $editing_teacher['specialization'] : ''; ?>" required>
                        </div>
                        <div class="form-group">
                            <label>Contact Number</label>
                            <input type="text" class="form-control" name="contact" placeholder="Enter phone number" value="<?php echo $editing_teacher ? $editing_teacher['contact'] : ''; ?>" required>
                        </div>
                    </div>
                    
                    <button type="submit" name="<?php echo $editing_teacher ? 'update_teacher' : 'register_teacher'; ?>" class="btn btn-primary">
                        <i class="fas fa-save"></i> <?php echo $editing_teacher ? 'Update Teacher' : 'Register Teacher'; ?>
                    </button>
                    
                    <?php if ($editing_teacher): ?>
                        <a href="teacher_management.php" class="btn btn-secondary">
                            <i class="fas fa-times"></i> Cancel
                        </a>
                    <?php endif; ?>
                </form>
            </div>
        </div>
        
        <!-- Manage Teachers Tab -->
        <div class="tab-content" id="manage">
            <div class="card">
                <div class="card-header">
                    <h2><i class="fas fa-users-cog"></i> Manage Teachers</h2>
                </div>
                
                <div class="table-container">
                    <table>
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Name</th>
                                <th>Email</th>
                                <th>Contact</th>
                                <th>Specialization</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($teachers)): ?>
                                <?php foreach ($teachers as $teacher): ?>
                                <tr>
                                    <td><?php echo $teacher['id']; ?></td>
                                    <td><?php echo $teacher['first_name'] . ' ' . $teacher['last_name']; ?></td>
                                    <td><?php echo $teacher['email']; ?></td>
                                    <td><?php echo $teacher['contact']; ?></td>
                                    <td><?php echo $teacher['specialization']; ?></td>
                                    <td class="action-buttons">
                                        <a href="?edit_teacher=<?php echo $teacher['id']; ?>" class="btn btn-primary btn-sm">
                                            <i class="fas fa-edit"></i> Edit
                                        </a>
                                        <form method="POST" style="display: inline;">
                                            <input type="hidden" name="teacher_id" value="<?php echo $teacher['id']; ?>">
                                            <button type="submit" name="delete_teacher" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure you want to delete this teacher?');">
                                                <i class="fas fa-trash"></i> Delete
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="6" style="text-align: center; padding: 30px; color: #666;">
                                        <i class="fas fa-chalkboard-teacher" style="font-size: 3rem; margin-bottom: 15px; display: block; opacity: 0.5;"></i>
                                        No teachers found. Register your first teacher above.
                                    </td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        
        <!-- Class Assignments Tab -->
        <div class="tab-content" id="assign">
            <div class="card">
                <div class="card-header">
                    <h2><i class="fas fa-tasks"></i> Assign Teacher to Class</h2>
                </div>
                <form method="POST">
                    <div class="form-row">
                        <div class="form-group">
                            <label>Teacher</label>
                            <select class="form-control" name="teacher_id" required>
                                <option value="">Select a teacher</option>
                                <?php foreach ($teachers as $teacher): ?>
                                <option value="<?php echo $teacher['id']; ?>">
                                    <?php echo $teacher['first_name'] . ' ' . $teacher['last_name']; ?>
                                </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="form-group">
                            <label>Class</label>
                            <select class="form-control" name="class_id" required>
                                <option value="">Select a class</option>
                                <?php foreach ($classes as $class): ?>
                                <option value="<?php echo $class['id']; ?>">
                                    <?php echo $class['name']; ?>
                                </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="form-group">
                            <label>Subject</label>
                            <select class="form-control" name="subject_id" required>
                                <option value="">Select a subject</option>
                                <?php foreach ($subjects as $subject): ?>
                                <option value="<?php echo $subject['id']; ?>">
                                    <?php echo $subject['name']; ?> (<?php echo $subject['code']; ?>)
                                </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                    
                    <button type="submit" name="assign_teacher_class" class="btn btn-success">
                        <i class="fas fa-link"></i> Assign Teacher
                    </button>
                </form>
            </div>
            
            <div class="card">
                <div class="card-header">
                    <h2><i class="fas fa-list"></i> Current Assignments</h2>
                </div>
                
                <div class="table-container">
                    <table>
                        <thead>
                            <tr>
                                <th>Teacher</th>
                                <th>Class</th>
                                <th>Subject</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($assignments)): ?>
                                <?php foreach ($assignments as $assignment): ?>
                                <tr>
                                    <td><?php echo $assignment['first_name'] . ' ' . $assignment['last_name']; ?></td>
                                    <td><?php echo $assignment['class_name']; ?></td>
                                    <td><?php echo $assignment['subject_name']; ?> (<?php echo $assignment['subject_code']; ?>)</td>
                                    <td class="action-buttons">
                                        <form method="POST" style="display: inline;">
                                            <input type="hidden" name="assignment_id" value="<?php echo $assignment['id']; ?>">
                                            <button type="submit" name="delete_assignment" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure you want to delete this assignment?');">
                                                <i class="fas fa-times"></i> Remove
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="4" style="text-align: center; padding: 30px; color: #666;">
                                        <i class="fas fa-tasks" style="font-size: 3rem; margin-bottom: 15px; display: block; opacity: 0.5;"></i>
                                        No assignments found. Assign teachers to classes above.
                                    </td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Mobile menu functionality
        document.addEventListener('DOMContentLoaded', function() {
            const mobileMenuToggle = document.getElementById('mobileMenuToggle');
            const sidebar = document.getElementById('sidebar');
            const mainContent = document.getElementById('mainContent');
            
            mobileMenuToggle.addEventListener('click', function() {
                sidebar.classList.toggle('active');
                mainContent.classList.toggle('sidebar-active');
            });
            
            // Close sidebar when clicking outside on mobile
            document.addEventListener('click', function(event) {
                if (window.innerWidth <= 768) {
                    if (!sidebar.contains(event.target) && !mobileMenuToggle.contains(event.target)) {
                        sidebar.classList.remove('active');
                        mainContent.classList.remove('sidebar-active');
                    }
                }
            });
            
            // Handle window resize
            window.addEventListener('resize', function() {
                if (window.innerWidth > 768) {
                    sidebar.classList.remove('active');
                    mainContent.classList.remove('sidebar-active');
                }
            });
        });

        function showTab(tabName) {
            // Hide all tab contents
            var tabContents = document.getElementsByClassName('tab-content');
            for (var i = 0; i < tabContents.length; i++) {
                tabContents[i].classList.remove('active');
            }
            
            // Show the selected tab content
            document.getElementById(tabName).classList.add('active');
            
            // Update active tab
            var tabs = document.getElementsByClassName('tab');
            for (var i = 0; i < tabs.length; i++) {
                tabs[i].classList.remove('active');
            }
            
            // Find and activate the clicked tab
            var tabButtons = document.querySelectorAll('.tab');
            tabButtons.forEach(function(tab) {
                if (tab.textContent.toLowerCase().includes(tabName)) {
                    tab.classList.add('active');
                }
            });
        }
        
        // If we're editing a teacher, show the register tab
        <?php if ($editing_teacher): ?>
            showTab('register');
        <?php endif; ?>
    </script>
</body>
</html>