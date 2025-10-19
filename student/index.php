<?php
require_once '../config.php'; // Fixed path
checkRole(['student']);

// Get student details
$student = getStudentDetails($_SESSION['user_id']);
$student_id = $student['id'];

// Get statistics
$stmt = $pdo->prepare("SELECT COUNT(*) as count FROM grades WHERE student_id = ?");
$stmt->execute([$student_id]);
$grades_count = $stmt->fetch()['count'];

$stmt = $pdo->prepare("SELECT COUNT(DISTINCT date) as count FROM attendance WHERE student_id = ? AND status = 'present'");
$stmt->execute([$student_id]);
$attendance_count = $stmt->fetch()['count'];

$stmt = $pdo->prepare("SELECT COUNT(*) as count FROM assignments a 
                      JOIN student_assignments sa ON a.id = sa.assignment_id 
                      WHERE sa.student_id = ?");
$stmt->execute([$student_id]);
$assignments_count = $stmt->fetch()['count'];

// Get recent grades
$stmt = $pdo->prepare("SELECT g.grade, g.term, s.name as subject_name 
                      FROM grades g 
                      JOIN subjects s ON g.subject_id = s.id 
                      WHERE g.student_id = ? 
                      ORDER BY g.created_at DESC LIMIT 5");
$stmt->execute([$student_id]);
$recent_grades = $stmt->fetchAll();

// Get upcoming assignments - FIXED QUERY
$stmt = $pdo->prepare("SELECT a.title, a.due_date, s.name as subject_name 
                      FROM assignments a 
                      JOIN student_assignments sa ON a.id = sa.assignment_id 
                      JOIN subjects s ON a.subject_id = s.id 
                      WHERE sa.student_id = ? AND a.due_date >= CURDATE() 
                      ORDER BY a.due_date ASC LIMIT 5");
$stmt->execute([$student_id]);
$upcoming_assignments = $stmt->fetchAll();

// Alternative query if due_date doesn't exist either
if (!$upcoming_assignments) {
    $stmt = $pdo->prepare("SELECT a.title, s.name as subject_name, sa.submitted_at
                          FROM assignments a 
                          JOIN student_assignments sa ON a.id = sa.assignment_id 
                          JOIN subjects s ON a.subject_id = s.id 
                          WHERE sa.student_id = ? 
                          ORDER BY sa.submitted_at DESC LIMIT 5");
    $stmt->execute([$student_id]);
    $upcoming_assignments = $stmt->fetchAll();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Dashboard - School Management System</title>
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
        
        /* Stats Container */
        .dashboard-stats { 
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
            font-size: 1.1rem;
            margin-bottom: 10px;
            font-weight: 400;
            opacity: 0.9;
        }
        
        .stat-card p {
            font-size: 2.5rem;
            margin-bottom: 10px;
            font-weight: 300;
        }
        
        .stat-card i {
            font-size: 2.5rem;
            margin-bottom: 15px;
            opacity: 0.9;
        }
        
        /* Dashboard Actions */
        .dashboard-actions {
            background: white;
            border-radius: 10px;
            padding: 25px;
            margin-bottom: 25px;
            box-shadow: 0 2px 15px rgba(0, 0, 0, 0.1);
            border: 1px solid #eaeaea;
        }
        
        .dashboard-actions h2 {
            color: #2c3e50;
            font-size: 1.5rem;
            margin-bottom: 20px;
            font-weight: 600;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        
        .action-buttons {
            display: flex;
            gap: 15px;
            flex-wrap: wrap;
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
            text-decoration: none;
            font-size: 0.95rem;
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
        
        .btn-secondary { 
            background: linear-gradient(135deg, #6c757d 0%, #5a6268 100%); 
            color: white; 
        }
        
        .btn-warning { 
            background: linear-gradient(135deg, #ff9800 0%, #e68900 100%); 
            color: white; 
        }
        
        /* Recent Activities */
        .recent-activities {
            background: white;
            border-radius: 10px;
            padding: 25px;
            margin-bottom: 25px;
            box-shadow: 0 2px 15px rgba(0, 0, 0, 0.1);
            border: 1px solid #eaeaea;
        }
        
        .recent-activities h2 {
            color: #2c3e50;
            font-size: 1.5rem;
            margin-bottom: 20px;
            font-weight: 600;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        
        .activity-list {
            display: flex;
            flex-direction: column;
            gap: 15px;
        }
        
        .activity-item {
            padding: 15px;
            background: #f8f9fa;
            border-radius: 8px;
            border-left: 4px solid #667eea;
            transition: all 0.3s ease;
        }
        
        .activity-item:hover {
            background: #e9ecef;
            transform: translateX(5px);
        }
        
        .activity-item p {
            margin: 0;
            color: #2c3e50;
            font-weight: 500;
        }
        
        .activity-item .activity-time {
            color: #6c757d;
            font-size: 0.85rem;
            margin-top: 5px;
        }
        
        /* Grid Layout for Activities */
        .activities-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(400px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
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
        
        .alert-info {
            background-color: #d1ecf1;
            color: #0c5460;
            border-left-color: #17a2b8;
        }
        
        .alert i {
            margin-right: 10px;
            font-size: 1.2rem;
        }
        
        /* Status Badges */
        .status-badge {
            display: inline-block;
            padding: 4px 8px;
            border-radius: 12px;
            font-size: 0.8rem;
            font-weight: 600;
            margin-left: 10px;
        }
        
        .status-pending {
            background-color: #fff3cd;
            color: #856404;
        }
        
        .status-completed {
            background-color: #d4edda;
            color: #155724;
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
            
            .dashboard-stats {
                grid-template-columns: 1fr;
            }
            
            .action-buttons {
                flex-direction: column;
            }
            
            .activities-grid {
                grid-template-columns: 1fr;
            }
            
            .user-info {
                position: relative;
                top: 0;
                right: 0;
                margin-bottom: 15px;
                text-align: center;
            }
            
            .content-header {
                padding: 20px;
            }
            
            .content-header h1 {
                font-size: 2rem;
            }
        }
        
        @media (max-width: 480px) {
            .main-content {
                padding: 10px;
            }
            
            .stat-card {
                padding: 15px;
            }
            
            .stat-card p {
                font-size: 2rem;
            }
            
            .btn {
                padding: 10px 20px;
                font-size: 0.9rem;
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
            <h3><i class="fas fa-user-graduate"></i> Student Portal</h3>
        </div>
        <ul class="sidebar-menu">
            <li><a href="index.php" class="active"><i class="fas fa-tachometer-alt"></i> Dashboard</a></li>
            <li><a href="view_grades.php"><i class="fas fa-chart-bar"></i> View Grades</a></li>
            <li><a href="view_attendance.php"><i class="fas fa-calendar-check"></i> Attendance</a></li>
            <li><a href="assignments.php"><i class="fas fa-tasks"></i> Assignments</a></li>
            <li><a href="timetable.php"><i class="fas fa-calendar-alt"></i> Timetable</a></li>
            <li><a href="profile.php"><i class="fas fa-user"></i> Profile</a></li>
            <li><a href="../logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
        </ul>
    </div>

    <!-- Main Content -->
    <div class="main-content" id="mainContent">
        <div class="content-header">
            <div class="user-info">
                Welcome, <?php echo $student['first_name'] . ' ' . $student['last_name']; ?> | 
                <a href="../logout.php" style="color: white; text-decoration: underline;">Logout</a>
            </div>
            <h1><i class="fas fa-tachometer-alt"></i> Student Dashboard</h1>
            <p>Welcome back! Here's your academic overview for Class: <?php echo $student['class_name']; ?></p>
        </div>
        
        <!-- Statistics Cards -->
        <div class="dashboard-stats">
            <div class="stat-card">
                <i class="fas fa-chart-bar"></i>
                <p><?php echo $grades_count; ?></p>
                <h3>Grades Recorded</h3>
            </div>
            
            <div class="stat-card">
                <i class="fas fa-calendar-check"></i>
                <p><?php echo $attendance_count; ?></p>
                <h3>Days Present</h3>
            </div>
            
            <div class="stat-card">
                <i class="fas fa-tasks"></i>
                <p><?php echo $assignments_count; ?></p>
                <h3>Total Assignments</h3>
            </div>
            
            <div class="stat-card">
                <i class="fas fa-book"></i>
                <p><?php echo count($upcoming_assignments); ?></p>
                <h3>Recent Assignments</h3>
            </div>
        </div>
        
        <!-- Quick Actions -->
        <div class="dashboard-actions">
            <h2><i class="fas fa-bolt"></i> Quick Actions</h2>
            <div class="action-buttons">
                <a href="view_grades.php" class="btn btn-primary">
                    <i class="fas fa-chart-bar"></i> View Grades
                </a>
                <a href="view_attendance.php" class="btn btn-secondary">
                    <i class="fas fa-calendar-check"></i> View Attendance
                </a>
                <a href="assignments.php" class="btn btn-success">
                    <i class="fas fa-tasks"></i> Assignments
                </a>
                <a href="timetable.php" class="btn btn-warning">
                    <i class="fas fa-calendar-alt"></i> Class Timetable
                </a>
            </div>
        </div>
        
        <!-- Recent Activities Grid -->
        <div class="activities-grid">
            <!-- Recent Grades -->
            <div class="recent-activities">
                <h2><i class="fas fa-chart-line"></i> Recent Grades</h2>
                <div class="activity-list">
                    <?php if (count($recent_grades) > 0): ?>
                        <?php foreach ($recent_grades as $grade): ?>
                            <div class="activity-item">
                                <p>
                                    <strong><?php echo $grade['subject_name']; ?></strong><br>
                                    Grade: <?php echo $grade['grade']; ?> | Term: <?php echo $grade['term']; ?>
                                </p>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <div class="activity-item">
                            <p>No grades recorded yet.</p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
            
            <!-- Recent Assignments -->
            <div class="recent-activities">
                <h2><i class="fas fa-tasks"></i> Recent Assignments</h2>
                <div class="activity-list">
                    <?php if (count($upcoming_assignments) > 0): ?>
                        <?php foreach ($upcoming_assignments as $assignment): ?>
                            <div class="activity-item">
                                <p>
                                    <strong><?php echo $assignment['title']; ?></strong><br>
                                    Subject: <?php echo $assignment['subject_name']; ?>
                                    <?php if (isset($assignment['due_date'])): ?>
                                        <br>Due: <?php echo date('M j, Y', strtotime($assignment['due_date'])); ?>
                                    <?php elseif (isset($assignment['submitted_at'])): ?>
                                        <br>Submitted: <?php echo date('M j, Y', strtotime($assignment['submitted_at'])); ?>
                                    <?php endif; ?>
                                </p>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <div class="activity-item">
                            <p>No assignments found.</p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        
        <!-- Welcome Message -->
        <div class="alert alert-info">
            <i class="fas fa-info-circle"></i>
            <strong>Welcome to your student portal!</strong> Here you can view your grades, attendance, assignments, and class schedule.
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
    </script>
</body>
</html>