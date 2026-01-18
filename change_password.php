<?php
$page_title = "Change Password";
include 'includes/config.php';
require_login();
?>
<?php include 'includes/header.php'; ?>

<div class="form-container">
    <h2>Change Password</h2>
    
    <?php if (isset($_SESSION['error'])): ?>
        <div class="alert alert-error"><?php echo $_SESSION['error']; unset($_SESSION['error']); ?></div>
    <?php endif; ?>
    
    <?php if (isset($_SESSION['success'])): ?>
        <div class="alert alert-success"><?php echo $_SESSION['success']; unset($_SESSION['success']); ?></div>
    <?php endif; ?>
    
    <form action="process_profile.php" method="POST">
        <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
        <input type="hidden" name="action" value="change_password">
        
        <div class="form-group">
            <label for="current_password">Current Password:</label>
            <input type="password" id="current_password" name="current_password" class="form-control" required>
        </div>
        
        <div class="form-group">
            <label for="new_password">New Password:</label>
            <input type="password" id="new_password" name="new_password" class="form-control" required>
            <small>Minimum 8 characters</small>
        </div>
        
        <div class="form-group">
            <label for="confirm_new_password">Confirm New Password:</label>
            <input type="password" id="confirm_new_password" name="confirm_new_password" class="form-control" required>
        </div>
        
        <button type="submit" class="btn">Change Password</button>
        <a href="profile.php" class="btn btn-secondary">Cancel</a>
    </form>
</div>

<script>
document.getElementById('new_password').addEventListener('input', function() {
    var password = this.value;
    var strength = 0;
    
    if (password.length >= 8) strength++;
    if (/[A-Z]/.test(password)) strength++;
    if (/[0-9]/.test(password)) strength++;
    if (/[^A-Za-z0-9]/.test(password)) strength++;
    
    var strengthText = document.getElementById('passwordStrength');
    if (!strengthText) {
        strengthText = document.createElement('small');
        strengthText.id = 'passwordStrength';
        strengthText.style.display = 'block';
        strengthText.style.marginTop = '5px';
        this.parentNode.appendChild(strengthText);
    }
    
    var labels = ['Very Weak', 'Weak', 'Moderate', 'Strong', 'Very Strong'];
    strengthText.textContent = 'Strength: ' + labels[strength];
    strengthText.style.color = 
        strength <= 1 ? '#dc3545' : 
        strength == 2 ? '#ffc107' : 
        strength == 3 ? '#28a745' : '#007bff';
});
</script>

<?php include 'includes/footer.php'; ?>