<?php
include 'includes/config.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: register.php");
    exit();
}

// CSRF protection
if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
    $_SESSION['error'] = "Invalid security token";
    header("Location: register.php");
    exit();
}

// Get and validate input
$username = clean_input($_POST['username'] ?? '', $pdo);
$email = clean_input($_POST['email'] ?? '', $pdo);
$password = $_POST['password'] ?? '';
$confirm_password = $_POST['confirm_password'] ?? '';
$full_name = clean_input($_POST['full_name'] ?? '', $pdo);
$user_type = clean_input($_POST['user_type'] ?? '', $pdo);
$address = clean_input($_POST['address'] ?? '', $pdo);
$phone = clean_input($_POST['phone'] ?? '', $pdo);

// Validation
$errors = [];

// Username validation
if (strlen($username) < 3 || strlen($username) > 50) {
    $errors[] = "Username must be between 3 and 50 characters";
}

// Email validation
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $errors[] = "Invalid email format";
}

// Password validation
if (strlen($password) < 8) {
    $errors[] = "Password must be at least 8 characters";
}
if ($password !== $confirm_password) {
    $errors[] = "Passwords do not match";
}

// User type validation
if (!in_array($user_type, ['buyer', 'seller'])) {
    $errors[] = "Invalid user type";
}

// Check if username or email already exists
try {
    $stmt = $pdo->prepare("SELECT id FROM users WHERE username = ? OR email = ?");
    $stmt->execute([$username, $email]);
    if ($stmt->rowCount() > 0) {
        $errors[] = "Username or email already exists";
    }
} catch (PDOException $e) {
    $errors[] = "Database error: " . $e->getMessage();
}

if (!empty($errors)) {
    $_SESSION['error'] = implode("<br>", $errors);
    header("Location: register.php");
    exit();
}

// Hash password
$hashed_password = password_hash($password, PASSWORD_DEFAULT);

// Insert user into database
try {
    $stmt = $pdo->prepare("INSERT INTO users (username, email, password, user_type, full_name, address, phone) 
                          VALUES (?, ?, ?, ?, ?, ?, ?)");
    $stmt->execute([$username, $email, $hashed_password, $user_type, $full_name, $address, $phone]);
    
    $_SESSION['success'] = "Registration successful! Please login.";
    header("Location: login.php");
    exit();
} catch (PDOException $e) {
    $_SESSION['error'] = "Registration failed: " . $e->getMessage();
    header("Location: register.php");
    exit();
}
?>