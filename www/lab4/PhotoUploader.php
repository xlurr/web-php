<?php
require_once 'FileUploader.php';

class PhotoUploader extends FileUploader {
    public function __construct($uploadDir = 'uploads/photos/') {
        parent::__construct($uploadDir);
        $this->allowedTypes = ['jpg','jpeg','png','gif','webp'];
        $this->maxSize = 10 * 1024 * 1024;
    }

    protected function generateCardBody($data, $path) {
        return '<div class="card-body text-center">
                    <img src="'.$path.'" class="card-img-top mb-3" style="max-height:200px;object-fit:cover;">
                    <h5>'.htmlspecialchars($data['original_name']).'</h5>
                    <p>'.htmlspecialchars($data['comment']).'</p>
                    <a href="'.$path.'" class="btn btn-primary" download>Скачать</a>
                </div>';
    }
}
?>