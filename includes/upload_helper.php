<?php
/**
 * Common helper for handling image uploads in the admin panel
 */

if (!function_exists('handleAdminFileUpload')) {
    /**
     * Handles file upload and returns the path relative to the root
     * 
     * @param array $file The $_FILES element
     * @param string $targetSubDir Directory within uploads/
     * @param string $prefix Prefix for the filename
     * @return string|null The path to save in DB, or null if no file was uploaded
     */
    function handleAdminFileUpload($file, $targetSubDir = 'cms/', $prefix = 'img_') {
        if (!isset($file) || $file['error'] !== UPLOAD_ERR_OK) {
            return null;
        }
        
        // Define directory relative to the file being executed (usually in admin/)
        // We want to save in ../uploads/sub-dir/
        $uploadBase = dirname(__DIR__) . DIRECTORY_SEPARATOR . 'uploads' . DIRECTORY_SEPARATOR;
        $targetDir = $uploadBase . $targetSubDir . DIRECTORY_SEPARATOR;
        
        // Ensure directory exists
        if (!is_dir($targetDir)) {
            mkdir($targetDir, 0777, true);
        }
        
        // Validate file type
        $allowedTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp', 'application/pdf', 'application/msword', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document', 'application/vnd.ms-excel', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'];
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mimeType = finfo_file($finfo, $file['tmp_name']);
        finfo_close($finfo);
        
        if (!in_array($mimeType, $allowedTypes)) {
            return null;
        }
        
        // Generate unique filename
        $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
        $filename = $prefix . time() . '_' . uniqid() . '.' . $extension;
        $targetPath = $targetDir . $filename;
        
        if (move_uploaded_file($file['tmp_name'], $targetPath)) {
            // Return path relative to project root
            return 'uploads/' . rtrim($targetSubDir, '/\\') . '/' . $filename;
        }
        
        return null;
    }
}
