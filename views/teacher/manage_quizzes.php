<?php
require_once __DIR__ . '/../../includes/auth.php';
require_once __DIR__ . '/../../config/db.php';

// Check if user is teacher
requireRole('teacher');

$pageTitle = 'My Quizzes - Teacher Dashboard';
$pdo = getDBConnection();

// Get teacher's quizzes
$stmt = $pdo->prepare("
    SELECT q.*, 
           COUNT(DISTINCT qs.id) as question_count
    FROM quizzes q 
    LEFT JOIN questions qs ON q.id = qs.quiz_id 
    WHERE q.teacher_id = ? 
    GROUP BY q.id 
    ORDER BY q.created_at DESC
");
$stmt->execute([$_SESSION['user_id']]);
$quizzes = $stmt->fetchAll(PDO::FETCH_ASSOC);

$message = $_GET['message'] ?? '';
$error = $_GET['error'] ?? '';

include __DIR__ . '/../../templates/header.php';
?>

<!-- Main content -->
<main class="col-md-9 ms-sm-auto col-lg-10 px-md-4">
    <div class="main-content fade-in">
        <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
            <h1 class="h2">
                <i class="fas fa-clipboard-list me-2"></i>
                My Quizzes
            </h1>
            <div class="btn-toolbar mb-2 mb-md-0">
                <a href="create_quiz.php" class="btn btn-primary">
                    <i class="fas fa-plus me-1"></i>Create New Quiz
                </a>
            </div>
        </div>

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

        <?php if (empty($quizzes)): ?>
            <div class="text-center py-5">
                <i class="fas fa-clipboard-list text-muted" style="font-size: 4rem;"></i>
                <h3 class="mt-3 text-muted">No Quizzes Yet</h3>
                <p class="text-muted">You haven't created any quizzes yet. Click the button below to create your first quiz.</p>
                <a href="create_quiz.php" class="btn btn-primary">
                    <i class="fas fa-plus me-1"></i>Create Your First Quiz
                </a>
            </div>
        <?php else: ?>
            <div class="row">
                <?php foreach ($quizzes as $quiz): ?>
                    <div class="col-lg-6 col-xl-4 mb-4">
                        <div class="card h-100 shadow-sm">
                            <div class="card-header bg-primary text-white">
                                <h5 class="card-title mb-0">
                                    <i class="fas fa-quiz me-2"></i>
                                    <?php echo htmlspecialchars($quiz['title']); ?>
                                </h5>
                            </div>
                            <div class="card-body">
                                <p class="card-text text-muted">
                                    <?php echo htmlspecialchars($quiz['description'] ?: 'No description provided'); ?>
                                </p>
                                
                                <div class="row text-center">
                                    <div class="col-12">
                                        <div class="stat-item">
                                            <h4 class="text-info"><?php echo $quiz['question_count']; ?></h4>
                                            <small class="text-muted">Questions</small>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="card-footer bg-light">
                                <div class="btn-group w-100" role="group">
                                    <a href="add_questions.php?quiz_id=<?php echo $quiz['id']; ?>" 
                                       class="btn btn-outline-primary btn-sm">
                                        <i class="fas fa-edit me-1"></i>Edit Questions
                                    </a>
                                    <button class="btn btn-outline-danger btn-sm" 
                                            onclick="deleteQuiz(<?php echo $quiz['id']; ?>)">
                                        <i class="fas fa-trash"></i>Delete
                                    </button>
                                </div>
                                <small class="text-muted d-block mt-2">
                                    Created: <?php echo date('M j, Y', strtotime($quiz['created_at'])); ?>
                                </small>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</main>

<script>
function deleteQuiz(quizId) {
    if (confirm('Are you sure you want to delete this quiz? This action cannot be undone.')) {
        window.location.href = 'delete_quiz.php?id=' + quizId;
    }
}
</script>

<style>
.stat-item h4 {
    margin-bottom: 0;
    font-weight: bold;
}

.card {
    transition: transform 0.2s;
}

.card:hover {
    transform: translateY(-2px);
}

.btn-group .btn {
    border-radius: 0;
}

.btn-group .btn:first-child {
    border-radius: 0.375rem 0 0 0.375rem;
}

.btn-group .btn:last-child {
    border-radius: 0 0.375rem 0.375rem 0;
}
</style>

<?php include __DIR__ . '/../../templates/footer.php'; ?>
