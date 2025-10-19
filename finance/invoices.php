<?php
require_once '../config.php';
checkRole(['admin', 'finance']);

$page_title = "Invoices";
$message = '';

// Handle form actions
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['generate_invoices'])) {
        $class_id = $_POST['class_id'];
        $term = $_POST['term'];
        $academic_year = $_POST['academic_year'];
        $amount = $_POST['amount'];
        $due_date = $_POST['due_date'];
        
        try {
            // Get all students in the selected class
            $stmt = $pdo->prepare("SELECT id FROM students WHERE class_id = ?");
            $stmt->execute([$class_id]);
            $students = $stmt->fetchAll();
            
            // Generate invoice for each student
            $count = 0;
            foreach ($students as $student) {
                // Check if invoice already exists
                $stmt = $pdo->prepare("SELECT id FROM fees WHERE student_id = ? AND term = ? AND academic_year = ?");
                $stmt->execute([$student['id'], $term, $academic_year]);
                $existing_invoice = $stmt->fetch();
                
                if (!$existing_invoice) {
                    $stmt = $pdo->prepare("INSERT INTO fees (student_id, amount, term, academic_year, due_date) 
                                          VALUES (?, ?, ?, ?, ?)");
                    $stmt->execute([$student['id'], $amount, $term, $academic_year, $due_date]);
                    $count++;
                }
            }
            
            $message = "Generated $count new invoices for $term $academic_year!";
        } catch (PDOException $e) {
            $message = "Error generating invoices: " . $e->getMessage();
        }
    }
}

// Get all invoices with student information
$stmt = $pdo->query("SELECT f.*, s.first_name, s.last_name, s.class_id, c.name as class_name 
                    FROM fees f 
                    JOIN students s ON f.student_id = s.id 
                    LEFT JOIN classes c ON s.class_id = c.id 
                    ORDER BY f.created_at DESC");
$invoices = $stmt->fetchAll();

// Get classes for dropdown
$stmt = $pdo->query("SELECT * FROM classes ORDER BY name");
$classes = $stmt->fetchAll();
?>

<?php include '../includes/header.php'; ?>
<?php include '../includes/sidebar.php'; ?>

<main class="main-content">
    <div class="content-header">
        <h1>Invoices</h1>
    </div>
    
    <?php if (!empty($message)): ?>
        <div class="alert <?php echo strpos($message, 'Error') !== false ? 'alert-error' : 'alert-success'; ?>">
            <?php echo $message; ?>
        </div>
    <?php endif; ?>
    
    <div class="form-container">
        <h2>Generate Invoices</h2>
        <form method="POST">
            <div class="form-group">
                <label for="class_id">Class</label>
                <select id="class_id" name="class_id" required>
                    <option value="">Select Class</option>
                    <?php foreach ($classes as $class): ?>
                        <option value="<?php echo $class['id']; ?>"><?php echo $class['name']; ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            
            <div class="form-group">
                <label for="term">Term</label>
                <select id="term" name="term" required>
                    <option value="">Select Term</option>
                    <option value="1st Term">1st Term</option>
                    <option value="2nd Term">2nd Term</option>
                    <option value="3rd Term">3rd Term</option>
                    <option value="Annual">Annual</option>
                </select>
            </div>
            
            <div class="form-group">
                <label for="academic_year">Academic Year</label>
                <input type="text" id="academic_year" name="academic_year" placeholder="e.g., 2023-2024" required>
            </div>
            
            <div class="form-group">
                <label for="amount">Amount</label>
                <input type="number" id="amount" name="amount" step="0.01" min="0" required>
            </div>
            
            <div class="form-group">
                <label for="due_date">Due Date</label>
                <input type="date" id="due_date" name="due_date" required>
            </div>
            
            <div class="form-group">
                <button type="submit" name="generate_invoices" class="btn btn-primary">Generate Invoices</button>
            </div>
        </form>
    </div>
    
    <div class="table-container">
        <h2>All Invoices</h2>
        <div class="filter-section">
            <form method="GET" class="filter-form">
                <div class="form-group">
                    <label for="filter_status">Status</label>
                    <select id="filter_status" name="status" onchange="this.form.submit()">
                        <option value="">All Statuses</option>
                        <option value="pending" <?php echo isset($_GET['status']) && $_GET['status'] == 'pending' ? 'selected' : ''; ?>>Pending</option>
                        <option value="paid" <?php echo isset($_GET['status']) && $_GET['status'] == 'paid' ? 'selected' : ''; ?>>Paid</option>
                        <option value="overdue" <?php echo isset($_GET['status']) && $_GET['status'] == 'overdue' ? 'selected' : ''; ?>>Overdue</option>
                    </select>
                </div>
                
                <div class="form-group">
                    <label for="filter_class">Class</label>
                    <select id="filter_class" name="class_id" onchange="this.form.submit()">
                        <option value="">All Classes</option>
                        <?php foreach ($classes as $class): ?>
                            <option value="<?php echo $class['id']; ?>" 
                                <?php echo isset($_GET['class_id']) && $_GET['class_id'] == $class['id'] ? 'selected' : ''; ?>>
                                <?php echo $class['name']; ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                
                <div class="form-group">
                    <label for="filter_term">Term</label>
                    <select id="filter_term" name="term" onchange="this.form.submit()">
                        <option value="">All Terms</option>
                        <option value="1st Term" <?php echo isset($_GET['term']) && $_GET['term'] == '1st Term' ? 'selected' : ''; ?>>1st Term</option>
                        <option value="2nd Term" <?php echo isset($_GET['term']) && $_GET['term'] == '2nd Term' ? 'selected' : ''; ?>>2nd Term</option>
                        <option value="3rd Term" <?php echo isset($_GET['term']) && $_GET['term'] == '3rd Term' ? 'selected' : ''; ?>>3rd Term</option>
                        <option value="Annual" <?php echo isset($_GET['term']) && $_GET['term'] == 'Annual' ? 'selected' : ''; ?>>Annual</option>
                    </select>
                </div>
            </form>
        </div>
        
        <table>
            <thead>
                <tr>
                    <th>Invoice ID</th>
                    <th>Student</th>
                    <th>Class</th>
                    <th>Amount</th>
                    <th>Term</th>
                    <th>Academic Year</th>
                    <th>Due Date</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php 
                // Filter invoices based on query parameters
                $filtered_invoices = $invoices;
                if (isset($_GET['status']) && !empty($_GET['status'])) {
                    $filtered_invoices = array_filter($filtered_invoices, function($invoice) {
                        return $invoice['status'] == $_GET['status'];
                    });
                }
                if (isset($_GET['class_id']) && !empty($_GET['class_id'])) {
                    $filtered_invoices = array_filter($filtered_invoices, function($invoice) {
                        return $invoice['class_id'] == $_GET['class_id'];
                    });
                }
                if (isset($_GET['term']) && !empty($_GET['term'])) {
                    $filtered_invoices = array_filter($filtered_invoices, function($invoice) {
                        return $invoice['term'] == $_GET['term'];
                    });
                }
                ?>
                
                <?php if (count($filtered_invoices) > 0): ?>
                    <?php foreach ($filtered_invoices as $invoice): ?>
                        <tr>
                            <td><?php echo $invoice['id']; ?></td>
                            <td><?php echo $invoice['first_name'] . ' ' . $invoice['last_name']; ?></td>
                            <td><?php echo $invoice['class_name'] ? $invoice['class_name'] : 'N/A'; ?></td>
                            <td>$<?php echo number_format($invoice['amount'], 2); ?></td>
                            <td><?php echo $invoice['term']; ?></td>
                            <td><?php echo $invoice['academic_year']; ?></td>
                            <td><?php echo date('M j, Y', strtotime($invoice['due_date'])); ?></td>
                            <td>
                                <span class="status-badge <?php echo $invoice['status']; ?>">
                                    <?php echo ucfirst($invoice['status']); ?>
                                </span>
                            </td>
                            <td>
                                <a href="fee_management.php?payment=<?php echo $invoice['id']; ?>" class="btn btn-sm btn-success">Record Payment</a>
                                <a href="fee_management.php?edit=<?php echo $invoice['id']; ?>" class="btn btn-sm btn-primary">Edit</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="9">No invoices found.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</main>

<?php include '../includes/footer.php'; ?>