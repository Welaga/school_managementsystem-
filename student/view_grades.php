<?php
require_once '../config.php';
checkRole(['admin', 'student']);

$page_title = "View Grades";

// Get student details
$student = getStudentDetails($_SESSION['user_id']);
$student_id = $student['id'];

// Get grades
$stmt = $pdo->prepare("SELECT g.*, s.name as subject_name FROM grades g 
                      JOIN subjects s ON g.subject_id = s.id 
                      WHERE g.student_id = ? ORDER BY g.academic_year, g.term, s.name");
$stmt->execute([$student_id]);
$grades = $stmt->fetchAll();

// Group grades by academic year and term
$grouped_grades = [];
foreach ($grades as $grade) {
    $key = $grade['academic_year'] . '|' . $grade['term'];
    if (!isset($grouped_grades[$key])) {
        $grouped_grades[$key] = [
            'academic_year' => $grade['academic_year'],
            'term' => $grade['term'],
            'subjects' => []
        ];
    }
    $grouped_grades[$key]['subjects'][] = $grade;
}

// Calculate averages
foreach ($grouped_grades as &$term_grades) {
    $total = 0;
    $count = 0;
    foreach ($term_grades['subjects'] as $subject) {
        $total += $subject['grade'];
        $count++;
    }
    $term_grades['average'] = $count > 0 ? round($total / $count, 2) : 0;
}
?>

<?php include '../includes/header.php'; ?>
<?php include '../includes/sidebar.php'; ?>

<main class="main-content">
    <div class="content-header">
        <h1>My Grades</h1>
        
        <p>Welcome, <?php echo $student['first_name'] . ' ' . $student['last_name']; ?> (Class: <?php echo $student['class_name']; ?>)</p>
    </div>
    
    <?php if (count($grouped_grades) > 0): ?>
        <?php foreach ($grouped_grades as $term): ?>
            <div class="table-container">
                <h2><?php echo $term['academic_year']; ?> - <?php echo $term['term']; ?></h2>
                <table>
                    <thead>
                        <tr>
                            <th>Subject</th>
                            <th>Grade</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($term['subjects'] as $subject): ?>
                            <tr>
                                <td><?php echo $subject['subject_name']; ?></td>
                                <td><?php echo $subject['grade']; ?></td>
                                <td>
                                    <span class="status-badge <?php echo $subject['grade'] >= 60 ? 'completed' : 'pending'; ?>">
                                        <?php echo $subject['grade'] >= 60 ? 'Pass' : 'Fail'; ?>
                                    </span>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                        <tr class="summary-row">
                            <td><strong>Average</strong></td>
                            <td><strong><?php echo $term['average']; ?></strong></td>
                            <td>
                                <span class="status-badge <?php echo $term['average'] >= 60 ? 'completed' : 'pending'; ?>">
                                    <?php echo $term['average'] >= 60 ? 'Pass' : 'Fail'; ?>
                                </span>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        <?php endforeach; ?>
    <?php else: ?>
        <div class="alert alert-info">
            No grades available yet.
        </div>
    <?php endif; ?>
</main>

<?php include '../includes/footer.php'; ?>