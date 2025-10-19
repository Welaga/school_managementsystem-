<?php
// Start session
session_start();

// Database connection
require_once '../config.php';
checkRole(['transport']);

// Get the logged-in user's ID from session
$id = $_SESSION['user_id'] ?? null;

// Get transport staff details if ID is available
$transport_staff = null;
if ($id) {
    $stmt = $pdo->prepare("SELECT * FROM transport WHERE id = :id");
    $stmt->execute([':id' => $id]);
    $transport_staff = $stmt->fetch();
}

// Get statistics
$stmt = $pdo->query("SELECT COUNT(*) as count FROM vehicles");
$vehicles_count = $stmt->fetch()['count'];

// Count drivers from the drivers table instead of vehicles
$stmt = $pdo->query("SELECT COUNT(*) as count FROM drivers");
$drivers_count = $stmt->fetch()['count'];

$stmt = $pdo->query("SELECT COUNT(*) as count FROM transport_schedule");
$routes_count = $stmt->fetch()['count'];

// Get recent transport schedules
$stmt = $pdo->query("SELECT ts.route, ts.stop_name, ts.arrival_time, ts.departure_time, v.vehicle_number 
                    FROM transport_schedule ts 
                    JOIN vehicles v ON ts.vehicle_id = v.id 
                    ORDER BY ts.day_of_week, ts.arrival_time LIMIT 5");
$recent_schedules = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Transport Dashboard - School Management System</title>
    <link rel="stylesheet" href="../../assets/css/style.css">
    <link rel="stylesheet" href="../../assets/css/dashboard.css">
</head>
<body>
    <?php include '../includes/header.php'; ?>
    <?php include '../includes/sidebar.php'; ?>
    
    <main class="main-content">
        <div class="content-header">
            <h1>Transport Dashboard</h1>
            <p>Welcome, <?php echo $_SESSION['username']; ?></p>
            <?php if ($transport_staff): ?>
                <p>Route: <?php echo $transport_staff['route']; ?></p>
            <?php endif; ?>
        </div>
        
        <div class="dashboard-stats">
            <div class="stat-card">
                <h3>Total Vehicles</h3>
                <p><?php echo $vehicles_count; ?></p>
            </div>
            
            <div class="stat-card">
                <h3>Drivers</h3>
                <p><?php echo $drivers_count; ?></p>
            </div>
            
            <div class="stat-card">
                <h3>Routes</h3>
                <p><?php echo $routes_count; ?></p>
            </div>
        </div>
        
        <div class="dashboard-actions">
            <h2>Quick Actions</h2>
            <div class="action-buttons">
                <a href="../transport/manage_vehicles.php" class="btn btn-primary">Manage Vehicles</a>
                <a href="../transport/manage_drivers.php" class="btn btn-secondary">Manage Drivers</a>
                <a href="../transport/transport_schedule.php" class="btn btn-success">Transport Schedule</a>
            </div>
        </div>
        
        <div class="recent-activities">
            <h2>Transport Schedules</h2>
            <div class="activity-list">
                <?php if (count($recent_schedules) > 0): ?>
                    <?php foreach ($recent_schedules as $schedule): ?>
                        <div class="activity-item">
                            <p>Route: <?php echo $schedule['route']; ?> - <?php echo $schedule['stop_name']; ?> (Vehicle: <?php echo $schedule['vehicle_number']; ?>)</p>
                            <span class="activity-time"><?php echo date('g:i A', strtotime($schedule['arrival_time'])) . ' - ' . date('g:i A', strtotime($schedule['departure_time'])); ?></span>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <p>No transport schedules available.</p>
                <?php endif; ?>
            </div>
        </div>
    </main>
    
    <?php include '../includes/footer.php'; ?>
</body>
</html>