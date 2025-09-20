<?php
include 'config.php';

$connection = new mysqli($host, $username, $password, $database);

if ($connection->connect_error) {
    die("Ошибка подключения: " . $connection->connect_error);
}

if (isset($_GET['id'])) {
    $id = intval($_GET['id']);
    
    $sql = "DELETE FROM tasks WHERE id = ?";
    $stmt = $connection->prepare($sql);
    $stmt->bind_param("i", $id);
    
    if ($stmt->execute()) {

    } else {
        die("Ошибка при удалении задачи: " . $connection->error);
    }
    
    $stmt->close();
}

$connection->close();

header("Location: index.php");
exit;
?>