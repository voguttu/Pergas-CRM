<?php

include "../database/db.php";

$id = $_POST["id"];
$note = $_POST["note"];

$location = '../views/managementnote.php';

$sql = "UPDATE managementnotes SET note='$note' WHERE id = '$id' ";

if ($conn->query($sql) === TRUE) {
  header("Location: $location?message=updated");
} else {
  echo "Error: " . $sql . "<br>" . $conn->error;
}

$conn->close();
?>