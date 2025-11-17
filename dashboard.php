<?php
session_start();
if(!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

include('config/database.php');
$database = new Database();
$db = $database->getConnection();

// Get stats
$users_count = $db->query("SELECT COUNT(*) FROM users")->fetchColumn();
$products_count = $db->query("SELECT COUNT(*) FROM products")->fetchColumn();
$orders_count = $db->query("SELECT COUNT(*) FROM orders")->fetchColumn();
$revenue = $db->query("SELECT COALESCE(SUM(total_amount), 0) FROM orders WHERE status = 'delivered'")->fetchColumn();

// Get recent orders
$recent_orders = $db->query("SELECT o.id, u.username, o.total_amount, o.status, o.order_date 
                             FROM orders o 
                             LEFT JOIN users u ON o.user_id = u.id 
                             ORDER BY o.order_date DESC LIMIT 5")->fetchAll(PDO::FETCH_ASSOC);

// Get sales data for chart
$sales_data = $db->query("SELECT DATE(order_date) as date, SUM(total_amount) as total 
                          FROM orders 
                          WHERE order_date >= DATE_SUB(NOW(), INTERVAL 7 DAY) 
                          GROUP BY DATE(order_date) 
                          ORDER BY date")->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Admin Panel</title>
    <link rel="stylesheet" href="css/style.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body>
    <?php include('includes/header.php'); ?>
    
    <div class="container">
        <?php include('includes/sidebar.php'); ?>
        
        <main class="main-content">
            <h1>Dashboard</h1>
            
            <div class="stats-grid">
                <div class="stat-card">
                    <h3>Total Users</h3>
                    <p class="stat-number"><?php echo $users_count; ?></p>
                </div>
                <div class="stat-card">
                    <h3>Total Products</h3>
                    <p class="stat-number"><?php echo $products_count; ?></p>
                </div>
                <div class="stat-card">
                    <h3>Total Orders</h3>
                    <p class="stat-number"><?php echo $orders_count; ?></p>
                </div>
                <div class="stat-card">
                    <h3>Total Revenue</h3>
                    <p class="stat-number">$<?php echo number_format($revenue, 2); ?></p>
                </div>
            </div>
            
            <div class="charts-grid">
                <div class="chart-container">
                    <h3>Sales Last 7 Days</h3>
                    <canvas id="salesChart"></canvas>
                </div>
                <div class="chart-container">
                    <h3>Order Status</h3>
                    <canvas id="ordersChart"></canvas>
                </div>
            </div>
            
            <div class="recent-orders">
                <h3>Recent Orders</h3>
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>Order ID</th>
                            <th>Customer</th>
                            <th>Amount</th>
                            <th>Status</th>
                            <th>Date</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach($recent_orders as $order): ?>
                        <tr>
                            <td>#<?php echo $order['id']; ?></td>
                            <td><?php echo $order['username']; ?></td>
                            <td>$<?php echo number_format($order['total_amount'], 2); ?></td>
                            <td><span class="status status-<?php echo $order['status']; ?>"><?php echo ucfirst($order['status']); ?></span></td>
                            <td><?php echo date('M j, Y', strtotime($order['order_date'])); ?></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </main>
    </div>
    
    <script>
        // Sales Chart
        const salesCtx = document.getElementById('salesChart').getContext('2d');
        const salesChart = new Chart(salesCtx, {
            type: 'line',
            data: {
                labels: [<?php 
                    $dates = array_column($sales_data, 'date');
                    foreach($dates as $date) {
                        echo "'" . date('M j', strtotime($date)) . "',";
                    }
                ?>],
                datasets: [{
                    label: 'Sales ($)',
                    data: [<?php 
                        $totals = array_column($sales_data, 'total');
                        foreach($totals as $total) {
                            echo $total . ',';
                        }
                    ?>],
                    borderColor: '#3498db',
                    backgroundColor: 'rgba(52, 152, 219, 0.1)',
                    tension: 0.4,
                    fill: true
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        display: false
                    }
                }
            }
        });

        // Orders Chart (dummy data for demo)
        const ordersCtx = document.getElementById('ordersChart').getContext('2d');
        const ordersChart = new Chart(ordersCtx, {
            type: 'doughnut',
            data: {
                labels: ['Pending', 'Processing', 'Shipped', 'Delivered', 'Cancelled'],
                datasets: [{
                    data: [12, 19, 3, 5, 2],
                    backgroundColor: [
                        '#f39c12',
                        '#3498db',
                        '#9b59b6',
                        '#2ecc71',
                        '#e74c3c'
                    ]
                }]
            }
        });
    </script>
    
    <?php include('includes/footer.php'); ?>
</body>
</html>