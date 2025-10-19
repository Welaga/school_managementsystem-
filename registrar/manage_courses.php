<?php
require_once '../config.php';
checkRole(['admin', 'registrar']);

$page_title = "Manage Courses";
$message = '';

// Handle form actions
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['add_course'])) {
        $name = trim($_POST['name']);
        $code = trim($_POST['code']);
        $description = trim($_POST['description']);
        
        try {
            $stmt = $pdo->prepare("INSERT INTO courses (name, code, description) VALUES (?, ?, ?)");
            $stmt->execute([$name, $code, $description]);
            $message = "Course added successfully!";
        } catch (PDOException $e) {
            $message = "Error adding course: " . $e->getMessage();
        }
    } elseif (isset($_POST['update_course'])) {
        $course_id = $_POST['course_id'];
        $name = trim($_POST['name']);
        $code = trim($_POST['code']);
        $description = trim($_POST['description']);
        
        try {
            $stmt = $pdo->prepare("UPDATE courses SET name = ?, code = ?, description = ? WHERE id = ?");
            $stmt->execute([$name, $code, $description, $course_id]);
            $message = "Course updated successfully!";
        } catch (PDOException $e) {
            $message = "Error updating course: " . $e->getMessage();
        }
    } elseif (isset($_POST['delete_course'])) {
        $course_id = $_POST['course_id'];
        
        try {
            $stmt = $pdo->prepare("DELETE FROM courses WHERE id = ?");
            $stmt->execute([$course_id]);
            $message = "Course deleted successfully!";
        } catch (PDOException $e) {
            $message = "Error deleting course: " . $e->getMessage();
        }
    }
}

// Get all courses
$stmt = $pdo->query("SELECT * FROM courses ORDER BY name");
$courses = $stmt->fetchAll();

// Get course for editing
$edit_course = null;
if (isset($_GET['edit'])) {
    $stmt = $pdo->prepare("SELECT * FROM courses WHERE id = ?");
    $stmt->execute([$_GET['edit']]);
    $edit_course = $stmt->fetch();
}
?>

<?php include '../includes/header.php'; ?>
<?php include '../includes/sidebar.php'; ?>

<main class="main-content">
    <div class="content-header">
        <h1>Manage Courses</h1>
    </div>
    
    <?php if (!empty($message)): ?>
        <div class="alert <?php echo strpos($message, 'Error') !== false ? 'alert-error' : 'alert-success'; ?>">
            <?php echo $message; ?>
        </div>
    <?php endif; ?>
    
    <div class="form-container">
        <h2><?php echo $edit_course ? 'Edit Course' : 'Add New Course'; ?></h2>
        <form method="POST">
            <?php if ($edit_course): ?>
                <input type="hidden" name="course_id" value="<?php echo $edit_course['id']; ?>">
            <?php endif; ?>
            
            <div class="form-group">
                <label for="name">Course Name</label>
                <input type="text" id="name" name="name" required 
                       value="<?php echo $edit_course ? $edit_course['name'] : ''; ?>">
            </div>
            
            <div class="form-group">
                <label for="code">Course Code</label>
                <input type="text" id="code" name="code" required 
                       value="<?php echo $edit_course ? $edit_course['code'] : ''; ?>">
            </div>
            
            <div class="form-group">
                <label for="description">Description</label>
                <textarea id="description" name="description"><?php echo $edit_course ? $edit_course['description'] : ''; ?></textarea>
            </div>
            
            <div class="form-group">
                <?php if ($edit_course): ?>
                    <button type="submit" name="update_course" class="btn btn-primary">Update Course</button>
                    <a href="manage_courses.php" class="btn btn-secondary">Cancel</a>
                <?php else: ?>
                    <button type="submit" name="add_course" class="btn btn-primary">Add Course</button>
                <?php endif; ?>
            </div>
        </form>
    </div>
    
    <div class="table-container">
        <h2>All Courses</h2>
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Name</th>
                    <th>Code</th>
                    <th>Description</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if (count($courses) > 0): ?>
                    <?php foreach ($courses as $course): ?>
                        <tr>
                            <td><?php echo $course['id']; ?></td>
                            <td><?php echo $course['name']; ?></td>
                            <td><?php echo $course['code']; ?></td>
                            <td><?php echo $course['description'] ? substr($course['description'], 0, 50) . '...' : 'N/A'; ?></td>
                            <td>
                                <a href="manage_courses.php?edit=<?php echo $course['id']; ?>" class="btn btn-sm btn-primary">Edit</a>
                                <form method="POST" class="inline-form">
                                    <input type="hidden" name="course_id" value="<?php echo $course['id']; ?>">
                                    <button type="submit" name="delete_course" class="btn btn-sm btn-danger" 
                                            onclick="return confirm('Are you sure you want to delete this course?')">Delete</button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="5">No courses found.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</main>

<?php include '../includes/footer.php'; ?>