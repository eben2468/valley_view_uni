<?php
/**
 * VVU Scholar — researcher accounts.
 *
 * Researchers sign up with a VVU email, claim their profile (or ask for a new
 * one), and once the Research Office approves the claim in
 *     Admin → Research Portal → Researcher Accounts
 * they manage their own profile and publications at research/account.php.
 *
 * Kept apart from the admin login on purpose: a researcher session lives in
 * $_SESSION['research_account_id'] and can never reach admin/.
 *
 * Schema: sql/research_accounts_migration.sql
 */

require_once __DIR__ . '/../../includes/session_bootstrap.php';
require_once __DIR__ . '/../../includes/security_headers.php';
require_once __DIR__ . '/../../includes/db_connect.php';
require_once __DIR__ . '/research_partials.php';

if (!defined('VVU_RESEARCH_ACCOUNT')) {
    define('VVU_RESEARCH_ACCOUNT', 1);

/** Sign-in attempts allowed before the account is locked for a while. */
define('RA_MAX_FAILED_LOGINS', 5);
define('RA_LOCK_MINUTES', 15);
/** Researcher sessions end after this much inactivity. */
define('RA_IDLE_TIMEOUT', 7200);
define('RA_MIN_PASSWORD', 8);

/* ==========================================================================
   Schema / settings
   ========================================================================== */

/** True once sql/research_accounts_migration.sql has been imported. */
function ra_ready(PDO $pdo)
{
    static $ready = null;
    if ($ready === null) {
        try {
            $pdo->query("SELECT 1 FROM research_accounts LIMIT 1");
            $pdo->query("SELECT 1 FROM research_claims LIMIT 1");
            $ready = true;
        } catch (Exception $e) {
            $ready = false;
        }
    }
    return $ready;
}

/** Email domains allowed to sign up, e.g. ['vvu.edu.gh']. */
function ra_domains(PDO $pdo)
{
    $raw = strtolower((string) r_set($pdo, 'claim_email_domains', 'vvu.edu.gh'));
    $out = [];
    foreach (preg_split('/[\s,;]+/', $raw) as $d) {
        $d = ltrim(trim($d), '@');
        if ($d !== '') { $out[] = $d; }
    }
    return $out ?: ['vvu.edu.gh'];
}

/** Is this an email at one of the allowed VVU domains (exact domain match)? */
function ra_email_allowed(PDO $pdo, $email)
{
    $email = strtolower(trim((string) $email));
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        return false;
    }
    $domain = substr(strrchr($email, '@'), 1);
    return in_array($domain, ra_domains($pdo), true);
}

/* ==========================================================================
   Session
   ========================================================================== */

/** The signed-in researcher's account row, or null. */
function ra_current(PDO $pdo)
{
    static $cache = false;
    if ($cache !== false) {
        return $cache;
    }
    $cache = null;

    $id = (int) ($_SESSION['research_account_id'] ?? 0);
    if ($id <= 0 || !ra_ready($pdo)) {
        return null;
    }

    $last = (int) ($_SESSION['research_last_activity'] ?? time());
    if (time() - $last > RA_IDLE_TIMEOUT) {
        ra_logout();
        return null;
    }
    $_SESSION['research_last_activity'] = time();

    $stmt = $pdo->prepare("SELECT * FROM research_accounts WHERE id = ?");
    $stmt->execute([$id]);
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    if (!$row) {
        ra_logout();
        return null;
    }
    $cache = $row;
    return $cache;
}

function ra_login_session(array $account)
{
    session_regenerate_id(true);
    $_SESSION['research_account_id']    = (int) $account['id'];
    $_SESSION['research_last_activity'] = time();
}

function ra_logout()
{
    unset($_SESSION['research_account_id'], $_SESSION['research_last_activity'], $_SESSION['ra_import']);
}

/**
 * Page guard. Sends visitors who are not signed in to the sign-in page, and
 * returns the account. With $needActive, only approved accounts get through.
 */
function ra_require(PDO $pdo, $needActive = true)
{
    $acct = ra_current($pdo);
    if (!$acct) {
        $back = basename($_SERVER['SCRIPT_NAME'] ?? 'account.php');
        $qs   = $_SERVER['QUERY_STRING'] ?? '';
        $_SESSION['ra_after_login'] = $back . ($qs !== '' ? '?' . $qs : '');
        header('Location: login.php');
        exit;
    }
    if ($needActive && ($acct['status'] !== 'active' || !(int) $acct['scholar_id'])) {
        header('Location: account.php');
        exit;
    }
    return $acct;
}

/** Same-directory page names only, so a stored "after login" can't redirect off-site. */
function ra_safe_next($next, $default = 'account.php')
{
    $next = (string) $next;
    return preg_match('#^[a-z_]+\.php(\?[A-Za-z0-9_=&%.-]*)?$#', $next) ? $next : $default;
}

/** Rejects a POST without this session's CSRF token. */
function ra_check_csrf()
{
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && !vvu_csrf_valid($_POST['csrf_token'] ?? null)) {
        http_response_code(419);
        exit('Your session expired. Go back, reload the page and try again.');
    }
}

/** One-shot message shown on the next page. */
function ra_flash($type = null, $message = null)
{
    if ($type !== null) {
        $_SESSION['ra_flash'] = [$type, $message];
        return null;
    }
    $f = $_SESSION['ra_flash'] ?? null;
    unset($_SESSION['ra_flash']);
    return $f;
}

/* ==========================================================================
   Accounts
   ========================================================================== */

function ra_find_by_email(PDO $pdo, $email)
{
    $stmt = $pdo->prepare("SELECT * FROM research_accounts WHERE email = ?");
    $stmt->execute([strtolower(trim((string) $email))]);
    return $stmt->fetch(PDO::FETCH_ASSOC) ?: null;
}

/** Problems with a new password, or '' when it is acceptable. */
function ra_password_problem($password, $confirm)
{
    if (strlen((string) $password) < RA_MIN_PASSWORD) {
        return 'Use a password of at least ' . RA_MIN_PASSWORD . ' characters.';
    }
    if (!preg_match('/[A-Za-z]/', $password) || !preg_match('/\d/', $password)) {
        return 'Use a password with at least one letter and one number.';
    }
    if ($password !== $confirm) {
        return 'The two passwords do not match.';
    }
    return '';
}

/**
 * Checks an email + password. Returns [account|null, error message].
 * Counts failures and locks the account for a while after too many.
 */
function ra_attempt_login(PDO $pdo, $email, $password)
{
    $generic = 'That email and password do not match an account.';
    $acct = ra_find_by_email($pdo, $email);
    if (!$acct) {
        password_hash((string) $password, PASSWORD_DEFAULT); // same cost as a real check, so timing does not reveal which emails exist
        return [null, $generic];
    }
    if ($acct['locked_until'] && strtotime($acct['locked_until']) > time()) {
        return [null, 'Too many unsuccessful attempts. Try again in ' . RA_LOCK_MINUTES . ' minutes, or reset your password.'];
    }
    if (!password_verify((string) $password, $acct['password_hash'])) {
        $fails = (int) $acct['failed_logins'] + 1;
        $lock  = $fails >= RA_MAX_FAILED_LOGINS ? date('Y-m-d H:i:s', time() + RA_LOCK_MINUTES * 60) : null;
        $pdo->prepare("UPDATE research_accounts SET failed_logins = ?, locked_until = ? WHERE id = ?")
            ->execute([$lock ? 0 : $fails, $lock, $acct['id']]);
        return [null, $generic];
    }
    if ($acct['status'] === 'suspended') {
        return [null, 'This account has been suspended. Please contact the Research Office.'];
    }

    $pdo->prepare("UPDATE research_accounts SET failed_logins = 0, locked_until = NULL, last_login_at = NOW() WHERE id = ?")
        ->execute([$acct['id']]);
    if (password_needs_rehash($acct['password_hash'], PASSWORD_DEFAULT)) {
        $pdo->prepare("UPDATE research_accounts SET password_hash = ? WHERE id = ?")
            ->execute([password_hash($password, PASSWORD_DEFAULT), $acct['id']]);
    }
    return [$acct, ''];
}

/**
 * Creates a reset token for an account and returns the full reset URL.
 * Only the token's hash is stored; the link is valid for $hours.
 */
function ra_issue_reset(PDO $pdo, $accountId, $hours = 24)
{
    $token = bin2hex(random_bytes(32));
    $pdo->prepare("UPDATE research_accounts SET reset_token_hash = ?, reset_expires_at = ? WHERE id = ?")
        ->execute([hash('sha256', $token), date('Y-m-d H:i:s', time() + $hours * 3600), (int) $accountId]);
    return ra_base_url() . 'research/reset.php?token=' . $token;
}

/** The account a reset token belongs to, if it is valid and unexpired. */
function ra_account_for_token(PDO $pdo, $token)
{
    $token = (string) $token;
    if (!preg_match('/^[a-f0-9]{64}$/', $token)) {
        return null;
    }
    $stmt = $pdo->prepare(
        "SELECT * FROM research_accounts WHERE reset_token_hash = ? AND reset_expires_at > NOW()"
    );
    $stmt->execute([hash('sha256', $token)]);
    return $stmt->fetch(PDO::FETCH_ASSOC) ?: null;
}

/** https://host/path-to-site/ — for links inside emails. */
function ra_base_url()
{
    $https = (!empty($_SERVER['HTTPS']) && strtolower($_SERVER['HTTPS']) !== 'off')
          || (($_SERVER['HTTP_X_FORWARDED_PROTO'] ?? '') === 'https');
    $host  = $_SERVER['HTTP_HOST'] ?? 'vvu.edu.gh';
    $host  = preg_replace('/[^A-Za-z0-9.:-]/', '', $host);
    // This file lives in <site>/research/includes, so the site root is two up
    // from the running script when that script is in research/ or admin/.
    $script = str_replace('\\', '/', (string) ($_SERVER['SCRIPT_NAME'] ?? '/'));
    $root   = rtrim(dirname(dirname($script)), '/') . '/';
    return ($https ? 'https' : 'http') . '://' . $host . $root;
}

/**
 * Best-effort email. Returns true when PHP handed it to the mail system.
 * Every flow that sends mail also works without it (the Research Office can
 * issue links from the admin), because many hosts have no working mail().
 */
function ra_mail(PDO $pdo, $to, $subject, $body)
{
    $to = trim((string) $to);
    if (!filter_var($to, FILTER_VALIDATE_EMAIL) || !function_exists('mail')) {
        return false;
    }
    $from = r_set($pdo, 'contact_email', 'research@vvu.edu.gh');
    $headers = 'From: VVU Scholar <' . $from . ">\r\n"
             . 'Reply-To: ' . $from . "\r\n"
             . "Content-Type: text/plain; charset=UTF-8\r\n";
    return @mail($to, '=?UTF-8?B?' . base64_encode($subject) . '?=', $body, $headers);
}

/** Where claim notifications go: the dedicated setting, else the portal contact. */
function ra_office_email(PDO $pdo)
{
    $e = trim((string) r_set($pdo, 'claim_notify_email', ''));
    return $e !== '' ? $e : r_set($pdo, 'contact_email', '');
}

/* ==========================================================================
   Claims & profile ownership
   ========================================================================== */

/** Account id that owns a scholar profile (approved), or 0. */
function ra_owner_of(PDO $pdo, $scholarId)
{
    $stmt = $pdo->prepare("SELECT id FROM research_accounts WHERE scholar_id = ? AND status IN ('active','suspended') LIMIT 1");
    $stmt->execute([(int) $scholarId]);
    return (int) $stmt->fetchColumn();
}

/** True when someone has a pending claim on this profile. */
function ra_claim_pending(PDO $pdo, $scholarId)
{
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM research_claims WHERE scholar_id = ? AND status = 'pending'");
    $stmt->execute([(int) $scholarId]);
    return (int) $stmt->fetchColumn() > 0;
}

/** The account's most recent claim. */
function ra_latest_claim(PDO $pdo, $accountId)
{
    $stmt = $pdo->prepare(
        "SELECT c.*, s.full_name AS scholar_name, s.slug AS scholar_slug
           FROM research_claims c
           LEFT JOIN research_scholars s ON s.id = c.scholar_id
          WHERE c.account_id = ?
          ORDER BY c.id DESC LIMIT 1"
    );
    $stmt->execute([(int) $accountId]);
    return $stmt->fetch(PDO::FETCH_ASSOC) ?: null;
}

/** Profiles a visitor can claim: name search over active, unowned scholars. */
function ra_claimable(PDO $pdo, $q, $limit = 12)
{
    $q = trim((string) $q);
    if (mb_strlen($q) < 2) {
        return [];
    }
    $like = '%' . str_replace(['\\', '%', '_'], ['\\\\', '\%', '\_'], $q) . '%';
    $stmt = $pdo->prepare(
        "SELECT s.id, s.full_name, s.slug, s.title, s.photo, s.position, s.publications_count, s.citations,
                u.name AS unit_name,
                (SELECT COUNT(*) FROM research_accounts a
                  WHERE a.scholar_id = s.id AND a.status IN ('active','suspended')) AS owned,
                (SELECT COUNT(*) FROM research_claims c
                  WHERE c.scholar_id = s.id AND c.status = 'pending') AS pending
           FROM research_scholars s
           LEFT JOIN research_units u ON u.id = s.unit_id
          WHERE s.is_active = 1 AND s.full_name LIKE ?
          ORDER BY s.publications_count DESC, s.full_name ASC
          LIMIT " . (int) $limit
    );
    $stmt->execute([$like]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

/** Writes one line to the activity log the Research Office reads. */
function ra_log(PDO $pdo, $accountId, $scholarId, $action, $detail = '')
{
    try {
        $pdo->prepare("INSERT INTO research_activity (account_id, scholar_id, action, detail) VALUES (?,?,?,?)")
            ->execute([$accountId ?: null, $scholarId ?: null, $action, mb_substr((string) $detail, 0, 600)]);
    } catch (Exception $e) {
        error_log('VVU Scholar: activity log failed — ' . $e->getMessage());
    }
}

/** Units as [id => label], each department indented under its faculty. */
function ra_unit_options(PDO $pdo)
{
    $out = [];
    try {
        $rows = $pdo->query(
            "SELECT id, parent_id, name FROM research_units WHERE is_active = 1 ORDER BY display_order, name"
        )->fetchAll(PDO::FETCH_ASSOC);
    } catch (Exception $e) {
        return $out;
    }
    $kids = [];
    foreach ($rows as $r) {
        if ($r['parent_id']) { $kids[(int) $r['parent_id']][] = $r; }
    }
    foreach ($rows as $r) {
        if ($r['parent_id']) { continue; }
        $out[(int) $r['id']] = $r['name'];
        foreach ($kids[(int) $r['id']] ?? [] as $k) {
            $out[(int) $k['id']] = '— ' . $k['name'];
        }
    }
    return $out;
}

/** "0000-0002-1825-0097" from a bare iD or an orcid.org URL; '' when invalid. */
function ra_clean_orcid($raw)
{
    $raw = strtoupper(trim((string) $raw));
    $raw = preg_replace('#^HTTPS?://(WWW\.)?ORCID\.ORG/#', '', $raw);
    return preg_match('/^\d{4}-\d{4}-\d{4}-\d{3}[\dX]$/', $raw) ? $raw : '';
}

/** Google Scholar user id from a profile URL or the bare id; '' when invalid. */
function ra_clean_scholar_id($raw)
{
    $raw = trim((string) $raw);
    if (preg_match('/[?&]user=([A-Za-z0-9_-]{8,20})/', $raw, $m)) {
        return $m[1];
    }
    return preg_match('/^[A-Za-z0-9_-]{8,20}$/', $raw) ? $raw : '';
}

/** An http(s) URL or ''. */
function ra_clean_url($raw)
{
    $raw = trim((string) $raw);
    if ($raw === '') {
        return '';
    }
    if (!preg_match('#^https?://#i', $raw)) {
        $raw = 'https://' . $raw;
    }
    return filter_var($raw, FILTER_VALIDATE_URL) ? mb_substr($raw, 0, 500) : '';
}

/* ==========================================================================
   Page frame
   ========================================================================== */

/** Opens an account page: site header, portal hero and sub-navigation. */
function ra_page_open(PDO $pdo, $title, $lead = '', array $crumbs = [])
{
    // header.php reads these as globals (it is normally included at file scope).
    global $vvu_root, $page_title, $active_page, $pdo;
    $vvu_root    = '../';
    $page_title  = $title . ' — VVU Scholar';
    $active_page = 'research';
    include __DIR__ . '/../../includes/header.php';
    ?>
    <link rel="stylesheet" href="assets/research.css?v=1.2">
    <link rel="stylesheet" href="assets/research-account.css?v=1.1">
    <main id="main-content" class="vvus">
        <?php r_pagehero('VVU Scholar', $title, $lead, $crumbs ?: ['Research Portal' => 'index.php', $title => null]); ?>
        <?php r_subnav('account.php'); ?>
    <?php
}

function ra_page_close()
{
    global $vvu_root, $pdo; // footer.php reads these as globals
    ?>
    </main>
    <script src="assets/research-account.js?v=1.0" defer></script>
    <?php
    include __DIR__ . '/../../includes/footer.php';
}

/** The one-shot message, if any, as an alert box. */
function ra_alerts($error = '', $success = '')
{
    $flash = ra_flash();
    if ($flash) {
        if ($flash[0] === 'error') { $error = $error ?: $flash[1]; } else { $success = $success ?: $flash[1]; }
    }
    if ($error !== '') {
        echo '<div class="ra-alert ra-alert--error" role="alert"><i class="fa-solid fa-circle-exclamation" aria-hidden="true"></i><span>' . r_e($error) . '</span></div>';
    }
    if ($success !== '') {
        echo '<div class="ra-alert ra-alert--ok" role="status"><i class="fa-solid fa-circle-check" aria-hidden="true"></i><span>' . r_e($success) . '</span></div>';
    }
}

/** Shown on every account page when the SQL has not been imported yet. */
function ra_not_ready_notice()
{
    echo '<div class="ra-alert ra-alert--error"><i class="fa-solid fa-database" aria-hidden="true"></i><span>'
       . 'Researcher accounts are not switched on yet. The site administrator needs to import '
       . '<code>sql/research_accounts_migration.sql</code>.</span></div>';
}

} // VVU_RESEARCH_ACCOUNT
