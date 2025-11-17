<?php
session_start();
if(!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

include('config/database.php');
$database = new Database();
$db = $database->getConnection();

// Handle product actions
if(isset($_GET['action']) && $_GET['action'] == 'delete' && isset($_GET['id'])) {
    $id = $_GET['id'];
    $stmt = $db->prepare("DELETE FROM products WHERE id = ?");
    $stmt->execute([$id]);
    header("Location: products.php?message=Product deleted successfully");
    exit;
}

if($_POST) {
    if(isset($_POST['add_product'])) {
        $name = $_POST['name'];
        $description = $_POST['description'];
        $price = $_POST['price'];
        $category = $_POST['category'];
        $stock_quantity = $_POST['stock_quantity'];
        $status = $_POST['status'];
        
        $stmt = $db->prepare("INSERT INTO products (name, description, price, category, stock_quantity, status) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->execute([$name, $description, $price, $category, $stock_quantity, $status]);
        header("Location: products.php?message=Product added successfully");
        exit;
    }
    
    if(isset($_POST['update_product'])) {
        $id = $_POST['product_id'];
        $name = $_POST['name'];
        $description = $_POST['description'];
        $price = $_POST['price'];
        $category = $_POST['category'];
        $stock_quantity = $_POST['stock_quantity'];
        $status = $_POST['status'];
        
        $stmt = $db->prepare("UPDATE products SET name=?, description=?, price=?, category=?, stock_quantity=?, status=? WHERE id=?");
        $stmt->execute([$name, $description, $price, $category, $stock_quantity, $status, $id]);
        header("Location: products.php?message=Product updated successfully");
        exit;
    }
}

// Get all products
$products = $db->query("SELECT * FROM products ORDER BY created_at DESC")->fetchAll(PDO::FETCH_ASSOC);

// Get product for editing
$edit_product = null;
if(isset($_GET['action']) && $_GET['action'] == 'edit' && isset($_GET['id'])) {
    $id = $_GET['id'];
    $stmt = $db->prepare("SELECT * FROM products WHERE id = ?");
    $stmt->execute([$id]);
    $edit_product = $stmt->fetch(PDO::FETCH_ASSOC);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Products - Admin Panel</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <?php include('includes/header.php'); ?>
    
    <div class="container">
        <?php include('includes/sidebar.php'); ?>
        
        <main class="main-content">
            <h1>Product Management</h1>
            
            <?php if(isset($_GET['message'])): ?>
                <div class="alert alert-success"><?php echo $_GET['message']; ?></div>
            <?php endif; ?>
            
            <div class="form-container">
                <h3><?php echo $edit_product ? 'Edit Product' : 'Add New Product'; ?></h3>
                <form method="POST">
                    <?php if($edit_product): ?>
                        <input type="hidden" name="product_id" value="<?php echo $edit_product['id']; ?>">
                    <?php endif; ?>
                    
                    <div class="form-row">
                        <div class="form-field">
                            <label for="name">Product Name</label>
                            <input type="text" id="name" name="name" value="<?php echo $edit_product ? $edit_product['name'] : ''; ?>" required>
                        </div>
                        <div class="form-field">
                            <label for="category">Category</label>
                            <input type="text" id="category" name="category" value="<?php echo $edit_product ? $edit_product['category'] : ''; ?>" required>
                        </div>
                    </div>
                    
                    <div class="form-row">
                        <div class="form-field">
                            <label for="price">Price ($)</label>
                            <input type="number" id="price" name="price" step="0.01" min="0" value="<?php echo $edit_product ? $edit_product['price'] : ''; ?>" required>
                        </div>
                        <div class="form-field">
                            <label for="stock_quantity">Stock Quantity</label>
                            <input type="number" id="stock_quantity" name="stock_quantity" min="0" value="<?php echo $edit_product ? $edit_product['stock_quantity'] : ''; ?>" required>
                        </div>
                    </div>
                    
                    <div class="form-row">
                        <div class="form-field">
                            <label for="status">Status</label>
                            <select id="status" name="status" required>
                                <option value="active" <?php echo ($edit_product && $edit_product['status'] == 'active') ? 'selected' : ''; ?>>Active</option>
                                <option value="inactive" <?php echo ($edit_product && $edit_product['status'] == 'inactive') ? 'selected' : ''; ?>>Inactive</option>
                            </select>
                        </div>
                    </div>
                    
                    <div class="form-row">
                        <div class="form-field" style="flex: 1 1 100%;">
                            <label for="description">Description</label>
                            <textarea id="description" name="description" rows="4" required><?php echo $edit_product ? $edit_product['description'] : ''; ?></textarea>
                        </div>
                    </div>
                    
                    <div class="form-actions">
                        <?php if($edit_product): ?>
                            <button type="submit" name="update_product" class="btn btn-success">Update Product</button>
                            <a href="products.php" class="btn btn-primary">Cancel</a>
                        <?php else: ?>
                            <button type="submit" name="add_product" class="btn btn-success">Add Product</button>
                        <?php endif; ?>
                    </div>
                </form>
            </div>
            
            <div class="data-table-container">
                <h3>All Products</h3>
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Name</th>
                            <th>Category</th>
                            <th>Price</th>
                            <th>Stock</th>
                            <th>Status</th>
                            <th>Created</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach($products as $product): ?>
                        <tr>
                            <td><?php echo $product['id']; ?></td>
                            <td><?php echo $product['name']; ?></td>
                            <td><?php echo $product['category']; ?></td>
                            <td>$<?php echo number_format($product['price'], 2); ?></td>
                            <td><?php echo $product['stock_quantity']; ?></td>
                            <td><span class="status status-<?php echo $product['status']; ?>"><?php echo ucfirst($product['status']); ?></span></td>
                            <td><?php echo date('M j, Y', strtotime($product['created_at'])); ?></td>
                            <td class="action-buttons">
                                <a href="products.php?action=edit&id=<?php echo $product['id']; ?>" class="btn btn-primary btn-sm">Edit</a>
                                <a href="products.php?action=delete&id=<?php echo $product['id']; ?>" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure?')">Delete</a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </main>
    </div>
    
    <?php include('includes/footer.php'); ?>
</body>
</html>