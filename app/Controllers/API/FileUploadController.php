<?php

// ========================================
// FILE UPLOAD CONTROLLER
// ========================================

namespace App\Controllers\API;

use App\Controllers\BaseController;

class FileUploadController extends BaseController
{
    /**
     * Upload file
     */
    public function upload()
    {
        $type = $this->request->getPost('type');
        $maxSize = $this->request->getPost('max_size') ?: 5120; // Default 5MB in KB

        $allowedTypes = [
            'surat_tugas' => ['pdf'],
            'bukti_perjalanan' => ['jpg', 'jpeg', 'png', 'pdf'],
            'bukti_penginapan' => ['jpg', 'jpeg', 'png', 'pdf'],
            'bukti_taxi' => ['jpg', 'jpeg', 'png', 'pdf'],
            'bukti_tiket' => ['jpg', 'jpeg', 'png', 'pdf'],
            'dokumentasi' => ['jpg', 'jpeg', 'png'],
            'foto_profile' => ['jpg', 'jpeg', 'png'],
        ];

        if (!isset($allowedTypes[$type])) {
            return $this->respondError('Invalid file type', null, 400);
        }

        $file = $this->request->getFile('file');

        if (!$file || !$file->isValid()) {
            return $this->respondError('No valid file uploaded', null, 400);
        }

        // Validate file extension
        $extension = $file->getExtension();
        if (!in_array($extension, $allowedTypes[$type])) {
            return $this->respondError('File type not allowed', null, 422);
        }

        // Validate file size
        if ($file->getSize() > ($maxSize * 1024)) {
            return $this->respondError('File size exceeds maximum limit', null, 422);
        }

        // Generate unique filename
        $newName = $type . '_' . time() . '_' . uniqid() . '.' . $extension;

        // Determine upload path
        $uploadPath = FCPATH . 'uploads/' . $type;
        
        if (!is_dir($uploadPath)) {
            mkdir($uploadPath, 0755, true);
        }

        // Move file
        if ($file->move($uploadPath, $newName)) {
            return $this->respondSuccess('File uploaded successfully', [
                'filename' => $newName,
                'path' => 'uploads/' . $type . '/' . $newName,
                'size' => $file->getSize(),
                'url' => base_url('uploads/' . $type . '/' . $newName),
            ]);
        }

        return $this->respondError('Failed to upload file', null, 500);
    }

    /**
     * Delete file
     */
    public function delete()
    {
        $path = $this->request->getPost('path');

        if (!$path) {
            return $this->respondError('File path required', null, 400);
        }

        $fullPath = FCPATH . $path;

        if (!file_exists($fullPath)) {
            return $this->respondError('File not found', null, 404);
        }

        if (unlink($fullPath)) {
            return $this->respondSuccess('File deleted successfully');
        }

        return $this->respondError('Failed to delete file', null, 500);
    }
}