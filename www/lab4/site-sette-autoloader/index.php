<?php

use classes\Base\Header;
use classes\Base\Menu;
use classes\Footer;
use classes\Page;

require_once 'autoloader.php';


class HomePage extends Page {
    public function __construct() {
        parent::__construct('Главная', '<p>Добро пожаловать на главную страницу!</p>');
        $this->setHeader(new Header());
        $this->setMenu(new Menu());
        $this->setFooter(new Footer());
    }
}

$page = new HomePage();
$page->render();

