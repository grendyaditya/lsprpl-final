<?php
include '../auth/auth.php';
checkAuth();
include '../config/db.php';

// Ambil data storage unit berdasarkan ID
if (isset($_GET['id'])) {
    $id = $_GET['id'];
    $stmt = $conn->prepare("SELECT * FROM storage_unit WHERE id = :id");
    $stmt->execute(['id' => $id]);
    $storage_unit = $stmt->fetch();
}

// Proses update storage unit
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id = $_POST['id'];
    $nama_gudang = $_POST['nama_gudang'];
    $lokasi = $_POST['lokasi'];

    try {
        // Update storage unit
        $stmt = $conn->prepare("UPDATE storage_unit SET nama_gudang = :nama_gudang, lokasi = :lokasi WHERE id = :id");
        $stmt->execute([
            'nama_gudang' => $nama_gudang,
            'lokasi' => $lokasi,
            'id' => $id,
        ]);

        header("Location: storage_unit.php");
        exit(); // Ensure no further code is executed after redirect
        
    } catch (PDOException $e) {
        die("Error: " . $e->getMessage());
    }
}
?>

<?php include '../partials/header.php'; ?>
<?php include '../partials/sidebar.php'; ?>

<div class="container-fluid">
    <div class="row">
        <div class="col-md-9">
            <h1 class="mt-4">Edit Storage Unit</h1>

            <?php if (isset($message)): ?>
                <div class="alert alert-success">
                    <?php echo htmlspecialchars($message); ?>
                </div>
            <?php endif; ?>

            <div class="card mt-3">
                <div class="card-header">
                    <h5>Edit Storage Unit</h5>
                </div>
                <div class="card-body">
                    <form method="POST">
                        <input type="hidden" name="id" value="<?php echo htmlspecialchars($storage_unit['id']); ?>">
                        <div class="form-group">
                            <label for="nama_gudang">Nama Gudang</label>
                            <input type="text" name="nama_gudang" class="form-control" value="<?php echo htmlspecialchars($storage_unit['nama_gudang']); ?>" required>
                        </div>
                        <div class="form-group">
                            <label for="lokasi">Lokasi</label>
                            <input type="text" name="lokasi" class="form-control" value="<?php echo htmlspecialchars($storage_unit['lokasi']); ?>" required>
                        </div>
                        <button type="submit" class="btn btn-primary">Update Storage Unit</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include '../partials/footer.php'; ?>
