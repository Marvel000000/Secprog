<?php
require_once "../controller/connection.php";
require_once "../controller/csrf.php";

// Initialize the session
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}



// Generate and store CSRF token
$_SESSION['csrf_token'] = generateCsrfToken();
$csrf_token = $_SESSION['csrf_token'];

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Update Profile</title>
    <link rel="stylesheet" href="../styles/update.css">
</head>
<body>
    <div class="container">

        <form action="../controller/updateProfile.php" method="post" enctype="multipart/form-data">
        <input type="hidden" name="csrf_token" value="<?php echo $csrf_token; ?>">

            <!-- Update Name Section -->
            <h3>Update Name</h3>
            <label for="new_name">New Name:</label>
            <input type="text" name="new_name">

            <!-- Update Email Section -->
            <h3>Update Email</h3>
            <label for="new_email">New Email:</label>
            <input type="email" name="new_email">

            <!-- Update Password Section -->
            <h3>Update Password</h3>
            <label for="prev_password">Previous Password:</label>
            <input type="password" name="prev_password">
            <label for="new_password">New Password:</label>
            <input type="password" name="new_password">

            <!-- Update Profile Image Section -->
            <h3>Update Profile Image</h3>
            <label for="new_image">New Profile Image:</label>
            <input type="file" name="new_image" accept="image/*">

            <input type="submit" value="Update Profile">
        </form>
    </div>
</body>
</html>
