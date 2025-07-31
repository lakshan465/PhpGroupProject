<?php
require_once __DIR__ . '/../../includes/auth.php';
require_once __DIR__ . '/../../config/db.php';

// Check if user is teacher
requireRole('teacher');

$quizId = $_GET['id'] ?? 0;
$pdo = getDBConnection();

// Verify quiz belongs to current teacher
$stmt = $pdo->prepare("SELECT * FROM quizzes WHERE id = ? AND teacher_id = ?");
$stmt->execute([$quizId, $_SESSION['user_id']]);
$quiz = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$quiz) {
    header('Location: manage_quizzes.php?error=' . urlencode('Quiz not found or access denied.'));
    exit();
}

try {
    // Delete the quiz (questions and attempts will be deleted automatically due to foreign key constraints)
    $stmt = $pdo->prepare("DELETE FROM quizzes WHERE id = ? AND teacher_id = ?");
    $stmt->execute([$quizId, $_SESSION['user_id']]);
    
    header('Location: manage_quizzes.php?message=' . urlencode('Quiz deleted successfully.'));
    exit();
    
} catch (PDOException $e) {
    header('Location: manage_quizzes.php?error=' . urlencode('Error deleting quiz: ' . $e->getMessage()));
    exit();
}
?>
