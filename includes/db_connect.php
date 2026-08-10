<?php
/**
 * Database connection.
 *
 * Credentials are NEVER hard-coded here. They come from, in order of priority:
 *   1. Environment variables  DB_HOST / DB_NAME / DB_USER / DB_PASS
 *   2. includes/config.php    (git-ignored; copy from config.example.php)
 *
 * This split exists because the previous version of this file shipped
 * `root` / empty-password to a public GitHub repo (pentest Finding 3).
 */

$vvu_db = [
    'host'    => 'localhost',
    'name'    => 'valley_view_uni',
    'user'    => null,
    'pass'    => null,
    'charset' => 'utf8mb4',
];

$vvu_config_file = __DIR__ . '/config.php';
if (is_readable($vvu_config_file)) {
    $vvu_file_config = require $vvu_config_file;
    if (is_array($vvu_file_config)) {
        $vvu_db = array_merge($vvu_db, array_filter($vvu_file_config, static function ($v) {
            return $v !== null;
        }));
    }
}

// Environment variables win over the file, so the same code can be deployed
// unchanged to servers that inject config through the web server / systemd.
foreach (['host' => 'DB_HOST', 'name' => 'DB_NAME', 'user' => 'DB_USER', 'pass' => 'DB_PASS'] as $key => $env) {
    $value = getenv($env);
    if ($value !== false && $value !== '') {
        $vvu_db[$key] = $value;
    }
}

if ($vvu_db['user'] === null) {
    // No credentials configured at all. Fail loudly for the operator, silently
    // for the visitor — never echo the reason to the browser.
    error_log('VVU: no database credentials found. Create includes/config.php from includes/config.example.php.');
    http_response_code(500);
    die('Service temporarily unavailable.');
}

// Legacy variable names — some older scripts still reference these directly.
$servername = $vvu_db['host'];
$username   = $vvu_db['user'];
$password   = $vvu_db['pass'];
$dbname     = $vvu_db['name'];

try {
    $pdo = new PDO(
        "mysql:host={$vvu_db['host']};dbname={$vvu_db['name']};charset={$vvu_db['charset']}",
        $vvu_db['user'],
        $vvu_db['pass'],
        // Fetch mode and prepare emulation are deliberately left at their PDO
        // defaults: 196 files consume this handle and some read rows by numeric
        // index, so changing them here would be a behaviour change, not a fix.
        // The connection charset is set above, which is what makes emulated
        // prepares safe against multibyte injection tricks.
        [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
    );
} catch (PDOException $e) {
    // The old code echoed $e->getMessage(), which discloses the DB host, user
    // and schema name to anyone who can trigger a connection failure.
    error_log('VVU database connection failed: ' . $e->getMessage());
    http_response_code(500);
    die('Service temporarily unavailable. Please try again shortly.');
}

unset($vvu_config_file, $vvu_file_config);

// Function to get database connection
function getDB() {
    global $pdo;
    return $pdo;
}
