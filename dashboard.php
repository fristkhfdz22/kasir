<?php
session_start(); // Start session

// Periksa apakah pengguna sudah login
if (!isset($_SESSION['username'])) {
    // Jika belum login, arahkan ke halaman login
    header("Location: login.php");
    exit();
}

// Lanjutkan dengan proses dashboard jika sudah login
include('config.php');



$search = '';
if (isset($_POST['search'])) {
    $search = $_POST['search'];
}

$query_barang = "SELECT * FROM barang WHERE kode_barang LIKE '%$search%' OR nama_barang LIKE '%$search%'";
$result_barang = $conn->query($query_barang);

$query_transaksi = "SELECT transaksi.kode_transaksi, transaksi.kode_barang, barang.nama_barang, 
                    barang.merk, transaksi.jumlah_beli, barang.harga, transaksi.tanggal
                    FROM transaksi JOIN barang ON transaksi.kode_barang = barang.kode_barang 
                    ORDER BY transaksi.kode_transaksi, transaksi.tanggal DESC";
$result_transaksi = $conn->query($query_transaksi);

$conn->close();
?>

<!-- Your HTML Layout and Content go here -->

<!-- Including the navbar -->
<?php include('includes/header.php'); ?>

<?php include('includes/navbar.php'); ?>

<!-- Main Layout -->
<div id="layoutSidenav">
    <!-- Sidebar -->
    <?php include('includes/sidebar.php'); ?>

    <!-- Main Content -->
    <div id="layoutSidenav_content">
        <main>
            <div class="container-fluid px-4">
                <h1 class="mt-4">Dashboard Kasir</h1>
                <ol class="breadcrumb mb-4">
                    <li class="breadcrumb-item active">Dashboard</li>
                </ol>

                <div class="container">
                    <h2 class="mt-5">Dashboard Kasir</h2>
                    <h4>Welcome, <?php echo $_SESSION['username']; ?></h4>

                    <!-- Displaying Error Message -->
                    <?php if (isset($_SESSION['error_message'])): ?>
                        <div class="alert alert-danger mt-4">
                            <?php echo $_SESSION['error_message']; ?>
                        </div>
                        <?php unset($_SESSION['error_message']); ?>
                    <?php endif; ?>

                    <!-- Search Form -->
                    <form method="POST" class="mb-3">
                        <div class="input-group">
                            <input type="text" class="form-control" name="search" value="<?php echo htmlspecialchars($search); ?>" placeholder="Search for Item Code or Name">
                            <button class="btn btn-primary" type="submit">Search</button>
                        </div>
                    </form>

                    <!-- Item List Table -->
                    <a href="logout.php" class="btn btn-danger mb-3">Logout</a>
                    <a href="barang.php" class="btn btn-primary mb-3">Barang</a>
                    <table class="table table-striped mt-4">
                        <thead>
                            <tr>
                                <th>Item Code</th>
                                <th>Item Name</th>
                                <th>Brand</th>
                                <th>Price</th>
                                <th>Stock</th>
                                <th>Action</th>
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
                                                    <button type='submit' class='btn btn-primary btn-sm'>Buy</button>
                                                </form>
                                            </td>
                                          </tr>";
                                }
                            } else {
                                echo "<tr><td colspan='6' class='text-center'>No items found</td></tr>";
                            }
                            ?>
                        </tbody>
                    </table>

                    <!-- Transactions Table -->
                    <h3 class="mt-5">Transaction History</h3>
                    <table class="table table-striped mt-4">
                        <thead>
                            <tr>
                                <th>Transaction Code</th>
                                <th>Item Code</th>
                                <th>Item Name</th>
                                <th>Brand</th>
                                <th>Quantity</th>
                                <th>Total Payment</th>
                                <th>Date</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>         
                            <?php
                            $total_bayar_transaksi = 0;
                            if ($result_transaksi->num_rows > 0) {
                                while($row = $result_transaksi->fetch_assoc()) {
                                    // Calculate the total payment for each item
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
                                                    <button type='submit' name='aksi' value='kurangi' class='btn btn-warning btn-sm'>Reduce</button>
                                                    <button type='submit' name='aksi' value='hapus' class='btn btn-danger btn-sm'>Delete</button>
                                                </form>
                                            </td>
                                          </tr>";
                                }
                                // Display total payment for transactions
                                echo "<tr><td colspan='8' class='text-center'>
                                <strong>Total Payment: Rp" . number_format($total_bayar_transaksi, 2, ',', '.') . "</strong>
                              </td></tr>";

                            } else {
                                echo "<tr><td colspan='8' class='text-center'>No transactions found</td></tr>";
                            }
                            ?>
                            <form action="transaksi.php" method="POST">
                                <input type="hidden" name="checkout" value="1">
                                <button type="submit" class="btn btn-success mt-3">Checkout</button>
                            </form>
                            <form action="reset_transaksi.php" method="POST">
                                <button type="submit" class="btn btn-danger mt-4">Reset Transaction</button>
                            </form>
                        </tbody>
                    </table>

                </div>
            </div>
        </main>
    </div>
</div>

<!-- Including the footer -->
<?php include('includes/footer.php'); ?>
