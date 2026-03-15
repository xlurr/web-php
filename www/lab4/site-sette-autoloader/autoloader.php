<?php

// Функция автозагрузки классов
function autoloadClasses($className) {
    // Определяем путь к классу относительно корневой директории
    $filePath = __DIR__ . '/' . str_replace('\\', '/', $className) . '.php';

    // Проверяем существование файла
    if (file_exists($filePath)) {
        require_once $filePath;
    } else {
        throw new Exception("Класс '$className' не найден.");
    }
}

// Регистрация автозагрузчика
spl_autoload_register('autoloadClasses');