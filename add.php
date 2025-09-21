<?php
include 'config.php';

$connection = new mysqli($host, $username, $password, $database);

if ($connection->connect_error) {
    die("Ошибка подключения: " . $connection->connect_error);
}

$success_message = '';
$error_message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = trim($_POST['title']);
    $descriptions = trim($_POST['descriptions']);
    $statis = $_POST['statis'];
    $priority = $_POST['priority'];
    $due_date = $_POST['due_date'] ?: null;
    
    if (empty($title) || empty($descriptions)) {
        $error_message = 'Пожалуйста, заполните все обязательные поля';
    } else {
        try {
            if (empty($due_date)) {
                $sql = "INSERT INTO tasks (title, descriptions, statis, priority, created_at) 
                        VALUES (?, ?, ?, ?, NOW())";
                $stmt = $connection->prepare($sql);
                $stmt->bind_param("ssss", $title, $descriptions, $statis, $priority);
            } else {
                $sql = "INSERT INTO tasks (title, descriptions, statis, priority, due_date, created_at) 
                        VALUES (?, ?, ?, ?, ?, NOW())";
                $stmt = $connection->prepare($sql);
                $stmt->bind_param("sssss", $title, $descriptions, $statis, $priority, $due_date);
            }
            
            if ($stmt->execute()) {
                $success_message = 'Задача успешно добавлена!';
                $_POST = array();
            } else {
                $error_message = 'Ошибка при добавлении задачи: ' . $connection->error;
            }
            
            $stmt->close();
        } catch (Exception $e) {
            $error_message = 'Ошибка: ' . $e->getMessage();
        }
    }
}

$connection->close();
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Добавить новую задачу</title>
    <link rel="stylesheet" href="styles.css">
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: url('/pic/bak.jpg') no-repeat center center fixed;
            background-size: cover;
            min-height: 100vh;
            margin: 0;
            padding: 20px;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        
        .create-container {
            max-width: 600px;
            width: 100%;
            background: rgba(255, 255, 255, 0.95);
            padding: 40px;
            border-radius: 20px;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.2);
        }
        
        .create-header {
            text-align: center;
            margin-bottom: 30px;
        }
        
        .create-header h1 {
            color: #2c3e50;
            font-size: 2.5rem;
            margin-bottom: 10px;
            font-weight: 700;
            background: linear-gradient(135deg, #667eea, #764ba2);
            -webkit-text-fill-color: transparent;
        }
        
        .create-header p {
            color: #7f8c8d;
            font-size: 1.2rem;
        }
        
        .form-group {
            margin-bottom: 25px;
        }
        
        .form-group label {
            display: block;
            margin-bottom: 10px;
            font-weight: 600;
            color: #2c3e50;
            font-size: 16px;
        }
        
        .form-group label.required::after {
            content: ' *';
            color: #e74c3c;
        }
        
        .form-control {
            width: 100%;
            padding: 15px 20px;
            border: 2px solid #e5e7eb;
            border-radius: 12px;
            font-size: 16px;
            transition: all 0.3s ease;
            background: #f8f9fa;
            font-family: inherit;
        }
        
        .form-control:focus {
            outline: none;
            border-color: #667eea;
            box-shadow: 0 0 0 4px rgba(102, 126, 234, 0.1);
            background: white;
            transform: translateY(-2px);
        }
        
        textarea.form-control {
            min-height: 120px;
            resize: vertical;
            line-height: 1.5;
        }
        
        .form-select {
            width: 100%;
            padding: 15px 20px;
            border: 2px solid #e5e7eb;
            border-radius: 12px;
            font-size: 16px;
            background: #f8f9fa;
            cursor: pointer;
            appearance: none;
            background-image: url("data:image/svg+xml;charset=UTF-8,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='none' stroke='currentColor' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'%3e%3cpolyline points='6 9 12 15 18 9'%3e%3c/polyline%3e%3c/svg%3e");
            background-repeat: no-repeat;
            background-position: right 15px center;
            background-size: 16px;
        }
        
        .form-select:focus {
            outline: none;
            border-color: #667eea;
            box-shadow: 0 0 0 4px rgba(102, 126, 234, 0.1);
        }
        
        .btn-group {
            display: flex;
            gap: 15px;
            justify-content: center;
            margin-top: 30px;
        }
        
        .btn {
            padding: 15px 30px;
            border: none;
            border-radius: 12px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }
        
        .btn-success {
            background: linear-gradient(135deg, #27ae60, #219a52);
            color: white;
        }
        
        .btn-success:hover {
            background: linear-gradient(135deg, #219a52, #27ae60);
            transform: translateY(-3px);
            box-shadow: 0 10px 30px rgba(39, 174, 96, 0.4);
        }
        
        .btn-cancel {
            background: linear-gradient(135deg, #95a5a6, #7f8c8d);
            color: white;
        }
        
        .btn-cancel:hover {
            background: linear-gradient(135deg, #7f8c8d, #95a5a6);
            transform: translateY(-3px);
        }
        
        .back-link {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            color: #667eea;
            text-decoration: none;
            margin-bottom: 25px;
            font-weight: 500;
            transition: all 0.3s ease;
            padding: 10px 15px;
            border-radius: 8px;
            background: rgba(102, 126, 234, 0.1);
        }
        
        .back-link:hover {
            color: #5a67d8;
            transform: translateX(-5px);
            background: rgba(102, 126, 234, 0.2);
        }
        
        .alert {
            padding: 20px;
            border-radius: 12px;
            margin-bottom: 25px;
            font-weight: 500;
            display: flex;
            align-items: center;
            gap: 12px;
            animation: slideIn 0.5s ease;
        }
        
        @keyframes slideIn {
            from {
                opacity: 0;
                transform: translateY(-20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        
        .alert-success {
            background: rgba(39, 174, 96, 0.1);
            color: #27ae60;
            border: 1px solid rgba(39, 174, 96, 0.2);
        }
        
        .alert-error {
            background: rgba(239, 68, 68, 0.1);
            color: #ef4444;
            border: 1px solid rgba(239, 68, 68, 0.2);
        }
        
        .character-count {
            text-align: right;
            font-size: 12px;
            color: #7f8c8d;
            margin-top: 5px;
            font-weight: 500;
        }
        
        .required-text {
            text-align: center;
            font-size: 12px;
            color: #7f8c8d;
            margin-top: 15px;
            padding: 10px;
            background: rgba(107, 114, 128, 0.1);
            border-radius: 8px;
        }
        
        .required-text::before {
            content: '* ';
            color: #ef4444;
            font-weight: bold;
        }
        
        @media (max-width: 768px) {
            .create-container {
                padding: 30px 20px;
                margin: 20px;
            }
            
            .create-header h1 {
                font-size: 2rem;
            }
            
            .btn-group {
                flex-direction: column;
            }
            
            .btn {
                width: 100%;
                justify-content: center;
            }
            
            body {
                padding: 10px;
                background: url('/pic/bak.jpg') no-repeat center center fixed;
                background-size: cover;
            }
        }
    </style>
</head>
<body>
    <div class="create-container">
        <a href="index.php" class="back-link">← Назад к списку задач</a>
        
        <div class="create-header">
            <p>Создайте новую задачу и управляйте своими делами эффективно</p>
        </div>
        
        <?php if ($success_message): ?>
            <div class="alert alert-success">
              <?php echo $success_message; ?>
                <br><small>Вы будете перенаправлены через 2 секунды...</small>
            </div>
            <script>
                setTimeout(function() {
                    window.location.href = 'index.php';
                }, 2000);
            </script>
        <?php endif; ?>
        
        <?php if ($error_message): ?>
            <div class="alert alert-error">
                ❌ <?php echo $error_message; ?>
            </div>
        <?php endif; ?>
        
        <form method="POST" id="taskForm">
            <div class="form-group">
                <label for="title" class="required">Название задачи</label>
                <input type="text" id="title" name="title" class="form-control" 
                       value="<?php echo isset($_POST['title']) ? htmlspecialchars($_POST['title']) : ''; ?>" 
                       placeholder="Введите название задачи" required maxlength="255">
                <div class="character-count" id="titleCount">0/255</div>
            </div>
            
            <div class="form-group">
                <label for="descriptions" class="required">Описание задачи</label>
                <textarea id="descriptions" name="descriptions" class="form-control" 
                          placeholder="Опишите детали задачи..." 
                          required maxlength="1000"><?php echo isset($_POST['descriptions']) ? htmlspecialchars($_POST['descriptions']) : ''; ?></textarea>
                <div class="character-count" id="descriptionCount">0/1000</div>
            </div>
            
            <div class="form-group">
                <label for="statis" class="required">Статус</label>
                <select id="statis" name="statis" class="form-select" required>
                    <option value="не выполнена" <?php echo (isset($_POST['statis']) && $_POST['statis'] == 'не выполнена') ? 'selected' : 'selected'; ?>>Не выполнена</option>
                    <option value="выполнена" <?php echo (isset($_POST['statis']) && $_POST['statis'] == 'выполнена') ? 'selected' : ''; ?>>Выполнена</option>
                </select>
            </div>
            
            <div class="form-group">
                <label for="priority" class="required">Приоритет</label>
                <select id="priority" name="priority" class="form-select" required>
                    <option value="низкий" <?php echo (isset($_POST['priority']) && $_POST['priority'] == 'низкий') ? 'selected' : ''; ?>>Низкий</option>
                    <option value="средний" <?php echo (isset($_POST['priority']) && $_POST['priority'] == 'средний') ? 'selected' : 'selected'; ?>>Средний</option>
                    <option value="высокий" <?php echo (isset($_POST['priority']) && $_POST['priority'] == 'высокий') ? 'selected' : ''; ?>>Высокий</option>
                </select>
            </div>
            
            <div class="form-group">
                <label for="due_date">Срок выполнения (необязательно)</label>
                <input type="date" id="due_date" name="due_date" class="form-control" 
                       value="<?php echo isset($_POST['due_date']) ? htmlspecialchars($_POST['due_date']) : ''; ?>"
                       min="<?php echo date('Y-m-d'); ?>">
            </div>
            
            <div class="required-text">Обязательные поля</div>
            
            <div class="btn-group">
                <a href="index.php" class="btn btn-cancel">Отмена</a>
                <button type="submit" class="btn btn-success">
                     Создать задачу
                </button>
            </div>
        </form>
    </div>

    <script>
        const titleInput = document.getElementById('title');
        const descriptionInput = document.getElementById('descriptions');
        const titleCount = document.getElementById('titleCount');
        const descriptionCount = document.getElementById('descriptionCount');
        
        function updateCharacterCount(input, counter, maxLength) {
            const count = input.value.length;
            counter.textContent = `${count}/${maxLength}`;
            
            if (count > maxLength * 0.8) {
                counter.style.color = '#e74c3c';
            } else {
                counter.style.color = '#7f8c8d';
            }
        }
        
        titleInput.addEventListener('input', () => {
            updateCharacterCount(titleInput, titleCount, 255);
        });
        
        descriptionInput.addEventListener('input', () => {
            updateCharacterCount(descriptionInput, descriptionCount, 1000);
        });
        
        
        updateCharacterCount(titleInput, titleCount, 255);
        updateCharacterCount(descriptionInput, descriptionCount, 1000);
        
        <?php if ($success_message): ?>
        setTimeout(function() {
            window.location.href = 'index.php';
        }, 2000);
        <?php endif; ?>
    </script>
</body>
</html>