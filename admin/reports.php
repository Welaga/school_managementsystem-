<?php
require_once '../config.php';
checkRole(['admin']);

$page_title = "Reports & Analytics";
$message = '';

// Get report parameters
$report_type = $_GET['report_type'] ?? 'overview';
$start_date = $_GET['start_date'] ?? date('Y-m-01');
$end_date = $_GET['end_date'] ?? date('Y-m-t');
$class_id = $_GET['class_id'] ?? '';

// Get overview statistics
$stmt = $pdo->query("SELECT COUNT(*) as count FROM users WHERE status = 1");
$total_users = $stmt->fetch()['count'];

$stmt = $pdo->query("SELECT COUNT(*) as count FROM students");
$total_students = $stmt->fetch()['count'];

$stmt = $pdo->query("SELECT COUNT(*) as count FROM teachers");
$total_teachers = $stmt->fetch()['count'];

$stmt = $pdo->query("SELECT COUNT(*) as count FROM classes");
$total_classes = $stmt->fetch()['count'];

$stmt = $pdo->query("SELECT SUM(amount) as total FROM fees WHERE status = 'paid'");
$total_revenue = $stmt->fetch()['total'] ?? 0;

// Get financial report data
if ($report_type == 'financial') {
    $stmt = $pdo->prepare("SELECT f.*, s.first_name, s.last_name, c.name as class_name 
                          FROM fees f 
                          JOIN students s ON f.student_id = s.id 
                          LEFT JOIN classes c ON s.class_id = c.id 
                          WHERE f.created_at BETWEEN ? AND ? 
                          " . ($class_id ? "AND s.class_id = ?" : "") . "
                          ORDER BY f.created_at DESC");
    $params = [$start_date, $end_date];
    if ($class_id) {
        $params[] = $class_id;
    }
    $stmt->execute($params);
    $financial_data = $stmt->fetchAll();
}

// Get attendance report data
if ($report_type == 'attendance') {
    $stmt = $pdo->prepare("SELECT a.*, s.first_name, s.last_name, c.name as class_name, sub.name as subject_name 
                          FROM attendance a 
                          JOIN students s ON a.student_id = s.id 
                          LEFT JOIN classes c ON s.class_id = c.id 
                          JOIN subjects sub ON a.subject_id = sub.id 
                          WHERE a.date BETWEEN ? AND ? 
                          " . ($class_id ? "AND s.class_id = ?" : "") . "
                          ORDER BY a.date DESC");
    $params = [$start_date, $end_date];
    if ($class_id) {
        $params[] = $class_id;
    }
    $stmt->execute($params);
    $attendance_data = $stmt->fetchAll();
}

// Get classes for filter
$stmt = $pdo->query("SELECT * FROM classes ORDER BY name");
$classes = $stmt->fetchAll();
?>

<?php include '../includes/header.php'; ?>
<?php include '../includes/sidebar.php'; ?>

<main class="main-content">
    <div class="content-header">
        <h1>Reports & Analytics</h1>
    </div>
    
    <div class="dashboard-stats">
        <div class="stat-card">
            <h3>Total Users</h3>
            <p><?php echo $total_users; ?></p>
        </div>
        
        <div class="stat-card">
            <h3>Students</h3>
            <p><?php echo $total_students; ?></p>
        </div>
        
        <div class="stat-card">
            <h3>Teachers</h3>
            <p><?php echo $total_teachers; ?></p>
        </div>
        
        <div class="stat-card">
            <h3>Classes</h3>
            <p><?php echo $total_classes; ?></p>
        </div>
        
        <div class="stat-card">
            <h3>Total Revenue</h3>
            <p>$<?php echo number_format($total_revenue, 2); ?></p>
        </div>
    </div>
    
    <div class="filter-section">
        <h2>Generate Report</h2>
        <form method="GET" class="filter-form">
            <div class="form-group">
                <label for="report_type">Report Type</label>
                <select id="report_type" name="report_type" onchange="this.form.submit()">
                    <option value="overview" <?php echo $report_type == 'overview' ? 'selected' : ''; ?>>Overview</option>
                    <option value="financial" <?php echo $report_type == 'financial' ? 'selected' : ''; ?>>Financial Report</option>
                    <option value="attendance" <?php echo $report_type == 'attendance' ? 'selected' : ''; ?>>Attendance Report</option>
                </select>
            </div>
            
            <div class="form-group">
                <label for="start_date">Start Date</label>
                <input type="date" id="start_date" name="start_date" 
                       value="<?php echo $start_date; ?>" onchange="this.form.submit()">
            </div>
            
            <div class="form-group">
                <label for="end_date">End Date</label>
                <input type="date" id="end_date" name="end_date" 
                       value="<?php echo $end_date; ?>" onchange="this.form.submit()">
            </div>
            
            <?php if ($report_type != 'overview'): ?>
                <div class="form-group">
                    <label for="class_id">Filter by Class</label>
                    <select id="class_id" name="class_id" onchange="this.form.submit()">
                        <option value="">All Classes</option>
                        <?php foreach ($classes as $class): ?>
                            <option value="<?php echo $class['id']; ?>" 
                                <?php echo $class_id == $class['id'] ? 'selected' : ''; ?>>
                                <?php echo $class['name']; ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
            <?php endif; ?>
            
            <div class="form-group">
                <button type="submit" class="btn btn-primary">Generate Report</button>
                <button type="button" onclick="window.print()" class="btn btn-secondary">Print Report</button>
            </div>
        </form>
    </div>
    
    <?php if ($report_type == 'financial'): ?>
        <div class="table-container">
            <h2>Financial Report (<?php echo date('M j, Y', strtotime($start_date)); ?> to <?php echo date('M j, Y', strtotime($end_date)); ?>)</h2>
            
            <?php if (count($financial_data) > 0): ?>
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
                            <th>Created</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php 
                        $total_amount = 0;
                        $paid_amount = 0;
                        $pending_amount = 0;
                        ?>
                        
                        <?php foreach ($financial_data as $fee): ?>
                            <?php
                            $total_amount += $fee['amount'];
                            if ($fee['status'] == 'paid') {
                                $paid_amount += $fee['amount'];
                            } else {
                                $pending_amount += $fee['amount'];
                            }
                            ?>
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
                                <td><?php echo date('M j, Y', strtotime($fee['created_at'])); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                    <tfoot>
                        <tr class="summary-row">
                            <td colspan="3"><strong>Totals:</strong></td>
                            <td><strong>$<?php echo number_format($total_amount, 2); ?></strong></td>
                            <td colspan="2"></td>
                            <td><strong>Paid: $<?php echo number_format($paid_amount, 2); ?></strong></td>
                            <td><strong>Pending: $<?php echo number_format($pending_amount, 2); ?></strong></td>
                            <td></td>
                        </tr>
                    </tfoot>
                </table>
            <?php else: ?>
                <div class="alert alert-info">
                    No financial data found for the selected period.
                </div>
            <?php endif; ?>
        </div>
    
    <?php elseif ($report_type == 'attendance'): ?>
        <div class="table-container">
            <h2>Attendance Report (<?php echo date('M j, Y', strtotime($start_date)); ?> to <?php echo date('M j, Y', strtotime($end_date)); ?>)</h2>
            
            <?php if (count($attendance_data) > 0): ?>
                <table>
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>Student</th>
                            <th>Class</th>
                            <th>Subject</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php 
                        $present_count = 0;
                        $absent_count = 0;
                        $late_count = 0;
                        $total_records = count($attendance_data);
                        ?>
                        
                        <?php foreach ($attendance_data as $attendance): ?>
                            <?php
                            if ($attendance['status'] == 'present') {
                                $present_count++;
                            } elseif ($attendance['status'] == 'absent') {
                                $absent_count++;
                            } elseif ($attendance['status'] == 'late') {
                                $late_count++;
                            }
                            ?>
                            <tr>
                                <td><?php echo date('M j, Y', strtotime($attendance['date'])); ?></td>
                                <td><?php echo $attendance['first_name'] . ' ' . $attendance['last_name']; ?></td>
                                <td><?php echo $attendance['class_name'] ? $attendance['class_name'] : 'N/A'; ?></td>
                                <td><?php echo $attendance['subject_name']; ?></td>
                                <td>
                                    <span class="status-badge <?php echo $attendance['status']; ?>">
                                        <?php echo ucfirst($attendance['status']); ?>
                                    </span>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                    <tfoot>
                        <tr class="summary-row">
                            <td colspan="3"><strong>Summary:</strong></td>
                            <td><strong>Total Records: <?php echo $total_records; ?></strong></td>
                            <td>
                                <strong>
                                    Present: <?php echo $present_count; ?> (<?php echo $total_records > 0 ? round(($present_count / $total_records) * 100, 2) : 0; ?>%)<br>
                                    Absent: <?php echo $absent_count; ?> (<?php echo $total_records > 0 ? round(($absent_count / $total_records) * 100, 2) : 0; ?>%)<br>
                                    Late: <?php echo $late_count; ?> (<?php echo $total_records > 0 ? round(($late_count / $total_records) * 100, 2) : 0; ?>%)
                                </strong>
                            </td>
                        </tr>
                    </tfoot>
                </table>
            <?php else: ?>
                <div class="alert alert-info">
                    No attendance data found for the selected period.
                </div>
            <?php endif; ?>
        </div>
    
    <?php else: ?>
        <div class="card-grid">
            <div class="card">
                <h3>User Statistics</h3>
                <div class="stat-chart">
                    <canvas id="userStatsChart" width="400" height="200"></canvas>
                </div>
            </div>
            
            <div class="card">
                <h3>Revenue Overview</h3>
                <div class="stat-chart">
                    <canvas id="revenueChart" width="400" height="200"></canvas>
                </div>
            </div>
        </div>
    <?php endif; ?>
</main>

<?php include '../includes/footer.php'; ?>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
// User Statistics Chart
const userStatsCtx = document.getElementById('userStatsChart').getContext('2d');
const userStatsChart = new Chart(userStatsCtx, {
    type: 'doughnut',
    data: {
        labels: ['Students', 'Teachers', 'Other Staff'],
        datasets: [{
            data: [<?php echo $total_students; ?>, <?php echo $total_teachers; ?>, <?php echo $total_users - $total_students - $total_teachers; ?>],
            backgroundColor: ['#4e73df', '#1cc88a', '#36b9cc'],
            hoverBackgroundColor: ['#2e59d9', '#17a673', '#2c9faf'],
            hoverBorderColor: "rgba(234, 236, 244, 1)",
        }]
    },
    options: {
        maintainAspectRatio: false,
        plugins: {
            legend: {
                position: 'bottom'
            }
        }
    }
});

// Revenue Chart (last 6 months)
const revenueCtx = document.getElementById('revenueChart').getContext('2d');
const revenueChart = new Chart(revenueCtx, {
    type: 'bar',
    data: {
        labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun'],
        datasets: [{
            label: 'Revenue',
            backgroundColor: '#4e73df',
            hoverBackgroundColor: '#2e59d9',
            borderColor: '#4e73df',
            data: [6500, 7200, 6800, 7500, 8200, 9000],
        }]
    },
    options: {
        maintainAspectRatio: false,
        scales: {
            y: {
                beginAtZero: true,
                ticks: {
                    callback: function(value) {
                        return '$' + value;
                    }
                }
            }
        }
    }
});
</script>