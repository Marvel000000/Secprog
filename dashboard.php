<?php
require_once '../controller/connection.php';

// Start the session with secure settings
session_set_cookie_params([
    'secure' => true,
    'httponly' => true,
    'samesite' => 'Lax'
]);
session_start();

// Check if the user is logged in
if (isset($_SESSION['loggedin'])) {
    $isLoggedIn = true;

    // Fetch all content from the database using prepared statements
    $sql = "SELECT id, title, image FROM content";
    $stmt = $conn->prepare($sql);
    $stmt->execute();
    $result = $stmt->get_result();

    // Check for success
    if ($result) {
        // Fetch associative array
        $contentList = $result->fetch_all(MYSQLI_ASSOC);
    } else {
        error_log("Error: " . $stmt->error);
        echo "An error occurred. Please try again later.";
    }

    // Check if a search term is provided
    if (isset($_GET['search']) && !empty($_GET['search'])) {
        // Validate and sanitize search term
        $searchTerm = mysqli_real_escape_string($conn, $_GET['search']);

        // Perform the search query using prepared statements
        $searchSql = "SELECT id, title, image FROM content WHERE title LIKE ?";
        $searchStmt = $conn->prepare($searchSql);
        $searchStmt->bind_param("s", $searchTerm);
        $searchStmt->execute();
        $searchResult = $searchStmt->get_result();

        // Check for success
        if ($searchResult) {
            // Fetch associative array for search results
            $searchContentList = $searchResult->fetch_all(MYSQLI_ASSOC);

            // Check if there are search results
            if (!empty($searchContentList)) {
                $contentList = $searchContentList; // Use search results if available
            } else {
                $noSearchResult = true; // Flag to indicate no search results
            }
        } else {
            error_log("Error: " . $searchStmt->error);
            echo "An error occurred. Please try again later.";
        }
    }
} else {
    $isLoggedIn = false;
    echo "User is not logged in."; // Add this for debugging
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
    <link rel="stylesheet" type="text/css" href="../styles/dashboard.css">
    <script src="../assets/js.js"></script>

</head>

<body>

    <!-- header.php -->

    <nav class="navbar">
        <div class="logo-container">
            <a href="dashboard.php" class="logo-link">
                <h1 class="logo-text">Bodtrest</h1>
            </a>
        </div>

        <div class="search-container">
            <form action="dashboard.php" method="get">
                <input type="text" placeholder="Search..." name="search" value="<?php echo isset($searchTerm) ? htmlspecialchars($searchTerm) : ''; ?>">
                <button type="submit">Search</button>
            </form>
        </div>

        <div class="user-container">
            <?php if ($isLoggedIn): ?>
                <button onclick="window.location.href='update.php'">Update Profile</button>
                <button onclick="window.location.href='../controller/logoutauth.php'">Log Out</button>
            <?php else: ?>
                <button onclick="window.location.href='login.php'">Login</button>
                <button onclick="window.location.href='register.php'">Register</button>
            <?php endif; ?>
        </div>
    </nav>

    <div class="custom-container">
        <h1>Content</h1>

        <?php
        // Check if there are search results
        if (isset($noSearchResult) && $noSearchResult) {
            echo "<p>No search result.</p>";
        } elseif ($isLoggedIn && !empty($contentList)) {
            echo "<ul>";
            foreach ($contentList as $content) {
                $contentId = $content['id'];
                $contentTitle = htmlspecialchars($content['title']);
                $contentImage = $content['image'];

                // Create a list item with a clickable image and a link to the view content page for each item
                echo "<li>";
                echo "<div class=\"content-item\" onclick=\"window.location.href='viewcontent.php?id=$contentId'\">";
                echo "<img src=\"../image/$contentImage\" alt=\"$contentTitle\">";
                echo "<p>$contentTitle</p>";
                echo "</div>";
                echo "</li>";
            }
            echo "</ul>";
        } elseif (!$isLoggedIn) {
            echo "<p>Please log in to view content.</p>";
        } else {
            echo "<p>No content available.</p>";
        }
        ?>

        <!-- Add Content Form Modal Button -->
        <?php if ($isLoggedIn): ?>
            <button onclick="openModal()">Add Content</button>
        <?php endif; ?>

        <!-- Add Content Form Modal -->
        <div id="addContentModal" class="modal">
            <div class="modal-content">
                <span class="close" onclick="closeModal()">&times;</span>
                <h2>Add Content</h2>
                <form action="../controller/uploadAuth.php" method="post" enctype="multipart/form-data">
                    <label for="title">Title:</label>
                    <input type="text" id="title" name="title" required>

                    <label for="description">Description:</label>
                    <textarea id="description" name="description" required></textarea>

                    <label for="image">Image:</label>
                    <input type="file" id="image" name="image" accept="image/*" required>

                    <input type="submit" value="Add Content">
                </form>
            </div>
        </div>
    </div>
</body>

</html>