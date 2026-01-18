<?php
include 'includes/config.php';
require_login();

// Only sellers can access this page
if ($_SESSION['user_type'] !== 'seller') {
    header("Location: dashboard.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// Handle product addition or update
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    
    // CSRF protection
    if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        $_SESSION['error'] = "Invalid security token";
        header("Location: add_product.php");
        exit();
    }
    
    if ($action === 'add' || $action === 'edit') {
        $name = clean_input($_POST['name'] ?? '', $pdo);
        $description = clean_input($_POST['description'] ?? '', $pdo);
        $category = clean_input($_POST['category'] ?? '', $pdo);
        $price = floatval($_POST['price'] ?? 0);
        $stock = intval($_POST['stock'] ?? 0);
        $product_id = intval($_POST['product_id'] ?? 0);
        
        // Validation
        if (empty($name) || empty($description) || empty($category) || $price <= 0 || $stock < 0) {
            $_SESSION['error'] = "Please fill all fields with valid data";
            header("Location: add_product.php");
            exit();
        }
        
        // Handle image upload
        $image_url = null;
        if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
            $file = $_FILES['image'];
            $allowed_types = ['image/jpeg', 'image/png', 'image/gif'];
            $max_size = 2 * 1024 * 1024; // 2MB
            
            if (!in_array($file['type'], $allowed_types)) {
                $_SESSION['error'] = 'Invalid file type. Only JPG, PNG, and GIF are allowed.';
                header("Location: add_product.php");
                exit();
            }
            
            if ($file['size'] > $max_size) {
                $_SESSION['error'] = 'File size exceeds 2MB limit';
                header("Location: add_product.php");
                exit();
            }
            
            // Generate unique filename
            $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
            $filename = 'product_' . time() . '_' . bin2hex(random_bytes(8)) . '.' . $extension;
            $upload_path = UPLOAD_DIR . $filename;
            
            if (move_uploaded_file($file['tmp_name'], $upload_path)) {
                $image_url = $filename;
            }
        }
        
        try {
            if ($action === 'add') {
                // Insert new product
                $stmt = $pdo->prepare("INSERT INTO products (seller_id, name, description, category, price, stock, image_url) 
                                      VALUES (?, ?, ?, ?, ?, ?, ?)");
                $stmt->execute([$user_id, $name, $description, $category, $price, $stock, $image_url]);
                
                $_SESSION['success'] = "Product added successfully";
                header("Location: manage_products.php");
                exit();
            } else {
                // Update existing product
                // First, get old image to delete if needed
                $old_image = null;
                if ($product_id) {
                    $stmt = $pdo->prepare("SELECT image_url FROM products WHERE id = ? AND seller_id = ?");
                    $stmt->execute([$product_id, $user_id]);
                    $old_image = $stmt->fetchColumn();
                }
                
                // If new image uploaded, delete old one
                if ($image_url && $old_image && file_exists('images/uploads/' . $old_image)) {
                    unlink('images/uploads/' . $old_image);
                }
                
                // Update product
                $sql = "UPDATE products SET name = ?, description = ?, category = ?, price = ?, stock = ?";
                $params = [$name, $description, $category, $price, $stock];
                
                if ($image_url) {
                    $sql .= ", image_url = ?";
                    $params[] = $image_url;
                }
                
                $sql .= " WHERE id = ? AND seller_id = ?";
                $params[] = $product_id;
                $params[] = $user_id;
                
                $stmt = $pdo->prepare($sql);
                $stmt->execute($params);
                
                $_SESSION['success'] = "Product updated successfully";
                header("Location: manage_products.php");
                exit();
            }
        } catch (PDOException $e) {
            $_SESSION['error'] = "Database error: " . $e->getMessage();
            header("Location: add_product.php");
            exit();
        }
    }
} elseif (isset($_GET['action']) && $_GET['action'] === 'delete') {
    // Handle product deletion
    $product_id = intval($_GET['id'] ?? 0);
    
    try {
        // Get product image to delete
        $stmt = $pdo->prepare("SELECT image_url FROM products WHERE id = ? AND seller_id = ?");
        $stmt->execute([$product_id, $user_id]);
        $image_url = $stmt->fetchColumn();
        
        // Delete product (cascade will handle orders)
        $stmt = $pdo->prepare("DELETE FROM products WHERE id = ? AND seller_id = ?");
        $stmt->execute([$product_id, $user_id]);
        
        // Delete image file if exists
        if ($image_url && file_exists('images/uploads/' . $image_url)) {
            unlink('images/uploads/' . $image_url);
        }
        
        $_SESSION['success'] = "Product deleted successfully";
        header("Location: manage_products.php");
        exit();
    } catch (PDOException $e) {
        $_SESSION['error'] = "Error deleting product: " . $e->getMessage();
        header("Location: manage_products.php");
        exit();
    }
} else {
    header("Location: manage_products.php");
    exit();
}
?>