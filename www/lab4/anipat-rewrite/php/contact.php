<?php


use classes\Base\Head;
use classes\Base\Header;
use classes\Base\Menu;
use classes\Base\Footer;
use classes\Base\Page;

require_once 'function/autoloader.php';
class ContactPage extends Page {
    public function __construct() {
        parent::__construct();
        $this->setHead(new Head());
        $this->setHeader(new Header());
        $this->setMenu(new Menu());
        $this->setFooter(new Footer());
    }
}

$page = new ContactPage();
$page->render();