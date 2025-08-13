<?php
$host = 'localhost';
$db = 'warehouse_db';
$user = 'root';
$pass = '';
$charset = 'utf8mb4';

$location_id = $_GET['location'] ?? '';

$dsn = "mysql:host=$host;dbname=$db;charset=$charset";
$options = [
  PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
  PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
];

try {
  $pdo = new PDO($dsn, $user, $pass, $options);

  // Получаем товар по location_id
  $stmt = $pdo->prepare("SELECT * FROM products WHERE location_id = ?");
  $stmt->execute([$location_id]);
  $product = $stmt->fetch();

  if ($product) {
    // Получаем местоположение из второй таблицы
    $stmt2 = $pdo->prepare("SELECT section, row_num, shelf, place FROM warehouse_locations WHERE location_id = ?");
    $stmt2->execute([$location_id]);
    $location = $stmt2->fetch();

    if ($location) {
      $product = array_merge($product, $location);
    }

    echo json_encode($product);
  } else {
    echo json_encode(["error" => "Товар не найден по этому местоположению"]);
  }
} catch (\PDOException $e) {
  echo json_encode(["error" => "Ошибка подключения к БД: " . $e->getMessage()]);
}