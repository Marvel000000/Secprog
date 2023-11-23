<?php
require_once "./connection.php";
session_start();

function is_valid_image($file) {
    $allowedExtensions = array("jpeg", "jpg", "png");

    if ($file["size"] > 5 * 1024 * 1024) { // 5 MB limit
        return false;
    }

    if ($file["size"] == 0) {
        return false;
    }

    $fileInfo = pathinfo($file["name"]);
    $extension = strtolower($fileInfo["extension"]);

    $imageType = exif_imagetype($file["tmp_name"]);
    if ($imageType === false) {
        return false;
    }

    $imageExtension = image_type_to_extension($imageType, false);

    return in_array($imageExtension, $allowedExtensions);
}

// Initialize error array
$errors = array();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Validate title
    $title = $_POST['title'];
    if (empty($title) || strlen($title) > 255) {
        $errors[] = "Invalid title.";
    }

    // Validate description
    $description = $_POST['description'];
    if (empty($description)) {
        $errors[] = "Invalid description.";
    }

    // Validate image
    $image = $_FILES["image"];
    if (!is_valid_image($image)) {
        $errors[] = "Invalid image.";
    }

    // If there are validation errors, store them in a session variable and redirect back to the upload form
    if (!empty($errors)) {
        $_SESSION['upload_errors'] = $errors;
        header("location: ../code/dashboard.php");
        exit;
    } else {
        // If no validation errors, proceed with database insertion

        $stmt = $conn->prepare("INSERT INTO content (title, description, image) VALUES (?, ?, ?)");
        if ($stmt === false) {
            die('Error in prepare statement: ' . htmlspecialchars($conn->error));
        }

        $stmt->bind_param("sss", $title, $description, $image_name);

        // Generate a random string for the image name
        $image_name = uniqid('', true) . '.' . pathinfo($image["name"], PATHINFO_EXTENSION);

        if ($stmt->execute()) {
            $target_dir = "../images/";
            $target_file = $target_dir . $image_name;
            move_uploaded_file($image["tmp_name"], $target_file);

            // Redirect to dashboard after successful insertion
            header("location: ../code/dashboard.php");
            exit;
        } else {
            error_log("Error: " . $stmt->error);
            echo "Error during database insertion. Check the logs for more details.";
        }

        $stmt->close();
    }
}
?>