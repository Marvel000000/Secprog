<?php
require_once "./connection.php";

// Initialize the session
session_start();

// Check if the user is not logged in, redirect to login page
if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
    header("location: login.php");
    exit;
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Prepare and bind parameters
    $stmt = $conn->prepare("UPDATE users SET name = COALESCE(?, name), email = COALESCE(?, email), password = COALESCE(?, password), image = COALESCE(?, image) WHERE id = ?");
    $stmt->bind_param("ssssi", $new_name, $new_email, $new_password, $new_image, $user_id);

    // Set parameters
    $new_name = !empty($_POST['new_name']) ? $_POST['new_name'] : null;
    $new_email = !empty($_POST['new_email']) ? $_POST['new_email'] : null;
    $new_password = !empty($_POST['new_password']) ? password_hash($_POST['new_password'], PASSWORD_DEFAULT) : null;
    $new_image = !empty($_FILES["new_image"]["name"]) ? basename($_FILES["new_image"]["name"]) : null;
    $user_id = $_SESSION["id"];

    // Check if the previous password is provided and validate it
    if (!empty($_POST['prev_password'])) {
        $stmt_prev = $conn->prepare("SELECT password FROM users WHERE id = ?");
        $stmt_prev->bind_param("i", $user_id);
        $stmt_prev->execute();
        $stmt_prev->bind_result($prev_password);
        $stmt_prev->fetch();

        if (!password_verify($_POST['prev_password'], $prev_password)) {
            echo "Error: Previous password is incorrect.";
            exit;
        }

        $stmt_prev->close();
    }

    if ($stmt->execute()) {
        // Upload new image to a folder if provided
        if (!empty($_FILES["new_image"]["name"])) {
            $target_dir = "../images/";
            $target_file = $target_dir . basename($_FILES["new_image"]["name"]);
            move_uploaded_file($_FILES["new_image"]["tmp_name"], $target_file);
        }

        header("location: ../code/dashboard.php");
    } else {
        echo "Error: " . $stmt->error;
    }

    // Close statement
    $stmt->close();
}

// Close the database connection
$conn->close();
?>
