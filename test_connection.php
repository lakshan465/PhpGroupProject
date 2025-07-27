<?php
// Test database connection and check for admin user
require_once __DIR__ . '/config/db.php';

echo "<h2>Database Connection Test</h2>";

try {
    $pdo = getDBConnection();
    echo "✅ Database connection successful!<br><br>";
    
    // Check if users table exists
    $stmt = $pdo->query("SHOW TABLES LIKE 'users'");
    $tableExists = $stmt->rowCount() > 0;
    
    if ($tableExists) {
        echo "✅ Users table exists!<br><br>";
        
        // Check if admin user exists
        $stmt = $pdo->prepare("SELECT id, username, email, role, password FROM users WHERE username = 'admin'");
        $stmt->execute();
        $adminUser = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($adminUser) {
            echo "✅ Admin user found!<br>";
            echo "ID: " . $adminUser['id'] . "<br>";
            echo "Username: " . $adminUser['username'] . "<br>";
            echo "Email: " . $adminUser['email'] . "<br>";
            echo "Role: " . $adminUser['role'] . "<br>";
            echo "Password Hash: " . substr($adminUser['password'], 0, 20) . "...<br><br>";
            
            // Test password verification
            $testPassword = 'admin123';
            if (password_verify($testPassword, $adminUser['password'])) {
                echo "✅ Password verification successful for 'admin123'!<br>";
            } else {
                echo "❌ Password verification failed for 'admin123'<br>";
                echo "Let's create a new hash for 'admin123':<br>";
                $newHash = password_hash($testPassword, PASSWORD_DEFAULT);
                echo "New hash: " . $newHash . "<br><br>";
                
                // Update the admin password
                $updateStmt = $pdo->prepare("UPDATE users SET password = ? WHERE username = 'admin'");
                if ($updateStmt->execute([$newHash])) {
                    echo "✅ Admin password updated successfully!<br>";
                } else {
                    echo "❌ Failed to update admin password<br>";
                }
            }
        } else {
            echo "❌ Admin user not found!<br>";
            echo "Let's create the admin user:<br>";
            
            $username = 'admin';
            $email = 'admin@example.com';
            $password = password_hash('admin123', PASSWORD_DEFAULT);
            $role = 'admin';
            $fullName = 'System Administrator';
            
            $stmt = $pdo->prepare("INSERT INTO users (username, email, password, role, full_name) VALUES (?, ?, ?, ?, ?)");
            if ($stmt->execute([$username, $email, $password, $role, $fullName])) {
                echo "✅ Admin user created successfully!<br>";
            } else {
                echo "❌ Failed to create admin user<br>";
            }
        }
        
        // Show all users
        echo "<br><h3>All Users in Database:</h3>";
        $stmt = $pdo->query("SELECT id, username, email, role, status FROM users");
        $users = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        if (count($users) > 0) {
            echo "<table border='1' cellpadding='5'>";
            echo "<tr><th>ID</th><th>Username</th><th>Email</th><th>Role</th><th>Status</th></tr>";
            foreach ($users as $user) {
                echo "<tr>";
                echo "<td>" . $user['id'] . "</td>";
                echo "<td>" . $user['username'] . "</td>";
                echo "<td>" . $user['email'] . "</td>";
                echo "<td>" . $user['role'] . "</td>";
                echo "<td>" . $user['status'] . "</td>";
                echo "</tr>";
            }
            echo "</table>";
        } else {
            echo "No users found in database.";
        }
        
    } else {
        echo "❌ Users table does not exist!<br>";
        echo "Please run the database.sql file to create the table.<br>";
        echo "Or click <a href='create_database.php'>here</a> to create it automatically.";
    }
    
    echo "<br><br><a href='/mini%203ii/test2/actions/login.php'>Go to Login Page</a>";
    
} catch (PDOException $e) {
    echo "❌ Database connection failed: " . $e->getMessage() . "<br>";
    echo "<br>Common solutions:<br>";
    echo "1. Make sure XAMPP MySQL service is running<br>";
    echo "2. Check if database 'user_management_system' exists<br>";
    echo "3. Verify database credentials in config/db.php<br>";
}
?>

<style>
body { font-family: Arial, sans-serif; margin: 20px; }
table { border-collapse: collapse; margin: 10px 0; }
th { background-color: #f0f0f0; }
a { color: #007bff; text-decoration: none; }
a:hover { text-decoration: underline; }
</style>
