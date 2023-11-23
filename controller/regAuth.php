<?php
require_once "./connection.php";

session_start(); // Start the session

function is_valid_email($email) {
    return filter_var($email, FILTER_VALIDATE_EMAIL);
}

function is_valid_password($password) {
    return preg_match('/^(?=.*\d)(?=.*[a-zA-Z])(?=.*[^a-zA-Z0-9]).{8,}$/', $password);
}

function is_valid_image($file) {
    $allowedExtensions = array("jpeg", "jpg", "png", "gif");

    if ($file["size"] > 5 * 1024 * 1024) { // 2 MB limit
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

function validateUsername($name) {
    return !empty($name) && strlen($name) <= 30 && !preg_match('/[\<\>\;\:\"\'\%]/', $name);
}

// Initialize error array
$errors = array();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Validate name
    $name = $_POST['name'];
    if (!validateUsername($name)) {
        $errors[] = "Invalid name.";
    }

    // Validate email
    $email = $_POST['email'];
    if (empty($email) || strlen($email) > 30 || preg_match('/[\<\>\;\:\"\'\%]/', $email) || !is_valid_email($email)) {
        $errors[] = "Invalid email.";
    }

    // Validate password
    $password = $_POST['password'];
    if (empty($password) || strlen($password) > 30 || preg_match('/[\<\>\;\:\"\'\%]/', $password) || !is_valid_password($password)) {
        $errors[] = "Invalid password.";
    }

    // Validate image
    $image = $_FILES["image"];
    if (!is_valid_image($image)) {
        $errors[] = "Invalid image.";
    }

    // If there are validation errors, store them in a session variable and redirect back to the registration form
    if (!empty($errors)) {
        $_SESSION['registration_errors'] = $errors;
        header("location: ../code/register.php");
        exit;
    } else {
        // If no validation errors, proceed with database insertion

        $stmt = $conn->prepare("INSERT INTO users (id, name, email, password, image) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("sssss", $id, $name, $email, $password, $image_name);

        $id = uniqid();
        $name = $_POST['name'];
        $email = $_POST['email'];
        $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
        
        // Generate a random string for the image name
        $image_name = uniqid('', true) . '.' . pathinfo($image["name"], PATHINFO_EXTENSION);

        if ($stmt->execute()) {
            $target_dir = "../images/";
            $target_file = $target_dir . $image_name;
            move_uploaded_file($image["tmp_name"], $target_file);

            // Redirect to login after successful insertion
            header("location: ../code/login.php");
            exit;
        } else {
            error_log("Error: " . $stmt->error);
            echo "Error during database insertion. Check the logs for more details.";
        }

        $stmt->close();
    }
}
?>
