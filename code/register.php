<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Registration</title>
    <link rel="stylesheet" href="styles.css">
    <link rel="stylesheet" href="../style/register.css">
</head>
<body>
    <form action="../controllers/regAuth.php" method="post" enctype="multipart/form-data">
         <h2>User Registration Form</h2>

        <label for="name">Name:</label>
        <input type="text" name="name" required><br>

        <label for="email">Email:</label>
        <input type="email" name="email" required><br>

        <label for="password">Password:</label>
        <input type="password" name="password" required><br>

        <label for="image">Profile Image:</label>
        <input type="file" name="image" accept="image/*" required><br>

        <p>If you already have an account, <a href="login.php">login here</a>.</p>

        <input type="submit" value="Register">
    </form>
</body>
</html>
