<?php

namespace Core;

/**
 * Image Upload and Processing Class
 */
class ImageUploader
{
    private array $allowedTypes = ['image/jpeg', 'image/png', 'image/webp'];
    private int $maxSize = 5 * 1024 * 1024; // 5MB
    private string $uploadPath;
    private array $thumbnailSizes = [
        'small' => [150, 150],
        'medium' => [300, 300], 
        'large' => [800, 600]
    ];

    public function __construct(string $uploadPath = null)
    {
        $this->uploadPath = $uploadPath ?? __DIR__ . '/../public/uploads/';
        
        // Создаем директорию если не существует
        if (!is_dir($this->uploadPath)) {
            mkdir($this->uploadPath, 0755, true);
        }
    }

    /**
     * Загрузить одно изображение
     */
    public function uploadSingle(array $file, string $directory = '', string $prefix = ''): array
    {
        // Проверка на ошибки загрузки
        if ($file['error'] !== UPLOAD_ERR_OK) {
            throw new \Exception($this->getUploadErrorMessage($file['error']));
        }

        // Валидация файла
        $this->validateFile($file);

        // Создание директории для файлов
        $targetDir = $this->uploadPath . $directory . '/';
        if (!is_dir($targetDir)) {
            mkdir($targetDir, 0755, true);
        }

        // Генерация имени файла
        $extension = $this->getFileExtension($file['name']);
        $filename = $prefix . uniqid() . '.' . $extension;
        $targetPath = $targetDir . $filename;

        // Дополнительная проверка типа файла по содержимому
        $imageInfo = getimagesize($file['tmp_name']);
        if ($imageInfo === false) {
            throw new \Exception('Файл не является изображением');
        }

        // Перемещение файла
        if (!move_uploaded_file($file['tmp_name'], $targetPath)) {
            throw new \Exception('Ошибка при сохранении файла');
        }

        // Создание миниатюр
        $thumbnails = $this->createThumbnails($targetPath, $directory);

        return [
            'filename' => $filename,
            'original_name' => $file['name'],
            'path' => $directory . '/' . $filename,
            'size' => $file['size'],
            'type' => $imageInfo['mime'],
            'width' => $imageInfo[0],
            'height' => $imageInfo[1],
            'thumbnails' => $thumbnails
        ];
    }

    /**
     * Загрузить множество изображений
     */
    public function uploadMultiple(array $files, string $directory = '', string $prefix = ''): array
    {
        $results = [];
        $errors = [];

        // Нормализация массива файлов
        $normalizedFiles = $this->normalizeFilesArray($files);

        foreach ($normalizedFiles as $index => $file) {
            try {
                $results[] = $this->uploadSingle($file, $directory, $prefix . $index . '_');
            } catch (\Exception $e) {
                $errors[] = "Файл {$file['name']}: " . $e->getMessage();
            }
        }

        return [
            'uploaded' => $results,
            'errors' => $errors
        ];
    }

    /**
     * Создать миниатюры изображения
     */
    public function createThumbnails(string $sourcePath, string $directory = ''): array
    {
        $thumbnails = [];
        
        foreach ($this->thumbnailSizes as $size => [$width, $height]) {
            try {
                $thumbnail = $this->createThumbnail($sourcePath, $width, $height, $directory, $size);
                if ($thumbnail) {
                    $thumbnails[$size] = $thumbnail;
                }
            } catch (\Exception $e) {
                // Логируем ошибку, но продолжаем работу
                error_log("Thumbnail creation failed: " . $e->getMessage());
            }
        }

        return $thumbnails;
    }

    /**
     * Создать отдельную миниатюру
     */
    private function createThumbnail(string $sourcePath, int $width, int $height, string $directory, string $suffix): ?string
    {
        $imageInfo = getimagesize($sourcePath);
        if (!$imageInfo) {
            return null;
        }

        // Создание ресурса изображения
        $sourceImage = $this->createImageResource($sourcePath, $imageInfo['mime']);
        if (!$sourceImage) {
            return null;
        }

        $sourceWidth = $imageInfo[0];
        $sourceHeight = $imageInfo[1];

        // Вычисление размеров с сохранением пропорций
        $ratio = min($width / $sourceWidth, $height / $sourceHeight);
        $newWidth = (int)($sourceWidth * $ratio);
        $newHeight = (int)($sourceHeight * $ratio);

        // Создание миниатюры
        $thumbnail = imagecreatetruecolor($newWidth, $newHeight);
        
        // Сохранение прозрачности для PNG и WebP
        if ($imageInfo['mime'] === 'image/png' || $imageInfo['mime'] === 'image/webp') {
            imagealphablending($thumbnail, false);
            imagesavealpha($thumbnail, true);
            $transparent = imagecolorallocatealpha($thumbnail, 255, 255, 255, 127);
            imagefill($thumbnail, 0, 0, $transparent);
        }

        imagecopyresampled($thumbnail, $sourceImage, 0, 0, 0, 0, $newWidth, $newHeight, $sourceWidth, $sourceHeight);

        // Сохранение миниатюры
        $pathInfo = pathinfo($sourcePath);
        $thumbnailName = $pathInfo['filename'] . '_' . $suffix . '.' . $pathInfo['extension'];
        $thumbnailPath = $this->uploadPath . $directory . '/thumbs/' . $thumbnailName;
        
        // Создание директории для миниатюр
        $thumbDir = dirname($thumbnailPath);
        if (!is_dir($thumbDir)) {
            mkdir($thumbDir, 0755, true);
        }

        $success = false;
        switch ($imageInfo['mime']) {
            case 'image/jpeg':
                $success = imagejpeg($thumbnail, $thumbnailPath, 90);
                break;
            case 'image/png':
                $success = imagepng($thumbnail, $thumbnailPath, 9);
                break;
            case 'image/webp':
                $success = imagewebp($thumbnail, $thumbnailPath, 90);
                break;
        }

        // Освобождение памяти
        imagedestroy($sourceImage);
        imagedestroy($thumbnail);

        return $success ? $directory . '/thumbs/' . $thumbnailName : null;
    }

    /**
     * Удалить изображение и его миниатюры
     */
    public function deleteImage(string $path): bool
    {
        $fullPath = $this->uploadPath . $path;
        $deleted = false;

        // Удаление основного файла
        if (file_exists($fullPath)) {
            $deleted = unlink($fullPath);
        }

        // Удаление миниатюр
        $pathInfo = pathinfo($fullPath);
        $thumbDir = $pathInfo['dirname'] . '/thumbs/';
        $filename = $pathInfo['filename'];
        
        foreach ($this->thumbnailSizes as $size => $dimensions) {
            $thumbPath = $thumbDir . $filename . '_' . $size . '.' . $pathInfo['extension'];
            if (file_exists($thumbPath)) {
                unlink($thumbPath);
            }
        }

        return $deleted;
    }

    /**
     * Валидация файла
     */
    private function validateFile(array $file): void
    {
        // Проверка размера
        if ($file['size'] > $this->maxSize) {
            throw new \Exception('Файл слишком большой. Максимальный размер: ' . $this->formatBytes($this->maxSize));
        }

        // Проверка типа MIME
        if (!in_array($file['type'], $this->allowedTypes)) {
            throw new \Exception('Недопустимый тип файла. Разрешены: JPEG, PNG, WebP');
        }

        // Дополнительная проверка по расширению
        $extension = strtolower($this->getFileExtension($file['name']));
        $allowedExtensions = ['jpg', 'jpeg', 'png', 'webp'];
        
        if (!in_array($extension, $allowedExtensions)) {
            throw new \Exception('Недопустимое расширение файла');
        }
    }

    /**
     * Создание ресурса изображения
     */
    private function createImageResource(string $path, string $mimeType)
    {
        switch ($mimeType) {
            case 'image/jpeg':
                return imagecreatefromjpeg($path);
            case 'image/png':
                return imagecreatefrompng($path);
            case 'image/webp':
                return imagecreatefromwebp($path);
            default:
                return false;
        }
    }

    /**
     * Нормализация массива файлов для множественной загрузки
     */
    private function normalizeFilesArray(array $files): array
    {
        $normalized = [];
        
        if (isset($files['name']) && is_array($files['name'])) {
            for ($i = 0; $i < count($files['name']); $i++) {
                $normalized[] = [
                    'name' => $files['name'][$i],
                    'type' => $files['type'][$i],
                    'tmp_name' => $files['tmp_name'][$i],
                    'error' => $files['error'][$i],
                    'size' => $files['size'][$i]
                ];
            }
        } else {
            $normalized[] = $files;
        }
        
        return $normalized;
    }

    /**
     * Получить расширение файла
     */
    private function getFileExtension(string $filename): string
    {
        return strtolower(pathinfo($filename, PATHINFO_EXTENSION));
    }

    /**
     * Форматирование размера в байтах
     */
    private function formatBytes(int $bytes): string
    {
        $units = ['B', 'KB', 'MB', 'GB'];
        
        for ($i = 0; $bytes > 1024 && $i < count($units) - 1; $i++) {
            $bytes /= 1024;
        }
        
        return round($bytes, 2) . ' ' . $units[$i];
    }

    /**
     * Получить сообщение об ошибке загрузки
     */
    private function getUploadErrorMessage(int $error): string
    {
        switch ($error) {
            case UPLOAD_ERR_INI_SIZE:
            case UPLOAD_ERR_FORM_SIZE:
                return 'Файл слишком большой';
            case UPLOAD_ERR_PARTIAL:
                return 'Файл загружен не полностью';
            case UPLOAD_ERR_NO_FILE:
                return 'Файл не был загружен';
            case UPLOAD_ERR_NO_TMP_DIR:
                return 'Отсутствует временная директория';
            case UPLOAD_ERR_CANT_WRITE:
                return 'Ошибка записи файла на диск';
            case UPLOAD_ERR_EXTENSION:
                return 'Загрузка файла остановлена расширением';
            default:
                return 'Неизвестная ошибка загрузки';
        }
    }

    /**
     * Установить максимальный размер файла
     */
    public function setMaxSize(int $bytes): void
    {
        $this->maxSize = $bytes;
    }

    /**
     * Установить разрешенные типы файлов
     */
    public function setAllowedTypes(array $types): void
    {
        $this->allowedTypes = $types;
    }

    /**
     * Добавить размер миниатюры
     */
    public function addThumbnailSize(string $name, int $width, int $height): void
    {
        $this->thumbnailSizes[$name] = [$width, $height];
    }

    /**
     * Получить информацию об изображении
     */
    public function getImageInfo(string $path): ?array
    {
        $fullPath = $this->uploadPath . $path;
        
        if (!file_exists($fullPath)) {
            return null;
        }
        
        $imageInfo = getimagesize($fullPath);
        if (!$imageInfo) {
            return null;
        }
        
        return [
            'width' => $imageInfo[0],
            'height' => $imageInfo[1],
            'type' => $imageInfo['mime'],
            'size' => filesize($fullPath),
            'path' => $path
        ];
    }
}