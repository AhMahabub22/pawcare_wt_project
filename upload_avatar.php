<?php
include 'includes/config.php';
require_login();

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'error' => 'Invalid request method']);
    exit();
}

// CSRF protection
if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
    echo json_encode(['success' => false, 'error' => 'Invalid security token']);
    exit();
}

if (!isset($_FILES['avatar']) || $_FILES['avatar']['error'] !== UPLOAD_ERR_OK) {
    echo json_encode(['success' => false, 'error' => 'No file uploaded']);
    exit();
}

$file = $_FILES['avatar'];
$user_id = $_SESSION['user_id'];

// Validate file
$allowed_types = ['image/jpeg', 'image/png', 'image/gif'];
$max_size = 2 * 1024 * 1024; // 2MB

if (!in_array($file['type'], $allowed_types)) {
    echo json_encode(['success' => false, 'error' => 'Invalid file type. Only JPG, PNG, and GIF are allowed.']);
    exit();
}

if ($file['size'] > $max_size) {
    echo json_encode(['success' => false, 'error' => 'File size exceeds 2MB limit']);
    exit();
}

// Generate unique filename
$extension = pathinfo($file['name'], PATHINFO_EXTENSION);
$filename = 'avatar_' . $user_id . '_' . time() . '.' . $extension;
$upload_path = UPLOAD_DIR . $filename;

// Create upload directory if it doesn't exist
if (!is_dir(UPLOAD_DIR)) {
    mkdir(UPLOAD_DIR, 0777, true);
}

// Move uploaded file
if (move_uploaded_file($file['tmp_name'], $upload_path)) {
    // Update database
    try {
        // Delete old avatar if not default
        $stmt = $pdo->prepare("SELECT profile_picture FROM users WHERE id = ?");
        $stmt->execute([$user_id]);
        $old_avatar = $stmt->fetchColumn();
        
        if ($old_avatar !== 'default-avatar.jpg' && file_exists('images/uploads/' . $old_avatar)) {
            unlink('images/uploads/' . $old_avatar);
        }
        
        // Update with new avatar
        $stmt = $pdo->prepare("UPDATE users SET profile_picture = ? WHERE id = ?");
        $stmt->execute([$filename, $user_id]);
        
        // Update session
        $_SESSION['profile_picture'] = $filename;
        
        echo json_encode([
            'success' => true,
            'avatar_url' => 'images/uploads/' . $filename
        ]);
    } catch (PDOException $e) {
        unlink($upload_path); // Delete uploaded file on error
        echo json_encode(['success' => false, 'error' => 'Database error: ' . $e->getMessage()]);
    }
} else {
    echo json_encode(['success' => false, 'error' => 'Failed to upload file']);
}
?>