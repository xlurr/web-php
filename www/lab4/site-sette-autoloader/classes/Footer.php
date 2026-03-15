<?php

namespace classes;
class Footer
{
    public static function render()
    {
        echo '<footer>';
        echo '&copy; ' . date('Y') . ' Мой сайт';
        echo '</footer>';
    }
}
