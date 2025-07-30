<?php
require_once __DIR__ . '/../../includes/auth.php';
require_once __DIR__ . '/../../includes/functions.php';

// Check if user is admin
requireRole('admin');

$pageTitle = 'Admin Dashboard - User Management System';

// Get statistics
$stats = getUserStatistics();

include __DIR__ . '/../../templates/header.php';
?>

<!-- Main content -->
<main class="col-md-9 ms-sm-auto col-lg-10 px-md-4">
    <div class="main-content fade-in">
        <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
            <h1 class="h2">
                <i class="fas fa-tachometer-alt me-2"></i>
                Admin Dashboard
            </h1>
            <div class="btn-toolbar mb-2 mb-md-0">
                <div class="btn-group me-2">
                    <a href="manage_users.php" class="btn btn-sm btn-primary">
                        <i class="fas fa-users me-1"></i>Manage Users
                    </a>
                </div>
            </div>
        </div>
        
        <!-- Statistics Cards -->
        <div class="row mb-4">
            <div class="col-xl-3 col-md-6 mb-4">
                <div class="stats-card">
                    <i class="fas fa-users"></i>
                    <h3><?php echo $stats['total_users']; ?></h3>
                    <p>Total Users</p>
                </div>
            </div>
            <div class="col-xl-3 col-md-6 mb-4">
                <div class="stats-card">
                    <i class="fas fa-user-shield"></i>
                    <h3><?php echo $stats['admin_count']; ?></h3>
                    <p>Administrators</p>
                </div>
            </div>
            <div class="col-xl-3 col-md-6 mb-4">
                <div class="stats-card">
                    <i class="fas fa-chalkboard-teacher"></i>
                    <h3><?php echo $stats['teacher_count']; ?></h3>
                    <p>Teachers</p>
                </div>
            </div>
            <div class="col-xl-3 col-md-6 mb-4">
                <div class="stats-card">
                    <i class="fas fa-graduation-cap"></i>
                    <h3><?php echo $stats['student_count']; ?></h3>
                    <p>Students</p>
                </div>
            </div>
        </div>
        
        <!-- Welcome Section -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">
                            <i class="fas fa-info-circle me-2"></i>
                            System Overview
                        </h5>
                    </div>
                    <div class="card-body">
                        <h4>Welcome, <?php echo htmlspecialchars($_SESSION['full_name']); ?>!</h4>
                        <p>You have administrative privileges to manage the entire system. Here's what you can do:</p>
                        
                        <div class="row">
                            <div class="col-md-6">
                                <h6><i class="fas fa-check text-success me-2"></i>User Management</h6>
                                <ul>
                                    <li>Create new user accounts</li>
                                    <li>Edit existing user information</li>
                                    <li>Delete user accounts</li>
                                    <li>Change user roles and permissions</li>
                                </ul>
                            </div>
                            <div class="col-md-6">
                                <h6><i class="fas fa-check text-success me-2"></i>System Administration</h6>
                                <ul>
                                    <li>View system statistics</li>
                                    <li>Monitor user activities</li>
                                    <li>Export user data</li>
                                    <li>Manage system settings</li>
                                </ul>
                            </div>
                        </div>
                        
                        <div class="mt-3">
                            <a href="manage_users.php" class="btn btn-primary me-2">
                                <i class="fas fa-users me-1"></i>Manage Users
                            </a>
                            <a href="#" class="btn btn-outline-secondary">
                                <i class="fas fa-cog me-1"></i>System Settings
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Recent Users -->
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">
                            <i class="fas fa-clock me-2"></i>
                            Recently Added Users
                        </h5>
                    </div>
                    <div class="card-body">
                        <?php
                        $recentUsers = getAllUsers();
                        $recentUsers = array_slice($recentUsers, 0, 5); // Get only 5 recent users
                        ?>
                        
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Username</th>
                                        <th>Full Name</th>
                                        <th>Email</th>
                                        <th>Role</th>
                                        <th>Status</th>
                                        <th>Created</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($recentUsers as $user): ?>
                                        <tr>
                                            <td><strong><?php echo htmlspecialchars($user['username']); ?></strong></td>
                                            <td><?php echo htmlspecialchars($user['full_name']); ?></td>
                                            <td><?php echo htmlspecialchars($user['email']); ?></td>
                                            <td>
                                                <span class="badge bg-<?php 
                                                    echo $user['role'] === 'admin' ? 'danger' : 
                                                        ($user['role'] === 'teacher' ? 'warning' : 'info'); 
                                                ?>">
                                                    <?php echo ucfirst($user['role']); ?>
                                                </span>
                                            </td>
                                            <td>
                                                <span class="badge bg-<?php echo $user['status'] === 'active' ? 'success' : 'secondary'; ?>">
                                                    <?php echo ucfirst($user['status']); ?>
                                                </span>
                                            </td>
                                            <td><?php echo date('M j, Y', strtotime($user['created_at'])); ?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                        
                        <div class="text-center mt-3">
                            <a href="manage_users.php" class="btn btn-outline-primary">
                                <i class="fas fa-eye me-1"></i>View All Users
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>

<?php include __DIR__ . '/../../templates/footer.php'; ?>
