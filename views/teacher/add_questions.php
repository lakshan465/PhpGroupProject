<?php
require_once __DIR__ . '/../../includes/auth.php';
require_once __DIR__ . '/../../config/db.php';

// Check if user is teacher
requireRole('teacher');

$pageTitle = 'Add Questions - Teacher Dashboard';
$pdo = getDBConnection();

$quizId = $_GET['quiz_id'] ?? 0;
$isNew = isset($_GET['new']);

// Verify quiz belongs to current teacher
$stmt = $pdo->prepare("SELECT * FROM quizzes WHERE id = ? AND teacher_id = ?");
$stmt->execute([$quizId, $_SESSION['user_id']]);
$quiz = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$quiz) {
    header('Location: manage_quizzes.php');
    exit();
}

// Get existing questions
$stmt = $pdo->prepare("SELECT * FROM questions WHERE quiz_id = ? ORDER BY id ASC");
$stmt->execute([$quizId]);
$questions = $stmt->fetchAll(PDO::FETCH_ASSOC);

$message = '';
$error = '';

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['add_question'])) {
        $questionText = trim($_POST['question_text']);
        $optionA = trim($_POST['option_a']);
        $optionB = trim($_POST['option_b']);
        $optionC = trim($_POST['option_c']);
        $optionD = trim($_POST['option_d']);
        $correctAnswer = $_POST['correct_answer'];
        
        if (empty($questionText) || empty($optionA) || empty($optionB) || empty($optionC) || empty($optionD)) {
            $error = 'All fields are required.';
        } else {
            try {
                $stmt = $pdo->prepare("INSERT INTO questions (quiz_id, question_text, option_a, option_b, option_c, option_d, correct_answer) VALUES (?, ?, ?, ?, ?, ?, ?)");
                $stmt->execute([$quizId, $questionText, $optionA, $optionB, $optionC, $optionD, $correctAnswer]);
                $message = 'Question added successfully!';
                
                // Refresh questions
                $stmt = $pdo->prepare("SELECT * FROM questions WHERE quiz_id = ? ORDER BY id ASC");
                $stmt->execute([$quizId]);
                $questions = $stmt->fetchAll(PDO::FETCH_ASSOC);
                
            } catch (PDOException $e) {
                $error = 'Error adding question: ' . $e->getMessage();
            }
        }
    }
    
    if (isset($_POST['delete_question'])) {
        $questionId = $_POST['question_id'];
        try {
            $stmt = $pdo->prepare("DELETE FROM questions WHERE id = ? AND quiz_id = ?");
            $stmt->execute([$questionId, $quizId]);
            $message = 'Question deleted successfully!';
            
            // Refresh questions
            $stmt = $pdo->prepare("SELECT * FROM questions WHERE quiz_id = ? ORDER BY id ASC");
            $stmt->execute([$quizId]);
            $questions = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
        } catch (PDOException $e) {
            $error = 'Error deleting question: ' . $e->getMessage();
        }
    }
}

include __DIR__ . '/../../templates/header.php';
?>

<!-- Main content -->
<main class="col-md-9 ms-sm-auto col-lg-10 px-md-4">
    <div class="main-content fade-in">
        <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
            <h1 class="h2">
                <i class="fas fa-edit me-2"></i>
                Manage Questions
            </h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item">
                        <a href="dashboard.php">Dashboard</a>
                    </li>
                    <li class="breadcrumb-item">
                        <a href="manage_quizzes.php">My Quizzes</a>
                    </li>
                    <li class="breadcrumb-item active">Questions</li>
                </ol>
            </nav>
        </div>

        <?php if ($isNew): ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="fas fa-check-circle me-2"></i>
                <strong>Quiz created successfully!</strong> Now add some questions to complete your quiz.
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <?php if ($message): ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="fas fa-check-circle me-2"></i>
                <?php echo htmlspecialchars($message); ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <?php if ($error): ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="fas fa-exclamation-circle me-2"></i>
                <?php echo htmlspecialchars($error); ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <!-- Quiz Info -->
        <div class="card mb-4">
            <div class="card-header bg-primary text-white">
                <h4 class="mb-0">
                    <i class="fas fa-clipboard-list me-2"></i>
                    <?php echo htmlspecialchars($quiz['title']); ?>
                </h4>
                <?php if ($quiz['description']): ?>
                    <small class="opacity-75"><?php echo htmlspecialchars($quiz['description']); ?></small>
                <?php endif; ?>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <strong>Questions:</strong> <?php echo count($questions); ?>
                    </div>
                    <div class="col-md-6 text-md-end">
                        <strong>Created:</strong> <?php echo date('M j, Y', strtotime($quiz['created_at'])); ?>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <!-- Add Question Form -->
            <div class="col-lg-6">
                <div class="card">
                    <div class="card-header bg-success text-white">
                        <h5 class="mb-0">
                            <i class="fas fa-plus me-2"></i>
                            Add New Question
                        </h5>
                    </div>
                    <div class="card-body">
                        <form method="POST" action="">
                            <div class="mb-3">
                                <label for="question_text" class="form-label">Question <span class="text-danger">*</span></label>
                                <textarea class="form-control" 
                                          id="question_text" 
                                          name="question_text" 
                                          rows="3" 
                                          required 
                                          placeholder="Enter your question here..."></textarea>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Answer Options <span class="text-danger">*</span></label>
                                
                                <div class="input-group mb-2">
                                    <span class="input-group-text">A)</span>
                                    <input type="text" class="form-control" name="option_a" required placeholder="First option">
                                </div>
                                
                                <div class="input-group mb-2">
                                    <span class="input-group-text">B)</span>
                                    <input type="text" class="form-control" name="option_b" required placeholder="Second option">
                                </div>
                                
                                <div class="input-group mb-2">
                                    <span class="input-group-text">C)</span>
                                    <input type="text" class="form-control" name="option_c" required placeholder="Third option">
                                </div>
                                
                                <div class="input-group mb-3">
                                    <span class="input-group-text">D)</span>
                                    <input type="text" class="form-control" name="option_d" required placeholder="Fourth option">
                                </div>
                            </div>

                            <div class="mb-3">
                                <label for="correct_answer" class="form-label">Correct Answer <span class="text-danger">*</span></label>
                                <select class="form-select" name="correct_answer" required>
                                    <option value="">Select correct answer</option>
                                    <option value="A">A) First option</option>
                                    <option value="B">B) Second option</option>
                                    <option value="C">C) Third option</option>
                                    <option value="D">D) Fourth option</option>
                                </select>
                            </div>

                            <button type="submit" name="add_question" class="btn btn-success w-100">
                                <i class="fas fa-plus me-1"></i>Add Question
                            </button>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Existing Questions -->
            <div class="col-lg-6">
                <div class="card">
                    <div class="card-header bg-info text-white">
                        <h5 class="mb-0">
                            <i class="fas fa-list me-2"></i>
                            Existing Questions (<?php echo count($questions); ?>)
                        </h5>
                    </div>
                    <div class="card-body" style="max-height: 600px; overflow-y: auto;">
                        <?php if (empty($questions)): ?>
                            <div class="text-center py-4">
                                <i class="fas fa-question-circle text-muted" style="font-size: 3rem;"></i>
                                <h5 class="mt-2 text-muted">No Questions Yet</h5>
                                <p class="text-muted">Add your first question using the form on the left.</p>
                            </div>
                        <?php else: ?>
                            <?php foreach ($questions as $index => $question): ?>
                                <div class="card mb-3">
                                    <div class="card-header d-flex justify-content-between align-items-center">
                                        <strong>Question <?php echo $index + 1; ?></strong>
                                        <form method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this question?')">
                                            <input type="hidden" name="question_id" value="<?php echo $question['id']; ?>">
                                            <button type="submit" name="delete_question" class="btn btn-outline-danger btn-sm">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                    <div class="card-body">
                                        <p class="mb-2"><strong><?php echo htmlspecialchars($question['question_text']); ?></strong></p>
                                        <ul class="list-unstyled mb-0">
                                            <li class="<?php echo $question['correct_answer'] === 'A' ? 'text-success fw-bold' : ''; ?>">
                                                A) <?php echo htmlspecialchars($question['option_a']); ?>
                                                <?php if ($question['correct_answer'] === 'A'): ?>
                                                    <i class="fas fa-check-circle text-success ms-1"></i>
                                                <?php endif; ?>
                                            </li>
                                            <li class="<?php echo $question['correct_answer'] === 'B' ? 'text-success fw-bold' : ''; ?>">
                                                B) <?php echo htmlspecialchars($question['option_b']); ?>
                                                <?php if ($question['correct_answer'] === 'B'): ?>
                                                    <i class="fas fa-check-circle text-success ms-1"></i>
                                                <?php endif; ?>
                                            </li>
                                            <li class="<?php echo $question['correct_answer'] === 'C' ? 'text-success fw-bold' : ''; ?>">
                                                C) <?php echo htmlspecialchars($question['option_c']); ?>
                                                <?php if ($question['correct_answer'] === 'C'): ?>
                                                    <i class="fas fa-check-circle text-success ms-1"></i>
                                                <?php endif; ?>
                                            </li>
                                            <li class="<?php echo $question['correct_answer'] === 'D' ? 'text-success fw-bold' : ''; ?>">
                                                D) <?php echo htmlspecialchars($question['option_d']); ?>
                                                <?php if ($question['correct_answer'] === 'D'): ?>
                                                    <i class="fas fa-check-circle text-success ms-1"></i>
                                                <?php endif; ?>
                                            </li>
                                        </ul>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>

        <!-- Action Buttons -->
        <div class="d-flex justify-content-between mt-4">
            <a href="manage_quizzes.php" class="btn btn-secondary">
                <i class="fas fa-arrow-left me-1"></i>Back to My Quizzes
            </a>
            <?php if (count($questions) > 0): ?>
                <span class="text-success">
                    <i class="fas fa-check-circle me-1"></i>
                    Quiz is ready! (<?php echo count($questions); ?> questions)
                </span>
            <?php endif; ?>
        </div>
    </div>
</main>

<?php include __DIR__ . '/../../templates/footer.php'; ?>
