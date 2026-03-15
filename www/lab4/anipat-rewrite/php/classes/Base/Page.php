<?php

namespace classes\Base;
abstract class Page
{
    private $head;
    private $header;
    private $menu;
    private $body;
    private $footer;

    public function __construct(BodyInterface $body = null) {
        $this->body = $body;
    }

    // Метод-сеттер для зависимостей js
    public function setHead($head)
    {
        $this->head = $head;
    }

    // Метод-сеттер для шапки
    public function setHeader($header)
    {
        $this->header = $header;
    }

    // Метод-сеттер для меню
    public function setMenu($menu)
    {
        $this->menu = $menu;
    }

    // Метод-сеттер для body
    public function setBody($body) {
        $this->body = $body;
    }

    // Метод-сеттер для подвала
    public function setFooter($footer)
    {
        $this->footer = $footer;
    }

    public function render()
    {
        echo '<html class="no-js" lang="zxx">';

        // Подключение js и bootstrap
        if ($this->head !== null) {
            $this->head->render();
        }

        echo '
        <header>
            <div class="header-area">';

        if ($this->header !== null) {
            $this->header->render();
        }

        if ($this->menu !== null) {
            $this->menu->render();
        }

        echo '
            </div>
        </header>
        ';

        if ($this->body !== null) {
            $this->body->render();
        }

        if ($this->footer !== null) {
            $this->footer->render();
        }

        echo '</html>';
    }
}
