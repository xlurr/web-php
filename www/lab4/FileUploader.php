<?php
class FileUploader {
    protected $uploadDir;
    protected $allowedTypes = [];
    protected $maxSize = 5242880; // 5MB
    protected $dataFile = 'files_data.json';

    public function __construct($uploadDir = 'uploads/') {
        $this->uploadDir = $uploadDir;
        if (!file_exists($uploadDir)) mkdir($uploadDir, 0777, true);
    }

    public function uploadFile($file, $comment = '') {
        if ($file['error'] !== UPLOAD_ERR_OK) throw new Exception('Ошибка загрузки');

        $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        if (!in_array($ext, $this->allowedTypes)) throw new Exception('Недопустимый тип');
        if ($file['size'] > $this->maxSize) throw new Exception('Файл слишком большой');

        $fileName = uniqid() . '_' . basename($file['name']);
        $filePath = $this->uploadDir . $fileName;
        move_uploaded_file($file['tmp_name'], $filePath);

        $this->saveFileData($fileName, $file['name'], $comment, $ext);
        return $fileName;
    }

    public function deleteFile($fileName) {
        $path = $this->uploadDir . $fileName;
        if (file_exists($path)) unlink($path);
        $this->removeFileData($fileName);
    }

    protected function saveFileData($fileName, $original, $comment, $type) {
        $data = $this->loadFileData();
        $data[] = [
            'file_name' => $fileName,
            'original_name' => $original,
            'comment' => $comment,
            'type' => $type,
            'upload_date' => date('Y-m-d H:i:s'),
            'class_type' => get_class($this)
        ];
        file_put_contents($this->dataFile, json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
    }

    protected function removeFileData($fileName) {
        $data = array_filter($this->loadFileData(), fn($d) => $d['file_name'] !== $fileName);
        file_put_contents($this->dataFile, json_encode(array_values($data), JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
    }

    public function loadFileData() {
        return file_exists($this->dataFile)
            ? json_decode(file_get_contents($this->dataFile), true) ?? []
            : [];
    }

    public function getAllFiles() { return $this->loadFileData(); }

    public function generateCard($fileData) {
        $path = $this->uploadDir . $fileData['file_name'];
        $card = '<div class="col-md-4 mb-4"><div class="card h-100">';
        $card .= $this->generateCardBody($fileData, $path);
        $card .= '<div class="card-footer"><small class="text-muted">'
            . $fileData['upload_date'] . '</small>
            <form method="get">
                <input type="hidden" name="delete" value="'.$fileData['file_name'].'">
                <button type="submit" class="btn btn-danger btn-sm float-end">Удалить</button>
            </form></div></div></div>';
        return $card;
    }

    protected function generateCardBody($data, $path) {
        return '<div class="card-body text-center">
                    <h5>'.htmlspecialchars($data['original_name']).'</h5>
                    <p>'.htmlspecialchars($data['comment']).'</p>
                    <a href="'.$path.'" class="btn btn-primary" download>Скачать</a>
                </div>';
    }
}
?>