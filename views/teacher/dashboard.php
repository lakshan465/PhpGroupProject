<?php
require_once __DIR__ . '/../../includes/auth.php';

// Check if user is teacher
requireRole('teacher');

$pageTitle = 'Teacher Dashboard - User Management System';

include __DIR__ . '/../../templates/header.php';
?>

<!-- Main content -->
<main class="col-md-9 ms-sm-auto col-lg-10 px-md-4">
    <div class="main-content fade-in">
        <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
            <h1 class="h2">
                <i class="fas fa-chalkboard-teacher me-2"></i>
                Teacher Dashboard
            </h1>
            <div class="btn-toolbar mb-2 mb-md-0">
                <div class="btn-group me-2">
                    <button type="button" class="btn btn-sm btn-outline-secondary">
                        <i class="fas fa-calendar me-1"></i>Schedule
                    </button>
                    <button type="button" class="btn btn-sm btn-outline-secondary">
                        <i class="fas fa-chart-bar me-1"></i>Reports
                    </button>
                </div>
            </div>
        </div>
        
        <!-- Welcome Section -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="alert alert-info" style="background: linear-gradient(135deg, #17a2b8, #138496); color: white; border: none;">
                    <h4 class="alert-heading">
                        <i class="fas fa-hand-wave me-2"></i>
                        Welcome back, <?php echo htmlspecialchars($_SESSION['full_name']); ?>!
                    </h4>
                    <p>Here's your teaching dashboard where you can manage your courses, view student progress, and access teaching resources.</p>
                </div>
            </div>
        </div>
        
        <!-- Statistics Cards -->
        <div class="row mb-4">
            <div class="col-xl-3 col-md-6 mb-4">
                <div class="stats-card">
                    <i class="fas fa-book"></i>
                    <h3>8</h3>
                    <p>Active Courses</p>
                </div>
            </div>
            <div class="col-xl-3 col-md-6 mb-4">
                <div class="stats-card">
                    <i class="fas fa-users"></i>
                    <h3>145</h3>
                    <p>Students</p>
                </div>
            </div>
            <div class="col-xl-3 col-md-6 mb-4">
                <div class="stats-card">
                    <i class="fas fa-tasks"></i>
                    <h3>23</h3>
                    <p>Assignments</p>
                </div>
            </div>
            <div class="col-xl-3 col-md-6 mb-4">
                <div class="stats-card">
                    <i class="fas fa-star"></i>
                    <h3>4.8</h3>
                    <p>Rating</p>
                </div>
            </div>
        </div>
        
        <!-- Course Management -->
        <div class="row">
            <div class="col-lg-8">
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">
                            <i class="fas fa-book me-2"></i>
                            My Courses
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Course</th>
                                        <th>Students</th>
                                        <th>Progress</th>
                                        <th>Status</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td>
                                            <strong>Web Development</strong><br>
                                            <small class="text-muted">CS101</small>
                                        </td>
                                        <td>32</td>
                                        <td>
                                            <div class="progress" style="height: 8px;">
                                                <div class="progress-bar bg-success" style="width: 85%"></div>
                                            </div>
                                            <small>85%</small>
                                        </td>
                                        <td><span class="badge bg-success">Active</span></td>
                                        <td>
                                            <div class="btn-group" role="group">
                                                <button class="btn btn-sm btn-primary" data-bs-toggle="tooltip" title="View Course">
                                                    <i class="fas fa-eye"></i>
                                                </button>
                                                <button class="btn btn-sm btn-warning" data-bs-toggle="tooltip" title="Edit Course">
                                                    <i class="fas fa-edit"></i>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>
                                            <strong>Database Design</strong><br>
                                            <small class="text-muted">CS201</small>
                                        </td>
                                        <td>28</td>
                                        <td>
                                            <div class="progress" style="height: 8px;">
                                                <div class="progress-bar bg-info" style="width: 72%"></div>
                                            </div>
                                            <small>72%</small>
                                        </td>
                                        <td><span class="badge bg-success">Active</span></td>
                                        <td>
                                            <div class="btn-group" role="group">
                                                <button class="btn btn-sm btn-primary" data-bs-toggle="tooltip" title="View Course">
                                                    <i class="fas fa-eye"></i>
                                                </button>
                                                <button class="btn btn-sm btn-warning" data-bs-toggle="tooltip" title="Edit Course">
                                                    <i class="fas fa-edit"></i>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>
                                            <strong>Software Engineering</strong><br>
                                            <small class="text-muted">CS301</small>
                                        </td>
                                        <td>24</td>
                                        <td>
                                            <div class="progress" style="height: 8px;">
                                                <div class="progress-bar bg-warning" style="width: 45%"></div>
                                            </div>
                                            <small>45%</small>
                                        </td>
                                        <td><span class="badge bg-success">Active</span></td>
                                        <td>
                                            <div class="btn-group" role="group">
                                                <button class="btn btn-sm btn-primary" data-bs-toggle="tooltip" title="View Course">
                                                    <i class="fas fa-eye"></i>
                                                </button>
                                                <button class="btn btn-sm btn-warning" data-bs-toggle="tooltip" title="Edit Course">
                                                    <i class="fas fa-edit"></i>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="col-lg-4">
                <!-- Recent Activities -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="mb-0">
                            <i class="fas fa-clock me-2"></i>
                            Recent Activities
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="list-group list-group-flush">
                            <div class="list-group-item border-0 px-0">
                                <div class="d-flex align-items-center">
                                    <div class="flex-shrink-0">
                                        <i class="fas fa-file-alt text-primary"></i>
                                    </div>
                                    <div class="flex-grow-1 ms-3">
                                        <h6 class="mb-0">Assignment Graded</h6>
                                        <small class="text-muted">Web Development - Assignment 3</small>
                                    </div>
                                    <small class="text-muted">2h ago</small>
                                </div>
                            </div>
                            <div class="list-group-item border-0 px-0">
                                <div class="d-flex align-items-center">
                                    <div class="flex-shrink-0">
                                        <i class="fas fa-users text-success"></i>
                                    </div>
                                    <div class="flex-grow-1 ms-3">
                                        <h6 class="mb-0">New Student Enrolled</h6>
                                        <small class="text-muted">Database Design Course</small>
                                    </div>
                                    <small class="text-muted">5h ago</small>
                                </div>
                            </div>
                            <div class="list-group-item border-0 px-0">
                                <div class="d-flex align-items-center">
                                    <div class="flex-shrink-0">
                                        <i class="fas fa-comment text-info"></i>
                                    </div>
                                    <div class="flex-grow-1 ms-3">
                                        <h6 class="mb-0">New Discussion Post</h6>
                                        <small class="text-muted">Software Engineering Forum</small>
                                    </div>
                                    <small class="text-muted">1d ago</small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Quick Actions -->
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">
                            <i class="fas fa-bolt me-2"></i>
                            Quick Actions
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="d-grid gap-2">
                            <button class="btn btn-primary">
                                <i class="fas fa-plus me-2"></i>
                                Create Assignment
                            </button>
                            <button class="btn btn-info">
                                <i class="fas fa-chart-line me-2"></i>
                                View Analytics
                            </button>
                            <button class="btn btn-success">
                                <i class="fas fa-calendar-plus me-2"></i>
                                Schedule Class
                            </button>
                            <button class="btn btn-warning">
                                <i class="fas fa-envelope me-2"></i>
                                Send Announcement
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>

<?php include __DIR__ . '/../../templates/footer.php'; ?>
