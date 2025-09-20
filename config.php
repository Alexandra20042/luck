<?php

// Данные для подключения
$host = "127.0.0.1";
$username = "sasha";
$password = "root";
$database = "task_manager";

// Создаем подключение
$connection = new mysqli($host, $username, $password, $database);

// Проверяем подключение
if ($connection->connect_error) {
    die("Ошибка подключения к базе данных: " . $connection->connect_error);
}

echo "✅ Подключение успешно установлено!";
?>