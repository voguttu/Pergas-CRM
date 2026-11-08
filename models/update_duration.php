<?php

include "../database/db.php";

$id = $_POST["id"];
$duration = $_POST["duration"];
$marketer = $_POST["marketer"];

// $location = "../views/marketers.php?username=$marketer";
$location = $_SERVER['HTTP_REFERER'];

// Check if the current duration is 'quoted'
$sql_check_duration = "SELECT duration FROM leads WHERE id = '$id'";
$result = $conn->query($sql_check_duration);

if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $current_duration = $row['duration'];

    $sql1 = "UPDATE leads SET duration='$duration', updated_at = NOW() WHERE id = '$id'";


    if ($conn->query($sql1) === TRUE) {
        header("Location: $location");
    } else {
        echo "Error: " . $sql1 . "<br>" . $conn->error;
    }
}

$conn->close();

?>
