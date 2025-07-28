<?php
require_once __DIR__ . '/includes/auth.php';

// Redirect to appropriate dashboard if logged in
if (isLoggedIn()) {
    $redirectPage = '';
    switch ($_SESSION['role']) {
        case 'admin':
            $redirectPage = '/mini%203ii/test2/views/admin/dashboard.php';
            break;
        case 'teacher':
            $redirectPage = '/mini%203ii/test2/views/teacher/dashboard.php';
            break;
        case 'student':
            $redirectPage = '/mini%203ii/test2/views/student/dashboard.php';
            break;
    }
    header("Location: $redirectPage");
    exit();
} else {
    // Redirect to login if not logged in
    header('Location: /mini%203ii/test2/actions/login.php');
    exit();
}
?>
