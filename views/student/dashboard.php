<?php
require_once __DIR__ . '/../../includes/auth.php';

// Check if user is student
requireRole('student');

$pageTitle = 'Student Dashboard - User Management System';

include __DIR__ . '/../../templates/header.php';
?>

<!-- Main content -->
<main class="col-md-9 ms-sm-auto col-lg-10 px-md-4">
    <div class="main-content fade-in">
        <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
            <h1 class="h2">
                <i class="fas fa-graduation-cap me-2"></i>
                Student Dashboard
            </h1>
            <div class="btn-toolbar mb-2 mb-md-0">
                <div class="btn-group me-2">
                    <button type="button" class="btn btn-sm btn-outline-secondary">
                        <i class="fas fa-download me-1"></i>Transcript
                    </button>
                    <button type="button" class="btn btn-sm btn-outline-secondary">
                        <i class="fas fa-calendar me-1"></i>Schedule
                    </button>
                </div>
            </div>
        </div>
        
        <!-- Welcome Section -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="alert alert-success" style="background: linear-gradient(135deg, #28a745, #20c997); color: white; border: none;">
                    <h4 class="alert-heading">
                        <i class="fas fa-rocket me-2"></i>
                        Welcome back, <?php echo htmlspecialchars($_SESSION['full_name']); ?>!
                    </h4>
                    <p>Ready to continue your learning journey? Check out your courses, assignments, and progress below.</p>
                </div>
            </div>
        </div>
        
        <!-- Statistics Cards -->
        <div class="row mb-4">
            <div class="col-xl-3 col-md-6 mb-4">
                <div class="stats-card">
                    <i class="fas fa-book-open"></i>
                    <h3>6</h3>
                    <p>Enrolled Courses</p>
                </div>
            </div>
            <div class="col-xl-3 col-md-6 mb-4">
                <div class="stats-card">
                    <i class="fas fa-tasks"></i>
                    <h3>12</h3>
                    <p>Assignments</p>
                </div>
            </div>
            <div class="col-xl-3 col-md-6 mb-4">
                <div class="stats-card">
                    <i class="fas fa-chart-line"></i>
                    <h3>3.8</h3>
                    <p>GPA</p>
                </div>
            </div>
            <div class="col-xl-3 col-md-6 mb-4">
                <div class="stats-card">
                    <i class="fas fa-trophy"></i>
                    <h3>5</h3>
                    <p>Achievements</p>
                </div>
            </div>
        </div>
        
        <!-- Main Content -->
        <div class="row">
            <div class="col-lg-8">
                <!-- Current Courses -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="mb-0">
                            <i class="fas fa-book-open me-2"></i>
                            My Courses
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <div class="card border-primary">
                                    <div class="card-body">
                                        <h6 class="card-title text-primary">Web Development</h6>
                                        <p class="card-text">
                                            <small class="text-muted">CS101 - Prof. Johnson</small>
                                        </p>
                                        <div class="progress mb-2" style="height: 8px;">
                                            <div class="progress-bar bg-primary" style="width: 85%"></div>
                                        </div>
                                        <div class="d-flex justify-content-between">
                                            <small>Progress: 85%</small>
                                            <small class="text-success">Grade: A-</small>
                                        </div>
                                        <div class="mt-2">
                                            <button class="btn btn-sm btn-primary">
                                                <i class="fas fa-play me-1"></i>Continue
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6 mb-3">
                                <div class="card border-info">
                                    <div class="card-body">
                                        <h6 class="card-title text-info">Database Design</h6>
                                        <p class="card-text">
                                            <small class="text-muted">CS201 - Prof. Smith</small>
                                        </p>
                                        <div class="progress mb-2" style="height: 8px;">
                                            <div class="progress-bar bg-info" style="width: 72%"></div>
                                        </div>
                                        <div class="d-flex justify-content-between">
                                            <small>Progress: 72%</small>
                                            <small class="text-info">Grade: B+</small>
                                        </div>
                                        <div class="mt-2">
                                            <button class="btn btn-sm btn-info">
                                                <i class="fas fa-play me-1"></i>Continue
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6 mb-3">
                                <div class="card border-warning">
                                    <div class="card-body">
                                        <h6 class="card-title text-warning">Software Engineering</h6>
                                        <p class="card-text">
                                            <small class="text-muted">CS301 - Prof. Brown</small>
                                        </p>
                                        <div class="progress mb-2" style="height: 8px;">
                                            <div class="progress-bar bg-warning" style="width: 45%"></div>
                                        </div>
                                        <div class="d-flex justify-content-between">
                                            <small>Progress: 45%</small>
                                            <small class="text-warning">Grade: B</small>
                                        </div>
                                        <div class="mt-2">
                                            <button class="btn btn-sm btn-warning">
                                                <i class="fas fa-play me-1"></i>Continue
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6 mb-3">
                                <div class="card border-success">
                                    <div class="card-body">
                                        <h6 class="card-title text-success">Data Structures</h6>
                                        <p class="card-text">
                                            <small class="text-muted">CS202 - Prof. Davis</small>
                                        </p>
                                        <div class="progress mb-2" style="height: 8px;">
                                            <div class="progress-bar bg-success" style="width: 90%"></div>
                                        </div>
                                        <div class="d-flex justify-content-between">
                                            <small>Progress: 90%</small>
                                            <small class="text-success">Grade: A</small>
                                        </div>
                                        <div class="mt-2">
                                            <button class="btn btn-sm btn-success">
                                                <i class="fas fa-play me-1"></i>Continue
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Upcoming Assignments -->
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">
                            <i class="fas fa-tasks me-2"></i>
                            Upcoming Assignments
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Assignment</th>
                                        <th>Course</th>
                                        <th>Due Date</th>
                                        <th>Status</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td>
                                            <strong>Final Project</strong><br>
                                            <small class="text-muted">E-commerce Website</small>
                                        </td>
                                        <td>Web Development</td>
                                        <td>
                                            <span class="text-danger">Dec 15, 2024</span><br>
                                            <small class="text-muted">3 days left</small>
                                        </td>
                                        <td><span class="badge bg-warning">In Progress</span></td>
                                        <td>
                                            <button class="btn btn-sm btn-primary">
                                                <i class="fas fa-edit me-1"></i>Work
                                            </button>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>
                                            <strong>Database Schema</strong><br>
                                            <small class="text-muted">Library Management System</small>
                                        </td>
                                        <td>Database Design</td>
                                        <td>
                                            <span class="text-warning">Dec 20, 2024</span><br>
                                            <small class="text-muted">8 days left</small>
                                        </td>
                                        <td><span class="badge bg-secondary">Not Started</span></td>
                                        <td>
                                            <button class="btn btn-sm btn-info">
                                                <i class="fas fa-play me-1"></i>Start
                                            </button>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="col-lg-4">
                <!-- Academic Progress -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="mb-0">
                            <i class="fas fa-chart-pie me-2"></i>
                            Academic Progress
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <div class="d-flex justify-content-between">
                                <span>Overall Progress</span>
                                <span>73%</span>
                            </div>
                            <div class="progress">
                                <div class="progress-bar bg-primary" style="width: 73%"></div>
                            </div>
                        </div>
                        <div class="mb-3">
                            <div class="d-flex justify-content-between">
                                <span>Assignments Completed</span>
                                <span>15/20</span>
                            </div>
                            <div class="progress">
                                <div class="progress-bar bg-success" style="width: 75%"></div>
                            </div>
                        </div>
                        <div class="mb-3">
                            <div class="d-flex justify-content-between">
                                <span>Current GPA</span>
                                <span>3.8/4.0</span>
                            </div>
                            <div class="progress">
                                <div class="progress-bar bg-info" style="width: 95%"></div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Quick Links -->
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">
                            <i class="fas fa-link me-2"></i>
                            Quick Links
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="d-grid gap-2">
                            <button class="btn btn-outline-primary">
                                <i class="fas fa-calendar me-2"></i>
                                View Schedule
                            </button>
                            <button class="btn btn-outline-info">
                                <i class="fas fa-book-open me-2"></i>
                                Library Resources
                            </button>
                            <button class="btn btn-outline-success">
                                <i class="fas fa-users me-2"></i>
                                Study Groups
                            </button>
                            <button class="btn btn-outline-warning">
                                <i class="fas fa-question-circle me-2"></i>
                                Get Help
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>

<?php include __DIR__ . '/../../templates/footer.php'; ?>
