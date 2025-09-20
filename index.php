<?php
include 'config.php';

$connection = new mysqli($host, $username, $password, $database);

if ($connection->connect_error) {
    die("Ошибка подключения: " . $connection->connect_error);
}

$connection->set_charset("utf8mb4");

// Updated query to sort by id and due_date
$query = "SELECT * FROM tasks ORDER BY 
          CASE priority 
            WHEN 'высокий' THEN 1 
            WHEN 'средний' THEN 2 
            WHEN 'низкий' THEN 3 
          END, 
          id ASC, 
          due_date ASC"; // Sort by id first, then by due_date
$result = $connection->query($query);

if (!$result) {
    die("Ошибка выполнения запроса: " . $connection->error);
}
$tasks = [];
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $tasks[] = $row;
    }
}

$connection->close();
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Список дел</title>
    <link rel="icon" type="image/x-icon" href="/pic/icon.jpg">
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <div class="container">
        <h1>Список дел</h1>
        
        <?php if (empty($tasks)): ?>
            <div class="empty-state">
                <p>Нет задач для отображения.</p>
                <a href="add.php" class="add-btn">➕ Добавить первую задачу</a>
            </div>
        <?php else: ?>
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Название</th>
                        <th>Описание</th>
                        <th>Статус</th>
                        <th>Приоритет</th>
                        <th>Срок выполнения</th>
                        <th>Дата создания</th>
                        <th>Действия</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($tasks as $task): ?>
                        <?php
                        $dueDateClass = 'date-future';
                        if (!empty($task['due_date'])) {
                            $dueDate = new DateTime($task['due_date']);
                            $today = new DateTime();
                            
                            if ($dueDate < $today) {
                                $dueDateClass = 'date-overdue';
                            } elseif ($dueDate->format('Y-m-d') == $today->format('Y-m-d')) {
                                $dueDateClass = 'date-today';
                            }
                        } else {
                            $dueDateClass = 'no-date';
                        }
                        ?>
                        <tr>
                            <td><?php echo htmlspecialchars($task['id'] ?? ''); ?></td>
                            <td><strong><?php echo htmlspecialchars($task['title'] ?? ''); ?></strong></td>
                            <td><?php 
                                $description = htmlspecialchars($task['descriptions'] ?? '');
                                echo strlen($description) > 50 ? substr($description, 0, 50) . '...' : $description;
                            ?></td>
                            <td>
                                <div class="status-container">
                                    <form action="update_status.php" method="GET" style="display: inline;">
                                        <input type="hidden" name="id" value="<?php echo $task['id']; ?>">
                                        <button type="submit" class="status-toggle-btn" 
                                                title="<?php echo ($task['statis'] == 'выполнена') ? 'Отметить как не выполненную' : 'Отметить как выполненную'; ?>">
                                            <?php echo ($task['statis'] == 'выполнена') ? 'изменить' : 'изменить'; ?>
                                        </button>
                                    </form>
                                    
                                    <?php 
                                    $status = htmlspecialchars($task['statis'] ?? '');
                                    $statusClass = $status == 'выполнена' ? 'status-completed' : 'status-pending';
                                    echo "<span class='status-badge $statusClass'>$status</span>";
                                    ?>
                                </div>
                            </td>
                            <td class="priority-<?php 
                                $priority = htmlspecialchars($task['priority'] ?? '');
                                echo str_replace(['высокий', 'средний', 'низкий'], ['high', 'medium', 'low'], $priority);
                            ?>">
                                <?php echo $priority; ?>
                            </td>
                            <td class="<?php echo $dueDateClass; ?>">
                                <?php 
                                if (!empty($task['due_date'])) {
                                    echo htmlspecialchars($task['due_date']);
                                } else {
                                    echo 'Не указан';
                                }
                                ?>
                            </td>
                            <td><?php echo htmlspecialchars($task['created_at'] ?? ''); ?></td>
                            <td class="actions">
                                <a href="edit.php?id=<?php echo $task['id']; ?>" class="edit" title="Редактировать">✏️</a>
                                <a href="delete.php?id=<?php echo $task['id']; ?>" class="delete" 
                                   onclick="return confirm('Вы уверены, что хотите удалить эту задачу?')" title="Удалить">🗑️</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>
        
        <br>
<a href="add.php" class="add-btn" target="_blank">Добавить новую задачу</a>
    </div>
</body>
</html>