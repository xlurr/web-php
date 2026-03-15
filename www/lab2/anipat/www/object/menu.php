<?php
$menu = array(
        "home"     => "IndexBody.php",
        "about"    => "about.php",
        "services" => "services.php",
        "contact"  => "contact.php",
        "animals"  => "animals.php",
);
?>

<div id="sticky-header" class="main-header-area">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-xl-3 col-lg-3">
                <div class="logo">
                    <a href="<?= $menu['home'] ?>">
                        <img src="../../img/logo.png" alt="">
                    </a>
                </div>
            </div>
            <div class="col-xl-9 col-lg-9">
                <div class="main-menu d-none d-lg-block">
                    <nav>
                        <ul id="navigation">
                            <?php foreach ($menu as $name => $link): ?>
                                <li><a href="<?= $link ?>"><?= ucfirst($name) ?></a></li>
                            <?php endforeach; ?>
                        </ul>
                    </nav>
                </div>
            </div>
            <div class="col-12">
                <div class="mobile_menu d-block d-lg-none"></div>
            </div>
        </div>
    </div>
</div>
