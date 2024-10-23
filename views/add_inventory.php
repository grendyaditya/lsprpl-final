<?php
include '../auth/auth.php';
checkAuth();
include '../config/db.php';

// Mengaktifkan error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

$nama_barang = $_POST['nama_barang'] ?? '';
$vendor_options = [];
$storage_units = [];

// Mengambil data storage unit
$stmt = $conn->query("SELECT id, nama_gudang FROM storage_unit");
$storage_units = $stmt->fetchAll(PDO::FETCH_ASSOC);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Cek jika form disubmit untuk menambah inventory
    if (isset($_POST['submit_inventory'])) {
        $vendor_id = $_POST['vendor_id'];

        // Dapatkan nama_gudang berdasarkan storage_unit_id yang dipilih
        $stmt = $conn->prepare("SELECT nama_gudang FROM storage_unit WHERE id = :storage_unit_id");
        $stmt->execute(['storage_unit_id' => $_POST['storage_unit_id']]);
        $storage_unit = $stmt->fetch();

        if ($storage_unit) {
            // Masukkan data inventory dengan nama_barang dan lokasi_gudang tetap di inventory
            $stmt = $conn->prepare("INSERT INTO inventory (vendor_id, nama_barang, jenis_barang, kuantitas_stok, storage_unit_id, lokasi_gudang, harga, barcode)
                                    VALUES (:vendor_id, :nama_barang, :jenis_barang, :kuantitas_stok, :storage_unit_id, :lokasi_gudang, :harga, :barcode)");
            $stmt->execute([
                'vendor_id' => $vendor_id, // Ambil vendor_id dari input yang dipilih
                'nama_barang' => $_POST['nama_barang'],
                'jenis_barang' => $_POST['jenis_barang'],
                'kuantitas_stok' => $_POST['kuantitas_stok'],
                'storage_unit_id' => $_POST['storage_unit_id'],
                'lokasi_gudang' => $storage_unit['nama_gudang'], // Ambil nama_gudang sebagai lokasi_gudang
                'harga' => $_POST['harga'],
                'barcode' => $_POST['barcode'],
            ]);

            $message = "Inventory berhasil ditambahkan!";
            header("Location: inventory_list.php");
            exit();
        } else {
            $message = "Storage unit tidak ditemukan!";
        }
    }

    // Mengambil daftar vendor berdasarkan nama_barang yang dipilih
    if (!empty($nama_barang)) {
        $stmt = $conn->prepare("SELECT id AS vendor_id, nama FROM vendor WHERE nama_barang = :nama_barang");
        $stmt->execute(['nama_barang' => $nama_barang]);
        $vendor_options = $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
?>

<?php include '../partials/header.php';?>
<?php include '../partials/sidebar.php';?>

<div class="container-fluid">
    <div class="row">
        <div class="col-md-9">
            <h1 class="mt-4">Add Inventory</h1>

            <?php if (isset($message)): ?>
                <div class="alert alert-success">
                    <?php echo $message; ?>
                </div>
            <?php endif; ?>

            <!-- Form untuk menambahkan inventory -->
            <form method="POST">
                <div class="form-group">
                    <label for="nama_barang">Nama Barang</label>
                    <select name="nama_barang" id="nama_barang" class="form-control" onchange="this.form.submit()">
                        <option value="">Pilih Nama Barang</option>
                        <?php
                        // Mengambil daftar nama barang dari vendor
                        foreach ($conn->query("SELECT DISTINCT nama_barang FROM vendor") as $barang) {
                            $selected = ($barang['nama_barang'] == $nama_barang) ? 'selected' : '';
                            echo "<option value='" . htmlspecialchars($barang['nama_barang']) . "' $selected>" . htmlspecialchars($barang['nama_barang']) . "</option>";
                        }
                        ?>
                    </select>
                </div>

                <!-- Dropdown vendor hanya muncul jika nama barang dipilih -->
                <?php if ($vendor_options): ?>
                    <div class="form-group">
                        <label for="vendor_id">Vendor</label>
                        <select name="vendor_id" id="vendor_id" class="form-control">
                            <?php
                            foreach ($vendor_options as $option) {
                                echo "<option value='" . htmlspecialchars($option['vendor_id']) . "'>" . htmlspecialchars($option['nama']) . "</option>";
                            }
                            ?>
                        </select>
                    </div>
                <?php endif; ?>

                <div class="form-group">
                    <label for="jenis_barang">Jenis Barang</label>
                    <input type="text" name="jenis_barang" class="form-control" required value="<?= htmlspecialchars($_POST['jenis_barang'] ?? '') ?>">
                </div>

                <div class="form-group">
                    <label for="kuantitas_stok">Kuantitas Stok</label>
                    <input type="number" name="kuantitas_stok" class="form-control" required value="<?= htmlspecialchars($_POST['kuantitas_stok'] ?? '') ?>">
                </div>

                <!-- Dropdown untuk memilih storage unit -->
                <div class="form-group">
                    <label for="storage_unit_id">Lokasi Gudang</label>
                    <select name="storage_unit_id" class="form-control" required>
                        <?php foreach ($storage_units as $storage_unit): ?>
                            <option value="<?php echo $storage_unit['id']; ?>" <?php echo (isset($_POST['storage_unit_id']) && $_POST['storage_unit_id'] == $storage_unit['id']) ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($storage_unit['nama_gudang']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="form-group">
                    <label for="harga">Harga</label>
                    <input type="number" name="harga" class="form-control" required value="<?= htmlspecialchars($_POST['harga'] ?? '') ?>">
                </div>

                <div class="form-group">
                    <label for="barcode">Barcode</label>
                    <input type="text" name="barcode" class="form-control" required value="<?= htmlspecialchars($_POST['barcode'] ?? '') ?>">
                </div>

                <button type="submit" name="submit_inventory" class="btn btn-primary">Add Inventory</button>
            </form>
        </div>
    </div>
</div>

<?php include '../partials/footer.php';?>
