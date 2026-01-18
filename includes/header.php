<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PawCare - <?php echo $page_title ?? 'Smart Pet Care Platform'; ?></title>
    
    <!-- Main Styles -->
    <link rel="stylesheet" href="css/style.css">
    
    <!-- Page Specific Styles -->
    <?php if (isset($page_css)): ?>
        <?php foreach ($page_css as $css_file): ?>
            <link rel="stylesheet" href="css/<?php echo $css_file; ?>">
        <?php endforeach; ?>
    <?php endif; ?>
    
    <!-- Font Awesome Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Segoe+UI:wght@300;400;600;700&family=Arial+Rounded+MT+Bold&display=swap" rel="stylesheet">
    
    <!-- Favicon -->
    <link rel="icon" type="image/x-icon" href="images/favicon.ico">
</head>
<body>
    <!-- Navigation -->
    <nav class="navbar">
        <div class="nav-container">
            <!-- Logo -->
            <div class="nav-brand">
                <a href="index.php">
                    <i class="fas fa-paw"></i>
                    PawCare
                </a>
            </div>
            
            <!-- Mobile Menu Toggle -->
            <button class="menu-toggle" id="menuToggle">
                <i class="fas fa-bars"></i>
            </button>
            
            <!-- Navigation Menu -->
            <ul class="nav-menu" id="navMenu">
                <?php if (is_logged_in()): ?>
                    <li><a href="dashboard.php" class="<?php echo basename($_SERVER['PHP_SELF']) == 'dashboard.php' ? 'active' : ''; ?>">
                        <i class="fas fa-home"></i> Dashboard</a>
                    </li>
                    <li><a href="profile.php" class="<?php echo basename($_SERVER['PHP_SELF']) == 'profile.php' ? 'active' : ''; ?>">
                        <i class="fas fa-user"></i> Profile</a>
                    </li>
                    
                    <?php if (get_user_type() == 'buyer'): ?>
                        <li><a href="products.php" class="<?php echo basename($_SERVER['PHP_SELF']) == 'products.php' ? 'active' : ''; ?>">
                            <i class="fas fa-paw"></i> Products</a>
                        </li>
                        <li><a href="order_history.php" class="<?php echo basename($_SERVER['PHP_SELF']) == 'order_history.php' ? 'active' : ''; ?>">
                            <i class="fas fa-history"></i> Orders</a>
                        </li>
                    <?php elseif (get_user_type() == 'seller'): ?>
                        <li><a href="add_product.php" class="<?php echo basename($_SERVER['PHP_SELF']) == 'add_product.php' ? 'active' : ''; ?>">
                            <i class="fas fa-plus"></i> Add Product</a>
                        </li>
                        <li><a href="manage_products.php" class="<?php echo basename($_SERVER['PHP_SELF']) == 'manage_products.php' ? 'active' : ''; ?>">
                            <i class="fas fa-boxes"></i> Manage Products</a>
                        </li>
                        <li><a href="view_orders.php" class="<?php echo basename($_SERVER['PHP_SELF']) == 'view_orders.php' ? 'active' : ''; ?>">
                            <i class="fas fa-clipboard-list"></i> View Orders</a>
                        </li>
                    <?php endif; ?>
                    
                    <li><a href="logout.php">
                        <i class="fas fa-sign-out-alt"></i> Logout</a>
                    </li>
                    
                    <!-- User Welcome -->
                    <li class="user-welcome">
                        <img src="images/uploads/<?php echo $_SESSION['profile_picture'] ?? 'default-avatar.jpg'; ?>" 
                             alt="Profile" class="nav-avatar">
                        <span>Welcome, <?php echo $_SESSION['username'] ?? 'User'; ?></span>
                    </li>
                <?php else: ?>
                    <li><a href="index.php" class="<?php echo basename($_SERVER['PHP_SELF']) == 'index.php' ? 'active' : ''; ?>">
                        <i class="fas fa-home"></i> Home</a>
                    </li>
                    <li><a href="login.php" class="<?php echo basename($_SERVER['PHP_SELF']) == 'login.php' ? 'active' : ''; ?>">
                        <i class="fas fa-sign-in-alt"></i> Login</a>
                    </li>
                    <li><a href="register.php" class="<?php echo basename($_SERVER['PHP_SELF']) == 'register.php' ? 'active' : ''; ?>">
                        <i class="fas fa-user-plus"></i> Register</a>
                    </li>
                <?php endif; ?>
            </ul>
        </div>
    </nav>
    
    <!-- Main Content Container -->
    <div class="container fade-in">