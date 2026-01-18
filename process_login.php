<?php
include 'includes/config.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: login.php");
    exit();
}

// CSRF protection
if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
    $_SESSION['error'] = "Invalid security token";
    header("Location: login.php");
    exit();
}

$username = clean_input($_POST['username'] ?? '', $pdo);
$password = $_POST['password'] ?? '';
$remember = isset($_POST['remember']) ? true : false;

try {
    // Find user by username or email
    $stmt = $pdo->prepare("SELECT * FROM users WHERE username = ? OR email = ?");
    $stmt->execute([$username, $username]);
    $user = $stmt->fetch();
    
    if ($user && password_verify($password, $user['password'])) {
        // Set session variables
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['username'] = $user['username'];
        $_SESSION['user_type'] = $user['user_type'];
        $_SESSION['full_name'] = $user['full_name'];
        $_SESSION['profile_picture'] = $user['profile_picture'];
        $_SESSION['email'] = $user['email'];
        
        // Update last login
        $stmt = $pdo->prepare("UPDATE users SET updated_at = NOW() WHERE id = ?");
        $stmt->execute([$user['id']]);
        
        // Set cookie if remember me is checked (30 days)
        if ($remember) {
            $token = bin2hex(random_bytes(32));
            setcookie('remember_token', $token, time() + (86400 * 30), "/");
            
            // Store token in database
            $stmt = $pdo->prepare("UPDATE users SET remember_token = ? WHERE id = ?");
            $stmt->execute([$token, $user['id']]);
        }
        
        // Log session
        $session_id = session_id();
        $ip_address = $_SERVER['REMOTE_ADDR'];
        $user_agent = $_SERVER['HTTP_USER_AGENT'];
        
        $stmt = $pdo->prepare("INSERT INTO user_sessions (session_id, user_id, ip_address, user_agent) 
                              VALUES (?, ?, ?, ?)");
        $stmt->execute([$session_id, $user['id'], $ip_address, $user_agent]);
        
        // Redirect based on user type
        if ($user['user_type'] == 'buyer') {
            header("Location: dashboard.php");
        } else {
            header("Location: dashboard.php");
        }
        exit();
    } else {
        $_SESSION['error'] = "Invalid username/email or password";
        header("Location: login.php");
        exit();
    }
} catch (PDOException $e) {
    $_SESSION['error'] = "Login failed: " . $e->getMessage();
    header("Location: login.php");
    exit();
}
?>