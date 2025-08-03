<?php
require_once __DIR__ . '/../../includes/auth.php';
require_once __DIR__ . '/../../config/db.php';

requireRole('student');

$attempt_id = isset($_GET['attempt_id']) ? (int)$_GET['attempt_id'] : 0;
$pdo = getDBConnection();

// Get attempt details
$attemptStmt = $pdo->prepare("
    SELECT qa.*, q.title, q.description, u.full_name 
    FROM quiz_attempts qa 
    JOIN quizzes q ON qa.quiz_id = q.id 
    JOIN users u ON qa.student_id = u.id 
    WHERE qa.id = ? AND qa.student_id = ?
");
$attemptStmt->execute([$attempt_id, $_SESSION['user_id']]);
$attempt = $attemptStmt->fetch(PDO::FETCH_ASSOC);

if (!$attempt) {
    header('Location: dashboard.php');
    exit;
}

// Get all answers with questions
$answersStmt = $pdo->prepare("
    SELECT sa.*, q.question_text, q.option_a, q.option_b, q.option_c, q.option_d, q.correct_answer
    FROM student_answers sa
    JOIN questions q ON sa.question_id = q.id
    WHERE sa.attempt_id = ?
    ORDER BY q.id
");
$answersStmt->execute([$attempt_id]);
$answers = $answersStmt->fetchAll(PDO::FETCH_ASSOC);

$percentage = ($attempt['score'] / $attempt['total_questions']) * 100;
$grade = $percentage >= 75 ? 'A' : ($percentage >= 65 ? 'B' : ($percentage >= 50 ? 'C' : ($percentage >= 35 ? 'D' : 'F')));

$pageTitle = 'Quiz Results - ' . $attempt['title'];
include __DIR__ . '/../../templates/header.php';
?>

<main class="col-md-9 ms-sm-auto col-lg-10 px-md-4">
    <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
        <h1 class="h2">
            <i class="fas fa-chart-bar me-2"></i>
            Quiz Results
        </h1>
        <div class="btn-toolbar mb-2 mb-md-0">
            <div class="btn-group me-2">
                <a href="pdf_generator.php?attempt_id=<?php echo $attempt_id; ?>" class="btn btn-sm btn-success">
                    <i class="fas fa-download me-1"></i>Download PDF
                </a>
                
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <!-- Score Summary -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0"><?php echo htmlspecialchars($attempt['title']); ?></h5>
                </div>
                <div class="card-body">
                    <div class="row text-center">
                        <div class="col-md-3">
                            <h3 class="text-primary"><?php echo $attempt['score']; ?>/<?php echo $attempt['total_questions']; ?></h3>
                            <p>Score</p>
                        </div>
                        <div class="col-md-3">
                            <h3 class="text-success"><?php echo round($percentage, 1); ?>%</h3>
                            <p>Percentage</p>
                        </div>
                        <div class="col-md-3">
                            <h3 class="text-info"><?php echo $grade; ?></h3>
                            <p>Grade</p>
                        </div>
                        <div class="col-md-3">
                            <h3 class="text-warning"><?php echo $percentage >= 35 ? 'Pass' : 'Fail'; ?></h3>
                            <p>Status</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Detailed Answers -->
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Detailed Results</h5>
                </div>
                <div class="card-body">
                    <?php foreach ($answers as $index => $answer): ?>
                    <div class="question-result mb-4 p-3 border rounded <?php 
                        if (empty($answer['selected_answer'])) {
                            echo 'border-warning';
                        } elseif ($answer['is_correct']) {
                            echo 'border-success';
                        } else {
                            echo 'border-danger';
                        }
                    ?>">
                        <div class="d-flex justify-content-between align-items-start">
                            <h6 class="fw-bold">Question <?php echo $index + 1; ?></h6>
                            <?php if (empty($answer['selected_answer'])): ?>
                                <span class="badge bg-warning">Not Answered</span>
                            <?php elseif ($answer['is_correct']): ?>
                                <span class="badge bg-success">Correct</span>
                            <?php else: ?>
                                <span class="badge bg-danger">Incorrect</span>
                            <?php endif; ?>
                        </div>
                        
                        <p class="mt-2"><?php echo htmlspecialchars($answer['question_text']); ?></p>
                        
                        <div class="row">
                            <div class="col-md-6">
                                <strong>Your Answer:</strong>
                                <?php if (empty($answer['selected_answer'])): ?>
                                    <p class="text-muted">
                                        <em>No answer selected</em>
                                    </p>
                                <?php else: ?>
                                    <p class="<?php echo $answer['is_correct'] ? 'text-success' : 'text-danger'; ?>">
                                        <?php echo $answer['selected_answer']; ?>) 
                                        <?php echo htmlspecialchars($answer['option_' . strtolower($answer['selected_answer'])]); ?>
                                    </p>
                                <?php endif; ?>
                            </div>
                            <?php if (!$answer['is_correct']): ?>
                            <div class="col-md-6">
                                <strong>Correct Answer:</strong>
                                <p class="text-success">
                                    <?php echo $answer['correct_answer']; ?>) 
                                    <?php echo htmlspecialchars($answer['option_' . strtolower($answer['correct_answer'])]); ?>
                                </p>
                            </div>
                            <?php endif; ?>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </div>
    
    <div class="text-center mt-4">
        <a href="dashboard.php" class="btn btn-secondary">
            <i class="fas fa-arrow-left me-2"></i>Back to Dashboard
        </a>
    </div>
</main>

<?php include __DIR__ . '/../../templates/footer.php'; ?>

<style>
.question-result {
    background-color: #f8f9fa;
}
</style>
