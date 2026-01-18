<?php
$page_title = "Dashboard";
include 'includes/config.php';
require_login();

// Get user info
$user_id = $_SESSION['user_id'];
$user_type = $_SESSION['user_type'];

// Get stats based on user type
try {
    if ($user_type == 'buyer') {
        $stmt = $pdo->prepare("SELECT COUNT(*) as order_count FROM orders WHERE buyer_id = ?");
        $stmt->execute([$user_id]);
        $order_stats = $stmt->fetch();
        
        $stmt = $pdo->prepare("SELECT COUNT(*) as pending_orders FROM orders WHERE buyer_id = ? AND status = 'pending'");
        $stmt->execute([$user_id]);
        $pending_stats = $stmt->fetch();
    } else {
        $stmt = $pdo->prepare("SELECT COUNT(*) as product_count FROM products WHERE seller_id = ?");
        $stmt->execute([$user_id]);
        $product_stats = $stmt->fetch();
        
        $stmt = $pdo->prepare("SELECT COUNT(*) as order_count FROM orders o 
                              JOIN products p ON o.product_id = p.id 
                              WHERE p.seller_id = ?");
        $stmt->execute([$user_id]);
        $order_stats = $stmt->fetch();
    }
} catch (PDOException $e) {
    // Handle error
}
?>
<?php include 'includes/header.php'; ?>

<h1>Welcome, <?php echo $_SESSION['full_name']; ?>!</h1>
<p>You are logged in as a <strong><?php echo ucfirst($user_type); ?></strong></p>

<div class="dashboard-cards">
    <?php if ($user_type == 'buyer'): ?>
        <div class="card">
            <i class="fas fa-shopping-cart"></i>
            <h3>Total Orders</h3>
            <p><?php echo $order_stats['order_count'] ?? 0; ?> orders</p>
            <a href="order_history.php" class="btn">View Orders</a>
        </div>
        
        <div class="card">
            <i class="fas fa-clock"></i>
            <h3>Pending Orders</h3>
            <p><?php echo $pending_stats['pending_orders'] ?? 0; ?> pending</p>
            <a href="order_history.php?status=pending" class="btn">View Pending</a>
        </div>
        
        <div class="card">
            <i class="fas fa-paw"></i>
            <h3>Browse Products</h3>
            <p>Shop for your pet</p>
            <a href="products.php" class="btn">Shop Now</a>
        </div>
        
        <div class="card">
            <i class="fas fa-user"></i>
            <h3>Profile</h3>
            <p>Manage your account</p>
            <a href="profile.php" class="btn">Edit Profile</a>
        </div>
        
    <?php else: ?>
        <div class="card">
            <i class="fas fa-box"></i>
            <h3>Products Listed</h3>
            <p><?php echo $product_stats['product_count'] ?? 0; ?> products</p>
            <a href="manage_products.php" class="btn">Manage Products</a>
        </div>
        
        <div class="card">
            <i class="fas fa-clipboard-list"></i>
            <h3>Orders Received</h3>
            <p><?php echo $order_stats['order_count'] ?? 0; ?> orders</p>
            <a href="view_orders.php" class="btn">View Orders</a>
        </div>
        
        <div class="card">
            <i class="fas fa-plus-circle"></i>
            <h3>Add Product</h3>
            <p>List new products</p>
            <a href="add_product.php" class="btn">Add New</a>
        </div>
        
        <div class="card">
            <i class="fas fa-chart-line"></i>
            <h3>Sales</h3>
            <p>View sales report</p>
            <a href="view_orders.php" class="btn">View Report</a>
        </div>
    <?php endif; ?>
</div>

<div style="margin-top: 40px;">
    <h2>Quick Actions</h2>
    <div style="display: flex; gap: 15px; margin-top: 20px;">
        <a href="profile.php" class="btn">Edit Profile</a>
        <a href="change_password.php" class="btn btn-secondary">Change Password</a>
        <?php if ($user_type == 'buyer'): ?>
            <a href="products.php" class="btn">Browse Products</a>
        <?php else: ?>
            <a href="add_product.php" class="btn">Add Product</a>
        <?php endif; ?>
    </div>
</div>

<?php include 'includes/footer.php'; ?>