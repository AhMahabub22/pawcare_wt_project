<?php
$page_title = "View Orders";
include 'includes/config.php';
require_login();

// Only sellers can access this page
if ($_SESSION['user_type'] !== 'seller') {
    header("Location: dashboard.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$status_filter = $_GET['status'] ?? '';

try {
    $sql = "SELECT o.*, p.name as product_name, p.image_url, u.username as buyer_name, u.email as buyer_email 
            FROM orders o 
            JOIN products p ON o.product_id = p.id 
            JOIN users u ON o.buyer_id = u.id 
            WHERE p.seller_id = ?";
    
    $params = [$user_id];
    
    if ($status_filter && in_array($status_filter, ['pending', 'processing', 'completed', 'cancelled'])) {
        $sql .= " AND o.status = ?";
        $params[] = $status_filter;
    }
    
    $sql .= " ORDER BY o.order_date DESC";
    
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    $orders = $stmt->fetchAll();
} catch (PDOException $e) {
    $orders = [];
    $_SESSION['error'] = "Error loading orders: " . $e->getMessage();
}
?>
<?php include 'includes/header.php'; ?>

<h1>Orders Received</h1>

<!-- Status Filter -->
<div style="margin-bottom: 20px;">
    <strong>Filter by Status:</strong>
    <a href="view_orders.php" class="btn <?php echo $status_filter == '' ? 'active' : ''; ?>" style="padding: 5px 15px;">All</a>
    <a href="view_orders.php?status=pending" class="btn <?php echo $status_filter == 'pending' ? 'active' : ''; ?>" style="padding: 5px 15px;">Pending</a>
    <a href="view_orders.php?status=processing" class="btn <?php echo $status_filter == 'processing' ? 'active' : ''; ?>" style="padding: 5px 15px;">Processing</a>
    <a href="view_orders.php?status=completed" class="btn <?php echo $status_filter == 'completed' ? 'active' : ''; ?>" style="padding: 5px 15px;">Completed</a>
    <a href="view_orders.php?status=cancelled" class="btn <?php echo $status_filter == 'cancelled' ? 'active' : ''; ?>" style="padding: 5px 15px;">Cancelled</a>
</div>

<?php if (isset($_SESSION['error'])): ?>
    <div class="alert alert-error"><?php echo $_SESSION['error']; unset($_SESSION['error']); ?></div>
<?php endif; ?>

<?php if (isset($_SESSION['success'])): ?>
    <div class="alert alert-success"><?php echo $_SESSION['success']; unset($_SESSION['success']); ?></div>
<?php endif; ?>

<?php if (empty($orders)): ?>
    <p>No orders found.</p>
<?php else: ?>
    <table class="table">
        <thead>
            <tr>
                <th>Order ID</th>
                <th>Product</th>
                <th>Buyer</th>
                <th>Quantity</th>
                <th>Total Price</th>
                <th>Status</th>
                <th>Order Date</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($orders as $order): ?>
                <tr>
                    <td>#<?php echo $order['id']; ?></td>
                    <td>
                        <img src="<?php echo $order['image_url'] ? 'images/uploads/' . $order['image_url'] : 'https://via.placeholder.com/50'; ?>" 
                             alt="<?php echo htmlspecialchars($order['product_name']); ?>" style="width:50px; height:50px; object-fit:cover; border-radius:4px;">
                        <?php echo htmlspecialchars($order['product_name']); ?>
                    </td>
                    <td>
                        <strong><?php echo htmlspecialchars($order['buyer_name']); ?></strong><br>
                        <small><?php echo htmlspecialchars($order['buyer_email']); ?></small>
                    </td>
                    <td><?php echo $order['quantity']; ?></td>
                    <td>$<?php echo number_format($order['total_price'], 2); ?></td>
                    <td>
                        <span class="status-badge status-<?php echo $order['status']; ?>">
                            <?php echo ucfirst($order['status']); ?>
                        </span>
                    </td>
                    <td><?php echo date('M j, Y g:i A', strtotime($order['order_date'])); ?></td>
                    <td>
                        <select onchange="updateOrderStatus(<?php echo $order['id']; ?>, this.value)" style="padding:5px; border-radius:4px; border:1px solid #ddd;">
                            <option value="pending" <?php echo $order['status'] == 'pending' ? 'selected' : ''; ?>>Pending</option>
                            <option value="processing" <?php echo $order['status'] == 'processing' ? 'selected' : ''; ?>>Processing</option>
                            <option value="completed" <?php echo $order['status'] == 'completed' ? 'selected' : ''; ?>>Completed</option>
                            <option value="cancelled" <?php echo $order['status'] == 'cancelled' ? 'selected' : ''; ?>>Cancelled</option>
                        </select>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
<?php endif; ?>

<script>
function updateOrderStatus(orderId, status) {
    if (confirm('Change order status to ' + status + '?')) {
        window.location.href = 'process_order.php?action=update_status&id=' + orderId + '&status=' + status;
    }
}
</script>

<style>
.status-badge {
    padding: 5px 10px;
    border-radius: 20px;
    font-size: 12px;
    font-weight: bold;
}
.status-pending { background-color: #fff3cd; color: #856404; }
.status-processing { background-color: #cce5ff; color: #004085; }
.status-completed { background-color: #d4edda; color: #155724; }
.status-cancelled { background-color: #f8d7da; color: #721c24; }
.btn.active { background-color: #3a5a80; }
</style>

<?php include 'includes/footer.php'; ?>