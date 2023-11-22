<?php
require_once "./connection.php";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Prepare and bind parameters
    $stmt = $conn->prepare("INSERT INTO content (id, title, description, image, date) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("sssss", $id, $title, $description, $image, $date);

    // Set parameters and execute
    $id = uniqid();  // Generate a unique identifier (UUID)
    $title = $_POST['title'];
    $description = $_POST['description'];
    $image = basename($_FILES["image"]["name"]);
    $date = date("Y-m-d H:i:s"); // Use the current date and time

    if ($stmt->execute()) {
        // Upload image to a folder
        $target_dir = "../image/";
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
