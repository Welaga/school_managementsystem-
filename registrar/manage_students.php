<?php
require_once '../config.php';
checkRole(['admin', 'registrar']);

$page_title = "Manage Students";
$message = '';

// Handle form actions
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['add_student'])) {
        $first_name = trim($_POST['first_name']);
        $last_name = trim($_POST['last_name']);
        $dob = $_POST['dob'];
        $class_id = $_POST['class_id'];
        $address = trim($_POST['address']);
        $contact = trim($_POST['contact']);
        $guardian_name = trim($_POST['guardian_name']);
        $guardian_contact = trim($_POST['guardian_contact']);
        
        try {
            // First create a user account
            $username = strtolower($first_name . '.' . $last_name);
            $password = password_hash('password123', PASSWORD_DEFAULT); // Default password
            
            $stmt = $pdo->prepare("INSERT INTO users (username, password, role, status) VALUES (?, ?, 'student', 1)");
            $stmt->execute([$username, $password]);
            $user_id = $pdo->lastInsertId();
            
            // Then create student record
            $stmt = $pdo->prepare("INSERT INTO students (user_id, first_name, last_name, dob, class_id, address, contact, guardian_name, guardian_contact) 
                                  VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
            $stmt->execute([$user_id, $first_name, $last_name, $dob, $class_id, $address, $contact, $guardian_name, $guardian_contact]);
            
            $message = "Student added successfully!";
        } catch (PDOException $e) {
            $message = "Error adding student: " . $e->getMessage();
        }
    } elseif (isset($_POST['update_student'])) {
        $student_id = $_POST['student_id'];
        $first_name = trim($_POST['first_name']);
        $last_name = trim($_POST['last_name']);
        $dob = $_POST['dob'];
        $class_id = $_POST['class_id'];
        $address = trim($_POST['address']);
        $contact = trim($_POST['contact']);
        $guardian_name = trim($_POST['guardian_name']);
        $guardian_contact = trim($_POST['guardian_contact']);
        
        try {
            $stmt = $pdo->prepare("UPDATE students SET first_name = ?, last_name = ?, dob = ?, class_id = ?, 
                                  address = ?, contact = ?, guardian_name = ?, guardian_contact = ? WHERE id = ?");
            $stmt->execute([$first_name, $last_name, $dob, $class_id, $address, $contact, $guardian_name, $guardian_contact, $student_id]);
            $message = "Student updated successfully!";
        } catch (PDOException $e) {
            $message = "Error updating student: " . $e->getMessage();
        }
    } elseif (isset($_POST['delete_student'])) {
        $student_id = $_POST['student_id'];
        
        try {
            // First get user_id to delete from users table
            $stmt = $pdo->prepare("SELECT user_id FROM students WHERE id = ?");
            $stmt->execute([$student_id]);
            $student = $stmt->fetch();
            
            if ($student) {
                // Delete student record
                $stmt = $pdo->prepare("DELETE FROM students WHERE id = ?");
                $stmt->execute([$student_id]);
                
                // Delete user account
                $stmt = $pdo->prepare("DELETE FROM users WHERE id = ?");
                $stmt->execute([$student['user_id']]);
                
                $message = "Student deleted successfully!";
            }
        } catch (PDOException $e) {
            $message = "Error deleting student: " . $e->getMessage();
        }
    }
}

// Get all students with class information
$stmt = $pdo->query("SELECT s.*, c.name as class_name FROM students s LEFT JOIN classes c ON s.class_id = c.id ORDER BY s.id DESC");
$students = $stmt->fetchAll();

// Get classes for dropdown
$stmt = $pdo->query("SELECT * FROM classes ORDER BY name");
$classes = $stmt->fetchAll();

// Get student for editing
$edit_student = null;
if (isset($_GET['edit'])) {
    $stmt = $pdo->prepare("SELECT * FROM students WHERE id = ?");
    $stmt->execute([$_GET['edit']]);
    $edit_student = $stmt->fetch();
}
?>

<?php include '../includes/header.php'; ?>
<?php include '../includes/sidebar.php'; ?>

<main class="main-content">
    <div class="content-header">
        <h1>Manage Students</h1>
    </div>
    
    <?php if (!empty($message)): ?>
        <div class="alert <?php echo strpos($message, 'Error') !== false ? 'alert-error' : 'alert-success'; ?>">
            <?php echo $message; ?>
        </div>
    <?php endif; ?>
    
    <div class="form-container">
        <h2><?php echo $edit_student ? 'Edit Student' : 'Add New Student'; ?></h2>
        <form method="POST">
            <?php if ($edit_student): ?>
                <input type="hidden" name="student_id" value="<?php echo $edit_student['id']; ?>">
            <?php endif; ?>
            
            <div class="form-group">
                <label for="first_name">First Name</label>
                <input type="text" id="first_name" name="first_name" required 
                       value="<?php echo $edit_student ? $edit_student['first_name'] : ''; ?>">
            </div>
            
            <div class="form-group">
                <label for="last_name">Last Name</label>
                <input type="text" id="last_name" name="last_name" required 
                       value="<?php echo $edit_student ? $edit_student['last_name'] : ''; ?>">
            </div>
            
            <div class="form-group">
                <label for="dob">Date of Birth</label>
                <input type="date" id="dob" name="dob" required 
                       value="<?php echo $edit_student ? $edit_student['dob'] : ''; ?>">
            </div>
            
            <div class="form-group">
                <label for="class_id">Class</label>
                <select id="class_id" name="class_id" required>
                    <option value="">Select Class</option>
                    <?php foreach ($classes as $class): ?>
                        <option value="<?php echo $class['id']; ?>" 
                            <?php echo $edit_student && $edit_student['class_id'] == $class['id'] ? 'selected' : ''; ?>>
                            <?php echo $class['name']; ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            
            <div class="form-group">
                <label for="address">Address</label>
                <textarea id="address" name="address"><?php echo $edit_student ? $edit_student['address'] : ''; ?></textarea>
            </div>
            
            <div class="form-group">
                <label for="contact">Contact Number</label>
                <input type="text" id="contact" name="contact" 
                       value="<?php echo $edit_student ? $edit_student['contact'] : ''; ?>">
            </div>
            
            <div class="form-group">
                <label for="guardian_name">Guardian Name</label>
                <input type="text" id="guardian_name" name="guardian_name" 
                       value="<?php echo $edit_student ? $edit_student['guardian_name'] : ''; ?>">
            </div>
            
            <div class="form-group">
                <label for="guardian_contact">Guardian Contact</label>
                <input type="text" id="guardian_contact" name="guardian_contact" 
                       value="<?php echo $edit_student ? $edit_student['guardian_contact'] : ''; ?>">
            </div>
            
            <div class="form-group">
                <?php if ($edit_student): ?>
                    <button type="submit" name="update_student" class="btn btn-primary">Update Student</button>
                    <a href="manage_students.php" class="btn btn-secondary">Cancel</a>
                <?php else: ?>
                    <button type="submit" name="add_student" class="btn btn-primary">Add Student</button>
                <?php endif; ?>
            </div>
        </form>
    </div>
    
    <div class="table-container">
        <h2>All Students</h2>
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Name</th>
                    <th>Date of Birth</th>
                    <th>Class</th>
                    <th>Contact</th>
                    <th>Guardian</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if (count($students) > 0): ?>
                    <?php foreach ($students as $student): ?>
                        <tr>
                            <td><?php echo $student['id']; ?></td>
                            <td><?php echo $student['first_name'] . ' ' . $student['last_name']; ?></td>
                            <td><?php echo date('M j, Y', strtotime($student['dob'])); ?></td>
                            <td><?php echo $student['class_name'] ? $student['class_name'] : 'Not assigned'; ?></td>
                            <td><?php echo $student['contact'] ? $student['contact'] : 'N/A'; ?></td>
                            <td><?php echo $student['guardian_name'] ? $student['guardian_name'] : 'N/A'; ?></td>
                            <td>
                                <a href="manage_students.php?edit=<?php echo $student['id']; ?>" class="btn btn-sm btn-primary">Edit</a>
                                <form method="POST" class="inline-form">
                                    <input type="hidden" name="student_id" value="<?php echo $student['id']; ?>">
                                    <button type="submit" name="delete_student" class="btn btn-sm btn-danger" 
                                            onclick="return confirm('Are you sure you want to delete this student?')">Delete</button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="7">No students found.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</main>

<?php include '../includes/footer.php'; ?>