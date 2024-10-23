<?php
include '../auth/auth.php';
checkAuth();
include '../config/db.php';
$tittle = "Vendor List";
// Proses hapus vendor
if (isset($_GET['delete'])) {
    $id = $_GET['delete'];

    // Hapus vendor
    $stmt = $conn->prepare("DELETE FROM vendor WHERE id = :id");
    $stmt->execute(['id' => $id]);

    $message = "Vendor deleted successfully!";
}

// Ambil daftar vendor
$stmt = $conn->query("SELECT * FROM vendor ORDER BY id DESC");
$vendors = $stmt->fetchAll();
?>

<?php include '../partials/header.php'; ?>
<?php include '../partials/sidebar.php'; ?>

<div class="container mt-4">
    <h1>Vendor List</h1>

    <?php if (isset($message)): ?>
        <div class="alert alert-success">
            <?php echo $message; ?>
        </div>
    <?php endif; ?>

    <div class="card mt-3">
        <div class="card-header">
            <h5>Vendor List</h5>
            <a href="add_vendor.php" class="btn btn-primary float-right">Add Vendor</a>
        </div>
        <div class="card-body">
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>No</th> <!-- Kolom untuk nomor urut -->
                        <th>Nama Vendor</th>
                        <th>Kontak</th>
                        <th>Nama Barang</th>
                        <th>Nomor Invoice</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    // Inisialisasi variabel untuk penomoran
                    $i = 1;
                    foreach ($vendors as $vendor): ?>
                        <tr>
                            <td><?php echo $i++; ?></td> <!-- Menampilkan nomor urut -->
                            <td><?php echo htmlspecialchars($vendor['nama']); ?></td>
                            <td><?php echo htmlspecialchars($vendor['kontak']); ?></td>
                            <td><?php echo htmlspecialchars($vendor['nama_barang']); ?></td>
                            <td><?php echo htmlspecialchars($vendor['nomor_invoice']); ?></td>
                            <td>
                                <a href="edit_vendor.php?id=<?php echo $vendor['id']; ?>" class="btn btn-warning btn-sm">Edit</a>
                                <a href="?delete=<?php echo $vendor['id']; ?>" class="btn btn-danger btn-sm">Delete</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php include '../partials/footer.php'; ?>
