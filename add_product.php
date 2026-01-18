<?php
$page_title = "Add Product";
include 'includes/config.php';
require_login();

// Only sellers can access this page
if ($_SESSION['user_type'] !== 'seller') {
    header("Location: dashboard.php");
    exit();
}
?>
<?php include 'includes/header.php'; ?>

<div class="form-container">
    <h2>Add New Product</h2>
    
    <?php if (isset($_SESSION['error'])): ?>
        <div class="alert alert-error"><?php echo $_SESSION['error']; unset($_SESSION['error']); ?></div>
    <?php endif; ?>
    
    <form action="process_product.php" method="POST" enctype="multipart/form-data">
        <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
        
        <div class="form-group">
            <label for="name">Product Name:</label>
            <input type="text" id="name" name="name" class="form-control" required>
        </div>
        
        <div class="form-group">
            <label for="description">Description:</label>
            <textarea id="description" name="description" class="form-control" rows="4" required></textarea>
        </div>
        
        <div class="form-group">
            <label for="category">Category:</label>
            <select id="category" name="category" class="form-control" required>
                <option value="">Select Category</option>
                <option value="food">Food</option>
                <option value="toys">Toys</option>
                <option value="health">Health Products</option>
                <option value="accessories">Accessories</option>
                <option value="services">Services</option>
            </select>
        </div>
        
        <div class="form-group">
            <label for="price">Price ($):</label>
            <input type="number" id="price" name="price" step="0.01" min="0.01" class="form-control" required>
        </div>
        
        <div class="form-group">
            <label for="stock">Stock Quantity:</label>
            <input type="number" id="stock" name="stock" min="0" class="form-control" required>
        </div>
        
        <div class="form-group">
            <label for="image">Product Image:</label>
            <input type="file" id="image" name="image" accept="image/*" class="form-control">
            <small>Optional. Max size: 2MB. Allowed: JPG, PNG, GIF</small>
        </div>
        
        <button type="submit" name="action" value="add" class="btn">Add Product</button>
        <a href="manage_products.php" class="btn btn-secondary">Cancel</a>
    </form>
</div>

<?php include 'includes/footer.php'; ?>