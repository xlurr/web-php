<?php

use classes\Base\Header;
use classes\Base\Menu;
use classes\Footer;
use classes\Page;

require_once 'autoloader.php';

class AboutPage extends Page {
    public function __construct() {
        parent::__construct('О нас', '<p>Здесь представлена информация о нашей компании.</p>');
        $this->setHeader(new Header());
        $this->setMenu(new Menu());
        $this->setFooter(new Footer());
    }
}

$page = new AboutPage();
$page->render();
