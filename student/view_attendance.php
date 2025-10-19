<?php
require_once '../config.php';
checkRole(['admin', 'student']);

$page_title = "View Attendance";

// Get student details
$student = getStudentDetails($_SESSION['user_id']);
$student_id = $student['id'];
$class_id = $student['class_id'];

// Get attendance records
$stmt = $pdo->prepare("SELECT a.*, s.name as subject_name FROM attendance a 
                      JOIN subjects s ON a.subject_id = s.id 
                      WHERE a.student_id = ? ORDER BY a.date DESC");
$stmt->execute([$student_id]);
$attendance = $stmt->fetchAll();

// Calculate attendance statistics
$total_days = count($attendance);
$present_days = 0;
$absent_days = 0;
$late_days = 0;

foreach ($attendance as $record) {
    if ($record['status'] == 'present') {
        $present_days++;
    } elseif ($record['status'] == 'absent') {
        $absent_days++;
    } elseif ($record['status'] == 'late') {
        $late_days++;
    }
}

$attendance_percentage = $total_days > 0 ? round(($present_days / $total_days) * 100, 2) : 0;
?>

<?php include '../includes/header.php'; ?>
<?php include '../includes/sidebar.php'; ?>

<main class="main-content">
    <div class="content-header">
        <h1>My Attendance</h1>
        <p>Welcome, <?php echo $student['first_name'] . ' ' . $student['last_name']; ?> (Class: <?php echo $student['class_name']; ?>)</p>
    </div>
    
    <div class="dashboard-stats">
        <div class="stat-card">
            <h3>Total Days</h3>
            <p><?php echo $total_days; ?></p>
        </div>
        
        <div class="stat-card">
            <h3>Present</h3>
            <p><?php echo $present_days; ?></p>
        </div>
        
        <div class="stat-card">
            <h3>Absent</h3>
            <p><?php echo $absent_days; ?></p>
        </div>
        
        <div class="stat-card">
            <h3>Late</h3>
            <p><?php echo $late_days; ?></p>
        </div>
        
        <div class="stat-card">
            <h3>Attendance %</h3>
            <p><?php echo $attendance_percentage; ?>%</p>
        </div>
    </div>
    
    <?php if (count($attendance) > 0): ?>
        <div class="table-container">
            <h2>Attendance Records</h2>
            <table>
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>Subject</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($attendance as $record): ?>
                        <tr>
                            <td><?php echo date('M j, Y', strtotime($record['date'])); ?></td>
                            <td><?php echo $record['subject_name']; ?></td>
                            <td>
                                <span class="status-badge <?php echo $record['status']; ?>">
                                    <?php echo ucfirst($record['status']); ?>
                                </span>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php else: ?>
        <div class="alert alert-info">
            No attendance records available yet.
        </div>
    <?php endif; ?>
</main>

<?php include '../includes/footer.php'; ?>