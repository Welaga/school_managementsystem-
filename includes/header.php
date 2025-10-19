<?php
if (!isset($page_title)) {
    $page_title = 'School Management System';
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $page_title; ?></title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <link rel="stylesheet" href="../assets/css/dashboard.css">
</head>
<body>
    <header class="header">
        <h1>Greenwood Academy</h1>
        <div class="user-menu">
            <div class="user-info">
                Welcome, <span><?php echo $_SESSION['username']; ?></span> (<?php echo ucfirst($_SESSION['role']); ?>)
            </div>
            <a href="/school_managementsystem/logout.php" class="logout-btn">Logout</a>
        </div>
<div class="notifications-menu">
    <button class="notifications-btn" onclick="toggleNotifications()">
        🔔 Notifications
        <?php
        $unread_count = 0;
        if (isLoggedIn()) {
            $stmt = $pdo->prepare("SELECT COUNT(*) as count FROM notification_recipients nr 
                                  JOIN notifications n ON nr.notification_id = n.id 
                                  WHERE nr.user_id = ? AND nr.is_read = 0");
            $stmt->execute([$_SESSION['user_id']]);
            $unread_count = $stmt->fetch()['count'];
        }
        if ($unread_count > 0): ?>
            <span class="notification-badge"><?php echo $unread_count; ?></span>
        <?php endif; ?>
    </button>
    <div class="notifications-dropdown" id="notificationsDropdown">
        <h3>Notifications</h3>
        <div class="notifications-list">
            <?php
            if (isLoggedIn()) {
                $stmt = $pdo->prepare("SELECT n.*, nr.is_read 
                                      FROM notifications n 
                                      LEFT JOIN notification_recipients nr ON n.id = nr.notification_id AND nr.user_id = ?
                                      WHERE n.recipient_type = 'all' OR (n.recipient_type = 'specific' AND nr.user_id = ?)
                                      ORDER BY n.created_at DESC LIMIT 5");
                $stmt->execute([$_SESSION['user_id'], $_SESSION['user_id']]);
                $user_notifications = $stmt->fetchAll();
                
                if (count($user_notifications) > 0) {
                    foreach ($user_notifications as $notification) {
                        echo '<div class="notification-item ' . ($notification['is_read'] ? 'read' : 'unread') . '">';
                        echo '<h4>' . htmlspecialchars($notification['title']) . '</h4>';
                        echo '<p>' . substr(htmlspecialchars($notification['message']), 0, 100) . '...</p>';
                        echo '<small>' . date('M j, Y g:i A', strtotime($notification['created_at'])) . '</small>';
                        echo '</div>';
                    }
                } else {
                    echo '<p>No notifications</p>';
                }
            }
            ?>
        </div>
        <a href="notifications.php" class="view-all-btn">View All Notifications</a>
    </div>
</div>

<style>
.notifications-menu {
    position: relative;
    margin-right: 15px;
}

.notifications-btn {
    background: #f8f9fa;
    border: 1px solid #ddd;
    padding: 8px 15px;
    border-radius: 5px;
    cursor: pointer;
    display: flex;
    align-items: center;
    gap: 5px;
}

.notification-badge {
    background: #e74c3c;
    color: white;
    border-radius: 50%;
    width: 20px;
    height: 20px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 12px;
}

.notifications-dropdown {
    display: none;
    position: absolute;
    top: 100%;
    right: 0;
    width: 300px;
    background: white;
    border: 1px solid #ddd;
    border-radius: 5px;
    box-shadow: 0 2px 10px rgba(0,0,0,0.1);
    z-index: 1000;
}

.notifications-dropdown h3 {
    padding: 15px;
    margin: 0;
    border-bottom: 1px solid #eee;
    font-size: 16px;
}

.notifications-list {
    max-height: 300px;
    overflow-y: auto;
}

.notification-item {
    padding: 10px 15px;
    border-bottom: 1px solid #eee;
}

.notification-item.unread {
    background: #f8f9fa;
    font-weight: 500;
}

.notification-item h4 {
    margin: 0 0 5px 0;
    font-size: 14px;
}

.notification-item p {
    margin: 0 0 5px 0;
    font-size: 12px;
    color: #666;
}

.notification-item small {
    color: #999;
    font-size: 11px;
}

.view-all-btn {
    display: block;
    text-align: center;
    padding: 10px;
    background: #f8f9fa;
    color: #333;
    text-decoration: none;
    border-top: 1px solid #eee;
}

.view-all-btn:hover {
    background: #e9ecef;
}
</style>

<script>
function toggleNotifications() {
    const dropdown = document.getElementById('notificationsDropdown');
    dropdown.style.display = dropdown.style.display === 'block' ? 'none' : 'block';
}

// Close dropdown when clicking outside
document.addEventListener('click', function(event) {
    const dropdown = document.getElementById('notificationsDropdown');
    const btn = document.querySelector('.notifications-btn');
    
    if (!dropdown.contains(event.target) && !btn.contains(event.target)) {
        dropdown.style.display = 'none';
    }
});
</script>
    </header>