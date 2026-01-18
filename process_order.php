<?php
include 'includes/config.php';
require_login();

// Handle different actions
$action = $_GET['action'] ?? '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && $action === '') {
    // Place new order (existing code)
    if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        $_SESSION['error'] = "Invalid security token";
        header("Location: products.php");
        exit();
    }

    // Only buyers can place orders
    if ($_SESSION['user_type'] !== 'buyer') {
        $_SESSION['error'] = "Only buyers can place orders";
        header("Location: products.php");
        exit();
    }

    $product_id = intval($_POST['product_id'] ?? 0);
    $quantity = intval($_POST['quantity'] ?? 0);
    $buyer_id = $_SESSION['user_id'];

    try {
        // Begin transaction
        $pdo->beginTransaction();
        
        // Get product details and check stock
        $stmt = $pdo->prepare("SELECT price, stock FROM products WHERE id = ? FOR UPDATE");
        $stmt->execute([$product_id]);
        $product = $stmt->fetch();
        
        if (!$product) {
            throw new Exception("Product not found");
        }
        
        if ($product['stock'] < $quantity) {
            throw new Exception("Insufficient stock. Available: " . $product['stock']);
        }
        
        if ($quantity <= 0) {
            throw new Exception("Invalid quantity");
        }
        
        // Calculate total price
        $total_price = $product['price'] * $quantity;
        
        // Create order
        $stmt = $pdo->prepare("INSERT INTO orders (buyer_id, product_id, quantity, total_price) 
                              VALUES (?, ?, ?, ?)");
        $stmt->execute([$buyer_id, $product_id, $quantity, $total_price]);
        
        // Update stock
        $new_stock = $product['stock'] - $quantity;
        $stmt = $pdo->prepare("UPDATE products SET stock = ? WHERE id = ?");
        $stmt->execute([$new_stock, $product_id]);
        
        // Commit transaction
        $pdo->commit();
        
        $_SESSION['success'] = "Order placed successfully! Order ID: " . $pdo->lastInsertId();
        header("Location: order_history.php");
        exit();
        
    } catch (Exception $e) {
        $pdo->rollBack();
        $_SESSION['error'] = "Order failed: " . $e->getMessage();
        header("Location: products.php");
        exit();
    }
    
} elseif ($action === 'cancel') {
    // Cancel order (buyer)
    $order_id = intval($_GET['id'] ?? 0);
    $user_id = $_SESSION['user_id'];
    
    try {
        $pdo->beginTransaction();
        
        // Get order details
        $stmt = $pdo->prepare("SELECT o.*, p.stock FROM orders o 
                              JOIN products p ON o.product_id = p.id 
                              WHERE o.id = ? AND o.buyer_id = ? AND o.status = 'pending'");
        $stmt->execute([$order_id, $user_id]);
        $order = $stmt->fetch();
        
        if (!$order) {
            throw new Exception("Order not found or cannot be cancelled");
        }
        
        // Update order status
        $stmt = $pdo->prepare("UPDATE orders SET status = 'cancelled' WHERE id = ?");
        $stmt->execute([$order_id]);
        
        // Restore stock
        $new_stock = $order['stock'] + $order['quantity'];
        $stmt = $pdo->prepare("UPDATE products SET stock = ? WHERE id = ?");
        $stmt->execute([$new_stock, $order['product_id']]);
        
        $pdo->commit();
        
        $_SESSION['success'] = "Order cancelled successfully";
        header("Location: order_history.php");
        exit();
        
    } catch (Exception $e) {
        $pdo->rollBack();
        $_SESSION['error'] = "Cancellation failed: " . $e->getMessage();
        header("Location: order_history.php");
        exit();
    }
    
} elseif ($action === 'update_status') {
    // Update order status (seller)
    if ($_SESSION['user_type'] !== 'seller') {
        $_SESSION['error'] = "Only sellers can update order status";
        header("Location: view_orders.php");
        exit();
    }
    
    $order_id = intval($_GET['id'] ?? 0);
    $status = $_GET['status'] ?? '';
    
    if (!in_array($status, ['pending', 'processing', 'completed', 'cancelled'])) {
        $_SESSION['error'] = "Invalid status";
        header("Location: view_orders.php");
        exit();
    }
    
    try {
        // Check if seller owns the product in this order
        $stmt = $pdo->prepare("SELECT p.id FROM orders o 
                              JOIN products p ON o.product_id = p.id 
                              WHERE o.id = ? AND p.seller_id = ?");
        $stmt->execute([$order_id, $_SESSION['user_id']]);
        
        if (!$stmt->fetch()) {
            throw new Exception("Order not found or you don't have permission");
        }
        
        // Update status
        $stmt = $pdo->prepare("UPDATE orders SET status = ? WHERE id = ?");
        $stmt->execute([$status, $order_id]);
        
        $_SESSION['success'] = "Order status updated successfully";
        header("Location: view_orders.php");
        exit();
        
    } catch (Exception $e) {
        $_SESSION['error'] = "Update failed: " . $e->getMessage();
        header("Location: view_orders.php");
        exit();
    }
} else {
    header("Location: dashboard.php");
    exit();
}
?>