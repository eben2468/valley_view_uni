# VVU Alpha — Security Remediation

Response to the penetration test of 9–10 August 2026 (`VAPT Scan for alpha.txt`),
which returned a **NO-GO** verdict for launch.

This document has two halves:

- **Part A — done in code.** Already committed in this repository.
- **Part B — must be done on the server / in accounts.** Nobody can do these
  from the codebase; they are yours to run.

Nothing here is finished until **Part B step 1** (rotate the live admin password)
is done. Until then the live site is still fully compromisable.

Work Part B in order. Steps 0 and 0b must come first — they stop the deploy from
taking the site down.

---

## Part A — Fixed in code

| # | Finding | What changed |
|---|---------|--------------|
| 1 | Default admin credentials | Default account removed from both schema files; `tools/set_admin_password.php` provisions accounts out-of-band; per-account lockout after 5 failures for 15 min; session id rotated on login; CSRF token on the login form; generic error messages; failed logins written to the error log |
| 2 | Exposed `.git/` | Denied in `.htaccess`; nginx equivalent in `nginx-vvu.conf.example` |
| 3 | Source public on GitHub | Credentials removed from `includes/db_connect.php` — they now come from git-ignored `includes/config.php` or `DB_*` env vars; `.gitignore` added; DB connection errors no longer echo the exception to the browser |
| 4 | phpMyAdmin exposed | Denied at the web-server level (config only — **also uninstall it**, Part B) |
| 5 | Tomcat on 8080 | Firewall + reverse-proxy instructions (Part B) |
| 6 | DSpace on 4000 | Reverse-proxy server block supplied in `nginx-vvu.conf.example` (Part B) |
| 7 | Missing security headers | `includes/security_headers.php` (incl. a CSP), plus header blocks in `.htaccess` and the nginx config |
| 8 | Outdated jQuery 3.6.0 | Upgraded to 3.7.1 and self-hosted at `js/vendor/` — the CDN no longer has script-execution rights on the site |

### Also fixed — issues the scan did NOT find

The pentest's Gobuster run used `common.txt`, which contains none of the
filenames below, so these were missed. Two of them are more severe than several
of the reported findings.

**A. Unauthenticated file upload → remote code execution (critical).**
`admin/campus_life_image_upload.php` checked the uploaded file's MIME type by
content, then built the saved filename using the extension from the *attacker's*
filename. A genuine GIF uploaded as `shell.php` passed the MIME check and was
written into the web root as executable PHP. The endpoint also only called
`session_start()` — it never checked that anyone was logged in, so this was
reachable by anyone on the internet. The extension is now derived from the
detected MIME type, and the endpoint requires authentication.

**B. Six unauthenticated admin editors (critical).**
`admin/campus_life_editors/edit_{accommodation,food_services,philosophy_on_dress,radio,sld,work_study}.php`
had no login check and called `move_uploaded_file()` with
`basename($_FILES[...]['name'])` and no type validation at all — 14 upload sites
in total, each an arbitrary file write into the web root. All now require
authentication and route through the validating `handleAdminFileUpload()` helper.

**C. Unauthenticated write to `academic_programs` (high).**
`admin/edit_program_detail.php` handled its `$_POST` INSERT/UPDATE in lines 9–38
and only reached its login check — `include 'header.php'` — on line 40. The
`add` branch redirects and `exit()`s at line 32, so authentication was never
evaluated at all on that path. The guard is now the first statement in the file.

**D. 65 unauthenticated maintenance scripts in the web root (high).**
`install_*`, `migrate_*`, `seed_*`, `fix_*`, `update_*`, `check_*`, `debug_*`,
`db_dump.php`, `list_tables.php`, `import_database.php` and friends all opened a
database connection with no login check, and most of them wrote to it. The worst
was `test.php`, which called **`phpinfo()`** — full PHP configuration, absolute
paths, loaded extensions and environment variables to any visitor. All 65 moved
to `dev-tools/`, which is git-ignored and denied by both server configs. See
`dev-tools/README.md`.

**E. Lockout silently disabled by a timezone mismatch (high).**
Worth calling out because it would have looked fine in review. PHP on this stack
runs `Europe/Berlin` while MySQL is two hours behind, so the natural
`strtotime($row['locked_until']) > time()` check compared timestamps from two
different clocks and every lockout read as already expired. The comparison is
now done inside the SQL query, against the database's own `NOW()`.

**F. Session hardening.** `includes/session_bootstrap.php` sets `HttpOnly`,
`SameSite=Lax`, `Secure` (auto-detected over HTTPS), strict session-id mode, a
non-default cookie name, and a 1-hour idle timeout.

### New files

| File | Purpose |
|------|---------|
| `includes/config.example.php` | Template — copy to `includes/config.php` per environment |
| `includes/session_bootstrap.php` | Hardened session start, CSRF helpers |
| `includes/security_headers.php` | Security headers + CSP, applied app-wide |
| `includes/admin_auth.php` | The admin authentication gate |
| `tools/set_admin_password.php` | CLI-only account provisioning / password reset |
| `tools/security_migration.sql` | Adds lockout columns, deletes the leaked account |
| `nginx-vvu.conf.example` | Full production nginx server block |
| `uploads/.htaccess` | Belt-and-braces: uploads can never execute |
| `.gitignore` | Keeps credentials and dumps out of the repo |

### Verified locally

Homepage and admin panel return 200; `.git/HEAD`, `.gitignore`, `*.sql`,
`*.md`, `includes/`, `tools/` and `dev-tools/` all return 403; the unauthenticated
upload endpoint returns `{"success":false,"message":"Authentication required."}`;
direct hits on the campus-life editors redirect to the login page; lockout
engages on the 6th attempt and a correct password still logs in and clears it;
all five security headers plus the CSP are present on responses.

> ⚠️ **Your local dev admin password was changed during testing** to
> `TempVerify!2026#x` (the original hash was overwritten and cannot be restored).
> Reset it with `php tools/set_admin_password.php admin`. This affects the local
> XAMPP database only, not the live server.

---

## Part B — You must do these on the server

Your server details, for reference throughout:

| Thing | Value |
|-------|-------|
| Host | `dspace-srv` (`alpha.vvu.edu.gh`) |
| Site directory | `/home/valley_view_uni` |
| Deploy method | `git pull origin master` |
| Web server | nginx |

### Step 0 — Pre-flight: create `includes/config.php` BEFORE you pull

**Read this before running anything.** Skipping it takes the live site down.

The new `includes/db_connect.php` reads its credentials from
`includes/config.php`, which is deliberately **not** in git. Your server does not
have that file yet. If you pull the new code first, every page will show
*"Service temporarily unavailable."* until you create it.

Your server also has its own edited copy of `includes/db_connect.php` — that is
why `git pull` failed with *"Your local changes would be overwritten"*. That
edited file contains your **live** database credentials, so read them out before
you discard it.

```bash
cd /home/valley_view_uni

# 1. Read the live credentials currently in use. Write down the four values.
grep -E '^\$(servername|username|password|dbname)' includes/db_connect.php

# 2. Keep a copy of the old file outside the site, just in case.
cp includes/db_connect.php /root/db_connect.php.backup

# 3. Back up the database before touching anything.
mysqldump -u root -p valley_view_uni > /root/vvu-backup-$(date +%F).sql
```

Now create the config file with the values from step 1:

```bash
nano includes/config.php
```

Paste this, substituting your four real values:

```php
<?php
return [
    'host'    => 'localhost',
    'name'    => 'valley_view_uni',
    'user'    => 'PUT_THE_USERNAME_HERE',
    'pass'    => 'PUT_THE_PASSWORD_HERE',
    'charset' => 'utf8mb4',
];
```

Save with `Ctrl+O`, `Enter`, then exit with `Ctrl+X`. Lock the file down:

```bash
chmod 640 includes/config.php
chown root:www-data includes/config.php
```

### Step 0b — Now pull the new code

```bash
cd /home/valley_view_uni
git checkout -- includes/db_connect.php   # discard the server's edited copy
git pull origin master
```

Check the site immediately — open `https://alpha.vvu.edu.gh` in a browser. If you
see *"Service temporarily unavailable"*, the credentials in `includes/config.php`
are wrong; re-check them against `/root/db_connect.php.backup`.

> **Note on `.git`:** because you deploy by pulling, the `.git` directory has to
> stay on the server, so Finding 2 is closed by the nginx rule in Step 3 rather
> than by deleting it. Step 3 is therefore not optional for you.

### Step 1 — Rotate the live admin password (do this today)

`admin` / `password` is a working login on the live site right now. Everything
else is secondary.

```bash
cd /home/valley_view_uni

mysql -u root -p valley_view_uni < tools/security_migration.sql
php tools/set_admin_password.php admin
```

The migration prints three `Duplicate column name` errors if you run it twice —
that is expected and harmless.

Then review the account list the migration prints. **Every row must be a real,
named person.** Delete anything you don't recognise:

```sql
DELETE FROM admin_users WHERE username = 'whatever';
```

Assume the CMS was accessed by someone else. Before launch, check
`uploads/` for files you didn't put there — especially anything ending in
`.php`, `.phtml` or `.phar`:

```bash
find uploads/ -type f \( -name '*.php*' -o -name '*.phtml' -o -name '*.phar' \) -ls
```

Also spot-check page content in the CMS for defacement.

### Step 2 — Make the GitHub repository private

`https://github.com/eben2468/valley_view_uni` is publicly cloneable from a
personal account.

1. GitHub → repo → **Settings** → *Danger Zone* → **Change visibility** →
   Private.
2. Transfer it to a VVU-controlled organisation account. A university's site
   should not depend on one person's personal GitHub.
3. Treat everything ever committed as permanently leaked — anyone could have
   cloned it already. Making it private does not undo that, which is why
   Step 2 is not optional.

### Step 3 — Rotate the database credentials

The repo published MySQL `root` with an empty password. Even though phpMyAdmin
rejected it, stop using `root` from the web app.

```sql
CREATE USER 'vvu_web'@'localhost' IDENTIFIED BY '<long random password>';
GRANT SELECT, INSERT, UPDATE, DELETE ON valley_view_uni.* TO 'vvu_web'@'localhost';
FLUSH PRIVILEGES;

-- and give root an actual password
ALTER USER 'root'@'localhost' IDENTIFIED BY '<different long random password>';
```

The web app never needs `DROP`, `ALTER`, `CREATE` or `GRANT`. Then, on the server:

```bash
cp includes/config.example.php includes/config.php
nano includes/config.php          # user: vvu_web, pass: the new password
chmod 640 includes/config.php
chown root:www-data includes/config.php
```

Confirm it is not readable over the web — `https://alpha.vvu.edu.gh/includes/config.php`
must return 403 or 404.

### Step 4 — Deploy the nginx configuration

`.htaccess` does nothing on nginx. This is the step that actually closes
Finding 2 in production.

```bash
sudo cp nginx-vvu.conf.example /etc/nginx/sites-available/alpha.vvu.edu.gh
sudo nano /etc/nginx/sites-available/alpha.vvu.edu.gh   # set root + php-fpm socket
sudo ln -sf /etc/nginx/sites-available/alpha.vvu.edu.gh /etc/nginx/sites-enabled/
sudo nginx -t          # must pass before you reload
sudo systemctl reload nginx
```

Add the rate-limit zone to `http{}` (it cannot live in a server block):

```bash
echo 'limit_req_zone $binary_remote_addr zone=vvu_login:10m rate=5r/m;' \
  | sudo tee /etc/nginx/conf.d/vvu-limits.conf
```

Then uncomment `limit_req zone=vvu_login burst=5 nodelay;` in the
`location = /admin/login.php` block and reload again.

Verify:

```bash
curl -sI https://alpha.vvu.edu.gh/.git/HEAD | head -1        # expect 404
curl -sI https://alpha.vvu.edu.gh/.git/config | head -1      # expect 404
curl -sI https://alpha.vvu.edu.gh/phpmyadmin/ | head -1      # expect 404
```

### Step 5 — Tighten what gets deployed

Blocking it is the safety net; not shipping it is the fix.

```bash
rsync -av --delete \
  --exclude='.git' --exclude='.gitignore' --exclude='dev-tools' \
  --exclude='includes/config.php' --exclude='*.md' --exclude='.qoder' \
  ./ youruser@alpha.vvu.edu.gh:/home/valley_view_uni/
```

Note `--exclude='includes/config.php'` — the server's own config must never be
overwritten by a local one.

If the live directory is currently a git working copy, delete `.git` from it
after you have a deployment method that doesn't need it:

```bash
rm -rf /home/valley_view_uni/.git
```

### Step 6 — Remove phpMyAdmin

Blocking it in nginx is not the same as removing it.

```bash
sudo apt-get remove --purge phpmyadmin
sudo rm -rf /usr/share/phpmyadmin /etc/phpmyadmin
```

If operations genuinely need a GUI, don't expose it — tunnel to it:

```bash
ssh -L 8888:localhost:80 youruser@alpha.vvu.edu.gh
# then browse http://localhost:8888/phpmyadmin/
```

### Step 7 — Close ports 8080 (Tomcat) and 4000 (DSpace)

Bind both to loopback:

- **Tomcat** — in `server.xml`, add `address="127.0.0.1"` to the `<Connector port="8080">`.
- **DSpace** — start the Node UI on `--host 127.0.0.1` (or set it in `config.prod.yml`).

Then firewall them:

```bash
sudo ufw deny 8080/tcp
sudo ufw deny 4000/tcp
sudo ufw reload
```

Upgrade both while you're there — Tomcat 9.0.85 (Jan 2024) and DSpace 7.6.1 both
have published advisories. Serve DSpace through the `dspace.vvu.edu.gh` TLS
server block at the bottom of `nginx-vvu.conf.example`.

Also confirm DSpace is even in scope for this host — the pentest was unsure, and
it is the sort of thing that gets left running by accident.

### Step 8 — Restrict SSH

```bash
sudo nano /etc/ssh/sshd_config
```

```
PermitRootLogin no
PasswordAuthentication no      # key-based auth only
AllowUsers youruser
```

```bash
sudo systemctl restart sshd
sudo apt-get install fail2ban && sudo systemctl enable --now fail2ban
```

### Step 9 — Put the site behind Cloudflare

DNS is already on Cloudflare nameservers, but `alpha.vvu.edu.gh` resolves
straight to the origin IP — the orange cloud is off, so there is no WAF, no DDoS
protection, and the origin IP is public.

1. Cloudflare dashboard → DNS → toggle the `alpha` record to **Proxied**.
2. SSL/TLS mode → **Full (strict)**.
3. Firewall → enable the Managed Ruleset.
4. Add a rate-limiting rule on `/admin/login.php`.
5. Firewall the origin so it only accepts :80/:443 from Cloudflare's IP ranges —
   otherwise attackers who know the origin IP can bypass all of the above.

### Step 10 — Review the 64 enumerated subdomains

Out of scope for the pentest, but `vaultwarden.`, `backup.`, `test.`, `uat.`,
`homeassistant.`, `plex.` and `proxy.` on a university domain each deserve their
own look. `vaultwarden.` is a password manager — if that is VVU's, it is the
highest-value target on the estate and should be reviewed first.

### Step 11 — Re-test, then decide Go / No-Go

```bash
curl -sI https://alpha.vvu.edu.gh/.git/HEAD          # 404
curl -sI https://alpha.vvu.edu.gh/phpmyadmin/        # 404
curl -sI https://alpha.vvu.edu.gh/ | grep -i strict  # HSTS present
nmap -p 22,80,443,4000,8080 alpha.vvu.edu.gh         # 4000/8080 filtered
git clone https://github.com/eben2468/valley_view_uni.git   # must fail
```

And confirm by hand that `admin` / `password` no longer logs in.

---

## Priority order

| When | Steps |
|------|-------|
| **Today** | 0, 0b (deploy safely), 1 (rotate admin password), 2 (repo private) |
| **Before launch** | 3, 4, 5, 6 |
| **Before launch** | 7, 8 |
| **Soon after** | 9, 10 |
| **Then** | 11 — re-test and re-decide Go / No-Go |

---

## Known gaps — honest limitations

These are real and not yet addressed. Listing them so they aren't mistaken for
finished work.

1. **CSRF protection is available but not yet applied site-wide.** The helpers
   exist (`vvu_csrf_field()`, `vvu_require_csrf()`) and the login form uses
   them, but the ~40 other admin POST forms do not yet. Until they do, a logged-in
   admin who visits a hostile page can be made to submit admin actions. Add
   `<?= vvu_csrf_field() ?>` to each admin form and `vvu_require_csrf();` to each
   POST handler.

2. **The CSP still allows `'unsafe-inline'` and `'unsafe-eval'`.** Hundreds of
   templates use inline scripts, and the Tailwind Play CDN needs `eval`. The
   policy meaningfully limits *where* external scripts may load from, but it
   will not stop an injected inline `<script>`. Tightening it means replacing
   the Tailwind CDN with a compiled stylesheet and moving inline handlers into
   `js/` files.

3. **No output-escaping audit was done.** The pentest found no SQL injection and
   the code does use prepared statements, but stored XSS — content saved through
   the CMS and echoed without `htmlspecialchars()` — was not systematically
   reviewed. Worth a dedicated pass, particularly given that the CMS was
   compromisable.

4. **No logging or alerting.** Failed logins now reach the PHP error log, but
   nothing watches it. Consider fail2ban on the admin login, or a weekly review.
