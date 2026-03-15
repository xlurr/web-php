<?php
// Функция для выполнения подготовленных запросов
function execute_prepared_query($sql, $params = [], $types = '') {
    global $connection;
    
    $stmt = mysqli_prepare($connection, $sql);
    if (!$stmt) {
        die("Ошибка подготовки запроса: " . mysqli_error($connection));
    }
    
    if (!empty($params)) {
        mysqli_stmt_bind_param($stmt, $types, ...$params);
    }
    
    if (!mysqli_stmt_execute($stmt)) {
        die("Ошибка выполнения запроса: " . mysqli_stmt_error($stmt));
    }
    
    return $stmt;
}

// Функция для получения всех факультетов
function get_all_faculties() {
    $sql = "SELECT * FROM faculties ORDER BY name ASC";
    $stmt = execute_prepared_query($sql);
    $result = mysqli_stmt_get_result($stmt);
    mysqli_stmt_close($stmt);
    return $result;
}

// Функция для получения факультета по ID
function get_faculty_by_id($id) {
    $sql = "SELECT * FROM faculties WHERE id = ?";
    $stmt = execute_prepared_query($sql, [$id], 'i');
    $result = mysqli_stmt_get_result($stmt);
    $faculty = mysqli_fetch_assoc($result);
    mysqli_stmt_close($stmt);
    return $faculty;
}

// Функция для добавления факультета
function add_faculty($name) {
    $sql = "INSERT INTO faculties (name) VALUES (?)";
    $stmt = execute_prepared_query($sql, [$name], 's');
    $affected = mysqli_stmt_affected_rows($stmt);
    mysqli_stmt_close($stmt);
    return $affected > 0;
}

// Функция для получения всех студентов с информацией о факультетах + предметы + группы
function get_all_students() {
    $sql = "SELECT 
                st.id,
                st.name, 
                st.email,
                st.phone,
                st.course,
                st.stud_group,
                st.created_at,
                f.name as faculty_name, 
                GROUP_CONCAT(su.name) as subjects_name
            FROM students st 
            LEFT JOIN faculties f ON st.faculty_id = f.id
            LEFT JOIN students_subjects ss ON st.id = ss.student_id
            LEFT JOIN subjects su ON ss.subject_id = su.id
            GROUP BY st.id,
                st.name, 
                st.email,
                st.phone,
                st.course,
                st.stud_group,
                st.created_at,
                f.name
            ORDER BY st.created_at DESC";
    $stmt = execute_prepared_query($sql);
    $result = mysqli_stmt_get_result($stmt);
    mysqli_stmt_close($stmt);
    return $result;
}

// Функция для получения студента по ID с информацией о факультете + предметы + группы
function get_student_by_id($id) {
    $sql = "SELECT 
                st.id,
                st.name, 
                st.email,
                st.phone,
                st.course,
                st.stud_group,
                st.created_at,
                f.name as faculty_name, 
                f.id as faculty_id, 
                GROUP_CONCAT(su.name) as subjects_name
            FROM students st 
            LEFT JOIN faculties f ON st.faculty_id = f.id
            LEFT JOIN students_subjects ss ON st.id = ss.student_id
            LEFT JOIN subjects su ON ss.subject_id = su.id
            WHERE st.id = ?
            GROUP BY st.id,
                st.name, 
                st.email,
                st.phone,
                st.course,
                st.stud_group,
                st.created_at,
                f.name,
                f.id
            ORDER BY st.created_at DESC";
    $stmt = execute_prepared_query($sql, [$id], 'i');
    $result = mysqli_stmt_get_result($stmt);
    $student = mysqli_fetch_assoc($result);
    mysqli_stmt_close($stmt);
    return $student;
}

// Функция для добавления студента
function add_student($name, $email, $phone, $faculty_id, $course, $stud_group) {
    $sql = "INSERT INTO students (name, email, phone, faculty_id, course, stud_group) VALUES (?, ?, ?, ?, ?, ?)";
    $stmt = execute_prepared_query($sql, [$name, $email, $phone, $faculty_id, $course, $stud_group], 'sssiii');
    if ($stmt) {
        $id = mysqli_stmt_insert_id($stmt);
        mysqli_stmt_close($stmt);
        return $id;
    } else {
        return 0;
    }
}

// Функция для обновления студента
function update_student($id, $name, $email, $phone, $faculty_id, $course, $stud_group) {
    $sql = "UPDATE students SET name = ?, email = ?, phone = ?, faculty_id = ?, course = ?, stud_group = ? WHERE id = ?";
    $stmt = execute_prepared_query($sql, [$name, $email, $phone, $faculty_id, $course, $stud_group, $id], 'sssiiii');
    $affected = mysqli_stmt_affected_rows($stmt);
    mysqli_stmt_close($stmt);
    return $affected > 0;
}

// Функция для удаления студента
function delete_student($id) {
    $sql = "DELETE FROM students WHERE id = ?";
    $stmt = execute_prepared_query($sql, [$id], 'i');
    $affected = mysqli_stmt_affected_rows($stmt);
    mysqli_stmt_close($stmt);
    return $affected > 0;
}

// Функция для проверки существования email
function email_exists($email, $exclude_id = null) {
    if ($exclude_id) {
        $sql = "SELECT id FROM students WHERE email = ? AND id != ?";
        $stmt = execute_prepared_query($sql, [$email, $exclude_id], 'si');
    } else {
        $sql = "SELECT id FROM students WHERE email = ?";
        $stmt = execute_prepared_query($sql, [$email], 's');
    }
    
    $result = mysqli_stmt_get_result($stmt);
    $exists = mysqli_num_rows($result) > 0;
    mysqli_stmt_close($stmt);
    return $exists;
}

// Функция добавление связи предмет и сутдент
function add_student_subject($student_id, $subject_id) {
    $sql = "INSERT INTO students_subjects (student_id, subject_id) VALUES (?, ?)";
    $stmt = execute_prepared_query($sql, [$student_id, $subject_id], 'ii');
    $affected = mysqli_stmt_affected_rows($stmt);
    mysqli_stmt_close($stmt);
    return $affected > 0;
}

// Функция получение предметов
function get_all_subjects() {
    $sql = "SELECT * FROM subjects ORDER BY name ASC";
    $stmt = execute_prepared_query($sql);
    $result = mysqli_stmt_get_result($stmt);
    mysqli_stmt_close($stmt);
    return $result;
}
//

// Функция для очистки входных данных
function clean_input($data) {
    return htmlspecialchars(trim($data));
}
?>