<?php
require_once "./csrf.php";

// Start the session

if (isset($_SESSION['username'])) {
    // Get the username from the session
    $username = $_SESSION['username'];

    // Include your database connection file
    require_once "./connection.php";

    // Prepare and execute the SQL statement to delete the user's session from active_sessions
    $delete_session_stmt = $conn->prepare("DELETE FROM active_sessions WHERE username = ?");
    $delete_session_stmt->bind_param("s", $username);
    $delete_session_stmt->execute();
    $delete_session_stmt->close();
}

// Unset all session variables
$_SESSION = array();

// Destroy the session
session_destroy();

// Redirect to the login page or any other desired page after logout
header("location: ../code/login.php");
exit;
?>
