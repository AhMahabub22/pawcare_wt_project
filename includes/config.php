<?php
session_start();

// Database configuration
define('DB_HOST', 'localhost');
define('DB_NAME', 'pawcare');
define('DB_USER', 'root');
define('DB_PASS', '');

// Base URL for absolute paths
$protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http";
$host = $_SERVER['HTTP_HOST'];
$script_path = dirname($_SERVER['SCRIPT_NAME']);

$base_url = $protocol . '://' . $host;
if ($script_path != '/') {
    $base_url .= $script_path;
}
$base_url .= '/';

define('BASE_URL', $base_url);

// Root path for file operations
define('ROOT_PATH', dirname(__DIR__) . DIRECTORY_SEPARATOR);

// File upload configuration
define('UPLOAD_DIR', 'uploads/');
define('UPLOAD_PATH', ROOT_PATH . 'images' . DIRECTORY_SEPARATOR . UPLOAD_DIR);
define('MAX_FILE_SIZE', 2 * 1024 * 1024); // 2MB
define('ALLOWED_TYPES', ['image/jpeg', 'image/png', 'image/gif']);

// Create upload directory if it doesn't exist
if (!is_dir(UPLOAD_PATH)) {
    mkdir(UPLOAD_PATH, 0777, true);
}

// Create database connection using PDO
try {
    $pdo = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME, DB_USER, DB_PASS);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
} catch(PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}

// Create MySQLi connection for specific operations
$mysqli = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
if ($mysqli->connect_error) {
    die("Connection failed: " . $mysqli->connect_error);
}

// Function to prevent SQL injection
function clean_input($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}

// Check if user is logged in
function is_logged_in() {
    return isset($_SESSION['user_id']);
}

// Check user type
function get_user_type() {
    return $_SESSION['user_type'] ?? null;
}

// Redirect if not logged in
function require_login() {
    if (!is_logged_in()) {
        header("Location: " . BASE_URL . "login.php");
        exit();
    }
}

// Get avatar URL
function get_avatar_url($filename) {
    if (empty($filename) || $filename == 'default-avatar.jpg') {
        return BASE_URL . 'images/default-avatar.jpg';
    }
    $file_path = UPLOAD_PATH . $filename;
    if (file_exists($file_path)) {
        return BASE_URL . 'images/' . UPLOAD_DIR . $filename;
    }
    return BASE_URL . 'images/default-avatar.jpg';
}

// Set CSRF token
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

// Function to resize image
function resize_image($file_path, $max_width, $max_height) {
    $image_info = getimagesize($file_path);
    if (!$image_info) return false;
    
    $mime = $image_info['mime'];
    
    switch($mime) {
        case 'image/jpeg':
            $image = imagecreatefromjpeg($file_path);
            break;
        case 'image/png':
            $image = imagecreatefrompng($file_path);
            break;
        case 'image/gif':
            $image = imagecreatefromgif($file_path);
            break;
        default:
            return false;
    }
    
    $width = imagesx($image);
    $height = imagesy($image);
    
    // Calculate new dimensions
    $ratio = $width / $height;
    if ($width > $max_width || $height > $max_height) {
        if ($ratio > 1) {
            $new_width = $max_width;
            $new_height = $max_width / $ratio;
        } else {
            $new_height = $max_height;
            $new_width = $max_height * $ratio;
        }
        
        $new_image = imagecreatetruecolor($new_width, $new_height);
        
        // Preserve transparency for PNG and GIF
        if ($mime == 'image/png' || $mime == 'image/gif') {
            imagecolortransparent($new_image, imagecolorallocatealpha($new_image, 0, 0, 0, 127));
            imagealphablending($new_image, false);
            imagesavealpha($new_image, true);
        }
        
        imagecopyresampled($new_image, $image, 0, 0, 0, 0, $new_width, $new_height, $width, $height);
        
        switch($mime) {
            case 'image/jpeg':
                imagejpeg($new_image, $file_path, 90);
                break;
            case 'image/png':
                imagepng($new_image, $file_path, 9);
                break;
            case 'image/gif':
                imagegif($new_image, $file_path);
                break;
        }
        
        imagedestroy($image);
        imagedestroy($new_image);
    }
    
    return true;
}
?>