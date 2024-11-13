<?php
session_start();

if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

include('config.php');

// Query untuk mendapatkan informasi transaksi beserta detail barang
$query_transaksi_list = "
    SELECT t.kode_transaksi, b.nama_barang, b.merk, t.jumlah_beli, t.total_bayar, t.tanggal
    FROM transaksi t
    JOIN barang b ON t.kode_barang = b.kode_barang
    ORDER BY t.tanggal DESC";
$result_transaksi = $conn->query($query_transaksi_list);

// Inisialisasi total bayar
$total_bayar_all = 0;
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar Transaksi</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0-beta3/dist/css/bootstrap.min.css" rel="stylesheet">
    <script>
        function printPage() {
            window.print();
        }
    </script>
</head>
<body>
    <div class="container mt-4">
        <h2>Daftar Transaksi</h2>

        <table class="table table-bordered mt-4">
            <thead>
                <tr>
                    <th>Kode Transaksi</th>
                    <th>Nama Barang</th>
                    <th>Merk</th>
                    <th>Jumlah Beli</th>
                    <th>Total Bayar</th>
                    <th>Tanggal</th>
                </tr>
            </thead>
            <tbody>
                <?php
                if ($result_transaksi->num_rows > 0) {
                    while ($row = $result_transaksi->fetch_assoc()) {
                        // Tambahkan total bayar ke total bayar keseluruhan
                        $total_bayar_all += $row['total_bayar'];

                        echo "<tr>
                                <td>{$row['kode_transaksi']}</td>
                                <td>{$row['nama_barang']}</td>
                                <td>{$row['merk']}</td>
                                <td>{$row['jumlah_beli']}</td>
                                <td>Rp" . number_format($row['total_bayar'], 2, ',', '.') . "</td>
                                <td>{$row['tanggal']}</td>
                              </tr>";
                    }
                } else {
                    echo "<tr><td colspan='6' class='text-center'>Tidak ada transaksi ditemukan</td></tr>";
                }
                ?>
            </tbody>
        </table>

        <!-- Menampilkan total bayar untuk semua transaksi -->
        <div class="row">
            <div class="col text-end">
                <strong>Total Semua Bayar:</strong> Rp<?= number_format($total_bayar_all, 2, ',', '.') ?>
            </div>
        </div>

        <!-- Tombol Print -->
        <button class="btn btn-primary mt-4" onclick="printPage()">Cetak</button>
        <a href="dashboard.php" class="btn btn-secondary mt-4">Kembali ke Dashboard</a>

    </div>
</body>
</html>
