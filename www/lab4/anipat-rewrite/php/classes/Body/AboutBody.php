<?php

namespace classes\Body;

use classes\Base\BodyInterface;

class AboutBody implements BodyInterface
{
    public function render(): void
    {
        ?>
        <body>
        <div class="bradcam_area breadcam_bg">
            <div class="container">
                <div class="row">
                    <div class="col-lg-12">
                        <div class="bradcam_text text-center">
                            <h3>About Us</h3>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        </body>
        <?php
    }
}