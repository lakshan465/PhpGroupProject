<?php
// Automatic database setup
require_once __DIR__ . '/config/db.php';

echo "<h2>Database Setup</h2>";

try {

    
    // Now connect to the specific database
    $pdo = getDBConnection();
    
    // Create users table
    $createTableSQL = "
    CREATE TABLE IF NOT EXISTS users (
        id INT(11) AUTO_INCREMENT PRIMARY KEY,
        username VARCHAR(50) UNIQUE NOT NULL,
        email VARCHAR(100) UNIQUE NOT NULL,
        password VARCHAR(255) NOT NULL,
        role ENUM('admin', 'teacher', 'student') NOT NULL,
        full_name VARCHAR(100) NOT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        status ENUM('active', 'inactive') DEFAULT 'active'
    )";
    
    $pdo->exec($createTableSQL);
    echo "✅ Users table created successfully<br>";
    
    // Check if admin user exists
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM users WHERE username = 'admin'");
    $stmt->execute();
    $adminExists = $stmt->fetchColumn() > 0;
    
    if (!$adminExists) {
        // Insert default admin user
        $adminPassword = password_hash('admin123', PASSWORD_DEFAULT);
        $insertAdminSQL = "
        INSERT INTO users (username, email, password, role, full_name) VALUES 
        ('admin', 'admin@example.com', ?, 'admin', 'System Administrator')";
        
        $stmt = $pdo->prepare($insertAdminSQL);
        $stmt->execute([$adminPassword]);
        echo "✅ Default admin user created<br>";
    } else {
        echo "ℹ️ Admin user already exists<br>";
    }
    
    // Insert sample users if they don't exist
    $sampleUsers = [
        ['teacher1', 'teacher1@example.com', 'teacher', 'John Teacher'],
        ['student1', 'student1@example.com', 'student', 'Jane Student']
    ];
    
    foreach ($sampleUsers as $user) {
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM users WHERE username = ?");
        $stmt->execute([$user[0]]);
        $userExists = $stmt->fetchColumn() > 0;
        
        if (!$userExists) {
            $userPassword = password_hash('admin123', PASSWORD_DEFAULT);
            $stmt = $pdo->prepare("INSERT INTO users (username, email, password, role, full_name) VALUES (?, ?, ?, ?, ?)");
            $stmt->execute([$user[0], $user[1], $userPassword, $user[2], $user[3]]);
            echo "✅ Sample user '{$user[0]}' created<br>";
        }
    }
    
    // Create quizzes table
    $createQuizzesTableSQL = "
    CREATE TABLE IF NOT EXISTS quizzes (
        id INT(11) AUTO_INCREMENT PRIMARY KEY,
        title VARCHAR(200) NOT NULL,
        description TEXT,
        teacher_id INT(11) NOT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        status ENUM('active', 'inactive') DEFAULT 'active',
        FOREIGN KEY (teacher_id) REFERENCES users(id) ON DELETE CASCADE
    )";
    
    $pdo->exec($createQuizzesTableSQL);
    echo "✅ Quizzes table created successfully<br>";
    
    // Create questions table
    $createQuestionsTableSQL = "
    CREATE TABLE IF NOT EXISTS questions (
        id INT(11) AUTO_INCREMENT PRIMARY KEY,
        quiz_id INT(11) NOT NULL,
        question_text TEXT NOT NULL,
        option_a VARCHAR(255) NOT NULL,
        option_b VARCHAR(255) NOT NULL,
        option_c VARCHAR(255) NOT NULL,
        option_d VARCHAR(255) NOT NULL,
        correct_answer ENUM('A', 'B', 'C', 'D') NOT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (quiz_id) REFERENCES quizzes(id) ON DELETE CASCADE
    )";
    
    $pdo->exec($createQuestionsTableSQL);
    echo "✅ Questions table created successfully<br>";
    
} catch (PDOException $e) {
    echo "❌ Error: " . $e->getMessage() . "<br>";
    echo "<br>Please check:<br>";
    echo "1. XAMPP MySQL service is running<br>";
    echo "2. Database credentials in config/db.php are correct<br>";
    echo "3. MySQL user has permission to create databases<br>";
}
?>

<style>
body { font-family: Arial, sans-serif; margin: 20px; }
a { color: #007bff; text-decoration: none; }
a:hover { text-decoration: underline; }
</style>
