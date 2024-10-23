<?php
include '../auth/auth.php';
checkAuth();
include '../config/db.php';

// Mengaktifkan error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

$inventory_id = $_GET['id'] ?? '';
$inventory_data = [];
$storage_units = [];
$vendor_name = '';

// Mengambil data inventory berdasarkan ID
if ($inventory_id) {
    $stmt = $conn->prepare("SELECT * FROM inventory WHERE id = :id");
    $stmt->execute(['id' => $inventory_id]);
    $inventory_data = $stmt->fetch(PDO::FETCH_ASSOC);

    // Pastikan data inventory ditemukan
    if ($inventory_data) {
        // Dapatkan nama vendor berdasarkan vendor_id
        $stmt = $conn->prepare("SELECT nama FROM vendor WHERE id = :vendor_id");
        $stmt->execute(['vendor_id' => $inventory_data['vendor_id']]);
        $vendor = $stmt->fetch(PDO::FETCH_ASSOC);
        $vendor_name = $vendor['nama'] ?? 'Vendor tidak ditemukan';
    } else {
        $message = "Inventory tidak ditemukan!";
    }
}

// Mengambil data storage unit
$stmt = $conn->query("SELECT id, nama_gudang FROM storage_unit");
$storage_units = $stmt->fetchAll(PDO::FETCH_ASSOC);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Cek jika form disubmit untuk mengupdate inventory
    if (isset($_POST['submit_inventory'])) {
        $vendor_id = $inventory_data['vendor_id']; // Pastikan vendor_id tidak diubah

        // Dapatkan nama_gudang berdasarkan storage_unit_id yang dipilih
        $stmt = $conn->prepare("SELECT nama_gudang FROM storage_unit WHERE id = :storage_unit_id");
        $stmt->execute(['storage_unit_id' => $_POST['storage_unit_id']]);
        $storage_unit = $stmt->fetch();

        if ($storage_unit) {
            // Update data inventory
            $stmt = $conn->prepare("UPDATE inventory SET jenis_barang = :jenis_barang, 
                                    kuantitas_stok = :kuantitas_stok, storage_unit_id = :storage_unit_id, 
                                    lokasi_gudang = :lokasi_gudang, harga = :harga, barcode = :barcode WHERE id = :id");
            $stmt->execute([
                'jenis_barang' => $_POST['jenis_barang'],
                'kuantitas_stok' => $_POST['kuantitas_stok'],
                'storage_unit_id' => $_POST['storage_unit_id'],
                'lokasi_gudang' => $storage_unit['nama_gudang'],
                'harga' => $_POST['harga'],
                'barcode' => $_POST['barcode'],
                'id' => $inventory_id,
            ]);

            $message = "Inventory berhasil diperbarui!";
            header("Location: inventory_list.php");
            exit();
        } else {
            $message = "Storage unit tidak ditemukan!";
        }
    }
}
?>

<?php include '../partials/header.php';?>
<?php include '../partials/sidebar.php';?>

<div class="container-fluid">
    <div class="row">
        <div class="col-md-9">
            <h1 class="mt-4">Edit Inventory</h1>

            <?php if (isset($message)): ?>
                <div class="alert alert-success">
                    <?php echo $message; ?>
                </div>
            <?php endif; ?>

            <!-- Form untuk mengedit inventory -->
            <form method="POST">
                <div class="form-group">
                    <label for="nama_barang">Nama Barang</label>
                    <input type="text" class="form-control" value="<?= htmlspecialchars($inventory_data['nama_barang']) ?>" disabled>
                </div>

                <div class="form-group">
                    <label for="vendor_id">Vendor</label>
                    <input type="text" class="form-control" value="<?= htmlspecialchars($vendor_name) ?>" disabled>
                </div>

                <div class="form-group">
                    <label for="jenis_barang">Jenis Barang</label>
                    <input type="text" name="jenis_barang" class="form-control" required value="<?= htmlspecialchars($_POST['jenis_barang'] ?? $inventory_data['jenis_barang']) ?>">
                </div>

                <div class="form-group">
                    <label for="kuantitas_stok">Kuantitas Stok</label>
                    <input type="number" name="kuantitas_stok" class="form-control" required value="<?= htmlspecialchars($_POST['kuantitas_stok'] ?? $inventory_data['kuantitas_stok']) ?>">
                </div>

                <!-- Dropdown untuk memilih storage unit -->
                <div class="form-group">
                    <label for="storage_unit_id">Lokasi Gudang</label>
                    <select name="storage_unit_id" class="form-control" required>
                        <?php foreach ($storage_units as $storage_unit): ?>
                            <option value="<?php echo $storage_unit['id']; ?>" <?php echo ($storage_unit['id'] == $inventory_data['storage_unit_id']) ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($storage_unit['nama_gudang']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="form-group">
                    <label for="harga">Harga</label>
                    <input type="number" name="harga" class="form-control" required value="<?= htmlspecialchars($_POST['harga'] ?? $inventory_data['harga']) ?>">
                </div>

                <div class="form-group">
                    <label for="barcode">Barcode</label>
                    <input type="text" name="barcode" class="form-control" required value="<?= htmlspecialchars($_POST['barcode'] ?? $inventory_data['barcode']) ?>">
                </div>

                <button type="submit" name="submit_inventory" class="btn btn-primary">Update Inventory</button>
            </form>
        </div>
    </div>
</div>

<?php include '../partials/footer.php';?>
