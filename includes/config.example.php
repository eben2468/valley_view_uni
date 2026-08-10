<?php
/**
 * Valley View University — database configuration TEMPLATE.
 *
 * Copy this file to `includes/config.php` on each machine/server and fill in
 * the real values there. `includes/config.php` is listed in .gitignore and must
 * NEVER be committed — that is exactly how the credentials leaked in the
 * August 2026 penetration test (Finding 3).
 *
 *     cp includes/config.example.php includes/config.php
 *
 * Alternatively, set the same values as environment variables
 * (DB_HOST, DB_NAME, DB_USER, DB_PASS) in the Apache/nginx/PHP-FPM config —
 * environment variables take precedence over this file.
 */

return [
    'host' => 'localhost',
    'name' => 'valley_view_uni',

    // Do NOT use the MySQL root account in production. Create a dedicated,
    // least-privilege user instead:
    //
    //   CREATE USER 'vvu_web'@'localhost' IDENTIFIED BY '<strong-random-password>';
    //   GRANT SELECT, INSERT, UPDATE, DELETE ON valley_view_uni.* TO 'vvu_web'@'localhost';
    //   FLUSH PRIVILEGES;
    //
    // The web application never needs DROP, ALTER, CREATE or GRANT.
    'user' => 'vvu_web',
    'pass' => '',

    'charset' => 'utf8mb4',
];
