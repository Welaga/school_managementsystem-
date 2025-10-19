<?php
require_once '../config.php';
session_start();

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: ../login.php');
    exit();
}

// Fetch transport data for overview
$vehicles = $pdo->query("SELECT COUNT(*) as total_vehicles FROM vehicles")->fetch()['total_vehicles'];
$drivers = $pdo->query("SELECT COUNT(*) as total_drivers FROM drivers")->fetch()['total_drivers'];
$schedules = $pdo->query("SELECT COUNT(*) as total_schedules FROM transport_schedule")->fetch()['total_schedules'];

// Fetch recent schedules
$recent_schedules = $pdo->query("SELECT ts.*, v.vehicle_number, d.driver_name 
                                FROM transport_schedule ts 
                                LEFT JOIN vehicles v ON ts.vehicle_id = v.id 
                                LEFT JOIN drivers d ON v.driver_id = d.id 
                                ORDER BY ts.id DESC LIMIT 5")->fetchAll();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Transport Overview</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-4">
        <h2>Transport System Overview</h2>
        
        <!-- Statistics Cards -->
        <div class="row mt-4">
            <div class="col-md-4">
                <div class="card text-white bg-primary">
                    <div class="card-body">
                        <h5 class="card-title">Total Vehicles</h5>
                        <h2><?php echo $vehicles; ?></h2>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card text-white bg-success">
                    <div class="card-body">
                        <h5 class="card-title">Total Drivers</h5>
                        <h2><?php echo $drivers; ?></h2>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card text-white bg-info">
                    <div class="card-body">
                        <h5 class="card-title">Active Schedules</h5>
                        <h2><?php echo $schedules; ?></h2>
                    </div>
                </div>
            </div>
        </div>

        <!-- Recent Schedules -->
        <div class="card mt-4">
            <div class="card-header">
                <h5>Recent Transport Schedules</h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>Day</th>
                                <th>Vehicle</th>
                                <th>Driver</th>
                                <th>Route</th>
                                <th>Stop</th>
                                <th>Time</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($recent_schedules as $schedule): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($schedule['day_of_week']); ?></td>
                                <td><?php echo htmlspecialchars($schedule['vehicle_number']); ?></td>
                                <td><?php echo htmlspecialchars($schedule['driver_name'] ?? 'N/A'); ?></td>
                                <td><?php echo htmlspecialchars($schedule['route']); ?></td>
                                <td><?php echo htmlspecialchars($schedule['stop_name']); ?></td>
                                <td><?php echo date('H:i', strtotime($schedule['departure_time'])); ?> - <?php echo date('H:i', strtotime($schedule['arrival_time'])); ?></td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Navigation -->
        <div class="mt-4">
            <?php if ($_SESSION['role'] === 'admin'): ?>
                <a href="transport_dashboard.php" class="btn btn-primary">Manage Transport</a>
            <?php endif; ?>
            <a href="../dashboard.php" class="btn btn-secondary">Back to Dashboard</a>
        </div>
    </div>
</body>
</html>