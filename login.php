<?php
$page_title = "Login";
include 'includes/config.php';

if (is_logged_in()) {
    header("Location: dashboard.php");
    exit();
}
?>
<?php include 'includes/header.php'; ?>

<div class="form-container">
    <h2>Login to PawCare</h2>
    
    <?php if (isset($_SESSION['error'])): ?>
        <div class="alert alert-error"><?php echo $_SESSION['error']; unset($_SESSION['error']); ?></div>
    <?php endif; ?>
    
    <?php if (isset($_SESSION['success'])): ?>
        <div class="alert alert-success"><?php echo $_SESSION['success']; unset($_SESSION['success']); ?></div>
    <?php endif; ?>
    
    <form action="process_login.php" method="POST">
        <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
        
        <div class="form-group">
            <label for="username">Username or Email:</label>
            <input type="text" id="username" name="username" class="form-control" required>
        </div>
        
        <div class="form-group">
            <label for="password">Password:</label>
            <input type="password" id="password" name="password" class="form-control" required>
        </div>
        
        <div class="form-group">
            <label>
                <input type="checkbox" name="remember" value="1"> Remember me
            </label>
        </div>
        
        <button type="submit" class="btn">Login</button>
        <p style="margin-top: 20px;">
            <a href="register.php">Create new account</a> | 
            <a href="change_password.php">Forgot password?</a>
        </p>
    </form>
</div>

<?php include 'includes/footer.php'; ?>