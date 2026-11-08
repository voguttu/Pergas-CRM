<?php

include "../database/db.php";

$marketer = $_POST["marketer"];
$id = $_POST["id"];
$date = $_POST["date"];
$name = $_POST["name"];
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
$amount = $_POST["amount"];
$task = $_POST["task"];
$marketernote = $_POST["marketernote"];

$productList = implode(', ', $product);
// $location2 = "../views/marketers.php?username=$marketer";
$location2 = $_SERVER['HTTP_REFERER'];


$sql = "UPDATE leads SET amount='$amount', date='$date', possibility='$possibility', duration='$duration', phone='$phone', location='$location', connectivity='$connectivity', 
project='$project', product='$productList', intouch='$intouch', task='$task', marketernote='$marketernote', clienttype='$clienttype', status='$status', updated_at = NOW() WHERE id = '$id'";

if ($conn->query($sql) === TRUE) {
  header("Location: $location2");
} else {
  echo "Error: " . $sql . "<br>" . $conn->error;
}

$conn->close();
?>
