<?php
require_once "./connection.php";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Prepare and bind parameters
    $stmt = $conn->prepare("INSERT INTO users (id, name, email, password, image) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("sssss", $id, $name, $email, $password, $image);

    // Set parameters and execute
    $id = uniqid();  // Generate a unique identifier (UUID)
    $name = $_POST['name'];
    $email = $_POST['email'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT); // Hash the password for security
    $image = basename($_FILES["image"]["name"]);

    if ($stmt->execute()) {
        // Upload image to a folder
        $target_dir = "../images/";
        $target_file = $target_dir . basename($_FILES["image"]["name"]);
        move_uploaded_file($_FILES["image"]["tmp_name"], $target_file);
        
        header("location: ../code/dashboard.php");
        exit;
    } else {
        // Log the error for debugging
        error_log("Error: " . $stmt->error);

        // Output an error message
        echo "Error during database insertion. Check the logs for more details.";
    }

    // Close statement
    $stmt->close();
}
?>
