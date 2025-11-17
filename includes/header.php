<?php
if(!isset($_SESSION)) {
    session_start();
}
?>
<header class="header">
    <h1>Admin Dashboard</h1>
    <div class="user-info">
        <span>Welcome, <?php echo $_SESSION['full_name'] ?? $_SESSION['username']; ?></span>
        <a href="logout.php" class="btn btn-danger btn-sm">Logout</a>
    </div>
</header>