<?php
require_once '../config.php';
checkRole(['admin', 'finance']);

$page_title = "Payment Records";

// Get all payment records with related information
$stmt = $pdo->query("SELECT p.*, f.amount as fee_amount, s.first_name, s.last_name, s.class_id, 
                    c.name as class_name, u.username as recorded_by_name 
                    FROM payments p 
                    JOIN fees f ON p.fee_id = f.id 
                    JOIN students s ON f.student_id = s.id 
                    LEFT JOIN classes c ON s.class_id = c.id 
                    LEFT JOIN users u ON p.recorded_by = u.id 
                    ORDER BY p.payment_date DESC, p.created_at DESC");
$payments = $stmt->fetchAll();

// Get students payment status
$students_stmt = $pdo->query("
    SELECT s.id, s.first_name, s.last_name, s.class_id, c.name as class_name,
           f.amount as total_fee, 
           COALESCE(SUM(p.amount_paid), 0) as total_paid,
           (f.amount - COALESCE(SUM(p.amount_paid), 0)) as balance,
           CASE 
               WHEN COALESCE(SUM(p.amount_paid), 0) = 0 THEN 'not_paid'
               WHEN COALESCE(SUM(p.amount_paid), 0) >= f.amount THEN 'fully_paid'
               ELSE 'partial'
           END as payment_status
    FROM students s
    JOIN fees f ON s.id = f.student_id
    LEFT JOIN payments p ON f.id = p.fee_id
    LEFT JOIN classes c ON s.class_id = c.id
    GROUP BY s.id, f.amount
    ORDER BY s.class_id, s.first_name
");
$students = $students_stmt->fetchAll();

// Initialize staff variables
$staff = [];
$paid_staff_count = 0;
$unpaid_staff_count = 0;
$total_salary_paid = 0;
$total_salary_pending = 0;
$staff_table_exists = true;

// Get staff salary payment status with error handling
try {
    $staff_stmt = $pdo->query("
        SELECT u.id, u.username, u.first_name, u.last_name, u.role,
               s.monthly_salary,
               sp.id as salary_payment_id, sp.amount_paid, sp.payment_date, sp.month, sp.year,
               CASE 
                   WHEN sp.id IS NOT NULL THEN 'paid'
                   ELSE 'unpaid'
               END as salary_status
        FROM users u
        JOIN staff_salaries s ON u.id = s.staff_id
        LEFT JOIN salary_payments sp ON u.id = sp.staff_id AND sp.month = MONTH(CURRENT_DATE()) AND sp.year = YEAR(CURRENT_DATE())
        WHERE u.role IN ('teacher', 'staff', 'admin')
        ORDER BY u.role, u.first_name
    ");
    $staff = $staff_stmt->fetchAll();
    
    // Calculate staff salary statistics
    foreach ($staff as $staff_member) {
        if ($staff_member['salary_status'] === 'paid') {
            $paid_staff_count++;
            $total_salary_paid += $staff_member['amount_paid'];
        } else {
            $unpaid_staff_count++;
            $total_salary_pending += $staff_member['monthly_salary'];
        }
    }
} catch (PDOException $e) {
    $staff_table_exists = false;
    error_log("Staff salary tables not found: " . $e->getMessage());
}

// Calculate statistics
$total_payments = count($payments);
$total_amount = 0;
$payment_methods = [];

// Student payment statistics
$fully_paid_count = 0;
$partial_paid_count = 0;
$not_paid_count = 0;
$total_owed = 0;

foreach ($payments as $payment) {
    $total_amount += $payment['amount_paid'];
    
    if (!isset($payment_methods[$payment['payment_method']])) {
        $payment_methods[$payment['payment_method']] = 0;
    }
    $payment_methods[$payment['payment_method']]++;
}

foreach ($students as $student) {
    if ($student['payment_status'] === 'fully_paid') {
        $fully_paid_count++;
    } elseif ($student['payment_status'] === 'partial') {
        $partial_paid_count++;
        $total_owed += $student['balance'];
    } else {
        $not_paid_count++;
        $total_owed += $student['total_fee'];
    }
}

?>

<?php include '../includes/header.php'; ?>
<?php include '../includes/sidebar.php'; ?>

<main class="main-content">
    <div class="content-header">
        <h1>Payment Records</h1>
        <div class="header-actions">
            <?php if ($staff_table_exists): ?>
                <a href="salary_management.php" class="btn btn-primary">Manage Salaries</a>
            <?php endif; ?>
        </div>
    </div>
    
    <!-- Dashboard Statistics -->
    <div class="dashboard-stats">
        <div class="stat-card">
            <h3>Total Payments</h3>
            <p><?php echo $total_payments; ?></p>
        </div>
        
        <div class="stat-card">
            <h3>Total Amount</h3>
            <p>$<?php echo number_format($total_amount, 2); ?></p>
        </div>
        
        <div class="stat-card">
            <h3>Fully Paid Students</h3>
            <p><?php echo $fully_paid_count; ?></p>
        </div>
        
        <div class="stat-card">
            <h3>Students with Balance</h3>
            <p><?php echo $partial_paid_count; ?></p>
        </div>
        
        <div class="stat-card">
            <h3>Unpaid Students</h3>
            <p><?php echo $not_paid_count; ?></p>
        </div>
        
        <div class="stat-card">
            <h3>Total Owed</h3>
            <p>$<?php echo number_format($total_owed, 2); ?></p>
        </div>
        
        <?php if ($staff_table_exists): ?>
            <div class="stat-card">
                <h3>Paid Staff</h3>
                <p><?php echo $paid_staff_count; ?></p>
            </div>
            
            <div class="stat-card">
                <h3>Unpaid Staff</h3>
                <p><?php echo $unpaid_staff_count; ?></p>
            </div>
            
            <div class="stat-card">
                <h3>Salary Paid</h3>
                <p>$<?php echo number_format($total_salary_paid, 2); ?></p>
            </div>
            
            <div class="stat-card">
                <h3>Salary Pending</h3>
                <p>$<?php echo number_format($total_salary_pending, 2); ?></p>
            </div>
        <?php endif; ?>
        
        <?php foreach ($payment_methods as $method => $count): ?>
            <div class="stat-card">
                <h3><?php echo ucfirst($method); ?> Payments</h3>
                <p><?php echo $count; ?></p>
            </div>
        <?php endforeach; ?>
    </div>
    
    <!-- Student Payment Status Section -->
    <div class="table-container">
        <h2>Student Payment Status</h2>
        
        <div class="filter-section">
            <form method="GET" class="filter-form">
                <div class="form-group">
                    <label for="filter_payment_status">Payment Status</label>
                    <select id="filter_payment_status" name="payment_status" onchange="this.form.submit()">
                        <option value="">All Students</option>
                        <option value="fully_paid" <?php echo isset($_GET['payment_status']) && $_GET['payment_status'] == 'fully_paid' ? 'selected' : ''; ?>>Fully Paid</option>
                        <option value="partial" <?php echo isset($_GET['payment_status']) && $_GET['payment_status'] == 'partial' ? 'selected' : ''; ?>>Partial Payment</option>
                        <option value="not_paid" <?php echo isset($_GET['payment_status']) && $_GET['payment_status'] == 'not_paid' ? 'selected' : ''; ?>>Not Paid</option>
                    </select>
                </div>
                
                <div class="form-group">
                    <label for="filter_class">Class</label>
                    <select id="filter_class" name="class_id" onchange="this.form.submit()">
                        <option value="">All Classes</option>
                        <?php
                        $classes_stmt = $pdo->query("SELECT id, name FROM classes ORDER BY name");
                        $classes = $classes_stmt->fetchAll();
                        foreach ($classes as $class): ?>
                            <option value="<?php echo $class['id']; ?>" 
                                <?php echo isset($_GET['class_id']) && $_GET['class_id'] == $class['id'] ? 'selected' : ''; ?>>
                                <?php echo $class['name']; ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                
                <div class="form-group">
                    <button type="submit" class="btn btn-primary">Apply Filters</button>
                    <a href="payment_records.php" class="btn btn-secondary">Clear</a>
                </div>
            </form>
        </div>
        
        <table>
            <thead>
                <tr>
                    <th>Student ID</th>
                    <th>Student Name</th>
                    <th>Class</th>
                    <th>Total Fee</th>
                    <th>Amount Paid</th>
                    <th>Balance</th>
                    <th>Payment Status</th>
                </tr>
            </thead>
            <tbody>
                <?php 
                // Filter students based on query parameters
                $filtered_students = $students;
                if (isset($_GET['payment_status']) && !empty($_GET['payment_status'])) {
                    $filtered_students = array_filter($filtered_students, function($student) {
                        return $student['payment_status'] == $_GET['payment_status'];
                    });
                }
                if (isset($_GET['class_id']) && !empty($_GET['class_id'])) {
                    $filtered_students = array_filter($filtered_students, function($student) {
                        return $student['class_id'] == $_GET['class_id'];
                    });
                }
                ?>
                
                <?php if (count($filtered_students) > 0): ?>
                    <?php foreach ($filtered_students as $student): ?>
                        <tr class="<?php echo $student['payment_status']; ?>">
                            <td><?php echo $student['id']; ?></td>
                            <td><?php echo $student['first_name'] . ' ' . $student['last_name']; ?></td>
                            <td><?php echo $student['class_name'] ? $student['class_name'] : 'N/A'; ?></td>
                            <td>$<?php echo number_format($student['total_fee'], 2); ?></td>
                            <td>$<?php echo number_format($student['total_paid'], 2); ?></td>
                            <td>$<?php echo number_format($student['balance'], 2); ?></td>
                            <td>
                                <span class="status-badge status-<?php echo $student['payment_status']; ?>">
                                    <?php 
                                    switch($student['payment_status']) {
                                        case 'fully_paid': echo 'Fully Paid'; break;
                                        case 'partial': echo 'Partial Payment'; break;
                                        case 'not_paid': echo 'Not Paid'; break;
                                    }
                                    ?>
                                </span>
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
    
    <!-- Staff Salary Status Section -->
    <?php if ($staff_table_exists): ?>
        <div class="table-container">
            <div class="section-header">
                <h2>Staff Salary Status (<?php echo date('F Y'); ?>)</h2>
                <a href="salary_management.php" class="btn btn-primary">Manage Salaries</a>
            </div>
            
            <div class="filter-section">
                <form method="GET" class="filter-form">
                    <div class="form-group">
                        <label for="filter_salary_status">Salary Status</label>
                        <select id="filter_salary_status" name="salary_status" onchange="this.form.submit()">
                            <option value="">All Staff</option>
                            <option value="paid" <?php echo isset($_GET['salary_status']) && $_GET['salary_status'] == 'paid' ? 'selected' : ''; ?>>Paid</option>
                            <option value="unpaid" <?php echo isset($_GET['salary_status']) && $_GET['salary_status'] == 'unpaid' ? 'selected' : ''; ?>>Unpaid</option>
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label for="filter_role">Role</label>
                        <select id="filter_role" name="role" onchange="this.form.submit()">
                            <option value="">All Roles</option>
                            <option value="teacher" <?php echo isset($_GET['role']) && $_GET['role'] == 'teacher' ? 'selected' : ''; ?>>Teacher</option>
                            <option value="staff" <?php echo isset($_GET['role']) && $_GET['role'] == 'staff' ? 'selected' : ''; ?>>Staff</option>
                            <option value="admin" <?php echo isset($_GET['role']) && $_GET['role'] == 'admin' ? 'selected' : ''; ?>>Admin</option>
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <button type="submit" class="btn btn-primary">Apply Filters</button>
                        <a href="payment_records.php" class="btn btn-secondary">Clear</a>
                    </div>
                </form>
            </div>
            
            <table>
                <thead>
                    <tr>
                        <th>Staff ID</th>
                        <th>Staff Name</th>
                        <th>Role</th>
                        <th>Monthly Salary</th>
                        <th>Amount Paid</th>
                        <th>Payment Date</th>
                        <th>Salary Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                    // Filter staff based on query parameters
                    $filtered_staff = $staff;
                    if (isset($_GET['salary_status']) && !empty($_GET['salary_status'])) {
                        $filtered_staff = array_filter($filtered_staff, function($staff_member) {
                            return $staff_member['salary_status'] == $_GET['salary_status'];
                        });
                    }
                    if (isset($_GET['role']) && !empty($_GET['role'])) {
                        $filtered_staff = array_filter($filtered_staff, function($staff_member) {
                            return $staff_member['role'] == $_GET['role'];
                        });
                    }
                    ?>
                    
                    <?php if (count($filtered_staff) > 0): ?>
                        <?php foreach ($filtered_staff as $staff_member): ?>
                            <tr class="<?php echo $staff_member['salary_status']; ?>">
                                <td><?php echo $staff_member['id']; ?></td>
                                <td><?php echo $staff_member['first_name'] . ' ' . $staff_member['last_name']; ?></td>
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
    <?php else: ?>
        <div class="alert alert-warning">
            <strong>Note:</strong> Staff salary management is not set up. Please run the SQL script to create the required tables.
            <div style="margin-top: 10px;">
                <a href="setup_salary_tables.php" class="btn btn-primary">Setup Salary Tables</a>
            </div>
        </div>
    <?php endif; ?>
    
    <!-- Original Payment Records Table -->
    <div class="table-container">
        <h2>All Payment Records</h2>
        <div class="filter-section">
            <form method="GET" class="filter-form">
                <div class="form-group">
                    <label for="filter_method">Payment Method</label>
                    <select id="filter_method" name="method" onchange="this.form.submit()">
                        <option value="">All Methods</option>
                        <option value="cash" <?php echo isset($_GET['method']) && $_GET['method'] == 'cash' ? 'selected' : ''; ?>>Cash</option>
                        <option value="bank_transfer" <?php echo isset($_GET['method']) && $_GET['method'] == 'bank_transfer' ? 'selected' : ''; ?>>Bank Transfer</option>
                        <option value="credit_card" <?php echo isset($_GET['method']) && $_GET['method'] == 'credit_card' ? 'selected' : ''; ?>>Credit Card</option>
                        <option value="debit_card" <?php echo isset($_GET['method']) && $_GET['method'] == 'debit_card' ? 'selected' : ''; ?>>Debit Card</option>
                        <option value="check" <?php echo isset($_GET['method']) && $_GET['method'] == 'check' ? 'selected' : ''; ?>>Check</option>
                        <option value="online" <?php echo isset($_GET['method']) && $_GET['method'] == 'online' ? 'selected' : ''; ?>>Online Payment</option>
                    </select>
                </div>
                
                <div class="form-group">
                    <label for="filter_date_from">Date From</label>
                    <input type="date" id="filter_date_from" name="date_from" 
                           value="<?php echo isset($_GET['date_from']) ? $_GET['date_from'] : ''; ?>" 
                           onchange="this.form.submit()">
                </div>
                
                <div class="form-group">
                    <label for="filter_date_to">Date To</label>
                    <input type="date" id="filter_date_to" name="date_to" 
                           value="<?php echo isset($_GET['date_to']) ? $_GET['date_to'] : ''; ?>" 
                           onchange="this.form.submit()">
                </div>
                
                <div class="form-group">
                    <button type="submit" class="btn btn-primary">Apply Filters</button>
                    <a href="payment_records.php" class="btn btn-secondary">Clear</a>
                </div>
            </form>
        </div>
        
        <table>
            <thead>
                <tr>
                    <th>Payment ID</th>
                    <th>Student</th>
                    <th>Class</th>
                    <th>Fee Amount</th>
                    <th>Amount Paid</th>
                    <th>Payment Date</th>
                    <th>Payment Method</th>
                    <th>Transaction ID</th>
                    <th>Recorded By</th>
                    <th>Recorded At</th>
                </tr>
            </thead>
            <tbody>
                <?php 
                // Filter payments based on query parameters
                $filtered_payments = $payments;
                if (isset($_GET['method']) && !empty($_GET['method'])) {
                    $filtered_payments = array_filter($filtered_payments, function($payment) {
                        return $payment['payment_method'] == $_GET['method'];
                    });
                }
                if (isset($_GET['date_from']) && !empty($_GET['date_from'])) {
                    $filtered_payments = array_filter($filtered_payments, function($payment) {
                        return strtotime($payment['payment_date']) >= strtotime($_GET['date_from']);
                    });
                }
                if (isset($_GET['date_to']) && !empty($_GET['date_to'])) {
                    $filtered_payments = array_filter($filtered_payments, function($payment) {
                        return strtotime($payment['payment_date']) <= strtotime($_GET['date_to']);
                    });
                }
                ?>
                
                <?php if (count($filtered_payments) > 0): ?>
                    <?php foreach ($filtered_payments as $payment): ?>
                        <tr>
                            <td><?php echo $payment['id']; ?></td>
                            <td><?php echo $payment['first_name'] . ' ' . $payment['last_name']; ?></td>
                            <td><?php echo $payment['class_name'] ? $payment['class_name'] : 'N/A'; ?></td>
                            <td>$<?php echo number_format($payment['fee_amount'], 2); ?></td>
                            <td>$<?php echo number_format($payment['amount_paid'], 2); ?></td>
                            <td><?php echo date('M j, Y', strtotime($payment['payment_date'])); ?></td>
                            <td><?php echo ucfirst(str_replace('_', ' ', $payment['payment_method'])); ?></td>
                            <td><?php echo $payment['transaction_id'] ? $payment['transaction_id'] : 'N/A'; ?></td>
                            <td><?php echo $payment['recorded_by_name']; ?></td>
                            <td><?php echo date('M j, Y g:i A', strtotime($payment['created_at'])); ?></td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="10">No payment records found.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</main>

<style>
.status-badge {
    padding: 4px 8px;
    border-radius: 4px;
    font-size: 12px;
    font-weight: bold;
}

.status-fully_paid {
    background-color: #d4edda;
    color: #155724;
}

.status-partial {
    background-color: #fff3cd;
    color: #856404;
}

.status-not_paid {
    background-color: #f8d7da;
    color: #721c24;
}

.status-paid {
    background-color: #d4edda;
    color: #155724;
}

.status-unpaid {
    background-color: #f8d7da;
    color: #721c24;
}

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

.filter-form {
    display: flex;
    gap: 15px;
    flex-wrap: wrap;
    margin-bottom: 20px;
    align-items: flex-end;
}

.form-group {
    display: flex;
    flex-direction: column;
}

.form-group label {
    margin-bottom: 5px;
    font-weight: bold;
}

.form-group select,
.form-group input {
    padding: 8px;
    border: 1px solid #ddd;
    border-radius: 4px;
}

.alert {
    padding: 15px;
    margin-bottom: 20px;
    border: 1px solid transparent;
    border-radius: 4px;
}

.alert-warning {
    color: #856404;
    background-color: #fff3cd;
    border-color: #ffeaa7;
}

.btn {
    display: inline-block;
    padding: 8px 16px;
    background-color: #007bff;
    color: white;
    text-decoration: none;
    border-radius: 4px;
    border: none;
    cursor: pointer;
    font-size: 14px;
}

.btn-primary {
    background-color: #007bff;
}

.btn-secondary {
    background-color: #6c757d;
}

.btn:hover {
    opacity: 0.9;
}

.header-actions {
    display: flex;
    gap: 10px;
    align-items: center;
}

.section-header {
    display: flex;
    justify-content: between;
    align-items: center;
    margin-bottom: 20px;
}

.section-header h2 {
    margin: 0;
    flex-grow: 1;
}

.text-muted {
    color: #6c757d;
    font-style: italic;
}

/* Responsive design */
@media (max-width: 768px) {
    .dashboard-stats {
        grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
    }
    
    .filter-form {
        flex-direction: column;
        align-items: stretch;
    }
    
    .form-group {
        width: 100%;
    }
    
    .section-header {
        flex-direction: column;
        gap: 10px;
        align-items: flex-start;
    }
}
</style>

<?php include '../includes/footer.php'; ?>