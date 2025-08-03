<?php
require_once __DIR__ . '/../../includes/auth.php';
require_once __DIR__ . '/../../config/db.php';

// Check if user is teacher
requireRole('teacher');

$pageTitle = 'Teacher Dashboard - User Management System';
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
    LIMIT 10
");
$stmt->execute([$_SESSION['user_id']]);
$quizzes = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Get quiz statistics
$stmt = $pdo->prepare("SELECT COUNT(*) FROM quizzes WHERE teacher_id = ?");
$stmt->execute([$_SESSION['user_id']]);
$total_quizzes = $stmt->fetchColumn();

$stmt = $pdo->prepare("
    SELECT COUNT(*) FROM questions q 
    JOIN quizzes qz ON q.quiz_id = qz.id 
    WHERE qz.teacher_id = ?
");
$stmt->execute([$_SESSION['user_id']]);
$total_questions = $stmt->fetchColumn();

include __DIR__ . '/../../templates/header.php';
?>

<!-- Main content -->
<main class="col-md-9 ms-sm-auto col-lg-10 px-md-4">
    <div class="main-content fade-in">
        <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
            <h1 class="h2">
                <i class="fas fa-chalkboard-teacher me-2"></i>
                Teacher Dashboard
            </h1>
            <div class="btn-toolbar mb-2 mb-md-0">
                <div class="btn-group me-2">
                    <a href="create_quiz.php" class="btn btn-primary">
                        <i class="fas fa-plus me-1"></i>Create Quiz
                    </a>
                    <a href="manage_quizzes.php" class="btn btn-outline-primary">
                        <i class="fas fa-cog me-1"></i>Manage Quizzes
                    </a>
                    <a href="student_marks.php" class="btn btn-outline-success">
                        <i class="fas fa-chart-line me-1"></i>Student Marks
                    </a>
                </div>
                <div class="btn-group">
                    
                </div>
            </div>
        </div>
        
        <!-- Welcome Section -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="alert alert-info" style="background: linear-gradient(135deg, #17a2b8, #138496); color: white; border: none;">
                    <h4 class="alert-heading">
                        <i class="fas fa-hand-wave me-2"></i>
                        Welcome back, <?php echo htmlspecialchars($_SESSION['full_name']); ?>!
                    </h4>
                    <p>Here's your teaching dashboard where you can manage your courses, view student progress, and access teaching resources.</p>
                </div>
            </div>
        </div>
        
        <!-- Statistics Cards -->
        <div class="row mb-4">
            <div class="col-xl-3 col-md-6 mb-4">
                <div class="stats-card">
                    <i class="fas fa-clipboard-list"></i>
                    <h3><?php echo $total_quizzes; ?></h3>
                    <p>Total Quizzes</p>
                </div>
            </div>
            <div class="col-xl-3 col-md-6 mb-4">
                <div class="stats-card">
                    <i class="fas fa-question-circle"></i>
                    <h3><?php echo $total_questions; ?></h3>
                    <p>Total Questions</p>
                </div>
            </div>
            <div class="col-xl-3 col-md-6 mb-4">
                <div class="stats-card">
                    <i class="fas fa-users"></i>
                    <h3>145</h3>
                    <p>Students</p>
                </div>
            </div>
            <div class="col-xl-3 col-md-6 mb-4">
                <div class="stats-card">
                    <i class="fas fa-star"></i>
                    <h3>4.8</h3>
                    <p>Rating</p>
                </div>
            </div>
        </div>
        
        
        <!-- Quiz Management -->
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">
                            <i class="fas fa-clipboard-list me-2"></i>
                            My Quizzes
                        </h5>
                        <div class="btn-group">
                            
                        </div>
                    </div>
                    <div class="card-body">
                        <?php if (empty($quizzes)): ?>
                            <div class="text-center py-5">
                                <i class="fas fa-clipboard-list text-muted" style="font-size: 3rem;"></i>
                                <h4 class="mt-3 text-muted">No Quizzes Yet</h4>
                                <p class="text-muted">You haven't created any quizzes yet. Get started by creating your first quiz!</p>
                                <a href="create_quiz.php" class="btn btn-primary">
                                    <i class="fas fa-plus me-1"></i>Create Your First Quiz
                                </a>
                            </div>
                        <?php else: ?>
                            <div class="row">
                                <?php foreach ($quizzes as $quiz): ?>
                                    <div class="col-lg-4 col-md-6 mb-4">
                                        <div class="card h-100 shadow-sm border-0 quiz-card" style="transition: transform 0.2s;">
                                            <div class="card-header text-white quiz-card-header">
                                                <h5 class="card-title mb-0 fw-bold">
                                                    <i class="fas fa-clipboard-question me-2"></i>
                                                    <?php echo htmlspecialchars($quiz['title']); ?>
                                                </h5>
                                            </div>
                                            <div class="card-body quiz-card-body">
                                                <p class="card-text text-muted">
                                                    <?php echo htmlspecialchars(substr($quiz['description'] ?: 'No description provided', 0, 80) . (strlen($quiz['description']) > 80 ? '...' : '')); ?>
                                                </p>
                                                
                                                <div class="row text-center mb-3">
                                                    <div class="col-6">
                                                        <div class="stat-item bg-light p-2 rounded">
                                                            <h4 class="text-primary mb-0 fw-bold"><?php echo $quiz['question_count']; ?></h4>
                                                            <small class="text-muted fw-semibold">Questions</small>
                                                        </div>
                                                    </div>
                                                    <div class="col-6">
                                                        <div class="stat-item bg-light p-2 rounded">
                                                            <h4 class="text-success mb-0 fw-bold"><?php echo ucfirst($quiz['status']); ?></h4>
                                                            <small class="text-muted fw-semibold">Status</small>
                                                        </div>
                                                    </div>
                                                </div>
                                                
                                                <small class="text-muted">
                                                    <i class="fas fa-calendar-alt me-1 text-info"></i>
                                                    <strong>Created:</strong> <?php echo date('M d, Y', strtotime($quiz['created_at'])); ?>
                                                </small>
                                            </div>
                                            <div class="card-footer bg-white border-top">
                                                <div class="btn-group w-100" role="group">
                                                    <a href="add_questions.php?quiz_id=<?php echo $quiz['id']; ?>" 
                                                       class="btn btn-primary btn-sm">
                                                        <i class="fas fa-edit me-1"></i>Edit
                                                    </a>
                                                    
                                                    <a href="delete_quiz.php?quiz_id=<?php echo $quiz['id']; ?>" 
                                                       class="btn btn-danger btn-sm"
                                                       onclick="return confirm('Are you sure you want to delete this quiz?')">
                                                        <i class="fas fa-trash me-1"></i>Delete
                                                    </a>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                            
                            <?php if (count($quizzes) >= 10): ?>
                                <div class="text-center mt-3">
                                    <a href="manage_quizzes.php" class="btn btn-outline-primary">
                                        <i class="fas fa-eye me-1"></i>View All Quizzes
                                    </a>
                                </div>
                            <?php endif; ?>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>

<?php include __DIR__ . '/../../templates/footer.php'; ?>

<style>
.card:hover {
    transform: translateY(-5px);
}

.stats-card {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    padding: 20px;
    border-radius: 10px;
    text-align: center;
    box-shadow: 0 4px 15px rgba(0,0,0,0.1);
    transition: transform 0.3s ease;
}

.stats-card:hover {
    transform: translateY(-5px);
}

.stats-card i {
    font-size: 2rem;
    margin-bottom: 10px;
    opacity: 0.8;
}

.stats-card h3 {
    font-size: 2.5rem;
    font-weight: bold;
    margin: 10px 0 5px 0;
}

.stats-card p {
    margin: 0;
    opacity: 0.9;
}

.stat-item h5 {
    font-weight: 600;
}

/* Quiz Card Styling */
.quiz-card {
    border: 2px solid #e3f2fd;
    border-radius: 12px !important;
    overflow: hidden;
    box-shadow: 0 4px 20px rgba(0,0,0,0.08);
    transition: all 0.3s ease;
}

.quiz-card:hover {
    transform: translateY(-8px);
    box-shadow: 0 8px 30px rgba(0,0,0,0.15);
    border-color: #2196f3;
}

.quiz-card-header {
    background: linear-gradient(135deg, #2196f3 0%, #1976d2 50%, #0d47a1 100%);
    border-bottom: none;
    padding: 15px 20px;
    position: relative;
}

.quiz-card-header::after {
    content: '';
    position: absolute;
    bottom: 0;
    left: 0;
    right: 0;
    height: 3px;
    background: linear-gradient(90deg, #4caf50, #2196f3, #ff9800);
}

.quiz-card-header h5 {
    font-size: 1.1rem;
    text-shadow: 0 1px 2px rgba(0,0,0,0.2);
    letter-spacing: 0.5px;
}

.quiz-card-body {
    padding: 20px;
    background: linear-gradient(180deg, #ffffff 0%, #f8f9fa 100%);
}

.quiz-card-body .stat-item {
    border: 1px solid #e9ecef;
    transition: all 0.2s ease;
}

.quiz-card-body .stat-item:hover {
    transform: scale(1.05);
    border-color: #2196f3;
    box-shadow: 0 2px 8px rgba(33, 150, 243, 0.2);
}

.quiz-card .card-footer {
    background: linear-gradient(180deg, #f8f9fa 0%, #ffffff 100%);
    border-top: 2px solid #e9ecef;
    padding: 15px 20px;
}

.quiz-card .btn-group .btn {
    font-weight: 600;
    text-transform: uppercase;
    font-size: 0.75rem;
    letter-spacing: 0.5px;
    transition: all 0.2s ease;
}

.quiz-card .btn-group .btn:hover {
    transform: translateY(-1px);
}
</style>
