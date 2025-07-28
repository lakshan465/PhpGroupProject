<?php
require_once __DIR__ . '/../config/db.php';

// Function to check if user is logged in
function isLoggedIn() {
    return isset($_SESSION['user_id']) && isset($_SESSION['role']);
}

// Function to check user role
function hasRole($role) {
    return isLoggedIn() && $_SESSION['role'] === $role;
}

// Function to redirect if not authorized
function requireRole($role) {
    if (!hasRole($role)) {
        header('Location: /mini%203ii/test2/actions/login.php');
        exit();
    }
}

// Function to redirect if not logged in
function requireLogin() {
    if (!isLoggedIn()) {
        header('Location: /mini%203ii/test2/actions/login.php');
        exit();
    }
}

// Function to authenticate user
function authenticateUser($username, $password) {
    $pdo = getDBConnection();
    
    $stmt = $pdo->prepare("SELECT id, username, email, password, role, full_name FROM users WHERE username = ? AND status = 'active'");
    $stmt->execute([$username]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($user && password_verify($password, $user['password'])) {
        // Set session variables
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['username'] = $user['username'];
        $_SESSION['email'] = $user['email'];
        $_SESSION['role'] = $user['role'];
        $_SESSION['full_name'] = $user['full_name'];
        
        return true;
    }
    
    return false;
}

// Function to logout user
function logoutUser() {
    session_destroy();
    header('Location: /mini%203ii/test2/actions/login.php');
    exit();
}
?>
