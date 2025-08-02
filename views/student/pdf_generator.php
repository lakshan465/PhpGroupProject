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

// Include TCPDF library
require_once(__DIR__ . '/../../vendor/tecnickcom/tcpdf/tcpdf.php');

// Create new PDF document
$pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

// Set document information
$pdf->SetCreator('Online Quiz System');
$pdf->SetAuthor('Student Portal');
$pdf->SetTitle('Quiz Results - ' . $attempt['title']);
$pdf->SetSubject('Quiz Results Report');

// Set margins
$pdf->SetMargins(20, 20, 20);
$pdf->SetHeaderMargin(10);
$pdf->SetFooterMargin(15);

// Set auto page breaks
$pdf->SetAutoPageBreak(TRUE, 25);

// Add a page
$pdf->AddPage();

// Set font
$pdf->SetFont('helvetica', '', 12);

// Title
$pdf->SetFont('helvetica', 'B', 18);
$pdf->SetTextColor(0, 123, 255);
$pdf->Cell(0, 15, 'Quiz Results Report', 0, 1, 'C');

$pdf->Ln(5);

// Quiz title
$pdf->SetFont('helvetica', 'B', 16);
$pdf->SetTextColor(51, 51, 51);
$pdf->Cell(0, 10, $attempt['title'], 0, 1, 'C');

$pdf->Ln(10);

// Student info
$pdf->SetFont('helvetica', '', 12);
$pdf->SetTextColor(0, 0, 0);
$pdf->Cell(40, 8, 'Student:', 0, 0, 'L');
$pdf->SetFont('helvetica', 'B', 12);
$pdf->Cell(0, 8, $attempt['full_name'], 0, 1, 'L');

$pdf->SetFont('helvetica', '', 12);
$pdf->Cell(40, 8, 'Date:', 0, 0, 'L');
$pdf->SetFont('helvetica', 'B', 12);
$pdf->Cell(0, 8, date('F j, Y g:i A', strtotime($attempt['completed_at'])), 0, 1, 'L');

$pdf->Ln(10);

// Score summary box
$pdf->SetFillColor(248, 249, 250);
$pdf->SetDrawColor(222, 226, 230);
$pdf->Rect(20, $pdf->GetY(), 170, 40, 'DF');

$pdf->SetFont('helvetica', 'B', 14);
$pdf->SetTextColor(0, 123, 255);
$pdf->Cell(0, 10, 'Score Summary', 0, 1, 'C');

$pdf->SetFont('helvetica', '', 12);
$pdf->SetTextColor(0, 0, 0);

// Score details in columns
$y_pos = $pdf->GetY();
$pdf->SetXY(30, $y_pos);
$pdf->Cell(35, 8, 'Score:', 0, 0, 'L');
$pdf->SetFont('helvetica', 'B', 12);
$pdf->Cell(40, 8, $attempt['score'] . ' / ' . $attempt['total_questions'], 0, 0, 'L');

$pdf->SetXY(120, $y_pos);
$pdf->SetFont('helvetica', '', 12);
$pdf->Cell(35, 8, 'Percentage:', 0, 0, 'L');
$pdf->SetFont('helvetica', 'B', 12);
$pdf->Cell(0, 8, round($percentage, 1) . '%', 0, 1, 'L');

$pdf->Ln(2);
$y_pos = $pdf->GetY();
$pdf->SetXY(30, $y_pos);
$pdf->SetFont('helvetica', '', 12);
$pdf->Cell(35, 8, 'Grade:', 0, 0, 'L');
$pdf->SetFont('helvetica', 'B', 12);
$pdf->Cell(40, 8, $grade, 0, 0, 'L');

$pdf->SetXY(120, $y_pos);
$pdf->SetFont('helvetica', '', 12);
$pdf->Cell(35, 8, 'Status:', 0, 0, 'L');
$pdf->SetFont('helvetica', 'B', 12);
if ($percentage >= 35) {
    $pdf->SetTextColor(40, 167, 69);
    $pdf->Cell(0, 8, 'PASSED', 0, 1, 'L');
} else {
    $pdf->SetTextColor(220, 53, 69);
    $pdf->Cell(0, 8, 'FAILED', 0, 1, 'L');
}

$pdf->Ln(15);

// QR Code Section
$pdf->SetFont('helvetica', 'B', 14);
$pdf->SetTextColor(0, 123, 255);
$pdf->Cell(0, 10, 'Verified Quiz Result QR Code', 0, 1, 'C');
$pdf->SetTextColor(0, 0, 0);

// Generate QR code URL (pointing to brief quiz result)
$base_url = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://" . $_SERVER['HTTP_HOST'];
$result_url = $base_url . '/Mini Project 3ii/PhpGroupProject/views/student/quiz_result_brief.php?attempt_id=' . $attempt_id;

// Generate QR code using TCPDF's built-in functionality
try {
    $current_y = $pdf->GetY();
    
    // Calculate center position for QR code
    $page_width = $pdf->getPageWidth();
    $qr_size = 40;
    $qr_x = ($page_width - $qr_size) / 2;
    
    // Add QR code centered
    $pdf->SetTextColor(0, 0, 0);
    $pdf->write2DBarcode($result_url, 'QRCODE,L', $qr_x, $current_y, $qr_size, $qr_size, array(), 'N');
    
    // Add centered text below QR code
    $pdf->SetY($current_y + $qr_size + 5);
    $pdf->SetFont('helvetica', 'B', 12);
    $pdf->Cell(0, 8, 'Scan QR Code', 0, 1, 'C');
    
    // Move Y position past the QR code section
    $pdf->SetY($current_y + $qr_size + 15);
    
} catch (Exception $e) {
    // Fallback if QR generation fails
    $current_y = $pdf->GetY();
    
    // Calculate center position for placeholder
    $page_width = $pdf->getPageWidth();
    $placeholder_size = 40;
    $placeholder_x = ($page_width - $placeholder_size) / 2;
    
    // Draw centered placeholder box
    $pdf->SetFillColor(240, 240, 240);
    $pdf->SetDrawColor(200, 200, 200);
    $pdf->Rect($placeholder_x, $current_y, $placeholder_size, $placeholder_size, 'DF');
    
    // Add QR icon text in the centered box
    $pdf->SetXY($placeholder_x, $current_y + 15);
    $pdf->SetFont('helvetica', 'B', 12);
    $pdf->SetTextColor(100, 100, 100);
    $pdf->Cell($placeholder_size, 8, 'QR CODE', 0, 1, 'C');
    $pdf->SetXY($placeholder_x, $current_y + 25);
    $pdf->SetFont('helvetica', '', 10);
    $pdf->Cell($placeholder_size, 6, 'ERROR', 0, 1, 'C');
    
    // Add centered error message below
    $pdf->SetY($current_y + $placeholder_size + 5);
    $pdf->SetFont('helvetica', 'B', 11);
    $pdf->SetTextColor(220, 53, 69);
    $pdf->Cell(0, 6, 'QR Generation Failed', 0, 1, 'C');
    
    // Move Y position past the placeholder section
    $pdf->SetY($current_y + $placeholder_size + 20);
}

$pdf->Ln(10);

// Questions and answers
$pdf->SetFont('helvetica', 'B', 14);
$pdf->SetTextColor(0, 123, 255);
$pdf->Cell(0, 10, 'Detailed Question Review', 0, 1, 'L');

$pdf->SetTextColor(0, 0, 0);

foreach ($answers as $index => $answer) {
    $questionNum = $index + 1;
    $isCorrect = $answer['is_correct'];
    
    // Check if we need a new page
    if ($pdf->GetY() > 250) {
        $pdf->AddPage();
    }
    
    // Question box
    if ($isCorrect) {
        $pdf->SetFillColor(212, 237, 218);
        $pdf->SetDrawColor(40, 167, 69);
    } else {
        $pdf->SetFillColor(248, 215, 218);
        $pdf->SetDrawColor(220, 53, 69);
    }
    
    $pdf->Ln(5);
    $startY = $pdf->GetY();
    
    // Question header
    $pdf->SetFont('helvetica', 'B', 12);
    $pdf->Cell(150, 8, 'Question ' . $questionNum, 0, 0, 'L');
    
    // Status badge
    $pdf->SetFont('helvetica', 'B', 10);
    if ($isCorrect) {
        $pdf->SetTextColor(255, 255, 255);
        $pdf->SetFillColor(40, 167, 69);
        $pdf->Cell(20, 8, 'CORRECT', 1, 1, 'C', true);
    } else {
        $pdf->SetTextColor(255, 255, 255);
        $pdf->SetFillColor(220, 53, 69);
        $pdf->Cell(20, 8, 'WRONG', 1, 1, 'C', true);
    }
    
    $pdf->SetTextColor(0, 0, 0);
    $pdf->SetFont('helvetica', '', 11);
    
    // Question text
    $pdf->SetFont('helvetica', 'B', 11);
    $pdf->Cell(20, 7, 'Q: ', 0, 0, 'L');
    $pdf->SetFont('helvetica', '', 11);
    $pdf->MultiCell(150, 7, $answer['question_text'], 0, 'L');
    
    $pdf->Ln(2);
    
    // Your answer
    $pdf->SetFont('helvetica', 'B', 11);
    $pdf->Cell(30, 7, 'Your Answer: ', 0, 0, 'L');
    $pdf->SetFont('helvetica', '', 11);
    $your_answer = $answer['selected_answer'] . ') ' . $answer['option_' . strtolower($answer['selected_answer'])];
    $pdf->MultiCell(140, 7, $your_answer, 0, 'L');
    
    // Correct answer (if wrong)
    if (!$isCorrect) {
        $pdf->Ln(1);
        $pdf->SetFont('helvetica', 'B', 11);
        $pdf->SetTextColor(40, 167, 69);
        $pdf->Cell(35, 7, 'Correct Answer: ', 0, 0, 'L');
        $pdf->SetFont('helvetica', '', 11);
        $correct_answer = $answer['correct_answer'] . ') ' . $answer['option_' . strtolower($answer['correct_answer'])];
        $pdf->MultiCell(135, 7, $correct_answer, 0, 'L');
        $pdf->SetTextColor(0, 0, 0);
    }
    
    $pdf->Ln(3);
}

// Footer
$pdf->Ln(10);
$pdf->SetFont('helvetica', '', 10);
$pdf->SetTextColor(108, 117, 125);
$pdf->Cell(0, 5, 'Generated on ' . date('F j, Y \a\t g:i:s A'), 0, 1, 'C');
$pdf->Cell(0, 5, 'Online Quiz System - Student Portal', 0, 1, 'C');

if ($percentage >= 60) {
    $pdf->Ln(5);
    $pdf->SetTextColor(40, 167, 69);
    $pdf->SetFont('helvetica', 'B', 12);
    $pdf->Cell(0, 8, 'Congratulations! You are eligible for a certificate.', 0, 1, 'C');
    
    // Add certificate download info
    $pdf->Ln(3);
    $pdf->SetFont('helvetica', '', 10);
    $pdf->SetTextColor(0, 123, 255);
    $pdf->Cell(0, 6, 'Visit your dashboard to download your certificate!', 0, 1, 'C');
}

// Add QR code info in footer
$pdf->Ln(8);
$pdf->SetFont('helvetica', '', 9);
$pdf->SetTextColor(108, 117, 125);
$pdf->Cell(0, 4, '📱 Scan the QR code above for verified quiz result summary!', 0, 1, 'C');

// Output PDF
$filename = 'quiz_results_' . $attempt_id . '_' . date('Y-m-d') . '.pdf';
$pdf->Output($filename, 'D');
?>
