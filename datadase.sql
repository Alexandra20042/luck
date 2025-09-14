CREATE DATABASE task_manager;

USE task_manager;

CREATE TABLE tasks (
    id INT PRIMARY KEY AUTO_INCREMENT,
    title VARCHAR(255) NOT NULL,
    description TEXT DEFAULT '',
    status ENUM('не выполнена', 'выполнена') NOT NULL DEFAULT 'не выполнена',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    priority ENUM('низкий', 'средний', 'высокий') NOT NULL DEFAULT 'низкий',
    due_date DATE DEFAULT NULL
);