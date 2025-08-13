<?php
$host = 'localhost';
$db = 'warehouse_db';
$user = 'root'; // стандартный логин в OpenServer
$pass = '';     // если ты не менял пароль в OpenServer

$conn = new mysqli($host, $user, $pass, $db);
$conn->set_charset("utf8");

if ($conn->connect_error) {
    die("Ошибка подключения: " . $conn->connect_error);
}
?>
