<?php
include '../config/db.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Retrieve and validate input
    $nama = trim($_POST['nama']);
    $kontak = trim($_POST['kontak']);
    $nama_barang = trim($_POST['nama_barang']);
    $nomor_invoice = trim($_POST['nomor_invoice']);
    
    $errors = [];

    // Basic validation
    if (empty($nama)) {
        $errors[] = "Nama vendor is required.";
    }
    if (empty($kontak)) {
        $errors[] = "Kontak vendor is required.";
    }
    if (empty($nama_barang)) {
        $errors[] = "Nama barang is required.";
    }
    if (empty($nomor_invoice)) {
        $errors[] = "Nomor invoice is required.";
    }

    if (empty($errors)) {
        try {
            $stmt = $conn->prepare("INSERT INTO vendor (nama, kontak, nama_barang, nomor_invoice)
                                    VALUES (:nama, :kontak, :nama_barang, :nomor_invoice)");
            $stmt->execute([    
                'nama' => $nama,
                'kontak' => $kontak,
                'nama_barang' => $nama_barang,
                'nomor_invoice' => $nomor_invoice,
            ]);
            // Redirect after successful insertion
            header('Location: vendor_list.php'); // Replace with the appropriate page
            exit;
        } catch (PDOException $e) {
            $errors[] = "Database error: " . $e->getMessage();
        }
    }
}
?>

<?php include '../partials/header.php';?>
<?php include '../partials/sidebar.php';?>

<div class="container">
    <h1 class="mt-4">Add Vendor</h1>

    <?php if (!empty($errors)): ?>
        <div class="alert alert-danger">
            <?php foreach ($errors as $error): ?>
                <p><?php echo htmlspecialchars($error); ?></p>
            <?php endforeach; ?>
        </div>
    <?php endif;?>

    <form action="" method="POST">
        <div class="form-group">
            <label for="nama">Nama Vendor</label>
            <input type="text" name="nama" id="nama" class="form-control" required>
        </div>
        <div class="form-group">
            <label for="kontak">Kontak Vendor</label>
            <input type="text" name="kontak" id="kontak" class="form-control" required>
        </div>
        <div class="form-group">
            <label for="nama_barang">Nama Barang</label>
            <input type="text" name="nama_barang" id="nama_barang" class="form-control" required>
        </div>
        <div class="form-group">
            <label for="nomor_invoice">Nomor Invoice</label>
            <input type="text" name="nomor_invoice" id="nomor_invoice" class="form-control" required>
        </div>
        <button type="submit" class="btn btn-primary">Add Vendor</button>
    </form>
</div>

<?php include '../partials/footer.php';?>
