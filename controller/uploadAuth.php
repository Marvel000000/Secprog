<?php
require_once "./connection.php";
require_once "./csrf.php";

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

$_SESSION['csrf_token'] = generateCsrfToken();
$csrf_token = $_SESSION['csrf_token'];

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

    $csrf_token = $_POST['csrf_token'];
    if (!validateCsrfToken($csrf_token)) {
        echo $csrf_token;

        echo $_SESSION['csrf_token'];
        die("CSRF token validation failed. Access denied.");
    }

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

        $stmt = $conn->prepare("INSERT INTO content (id, title, description, image) VALUES (?, ?, ?, ?)");
        if ($stmt === false) {
            die('Error in prepare statement: ' . htmlspecialchars($conn->error));
        }

        // Generate a random string for the id
        $id = substr(uniqid(), 0, 20);

        $stmt->bind_param("ssss", $id, $title, $description, $image_name);

        // Generate a random string for the image name
        $image_name = uniqid('', true) . '.' . pathinfo($image["name"], PATHINFO_EXTENSION);

        if ($stmt->execute()) {
            $target_dir = "../image/";
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
