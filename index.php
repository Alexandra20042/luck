<?php
include 'config.php';

$connection = new mysqli($host, $username, $password, $database);

if ($connection->connect_error) {
    die("Ошибка подключения: " . $connection->connect_error);
}

$connection->set_charset("utf8mb4");

$query = "SELECT * FROM tasks ORDER BY 
          CASE priority 
            WHEN 'высокий' THEN 1 
            WHEN 'средний' THEN 2 
            WHEN 'низкий' THEN 3 
          END, 
          id ASC, 
          due_date ASC"; 
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
    <style>
        body {
            color: #000000; 
        }
        
        .container {
            color: #000000;
        }
        
        table {
            color: #000000; 
        }
        
        td, th {
            color: #000000; 
        }
        
        .actions {
            display: flex;
            flex-direction: column;
            gap: 8px;
            align-items: center;
        }
        
        .action-btn {
            display: block;
            padding: 8px 16px;
            text-decoration: none;
            border-radius: 6px;
            font-weight: 500;
            font-size: 14px;
            text-align: center;
            transition: all 0.3s ease;
            min-width: 100px;
            color: #000000; 
        }
        
        .edit {
            background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
            color: white;
            border: 1px solid #4facfe;
        }
        
        .edit:hover {
            background: linear-gradient(135deg, #00f2fe 0%, #4facfe 100%);
            transform: translateY(-2px);
            box-shadow: 0 4px 15px rgba(79, 172, 254, 0.4);
        }
        
        .delete {
            background: linear-gradient(135deg, #ff6b6b 0%, #ff9a3d 100%);
            color: white;
            border: 1px solid #ff6b6b;
        }
        
        .delete:hover {
            background: linear-gradient(135deg, #ff9a3d 0%, #ff6b6b 100%);
            transform: translateY(-2px);
            box-shadow: 0 4px 15px rgba(255, 107, 107, 0.4);
        }
        
        .status-container {
            display: flex;
            align-items: center;
            gap: 10px;
            justify-content: center;
        }
        
        .status-toggle-btn {
            background: linear-gradient(135deg, #6a11cb 0%, #2575fc 100%);
            color: white;
            border: none;
            padding: 8px 16px;
            border-radius: 20px;
            cursor: pointer;
            font-size: 14px;
            transition: all 0.3s ease;
            white-space: nowrap;
        }
        
        .status-toggle-btn:hover {
            transform: scale(1.05);
            box-shadow: 0 4px 15px rgba(106, 17, 203, 0.4);
        }
        
        .status-badge {
            padding: 8px 16px;
            border-radius: 20px;
            font-size: 14px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            color: white;
            min-width: 120px;
            text-align: center;
        }
        
        .status-completed {
            background: linear-gradient(135deg, #27ae60, #219a52);
        }
        
        .status-pending {
            background: linear-gradient(135deg, #f39c12, #e67e22);
        }
        
        .priority-high {
            color: #e74c3c;
            font-weight: 700;
        }
        
        .priority-medium {
            color: #f39c12;
            font-weight: 600;
        }
        
        .priority-low {
            color: #27ae60;
            font-weight: 500;
        }
        
        .date-overdue {
            color: #e74c3c;
            font-weight: 700;
        }
        
        .date-today {
            color: #f39c12;
            font-weight: 700;
        }
        
        .date-future {
            color: #27ae60;
            font-weight: 600;
        }
        
        .no-date {
            color: #95a5a6;
            font-style: italic;
        }
    </style>
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
                        <th>Номер</th>
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
                                    <?php if ($task['statis'] == 'не выполнена'): ?>
                                    <form action="update_status.php" method="GET" style="display: inline;">
                                        <input type="hidden" name="id" value="<?php echo $task['id']; ?>">
                                        <button type="submit" class="status-toggle-btn" 
                                                title="Отметить как выполненную">
                                             Выполнить
                                        </button>
                                    </form>
                                    <?php endif; ?>
                                    
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
                                <a href="edit.php?id=<?php echo $task['id']; ?>" class="action-btn edit" title="Редактировать">Изменить</a>
                                <a href="delete.php?id=<?php echo $task['id']; ?>" class="action-btn delete" 
                                   onclick="return confirm('Вы уверены, что хотите удалить эту задачу?')" title="Удалить"> Удалить</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>
        
        <br>
        <a href="add.php" class="add-btn" target="_blank">➕ Добавить новую задачу</a>
    </div>
</body>
</html>