<?php
/**
 * E-Books Access Helper
 *
 * Gates the VVU digital library (Google Drive) resources behind a student /
 * staff email verification step. Only @st.vvu.edu.gh and @vvu.edu.gh addresses
 * are accepted. The grant is stored in the session so a verified visitor is not
 * asked again for the rest of their visit — this covers both the on-page button
 * and QR code scans, because the QR code points at the same gate page.
 */

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

/** Email domains allowed through the gate. */
function vvu_ebook_allowed_domains() {
    return ['st.vvu.edu.gh', 'vvu.edu.gh'];
}

/** How long a verification stays valid, in seconds (12 hours). */
function vvu_ebook_access_ttl() {
    return 12 * 60 * 60;
}

/** Max failed attempts before the form is temporarily locked. */
function vvu_ebook_max_attempts() {
    return 8;
}

/** Lockout window in seconds after too many failed attempts. */
function vvu_ebook_lockout_seconds() {
    return 10 * 60;
}

/**
 * Resources that live behind the gate.
 * Each entry is resolved from the CMS (page 53 / library_resources) so the
 * library staff can keep editing the destination from the admin panel.
 */
function vvu_ebook_resources($pdo = null) {
    static $cache = null;
    if ($cache !== null) {
        return $cache;
    }

    $definitions = [
        'ebooks' => [
            'label'       => 'E-Books Collection',
            'description' => 'The VVU Library e-books collection hosted on Google Drive.',
            'section'     => 'db_qr_ebooks',
            'field'       => 'ebooks_url',
            'fallback'    => '',
        ],
    ];

    $cache = [];
    foreach ($definitions as $key => $def) {
        $url = $def['fallback'];
        if ($pdo instanceof PDO) {
            try {
                $stmt = $pdo->prepare("
                    SELECT cf.field_value
                    FROM administration_content c
                    JOIN administration_content_fields cf ON c.id = cf.content_id
                    WHERE c.section_key = ? AND cf.field_key = ?
                    LIMIT 1
                ");
                $stmt->execute([$def['section'], $def['field']]);
                $value = $stmt->fetchColumn();
                if ($value) {
                    $url = trim($value);
                }
            } catch (PDOException $e) {
                // Fall back to the default URL below.
            }
        }
        $cache[$key] = [
            'label'       => $def['label'],
            'description' => $def['description'],
            'url'         => $url,
        ];
    }

    return $cache;
}

/** Resolve a resource key, returning null when it is not whitelisted. */
function vvu_ebook_resource($key, $pdo = null) {
    $resources = vvu_ebook_resources($pdo);
    return isset($resources[$key]) ? $resources[$key] : null;
}

/** Lower-case and trim a submitted address. */
function vvu_ebook_normalize_email($email) {
    return strtolower(trim((string)$email));
}

/** Is this a well formed address on an allowed VVU domain? */
function vvu_ebook_is_valid_email($email) {
    $email = vvu_ebook_normalize_email($email);
    if ($email === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        return false;
    }
    $at = strrpos($email, '@');
    if ($at === false) {
        return false;
    }
    $domain = substr($email, $at + 1);
    return in_array($domain, vvu_ebook_allowed_domains(), true);
}

/** Has the current visitor already verified, and is it still valid? */
function vvu_ebook_has_access() {
    if (empty($_SESSION['ebook_access']['email']) || empty($_SESSION['ebook_access']['granted_at'])) {
        return false;
    }
    if ((time() - (int)$_SESSION['ebook_access']['granted_at']) > vvu_ebook_access_ttl()) {
        vvu_ebook_revoke_access();
        return false;
    }
    return true;
}

/** The verified address, or an empty string. */
function vvu_ebook_verified_email() {
    return vvu_ebook_has_access() ? $_SESSION['ebook_access']['email'] : '';
}

/** Record a successful verification in the session. */
function vvu_ebook_grant_access($email) {
    $email = vvu_ebook_normalize_email($email);
    session_regenerate_id(true);
    $_SESSION['ebook_access'] = [
        'email'      => $email,
        'granted_at' => time(),
    ];
    unset($_SESSION['ebook_attempts']);
}

/** Drop the verification (used by the "not you?" link and on expiry). */
function vvu_ebook_revoke_access() {
    unset($_SESSION['ebook_access']);
}

/** Number of seconds still remaining on a lockout, or 0 when not locked. */
function vvu_ebook_lockout_remaining() {
    if (empty($_SESSION['ebook_attempts'])) {
        return 0;
    }
    $attempts = $_SESSION['ebook_attempts'];
    if ((int)$attempts['count'] < vvu_ebook_max_attempts()) {
        return 0;
    }
    $elapsed = time() - (int)$attempts['last'];
    $remaining = vvu_ebook_lockout_seconds() - $elapsed;
    if ($remaining <= 0) {
        unset($_SESSION['ebook_attempts']);
        return 0;
    }
    return $remaining;
}

/** Count a rejected attempt. */
function vvu_ebook_record_failed_attempt() {
    if (empty($_SESSION['ebook_attempts']) ||
        (time() - (int)$_SESSION['ebook_attempts']['last']) > vvu_ebook_lockout_seconds()) {
        $_SESSION['ebook_attempts'] = ['count' => 0, 'last' => time()];
    }
    $_SESSION['ebook_attempts']['count']++;
    $_SESSION['ebook_attempts']['last'] = time();
}

/** CSRF token for the verification form. */
function vvu_ebook_csrf_token() {
    if (empty($_SESSION['ebook_csrf'])) {
        $_SESSION['ebook_csrf'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['ebook_csrf'];
}

/** Constant-time CSRF check. */
function vvu_ebook_check_csrf($token) {
    return !empty($_SESSION['ebook_csrf'])
        && is_string($token)
        && hash_equals($_SESSION['ebook_csrf'], $token);
}

/** Relative URL of the gate for a given resource. */
function vvu_ebook_gate_url($key = 'ebooks') {
    return 'ebooks_access.php?r=' . urlencode($key);
}

/** Absolute URL of the gate — this is what the QR code encodes. */
function vvu_ebook_gate_absolute_url($key = 'ebooks') {
    $https = (!empty($_SERVER['HTTPS']) && strtolower($_SERVER['HTTPS']) !== 'off')
        || (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && strtolower($_SERVER['HTTP_X_FORWARDED_PROTO']) === 'https');
    $scheme = $https ? 'https' : 'http';
    $host = $_SERVER['HTTP_HOST'] ?? 'vvu.edu.gh';
    $dir = rtrim(str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME'] ?? '/')), '/');
    return $scheme . '://' . $host . $dir . '/' . vvu_ebook_gate_url($key);
}

/**
 * Log a granted access so the library can see who used the collection.
 * Silently does nothing if the table cannot be created (e.g. limited DB user).
 */
function vvu_ebook_log_access($pdo, $email, $resource_key) {
    if (!($pdo instanceof PDO)) {
        return;
    }
    static $table_ready = false;
    try {
        if (!$table_ready) {
            $pdo->exec("
                CREATE TABLE IF NOT EXISTS ebook_access_log (
                    id INT AUTO_INCREMENT PRIMARY KEY,
                    student_email VARCHAR(190) NOT NULL,
                    resource_key VARCHAR(64) NOT NULL,
                    ip_address VARCHAR(45) NULL,
                    user_agent VARCHAR(255) NULL,
                    accessed_at DATETIME NOT NULL,
                    INDEX idx_email (student_email),
                    INDEX idx_accessed (accessed_at)
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4
            ");
            $table_ready = true;
        }
        $stmt = $pdo->prepare("
            INSERT INTO ebook_access_log (student_email, resource_key, ip_address, user_agent, accessed_at)
            VALUES (?, ?, ?, ?, NOW())
        ");
        $stmt->execute([
            vvu_ebook_normalize_email($email),
            $resource_key,
            substr($_SERVER['REMOTE_ADDR'] ?? '', 0, 45),
            substr($_SERVER['HTTP_USER_AGENT'] ?? '', 0, 255),
        ]);
    } catch (PDOException $e) {
        // Logging is best-effort; never block access on it.
    }
}
