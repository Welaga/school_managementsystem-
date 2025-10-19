<?php
require_once '../config.php';
checkRole(['admin']);

// Get statistics
$stmt = $pdo->query("SELECT COUNT(*) as count FROM users");
$users_count = $stmt->fetch()['count'];

$stmt = $pdo->query("SELECT COUNT(*) as count FROM students");
$students_count = $stmt->fetch()['count'];

$stmt = $pdo->query("SELECT COUNT(*) as count FROM teachers");
$teachers_count = $stmt->fetch()['count'];

$stmt = $pdo->query("SELECT COUNT(*) as count FROM courses");
$courses_count = $stmt->fetch()['count'];

$stmt = $pdo->query("SELECT COUNT(*) as count FROM classes");
$classes_count = $stmt->fetch()['count'];

$stmt = $pdo->query("SELECT COUNT(*) as count FROM fees WHERE status = 'paid'");
$paid_fees = $stmt->fetch()['count'];

$stmt = $pdo->query("SELECT COUNT(*) as count FROM fees WHERE status = 'pending'");
$pending_fees = $stmt->fetch()['count'];

$stmt = $pdo->query("SELECT SUM(amount) as total FROM fees WHERE status = 'paid'");
$total_revenue = $stmt->fetch()['total'] ?? 0;

$stmt = $pdo->query("SELECT COUNT(*) as count FROM users WHERE status = 0");
$pending_registrations = $stmt->fetch()['count'];

// Get recent activities
$recent_activities = $pdo->query("
    SELECT 'student' as type, CONCAT('New student registered - ', first_name, ' ', last_name) as description, created_at 
    FROM students 
    ORDER BY created_at DESC 
    LIMIT 5
")->fetchAll(PDO::FETCH_ASSOC);

// Get system status
$stmt = $pdo->query("SHOW STATUS LIKE 'Threads_connected'");
$db_connections = $stmt->fetch()['Value'];

$current_date = date('Y-m-d');
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - Greenwood Academy</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <link rel="stylesheet" href="../assets/css/dashboard.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        .dashboard-stats {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }
        
        .stat-card {
            background: white;
            border-radius: 12px;
            padding: 25px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            border-left: 4px solid;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }
        
        .stat-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 15px rgba(0, 0, 0, 0.15);
        }
        
        .stat-card.users { border-left-color: #667eea; }
        .stat-card.students { border-left-color: #4CAF50; }
        .stat-card.teachers { border-left-color: #2196F3; }
        .stat-card.courses { border-left-color: #FF9800; }
        .stat-card.classes { border-left-color: #9C27B0; }
        .stat-card.revenue { border-left-color: #607D8B; }
        .stat-card.pending { border-left-color: #F44336; }
        
        .stat-card h3 {
            margin: 0 0 10px 0;
            font-size: 1rem;
            font-weight: 600;
            color: #555;
            display: flex;
            align-items: center;
            gap: 8px;
        }
        
        .stat-card p {
            margin: 0;
            font-size: 2.2rem;
            font-weight: 700;
            color: #2c3e50;
        }
        
        .stat-card .stat-trend {
            font-size: 0.9rem;
            margin-top: 8px;
            display: flex;
            align-items: center;
            gap: 5px;
        }
        
        .stat-card .trend-up { color: #4CAF50; }
        .stat-card .trend-down { color: #F44336; }
        
        .dashboard-grid {
            display: grid;
            grid-template-columns: 2fr 1fr;
            gap: 30px;
            margin-bottom: 30px;
        }
        
        @media (max-width: 1024px) {
            .dashboard-grid {
                grid-template-columns: 1fr;
            }
        }
        
        .dashboard-card {
            background: white;
            border-radius: 12px;
            padding: 25px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }
        
        .dashboard-card h2 {
            margin-top: 0;
            color: #2c3e50;
            border-bottom: 2px solid #f8f9fa;
            padding-bottom: 15px;
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        
        .action-buttons {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 15px;
        }
        
        .btn {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            padding: 15px;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            font-weight: 600;
            text-decoration: none;
            transition: all 0.3s ease;
            text-align: center;
        }
        
        .btn-primary { background: #667eea; color: white; }
        .btn-secondary { background: #6c757d; color: white; }
        .btn-success { background: #4CAF50; color: white; }
        .btn-warning { background: #ff9800; color: white; }
        .btn-info { background: #2196F3; color: white; }
        .btn-danger { background: #f44336; color: white; }
        
        .btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
        }
        
        .activity-list {
            display: flex;
            flex-direction: column;
            gap: 15px;
        }
        
        .activity-item {
            display: flex;
            justify-content: between;
            align-items: center;
            padding: 15px;
            background: #f8f9fa;
            border-radius: 8px;
            border-left: 4px solid #667eea;
        }
        
        .activity-item p {
            margin: 0;
            flex: 1;
            font-weight: 500;
        }
        
        .activity-time {
            color: #6c757d;
            font-size: 0.9rem;
        }
        
        .system-status {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
            gap: 15px;
            margin-top: 20px;
        }
        
        .status-item {
            text-align: center;
            padding: 15px;
            background: #f8f9fa;
            border-radius: 8px;
        }
        
        .status-item .status-value {
            font-size: 1.5rem;
            font-weight: 700;
            color: #667eea;
        }
        
        .status-item .status-label {
            font-size: 0.9rem;
            color: #6c757d;
        }
        
        .welcome-banner {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 30px;
            border-radius: 12px;
            margin-bottom: 30px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        .welcome-text h1 {
            margin: 0 0 10px 0;
            font-size: 2rem;
        }
        
        .welcome-text p {
            margin: 0;
            opacity: 0.9;
        }
        
        .date-display {
            text-align: right;
        }
        
        .date-display .current-date {
            font-size: 1.2rem;
            font-weight: 600;
        }
        
        .date-display .current-time {
            font-size: 0.9rem;
            opacity: 0.8;
        }
        
        .empty-state {
            text-align: center;
            padding: 40px 20px;
            color: #6c757d;
        }
        
        .empty-state i {
            font-size: 3rem;
            margin-bottom: 15px;
            opacity: 0.5;
        }
    </style>
</head>
<body>
    <?php include("../includes/header.php"); ?>
    <?php include("../includes/sidebar.php"); ?>

    <main class="main-content">
        <div class="welcome-banner">
            <div class="welcome-text">
                <h1>Welcome back, Administrator!</h1>
                <p>Here's what's happening with your school today.</p>
            </div>
            <div class="date-display">
                <div class="current-date"><?php echo date('l, F j, Y'); ?></div>
                <div class="current-time" id="current-time"><?php echo date('h:i:s A'); ?></div>
            </div>
        </div>
        
        <div class="dashboard-stats">
            <div class="stat-card users">
                <h3><i class="fas fa-users"></i> Total Users</h3>
                <p><?php echo $users_count; ?></p>
                <div class="stat-trend trend-up">
                    <i class="fas fa-arrow-up"></i>
                    <span>12% from last month</span>
                </div>
            </div>
            
            <div class="stat-card students">
                <h3><i class="fas fa-user-graduate"></i> Students</h3>
                <p><?php echo $students_count; ?></p>
                <div class="stat-trend trend-up">
                    <i class="fas fa-arrow-up"></i>
                    <span>8% from last month</span>
                </div>
            </div>
            
            <div class="stat-card teachers">
                <h3><i class="fas fa-chalkboard-teacher"></i> Teachers</h3>
                <p><?php echo $teachers_count; ?></p>
                <div class="stat-trend trend-up">
                    <i class="fas fa-arrow-up"></i>
                    <span>5% from last month</span>
                </div>
            </div>
            
            <div class="stat-card courses">
                <h3><i class="fas fa-book"></i> Courses</h3>
                <p><?php echo $courses_count; ?></p>
                <div class="stat-trend trend-up">
                    <i class="fas fa-plus"></i>
                    <span>3 new this month</span>
                </div>
            </div>
            
            <div class="stat-card classes">
                <h3><i class="fas fa-door-open"></i> Classes</h3>
                <p><?php echo $classes_count; ?></p>
                <div class="stat-trend trend-up">
                    <i class="fas fa-arrow-up"></i>
                    <span>2 new this term</span>
                </div>
            </div>
            
            <div class="stat-card revenue">
                <h3><i class="fas fa-dollar-sign"></i> Total Revenue</h3>
                <p>$<?php echo number_format($total_revenue, 2); ?></p>
                <div class="stat-trend trend-up">
                    <i class="fas fa-arrow-up"></i>
                    <span>15% from last month</span>
                </div>
            </div>
            
            <div class="stat-card pending">
                <h3><i class="fas fa-clock"></i> Pending Fees</h3>
                <p><?php echo $pending_fees; ?></p>
                <div class="stat-trend trend-down">
                    <i class="fas fa-exclamation-circle"></i>
                    <span>Requires attention</span>
                </div>
            </div>
            
            <div class="stat-card pending">
                <h3><i class="fas fa-user-clock"></i> Pending Registrations</h3>
                <p><?php echo $pending_registrations; ?></p>
                <div class="stat-trend trend-down">
                    <i class="fas fa-exclamation-circle"></i>
                    <span>Needs review</span>
                </div>
            </div>
        </div>
        
        <div class="dashboard-grid">
            <div class="dashboard-card">
                <h2><i class="fas fa-bolt"></i> Quick Actions</h2>
                <div class="action-buttons">
                    <a href="../admin/manage_users.php" class="btn btn-primary">
                        <i class="fas fa-users-cog"></i> Manage Users
                    </a>
                    <a href="../admin/system_settings.php" class="btn btn-secondary">
                        <i class="fas fa-cogs"></i> System Settings
                    </a>
                    <a href="../registrar/manage_students.php" class="btn btn-success">
                        <i class="fas fa-user-graduate"></i> Manage Students
                    </a>
                    <a href="../finance/fee_management.php" class="btn btn-warning">
                        <i class="fas fa-money-bill-wave"></i> Fee Management
                    </a>
                    <a href="manage_users.php?filter=pending" class="btn btn-info">
                        <i class="fas fa-user-check"></i> Review Registrations
                    </a>
                    <a href="../admin/teacher_management.php" class="btn btn-danger">
                        <i class="fas fa-chalkboard-teacher"></i> Manage Teachers
                    </a>
                    <a href="../academics/manage_courses.php" class="btn btn-primary">
                        <i class="fas fa-book"></i> Manage Courses
                    </a>
                    <a href="../admin/reports.php" class="btn btn-secondary">
                        <i class="fas fa-chart-bar"></i> Generate Reports
                    </a>
                </div>
                
                <div class="system-status">
                    <div class="status-item">
                        <div class="status-value"><?php echo $db_connections; ?></div>
                        <div class="status-label">DB Connections</div>
                    </div>
                    <div class="status-item">
                        <div class="status-value"><?php echo round(memory_get_usage(true)/1024/1024, 2); ?>MB</div>
                        <div class="status-label">Memory Usage</div>
                    </div>
                    <div class="status-item">
                        <div class="status-value">Online</div>
                        <div class="status-label">System Status</div>
                    </div>
                    <div class="status-item">
                        <div class="status-value">v2.1.0</div>
                        <div class="status-label">System Version</div>
                    </div>
                </div>
            </div>
            
            <div class="dashboard-card">
                <h2><i class="fas fa-history"></i> Recent Activities</h2>
                <div class="activity-list">
                    <?php if (!empty($recent_activities)): ?>
                        <?php foreach ($recent_activities as $activity): ?>
                            <div class="activity-item">
                                <p><?php echo $activity['description']; ?></p>
                                <span class="activity-time">
                                    <?php 
                                        $time_ago = time() - strtotime($activity['created_at']);
                                        if ($time_ago < 3600) {
                                            echo round($time_ago/60) . ' minutes ago';
                                        } elseif ($time_ago < 86400) {
                                            echo round($time_ago/3600) . ' hours ago';
                                        } else {
                                            echo round($time_ago/86400) . ' days ago';
                                        }
                                    ?>
                                </span>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <div class="empty-state">
                            <i class="fas fa-inbox"></i>
                            <p>No recent activities</p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        
        <div class="dashboard-card">
            <h2><i class="fas fa-chart-line"></i> System Overview</h2>
            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 20px;">
                <div style="text-align: center; padding: 20px; background: #f8f9fa; border-radius: 8px;">
                    <div style="font-size: 2rem; font-weight: bold; color: #667eea;"><?php echo $paid_fees; ?></div>
                    <div style="color: #6c757d;">Paid Fees</div>
                </div>
                <div style="text-align: center; padding: 20px; background: #f8f9fa; border-radius: 8px;">
                    <div style="font-size: 2rem; font-weight: bold; color: #4CAF50;"><?php echo $students_count + $teachers_count; ?></div>
                    <div style="color: #6c757d;">Active Members</div>
                </div>
                <div style="text-align: center; padding: 20px; background: #f8f9fa; border-radius: 8px;">
                    <div style="font-size: 2rem; font-weight: bold; color: #FF9800;"><?php echo $classes_count; ?></div>
                    <div style="color: #6c757d;">Active Classes</div>
                </div>
                <div style="text-align: center; padding: 20px; background: #f8f9fa; border-radius: 8px;">
                    <div style="font-size: 2rem; font-weight: bold; color: #9C27B0;">98%</div>
                    <div style="color: #6c757d;">System Uptime</div>
                </div>
            </div>
        </div>
    </main>
    
    <?php include '../includes/footer.php'; ?>
    
    <script>
        // Update current time
        function updateTime() {
            const now = new Date();
            const timeString = now.toLocaleTimeString('en-US', { 
                hour: '2-digit', 
                minute: '2-digit', 
                second: '2-digit',
                hour12: true 
            });
            document.getElementById('current-time').textContent = timeString;
        }
        
        setInterval(updateTime, 1000);
        
        // Add animation to stat cards on load
        document.addEventListener('DOMContentLoaded', function() {
            const statCards = document.querySelectorAll('.stat-card');
            statCards.forEach((card, index) => {
                card.style.animationDelay = `${index * 0.1}s`;
                card.classList.add('fade-in');
            });
        });
    </script>
</body>
</html>