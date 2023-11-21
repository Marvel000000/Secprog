<?php
require_once "./connection.php";

// Initialize the session
session_start();

// Check if the user is already logged in, redirect to dashboard if true
if (isset($_SESSION["loggedin"]) && $_SESSION["loggedin"] === true) {
    header("location: ../code/dashboard.php");
    exit;
}

// Check if the form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Validate user input
    $email = $_POST["email"];
    $password = $_POST["password"];

    // Prepare and execute the SQL statement
    $stmt = $conn->prepare("SELECT id, name, password FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->store_result();

    // Check if the user exists
    if ($stmt->num_rows > 0) {
        // Bind the results
        $stmt->bind_result($user_id, $name, $hashed_password);
        $stmt->fetch();

        // Verify the password
        if (password_verify($password, $hashed_password)) {
            // Password is correct, start a new session
            session_start();

            // Store data in session variables
            $_SESSION["loggedin"] = true;
            $_SESSION["id"] = $user_id;
            $_SESSION["name"] = $name;
            $_SESSION["email"] = $email;

            // Redirect to the dashboard
            header("location: ../code/dashboard.php");
            exit;
        } else {
            $login_error = "Invalid password";
        }
    } else {
        $login_error = "User not found";
    }

    $stmt->close();
}

// Close the database connection
$conn->close();
?>
