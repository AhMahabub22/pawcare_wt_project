<?php
$page_title = "Register";
include 'includes/config.php';

if (is_logged_in()) {
    header("Location: dashboard.php");
    exit();
}
?>
<?php include 'includes/header.php'; ?>

<div class="form-container">
    <h2>Create Account</h2>
    
    <?php if (isset($_SESSION['error'])): ?>
        <div class="alert alert-error"><?php echo $_SESSION['error']; unset($_SESSION['error']); ?></div>
    <?php endif; ?>
    
    <form id="registerForm" action="process_register.php" method="POST" onsubmit="return validateRegister()">
        <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
        
        <div class="form-group">
            <label for="username">Username:</label>
            <input type="text" id="username" name="username" class="form-control" required>
            <span class="error" id="usernameError"></span>
        </div>
        
        <div class="form-group">
            <label for="email">Email:</label>
            <input type="email" id="email" name="email" class="form-control" required>
            <span class="error" id="emailError"></span>
        </div>
        
        <div class="form-group">
            <label for="password">Password:</label>
            <input type="password" id="password" name="password" class="form-control" required>
            <span class="error" id="passwordError"></span>
        </div>
        
        <div class="form-group">
            <label for="confirm_password">Confirm Password:</label>
            <input type="password" id="confirm_password" name="confirm_password" class="form-control" required>
            <span class="error" id="confirmPasswordError"></span>
        </div>
        
        <div class="form-group">
            <label for="full_name">Full Name:</label>
            <input type="text" id="full_name" name="full_name" class="form-control" required>
        </div>
        
        <div class="form-group">
            <label for="user_type">Account Type:</label>
            <select id="user_type" name="user_type" class="form-control" required>
                <option value="">Select Type</option>
                <option value="buyer">Buyer</option>
                <option value="seller">Seller</option>
            </select>
        </div>
        
        <div class="form-group">
            <label for="address">Address:</label>
            <textarea id="address" name="address" class="form-control" rows="3"></textarea>
        </div>
        
        <div class="form-group">
            <label for="phone">Phone:</label>
            <input type="tel" id="phone" name="phone" class="form-control">
        </div>
        
        <button type="submit" class="btn">Register</button>
        <p style="margin-top: 20px;">Already have an account? <a href="login.php">Login here</a></p>
    </form>
</div>

<style>
.error {
    color: #dc3545;
    font-size: 14px;
    margin-top: 5px;
    display: block;
}
</style>

<?php include 'includes/footer.php'; ?>