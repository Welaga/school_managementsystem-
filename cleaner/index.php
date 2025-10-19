<?php
require_once '../config.php';
checkRole(['cleaner']);

// Get cleaner details
$stmt = $pdo->prepare("SELECT * FROM cleaners WHERE user_id = ?");
$stmt->execute([$_SESSION['user_id']]);
$cleaner = $stmt->fetch();

// Get cleaning schedule
$stmt = $pdo->prepare("SELECT * FROM cleaning_schedule WHERE cleaner_id = ? ORDER BY day_of_week, start_time");
$stmt->execute([$cleaner['id']]);
$cleaning_schedule = $stmt->fetchAll();

// Days of week for display
$days_of_week = [
    'Monday' => 'Mon',
    'Tuesday' => 'Tue',
    'Wednesday' => 'Wed',
    'Thursday' => 'Thu',
    'Friday' => 'Fri',
    'Saturday' => 'Sat',
    'Sunday' => 'Sun'
];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cleaner Dashboard - School Management System</title>
    <link rel="stylesheet" href="../../assets/css/style.css">
    <link rel="stylesheet" href="../../assets/css/dashboard.css">
</head>
<body>
    <?php include '../includes/header.php'; ?>
    <?php include '../includes/sidebar.php'; ?>
    
    <main class="main-content">
        <div class="content-header">
            <h1>Cleaner Dashboard</h1>
            <p>Welcome, <?php echo $_SESSION['username']; ?></p>
            <?php if ($cleaner): ?>
                <p>Assigned Area: <?php echo $cleaner['assigned_area']; ?></p>
            <?php endif; ?>
        </div>
        
        <div class="cleaning-schedule">
            <h2>Your Cleaning Schedule</h2>
            
            <?php if (count($cleaning_schedule) > 0): ?>
                <div class="schedule-table">
                    <table>
                        <thead>
                            <tr>
                                <th>Day</th>
                                <th>Time</th>
                                <th>Area</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($cleaning_schedule as $schedule): ?>
                                <tr>
                                    <td><?php echo $days_of_week[$schedule['day_of_week']]; ?></td>
                                    <td><?php echo date('g:i A', strtotime($schedule['start_time'])) . ' - ' . date('g:i A', strtotime($schedule['end_time'])); ?></td>
                                    <td><?php echo $schedule['area']; ?></td>
                                    <td>
                                        <span class="status-badge <?php echo $schedule['status']; ?>">
                                            <?php echo ucfirst($schedule['status']); ?>
                                        </span>
                                    </td>
                                    <td>
                                        <?php if ($schedule['status'] == 'pending'): ?>
                                            <form action="mark_complete.php" method="POST" class="inline-form">
                                                <input type="hidden" name="schedule_id" value="<?php echo $schedule['id']; ?>">
                                                <button type="submit" class="btn btn-sm btn-success">Mark Complete</button>
                                            </form>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php else: ?>
                <p>No cleaning schedule assigned yet.</p>
            <?php endif; ?>
        </div>
    </main>
    
    <?php include '../includes/footer.php'; ?>
</body>
</html>