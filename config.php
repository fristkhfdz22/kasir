<?php
// File: config.php

// Konfigurasi Database
$host = 'localhost'; // atau IP server database Anda
$username = 'root';  // Username MySQL (sesuaikan jika menggunakan user lain)
$password = '';      // Password MySQL (sesuaikan dengan password Anda)
$dbname = 'new-kasir';   // Nama database yang telah dibuat

// Membuat koneksi
$conn = new mysqli($host, $username, $password, $dbname);

// Memeriksa koneksi
if ($conn->connect_error) {
    die("Koneksi gagal: " . $conn->connect_error);
}
?>
