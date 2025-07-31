<?php
require_once __DIR__ . '/../../includes/auth.php';
require_once __DIR__ . '/../../config/db.php';

// Check if user is teacher
requireRole('teacher');

$pageTitle = 'Create Quiz - Teacher Dashboard';
$pdo = getDBConnection();

$error = '';
$success = '';

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = trim($_POST['title'] ?? '');
    $description = trim($_POST['description'] ?? '');
    
    // Validation
    if (empty($title)) {
        $error = 'Quiz title is required.';
    } elseif (strlen($title) > 200) {
        $error = 'Quiz title must be less than 200 characters.';
    } else {
        try {
            // Insert quiz into database
            $stmt = $pdo->prepare("
                INSERT INTO quizzes (title, description, teacher_id, status) 
                VALUES (?, ?, ?, 'active')
            ");
            $stmt->execute([$title, $description, $_SESSION['user_id']]);
            
            $quiz_id = $pdo->lastInsertId();
            $success = 'Quiz created successfully!';
            
            // Redirect to add questions page
            header("Location: add_questions.php?quiz_id=" . $quiz_id . "&message=" . urlencode('Quiz created! Now add some questions.'));
            exit();
            
        } catch (PDOException $e) {
            $error = 'Error creating quiz: ' . $e->getMessage();
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
                <i class="fas fa-plus-circle me-2"></i>
                Create New Quiz
            </h1>
            <div class="btn-toolbar mb-2 mb-md-0">
                <a href="manage_quizzes.php" class="btn btn-outline-secondary">
                    <i class="fas fa-arrow-left me-1"></i>Back to My Quizzes
                </a>
            </div>
        </div>

        <?php if ($error): ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="fas fa-exclamation-circle me-2"></i>
                <?php echo htmlspecialchars($error); ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <?php if ($success): ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="fas fa-check-circle me-2"></i>
                <?php echo htmlspecialchars($success); ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <div class="row justify-content-center">
            <div class="col-md-8 col-lg-6">
                <div class="card shadow">
                    <div class="card-header bg-primary text-white">
                        <h4 class="card-title mb-0">
                            <i class="fas fa-clipboard-list me-2"></i>
                            Quiz Details
                        </h4>
                    </div>
                    <div class="card-body">
                        <form method="POST" action="">
                            <div class="mb-3">
                                <label for="title" class="form-label">
                                    <i class="fas fa-heading me-1"></i>
                                    Quiz Title <span class="text-danger">*</span>
                                </label>
                                <input type="text" 
                                       class="form-control" 
                                       id="title" 
                                       name="title" 
                                       value="<?php echo htmlspecialchars($_POST['title'] ?? ''); ?>"
                                       placeholder="Enter quiz title..."
                                       maxlength="200"
                                       required>
                                <div class="form-text">Maximum 200 characters</div>
                            </div>

                            <div class="mb-4">
                                <label for="description" class="form-label">
                                    <i class="fas fa-align-left me-1"></i>
                                    Quiz Description
                                </label>
                                <textarea class="form-control" 
                                          id="description" 
                                          name="description" 
                                          rows="4"
                                          placeholder="Enter quiz description (optional)..."><?php echo htmlspecialchars($_POST['description'] ?? ''); ?></textarea>
                                <div class="form-text">Provide a brief description of what this quiz covers</div>
                            </div>

                            <div class="d-grid gap-2 d-md-flex justify-content-md-between">
                                <a href="manage_quizzes.php" class="btn btn-secondary">
                                    <i class="fas fa-times me-1"></i>Cancel
                                </a>
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-plus me-1"></i>Create Quiz
                                </button>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Info Card -->
                <div class="card mt-4 border-info">
                    <div class="card-header bg-info text-white">
                        <h5 class="card-title mb-0">
                            <i class="fas fa-info-circle me-2"></i>
                            What's Next?
                        </h5>
                    </div>
                    <div class="card-body">
                        <p class="card-text">After creating your quiz, you'll be able to:</p>
                        <ul class="list-unstyled">
                            <li><i class="fas fa-check text-success me-2"></i>Add multiple choice questions</li>
                            <li><i class="fas fa-check text-success me-2"></i>Set correct answers for each question</li>
                            <li><i class="fas fa-check text-success me-2"></i>Edit or delete questions anytime</li>
                            <li><i class="fas fa-check text-success me-2"></i>View quiz results from students</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>

<?php include __DIR__ . '/../../templates/footer.php'; ?>

<script>
// Character counter for title
document.getElementById('title').addEventListener('input', function() {
    const maxLength = 200;
    const currentLength = this.value.length;
    const remaining = maxLength - currentLength;
    
    let formText = this.nextElementSibling;
    formText.textContent = `${remaining} characters remaining`;
    
    if (remaining < 20) {
        formText.classList.add('text-warning');
        formText.classList.remove('text-muted');
    } else {
        formText.classList.remove('text-warning');
        formText.classList.add('text-muted');
    }
});

// Form validation
document.querySelector('form').addEventListener('submit', function(e) {
    const title = document.getElementById('title').value.trim();
    
    if (!title) {
        e.preventDefault();
        alert('Please enter a quiz title.');
        document.getElementById('title').focus();
        return false;
    }
    
    if (title.length > 200) {
        e.preventDefault();
        alert('Quiz title must be less than 200 characters.');
        document.getElementById('title').focus();
        return false;
    }
});
</script>
