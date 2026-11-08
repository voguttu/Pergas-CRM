<?php

include "../database/db.php";

$location = '../views/managementnote.php';
$id = $_GET['id'];

$query = "DELETE FROM managementnotes WHERE id = '$id';";

$result = mysqli_query($conn, $query);
if ($result) {
    mysqli_close($conn);
    header("location: $location?message=deleted");
} else {
    echo "Error deleting record";
}

$conn->close();
?>