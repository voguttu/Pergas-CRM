<?php

include "../database/db.php";

$location = '../views/todo.php';
$id = $_GET['id'];

$query = "DELETE FROM remindernotes WHERE id = '$id';";

$result = mysqli_query($conn, $query);
if ($result) {
    mysqli_close($conn);
    header("location: $location?message=deleted");
} else {
    echo "Error deleting reminder";
}

$conn->close();
?>