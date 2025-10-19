<?php
require_once '../config.php';
checkRole(['admin', 'transport']);

$page_title = "Manage Vehicles";
$message = '';

// Get all drivers for the dropdown
$drivers_stmt = $pdo->query("SELECT * FROM drivers ORDER BY driver_name");
$drivers = $drivers_stmt->fetchAll();

// Handle form actions
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['add_vehicle'])) {
        $vehicle_number = trim($_POST['vehicle_number']);
        $model = trim($_POST['model']);
        $capacity = $_POST['capacity'];
        $driver_id = !empty($_POST['driver_id']) ? $_POST['driver_id'] : null;
        
        try {
            $stmt = $pdo->prepare("INSERT INTO vehicles (vehicle_number, model, capacity, driver_id) 
                                  VALUES (?, ?, ?, ?)");
            $stmt->execute([$vehicle_number, $model, $capacity, $driver_id]);
            $message = "Vehicle added successfully!";
        } catch (PDOException $e) {
            $message = "Error adding vehicle: " . $e->getMessage();
        }
    } elseif (isset($_POST['update_vehicle'])) {
        $vehicle_id = $_POST['vehicle_id'];
        $vehicle_number = trim($_POST['vehicle_number']);
        $model = trim($_POST['model']);
        $capacity = $_POST['capacity'];
        $driver_id = !empty($_POST['driver_id']) ? $_POST['driver_id'] : null;
        
        try {
            $stmt = $pdo->prepare("UPDATE vehicles SET vehicle_number = ?, model = ?, capacity = ?, 
                                  driver_id = ? WHERE id = ?");
            $stmt->execute([$vehicle_number, $model, $capacity, $driver_id, $vehicle_id]);
            $message = "Vehicle updated successfully!";
        } catch (PDOException $e) {
            $message = "Error updating vehicle: " . $e->getMessage();
        }
    } elseif (isset($_POST['delete_vehicle'])) {
        $vehicle_id = $_POST['vehicle_id'];
        
        try {
            $stmt = $pdo->prepare("DELETE FROM vehicles WHERE id = ?");
            $stmt->execute([$vehicle_id]);
            $message = "Vehicle deleted successfully!";
        } catch (PDOException $e) {
            $message = "Error deleting vehicle: " . $e->getMessage();
        }
    }
}

// Get all vehicles with driver information
$stmt = $pdo->query("SELECT v.*, d.driver_name, d.contact as driver_contact 
                     FROM vehicles v 
                     LEFT JOIN drivers d ON v.driver_id = d.id 
                     ORDER BY v.vehicle_number");
$vehicles = $stmt->fetchAll();

// Get vehicle for editing
$edit_vehicle = null;
if (isset($_GET['edit'])) {
    $stmt = $pdo->prepare("SELECT * FROM vehicles WHERE id = ?");
    $stmt->execute([$_GET['edit']]);
    $edit_vehicle = $stmt->fetch();
}
?>

<?php include '../includes/header.php'; ?>
<?php include '../includes/sidebar.php'; ?>

<main class="main-content">
    
    <div class="content-header">
               <style>
    .btn-custom {
      background: #e90b0b;     /* Red background */
      color: white;            /* White text */
      padding: 10px 20px;      /* Space inside */
      border: none;            /* No border */
      border-radius: 6px;      /* Rounded corners */
      cursor: pointer;         /* Pointer on hover */
      font-size: 16px;         /* Readable text */
      transition: 0.3s;        /* Smooth hover effect */
      text-decoration: none;   /* Remove underline from <a> */
      display: inline-block;   /* Keeps spacing clean */
      margin-left: 10px;       /* Separation between buttons */
    }

    .btn-custom:hover {
      background: #45a049;     /* Darker green on hover */
    }

    .btn-container {
      float: right;            /* Align to the right */
    }
  </style>
   <div class="btn-container">
    <a href="../transport/index.php" class="btn-custom">Back</a>
  </div>
        <h1>Manage Vehicles</h1>
    </div>
    
    <?php if (!empty($message)): ?>
        <div class="alert <?php echo strpos($message, 'Error') !== false ? 'alert-error' : 'alert-success'; ?>">
            <?php echo $message; ?>
        </div>
    <?php endif; ?>
    
    <div class="form-container">
        <h2><?php echo $edit_vehicle ? 'Edit Vehicle' : 'Add New Vehicle'; ?></h2>
        <form method="POST">
            <?php if ($edit_vehicle): ?>
                <input type="hidden" name="vehicle_id" value="<?php echo $edit_vehicle['id']; ?>">
            <?php endif; ?>
            
            <div class="form-group">
                <label for="vehicle_number">Vehicle Number</label>
                <input type="text" id="vehicle_number" name="vehicle_number" required 
                       value="<?php echo $edit_vehicle ? $edit_vehicle['vehicle_number'] : ''; ?>">
            </div>
            
            <div class="form-group">
                <label for="model">Model</label>
                <input type="text" id="model" name="model" 
                       value="<?php echo $edit_vehicle ? $edit_vehicle['model'] : ''; ?>">
            </div>
            
            <div class="form-group">
                <label for="capacity">Capacity</label>
                <input type="number" id="capacity" name="capacity" min="1" 
                       value="<?php echo $edit_vehicle ? $edit_vehicle['capacity'] : ''; ?>">
            </div>
            
            <div class="form-group">
                <label for="driver_id">Driver</label>
                <select id="driver_id" name="driver_id">
                    <option value="">Select Driver</option>
                    <?php foreach ($drivers as $driver): ?>
                    <option value="<?php echo $driver['id']; ?>" 
                    <?php echo $edit_vehicle && $edit_vehicle['driver_id'] == $driver['id'] ? 'selected' : ''; ?>>
                    <?php echo $driver['driver_name']; ?> (<?php echo $driver['license_number']; ?>)
                    </option>
                    <?php endforeach; ?>
                </select>
            </div>
            
            <div class="form-group">
                <?php if ($edit_vehicle): ?>
                    <button type="submit" name="update_vehicle" class="btn btn-primary">Update Vehicle</button>
                    <a href="manage_vehicles.php" class="btn btn-secondary">Cancel</a>
                <?php else: ?>
                    <button type="submit" name="add_vehicle" class="btn btn-primary">Add Vehicle</button>
                <?php endif; ?>
            </div>
        </form>
    </div>
    
    <div class="table-container">
        <h2>All Vehicles</h2>
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Vehicle Number</th>
                    <th>Model</th>
                    <th>Capacity</th>
                    <th>Driver</th>
                    <th>Contact</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if (count($vehicles) > 0): ?>
                    <?php foreach ($vehicles as $vehicle): ?>
                        <tr>
                            <td><?php echo $vehicle['id']; ?></td>
                            <td><?php echo $vehicle['vehicle_number']; ?></td>
                            <td><?php echo $vehicle['model'] ? $vehicle['model'] : 'N/A'; ?></td>
                            <td><?php echo $vehicle['capacity'] ? $vehicle['capacity'] : 'N/A'; ?></td>
                            <td><?php echo $vehicle['driver_name'] ? $vehicle['driver_name'] : 'N/A'; ?></td>
                            <td><?php echo $vehicle['driver_contact'] ? $vehicle['driver_contact'] : 'N/A'; ?></td>
                            <td>
                                <a href="manage_vehicles.php?edit=<?php echo $vehicle['id']; ?>" class="btn btn-sm btn-primary">Edit</a>
                                <form method="POST" class="inline-form">
                                    <input type="hidden" name="vehicle_id" value="<?php echo $vehicle['id']; ?>">
                                    <button type="submit" name="delete_vehicle" class="btn btn-sm btn-danger" 
                                            onclick="return confirm('Are you sure you want to delete this vehicle?')">Delete</button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="7">No vehicles found.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</main>

<?php include '../includes/footer.php'; ?>