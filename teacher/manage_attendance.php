<?php
require_once '../config.php';
checkRole(['admin', 'teacher']);

$page_title = "Manage Attendance";
$message = '';

// Get teacher details
$teacher = getTeacherDetails($_SESSION['user_id']);
$teacher_id = $teacher['id'];

// Get classes assigned to teacher
$stmt = $pdo->prepare("SELECT DISTINCT c.* FROM classes c 
                      JOIN class_subjects cs ON c.id = cs.class_id 
                      WHERE cs.teacher_id = ? ORDER BY c.name");
$stmt->execute([$teacher_id]);
$classes = $stmt->fetchAll();

// Handle form actions
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['mark_attendance'])) {
        $class_id = $_POST['class_id'];
        $subject_id = $_POST['subject_id'];
        $date = $_POST['date'];
        
        // Process attendance for each student
        foreach ($_POST['attendance'] as $student_id => $status) {
            try {
                // Check if attendance already exists
                $stmt = $pdo->prepare("SELECT id FROM attendance WHERE student_id = ? AND class_id = ? AND subject_id = ? AND date = ?");
                $stmt->execute([$student_id, $class_id, $subject_id, $date]);
                $existing_attendance = $stmt->fetch();
                
                if ($existing_attendance) {
                    // Update existing attendance
                    $stmt = $pdo->prepare("UPDATE attendance SET status = ? WHERE id = ?");
                    $stmt->execute([$status, $existing_attendance['id']]);
                } else {
                    // Insert new attendance
                    $stmt = $pdo->prepare("INSERT INTO attendance (student_id, class_id, subject_id, date, status) 
                                          VALUES (?, ?, ?, ?, ?)");
                    $stmt->execute([$student_id, $class_id, $subject_id, $date, $status]);
                }
            } catch (PDOException $e) {
                $message = "Error saving attendance: " . $e->getMessage();
                break;
            }
        }
        
        if (empty($message)) {
            $message = "Attendance marked successfully!";
        }
    }
}

// Get students based on selected class
$students = [];
$subjects = [];
$existing_attendance = [];

if (isset($_GET['class_id']) && !empty($_GET['class_id'])) {
    $class_id = $_GET['class_id'];
    
    // Get students in class
    $stmt = $pdo->prepare("SELECT * FROM students WHERE class_id = ? ORDER BY first_name, last_name");
    $stmt->execute([$class_id]);
    $students = $stmt->fetchAll();
    
    // Get subjects taught by teacher in this class
    $stmt = $pdo->prepare("SELECT s.* FROM subjects s 
                          JOIN class_subjects cs ON s.id = cs.subject_id 
                          WHERE cs.class_id = ? AND cs.teacher_id = ? ORDER BY s.name");
    $stmt->execute([$class_id, $teacher_id]);
    $subjects = $stmt->fetchAll();
    
    // Get existing attendance if date is selected
    if (isset($_GET['date']) && !empty($_GET['date']) && isset($_GET['subject_id']) && !empty($_GET['subject_id'])) {
        $date = $_GET['date'];
        $subject_id = $_GET['subject_id'];
        
        $stmt = $pdo->prepare("SELECT student_id, status FROM attendance 
                              WHERE class_id = ? AND subject_id = ? AND date = ?");
        $stmt->execute([$class_id, $subject_id, $date]);
        $attendance_records = $stmt->fetchAll();
        
        foreach ($attendance_records as $record) {
            $existing_attendance[$record['student_id']] = $record['status'];
        }
    }
}
?>

<?php include '../includes/header.php'; ?>
<?php include '../includes/sidebar.php'; ?>

<main class="main-content">
    <div class="content-header">
        <h1>Manage Attendance</h1>
        <p>Welcome, <?php echo $teacher['first_name'] . ' ' . $teacher['last_name']; ?></p>
    </div>
    
    <?php if (!empty($message)): ?>
        <div class="alert <?php echo strpos($message, 'Error') !== false ? 'alert-error' : 'alert-success'; ?>">
            <?php echo $message; ?>
        </div>
    <?php endif; ?>
    
    <div class="form-container">
        <h2>Select Class and Date</h2>
        <form method="GET">
            <div class="form-group">
                <label for="class_id">Class</label>
                <select id="class_id" name="class_id" required onchange="this.form.submit()">
                    <option value="">Select Class</option>
                    <?php foreach ($classes as $class): ?>
                        <option value="<?php echo $class['id']; ?>" 
                            <?php echo isset($_GET['class_id']) && $_GET['class_id'] == $class['id'] ? 'selected' : ''; ?>>
                            <?php echo $class['name']; ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            
            <?php if (isset($_GET['class_id']) && !empty($_GET['class_id'])): ?>
                <div class="form-group">
                    <label for="subject_id">Subject</label>
                    <select id="subject_id" name="subject_id" required onchange="this.form.submit()">
                        <option value="">Select Subject</option>
                        <?php foreach ($subjects as $subject): ?>
                            <option value="<?php echo $subject['id']; ?>" 
                                <?php echo isset($_GET['subject_id']) && $_GET['subject_id'] == $subject['id'] ? 'selected' : ''; ?>>
                                <?php echo $subject['name']; ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                
                <?php if (isset($_GET['subject_id']) && !empty($_GET['subject_id'])): ?>
                    <div class="form-group">
                        <label for="date">Date</label>
                        <input type="date" id="date" name="date" required 
                               value="<?php echo isset($_GET['date']) ? $_GET['date'] : date('Y-m-d'); ?>" 
                               onchange="this.form.submit()">
                    </div>
                <?php endif; ?>
            <?php endif; ?>
        </form>
    </div>
    
    <?php if (isset($_GET['class_id']) && !empty($_GET['class_id']) && 
              isset($_GET['subject_id']) && !empty($_GET['subject_id']) &&
              isset($_GET['date']) && !empty($_GET['date'])): ?>
        
        <div class="form-container">
            <h2>Mark Attendance for <?php echo date('M j, Y', strtotime($_GET['date'])); ?></h2>
            <form method="POST">
                <input type="hidden" name="class_id" value="<?php echo $_GET['class_id']; ?>">
                <input type="hidden" name="subject_id" value="<?php echo $_GET['subject_id']; ?>">
                <input type="hidden" name="date" value="<?php echo $_GET['date']; ?>">
                
                <div class="table-container">
                    <table>
                        <thead>
                            <tr>
                                <th>Student ID</th>
                                <th>Student Name</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (count($students) > 0): ?>
                                <?php foreach ($students as $student): ?>
                                    <tr>
                                        <td><?php echo $student['id']; ?></td>
                                        <td><?php echo $student['first_name'] . ' ' . $student['last_name']; ?></td>
                                        <td>
                                            <select name="attendance[<?php echo $student['id']; ?>]" required>
                                                <option value="present" <?php echo isset($existing_attendance[$student['id']]) && $existing_attendance[$student['id']] == 'present' ? 'selected' : ''; ?>>Present</option>
                                                <option value="absent" <?php echo isset($existing_attendance[$student['id']]) && $existing_attendance[$student['id']] == 'absent' ? 'selected' : ''; ?>>Absent</option>
                                                <option value="late" <?php echo isset($existing_attendance[$student['id']]) && $existing_attendance[$student['id']] == 'late' ? 'selected' : ''; ?>>Late</option>
                                            </select>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="3">No students found in this class.</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
                
                <?php if (count($students) > 0): ?>
                    <div class="form-group">
                        <button type="submit" name="mark_attendance" class="btn btn-primary">Save Attendance</button>
                    </div>
                <?php endif; ?>
            </form>
        </div>
    <?php endif; ?>
</main>

<?php include '../includes/footer.php'; ?>