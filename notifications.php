<?php
// notifications.php
require_once 'config.php';
if (session_status() === PHP_SESSION_NONE) session_start();
if (empty($_SESSION['user_id'])) { header("Location: login.php"); exit; }

checkRole(['admin','registrar','finance','teacher','student','cleaner','transport']);

$user_id = (int)$_SESSION['user_id'];
$message = "";

/* ---------- Handle mark all as read ---------- */
if (isset($_POST['mark_all_read'])) {
    try {
        $pdo->beginTransaction();

        $update = $pdo->prepare("UPDATE notification_recipients SET is_read=1, read_at=NOW() WHERE user_id=? AND is_read=0");
        $update->execute([$user_id]);

        $insert = $pdo->prepare("
            INSERT INTO notification_recipients (notification_id, user_id, is_read, read_at, created_at)
            SELECT n.id, ?, 1, NOW(), NOW()
            FROM notifications n
            LEFT JOIN notification_recipients nr ON n.id=nr.notification_id AND nr.user_id=?
            WHERE nr.id IS NULL AND n.recipient_type='all'
        ");
        $insert->execute([$user_id,$user_id]);

        $pdo->commit();
        $message = "All notifications marked as read!";
    } catch (Exception $e) {
        if ($pdo->inTransaction()) $pdo->rollBack();
        $message = "Error: ".$e->getMessage();
    }
}

/* ---------- Filtering & Pagination ---------- */
$filter = $_GET['filter'] ?? 'all'; // all|unread|read
$per_page = 5;
$page = max(1, (int)($_GET['page'] ?? 1));
$offset = ($page-1)*$per_page;

$whereFilter = "";
if ($filter==='unread') $whereFilter = "AND COALESCE(nr.is_read,0)=0";
elseif ($filter==='read') $whereFilter = "AND COALESCE(nr.is_read,0)=1";

/* ---------- Get notifications ---------- */
$sqlBase = "
    FROM notifications n
    LEFT JOIN notification_recipients nr ON n.id=nr.notification_id AND nr.user_id=?
    LEFT JOIN users u ON n.sender_id=u.id
    WHERE n.recipient_type='all' OR (n.recipient_type='specific' AND nr.user_id=?)
    $whereFilter
";

$countStmt = $pdo->prepare("SELECT COUNT(*) $sqlBase");
$countStmt->execute([$user_id,$user_id]);
$total = (int)$countStmt->fetchColumn();

$stmt = $pdo->prepare("
    SELECT n.*, COALESCE(nr.is_read,0) as is_read, nr.read_at, u.username as sender_name
    $sqlBase
    ORDER BY n.created_at DESC
    LIMIT $per_page OFFSET $offset
");
$stmt->execute([$user_id,$user_id]);
$notifications = $stmt->fetchAll(PDO::FETCH_ASSOC);

$unread_count = 0;
foreach ($notifications as $n) if ((int)$n['is_read']===0) $unread_count++;

$total_pages = ceil($total/$per_page);
?>
<?php include 'includes/header.php'; ?>
<?php include 'includes/sidebar.php'; ?>

<main class="main-content">
    <div class="content-header">
        <h1>Notifications</h1>
        <div class="header-actions">
            <form method="get" style="display:inline;">
                <select name="filter" onchange="this.form.submit()" class="btn btn-secondary">
                    <option value="all"   <?=($filter==='all'?'selected':'')?>>All</option>
                    <option value="unread"<?=($filter==='unread'?'selected':'')?>>Unread</option>
                    <option value="read"  <?=($filter==='read'?'selected':'')?>>Read</option>
                </select>
            </form>
            <?php if ($unread_count>0): ?>
                <form method="post" style="display:inline;">
                    <button type="submit" name="mark_all_read" class="btn btn-primary">Mark All as Read</button>
                </form>
            <?php endif; ?>
        </div>
    </div>

    <?php if ($message): ?>
        <div class="alert <?=strpos($message,'Error')!==false?'alert-error':'alert-success'?>">
            <?=htmlspecialchars($message)?>
        </div>
    <?php endif; ?>

    <div class="notifications-container">
        <?php if ($total>0): ?>
            <?php foreach ($notifications as $n): ?>
                <div class="notification-card <?=($n['is_read']?'read':'unread')?>" 
                     data-id="<?=$n['id']?>">
                    <div class="notification-header">
                        <h3><?=htmlspecialchars($n['title'])?></h3>
                        <span class="notification-date"><?=date('M j, Y g:i A', strtotime($n['created_at']))?></span>
                    </div>
                    <div class="notification-body">
                        <p><?=nl2br(htmlspecialchars(mb_substr($n['message'],0,150)))?>...</p>
                    </div>
                    <div class="notification-footer">
                        <span>From: <?=htmlspecialchars($n['sender_name']??'System')?></span>
                        <?php if ($n['is_read']): ?>
                            <span class="read-status">Read</span>
                        <?php else: ?>
                            <span class="unread-status">Unread</span>
                        <?php endif; ?>
                        <button class="btn btn-link view-btn">View</button>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <div class="alert alert-info">No notifications found.</div>
        <?php endif; ?>
    </div>

    <!-- Pagination -->
    <?php if ($total_pages>1): ?>
        <div class="pagination">
            <?php for($i=1;$i<=$total_pages;$i++): ?>
                <a class="page-link <?=($i==$page?'active':'')?>"
                   href="?page=<?=$i?>&filter=<?=$filter?>"><?=$i?></a>
            <?php endfor; ?>
        </div>
    <?php endif; ?>
</main>

<?php include 'includes/footer.php'; ?>

<!-- Modal -->
<div id="notifModal" class="modal" style="display:none;">
  <div class="modal-content">
    <span class="close">&times;</span>
    <div id="notifDetails"></div>
  </div>
</div>

<style>
.notifications-container {display:flex;flex-direction:column;gap:1rem;margin-top:1rem;}
.notification-card {background:#fff;border-radius:10px;padding:1rem;box-shadow:0 2px 8px rgba(0,0,0,.1);}
.notification-card.unread {border-left:4px solid #667eea;background:#f8f9ff;}
.notification-header {display:flex;justify-content:space-between;}
.notification-footer {display:flex;justify-content:space-between;align-items:center;margin-top:.5rem;}
.btn {padding:.35rem .6rem;border-radius:6px;cursor:pointer;}
.btn-primary{background:#667eea;color:#fff;}
.btn-secondary{background:#6c757d;color:#fff;}
.btn-link{background:none;color:#007bff;text-decoration:underline;cursor:pointer;}
.read-status{color:#28a745;}
.unread-status{color:#e90b0b;}
.pagination{margin-top:1rem;}
.page-link{margin:0 .2rem;padding:.3rem .6rem;border:1px solid #ccc;border-radius:4px;text-decoration:none;}
.page-link.active{background:#667eea;color:#fff;}
.modal{position:fixed;top:0;left:0;width:100%;height:100%;background:rgba(0,0,0,.5);display:flex;align-items:center;justify-content:center;}
.modal-content{background:#fff;padding:1rem;border-radius:8px;max-width:600px;width:90%;}
.close{float:right;font-size:20px;cursor:pointer;}
</style>

<script>
// Modal logic
const modal=document.getElementById('notifModal');
const details=document.getElementById('notifDetails');
document.querySelectorAll('.view-btn').forEach(btn=>{
  btn.addEventListener('click',function(e){
    const id=this.closest('.notification-card').dataset.id;
    fetch('notification_view.php?id='+id)
      .then(r=>r.text())
      .then(html=>{
        details.innerHTML=html;
        modal.style.display='flex';
      });
  });
});
document.querySelector('.close').onclick=()=>modal.style.display='none';
window.onclick=e=>{if(e.target===modal) modal.style.display='none';}
</script>
