-- Valley View University — security remediation migration
-- Apply once to an EXISTING database (new installs get these from
-- database_schema.sql). Safe to re-run: each statement is guarded.
--
--     mysql -u root -p valley_view_uni < tools/security_migration.sql

USE valley_view_uni;

-- 1. Brute-force lockout state for admin/login.php (pentest Finding 1).
--    MySQL has no "ADD COLUMN IF NOT EXISTS" before 8.0.29, so these are
--    written as ignorable errors — a duplicate-column error here is expected
--    on a second run and can be ignored.
ALTER TABLE admin_users ADD COLUMN failed_attempts INT NOT NULL DEFAULT 0;
ALTER TABLE admin_users ADD COLUMN locked_until DATETIME NULL DEFAULT NULL;
ALTER TABLE admin_users ADD COLUMN last_login_at DATETIME NULL DEFAULT NULL;

-- 2. Remove the leaked default account.
--    The bcrypt hash below is the hash of the literal string "password" that
--    was published in the public GitHub repo. Delete any account still using
--    it, whatever it was renamed to.
DELETE FROM admin_users
 WHERE password = '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi';

-- 3. Review what is left. Every row here must be a real, named person whose
--    password you have just rotated with tools/set_admin_password.php.
SELECT id, username, email, full_name, created_at FROM admin_users;
