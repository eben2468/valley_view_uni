<?php
/**
 * Campus Life Pages - Image Upload Handler
 * Handles secure image uploads for campus life pages
 */

// Authentication FIRST. Without this, the endpoint accepted uploads from
// anyone on the internet (it only called session_start(), never checked the
// session). admin_auth.php answers XHR callers with a 401 JSON body.
require_once('../includes/admin_auth.php');
require_once('../includes/db_connect.php');

// Set JSON response header
header('Content-Type: application/json');

// Check if file was uploaded
if (!isset($_FILES['image']) || $_FILES['image']['error'] !== UPLOAD_ERR_OK) {
    echo json_encode(['success' => false, 'message' => 'No file uploaded or upload error occurred']);
    exit;
}

$file = $_FILES['image'];
$upload_dir = '../uploads/campus_life/';

// Create upload directory if it doesn't exist
if (!file_exists($upload_dir)) {
    mkdir($upload_dir, 0755, true);
}

// Validate file type
$allowed_types = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif', 'image/webp'];
$finfo = finfo_open(FILEINFO_MIME_TYPE);
$mime_type = finfo_file($finfo, $file['tmp_name']);
finfo_close($finfo);

if (!in_array($mime_type, $allowed_types)) {
    echo json_encode(['success' => false, 'message' => 'Invalid file type. Only JPG, PNG, GIF, and WebP images are allowed.']);
    exit;
}

// Validate file size (max 5MB)
$max_size = 5 * 1024 * 1024; // 5MB in bytes
if ($file['size'] > $max_size) {
    echo json_encode(['success' => false, 'message' => 'File size exceeds 5MB limit.']);
    exit;
}

// Generate unique filename.
//
// The extension is derived from the DETECTED mime type, never from the
// uploaded filename. Previously it was pathinfo($file['name']), so a genuine
// GIF uploaded as "shell.php" passed the mime check above and was then written
// into the web root as an executable .php file — remote code execution.
$extension_for_mime = [
    'image/jpeg' => 'jpg',
    'image/jpg'  => 'jpg',
    'image/png'  => 'png',
    'image/gif'  => 'gif',
    'image/webp' => 'webp',
];
$extension = $extension_for_mime[$mime_type];
$filename = uniqid('campus_life_', true) . '.' . $extension;
$filepath = $upload_dir . $filename;

// Move uploaded file
if (move_uploaded_file($file['tmp_name'], $filepath)) {
    @chmod($filepath, 0644);
    // Return relative path for database storage
    $relative_path = 'uploads/campus_life/' . $filename;
    
    echo json_encode([
        'success' => true,
        'message' => 'Image uploaded successfully',
        'path' => $relative_path,
        'filename' => $filename
    ]);
} else {
    echo json_encode(['success' => false, 'message' => 'Failed to move uploaded file']);
}
?>
