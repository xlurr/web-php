<?php

use classes\Base\Header;
use classes\Base\Menu;
use classes\Footer;
use classes\Page;

require_once 'autoloader.php';

class ContactPage extends Page {
    public function __construct() {
        parent::__construct('Контакты', '<p>Свяжитесь с нами по телефону или электронной почте.</p>');
        $this->setHeader(new Header());
        $this->setMenu(new Menu());
        $this->setFooter(new Footer());
    }
}

$page = new ContactPage();
$page->render();
