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

/**
 * Where a document uploaded against a CMS item should be stored.
 *
 * Download Forms keeps its PDFs in uploads/Download Forms/ alongside the ones
 * already published there, which is where download-forms.php looks for them.
 * Everything else goes to the generic resources folder.
 */
if (!function_exists('vvu_resource_upload_dir')) {
    function vvu_resource_upload_dir($page_key)
    {
        return $page_key === 'download_forms' ? 'Download Forms' : 'resources';
    }
}

/**
 * The item's downloadable file: a freshly uploaded one when the admin chose a
 * file, otherwise whatever path is already typed in the link box.
 *
 * Lives here rather than in one manager because two separate admin screens
 * edit the same academic_pages_items rows — manage_resources_pages.php and
 * manage_info_pages.php both list Download Forms — and a document uploaded
 * through either has to end up in the same column and the same folder.
 *
 * UPLOAD_ERR_NO_FILE is the only error worth ignoring: it just means the field
 * was left empty. Every other failure is left for handleAdminFileUpload to
 * record, and admin/header.php shows it on the next page load, so a rejected
 * upload can no longer look like a successful save.
 */
if (!function_exists('vvu_resource_item_link')) {
    function vvu_resource_item_link($page_key)
    {
        $link = $_POST['item_link'] ?? '';

        if (isset($_FILES['item_file']) && $_FILES['item_file']['error'] !== UPLOAD_ERR_NO_FILE) {
            $uploaded = handleAdminFileUpload($_FILES['item_file'], vvu_resource_upload_dir($page_key), 'form_');
            if ($uploaded) {
                $link = $uploaded;
            }
        }

        return $link;
    }
}

if (!function_exists('vvu_video_upload_types')) {
    /**
     * Video formats the homepage player accepts, mapped to the extension the
     * file is stored under. Deliberately narrow: these four are the only
     * containers every current browser can play without a plugin.
     *
     * .mov is included because that is what an iPhone produces, but Safari is
     * the only browser that reliably plays it — the admin form warns about it.
     */
    function vvu_video_upload_types() {
        return [
            'video/mp4'        => 'mp4',
            'video/webm'       => 'webm',
            'video/ogg'        => 'ogv',
            'video/quicktime'  => 'mov',
        ];
    }
}

if (!function_exists('handleAdminVideoUpload')) {
    /**
     * Stores an uploaded video and returns its path relative to the project
     * root, or null on failure (with the reason in vvu_last_upload_error()).
     *
     * Kept separate from handleAdminFileUpload() rather than widening that
     * function's allow-list: images and documents are uploaded on ~30 admin
     * screens, and none of them should silently start accepting a 60MB video.
     */
    function handleAdminVideoUpload($file, $targetSubDir = 'videos', $prefix = 'video_') {
        vvu_set_upload_error(null);

        if (!isset($file) || !is_array($file)) {
            return null;
        }

        if ($file['error'] !== UPLOAD_ERR_OK) {
            if ($file['error'] !== UPLOAD_ERR_NO_FILE) {
                vvu_set_upload_error(vvu_upload_error_message($file['error']));
            }
            return null;
        }

        $uploadBase = dirname(__DIR__) . DIRECTORY_SEPARATOR . 'uploads' . DIRECTORY_SEPARATOR;
        $targetDir  = $uploadBase . rtrim($targetSubDir, '/\\') . DIRECTORY_SEPARATOR;

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

        $allowed = vvu_video_upload_types();
        $mimeType = vvu_detect_video_mime($file['tmp_name'], $file['name']);

        if ($mimeType === null) {
            vvu_set_upload_error('The server could not determine the file type. Ask your host to enable the PHP "fileinfo" extension.');
            return null;
        }

        if (!isset($allowed[$mimeType])) {
            vvu_set_upload_error("That file type ({$mimeType}) is not a supported video. Use MP4, WebM, OGV or MOV.");
            return null;
        }

        // Extension comes from the detected type, never the uploaded filename,
        // for the same reason as in handleAdminFileUpload().
        $filename   = $prefix . time() . '_' . uniqid() . '.' . $allowed[$mimeType];
        $targetPath = $targetDir . $filename;

        if (move_uploaded_file($file['tmp_name'], $targetPath)) {
            @chmod($targetPath, 0644);
            return 'uploads/' . rtrim($targetSubDir, '/\\') . '/' . $filename;
        }

        vvu_set_upload_error('The video could not be saved to the server. Check that uploads/ is writable.');

        return null;
    }
}

if (!function_exists('vvu_detect_video_mime')) {
    /**
     * MIME type of an uploaded video.
     *
     * vvu_detect_mime() is image/document oriented — its getimagesize() and
     * extension fallbacks know nothing about video — so a host without the
     * "fileinfo" extension would reject every video with a confusing message.
     * This adds a video-aware fallback: the container signature in the first
     * bytes, then the extension.
     */
    function vvu_detect_video_mime($tmpPath, $originalName = '') {
        if (function_exists('finfo_open')) {
            $finfo = @finfo_open(FILEINFO_MIME_TYPE);
            if ($finfo) {
                $mime = @finfo_file($finfo, $tmpPath);
                finfo_close($finfo);
                if (is_string($mime) && $mime !== '' && $mime !== 'application/octet-stream') {
                    return $mime;
                }
            }
        }

        // Fallback 1: container signatures.
        $head = '';
        $fh = @fopen($tmpPath, 'rb');
        if ($fh) {
            $head = (string) fread($fh, 16);
            fclose($fh);
        }

        if ($head !== '') {
            if (substr($head, 4, 4) === 'ftyp') {
                // ISO base media: MP4 and QuickTime share it; the brand tells
                // them apart ('qt  ' is QuickTime, everything else is MP4).
                return substr($head, 8, 4) === 'qt  ' ? 'video/quicktime' : 'video/mp4';
            }
            if (substr($head, 0, 4) === "\x1A\x45\xDF\xA3") {
                return 'video/webm';   // Matroska/WebM
            }
            if (substr($head, 0, 4) === 'OggS') {
                return 'video/ogg';
            }
        }

        // Fallback 2: the extension, still checked against the allow-list.
        $byExtension = [
            'mp4'  => 'video/mp4',
            'm4v'  => 'video/mp4',
            'webm' => 'video/webm',
            'ogv'  => 'video/ogg',
            'ogg'  => 'video/ogg',
            'mov'  => 'video/quicktime',
        ];
        $ext = strtolower(pathinfo($originalName, PATHINFO_EXTENSION));

        return $byExtension[$ext] ?? null;
    }
}

if (!function_exists('vvu_php_upload_limit_bytes')) {
    /**
     * The smaller of upload_max_filesize and post_max_size, in bytes — the
     * real ceiling a single uploaded file has to stay under. Shown to the
     * editor so an oversized video is caught in the browser rather than
     * failing halfway through a long upload.
     */
    function vvu_php_upload_limit_bytes() {
        $toBytes = function ($value) {
            $value = trim((string) $value);
            if ($value === '') {
                return 0;
            }
            $unit   = strtolower(substr($value, -1));
            $number = (float) $value;
            switch ($unit) {
                case 'g': return (int) ($number * 1024 * 1024 * 1024);
                case 'm': return (int) ($number * 1024 * 1024);
                case 'k': return (int) ($number * 1024);
                default:  return (int) $number;
            }
        };

        $limits = array_filter([
            $toBytes(ini_get('upload_max_filesize')),
            $toBytes(ini_get('post_max_size')),
        ]);

        return $limits ? min($limits) : 0;
    }
}

if (!function_exists('vvu_json_column_value')) {
    /**
     * Normalises an "Extra Data (JSON)" form field before it reaches a JSON column.
     *
     * `extra_data` is a JSON column, which MariaDB/MySQL enforce with
     * CHECK (json_valid(`extra_data`)). An empty textarea posts '' rather than
     * nothing at all, and '' is not valid JSON, so the whole row is rejected with
     * "Check constraint 'academic_pages_items_chk_1' is violated". Blank means
     * "no extra data", so it has to arrive as NULL.
     *
     * @throws InvalidArgumentException when the editor typed something that is not
     *         JSON — the save stops with a readable message instead of silently
     *         discarding what they wrote.
     */
    function vvu_json_column_value($value) {
        if ($value === null) {
            return null;
        }

        $value = trim((string) $value);
        if ($value === '') {
            return null;
        }

        json_decode($value);
        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new InvalidArgumentException(
                'The "Extra Data" box must contain valid JSON (for example {"key": "value"}) '
                . 'or be left empty. Nothing was saved.'
            );
        }

        return $value;
    }
}

if (!function_exists('vvu_audio_upload_types')) {
    /**
     * Audio containers an editor may upload, mapped to the extension they are
     * stored with. Kept separate from vvu_video_upload_types() so a screen that
     * wants a video does not silently start accepting an MP3, and vice versa.
     */
    function vvu_audio_upload_types() {
        return [
            'audio/mpeg'  => 'mp3',
            'audio/mp3'   => 'mp3',
            'audio/mp4'   => 'm4a',
            'audio/x-m4a' => 'm4a',
            'audio/aac'   => 'm4a',
            'audio/ogg'   => 'ogg',
            'audio/wav'   => 'wav',
            'audio/x-wav' => 'wav',
            'audio/webm'  => 'weba',
        ];
    }
}

if (!function_exists('vvu_detect_audio_mime')) {
    /**
     * MIME type of an uploaded audio file, with the same fallback ladder as
     * vvu_detect_video_mime(): fileinfo, then the container signature in the
     * first bytes, then the extension — so a host without the "fileinfo"
     * extension can still accept an MP3.
     */
    function vvu_detect_audio_mime($tmpPath, $originalName = '') {
        if (function_exists('finfo_open')) {
            $finfo = @finfo_open(FILEINFO_MIME_TYPE);
            if ($finfo) {
                $mime = @finfo_file($finfo, $tmpPath);
                finfo_close($finfo);
                if (is_string($mime) && $mime !== '' && $mime !== 'application/octet-stream') {
                    return $mime;
                }
            }
        }

        // Fallback 1: container signatures.
        $head = '';
        $fh = @fopen($tmpPath, 'rb');
        if ($fh) {
            $head = (string) fread($fh, 16);
            fclose($fh);
        }

        if ($head !== '') {
            if (substr($head, 0, 3) === 'ID3') {
                return 'audio/mpeg';                       // MP3 carrying an ID3 tag
            }
            if (strlen($head) > 1 && ord($head[0]) === 0xFF && (ord($head[1]) & 0xE0) === 0xE0) {
                return 'audio/mpeg';                       // bare MPEG frame sync
            }
            if (substr($head, 4, 4) === 'ftyp' && substr($head, 8, 3) === 'M4A') {
                return 'audio/mp4';
            }
            if (substr($head, 0, 4) === 'OggS') {
                return 'audio/ogg';
            }
            if (substr($head, 0, 4) === 'RIFF' && substr($head, 8, 4) === 'WAVE') {
                return 'audio/wav';
            }
        }

        // Fallback 2: the extension, still checked against the allow-list.
        $byExtension = [
            'mp3'  => 'audio/mpeg',
            'm4a'  => 'audio/mp4',
            'aac'  => 'audio/aac',
            'ogg'  => 'audio/ogg',
            'oga'  => 'audio/ogg',
            'wav'  => 'audio/wav',
            'weba' => 'audio/webm',
        ];
        $ext = strtolower(pathinfo($originalName, PATHINFO_EXTENSION));

        return $byExtension[$ext] ?? null;
    }
}

if (!function_exists('handleAdminAudioUpload')) {
    /**
     * Stores an uploaded audio file and returns its path relative to the
     * project root, or null on failure (with the reason in
     * vvu_last_upload_error()). Mirrors handleAdminVideoUpload().
     */
    function handleAdminAudioUpload($file, $targetSubDir = 'audio', $prefix = 'audio_') {
        vvu_set_upload_error(null);

        if (!isset($file) || !is_array($file)) {
            return null;
        }

        if ($file['error'] !== UPLOAD_ERR_OK) {
            if ($file['error'] !== UPLOAD_ERR_NO_FILE) {
                vvu_set_upload_error(vvu_upload_error_message($file['error']));
            }
            return null;
        }

        $uploadBase = dirname(__DIR__) . DIRECTORY_SEPARATOR . 'uploads' . DIRECTORY_SEPARATOR;
        $targetDir  = $uploadBase . rtrim($targetSubDir, '/\\') . DIRECTORY_SEPARATOR;

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

        $allowed  = vvu_audio_upload_types();
        $mimeType = vvu_detect_audio_mime($file['tmp_name'], $file['name']);

        if ($mimeType === null) {
            vvu_set_upload_error('The server could not determine the file type. Ask your host to enable the PHP "fileinfo" extension.');
            return null;
        }

        if (!isset($allowed[$mimeType])) {
            vvu_set_upload_error("That file type ({$mimeType}) is not a supported audio file. Use MP3, M4A, OGG or WAV.");
            return null;
        }

        // Extension comes from the detected type, never the uploaded filename,
        // for the same reason as in handleAdminFileUpload().
        $filename   = $prefix . time() . '_' . uniqid() . '.' . $allowed[$mimeType];
        $targetPath = $targetDir . $filename;

        if (move_uploaded_file($file['tmp_name'], $targetPath)) {
            @chmod($targetPath, 0644);
            return 'uploads/' . rtrim($targetSubDir, '/\\') . '/' . $filename;
        }

        vvu_set_upload_error('The audio could not be saved to the server. Check that uploads/ is writable.');

        return null;
    }
}

if (!function_exists('vvu_media_is_audio')) {
    /**
     * Whether a stored media path points at audio rather than video. Used by
     * the anthem section, where one column (`video_url`) holds either kind and
     * the page has to choose between an <audio> and a <video> player.
     */
    function vvu_media_is_audio($url) {
        $path = parse_url(trim((string) $url), PHP_URL_PATH);
        $ext  = strtolower(pathinfo((string) $path, PATHINFO_EXTENSION));

        return in_array($ext, ['mp3', 'm4a', 'aac', 'ogg', 'oga', 'wav', 'weba'], true);
    }
}

if (!function_exists('vvu_media_mime')) {
    /**
     * The `type` attribute for a <source> pointing at a stored media file, so
     * the browser is told what it is getting. Empty when unknown — an omitted
     * type is better than a wrong one.
     */
    function vvu_media_mime($url) {
        $path = parse_url(trim((string) $url), PHP_URL_PATH);
        $ext  = strtolower(pathinfo((string) $path, PATHINFO_EXTENSION));

        $types = [
            'mp3'  => 'audio/mpeg',
            'm4a'  => 'audio/mp4',
            'aac'  => 'audio/aac',
            'ogg'  => 'audio/ogg',
            'oga'  => 'audio/ogg',
            'wav'  => 'audio/wav',
            'weba' => 'audio/webm',
            'mp4'  => 'video/mp4',
            'm4v'  => 'video/mp4',
            'webm' => 'video/webm',
            'ogv'  => 'video/ogg',
            'mov'  => 'video/quicktime',
        ];

        return $types[$ext] ?? '';
    }
}
