<?php
require_once __DIR__ . '/../../includes/auth.php';
require_once __DIR__ . '/../../config/db.php';

// Check if user is student
requireRole('student');

$pdo = getDBConnection();

// Get available quizzes
$quizzesStmt = $pdo->prepare("SELECT * FROM quizzes WHERE status = 'active' ORDER BY created_at DESC");
$quizzesStmt->execute();
$quizzes = $quizzesStmt->fetchAll(PDO::FETCH_ASSOC);

// Get student's completed quizzes
$completedStmt = $pdo->prepare("
    SELECT qa.*, q.title, q.description 
    FROM quiz_attempts qa 
    JOIN quizzes q ON qa.quiz_id = q.id 
    WHERE qa.student_id = ? AND qa.status = 'completed' 
    ORDER BY qa.completed_at DESC
");
$completedStmt->execute([$_SESSION['user_id']]);
$completedQuizzes = $completedStmt->fetchAll(PDO::FETCH_ASSOC);

// Calculate statistics
$totalScore = 0;
$totalQuestions = 0;
$certificates = 0;
foreach ($completedQuizzes as $cq) {
    $totalScore += $cq['score'];
    $totalQuestions += $cq['total_questions'];
    if (($cq['score'] / $cq['total_questions']) * 100 >= 60) $certificates++;
}

$pageTitle = 'Student Dashboard - Quiz System';
include __DIR__ . '/../../templates/header.php';
?>

<!-- Main content -->
<main class="col-md-9 ms-sm-auto col-lg-10 px-md-4">
    <div class="main-content fade-in">
        <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
            <h1 class="h2">
                <i class="fas fa-graduation-cap me-2"></i>
                Student Dashboard
            </h1>
            <div class="btn-toolbar mb-2 mb-md-0">
                <div class="btn-group me-2">
                    <button type="button" class="btn btn-sm btn-outline-primary" onclick="generateQRCode()">
                        <i class="fas fa-qrcode me-1"></i>My QR Code
                    </button>
                </div>
            </div>
        </div>
        
        <!-- Welcome Section -->
        
        
        <!-- Statistics Cards -->
        <div class="row mb-4">
            <div class="col-xl-3 col-md-6 mb-4">
                <div class="stats-card">
                    <i class="fas fa-question-circle"></i>
                    <h3><?php echo count($quizzes); ?></h3>
                    <p>Available Quizzes</p>
                </div>
            </div>
            <div class="col-xl-3 col-md-6 mb-4">
                <div class="stats-card">
                    <i class="fas fa-check-circle"></i>
                    <h3><?php echo count($completedQuizzes); ?></h3>
                    <p>Completed Quizzes</p>
                </div>
            </div>
            <div class="col-xl-3 col-md-6 mb-4">
                <div class="stats-card">
                    <i class="fas fa-chart-line"></i>
                    <h3><?php echo $totalQuestions > 0 ? round(($totalScore / $totalQuestions) * 100, 1) : 0; ?>%</h3>
                    <p>Average Score</p>
                </div>
            </div>
            <div class="col-xl-3 col-md-6 mb-4">
                <div class="stats-card">
                    <i class="fas fa-certificate"></i>
                    <h3><?php echo $certificates; ?></h3>
                    <p>Certificates Earned</p>
                </div>
            </div>
        </div>
        
        <!-- Main Content -->
        <div class="row">
            <div class="col-lg-12">
                <!-- Available Quizzes -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="mb-0">
                            <i class="fas fa-question-circle me-2"></i>
                            Available Quizzes
                        </h5>
                    </div>
                    <div class="card-body">
                        <?php if (empty($quizzes)): ?>
                            <div class="text-center py-4">
                                <i class="fas fa-info-circle fa-3x text-muted mb-3"></i>
                                <p class="text-muted">No quizzes available at the moment.</p>
                            </div>
                        <?php else: ?>
                            <div class="row">
                                <?php foreach ($quizzes as $quiz): 
                                // Check if already completed
                                $completed = false;
                                $completedAttemptId = null;
                                foreach ($completedQuizzes as $cq) {
                                    if ($cq['quiz_id'] == $quiz['id']) {
                                        $completed = true;
                                        $completedAttemptId = $cq['id'];
                                        break;
                                    }
                                }
                                ?>
                                <div class="col-md-6 mb-3">
                                    <div class="card border-primary">
                                        <div class="card-body">
                                            <h6 class="card-title text-primary"><?php echo htmlspecialchars($quiz['title']); ?></h6>
                                            <p class="card-text">
                                                <small class="text-muted"><?php echo htmlspecialchars($quiz['description']); ?></small>
                                            </p>
                                            <?php if ($completed): ?>
                                                <span class="badge bg-success mb-2">Completed</span>
                                                <div class="mt-2">
                                                    <a href="quiz_result.php?attempt_id=<?php echo $completedAttemptId; ?>" class="btn btn-sm btn-outline-primary">
                                                        <i class="fas fa-eye me-1"></i>View Results
                                                    </a>
                                                </div>
                                            <?php else: ?>
                                                <div class="mt-2">
                                                    <a href="take_quiz.php?id=<?php echo $quiz['id']; ?>" class="btn btn-sm btn-primary">
                                                        <i class="fas fa-play me-1"></i>Take Quiz
                                                    </a>
                                                </div>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </div>
                                <?php endforeach; ?>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Recent Quiz Results -->
                <?php if (!empty($completedQuizzes)): ?>
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">
                            <i class="fas fa-chart-bar me-2"></i>
                            Recent Quiz Results
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Quiz</th>
                                        <th>Score</th>
                                        <th>Percentage</th>
                                        <th>Date</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach (array_slice($completedQuizzes, 0, 5) as $quiz): 
                                    $percentage = ($quiz['score'] / $quiz['total_questions']) * 100;
                                    ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($quiz['title']); ?></td>
                                        <td><?php echo $quiz['score']; ?>/<?php echo $quiz['total_questions']; ?></td>
                                        <td>
                                            <span class="badge <?php echo $percentage >= 60 ? 'bg-success' : 'bg-danger'; ?>">
                                                <?php echo round($percentage, 1); ?>%
                                            </span>
                                        </td>
                                        <td><?php echo date('M j, Y', strtotime($quiz['completed_at'])); ?></td>
                                        <td>
                                            <a href="quiz_result.php?attempt_id=<?php echo $quiz['id']; ?>" class="btn btn-sm btn-outline-primary">View</a>
                                            <a href="pdf_generator.php?attempt_id=<?php echo $quiz['id']; ?>" class="btn btn-sm btn-outline-success">PDF</a>
                                            <?php if ($percentage >= 60): ?>
                                            <a href="generate_certificate.php?attempt_id=<?php echo $quiz['id']; ?>" class="btn btn-sm btn-outline-warning">Cert</a>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                <?php endif; ?>
            </div>
            
            

                <!-- Performance Summary -->
                
                
                <!-- Quick Actions -->
                
            </div>
        </div>
    </div>
</main>

<!-- QR Code Modal -->
<div class="modal fade" id="qrModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">My Student QR Code</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body text-center">
                <img src="generate_qr.php?student_id=<?php echo $_SESSION['user_id']; ?>&size=300" 
                     alt="Student Profile QR Code" class="img-fluid" id="modal-qr-image" crossorigin="anonymous">
                <p class="mt-3">Scan this QR code to access your complete student profile with live quiz results and achievements.</p>
                <div class="alert alert-info">
                    <small><i class="fas fa-info-circle me-2"></i>This QR code links to your public profile page that shows real-time statistics and quiz history.</small>
                </div>
                
                <!-- Profile URL Display -->
                <div class="mt-3">
                    <label class="form-label"><strong>Profile URL:</strong></label>
                    <div class="input-group">
                        <input type="text" class="form-control form-control-sm" 
                               value="<?php 
                                   $protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https://' : 'http://';
                                   $host = $_SERVER['HTTP_HOST'];
                                   $basePath = dirname(dirname($_SERVER['REQUEST_URI']));
                                   echo $protocol . $host . $basePath . '/views/student/public_profile.php?id=' . $_SESSION['user_id'];
                               ?>" 
                               readonly id="profile-url">
                        <button class="btn btn-outline-secondary btn-sm" onclick="copyProfileURL()" title="Copy URL">
                            <i class="fas fa-copy"></i>
                        </button>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary" onclick="downloadQR()">
                    <i class="fas fa-download me-1"></i>Download QR Code
                </button>
                <button type="button" class="btn btn-success" onclick="downloadQRFromModal()">
                    <i class="fas fa-save me-1"></i>Save as Image
                </button>
            </div>
        </div>
    </div>
</div>

<?php include __DIR__ . '/../../templates/footer.php'; ?>

