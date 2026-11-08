<?php

include "../database/db.php";

$id = $_POST["id"];
$status = $_POST["status"];
$marketer = $_POST["marketer"];

// $location = "../views/marketers.php?username=$marketer";
// direct to the current page 
$location = $_SERVER['HTTP_REFERER'];

// Check if the current status is 'quoted'
$sql_check_status = "SELECT status FROM leads WHERE id = '$id'";
$result = $conn->query($sql_check_status);

if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $current_status = $row['status'];

    // If the current status is 'quoted' and the new status is different, set amount to 0
    if ($current_status == 'quoted' && $status != 'quoted') {
        $sql1 = "UPDATE leads SET status='$status', amount=0, updated_at = NOW() WHERE id = '$id'";
    } else {
        $sql1 = "UPDATE leads SET status='$status', updated_at = NOW() WHERE id = '$id'";
    }

    $sql2 = "INSERT INTO status (status, lead_id) VALUES ('$status', '$id')";

    if ($conn->query($sql1) === TRUE && $conn->query($sql2) === TRUE) {
        header("Location: $location");
    } else {
        echo "Error: " . $sql1 . "<br>" . $conn->error;
        echo "Error: " . $sql2 . "<br>" . $conn->error;
    }
}

$conn->close();

?>
