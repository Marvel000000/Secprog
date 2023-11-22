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
        <h1>Login</h1>
        <label for="email">Email:</label>
        <input type="email" name="email" required>

        <label for="password">Password:</label>
        <input type="password" name="password" required>

        <p>Don't have an account? <a href="register.php">Register here</a></p>

        <input type="submit" value="Login">
    </form>

</body>
</html>
