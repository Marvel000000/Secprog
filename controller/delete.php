<?php
require_once "./connection.php";

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['delete_id'])) {
    // Prepare and bind parameters
    $stmt = $conn->prepare("DELETE FROM content WHERE id = ?");
    $stmt->bind_param("s", $id);

    // Set parameters and execute
    $id = $_POST['delete_id'];

    if ($stmt->execute()) {
        // Delete the corresponding image file from the folder
        $target_dir = "../images/";
        $target_file = $target_dir . $_POST['delete_image'];
        if (file_exists($target_file)) {
            unlink($target_file);
        }

        // Redirect to the dashboard after successful deletion
        header("location: ../code/dashboard.php");
        exit;
    } else {
        // Log the error for debugging
        error_log("Error: " . $stmt->error);

        // Output an error message
        echo "Error during database deletion. Check the logs for more details.";
    }

    // Close statement
    $stmt->close();
}
?>