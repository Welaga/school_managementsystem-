<?php
require_once '../config.php';
checkRole(['registrar']);

// Get statistics
$stmt = $pdo->query("SELECT COUNT(*) as count FROM students");
$students_count = $stmt->fetch()['count'];

$stmt = $pdo->query("SELECT COUNT(*) as count FROM classes");
$classes_count = $stmt->fetch()['count'];

$stmt = $pdo->query("SELECT COUNT(*) as count FROM courses");
$courses_count = $stmt->fetch()['count'];

// Get recent student registrations
$stmt = $pdo->query("SELECT s.first_name, s.last_name, s.created_at, c.name as class_name 
                    FROM students s 
                    LEFT JOIN classes c ON s.class_id = c.id 
                    ORDER BY s.created_at DESC LIMIT 5");
$recent_students = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registrar Dashboard - School Management System</title>
    <link rel="stylesheet" href="../../assets/css/style.css">
    <link rel="stylesheet" href="../../assets/css/dashboard.css">
</head>
<body>
    <?php include '../includes/header.php'; ?>
    <?php include '../includes/sidebar.php'; ?>
    
    <main class="main-content">
        <div class="content-header">
            <h1>Registrar Dashboard</h1>
        </div>
        
        <div class="dashboard-stats">
            <div class="stat-card">
                <h3>Total Students</h3>
                <p><?php echo $students_count; ?></p>
            </div>
            
            <div class="stat-card">
                <h3>Classes</h3>
                <p><?php echo $classes_count; ?></p>
            </div>
            
            <div class="stat-card">
                <h3>Courses</h3>
                <p><?php echo $courses_count; ?></p>
            </div>
        </div>
        
        <div class="dashboard-actions">
            <h2>Quick Actions</h2>
            <div class="action-buttons">
                <a href="../../registrar/manage_students.php" class="btn btn-primary">Manage Students</a>
                <a href="../../registrar/manage_classes.php" class="btn btn-secondary">Manage Classes</a>
                <a href="../../registrar/manage_courses.php" class="btn btn-success">Manage Courses</a>
            </div>
        </div>
        
        <div class="recent-activities">
            <h2>Recent Student Registrations</h2>
            <div class="activity-list">
                <?php if (count($recent_students) > 0): ?>
                    <?php foreach ($recent_students as $student): ?>
                        <div class="activity-item">
                            <p><?php echo $student['first_name'] . ' ' . $student['last_name']; ?> registered in <?php echo $student['class_name']; ?></p>
                            <span class="activity-time"><?php echo date('M j, Y', strtotime($student['created_at'])); ?></span>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <p>No recent student registrations.</p>
                <?php endif; ?>
            </div>
        </div>
    </main>
    
    <?php include '../includes/footer.php'; ?>
</body>
</html>