<?php
include 'includes/config.php';
require_login();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    
    if ($action === 'change_password') {
        // Change password logic
        $current_password = $_POST['current_password'] ?? '';
        $new_password = $_POST['new_password'] ?? '';
        $confirm_new_password = $_POST['confirm_new_password'] ?? '';
        
        // Validation
        if (strlen($new_password) < 8) {
            $_SESSION['error'] = "New password must be at least 8 characters";
            header("Location: change_password.php");
            exit();
        }
        
        if ($new_password !== $confirm_new_password) {
            $_SESSION['error'] = "New passwords do not match";
            header("Location: change_password.php");
            exit();
        }
        
        // Verify current password
        try {
            $stmt = $pdo->prepare("SELECT password FROM users WHERE id = ?");
            $stmt->execute([$_SESSION['user_id']]);
            $user = $stmt->fetch();
            
            if (!password_verify($current_password, $user['password'])) {
                $_SESSION['error'] = "Current password is incorrect";
                header("Location: change_password.php");
                exit();
            }
            
            // Update password
            $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
            $stmt = $pdo->prepare("UPDATE users SET password = ? WHERE id = ?");
            $stmt->execute([$hashed_password, $_SESSION['user_id']]);
            
            $_SESSION['success'] = "Password changed successfully";
            header("Location: change_password.php");
            exit();
            
        } catch (PDOException $e) {
            $_SESSION['error'] = "Error changing password: " . $e->getMessage();
            header("Location: change_password.php");
            exit();
        }
        
    } else {
        // Update profile information
        $full_name = clean_input($_POST['full_name'] ?? '', $pdo);
        $email = clean_input($_POST['email'] ?? '', $pdo);
        $address = clean_input($_POST['address'] ?? '', $pdo);
        $phone = clean_input($_POST['phone'] ?? '', $pdo);
        
        // Validate email
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $_SESSION['error'] = "Invalid email format";
            header("Location: profile.php");
            exit();
        }
        
        try {
            $stmt = $pdo->prepare("UPDATE users SET full_name = ?, email = ?, address = ?, phone = ? WHERE id = ?");
            $stmt->execute([$full_name, $email, $address, $phone, $_SESSION['user_id']]);
            
            // Update session
            $_SESSION['full_name'] = $full_name;
            $_SESSION['email'] = $email;
            
            $_SESSION['success'] = "Profile updated successfully";
            header("Location: profile.php");
            exit();
            
        } catch (PDOException $e) {
            $_SESSION['error'] = "Error updating profile: " . $e->getMessage();
            header("Location: profile.php");
            exit();
        }
    }
    
} elseif (isset($_GET['action']) && $_GET['action'] === 'delete') {
    // Delete account
    try {
        $pdo->beginTransaction();
        
        // Get user info
        $stmt = $pdo->prepare("SELECT profile_picture FROM users WHERE id = ?");
        $stmt->execute([$_SESSION['user_id']]);
        $profile_picture = $stmt->fetchColumn();
        
        // Delete old avatar if not default
        if ($profile_picture !== 'default-avatar.jpg' && file_exists('images/uploads/' . $profile_picture)) {
            unlink('images/uploads/' . $profile_picture);
        }
        
        // Delete user (cascade will handle related records)
        $stmt = $pdo->prepare("DELETE FROM users WHERE id = ?");
        $stmt->execute([$_SESSION['user_id']]);
        
        $pdo->commit();
        
        // Logout
        session_unset();
        session_destroy();
        
        $_SESSION['success'] = "Account deleted successfully";
        header("Location: index.php");
        exit();
        
    } catch (PDOException $e) {
        $pdo->rollBack();
        $_SESSION['error'] = "Error deleting account: " . $e->getMessage();
        header("Location: profile.php");
        exit();
    }
} else {
    header("Location: profile.php");
    exit();
}
?>