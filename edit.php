<?php
include 'config.php';

if (isset($_GET['id'])) {
    $id = intval($_GET['id']);
    
    try {

        $sql = "SELECT * FROM tasks WHERE id = :id";
        $stmt = $conn->prepare($sql);
        $stmt->execute(['id' => $id]);
        $task = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$task) {
            die("Задача не найдена.");
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $title = $_POST['title'];
            $description = $_POST['description'];
            $status = $_POST['status'];
            $priority = $_POST['priority'];
            $due_date = $_POST['due_date'];

            $updateSql = "UPDATE tasks SET title = :title, description = :description, status = :status, priority = :priority, due_date = :due_date WHERE id = :id";
            $updateStmt = $conn->prepare($updateSql);
            $updateStmt->execute([
                'title' => $title,
                'description' => $description,
                'status' => $status,
                'priority' => $priority,
                'due_date' => $due_date,
                'id' => $id
            ]);
            
            header("Location: index.php");
            exit;
        }
    } catch (PDOException $e) {
        die("Ошибка: " . $e->getMessage());
    }
} else {
    die("ID не указан.");
}
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Редактировать задачу</title>
</head>
<body>
    <h1>Редактировать задачу</h1>
    <form method="POST">
        <label>Название:</label>
        <input type="text" name="title" value="<?php echo htmlspecialchars($task['title']); ?>" required><br>
        
        <label>Описание:</label>
        <textarea name="description" required><?php echo htmlspecialchars($task['description']); ?></textarea><br>
        
        <label>Статус:</label>
        <input type="text" name="status" value="<?php echo htmlspecialchars($task['status']); ?>" required><br>
        
        <label>Приоритет:</label>
        <input type="text" name="priority" value="<?php echo htmlspecialchars($task['priority']); ?>" required><br>
        
        <label>Срок:</label>
        <input type="date" name="due_date" value="<?php echo htmlspecialchars($task['due_date']); ?>" required><br>
        
        <input type="submit" value="Сохранить изменения">
    </form>
</body>
</html>