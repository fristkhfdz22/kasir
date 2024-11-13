<?php
include('config.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['kode_barang'])) {
    $kode_barang = $_POST['kode_barang'];

    // Ambil data barang berdasarkan kode_barang
    $query_barang = "SELECT * FROM barang WHERE kode_barang = '$kode_barang'";
    $result_barang = $conn->query($query_barang);
    $barang = $result_barang->fetch_assoc();

    if ($barang && $barang['stok'] > 0) {
        $jumlah_beli = 1;
        $harga_barang = $barang['harga'];

        // Cek apakah sudah ada transaksi dengan kode_barang yang sama
        $query_cek_transaksi = "SELECT * FROM transaksi WHERE kode_barang = '$kode_barang' LIMIT 1";
        $result_cek_transaksi = $conn->query($query_cek_transaksi);
        
        if ($result_cek_transaksi->num_rows > 0) {
            // Jika transaksi sudah ada, update jumlah beli dan total bayar
            $transaksi = $result_cek_transaksi->fetch_assoc();
            $kode_transaksi = $transaksi['kode_transaksi'];
            $jumlah_beli_baru = $transaksi['jumlah_beli'] + $jumlah_beli;
            $total_bayar_baru = $harga_barang * $jumlah_beli_baru;

            $query_update_transaksi = "UPDATE transaksi SET jumlah_beli = $jumlah_beli_baru, total_bayar = $total_bayar_baru WHERE kode_transaksi = '$kode_transaksi'";
            $conn->query($query_update_transaksi);
        } else {
            // Jika transaksi belum ada, buat transaksi baru
            $kode_transaksi = "TRX" . time();
            $total_bayar = $harga_barang * $jumlah_beli;

            $query_insert_transaksi = "INSERT INTO transaksi (kode_transaksi, kode_barang, jumlah_beli, total_bayar) 
                                       VALUES ('$kode_transaksi', '$kode_barang', '$jumlah_beli', '$total_bayar')";
            $conn->query($query_insert_transaksi);
        }

        // Kurangi stok barang
        $query_update_stok = "UPDATE barang SET stok = stok - $jumlah_beli WHERE kode_barang = '$kode_barang'";
        $conn->query($query_update_stok);
    }
}

// Arahkan kembali ke dashboard.php
header("Location: dashboard.php");
exit();
?>
