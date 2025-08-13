<?php
header('Content-Type: application/json');

$conn = new mysqli('localhost', 'root', '', 'warehouse_db');
if ($conn->connect_error) {
    echo json_encode(["error" => "Ошибка подключения к базе данных"]);
    exit;
}

$conn->set_charset("utf8");

$name = isset($_POST['name']) ? trim($conn->real_escape_string($_POST['name'])) : '';

$sql = "
SELECT 
    p.id,
    p.code,
    p.name,
    p.quantity,
    p.length,
    p.width,
    p.height,
    w.section,
    w.row_num,
    w.shelf,
    w.place
FROM products p
LEFT JOIN warehouse_locations w ON p.location_id = w.location_id
WHERE LOWER(p.name) LIKE LOWER('%$name%')
";

$result = $conn->query($sql);

if (!$result || $result->num_rows === 0) {
    echo json_encode(["error" => "Товар не найден"]);
    exit;
}

$items = [];

while ($row = $result->fetch_assoc()) {
   $items[] = [
    "id" => $row["id"],
    "code" => $row["code"],
    "name" => $row["name"],
    "quantity" => $row["quantity"],
    "size" => $row["length"] . "x" . $row["width"] . "x" . $row["height"],
    "length" => $row["length"],
    "width" => $row["width"],
    "height" => $row["height"],
    "section" => $row["section"],
    "row" => $row["row_num"],
    "shelf" => $row["shelf"],
    "place" => $row["place"]
];

}

echo json_encode($items);
?>
