<?php
require_once '../config.php';
checkRole(['admin', 'teacher']);

$page_title = "Upload Grades";
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

// Get subjects assigned to teacher
$stmt = $pdo->prepare("SELECT DISTINCT s.* FROM subjects s 
                      JOIN class_subjects cs ON s.id = cs.subject_id 
                      WHERE cs.teacher_id = ? ORDER BY s.name");
$stmt->execute([$teacher_id]);
$subjects = $stmt->fetchAll();

// Handle form actions
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['upload_grades'])) {
        $class_id = $_POST['class_id'];
        $subject_id = $_POST['subject_id'];
        $term = $_POST['term'];
        $academic_year = $_POST['academic_year'];
        
        // Process grades for each student
        foreach ($_POST['grades'] as $student_id => $grade_value) {
            if (!empty($grade_value)) {
                try {
                    // Check if grade already exists
                    $stmt = $pdo->prepare("SELECT id FROM grades WHERE student_id = ? AND subject_id = ? AND term = ? AND academic_year = ?");
                    $stmt->execute([$student_id, $subject_id, $term, $academic_year]);
                    $existing_grade = $stmt->fetch();
                    
                    if ($existing_grade) {
                        // Update existing grade
                        $stmt = $pdo->prepare("UPDATE grades SET grade = ? WHERE id = ?");
                        $stmt->execute([$grade_value, $existing_grade['id']]);
                    } else {
                        // Insert new grade
                        $stmt = $pdo->prepare("INSERT INTO grades (student_id, subject_id, grade, term, academic_year) 
                                              VALUES (?, ?, ?, ?, ?)");
                        $stmt->execute([$student_id, $subject_id, $grade_value, $term, $academic_year]);
                    }
                } catch (PDOException $e) {
                    $message = "Error saving grades: " . $e->getMessage();
                    break;
                }
            }
        }
        
        if (empty($message)) {
            $message = "Grades uploaded successfully!";
        }
    }
}

// Get students based on selected class
$students = [];
if (isset($_GET['class_id']) && !empty($_GET['class_id'])) {
    $class_id = $_GET['class_id'];
    $stmt = $pdo->prepare("SELECT * FROM students WHERE class_id = ? ORDER BY first_name, last_name");
    $stmt->execute([$class_id]);
    $students = $stmt->fetchAll();
    
    // Get existing grades if subject and term are selected
    $existing_grades = [];
    if (isset($_GET['subject_id']) && !empty($_GET['subject_id']) && isset($_GET['term']) && !empty($_GET['term']) && isset($_GET['academic_year']) && !empty($_GET['academic_year'])) {
        $subject_id = $_GET['subject_id'];
        $term = $_GET['term'];
        $academic_year = $_GET['academic_year'];
        
        $stmt = $pdo->prepare("SELECT student_id, grade FROM grades WHERE subject_id = ? AND term = ? AND academic_year = ?");
        $stmt->execute([$subject_id, $term, $academic_year]);
        $grades = $stmt->fetchAll();
        
        foreach ($grades as $grade) {
            $existing_grades[$grade['student_id']] = $grade['grade'];
        }
    }
}
?>

<?php include '../includes/header.php'; ?>
<?php include '../includes/sidebar.php'; ?>

<main class="main-content">
    <div class="content-header">
        <h1>Upload Grades</h1>
        <p>Welcome, <?php echo $teacher['first_name'] . ' ' . $teacher['last_name']; ?></p>
    </div>
    
    <?php if (!empty($message)): ?>
        <div class="alert <?php echo strpos($message, 'Error') !== false ? 'alert-error' : 'alert-success'; ?>">
            <?php echo $message; ?>
        </div>
    <?php endif; ?>
    
    <div class="form-container">
        <h2>Select Class and Subject</h2>
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
                        <label for="term">Term</label>
                        <select id="term" name="term" required onchange="this.form.submit()">
                            <option value="">Select Term</option>
                            <option value="1st Term" <?php echo isset($_GET['term']) && $_GET['term'] == '1st Term' ? 'selected' : ''; ?>>1st Term</option>
                            <option value="2nd Term" <?php echo isset($_GET['term']) && $_GET['term'] == '2nd Term' ? 'selected' : ''; ?>>2nd Term</option>
                            <option value="3rd Term" <?php echo isset($_GET['term']) && $_GET['term'] == '3rd Term' ? 'selected' : ''; ?>>3rd Term</option>
                        </select>
                    </div>
                    
                    <?php if (isset($_GET['term']) && !empty($_GET['term'])): ?>
                        <div class="form-group">
                            <label for="academic_year">Academic Year</label>
                            <input type="text" id="academic_year" name="academic_year" placeholder="e.g., 2023-2024" required 
                                   value="<?php echo isset($_GET['academic_year']) ? $_GET['academic_year'] : ''; ?>" onchange="this.form.submit()">
                        </div>
                    <?php endif; ?>
                <?php endif; ?>
            <?php endif; ?>
        </form>
    </div>
    
    <?php if (isset($_GET['class_id']) && !empty($_GET['class_id']) && 
              isset($_GET['subject_id']) && !empty($_GET['subject_id']) &&
              isset($_GET['term']) && !empty($_GET['term']) &&
              isset($_GET['academic_year']) && !empty($_GET['academic_year'])): ?>
        
        <div class="form-container">
            <h2>Enter Grades for <?php echo $_GET['academic_year']; ?> - <?php echo $_GET['term']; ?></h2>
            <form method="POST">
                <input type="hidden" name="class_id" value="<?php echo $_GET['class_id']; ?>">
                <input type="hidden" name="subject_id" value="<?php echo $_GET['subject_id']; ?>">
                <input type="hidden" name="term" value="<?php echo $_GET['term']; ?>">
                <input type="hidden" name="academic_year" value="<?php echo $_GET['academic_year']; ?>">
                
                <div class="table-container">
                    <table>
                        <thead>
                            <tr>
                                <th>Student ID</th>
                                <th>Student Name</th>
                                <th>Grade</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (count($students) > 0): ?>
                                <?php foreach ($students as $student): ?>
                                    <tr>
                                        <td><?php echo $student['id']; ?></td>
                                        <td><?php echo $student['first_name'] . ' ' . $student['last_name']; ?></td>
                                        <td>
                                            <input type="number" name="grades[<?php echo $student['id']; ?>]" 
                                                   step="0.01" min="0" max="100" 
                                                   value="<?php echo isset($existing_grades[$student['id']]) ? $existing_grades[$student['id']] : ''; ?>"
                                                   placeholder="Enter grade">
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
                        <button type="submit" name="upload_grades" class="btn btn-primary">Upload Grades</button>
                    </div>
                <?php endif; ?>
            </form>
        </div>
    <?php endif; ?>
</main>

<?php include '../includes/footer.php'; ?>