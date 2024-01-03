<?php
require_once "../controller/connection.php";
require_once "../controller/csrf.php";

// Initialize the session
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

$_SESSION['csrf_token'] = generateCsrfToken();
$csrf_token = $_SESSION['csrf_token'];

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link rel="stylesheet" href="../styles/login.css">
</head>

<body>

    <form action="../controller/loginAuth.php" method="post">
        <!-- Add the CSRF token as a hidden input field -->
        <input type="hidden" name="csrf_token" value="<?php echo $csrf_token; ?>">

        <h1>Login</h1>
        <?php
        // Display error message if it exists
        if (isset($_SESSION['login_error'])) {
            echo '<p class="error-message">' . htmlspecialchars($_SESSION['login_error']) . '</p>';
            unset($_SESSION['login_error']); // Clear the error message after displaying
        }
        ?>
        <label for="email">Email:</label>
        <input type="email" name="email" required>

        <label for="password">Password:</label>
        <input type="password" name="password" required>

        <p>Don't have an account? <a href="register.php">Register here</a></p>

        <input type="submit" value="Login">
    </form>

</body>

</html>
