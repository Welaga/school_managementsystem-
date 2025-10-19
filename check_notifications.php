<?php
require_once 'config.php';
session_start();

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['unread_count' => 0]);
    exit;
}

try {
    $stmt = $pdo->prepare("SELECT COUNT(*) as unread_count 
                          FROM notification_recipients 
                          WHERE user_id = ? AND is_read = 0");
    $stmt->execute([$_SESSION['user_id']]);
    $result = $stmt->fetch();
    
    echo json_encode(['unread_count' => $result['unread_count']]);
} catch (PDOException $e) {
    echo json_encode(['unread_count' => 0]);
}
?>