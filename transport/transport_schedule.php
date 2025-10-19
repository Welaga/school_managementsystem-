<?php
require_once '../config.php';
checkRole(['admin', 'transport']);

$page_title = "Transport Schedule";
$message = '';

// Handle form actions
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['add_schedule'])) {
        $vehicle_id = $_POST['vehicle_id'];
        $route = trim($_POST['route']);
        $stop_name = trim($_POST['stop_name']);
        $arrival_time = $_POST['arrival_time'];
        $departure_time = $_POST['departure_time'];
        $day_of_week = $_POST['day_of_week'];
        
        try {
            $stmt = $pdo->prepare("INSERT INTO transport_schedule (vehicle_id, route, stop_name, arrival_time, departure_time, day_of_week) 
                                  VALUES (?, ?, ?, ?, ?, ?)");
            $stmt->execute([$vehicle_id, $route, $stop_name, $arrival_time, $departure_time, $day_of_week]);
            $message = "Schedule added successfully!";
        } catch (PDOException $e) {
            $message = "Error adding schedule: " . $e->getMessage();
        }
    } elseif (isset($_POST['update_schedule'])) {
        $schedule_id = $_POST['schedule_id'];
        $vehicle_id = $_POST['vehicle_id'];
        $route = trim($_POST['route']);
        $stop_name = trim($_POST['stop_name']);
        $arrival_time = $_POST['arrival_time'];
        $departure_time = $_POST['departure_time'];
        $day_of_week = $_POST['day_of_week'];
        
        try {
            $stmt = $pdo->prepare("UPDATE transport_schedule SET vehicle_id = ?, route = ?, stop_name = ?, 
                                  arrival_time = ?, departure_time = ?, day_of_week = ? WHERE id = ?");
            $stmt->execute([$vehicle_id, $route, $stop_name, $arrival_time, $departure_time, $day_of_week, $schedule_id]);
            $message = "Schedule updated successfully!";
        } catch (PDOException $e) {
            $message = "Error updating schedule: " . $e->getMessage();
        }
    } elseif (isset($_POST['delete_schedule'])) {
        $schedule_id = $_POST['schedule_id'];
        
        try {
            $stmt = $pdo->prepare("DELETE FROM transport_schedule WHERE id = ?");
            $stmt->execute([$schedule_id]);
            $message = "Schedule deleted successfully!";
        } catch (PDOException $e) {
            $message = "Error deleting schedule: " . $e->getMessage();
        }
    }
}

// Get all schedules with vehicle and driver information
$stmt = $pdo->query("SELECT ts.*, v.vehicle_number, d.driver_name 
                    FROM transport_schedule ts 
                    JOIN vehicles v ON ts.vehicle_id = v.id 
                    JOIN drivers d ON v.driver_id = d.id 
                    ORDER BY ts.day_of_week, ts.arrival_time");
$schedules = $stmt->fetchAll();

// Get vehicles for dropdown
$stmt = $pdo->query("SELECT v.*, d.driver_name FROM vehicles v JOIN drivers d ON v.driver_id = d.id ORDER BY v.vehicle_number");
$vehicles = $stmt->fetchAll();

// Get schedule for editing
$edit_schedule = null;
if (isset($_GET['edit'])) {
    $stmt = $pdo->prepare("SELECT * FROM transport_schedule WHERE id = ?");
    $stmt->execute([$_GET['edit']]);
    $edit_schedule = $stmt->fetch();
}

// Days of week for dropdown
$days_of_week = [
    'Monday' => 'Monday',
    'Tuesday' => 'Tuesday',
    'Wednesday' => 'Wednesday',
    'Thursday' => 'Thursday',
    'Friday' => 'Friday',
    'Saturday' => 'Saturday',
    'Sunday' => 'Sunday'
];

// Group schedules by day
$schedule_by_day = [];
foreach ($schedules as $schedule) {
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
     <a href="../transport/index.php" class="btn btn-secondary" style="float: right;">
  <button>Back</button>
    <style>
    button {
      background: #e90b0bff;      /* Green background */
      color: white;             /* White text */
      padding: 10px 20px;       /* Space inside */
      border: none;             /* No border */
      border-radius: 6px;       /* Rounded corners */
      cursor: pointer;          /* Pointer on hover */
      font-size: 16px;          /* Readable text */
      transition: 0.3s;         /* Smooth hover effect */
    }

    button:hover {
      background: #45a049;      /* Slightly darker on hover */
    }
  </style>
</a>

        <h1>Transport Schedule</h1>
    </div>
    
    <?php if (!empty($message)): ?>
        <div class="alert <?php echo strpos($message, 'Error') !== false ? 'alert-error' : 'alert-success'; ?>">
            <?php echo $message; ?>
        </div>
    <?php endif; ?>
    
    <div class="form-container">
        <h2><?php echo $edit_schedule ? 'Edit Schedule' : 'Add New Schedule'; ?></h2>
        <form method="POST">
            <?php if ($edit_schedule): ?>
                <input type="hidden" name="schedule_id" value="<?php echo $edit_schedule['id']; ?>">
            <?php endif; ?>
            
            <div class="form-group">
                <label for="vehicle_id">Vehicle</label>
                <select id="vehicle_id" name="vehicle_id" required>
                    <option value="">Select Vehicle</option>
                    <?php foreach ($vehicles as $vehicle): ?>
                        <option value="<?php echo $vehicle['id']; ?>" 
                            <?php echo $edit_schedule && $edit_schedule['vehicle_id'] == $vehicle['id'] ? 'selected' : ''; ?>>
                            <?php echo $vehicle['vehicle_number']; ?> (<?php echo $vehicle['driver_name']; ?>)
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            
            <div class="form-group">
                <label for="route">Route</label>
                <input type="text" id="route" name="route" required 
                       value="<?php echo $edit_schedule ? $edit_schedule['route'] : ''; ?>">
            </div>
            
            <div class="form-group">
                <label for="stop_name">Stop Name</label>
                <input type="text" id="stop_name" name="stop_name" 
                       value="<?php echo $edit_schedule ? $edit_schedule['stop_name'] : ''; ?>">
            </div>
            
            <div class="form-group">
                <label for="arrival_time">Arrival Time</label>
                <input type="time" id="arrival_time" name="arrival_time" required 
                       value="<?php echo $edit_schedule ? $edit_schedule['arrival_time'] : ''; ?>">
            </div>
            
            <div class="form-group">
                <label for="departure_time">Departure Time</label>
                <input type="time" id="departure_time" name="departure_time" required 
                       value="<?php echo $edit_schedule ? $edit_schedule['departure_time'] : ''; ?>">
            </div>
            
            <div class="form-group">
                <label for="day_of_week">Day of Week</label>
                <select id="day_of_week" name="day_of_week" required>
                    <option value="">Select Day</option>
                    <?php foreach ($days_of_week as $day): ?>
                        <option value="<?php echo $day; ?>" 
                            <?php echo $edit_schedule && $edit_schedule['day_of_week'] == $day ? 'selected' : ''; ?>>
                            <?php echo $day; ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            
            <div class="form-group">
                <?php if ($edit_schedule): ?>
                    <button type="submit" name="update_schedule" class="btn btn-primary">Update Schedule</button>
                    <a href="transport_schedule.php" class="btn btn-secondary">Cancel</a>
                <?php else: ?>
                    <button type="submit" name="add_schedule" class="btn btn-primary">Add Schedule</button>
                <?php endif; ?>
            </div>
        </form>
    </div>
    
    <div class="schedule-container">
        <h2>Transport Schedule</h2>
        <?php if (count($schedules) > 0): ?>
            <?php foreach ($days_of_week as $day): ?>
                <?php if (isset($schedule_by_day[$day])): ?>
                    <div class="schedule-day">
                        <h3><?php echo $day; ?></h3>
                        <div class="schedule-table">
                            <table>
                                <thead>
                                    <tr>
                                        <th>Vehicle</th>
                                        <th>Route</th>
                                        <th>Stop</th>
                                        <th>Arrival Time</th>
                                        <th>Departure Time</th>
                                        <th>Driver</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($schedule_by_day[$day] as $schedule): ?>
                                        <tr>
                                            <td><?php echo $schedule['vehicle_number']; ?></td>
                                            <td><?php echo $schedule['route']; ?></td>
                                            <td><?php echo $schedule['stop_name'] ? $schedule['stop_name'] : 'N/A'; ?></td>
                                            <td><?php echo date('g:i A', strtotime($schedule['arrival_time'])); ?></td>
                                            <td><?php echo date('g:i A', strtotime($schedule['departure_time'])); ?></td>
                                            <td><?php echo $schedule['driver_name'] ? $schedule['driver_name'] : 'N/A'; ?></td>
                                            <td>
                                                <a href="transport_schedule.php?edit=<?php echo $schedule['id']; ?>" class="btn btn-sm btn-primary">Edit</a>
                                                <form method="POST" class="inline-form">
                                                    <input type="hidden" name="schedule_id" value="<?php echo $schedule['id']; ?>">
                                                    <button type="submit" name="delete_schedule" class="btn btn-sm btn-danger" 
                                                            onclick="return confirm('Are you sure you want to delete this schedule?')">Delete</button>
                                                </form>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                <?php endif; ?>
            <?php endforeach; ?>
        <?php else: ?>
            <div class="alert alert-info">
                No transport schedules found.
            </div>
        <?php endif; ?>
    </div>
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

.schedule-day h3 {
    color: #2c3e50;
    margin-bottom: 1rem;
    padding-bottom: 0.5rem;
    border-bottom: 2px solid #ecf0f1;
}
</style>