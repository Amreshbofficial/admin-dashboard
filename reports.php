<?php
session_start();
if(!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

include('config/database.php');
$database = new Database();
$db = $database->getConnection();

// Get report data
$total_users = $db->query("SELECT COUNT(*) FROM users")->fetchColumn();
$total_products = $db->query("SELECT COUNT(*) FROM products")->fetchColumn();
$total_orders = $db->query("SELECT COUNT(*) FROM orders")->fetchColumn();
$total_revenue = $db->query("SELECT COALESCE(SUM(total_amount), 0) FROM orders WHERE status = 'delivered'")->fetchColumn();

// Monthly sales data
$monthly_sales = $db->query("SELECT 
    YEAR(order_date) as year,
    MONTH(order_date) as month,
    SUM(total_amount) as total,
    COUNT(*) as order_count
    FROM orders 
    WHERE order_date >= DATE_SUB(NOW(), INTERVAL 12 MONTH)
    GROUP BY YEAR(order_date), MONTH(order_date)
    ORDER BY year DESC, month DESC")->fetchAll(PDO::FETCH_ASSOC);

// Top products
$top_products = $db->query("SELECT 
    p.name,
    SUM(oi.quantity) as total_sold,
    SUM(oi.quantity * oi.unit_price) as revenue
    FROM order_items oi
    JOIN products p ON oi.product_id = p.id
    JOIN orders o ON oi.order_id = o.id
    WHERE o.status = 'delivered'
    GROUP BY p.id, p.name
    ORDER BY total_sold DESC
    LIMIT 10")->fetchAll(PDO::FETCH_ASSOC);

// Order status distribution
$order_status = $db->query("SELECT 
    status,
    COUNT(*) as count
    FROM orders 
    GROUP BY status")->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reports - Admin Panel</title>
    <link rel="stylesheet" href="css/style.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body>
    <?php include('includes/header.php'); ?>
    
    <div class="container">
        <?php include('includes/sidebar.php'); ?>
        
        <main class="main-content">
            <h1>Reports & Analytics</h1>
            
            <div class="stats-grid">
                <div class="stat-card">
                    <h3>Total Users</h3>
                    <p class="stat-number"><?php echo $total_users; ?></p>
                </div>
                <div class="stat-card">
                    <h3>Total Products</h3>
                    <p class="stat-number"><?php echo $total_products; ?></p>
                </div>
                <div class="stat-card">
                    <h3>Total Orders</h3>
                    <p class="stat-number"><?php echo $total_orders; ?></p>
                </div>
                <div class="stat-card">
                    <h3>Total Revenue</h3>
                    <p class="stat-number">$<?php echo number_format($total_revenue, 2); ?></p>
                </div>
            </div>
            
            <div class="charts-grid">
                <div class="chart-container">
                    <h3>Monthly Sales</h3>
                    <canvas id="monthlySalesChart"></canvas>
                </div>
                <div class="chart-container">
                    <h3>Order Status Distribution</h3>
                    <canvas id="orderStatusChart"></canvas>
                </div>
            </div>
            
            <div class="charts-grid">
                <div class="chart-container">
                    <h3>Top Selling Products</h3>
                    <canvas id="topProductsChart"></canvas>
                </div>
                <div class="chart-container">
                    <h3>Revenue by Product</h3>
                    <canvas id="revenueChart"></canvas>
                </div>
            </div>
            
            <div class="data-table-container">
                <h3>Monthly Sales Report</h3>
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>Month</th>
                            <th>Orders</th>
                            <th>Total Sales</th>
                            <th>Average Order Value</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach($monthly_sales as $sale): ?>
                        <tr>
                            <td><?php echo date('F Y', mktime(0, 0, 0, $sale['month'], 1, $sale['year'])); ?></td>
                            <td><?php echo $sale['order_count']; ?></td>
                            <td>$<?php echo number_format($sale['total'], 2); ?></td>
                            <td>$<?php echo number_format($sale['total'] / $sale['order_count'], 2); ?></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            
            <div class="data-table-container">
                <h3>Top Products</h3>
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>Product Name</th>
                            <th>Units Sold</th>
                            <th>Revenue</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach($top_products as $product): ?>
                        <tr>
                            <td><?php echo $product['name']; ?></td>
                            <td><?php echo $product['total_sold']; ?></td>
                            <td>$<?php echo number_format($product['revenue'], 2); ?></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </main>
    </div>
    
    <script>
        // Monthly Sales Chart
        const monthlySalesCtx = document.getElementById('monthlySalesChart').getContext('2d');
        const monthlySalesChart = new Chart(monthlySalesCtx, {
            type: 'bar',
            data: {
                labels: [<?php 
                    foreach(array_reverse($monthly_sales) as $sale) {
                        echo "'" . date('M Y', mktime(0, 0, 0, $sale['month'], 1, $sale['year'])) . "',";
                    }
                ?>],
                datasets: [{
                    label: 'Sales ($)',
                    data: [<?php 
                        foreach(array_reverse($monthly_sales) as $sale) {
                            echo $sale['total'] . ',';
                        }
                    ?>],
                    backgroundColor: '#3498db',
                    borderColor: '#2980b9',
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });

        // Order Status Chart
        const orderStatusCtx = document.getElementById('orderStatusChart').getContext('2d');
        const orderStatusChart = new Chart(orderStatusCtx, {
            type: 'pie',
            data: {
                labels: [<?php 
                    foreach($order_status as $status) {
                        echo "'" . ucfirst($status['status']) . "',";
                    }
                ?>],
                datasets: [{
                    data: [<?php 
                        foreach($order_status as $status) {
                            echo $status['count'] . ',';
                        }
                    ?>],
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

        // Top Products Chart
        const topProductsCtx = document.getElementById('topProductsChart').getContext('2d');
        const topProductsChart = new Chart(topProductsCtx, {
            type: 'bar',
            data: {
                labels: [<?php 
                    foreach($top_products as $product) {
                        echo "'" . addslashes($product['name']) . "',";
                    }
                ?>],
                datasets: [{
                    label: 'Units Sold',
                    data: [<?php 
                        foreach($top_products as $product) {
                            echo $product['total_sold'] . ',';
                        }
                    ?>],
                    backgroundColor: '#27ae60'
                }]
            },
            options: {
                indexAxis: 'y',
                responsive: true
            }
        });

        // Revenue Chart
        const revenueCtx = document.getElementById('revenueChart').getContext('2d');
        const revenueChart = new Chart(revenueCtx, {
            type: 'doughnut',
            data: {
                labels: [<?php 
                    foreach($top_products as $product) {
                        echo "'" . addslashes($product['name']) . "',";
                    }
                ?>],
                datasets: [{
                    data: [<?php 
                        foreach($top_products as $product) {
                            echo $product['revenue'] . ',';
                        }
                    ?>],
                    backgroundColor: [
                        '#3498db', '#e74c3c', '#2ecc71', '#f39c12', '#9b59b6',
                        '#1abc9c', '#34495e', '#d35400', '#c0392b', '#16a085'
                    ]
                }]
            }
        });
    </script>
    
    <?php include('includes/footer.php'); ?>
</body>
</html>