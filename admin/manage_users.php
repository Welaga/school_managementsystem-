<?php
require_once '../config.php';
checkRole(['admin']);

$page_title = "Manage Users";
$message = '';

// Handle form actions
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['add_user'])) {
        $username = trim($_POST['username']);
        $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
        $role = $_POST['role'];
        $status = isset($_POST['status']) ? 1 : 0;
        
        try {
            $stmt = $pdo->prepare("INSERT INTO users (username, password, role, status) VALUES (?, ?, ?, ?)");
            $stmt->execute([$username, $password, $role, $status]);
            $message = "User added successfully!";
        } catch (PDOException $e) {
            $message = "Error adding user: " . $e->getMessage();
        }
    } elseif (isset($_POST['update_user'])) {
        $user_id = $_POST['user_id'];
        $username = trim($_POST['username']);
        $role = $_POST['role'];
        $status = isset($_POST['status']) ? 1 : 0;
        
        try {
            $stmt = $pdo->prepare("UPDATE users SET username = ?, role = ?, status = ? WHERE id = ?");
            $stmt->execute([$username, $role, $status, $user_id]);
            $message = "User updated successfully!";
        } catch (PDOException $e) {
            $message = "Error updating user: " . $e->getMessage();
        }
        
        // Update password if provided
        if (!empty($_POST['password'])) {
            $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
            $stmt = $pdo->prepare("UPDATE users SET password = ? WHERE id = ?");
            $stmt->execute([$password, $user_id]);
        }
    } elseif (isset($_POST['delete_user'])) {
        $user_id = $_POST['user_id'];
        
        try {
            $stmt = $pdo->prepare("DELETE FROM users WHERE id = ?");
            $stmt->execute([$user_id]);
            $message = "User deleted successfully!";
        } catch (PDOException $e) {
            $message = "Error deleting user: " . $e->getMessage();
        }
    }
}

// Get all users
$stmt = $pdo->query("SELECT * FROM users ORDER BY id DESC");
$users = $stmt->fetchAll();

// Get user for editing
$edit_user = null;
if (isset($_GET['edit'])) {
    $stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
    $stmt->execute([$_GET['edit']]);
    $edit_user = $stmt->fetch();
}
?>

<?php include '../includes/header.php'; ?>
<?php include '../includes/sidebar.php'; ?>

<main class="main-content">
    <div class="content-header">
        <h1>Manage Users</h1>
    </div>
    
    <?php if (!empty($message)): ?>
        <div class="alert <?php echo strpos($message, 'Error') !== false ? 'alert-error' : 'alert-success'; ?>">
            <?php echo $message; ?>
        </div>
    <?php endif; ?>
    
    <div class="form-container">
        <h2><?php echo $edit_user ? 'Edit User' : 'Add New User'; ?></h2>
        <form method="POST">
            <?php if ($edit_user): ?>
                <input type="hidden" name="user_id" value="<?php echo $edit_user['id']; ?>">
            <?php endif; ?>
            
            <div class="form-group">
                <label for="username">Username</label>
                <input type="text" id="username" name="username" required 
                       value="<?php echo $edit_user ? $edit_user['username'] : ''; ?>">
            </div>
            
            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" id="password" name="password" 
                       <?php echo !$edit_user ? 'required' : 'placeholder="Leave blank to keep current password"'; ?>>
            </div>
            
            <div class="form-group">
                <label for="role">Role</label>
                <select id="role" name="role" required>
                    <option value="">Select Role</option>
                    <option value="admin" <?php echo $edit_user && $edit_user['role'] == 'admin' ? 'selected' : ''; ?>>Admin</option>
                    <option value="registrar" <?php echo $edit_user && $edit_user['role'] == 'registrar' ? 'selected' : ''; ?>>Registrar</option>
                    <option value="finance" <?php echo $edit_user && $edit_user['role'] == 'finance' ? 'selected' : ''; ?>>Finance</option>
                    <option value="teacher" <?php echo $edit_user && $edit_user['role'] == 'teacher' ? 'selected' : ''; ?>>Teacher</option>
                    <option value="student" <?php echo $edit_user && $edit_user['role'] == 'student' ? 'selected' : ''; ?>>Student</option>
                    <option value="cleaner" <?php echo $edit_user && $edit_user['role'] == 'cleaner' ? 'selected' : ''; ?>>Cleaner</option>
                    <option value="transport" <?php echo $edit_user && $edit_user['role'] == 'transport' ? 'selected' : ''; ?>>Transport</option>
                </select>
            </div>
            
            <div class="form-group">
                <label>
                    <input type="checkbox" name="status" value="1" 
                           <?php echo $edit_user && $edit_user['status'] == 1 ? 'checked' : 'checked'; ?>>
                    Active
                </label>
            </div>
            
            <div class="form-group">
                <?php if ($edit_user): ?>
                    <button type="submit" name="update_user" class="btn btn-primary">Update User</button>
                    <a href="manage_users.php" class="btn btn-secondary">Cancel</a>
                <?php else: ?>
                    <button type="submit" name="add_user" class="btn btn-primary">Add User</button>
                <?php endif; ?>
            </div>
        </form>
    </div>
    
    <div class="table-container">
        <h2>All Users</h2>
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Username</th>
                    <th>Role</th>
                    <th>Status</th>
                    <th>Created</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if (count($users) > 0): ?>
                    <?php foreach ($users as $user): ?>
                        <tr>
                            <td><?php echo $user['id']; ?></td>
                            <td><?php echo $user['username']; ?></td>
                            <td><?php echo ucfirst($user['role']); ?></td>
                            <td>
                                <span class="status-badge <?php echo $user['status'] ? 'completed' : 'pending'; ?>">
                                    <?php echo $user['status'] ? 'Active' : 'Inactive'; ?>
                                </span>
                            </td>
                            <td><?php echo date('M j, Y', strtotime($user['created_at'])); ?></td>
                            <td>
                                <a href="manage_users.php?edit=<?php echo $user['id']; ?>" class="btn btn-sm btn-primary">Edit</a>
                                <form method="POST" class="inline-form">
                                    <input type="hidden" name="user_id" value="<?php echo $user['id']; ?>">
                                    <button type="submit" name="delete_user" class="btn btn-sm btn-danger" 
                                            onclick="return confirm('Are you sure you want to delete this user?')">Delete</button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="6">No users found.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</main>

<?php include '../includes/footer.php'; ?>