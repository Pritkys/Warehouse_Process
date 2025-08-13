<?php
$conn = new mysqli("localhost", "root", "", "warehouse_db");
$data = json_decode(file_get_contents("php://input"), true);

$code = uniqid("P");
$name = $conn->real_escape_string($data["name"]);
$qty = (int)$data["qty"];
$length = (int)$data["length"];
$width = (int)$data["width"];
$height = (int)$data["height"];
$location_id = $conn->real_escape_string($data["location_id"]);

$insert = "INSERT INTO products (code, name, quantity, length, width, height, location_id) 
           VALUES ('$code', '$name', $qty, $length, $width, $height, '$location_id')";

$update = "UPDATE warehouse_locations SET status = 'occupied' WHERE location_id = '$location_id'";

$success = $conn->query($insert) && $conn->query($update);
echo json_encode(["success" => $success]);
?>
