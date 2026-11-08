<?php

include "../database/db.php";

$id = $_POST["id"];
$name = $_POST["name"];
$email = $_POST["email"];
$department = $_POST["department"];
$designation = $_POST["designation"];
$password = $_POST["password"];

// Hash the password
$hashedPassword = password_hash($password, PASSWORD_DEFAULT);

$location = '../views/addmarketer.php';

$sql = "UPDATE login SET username='$name', email='$email', password='$hashedPassword', designation='$designation', department='$department', updated_at = NOW() WHERE id = '$id' ";

if ($conn->query($sql) === TRUE) {
  header("Location: $location?message=updated");
} else {
  echo "Error: " . $sql . "<br>" . $conn->error;
}

$conn->close();
?>
