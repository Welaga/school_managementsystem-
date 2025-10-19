<?php
require_once '../config.php';
checkRole(['admin', 'registrar']);

// Initialize variables
$error = '';
$success = '';

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['register_cleaner'])) {
        // Register cleaner logic
        $username = trim($_POST['username']);
        $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
        $assigned_area = trim($_POST['assigned_area']);
        $contact = trim($_POST['contact']);
        $email = trim($_POST['email']);
        $schedule = trim($_POST['schedule']);
        
        try {
            // Check if username already exists
            $stmt = $pdo->prepare("SELECT id FROM users WHERE username = ?");
            $stmt->execute([$username]);
            if ($stmt->fetch()) {
                $error = "Username already exists!";
            } else {
                // First create user account
                $stmt = $pdo->prepare("INSERT INTO users (username, password, role, email, created_at) 
                                      VALUES (?, ?, 'cleaner', ?, NOW())");
                $stmt->execute([$username, $password, $email]);
                
                $user_id = $pdo->lastInsertId();
                
                // Then create cleaner profile
                $stmt = $pdo->prepare("INSERT INTO cleaners (user_id, assigned_area, contact, schedule, created_at) 
                                      VALUES (?, ?, ?, ?, NOW())");
                $stmt->execute([$user_id, $assigned_area, $contact, $schedule]);
                
                $cleaner_id = $pdo->lastInsertId();
                $success = "Cleaner registered successfully! Cleaner ID: " . $cleaner_id;
            }
        } catch (PDOException $e) {
            $error = "Error registering cleaner: " . $e->getMessage();
        }
    }
    elseif (isset($_POST['update_cleaner'])) {
        // Update cleaner logic
        $cleaner_id = $_POST['cleaner_id'];
        $assigned_area = trim($_POST['assigned_area']);
        $contact = trim($_POST['contact']);
        $email = trim($_POST['email']);
        $schedule = trim($_POST['schedule']);
        
        try {
            // Update cleaner table
            $stmt = $pdo->prepare("UPDATE cleaners SET assigned_area = ?, contact = ?, schedule = ?, updated_at = NOW() WHERE id = ?");
            $stmt->execute([$assigned_area, $contact, $schedule, $cleaner_id]);
            
            // Also update user email
            $stmt = $pdo->prepare("UPDATE users u JOIN cleaners c ON u.id = c.user_id SET u.email = ? WHERE c.id = ?");
            $stmt->execute([$email, $cleaner_id]);
            
            $success = "Cleaner updated successfully!";
        } catch (PDOException $e) {
            $error = "Error updating cleaner: " . $e->getMessage();
        }
    }
    elseif (isset($_POST['delete_cleaner'])) {
        // Delete cleaner logic
        $cleaner_id = $_POST['cleaner_id'];
        
        try {
            // Get user_id first
            $stmt = $pdo->prepare("SELECT user_id FROM cleaners WHERE id = ?");
            $stmt->execute([$cleaner_id]);
            $cleaner = $stmt->fetch();
            
            if ($cleaner) {
                // Delete cleaner record
                $stmt = $pdo->prepare("DELETE FROM cleaners WHERE id = ?");
                $stmt->execute([$cleaner_id]);
                
                // Also delete user account
                $stmt = $pdo->prepare("DELETE FROM users WHERE id = ?");
                $stmt->execute([$cleaner['user_id']]);
                
                $success = "Cleaner deleted successfully!";
            }
        } catch (PDOException $e) {
            $error = "Error deleting cleaner: " . $e->getMessage();
        }
    }
    elseif (isset($_POST['assign_duty'])) {
        // Assign cleaning duty logic
        $cleaner_id = $_POST['cleaner_id'];
        $duty_area = trim($_POST['duty_area']);
        $duty_date = $_POST['duty_date'];
        $duty_time = $_POST['duty_time'];
        $notes = trim($_POST['notes']);
        
        try {
            $stmt = $pdo->prepare("INSERT INTO cleaning_duties (cleaner_id, duty_area, duty_date, duty_time, notes, assigned_by, assigned_at, status) 
                                  VALUES (?, ?, ?, ?, ?, ?, NOW(), 'assigned')");
            $stmt->execute([$cleaner_id, $duty_area, $duty_date, $duty_time, $notes, $_SESSION['user_id']]);
            
            $success = "Cleaning duty assigned successfully!";
        } catch (PDOException $e) {
            $error = "Error assigning duty: " . $e->getMessage();
        }
    }
}

// Fetch cleaners data
try {
    $cleaners = $pdo->query("
        SELECT c.*, u.username, u.role, u.email as user_email, u.created_at as user_created 
        FROM cleaners c 
        JOIN users u ON c.user_id = u.id 
        ORDER BY c.created_at DESC
    ")->fetchAll(PDO::FETCH_ASSOC);
    
    // Get available areas for dropdown
    $areas = $pdo->query("SELECT DISTINCT assigned_area FROM cleaners WHERE assigned_area IS NOT NULL AND assigned_area != ''")->fetchAll(PDO::FETCH_ASSOC);
    
    // Fetch cleaning duties
    $duties = $pdo->query("
        SELECT cd.*, c.assigned_area, u.username as cleaner_name 
        FROM cleaning_duties cd 
        JOIN cleaners c ON cd.cleaner_id = c.id 
        JOIN users u ON c.user_id = u.id 
        ORDER BY cd.duty_date DESC, cd.duty_time DESC
    ")->fetchAll(PDO::FETCH_ASSOC);
    
} catch (PDOException $e) {
    $error = "Error fetching data: " . $e->getMessage();
    $cleaners = [];
    $areas = [];
    $duties = [];
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cleaner Management - School Management System</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        .cleaner-management {
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
        }
        
        .tabs {
            display: flex;
            background-color: #fff;
            border-radius: 10px;
            overflow: hidden;
            margin-bottom: 30px;
            flex-wrap: wrap;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }
        
        .tab {
            padding: 15px 20px;
            cursor: pointer;
            font-weight: 600;
            text-align: center;
            flex: 1;
            min-width: 150px;
            transition: all 0.3s ease;
            border-bottom: 3px solid transparent;
            color: #555;
        }
        
        .tab.active {
            border-bottom: 3px solid #667eea;
            color: #667eea;
            background-color: #f8f9fa;
        }
        
        .tab-content {
            display: none;
            animation: fadeIn 0.5s ease-in;
        }
        
        .tab-content.active {
            display: block;
        }
        
        .card {
            background-color: #fff;
            border-radius: 10px;
            padding: 25px;
            margin-bottom: 25px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }
        
        .card-header {
            border-bottom: 1px solid #eee;
            padding-bottom: 15px;
            margin-bottom: 20px;
        }
        
        .card-header h2 {
            margin: 0;
            color: #2c3e50;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        
        .form-row {
            display: flex;
            flex-wrap: wrap;
            margin: 0 -10px;
        }
        
        .form-group {
            flex: 1 0 calc(50% - 20px);
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
            transition: border-color 0.3s;
        }
        
        .form-control:focus {
            border-color: #667eea;
            outline: none;
        }
        
        .btn {
            padding: 12px 25px;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            font-weight: 600;
            transition: all 0.3s ease;
            font-size: 1rem;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            text-decoration: none;
        }
        
        .btn-primary {
            background: #667eea;
            color: white;
        }
        
        .btn-primary:hover {
            background: #5a6fd8;
        }
        
        .btn-success {
            background: #4CAF50;
            color: white;
        }
        
        .btn-warning {
            background: #ff9800;
            color: white;
        }
        
        .btn-danger {
            background: #f44336;
            color: white;
        }
        
        .btn-sm {
            padding: 8px 15px;
            font-size: 0.9rem;
        }
        
        .btn-secondary {
            background: #6c757d;
            color: white;
        }
        
        .btn-info {
            background: #17a2b8;
            color: white;
        }
        
        .table-container {
            overflow-x: auto;
            margin-top: 20px;
            border-radius: 8px;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
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
        }
        
        tr:hover {
            background-color: #f8f9fa;
        }
        
        .status-badge {
            padding: 4px 8px;
            border-radius: 12px;
            font-size: 0.8rem;
            font-weight: 600;
        }
        
        .status-assigned {
            background-color: #d4edda;
            color: #155724;
        }
        
        .status-completed {
            background-color: #d1ecf1;
            color: #0c5460;
        }
        
        .status-pending {
            background-color: #fff3cd;
            color: #856404;
        }
        
        .action-buttons {
            display: flex;
            gap: 8px;
            flex-wrap: wrap;
        }
        
        .search-filter {
            display: flex;
            gap: 15px;
            margin-bottom: 20px;
            flex-wrap: wrap;
        }
        
        .search-box {
            flex: 1;
            min-width: 300px;
        }
        
        .filter-select {
            min-width: 200px;
        }
        
        .stats-overview {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }
        
        .stat-card {
            background: white;
            border-radius: 10px;
            padding: 20px;
            text-align: center;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            border-left: 4px solid #667eea;
        }
        
        .stat-card h3 {
            margin: 0 0 10px 0;
            font-size: 0.9rem;
            color: #666;
        }
        
        .stat-card p {
            margin: 0;
            font-size: 2rem;
            font-weight: 700;
            color: #2c3e50;
        }
        
        .modal {
            display: none;
            position: fixed;
            z-index: 1000;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0,0,0,0.5);
        }
        
        .modal-content {
            background-color: white;
            margin: 5% auto;
            padding: 0;
            border-radius: 10px;
            width: 90%;
            max-width: 600px;
            animation: slideIn 0.3s ease;
        }
        
        .modal-header {
            padding: 20px;
            border-bottom: 1px solid #eee;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        .modal-body {
            padding: 20px;
        }
        
        .close {
            font-size: 24px;
            cursor: pointer;
            color: #666;
        }
        
        .alert {
            padding: 15px;
            margin-bottom: 20px;
            border-radius: 6px;
            font-weight: 500;
        }
        
        .alert-success {
            background-color: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }
        
        .alert-error {
            background-color: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }
        
        @keyframes fadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
        }
        
        @keyframes slideIn {
            from { transform: translateY(-50px); opacity: 0; }
            to { transform: translateY(0); opacity: 1; }
        }
        
        @media (max-width: 768px) {
            .form-group {
                flex: 1 0 calc(100% - 20px);
            }
            
            .tab {
                flex: 1 0 100%;
            }
            
            .action-buttons {
                flex-direction: column;
            }
            
            .search-filter {
                flex-direction: column;
            }
            
            .search-box, .filter-select {
                min-width: 100%;
            }
        }
    </style>
</head>
<body>
    <?php include("../includes/header.php"); ?>
    <?php include("../includes/sidebar.php"); ?>

    <main class="main-content">
        <div class="content-header">
            <h1><i class="fas fa-broom"></i> Cleaner Management</h1>
            <p>Manage cleaner accounts and assignments</p>
        </div>
        
        <?php if ($success): ?>
            <div class="alert alert-success"><?php echo $success; ?></div>
        <?php endif; ?>
        
        <?php if ($error): ?>
            <div class="alert alert-error"><?php echo $error; ?></div>
        <?php endif; ?>
        
        <div class="cleaner-management">
            <div class="stats-overview">
                <div class="stat-card">
                    <h3>Total Cleaners</h3>
                    <p><?php echo count($cleaners); ?></p>
                </div>
                <div class="stat-card">
                    <h3>Assigned Areas</h3>
                    <p><?php echo count($areas); ?></p>
                </div>
                <div class="stat-card">
                    <h3>Active Duties</h3>
                    <p><?php echo count(array_filter($duties, function($duty) { return $duty['status'] === 'assigned'; })); ?></p>
                </div>
                <div class="stat-card">
                    <h3>Completed Duties</h3>
                    <p><?php echo count(array_filter($duties, function($duty) { return $duty['status'] === 'completed'; })); ?></p>
                </div>
            </div>
            
            <div class="tabs">
                <div class="tab active" data-tab="cleaners">
                    <i class="fas fa-user-plus"></i> Register Cleaner
                </div>
                <div class="tab" data-tab="manage-cleaners">
                    <i class="fas fa-list"></i> Manage Cleaners
                </div>
                <div class="tab" data-tab="assignments">
                    <i class="fas fa-tasks"></i>Assignments Status 
                </div>
                <div class="tab" data-tab="duties">
                    <i class="fas fa-clipboard-list"></i> Cleaning Duties
                </div>
            </div>
            
            <!-- Register Cleaner Form -->
            <div id="cleaners" class="tab-content active">
                <div class="card">
                    <div class="card-header">
                        <h2><i class="fas fa-user-plus"></i> Register New Cleaner</h2>
                    </div>
                    <form method="POST">
                        <div class="form-row">
                            <div class="form-group">
                                <label for="username">Username *</label>
                                <input type="text" class="form-control" id="username" name="username" required>
                            </div>
                            <div class="form-group">
                                <label for="password">Password *</label>
                                <input type="password" class="form-control" id="password" name="password" required>
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="form-group">
                                <label for="assigned_area">Assigned Area *</label>
                                <select class="form-control" id="assigned_area" name="assigned_area" required>
                                    <option value="">Select Area</option>
                                    <option value="Main Building">Main Building</option>
                                    <option value="Classroom Wing A">Classroom Wing A</option>
                                    <option value="Classroom Wing B">Classroom Wing B</option>
                                    <option value="Administration Block">Administration Block</option>
                                    <option value="Library">Library</option>
                                    <option value="Cafeteria">Cafeteria</option>
                                    <option value="Sports Complex">Sports Complex</option>
                                    <option value="Outdoor Areas">Outdoor Areas</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="contact">Contact Number *</label>
                                <input type="text" class="form-control" id="contact" name="contact" required>
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="form-group">
                                <label for="email">Email Address</label>
                                <input type="email" class="form-control" id="email" name="email">
                            </div>
                            <div class="form-group">
                                <label for="schedule">Work Schedule *</label>
                                <select class="form-control" id="schedule" name="schedule" required>
                                    <option value="">Select Schedule</option>
                                    <option value="Morning Shift (6AM-2PM)">Morning Shift (6AM-2PM)</option>
                                    <option value="Afternoon Shift (2PM-10PM)">Afternoon Shift (2PM-10PM)</option>
                                    <option value="Evening Shift (4PM-12AM)">Evening Shift (4PM-12AM)</option>
                                    <option value="Full Day (8AM-5PM)">Full Day (8AM-5PM)</option>
                                </select>
                            </div>
                        </div>
                        <button type="submit" name="register_cleaner" class="btn btn-primary">
                            <i class="fas fa-save"></i> Register Cleaner
                        </button>
                    </form>
                </div>
            </div>

            <!-- Manage Cleaners -->
            <div id="manage-cleaners" class="tab-content">
                <div class="card">
                    <div class="card-header">
                        <h2><i class="fas fa-list"></i> Manage Cleaners</h2>
                    </div>
                    
                    <div class="search-filter">
                        <div class="search-box">
                            <input type="text" class="form-control" id="searchInput" placeholder="Search cleaners...">
                        </div>
                        <div class="filter-select">
                            <select class="form-control" id="areaFilter">
                                <option value="">All Areas</option>
                                <?php foreach ($areas as $area): ?>
                                    <option value="<?php echo $area['assigned_area']; ?>"><?php echo $area['assigned_area']; ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                    
                    <div class="table-container">
                        <table class="table" id="cleanersTable">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Username</th>
                                    <th>Assigned Area</th>
                                    <th>Contact</th>
                                    <th>Email</th>
                                    <th>Schedule</th>
                                    <th>Joined Date</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (empty($cleaners)): ?>
                                    <tr>
                                        <td colspan="8" style="text-align: center; padding: 40px;">
                                            <i class="fas fa-info-circle" style="font-size: 3rem; color: #6c757d; margin-bottom: 15px;"></i>
                                            <p>No cleaners found. Register a new cleaner to get started.</p>
                                        </td>
                                    </tr>
                                <?php else: ?>
                                    <?php foreach ($cleaners as $cleaner): ?>
                                    <tr>
                                        <td><?php echo $cleaner['id']; ?></td>
                                        <td><?php echo htmlspecialchars($cleaner['username']); ?></td>
                                        <td><?php echo htmlspecialchars($cleaner['assigned_area']); ?></td>
                                        <td><?php echo htmlspecialchars($cleaner['contact']); ?></td>
                                        <td><?php echo htmlspecialchars($cleaner['user_email']); ?></td>
                                        <td><?php echo htmlspecialchars($cleaner['schedule']); ?></td>
                                        <td><?php echo date('M j, Y', strtotime($cleaner['user_created'])); ?></td>
                                        <td>
                                            <div class="action-buttons">
                                                <button type="button" class="btn btn-warning btn-sm" onclick="editCleaner(<?php echo $cleaner['id']; ?>)">
                                                    <i class="fas fa-edit"></i> Edit
                                                </button>
                                                <button type="button" class="btn btn-info btn-sm" onclick="assignDuty(<?php echo $cleaner['id']; ?>, '<?php echo htmlspecialchars($cleaner['username']); ?>')">
                                                    <i class="fas fa-tasks"></i> Assign Duty
                                                </button>
                                                <form method="POST" style="display: inline;" onsubmit="return confirm('Are you sure you want to delete this cleaner?')">
                                                    <input type="hidden" name="cleaner_id" value="<?php echo $cleaner['id']; ?>">
                                                    <button type="submit" name="delete_cleaner" class="btn btn-danger btn-sm">
                                                        <i class="fas fa-trash"></i> Delete
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            
            <!-- Area Assignments -->
            <div id="assignments" class="tab-content">
                <div class="card">
                    <div class="card-header">
                        <h2><i class="fas fa-tasks"></i> Area Assignments Overview</h2>
                    </div>
                    <div class="table-container">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>Area</th>
                                    <th>Assigned Cleaner</th>
                                    <th>Schedule</th>
                                    <th>Contact</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php 
                                $default_areas = ['Main Building', 'Classroom Wing A', 'Classroom Wing B', 'Administration Block', 'Library', 'Cafeteria', 'Sports Complex', 'Outdoor Areas'];
                                
                                foreach ($default_areas as $area): 
                                    $assigned_cleaner = null;
                                    foreach ($cleaners as $cleaner) {
                                        if ($cleaner['assigned_area'] === $area) {
                                            $assigned_cleaner = $cleaner;
                                            break;
                                        }
                                    }
                                ?>
                                <tr>
                                    <td><strong><?php echo $area; ?></strong></td>
                                    <td>
                                        <?php if ($assigned_cleaner): ?>
                                            <?php echo htmlspecialchars($assigned_cleaner['username']); ?>
                                        <?php else: ?>
                                            <span style="color: #f44336; font-style: italic;">Not assigned</span>
                                        <?php endif; ?>
                                    </td>
                                    <td><?php echo $assigned_cleaner ? htmlspecialchars($assigned_cleaner['schedule']) : '-'; ?></td>
                                    <td><?php echo $assigned_cleaner ? htmlspecialchars($assigned_cleaner['contact']) : '-'; ?></td>
                                    <td>
                                        <?php if ($assigned_cleaner): ?>
                                            <span class="status-badge status-assigned">Assigned</span>
                                        <?php else: ?>
                                            <span style="color: #f44336; font-style: italic;">Unassigned</span>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            
            <!-- Cleaning Duties -->
            <div id="duties" class="tab-content">
                <div class="card">
                    <div class="card-header">
                        <h2><i class="fas fa-clipboard-list"></i> Cleaning Duties</h2>
                    </div>
                    <div class="table-container">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>Duty ID</th>
                                    <th>Cleaner</th>
                                    <th>Area</th>
                                    <th>Date</th>
                                    <th>Time</th>
                                    <th>Status</th>
                                    <th>Assigned At</th>
                                    <th>Notes</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (empty($duties)): ?>
                                    <tr>
                                        <td colspan="8" style="text-align: center; padding: 40px;">
                                            <i class="fas fa-info-circle" style="font-size: 3rem; color: #6c757d; margin-bottom: 15px;"></i>
                                            <p>No cleaning duties assigned yet.</p>
                                        </td>
                                    </tr>
                                <?php else: ?>
                                    <?php foreach ($duties as $duty): ?>
                                    <tr>
                                        <td><?php echo $duty['id']; ?></td>
                                        <td><?php echo htmlspecialchars($duty['cleaner_name']); ?></td>
                                        <td><?php echo htmlspecialchars($duty['duty_area']); ?></td>
                                        <td><?php echo date('M j, Y', strtotime($duty['duty_date'])); ?></td>
                                        <td><?php echo htmlspecialchars($duty['duty_time']); ?></td>
                                        <td>
                                            <?php 
                                            $status_class = '';
                                            switch($duty['status']) {
                                                case 'assigned': $status_class = 'status-assigned'; break;
                                                case 'completed': $status_class = 'status-completed'; break;
                                                case 'pending': $status_class = 'status-pending'; break;
                                            }
                                            ?>
                                            <span class="status-badge <?php echo $status_class; ?>"><?php echo ucfirst($duty['status']); ?></span>
                                        </td>
                                        <td><?php echo date('M j, Y g:i A', strtotime($duty['assigned_at'])); ?></td>
                                        <td><?php echo htmlspecialchars($duty['notes'] ?: '-'); ?></td>
                                    </tr>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </main>
    
    <!-- Edit Cleaner Modal -->
    <div id="editModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h2><i class="fas fa-edit"></i> Edit Cleaner</h2>
                <span class="close" onclick="closeModal('editModal')">&times;</span>
            </div>
            <div class="modal-body">
                <form method="POST" id="editForm">
                    <input type="hidden" name="cleaner_id" id="edit_cleaner_id">
                    <div class="form-row">
                        <div class="form-group">
                            <label>Assigned Area *</label>
                            <select class="form-control" name="assigned_area" id="edit_area" required>
                                <option value="">Select Area</option>
                                <option value="Main Building">Main Building</option>
                                <option value="Classroom Wing A">Classroom Wing A</option>
                                <option value="Classroom Wing B">Classroom Wing B</option>
                                <option value="Administration Block">Administration Block</option>
                                <option value="Library">Library</option>
                                <option value="Cafeteria">Cafeteria</option>
                                <option value="Sports Complex">Sports Complex</option>
                                <option value="Outdoor Areas">Outdoor Areas</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label>Contact Number *</label>
                            <input type="text" class="form-control" name="contact" id="edit_contact" required>
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group">
                            <label>Email Address</label>
                            <input type="email" class="form-control" name="email" id="edit_email">
                        </div>
                        <div class="form-group">
                            <label>Work Schedule *</label>
                            <select class="form-control" name="schedule" id="edit_schedule" required>
                                <option value="">Select Schedule</option>
                                <option value="Morning Shift (6AM-2PM)">Morning Shift (6AM-2PM)</option>
                                <option value="Afternoon Shift (2PM-10PM)">Afternoon Shift (2PM-10PM)</option>
                                <option value="Evening Shift (4PM-12AM)">Evening Shift (4PM-12AM)</option>
                                <option value="Full Day (8AM-5PM)">Full Day (8AM-5PM)</option>
                            </select>
                        </div>
                    </div>
                    <div class="form-row">
                        <button type="submit" name="update_cleaner" class="btn btn-primary">
                            <i class="fas fa-save"></i> Update Cleaner
                        </button>
                        <button type="button" class="btn btn-secondary" onclick="closeModal('editModal')">
                            <i class="fas fa-times"></i> Cancel
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    
    <!-- Assign Duty Modal -->
    <div id="dutyModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h2><i class="fas fa-tasks"></i> Assign Cleaning Duty</h2>
                <span class="close" onclick="closeModal('dutyModal')">&times;</span>
            </div>
            <div class="modal-body">
                <form method="POST" id="dutyForm">
                    <input type="hidden" name="cleaner_id" id="duty_cleaner_id">
                    <div class="form-row">
                        <div class="form-group">
                            <label>Cleaner</label>
                            <input type="text" class="form-control" id="duty_cleaner_name" readonly>
                        </div>
                        <div class="form-group">
                            <label>Duty Area *</label>
                            <select class="form-control" name="duty_area" required>
                                <option value="">Select Area</option>
                                <option value="Main Building">Main Building</option>
                                <option value="Classroom Wing A">Classroom Wing A</option>
                                <option value="Classroom Wing B">Classroom Wing B</option>
                                <option value="Administration Block">Administration Block</option>
                                <option value="Library">Library</option>
                                <option value="Cafeteria">Cafeteria</option>
                                <option value="Sports Complex">Sports Complex</option>
                                <option value="Outdoor Areas">Outdoor Areas</option>
                            </select>
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group">
                            <label>Duty Date *</label>
                            <input type="date" class="form-control" name="duty_date" required min="<?php echo date('Y-m-d'); ?>">
                        </div>
                        <div class="form-group">
                            <label>Duty Time *</label>
                            <select class="form-control" name="duty_time" required>
                                <option value="">Select Time</option>
                                <option value="Morning (6:00 AM)">Morning (6:00 AM)</option>
                                <option value="Afternoon (2:00 PM)">Afternoon (2:00 PM)</option>
                                <option value="Evening (6:00 PM)">Evening (6:00 PM)</option>
                                <option value="Night (10:00 PM)">Night (10:00 PM)</option>
                            </select>
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group full-width">
                            <label>Notes</label>
                            <textarea class="form-control" name="notes" rows="3" placeholder="Additional instructions..."></textarea>
                        </div>
                    </div>
                    <div class="form-row">
                        <button type="submit" name="assign_duty" class="btn btn-primary">
                            <i class="fas fa-calendar-plus"></i> Assign Duty
                        </button>
                        <button type="button" class="btn btn-secondary" onclick="closeModal('dutyModal')">
                            <i class="fas fa-times"></i> Cancel
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    
    <?php include '../includes/footer.php'; ?>
    
    <script>
        // Tab switching functionality
        document.addEventListener('DOMContentLoaded', function() {
            // Add click event listeners to all tabs
            const tabs = document.querySelectorAll('.tab');
            tabs.forEach(tab => {
                tab.addEventListener('click', function() {
                    const tabName = this.getAttribute('data-tab');
                    showTab(tabName);
                });
            });
            
            // Initialize search and filter
            filterCleaners();
        });

        function showTab(tabName) {
            console.log('Switching to tab:', tabName);
            
            // Hide all tab contents
            const tabContents = document.querySelectorAll('.tab-content');
            tabContents.forEach(content => {
                content.classList.remove('active');
            });
            
            // Remove active class from all tabs
            const tabs = document.querySelectorAll('.tab');
            tabs.forEach(tab => {
                tab.classList.remove('active');
            });
            
            // Show the selected tab content
            const selectedTabContent = document.getElementById(tabName);
            if (selectedTabContent) {
                selectedTabContent.classList.add('active');
            }
            
            // Activate the clicked tab
            const selectedTab = document.querySelector(`.tab[data-tab="${tabName}"]`);
            if (selectedTab) {
                selectedTab.classList.add('active');
            }
        }
        
        // Search and filter functionality
        document.getElementById('searchInput').addEventListener('keyup', function() {
            filterCleaners();
        });
        
        document.getElementById('areaFilter').addEventListener('change', function() {
            filterCleaners();
        });
        
        function filterCleaners() {
            const search = document.getElementById('searchInput').value.toLowerCase();
            const area = document.getElementById('areaFilter').value;
            const rows = document.querySelectorAll('#cleanersTable tbody tr');
            
            rows.forEach(row => {
                const username = row.cells[1].textContent.toLowerCase();
                const assignedArea = row.cells[2].textContent;
                const contact = row.cells[3].textContent.toLowerCase();
                const email = row.cells[4].textContent.toLowerCase();
                
                const matchesSearch = username.includes(search) || 
                                    assignedArea.toLowerCase().includes(search) ||
                                    contact.includes(search) ||
                                    email.includes(search);
                const matchesArea = !area || assignedArea === area;
                
                if (matchesSearch && matchesArea) {
                    row.style.display = '';
                } else {
                    row.style.display = 'none';
                }
            });
        }
        
        // Modal functions
        function editCleaner(cleanerId) {
            // Find the row and populate the form
            const rows = document.querySelectorAll('#cleanersTable tbody tr');
            let foundRow = null;
            
            rows.forEach(row => {
                if (parseInt(row.cells[0].textContent) === cleanerId) {
                    foundRow = row;
                }
            });
            
            if (foundRow) {
                document.getElementById('edit_cleaner_id').value = cleanerId;
                document.getElementById('edit_area').value = foundRow.cells[2].textContent;
                document.getElementById('edit_contact').value = foundRow.cells[3].textContent;
                document.getElementById('edit_email').value = foundRow.cells[4].textContent;
                document.getElementById('edit_schedule').value = foundRow.cells[5].textContent;
                
                document.getElementById('editModal').style.display = 'block';
            }
        }
        
        function assignDuty(cleanerId, cleanerName) {
            document.getElementById('duty_cleaner_id').value = cleanerId;
            document.getElementById('duty_cleaner_name').value = cleanerName;
            document.getElementById('dutyModal').style.display = 'block';
        }
        
        function closeModal(modalId) {
            document.getElementById(modalId).style.display = 'none';
        }
        
        // Close modal when clicking outside
        window.onclick = function(event) {
            const modals = document.querySelectorAll('.modal');
            modals.forEach(modal => {
                if (event.target === modal) {
                    modal.style.display = 'none';
                }
            });
        }
    </script>
</body>
</html>