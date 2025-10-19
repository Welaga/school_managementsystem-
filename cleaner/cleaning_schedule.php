<?php
require_once '../config.php';
checkRole(['admin', 'cleaner']);

$page_title = "Cleaning Schedule";
$message = '';

// Get cleaner details
$stmt = $pdo->prepare("SELECT * FROM cleaners WHERE user_id = ?");
$stmt->execute([$_SESSION['user_id']]);
$cleaner = $stmt->fetch();

// Handle mark complete action
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['mark_complete'])) {
    $schedule_id = $_POST['schedule_id'];
    
    try {
        $stmt = $pdo->prepare("UPDATE cleaning_schedule SET status = 'completed' WHERE id = ?");
        $stmt->execute([$schedule_id]);
        $message = "Task marked as completed!";
    } catch (PDOException $e) {
        $message = "Error updating task: " . $e->getMessage();
    }
}

// Get cleaning schedule
if ($_SESSION['role'] == 'admin') {
    // Admin can see all schedules
    $stmt = $pdo->query("SELECT cs.*, c.assigned_area, u.username as cleaner_name 
                        FROM cleaning_schedule cs 
                        JOIN cleaners c ON cs.cleaner_id = c.id 
                        JOIN users u ON c.user_id = u.id 
                        ORDER BY cs.day_of_week, cs.start_time");
    $cleaning_schedule = $stmt->fetchAll();
} else {
    // Cleaner can only see their own schedule
    $stmt = $pdo->prepare("SELECT cs.*, c.assigned_area 
                          FROM cleaning_schedule cs 
                          JOIN cleaners c ON cs.cleaner_id = c.id 
                          WHERE c.user_id = ? 
                          ORDER BY cs.day_of_week, cs.start_time");
    $stmt->execute([$_SESSION['user_id']]);
    $cleaning_schedule = $stmt->fetchAll();
}

// Days of week for display
$days_of_week = [
    'Monday' => 'Monday',
    'Tuesday' => 'Tuesday',
    'Wednesday' => 'Wednesday',
    'Thursday' => 'Thursday',
    'Friday' => 'Friday',
    'Saturday' => 'Saturday',
    'Sunday' => 'Sunday'
];

// Group schedule by day
$schedule_by_day = [];
foreach ($cleaning_schedule as $schedule) {
    $day = $schedule['day_of_week'];
    if (!isset($schedule_by_day[$day])) {
        $schedule_by_day[$day] = [];
    }
    $schedule_by_day[$day][] = $schedule;
}
?>

<?php include '../includes/header.php'; ?>
<?php include '../includes/sidebar.php'; ?>

<main class="main-content">
    <div class="content-header">
        <h1>Cleaning Schedule</h1>
        <?php if ($cleaner): ?>
            <p>Assigned Area: <?php echo $cleaner['assigned_area']; ?></p>
        <?php endif; ?>
    </div>
    
    <?php if (!empty($message)): ?>
        <div class="alert <?php echo strpos($message, 'Error') !== false ? 'alert-error' : 'alert-success'; ?>">
            <?php echo $message; ?>
        </div>
    <?php endif; ?>
    
    <?php if ($_SESSION['role'] == 'admin'): ?>
        <div class="dashboard-actions">
            <h2>Admin Actions</h2>
            <div class="action-buttons">
                <a href="../admin/manage_cleaners.php" class="btn btn-primary">Manage Cleaners</a>
                <a href="../admin/manage_cleaning_schedule.php" class="btn btn-secondary">Manage Schedule</a>
            </div>
        </div>
    <?php endif; ?>
    
    <?php if (count($cleaning_schedule) > 0): ?>
        <div class="schedule-container">
            <?php foreach ($days_of_week as $day): ?>
                <?php if (isset($schedule_by_day[$day])): ?>
                    <div class="schedule-day">
                        <h2><?php echo $day; ?></h2>
                        <div class="schedule-table">
                            <table>
                                <thead>
                                    <tr>
                                        <th>Time</th>
                                        <th>Area</th>
                                        <?php if ($_SESSION['role'] == 'admin'): ?>
                                            <th>Cleaner</th>
                                        <?php endif; ?>
                                        <th>Status</th>
                                        <?php if ($_SESSION['role'] != 'admin'): ?>
                                            <th>Actions</th>
                                        <?php endif; ?>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($schedule_by_day[$day] as $schedule): ?>
                                        <tr>
                                            <td><?php echo date('g:i A', strtotime($schedule['start_time'])) . ' - ' . date('g:i A', strtotime($schedule['end_time'])); ?></td>
                                            <td><?php echo $schedule['area']; ?></td>
                                            <?php if ($_SESSION['role'] == 'admin'): ?>
                                                <td><?php echo $schedule['cleaner_name']; ?></td>
                                            <?php endif; ?>
                                            <td>
                                                <span class="status-badge <?php echo $schedule['status']; ?>">
                                                    <?php echo ucfirst($schedule['status']); ?>
                                                </span>
                                            </td>
                                            <?php if ($_SESSION['role'] != 'admin'): ?>
                                                <td>
                                                    <?php if ($schedule['status'] == 'pending'): ?>
                                                        <form method="POST" class="inline-form">
                                                            <input type="hidden" name="schedule_id" value="<?php echo $schedule['id']; ?>">
                                                            <button type="submit" name="mark_complete" class="btn btn-sm btn-success">Mark Complete</button>
                                                        </form>
                                                    <?php else: ?>
                                                        <span class="completed-text">Completed</span>
                                                    <?php endif; ?>
                                                </td>
                                            <?php endif; ?>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                <?php endif; ?>
            <?php endforeach; ?>
        </div>
    <?php else: ?>
        <div class="alert alert-info">
            No cleaning schedule assigned yet.
        </div>
    <?php endif; ?>
</main>

<?php include '../includes/footer.php'; ?>

<style>
.schedule-container {
    display: grid;
    gap: 2rem;
}

.schedule-day {
    background: white;
    border-radius: 10px;
    padding: 1.5rem;
    box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
}

.schedule-day h2 {
    color: #2c3e50;
    margin-bottom: 1rem;
    padding-bottom: 0.5rem;
    border-bottom: 2px solid #ecf0f1;
}

.completed-text {
    color: #00b894;
    font-weight: 600;
}
</style>