<?php
require_once __DIR__ . '/../../config/db.php';

$attempt_id = isset($_GET['attempt_id']) ? (int)$_GET['attempt_id'] : 0;
$pdo = getDBConnection();

if (!$attempt_id) {
    header('HTTP/1.0 404 Not Found');
    exit('Quiz result not found');
}

// Get attempt details with student info
$attemptStmt = $pdo->prepare("
    SELECT qa.*, q.title, q.description, u.full_name 
    FROM quiz_attempts qa 
    JOIN quizzes q ON qa.quiz_id = q.id 
    JOIN users u ON qa.student_id = u.id 
    WHERE qa.id = ?
");
$attemptStmt->execute([$attempt_id]);
$attempt = $attemptStmt->fetch(PDO::FETCH_ASSOC);

if (!$attempt) {
    header('HTTP/1.0 404 Not Found');
    exit('Quiz result not found');
}

$percentage = ($attempt['score'] / $attempt['total_questions']) * 100;
$passed = $percentage >= 60;
$grade = $percentage >= 75 ? 'A' : ($percentage >= 65 ? 'B' : ($percentage >= 50 ? 'C' : ($percentage >= 35 ? 'D' : 'F')));
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quiz Result - <?php echo htmlspecialchars($attempt['title']); ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            font-family: 'Arial', sans-serif;
        }
        .result-card {
            background: white;
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.3);
            margin: 2rem auto;
            max-width: 500px;
            overflow: hidden;
        }
        .result-header {
            background: <?php echo $passed ? '#28a745' : '#dc3545'; ?>;
            color: white;
            padding: 2rem;
            text-align: center;
        }
        .result-body {
            padding: 2rem;
        }
        .status-badge {
            font-size: 1.2rem;
            padding: 0.5rem 1.5rem;
            border-radius: 25px;
            font-weight: bold;
        }
        .score-circle {
            width: 100px;
            height: 100px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
            font-weight: bold;
            margin: 1rem auto;
        }
        .info-item {
            border-bottom: 1px solid #eee;
            padding: 0.75rem 0;
        }
        .info-item:last-child {
            border-bottom: none;
        }
        .qr-notice {
            background: #e7f3ff;
            border-left: 4px solid #0066cc;
            padding: 1rem;
            margin-top: 1rem;
            border-radius: 0 5px 5px 0;
        }
        @media print {
            body { background: white !important; }
            .result-card { box-shadow: none !important; margin: 0 !important; }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="result-card">
            <!-- Header -->
            <div class="result-header">
                <i class="fas fa-check-circle fa-3x mb-3"></i>
                <h2>✅ Verified Quiz Result</h2>
                <div class="score-circle" style="background: rgba(255,255,255,0.2);">
                    <?php echo round($percentage, 1); ?>%
                </div>
            </div>

            <!-- Body -->
            <div class="result-body">
                <!-- Student Info -->
                <div class="info-item">
                    <div class="row align-items-center">
                        <div class="col-4">
                            <i class="fas fa-user text-muted me-2"></i>
                            <strong>Student:</strong>
                        </div>
                        <div class="col-8">
                            <?php echo htmlspecialchars($attempt['full_name']); ?>
                        </div>
                    </div>
                </div>

                <!-- Quiz Info -->
                <div class="info-item">
                    <div class="row align-items-center">
                        <div class="col-4">
                            <i class="fas fa-clipboard-list text-muted me-2"></i>
                            <strong>Quiz:</strong>
                        </div>
                        <div class="col-8">
                            <?php echo htmlspecialchars($attempt['title']); ?>
                        </div>
                    </div>
                </div>

                <!-- Date -->
                <div class="info-item">
                    <div class="row align-items-center">
                        <div class="col-4">
                            <i class="fas fa-calendar text-muted me-2"></i>
                            <strong>Date:</strong>
                        </div>
                        <div class="col-8">
                            <?php echo date('F j, Y', strtotime($attempt['completed_at'])); ?>
                        </div>
                    </div>
                </div>

                <!-- Score -->
                <div class="info-item">
                    <div class="row align-items-center">
                        <div class="col-4">
                            <i class="fas fa-chart-bar text-muted me-2"></i>
                            <strong>Score:</strong>
                        </div>
                        <div class="col-8">
                            <span class="fs-5 fw-bold">
                                <?php echo $attempt['score']; ?>/<?php echo $attempt['total_questions']; ?> 
                                (<?php echo round($percentage, 1); ?>%)
                            </span>
                        </div>
                    </div>
                </div>

                <!-- Grade -->
                <div class="info-item">
                    <div class="row align-items-center">
                        <div class="col-4">
                            <i class="fas fa-award text-muted me-2"></i>
                            <strong>Grade:</strong>
                        </div>
                        <div class="col-8">
                            <span class="badge bg-primary fs-6"><?php echo $grade; ?></span>
                        </div>
                    </div>
                </div>

                <!-- Result Status -->
                <div class="info-item">
                    <div class="row align-items-center">
                        <div class="col-4">
                            <i class="fas fa-flag text-muted me-2"></i>
                            <strong>Result Status:</strong>
                        </div>
                        <div class="col-8">
                            <?php if ($passed): ?>
                                <span class="status-badge bg-success text-white">
                                    <i class="fas fa-check me-1"></i>PASS
                                </span>
                            <?php else: ?>
                                <span class="status-badge bg-danger text-white">
                                    <i class="fas fa-times me-1"></i>FAIL
                                </span>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>

                <!-- QR Notice -->
                <div class="qr-notice">
                    <small>
                        <i class="fas fa-qrcode me-2"></i>
                        <strong>QR Code Access:</strong> This result was accessed by scanning a QR code from the quiz result PDF.
                        This verifies the authenticity of the quiz result.
                    </small>
                </div>

                <!-- Actions -->
                <div class="text-center mt-4">
                    <button class="btn btn-primary me-2" onclick="window.print()">
                        <i class="fas fa-print me-1"></i>Print Result
                    </button>
                    <button class="btn btn-outline-secondary" onclick="window.close()">
                        <i class="fas fa-times me-1"></i>Close
                    </button>
                </div>
            </div>
        </div>

        <!-- Footer -->
        <div class="text-center text-white mt-3 mb-4">
            <small>
                <i class="fas fa-shield-alt me-1"></i>
                Verified Result from Online Quiz System
            </small>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
