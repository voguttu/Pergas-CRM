<?php
include "../database/db.php";

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

$name = $_POST["name"];
$date = $_POST["date"];
$phone = $_POST["phone"];
$location = $_POST["location"];
$connectivity = $_POST["connectivity"];
$project = $_POST["project"];
$product = $_POST["product"];
$intouch = $_POST["intouch"];
$clienttype = $_POST["clienttype"];
$status = $_POST["status"];
$possibility = $_POST["possibility"];
$duration = $_POST["duration"];
$marketer = $_POST["marketer"];
$amount = $_POST["amount"];
// $task = $_POST["task"];

$task = $_POST["task"] ?? '';
$radio_status = $_POST['radio_status'] ?? 0;
$period = $_POST['period'] ?? 0;

$marketernote = $_POST["marketernote"];

$productList = implode(', ', $product);
$location2 = "../views/marketers.php?username=$marketer";

// Insert lead details into leads table
$sql = "INSERT INTO leads (date, clientName, phone, location, connectivity, project, clienttype, product, intouch, status, possibility, duration, marketer, amount, task, radio_status, period, marketernote, updated_at)
VALUES ('$date', '$name', '$phone', '$location', '$connectivity', '$project', '$clienttype', '$productList', '$intouch', '$status', '$possibility', '$duration', '$marketer', '$amount', '$task', '$radio_status', '$period', '$marketernote', NOW());";

if ($conn->query($sql) === TRUE) {
    // Retrieve the last inserted lead ID
    $lead_id = $conn->insert_id;
    
    // Insert status into status table
    $sql_status = "INSERT INTO status (lead_id, status) VALUES ('$lead_id', '$status')";
    $conn->query($sql_status);
    
    // Insert amount into amount table
    $sql_amount = "INSERT INTO amount (lead_id, amount) VALUES ('$lead_id', '$amount')";
    $conn->query($sql_amount);
    
    // Redirect back to the previous page with success message
    header("Location: $location2");
} else {
    echo "Error: " . $sql . "<br>" . $conn->error;
}

$conn->close();
?>
