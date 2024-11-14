<?php
session_start(); // Start the session

// Check if the user is already logged in
if (isset($_SESSION['username'])) {
    // If already logged in, redirect to dashboard immediately
    header("Location: dashboard.php");
    exit();
}

include('config.php'); // Include database connection

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Check if username or password is empty
    if (empty($username) || empty($password)) {
        $error_message = 'Username or password cannot be empty';
    } else {
        // Query to check the username
        $query = "SELECT * FROM pengguna WHERE username = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("s", $username); // Prevent SQL injection
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            if (MD5($password) == $row['kata_sandi']) {
                // Password matched, set session variables
                $_SESSION['username'] = $username;
                $_SESSION['id'] = $row['id'];
                header("Location: dashboard.php"); // Redirect to dashboard
                exit();
            } else {
                $error_message = 'Incorrect password';
            }
        } else {
            $error_message = 'Username not found';
        }

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

        <!-- Display error message if exists -->
        <?php if (isset($error_message)): ?>
            <div class="alert alert-danger mt-3">
                <?php echo $error_message; ?>
            </div>
        <?php endif; ?>

        <!-- Login form -->
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
