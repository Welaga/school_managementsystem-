<?php
require_once '../config.php';
checkRole(['admin', 'transport']);

$page_title = "Manage Drivers";
$message = '';

// Handle form actions
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['add_driver'])) {
        $driver_name = trim($_POST['driver_name']);
        $contact = trim($_POST['contact']);
        $license_number = trim($_POST['license_number']);
        $license_expiry = $_POST['license_expiry'];
        
        try {
            $stmt = $pdo->prepare("INSERT INTO drivers (driver_name, contact, license_number, license_expiry) 
                                  VALUES (?, ?, ?, ?)");
            $stmt->execute([$driver_name, $contact, $license_number, $license_expiry]);
            $message = "Driver added successfully!";
        } catch (PDOException $e) {
            $message = "Error adding driver: " . $e->getMessage();
        }
    } elseif (isset($_POST['update_driver'])) {
        $driver_id = $_POST['driver_id'];
        $driver_name = trim($_POST['driver_name']);
        $contact = trim($_POST['contact']);
        $license_number = trim($_POST['license_number']);
        $license_expiry = $_POST['license_expiry'];
        
        try {
            $stmt = $pdo->prepare("UPDATE drivers SET driver_name = ?, contact = ?, license_number = ?, license_expiry = ? 
                                  WHERE id = ?");
            $stmt->execute([$driver_name, $contact, $license_number, $license_expiry, $driver_id]);
            $message = "Driver updated successfully!";
        } catch (PDOException $e) {
            $message = "Error updating driver: " . $e->getMessage();
        }
    } elseif (isset($_POST['delete_driver'])) {
        $driver_id = $_POST['driver_id'];
        
        try {
            // Check if driver is assigned to any vehicle
            $stmt = $pdo->prepare("SELECT COUNT(*) as count FROM vehicles WHERE driver_id = ?");
            $stmt->execute([$driver_id]);
            $vehicle_count = $stmt->fetch()['count'];
            
            if ($vehicle_count > 0) {
                $message = "Cannot delete driver. Driver is assigned to " . $vehicle_count . " vehicle(s).";
            } else {
                $stmt = $pdo->prepare("DELETE FROM drivers WHERE id = ?");
                $stmt->execute([$driver_id]);
                $message = "Driver deleted successfully!";
            }
        } catch (PDOException $e) {
            $message = "Error deleting driver: " . $e->getMessage();
        }
    }
}

// Get all drivers
$stmt = $pdo->query("SELECT d.*, 
                    (SELECT COUNT(*) FROM vehicles v WHERE v.driver_id = d.id) as vehicle_count 
                    FROM drivers d ORDER BY driver_name");
$drivers = $stmt->fetchAll();

// Get driver for editing
$edit_driver = null;
if (isset($_GET['edit'])) {
    $stmt = $pdo->prepare("SELECT * FROM drivers WHERE id = ?");
    $stmt->execute([$_GET['edit']]);
    $edit_driver = $stmt->fetch();
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
        <h1>Manage Drivers</h1>
    </div>
    
    <?php if (!empty($message)): ?>
        <div class="alert <?php echo strpos($message, 'Error') !== false ? 'alert-error' : 'alert-success'; ?>">
            <?php echo $message; ?>
        </div>
    <?php endif; ?>
    
    <div class="form-container">
        <h2><?php echo $edit_driver ? 'Edit Driver' : 'Add New Driver'; ?></h2>
        <form method="POST">
            <?php if ($edit_driver): ?>
                <input type="hidden" name="driver_id" value="<?php echo $edit_driver['id']; ?>">
            <?php endif; ?>
            
            <div class="form-group">
                <label for="driver_name">Driver Name</label>
                <input type="text" id="driver_name" name="driver_name" required 
                       value="<?php echo $edit_driver ? $edit_driver['driver_name'] : ''; ?>">
            </div>
            
            <div class="form-group">
                <label for="contact">Contact Number</label>
                <input type="text" id="contact" name="contact" 
                       value="<?php echo $edit_driver ? $edit_driver['contact'] : ''; ?>">
            </div>
            
            <div class="form-group">
                <label for="license_number">License Number</label>
                <input type="text" id="license_number" name="license_number" 
                       value="<?php echo $edit_driver ? $edit_driver['license_number'] : ''; ?>">
            </div>
            
            <div class="form-group">
                <label for="license_expiry">License Expiry Date</label>
                <input type="date" id="license_expiry" name="license_expiry" 
                       value="<?php echo $edit_driver ? $edit_driver['license_expiry'] : ''; ?>">
            </div>
            
            <div class="form-group">
                <?php if ($edit_driver): ?>
                    <button type="submit" name="update_driver" class="btn btn-primary">Update Driver</button>
                    <a href="manage_drivers.php" class="btn btn-secondary">Cancel</a>
                <?php else: ?>
                    <button type="submit" name="add_driver" class="btn btn-primary">Add Driver</button>
                <?php endif; ?>
            </div>
        </form>
    </div>
    
    <div class="table-container">
        <h2>All Drivers</h2>
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Driver Name</th>
                    <th>Contact</th>
                    <th>License Number</th>
                    <th>License Expiry</th>
                    <th>Assigned Vehicles</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if (count($drivers) > 0): ?>
                    <?php foreach ($drivers as $driver): ?>
                        <tr>
                            <td><?php echo $driver['id']; ?></td>
                            <td><?php echo $driver['driver_name']; ?></td>
                            <td><?php echo $driver['contact'] ? $driver['contact'] : 'N/A'; ?></td>
                            <td><?php echo $driver['license_number'] ? $driver['license_number'] : 'N/A'; ?></td>
                            <td><?php echo $driver['license_expiry'] ? date('M j, Y', strtotime($driver['license_expiry'])) : 'N/A'; ?></td>
                            <td><?php echo $driver['vehicle_count']; ?></td>
                            <td>
                                <a href="manage_drivers.php?edit=<?php echo $driver['id']; ?>" class="btn btn-sm btn-primary">Edit</a>
                                <form method="POST" class="inline-form">
                                    <input type="hidden" name="driver_id" value="<?php echo $driver['id']; ?>">
                                    <button type="submit" name="delete_driver" class="btn btn-sm btn-danger" 
                                            onclick="return confirm('Are you sure you want to delete this driver?')">Delete</button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="7">No drivers found.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</main>

<?php include '../includes/footer.php'; ?>