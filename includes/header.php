<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PawCare - <?php echo $page_title ?? 'Smart Pet Care Platform'; ?></title>
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
    <nav class="navbar">
        <div class="nav-container">
            <div class="nav-brand">
                <a href="index.php">PawCare</a>
            </div>
            <ul class="nav-menu">
                <?php if (is_logged_in()): ?>
                    <li><a href="dashboard.php"><i class="fas fa-home"></i> Dashboard</a></li>
                    <li><a href="profile.php"><i class="fas fa-user"></i> Profile</a></li>
                    
                    <?php if (get_user_type() == 'buyer'): ?>
                        <li><a href="products.php"><i class="fas fa-paw"></i> Products</a></li>
                        <li><a href="order_history.php"><i class="fas fa-history"></i> Orders</a></li>
                    <?php elseif (get_user_type() == 'seller'): ?>
                        <li><a href="add_product.php"><i class="fas fa-plus"></i> Add Product</a></li>
                        <li><a href="manage_products.php"><i class="fas fa-boxes"></i> Manage Products</a></li>
                        <li><a href="view_orders.php"><i class="fas fa-clipboard-list"></i> View Orders</a></li>
                    <?php endif; ?>
                    
                    <li><a href="logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
                    <li class="user-welcome">
                        <img src="images/uploads/<?php echo $_SESSION['profile_picture'] ?? 'default-avatar.jpg'; ?>" 
                             alt="Profile" class="nav-avatar">
                        <span>Welcome, <?php echo $_SESSION['username'] ?? 'User'; ?></span>
                    </li>
                <?php else: ?>
                    <li><a href="index.php"><i class="fas fa-home"></i> Home</a></li>
                    <li><a href="login.php"><i class="fas fa-sign-in-alt"></i> Login</a></li>
                    <li><a href="register.php"><i class="fas fa-user-plus"></i> Register</a></li>
                <?php endif; ?>
            </ul>
        </div>
    </nav>
    <div class="container">