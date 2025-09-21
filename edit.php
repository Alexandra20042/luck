<?php
include 'config.php';

if (isset($_GET['id'])) {
    $id = intval($_GET['id']);
    
    try {
        $connection = new mysqli($host, $username, $password, $database);
        
        if ($connection->connect_error) {
            die("Ошибка подключения: " . $connection->connect_error);
        }

        $sql = "SELECT * FROM tasks WHERE id = ?";
        $stmt = $connection->prepare($sql);
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();
        $task = $result->fetch_assoc();

        if (!$task) {
            die("Задача не найдена.");
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $title = $_POST['title'];
            $descriptions = $_POST['descriptions'];
            $statis = $_POST['statis'];
            $priority = $_POST['priority'];
            $due_date = $_POST['due_date'];

            $updateSql = "UPDATE tasks SET title = ?, descriptions = ?, statis = ?, priority = ?, due_date = ? WHERE id = ?";
            $updateStmt = $connection->prepare($updateSql);
            $updateStmt->bind_param("sssssi", $title, $descriptions, $statis, $priority, $due_date, $id);
            
            if ($updateStmt->execute()) {
                header("Location: index.php");
                exit;
            } else {
                die("Ошибка при обновлении задачи: " . $connection->error);
            }
        }
        
        $connection->close();
    } catch (Exception $e) {
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
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Редактировать задачу</title>
    <link rel="stylesheet" href="styles.css">
    <style>
        .edit-container {
            max-width: 600px;
            margin: 40px auto;
            padding: 30px;
            background: var(--bg-primary);
            border-radius: var(--border-radius);
            box-shadow: var(--shadow);
        }
        
        .edit-header {
            text-align: center;
            margin-bottom: 30px;
        }
        
        .edit-header h1 {
            color: var(--primary-color);
            font-size: 2.2rem;
            margin-bottom: 10px;
        }
        
        .form-group {
            margin-bottom: 20px;
        }
        
        .form-group label {
            display: block;
            margin-bottom: 8px;
            font-weight: 600;
            color: var(--text-primary);
        }
        
        .form-control {
            width: 100%;
            padding: 12px 16px;
            border: 2px solid var(--border-color);
            border-radius: var(--border-radius-sm);
            font-size: 16px;
            transition: var(--transition);
            background: var(--bg-secondary);
        }
        
        .form-control:focus {
            outline: none;
            border-color: var(--primary-color);
            box-shadow: 0 0 0 3px rgba(52, 152, 219, 0.1);
            background: var(--bg-primary);
        }
        
        textarea.form-control {
            min-height: 120px;
            resize: vertical;
        }
        
        .form-select {
            width: 100%;
            padding: 12px 16px;
            border: 2px solid var(--border-color);
            border-radius: var(--border-radius-sm);
            font-size: 16px;
            background: var(--bg-secondary);
            cursor: pointer;
        }
        
        .form-select:focus {
            outline: none;
            border-color: var(--primary-color);
            box-shadow: 0 0 0 3px rgba(52, 152, 219, 0.1);
        }
        
        .btn-group {
            display: flex;
            gap: 15px;
            justify-content: center;
            margin-top: 30px;
        }
        
        .btn-cancel {
            background: var(--secondary-color);
            color: var(--text-light);
        }
        
        .btn-cancel:hover {
            background: var(--secondary-hover);
        }
        
        .back-link {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            color: var(--primary-color);
            text-decoration: none;
            margin-top: 20px;
            font-weight: 500;
        }
        
        .back-link:hover {
            color: var(--primary-hover);
        }
    </style>
</head>
<body>
    <div class="container">
        <a href="index.php" class="back-link">← Назад к списку задач</a>
        
        <div class="edit-container">
            <div class="edit-header">
                <h1>✏️ Редактировать задачу</h1>
                <p>ID: #<?php echo htmlspecialchars($task['id']); ?></p>
            </div>
            
            <form method="POST">
                <div class="form-group">
                    <label for="title">Название задачи</label>
                    <input type="text" id="title" name="title" class="form-control" 
                           value="<?php echo htmlspecialchars($task['title']); ?>" required>
                </div>
                
                <div class="form-group">
                    <label for="descriptions">Описание</label>
                    <textarea id="descriptions" name="descriptions" class="form-control" 
                              required><?php echo htmlspecialchars($task['descriptions']); ?></textarea>
                </div>
                
                <div class="form-group">
                    <label for="statis">Статус</label>
                    <select id="statis" name="statis" class="form-select" required>
                        <option value="не выполнена" <?php echo ($task['statis'] == 'не выполнена') ? 'selected' : ''; ?>>Не выполнена</option>
                        <option value="выполнена" <?php echo ($task['statis'] == 'выполнена') ? 'selected' : ''; ?>>Выполнена</option>
                    </select>
                </div>
                
                <div class="form-group">
                    <label for="priority">Приоритет</label>
                    <select id="priority" name="priority" class="form-select" required>
                        <option value="высокий" <?php echo ($task['priority'] == 'высокий') ? 'selected' : ''; ?>>Высокий</option>
                        <option value="средний" <?php echo ($task['priority'] == 'средний') ? 'selected' : ''; ?>>Средний</option>
                        <option value="низкий" <?php echo ($task['priority'] == 'низкий') ? 'selected' : ''; ?>>Низкий</option>
                    </select>
                </div>
                
                <div class="form-group">
                    <label for="due_date">Срок выполнения</label>
                    <input type="date" id="due_date" name="due_date" class="form-control" 
                           value="<?php echo htmlspecialchars($task['due_date']); ?>">
                </div>
                
                <div class="btn-group">
                    <a href="index.php" class="btn btn-cancel">Отмена</a>
                    <button type="submit" class="btn btn-success">
                     Сохранить изменения
                    </button>
                </div>
            </form>
        </div>
    </div>
</body>
</html>