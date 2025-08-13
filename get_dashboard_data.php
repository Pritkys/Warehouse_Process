<?php
header('Content-Type: application/json');
$pdo = new PDO("mysql:host=localhost;dbname=warehouse_db", "root", "");

// Получаем общее количество мест и количество занятых
$stmt = $pdo->query("SELECT COUNT(*) AS total FROM warehouse_locations");
$totalLocations = $stmt->fetch()['total'];

$stmt = $pdo->query("SELECT COUNT(*) AS occupied FROM warehouse_locations WHERE status = 'occupied'");
$occupiedLocations = $stmt->fetch()['occupied'];

$freeLocations = $totalLocations - $occupiedLocations;

// Получаем загрузку по секциям
$stmt = $pdo->query("
  SELECT section, COUNT(*) AS total_in_section,
    SUM(CASE WHEN status = 'occupied' THEN 1 ELSE 0 END) AS occupied_in_section
  FROM warehouse_locations
  GROUP BY section
");

$sections = [];
while ($row = $stmt->fetch()) {
    $percent = $row['total_in_section'] > 0
        ? round(($row['occupied_in_section'] / $row['total_in_section']) * 100, 1)
        : 0;
    $sections[] = [
        'section' => $row['section'],
        'percent' => $percent
    ];
}

// Получаем количество уникальных товаров
$stmt = $pdo->query("SELECT COUNT(DISTINCT code) AS unique_products FROM products");
$uniqueProducts = $stmt->fetch()['unique_products'] ?? 0;

// Получаем общее количество единиц товаров
$stmt = $pdo->query("SELECT SUM(quantity) AS total_units FROM products");
$totalUnits = $stmt->fetch()['total_units'] ?? 0;

// Отправляем JSON-ответ
echo json_encode([
    'total' => [
        'occupied' => (int)$occupiedLocations,
        'free' => (int)$freeLocations
    ],
    'sections' => $sections,
    'metrics' => [
        'unique_products' => (int)$uniqueProducts,
        'total_units' => (int)$totalUnits
    ]
]);
?>
