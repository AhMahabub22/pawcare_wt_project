<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PawCare - <?php echo $page_title ?? 'Smart Pet Care Platform'; ?></title>
    
    <!-- Main CSS -->
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>css/style.css">
    
    <!-- Page Specific CSS -->
    <?php if (isset($page_css)): ?>
        <?php foreach ($page_css as $css_file): ?>
            <link rel="stylesheet" href="<?php echo BASE_URL; ?>css/<?php echo $css_file; ?>">
        <?php endforeach; ?>
    <?php endif; ?>
    
    <!-- Font Awesome (using jsDelivr CDN - No Tracking) -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@fortawesome/fontawesome-free@6.4.0/css/all.min.css">
    
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&family=Roboto:wght@300;400;500&display=swap" rel="stylesheet">
    
    <!-- Favicon -->
    <link rel="icon" type="image/x-icon" href="<?php echo BASE_URL; ?>images/favicon.ico">
    
    <!-- Additional meta tags to prevent tracking blocking -->
    <meta http-equiv="Permissions-Policy" content="interest-cohort=()">
    <meta name="robots" content="noindex, nofollow">
</head>
<body>
    <!-- Navigation -->
    <nav class="navbar">
        <div class="nav-container">
            <!-- Logo -->
            <div class="nav-brand">
                <a href="<?php echo BASE_URL; ?>index.php" title="PawCare Home">
                    <i class="fas fa-paw" aria-hidden="true"></i>
                    <span>PawCare</span>
                </a>
            </div>
            
            <!-- Mobile Menu Toggle -->
            <button class="menu-toggle" id="menuToggle" aria-label="Toggle navigation menu" title="Menu">
                <i class="fas fa-bars" aria-hidden="true"></i>
            </button>
            
            <!-- Navigation Menu -->
            <ul class="nav-menu" id="navMenu">
                <?php if (is_logged_in()): ?>
                    <li>
                        <a href="<?php echo BASE_URL; ?>dashboard.php" 
                           class="<?php echo basename($_SERVER['PHP_SELF']) == 'dashboard.php' ? 'active' : ''; ?>"
                           title="Dashboard">
                            <i class="fas fa-home" aria-hidden="true"></i>
                            <span>Dashboard</span>
                        </a>
                    </li>
                    <li>
                        <a href="<?php echo BASE_URL; ?>profile.php" 
                           class="<?php echo basename($_SERVER['PHP_SELF']) == 'profile.php' ? 'active' : ''; ?>"
                           title="My Profile">
                            <i class="fas fa-user" aria-hidden="true"></i>
                            <span>Profile</span>
                        </a>
                    </li>
                    
                    <?php if (get_user_type() == 'buyer'): ?>
                        <li>
                            <a href="<?php echo BASE_URL; ?>products.php" 
                               class="<?php echo basename($_SERVER['PHP_SELF']) == 'products.php' ? 'active' : ''; ?>"
                               title="Browse Products">
                                <i class="fas fa-paw" aria-hidden="true"></i>
                                <span>Products</span>
                            </a>
                        </li>
                        <li>
                            <a href="<?php echo BASE_URL; ?>order_history.php" 
                               class="<?php echo basename($_SERVER['PHP_SELF']) == 'order_history.php' ? 'active' : ''; ?>"
                               title="My Orders">
                                <i class="fas fa-history" aria-hidden="true"></i>
                                <span>Orders</span>
                            </a>
                        </li>
                    <?php elseif (get_user_type() == 'seller'): ?>
                        <li>
                            <a href="<?php echo BASE_URL; ?>add_product.php" 
                               class="<?php echo basename($_SERVER['PHP_SELF']) == 'add_product.php' ? 'active' : ''; ?>"
                               title="Add New Product">
                                <i class="fas fa-plus" aria-hidden="true"></i>
                                <span>Add Product</span>
                            </a>
                        </li>
                        <li>
                            <a href="<?php echo BASE_URL; ?>manage_products.php" 
                               class="<?php echo basename($_SERVER['PHP_SELF']) == 'manage_products.php' ? 'active' : ''; ?>"
                               title="Manage Products">
                                <i class="fas fa-boxes" aria-hidden="true"></i>
                                <span>Manage Products</span>
                            </a>
                        </li>
                        <li>
                            <a href="<?php echo BASE_URL; ?>view_orders.php" 
                               class="<?php echo basename($_SERVER['PHP_SELF']) == 'view_orders.php' ? 'active' : ''; ?>"
                               title="View Orders">
                                <i class="fas fa-clipboard-list" aria-hidden="true"></i>
                                <span>View Orders</span>
                            </a>
                        </li>
                    <?php endif; ?>
                    
                    <li>
                        <a href="<?php echo BASE_URL; ?>logout.php" title="Logout">
                            <i class="fas fa-sign-out-alt" aria-hidden="true"></i>
                            <span>Logout</span>
                        </a>
                    </li>
                    
                    <!-- User Welcome -->
                    <li class="user-welcome">
                        <img src="<?php echo get_avatar_url($_SESSION['profile_picture'] ?? 'default-avatar.jpg'); ?>" 
                             alt="Profile picture of <?php echo $_SESSION['username'] ?? 'User'; ?>" 
                             class="nav-avatar">
                        <span>Welcome, <?php echo $_SESSION['username'] ?? 'User'; ?></span>
                    </li>
                <?php else: ?>
                    <li>
                        <a href="<?php echo BASE_URL; ?>index.php" 
                           class="<?php echo basename($_SERVER['PHP_SELF']) == 'index.php' ? 'active' : ''; ?>"
                           title="Home">
                            <i class="fas fa-home" aria-hidden="true"></i>
                            <span>Home</span>
                        </a>
                    </li>
                    <li>
                        <a href="<?php echo BASE_URL; ?>login.php" 
                           class="<?php echo basename($_SERVER['PHP_SELF']) == 'login.php' ? 'active' : ''; ?>"
                           title="Login">
                            <i class="fas fa-sign-in-alt" aria-hidden="true"></i>
                            <span>Login</span>
                        </a>
                    </li>
                    <li>
                        <a href="<?php echo BASE_URL; ?>register.php" 
                           class="<?php echo basename($_SERVER['PHP_SELF']) == 'register.php' ? 'active' : ''; ?>"
                           title="Register">
                            <i class="fas fa-user-plus" aria-hidden="true"></i>
                            <span>Register</span>
                        </a>
                    </li>
                <?php endif; ?>
            </ul>
        </div>
    </nav>
    
    <!-- Main Content Container -->
    <div class="container fade-in">