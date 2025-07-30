<?php
require_once __DIR__ . '/../includes/auth.php';

// Redirect if already logged in
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
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username']);
    $password = $_POST['password'];
    
    if (empty($username) || empty($password)) {
        $error = 'Please fill in all fields.';
    } else {
        if (authenticateUser($username, $password)) {
            // Redirect based on role
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
            $error = 'Invalid username or password.';
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Online Quiz System</title>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <!-- Custom CSS -->
    <link href="/Mini%20Project%203ii/PhpGroupProject/public/css/style.css" rel="stylesheet">
</head>
<body>
    <div class="login-container">
        <div class="login-card fade-in">
            <div class="login-header">
                <i class="fas fa-users fa-3x text-primary mb-3"></i>
                <h2>Welcome Back</h2>
                <p>Sign in to your account</p>
            </div>
            
            <?php if ($error): ?>
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <i class="fas fa-exclamation-triangle me-2"></i>
                    <?php echo htmlspecialchars($error); ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>
            
            <form method="POST" class="needs-validation" novalidate>
                <div class="mb-3">
                    <label for="username" class="form-label">
                        <i class="fas fa-user me-2"></i>Username
                    </label>
                    <input type="text" class="form-control" id="username" name="username" 
                           value="<?php echo htmlspecialchars($_POST['username'] ?? ''); ?>" 
                           required>
                    <div class="invalid-feedback">
                        Please provide a valid username.
                    </div>
                </div>
                
                <div class="mb-4">
                    <label for="password" class="form-label">
                        <i class="fas fa-lock me-2"></i>Password
                    </label>
                    <div class="input-group">
                        <input type="password" class="form-control" id="password" name="password" required>
                        <button class="btn btn-outline-secondary" type="button" onclick="togglePassword('password')">
                            <i class="fas fa-eye"></i>
                        </button>
                    </div>
                    <div class="invalid-feedback">
                        Please provide a valid password.
                    </div>
                </div>
                
                <div class="d-grid">
                    <button type="submit" class="btn btn-primary btn-lg">
                        <i class="fas fa-sign-in-alt me-2"></i>Sign In
                    </button>
                </div>
            </form>
            
            <div class="mt-4 text-center">
                <small class="text-muted">
                    Default Admin: username: <strong>admin</strong>, password: <strong>admin123</strong>
                </small>
            </div>
        </div>
    </div>
    
    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <!-- Custom JS -->
    <script src="/public/js/script.js"></script>
</body>
</html>
