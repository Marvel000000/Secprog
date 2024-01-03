<?php
// Include your connection and configuration files


require_once '../controller/connection.php';
require_once "../controller/csrf.php";

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

$_SESSION['csrf_token'] = generateCsrfToken();
$csrf_token = $_SESSION['csrf_token'];


if (isset($_SESSION['loggedin'])) {
       
        $id = $_GET['id'];
        $sql = "SELECT image, title, description, date FROM content WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("s", $id); // Assuming the ID is an integer, adjust the type if it's different
        $stmt->execute();
        $stmt->bind_result($image, $title, $description, $date);
        $stmt->fetch();
        $stmt->close();
        $conn->close();
       
} else {
    header("location: ../code/login.php");
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($title); ?></title>
    <link rel="stylesheet" type="text/css" href="../styles/viewcontent.css">
</head>
<body>

<custom-container>

    <img src="../image/<?php echo htmlspecialchars($image); ?>" alt="Image">
    <div>
        <h1><?php echo htmlspecialchars($title); ?></h1>
        <p><?php echo htmlspecialchars($description); ?></p>
        <p>Date: <?php echo htmlspecialchars($date); ?></p>
        <div class="button">
            <form action="../controller/delete.php" method="post" onsubmit="return confirm('Are you sure you want to delete this content?');">
                <input type="hidden" name="csrf_token" value="<?php echo $csrf_token; ?>">
                <input type="hidden" name="delete_id" value="<?php echo htmlspecialchars($id); ?>">
                <input type="hidden" name="delete_image" value="<?php echo htmlspecialchars($image); ?>">
                <button id="delete-button" type="submit">Delete</button>
            </form>
            <button id="back-button" onclick="window.location.href='./dashboard.php'">Back</button>
        </div>
    </div>

</custom-container>

</body>
</html>
