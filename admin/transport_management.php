<?php
require_once '../config.php';

// Initialize variables
$error = '';
$success = '';

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['register_driver'])) {
        // Register driver logic
        $driver_name = $_POST['driver_name'];
        $contact = $_POST['contact'];
        $email = $_POST['email'];
        $address = $_POST['address'];
        $license_number = $_POST['license_number'];
        $license_expiry = $_POST['license_expiry'];
        $emergency_contact = $_POST['emergency_contact'];
        $hire_date = $_POST['hire_date'];
        $username = $_POST['username'];
        $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
        
        try {
            // First, create a user account for the driver
            $stmt = $pdo->prepare("INSERT INTO users (username, password, role, email, status, created_at) 
                                  VALUES (?, ?, 'transport', ?, 1, NOW())");
            $stmt->execute([$username, $password, $email]);
            $user_id = $pdo->lastInsertId();
            
            // Then, insert driver details
            $stmt = $pdo->prepare("INSERT INTO drivers (user_id, driver_name, contact, email, address, license_number, license_expiry, emergency_contact, hire_date, status, created_at) 
                                  VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, 'active', NOW())");
            $stmt->execute([$user_id, $driver_name, $contact, $email, $address, $license_number, $license_expiry, $emergency_contact, $hire_date]);
            
            $driver_id = $pdo->lastInsertId();
            $success = "Driver registered successfully! Driver ID: " . $driver_id . " | Username: " . $username;
        } catch (PDOException $e) {
            $error = "Error registering driver: " . $e->getMessage();
        }
    } 
    // ... rest of your existing form handling code remains the same
    elseif (isset($_POST['register_vehicle'])) {
        // Register vehicle logic (unchanged)
        $vehicle_number = $_POST['vehicle_number'];
        $model = $_POST['model'];
        $year = $_POST['year'];
        $color = $_POST['color'];
        $vehicle_type_id = $_POST['vehicle_type_id'];
        $capacity = $_POST['capacity'];
        $driver_id = $_POST['driver_id'];
        $route_id = $_POST['route_id'];
        $insurance_number = $_POST['insurance_number'];
        $insurance_expiry = $_POST['insurance_expiry'];
        
        try {
            $stmt = $pdo->prepare("INSERT INTO vehicles (vehicle_number, model, year, color, vehicle_type_id, capacity, driver_id, route_id, insurance_number, insurance_expiry, status, created_at) 
                                  VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 'active', NOW())");
            $stmt->execute([$vehicle_number, $model, $year, $color, $vehicle_type_id, $capacity, $driver_id, $route_id, $insurance_number, $insurance_expiry]);
            
            $vehicle_id = $pdo->lastInsertId();
            $success = "Vehicle registered successfully! Vehicle ID: " . $vehicle_id;
        } catch (PDOException $e) {
            $error = "Error registering vehicle: " . $e->getMessage();
        }
    }
    // ... rest of your existing code
}

// Fetch data for display (unchanged)
try {
    // Get all drivers
    $drivers = $pdo->query("SELECT * FROM drivers ORDER BY id DESC")->fetchAll(PDO::FETCH_ASSOC);
    
    // Get all vehicles with related info
    $vehicles = $pdo->query("
        SELECT v.*, d.driver_name, vt.type_name as vehicle_type, tr.route_name 
        FROM vehicles v 
        LEFT JOIN drivers d ON v.driver_id = d.id 
        LEFT JOIN vehicle_types vt ON v.vehicle_type_id = vt.id
        LEFT JOIN transport_routes tr ON v.route_id = tr.id
        ORDER BY v.id DESC
    ")->fetchAll(PDO::FETCH_ASSOC);
    
    // Get all vehicle types
    $vehicle_types = $pdo->query("SELECT * FROM vehicle_types")->fetchAll(PDO::FETCH_ASSOC);
    
    // Get all routes
    $routes = $pdo->query("SELECT * FROM transport_routes")->fetchAll(PDO::FETCH_ASSOC);
    
    // Get all schedules with related info
    $schedules = $pdo->query("
        SELECT ts.*, v.vehicle_number, d.driver_name, tr.route_name 
        FROM transport_schedules ts 
        LEFT JOIN vehicles v ON ts.vehicle_id = v.id 
        LEFT JOIN drivers d ON ts.driver_id = d.id 
        LEFT JOIN transport_routes tr ON ts.route_id = tr.id
        ORDER BY ts.id DESC
    ")->fetchAll(PDO::FETCH_ASSOC);
    
    // Get all students
    $students = $pdo->query("SELECT * FROM students ORDER BY first_name, last_name")->fetchAll(PDO::FETCH_ASSOC);
    
    // Get student transport assignments
    $student_transports = $pdo->query("
        SELECT st.*, s.first_name, s.last_name, ts.departure_time, ts.arrival_time, 
               v.vehicle_number, d.driver_name, tr.route_name 
        FROM student_transport st 
        JOIN students s ON st.student_id = s.id 
        JOIN transport_schedules ts ON st.schedule_id = ts.id 
        LEFT JOIN vehicles v ON ts.vehicle_id = v.id 
        LEFT JOIN drivers d ON ts.driver_id = d.id 
        LEFT JOIN transport_routes tr ON ts.route_id = tr.id
        ORDER BY st.id DESC
    ")->fetchAll(PDO::FETCH_ASSOC);
    
    // Get transport bookings
    $bookings = $pdo->query("
        SELECT tb.*, v.vehicle_number, d.driver_name, u.username as booked_by_name 
        FROM transport_bookings tb 
        LEFT JOIN vehicles v ON tb.vehicle_id = v.id 
        LEFT JOIN drivers d ON tb.driver_id = d.id 
        LEFT JOIN users u ON tb.booked_by = u.id
        ORDER BY tb.booking_date DESC, tb.start_time DESC
    ")->fetchAll(PDO::FETCH_ASSOC);
    
} catch (PDOException $e) {
    $error = "Error fetching data: " . $e->getMessage();
    // Initialize empty arrays to prevent errors
    $drivers = []; $vehicles = []; $vehicle_types = []; $routes = []; 
    $schedules = []; $students = []; $student_transports = []; $bookings = [];
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Transport Management System</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        /* Add the responsive CSS styles we created earlier */
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
        
        /* Tabs Navigation */
        .tabs { 
            display: flex; 
            background-color: #fff; 
            border-radius: 10px; 
            overflow: hidden; 
            margin-bottom: 30px; 
            flex-wrap: wrap;
            box-shadow: 0 2px 15px rgba(0, 0, 0, 0.1);
        }
        
        .tab { 
            padding: 20px 15px; 
            cursor: pointer; 
            font-weight: 600; 
            text-align: center; 
            flex: 1; 
            min-width: 150px; 
            transition: all 0.3s ease; 
            border-bottom: 3px solid transparent;
            color: #666;
        }
        
        .tab:hover {
            background-color: #f8f9fa;
            color: #667eea;
        }
        
        .tab.active { 
            border-bottom: 3px solid #667eea; 
            color: #667eea; 
            background-color: #f8f9fa; 
        }
        
        .tab i {
            margin-right: 10px;
        }
        
        .tab-content { 
            display: none; 
            animation: fadeIn 0.5s ease-in; 
        }
        
        .tab-content.active { 
            display: block; 
        }
        
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }
        
        /* Card Styles */
        .card { 
            background-color: #fff; 
            border-radius: 10px; 
            padding: 25px; 
            margin-bottom: 25px;
            box-shadow: 0 2px 15px rgba(0, 0, 0, 0.1);
            border: 1px solid #eaeaea;
        }
        
        .card-header {
            border-bottom: 2px solid #f0f0f0;
            padding-bottom: 15px;
            margin-bottom: 20px;
        }
        
        .card-header h2 {
            color: #2c3e50;
            font-size: 1.5rem;
            font-weight: 600;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        
        /* Form Styles */
        .form-row { 
            display: flex; 
            flex-wrap: wrap; 
            margin: 0 -10px; 
        }
        
        .form-group { 
            flex: 1 0 calc(33.333% - 20px); 
            margin: 0 10px 20px; 
            min-width: 250px; 
        }
        
        .form-group.full-width { 
            flex: 1 0 calc(100% - 20px); 
        }
        
        label { 
            display: block; 
            margin-bottom: 8px; 
            font-weight: 600; 
            color: #2c3e50; 
        }
        
        .form-control { 
            width: 100%; 
            padding: 12px; 
            border: 2px solid #eaeaea; 
            border-radius: 6px; 
            font-size: 1rem; 
            transition: all 0.3s ease;
            background-color: #fafafa;
        }
        
        .form-control:focus {
            border-color: #667eea;
            outline: none;
            box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
            background-color: #fff;
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
        
        /* Table Styles */
        .table-container { 
            overflow-x: auto; 
            margin-top: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }
        
        table { 
            width: 100%; 
            border-collapse: collapse; 
            background: white;
        }
        
        th, td { 
            padding: 12px 15px; 
            text-align: left; 
            border-bottom: 1px solid #eaeaea; 
        }
        
        th { 
            background-color: #667eea; 
            color: white; 
            font-weight: 600;
            position: sticky;
            top: 0;
        }
        
        tr:hover {
            background-color: #f8f9fa;
        }
        
        /* Status Badges */
        .status-badge { 
            padding: 6px 12px; 
            border-radius: 15px; 
            font-size: 0.8rem; 
            font-weight: 600; 
            display: inline-block;
        }
        
        .status-active { 
            background-color: #d4edda; 
            color: #155724; 
        }
        
        .status-pending { 
            background-color: #fff3cd; 
            color: #856404; 
        }
        
        /* Stats Container */
        .stats-container { 
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
            font-size: 2rem;
            margin-bottom: 10px;
            font-weight: 300;
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
        
        .alert-success {
            background-color: #d4edda;
            color: #155724;
            border-left-color: #28a745;
        }
        
        .alert-error {
            background-color: #f8d7da;
            color: #721c24;
            border-left-color: #dc3545;
        }
        
        .alert i {
            margin-right: 10px;
            font-size: 1.2rem;
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
            
            .form-group { 
                flex: 1 0 calc(100% - 20px); 
            }
            
            .tab { 
                flex: 1 0 100%; 
            }
            
            .tabs {
                flex-direction: column;
            }
            
            .user-info {
                position: relative;
                top: 0;
                right: 0;
                margin-bottom: 15px;
                text-align: center;
            }
            
            .stats-container {
                grid-template-columns: 1fr;
            }
            
            .content-header {
                padding: 20px;
            }
            
            .content-header h1 {
                font-size: 2rem;
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
            <h3><i class="fas fa-bus"></i> Transport System</h3>
        </div>
        <ul class="sidebar-menu">
             <li><a href="../admin/" class="<?php echo basename($_SERVER['PHP_SELF']) == 'index.php' ? 'active' : ''; ?>">Dashboard</a></li>
            <li><a href="../admin/teacher_management.php">Teacher</a></li>
            <li><a href="../admin/manage_users.php">Manage Users</a></li>
            <li><a href="../admin/system_settings.php">System Settings</a></li>
            <li><a href="../admin/register_student.php">Students</a></li>
            <li><a href="../admin/manage_classes.php">Classes</a></li>
            <li><a href="../admin/finance/fee_management.php">Finance</a></li>
            <li><a href="../admin/registrar_cleaner.php">Cleaner</a></li>
            <li><a href="../admin/transport_management.php" class="<?php echo basename($_SERVER['PHP_SELF']) == 'transport_management.php' ? 'active' : ''; ?>"><i class="fas fa-bus"></i> Transport Management</a></li>
            <li><a href="../admin/profile.php">Profile</a></li>
            <li><a href="../admin/change_password.php">Change Password</a></li>
        </ul>
    </div>

    <!-- Main Content -->
    <div class="main-content" id="mainContent">
        <div class="content-header">
            <div class="user-info">
                Welcome admin (Admin) | <a href="/school_managementsystem/logout.php" style="color: white; text-decoration: underline;">Logout</a>
            </div>
            <h1><i class="fas fa-bus"></i> Transport Management System</h1>
            <p>Manage drivers, vehicles, routes, and student transportation</p>
        </div>
        
        <?php if ($success): ?>
            <div class="alert alert-success">
                <i class="fas fa-check-circle"></i> <?php echo $success; ?>
            </div>
        <?php endif; ?>
        
        <?php if ($error): ?>
            <div class="alert alert-error">
                <i class="fas fa-exclamation-circle"></i> <?php echo $error; ?>
            </div>
        <?php endif; ?>
        
        <div class="stats-container">
            <div class="stat-card">
                <h3><?php echo count($drivers); ?></h3>
                <p>Total Drivers</p>
            </div>
            <div class="stat-card">
                <h3><?php echo count($vehicles); ?></h3>
                <p>Available Vehicles</p>
            </div>
            <div class="stat-card">
                <h3><?php echo count($schedules); ?></h3>
                <p>Active Schedules</p>
            </div>
            <div class="stat-card">
                <h3><?php echo count($student_transports); ?></h3>
                <p>Student Assignments</p>
            </div>
        </div>
        
        <div class="tabs">
            <div class="tab active" onclick="showTab('drivers')"><i class="fas fa-id-card"></i> Drivers</div>
            <div class="tab" onclick="showTab('vehicles')"><i class="fas fa-car"></i> Vehicles</div>
            <div class="tab" onclick="showTab('schedules')"><i class="fas fa-calendar-alt"></i> Schedules</div>
            <div class="tab" onclick="showTab('assignments')"><i class="fas fa-user-graduate"></i> Student Assignments</div>
            <div class="tab" onclick="showTab('bookings')"><i class="fas fa-calendar-check"></i> Bookings</div>
            <div class="tab" onclick="showTab('routes')"><i class="fas fa-route"></i> Routes</div>
        </div>
        
        <!-- Drivers Tab -->
        <div class="tab-content active" id="drivers">
            <div class="card">
                <div class="card-header">
                    <h2><i class="fas fa-id-card"></i> Register New Driver</h2>
                </div>
                <form method="POST">
                    <div class="form-row">
                        <div class="form-group">
                            <label>Driver Name</label>
                            <input type="text" class="form-control" name="driver_name" required>
                        </div>
                        <div class="form-group">
                            <label>Contact Number</label>
                            <input type="text" class="form-control" name="contact" required>
                        </div>
                        <div class="form-group">
                            <label>Email</label>
                            <input type="email" class="form-control" name="email" required>
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group">
                            <label>Username</label>
                            <input type="text" class="form-control" name="username" required>
                        </div>
                        <div class="form-group">
                            <label>Password</label>
                            <input type="password" class="form-control" name="password" required>
                        </div>
                        <div class="form-group">
                            <label>License Number</label>
                            <input type="text" class="form-control" name="license_number" required>
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group">
                            <label>License Expiry</label>
                            <input type="date" class="form-control" name="license_expiry" required>
                        </div>
                        <div class="form-group">
                            <label>Emergency Contact</label>
                            <input type="text" class="form-control" name="emergency_contact">
                        </div>
                        <div class="form-group">
                            <label>Hire Date</label>
                            <input type="date" class="form-control" name="hire_date" required>
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group full-width">
                            <label>Address</label>
                            <textarea class="form-control" name="address" rows="2"></textarea>
                        </div>
                    </div>
                    <button type="submit" name="register_driver" class="btn btn-primary">
                        <i class="fas fa-user-plus"></i> Register Driver
                    </button>
                </form>
            </div>
            
            <div class="card">
                <div class="card-header">
                    <h2><i class="fas fa-list"></i> Driver List</h2>
                </div>
                <div class="table-container">
                    <table>
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Name</th>
                                <th>Contact</th>
                                <th>Email</th>
                                <th>License No.</th>
                                <th>License Expiry</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($drivers as $driver): ?>
                            <tr>
                                <td><?php echo $driver['id']; ?></td>
                                <td><?php echo $driver['driver_name']; ?></td>
                                <td><?php echo $driver['contact']; ?></td>
                                <td><?php echo $driver['email']; ?></td>
                                <td><?php echo $driver['license_number']; ?></td>
                                <td><?php echo $driver['license_expiry']; ?></td>
                                <td><span class="status-badge status-active">Active</span></td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        
        <!-- Rest of your existing tabs remain unchanged -->
        <!-- Vehicles Tab -->
        <div class="tab-content" id="vehicles">
            <div class="card">
                <h2><i class="fas fa-car"></i> Register New Vehicle</h2>
                <form method="POST">
                    <div class="form-row">
                        <div class="form-group">
                            <label>Vehicle Number</label>
                            <input type="text" class="form-control" name="vehicle_number" required>
                        </div>
                        <div class="form-group">
                            <label>Model</label>
                            <input type="text" class="form-control" name="model" required>
                        </div>
                        <div class="form-group">
                            <label>Year</label>
                            <input type="number" class="form-control" name="year" min="2000" max="2030">
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group">
                            <label>Color</label>
                            <input type="text" class="form-control" name="color">
                        </div>
                        <div class="form-group">
                            <label>Vehicle Type</label>
                            <select class="form-control" name="vehicle_type_id" required>
                                <option value="">Select Type</option>
                                <?php foreach ($vehicle_types as $type): ?>
                                <option value="<?php echo $type['id']; ?>"><?php echo $type['type_name']; ?> (Capacity: <?php echo $type['capacity']; ?>)</option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="form-group">
                            <label>Capacity</label>
                            <input type="number" class="form-control" name="capacity" required>
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group">
                            <label>Assigned Driver</label>
                            <select class="form-control" name="driver_id">
                                <option value="">Select Driver</option>
                                <?php foreach ($drivers as $driver): ?>
                                <option value="<?php echo $driver['id']; ?>"><?php echo $driver['driver_name']; ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="form-group">
                            <label>Route</label>
                            <select class="form-control" name="route_id">
                                <option value="">Select Route</option>
                                <?php foreach ($routes as $route): ?>
                                <option value="<?php echo $route['id']; ?>"><?php echo $route['route_name']; ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="form-group">
                            <label>Insurance Number</label>
                            <input type="text" class="form-control" name="insurance_number">
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group">
                            <label>Insurance Expiry</label>
                            <input type="date" class="form-control" name="insurance_expiry">
                        </div>
                    </div>
                    <button type="submit" name="register_vehicle" class="btn btn-primary">Register Vehicle</button>
                </form>
            </div>
            
            <div class="card">
                <h2><i class="fas fa-list"></i> Vehicle List</h2>
                <div class="table-container">
                    <table>
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Vehicle No.</th>
                                <th>Model</th>
                                <th>Type</th>
                                <th>Capacity</th>
                                <th>Driver</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($vehicles as $vehicle): ?>
                            <tr>
                                <td><?php echo $vehicle['id']; ?></td>
                                <td><?php echo $vehicle['vehicle_number']; ?></td>
                                <td><?php echo $vehicle['model']; ?></td>
                                <td><?php echo $vehicle['vehicle_type']; ?></td>
                                <td><?php echo $vehicle['capacity']; ?></td>
                                <td><?php echo $vehicle['driver_name'] ?? 'Not assigned'; ?></td>
                                <td><span class="status-badge status-active">Active</span></td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <!-- Schedules Tab -->
        <div class="tab-content" id="schedules">
            <div class="card">
                <h2><i class="fas fa-calendar-alt"></i> Create Transport Schedule</h2>
                <form method="POST">
                    <div class="form-row">
                        <div class="form-group">
                            <label>Vehicle</label>
                            <select class="form-control" name="vehicle_id" required>
                                <option value="">Select Vehicle</option>
                                <?php foreach ($vehicles as $vehicle): ?>
                                <option value="<?php echo $vehicle['id']; ?>"><?php echo $vehicle['vehicle_number']; ?> - <?php echo $vehicle['model']; ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="form-group">
                            <label>Route</label>
                            <select class="form-control" name="route_id" required>
                                <option value="">Select Route</option>
                                <?php foreach ($routes as $route): ?>
                                <option value="<?php echo $route['id']; ?>"><?php echo $route['route_name']; ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="form-group">
                            <label>Driver</label>
                            <select class="form-control" name="driver_id" required>
                                <option value="">Select Driver</option>
                                <?php foreach ($drivers as $driver): ?>
                                <option value="<?php echo $driver['id']; ?>"><?php echo $driver['driver_name']; ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group">
                            <label>Schedule Type</label>
                            <select class="form-control" name="schedule_type" required>
                                <option value="morning">Morning Only</option>
                                <option value="evening">Evening Only</option>
                                <option value="both">Both Morning & Evening</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label>Departure Time</label>
                            <input type="time" class="form-control" name="departure_time" required>
                        </div>
                        <div class="form-group">
                            <label>Arrival Time</label>
                            <input type="time" class="form-control" name="arrival_time" required>
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group">
                            <label>Day of Week</label>
                            <select class="form-control" name="day_of_week" required>
                                <option value="Monday">Monday</option>
                                <option value="Tuesday">Tuesday</option>
                                <option value="Wednesday">Wednesday</option>
                                <option value="Thursday">Thursday</option>
                                <option value="Friday">Friday</option>
                                <option value="Saturday">Saturday</option>
                                <option value="Sunday">Sunday</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label>Start Date</label>
                            <input type="date" class="form-control" name="start_date" required>
                        </div>
                        <div class="form-group">
                            <label>End Date (Optional)</label>
                            <input type="date" class="form-control" name="end_date">
                        </div>
                    </div>
                    <button type="submit" name="create_schedule" class="btn btn-primary">Create Schedule</button>
                </form>
            </div>
            
            <div class="card">
                <h2><i class="fas fa-list"></i> Schedule List</h2>
                <div class="table-container">
                    <table>
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Vehicle</th>
                                <th>Route</th>
                                <th>Driver</th>
                                <th>Schedule</th>
                                <th>Time</th>
                                <th>Day</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($schedules as $schedule): ?>
                            <tr>
                                <td><?php echo $schedule['id']; ?></td>
                                <td><?php echo $schedule['vehicle_number']; ?></td>
                                <td><?php echo $schedule['route_name']; ?></td>
                                <td><?php echo $schedule['driver_name']; ?></td>
                                <td><?php echo ucfirst($schedule['schedule_type']); ?></td>
                                <td><?php echo $schedule['departure_time'] . ' - ' . $schedule['arrival_time']; ?></td>
                                <td><?php echo $schedule['day_of_week']; ?></td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        
        <!-- Student Assignments Tab -->
        <div class="tab-content" id="assignments">
            <div class="card">
                <h2><i class="fas fa-user-graduate"></i> Assign Student to Transport</h2>
                <form method="POST">
                    <div class="form-row">
                        <div class="form-group">
                            <label>Student</label>
                            <select class="form-control" name="student_id" required>
                                <option value="">Select Student</option>
                                <?php foreach ($students as $student): ?>
                                <option value="<?php echo $student['id']; ?>"><?php echo $student['first_name'] . ' ' . $student['last_name']; ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="form-group">
                            <label>Transport Schedule</label>
                            <select class="form-control" name="schedule_id" required>
                                <option value="">Select Schedule</option>
                                <?php foreach ($schedules as $schedule): ?>
                                <option value="<?php echo $schedule['id']; ?>">
                                    <?php echo $schedule['vehicle_number'] . ' - ' . $schedule['route_name'] . ' (' . $schedule['departure_time'] . ')'; ?>
                                </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="form-group">
                            <label>Transport Fee</label>
                            <input type="number" class="form-control" name="fee_amount" step="0.01" required>
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group">
                            <label>Pickup Point</label>
                            <input type="text" class="form-control" name="pickup_point" required>
                        </div>
                        <div class="form-group">
                            <label>Dropoff Point</label>
                            <input type="text" class="form-control" name="dropoff_point" required>
                        </div>
                        <div class="form-group">
                            <label>Start Date</label>
                            <input type="date" class="form-control" name="start_date" required>
                        </div>
                    </div>
                    <button type="submit" name="assign_student_transport" class="btn btn-success">Assign Student</button>
                </form>
            </div>
            
            <div class="card">
                <h2><i class="fas fa-list"></i> Student Transport Assignments</h2>
                <div class="table-container">
                    <table>
                        <thead>
                            <tr>
                                <th>Student</th>
                                <th>Vehicle</th>
                                <th>Route</th>
                                <th>Driver</th>
                                <th>Pickup Point</th>
                                <th>Fee</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($student_transports as $assignment): ?>
                            <tr>
                                <td><?php echo $assignment['first_name'] . ' ' . $assignment['last_name']; ?></td>
                                <td><?php echo $assignment['vehicle_number']; ?></td>
                                <td><?php echo $assignment['route_name']; ?></td>
                                <td><?php echo $assignment['driver_name']; ?></td>
                                <td><?php echo $assignment['pickup_point']; ?></td>
                                <td>$<?php echo $assignment['fee_amount']; ?></td>
                                <td><span class="status-badge status-active">Active</span></td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        
        <!-- Bookings Tab -->
        <div class="tab-content" id="bookings">
            <div class="card">
                <h2><i class="fas fa-calendar-check"></i> Create Transport Booking</h2>
                <form method="POST">
                    <div class="form-row">
                        <div class="form-group">
                            <label>Vehicle</label>
                            <select class="form-control" name="vehicle_id" required>
                                <option value="">Select Vehicle</option>
                                <?php foreach ($vehicles as $vehicle): ?>
                                <option value="<?php echo $vehicle['id']; ?>"><?php echo $vehicle['vehicle_number']; ?> - <?php echo $vehicle['model']; ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="form-group">
                            <label>Driver</label>
                            <select class="form-control" name="driver_id" required>
                                <option value="">Select Driver</option>
                                <?php foreach ($drivers as $driver): ?>
                                <option value="<?php echo $driver['id']; ?>"><?php echo $driver['driver_name']; ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="form-group">
                            <label>Booking Date</label>
                            <input type="date" class="form-control" name="booking_date" required>
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group">
                            <label>Start Time</label>
                            <input type="time" class="form-control" name="start_time" required>
                        </div>
                        <div class="form-group">
                            <label>End Time</label>
                            <input type="time" class="form-control" name="end_time" required>
                        </div>
                        <div class="form-group">
                            <label>Passenger Count</label>
                            <input type="number" class="form-control" name="passenger_count" min="1" required>
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group">
                            <label>Pickup Location</label>
                            <input type="text" class="form-control" name="pickup_location" required>
                        </div>
                        <div class="form-group">
                            <label>Destination</label>
                            <input type="text" class="form-control" name="destination" required>
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group full-width">
                            <label>Purpose</label>
                            <input type="text" class="form-control" name="purpose" required>
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group full-width">
                            <label>Remarks</label>
                            <textarea class="form-control" name="remarks" rows="2"></textarea>
                        </div>
                    </div>
                    <button type="submit" name="create_booking" class="btn btn-primary">Create Booking</button>
                </form>
            </div>
            
            <div class="card">
                <h2><i class="fas fa-list"></i> Booking Requests</h2>
                <div class="table-container">
                    <table>
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Vehicle</th>
                                <th>Driver</th>
                                <th>Date</th>
                                <th>Time</th>
                                <th>Purpose</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($bookings as $booking): ?>
                            <tr>
                                <td><?php echo $booking['id']; ?></td>
                                <td><?php echo $booking['vehicle_number']; ?></td>
                                <td><?php echo $booking['driver_name']; ?></td>
                                <td><?php echo $booking['booking_date']; ?></td>
                                <td><?php echo $booking['start_time'] . ' - ' . $booking['end_time']; ?></td>
                                <td><?php echo $booking['purpose']; ?></td>
                                <td>
                                    <span class="status-badge status-<?php echo $booking['status']; ?>">
                                        <?php echo ucfirst($booking['status']); ?>
                                    </span>
                                </td>
                                <td>
                                    <?php if ($booking['status'] == 'pending'): ?>
                                    <form method="POST" style="display: inline;">
                                        <input type="hidden" name="booking_id" value="<?php echo $booking['id']; ?>">
                                        <input type="hidden" name="status" value="approved">
                                        <button type="submit" name="update_booking_status" class="btn btn-success btn-sm">Approve</button>
                                    </form>
                                    <form method="POST" style="display: inline;">
                                        <input type="hidden" name="booking_id" value="<?php echo $booking['id']; ?>">
                                        <input type="hidden" name="status" value="rejected">
                                        <button type="submit" name="update_booking_status" class="btn btn-danger btn-sm">Reject</button>
                                    </form>
                                    <?php endif; ?>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        
        <!-- Routes Tab -->
        <div class="tab-content" id="routes">
              <div class="card">
                <h2><i class="fas fa-route"></i> Transport Routes</h2>
                <div class="table-container">
                    <table>
                        <thead>
                            <tr>
                                <th>Route Name</th>
                                <th>Start Point</th>
                                <th>End Point</th>
                                <th>Distance</th>
                                <th>Estimated Time</th>
                                <th>Description</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($routes as $route): ?>
                            <tr>
                                <td><?php echo $route['route_name']; ?></td>
                                <td><?php echo $route['start_point']; ?></td>
                                <td><?php echo $route['end_point']; ?></td>
                                <td><?php echo $route['distance_km']; ?> km</td>
                                <td><?php echo $route['estimated_time_minutes']; ?> min</td>
                                <td><?php echo $route['description']; ?></td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
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

        function showTab(tabName) {
            // Hide all tab contents
            var tabContents = document.getElementsByClassName('tab-content');
            for (var i = 0; i < tabContents.length; i++) {
                tabContents[i].classList.remove('active');
            }
            
            // Show the selected tab content
            document.getElementById(tabName).classList.add('active');
            
            // Update active tab
            var tabs = document.getElementsByClassName('tab');
            for (var i = 0; i < tabs.length; i++) {
                tabs[i].classList.remove('active');
            }
            
            // Find and activate the clicked tab
            var tabButtons = document.querySelectorAll('.tab');
            tabButtons.forEach(function(tab) {
                if (tab.textContent.toLowerCase().includes(tabName)) {
                    tab.classList.add('active');
                }
            });
        }
        
        // Set today's date as default for date fields
        document.addEventListener('DOMContentLoaded', function() {
            var today = new Date().toISOString().split('T')[0];
            var dateFields = document.querySelectorAll('input[type="date"]');
            dateFields.forEach(function(field) {
                if (!field.value) {
                    field.value = today;
                }
            });
        });
    </script>
</body>
</html>