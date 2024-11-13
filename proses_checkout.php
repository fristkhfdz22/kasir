<?php
session_start();
include('config.php'); // Menghubungkan ke database

if (isset($_GET['kode_transaksi'])) {
    $kode_transaksi = $_GET['kode_transaksi'];

    // Hapus data transaksi yang di-checkout atau set transaksi sebagai selesai
    $query = "DELETE FROM transaksi WHERE kode_transaksi = '$kode_transaksi'";
    if ($conn->query($query) === TRUE) {
        $_SESSION['success_message'] = "Transaksi berhasil di-checkout.";
    } else {
        $_SESSION['error_message'] = "Gagal melakukan checkout.";
    }
}

$conn->close();
header("Location: dashboard.php"); // Redirect kembali ke dashboard
exit();
