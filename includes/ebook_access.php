<?php
/**
 * E-Books Access Helper
 *
 * Access control for the VVU digital library lives in Google Drive, not here.
 * The collection is shared with the st.vvu.edu.gh domain only, so Google asks
 * for a real student sign-in before the folder opens.
 *
 * This file used to run its own "verification": it accepted any string that
 * merely *ended* in @st.vvu.edu.gh, which proved nothing — anyone could type
 * abc@st.vvu.edu.gh and be redirected straight to a link that was shared with
 * "anyone on the internet". That check is gone. What remains is a signpost:
 * it tells the student which account to sign in with, then hands them off to
 * Google, which does the actual authentication.
 */

/** The Google Workspace domain the Drive folder is shared with. */
function vvu_ebook_student_domain() {
    return 'st.vvu.edu.gh';
}

/**
 * Resources that sit behind the Google sign-in.
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

/**
 * Only ever hand the browser an http(s) link we recognise as a Drive/Docs URL.
 * The destination comes out of the CMS, so a mistyped or hostile value there
 * should not turn this page into an open redirect.
 */
function vvu_ebook_is_safe_destination($url) {
    $url = trim((string)$url);
    if ($url === '') {
        return false;
    }
    $parts = parse_url($url);
    if (!$parts || empty($parts['scheme']) || empty($parts['host'])) {
        return false;
    }
    if (!in_array(strtolower($parts['scheme']), ['http', 'https'], true)) {
        return false;
    }
    $host = strtolower($parts['host']);
    $allowed = ['drive.google.com', 'docs.google.com', 'drive.usercontent.google.com'];
    foreach ($allowed as $ok) {
        if ($host === $ok || substr($host, -strlen('.' . $ok)) === '.' . $ok) {
            return true;
        }
    }
    return false;
}

/** Relative URL of the sign-in signpost for a given resource. */
function vvu_ebook_gate_url($key = 'ebooks') {
    return 'ebooks_access.php?r=' . urlencode($key);
}

/** Absolute URL of the signpost — this is what the QR code encodes. */
function vvu_ebook_gate_absolute_url($key = 'ebooks') {
    $https = (!empty($_SERVER['HTTPS']) && strtolower($_SERVER['HTTPS']) !== 'off')
        || (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && strtolower($_SERVER['HTTP_X_FORWARDED_PROTO']) === 'https');
    $scheme = $https ? 'https' : 'http';
    $host = $_SERVER['HTTP_HOST'] ?? 'vvu.edu.gh';
    $dir = rtrim(str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME'] ?? '/')), '/');
    return $scheme . '://' . $host . $dir . '/' . vvu_ebook_gate_url($key);
}

/**
 * Count a hand-off to Google so the library can see how much the collection is
 * used. There is no email to record any more — whoever actually gets in is
 * decided by Google, and this site never sees it. Best effort; never blocks.
 */
function vvu_ebook_log_open($pdo, $resource_key) {
    if (!($pdo instanceof PDO)) {
        return;
    }
    static $table_ready = false;
    try {
        if (!$table_ready) {
            $pdo->exec("
                CREATE TABLE IF NOT EXISTS ebook_access_log (
                    id INT AUTO_INCREMENT PRIMARY KEY,
                    student_email VARCHAR(190) NULL,
                    resource_key VARCHAR(64) NOT NULL,
                    ip_address VARCHAR(45) NULL,
                    user_agent VARCHAR(255) NULL,
                    accessed_at DATETIME NOT NULL,
                    INDEX idx_email (student_email),
                    INDEX idx_accessed (accessed_at)
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4
            ");
            // The column was NOT NULL while this site collected (unverified)
            // addresses. Widen it so the hand-off can be logged without one.
            try {
                $pdo->exec("ALTER TABLE ebook_access_log MODIFY student_email VARCHAR(190) NULL");
            } catch (PDOException $e) {
                // Older MySQL or a restricted user — the insert below still works.
            }
            $table_ready = true;
        }
        $stmt = $pdo->prepare("
            INSERT INTO ebook_access_log (student_email, resource_key, ip_address, user_agent, accessed_at)
            VALUES (NULL, ?, ?, ?, NOW())
        ");
        $stmt->execute([
            $resource_key,
            substr($_SERVER['REMOTE_ADDR'] ?? '', 0, 45),
            substr($_SERVER['HTTP_USER_AGENT'] ?? '', 0, 255),
        ]);
    } catch (PDOException $e) {
        // Logging is best-effort; never block access on it.
    }
}
