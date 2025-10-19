<?php
require_once '../config.php';
checkRole(['teacher']);

if (isset($_GET['file'])) {
    $file_path = $_GET['file'];
    
    // Security: Validate that the file is in the submissions directory
    $allowed_base_dir = "uploads/submissions/";
    if (strpos($file_path, $allowed_base_dir) !== 0) {
        die("Invalid file path.");
    }
    
    // Get the full server path
    $full_path = __DIR__ . '/../../' . $file_path;
    
    // Check if file exists
    if (file_exists($full_path)) {
        // Set headers for download
        header('Content-Description: File Transfer');
        header('Content-Type: application/octet-stream');
        header('Content-Disposition: attachment; filename="' . basename($file_path) . '"');
        header('Expires: 0');
        header('Cache-Control: must-revalidate');
        header('Pragma: public');
        header('Content-Length: ' . filesize($full_path));
        flush(); // Flush system output buffer
        readfile($full_path);
        exit;
    } else {
        http_response_code(404);
        die("File not found: " . basename($file_path));
    }
} else {
    die("No file specified.");
}
?>