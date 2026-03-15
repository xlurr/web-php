<?php

namespace classes;
abstract class Page
{
    protected $title;
    protected $content;
    private $header;
    private $menu;
    private $footer;

    public function __construct($title, $content)
    {
        $this->title = $title;
        $this->content = $content;
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

    // Метод-сеттер для подвала
    public function setFooter($footer)
    {
        $this->footer = $footer;
    }

    public function render()
    {
        echo '<html>';
        echo '<head><title>' . $this->title . '</title></head>';
        echo '<body>';

        if ($this->header !== null) {
            $this->header->render();
        }

        if ($this->menu !== null) {
            $this->menu->render();
        }

        echo '<h1>' . $this->title . '</h1>';
        echo $this->content;

        if ($this->footer !== null) {
            $this->footer->render();
        }

        echo '</body></html>';
    }
}
