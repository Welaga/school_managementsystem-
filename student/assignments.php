<?php
require_once '../config.php';
checkRole(['admin', 'student']);

$page_title = "My Assignments";

// Get student details
$student = getStudentDetails($_SESSION['user_id']);
$student_id = $student['id'];
$class_id = $student['class_id'];

// Handle form actions
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['submit_assignment'])) {
        $assignment_id = $_POST['assignment_id'];
        $submission_text = trim($_POST['submission_text']);
        
        // File upload handling
        $file_path = null;
        if (isset($_FILES['submission_file']) && $_FILES['submission_file']['error'] == UPLOAD_ERR_OK) {
            $upload_dir = '../uploads/submissions/';
            if (!file_exists($upload_dir)) {
                mkdir($upload_dir, 0777, true);
            }
            
            $file_name = time() . '_' . basename($_FILES['submission_file']['name']);
            $target_path = $upload_dir . $file_name;
            
            if (move_uploaded_file($_FILES['submission_file']['tmp_name'], $target_path)) {
                $file_path = 'uploads/submissions/' . $file_name;
            }
        }
        
        try {
            // Check if submission already exists
            $stmt = $pdo->prepare("SELECT id FROM student_assignments WHERE assignment_id = ? AND student_id = ?");
            $stmt->execute([$assignment_id, $student_id]);
            $existing_submission = $stmt->fetch();
            
            if ($existing_submission) {
                // Update existing submission
                $stmt = $pdo->prepare("UPDATE student_assignments SET submission = ?, file_path = ?, submitted_at = NOW() 
                                      WHERE id = ?");
                $stmt->execute([$submission_text, $file_path, $existing_submission['id']]);
            } else {
                // Insert new submission
                $stmt = $pdo->prepare("INSERT INTO student_assignments (assignment_id, student_id, submission, file_path) 
                                      VALUES (?, ?, ?, ?)");
                $stmt->execute([$assignment_id, $student_id, $submission_text, $file_path]);
            }
            
            $message = "Assignment submitted successfully!";
        } catch (PDOException $e) {
            $message = "Error submitting assignment: " . $e->getMessage();
        }
    }
}

// Get all assignments for student's class
$stmt = $pdo->prepare("SELECT a.*, s.name as subject_name, 
                      (SELECT COUNT(*) FROM student_assignments sa WHERE sa.assignment_id = a.id AND sa.student_id = ?) as submitted
                      FROM assignments a 
                      JOIN subjects s ON a.subject_id = s.id 
                      WHERE a.class_id = ? 
                      ORDER BY a.due_date DESC");
$stmt->execute([$student_id, $class_id]);
$assignments = $stmt->fetchAll();

// Get assignment for submission
$submit_assignment = null;
if (isset($_GET['submit'])) {
    $stmt = $pdo->prepare("SELECT a.*, s.name as subject_name FROM assignments a 
                          JOIN subjects s ON a.subject_id = s.id 
                          WHERE a.id = ?");
    $stmt->execute([$_GET['submit']]);
    $submit_assignment = $stmt->fetch();
    
    // Get existing submission if any
    $stmt = $pdo->prepare("SELECT * FROM student_assignments WHERE assignment_id = ? AND student_id = ?");
    $stmt->execute([$_GET['submit'], $student_id]);
    $existing_submission = $stmt->fetch();
}
?>

<?php include '../includes/header.php'; ?>
<?php include '../includes/sidebar.php'; ?>

<main class="main-content">
    <div class="content-header">
        <h1>My Assignments</h1>
        <p>Welcome, <?php echo $student['first_name'] . ' ' . $student['last_name']; ?> (Class: <?php echo $student['class_name']; ?>)</p>
    </div>
    
    <?php if (!empty($message)): ?>
        <div class="alert <?php echo strpos($message, 'Error') !== false ? 'alert-error' : 'alert-success'; ?>">
            <?php echo $message; ?>
        </div>
    <?php endif; ?>
    
    <?php if ($submit_assignment): ?>
        <div class="form-container">
            <h2>Submit Assignment: <?php echo $submit_assignment['title']; ?></h2>
            <p><strong>Subject:</strong> <?php echo $submit_assignment['subject_name']; ?></p>
            <p><strong>Due Date:</strong> <?php echo date('M j, Y g:i A', strtotime($submit_assignment['due_date'])); ?></p>
            <?php if ($submit_assignment['description']): ?>
                <p><strong>Description:</strong> <?php echo $submit_assignment['description']; ?></p>
            <?php endif; ?>
            <?php if ($submit_assignment['file_path']): ?>
                <p><strong>Assignment File:</strong> <a href="../<?php echo $submit_assignment['file_path']; ?>" target="_blank">Download</a></p>
            <?php endif; ?>
            
            <form method="POST" enctype="multipart/form-data">
                <input type="hidden" name="assignment_id" value="<?php echo $submit_assignment['id']; ?>">
                
                <div class="form-group">
                    <label for="submission_text">Your Submission (Text)</label>
                    <textarea id="submission_text" name="submission_text" rows="6" 
                              placeholder="Type your assignment submission here..."><?php echo $existing_submission ? $existing_submission['submission'] : ''; ?></textarea>
                </div>
                
                <div class="form-group">
                    <label for="submission_file">Submission File (Optional)</label>
                    <input type="file" id="submission_file" name="submission_file">
                    <?php if ($existing_submission && $existing_submission['file_path']): ?>
                        <p>Current file: <a href="../<?php echo $existing_submission['file_path']; ?>" target="_blank">Download</a></p>
                    <?php endif; ?>
                </div>
                
                <div class="form-group">
                    <button type="submit" name="submit_assignment" class="btn btn-primary">
                        <?php echo $existing_submission ? 'Update Submission' : 'Submit Assignment'; ?>
                    </button>
                    <a href="assignments.php" class="btn btn-secondary">Cancel</a>
                </div>
            </form>
        </div>
    <?php else: ?>
        <div class="table-container">
            <h2>All Assignments</h2>
            <table>
                <thead>
                    <tr>
                        <th>Title</th>
                        <th>Subject</th>
                        <th>Due Date</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (count($assignments) > 0): ?>
                        <?php foreach ($assignments as $assignment): ?>
                            <tr>
                                <td><?php echo $assignment['title']; ?></td>
                                <td><?php echo $assignment['subject_name']; ?></td>
                                <td><?php echo date('M j, Y g:i A', strtotime($assignment['due_date'])); ?></td>
                                <td>
                                    <span class="status-badge <?php echo $assignment['submitted'] ? 'completed' : 'pending'; ?>">
                                        <?php echo $assignment['submitted'] ? 'Submitted' : 'Pending'; ?>
                                    </span>
                                </td>
                                <td>
                                    <a href="assignments.php?submit=<?php echo $assignment['id']; ?>" class="btn btn-sm btn-primary">
                                        <?php echo $assignment['submitted'] ? 'View/Edit' : 'Submit'; ?>
                                    </a>
                                    <?php if ($assignment['file_path']): ?>
                                        <a href="../<?php echo $assignment['file_path']; ?>" target="_blank" class="btn btn-sm btn-info">Download</a>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="5">No assignments found.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
</main>

<?php include '../includes/footer.php'; ?>