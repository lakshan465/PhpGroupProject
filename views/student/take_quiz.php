<?php
require_once __DIR__ . '/../../includes/auth.php';
require_once __DIR__ . '/../../config/db.php';

requireRole('student');

$quiz_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$pdo = getDBConnection();

// Get quiz details
$quizStmt = $pdo->prepare("SELECT * FROM quizzes WHERE id = ? AND status = 'active'");
$quizStmt->execute([$quiz_id]);
$quiz = $quizStmt->fetch(PDO::FETCH_ASSOC);

if (!$quiz) {
    header('Location: /Mini%20Project%203ii/PhpGroupProject/views/student/dashboard.php');
    exit;
}

// Check if student already completed this quiz
$attemptStmt = $pdo->prepare("SELECT * FROM quiz_attempts WHERE student_id = ? AND quiz_id = ? AND status = 'completed'");
$attemptStmt->execute([$_SESSION['user_id'], $quiz_id]);
$existingAttempt = $attemptStmt->fetch(PDO::FETCH_ASSOC);

if ($existingAttempt) {
    header('Location: quiz_result.php?attempt_id=' . $existingAttempt['id']);
    exit;
}

// Get questions
$questionsStmt = $pdo->prepare("SELECT * FROM questions WHERE quiz_id = ? ORDER BY id");
$questionsStmt->execute([$quiz_id]);
$questions = $questionsStmt->fetchAll(PDO::FETCH_ASSOC);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Create attempt record
    $attemptStmt = $pdo->prepare("INSERT INTO quiz_attempts (student_id, quiz_id, total_questions) VALUES (?, ?, ?)");
    $attemptStmt->execute([$_SESSION['user_id'], $quiz_id, count($questions)]);
    $attempt_id = $pdo->lastInsertId();
    
    $score = 0;
    
    // Process answers
    foreach ($questions as $question) {
        $selected_answer = $_POST['question_' . $question['id']] ?? '';
        
        // Skip if no answer selected
        if (empty($selected_answer)) {
            $is_correct = 0; // Explicitly set to 0 for unanswered questions
        } else {
            $is_correct = ($selected_answer === $question['correct_answer']) ? 1 : 0;
            if ($is_correct) $score++;
        }
        
        $answerStmt = $pdo->prepare("INSERT INTO student_answers (attempt_id, question_id, selected_answer, is_correct) VALUES (?, ?, ?, ?)");
        $answerStmt->execute([$attempt_id, $question['id'], $selected_answer, $is_correct]);
    }
    
    // Update attempt with score and completion
    $updateAttemptStmt = $pdo->prepare("UPDATE quiz_attempts SET score = ?, completed_at = NOW(), status = 'completed' WHERE id = ?");
    $updateAttemptStmt->execute([$score, $attempt_id]);
    
    header('Location: quiz_result.php?attempt_id=' . $attempt_id);
    exit;
}

$pageTitle = 'Take Quiz - ' . $quiz['title'];
include __DIR__ . '/../../templates/header.php';
?>

<main class="col-md-9 ms-sm-auto col-lg-10 px-md-4">
    <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
        <h1 class="h2">
            <i class="fas fa-question-circle me-2"></i>
            <?php echo htmlspecialchars($quiz['title']); ?>
        </h1>
    </div>

    <div class="row">
        <div class="col-lg-8">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0"><?php echo htmlspecialchars($quiz['description']); ?></h5>
                    <small class="text-muted">Total Questions: <?php echo count($questions); ?></small>
                </div>
                <div class="card-body">
                    <form method="POST">
                        <?php foreach ($questions as $index => $question): ?>
                        <div class="question-block mb-4 p-3 border rounded">
                            <h6 class="fw-bold">Question <?php echo $index + 1; ?></h6>
                            <p><?php echo htmlspecialchars($question['question_text']); ?></p>
                            
                            <div class="options">
                                <div class="form-check mb-2">
                                    <input class="form-check-input" type="radio" name="question_<?php echo $question['id']; ?>" value="A" id="q<?php echo $question['id']; ?>_a">
                                    <label class="form-check-label" for="q<?php echo $question['id']; ?>_a">
                                        A) <?php echo htmlspecialchars($question['option_a']); ?>
                                    </label>
                                </div>
                                <div class="form-check mb-2">
                                    <input class="form-check-input" type="radio" name="question_<?php echo $question['id']; ?>" value="B" id="q<?php echo $question['id']; ?>_b">
                                    <label class="form-check-label" for="q<?php echo $question['id']; ?>_b">
                                        B) <?php echo htmlspecialchars($question['option_b']); ?>
                                    </label>
                                </div>
                                <div class="form-check mb-2">
                                    <input class="form-check-input" type="radio" name="question_<?php echo $question['id']; ?>" value="C" id="q<?php echo $question['id']; ?>_c">
                                    <label class="form-check-label" for="q<?php echo $question['id']; ?>_c">
                                        C) <?php echo htmlspecialchars($question['option_c']); ?>
                                    </label>
                                </div>
                                <div class="form-check mb-2">
                                    <input class="form-check-input" type="radio" name="question_<?php echo $question['id']; ?>" value="D" id="q<?php echo $question['id']; ?>_d">
                                    <label class="form-check-label" for="q<?php echo $question['id']; ?>_d">
                                        D) <?php echo htmlspecialchars($question['option_d']); ?>
                                    </label>
                                </div>
                            </div>
                        </div>
                        <?php endforeach; ?>
                        
                        <div class="text-center">
                            <button type="submit" class="btn btn-primary btn-lg" onclick="return validateQuiz()">
                                <i class="fas fa-check me-2"></i>Submit Quiz
                            </button>
                            <a href="dashboard.php" class="btn btn-secondary btn-lg ms-2">
                                <i class="fas fa-arrow-left me-2"></i>Back to Dashboard
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        
        <div class="col-lg-4">
            <div class="card">
                <div class="card-header">
                    <h6 class="mb-0">Quiz Information</h6>
                </div>
                <div class="card-body">
                    <p><strong>Total Questions:</strong> <?php echo count($questions); ?></p>
                    <p><strong>Time:</strong> No time limit</p>
                    <p><strong>Attempts:</strong> 1 attempt allowed</p>
                    <div class="alert alert-info">
                        <small><i class="fas fa-info-circle me-1"></i>Make sure to answer all questions before submitting.</small>
                    </div>
                    <div class="progress mt-3">
                        <div class="progress-bar" id="progressBar" style="width: 0%"></div>
                    </div>
                    <small class="text-muted">Progress: <span id="progressText">0/<?php echo count($questions); ?></span></small>
                </div>
            </div>
        </div>
    </div>
</main>

<?php include __DIR__ . '/../../templates/footer.php'; ?>

<style>
.question-block {
    background-color: #f8f9fa;
}
.question-block:hover {
    background-color: #e9ecef;
}
.question-answered {
    border-color: #28a745 !important;
    background-color: #d4edda !important;
}
</style>

<script>
function validateQuiz() {
    const totalQuestions = <?php echo count($questions); ?>;
    let answeredQuestions = 0;
    const unansweredQuestions = [];
    
    // Check each question
    <?php foreach ($questions as $index => $question): ?>
    const question<?php echo $question['id']; ?> = document.querySelector('input[name="question_<?php echo $question['id']; ?>"]:checked');
    if (question<?php echo $question['id']; ?>) {
        answeredQuestions++;
    } else {
        unansweredQuestions.push(<?php echo $index + 1; ?>);
    }
    <?php endforeach; ?>
    
    if (answeredQuestions < totalQuestions) {
        alert(`Please answer all questions before submitting.\nUnanswered questions: ${unansweredQuestions.join(', ')}`);
        return false;
    }
    
    return confirm('Are you sure you want to submit your quiz? You cannot change your answers after submission.');
}

function updateProgress() {
    const totalQuestions = <?php echo count($questions); ?>;
    let answeredQuestions = 0;
    
    // Count answered questions
    <?php foreach ($questions as $question): ?>
    const question<?php echo $question['id']; ?> = document.querySelector('input[name="question_<?php echo $question['id']; ?>"]:checked');
    if (question<?php echo $question['id']; ?>) {
        answeredQuestions++;
        document.querySelector('.question-block:has(input[name="question_<?php echo $question['id']; ?>"])').classList.add('question-answered');
    }
    <?php endforeach; ?>
    
    // Update progress bar
    const percentage = (answeredQuestions / totalQuestions) * 100;
    document.getElementById('progressBar').style.width = percentage + '%';
    document.getElementById('progressText').textContent = answeredQuestions + '/' + totalQuestions;
}

// Add event listeners to all radio buttons
document.addEventListener('DOMContentLoaded', function() {
    const radioButtons = document.querySelectorAll('input[type="radio"]');
    radioButtons.forEach(radio => {
        radio.addEventListener('change', updateProgress);
    });
    
    // Initial progress update
    updateProgress();
});
</script>
