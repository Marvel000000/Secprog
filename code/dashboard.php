<?php
require_once '../controller/connection.php';

// Start the session
session_start();

// Check if the user is logged in
if (isset($_SESSION['loggedin'])) {
    $isLoggedIn = true;
    // Fetch all content from the database
    $sql = "SELECT id, title, image FROM content";
    $result = $conn->query($sql);

    // Check for success
    if ($result) {
        // Fetch associative array
        $contentList = $result->fetch_all(MYSQLI_ASSOC);
    } else {
        echo "Error: " . $conn->error;
    }

    // Close the connection (if not done automatically in connection.php)
    $conn->close();
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
</head>
<body>

    <header>
        <h1>Dashboard!</h1>

        <?php
        if ($isLoggedIn) {
            echo '<button onclick="window.location.href=\'update.php\'">Update Profile</button>';
            echo '<button onclick="window.location.href=\'../controller/logoutauth.php\'">Log Out</button>';
        } else {
            echo '<button onclick="window.location.href=\'login.php\'">Login</button>';
            echo '<button onclick="window.location.href=\'register.php\'">Register</button>';
        }
        ?>
    </header>

    <div class="custom-container">
        <h1>Content</h1>

        <?php
        // Check if content is available
        if ($isLoggedIn && !empty($contentList)) {
            echo "<ul>";
            foreach ($contentList as $content) {
                $contentId = $content['id'];
                $contentTitle = $content['title'];
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
        <?php if ($isLoggedIn) { ?>
            <button onclick="openModal()">Add Content</button>
        <?php } ?>

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

    <script>
        // JavaScript functions for modal
        function openModal() {
            document.getElementById('addContentModal').style.display = 'block';
        }

        function closeModal() {
            document.getElementById('addContentModal').style.display = 'none';
        }
    </script>

</body>
</html>



