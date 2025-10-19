<?php
require_once '../config.php';
checkRole(['admin']);

$page_title = "System Settings";
$message = '';

// Handle form actions
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['update_settings'])) {
        $school_name = trim($_POST['school_name']);
        $school_address = trim($_POST['school_address']);
        $school_phone = trim($_POST['school_phone']);
        $school_email = trim($_POST['school_email']);
        $academic_year = trim($_POST['academic_year']);
        $term = $_POST['term'];
        
        try {
            // Update or insert settings
            $stmt = $pdo->prepare("INSERT INTO system_settings (setting_key, setting_value) 
                                  VALUES 
                                  ('school_name', ?), 
                                  ('school_address', ?), 
                                  ('school_phone', ?), 
                                  ('school_email', ?), 
                                  ('academic_year', ?), 
                                  ('current_term', ?)
                                  ON DUPLICATE KEY UPDATE setting_value = VALUES(setting_value)");
            $stmt->execute([$school_name, $school_address, $school_phone, $school_email, $academic_year, $term]);
            
            $message = "System settings updated successfully!";
        } catch (PDOException $e) {
            $message = "Error updating settings: " . $e->getMessage();
        }
    } elseif (isset($_POST['send_notification'])) {
        $notification_title = trim($_POST['notification_title']);
        $notification_message = trim($_POST['notification_message']);
        $recipient_type = $_POST['recipient_type'];
        $specific_recipients = isset($_POST['specific_recipients']) ? $_POST['specific_recipients'] : [];
        
        try {
            // Create notification
            $stmt = $pdo->prepare("INSERT INTO notifications (title, message, sender_id, recipient_type) 
                                  VALUES (?, ?, ?, ?)");
            $stmt->execute([$notification_title, $notification_message, $_SESSION['user_id'], $recipient_type]);
            $notification_id = $pdo->lastInsertId();
            
            // Add recipients
            if ($recipient_type == 'specific' && !empty($specific_recipients)) {
                foreach ($specific_recipients as $user_id) {
                    $stmt = $pdo->prepare("INSERT INTO notification_recipients (notification_id, user_id) 
                                          VALUES (?, ?)");
                    $stmt->execute([$notification_id, $user_id]);
                }
            }
            
            $message = "Notification sent successfully!";
        } catch (PDOException $e) {
            $message = "Error sending notification: " . $e->getMessage();
        }
    }
}

// Get current settings
$settings = [];
$stmt = $pdo->query("SELECT setting_key, setting_value FROM system_settings");
$settings_result = $stmt->fetchAll(PDO::FETCH_KEY_PAIR);
$settings = $settings_result;

// Get all users for notification recipients
$stmt = $pdo->query("SELECT id, username, role FROM users WHERE status = 1 ORDER BY role, username");
$users = $stmt->fetchAll();

// Get recent notifications
$stmt = $pdo->query("SELECT n.*, u.username as sender_name 
                    FROM notifications n 
                    JOIN users u ON n.sender_id = u.id 
                    ORDER BY n.created_at DESC LIMIT 10");
$notifications = $stmt->fetchAll();
?>

<?php include '../includes/header.php'; ?>
<?php include '../includes/sidebar.php'; ?>

<main class="main-content">
    <div class="content-header">
        <h1>System Settings</h1>
    </div>
    
    <?php if (!empty($message)): ?>
        <div class="alert <?php echo strpos($message, 'Error') !== false ? 'alert-error' : 'alert-success'; ?>">
            <?php echo $message; ?>
        </div>
    <?php endif; ?>
    
    <div class="card-grid">
        <div class="card">
            <h3>School Information</h3>
            <form method="POST">
                <div class="form-group">
                    <label for="school_name">School Name</label>
                    <input type="text" id="school_name" name="school_name" 
                           value="<?php echo isset($settings['school_name']) ? $settings['school_name'] : ''; ?>">
                </div>
                
                <div class="form-group">
                    <label for="school_address">Address</label>
                    <textarea id="school_address" name="school_address" rows="3"><?php echo isset($settings['school_address']) ? $settings['school_address'] : ''; ?></textarea>
                </div>
                
                <div class="form-group">
                    <label for="school_phone">Phone</label>
                    <input type="text" id="school_phone" name="school_phone" 
                           value="<?php echo isset($settings['school_phone']) ? $settings['school_phone'] : ''; ?>">
                </div>
                
                <div class="form-group">
                    <label for="school_email">Email</label>
                    <input type="email" id="school_email" name="school_email" 
                           value="<?php echo isset($settings['school_email']) ? $settings['school_email'] : ''; ?>">
                </div>
                
                <div class="form-group">
                    <label for="academic_year">Academic Year</label>
                    <input type="text" id="academic_year" name="academic_year" placeholder="e.g., 2023-2024" 
                           value="<?php echo isset($settings['academic_year']) ? $settings['academic_year'] : ''; ?>">
                </div>
                
                <div class="form-group">
                    <label for="term">Current Term</label>
                    <select id="term" name="term">
                        <option value="1st Term" <?php echo isset($settings['current_term']) && $settings['current_term'] == '1st Term' ? 'selected' : ''; ?>>1st Term</option>
                        <option value="2nd Term" <?php echo isset($settings['current_term']) && $settings['current_term'] == '2nd Term' ? 'selected' : ''; ?>>2nd Term</option>
                        <option value="3rd Term" <?php echo isset($settings['current_term']) && $settings['current_term'] == '3rd Term' ? 'selected' : ''; ?>>3rd Term</option>
                    </select>
                </div>
                
                <div class="form-group">
                    <button type="submit" name="update_settings" class="btn btn-primary">Save Settings</button>
                </div>
            </form>
        </div>
        
        <div class="card">
            <h3>Send Notification</h3>
            <form method="POST">
                <div class="form-group">
                    <label for="notification_title">Title</label>
                    <input type="text" id="notification_title" name="notification_title" required>
                </div>
                
                <div class="form-group">
                    <label for="notification_message">Message</label>
                    <textarea id="notification_message" name="notification_message" rows="4" required></textarea>
                </div>
                
                <div class="form-group">
                    <label for="recipient_type">Recipient Type</label>
                    <select id="recipient_type" name="recipient_type" required onchange="toggleRecipients()">
                        <option value="all">All Users</option>
                        <option value="specific">Specific Users</option>
                    </select>
                </div>
                
                <div class="form-group" id="specific_recipients_group" style="display: none;">
                    <label>Select Recipients</label>
                    <div style="max-height: 200px; overflow-y: auto; border: 1px solid #ddd; padding: 10px; border-radius: 5px;">
                        <?php foreach ($users as $user): ?>
                            <div>
                                <input type="checkbox" name="specific_recipients[]" value="<?php echo $user['id']; ?>" 
                                       id="user_<?php echo $user['id']; ?>">
                                <label for="user_<?php echo $user['id']; ?>">
                                    <?php echo $user['username']; ?> (<?php echo ucfirst($user['role']); ?>)
                                </label>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
                
                <div class="form-group">
                    <button type="submit" name="send_notification" class="btn btn-primary">Send Notification</button>
                </div>
            </form>
        </div>
    </div>
    
    <div class="table-container">
        <h2>Recent Notifications</h2>
        <table>
            <thead>
                <tr>
                    <th>Title</th>
                    <th>Message</th>
                    <th>Sender</th>
                    <th>Recipients</th>
                    <th>Date</th>
                </tr>
            </thead>
            <tbody>
                <?php if (count($notifications) > 0): ?>
                    <?php foreach ($notifications as $notification): ?>
                        <tr>
                            <td><?php echo $notification['title']; ?></td>
                            <td><?php echo substr($notification['message'], 0, 100); ?><?php echo strlen($notification['message']) > 100 ? '...' : ''; ?></td>
                            <td><?php echo $notification['sender_name']; ?></td>
                            <td>
                                <?php 
                                if ($notification['recipient_type'] == 'all') {
                                    echo 'All Users';
                                } else {
                                    $stmt = $pdo->prepare("SELECT COUNT(*) as count FROM notification_recipients WHERE notification_id = ?");
                                    $stmt->execute([$notification['id']]);
                                    $count = $stmt->fetch()['count'];
                                    echo $count . ' Specific Users';
                                }
                                ?>
                            </td>
                            <td><?php echo date('M j, Y g:i A', strtotime($notification['created_at'])); ?></td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="5">No notifications found.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</main>

<?php include '../includes/footer.php'; ?>

<script>
function toggleRecipients() {
    const recipientType = document.getElementById('recipient_type').value;
    const recipientsGroup = document.getElementById('specific_recipients_group');
    
    if (recipientType === 'specific') {
        recipientsGroup.style.display = 'block';
    } else {
        recipientsGroup.style.display = 'none';
    }
}

// Initialize on page load
document.addEventListener('DOMContentLoaded', function() {
    toggleRecipients();
});
</script>