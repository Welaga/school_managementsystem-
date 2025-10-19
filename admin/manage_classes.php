<?php
require_once '../config.php';
checkRole(['admin', 'registrar']);

$page_title = "Manage Classes";
$message = '';
$error = '';

// CSRF Protection
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

function generateCsrfToken() {
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

function validateCsrfToken($token) {
    return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
}

// Handle form actions
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Validate CSRF token
    if (!isset($_POST['csrf_token']) || !validateCsrfToken($_POST['csrf_token'])) {
        $error = "Security token validation failed.";
    } else {
        if (isset($_POST['add_class'])) {
            $name = trim($_POST['name']);
            $room = trim($_POST['room']);
            $capacity = $_POST['capacity'];
            $description = trim($_POST['description'] ?? '');
            $status = $_POST['status'] ?? 'active';
            
            // Validation
            $errors = [];
            
            if (empty($name)) {
                $errors[] = "Class name is required.";
            } elseif (strlen($name) > 100) {
                $errors[] = "Class name must be less than 100 characters.";
            }
            
            if (!empty($room) && strlen($room) > 50) {
                $errors[] = "Room number must be less than 50 characters.";
            }
            
            if (!empty($capacity) && (!is_numeric($capacity) || $capacity < 1 || $capacity > 1000)) {
                $errors[] = "Capacity must be a number between 1 and 1000.";
            }
            
            if (!empty($description) && strlen($description) > 500) {
                $errors[] = "Description must be less than 500 characters.";
            }
            
            if (empty($errors)) {
                try {
                    // Check for duplicate class name
                    $checkStmt = $pdo->prepare("SELECT id FROM classes WHERE name = ?");
                    $checkStmt->execute([$name]);
                    if ($checkStmt->fetch()) {
                        $error = "A class with this name already exists.";
                    } else {
                        // Check if status column exists
                        $columnCheck = $pdo->prepare("SHOW COLUMNS FROM classes LIKE 'status'");
                        $columnCheck->execute();
                        $hasStatus = $columnCheck->fetch();
                        
                        if ($hasStatus) {
                            $stmt = $pdo->prepare("INSERT INTO classes (name, room, capacity, description, status) VALUES (?, ?, ?, ?, ?)");
                            $stmt->execute([$name, $room, $capacity ?: null, $description, $status]);
                        } else {
                            $stmt = $pdo->prepare("INSERT INTO classes (name, room, capacity, description) VALUES (?, ?, ?, ?)");
                            $stmt->execute([$name, $room, $capacity ?: null, $description]);
                        }
                        $message = "Class added successfully!";
                    }
                } catch (PDOException $e) {
                    error_log("Error adding class: " . $e->getMessage());
                    $error = "Error adding class. Please try again.";
                }
            } else {
                $error = implode(" ", $errors);
            }
            
        } elseif (isset($_POST['update_class'])) {
            $class_id = (int)$_POST['class_id'];
            $name = trim($_POST['name']);
            $room = trim($_POST['room']);
            $capacity = $_POST['capacity'];
            $description = trim($_POST['description'] ?? '');
            $status = $_POST['status'] ?? 'active';
            
            // Validation (same as add)
            $errors = [];
            
            if (empty($name)) {
                $errors[] = "Class name is required.";
            } elseif (strlen($name) > 100) {
                $errors[] = "Class name must be less than 100 characters.";
            }
            
            if (!empty($room) && strlen($room) > 50) {
                $errors[] = "Room number must be less than 50 characters.";
            }
            
            if (!empty($capacity) && (!is_numeric($capacity) || $capacity < 1 || $capacity > 1000)) {
                $errors[] = "Capacity must be a number between 1 and 1000.";
            }
            
            if (!empty($description) && strlen($description) > 500) {
                $errors[] = "Description must be less than 500 characters.";
            }
            
            if (empty($errors)) {
                try {
                    // Check for duplicate class name (excluding current class)
                    $checkStmt = $pdo->prepare("SELECT id FROM classes WHERE name = ? AND id != ?");
                    $checkStmt->execute([$name, $class_id]);
                    if ($checkStmt->fetch()) {
                        $error = "A class with this name already exists.";
                    } else {
                        // Check if status column exists
                        $columnCheck = $pdo->prepare("SHOW COLUMNS FROM classes LIKE 'status'");
                        $columnCheck->execute();
                        $hasStatus = $columnCheck->fetch();
                        
                        if ($hasStatus) {
                            $stmt = $pdo->prepare("UPDATE classes SET name = ?, room = ?, capacity = ?, description = ?, status = ? WHERE id = ?");
                            $stmt->execute([$name, $room, $capacity ?: null, $description, $status, $class_id]);
                        } else {
                            $stmt = $pdo->prepare("UPDATE classes SET name = ?, room = ?, capacity = ?, description = ? WHERE id = ?");
                            $stmt->execute([$name, $room, $capacity ?: null, $description, $class_id]);
                        }
                        $message = "Class updated successfully!";
                    }
                } catch (PDOException $e) {
                    error_log("Error updating class: " . $e->getMessage());
                    $error = "Error updating class. Please try again.";
                }
            } else {
                $error = implode(" ", $errors);
            }
            
        } elseif (isset($_POST['delete_class'])) {
            $class_id = (int)$_POST['class_id'];
            
            try {
                // Check if class has students enrolled (if student_classes table exists)
                $tableCheck = $pdo->prepare("SHOW TABLES LIKE 'student_classes'");
                $tableCheck->execute();
                $hasStudentClasses = $tableCheck->fetch();
                
                if ($hasStudentClasses) {
                    $checkStmt = $pdo->prepare("SELECT COUNT(*) FROM student_classes WHERE class_id = ?");
                    $checkStmt->execute([$class_id]);
                    $studentCount = $checkStmt->fetchColumn();
                    
                    if ($studentCount > 0) {
                        $error = "Cannot delete class with enrolled students. Please remove students first.";
                    } else {
                        $stmt = $pdo->prepare("DELETE FROM classes WHERE id = ?");
                        $stmt->execute([$class_id]);
                        $message = "Class deleted successfully!";
                    }
                } else {
                    $stmt = $pdo->prepare("DELETE FROM classes WHERE id = ?");
                    $stmt->execute([$class_id]);
                    $message = "Class deleted successfully!";
                }
            } catch (PDOException $e) {
                error_log("Error deleting class: " . $e->getMessage());
                $error = "Error deleting class. Please try again.";
            }
        } elseif (isset($_POST['toggle_status'])) {
            $class_id = (int)$_POST['class_id'];
            
            try {
                // Check if status column exists
                $columnCheck = $pdo->prepare("SHOW COLUMNS FROM classes LIKE 'status'");
                $columnCheck->execute();
                $hasStatus = $columnCheck->fetch();
                
                if ($hasStatus) {
                    $stmt = $pdo->prepare("UPDATE classes SET status = CASE WHEN status = 'active' THEN 'inactive' ELSE 'active' END WHERE id = ?");
                    $stmt->execute([$class_id]);
                    $message = "Class status updated successfully!";
                } else {
                    $error = "Status feature is not available. Please add the status column to the database.";
                }
            } catch (PDOException $e) {
                error_log("Error updating class status: " . $e->getMessage());
                $error = "Error updating class status. Please try again.";
            }
        }
    }
}

// Get filter parameters
$status_filter = $_GET['status'] ?? 'all';
$search_term = $_GET['search'] ?? '';

// Build query with filters
$query = "SELECT * FROM classes WHERE 1=1";
$params = [];

// Check if status column exists
$columnCheck = $pdo->prepare("SHOW COLUMNS FROM classes LIKE 'status'");
$columnCheck->execute();
$hasStatus = $columnCheck->fetch();

if ($hasStatus && $status_filter !== 'all') {
    $query .= " AND status = ?";
    $params[] = $status_filter;
}

if (!empty($search_term)) {
    $query .= " AND (name LIKE ? OR room LIKE ? OR description LIKE ?)";
    $search_param = "%$search_term%";
    $params[] = $search_param;
    $params[] = $search_param;
    $params[] = $search_param;
}

$query .= " ORDER BY name";

$stmt = $pdo->prepare($query);
$stmt->execute($params);
$classes = $stmt->fetchAll();

// Get class for editing
$edit_class = null;
if (isset($_GET['edit'])) {
    $stmt = $pdo->prepare("SELECT * FROM classes WHERE id = ?");
    $stmt->execute([(int)$_GET['edit']]);
    $edit_class = $stmt->fetch();
}

$csrf_token = generateCsrfToken();
?>

<?php include '../includes/header.php'; ?>
<?php include '../includes/sidebar.php'; ?>

<main class="main-content">
    <div class="content-header">
        <h1>Manage Classes</h1>
    </div>
    
    <?php if (!empty($message)): ?>
        <div class="alert alert-success">
            <?php echo htmlspecialchars($message); ?>
        </div>
    <?php endif; ?>
    
    <?php if (!empty($error)): ?>
        <div class="alert alert-error">
            <?php echo htmlspecialchars($error); ?>
        </div>
    <?php endif; ?>
    
    <div class="form-container">
        <h2><?php echo $edit_class ? 'Edit Class' : 'Add New Class'; ?></h2>
        <form method="POST">
            <input type="hidden" name="csrf_token" value="<?php echo $csrf_token; ?>">
            
            <?php if ($edit_class): ?>
                <input type="hidden" name="class_id" value="<?php echo (int)$edit_class['id']; ?>">
            <?php endif; ?>
            
            <div class="form-row">
                <div class="form-group">
                    <label for="name">Class Name *</label>
                    <input type="text" id="name" name="name" required 
                           value="<?php echo $edit_class ? htmlspecialchars($edit_class['name']) : ''; ?>"
                           maxlength="100">
                    <small class="form-help">Required, max 100 characters</small>
                </div>
                
                <div class="form-group">
                    <label for="room">Room Number</label>
                    <input type="text" id="room" name="room" 
                           value="<?php echo $edit_class ? htmlspecialchars($edit_class['room']) : ''; ?>"
                           maxlength="50">
                    <small class="form-help">Max 50 characters</small>
                </div>
            </div>
            
            <div class="form-row">
                <div class="form-group">
                    <label for="capacity">Capacity</label>
                    <input type="number" id="capacity" name="capacity" min="1" max="1000"
                           value="<?php echo $edit_class ? htmlspecialchars($edit_class['capacity']) : ''; ?>">
                    <small class="form-help">Between 1-1000</small>
                </div>
                
                <?php if ($hasStatus): ?>
                <div class="form-group">
                    <label for="status">Status</label>
                    <select id="status" name="status">
                        <option value="active" <?php echo ($edit_class && $edit_class['status'] == 'active') ? 'selected' : ''; ?>>Active</option>
                        <option value="inactive" <?php echo ($edit_class && $edit_class['status'] == 'inactive') ? 'selected' : ''; ?>>Inactive</option>
                    </select>
                </div>
                <?php endif; ?>
            </div>
            
            <div class="form-group">
                <label for="description">Description</label>
                <textarea id="description" name="description" rows="3" maxlength="500"><?php echo $edit_class ? htmlspecialchars($edit_class['description']) : ''; ?></textarea>
                <small class="form-help">Max 500 characters</small>
            </div>
            
            <div class="form-group">
                <?php if ($edit_class): ?>
                    <button type="submit" name="update_class" class="btn btn-primary">Update Class</button>
                    <a href="manage_classes.php" class="btn btn-secondary">Cancel</a>
                <?php else: ?>
                    <button type="submit" name="add_class" class="btn btn-primary">Add Class</button>
                <?php endif; ?>
            </div>
        </form>
    </div>
    
    <?php if ($hasStatus): ?>
    <div class="filters-container">
        <h2>Class Filters</h2>
        <form method="GET" class="filter-form">
            <div class="form-row">
                <div class="form-group">
                    <label for="status">Status</label>
                    <select id="status" name="status">
                        <option value="all" <?php echo $status_filter === 'all' ? 'selected' : ''; ?>>All</option>
                        <option value="active" <?php echo $status_filter === 'active' ? 'selected' : ''; ?>>Active</option>
                        <option value="inactive" <?php echo $status_filter === 'inactive' ? 'selected' : ''; ?>>Inactive</option>
                    </select>
                </div>
                
                <div class="form-group">
                    <label for="search">Search</label>
                    <input type="text" id="search" name="search" 
                           value="<?php echo htmlspecialchars($search_term); ?>"
                           placeholder="Search by name, room, or description">
                </div>
                
                <div class="form-group">
                    <button type="submit" class="btn btn-secondary">Apply Filters</button>
                    <a href="manage_classes.php" class="btn btn-outline">Clear</a>
                </div>
            </div>
        </form>
    </div>
    <?php endif; ?>
    
    <div class="table-container">
        <h2>All Classes (<?php echo count($classes); ?>)</h2>
        
        <?php if (count($classes) > 0): ?>
            <div class="table-responsive">
                <table>
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Name</th>
                            <th>Room</th>
                            <th>Capacity</th>
                            <?php if ($hasStatus): ?>
                                <th>Status</th>
                            <?php endif; ?>
                            <th>Description</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($classes as $class): ?>
                            <tr class="<?php echo ($hasStatus && $class['status'] === 'inactive') ? 'inactive-row' : ''; ?>">
                                <td><?php echo (int)$class['id']; ?></td>
                                <td>
                                    <strong><?php echo htmlspecialchars($class['name']); ?></strong>
                                </td>
                                <td><?php echo $class['room'] ? htmlspecialchars($class['room']) : 'N/A'; ?></td>
                                <td><?php echo $class['capacity'] ? (int)$class['capacity'] : 'N/A'; ?></td>
                                <?php if ($hasStatus): ?>
                                    <td>
                                        <span class="status-badge status-<?php echo $class['status']; ?>">
                                            <?php echo ucfirst($class['status']); ?>
                                        </span>
                                    </td>
                                <?php endif; ?>
                                <td>
                                    <?php if (!empty($class['description'])): ?>
                                        <span class="tooltip" title="<?php echo htmlspecialchars($class['description']); ?>">
                                            <?php echo strlen($class['description']) > 50 ? htmlspecialchars(substr($class['description'], 0, 50)) . '...' : htmlspecialchars($class['description']); ?>
                                        </span>
                                    <?php else: ?>
                                        N/A
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <div class="action-buttons">
                                        <a href="manage_classes.php?edit=<?php echo (int)$class['id']; ?>" 
                                           class="btn btn-sm btn-primary" title="Edit Class">Edit</a>
                                        
                                        <?php if ($hasStatus): ?>
                                        <form method="POST" class="inline-form">
                                            <input type="hidden" name="csrf_token" value="<?php echo $csrf_token; ?>">
                                            <input type="hidden" name="class_id" value="<?php echo (int)$class['id']; ?>">
                                            <button type="submit" name="toggle_status" class="btn btn-sm btn-<?php echo $class['status'] === 'active' ? 'warning' : 'success'; ?>" 
                                                    title="<?php echo $class['status'] === 'active' ? 'Deactivate' : 'Activate'; ?> Class">
                                                <?php echo $class['status'] === 'active' ? 'Deactivate' : 'Activate'; ?>
                                            </button>
                                        </form>
                                        <?php endif; ?>
                                        
                                        <form method="POST" class="inline-form">
                                            <input type="hidden" name="csrf_token" value="<?php echo $csrf_token; ?>">
                                            <input type="hidden" name="class_id" value="<?php echo (int)$class['id']; ?>">
                                            <button type="submit" name="delete_class" class="btn btn-sm btn-danger" 
                                                    onclick="return confirm('Are you sure you want to delete this class? This action cannot be undone.')" 
                                                    title="Delete Class">Delete</button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php else: ?>
            <div class="empty-state">
                <p>No classes found.</p>
                <?php if (($hasStatus && $status_filter !== 'all') || !empty($search_term)): ?>
                    <p>Try adjusting your filters or <a href="manage_classes.php">clear all filters</a>.</p>
                <?php endif; ?>
            </div>
        <?php endif; ?>
    </div>
</main>

<style>
.form-row {
    display: flex;
    gap: 1rem;
    margin-bottom: 1rem;
}

.form-row .form-group {
    flex: 1;
}

.form-help {
    display: block;
    color: #666;
    font-size: 0.875rem;
    margin-top: 0.25rem;
}

.filters-container {
    background: #f8f9fa;
    padding: 1.5rem;
    border-radius: 0.5rem;
    margin-bottom: 2rem;
}

.filter-form .form-row {
    margin-bottom: 0;
}

.table-responsive {
    overflow-x: auto;
}

.status-badge {
    padding: 0.25rem 0.5rem;
    border-radius: 0.25rem;
    font-size: 0.875rem;
    font-weight: 500;
}

.status-active {
    background: #d1fae5;
    color: #065f46;
}

.status-inactive {
    background: #fef3c7;
    color: #92400e;
}

.inactive-row {
    opacity: 0.7;
    background: #f9fafb;
}

.action-buttons {
    display: flex;
    gap: 0.5rem;
    flex-wrap: wrap;
}

.tooltip {
    border-bottom: 1px dotted #666;
    cursor: help;
}

.empty-state {
    text-align: center;
    padding: 3rem;
    color: #666;
}

.btn-outline {
    background: transparent;
    border: 1px solid #d1d5db;
    color: #374151;
}

.btn-outline:hover {
    background: #f9fafb;
}
</style>

<?php include '../includes/footer.php'; ?>