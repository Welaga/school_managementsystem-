<?php
require_once '../config.php';
checkRole(['admin', 'teacher']);

$page_title = "Manage Assignments";
$message = '';

// Get teacher details
$teacher = getTeacherDetails($_SESSION['user_id']);
if (!$teacher) {
    header("Location: ../login.php");
    exit();
}
$teacher_id = $teacher['id'];

// Get classes and subjects assigned to teacher
$stmt = $pdo->prepare("SELECT DISTINCT c.* FROM classes c 
                      JOIN class_subjects cs ON c.id = cs.class_id 
                      WHERE cs.teacher_id = ? ORDER BY c.name");
$stmt->execute([$teacher_id]);
$classes = $stmt->fetchAll();

// Handle form actions
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['add_assignment'])) {
        $title = trim($_POST['title']);
        $description = trim($_POST['description']);
        $subject_id = $_POST['subject_id'];
        $class_id = $_POST['class_id'];
        $due_date = $_POST['due_date'];
        
        // File upload handling
        $file_path = null;
        if (isset($_FILES['assignment_file']) && $_FILES['assignment_file']['error'] == UPLOAD_ERR_OK) {
            $upload_dir = '../uploads/assignments/';
            if (!file_exists($upload_dir)) {
                mkdir($upload_dir, 0777, true);
            }
            
            $file_name = time() . '_' . basename($_FILES['assignment_file']['name']);
            $target_path = $upload_dir . $file_name;
            
            if (move_uploaded_file($_FILES['assignment_file']['tmp_name'], $target_path)) {
                $file_path = 'uploads/assignments/' . $file_name;
            }
        }
        
        try {
            $stmt = $pdo->prepare("INSERT INTO assignments (title, description, subject_id, teacher_id, class_id, due_date, file_path) 
                                  VALUES (?, ?, ?, ?, ?, ?, ?)");
            $stmt->execute([$title, $description, $subject_id, $teacher_id, $class_id, $due_date, $file_path]);
            $message = "Assignment created successfully!";
        } catch (PDOException $e) {
            $message = "Error creating assignment: " . $e->getMessage();
        }
    } elseif (isset($_POST['update_assignment'])) {
        $assignment_id = $_POST['assignment_id'];
        $title = trim($_POST['title']);
        $description = trim($_POST['description']);
        $subject_id = $_POST['subject_id'];
        $due_date = $_POST['due_date'];
        
        // File upload handling
        $file_path = $_POST['existing_file'];
        if (isset($_FILES['assignment_file']) && $_FILES['assignment_file']['error'] == UPLOAD_ERR_OK) {
            $upload_dir = '../uploads/assignments/';
            if (!file_exists($upload_dir)) {
                mkdir($upload_dir, 0777, true);
            }
            
            // Delete old file if exists
            if ($file_path && file_exists('../' . $file_path)) {
                unlink('../' . $file_path);
            }
            
            $file_name = time() . '_' . basename($_FILES['assignment_file']['name']);
            $target_path = $upload_dir . $file_name;
            
            if (move_uploaded_file($_FILES['assignment_file']['tmp_name'], $target_path)) {
                $file_path = 'uploads/assignments/' . $file_name;
            }
        }
        
        try {
            $stmt = $pdo->prepare("UPDATE assignments SET title = ?, description = ?, subject_id = ?, 
                                  due_date = ?, file_path = ? WHERE id = ?");
            $stmt->execute([$title, $description, $subject_id, $due_date, $file_path, $assignment_id]);
            $message = "Assignment updated successfully!";
        } catch (PDOException $e) {
            $message = "Error updating assignment: " . $e->getMessage();
        }
    } elseif (isset($_POST['delete_assignment'])) {
        $assignment_id = $_POST['assignment_id'];
        
        try {
            // Get file path to delete the file
            $stmt = $pdo->prepare("SELECT file_path FROM assignments WHERE id = ?");
            $stmt->execute([$assignment_id]);
            $assignment = $stmt->fetch();
            
            if ($assignment && $assignment['file_path'] && file_exists('../' . $assignment['file_path'])) {
                unlink('../' . $assignment['file_path']);
            }
            
            $stmt = $pdo->prepare("DELETE FROM assignments WHERE id = ?");
            $stmt->execute([$assignment_id]);
            $message = "Assignment deleted successfully!";
        } catch (PDOException $e) {
            $message = "Error deleting assignment: " . $e->getMessage();
        }
    }
}

// Get all assignments by this teacher with submission counts
$stmt = $pdo->prepare("SELECT a.*, s.name as subject_name, c.name as class_name,
                      (SELECT COUNT(*) FROM student_assignments sa WHERE sa.assignment_id = a.id) as submission_count
                      FROM assignments a 
                      JOIN subjects s ON a.subject_id = s.id 
                      JOIN classes c ON a.class_id = c.id 
                      WHERE a.teacher_id = ? 
                      ORDER BY a.due_date DESC");
$stmt->execute([$teacher_id]);
$assignments = $stmt->fetchAll();

// Create submission counts array for easy access
$submission_counts = [];
foreach ($assignments as $assignment) {
    $submission_counts[$assignment['id']] = $assignment['submission_count'];
}

// Get assignment for editing
$edit_assignment = null;
if (isset($_GET['edit'])) {
    $stmt = $pdo->prepare("SELECT * FROM assignments WHERE id = ? AND teacher_id = ?");
    $stmt->execute([$_GET['edit'], $teacher_id]);
    $edit_assignment = $stmt->fetch();
    
    // If assignment doesn't exist or doesn't belong to this teacher
    if (!$edit_assignment) {
        // Clear the edit parameter to prevent errors
        unset($_GET['edit']);
        $message = "Assignment not found or you don't have permission to edit it.";
    }
}

// Get subjects for selected class
$subjects = [];
if (isset($_GET['class_id']) && !empty($_GET['class_id'])) {
    $class_id = $_GET['class_id'];
    $stmt = $pdo->prepare("SELECT s.* FROM subjects s 
                          JOIN class_subjects cs ON s.id = cs.subject_id 
                          WHERE cs.class_id = ? AND cs.teacher_id = ? 
                          ORDER BY s.name");
    $stmt->execute([$class_id, $teacher_id]);
    $subjects = $stmt->fetchAll();
}
?>

<?php include '../includes/header.php'; ?>
<?php include '../includes/sidebar.php'; ?>

<main class="main-content">
    <div class="content-header">
        <h1>Manage Assignments</h1>
        <p>Welcome, <?php echo $teacher['first_name'] . ' ' . $teacher['last_name']; ?></p>
    </div>
    
    <?php if (!empty($message)): ?>
        <div class="alert <?php echo strpos($message, 'Error') !== false ? 'alert-error' : 'alert-success'; ?>">
            <?php echo $message; ?>
        </div>
    <?php endif; ?>
    
    <div class="form-container">
        <h2><?php echo $edit_assignment ? 'Edit Assignment' : 'Create New Assignment'; ?></h2>
        <form method="POST" enctype="multipart/form-data">
            <?php if ($edit_assignment): ?>
                <input type="hidden" name="assignment_id" value="<?php echo $edit_assignment['id']; ?>">
                <input type="hidden" name="existing_file" value="<?php echo $edit_assignment['file_path']; ?>">
            <?php endif; ?>
            
            <div class="form-group">
                <label for="title">Assignment Title</label>
                <input type="text" id="title" name="title" required 
                       value="<?php echo $edit_assignment ? $edit_assignment['title'] : ''; ?>">
            </div>
            
            <div class="form-group">
                <label for="description">Description</label>
                <textarea id="description" name="description" rows="4"><?php echo $edit_assignment ? $edit_assignment['description'] : ''; ?></textarea>
            </div>
            
            <div class="form-group">
                <label for="class_id">Class</label>
                <select id="class_id" name="class_id" required 
                    <?php echo $edit_assignment ? 'disabled' : ''; ?> 
                    onchange="window.location.href='assignments.php?class_id=' + this.value">
                    <option value="">Select Class</option>
                    <?php foreach ($classes as $class): ?>
                        <option value="<?php echo $class['id']; ?>" 
                            <?php echo ($edit_assignment && $edit_assignment['class_id'] == $class['id']) || 
                                     (isset($_GET['class_id']) && $_GET['class_id'] == $class['id']) ? 'selected' : ''; ?>>
                            <?php echo $class['name']; ?>
                        </option>
                    <?php endforeach; ?>
                </select>
                <?php if ($edit_assignment): ?>
                    <input type="hidden" name="class_id" value="<?php echo $edit_assignment['class_id']; ?>">
                <?php endif; ?>
            </div>
            
            <?php if ((isset($_GET['class_id']) && !empty($_GET['class_id'])) || ($edit_assignment)): ?>
                <div class="form-group">
                    <label for="subject_id">Subject</label>
                    <select id="subject_id" name="subject_id" required>
                        <option value="">Select Subject</option>
                        <?php foreach ($subjects as $subject): ?>
                            <option value="<?php echo $subject['id']; ?>" 
                                <?php echo $edit_assignment && $edit_assignment['subject_id'] == $subject['id'] ? 'selected' : ''; ?>>
                                <?php echo $subject['name']; ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
        
                <label for="title">Term and Year</label>
            <?php endif; ?>
        <select name="term" required>
            <option value="1st Term">1st Term</option>
            <option value="2nd Term">2nd Term</option>
            <option value="3rd Term">3rd Term</option>
        </select>

                <select name="academic_year" required>
                <option value="<?php echo date('Y'); ?>"><?php echo date('Y'); ?></option>
                <option value="<?php echo date('Y')-1; ?>"><?php echo date('Y')-1; ?></option>
                <option value="<?php echo date('Y')+1; ?>"><?php echo date('Y')+1; ?></option>
            </select>
        </div>    
            <div class="form-group">
                <label for="due_date">Due Date</label>
                <input type="datetime-local" id="due_date" name="due_date" required 
                       value="<?php echo $edit_assignment ? date('Y-m-d\TH:i', strtotime($edit_assignment['due_date'])) : ''; ?>">
        
            
            <div class="form-group">
                <label for="assignment_file">Assignment File (Optional)</label>
                <input type="file" id="assignment_file" name="assignment_file">
                <?php if ($edit_assignment && $edit_assignment['file_path']): ?>
                    <p>Current file: <a href="../<?php echo $edit_assignment['file_path']; ?>" target="_blank">Download</a></p>
                <?php endif; ?>
            </div>
            
            <div class="form-group">
                <?php if ($edit_assignment): ?>
                    <button type="submit" name="update_assignment" class="btn btn-primary">Update Assignment</button>
                    <a href="assignments.php" class="btn btn-secondary">Cancel</a>
                <?php else: ?>
                    <button type="submit" name="add_assignment" class="btn btn-primary">Create Assignment</button>
                <?php endif; ?>
            </div>
        </form>
    </div>
    
    <div class="table-container">
        <h2>My Assignments</h2>
        <table>
            <thead>
                <tr>
                    <th>Title</th>
                    <th>Class</th>
                    <th>Subject</th>
                    <th>Due Date</th>
                    <th>File</th>
                    <th>Submissions</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if (count($assignments) > 0): ?>
                    <?php foreach ($assignments as $assignment): ?>
                        <tr>
                            <td><?php echo $assignment['title']; ?></td>
                            <td><?php echo $assignment['class_name']; ?></td>
                            <td><?php echo $assignment['subject_name']; ?></td>
                            <td><?php echo date('M j, Y g:i A', strtotime($assignment['due_date'])); ?></td>
                            <td>
                                <?php if ($assignment['file_path']): ?>
                                    <a href="../<?php echo $assignment['file_path']; ?>" target="_blank" class="btn btn-sm btn-info">Download</a>
                                <?php else: ?>
                                    No file
                                <?php endif; ?>
                            </td>
                            <td>
                                <a href="view_submissions.php?assignment_id=<?php echo $assignment['id']; ?>" 
                                   class="btn btn-info btn-sm">
                                   View Submissions (<?php echo $assignment['submission_count']; ?>)
                                </a>
                            </td>
                            <td>
                                <a href="assignments.php?edit=<?php echo $assignment['id']; ?>" class="btn btn-sm btn-primary">Edit</a>
                                <form method="POST" class="inline-form">
                                    <input type="hidden" name="assignment_id" value="<?php echo $assignment['id']; ?>">
                                    <button type="submit" name="delete_assignment" class="btn btn-sm btn-danger" 
                                            onclick="return confirm('Are you sure you want to delete this assignment?')">Delete</button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="7">No assignments found.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</main>

<style>
.alert {
    padding: 15px;
    margin-bottom: 20px;
    border: 1px solid transparent;
    border-radius: 4px;
}

.alert-success {
    color: #155724;
    background-color: #d4edda;
    border-color: #c3e6cb;
}

.alert-error {
    color: #721c24;
    background-color: #f8d7da;
    border-color: #f5c6cb;
}

.btn-sm {
    padding: 5px 10px;
    font-size: 0.875rem;
}

.inline-form {
    display: inline-block;
    margin-left: 5px;
}

.form-container {
    background: white;
    padding: 20px;
    border-radius: 8px;
    margin-bottom: 30px;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}

.table-container {
    background: white;
    padding: 20px;
    border-radius: 8px;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}

table {
    width: 100%;
    border-collapse: collapse;
}

table th, table td {
    padding: 12px;
    text-align: left;
    border-bottom: 1px solid #ddd;
}

table th {
    background-color: #f8f9fa;
    font-weight: bold;
}

.form-group {
    margin-bottom: 20px;
}

.form-group label {
    display: block;
    margin-bottom: 5px;
    font-weight: bold;
}

.form-group input, .form-group select, .form-group textarea {
    width: 100%;
    padding: 10px;
    border: 1px solid #ddd;
    border-radius: 4px;
    box-sizing: border-box;
}

.btn {
    padding: 10px 20px;
    border: none;
    border-radius: 4px;
    cursor: pointer;
    text-decoration: none;
    display: inline-block;
    text-align: center;
}

.btn-primary {
    background-color: #007bff;
    color: white;
}

.btn-secondary {
    background-color: #6c757d;
    color: white;
}

.btn-info {
    background-color: #17a2b8;
    color: white;
}

.btn-danger {
    background-color: #dc3545;
    color: white;
}

.btn:hover {
    opacity: 0.9;
}
select[name="term"],
select[name="academic_year"] {
    width: 200px;
    padding: 8px 12px;
    margin: 5px;
    border: 1px solid #ccc;
    border-radius: 4px;
    background: white;
    font-size: 14px;
}

select[name="term"]:focus,
select[name="academic_year"]:focus {
    outline: none;
    border-color: #007bff;
    box-shadow: 0 0 3px rgba(0,123,255,0.3);
}

</style>

<?php include '../includes/footer.php'; ?>