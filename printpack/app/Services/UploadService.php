<?php
declare(strict_types=1);
namespace App\Services;
final class UploadService {
    private const MIME_EXT = ['application/pdf'=>'pdf','image/jpeg'=>'jpg','image/png'=>'png','image/webp'=>'webp'];
    public static function saveDesign(array $file): ?string {
        if (($file['error'] ?? UPLOAD_ERR_NO_FILE) === UPLOAD_ERR_NO_FILE) return null;
        if (($file['error'] ?? UPLOAD_ERR_OK) !== UPLOAD_ERR_OK) throw new \RuntimeException('Upload failed. Please try again.');
        $max = (int)(\setting('UPLOAD_MAX_MB', '15')) * 1024 * 1024;
        if (($file['size'] ?? 0) > $max) throw new \RuntimeException('Design file must be 15 MB or smaller.');
        $finfo = new \finfo(FILEINFO_MIME_TYPE);
        $mime = $finfo->file($file['tmp_name']);
        if (!isset(self::MIME_EXT[$mime])) throw new \RuntimeException('Only PDF, JPG, PNG, and WEBP files are allowed.');
        $dir = PUBLIC_PATH . '/uploads/designs';
        if (!is_dir($dir) && !mkdir($dir, 0750, true) && !is_dir($dir)) throw new \RuntimeException('Upload directory unavailable.');
        $name = bin2hex(random_bytes(20)) . '.' . self::MIME_EXT[$mime];
        if (!move_uploaded_file($file['tmp_name'], $dir . '/' . $name)) throw new \RuntimeException('Could not save design file.');
        return $name;
    }
}
