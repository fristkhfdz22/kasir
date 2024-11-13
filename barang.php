<?php
// dashboard.php

session_start();

// Cek apakah pengguna sudah login
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

include('config.php'); // Menghubungkan ke database

// Cek apakah ada pencarian
$search_query = '';
if (isset($_POST['search'])) {
    $search_query = $_POST['search'];
}

// Query untuk mendapatkan barang berdasarkan pencarian (kode_barang atau nama_barang)
$query = "SELECT * FROM barang WHERE kode_barang LIKE '%$search_query%' OR nama_barang LIKE '%$search_query%'";
$result = $conn->query($query);

// Menutup koneksi
$conn->close();
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Kasir</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0-beta3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container">
        <h2 class="mt-5">Dashboard Kasir</h2>
        <?php
        echo "<h2>Selamat datang, " . $_SESSION['username'] . "</h2>";
        echo "<p>Ini adalah halaman dashboard kasir.</p>";
        ?>

        <!-- Form Pencarian -->
        <form method="POST" class="mb-3">
            <div class="input-group">
                <input type="text" class="form-control" name="search" placeholder="Cari barang..." value="<?php echo $search_query; ?>">
                <button class="btn btn-primary" type="submit">Cari</button>
            </div>
        </form>

        <!-- Tombol Tambah Barang -->
        <a href="dashboard.php" class="btn btn-warning">Kasir</a>
        <a href="tambah_barang.php" class="btn btn-primary mt-3">Tambah Barang</a>
        <a href="logout.php" class="btn btn-danger">Logout</a>

        <!-- Tabel Daftar Barang -->
        <table class="table table-striped mt-4">
            <thead>
                <tr>
                    <th>Kode Barang</th>
                    <th>Nama Barang</th>
                    <th>Merk</th>
                    <th>Harga</th>
                    <th>Stok</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php
                if ($result->num_rows > 0) {
                    // Menampilkan semua data barang
                    while($row = $result->fetch_assoc()) {
                        echo "<tr>
                                <td>{$row['kode_barang']}</td>
                                <td>{$row['nama_barang']}</td>
                                <td>{$row['merk']}</td>
                                <td>Rp" . number_format($row['harga'], 2, ',', '.') . "</td>
                                <td>{$row['stok']}</td>
                                <td>
                                    <a href='edit_barang.php?id={$row['id']}' class='btn btn-warning btn-sm'>Edit</a>
                                    <a href='hapus_barang.php?id={$row['id']}' class='btn btn-danger btn-sm'>Hapus</a>
                                </td>
                              </tr>";
                    }
                } else {
                    echo "<tr><td colspan='6' class='text-center'>Tidak ada barang ditemukan</td></tr>";
                }
                ?>
            </tbody>
        </table>
    </div>
</body>
</html>
