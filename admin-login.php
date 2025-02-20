<?php
// Start session
session_start();

// Database configuration and connection
include 'db_connection.php';

$error_message = "";

// Check if form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Prepare and bind
    $stmt = $conn->prepare("SELECT id, password FROM admins WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $stmt->store_result();
    
    if ($stmt->num_rows > 0) {
        $stmt->bind_result($id, $hashed_password);
        $stmt->fetch();

        // Verify password
        if (password_verify($password, $hashed_password)) {
            // Set session variables
            $_SESSION['admin_id'] = $id;
            
            // Redirect to admin dashboard
            header("Location: admin_dashboard.php");
            exit(); // Ensure script termination after redirect
        } else {
            $error_message = "Invalid credentials";
        }
    } else {
        $error_message = "Invalid credentials";
    }

    $stmt->close();

}
// Close database connection
$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login</title>
    <link rel="stylesheet" href="admin_login.css">
</head>
<body>
    <header>
        <div class="logo">
            <a href="index.html"><img src="images/logo (1).png" alt=""></a>
        </div>
    </header>
    <div class="login-container">
        <h2>Admin Login</h2>
        <form id="loginForm" method="POST" action="admin-login.php">
            <div class="form-group">
                <label for="username">Username</label>
                <input type="text" id="username" name="username" required>
            </div>
            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" id="password" name="password" required>
            </div>
            <button type="submit">Login</button>
            <div id="message"><?php echo htmlspecialchars($error_message); ?></div>
        </form>
    </div>
    <script>
        document.getElementById('loginForm').addEventListener('submit', function(event) {
            var username = document.getElementById('username').value.trim();
            var password = document.getElementById('password').value.trim();

            if (username === '' || password === '') {
                event.preventDefault();
                document.getElementById('message').innerText = 'Please fill in both fields.';
            }
        });
    </script>
</body>
</html>
