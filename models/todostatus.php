<?php

include "../database/db.php";

$id = $_POST["id"];
$status = $_POST['status'];

$location = '../views/todo.php';

$sql = "UPDATE remindernotes SET status='$status' WHERE id = '$id'";

if ($conn->query($sql) === TRUE) {
    header("Location: $location?message=notemarked");
} else {
    echo "Error: " . $sql . "<br>" . $conn->error;
}

$conn->close();
?>
