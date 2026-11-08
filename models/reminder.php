<?php

include "../database/db.php";

// Capture and escape user input
$note = mysqli_real_escape_string($conn, $_POST['note']);
$user = mysqli_real_escape_string($conn, $_POST['user']);
$client = mysqli_real_escape_string($conn, $_POST['client']);
$reminderdate = mysqli_real_escape_string($conn, $_POST['reminderdate']);

$location = '../views/todo.php';

$sql = "INSERT INTO remindernotes (note, client, reminderdate, user)
VALUES ('$note', '$client', '$reminderdate', '$user')";

if ($conn->query($sql) === TRUE) {
  header("Location: $location?message=noteadded");
} else {
  echo "Error: " . $sql . "<br>" . $conn->error;
}

$conn->close();
?>