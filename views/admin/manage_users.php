<?php
require_once __DIR__ . '/../../includes/auth.php';
require_once __DIR__ . '/../../includes/functions.php';

// Check if user is admin
requireRole('admin');

$pageTitle = 'Manage Users - User Management System';

// Handle form submissions
$message = '';
$messageType = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action'])) {
        switch ($_POST['action']) {
            case 'create_user':
                $username = trim($_POST['username']);
                $email = trim($_POST['email']);
                $password = $_POST['password'];
                $role = $_POST['role'];
                $full_name = trim($_POST['full_name']);
                
                if (createUser($username, $email, $password, $role, $full_name)) {
                    $message = 'User created successfully!';
                    $messageType = 'success';
                } else {
                    $message = 'Error creating user. Username or email may already exist.';
                    $messageType = 'danger';
                }
                break;
                
            case 'update_user':
                $id = $_POST['user_id'];
                $username = trim($_POST['username']);
                $email = trim($_POST['email']);
                $role = $_POST['role'];
                $full_name = trim($_POST['full_name']);
                $status = $_POST['status'];
                
                if (updateUser($id, $username, $email, $role, $full_name, $status)) {
                    $message = 'User updated successfully!';
                    $messageType = 'success';
                } else {
                    $message = 'Error updating user.';
                    $messageType = 'danger';
                }
                break;
                
            case 'change_password':
                $userId = $_POST['user_id'];
                $newPassword = $_POST['new_password'];
                
                // Simple validation - just check if password is not empty
                if (empty($newPassword)) {
                    $message = 'Password cannot be empty.';
                    $messageType = 'danger';
                } elseif (changeUserPassword($userId, $newPassword)) {
                    $user = getUserById($userId);
                    $message = 'Password changed successfully for user: ' . htmlspecialchars($user['username']);
                    $messageType = 'success';
                } else {
                    $message = 'Error changing password.';
                    $messageType = 'danger';
                }
                break;
        }
    }
}

// Handle GET actions (delete)
if (isset($_GET['action']) && $_GET['action'] === 'delete' && isset($_GET['id'])) {
    if (deleteUser($_GET['id'])) {
        $message = 'User deleted successfully!';
        $messageType = 'success';
    } else {
        $message = 'Error deleting user. Cannot delete admin user.';
        $messageType = 'danger';
    }
}

// Get all users
$users = getAllUsers();

include __DIR__ . '/../../templates/header.php';
?>

<!-- Main content -->
<main class="col-md-9 ms-sm-auto col-lg-10 px-md-4">
    <div class="main-content fade-in">
        <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
            <h1 class="h2">
                <i class="fas fa-users me-2"></i>
                Manage Users
            </h1>
            <div class="btn-toolbar mb-2 mb-md-0">
                <div class="btn-group me-2">
                    <button type="button" class="btn btn-sm btn-outline-secondary" onclick="printTable()">
                        <i class="fas fa-print me-1"></i>Print
                    </button>
                    <button type="button" class="btn btn-sm btn-outline-secondary" onclick="exportToCSV()">
                        <i class="fas fa-download me-1"></i>Export
                    </button>
                </div>
                <button type="button" class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#addUserModal">
                    <i class="fas fa-plus me-1"></i>Add User
                </button>
            </div>
        </div>
        
        <?php if ($message): ?>
            <div class="alert alert-<?php echo $messageType; ?> alert-dismissible fade show" role="alert">
                <i class="fas fa-<?php echo $messageType === 'success' ? 'check-circle' : 'exclamation-triangle'; ?> me-2"></i>
                <?php echo htmlspecialchars($message); ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>
        
        <!-- Users Management Section -->
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">
                    <i class="fas fa-users me-2"></i>
                    All Users
                </h5>
            </div>
            <div class="card-body">
                <!-- Search Box -->
                <div class="row mb-3">
                    <div class="col-md-6">
                        <div class="input-group">
                            <span class="input-group-text">
                                <i class="fas fa-search"></i>
                            </span>
                            <input type="text" class="form-control" id="searchInput" 
                                   placeholder="Search users...">
                        </div>
                    </div>
                </div>
                
                <!-- Users Table -->
                <div class="table-responsive">
                    <table class="table table-hover" id="usersTable">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Username</th>
                                <th>Full Name</th>
                                <th>Email</th>
                                <th>Role</th>
                                <th>Status</th>
                                <th>Created</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($users as $user): ?>
                                <tr>
                                    <td><?php echo $user['id']; ?></td>
                                    <td>
                                        <strong><?php echo htmlspecialchars($user['username']); ?></strong>
                                    </td>
                                    <td><?php echo htmlspecialchars($user['full_name']); ?></td>
                                    <td><?php echo htmlspecialchars($user['email']); ?></td>
                                    <td>
                                        <span class="badge bg-<?php 
                                            echo $user['role'] === 'admin' ? 'danger' : 
                                                ($user['role'] === 'teacher' ? 'warning' : 'info'); 
                                        ?>">
                                            <i class="fas fa-<?php 
                                                echo $user['role'] === 'admin' ? 'user-shield' : 
                                                    ($user['role'] === 'teacher' ? 'chalkboard-teacher' : 'graduation-cap'); 
                                            ?> me-1"></i>
                                            <?php echo ucfirst($user['role']); ?>
                                        </span>
                                    </td>
                                    <td>
                                        <span class="badge bg-<?php echo $user['status'] === 'active' ? 'success' : 'secondary'; ?>">
                                            <?php echo ucfirst($user['status']); ?>
                                        </span>
                                    </td>
                                    <td><?php echo date('M j, Y', strtotime($user['created_at'])); ?></td>
                                    <td>
                                        <div class="btn-group" role="group">
                                            <button type="button" class="btn btn-sm btn-warning" 
                                                    onclick="editUser(<?php echo htmlspecialchars(json_encode($user)); ?>)"
                                                    data-bs-toggle="tooltip" title="Edit User">
                                                <i class="fas fa-edit"></i>
                                            </button>
                                            <button type="button" class="btn btn-sm btn-info" 
                                                    onclick="changePassword(<?php echo $user['id']; ?>, '<?php echo htmlspecialchars($user['username'], ENT_QUOTES); ?>')"
                                                    data-bs-toggle="modal" data-bs-target="#changePasswordModal"
                                                    title="Change Password">
                                                <i class="fas fa-key"></i>
                                            </button>
                                            <?php if ($user['id'] != 1): // Don't allow deleting main admin ?>
                                                <button type="button" class="btn btn-sm btn-danger" 
                                                        onclick="handleUserDelete(<?php echo $user['id']; ?>, '<?php echo htmlspecialchars($user['username'], ENT_QUOTES); ?>')"
                                                        data-bs-toggle="tooltip" title="Delete User">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            <?php endif; ?>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</main>

<!-- Add User Modal -->
<div class="modal fade" id="addUserModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="fas fa-user-plus me-2"></i>
                    Add New User
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST" class="needs-validation" novalidate>
                <div class="modal-body">
                    <input type="hidden" name="action" value="create_user">
                    
                    <div class="mb-3">
                        <label for="username" class="form-label">Username</label>
                        <input type="text" class="form-control" id="username" name="username" required>
                        <div class="invalid-feedback">Please provide a username.</div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="full_name" class="form-label">Full Name</label>
                        <input type="text" class="form-control" id="full_name" name="full_name" required>
                        <div class="invalid-feedback">Please provide a full name.</div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="email" class="form-label">Email</label>
                        <input type="email" class="form-control" id="email" name="email" required>
                        <div class="invalid-feedback">Please provide a valid email.</div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="password" class="form-label">Password</label>
                        <input type="password" class="form-control" id="password" name="password" required>
                        <div id="passwordStrength"></div>
                        <div class="invalid-feedback">Please provide a password.</div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="role" class="form-label">Role</label>
                        <select class="form-select" id="role" name="role" required>
                            <option value="">Choose...</option>
                            <option value="admin">Administrator</option>
                            <option value="teacher">Teacher</option>
                            <option value="student">Student</option>
                        </select>
                        <div class="invalid-feedback">Please select a role.</div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="fas fa-times me-1"></i>Cancel
                    </button>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save me-1"></i>Save User
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Edit User Modal -->
<div class="modal fade" id="editUserModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="fas fa-user-edit me-2"></i>
                    Edit User
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST" class="needs-validation" novalidate>
                <div class="modal-body">
                    <input type="hidden" name="action" value="update_user">
                    <input type="hidden" name="user_id" id="edit_user_id">
                    
                    <div class="mb-3">
                        <label for="edit_username" class="form-label">Username</label>
                        <input type="text" class="form-control" id="edit_username" name="username" required>
                        <div class="invalid-feedback">Please provide a username.</div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="edit_full_name" class="form-label">Full Name</label>
                        <input type="text" class="form-control" id="edit_full_name" name="full_name" required>
                        <div class="invalid-feedback">Please provide a full name.</div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="edit_email" class="form-label">Email</label>
                        <input type="email" class="form-control" id="edit_email" name="email" required>
                        <div class="invalid-feedback">Please provide a valid email.</div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="edit_role" class="form-label">Role</label>
                        <select class="form-select" id="edit_role" name="role" required>
                            <option value="admin">Administrator</option>
                            <option value="teacher">Teacher</option>
                            <option value="student">Student</option>
                        </select>
                        <div class="invalid-feedback">Please select a role.</div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="edit_status" class="form-label">Status</label>
                        <select class="form-select" id="edit_status" name="status" required>
                            <option value="active">Active</option>
                            <option value="inactive">Inactive</option>
                        </select>
                        <div class="invalid-feedback">Please select a status.</div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="fas fa-times me-1"></i>Cancel
                    </button>
                    <button type="submit" class="btn btn-warning">
                        <i class="fas fa-save me-1"></i>Update User
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Change Password Modal -->
<div class="modal fade" id="changePasswordModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="fas fa-key me-2"></i>
                    Change User Password
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST" id="passwordChangeForm">
                <input type="hidden" name="action" value="change_password">
                <input type="hidden" name="user_id" id="change_user_id">
                
                <div class="modal-body">
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle me-2"></i>
                        You are changing the password for user: <strong id="change_username"></strong>
                    </div>
                    
                    <div class="mb-3">
                        <label for="new_password" class="form-label">New Password</label>
                        <div class="input-group">
                            <input type="password" class="form-control" id="new_password" name="new_password" 
                                   required placeholder="Enter new password">
                            <button class="btn btn-outline-secondary" type="button" id="togglePassword">
                                <i class="fas fa-eye"></i>
                            </button>
                        </div>
                        <div class="form-text">
                            Enter any password you want.
                        </div>
                        <div class="invalid-feedback" style="display: none;">Please enter a password.</div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="confirm_password" class="form-label">Confirm New Password</label>
                        <input type="password" class="form-control" id="confirm_password" 
                               required placeholder="Confirm new password">
                        <div class="invalid-feedback" style="display: none;">Passwords do not match.</div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="fas fa-times me-1"></i>Cancel
                    </button>
                    <button type="submit" class="btn btn-info">
                        <i class="fas fa-key me-1"></i>Change Password
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
// Enable tooltips
document.addEventListener('DOMContentLoaded', function() {
    // Initialize tooltips
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });
    
    console.log('Bootstrap tooltips initialized');
});

function editUser(user) {
    document.getElementById('edit_user_id').value = user.id;
    document.getElementById('edit_username').value = user.username;
    document.getElementById('edit_full_name').value = user.full_name;
    document.getElementById('edit_email').value = user.email;
    document.getElementById('edit_role').value = user.role;
    document.getElementById('edit_status').value = user.status;
    
    new bootstrap.Modal(document.getElementById('editUserModal')).show();
}

// Function to handle password change
function changePassword(userId, username) {
    console.log('changePassword function called:', userId, username);
    
    // Set the form values
    const userIdField = document.getElementById('change_user_id');
    const usernameField = document.getElementById('change_username');
    
    if (userIdField && usernameField) {
        userIdField.value = userId;
        usernameField.textContent = username;
        console.log('Values set successfully');
    } else {
        console.error('Could not find form fields');
        return;
    }
    
    // Clear form fields
    const newPasswordField = document.getElementById('new_password');
    const confirmPasswordField = document.getElementById('confirm_password');
    
    if (newPasswordField) newPasswordField.value = '';
    if (confirmPasswordField) confirmPasswordField.value = '';
    
    // Clear any previous validation classes
    if (newPasswordField) {
        newPasswordField.classList.remove('is-invalid', 'is-valid');
    }
    if (confirmPasswordField) {
        confirmPasswordField.classList.remove('is-invalid', 'is-valid');
    }
    
    console.log('Password change modal data prepared');
}

// Toggle password visibility
document.addEventListener('DOMContentLoaded', function() {
    const togglePassword = document.getElementById('togglePassword');
    const passwordField = document.getElementById('new_password');
    
    if (togglePassword && passwordField) {
        togglePassword.addEventListener('click', function() {
            const type = passwordField.getAttribute('type') === 'password' ? 'text' : 'password';
            passwordField.setAttribute('type', type);
            
            const icon = this.querySelector('i');
            icon.classList.toggle('fa-eye');
            icon.classList.toggle('fa-eye-slash');
        });
    }
    
    // Password confirmation validation
    const form = document.querySelector('#passwordChangeForm');
    if (form) {
        form.addEventListener('submit', function(e) {
            console.log('Form submission attempted');
            
            const password = document.getElementById('new_password').value;
            const confirmPassword = document.getElementById('confirm_password').value;
            const passwordField = document.getElementById('new_password');
            const confirmField = document.getElementById('confirm_password');
            
            console.log('Password:', password.length, 'characters');
            console.log('Confirm Password:', confirmPassword.length, 'characters');
            
            let hasErrors = false;
            
            // Clear previous errors
            passwordField.classList.remove('is-invalid', 'is-valid');
            confirmField.classList.remove('is-invalid', 'is-valid');
            passwordField.parentElement.parentElement.querySelector('.invalid-feedback').style.display = 'none';
            confirmField.parentElement.querySelector('.invalid-feedback').style.display = 'none';
            
            // Only check if password is not empty
            if (password.length === 0) {
                console.log('Password is empty');
                passwordField.classList.add('is-invalid');
                passwordField.parentElement.parentElement.querySelector('.invalid-feedback').style.display = 'block';
                hasErrors = true;
            } else {
                passwordField.classList.add('is-valid');
            }
            
            // Check if passwords match
            if (password !== confirmPassword) {
                console.log('Passwords do not match');
                confirmField.classList.add('is-invalid');
                confirmField.parentElement.querySelector('.invalid-feedback').style.display = 'block';
                hasErrors = true;
            } else if (password.length > 0) {
                confirmField.classList.add('is-valid');
            }
            
            if (hasErrors) {
                console.log('Form has errors, preventing submission');
                e.preventDefault();
                return false;
            } else {
                console.log('Form validation passed, submitting...');
                return true;
            }
        });
    }
    
    // Clear validation on input
    document.getElementById('confirm_password')?.addEventListener('input', function() {
        const password = document.getElementById('new_password').value;
        if (this.value === password) {
            this.classList.remove('is-invalid');
            this.classList.add('is-valid');
        } else {
            this.classList.remove('is-valid');
            this.classList.add('is-invalid');
        }
    });
});

// Custom delete handler for this page only
function handleUserDelete(userId, userName) {
    if (confirm(`Are you sure you want to delete user "${userName}"? This action cannot be undone.`)) {
        window.location.href = `manage_users.php?action=delete&id=${userId}`;
    }
}
</script>

<?php include __DIR__ . '/../../templates/footer.php'; ?>
