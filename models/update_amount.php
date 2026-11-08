<?php

include "../database/db.php";

$id = $_POST["id"];
$amountToAdd = $_POST["amount"];

// Retrieve the existing amount from the database
$sqlSelect = "SELECT amount FROM leads WHERE id = '$id'";
$resultSelect = $conn->query($sqlSelect);

if ($resultSelect->num_rows > 0) {
    $row = $resultSelect->fetch_assoc();
    $existingAmount = $row["amount"];

    // Calculate the new amount by adding the received amount to the existing amount
    $newAmount = $existingAmount + $amountToAdd;

    // Update the amount in the database
    $sqlUpdate = "UPDATE leads SET amount='$newAmount', updated_at = NOW() WHERE id = '$id'";
    $sql2 = "INSERT INTO amount (amount, lead_id) VALUES ('$amountToAdd', '$id')";

    if ($conn->query($sqlUpdate) === TRUE && $conn->query($sql2) === TRUE) {
        // Redirect to the specified location
        $marketer = $_POST["marketer"];
        // $location = "../views/marketers.php?username=$marketer";
        // direct to the current page 
        $location = $_SERVER['HTTP_REFERER'];
        header("Location: $location");
    } else {
        // Handle the error if the update fails
        echo "Error updating record: " . $conn->error;
    }
} else {
    // Handle the case where the record with the specified ID is not found
    echo "Record not found";
}

$conn->close();
?>
