<?php
session_start();

if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

include('config.php'); // Menghubungkan ke database

// Query untuk mendapatkan semua barang
$query_barang = "SELECT * FROM barang";
$result_barang = $conn->query($query_barang);

// Query untuk mendapatkan semua transaksi dengan informasi barang menggunakan JOIN
$query_transaksi = "
    SELECT transaksi.kode_transaksi, transaksi.kode_barang, 
           barang.nama_barang, barang.merk, transaksi.jumlah_beli, 
           barang.harga, transaksi.tanggal
    FROM transaksi
    JOIN barang ON transaksi.kode_barang = barang.kode_barang
    ORDER BY transaksi.kode_transaksi, transaksi.tanggal DESC";
$result_transaksi = $conn->query($query_transaksi);

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
        <h4>Selamat datang, <?php echo $_SESSION['username']; ?></h4>

        <!-- Menampilkan Pesan Error -->
        <?php if (isset($_SESSION['error_message'])): ?>
            <div class="alert alert-danger mt-4">
                <?php echo $_SESSION['error_message']; ?>
            </div>
            <?php unset($_SESSION['error_message']); ?>
        <?php endif; ?>

        <!-- Tabel Daftar Barang -->
        <a href="logout.php" class="btn btn-danger mb-3">Logout</a>
        <a href="barang.php" class="btn btn-primary mb-3">Barang</a>
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
                if ($result_barang->num_rows > 0) {
                    while($row = $result_barang->fetch_assoc()) {
                        echo "<tr>
                                <td>{$row['kode_barang']}</td>
                                <td>{$row['nama_barang']}</td>
                                <td>{$row['merk']}</td>
                                <td>Rp" . number_format($row['harga'], 2, ',', '.') . "</td>
                                <td>{$row['stok']}</td>
                                <td>
                                    <form action='proses_beli.php' method='POST'>
                                        <input type='hidden' name='kode_barang' value='{$row['kode_barang']}'>
                                        <button type='submit' class='btn btn-primary btn-sm'>Beli</button>
                                    </form>
                                </td>
                              </tr>";
                    }
                } else {
                    echo "<tr><td colspan='6' class='text-center'>Tidak ada barang ditemukan</td></tr>";
                }
                ?>
            </tbody>
        </table>

        <!-- Tabel Daftar Transaksi -->
        <h3 class="mt-5">Riwayat Transaksi</h3>
        <table class="table table-striped mt-4">
            <thead>
                <tr>
                    <th>Kode Transaksi</th>
                    <th>Kode Barang</th>
                    <th>Nama Barang</th>
                    <th>Merk</th>
                    <th>Jumlah Beli</th>
                    <th>Total Bayar</th>
                    <th>Tanggal</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $kode_transaksi_terakhir = null; // Variabel untuk menyimpan kode transaksi terakhir
                $total_bayar_transaksi = 0; // Variabel untuk total bayar transaksi
                if ($result_transaksi->num_rows > 0) {
                    while($row = $result_transaksi->fetch_assoc()) {
                        // Hitung total bayar untuk setiap barang
                        $total_bayar_per_barang = $row['harga'] * $row['jumlah_beli'];
                        $total_bayar_transaksi += $total_bayar_per_barang;

                        echo "<tr>
                                <td>{$row['kode_transaksi']}</td>
                                <td>{$row['kode_barang']}</td>
                                <td>{$row['nama_barang']}</td>
                                <td>{$row['merk']}</td>
                                <td>{$row['jumlah_beli']}</td>
                                <td>Rp" . number_format($total_bayar_per_barang, 2, ',', '.') . "</td>
                                <td>{$row['tanggal']}</td>
                                <td>
                                    <form action='proses_aksi_transaksi.php' method='POST'>
                                        <input type='hidden' name='kode_transaksi' value='{$row['kode_transaksi']}'>
                                        <input type='hidden' name='kode_barang' value='{$row['kode_barang']}'>
                                        <button type='submit' name='aksi' value='kurangi' class='btn btn-warning btn-sm'>Kurangi</button>
                                        <button type='submit' name='aksi' value='hapus' class='btn btn-danger btn-sm'>Hapus</button>
                                    </form>
                                </td>
                            </tr>";
                    }
                    // Tampilkan total bayar untuk transaksi
                    echo "<tr><td colspan='8' class='text-center'>
                            <strong>Total Bayar: Rp" . number_format($total_bayar_transaksi, 2, ',', '.') . "</strong>
                        </td></tr>";
                } else {
                    echo "<tr><td colspan='8' class='text-center'>Tidak ada transaksi ditemukan</td></tr>";
                }
                ?>
            </tbody>
        </table>

    </div>
</body>
</html>


 echo "<tr><td colspan='8' class='text-center'>
                    <a href='transaksi.php?kode_transaksi={$kode_transaksi_terakhir}' class='btn btn-success mt-3'>Checkout</a>
                </td></tr>";

                echo "<tr><td colspan='8' class='text-center'>
                    <a href='transaksi.php?kode_transaksi={$kode_transaksi_terakhir}' class='btn btn-success mt-3'>Checkout</a>
                </td></tr>";
        } else {
            echo "<tr><td colspan='8' class='text-center'>Tidak ada transaksi ditemukan</td></tr>";
        }