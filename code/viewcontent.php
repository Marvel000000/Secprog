<?php
// Include your connection and configuration files
require_once '../controller/connection.php';
$id = $_GET['id'];
$sql = "SELECT image, title, description, date FROM content WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $id); // Assuming the ID is an integer, adjust the type if it's different
$stmt->execute();
$stmt->bind_result($image, $title, $description, $date);
$stmt->fetch();
$stmt->close();
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $title; ?></title>
    <link rel="stylesheet" type="text/css" href="../styles/viewcontent.css">
</head>
<body>

    <custom-container>

        <img src="../image/<?php echo $image; ?>" alt="Image">
        <div>
            <h1><?php echo $title; ?></h1>
            <p><?php echo $description; ?></p>
            <p>Date: <?php echo $date; ?></p>
        </div>
        <button onclick="window.location.href='./dashboard.php'">Back</button>
    </custom-container>

</body>

</html>
