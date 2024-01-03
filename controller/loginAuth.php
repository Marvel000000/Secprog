<?php
require_once "./connection.php";
require_once "./csrf.php";
// Initialize the session
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}


// Check if the user is already logged in, redirect to dashboard if true
if (isset($_SESSION["loggedin"])  === true) {
    header("location: ../code/dashboard.php");
    exit;
}

// Check if the form is submitted and the CSRF token is valid
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Validate CSRF token
    $csrf_token = $_POST['csrf_token'];
    if (!validateCsrfToken($csrf_token)) {
        die("CSRF token validation failed. Access denied.");
    }
    // Validate user input
    $email = $_POST["email"];
    $password = $_POST["password"];

    // Prepare and execute the SQL statement
    $stmt = $conn->prepare("SELECT id, email, password FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->store_result();

    // Check if the user exists
    if ($stmt->num_rows > 0) {
        // Bind the results
        $stmt->bind_result($user_id, $username, $hashed_password);
        $stmt->fetch();

        // Verify the password
        if (password_verify($password, $hashed_password)) {
            // Password is correct, start a new session

            // Check for double login (delete old session and add new session)
            $double_login_stmt = $conn->prepare("SELECT sessionID FROM active_sessions WHERE username = ?");
            $double_login_stmt->bind_param("s", $username);
            $double_login_stmt->execute();
            $double_login_stmt->store_result();

            if ($double_login_stmt->num_rows > 0) {
                // Delete the old session information
                $double_login_stmt->bind_result($old_session_id);
                $double_login_stmt->fetch();

                $delete_old_session_stmt = $conn->prepare("DELETE FROM active_sessions WHERE sessionID = ?");
                $delete_old_session_stmt->bind_param("s", $old_session_id);
                $delete_old_session_stmt->execute();
                $delete_old_session_stmt->close();
            }

            // Store data in session variables
            $_SESSION["loggedin"] = true;
            $_SESSION["id"] = $user_id;
            $_SESSION["username"] = $username;
            $_SESSION["email"] = $email;

            // Store session information in the active_sessions table
            $session_temp = session_id();
            $session_insert_stmt = $conn->prepare("INSERT INTO active_sessions (sessionID, username, last_login) VALUES (?, ?, NOW())");
            $session_insert_stmt->bind_param("ss", $session_temp, $username);
            $session_insert_stmt->execute();
            $session_insert_stmt->close();

            // Redirect to dashboard after successful login
            header("location: ../code/dashboard.php");
            exit;
        } else {
            // Set login error message in session
            $_SESSION['login_error'] = "Invalid password";
        }
    } else {
        // Set login error message in session
        $_SESSION['login_error'] = "User not found";
    }

    $stmt->close();
}

// If there's a login error, redirect to login page
header("location: ../code/login.php");
exit;
?>
