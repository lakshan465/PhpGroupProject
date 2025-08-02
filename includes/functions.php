<?php
require_once __DIR__ . '/../config/db.php';

// Function to get all users (for admin)
function getAllUsers() {
    $pdo = getDBConnection();
    $stmt = $pdo->query("SELECT id, username, email, role, full_name, status, created_at FROM users ORDER BY created_at DESC");
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// Function to create new user
function createUser($username, $email, $password, $role, $full_name) {
    $pdo = getDBConnection();
    
    // Check if username or email already exists
    $stmt = $pdo->prepare("SELECT id FROM users WHERE username = ? OR email = ?");
    $stmt->execute([$username, $email]);
    
    if ($stmt->fetch()) {
        return false; // User already exists
    }
    
    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
    
    $stmt = $pdo->prepare("INSERT INTO users (username, email, password, role, full_name) VALUES (?, ?, ?, ?, ?)");
    return $stmt->execute([$username, $email, $hashedPassword, $role, $full_name]);
}

// Function to update user
function updateUser($id, $username, $email, $role, $full_name, $status) {
    $pdo = getDBConnection();
    
    $stmt = $pdo->prepare("UPDATE users SET username = ?, email = ?, role = ?, full_name = ?, status = ? WHERE id = ?");
    return $stmt->execute([$username, $email, $role, $full_name, $status, $id]);
}

// Function to delete user
function deleteUser($id) {
    $pdo = getDBConnection();
    
    // Don't delete admin user with id = 1
    if ($id == 1) {
        return false;
    }
    
    $stmt = $pdo->prepare("DELETE FROM users WHERE id = ?");
    return $stmt->execute([$id]);
}

// Function to get user by ID
function getUserById($id) {
    $pdo = getDBConnection();
    $stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
    $stmt->execute([$id]);
    return $stmt->fetch(PDO::FETCH_ASSOC);
}

// Function to get user statistics
function getUserStatistics() {
    $pdo = getDBConnection();
    
    $stats = [];
    
    // Total users
    $stmt = $pdo->query("SELECT COUNT(*) FROM users");
    $stats['total_users'] = $stmt->fetchColumn();
    
    // Users by role
    $stmt = $pdo->query("SELECT role, COUNT(*) as count FROM users GROUP BY role");
    $roleStats = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    $stats['admin_count'] = 0;
    $stats['teacher_count'] = 0;
    $stats['student_count'] = 0;
    
    foreach ($roleStats as $role) {
        $stats[$role['role'] . '_count'] = $role['count'];
    }
    
    return $stats;
}

// Function to sanitize input
function sanitizeInput($input) {
    return htmlspecialchars(trim($input), ENT_QUOTES, 'UTF-8');
}

// Function to validate email
function validateEmail($email) {
    return filter_var($email, FILTER_VALIDATE_EMAIL);
}

// Function to validate password strength
function validatePassword($password) {
    // At least 8 characters, one uppercase, one lowercase, one number
    return strlen($password) >= 8 && 
           preg_match('/[A-Z]/', $password) && 
           preg_match('/[a-z]/', $password) && 
           preg_match('/[0-9]/', $password);
}

// Function to change user password (admin only)
function changeUserPassword($userId, $newPassword) {
    $pdo = getDBConnection();
    
    try {
        // Hash the new password
        $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);
        
        // Update the password
        $stmt = $pdo->prepare("UPDATE users SET password = ? WHERE id = ?");
        return $stmt->execute([$hashedPassword, $userId]);
    } catch (PDOException $e) {
        return false;
    }
}
?>
