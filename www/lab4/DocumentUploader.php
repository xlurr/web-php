<?php
require_once 'FileUploader.php';

class DocumentUploader extends FileUploader {
    public function __construct($uploadDir = 'uploads/docs/') {
        parent::__construct($uploadDir);
        $this->allowedTypes = ['pdf','doc','docx','txt','xls','xlsx','ppt','pptx'];
        $this->maxSize = 20 * 1024 * 1024;
    }

    protected function generateCardBody($data, $path) {
        return '<div class="card-body text-center">
                    <i class="bi bi-file-earmark-text" style="font-size:3rem;"></i>
                    <h5>'.htmlspecialchars($data['original_name']).'</h5>
                    <p>'.htmlspecialchars($data['comment']).'</p>
                    <a href="'.$path.'" class="btn btn-primary" download>Скачать</a>
                </div>';
    }
}
?>