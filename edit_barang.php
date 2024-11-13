<?php
// edit_barang.php

session_start();

// Cek apakah pengguna sudah login
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

include('config.php'); // Menghubungkan ke database

// Cek apakah ada ID barang yang diberikan di URL
if (isset($_GET['id'])) {
    $id = $_GET['id'];

    // Query untuk mendapatkan data barang berdasarkan ID
    $query = "SELECT * FROM barang WHERE id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();

    // Jika barang ditemukan, ambil data barang tersebut
    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $kode_barang = $row['kode_barang'];
        $nama_barang = $row['nama_barang'];
        $merk = $row['merk'];
        $harga = $row['harga'];
        $stok = $row['stok'];
    } else {
        // Jika barang tidak ditemukan
        echo "Barang tidak ditemukan.";
        exit();
    }

    $stmt->close();
}

// Proses update barang jika form disubmit
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Mendapatkan input dari form
    $kode_barang = $_POST['kode_barang'];
    $nama_barang = $_POST['nama_barang'];
    $merk = $_POST['merk'];
    $harga = $_POST['harga'];
    $stok = $_POST['stok'];

    // Mengecek apakah input kosong
    if (empty($kode_barang) || empty($nama_barang) || empty($harga) || empty($stok)) {
        $error_message = "Semua kolom harus diisi.";
    } else {
        // Query untuk mengupdate barang berdasarkan ID
        $query = "UPDATE barang SET kode_barang = ?, nama_barang = ?, merk = ?, harga = ?, stok = ? WHERE id = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("sssssi", $kode_barang, $nama_barang, $merk, $harga, $stok, $id);

        // Eksekusi query
        if ($stmt->execute()) {
            $success_message = "Barang berhasil diupdate.";
        } else {
            $error_message = "Terjadi kesalahan: " . $stmt->error;
        }

        // Menutup koneksi
        $stmt->close();
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Barang</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0-beta3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container">
        <h2 class="mt-5">Edit Barang</h2>

        <!-- Menampilkan pesan error atau sukses -->
        <?php if (isset($error_message)): ?>
            <div class="alert alert-danger mt-3">
                <?php echo $error_message; ?>
            </div>
        <?php endif; ?>

        <?php if (isset($success_message)): ?>
            <div class="alert alert-success mt-3">
                <?php echo $success_message; ?>
            </div>
        <?php endif; ?>

        <!-- Form untuk mengedit barang -->
        <form action="edit_barang.php?id=<?php echo $id; ?>" method="POST" class="mt-4">
            <div class="mb-3">
                <label for="kode_barang" class="form-label">Kode Barang</label>
                <input type="text" class="form-control" id="kode_barang" name="kode_barang" value="<?php echo $kode_barang; ?>" required>
            </div>
            <div class="mb-3">
                <label for="nama_barang" class="form-label">Nama Barang</label>
                <input type="text" class="form-control" id="nama_barang" name="nama_barang" value="<?php echo $nama_barang; ?>" required>
            </div>
            <div class="mb-3">
                <label for="merk" class="form-label">Merk</label>
                <input type="text" class="form-control" id="merk" name="merk" value="<?php echo $merk; ?>">
            </div>
            <div class="mb-3">
                <label for="harga" class="form-label">Harga</label>
                <input type="number" class="form-control" id="harga" name="harga" value="<?php echo $harga; ?>" required>
            </div>
            <div class="mb-3">
                <label for="stok" class="form-label">Stok</label>
                <input type="number" class="form-control" id="stok" name="stok" value="<?php echo $stok; ?>" required>
            </div>
            <button type="submit" class="btn btn-success">Update Barang</button>
        </form>
    </div>
</body>
</html>
