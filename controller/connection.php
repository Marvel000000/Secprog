<?php
    require_once "../config/config.php";

    $conn = new mysqli(
        $config["server"],
        $config["username"],
        $config["password"],
        $config["database"]
    );

    if ($conn->connect_error) {
         die("Cannot connect to database.");
     }

?>