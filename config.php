<?php
session_start();

// Error reporting for development
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Database configuration
define('DB_HOST', 'localhost');
define('DB_NAME', 'school_management_system');
define('DB_USER', 'root');
define('DB_PASS', '');

// Create connection
try {
    $pdo = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME, DB_USER, DB_PASS);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch(PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}

// Function to check if user is logged in
function isLoggedIn() {
    return isset($_SESSION['user_id']);
}

// Function to check user role
function checkRole($allowedRoles) {
    if (!isLoggedIn() || !in_array($_SESSION['role'], (array)$allowedRoles)) {
        header("Location: ../login.php");
        exit();
    }
}

// Function to redirect based on role
function redirectByRole($role) {
    switch ($role) {
        case 'admin':
            header("Location: admin/");
            break;
            case 'registrar':
            header("Location: dashboard/");
            break;
        case 'registrar':
            header("Location: registrar/");
            break;
        case 'finance':
            header("Location: finance/");
            break;
        case 'teacher':
            header("Location: teacher/");
            break;
        case 'student':
            header("Location: student/");
            break;
        case 'cleaner':
            header("Location: cleaner/");
            break;
        case 'transport':
            header("Location: transport/");
            break;
        default:
            header("Location: login.php");
    }
    exit();
}

// Function to get user details
function getUserDetails($user_id) {
    global $pdo;
    $stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
    $stmt->execute([$user_id]);
    return $stmt->fetch();
}

// Function to get student details
function getStudentDetails($user_id) {
    global $pdo;
    $stmt = $pdo->prepare("SELECT s.*, c.name as class_name FROM students s 
                          LEFT JOIN classes c ON s.class_id = c.id 
                          WHERE s.user_id = ?");
    $stmt->execute([$user_id]);
    return $stmt->fetch();
}

// Function to get teacher details
function getTeacherDetails($user_id) {
    global $pdo;
    $stmt = $pdo->prepare("SELECT * FROM teachers WHERE user_id = ?");
    $stmt->execute([$user_id]);
    return $stmt->fetch();
}
// Function to get class name by ID
function getClassName($class_id) {
    global $pdo;
    if (!$class_id) return 'Not assigned';
    
    $stmt = $pdo->prepare("SELECT name FROM classes WHERE id = ?");
    $stmt->execute([$class_id]);
    $class = $stmt->fetch();
    
    return $class ? $class['name'] : 'Not assigned';
}

// Function to check if username exists
function usernameExists($username) {
    global $pdo;
    $stmt = $pdo->prepare("SELECT id FROM users WHERE username = ?");
    $stmt->execute([$username]);
    return $stmt->fetch() !== false;
}

// Function to validate email format
function isValidEmail($email) {
    return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
}
// Add to config.php
function handleLogout() {
    if (isset($_GET['logout'])) {
        session_start();
        session_unset();
        session_destroy();
        header("Location: login.php");
        exit();
    }
}

// Call this function at the top of every protected page
handleLogout();

?>
