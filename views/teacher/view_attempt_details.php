<?php
require_once __DIR__ . '/../../includes/auth.php';
require_once __DIR__ . '/../../config/db.php';

// Check if user is teacher
requireRole('teacher');

$attempt_id = isset($_GET['attempt_id']) ? (int)$_GET['attempt_id'] : 0;
$pdo = getDBConnection();

// Get attempt details - verify the quiz belongs to this teacher
$attemptStmt = $pdo->prepare("
    SELECT 
        qa.*,
        q.title as quiz_title,
        q.description as quiz_description,
        u.full_name as student_name,
        u.email as student_email,
        (SELECT COUNT(*) FROM questions WHERE quiz_id = qa.quiz_id) as total_questions,
        ROUND((qa.score / (SELECT COUNT(*) FROM questions WHERE quiz_id = qa.quiz_id)) * 100, 2) as percentage
    FROM quiz_attempts qa 
    JOIN quizzes q ON qa.quiz_id = q.id 
    JOIN users u ON qa.student_id = u.id 
    WHERE qa.id = ? AND q.teacher_id = ? AND qa.completed_at IS NOT NULL
");
$attemptStmt->execute([$attempt_id, $_SESSION['user_id']]);
$attempt = $attemptStmt->fetch(PDO::FETCH_ASSOC);

if (!$attempt) {
    header('Location: student_marks.php');
    exit;
}

// Get all answers with questions for this attempt
$answersStmt = $pdo->prepare("
    SELECT 
        sa.*,
        q.question_text,
        q.option_a,
        q.option_b,
        q.option_c,
        q.option_d,
        q.correct_answer,
        CASE 
            WHEN sa.selected_answer = q.correct_answer THEN 1 
            ELSE 0 
        END as is_correct
    FROM student_answers sa
    JOIN questions q ON sa.question_id = q.id
    WHERE sa.attempt_id = ?
    ORDER BY q.id
");
$answersStmt->execute([$attempt_id]);
$answers = $answersStmt->fetchAll(PDO::FETCH_ASSOC);

// Function to get grade based on percentage
function getGrade($percentage) {
    if ($percentage >= 90) return ['A+', 'success'];
    if ($percentage >= 80) return ['A', 'success'];
    if ($percentage >= 70) return ['B', 'info'];
    if ($percentage >= 60) return ['C', 'warning'];
    if ($percentage >= 50) return ['D', 'warning'];
    return ['F', 'danger'];
}

$grade_info = getGrade($attempt['percentage']);
$grade = $grade_info[0];
$grade_class = $grade_info[1];

$pageTitle = 'Attempt Details - ' . $attempt['student_name'];
include __DIR__ . '/../../templates/header.php';
?>

<main class="col-md-9 ms-sm-auto col-lg-10 px-md-4">
    <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
        <h1 class="h2">
            <i class="fas fa-user-graduate me-2"></i>
            Attempt Details
        </h1>
        <div class="btn-toolbar mb-2 mb-md-0">
            <a href="student_marks.php" class="btn btn-outline-secondary me-2">
                <i class="fas fa-arrow-left me-1"></i>Back to Marks
            </a>
            <button type="button" class="btn btn-outline-primary" onclick="window.print()">
                <i class="fas fa-print me-1"></i>Print
            </button>
        </div>
    </div>

    <!-- Student & Quiz Information -->
    <div class="row mb-4">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">
                        <i class="fas fa-user me-2"></i>Student Information
                    </h5>
                </div>
                <div class="card-body">
                    <table class="table table-borderless">
                        <tr>
                            <td><strong>Name:</strong></td>
                            <td><?php echo htmlspecialchars($attempt['student_name']); ?></td>
                        </tr>
                        <tr>
                            <td><strong>Email:</strong></td>
                            <td><?php echo htmlspecialchars($attempt['student_email']); ?></td>
                        </tr>
                        <tr>
                            <td><strong>Completed:</strong></td>
                            <td><?php echo date('M j, Y g:i A', strtotime($attempt['completed_at'])); ?></td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="card">
                <div class="card-header bg-info text-white">
                    <h5 class="mb-0">
                        <i class="fas fa-clipboard-question me-2"></i>Quiz Information
                    </h5>
                </div>
                <div class="card-body">
                    <table class="table table-borderless">
                        <tr>
                            <td><strong>Quiz:</strong></td>
                            <td><?php echo htmlspecialchars($attempt['quiz_title']); ?></td>
                        </tr>
                        <tr>
                            <td><strong>Score:</strong></td>
                            <td>
                                <span class="badge bg-<?php echo $grade_class; ?> fs-6">
                                    <?php echo $attempt['score']; ?> / <?php echo $attempt['total_questions']; ?>
                                </span>
                            </td>
                        </tr>
                        <tr>
                            <td><strong>Percentage:</strong></td>
                            <td>
                                <div class="progress" style="height: 25px;">
                                    <div class="progress-bar bg-<?php echo $grade_class; ?>" 
                                         role="progressbar" 
                                         style="width: <?php echo $attempt['percentage']; ?>%">
                                        <?php echo $attempt['percentage']; ?>%
                                    </div>
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <td><strong>Grade:</strong></td>
                            <td>
                                <span class="badge bg-<?php echo $grade_class; ?> fs-5">
                                    <?php echo $grade; ?>
                                </span>
                            </td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Question by Question Review -->
    <div class="card">
        <div class="card-header">
            <h5 class="mb-0">
                <i class="fas fa-list-check me-2"></i>
                Question by Question Review
            </h5>
        </div>
        <div class="card-body">
            <?php if (empty($answers)): ?>
                <div class="text-center py-4">
                    <i class="fas fa-exclamation-triangle text-warning" style="font-size: 3rem;"></i>
                    <h4 class="mt-3 text-muted">No Answer Details Found</h4>
                    <p class="text-muted">Answer details are not available for this attempt.</p>
                </div>
            <?php else: ?>
                <div class="accordion" id="questionsAccordion">
                    <?php foreach ($answers as $index => $answer): ?>
                        <div class="accordion-item">
                            <h2 class="accordion-header" id="heading<?php echo $index; ?>">
                                <button class="accordion-button <?php echo $index === 0 ? '' : 'collapsed'; ?>" 
                                        type="button" 
                                        data-bs-toggle="collapse" 
                                        data-bs-target="#collapse<?php echo $index; ?>" 
                                        aria-expanded="<?php echo $index === 0 ? 'true' : 'false'; ?>" 
                                        aria-controls="collapse<?php echo $index; ?>">
                                    <div class="d-flex align-items-center w-100">
                                        <span class="me-3">
                                            <i class="fas fa-<?php echo $answer['is_correct'] ? 'check-circle text-success' : 'times-circle text-danger'; ?>"></i>
                                        </span>
                                        <span class="flex-grow-1">
                                            Question <?php echo $index + 1; ?>
                                        </span>
                                        <span class="badge bg-<?php echo $answer['is_correct'] ? 'success' : 'danger'; ?> me-3">
                                            <?php echo $answer['is_correct'] ? 'Correct' : 'Incorrect'; ?>
                                        </span>
                                    </div>
                                </button>
                            </h2>
                            <div id="collapse<?php echo $index; ?>" 
                                 class="accordion-collapse collapse <?php echo $index === 0 ? 'show' : ''; ?>" 
                                 aria-labelledby="heading<?php echo $index; ?>" 
                                 data-bs-parent="#questionsAccordion">
                                <div class="accordion-body">
                                    <div class="row">
                                        <div class="col-12">
                                            <h6 class="fw-bold mb-3">Question:</h6>
                                            <p class="mb-4"><?php echo htmlspecialchars($answer['question_text']); ?></p>
                                        </div>
                                    </div>
                                    
                                    <div class="row">
                                        <div class="col-md-6">
                                            <h6 class="fw-bold mb-3">Answer Options:</h6>
                                            <div class="list-group">
                                                <?php 
                                                $options = [
                                                    'A' => $answer['option_a'],
                                                    'B' => $answer['option_b'],
                                                    'C' => $answer['option_c'],
                                                    'D' => $answer['option_d']
                                                ];
                                                
                                                foreach ($options as $key => $option): 
                                                    $is_selected = ($answer['selected_answer'] === $key);
                                                    $is_correct = ($answer['correct_answer'] === $key);
                                                    
                                                    $class = '';
                                                    $icon = '';
                                                    
                                                    if ($is_correct) {
                                                        $class = 'list-group-item-success';
                                                        $icon = '<i class="fas fa-check-circle text-success me-2"></i>';
                                                    } elseif ($is_selected && !$is_correct) {
                                                        $class = 'list-group-item-danger';
                                                        $icon = '<i class="fas fa-times-circle text-danger me-2"></i>';
                                                    }
                                                ?>
                                                    <div class="list-group-item <?php echo $class; ?>">
                                                        <?php echo $icon; ?>
                                                        <strong><?php echo $key; ?>.</strong> 
                                                        <?php echo htmlspecialchars($option); ?>
                                                        <?php if ($is_selected): ?>
                                                            <span class="badge bg-primary ms-2">Selected</span>
                                                        <?php endif; ?>
                                                        <?php if ($is_correct): ?>
                                                            <span class="badge bg-success ms-2">Correct Answer</span>
                                                        <?php endif; ?>
                                                    </div>
                                                <?php endforeach; ?>
                                            </div>
                                        </div>
                                        
                                        <div class="col-md-6">
                                            <h6 class="fw-bold mb-3">Summary:</h6>
                                            <div class="card bg-light">
                                                <div class="card-body">
                                                    <p class="mb-2">
                                                        <strong>Student's Answer:</strong>
                                                        <span class="badge bg-<?php echo $answer['is_correct'] ? 'success' : 'danger'; ?>">
                                                            Option <?php echo $answer['selected_answer']; ?>
                                                        </span>
                                                    </p>
                                                    <p class="mb-2">
                                                        <strong>Correct Answer:</strong>
                                                        <span class="badge bg-success">
                                                            Option <?php echo $answer['correct_answer']; ?>
                                                        </span>
                                                    </p>
                                                    <p class="mb-0">
                                                        <strong>Result:</strong>
                                                        <span class="badge bg-<?php echo $answer['is_correct'] ? 'success' : 'danger'; ?>">
                                                            <?php echo $answer['is_correct'] ? 'Correct (+1 point)' : 'Incorrect (0 points)'; ?>
                                                        </span>
                                                    </p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>
</main>

<style>
.table-borderless td {
    padding: 0.5rem 0;
}

.accordion-button:not(.collapsed) {
    background-color: #e7f1ff;
    color: #0c63e4;
}

.list-group-item {
    border: 1px solid #dee2e6;
    margin-bottom: 2px;
}

.list-group-item-success {
    background-color: #d1e7dd;
    border-color: #badbcc;
}

.list-group-item-danger {
    background-color: #f8d7da;
    border-color: #f5c2c7;
}

@media print {
    .btn-toolbar, .navbar, .sidebar {
        display: none !important;
    }
    
    .accordion-button {
        display: none !important;
    }
    
    .accordion-collapse {
        display: block !important;
    }
    
    .accordion-body {
        border: 1px solid #dee2e6;
        margin-bottom: 1rem;
    }
}
</style>

<?php include __DIR__ . '/../../templates/footer.php'; ?>
