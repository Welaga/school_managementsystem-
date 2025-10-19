<?php
require_once '../config.php';
checkRole(['admin', 'finance']);

$page_title = "Fee Management";
$message = '';

// Handle form actions
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['add_fee'])) {
        $student_id = $_POST['student_id'];
        $amount = $_POST['amount'];
        $term = $_POST['term'];
        $academic_year = $_POST['academic_year'];
        $due_date = $_POST['due_date'];
        
        try {
            $stmt = $pdo->prepare("INSERT INTO fees (student_id, amount, term, academic_year, due_date) 
                                  VALUES (?, ?, ?, ?, ?)");
            $stmt->execute([$student_id, $amount, $term, $academic_year, $due_date]);
            $message = "Fee record added successfully!";
        } catch (PDOException $e) {
            $message = "Error adding fee record: " . $e->getMessage();
        }
    } elseif (isset($_POST['update_fee'])) {
        $fee_id = $_POST['fee_id'];
        $student_id = $_POST['student_id'];
        $amount = $_POST['amount'];
        $term = $_POST['term'];
        $academic_year = $_POST['academic_year'];
        $due_date = $_POST['due_date'];
        $status = $_POST['status'];
        
        try {
            $stmt = $pdo->prepare("UPDATE fees SET student_id = ?, amount = ?, term = ?, academic_year = ?, 
                                  due_date = ?, status = ? WHERE id = ?");
            $stmt->execute([$student_id, $amount, $term, $academic_year, $due_date, $status, $fee_id]);
            $message = "Fee record updated successfully!";
        } catch (PDOException $e) {
            $message = "Error updating fee record: " . $e->getMessage();
        }
    } elseif (isset($_POST['delete_fee'])) {
        $fee_id = $_POST['fee_id'];
        
        try {
            $stmt = $pdo->prepare("DELETE FROM fees WHERE id = ?");
            $stmt->execute([$fee_id]);
            $message = "Fee record deleted successfully!";
        } catch (PDOException $e) {
            $message = "Error deleting fee record: " . $e->getMessage();
        }
    } elseif (isset($_POST['record_payment'])) {
        $fee_id = $_POST['fee_id'];
        $amount_paid = $_POST['amount_paid'];
        $payment_date = $_POST['payment_date'];
        $payment_method = $_POST['payment_method'];
        $transaction_id = $_POST['transaction_id'];
        
        try {
            // Record payment
            $stmt = $pdo->prepare("INSERT INTO payments (fee_id, amount_paid, payment_date, payment_method, transaction_id, recorded_by) 
                                  VALUES (?, ?, ?, ?, ?, ?)");
            $stmt->execute([$fee_id, $amount_paid, $payment_date, $payment_method, $transaction_id, $_SESSION['user_id']]);
            
            // Update fee status
            $stmt = $pdo->prepare("UPDATE fees SET status = 'paid' WHERE id = ?");
            $stmt->execute([$fee_id]);
            
            $message = "Payment recorded successfully!";
        } catch (PDOException $e) {
            $message = "Error recording payment: " . $e->getMessage();
        }
    }
}

// Get all fees with student information
$stmt = $pdo->query("SELECT f.*, s.first_name, s.last_name, s.class_id, c.name as class_name 
                    FROM fees f 
                    JOIN students s ON f.student_id = s.id 
                    LEFT JOIN classes c ON s.class_id = c.id 
                    ORDER BY f.created_at DESC");
$fees = $stmt->fetchAll();

// Get students for dropdown
$stmt = $pdo->query("SELECT * FROM students ORDER BY first_name, last_name");
$students = $stmt->fetchAll();

// Get fee for editing
$edit_fee = null;
if (isset($_GET['edit'])) {
    $stmt = $pdo->prepare("SELECT * FROM fees WHERE id = ?");
    $stmt->execute([$_GET['edit']]);
    $edit_fee = $stmt->fetch();
}

// Get fee for payment
$payment_fee = null;
if (isset($_GET['payment'])) {
    $stmt = $pdo->prepare("SELECT f.*, s.first_name, s.last_name FROM fees f 
                          JOIN students s ON f.student_id = s.id 
                          WHERE f.id = ?");
    $stmt->execute([$_GET['payment']]);
    $payment_fee = $stmt->fetch();
}
?>

<?php include '../includes/header.php'; ?>
<?php include '../includes/sidebar.php'; ?>

<main class="main-content">
    <div class="content-header">
        <h1>Fee Management</h1>
    </div>
    
    <?php if (!empty($message)): ?>
        <div class="alert <?php echo strpos($message, 'Error') !== false ? 'alert-error' : 'alert-success'; ?>">
            <?php echo $message; ?>
        </div>
    <?php endif; ?>
    
    <div class="form-container">
        <h2><?php echo $edit_fee ? 'Edit Fee Record' : ($payment_fee ? 'Record Payment' : 'Add New Fee Record'); ?></h2>
        <form method="POST">
            <?php if ($edit_fee): ?>
                <input type="hidden" name="fee_id" value="<?php echo $edit_fee['id']; ?>">
            <?php elseif ($payment_fee): ?>
                <input type="hidden" name="fee_id" value="<?php echo $payment_fee['id']; ?>">
            <?php endif; ?>
            
            <?php if ($payment_fee): ?>
                <div class="form-group">
                    <label>Student</label>
                    <p><?php echo $payment_fee['first_name'] . ' ' . $payment_fee['last_name']; ?></p>
                </div>
                
                <div class="form-group">
                    <label>Amount Due</label>
                    <p>$<?php echo number_format($payment_fee['amount'], 2); ?></p>
                </div>
                
                <div class="form-group">
                    <label for="amount_paid">Amount Paid</label>
                    <input type="number" id="amount_paid" name="amount_paid" step="0.01" min="0" 
                           max="<?php echo $payment_fee['amount']; ?>" required>
                </div>
                
                <div class="form-group">
                    <label for="payment_date">Payment Date</label>
                    <input type="date" id="payment_date" name="payment_date" required 
                           value="<?php echo date('Y-m-d'); ?>">
                </div>
                
                <div class="form-group">
                    <label for="payment_method">Payment Method</label>
                    <select id="payment_method" name="payment_method" required>
                        <option value="">Select Method</option>
                        <option value="cash">Cash</option>
                        <option value="bank_transfer">Bank Transfer</option>
                        <option value="credit_card">Credit Card</option>
                        <option value="debit_card">Debit Card</option>
                        <option value="check">Check</option>
                        <option value="online">Online Payment</option>
                    </select>
                </div>
                
                <div class="form-group">
                    <label for="transaction_id">Transaction ID (Optional)</label>
                    <input type="text" id="transaction_id" name="transaction_id">
                </div>
                
                <div class="form-group">
                    <button type="submit" name="record_payment" class="btn btn-primary">Record Payment</button>
                    <a href="fee_management.php" class="btn btn-secondary">Cancel</a>
                </div>
            <?php else: ?>
                <div class="form-group">
                    <label for="student_id">Student</label>
                    <select id="student_id" name="student_id" required>
                        <option value="">Select Student</option>
                        <?php foreach ($students as $student): ?>
                            <option value="<?php echo $student['id']; ?>" 
                                <?php echo $edit_fee && $edit_fee['student_id'] == $student['id'] ? 'selected' : ''; ?>>
                                <?php echo $student['first_name'] . ' ' . $student['last_name']; ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                
                <div class="form-group">
                    <label for="amount">Amount</label>
                    <input type="number" id="amount" name="amount" step="0.01" min="0" required 
                           value="<?php echo $edit_fee ? $edit_fee['amount'] : ''; ?>">
                </div>
                
                <div class="form-group">
                    <label for="term">Term</label>
                    <select id="term" name="term" required>
                        <option value="">Select Term</option>
                        <option value="1st Term" <?php echo $edit_fee && $edit_fee['term'] == '1st Term' ? 'selected' : ''; ?>>1st Term</option>
                        <option value="2nd Term" <?php echo $edit_fee && $edit_fee['term'] == '2nd Term' ? 'selected' : ''; ?>>2nd Term</option>
                        <option value="3rd Term" <?php echo $edit_fee && $edit_fee['term'] == '3rd Term' ? 'selected' : ''; ?>>3rd Term</option>
                        <option value="Annual" <?php echo $edit_fee && $edit_fee['term'] == 'Annual' ? 'selected' : ''; ?>>Annual</option>
                    </select>
                </div>
                
                <div class="form-group">
                    <label for="academic_year">Academic Year</label>
                    <input type="text" id="academic_year" name="academic_year" placeholder="e.g., 2023-2024" required 
                           value="<?php echo $edit_fee ? $edit_fee['academic_year'] : ''; ?>">
                </div>
                
                <div class="form-group">
                    <label for="due_date">Due Date</label>
                    <input type="date" id="due_date" name="due_date" required 
                           value="<?php echo $edit_fee ? $edit_fee['due_date'] : ''; ?>">
                </div>
                
                <?php if ($edit_fee): ?>
                    <div class="form-group">
                        <label for="status">Status</label>
                        <select id="status" name="status" required>
                            <option value="pending" <?php echo $edit_fee && $edit_fee['status'] == 'pending' ? 'selected' : ''; ?>>Pending</option>
                            <option value="paid" <?php echo $edit_fee && $edit_fee['status'] == 'paid' ? 'selected' : ''; ?>>Paid</option>
                            <option value="overdue" <?php echo $edit_fee && $edit_fee['status'] == 'overdue' ? 'selected' : ''; ?>>Overdue</option>
                        </select>
                    </div>
                <?php endif; ?>
                
                <div class="form-group">
                    <?php if ($edit_fee): ?>
                        <button type="submit" name="update_fee" class="btn btn-primary">Update Fee Record</button>
                        <a href="fee_management.php" class="btn btn-secondary">Cancel</a>
                    <?php else: ?>
                        <button type="submit" name="add_fee" class="btn btn-primary">Add Fee Record</button>
                    <?php endif; ?>
                </div>
            <?php endif; ?>
        </form>
    </div>
    
    <div class="table-container">
        <h2>All Fee Records</h2>
        <table>
            <thead>
                <tr>
                    <th>ID</th>
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
                <?php if (count($fees) > 0): ?>
                    <?php foreach ($fees as $fee): ?>
                        <tr>
                            <td><?php echo $fee['id']; ?></td>
                            <td><?php echo $fee['first_name'] . ' ' . $fee['last_name']; ?></td>
                            <td><?php echo $fee['class_name'] ? $fee['class_name'] : 'N/A'; ?></td>
                            <td>$<?php echo number_format($fee['amount'], 2); ?></td>
                            <td><?php echo $fee['term']; ?></td>
                            <td><?php echo $fee['academic_year']; ?></td>
                            <td><?php echo date('M j, Y', strtotime($fee['due_date'])); ?></td>
                            <td>
                                <span class="status-badge <?php echo $fee['status']; ?>">
                                    <?php echo ucfirst($fee['status']); ?>
                                </span>
                            </td>
                            <td>
                                <a href="fee_management.php?edit=<?php echo $fee['id']; ?>" class="btn btn-sm btn-primary">Edit</a>
                                <?php if ($fee['status'] != 'paid'): ?>
                                    <a href="fee_management.php?payment=<?php echo $fee['id']; ?>" class="btn btn-sm btn-success">Record Payment</a>
                                <?php endif; ?>
                                <form method="POST" class="inline-form">
                                    <input type="hidden" name="fee_id" value="<?php echo $fee['id']; ?>">
                                    <button type="submit" name="delete_fee" class="btn btn-sm btn-danger" 
                                            onclick="return confirm('Are you sure you want to delete this fee record?')">Delete</button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="9">No fee records found.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</main>

<?php include '../includes/footer.php'; ?>