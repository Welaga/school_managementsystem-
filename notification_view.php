<?php
require_once 'config.php';
if (session_status()===PHP_SESSION_NONE) session_start();
if (empty($_SESSION['user_id'])) exit("Not logged in.");

$user_id=(int)$_SESSION['user_id'];
$id=(int)($_GET['id']??0);

$stmt=$pdo->prepare("
  SELECT n.*, u.username as sender_name, nr.is_read, nr.read_at
  FROM notifications n
  LEFT JOIN users u ON n.sender_id=u.id
  LEFT JOIN notification_recipients nr ON n.id=nr.notification_id AND nr.user_id=?
  WHERE n.id=? AND (n.recipient_type='all' OR nr.user_id=?)
  LIMIT 1
");
$stmt->execute([$user_id,$id,$user_id]);
$notif=$stmt->fetch(PDO::FETCH_ASSOC);

if(!$notif){ echo "<p>Notification not found.</p>"; exit; }

// Mark as read if not already
if((int)$notif['is_read']===0){
    $up=$pdo->prepare("UPDATE notification_recipients SET is_read=1, read_at=NOW() WHERE notification_id=? AND user_id=?");
    $up->execute([$id,$user_id]);
    if($up->rowCount()===0){
        $ins=$pdo->prepare("INSERT INTO notification_recipients (notification_id,user_id,is_read,read_at,created_at)
                            VALUES (?,?,1,NOW(),NOW())");
        $ins->execute([$id,$user_id]);
    }
}
?>
<h2><?=htmlspecialchars($notif['title'])?></h2>
<p><em>From: <?=htmlspecialchars($notif['sender_name']??'System')?> | 
    <?=date('M j, Y g:i A',strtotime($notif['created_at']))?></em></p>
<hr>
<p><?=nl2br(htmlspecialchars($notif['message']))?></p>
