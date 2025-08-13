<?php
$conn = new mysqli("localhost", "root", "", "warehouse_db");
$data = json_decode(file_get_contents("php://input"), true);

$code = $conn->real_escape_string($data["code"]);
$new_location = $conn->real_escape_string($data["new_location"]);

// Получаем текущее местоположение товара
$old_query = $conn->query("SELECT location_id FROM products WHERE code = '$code'");
if ($old = $old_query->fetch_assoc()) {
    $old_location = $old["location_id"];

    $update_product = "UPDATE products SET location_id = '$new_location' WHERE code = '$code'";
    $free_old = "UPDATE warehouse_locations SET status = 'free' WHERE location_id = '$old_location'";
    $occupy_new = "UPDATE warehouse_locations SET status = 'occupied' WHERE location_id = '$new_location'";

    $success = $conn->query($update_product) && $conn->query($free_old) && $conn->query($occupy_new);

    echo json_encode(["success" => $success]);
} else {
    echo json_encode(["success" => false, "error" => "Товар не найден"]);
}
?>