<?php
// login.php

session_start();
include('config.php'); // Menghubungkan ke database

// Jika pengguna sudah login, langsung diarahkan ke dashboard
if (isset($_SESSION['username'])) {
    header("Location: dashboard.php");
    exit();
}

// Proses login saat form disubmit
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Mendapatkan input dari form
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Mengecek apakah username dan password kosong
    if (empty($username) || empty($password)) {
        $error_message = 'Username atau password tidak boleh kosong';
    } else {
        // Query untuk mencari pengguna yang sesuai
        $query = "SELECT * FROM pengguna WHERE username = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("s", $username); // Mengikat parameter username
        $stmt->execute();
        $result = $stmt->get_result();

        // Memeriksa apakah pengguna ditemukan
        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            // Verifikasi password yang di-hash dengan MD5
            if (MD5($password) == $row['kata_sandi']) {
                // Login berhasil, buat session
                $_SESSION['username'] = $username;
                $_SESSION['id'] = $row['id'];
                header("Location: dashboard.php"); // Arahkan ke dashboard
                exit();
            } else {
                $error_message = "Password salah.";
            }
        } else {
            $error_message = "Username tidak ditemukan.";
        }

        // Menutup koneksi
        $stmt->close();
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Kasir</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0-beta3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container">
        <h2 class="mt-5">Login Kasir</h2>
        
        <!-- Menampilkan pesan error jika ada -->
        <?php if (isset($error_message)): ?>
            <div class="alert alert-danger mt-3">
                <?php echo $error_message; ?>
            </div>
        <?php endif; ?>

        <form action="login.php" method="POST" class="mt-4">
            <div class="mb-3">
                <label for="username" class="form-label">Username</label>
                <input type="text" class="form-control" id="username" name="username" required>
            </div>
            <div class="mb-3">
                <label for="password" class="form-label">Password</label>
                <input type="password" class="form-control" id="password" name="password" required>
            </div>
            <button type="submit" class="btn btn-primary">Login</button>
        </form>
    </div>
</body>
</html>
