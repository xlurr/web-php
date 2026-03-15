<?php


use classes\Base\Head;
use classes\Base\Menu;
use classes\Base\Footer;
use classes\Base\Page;
use classes\Body\ServicesBody;

require_once 'function/autoloader.php';
class AnimalsPage extends Page {
    public function __construct() {
        parent::__construct(new ServicesBody());
        $this->setHead(new Head());
        $this->setMenu(new Menu());
        $this->setFooter(new Footer());
    }
}

$page = new AnimalsPage();
$page->render();