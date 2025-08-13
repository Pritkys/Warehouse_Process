<?php
$conn = new mysqli("localhost", "root", "", "warehouse_db");
$data = json_decode(file_get_contents("php://input"), true);

$length = $data["length"];
$width = $data["width"];
$height = $data["height"];

$sql = "SELECT location_id, place_length, place_width, place_height 
        FROM warehouse_locations 
        WHERE status = 'free'
          AND place_length >= $length 
          AND place_width >= $width 
          AND place_height >= $height";

$result = $conn->query($sql);
$locations = [];

while ($row = $result->fetch_assoc()) {
  $locations[] = [
    "location_id" => $row["location_id"],
    "length" => $row["place_length"],
    "width" => $row["place_width"],
    "height" => $row["place_height"]
  ];
}

echo json_encode($locations);
?>
