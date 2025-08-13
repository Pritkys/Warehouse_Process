<?php
header('Content-Type: application/json');

$conn = new mysqli('localhost', 'root', '', 'warehouse_db');
if ($conn->connect_error) {
    echo json_encode(["error" => "Ошибка подключения к базе данных"]);
    exit;
}

$conn->set_charset("utf8");

$section = isset($_POST['section']) ? $conn->real_escape_string($_POST['section']) : '';
$row = isset($_POST['row']) ? $conn->real_escape_string($_POST['row']) : '';
$shelf = isset($_POST['shelf']) ? $conn->real_escape_string($_POST['shelf']) : '';
$place = isset($_POST['place']) ? $conn->real_escape_string($_POST['place']) : '';

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
FROM warehouse_locations w
LEFT JOIN products p ON p.location_id = w.location_id
WHERE w.section = '$section' AND w.row_num = '$row' AND w.shelf = '$shelf' AND w.place = '$place'
";

$result = $conn->query($sql);

if (!$result || $result->num_rows === 0) {
    echo json_encode(["error" => "Товар не найден в этом местоположении"]);
    exit;
}

$items = [];

while ($row = $result->fetch_assoc()) {
    if (!$row["name"]) continue; // Если место занято, но товара нет — пропускаем

    $items[] = [
    "id" => $row["id"],
    "code" => $row["code"],
    "name" => $row["name"],
    "quantity" => $row["quantity"],
    "size" => $row["length"] . "x" . $row["width"] . "x" . $row["height"],
    "length" => $row["length"], // 🆕 добавлено
    "width" => $row["width"],   // 🆕 добавлено
    "height" => $row["height"], // 🆕 добавлено
    "section" => $row["section"],
    "row" => $row["row_num"],
    "shelf" => $row["shelf"],
    "place" => $row["place"]
];

}

echo json_encode($items);
?>
