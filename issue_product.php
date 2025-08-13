<?php
$conn = new mysqli("localhost", "root", "", "warehouse_db");
$data = json_decode(file_get_contents("php://input"), true);

$code = $conn->real_escape_string($data["code"]);
$location_id = $conn->real_escape_string($data["location_id"]);
$quantity_to_issue = (int) $data["quantity"];

// Получаем текущий остаток
$res = $conn->query("SELECT quantity FROM products WHERE code = '$code'");
if (!$res || $res->num_rows === 0) {
    echo json_encode(["success" => false, "error" => "Товар не найден."]);
    exit;
}
$current_quantity = (int) $res->fetch_assoc()["quantity"];

if ($quantity_to_issue > $current_quantity) {
    echo json_encode(["success" => false, "error" => "Недостаточно товара на складе."]);
    exit;
}

if ($quantity_to_issue < $current_quantity) {
    // Просто уменьшаем количество
    $new_quantity = $current_quantity - $quantity_to_issue;
    $update = $conn->query("UPDATE products SET quantity = $new_quantity WHERE code = '$code'");
    echo json_encode(["success" => $update]);
} else {
    // Удаляем товар и освобождаем место
    $delete = $conn->query("DELETE FROM products WHERE code = '$code'");
    $free_place = $conn->query("UPDATE warehouse_locations SET status = 'free' WHERE location_id = '$location_id'");
    echo json_encode(["success" => ($delete && $free_place)]);
}
?>
