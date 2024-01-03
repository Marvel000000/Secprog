    <?php
    require_once "./connection.php";
    require_once "./csrf.php";


    // Initialize the session
    if (session_status() == PHP_SESSION_NONE) {
        session_start();
    }



    // Check if the user is not logged in, redirect to login page
    if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
        header("location: ../code/login.php");
        exit;
    }
    $_SESSION['csrf_token'] = generateCsrfToken();
    $csrf_token = $_SESSION['csrf_token'];

    if ($_SERVER["REQUEST_METHOD"] == "POST") {

        $csrf_token = $_POST['csrf_token'];
        if (!validateCsrfToken($csrf_token)) {
            die("CSRF token validation failed. Access denied.");
        }
        // Function to validate email
        function is_valid_email($email) {
            return filter_var($email, FILTER_VALIDATE_EMAIL);
        }

        // Function to validate password
        function is_valid_password($password) {
            return preg_match('/^(?=.*\d)(?=.*[a-zA-Z])(?=.*[^a-zA-Z0-9]).{8,}$/', $password);
        }

        // Function to validate image
        function is_valid_image($file) {
            $allowedExtensions = array("jpeg", "jpg", "png", "gif");

            if ($file["size"] > 2 * 1024 * 1024) { // 2 MB limit
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

        // Prepare and bind parameters
        $stmt = $conn->prepare("UPDATE users SET name = COALESCE(?, name), email = COALESCE(?, email), password = COALESCE(?, password), image = COALESCE(?, image) WHERE id = ?");
        $stmt->bind_param("ssssi", $new_name, $new_email, $new_password, $new_image, $user_id);

        // Set parameters
        $new_name = !empty($_POST['new_name']) ? $_POST['new_name'] : null;
        $new_email = !empty($_POST['new_email']) ? $_POST['new_email'] : null;
        $new_password = !empty($_POST['new_password']) ? password_hash($_POST['new_password'], PASSWORD_DEFAULT) : null;
        $new_image = !empty($_FILES["new_image"]["name"]) ? basename($_FILES["new_image"]["name"]) : null;
        $user_id = $_SESSION["id"];

        $errors = array();

        // Validate new name
        if (!empty($new_name) && (strlen($new_name) > 30 || preg_match('/[\<\>\;\:\"\'\%]/', $new_name))) {
            $errors[] = "Invalid name.";
        }

        // Validate new email
        if (!empty($new_email) && (strlen($new_email) > 30 || preg_match('/[\<\>\;\:\"\'\%]/', $new_email) || !is_valid_email($new_email))) {
            $errors[] = "Invalid email.";
        }

        // Validate new password
        if (!empty($new_password) && (strlen($new_password) > 30 || preg_match('/[\<\>\;\:\"\'\%]/', $new_password) || !is_valid_password($new_password))) {
            $errors[] = "Invalid password.";
        }

        // Validate new image
        if (!empty($_FILES["new_image"]["name"]) && !is_valid_image($_FILES["new_image"])) {
            $errors[] = "Invalid image.";
        }

        // Check if the previous password is provided and validate it
        if (!empty($_POST['prev_password'])) {
            $stmt_prev = $conn->prepare("SELECT password FROM users WHERE id = ?");
            $stmt_prev->bind_param("i", $user_id);
            $stmt_prev->execute();
            $stmt_prev->bind_result($prev_password);
            $stmt_prev->fetch();

            if (!password_verify($_POST['prev_password'], $prev_password)) {
                $errors[] = "Previous password is incorrect.";
            }

            $stmt_prev->close();
        }

        if (empty($errors)) {
            // If no errors, execute the update

            if ($stmt->execute()) {
                // Upload new image to a folder if provided
                if (!empty($_FILES["new_image"]["name"])) {
                    $target_dir = "../images/";
                    $target_file = $target_dir . basename($_FILES["new_image"]["name"]);
                    move_uploaded_file($_FILES["new_image"]["tmp_name"], $target_file);
                }

                header("location: ../code/dashboard.php");
            } else {
                $_SESSION["error"] = "Error: " . $stmt->error;
                header("location: ../code/update.php");
            }
        } else {
            // If there are errors, store them in session and redirect to update.php
            $_SESSION["errors"] = $errors;
            header("location: ../code/update.php");
        }

        // Close statement
        $stmt->close();
    }

    // Close the database connection
    $conn->close();
    ?>
