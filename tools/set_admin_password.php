<?php
/**
 * Create an administrator, or reset an existing one's password.
 *
 * CLI ONLY — it refuses to run over HTTP, so it is harmless even if the tools/
 * directory is accidentally left inside the web root (it is also denied at the
 * web-server level; see .htaccess / nginx-vvu.conf.example).
 *
 * Usage:
 *     php tools/set_admin_password.php <username> [email] [full name]
 *
 * The password is read from the terminal, never from an argument, so it does
 * not end up in shell history or the process list.
 */

if (PHP_SAPI !== 'cli') {
    http_response_code(404);
    exit;
}

require_once __DIR__ . '/../includes/db_connect.php';

const MIN_PASSWORD_LENGTH = 14;

$username = $argv[1] ?? null;
$email    = $argv[2] ?? null;
$fullName = $argv[3] ?? 'Administrator';

if ($username === null) {
    fwrite(STDERR, "Usage: php tools/set_admin_password.php <username> [email] [full name]\n");
    exit(1);
}

/**
 * Read a line from the terminal with echo disabled where the platform allows
 * it. Windows has no stty, so it falls back to a visible prompt and warns.
 */
function prompt_secret(string $label): string {
    fwrite(STDOUT, $label);

    if (DIRECTORY_SEPARATOR !== '\\' && shell_exec('command -v stty') !== null) {
        shell_exec('stty -echo');
        $value = rtrim((string) fgets(STDIN), "\r\n");
        shell_exec('stty echo');
        fwrite(STDOUT, "\n");
        return $value;
    }

    $value = rtrim((string) fgets(STDIN), "\r\n");
    fwrite(STDOUT, "  (note: this terminal cannot hide input — clear your scrollback)\n");
    return $value;
}

$password = prompt_secret('New password: ');
$confirm  = prompt_secret('Confirm password: ');

if ($password !== $confirm) {
    fwrite(STDERR, "Passwords do not match. Nothing was changed.\n");
    exit(1);
}

if (strlen($password) < MIN_PASSWORD_LENGTH) {
    fwrite(STDERR, "Password must be at least " . MIN_PASSWORD_LENGTH . " characters. Nothing was changed.\n");
    exit(1);
}

// Reject the credentials the pentest found live, and the other usual suspects.
$forbidden = ['password', 'admin123', 'Password1', 'valleyview', 'vvu2026'];
if (in_array(strtolower($password), array_map('strtolower', $forbidden), true)) {
    fwrite(STDERR, "That password is on the known-compromised list. Choose another.\n");
    exit(1);
}

$hash = password_hash($password, PASSWORD_DEFAULT);

$existing = $pdo->prepare('SELECT id FROM admin_users WHERE username = ?');
$existing->execute([$username]);
$id = $existing->fetchColumn();

if ($id) {
    $update = $pdo->prepare('UPDATE admin_users SET password = ? WHERE id = ?');
    $update->execute([$hash, $id]);
    echo "Password updated for existing admin '{$username}'.\n";
} else {
    if ($email === null) {
        fwrite(STDERR, "Admin '{$username}' does not exist — an email address is required to create it.\n");
        exit(1);
    }
    $insert = $pdo->prepare(
        'INSERT INTO admin_users (username, password, email, full_name) VALUES (?, ?, ?, ?)'
    );
    $insert->execute([$username, $hash, $email, $fullName]);
    echo "Created admin '{$username}'.\n";
}

// Clear any lockout left over from brute-force attempts against this account.
try {
    $pdo->prepare('UPDATE admin_users SET failed_attempts = 0, locked_until = NULL WHERE username = ?')
        ->execute([$username]);
} catch (PDOException $e) {
    // Columns not present yet — see tools/security_migration.sql. Not fatal.
}

echo "Done.\n";
