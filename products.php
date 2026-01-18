<?php
$page_title = "Products";
include 'includes/config.php';
require_login();

// Only buyers can access this page
if ($_SESSION['user_type'] !== 'buyer') {
    header("Location: dashboard.php");
    exit();
}

// Get products with optional category filter
$category = $_GET['category'] ?? '';
$search = $_GET['search'] ?? '';

try {
    $sql = "SELECT p.*, u.username as seller_name FROM products p 
            JOIN users u ON p.seller_id = u.id 
            WHERE p.stock > 0";
    
    $params = [];
    
    if ($category) {
        $sql .= " AND p.category = ?";
        $params[] = $category;
    }
    
    if ($search) {
        $sql .= " AND (p.name LIKE ? OR p.description LIKE ?)";
        $params[] = "%$search%";
        $params[] = "%$search%";
    }
    
    $sql .= " ORDER BY p.created_at DESC";
    
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    $products = $stmt->fetchAll();
} catch (PDOException $e) {
    $products = [];
    $_SESSION['error'] = "Error loading products: " . $e->getMessage();
}
?>
<?php include 'includes/header.php'; ?>
<link rel="stylesheet" href="css/products.css">

<h1>Browse Products</h1>

<!-- Search and Filter -->
<div class="product-filters">
    <form method="GET" class="filter-form">
        <div class="form-group" style="display: inline-block; margin-right: 15px;">
            <input type="text" name="search" placeholder="Search products..." 
                   value="<?php echo htmlspecialchars($search); ?>" class="form-control" style="width: 300px;">
        </div>
        
        <div class="form-group" style="display: inline-block; margin-right: 15px;">
            <select name="category" class="form-control" onchange="this.form.submit()">
                <option value="">All Categories</option>
                <option value="food" <?php echo $category == 'food' ? 'selected' : ''; ?>>Food</option>
                <option value="toys" <?php echo $category == 'toys' ? 'selected' : ''; ?>>Toys</option>
                <option value="health" <?php echo $category == 'health' ? 'selected' : ''; ?>>Health</option>
                <option value="accessories" <?php echo $category == 'accessories' ? 'selected' : ''; ?>>Accessories</option>
                <option value="services" <?php echo $category == 'services' ? 'selected' : ''; ?>>Services</option>
            </select>
        </div>
        
        <button type="submit" class="btn">Search</button>
        <a href="products.php" class="btn btn-secondary">Clear</a>
    </form>
</div>

<?php if (isset($_SESSION['error'])): ?>
    <div class="alert alert-error"><?php echo $_SESSION['error']; unset($_SESSION['error']); ?></div>
<?php endif; ?>

<?php if (isset($_SESSION['success'])): ?>
    <div class="alert alert-success"><?php echo $_SESSION['success']; unset($_SESSION['success']); ?></div>
<?php endif; ?>

<div class="product-grid">
    <?php if (empty($products)): ?>
        <p style="grid-column: 1/-1; text-align: center; padding: 40px;">No products found.</p>
    <?php else: ?>
        <?php foreach ($products as $product): ?>
            <div class="product-card">
                <img src="<?php echo $product['image_url'] ? 'images/uploads/' . $product['image_url'] : 'https://via.placeholder.com/300x200?text=No+Image'; ?>" 
                     alt="<?php echo htmlspecialchars($product['name']); ?>" class="product-image">
                <div class="product-info">
                    <h3><?php echo htmlspecialchars($product['name']); ?></h3>
                    <p class="product-price">$<?php echo number_format($product['price'], 2); ?></p>
                    <p><?php echo htmlspecialchars(substr($product['description'], 0, 100)) . '...'; ?></p>
                    <p><small>Category: <?php echo ucfirst($product['category']); ?></small></p>
                    <p><small>Seller: <?php echo htmlspecialchars($product['seller_name']); ?></small></p>
                    <p><small>Stock: <?php echo $product['stock']; ?> available</small></p>
                    
                    <form action="process_order.php" method="POST" onsubmit="return validateOrder(this)">
                        <input type="hidden" name="product_id" value="<?php echo $product['id']; ?>">
                        <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
                        
                        <div class="form-group" style="margin-top: 15px;">
                            <label for="quantity_<?php echo $product['id']; ?>">Quantity:</label>
                            <input type="number" id="quantity_<?php echo $product['id']; ?>" 
                                   name="quantity" min="1" max="<?php echo $product['stock']; ?>" 
                                   value="1" class="form-control" style="width: 80px; display: inline-block;">
                        </div>
                        
                        <button type="submit" class="btn" style="width: 100%; margin-top: 10px;">
                            <i class="fas fa-cart-plus"></i> Add to Cart
                        </button>
                    </form>
                </div>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>
</div>

<style>
.product-filters {
    background: white;
    padding: 20px;
    border-radius: 8px;
    box-shadow: 0 2px 10px rgba(0,0,0,0.1);
    margin: 20px 0;
}

.filter-form {
    display: flex;
    align-items: center;
    flex-wrap: wrap;
    gap: 10px;
}
</style>

<script>
function validateOrder(form) {
    var quantity = form.querySelector('input[name="quantity"]').value;
    var maxStock = parseInt(form.querySelector('input[name="quantity"]').max);
    
    if (quantity < 1) {
        alert('Quantity must be at least 1');
        return false;
    }
    
    if (quantity > maxStock) {
        alert('Quantity exceeds available stock');
        return false;
    }
    
    return true;
}
</script>

<?php include 'includes/footer.php'; ?>