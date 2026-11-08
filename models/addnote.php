<?php

include "../database/db.php";

$note = $_POST["note"];

$location = '../views/managementnote.php';

$sql = "INSERT INTO managementnotes (note)
VALUES ('$note')";

if ($conn->query($sql) === TRUE) {
  header("Location: $location?message=noteadded");
} else {
  echo "Error: " . $sql . "<br>" . $conn->error;
}

$conn->close();
?>