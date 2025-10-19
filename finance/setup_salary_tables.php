<?php
// setup_salary_tables.php
require_once '../config.php';
checkRole(['admin', 'finance']);

$page_title = "Setup Salary Tables";

$success = false;
$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['setup_tables'])) {
    try {
        // Start transaction
        $pdo->beginTransaction();

        // Create staff_salaries table
        $pdo->exec("CREATE TABLE IF NOT EXISTS staff_salaries (
            id INT PRIMARY KEY AUTO_INCREMENT,
            staff_id INT NOT NULL,
            monthly_salary DECIMAL(10,2) NOT NULL,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            FOREIGN KEY (staff_id) REFERENCES users(id) ON DELETE CASCADE
        )");

        // Create salary_payments table
        $pdo->exec("CREATE TABLE IF NOT EXISTS salary_payments (
            id INT PRIMARY KEY AUTO_INCREMENT,
            staff_id INT NOT NULL,
            amount_paid DECIMAL(10,2) NOT NULL,
            payment_date DATE NOT NULL,
            month INT NOT NULL,
            year INT NOT NULL,
            payment_method ENUM('cash', 'bank_transfer', 'check', 'online') DEFAULT 'bank_transfer',
            transaction_id VARCHAR(100),
            recorded_by INT,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (staff_id) REFERENCES users(id) ON DELETE CASCADE,
            FOREIGN KEY (recorded_by) REFERENCES users(id) ON DELETE SET NULL
        )");

        // Insert sample salary data for existing staff
        $users_stmt = $pdo->query("SELECT id, role FROM users WHERE role IN ('admin', 'teacher', 'staff')");
        $users = $users_stmt->fetchAll();

        foreach ($users as $user) {
            $salary = 0;
            switch($user['role']) {
                case 'admin': $salary = 5000.00; break;
                case 'teacher': $salary = 3000.00; break;
                case 'staff': $salary = 2000.00; break;
                default: $salary = 1500.00;
            }

            $stmt = $pdo->prepare("INSERT INTO staff_salaries (staff_id, monthly_salary) VALUES (?, ?)");
            $stmt->execute([$user['id'], $salary]);
        }

        $pdo->commit();
        $success = true;

    } catch (PDOException $e) {
        $pdo->rollBack();
        $errors[] = "Error setting up tables: " . $e->getMessage();
    }
}

// Check if tables already exist
$tables_exist = false;
try {
    $pdo->query("SELECT 1 FROM staff_salaries LIMIT 1");
    $pdo->query("SELECT 1 FROM salary_payments LIMIT 1");
    $tables_exist = true;
} catch (PDOException $e) {
    $tables_exist = false;
}
?>

<?php include '../includes/header.php'; ?>
<?php include '../includes/sidebar.php'; ?>

<main class="main-content">
    <div class="content-header">
        <h1>Setup Salary Management System</h1>
    </div>

    <?php if ($success): ?>
        <div class="alert alert-success">
            <strong>Success!</strong> Salary tables have been created successfully.
            <div style="margin-top: 10px;">
                <a href="payment_records.php" class="btn btn-primary">Go to Payment Records</a>
                <a href="salary_management.php" class="btn btn-secondary">Manage Salaries</a>
            </div>
        </div>
    <?php endif; ?>

    <?php if (!empty($errors)): ?>
        <div class="alert alert-danger">
            <strong>Errors occurred:</strong>
            <ul>
                <?php foreach ($errors as $error): ?>
                    <li><?php echo $error; ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>

    <?php if ($tables_exist && !$success): ?>
        <div class="alert alert-info">
            <strong>Info:</strong> Salary tables already exist.
            <div style="margin-top: 10px;">
                <a href="payment_records.php" class="btn btn-primary">Go to Payment Records</a>
                <a href="salary_management.php" class="btn btn-secondary">Manage Salaries</a>
            </div>
        </div>
    <?php endif; ?>

    <?php if (!$tables_exist && !$success): ?>
        <div class="setup-instructions">
            <div class="alert alert-warning">
                <h3>Setup Required</h3>
                <p>This will create the necessary tables for staff salary management:</p>
                <ul>
                    <li><strong>staff_salaries</strong> - Stores staff salary information</li>
                    <li><strong>salary_payments</strong> - Records salary payment history</li>
                </ul>
                <p>Sample salary data will be added for existing staff members based on their roles:</p>
                <ul>
                    <li>Admin: $5,000.00</li>
                    <li>Teacher: $3,000.00</li>
                    <li>Staff: $2,000.00</li>
                </ul>
            </div>

            <form method="POST">
                <button type="submit" name="setup_tables" class="btn btn-primary btn-lg">
                    Setup Salary Tables
                </button>
                <a href="payment_records.php" class="btn btn-secondary">Cancel</a>
            </form>
        </div>
    <?php endif; ?>
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

.alert-danger {
    color: #721c24;
    background-color: #f8d7da;
    border-color: #f5c6cb;
}

.alert-warning {
    color: #856404;
    background-color: #fff3cd;
    border-color: #ffeaa7;
}

.alert-info {
    color: #0c5460;
    background-color: #d1ecf1;
    border-color: #bee5eb;
}

.btn {
    display: inline-block;
    padding: 10px 20px;
    margin: 5px;
    border: none;
    border-radius: 4px;
    text-decoration: none;
    cursor: pointer;
}

.btn-primary {
    background-color: #007bff;
    color: white;
}

.btn-secondary {
    background-color: #6c757d;
    color: white;
}

.btn-lg {
    padding: 12px 24px;
    font-size: 16px;
}

.setup-instructions {
    max-width: 800px;
}
</style>

<?php include '../includes/footer.php'; ?>