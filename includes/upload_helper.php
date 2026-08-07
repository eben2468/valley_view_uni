<?php
/**
 * Common helper for handling image uploads in the admin panel
 */

if (!function_exists('vvu_set_upload_error')) {
    /**
     * Records / reads why the last upload attempt failed. The upload function
     * keeps returning null so none of its 28 call sites need to change, but the
     * reason is now retrievable instead of being swallowed.
     */
    function vvu_set_upload_error($message = null) {
        $GLOBALS['vvu_last_upload_error'] = $message;
    }

    function vvu_last_upload_error() {
        return $GLOBALS['vvu_last_upload_error'] ?? null;
    }

    function vvu_upload_error_message($code) {
        $limit = ini_get('upload_max_filesize');
        switch ($code) {
            case UPLOAD_ERR_INI_SIZE:
                return "The file is larger than the server allows ({$limit}). Please use a smaller image.";
            case UPLOAD_ERR_FORM_SIZE:
                return 'The file is larger than this form allows. Please use a smaller image.';
            case UPLOAD_ERR_PARTIAL:
                return 'The upload was interrupted. Please try again.';
            case UPLOAD_ERR_NO_TMP_DIR:
                return 'Server error: no temporary folder available for uploads.';
            case UPLOAD_ERR_CANT_WRITE:
                return 'Server error: the upload could not be written to disk.';
            case UPLOAD_ERR_EXTENSION:
                return 'The upload was blocked by a server extension.';
            default:
                return 'The file could not be uploaded (error code ' . (int) $code . ').';
        }
    }
}

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
        vvu_set_upload_error(null);

        if (!isset($file) || !is_array($file)) {
            return null;
        }

        if ($file['error'] !== UPLOAD_ERR_OK) {
            // Previously this returned null with no explanation, so an upload
            // rejected for being too large looked identical to "no file chosen".
            if ($file['error'] !== UPLOAD_ERR_NO_FILE) {
                vvu_set_upload_error(vvu_upload_error_message($file['error']));
            }
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
            vvu_set_upload_error("That file type ({$mimeType}) is not allowed.");
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
