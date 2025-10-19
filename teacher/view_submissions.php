<?php
require_once '../config.php';
checkRole(['student']);

// Get student details
$student = getStudentDetails($_SESSION['user_id']);
$student_id = $student['id'];

// Get grades for the student
$stmt = $pdo->prepare("
    SELECT g.*, s.name as subject_name, s.code as subject_code
    FROM grades g
    JOIN subjects s ON g.subject_id = s.id
    WHERE g.student_id = ?
    ORDER BY g.academic_year DESC, g.term, s.name
");
$stmt->execute([$student_id]);
$grades = $stmt->fetchAll();

// Calculate GPA and statistics
$total_grade_points = 0;
$total_subjects = count($grades);
$grade_count = array_fill(0, 6, 0); // A, B, C, D, E, F

foreach ($grades as $grade) {
    $total_grade_points += $grade['grade'];
    
    // Count grades by category
    if ($grade['grade'] >= 90) $grade_count[0]++; // A
    elseif ($grade['grade'] >= 80) $grade_count[1]++; // B
    elseif ($grade['grade'] >= 70) $grade_count[2]++; // C
    elseif ($grade['grade'] >= 60) $grade_count[3]++; // D
    elseif ($grade['grade'] >= 50) $grade_count[4]++; // E
    else $grade_count[5]++; // F
}

$average_grade = $total_subjects > 0 ? round($total_grade_points / $total_subjects, 2) : 0;
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Grades - Student Dashboard</title>
    <link rel="stylesheet" href="../../assets/css/style.css">
    <link rel="stylesheet" href="../../assets/css/dashboard.css">
    <style>
        .grades-container {
            background: white;
            border-radius: 8px;
            padding: 20px;
            margin-bottom: 20px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        
        .stats-card {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 20px;
            border-radius: 8px;
            margin-bottom: 20px;
        }
        
        .grade-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 15px;
            border-bottom: 1px solid #eee;
        }
        
        .grade-value {
            font-weight: bold;
            font-size: 1.1em;
        }
        
        .grade-A { color: #28a745; }
        .grade-B { color: #17a2b8; }
        .grade-C { color: #ffc107; }
        .grade-D { color: #fd7e14; }
        .grade-E { color: #dc3545; }
        .grade-F { color: #6c757d; }
    </style>
</head>
<body>
    <?php include '../includes/header.php'; ?>
    <?php include '../includes/sidebar.php'; ?>
    
    <main class="main-content">
        <div class="content-header">
            <h1>My Grades</h1>
            <p>Academic Performance Overview</p>
        </div>

        <div class="stats-card">
            <h3>Academic Summary</h3>
            <p>Average Grade: <strong><?php echo $average_grade; ?>%</strong></p>
            <p>Total Subjects: <strong><?php echo $total_subjects; ?></strong></p>
            <p>Grade Distribution: 
                A(<?php echo $grade_count[0]; ?>) | 
                B(<?php echo $grade_count[1]; ?>) | 
                C(<?php echo $grade_count[2]; ?>) | 
                D(<?php echo $grade_count[3]; ?>) | 
                E(<?php echo $grade_count[4]; ?>) | 
                F(<?php echo $grade_count[5]; ?>)
            </p>
        </div>

        <?php if (count($grades) > 0): ?>
            <div class="grades-container">
                <h3>Subject Grades</h3>
                <?php 
                $current_year = '';
                $current_term = '';
                foreach ($grades as $grade): 
                    if ($current_year != $grade['academic_year'] || $current_term != $grade['term']):
                        if ($current_year != ''): ?>
                            </div>
                        <?php endif; ?>
                        <div style="margin-bottom: 20px;">
                            <h4><?php echo $grade['academic_year']; ?> - <?php echo $grade['term']; ?></h4>
                        <?php 
                        $current_year = $grade['academic_year'];
                        $current_term = $grade['term'];
                    endif;
                    
                    $grade_class = '';
                    if ($grade['grade'] >= 90) $grade_class = 'grade-A';
                    elseif ($grade['grade'] >= 80) $grade_class = 'grade-B';
                    elseif ($grade['grade'] >= 70) $grade_class = 'grade-C';
                    elseif ($grade['grade'] >= 60) $grade_class = 'grade-D';
                    elseif ($grade['grade'] >= 50) $grade_class = 'grade-E';
                    else $grade_class = 'grade-F';
                ?>
                
                <div class="grade-item">
                    <div>
                        <strong><?php echo $grade['subject_name']; ?> (<?php echo $grade['subject_code']; ?>)</strong>
                        <br>
                        <small>Last Updated: <?php echo date('M j, Y', strtotime($grade['created_at'])); ?></small>
                    </div>
                    <div class="grade-value <?php echo $grade_class; ?>">
                        <?php echo $grade['grade']; ?>%
                    </div>
                </div>
                <?php endforeach; ?>
                </div>
            </div>
        <?php else: ?>
            <div class="alert alert-info">
                <p>No grades have been recorded yet.</p>
            </div>
        <?php endif; ?>
    </main>
    
    <?php include '../includes/footer.php'; ?>
</body>
</html>