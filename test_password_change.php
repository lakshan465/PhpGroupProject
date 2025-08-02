<?php
// Simple test for password change functionality
require_once __DIR__ . '/includes/functions.php';

// Test the password change function
if (isset($_POST['test_password_change'])) {
    $userId = 1; // Test with admin user
    $newPassword = $_POST['new_password'];
    
    echo "<h3>Testing Password Change Function</h3>";
    echo "User ID: " . $userId . "<br>";
    echo "New Password Length: " . strlen($newPassword) . "<br>";
    
    if (changeUserPassword($userId, $newPassword)) {
        echo "<div style='color: green;'>✅ Password changed successfully!</div>";
    } else {
        echo "<div style='color: red;'>❌ Password change failed!</div>";
    }
    echo "<hr>";
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Test Password Change</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <h2>Test Password Change Function</h2>
        
        <form method="POST" class="mt-4">
            <div class="mb-3">
                <label class="form-label">New Password (for admin user)</label>
                <input type="password" class="form-control" name="new_password" required>
                <div class="form-text">Enter any password - no restrictions!</div>
            </div>
            <button type="submit" name="test_password_change" class="btn btn-primary">Test Password Change</button>
        </form>
        
        <hr>
        <a href="views/admin/manage_users.php" class="btn btn-secondary">Go to Manage Users</a>
    </div>
</body>
</html>
