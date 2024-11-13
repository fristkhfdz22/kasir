<?php
// hapus_barang.php

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

    // Query untuk menghapus barang berdasarkan ID
    $query = "DELETE FROM barang WHERE id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $id);

    if ($stmt->execute()) {
        // Redirect ke halaman dashboard setelah barang dihapus
        header("Location: dashboard.php");
        exit();
    } else {
        echo "Terjadi kesalahan saat menghapus barang.";
    }

    $stmt->close();
}

$conn->close();
?>
