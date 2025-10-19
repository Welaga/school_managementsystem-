<?php
require_once '../config.php';
checkRole(['teacher']);

// Get teacher details
$teacher = getTeacherDetails($_SESSION['user_id']);
$teacher_id = $teacher['id'];

// Get statistics
$stmt = $pdo->prepare("SELECT COUNT(DISTINCT cs.class_id) as count 
                      FROM class_subjects cs 
                      WHERE cs.teacher_id = ?");
$stmt->execute([$teacher_id]);
$classes_count = $stmt->fetch()['count'];

$stmt = $pdo->prepare("SELECT COUNT(DISTINCT a.id) as count 
                      FROM assignments a 
                      WHERE a.teacher_id = ?");
$stmt->execute([$teacher_id]);
$assignments_count = $stmt->fetch()['count'];

// Get recent assignments
$stmt = $pdo->prepare("SELECT a.title, a.due_date, s.name as subject_name 
                      FROM assignments a 
                      JOIN subjects s ON a.subject_id = s.id 
                      WHERE a.teacher_id = ? 
                      ORDER BY a.created_at DESC LIMIT 5");
$stmt->execute([$teacher_id]);
$recent_assignments = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Teacher Dashboard - School Management System</title>
    <link rel="stylesheet" href="../../assets/css/style.css">
    <link rel="stylesheet" href="../../assets/css/dashboard.css">
</head>
<body>
    <?php include '../includes/header.php'; ?>
    <?php include '../includes/sidebar.php'; ?>
    
    <main class="main-content">
        <div class="content-header">
            <h1>Teacher Dashboard</h1>
            <p>Welcome, <?php echo $teacher['first_name'] . ' ' . $teacher['last_name']; ?></p>
        </div>
        
        <div class="dashboard-stats">
            <div class="stat-card">
                <h3>Classes Assigned</h3>
                <p><?php echo $classes_count; ?></p>
            </div>
            
            <div class="stat-card">
                <h3>Assignments Created</h3>
                <p><?php echo $assignments_count; ?></p>
            </div>
        </div>
        
        <div class="dashboard-actions">
            <h2>Quick Actions</h2>
            <div class="action-buttons">
                <a href="../teacher/upload_grades.php" class="btn btn-primary">Upload Grades</a>
                <a href="../teacher/manage_attendance.php" class="btn btn-secondary">Manage Attendance</a>
                <a href="../teacher/assignments.php" class="btn btn-success">Assignments</a>
            </div>
        </div>
        
        <div class="recent-activities">
            <h2>Recent Assignments</h2>
            <div class="activity-list">
                <?php if (count($recent_assignments) > 0): ?>
                    <?php foreach ($recent_assignments as $assignment): ?>
                        <div class="activity-item">
                            <p><?php echo $assignment['title']; ?> (<?php echo $assignment['subject_name']; ?>)</p>
                            <span class="activity-time">Due: <?php echo date('M j, Y', strtotime($assignment['due_date'])); ?></span>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <p>No recent assignments.</p>
                <?php endif; ?>
            </div>
        </div>
    </main>
    
    <?php include '../includes/footer.php'; ?>
</body>
</html>