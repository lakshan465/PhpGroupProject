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
    
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            position: relative;
            overflow-x: hidden;
        }

        /* Animated Background Elements */
        .bg-shapes {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            overflow: hidden;
            z-index: 1;
        }

        .shape {
            position: absolute;
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(10px);
            border-radius: 50%;
            animation: float 15s infinite ease-in-out;
        }

        .shape:nth-child(1) {
            width: 200px;
            height: 200px;
            top: 10%;
            left: 10%;
            animation-delay: 0s;
            background: linear-gradient(45deg, rgba(255, 255, 255, 0.1), rgba(102, 126, 234, 0.2));
        }

        .shape:nth-child(2) {
            width: 150px;
            height: 150px;
            top: 70%;
            left: 80%;
            animation-delay: 2s;
            background: linear-gradient(45deg, rgba(255, 255, 255, 0.1), rgba(240, 147, 251, 0.2));
        }

        .shape:nth-child(3) {
            width: 100px;
            height: 100px;
            top: 30%;
            right: 20%;
            animation-delay: 4s;
            background: linear-gradient(45deg, rgba(255, 255, 255, 0.1), rgba(245, 87, 108, 0.2));
        }

        .shape:nth-child(4) {
            width: 120px;
            height: 120px;
            bottom: 20%;
            left: 30%;
            animation-delay: 6s;
            background: linear-gradient(45deg, rgba(255, 255, 255, 0.1), rgba(118, 75, 162, 0.2));
        }

        @keyframes float {
            0%, 100% { 
                transform: translateY(0px) rotate(0deg) scale(1); 
                opacity: 0.7;
            }
            33% { 
                transform: translateY(-30px) rotate(120deg) scale(1.1); 
                opacity: 0.9;
            }
            66% { 
                transform: translateY(30px) rotate(240deg) scale(0.9); 
                opacity: 0.5;
            }
        }

        .login-container {
            position: relative;
            z-index: 2;
            width: 100%;
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
            display: flex;
            align-items: center;
            justify-content: center;
            min-height: 100vh;
        }

        .login-wrapper {
            display: grid;
            grid-template-columns: 1fr 1fr;
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(20px);
            border-radius: 20px;
            overflow: hidden;
            box-shadow: 0 25px 60px rgba(0, 0, 0, 0.15);
            max-width: 900px;
            width: 100%;
            min-height: 600px;
            animation: slideInScale 1s ease-out, cardFloat 6s ease-in-out infinite 2s;
            border: 1px solid rgba(255, 255, 255, 0.2);
        }

        @keyframes slideInScale {
            0% {
                opacity: 0;
                transform: translateY(50px) scale(0.9);
            }
            100% {
                opacity: 1;
                transform: translateY(0) scale(1);
            }
        }

        @keyframes cardFloat {
            0%, 100% {
                transform: translateY(0px);
                box-shadow: 0 25px 60px rgba(0, 0, 0, 0.15);
            }
            50% {
                transform: translateY(-10px);
                box-shadow: 0 35px 80px rgba(0, 0, 0, 0.2);
            }
        }

        .login-image {
            background: linear-gradient(45deg, rgba(102, 126, 234, 0.4), rgba(118, 75, 162, 0.4)),
                        url('https://images.unsplash.com/photo-1434030216411-0b793f4b4173?ixlib=rb-1.2.1&auto=format&fit=crop&w=1950&q=80');
            background-size: cover;
            background-position: center;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            color: white;
            text-align: center;
            padding: 40px;
            position: relative;
        }

        .login-image::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(0, 0, 0, 0.1);
            z-index: 1;
        }

        .login-image-content {
            position: relative;
            z-index: 2;
        }

        .login-image h1 {
            font-size: 2.5rem;
            font-weight: 700;
            margin-bottom: 20px;
            text-shadow: 0 2px 10px rgba(0, 0, 0, 0.3);
        }

        .login-image p {
            font-size: 1.2rem;
            opacity: 0.9;
            line-height: 1.6;
            margin-bottom: 30px;
        }

        .feature-list {
            list-style: none;
            text-align: left;
        }

        .feature-list li {
            margin: 15px 0;
            display: flex;
            align-items: center;
            font-size: 1.1rem;
        }

        .feature-list li i {
            margin-right: 15px;
            font-size: 1.3rem;
            color: #ffd700;
        }

        .login-form {
            padding: 60px 50px;
            display: flex;
            flex-direction: column;
            justify-content: center;
            position: relative;
        }

        .login-form::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: linear-gradient(45deg, transparent, rgba(102, 126, 234, 0.02), transparent);
            animation: formShimmer 8s ease-in-out infinite;
            pointer-events: none;
            z-index: 1;
        }

        @keyframes formShimmer {
            0%, 100% { opacity: 0; }
            50% { opacity: 1; }
        }

        .login-header {
            text-align: center;
            margin-bottom: 40px;
        }

        .login-header .logo {
            width: 80px;
            height: 80px;
            background: linear-gradient(135deg, #667eea, #764ba2);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 20px;
            box-shadow: 0 10px 30px rgba(102, 126, 234, 0.3);
            animation: logoPulse 3s ease-in-out infinite;
            position: relative;
            overflow: hidden;
        }

        .login-header .logo::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.4), transparent);
            animation: logoShine 4s ease-in-out infinite;
        }

        @keyframes logoPulse {
            0%, 100% { transform: scale(1); }
            50% { transform: scale(1.05); }
        }

        @keyframes logoShine {
            0% { left: -100%; }
            50% { left: 100%; }
            100% { left: 100%; }
        }

        .login-header .logo i {
            font-size: 2rem;
            color: white;
        }

        .login-header h2 {
            color: #333;
            font-size: 2rem;
            font-weight: 700;
            margin-bottom: 10px;
            animation: fadeInDown 1s ease-out;
        }

        @keyframes fadeInDown {
            0% {
                opacity: 0;
                transform: translateY(-20px);
            }
            100% {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .login-header p {
            color: #666;
            font-size: 1.1rem;
        }

        .form-control {
            width: 100%;
            padding: 15px 20px;
            border: 2px solid #e1e5e9;
            border-radius: 12px;
            font-size: 16px;
            transition: all 0.3s ease;
            background: #f8f9fa;
            position: relative;
            z-index: 100;
            pointer-events: auto;
            cursor: text;
        }

        .form-control:focus {
            outline: none;
            border-color: #667eea;
            background: white;
            box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
            transform: translateY(-2px);
            animation: inputPulse 0.6s ease-out;
        }

        @keyframes inputPulse {
            0% { transform: translateY(-2px) scale(1); }
            50% { transform: translateY(-2px) scale(1.02); }
            100% { transform: translateY(-2px) scale(1); }
        }

        .form-group {
            margin-bottom: 25px;
            position: relative;
            animation: fadeInUp 0.6s ease-out forwards;
            opacity: 0;
            transform: translateY(20px);
            z-index: 100;
            pointer-events: auto;
        }

        .form-group:nth-child(1) { animation-delay: 0.3s; }
        .form-group:nth-child(2) { animation-delay: 0.5s; }

        @keyframes fadeInUp {
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .form-control::placeholder {
            color: #adb5bd;
        }

        .input-icon {
            position: absolute;
            left: 20px;
            top: 50%;
            transform: translateY(-50%);
            color: #adb5bd;
            font-size: 1.1rem;
            transition: all 0.3s ease;
            z-index: 5;
            pointer-events: none;
            user-select: none;
        }

        .form-control.with-icon {
            padding-left: 50px;
            z-index: 100;
            pointer-events: auto;
            cursor: text;
        }

        .btn-login {
            width: 100%;
            padding: 15px;
            background: linear-gradient(135deg, #667eea, #764ba2);
            color: white;
            border: none;
            border-radius: 12px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            margin-top: 10px;
            position: relative;
            overflow: hidden;
            animation: fadeInUp 0.6s ease-out 0.7s forwards;
            opacity: 0;
            transform: translateY(20px);
        }

        .btn-login::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.2), transparent);
            transition: left 0.5s;
        }

        .btn-login:hover {
            transform: translateY(-3px);
            box-shadow: 0 10px 30px rgba(102, 126, 234, 0.4);
            background: linear-gradient(135deg, #5a6fd8, #6a4c93);
        }

        .btn-login:hover::before {
            left: 100%;
        }

        .btn-login:active {
            transform: translateY(-1px);
        }

        .demo-info {
            background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
            color: white;
            padding: 20px;
            border-radius: 12px;
            margin-top: 30px;
            text-align: center;
        }

        .demo-info h6 {
            margin-bottom: 15px;
            font-weight: 600;
        }

        .demo-credentials {
            background: rgba(255, 255, 255, 0.2);
            padding: 15px;
            border-radius: 8px;
            backdrop-filter: blur(10px);
        }

        .alert {
            border-radius: 12px;
            border: none;
            margin-bottom: 25px;
        }

        /* Responsive Design */
        @media (max-width: 768px) {
            .login-wrapper {
                grid-template-columns: 1fr;
                max-width: 450px;
            }
            
            .login-image {
                display: none;
            }
            
            .login-form {
                padding: 40px 30px;
            }
        }

        /* Geometric Animation Effects */
        .particles {
            position: absolute;
            width: 100%;
            height: 100%;
            overflow: hidden;
            z-index: 1;
            pointer-events: none;
        }

        .particle {
            position: absolute;
            width: 8px;
            height: 8px;
            background: rgba(255, 255, 255, 0.8);
            animation: geometricFloat 25s linear infinite;
            pointer-events: none;
            user-select: none;
        }

        .particle:nth-child(1) {
            left: 10%;
            animation-delay: 0s;
            animation-duration: 20s;
            border-radius: 50%;
        }

        .particle:nth-child(2) {
            left: 30%;
            animation-delay: 5s;
            animation-duration: 25s;
            transform: rotate(45deg);
        }

        .particle:nth-child(3) {
            left: 50%;
            animation-delay: 10s;
            animation-duration: 30s;
            border-radius: 2px;
            transform: rotate(90deg);
        }

        .particle:nth-child(4) {
            left: 70%;
            animation-delay: 15s;
            animation-duration: 22s;
            width: 6px;
            height: 6px;
            border-radius: 50%;
        }

        .particle:nth-child(5) {
            left: 90%;
            animation-delay: 20s;
            animation-duration: 28s;
            transform: rotate(135deg);
        }

        .particle:nth-child(6) {
            left: 25%;
            animation-delay: 25s;
            animation-duration: 24s;
            border-radius: 50% 0;
        }

        @keyframes geometricFloat {
            0% {
                transform: translateY(100vh) rotate(0deg);
                opacity: 0;
            }
            10% {
                opacity: 1;
            }
            90% {
                opacity: 1;
            }
            100% {
                transform: translateY(-100vh) rotate(360deg);
                opacity: 0;
            }
        }

        /* Input Focus Glow Effect */
        .input-icon {
            position: absolute;
            left: 20px;
            top: 50%;
            transform: translateY(-50%);
            color: #adb5bd;
            font-size: 1.1rem;
            transition: all 0.3s ease;
        }

        .form-control:focus + .input-icon,
        .form-control:focus ~ .input-icon {
            color: #667eea;
            text-shadow: 0 0 8px rgba(102, 126, 234, 0.3);
        }
    </style>
</head>
<body>
    <!-- Animated Background Shapes -->
    <div class="bg-shapes">
        <div class="shape"></div>
        <div class="shape"></div>
        <div class="shape"></div>
        <div class="shape"></div>
    </div>

    <div class="login-container">
        <!-- Particle Effects -->
        <div class="particles">
            <div class="particle"></div>
            <div class="particle"></div>
            <div class="particle"></div>
            <div class="particle"></div>
            <div class="particle"></div>
            <div class="particle"></div>
        </div>

        <div class="login-wrapper">
            <!-- Left Side - Beautiful Image -->
            <div class="login-image">
                <div class="login-image-content">
                    <!-- Clean image without text -->
                </div>
            </div>

            <!-- Right Side - Login Form -->
            <div class="login-form">
                <div class="login-header">
                    <div class="logo">
                        <i class="fas fa-graduation-cap"></i>
                    </div>
                    <h2>Welcome Back</h2>
                    <p>Sign in to continue your learning journey</p>
                </div>

                <?php if ($error): ?>
                    <div class="alert alert-danger" role="alert">
                        <i class="fas fa-exclamation-triangle me-2"></i>
                        <?php echo htmlspecialchars($error); ?>
                    </div>
                <?php endif; ?>

                <form method="POST">
                    <div class="form-group">
                        <i class="fas fa-user input-icon"></i>
                        <input type="text" class="form-control with-icon" name="username" 
                               placeholder="Enter your username" 
                               value="<?php echo htmlspecialchars($_POST['username'] ?? ''); ?>" 
                               required>
                    </div>

                    <div class="form-group">
                        <i class="fas fa-lock input-icon"></i>
                        <input type="password" class="form-control with-icon" name="password" 
                               placeholder="Enter your password" required>
                    </div>

                    <button type="submit" class="btn-login">
                        <i class="fas fa-sign-in-alt me-2"></i>Sign In
                    </button>
                </form>

                
            </div>
        </div>
    </div>
    
    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <!-- Custom JS -->
    <script src="/public/js/script.js"></script>
</body>
</html>
