<?php
include '../auth/auth.php';
checkAuth(); // Check if admin is logged in
include '../config/db.php'; // Database connection
$tittle = "Dashboard";
// Fetch total counts
$total_vendor = $conn->query("SELECT COUNT(*) FROM vendor")->fetchColumn();
$total_inventory = $conn->query("SELECT COUNT(*) FROM inventory")->fetchColumn();
$total_storage_unit = $conn->query("SELECT COUNT(*) FROM storage_unit")->fetchColumn();
?>



<?php include '../partials/header.php'; ?>
<?php include '../partials/sidebar.php'; ?>

<div class="container-fluid">
    <h1 class="mt-4">Dashboard</h1>
    <p>Welcome to the admin panel!</p>

    <div class="row mt-3">
        <div class="col-md-4">
            <div class="card text-white bg-info mb-3">
                <div class="card-header">Total Vendors</div>
                <div class="card-body">
                    <h5 class="card-title"><?php echo $total_vendor; ?></h5>
                    <a href="vendor_list.php" class="btn btn-light">Manage Vendors</a>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card text-white bg-success mb-3">
                <div class="card-header">Total Inventory Items</div>
                <div class="card-body">
                    <h5 class="card-title"><?php echo $total_inventory; ?></h5>
                    <a href="inventory_list.php" class="btn btn-light">Manage Inventory</a>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card text-white bg-warning mb-3">
                <div class="card-header">Total Storage Units</div>
                <div class="card-body">
                    <h5 class="card-title"><?php echo $total_storage_unit; ?></h5>
                    <a href="storage_unit_list.php" class="btn btn-light">Manage Storage Units</a>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include '../partials/footer.php'; ?>
</body>
</html>
