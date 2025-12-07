<?php

namespace App\Libraries\Media;

use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;

/**
 * Image Processing Service
 * 
 * Handles image manipulation and optimization
 * Requires: composer require intervention/image
 */
class ImageService
{
    protected $manager;

    public function __construct()
    {
        $this->manager = new ImageManager(new Driver());
    }

    /**
     * Resize image
     */
    public function resize($sourcePath, $width, $height = null, $aspectRatio = true)
    {
        $image = $this->manager->read($sourcePath);

        if ($aspectRatio) {
            $image->scale($width, $height);
        } else {
            $image->resize($width, $height);
        }

        return $image;
    }

    /**
     * Create thumbnail
     */
    public function createThumbnail($sourcePath, $destinationPath, $width = 150, $height = 150)
    {
        try {
            $image = $this->manager->read($sourcePath);
            $image->cover($width, $height);
            $image->save($destinationPath);

            return [
                'success' => true,
                'path' => $destinationPath,
                'url' => $this->pathToUrl($destinationPath)
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }

    /**
     * Compress image
     */
    public function compress($sourcePath, $destinationPath = null, $quality = 75)
    {
        if (!$destinationPath) {
            $destinationPath = $sourcePath;
        }

        try {
            $image = $this->manager->read($sourcePath);
            
            // Get file extension
            $extension = strtolower(pathinfo($sourcePath, PATHINFO_EXTENSION));
            
            // Save with compression
            if ($extension === 'jpg' || $extension === 'jpeg') {
                $image->toJpeg($quality)->save($destinationPath);
            } elseif ($extension === 'png') {
                $image->toPng()->save($destinationPath);
            } elseif ($extension === 'webp') {
                $image->toWebp($quality)->save($destinationPath);
            }

            return [
                'success' => true,
                'path' => $destinationPath,
                'original_size' => filesize($sourcePath),
                'compressed_size' => filesize($destinationPath),
                'savings_percent' => round((1 - filesize($destinationPath) / filesize($sourcePath)) * 100, 2)
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }

    /**
     * Add watermark
     */
    public function addWatermark($sourcePath, $watermarkPath, $position = 'bottom-right', $opacity = 50)
    {
        try {
            $image = $this->manager->read($sourcePath);
            $watermark = $this->manager->read($watermarkPath);

            // Resize watermark if needed (20% of image width)
            $watermarkWidth = $image->width() * 0.2;
            $watermark->scale($watermarkWidth);

            // Set watermark opacity
            $watermark->opacity($opacity);

            // Determine position
            $positions = [
                'top-left' => ['x' => 10, 'y' => 10],
                'top-right' => ['x' => $image->width() - $watermark->width() - 10, 'y' => 10],
                'bottom-left' => ['x' => 10, 'y' => $image->height() - $watermark->height() - 10],
                'bottom-right' => ['x' => $image->width() - $watermark->width() - 10, 'y' => $image->height() - $watermark->height() - 10],
                'center' => ['x' => ($image->width() - $watermark->width()) / 2, 'y' => ($image->height() - $watermark->height()) / 2],
            ];

            $pos = $positions[$position] ?? $positions['bottom-right'];

            // Place watermark
            $image->place($watermark, 'top-left', $pos['x'], $pos['y']);
            $image->save($sourcePath);

            return [
                'success' => true,
                'path' => $sourcePath
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }

    /**
     * Convert to WebP
     */
    public function convertToWebp($sourcePath, $destinationPath = null, $quality = 80)
    {
        if (!$destinationPath) {
            $destinationPath = preg_replace('/\.[^.]+$/', '.webp', $sourcePath);
        }

        try {
            $image = $this->manager->read($sourcePath);
            $image->toWebp($quality)->save($destinationPath);

            return [
                'success' => true,
                'path' => $destinationPath,
                'url' => $this->pathToUrl($destinationPath)
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }

    /**
     * Crop image
     */
    public function crop($sourcePath, $width, $height, $x = 0, $y = 0)
    {
        try {
            $image = $this->manager->read($sourcePath);
            $image->crop($width, $height, $x, $y);
            $image->save($sourcePath);

            return [
                'success' => true,
                'path' => $sourcePath
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }

    /**
     * Process profile photo
     */
    public function processProfilePhoto($uploadedFile, $userId)
    {
        $uploadPath = FCPATH . 'uploads/foto_profile/';
        
        if (!is_dir($uploadPath)) {
            mkdir($uploadPath, 0755, true);
        }

        $filename = 'profile_' . $userId . '_' . time() . '.jpg';
        $filepath = $uploadPath . $filename;

        try {
            // Read and process image
            $image = $this->manager->read($uploadedFile->getTempName());
            
            // Resize to 300x300
            $image->cover(300, 300);
            
            // Convert to JPEG with quality 85
            $image->toJpeg(85)->save($filepath);

            // Create thumbnail
            $thumbPath = $uploadPath . 'thumb_' . $filename;
            $this->createThumbnail($filepath, $thumbPath, 100, 100);

            return [
                'success' => true,
                'filename' => $filename,
                'path' => $filepath,
                'url' => base_url('uploads/foto_profile/' . $filename),
                'thumbnail' => base_url('uploads/foto_profile/thumb_' . $filename)
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }

    /**
     * Process dokumentasi photo
     */
    public function processDokumentasiPhoto($uploadedFile, $sppdId)
    {
        $uploadPath = FCPATH . 'uploads/dokumentasi_kegiatan/';
        
        if (!is_dir($uploadPath)) {
            mkdir($uploadPath, 0755, true);
        }

        $filename = 'dok_' . $sppdId . '_' . time() . '_' . uniqid() . '.jpg';
        $filepath = $uploadPath . $filename;

        try {
            // Read and process image
            $image = $this->manager->read($uploadedFile->getTempName());
            
            // Resize if too large (max 1200px width)
            if ($image->width() > 1200) {
                $image->scale(1200);
            }
            
            // Compress
            $image->toJpeg(80)->save($filepath);

            // Add watermark if configured
            $watermarkPath = FCPATH . 'assets/images/watermark.png';
            if (file_exists($watermarkPath)) {
                $this->addWatermark($filepath, $watermarkPath);
            }

            return [
                'success' => true,
                'filename' => $filename,
                'path' => $filepath,
                'url' => base_url('uploads/dokumentasi_kegiatan/' . $filename),
                'size' => filesize($filepath)
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }

    /**
     * Process bukti upload (receipt)
     */
    public function processBuktiUpload($uploadedFile, $type = 'perjalanan')
    {
        $uploadPath = FCPATH . 'uploads/bukti_' . $type . '/';
        
        if (!is_dir($uploadPath)) {
            mkdir($uploadPath, 0755, true);
        }

        $extension = $uploadedFile->getExtension();
        $filename = 'bukti_' . $type . '_' . time() . '_' . uniqid() . '.' . $extension;
        $filepath = $uploadPath . $filename;

        try {
            // If image, process it
            if (in_array($extension, ['jpg', 'jpeg', 'png'])) {
                $image = $this->manager->read($uploadedFile->getTempName());
                
                // Resize if too large
                if ($image->width() > 1600) {
                    $image->scale(1600);
                }
                
                // Compress
                $image->toJpeg(85)->save($filepath);
            } else {
                // PDF, just move
                $uploadedFile->move($uploadPath, $filename);
            }

            return [
                'success' => true,
                'filename' => $filename,
                'path' => $filepath,
                'url' => base_url('uploads/bukti_' . $type . '/' . $filename),
                'size' => filesize($filepath)
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }

    /**
     * Delete image and its thumbnail
     */
    public function deleteImage($filepath)
    {
        if (file_exists($filepath)) {
            unlink($filepath);

            // Delete thumbnail if exists
            $dir = dirname($filepath);
            $filename = basename($filepath);
            $thumbPath = $dir . '/thumb_' . $filename;
            
            if (file_exists($thumbPath)) {
                unlink($thumbPath);
            }

            return true;
        }

        return false;
    }

    /**
     * Convert file path to URL
     */
    protected function pathToUrl($path)
    {
        $relativePath = str_replace(FCPATH, '', $path);
        return base_url($relativePath);
    }

    /**
     * Get image dimensions
     */
    public function getDimensions($filepath)
    {
        try {
            $image = $this->manager->read($filepath);
            return [
                'width' => $image->width(),
                'height' => $image->height()
            ];
        } catch (\Exception $e) {
            return null;
        }
    }

    /**
     * Validate image file
     */
    public function validateImage($file, $maxSize = 5120)
    {
        $errors = [];

        // Check file validity
        if (!$file->isValid()) {
            $errors[] = 'File tidak valid';
        }

        // Check mime type
        $allowedMimes = ['image/jpeg', 'image/jpg', 'image/png'];
        if (!in_array($file->getMimeType(), $allowedMimes)) {
            $errors[] = 'File harus berupa gambar (JPG, JPEG, PNG)';
        }

        // Check file size (in KB)
        if ($file->getSize() > ($maxSize * 1024)) {
            $errors[] = 'Ukuran file maksimal ' . format_file_size($maxSize * 1024);
        }

        return [
            'valid' => empty($errors),
            'errors' => $errors
        ];
    }
}