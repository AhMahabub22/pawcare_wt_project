<?php
$page_title = "Manage Products";
include 'includes/config.php';
require_login();

// Only sellers can access this page
if ($_SESSION['user_type'] !== 'seller') {
    header("Location: dashboard.php");
    exit();
}

$user_id = $_SESSION['user_id'];

try {
    $stmt = $pdo->prepare("SELECT * FROM products WHERE seller_id = ? ORDER BY created_at DESC");
    $stmt->execute([$user_id]);
    $products = $stmt->fetchAll();
} catch (PDOException $e) {
    $products = [];
    $_SESSION['error'] = "Error loading products: " . $e->getMessage();
}
?>
<?php include 'includes/header.php'; ?>

<h1>Manage Products</h1>

<?php if (isset($_SESSION['error'])): ?>
    <div class="alert alert-error"><?php echo $_SESSION['error']; unset($_SESSION['error']); ?></div>
<?php endif; ?>

<?php if (isset($_SESSION['success'])): ?>
    <div class="alert alert-success"><?php echo $_SESSION['success']; unset($_SESSION['success']); ?></div>
<?php endif; ?>

<a href="add_product.php" class="btn" style="margin-bottom: 20px;"><i class="fas fa-plus"></i> Add New Product</a>

<?php if (empty($products)): ?>
    <p>No products found. <a href="add_product.php">Add your first product</a></p>
<?php else: ?>
    <table class="table">
        <thead>
            <tr>
                <th>Image</th>
                <th>Name</th>
                <th>Category</th>
                <th>Price</th>
                <th>Stock</th>
                <th>Created</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($products as $product): ?>
                <tr>
                    <td>
                        <img src="<?php echo $product['image_url'] ? 'images/uploads/' . $product['image_url'] : 'https://via.placeholder.com/50'; ?>" 
                             alt="<?php echo htmlspecialchars($product['name']); ?>" style="width:50px; height:50px; object-fit:cover; border-radius:4px;">
                    </td>
                    <td><?php echo htmlspecialchars($product['name']); ?></td>
                    <td><?php echo ucfirst($product['category']); ?></td>
                    <td>$<?php echo number_format($product['price'], 2); ?></td>
                    <td><?php echo $product['stock']; ?></td>
                    <td><?php echo date('M j, Y', strtotime($product['created_at'])); ?></td>
                    <td>
                        <a href="add_product.php?edit=<?php echo $product['id']; ?>" class="btn" style="padding:5px 10px; font-size:12px;">Edit</a>
                        <button onclick="deleteProduct(<?php echo $product['id']; ?>)" class="btn btn-secondary" style="padding:5px 10px; font-size:12px; background-color:#dc3545;">Delete</button>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
<?php endif; ?>

<script>
function deleteProduct(productId) {
    if (confirm('Are you sure you want to delete this product? This action cannot be undone.')) {
        window.location.href = 'process_product.php?action=delete&id=' + productId;
    }
}
</script>

<?php include 'includes/footer.php'; ?>