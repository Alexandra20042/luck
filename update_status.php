<?php
include 'config.php';

$connection = new mysqli($host, $username, $password, $database);

if ($connection->connect_error) {
    die("Ошибка подключения: " . $connection->connect_error);
}

if (isset($_GET['id'])) {
    $id = intval($_GET['id']);
    
    $select_sql = "SELECT statis FROM tasks WHERE id = ?";
    $select_stmt = $connection->prepare($select_sql);
    $select_stmt->bind_param("i", $id);
    $select_stmt->execute();
    $select_stmt->bind_result($current_status);
    $select_stmt->fetch();
    $select_stmt->close();
    
    $new_status = ($current_status == 'выполнена') ? 'не выполнена' : 'выполнена';
    
    $update_sql = "UPDATE tasks SET statis = ? WHERE id = ?";
    $update_stmt = $connection->prepare($update_sql);
    $update_stmt->bind_param("si", $new_status, $id);
    
    if ($update_stmt->execute()) {
    } else {
        die("Ошибка при обновлении задачи: " . $connection->error);
    }
    
    $update_stmt->close();
}
$connection->close();

header("Location: index.php");
exit;
?>