<?php
$page_title = "My Profile";
$page_css = ['profile.css'];
include 'includes/config.php';
require_login();

$user_id = $_SESSION['user_id'];

// Get user data
try {
    $stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
    $stmt->execute([$user_id]);
    $user = $stmt->fetch();
    
    if (!$user) {
        header("Location: " . BASE_URL . "logout.php");
        exit();
    }
} catch (PDOException $e) {
    $_SESSION['error'] = "Error loading profile: " . $e->getMessage();
}

// Get avatar URL
$avatar_url = get_avatar_url($user['profile_picture']);
?>
<?php include 'includes/header.php'; ?>

<div class="profile-container">
    <?php if (isset($_SESSION['success'])): ?>
        <div class="alert alert-success">
            <i class="fas fa-check-circle" aria-hidden="true"></i>
            <?php echo $_SESSION['success']; unset($_SESSION['success']); ?>
        </div>
    <?php endif; ?>
    
    <?php if (isset($_SESSION['error'])): ?>
        <div class="alert alert-error">
            <i class="fas fa-exclamation-circle" aria-hidden="true"></i>
            <?php echo $_SESSION['error']; unset($_SESSION['error']); ?>
        </div>
    <?php endif; ?>
    
    <div class="profile-header">
        <div class="profile-avatar">
            <div class="avatar-wrapper">
                <img id="avatarPreview" src="<?php echo $avatar_url; ?>" 
                     alt="Profile picture of <?php echo htmlspecialchars($user['full_name']); ?>" 
                     class="avatar-preview">
                <div class="avatar-upload" title="Upload new profile picture">
                    <i class="fas fa-camera" aria-hidden="true"></i>
                    <span class="sr-only">Upload profile picture</span>
                    <input type="file" id="avatarUpload" accept="image/*" 
                           aria-label="Choose profile picture" 
                           title="Select an image file to upload">
                </div>
            </div>
        </div>
        <div class="profile-info">
            <div class="profile-title">
                <h1><?php echo htmlspecialchars($user['full_name']); ?></h1>
                <span class="user-type"><?php echo ucfirst($user['user_type']); ?></span>
            </div>
            
            <div class="profile-meta">
                <div class="meta-item">
                    <i class="fas fa-user" aria-hidden="true"></i>
                    <span>@<?php echo htmlspecialchars($user['username']); ?></span>
                </div>
                <div class="meta-item">
                    <i class="fas fa-envelope" aria-hidden="true"></i>
                    <span><?php echo htmlspecialchars($user['email']); ?></span>
                </div>
                <div class="meta-item">
                    <i class="fas fa-calendar-alt" aria-hidden="true"></i>
                    <span>Joined: <?php echo date('F j, Y', strtotime($user['created_at'])); ?></span>
                </div>
            </div>
        </div>
    </div>
    
    <div class="profile-sections">
        <div class="profile-section">
            <div class="section-header">
                <i class="fas fa-user-edit" aria-hidden="true"></i>
                <h2>Personal Information</h2>
            </div>
            
            <form action="<?php echo BASE_URL; ?>process_profile.php" method="POST">
                <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
                
                <div class="form-group">
                    <label for="edit_full_name">Full Name</label>
                    <input type="text" id="edit_full_name" name="full_name" 
                           value="<?php echo htmlspecialchars($user['full_name']); ?>" 
                           class="form-control" required
                           placeholder="Enter your full name"
                           aria-required="true"
                           aria-describedby="nameHelp">
                    <small id="nameHelp" class="form-text">Your full name as it should appear</small>
                </div>
                
                <div class="form-group">
                    <label for="edit_email">Email Address</label>
                    <input type="email" id="edit_email" name="email" 
                           value="<?php echo htmlspecialchars($user['email']); ?>" 
                           class="form-control" required
                           placeholder="your.email@example.com"
                           aria-required="true"
                           aria-describedby="emailHelp">
                    <small id="emailHelp" class="form-text">We'll never share your email with anyone else</small>
                </div>
                
                <div class="form-group">
                    <label for="edit_address">Address</label>
                    <textarea id="edit_address" name="address" class="form-control" rows="3"
                              placeholder="Enter your address"
                              aria-describedby="addressHelp"><?php echo htmlspecialchars($user['address']); ?></textarea>
                    <small id="addressHelp" class="form-text">Your address will be used for shipping</small>
                </div>
                
                <div class="form-group">
                    <label for="edit_phone">Phone Number</label>
                    <input type="tel" id="edit_phone" name="phone" 
                           value="<?php echo htmlspecialchars($user['phone']); ?>" 
                           class="form-control"
                           placeholder="(123) 456-7890"
                           aria-describedby="phoneHelp">
                    <small id="phoneHelp" class="form-text">Optional. For order updates</small>
                </div>
                
                <button type="submit" class="btn btn-primary" title="Save profile changes">
                    <i class="fas fa-save" aria-hidden="true"></i>
                    <span>Update Profile</span>
                </button>
            </form>
        </div>
        
        <div class="profile-section">
            <div class="section-header">
                <i class="fas fa-info-circle" aria-hidden="true"></i>
                <h2>Account Information</h2>
            </div>
            
            <div class="info-group">
                <div class="info-label">Username</div>
                <div class="info-value"><?php echo htmlspecialchars($user['username']); ?></div>
            </div>
            
            <div class="info-group">
                <div class="info-label">Account Type</div>
                <div class="info-value"><?php echo ucfirst($user['user_type']); ?></div>
            </div>
            
            <div class="info-group">
                <div class="info-label">Member Since</div>
                <div class="info-value"><?php echo date('F j, Y, g:i a', strtotime($user['created_at'])); ?></div>
            </div>
            
            <div class="info-group">
                <div class="info-label">Last Updated</div>
                <div class="info-value"><?php echo date('F j, Y, g:i a', strtotime($user['updated_at'])); ?></div>
            </div>
            
            <div class="profile-actions">
                <a href="<?php echo BASE_URL; ?>change_password.php" class="btn btn-secondary" title="Change your password">
                    <i class="fas fa-key" aria-hidden="true"></i>
                    <span>Change Password</span>
                </a>
                <button onclick="confirmDelete()" class="btn btn-danger" 
                        title="Permanently delete your account">
                    <i class="fas fa-trash-alt" aria-hidden="true"></i>
                    <span>Delete Account</span>
                </button>
            </div>
        </div>
    </div>
</div>
<script>
document.addEventListener('DOMContentLoaded', function() {
    console.log('Profile page loaded');
    
    const avatarUpload = document.getElementById('avatarUpload');
    const avatarPreview = document.getElementById('avatarPreview');
    
    // Click handler for avatar upload
    if (avatarUpload) {
        avatarUpload.addEventListener('change', function(e) {
            console.log('File selected');
            const file = e.target.files[0];
            if (!file) {
                console.log('No file selected');
                return;
            }
            
            // Validate file type
            const validTypes = ['image/jpeg', 'image/png', 'image/gif'];
            if (!validTypes.includes(file.type)) {
                alert('Please upload a valid image file (JPEG, PNG, or GIF)');
                return;
            }
            
            // Validate file size (2MB)
            if (file.size > 2 * 1024 * 1024) {
                alert('File size must be less than 2MB');
                return;
            }
            
            // Show preview immediately
            const reader = new FileReader();
            reader.onload = function(e) {
                avatarPreview.src = e.target.result;
            };
            reader.readAsDataURL(file);
            
            // Create FormData for upload
            const formData = new FormData();
            formData.append('avatar', file);
            formData.append('csrf_token', '<?php echo $_SESSION['csrf_token']; ?>');
            
            // Show upload progress
            showUploadProgress();
            
            // Send AJAX request
            const xhr = new XMLHttpRequest();
            xhr.open('POST', '<?php echo BASE_URL; ?>upload_avatar.php', true);
            
            xhr.onload = function() {
                console.log('Upload response:', xhr.responseText);
                hideUploadProgress();
                
                try {
                    const response = JSON.parse(xhr.responseText);
                    if (response.success) {
                        // Update avatar preview with new URL
                        avatarPreview.src = response.avatar_url + '?t=' + new Date().getTime();
                        
                        // Update navigation avatar
                        const navAvatar = document.querySelector('.nav-avatar');
                        if (navAvatar) {
                            navAvatar.src = response.avatar_url + '?t=' + new Date().getTime();
                        }
                        
                        showNotification('success', response.message || 'Avatar updated successfully!');
                        
                        // Reload page after 2 seconds to update everywhere
                        setTimeout(() => {
                            window.location.reload();
                        }, 2000);
                    } else {
                        showNotification('error', response.error || 'Upload failed');
                        // Revert to original avatar on error
                        avatarPreview.src = '<?php echo $avatar_url; ?>';
                    }
                } catch (e) {
                    console.error('Parse error:', e);
                    showNotification('error', 'Error parsing response');
                    avatarPreview.src = '<?php echo $avatar_url; ?>';
                }
            };
            
            xhr.onerror = function() {
                console.error('Network error');
                hideUploadProgress();
                showNotification('error', 'Network error occurred');
                avatarPreview.src = '<?php echo $avatar_url; ?>';
            };
            
            xhr.upload.onprogress = function(e) {
                if (e.lengthComputable) {
                    const percent = (e.loaded / e.total) * 100;
                    updateProgressBar(percent);
                }
            };
            
            xhr.send(formData);
        });
    }
    
    // Make camera icon clickable
    const cameraIcon = document.querySelector('.avatar-upload');
    if (cameraIcon) {
        cameraIcon.style.cursor = 'pointer';
    }
});
</script>

<?php include 'includes/footer.php'; ?>