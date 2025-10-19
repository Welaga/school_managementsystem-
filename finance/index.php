<?php
require_once '../config.php';
checkRole(['finance']);

// Get statistics
$stmt = $pdo->query("SELECT COUNT(*) as count FROM fees");
$fees_count = $stmt->fetch()['count'];

$stmt = $pdo->query("SELECT SUM(amount) as total FROM fees WHERE status = 'paid'");
$total_paid = $stmt->fetch()['total'] ?? 0;

$stmt = $pdo->query("SELECT SUM(amount) as total FROM fees WHERE status = 'pending'");
$total_pending = $stmt->fetch()['total'] ?? 0;

$stmt = $pdo->query("SELECT SUM(amount) as total FROM fees WHERE status = 'overdue'");
$total_overdue = $stmt->fetch()['total'] ?? 0;

// Get recent payments
$stmt = $pdo->query("SELECT f.amount, f.status, s.first_name, s.last_name, f.due_date 
                    FROM fees f 
                    JOIN students s ON f.student_id = s.id 
                    ORDER BY f.created_at DESC LIMIT 5");
$recent_fees = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Finance Dashboard - School Management System</title>
    <link rel="stylesheet" href="../../assets/css/style.css">
    <link rel="stylesheet" href="../../assets/css/dashboard.css">
</head>
<body>
    <?php include '../includes/header.php'; ?>
    <?php include '../includes/sidebar.php'; ?>
    
    <main class="main-content">
        <div class="content-header">
            <h1>Finance Dashboard</h1>
        </div>
        
        <div class="dashboard-stats">
            <div class="stat-card">
                <h3>Total Fees</h3>
                <p><?php echo $fees_count; ?></p>
            </div>
            
            <div class="stat-card">
                <h3>Total Paid</h3>
                <p>$<?php echo number_format($total_paid, 2); ?></p>
            </div>
            
            <div class="stat-card">
                <h3>Total Pending</h3>
                <p>$<?php echo number_format($total_pending, 2); ?></p>
            </div>
            
            <div class="stat-card">
                <h3>Total Overdue</h3>
                <p>$<?php echo number_format($total_overdue, 2); ?></p>
            </div>
        </div>
        
        <div class="dashboard-actions">
            <h2>Quick Actions</h2>
            <div class="action-buttons">
                <a href="../finance/fee_management.php" class="btn btn-primary">Fee Management</a>
                <a href="../finance/invoices.php" class="btn btn-secondary">Invoices</a>
                <a href="../finance/payment_records.php" class="btn btn-success">Payment Records</a>
            </div>
        </div>
        
        <div class="recent-activities">
            <h2>Recent Fee Records</h2>
            <div class="activity-list">
                <?php if (count($recent_fees) > 0): ?>
                    <?php foreach ($recent_fees as $fee): ?>
                        <div class="activity-item">
                            <p><?php echo $fee['first_name'] . ' ' . $fee['last_name']; ?> - $<?php echo number_format($fee['amount'], 2); ?> (<?php echo ucfirst($fee['status']); ?>)</p>
                            <span class="activity-time">Due: <?php echo date('M j, Y', strtotime($fee['due_date'])); ?></span>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <p>No recent fee records.</p>
                <?php endif; ?>
            </div>
        </div>
    </main>
    
    <?php include '../includes/footer.php'; ?>
</body>
</html>