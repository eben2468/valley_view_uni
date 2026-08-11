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

        // Also stash it in the session: most admin pages redirect straight
        // after saving, so an in-request variable would be gone before
        // anything could display it. admin/header.php shows and clears this.
        if (session_status() === PHP_SESSION_ACTIVE) {
            if ($message === null) {
                unset($_SESSION['vvu_upload_error']);
            } else {
                $_SESSION['vvu_upload_error'] = $message;
            }
        }
    }

    function vvu_last_upload_error() {
        return $GLOBALS['vvu_last_upload_error'] ?? null;
    }

    /** Returns the flashed upload error (if any) and clears it. */
    function vvu_take_upload_error() {
        if (session_status() !== PHP_SESSION_ACTIVE || empty($_SESSION['vvu_upload_error'])) {
            return null;
        }
        $message = $_SESSION['vvu_upload_error'];
        unset($_SESSION['vvu_upload_error']);
        return $message;
    }

    /**
     * Detects a file's MIME type from its contents.
     *
     * finfo_open() was called unguarded before. On a host without the
     * "fileinfo" extension it returns false, and the follow-up finfo_file()
     * call then fails — so every upload was rejected with no explanation on
     * those servers while working perfectly on XAMPP. This falls back to
     * getimagesize() for images, then to the file extension for documents.
     *
     * @return string|null MIME type, or null if it genuinely cannot be determined
     */
    function vvu_detect_mime($tmpPath, $originalName = '') {
        if (function_exists('finfo_open')) {
            $finfo = @finfo_open(FILEINFO_MIME_TYPE);
            if ($finfo) {
                $mime = @finfo_file($finfo, $tmpPath);
                finfo_close($finfo);
                if (is_string($mime) && $mime !== '') {
                    return $mime;
                }
            }
        }

        // Fallback 1: images can be identified from their header bytes
        if (function_exists('getimagesize')) {
            $info = @getimagesize($tmpPath);
            if (!empty($info['mime'])) {
                return $info['mime'];
            }
        }

        // Fallback 2: documents — trust the extension, since the allow-list
        // below still restricts what can be stored
        $byExtension = [
            'pdf'  => 'application/pdf',
            'doc'  => 'application/msword',
            'docx' => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
            'xls'  => 'application/vnd.ms-excel',
            'xlsx' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        ];
        $ext = strtolower(pathinfo($originalName, PATHINFO_EXTENSION));

        return $byExtension[$ext] ?? null;
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

    /**
     * Renders an admin-facing alert for a failed save.
     *
     * A missing column is an install/migration problem, not something the
     * editor did wrong, so it gets its own actionable message instead of a raw
     * SQL string. Everything else shows a generic alert; the full exception
     * goes to the error log, never to the browser, so the schema is not
     * disclosed to whoever can trigger the failure.
     */
    function vvu_render_save_error(Throwable $e) {
        if (strpos($e->getMessage(), 'Unknown column') !== false) {
            return '<div class="alert alert-danger">'
                 . '<strong>Save failed — the database is out of date.</strong><br>'
                 . 'This page stores fields that are missing from the database, so nothing was saved '
                 . '(including any image you selected).<br>'
                 . '<strong>Fix:</strong> run <code>dev-tools/migrate_campus_life_v3.php</code> once on this server, '
                 . 'then save again.'
                 . '</div>';
        }

        return '<div class="alert alert-danger">'
             . '<strong>Save failed.</strong> The changes were not stored. '
             . 'Please try again — if it keeps happening, check the server error log.'
             . '</div>';
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
        $targetDir = $uploadBase . rtrim($targetSubDir, '/\\') . DIRECTORY_SEPARATOR;

        // Ensure the directory exists AND is writable. These checks used to be
        // skipped entirely, so on a server where uploads/ is not writable the
        // upload failed silently and the old image was simply kept.
        if (!is_dir($targetDir)) {
            if (!@mkdir($targetDir, 0755, true) && !is_dir($targetDir)) {
                vvu_set_upload_error('Could not create the upload folder (' . htmlspecialchars($targetSubDir) . '). Set uploads/ to 755 on the server.');
                return null;
            }
        }

        if (!is_writable($targetDir)) {
            vvu_set_upload_error('The upload folder is not writable (uploads/' . htmlspecialchars(rtrim($targetSubDir, '/\\')) . '). Set it to 755 on the server.');
            return null;
        }

        // Validate file type by content
        $allowedTypes = [
            'image/jpeg' => 'jpg',
            'image/png'  => 'png',
            'image/gif'  => 'gif',
            'image/webp' => 'webp',
            'application/pdf' => 'pdf',
            'application/msword' => 'doc',
            'application/vnd.openxmlformats-officedocument.wordprocessingml.document' => 'docx',
            'application/vnd.ms-excel' => 'xls',
            'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet' => 'xlsx',
        ];

        $mimeType = vvu_detect_mime($file['tmp_name'], $file['name']);

        if ($mimeType === null) {
            vvu_set_upload_error('The server could not determine the file type. Ask your host to enable the PHP "fileinfo" extension.');
            return null;
        }

        if (!isset($allowedTypes[$mimeType])) {
            vvu_set_upload_error("That file type ({$mimeType}) is not allowed.");
            return null;
        }

        // Take the extension from the DETECTED type, never from the uploaded
        // filename: a file called "shell.php" whose bytes look like a JPEG
        // would otherwise be stored as an executable .php inside the web root.
        $extension = $allowedTypes[$mimeType];
        $filename  = $prefix . time() . '_' . uniqid() . '.' . $extension;
        $targetPath = $targetDir . $filename;

        if (move_uploaded_file($file['tmp_name'], $targetPath)) {
            @chmod($targetPath, 0644);
            // Return path relative to project root
            return 'uploads/' . rtrim($targetSubDir, '/\\') . '/' . $filename;
        }

        vvu_set_upload_error('The file could not be saved to the server. Check that uploads/ is writable.');
        
        return null;
    }
}
