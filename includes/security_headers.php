<?php
/**
 * HTTP security headers (pentest Finding 7).
 *
 * These belong at the web-server level — see .htaccess and
 * nginx-vvu.conf.example — but they are also emitted here so the protection
 * survives a server-config rollback, a host migration, or a vhost that was
 * never updated. Duplicate headers from nginx/Apache are harmless; the
 * server's `always add_header` wins.
 *
 * Must be required before any output.
 */

if (headers_sent()) {
    return;
}

// Clickjacking. Kept alongside CSP frame-ancestors for older browsers.
header('X-Frame-Options: SAMEORIGIN');

// Stop browsers guessing a response is HTML/JS when it isn't — the main
// defence against an uploaded "image" being executed as script.
header('X-Content-Type-Options: nosniff');

// Don't leak the full admin URL (which can carry record ids) to third parties.
header('Referrer-Policy: strict-origin-when-cross-origin');

// Explicitly switch off browser features the site does not use.
header('Permissions-Policy: geolocation=(), microphone=(), camera=(), payment=(), usb=()');

// HSTS only over HTTPS — sending it over plain HTTP is ignored, and setting it
// on a host that isn't fully TLS-ready locks visitors out.
$isHttps =
    (!empty($_SERVER['HTTPS']) && strtolower($_SERVER['HTTPS']) !== 'off')
    || (($_SERVER['HTTP_X_FORWARDED_PROTO'] ?? '') === 'https')
    || (($_SERVER['SERVER_PORT'] ?? '') === '443');

if ($isHttps) {
    header('Strict-Transport-Security: max-age=31536000; includeSubDomains');
}

/*
 * Content-Security-Policy.
 *
 * The allow-list below was built from the external origins this codebase
 * actually references. Two compromises are called out on purpose:
 *
 *  - 'unsafe-inline' — hundreds of templates carry inline <script> blocks and
 *    style="" attributes. Removing it today would break the site.
 *  - 'unsafe-eval'   — required by the Tailwind Play CDN (cdn.tailwindcss.com),
 *    which compiles classes in the browser.
 *
 * So this is a reduction in attack surface (an injected <script src> can only
 * point at these hosts, and object/base/form targets are pinned), not a
 * complete XSS defence. Tighten in stages:
 *   1. Replace the Tailwind Play CDN with a compiled stylesheet → drop 'unsafe-eval'.
 *   2. Move inline handlers into js/ files → drop 'unsafe-inline' from script-src.
 * Set VVU_CSP_REPORT_ONLY to true while testing a tightened policy: violations
 * are then logged by the browser instead of blocking the page.
 */
$cspReportOnly = defined('VVU_CSP_REPORT_ONLY') && VVU_CSP_REPORT_ONLY;

$csp =
      "default-src 'self'; "
    . "script-src 'self' 'unsafe-inline' 'unsafe-eval' https://cdn.jsdelivr.net https://cdnjs.cloudflare.com "
        . "https://cdn.ckeditor.com https://cdn.tailwindcss.com; "
    . "style-src 'self' 'unsafe-inline' https://fonts.googleapis.com https://cdnjs.cloudflare.com https://cdn.jsdelivr.net; "
    . "font-src 'self' data: https://fonts.gstatic.com https://cdnjs.cloudflare.com https://cdn.jsdelivr.net; "
    . "img-src 'self' data: blob: https:; "
    . "media-src 'self' https://assets.mixkit.co; "
    . "connect-src 'self' https://cdn.ckeditor.com; "
    . "frame-src 'self' https://www.google.com https://www.youtube.com https://www.youtube-nocookie.com; "
    . "frame-ancestors 'self'; "
    . "base-uri 'self'; "
    . "form-action 'self'; "
    . "object-src 'none'";

header(($cspReportOnly ? 'Content-Security-Policy-Report-Only: ' : 'Content-Security-Policy: ') . $csp);

// Don't advertise the PHP version in the Server/X-Powered-By headers.
header_remove('X-Powered-By');
