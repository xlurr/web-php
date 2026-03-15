<?php
require_once 'config/database.php';

// Удаляем старые таблицы, если они существуют
mysqli_query($connection, "DROP TABLE IF EXISTS students_subjects");
mysqli_query($connection, "DROP TABLE IF EXISTS students");
mysqli_query($connection, "DROP TABLE IF EXISTS subjects");
mysqli_query($connection, "DROP TABLE IF EXISTS faculties");
echo "<div class='alert alert-warning'>Старые таблицы удалены</div>";


// Создание таблицы факультетов
$sql_faculties = "CREATE TABLE IF NOT EXISTS faculties (
    id INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL UNIQUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)";

if (mysqli_query($connection, $sql_faculties)) {
    echo "<div class='alert alert-success'>Таблица 'faculties' создана успешно!</div>";

    // Добавляем тестовые факультеты
    $test_faculties = [
        'Информационные технологии',
        'Экономика и финансы',
        'Юриспруденция',
        'Медицина',
        'Строительство',
        'Дизайн'
    ];

    foreach ($test_faculties as $faculty) {
        $sql = "INSERT IGNORE INTO faculties (name) VALUES ('$faculty')";
        mysqli_query($connection, $sql);
    }
    echo "<div class='alert alert-info'>Добавлены тестовые факультеты</div>";

} else {
    echo "<div class='alert alert-danger'>Ошибка создания таблицы faculties: " . mysqli_error($connection) . "</div>";
}

// Создание таблицы студентов (обновленная)
$sql_students = "CREATE TABLE IF NOT EXISTS students (
    id INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    phone VARCHAR(20),
    faculty_id INT(6) UNSIGNED,
    course INT(2),
    stud_group INT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (faculty_id) REFERENCES faculties(id) ON DELETE SET NULL
)";

if (mysqli_query($connection, $sql_students)) {
    echo "<div class='alert alert-success'>Таблица 'students' создана успешно!</div>";
} else {
    echo "<div class='alert alert-danger'>Ошибка создания таблицы students: " . mysqli_error($connection) . "</div>";
}

// Создание таблицы предметов
$sql_subjects = "CREATE TABLE IF NOT EXISTS subjects (
    id INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name Varchar(100) UNIQUE NOT NULL
)";

if (mysqli_query($connection, $sql_subjects)) {
    // Добавляем тестовые предметы
    $test_subjects = [
        'Web',
        'Физра',
        'OOP',
        'Микросервисы',
        'Мобильные приложения',
        'Дизайн'
    ];

    foreach ($test_subjects as $subject) {
        $sql = "INSERT IGNORE INTO subjects (name) VALUES ('$subject')";
        mysqli_query($connection, $sql);
    }
    echo "<div class='alert alert-success'>Таблица 'subject' с тестовыми данными создана успешно!</div>";
} else {
    echo "<div class='alert alert-danger'>Ошибка создания таблицы students: " . mysqli_error($connection) . "</div>";
}

// Таблица связей студент - предмент
$sql_students_subjects = "CREATE TABLE IF NOT EXISTS students_subjects (
    student_id INT(6) UNSIGNED NOT NULL,
    subject_id INT(6) UNSIGNED NOT NULL,
    PRIMARY KEY (student_id, subject_id),
    FOREIGN KEY (subject_id) REFERENCES subjects(id) ON DELETE CASCADE,
    FOREIGN KEY (student_id) REFERENCES students(id) ON DELETE CASCADE
)";

if (mysqli_query($connection, $sql_students_subjects)) {
    echo "<div class='alert alert-success'>Таблица 'students_subjects' создана успешно!</div>";
} else {
    echo "<div class='alert alert-danger'>Ошибка создания таблицы students: " . mysqli_error($connection) . "</div>";
}
mysqli_close($connection);
?>