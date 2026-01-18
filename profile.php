<?php
$page_title = "Profile";
include 'includes/config.php';
require_login();

$user_id = $_SESSION['user_id'];

// Get user data
try {
    $stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
    $stmt->execute([$user_id]);
    $user = $stmt->fetch();
    
    if (!$user) {
        header("Location: logout.php");
        exit();
    }
} catch (PDOException $e) {
    $_SESSION['error'] = "Error loading profile: " . $e->getMessage();
}
?>
<?php include 'includes/header.php'; ?>
<link rel="stylesheet" href="css/profile.css">

<div class="profile-container">
    <?php if (isset($_SESSION['success'])): ?>
        <div class="alert alert-success"><?php echo $_SESSION['success']; unset($_SESSION['success']); ?></div>
    <?php endif; ?>
    
    <?php if (isset($_SESSION['error'])): ?>
        <div class="alert alert-error"><?php echo $_SESSION['error']; unset($_SESSION['error']); ?></div>
    <?php endif; ?>
    
    <div class="profile-header">
        <div class="profile-avatar">
            <img id="avatarPreview" src="images/uploads/<?php echo $user['profile_picture']; ?>" 
                 alt="Profile Picture" class="avatar-preview">
            <div class="avatar-upload">
                <i class="fas fa-camera"></i>
                <input type="file" id="avatarUpload" accept="image/*">
            </div>
        </div>
        <div class="profile-info">
            <h1><?php echo htmlspecialchars($user['full_name']); ?></h1>
            <span class="user-type"><?php echo ucfirst($user['user_type']); ?></span>
            <p><i class="fas fa-user"></i> @<?php echo htmlspecialchars($user['username']); ?></p>
            <p><i class="fas fa-envelope"></i> <?php echo htmlspecialchars($user['email']); ?></p>
            <p><i class="fas fa-calendar"></i> Member since: <?php echo date('F j, Y', strtotime($user['created_at'])); ?></p>
        </div>
    </div>
    
    <div class="profile-details">
        <div class="profile-section">
            <h2>Personal Information</h2>
            <form action="process_profile.php" method="POST">
                <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
                
                <div class="form-group">
                    <label for="edit_full_name">Full Name:</label>
                    <input type="text" id="edit_full_name" name="full_name" 
                           value="<?php echo htmlspecialchars($user['full_name']); ?>" 
                           class="form-control" required>
                </div>
                
                <div class="form-group">
                    <label for="edit_email">Email:</label>
                    <input type="email" id="edit_email" name="email" 
                           value="<?php echo htmlspecialchars($user['email']); ?>" 
                           class="form-control" required>
                </div>
                
                <div class="form-group">
                    <label for="edit_address">Address:</label>
                    <textarea id="edit_address" name="address" class="form-control" rows="3"><?php echo htmlspecialchars($user['address']); ?></textarea>
                </div>
                
                <div class="form-group">
                    <label for="edit_phone">Phone:</label>
                    <input type="tel" id="edit_phone" name="phone" 
                           value="<?php echo htmlspecialchars($user['phone']); ?>" 
                           class="form-control">
                </div>
                
                <button type="submit" class="btn">Update Profile</button>
            </form>
        </div>
        
        <div class="profile-section">
            <h2>Account Information</h2>
            <div class="info-group">
                <label>Username:</label>
                <div class="value"><?php echo htmlspecialchars($user['username']); ?></div>
            </div>
            
            <div class="info-group">
                <label>Account Type:</label>
                <div class="value"><?php echo ucfirst($user['user_type']); ?></div>
            </div>
            
            <div class="info-group">
                <label>Member Since:</label>
                <div class="value"><?php echo date('F j, Y, g:i a', strtotime($user['created_at'])); ?></div>
            </div>
            
            <div class="info-group">
                <label>Last Updated:</label>
                <div class="value"><?php echo date('F j, Y, g:i a', strtotime($user['updated_at'])); ?></div>
            </div>
            
            <div style="margin-top: 20px;">
                <a href="change_password.php" class="btn">Change Password</a>
                <button onclick="confirmDelete()" class="btn btn-secondary" style="background-color: #dc3545;">Delete Account</button>
            </div>
        </div>
    </div>
</div>

<script>
// AJAX for avatar upload
document.getElementById('avatarUpload').addEventListener('change', function(e) {
    var file = e.target.files[0];
    if (!file) return;
    
    // Validate file type
    var validTypes = ['image/jpeg', 'image/png', 'image/gif'];
    if (!validTypes.includes(file.type)) {
        alert('Please upload a valid image file (JPEG, PNG, GIF)');
        return;
    }
    
    // Validate file size (2MB)
    if (file.size > 2 * 1024 * 1024) {
        alert('File size must be less than 2MB');
        return;
    }
    
    var formData = new FormData();
    formData.append('avatar', file);
    formData.append('csrf_token', '<?php echo $_SESSION['csrf_token']; ?>');
    
    // Show loading
    var preview = document.getElementById('avatarPreview');
    preview.style.opacity = '0.5';
    
    var xhr = new XMLHttpRequest();
    xhr.open('POST', 'upload_avatar.php', true);
    
    xhr.onload = function() {
        preview.style.opacity = '1';
        
        try {
            var response = JSON.parse(xhr.responseText);
            if (response.success) {
                preview.src = response.avatar_url + '?' + new Date().getTime(); // Cache bust
                alert('Profile picture updated successfully!');
            } else {
                alert('Error: ' + response.error);
            }
        } catch (e) {
            alert('Error uploading image');
        }
    };
    
    xhr.onerror = function() {
        preview.style.opacity = '1';
        alert('Network error occurred');
    };
    
    xhr.send(formData);
});

function confirmDelete() {
    if (confirm('Are you sure you want to delete your account? This action cannot be undone.')) {
        window.location.href = 'process_profile.php?action=delete';
    }
}
</script>

<?php include 'includes/footer.php'; ?>