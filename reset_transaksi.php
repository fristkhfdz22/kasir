
<?php
session_start();

// Koneksi ke database
include('config.php');

// Query untuk menghapus semua data transaksi
$query_reset_transaksi = "DELETE FROM transaksi";

// Menjalankan query untuk menghapus data transaksi
if ($conn->query($query_reset_transaksi) === TRUE) {
    // Menambahkan pesan untuk menunjukkan bahwa transaksi telah direset
    $_SESSION['message'] = "Semua transaksi telah direset.";
} else {
    $_SESSION['message'] = "Terjadi kesalahan saat mereset transaksi: " . $conn->error;
}

// Redirect kembali ke halaman transaksi setelah reset
header("Location: dashboard.php");
exit();
?>
