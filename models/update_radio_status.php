<?php

include "../database/db.php";

$id = $_POST["id"];
$radio_status = (isset($_POST['radio_status']) && $_POST['radio_status'] === '1') ? '1' : '0';

// Capture the current URL
$current_url = $_SERVER['HTTP_REFERER'];

$sql = "UPDATE leads SET radio_status='$radio_status' WHERE id = '$id'";

if ($conn->query($sql) === TRUE) {
    header("Location: $current_url");
} else {
    echo "Error: " . $sql . "<br>" . $conn->error;
}

$conn->close();
?>
