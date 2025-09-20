<?php
include 'config.php';

if (isset($_GET['id'])) {
    $id = intval($_GET['id']);
    
    $sql = "DELETE FROM tasks WHERE id = $id";
    $conn->query($sql);
}

header("Location: index.php");
exit;

$conn->close();
?>