<?php
// salary_management.php
require_once '../config.php';
checkRole(['admin', 'finance']);

$page_title = "Salary Management";

// Check if salary tables exist
try {
    $pdo->query("SELECT 1 FROM staff_salaries LIMIT 1");
    $pdo->query("SELECT 1 FROM salary_payments LIMIT 1");
    $tables_exist = true;
} catch (PDOException $e) {
    header('Location: setup_salary_tables.php');
    exit();
}

// Handle salary payment
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['pay_salary'])) {
    $staff_id = $_POST['staff_id'];
    $amount_paid = $_POST['amount_paid'];
    $payment_date = $_POST['payment_date'];
    $payment_method = $_POST['payment_method'];
    $transaction_id = $_POST['transaction_id'];
    $month = date('m', strtotime($payment_date));
    $year = date('Y', strtotime($payment_date));
    $recorded_by = $_SESSION['user_id'];

    try {
        $stmt = $pdo->prepare("INSERT INTO salary_payments (staff_id, amount_paid, payment_date, month, year, payment_method, transaction_id, recorded_by) 
                              VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->execute([$staff_id, $amount_paid, $payment_date, $month, $year, $payment_method, $transaction_id, $recorded_by]);
        
        $_SESSION['success_message'] = "Salary paid successfully!";
        header('Location: salary_management.php');
        exit();
    } catch (PDOException $e) {
        $error_message = "Error processing payment: " . $e->getMessage();
    }
}

// Handle salary update
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_salary'])) {
    $staff_id = $_POST['staff_id'];
    $monthly_salary = $_POST['monthly_salary'];

    try {
        $stmt = $pdo->prepare("UPDATE staff_salaries SET monthly_salary = ? WHERE staff_id = ?");
        $stmt->execute([$monthly_salary, $staff_id]);
        
        $_SESSION['success_message'] = "Salary updated successfully!";
        header('Location: salary_management.php');
        exit();
    } catch (PDOException $e) {
        $error_message = "Error updating salary: " . $e->getMessage();
    }
}

// Get staff with salary information
$staff_stmt = $pdo->query("
    SELECT u.id, u.username, u.first_name, u.last_name, u.role, u.email,
           s.monthly_salary,
           sp.id as salary_payment_id, sp.amount_paid, sp.payment_date, sp.month, sp.year,
           CASE 
               WHEN sp.id IS NOT NULL AND sp.month = MONTH(CURRENT_DATE()) AND sp.year = YEAR(CURRENT_DATE()) THEN 'paid'
               ELSE 'unpaid'
           END as salary_status
    FROM users u
    JOIN staff_salaries s ON u.id = s.staff_id
    LEFT JOIN salary_payments sp ON u.id = sp.staff_id AND sp.month = MONTH(CURRENT_DATE()) AND sp.year = YEAR(CURRENT_DATE())
    WHERE u.role IN ('teacher', 'staff', 'admin')
    ORDER BY u.role, u.first_name
");
$staff = $staff_stmt->fetchAll();

// Get payment history
$payment_history_stmt = $pdo->query("
    SELECT sp.*, u.first_name, u.last_name, u.role,
           recorder.username as recorded_by_name
    FROM salary_payments sp
    JOIN users u ON sp.staff_id = u.id
    LEFT JOIN users recorder ON sp.recorded_by = recorder.id
    ORDER BY sp.payment_date DESC, sp.created_at DESC
    LIMIT 50
");
$payment_history = $payment_history_stmt->fetchAll();

// Calculate statistics
$total_staff = count($staff);
$paid_this_month = 0;
$unpaid_this_month = 0;
$total_salary_paid = 0;
$total_salary_pending = 0;

foreach ($staff as $staff_member) {
    if ($staff_member['salary_status'] === 'paid') {
        $paid_this_month++;
        $total_salary_paid += $staff_member['amount_paid'];
    } else {
        $unpaid_this_month++;
        $total_salary_pending += $staff_member['monthly_salary'];
    }
}
?>

<?php include '../includes/header.php'; ?>
<?php include '../includes/sidebar.php'; ?>

<main class="main-content">
    <div class="content-header">
        <h1>Salary Management</h1>
        <div class="header-actions">
            <a href="payment_records.php" class="btn btn-secondary">Back to Payments</a>
        </div>
    </div>

    <?php if (isset($_SESSION['success_message'])): ?>
        <div class="alert alert-success">
            <?php echo $_SESSION['success_message']; unset($_SESSION['success_message']); ?>
        </div>
    <?php endif; ?>

    <?php if (isset($error_message)): ?>
        <div class="alert alert-danger">
            <?php echo $error_message; ?>
        </div>
    <?php endif; ?>

    <!-- Statistics -->
    <div class="dashboard-stats">
        <div class="stat-card">
            <h3>Total Staff</h3>
            <p><?php echo $total_staff; ?></p>
        </div>
        
        <div class="stat-card">
            <h3>Paid This Month</h3>
            <p><?php echo $paid_this_month; ?></p>
        </div>
        
        <div class="stat-card">
            <h3>Unpaid This Month</h3>
            <p><?php echo $unpaid_this_month; ?></p>
        </div>
        
        <div class="stat-card">
            <h3>Total Paid</h3>
            <p>$<?php echo number_format($total_salary_paid, 2); ?></p>
        </div>
        
        <div class="stat-card">
            <h3>Total Pending</h3>
            <p>$<?php echo number_format($total_salary_pending, 2); ?></p>
        </div>
    </div>

    <!-- Staff Salary List -->
    <div class="table-container">
        <h2>Staff Salary Status - <?php echo date('F Y'); ?></h2>
        
        <table>
            <thead>
                <tr>
                    <th>Staff Name</th>
                    <th>Role</th>
                    <th>Monthly Salary</th>
                    <th>Amount Paid</th>
                    <th>Payment Date</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if (count($staff) > 0): ?>
                    <?php foreach ($staff as $staff_member): ?>
                        <tr class="<?php echo $staff_member['salary_status']; ?>">
                            <td>
                                <strong><?php echo $staff_member['first_name'] . ' ' . $staff_member['last_name']; ?></strong>
                                <br><small><?php echo $staff_member['email']; ?></small>
                            </td>
                            <td><?php echo ucfirst($staff_member['role']); ?></td>
                            <td>$<?php echo number_format($staff_member['monthly_salary'], 2); ?></td>
                            <td>
                                <?php if ($staff_member['salary_status'] === 'paid'): ?>
                                    $<?php echo number_format($staff_member['amount_paid'], 2); ?>
                                <?php else: ?>
                                    $0.00
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php if ($staff_member['salary_status'] === 'paid'): ?>
                                    <?php echo date('M j, Y', strtotime($staff_member['payment_date'])); ?>
                                <?php else: ?>
                                    <span class="text-muted">Not paid</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <span class="status-badge status-<?php echo $staff_member['salary_status']; ?>">
                                    <?php echo ucfirst($staff_member['salary_status']); ?>
                                </span>
                            </td>
                            <td>
                                <div class="action-buttons">
                                    <?php if ($staff_member['salary_status'] === 'unpaid'): ?>
                                        <button class="btn btn-sm btn-primary" 
                                                onclick="openPaySalaryModal(<?php echo $staff_member['id']; ?>, '<?php echo $staff_member['first_name'] . ' ' . $staff_member['last_name']; ?>', <?php echo $staff_member['monthly_salary']; ?>)">
                                            Pay Salary
                                        </button>
                                    <?php endif; ?>
                                    <button class="btn btn-sm btn-secondary" 
                                            onclick="openUpdateSalaryModal(<?php echo $staff_member['id']; ?>, '<?php echo $staff_member['first_name'] . ' ' . $staff_member['last_name']; ?>', <?php echo $staff_member['monthly_salary']; ?>)">
                                        Edit Salary
                                    </button>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="7">No staff members found.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <!-- Payment History -->
    <div class="table-container">
        <h2>Recent Salary Payments</h2>
        
        <table>
            <thead>
                <tr>
                    <th>Staff Name</th>
                    <th>Role</th>
                    <th>Amount Paid</th>
                    <th>Payment Date</th>
                    <th>Month/Year</th>
                    <th>Payment Method</th>
                    <th>Transaction ID</th>
                    <th>Recorded By</th>
                </tr>
            </thead>
            <tbody>
                <?php if (count($payment_history) > 0): ?>
                    <?php foreach ($payment_history as $payment): ?>
                        <tr>
                            <td><?php echo $payment['first_name'] . ' ' . $payment['last_name']; ?></td>
                            <td><?php echo ucfirst($payment['role']); ?></td>
                            <td>$<?php echo number_format($payment['amount_paid'], 2); ?></td>
                            <td><?php echo date('M j, Y', strtotime($payment['payment_date'])); ?></td>
                            <td><?php echo date('F Y', strtotime($payment['payment_date'])); ?></td>
                            <td><?php echo ucfirst(str_replace('_', ' ', $payment['payment_method'])); ?></td>
                            <td><?php echo $payment['transaction_id'] ?: 'N/A'; ?></td>
                            <td><?php echo $payment['recorded_by_name']; ?></td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="8">No payment history found.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</main>

<!-- Pay Salary Modal -->
<div id="paySalaryModal" class="modal">
    <div class="modal-content">
        <span class="close">&times;</span>
        <h2>Pay Salary</h2>
        <form method="POST" id="paySalaryForm">
            <input type="hidden" name="staff_id" id="pay_staff_id">
            
            <div class="form-group">
                <label>Staff Member</label>
                <input type="text" id="pay_staff_name" class="form-control" readonly>
            </div>
            
            <div class="form-group">
                <label>Monthly Salary</label>
                <input type="text" id="pay_monthly_salary" class="form-control" readonly>
            </div>
            
            <div class="form-group">
                <label for="amount_paid">Amount to Pay *</label>
                <input type="number" id="amount_paid" name="amount_paid" class="form-control" step="0.01" required>
            </div>
            
            <div class="form-group">
                <label for="payment_date">Payment Date *</label>
                <input type="date" id="payment_date" name="payment_date" class="form-control" value="<?php echo date('Y-m-d'); ?>" required>
            </div>
            
            <div class="form-group">
                <label for="payment_method">Payment Method *</label>
                <select id="payment_method" name="payment_method" class="form-control" required>
                    <option value="bank_transfer">Bank Transfer</option>
                    <option value="cash">Cash</option>
                    <option value="check">Check</option>
                    <option value="online">Online Payment</option>
                </select>
            </div>
            
            <div class="form-group">
                <label for="transaction_id">Transaction ID</label>
                <input type="text" id="transaction_id" name="transaction_id" class="form-control" placeholder="Optional">
            </div>
            
            <div class="form-actions">
                <button type="submit" name="pay_salary" class="btn btn-primary">Process Payment</button>
                <button type="button" class="btn btn-secondary" onclick="closeModal('paySalaryModal')">Cancel</button>
            </div>
        </form>
    </div>
</div>

<!-- Update Salary Modal -->
<div id="updateSalaryModal" class="modal">
    <div class="modal-content">
        <span class="close">&times;</span>
        <h2>Update Salary</h2>
        <form method="POST" id="updateSalaryForm">
            <input type="hidden" name="staff_id" id="update_staff_id">
            
            <div class="form-group">
                <label>Staff Member</label>
                <input type="text" id="update_staff_name" class="form-control" readonly>
            </div>
            
            <div class="form-group">
                <label for="monthly_salary">Monthly Salary *</label>
                <input type="number" id="monthly_salary" name="monthly_salary" class="form-control" step="0.01" min="0" required>
            </div>
            
            <div class="form-actions">
                <button type="submit" name="update_salary" class="btn btn-primary">Update Salary</button>
                <button type="button" class="btn btn-secondary" onclick="closeModal('updateSalaryModal')">Cancel</button>
            </div>
        </form>
    </div>
</div>

<style>
.dashboard-stats {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 15px;
    margin-bottom: 20px;
}

.stat-card {
    background: #f8f9fa;
    padding: 15px;
    border-radius: 8px;
    text-align: center;
    border-left: 4px solid #007bff;
}

.stat-card h3 {
    margin: 0 0 10px 0;
    font-size: 14px;
    color: #6c757d;
}

.stat-card p {
    margin: 0;
    font-size: 24px;
    font-weight: bold;
    color: #007bff;
}

.status-badge {
    padding: 4px 8px;
    border-radius: 4px;
    font-size: 12px;
    font-weight: bold;
}

.status-paid {
    background-color: #d4edda;
    color: #155724;
}

.status-unpaid {
    background-color: #f8d7da;
    color: #721c24;
}

.action-buttons {
    display: flex;
    gap: 5px;
    flex-wrap: wrap;
}

.btn {
    display: inline-block;
    padding: 6px 12px;
    border: none;
    border-radius: 4px;
    text-decoration: none;
    cursor: pointer;
    font-size: 14px;
}

.btn-sm {
    padding: 4px 8px;
    font-size: 12px;
}

.btn-primary {
    background-color: #007bff;
    color: white;
}

.btn-secondary {
    background-color: #6c757d;
    color: white;
}

.text-muted {
    color: #6c757d;
}

/* Modal Styles */
.modal {
    display: none;
    position: fixed;
    z-index: 1000;
    left: 0;
    top: 0;
    width: 100%;
    height: 100%;
    background-color: rgba(0,0,0,0.5);
}

.modal-content {
    background-color: white;
    margin: 5% auto;
    padding: 20px;
    border-radius: 8px;
    width: 90%;
    max-width: 500px;
    position: relative;
}

.close {
    position: absolute;
    right: 15px;
    top: 10px;
    font-size: 24px;
    cursor: pointer;
}

.form-group {
    margin-bottom: 15px;
}

.form-group label {
    display: block;
    margin-bottom: 5px;
    font-weight: bold;
}

.form-control {
    width: 100%;
    padding: 8px;
    border: 1px solid #ddd;
    border-radius: 4px;
    box-sizing: border-box;
}

.form-actions {
    display: flex;
    gap: 10px;
    justify-content: flex-end;
    margin-top: 20px;
}
</style>

<script>
function openPaySalaryModal(staffId, staffName, monthlySalary) {
    document.getElementById('pay_staff_id').value = staffId;
    document.getElementById('pay_staff_name').value = staffName;
    document.getElementById('pay_monthly_salary').value = '$' + monthlySalary.toLocaleString('en-US', {minimumFractionDigits: 2});
    document.getElementById('amount_paid').value = monthlySalary;
    document.getElementById('amount_paid').max = monthlySalary;
    document.getElementById('paySalaryModal').style.display = 'block';
}

function openUpdateSalaryModal(staffId, staffName, monthlySalary) {
    document.getElementById('update_staff_id').value = staffId;
    document.getElementById('update_staff_name').value = staffName;
    document.getElementById('monthly_salary').value = monthlySalary;
    document.getElementById('updateSalaryModal').style.display = 'block';
}

function closeModal(modalId) {
    document.getElementById(modalId).style.display = 'none';
}

// Close modal when clicking on X
document.querySelectorAll('.close').forEach(function(closeBtn) {
    closeBtn.onclick = function() {
        this.closest('.modal').style.display = 'none';
    }
});

// Close modal when clicking outside
window.onclick = function(event) {
    if (event.target.classList.contains('modal')) {
        event.target.style.display = 'none';
    }
}
</script>

<?php include '../includes/footer.php'; ?>