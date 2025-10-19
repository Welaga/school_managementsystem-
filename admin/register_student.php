<?php
require_once '../config.php';
// Initialize variables
$error = '';
$success = '';

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['register_student'])) {
        // Register student logic
        $username = $_POST['username'];
        $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
        $first_name = $_POST['first_name'];
        $last_name = $_POST['last_name'];
        $email = $_POST['email'];
        $dob = $_POST['dob'];
        $class_id = $_POST['class_id'];
        $address = $_POST['address'];
        $contact = $_POST['contact'];
        $guardian_name = $_POST['guardian_name'];
        $guardian_contact = $_POST['guardian_contact'];
        
        try {
            // Insert into users table
            $stmt = $pdo->prepare("INSERT INTO users (username, password, role, email, status, created_at) VALUES (?, ?, 'student', ?, 1, NOW())");
            $stmt->execute([$username, $password, $email]);
            $user_id = $pdo->lastInsertId();
            
            // Insert into students table
            $stmt = $pdo->prepare("INSERT INTO students (user_id, first_name, last_name, dob, class_id, address, contact, email, guardian_name, guardian_contact, created_at) 
                                  VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())");
            $stmt->execute([$user_id, $first_name, $last_name, $dob, $class_id, $address, $contact, $email, $guardian_name, $guardian_contact]);
            
            $student_id = $pdo->lastInsertId();
            $success = "Student registered successfully! Student ID: " . $student_id . " | Username: " . $username;
        } catch (PDOException $e) {
            $error = "Error registering student: " . $e->getMessage();
        }
    } 
    elseif (isset($_POST['assign_student_subject'])) {
        // Assign student to multiple subjects logic
        $student_id = $_POST['student_id'];
        $subject_ids = isset($_POST['subject_ids']) ? $_POST['subject_ids'] : [];
        
        // Validate inputs
        if (empty($student_id) || empty($subject_ids)) {
            $error = "Please select both a student and at least one subject!";
        } else {
            try {
                $success_count = 0;
                $error_count = 0;
                $already_enrolled = 0;
                
                foreach ($subject_ids as $subject_id) {
                    // Create a simple enrollment system using student_assignments table
                    // First, get an assignment for this subject
                    $stmt = $pdo->prepare("SELECT id FROM assignments WHERE subject_id = ? LIMIT 1");
                    $stmt->execute([$subject_id]);
                    $assignment = $stmt->fetch(PDO::FETCH_ASSOC);
                    
                    if ($assignment) {
                        $assignment_id = $assignment['id'];
                        
                        // Check if already enrolled
                        $stmt = $pdo->prepare("SELECT * FROM student_assignments WHERE student_id = ? AND assignment_id = ?");
                        $stmt->execute([$student_id, $assignment_id]);
                        
                        if ($stmt->fetch()) {
                            $already_enrolled++;
                        } else {
                            $stmt = $pdo->prepare("INSERT INTO student_assignments (student_id, assignment_id, submitted_at) VALUES (?, ?, NOW())");
                            $stmt->execute([$student_id, $assignment_id]);
                            $success_count++;
                        }
                    } else {
                        // Get the first available teacher
                        $stmt = $pdo->prepare("SELECT id FROM teachers LIMIT 1");
                        $stmt->execute();
                        $teacher = $stmt->fetch(PDO::FETCH_ASSOC);
                        
                        if ($teacher) {
                            $teacher_id = $teacher['id'];
                            
                            // Create a dummy assignment for this subject if none exists
                            $stmt = $pdo->prepare("INSERT INTO assignments (title, description, subject_id, teacher_id, class_id, created_at) 
                                                  VALUES ('Enrollment Assignment', 'Default assignment for subject enrollment', ?, ?, (SELECT class_id FROM students WHERE id = ?), NOW())");
                            $stmt->execute([$subject_id, $teacher_id, $student_id]);
                            $assignment_id = $pdo->lastInsertId();
                            
                            $stmt = $pdo->prepare("INSERT INTO student_assignments (student_id, assignment_id, submitted_at) VALUES (?, ?, NOW())");
                            $stmt->execute([$student_id, $assignment_id]);
                            $success_count++;
                        } else {
                            $error_count++;
                        }
                    }
                }
                
                // Build result message
                $message_parts = [];
                if ($success_count > 0) {
                    $message_parts[] = "Successfully assigned to {$success_count} subject(s)";
                }
                if ($already_enrolled > 0) {
                    $message_parts[] = "{$already_enrolled} subject(s) already assigned";
                }
                if ($error_count > 0) {
                    $message_parts[] = "Failed to assign {$error_count} subject(s) (no teachers available)";
                }
                
                if (!empty($message_parts)) {
                    $success = implode(". ", $message_parts) . ".";
                }
                
            } catch (PDOException $e) {
                $error = "Error assigning student to subjects: " . $e->getMessage();
            }
        }
    }
    elseif (isset($_POST['delete_student'])) {
        // Delete student logic
        $student_id = $_POST['student_id'];
        
        try {
            // First get the user_id from the student
            $stmt = $pdo->prepare("SELECT user_id FROM students WHERE id = ?");
            $stmt->execute([$student_id]);
            $student = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if ($student) {
                $user_id = $student['user_id'];
                
                // Delete from student_assignments table
                $stmt = $pdo->prepare("DELETE FROM student_assignments WHERE student_id = ?");
                $stmt->execute([$student_id]);
                
                // Delete from students table
                $stmt = $pdo->prepare("DELETE FROM students WHERE id = ?");
                $stmt->execute([$student_id]);
                
                // Delete from users table
                $stmt = $pdo->prepare("DELETE FROM users WHERE id = ?");
                $stmt->execute([$user_id]);
                
                $success = "Student deleted successfully!";
            } else {
                $error = "Student not found!";
            }
        } catch (PDOException $e) {
            $error = "Error deleting student: " . $e->getMessage();
        }
    }
    elseif (isset($_POST['update_student'])) {
        // Update student logic
        $student_id = $_POST['student_id'];
        $first_name = $_POST['first_name'];
        $last_name = $_POST['last_name'];
        $email = $_POST['email'];
        $dob = $_POST['dob'];
        $class_id = $_POST['class_id'];
        $address = $_POST['address'];
        $contact = $_POST['contact'];
        $guardian_name = $_POST['guardian_name'];
        $guardian_contact = $_POST['guardian_contact'];
        
        try {
            // Update students table
            $stmt = $pdo->prepare("UPDATE students SET first_name = ?, last_name = ?, dob = ?, class_id = ?, address = ?, contact = ?, email = ?, guardian_name = ?, guardian_contact = ? WHERE id = ?");
            $stmt->execute([$first_name, $last_name, $dob, $class_id, $address, $contact, $email, $guardian_name, $guardian_contact, $student_id]);
            
            // Also update the users table email
            $stmt = $pdo->prepare("UPDATE users SET email = ? WHERE id = (SELECT user_id FROM students WHERE id = ?)");
            $stmt->execute([$email, $student_id]);
            
            $success = "Student updated successfully!";
        } catch (PDOException $e) {
            $error = "Error updating student: " . $e->getMessage();
        }
    }
    elseif (isset($_POST['delete_enrollment'])) {
        // Delete enrollment logic
        $enrollment_id = $_POST['enrollment_id'];
        
        try {
            $stmt = $pdo->prepare("DELETE FROM student_assignments WHERE id = ?");
            $stmt->execute([$enrollment_id]);
            
            $success = "Subject enrollment removed successfully!";
        } catch (PDOException $e) {
            $error = "Error removing enrollment: " . $e->getMessage();
        }
    }
}

// Fetch data for display
try {
    // Get all students with their class info
    $students = $pdo->query("
        SELECT s.*, c.name as class_name, u.username 
        FROM students s 
        LEFT JOIN classes c ON s.class_id = c.id 
        LEFT JOIN users u ON s.user_id = u.id
        ORDER BY s.id DESC
    ")->fetchAll(PDO::FETCH_ASSOC);
    
    // Get all classes
    $classes = $pdo->query("SELECT * FROM classes")->fetchAll(PDO::FETCH_ASSOC);
    
    // Get all subjects
    $subjects = $pdo->query("SELECT * FROM subjects")->fetchAll(PDO::FETCH_ASSOC);
    
    // Get student enrollments (using assignments as subjects)
    $enrollments = $pdo->query("
        SELECT sa.id as enrollment_id, s.id as student_id, s.first_name, s.last_name, 
               sub.name as subject_name, sub.code as subject_code, sa.submitted_at as enrolled_at
        FROM student_assignments sa
        JOIN students s ON sa.student_id = s.id
        JOIN assignments a ON sa.assignment_id = a.id
        JOIN subjects sub ON a.subject_id = sub.id
        GROUP BY s.id, sub.id
        ORDER BY sa.submitted_at DESC
    ")->fetchAll(PDO::FETCH_ASSOC);
    
} catch (PDOException $e) {
    $error = "Error fetching data: " . $e->getMessage();
    // Initialize empty arrays to prevent errors
    $students = [];
    $classes = [];
    $subjects = [];
    $enrollments = [];
}

// Check if we're editing a student
$editing_student = null;
if (isset($_GET['edit_student'])) {
    $student_id = $_GET['edit_student'];
    foreach ($students as $student) {
        if ($student['id'] == $student_id) {
            $editing_student = $student;
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
    <title>Student Management System</title>
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
        
        /* Checkbox styling */
        .subjects-checkbox-container {
            border: 1px solid #eaeaea;
            border-radius: 8px;
            padding: 20px;
            background-color: #fafafa;
            max-height: 300px;
            overflow-y: auto;
        }

        .subjects-grid {
            max-height: 200px;
            overflow-y: auto;
            padding-right: 5px;
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
            gap: 15px;
            margin-top: 15px;
        }

        .subject-checkbox-item {
            display: flex;
            align-items: center;
            padding: 10px;
            background: white;
            border-radius: 5px;
            border: 1px solid #eaeaea;
            transition: all 0.3s ease;
        }

        .subject-checkbox-item:hover {
            background-color: #f8f9fa;
            border-color: #667eea;
        }

        .subject-checkbox-item label {
            margin-left: 10px;
            cursor: pointer;
            flex-grow: 1;
            margin-bottom: 0;
        }

        .subject-checkbox {
            transform: scale(1.2);
            margin-right: 10px;
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
            
            .subjects-grid {
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
            <h3><i class="fas fa-user-graduate"></i> Student System</h3>
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
        </ul>
    </div>

    <!-- Main Content -->
    <div class="main-content" id="mainContent">
        <div class="content-header">
            <div class="user-info">
                Welcome admin (Admin) | <a href="/school_managementsystem/logout.php" style="color: white; text-decoration: underline;">Logout</a>
            </div>
            <h1><i class="fas fa-user-graduate"></i> Student Management System</h1>
            <p>Manage students, classes, subjects, and enrollments</p>
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
                <i class="fas fa-user-graduate"></i>
                <h3><?php echo count($students); ?></h3>
                <p>Total Students</p>
            </div>
            <div class="stat-card">
                <i class="fas fa-book"></i>
                <h3><?php echo count($subjects); ?></h3>
                <p>Available Subjects</p>
            </div>
            <div class="stat-card">
                <i class="fas fa-door-open"></i>
                <h3><?php echo count($classes); ?></h3>
                <p>Classes</p>
            </div>
            <div class="stat-card">
                <i class="fas fa-link"></i>
                <h3><?php echo count($enrollments); ?></h3>
                <p>Subject Enrollments</p>
            </div>
        </div>
        
        <div class="tabs">
            <div class="tab <?php echo !$editing_student ? 'active' : ''; ?>" onclick="showTab('register')">
                <i class="fas fa-user-plus"></i> <?php echo $editing_student ? 'Edit Student' : 'Register Student'; ?>
            </div>
            <div class="tab" onclick="showTab('manage')">
                <i class="fas fa-users-cog"></i> Manage Students
            </div>
            <div class="tab" onclick="showTab('assign')">
                <i class="fas fa-tasks"></i> Assign to Subject
            </div>
        </div>
        
        <!-- Register/Edit Student Tab -->
        <div class="tab-content <?php echo !$editing_student ? 'active' : ''; ?>" id="register">
            <div class="card">
                <div class="card-header">
                    <h2><i class="fas fa-user-plus"></i> <?php echo $editing_student ? 'Edit Student' : 'Register New Student'; ?></h2>
                </div>
                <form method="POST">
                    <?php if ($editing_student): ?>
                        <input type="hidden" name="student_id" value="<?php echo $editing_student['id']; ?>">
                    <?php endif; ?>
                    
                    <div class="form-row">
                        <?php if (!$editing_student): ?>
                        <div class="form-group">
                            <label>Username</label>
                            <input type="text" class="form-control" name="username" placeholder="Enter username" value="<?php echo $editing_student ? $editing_student['username'] : ''; ?>" required>
                        </div>
                        <div class="form-group">
                            <label>Password</label>
                            <input type="password" class="form-control" name="password" placeholder="Enter password" <?php echo $editing_student ? '' : 'required'; ?>>
                            <?php if ($editing_student): ?>
                                <small style="color: #666; font-size: 0.9rem;">Leave blank to keep current password</small>
                            <?php endif; ?>
                        </div>
                        <?php endif; ?>
                    </div>
                    
                    <div class="form-row">
                        <div class="form-group">
                            <label>First Name</label>
                            <input type="text" class="form-control" name="first_name" placeholder="Enter first name" value="<?php echo $editing_student ? $editing_student['first_name'] : ''; ?>" required>
                        </div>
                        <div class="form-group">
                            <label>Last Name</label>
                            <input type="text" class="form-control" name="last_name" placeholder="Enter last name" value="<?php echo $editing_student ? $editing_student['last_name'] : ''; ?>" required>
                        </div>
                        <div class="form-group">
                            <label>Email Address</label>
                            <input type="email" class="form-control" name="email" placeholder="Enter email address" value="<?php echo $editing_student ? $editing_student['email'] : ''; ?>" required>
                        </div>
                    </div>
                    
                    <div class="form-row">
                        <div class="form-group">
                            <label>Date of Birth</label>
                            <input type="date" class="form-control" name="dob" value="<?php echo $editing_student ? $editing_student['dob'] : ''; ?>" required>
                        </div>
                        <div class="form-group">
                            <label>Class</label>
                            <select class="form-control" name="class_id" required>
                                <option value="">Select Class</option>
                                <?php foreach ($classes as $class): ?>
                                <option value="<?php echo $class['id']; ?>" <?php echo ($editing_student && $editing_student['class_id'] == $class['id']) ? 'selected' : ''; ?>>
                                    <?php echo $class['name']; ?> (<?php echo $class['room']; ?>)
                                </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="form-group">
                            <label>Contact Number</label>
                            <input type="text" class="form-control" name="contact" placeholder="Enter contact number" value="<?php echo $editing_student ? $editing_student['contact'] : ''; ?>">
                        </div>
                    </div>
                    
                    <div class="form-row">
                        <div class="form-group full-width">
                            <label>Address</label>
                            <textarea class="form-control" name="address" placeholder="Enter address" rows="3"><?php echo $editing_student ? $editing_student['address'] : ''; ?></textarea>
                        </div>
                    </div>
                    
                    <div class="form-row">
                        <div class="form-group">
                            <label>Guardian Name</label>
                            <input type="text" class="form-control" name="guardian_name" placeholder="Enter guardian name" value="<?php echo $editing_student ? $editing_student['guardian_name'] : ''; ?>" required>
                        </div>
                        <div class="form-group">
                            <label>Guardian Contact</label>
                            <input type="text" class="form-control" name="guardian_contact" placeholder="Enter guardian contact" value="<?php echo $editing_student ? $editing_student['guardian_contact'] : ''; ?>" required>
                        </div>
                    </div>
                    
                    <button type="submit" name="<?php echo $editing_student ? 'update_student' : 'register_student'; ?>" class="btn btn-primary">
                        <i class="fas fa-save"></i> <?php echo $editing_student ? 'Update Student' : 'Register Student'; ?>
                    </button>
                    
                    <?php if ($editing_student): ?>
                        <a href="student_management.php" class="btn btn-secondary">
                            <i class="fas fa-times"></i> Cancel
                        </a>
                    <?php endif; ?>
                </form>
            </div>
        </div>
        
        <!-- Manage Students Tab -->
        <div class="tab-content" id="manage">
            <div class="card">
                <div class="card-header">
                    <h2><i class="fas fa-users-cog"></i> Manage Students</h2>
                </div>
                
                <div class="table-container">
                    <table>
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Name</th>
                                <th>Username</th>
                                <th>Email</th>
                                <th>Class</th>
                                <th>Contact</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($students)): ?>
                                <?php foreach ($students as $student): ?>
                                <tr>
                                    <td><?php echo $student['id']; ?></td>
                                    <td><?php echo $student['first_name'] . ' ' . $student['last_name']; ?></td>
                                    <td><?php echo $student['username']; ?></td>
                                    <td><?php echo $student['email']; ?></td>
                                    <td><?php echo $student['class_name'] ?? 'Not assigned'; ?></td>
                                    <td><?php echo $student['contact'] ?? 'N/A'; ?></td>
                                    <td class="action-buttons">
                                        <a href="?edit_student=<?php echo $student['id']; ?>" class="btn btn-primary btn-sm">
                                            <i class="fas fa-edit"></i> Edit
                                        </a>
                                        <form method="POST" style="display: inline;">
                                            <input type="hidden" name="student_id" value="<?php echo $student['id']; ?>">
                                            <button type="submit" name="delete_student" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure you want to delete this student?');">
                                                <i class="fas fa-trash"></i> Delete
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="7" style="text-align: center; padding: 30px; color: #666;">
                                        <i class="fas fa-user-graduate" style="font-size: 3rem; margin-bottom: 15px; display: block; opacity: 0.5;"></i>
                                        No students found. Register your first student above.
                                    </td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        
        <!-- Assign Subjects Tab -->
        <div class="tab-content" id="assign">
            <div class="card">
                <div class="card-header">
                    <h2><i class="fas fa-tasks"></i> Assign Student to Subjects</h2>
                </div>
                <form method="POST">
                    <div class="form-row">
                        <div class="form-group">
                            <label>Student</label>
                            <select class="form-control" name="student_id" required>
                                <option value="">Select a student</option>
                                <?php foreach ($students as $student): ?>
                                <option value="<?php echo $student['id']; ?>">
                                    <?php echo $student['first_name'] . ' ' . $student['last_name'] . ' (Class: ' . ($student['class_name'] ?? 'Not assigned') . ')'; ?>
                                </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                    
                    <div class="form-row">
                        <div class="form-group full-width">
                            <label>Select Subjects</label>
                            <div class="subjects-checkbox-container">
                                <div style="margin-bottom: 15px; padding: 10px; background: white; border-radius: 5px;">
                                    <input type="checkbox" id="select_all_subjects" onchange="toggleAllSubjects(this)">
                                    <label for="select_all_subjects" style="font-weight: bold; color: #667eea; cursor: pointer;">
                                        <i class="fas fa-check-double"></i> Select All Subjects
                                    </label>
                                </div>
                                
                                <div class="subjects-grid">
                                    <?php foreach ($subjects as $subject): ?>
                                    <div class="subject-checkbox-item">
                                        <input type="checkbox" id="subject_<?php echo $subject['id']; ?>" name="subject_ids[]" value="<?php echo $subject['id']; ?>" class="subject-checkbox">
                                        <label for="subject_<?php echo $subject['id']; ?>">
                                            <strong><?php echo $subject['name']; ?></strong> 
                                            <span style="color: #666;">(<?php echo $subject['code']; ?>)</span>
                                        </label>
                                    </div>
                                    <?php endforeach; ?>
                                </div>
                                
                                <?php if (empty($subjects)): ?>
                                <div style="text-align: center; padding: 20px; color: #666;">
                                    <i class="fas fa-book" style="font-size: 3rem; margin-bottom: 10px; display: block; opacity: 0.5;"></i>
                                    No subjects available. Please add subjects first.
                                </div>
                                <?php endif; ?>
                            </div>
                            <small style="color: #666; font-size: 0.9rem;">Check the boxes for subjects you want to assign to the student</small>
                        </div>
                    </div>
                    
                    <button type="submit" name="assign_student_subject" class="btn btn-success">
                        <i class="fas fa-link"></i> Assign to Selected Subjects
                    </button>
                </form>
            </div>
            
            <div class="card">
                <div class="card-header">
                    <h2><i class="fas fa-list"></i> Current Subject Enrollments</h2>
                </div>
                
                <div class="table-container">
                    <table>
                        <thead>
                            <tr>
                                <th>Student</th>
                                <th>Subject</th>
                                <th>Enrollment Date</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($enrollments)): ?>
                                <?php foreach ($enrollments as $enrollment): ?>
                                <tr>
                                    <td><?php echo $enrollment['first_name'] . ' ' . $enrollment['last_name']; ?></td>
                                    <td><?php echo $enrollment['subject_name'] . ' (' . $enrollment['subject_code'] . ')'; ?></td>
                                    <td><?php echo $enrollment['enrolled_at']; ?></td>
                                    <td class="action-buttons">
                                        <form method="POST" style="display: inline;">
                                            <input type="hidden" name="enrollment_id" value="<?php echo $enrollment['enrollment_id']; ?>">
                                            <button type="submit" name="delete_enrollment" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure you want to remove this enrollment?');">
                                                <i class="fas fa-times"></i> Remove
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="4" style="text-align: center; padding: 30px; color: #666;">
                                        <i class="fas fa-book" style="font-size: 3rem; margin-bottom: 15px; display: block; opacity: 0.5;"></i>
                                        No subject enrollments found. Assign students to subjects above.
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
        
        function toggleAllSubjects(selectAllCheckbox) {
            const subjectCheckboxes = document.querySelectorAll('.subject-checkbox');
            subjectCheckboxes.forEach(checkbox => {
                checkbox.checked = selectAllCheckbox.checked;
            });
        }

        // Optional: Uncheck "Select All" when individual checkboxes are changed
        document.addEventListener('DOMContentLoaded', function() {
            const subjectCheckboxes = document.querySelectorAll('.subject-checkbox');
            const selectAllCheckbox = document.getElementById('select_all_subjects');
            
            subjectCheckboxes.forEach(checkbox => {
                checkbox.addEventListener('change', function() {
                    if (!this.checked) {
                        selectAllCheckbox.checked = false;
                    } else {
                        // Check if all subjects are selected
                        const allChecked = Array.from(subjectCheckboxes).every(cb => cb.checked);
                        selectAllCheckbox.checked = allChecked;
                    }
                });
            });
        });
        
        // Set today's date as default for date fields
        document.addEventListener('DOMContentLoaded', function() {
            var today = new Date().toISOString().split('T')[0];
            var dateFields = document.querySelectorAll('input[type="date"]');
            dateFields.forEach(function(field) {
                if (!field.value) {
                    field.value = today;
                }
            });
        });
        
        // If we're editing a student, show the register tab
        <?php if ($editing_student): ?>
            showTab('register');
        <?php endif; ?>
    </script>
</body>
</html>