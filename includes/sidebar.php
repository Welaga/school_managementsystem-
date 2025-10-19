<nav class="sidebar">
    <ul class="sidebar-menu">
        <?php if ($_SESSION['role'] == 'admin'): ?>
            <li><a href="../admin/" class="<?php echo basename($_SERVER['PHP_SELF']) == 'index.php' ? 'active' : ''; ?>">Dashboard</a></li>
            <li><a href="../admin/teacher_management.php">Teacher</a></li>
            <li><a href="../admin/manage_users.php">Manage Users</a></li>
            <li><a href="../admin/system_settings.php">System Settings</a></li>
            <li><a href="../admin/register_student.php">Students</a></li>
            <li><a href="../admin/manage_classes.php">Classes</a></li>
            <li><a href="../finance/payment_records.php">Fees Report</a></li>
            <li><a href="../admin/registrar_cleaner.php">Cleaner</a></li>
            <li><a href="../admin/transport_management.php" class="<?php echo basename($_SERVER['PHP_SELF']) == 'transport_management.php' ? 'active' : ''; ?>"><i class="fas fa-bus"></i> Transport Management</a></li>
            <li><a href="../admin/profile.php">Profile</a></li>
            <li><a href="../admin/change_password.php">Change Password</a></li>
        <?php elseif ($_SESSION['role'] == 'registrar'): ?>
            <li><a href="../registrar/" class="<?php echo basename($_SERVER['PHP_SELF']) == 'index.php' ? 'active' : ''; ?>">Dashboard</a></li>
            <li><a href="../admin/teacher_management.php">Teacher</a></li>
            <li><a href="../registrar/manage_students.php">Manage Students</a></li>
            <li><a href="../registrar/manage_classes.php">Manage Classes</a></li>
            <li><a href="../registrar/manage_courses.php">Manage Courses</a></li>
             <li><a href="../change_password.php">Change Password</a></li>
        <?php elseif ($_SESSION['role'] == 'finance'): ?>
            <li><a href="../finance/" class="<?php echo basename($_SERVER['PHP_SELF']) == 'index.php' ? 'active' : ''; ?>">Dashboard</a></li>
            <li><a href="../finance/fee_management.php">Fee Management</a></li>
            <li><a href="../finance/invoices.php">Invoices</a></li>
            <div class="section-header">
    <h2>Staff Salary Payments</h2>
    <div>
        <a href="salary_management.php" class="btn btn-primary">Manage Salaries</a>
    </div>
</div>
            <li><a href="../finance/payment_records.php">Payment Records</a></li>
             <li><a href="../change_password.php">Change Password</a></li>
        <?php elseif ($_SESSION['role'] == 'teacher'): ?>
            <li><a href="../teacher/" class="<?php echo basename($_SERVER['PHP_SELF']) == 'index.php' ? 'active' : ''; ?>">Dashboard</a></li>
            <li><a href="../teacher/upload_grades.php">Upload Grades</a></li>
            <li><a href="../teacher/manage_attendance.php">Manage Attendance</a></li>
            <li><a href="../teacher/assignments.php">Assignments</a></li>
             <li><a href="../change_password.php">Change Password</a></li>
        <?php elseif ($_SESSION['role'] == 'student'): ?>
            <li><a href="index.php" class="active"><i class="fas fa-tachometer-alt"></i> Dashboard</a></li>
            <li><a href="view_grades.php"><i class="fas fa-chart-bar"></i> View Grades</a></li>
            <li><a href="view_attendance.php"><i class="fas fa-calendar-check"></i> Attendance</a></li>
            <li><a href="assignments.php"><i class="fas fa-tasks"></i> Assignments</a></li>
            <li><a href="timetable.php"><i class="fas fa-calendar-alt"></i> Timetable</a></li>
            <li><a href="profile.php"><i class="fas fa-user"></i> Profile</a></li>
            <li><a href="../logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
        <?php elseif ($_SESSION['role'] == 'cleaner'): ?>
            <li><a href="../cleaner/" class="<?php echo basename($_SERVER['PHP_SELF']) == 'index.php' ? 'active' : ''; ?>">Dashboard</a></li>
            <li><a href="../cleaner/cleaning_schedule.php">Cleaning Schedule</a></li>
             <li><a href="../change_password.php">Change Password</a></li>
        <?php elseif ($_SESSION['role'] == 'transport'): ?>
            <li><a href="../transport/" class="<?php echo basename($_SERVER['PHP_SELF']) == 'index.php' ? 'active' : ''; ?>">Dashboard</a></li>
            <li><a href="../transport/manage_vehicles.php">Manage Vehicles</a></li>
            <li><a href="../transport/manage_drivers.php">Manage Drivers</a></li>
            <li><a href="../transport/transport_schedule.php">Transport Schedule</a></li>
             <li><a href="../change_password.php">Change Password</a></li>
        <?php endif; ?>
    </ul>
</nav>