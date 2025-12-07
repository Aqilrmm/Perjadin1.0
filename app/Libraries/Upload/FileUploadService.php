<?php

namespace App\Libraries\Upload;

/**
 * File Upload Service
 * 
 * Handles file upload operations with validation, compression, and storage management
 */
class FileUploadService
{
    protected $uploadPath = FCPATH . 'uploads/';
    protected $allowedTypes = [
        'surat_tugas' => ['pdf'],
        'bukti_perjalanan' => ['jpg', 'jpeg', 'png', 'pdf'],
        'bukti_penginapan' => ['jpg', 'jpeg', 'png', 'pdf'],
        'bukti_taxi' => ['jpg', 'jpeg', 'png', 'pdf'],
        'bukti_tiket' => ['jpg', 'jpeg', 'png', 'pdf'],
        'dokumentasi' => ['jpg', 'jpeg', 'png'],
        'foto_profile' => ['jpg', 'jpeg', 'png'],
    ];
    protected $maxSizes = [
        'surat_tugas' => 5120, // 5MB in KB
        'bukti_perjalanan' => 2048, // 2MB
        'bukti_penginapan' => 2048,
        'bukti_taxi' => 2048,
        'bukti_tiket' => 2048,
        'dokumentasi' => 2048,
        'foto_profile' => 1024, // 1MB
    ];

    /**
     * Upload single file
     * 
     * @param \CodeIgniter\HTTP\Files\UploadedFile $file
     * @param string $type
     * @return array|false
     */
    public function upload($file, string $type)
    {
        if (!$file || !$file->isValid()) {
            return false;
        }

        // Validate file
        $validation = $this->validateFile($file, $type);
        if ($validation !== true) {
            return ['error' => $validation];
        }

        // Generate filename
        $filename = $this->generateFilename($file->getName(), $type);

        // Determine directory
        $directory = $this->uploadPath . $type . '/';

        // Create directory if not exists
        if (!is_dir($directory)) {
            mkdir($directory, 0755, true);
        }

        // For images, compress if needed
        if ($this->isValidImage($file)) {
            $filepath = $directory . $filename;
            
            // Save temporarily
            $file->move($directory, $filename);
            
            // Compress if > 500KB
            if (filesize($filepath) > 512000) {
                $this->compressImage($filepath, 85);
            }
        } else {
            // Move file directly for non-images
            if (!$this->moveToStorage($file, $directory, $filename)) {
                return false;
            }
        }

        return [
            'filename' => $filename,
            'path' => $type . '/' . $filename,
            'url' => base_url('uploads/' . $type . '/' . $filename),
            'size' => filesize($directory . $filename),
            'type' => $file->getMimeType()
        ];
    }

    /**
     * Upload multiple files
     * 
     * @param array $files
     * @param string $type
     * @return array
     */
    public function uploadMultiple(array $files, string $type): array
    {
        $results = [];
        
        foreach ($files as $file) {
            $result = $this->upload($file, $type);
            if ($result) {
                $results[] = $result;
            }
        }
        
        return $results;
    }

    /**
     * Delete file from storage
     * 
     * @param string $filepath Relative path from uploads directory
     * @return bool
     */
    public function delete(string $filepath): bool
    {
        $fullPath = $this->uploadPath . $filepath;
        
        if (file_exists($fullPath)) {
            return unlink($fullPath);
        }
        
        return false;
    }

    /**
     * Validate file before upload
     * 
     * @param \CodeIgniter\HTTP\Files\UploadedFile $file
     * @param string $type
     * @return bool|string
     */
    public function validateFile($file, string $type)
    {
        // Check if type is allowed
        if (!isset($this->allowedTypes[$type])) {
            return 'Tipe file tidak dikenali';
        }

        // Check extension
        $extension = strtolower($file->getExtension());
        if (!in_array($extension, $this->allowedTypes[$type])) {
            return 'Ekstensi file tidak diizinkan. Gunakan: ' . implode(', ', $this->allowedTypes[$type]);
        }

        // Check file size
        $maxSize = $this->maxSizes[$type] ?? 2048;
        if ($file->getSize() > ($maxSize * 1024)) {
            return 'Ukuran file melebihi batas maksimal ' . number_format($maxSize / 1024, 0) . 'MB';
        }

        // Validate mime type for images
        if ($this->isValidImage($file)) {
            $allowedMimes = ['image/jpeg', 'image/jpg', 'image/png'];
            if (!in_array($file->getMimeType(), $allowedMimes)) {
                return 'Tipe MIME file tidak valid untuk gambar';
            }
        }

        return true;
    }

    /**
     * Compress image
     * 
     * @param string $filepath
     * @param int $quality (0-100)
     * @return bool
     */
    public function compressImage(string $filepath, int $quality = 85): bool
    {
        $info = getimagesize($filepath);
        if (!$info) {
            return false;
        }

        $mime = $info['mime'];
        
        switch ($mime) {
            case 'image/jpeg':
            case 'image/jpg':
                $image = imagecreatefromjpeg($filepath);
                break;
            case 'image/png':
                $image = imagecreatefrompng($filepath);
                break;
            default:
                return false;
        }

        if (!$image) {
            return false;
        }

        // Save compressed
        if ($mime === 'image/png') {
            // PNG compression (0-9, where 9 is max compression)
            $pngQuality = floor((100 - $quality) / 11);
            imagepng($image, $filepath, $pngQuality);
        } else {
            imagejpeg($image, $filepath, $quality);
        }

        imagedestroy($image);
        return true;
    }

    /**
     * Resize image
     * 
     * @param string $filepath
     * @param int $width
     * @param int $height
     * @return bool
     */
    public function resizeImage(string $filepath, int $width, int $height): bool
    {
        $info = getimagesize($filepath);
        if (!$info) {
            return false;
        }

        list($origWidth, $origHeight) = $info;
        $mime = $info['mime'];

        // Calculate aspect ratio
        $ratio = $origWidth / $origHeight;
        if ($width / $height > $ratio) {
            $width = $height * $ratio;
        } else {
            $height = $width / $ratio;
        }

        // Create source image
        switch ($mime) {
            case 'image/jpeg':
            case 'image/jpg':
                $source = imagecreatefromjpeg($filepath);
                break;
            case 'image/png':
                $source = imagecreatefrompng($filepath);
                break;
            default:
                return false;
        }

        if (!$source) {
            return false;
        }

        // Create new image
        $thumb = imagecreatetruecolor($width, $height);
        
        // Preserve transparency for PNG
        if ($mime === 'image/png') {
            imagealphablending($thumb, false);
            imagesavealpha($thumb, true);
        }

        // Resize
        imagecopyresampled($thumb, $source, 0, 0, 0, 0, $width, $height, $origWidth, $origHeight);

        // Save
        if ($mime === 'image/png') {
            imagepng($thumb, $filepath, 9);
        } else {
            imagejpeg($thumb, $filepath, 90);
        }

        imagedestroy($source);
        imagedestroy($thumb);

        return true;
    }

    /**
     * Generate unique filename
     * 
     * @param string $originalName
     * @param string $prefix
     * @return string
     */
    public function generateFilename(string $originalName, string $prefix): string
    {
        $extension = pathinfo($originalName, PATHINFO_EXTENSION);
        return $prefix . '_' . time() . '_' . uniqid() . '.' . $extension;
    }

    /**
     * Move file to storage
     * 
     * @param \CodeIgniter\HTTP\Files\UploadedFile $file
     * @param string $directory
     * @param string $filename
     * @return bool
     */
    public function moveToStorage($file, string $directory, string $filename): bool
    {
        return $file->move($directory, $filename);
    }

    /**
     * Get file information
     * 
     * @param string $filepath
     * @return array|false
     */
    public function getFileInfo(string $filepath)
    {
        $fullPath = $this->uploadPath . $filepath;
        
        if (!file_exists($fullPath)) {
            return false;
        }

        return [
            'size' => filesize($fullPath),
            'type' => mime_content_type($fullPath),
            'modified' => date('Y-m-d H:i:s', filemtime($fullPath)),
            'url' => base_url('uploads/' . $filepath)
        ];
    }

    /**
     * Check if file is valid image
     * 
     * @param \CodeIgniter\HTTP\Files\UploadedFile $file
     * @return bool
     */
    public function isValidImage($file): bool
    {
        $extension = strtolower($file->getExtension());
        return in_array($extension, ['jpg', 'jpeg', 'png']);
    }

    /**
     * Set custom upload path
     * 
     * @param string $path
     * @return self
     */
    public function setUploadPath(string $path): self
    {
        $this->uploadPath = $path;
        return $this;
    }

    /**
     * Set custom allowed types for specific upload type
     * 
     * @param string $type
     * @param array $extensions
     * @return self
     */
    public function setAllowedTypes(string $type, array $extensions): self
    {
        $this->allowedTypes[$type] = $extensions;
        return $this;
    }

    /**
     * Set custom max size for specific upload type
     * 
     * @param string $type
     * @param int $sizeInKB
     * @return self
     */
    public function setMaxSize(string $type, int $sizeInKB): self
    {
        $this->maxSizes[$type] = $sizeInKB;
        return $this;
    }
}