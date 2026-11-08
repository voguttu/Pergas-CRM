<?php

include "../database/db.php";

$id = $_GET['id'];
$marketer = $_GET['marketer'];

$location = "../views/marketers.php?username=$marketer";

// Delete from leads table
$query_leads = "DELETE FROM leads WHERE id = '$id';";
$result_leads = mysqli_query($conn, $query_leads);

// Delete from status table
$query_status = "DELETE FROM status WHERE lead_id = '$id';";
$result_status = mysqli_query($conn, $query_status);

// Delete from amount table
$query_amount = "DELETE FROM amount WHERE lead_id = '$id';";
$result_amount = mysqli_query($conn, $query_amount);

// Check if all deletions were successful
if ($result_leads && $result_status && $result_amount) {
    mysqli_close($conn);
    header("location: $location");
} else {
    echo "Error deleting record";
}

$conn->close();
?>
