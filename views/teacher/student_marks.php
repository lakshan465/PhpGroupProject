<?php
require_once __DIR__ . '/../../includes/auth.php';
require_once __DIR__ . '/../../config/db.php';

// Check if user is teacher
requireRole('teacher');

$pageTitle = 'Student Marks - Teacher Dashboard';
$pdo = getDBConnection();

// Get all quizzes created by this teacher
$stmt = $pdo->prepare("
    SELECT id, title 
    FROM quizzes 
    WHERE teacher_id = ? 
    ORDER BY title ASC
");
$stmt->execute([$_SESSION['user_id']]);
$teacher_quizzes = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Get selected quiz filter
$selected_quiz = isset($_GET['quiz_id']) ? (int)$_GET['quiz_id'] : null;

// Build query for student attempts
$query = "
    SELECT 
        qa.id as attempt_id,
        qa.quiz_id,
        qa.score,
        qa.completed_at,
        u.id as student_id,
        u.full_name as student_name,
        u.email as student_email,
        q.title as quiz_title,
        (SELECT COUNT(*) FROM questions WHERE quiz_id = qa.quiz_id) as total_questions,
        ROUND((qa.score / (SELECT COUNT(*) FROM questions WHERE quiz_id = qa.quiz_id)) * 100, 2) as percentage
    FROM quiz_attempts qa
    JOIN users u ON qa.student_id = u.id
    JOIN quizzes q ON qa.quiz_id = q.id
    WHERE q.teacher_id = ? AND qa.completed_at IS NOT NULL
";

$params = [$_SESSION['user_id']];

if ($selected_quiz) {
    $query .= " AND qa.quiz_id = ?";
    $params[] = $selected_quiz;
}

$query .= " ORDER BY qa.completed_at DESC";

$stmt = $pdo->prepare($query);
$stmt->execute($params);
$student_attempts = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Get summary statistics
$stats_query = "
    SELECT 
        COUNT(DISTINCT qa.student_id) as total_students,
        COUNT(qa.id) as total_attempts,
        ROUND(AVG((qa.score / (SELECT COUNT(*) FROM questions WHERE quiz_id = qa.quiz_id)) * 100), 2) as average_percentage,
        MAX((qa.score / (SELECT COUNT(*) FROM questions WHERE quiz_id = qa.quiz_id)) * 100) as highest_percentage,
        MIN((qa.score / (SELECT COUNT(*) FROM questions WHERE quiz_id = qa.quiz_id)) * 100) as lowest_percentage
    FROM quiz_attempts qa
    JOIN quizzes q ON qa.quiz_id = q.id
    WHERE q.teacher_id = ? AND qa.completed_at IS NOT NULL
";

$stats_params = [$_SESSION['user_id']];

if ($selected_quiz) {
    $stats_query .= " AND qa.quiz_id = ?";
    $stats_params[] = $selected_quiz;
}

$stmt = $pdo->prepare($stats_query);
$stmt->execute($stats_params);
$stats = $stmt->fetch(PDO::FETCH_ASSOC);

// Function to get grade based on percentage
function getGrade($percentage) {
    if ($percentage >= 90) return ['A+', 'success'];
    if ($percentage >= 80) return ['A', 'success'];
    if ($percentage >= 70) return ['B', 'info'];
    if ($percentage >= 60) return ['C', 'warning'];
    if ($percentage >= 50) return ['D', 'warning'];
    return ['F', 'danger'];
}

include __DIR__ . '/../../templates/header.php';
?>

<main class="col-md-9 ms-sm-auto col-lg-10 px-md-4">
    <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
        <h1 class="h2">
            <i class="fas fa-chart-line me-2"></i>
            Student Marks & Performance
        </h1>
        <div class="btn-toolbar mb-2 mb-md-0">
            <button type="button" class="btn btn-outline-primary" onclick="window.print()">
                <i class="fas fa-print me-1"></i>Print Report
            </button>
        </div>
    </div>

    <!-- Filter Section -->
    <div class="row mb-4">
        <div class="col-md-6">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">
                        <i class="fas fa-filter me-2"></i>Filter Results
                    </h5>
                    <form method="GET" class="row g-3">
                        <div class="col-md-8">
                            <label for="quiz_id" class="form-label">Select Quiz</label>
                            <select class="form-select" id="quiz_id" name="quiz_id">
                                <option value="">All Quizzes</option>
                                <?php foreach ($teacher_quizzes as $quiz): ?>
                                    <option value="<?php echo $quiz['id']; ?>" 
                                            <?php echo $selected_quiz == $quiz['id'] ? 'selected' : ''; ?>>
                                        <?php echo htmlspecialchars($quiz['title']); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-4 d-flex align-items-end">
                            <button type="submit" class="btn btn-primary me-2">
                                <i class="fas fa-search me-1"></i>Filter
                            </button>
                            <a href="student_marks.php" class="btn btn-outline-secondary">
                                <i class="fas fa-times me-1"></i>Clear
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        
        <!-- Statistics Summary -->
        <div class="col-md-6">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">
                        <i class="fas fa-chart-bar me-2"></i>Performance Summary
                    </h5>
                    <div class="row text-center">
                        <div class="col-6">
                            <h4 class="text-primary"><?php echo $stats['total_students'] ?: 0; ?></h4>
                            <small class="text-muted">Students</small>
                        </div>
                        <div class="col-6">
                            <h4 class="text-info"><?php echo $stats['total_attempts'] ?: 0; ?></h4>
                            <small class="text-muted">Attempts</small>
                        </div>
                    </div>
                    <hr>
                    <div class="row text-center">
                        <div class="col-4">
                            <h5 class="text-success"><?php echo $stats['average_percentage'] ?: 0; ?>%</h5>
                            <small class="text-muted">Average</small>
                        </div>
                        <div class="col-4">
                            <h5 class="text-warning"><?php echo $stats['highest_percentage'] ?: 0; ?>%</h5>
                            <small class="text-muted">Highest</small>
                        </div>
                        <div class="col-4">
                            <h5 class="text-danger"><?php echo $stats['lowest_percentage'] ?: 0; ?>%</h5>
                            <small class="text-muted">Lowest</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Student Marks Table -->
    <div class="card">
        <div class="card-header">
            <h5 class="mb-0">
                <i class="fas fa-users me-2"></i>
                Student Marks
                <?php if ($selected_quiz): ?>
                    <span class="badge bg-primary ms-2">
                        <?php 
                        $quiz_title = array_filter($teacher_quizzes, function($q) use ($selected_quiz) {
                            return $q['id'] == $selected_quiz;
                        });
                        echo htmlspecialchars(current($quiz_title)['title']);
                        ?>
                    </span>
                <?php endif; ?>
            </h5>
        </div>
        <div class="card-body">
            <?php if (empty($student_attempts)): ?>
                <div class="text-center py-5">
                    <i class="fas fa-chart-line text-muted" style="font-size: 3rem;"></i>
                    <h4 class="mt-3 text-muted">No Results Found</h4>
                    <p class="text-muted">
                        <?php if ($selected_quiz): ?>
                            No students have completed this quiz yet.
                        <?php else: ?>
                            No students have completed any of your quizzes yet.
                        <?php endif; ?>
                    </p>
                </div>
            <?php else: ?>
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead class="table-light">
                            <tr>
                                <th>Student Name</th>
                                <th>Email</th>
                                <th>Quiz</th>
                                <th>Score</th>
                                <th>Percentage</th>
                                <th>Grade</th>
                                <th>Completed</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($student_attempts as $attempt): ?>
                                <?php 
                                $grade_info = getGrade($attempt['percentage']);
                                $grade = $grade_info[0];
                                $grade_class = $grade_info[1];
                                ?>
                                <tr>
                                    <td>
                                        <i class="fas fa-user-graduate me-2 text-primary"></i>
                                        <?php echo htmlspecialchars($attempt['student_name']); ?>
                                    </td>
                                    <td><?php echo htmlspecialchars($attempt['student_email']); ?></td>
                                    <td>
                                        <small class="text-muted">
                                            <?php echo htmlspecialchars($attempt['quiz_title']); ?>
                                        </small>
                                    </td>
                                    <td>
                                        <strong><?php echo $attempt['score']; ?></strong>
                                        <small class="text-muted">/ <?php echo $attempt['total_questions']; ?></small>
                                    </td>
                                    <td>
                                        <div class="progress" style="height: 20px;">
                                            <div class="progress-bar bg-<?php echo $grade_class; ?>" 
                                                 role="progressbar" 
                                                 style="width: <?php echo $attempt['percentage']; ?>%"
                                                 aria-valuenow="<?php echo $attempt['percentage']; ?>" 
                                                 aria-valuemin="0" 
                                                 aria-valuemax="100">
                                                <?php echo $attempt['percentage']; ?>%
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <span class="badge bg-<?php echo $grade_class; ?>">
                                            <?php echo $grade; ?>
                                        </span>
                                    </td>
                                    <td>
                                        <small class="text-muted">
                                            <?php echo date('M j, Y g:i A', strtotime($attempt['completed_at'])); ?>
                                        </small>
                                    </td>
                                    <td>
                                        <div class="btn-group btn-group-sm">
                                            <a href="view_attempt_details.php?attempt_id=<?php echo $attempt['attempt_id']; ?>" 
                                               class="btn btn-outline-primary btn-sm" 
                                               title="View Details">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <a href="mailto:<?php echo htmlspecialchars($attempt['student_email']); ?>" 
                                               class="btn btn-outline-info btn-sm" 
                                               title="Email Student">
                                                <i class="fas fa-envelope"></i>
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </div>
    </div>
</main>

<style>
.progress {
    background-color: #e9ecef;
}

.table th {
    font-weight: 600;
    color: #495057;
    border-top: none;
}

.table td {
    vertical-align: middle;
}

.btn-group-sm .btn {
    padding: 0.25rem 0.5rem;
}

@media print {
    .btn-toolbar, .navbar, .sidebar {
        display: none !important;
    }
    
    .main {
        margin: 0 !important;
        padding: 0 !important;
    }
    
    .card {
        border: 1px solid #dee2e6 !important;
        box-shadow: none !important;
    }
}
</style>

<?php include __DIR__ . '/../../templates/footer.php'; ?>
