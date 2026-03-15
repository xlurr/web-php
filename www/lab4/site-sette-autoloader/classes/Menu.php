<?php

namespace classes;
class Menu
{
    public static function render()
    {
        echo '<nav>';
        echo '<ul>';
        echo '<li><a href="IndexBody.php">Главная</a></li>';
        echo '<li><a href="about.php">О нас</a></li>';
        echo '<li><a href="contact.php">Контакты</a></li>';
        echo '</ul>';
        echo '</nav>';
    }
}

