<?php

include "../database/db.php";

$id = $_POST["id"];
$marketernote = $_POST["marketernote"];
$marketer = $_POST["marketer"];

// $location = "../views/marketers.php?username=$marketer";
$location = $_SERVER['HTTP_REFERER'];

$sql1 = "UPDATE leads SET marketernote='$marketernote', updated_at = NOW() WHERE id = '$id' ";

if ($conn->query($sql1) === TRUE) {
  header("Location: $location");
} else {
  echo "Error: " . $sql1 . "<br>" . $conn->error;
}

$conn->close();
?>
