<?php

include "../database/db.php";

$id = $_GET['id'];

$query = "DELETE FROM login WHERE id = '$id';";

$location = '../views/addmarketer.php';

$result = mysqli_query($conn, $query);
if ($result) {
    mysqli_close($conn);
    header("location: $location?message=success");
} else {
    echo "Error deleting record";
}

$conn->close();
?>