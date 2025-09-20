<?php

$host = "127.0.0.1";
$username = "sasha";
$password = "root"; 
$database = "task_manager";
$port = 3306; 

// Создаем подключение
$conn = new mysqli($host, $username, $password, $database, $port);

// Проверяем соединение
if ($conn->connect_error) {
    die("Ошибка подключения: " . $conn->connect_error);
}

// Устанавливаем кодировку
$conn->set_charset("utf8mb4");

// Выполняем запрос
$query = "SELECT * FROM tasks ORDER BY 
          CASE priority 
            WHEN 'высокий' THEN 1 
            WHEN 'средний' THEN 2 
            WHEN 'низкий' THEN 3 
          END, due_date ASC";
$result = $conn->query($query);

// Обрабатываем ошибки запроса
if (!$result) {
    die("Ошибка выполнения запроса: " . $conn->error);
}

// Получаем задачи
$tasks = [];
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $tasks[] = $row;
    }
}

// Выводим результаты (добавьте этот блок для отображения данных)
echo "<pre>";
print_r($tasks);
echo "</pre>";

// Закрываем соединение
$conn->close();
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Список задач</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            margin: 0;
            padding: 20px;
            background-color: #f5f5f5;
            color: #333;
        }
        .container {
            max-width: 1200px;
            margin: 0 auto;
            background-color: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }
        h1 {
            color: #2c3e50;
            margin-top: 0;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 12px;
            text-align: left;
        }
        th {
            background-color: #3498db;
            color: white;
            cursor: pointer;
        }
        th:hover {
            background-color: #2980b9;
        }
        tr:nth-child(even) {
            background-color: #f9f9f9;
        }
        tr:hover {
            background-color: #f1f1f1;
        }
        .actions {
            white-space: nowrap;
        }
        .actions a {
            display: inline-block;
            margin-right: 8px;
            text-decoration: none;
            padding: 6px 12px;
            border-radius: 4px;
            font-size: 14px;
            transition: background-color 0.2s;
        }
        .edit { 
            background-color: #2196F3; 
            color: white; 
        }
        .edit:hover {
            background-color: #0b7dda;
        }
        .delete { 
            background-color: #f44336; 
            color: white; 
        }
        .delete:hover {
            background-color: #d32f2f;
        }
        .complete { 
            background-color: #4CAF50; 
            color: white; 
        }
        .complete:hover {
            background-color: #388e3c;
        }
        .add-btn {
            display: inline-block;
            background-color: #4CAF50;
            color: white;
            padding: 12px 20px;
            text-decoration: none;
            border-radius: 5px;
            font-weight: bold;
            transition: background-color 0.3s;
        }
        .add-btn:hover {
            background-color: #388e3c;
        }
        .empty-state {
            text-align: center;
            padding: 40px;
            color: #7f8c8d;
        }
        .empty-state p {
            font-size: 18px;
        }
        .status-badge {
            padding: 4px 12px;
            border-radius: 12px;
            font-size: 12px;
            font-weight: bold;
            display: inline-block;
        }
        .status-completed {
            background-color: #e8f5e9;
            color: #2e7d32;
        }
        .status-pending {
            background-color: #fff3e0;
            color: #ef6c00;
        }
        .priority-high {
            color: #f44336;
            font-weight: bold;
        }
        .priority-medium {
            color: #ff9800;
        }
        .priority-low {
            color: #4caf50;
        }
        .date-overdue {
            color: #f44336;
            font-weight: bold;
        }
        .date-today {
            color: #ff9800;
            font-weight: bold;
        }
        .date-future {
            color: #4caf50;
        }
        .no-date {
            color: #9e9e9e;
            font-style: italic;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>📝 Список задач</h1>
        
        <?php if (empty($tasks)): ?>
            <div class="empty-state">
                <p>Нет задач для отображения.</p>
                <a href="create.php" class="add-btn">➕ Добавить первую задачу</a>
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
                        // Определяем класс для даты
                        $dueDateClass = 'date-future';
                        if ($task['due_date']) {
                            $dueDate = new DateTime($task['due_date']);
                            $today = new DateTime();
                            $interval = $today->diff($dueDate);
                            
                            if ($dueDate < $today) {
                                $dueDateClass = 'date-overdue';
                            } elseif ($dueDate == $today) {
                                $dueDateClass = 'date-today';
                            }
                        } else {
                            $dueDateClass = 'no-date';
                        }
                        ?>
                        <tr>
                            <td><?php echo htmlspecialchars($task['id']); ?></td>
                            <td><strong><?php echo htmlspecialchars($task['title']); ?></strong></td>
                            <td><?php 
                                $description = htmlspecialchars($task['descriptions']);
                                echo strlen($description) > 50 ? substr($description, 0, 50) . '...' : $description;
                            ?></td>
                            <td>
                                <?php 
                                $status = htmlspecialchars($task['statis']);
                                $statusClass = $status == 'выполнена' ? 'status-completed' : 'status-pending';
                                echo "<span class='status-badge $statusClass'>$status</span>";
                                ?>
                            </td>
                            <td class="priority-<?php 
                                $priority = htmlspecialchars($task['priority']);
                                echo str_replace(['высокий', 'средний', 'низкий'], ['high', 'medium', 'low'], $priority);
                            ?>">
                                <?php echo $priority; ?>
                            </td>
                            <td class="<?php echo $dueDateClass; ?>">
                                <?php 
                                if ($task['due_date']) {
                                    echo htmlspecialchars($task['due_date']);
                                } else {
                                    echo 'Не указан';
                                }
                                ?>
                            </td>
                            <td><?php echo htmlspecialchars($task['created_at']); ?></td>
                            <td class="actions">
                                <a href="edit.php?id=<?php echo $task['id']; ?>" class="edit" title="Редактировать">✏️</a>
                                <a href="delete.php?id=<?php echo $task['id']; ?>" class="delete" 
                                   onclick="return confirm('Вы уверены, что хотите удалить эту задачу?')" title="Удалить">🗑️</a>
                                <?php if ($status != 'выполнена'): ?>
                                    <a href="mark_completed.php?id=<?php echo $task['id']; ?>" class="complete" title="Отметить как выполненную">✅</a>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>
        
        <br>
        <a href="create.php" class="add-btn">➕ Добавить новую задачу</a>
    </div>
</body>
</html>*/