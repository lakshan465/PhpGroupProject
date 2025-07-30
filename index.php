<?php
require_once __DIR__ . '/includes/auth.php';

// Redirect to appropriate dashboard if logged in
if (isLoggedIn()) {
    $redirectPage = '';
    switch ($_SESSION['role']) {
        case 'admin':
            $redirectPage = '/Mini%20Project%203ii/PhpGroupProject/views/admin/dashboard.php';
            break;
        case 'teacher':
            $redirectPage = '/Mini%20Project%203ii/PhpGroupProject/views/teacher/dashboard.php';
            break;
        case 'student':
            $redirectPage = '/Mini%20Project%203ii/PhpGroupProject/views/student/dashboard.php';
            break;
    }
    header("Location: $redirectPage");
    exit();
} else {
    // Redirect to login if not logged in
    header('Location: /Mini%20Project%203ii/PhpGroupProject/actions/login.php');
    exit();
}
?>
