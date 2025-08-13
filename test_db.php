<?php
$pdo = new PDO("mysql:host=localhost;dbname=warehouse_db", "root", "");
$stmt = $pdo->query("SELECT COUNT(*) FROM products");
$count = $stmt->fetchColumn();
echo "Всего товаров: $count";
