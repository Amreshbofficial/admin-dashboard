<?php
session_start();
if(!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

include('config/database.php');
$database = new Database();
$db = $database->getConnection();

// Handle order actions
if(isset($_GET['action']) && $_GET['action'] == 'delete' && isset($_GET['id'])) {
    $id = $_GET['id'];
    $stmt = $db->prepare("DELETE FROM orders WHERE id = ?");
    $stmt->execute([$id]);
    header("Location: orders.php?message=Order deleted successfully");
    exit;
}

if($_POST && isset($_POST['update_status'])) {
    $id = $_POST['order_id'];
    $status = $_POST['status'];
    
    $stmt = $db->prepare("UPDATE orders SET status = ? WHERE id = ?");
    $stmt->execute([$status, $id]);
    header("Location: orders.php?message=Order status updated successfully");
    exit;
}

// Get all orders with user information
$orders = $db->query("SELECT o.*, u.username, u.email 
                     FROM orders o 
                     LEFT JOIN users u ON o.user_id = u.id 
                     ORDER BY o.order_date DESC")->fetchAll(PDO::FETCH_ASSOC);

// Get order details for view
$order_details = null;
if(isset($_GET['action']) && $_GET['action'] == 'view' && isset($_GET['id'])) {
    $id = $_GET['id'];
    
    // Get order info
    $stmt = $db->prepare("SELECT o.*, u.username, u.email, u.full_name 
                         FROM orders o 
                         LEFT JOIN users u ON o.user_id = u.id 
                         WHERE o.id = ?");
    $stmt->execute([$id]);
    $order_details = $stmt->fetch(PDO::FETCH_ASSOC);
    
    // Get order items
    if($order_details) {
        $stmt = $db->prepare("SELECT oi.*, p.name as product_name 
                             FROM order_items oi 
                             LEFT JOIN products p ON oi.product_id = p.id 
                             WHERE oi.order_id = ?");
        $stmt->execute([$id]);
        $order_items = $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Orders - Admin Panel</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <?php include('includes/header.php'); ?>
    
    <div class="container">
        <?php include('includes/sidebar.php'); ?>
        
        <main class="main-content">
            <h1>Order Management</h1>
            
            <?php if(isset($_GET['message'])): ?>
                <div class="alert alert-success"><?php echo $_GET['message']; ?></div>
            <?php endif; ?>
            
            <?php if(isset($_GET['action']) && $_GET['action'] == 'view' && $order_details): ?>
                <!-- Order Details View -->
                <div class="form-container">
                    <div style="display: flex; justify-content: between; align-items: center; margin-bottom: 20px;">
                        <h3>Order #<?php echo $order_details['id']; ?> Details</h3>
                        <a href="orders.php" class="btn btn-primary">Back to Orders</a>
                    </div>
                    
                    <div class="order-info">
                        <div class="form-row">
                            <div class="form-field">
                                <strong>Customer:</strong> <?php echo $order_details['full_name'] ?: $order_details['username']; ?>
                            </div>
                            <div class="form-field">
                                <strong>Email:</strong> <?php echo $order_details['email']; ?>
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="form-field">
                                <strong>Order Date:</strong> <?php echo date('M j, Y g:i A', strtotime($order_details['order_date'])); ?>
                            </div>
                            <div class="form-field">
                                <strong>Status:</strong> 
                                <span class="status status-<?php echo $order_details['status']; ?>">
                                    <?php echo ucfirst($order_details['status']); ?>
                                </span>
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="form-field" style="flex: 1 1 100%;">
                                <strong>Shipping Address:</strong><br>
                                <?php echo nl2br($order_details['shipping_address']); ?>
                            </div>
                        </div>
                    </div>
                    
                    <h4 style="margin-top: 30px;">Order Items</h4>
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>Product</th>
                                <th>Quantity</th>
                                <th>Unit Price</th>
                                <th>Total</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach($order_items as $item): ?>
                            <tr>
                                <td><?php echo $item['product_name']; ?></td>
                                <td><?php echo $item['quantity']; ?></td>
                                <td>$<?php echo number_format($item['unit_price'], 2); ?></td>
                                <td>$<?php echo number_format($item['quantity'] * $item['unit_price'], 2); ?></td>
                            </tr>
                            <?php endforeach; ?>
                            <tr style="font-weight: bold;">
                                <td colspan="3" style="text-align: right;">Total Amount:</td>
                                <td>$<?php echo number_format($order_details['total_amount'], 2); ?></td>
                            </tr>
                        </tbody>
                    </table>
                    
                    <div class="form-actions" style="margin-top: 20px;">
                        <form method="POST" style="display: inline-block;">
                            <input type="hidden" name="order_id" value="<?php echo $order_details['id']; ?>">
                            <select name="status" style="padding: 8px; margin-right: 10px;">
                                <option value="pending" <?php echo $order_details['status'] == 'pending' ? 'selected' : ''; ?>>Pending</option>
                                <option value="processing" <?php echo $order_details['status'] == 'processing' ? 'selected' : ''; ?>>Processing</option>
                                <option value="shipped" <?php echo $order_details['status'] == 'shipped' ? 'selected' : ''; ?>>Shipped</option>
                                <option value="delivered" <?php echo $order_details['status'] == 'delivered' ? 'selected' : ''; ?>>Delivered</option>
                                <option value="cancelled" <?php echo $order_details['status'] == 'cancelled' ? 'selected' : ''; ?>>Cancelled</option>
                            </select>
                            <button type="submit" name="update_status" class="btn btn-success">Update Status</button>
                        </form>
                    </div>
                </div>
            <?php else: ?>
                <!-- Orders List -->
                <div class="data-table-container">
                    <h3>All Orders</h3>
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>Order ID</th>
                                <th>Customer</th>
                                <th>Amount</th>
                                <th>Status</th>
                                <th>Order Date</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach($orders as $order): ?>
                            <tr>
                                <td>#<?php echo $order['id']; ?></td>
                                <td><?php echo $order['username']; ?></td>
                                <td>$<?php echo number_format($order['total_amount'], 2); ?></td>
                                <td><span class="status status-<?php echo $order['status']; ?>"><?php echo ucfirst($order['status']); ?></span></td>
                                <td><?php echo date('M j, Y', strtotime($order['order_date'])); ?></td>
                                <td class="action-buttons">
                                    <a href="orders.php?action=view&id=<?php echo $order['id']; ?>" class="btn btn-primary btn-sm">View</a>
                                    <a href="orders.php?action=delete&id=<?php echo $order['id']; ?>" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure?')">Delete</a>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </main>
    </div>
    
    <?php include('includes/footer.php'); ?>
</body>
</html>