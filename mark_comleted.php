<?php
include 'config.php';

if (isset($_GET['id'])) {
    $id = intval($_GET['id']);
    $sql = "UPDATE tasks SET status = 'completed' WHERE id = $id";
    $conn->query($sql);
}

header("Location: index.php");
exit;

$conn->close();
?>