<?php

include "../database/db.php";

$id = $_POST["id"];
$product = $_POST["product"];
$marketer = $_POST["marketer"];

$productList = implode(', ', $product);
$location = $_SERVER['HTTP_REFERER'];

$sql1 = "UPDATE leads SET product='$productList', updated_at = NOW() WHERE id = '$id' ";

if ($conn->query($sql1) === TRUE) {
  header("Location: $location");
} else {
  echo "Error: " . $sql1 . "<br>" . $conn->error;
}

$conn->close();
?>
