#!/usr/bin/env bash
# ═══════════════════════════════════════════════════════════════════════════
# Valley View University — external hardening check
#
# Re-runs the pentest's key findings against the LIVE site and reports what is
# actually closed. Read-only: it makes ordinary HTTP requests and changes
# nothing.
#
# Run it from a machine OUTSIDE the server (your laptop). Running it on
# dspace-srv itself makes the port checks meaningless, because connections to
# 127.0.0.1 bypass the firewall and always succeed.
#
#     bash tools/verify_hardening.sh
#
# Exit code is 0 when every check passes, 1 otherwise.
# ═══════════════════════════════════════════════════════════════════════════

HOST="${1:-alpha.vvu.edu.gh}"
FAILED=0

green() { printf '  \033[32mPASS\033[0m  %s\n' "$1"; }
red()   { printf '  \033[31mFAIL\033[0m  %s\n' "$1"; FAILED=1; }
warn()  { printf '  \033[33m????\033[0m  %s\n' "$1"; }
head_() { printf '\n\033[1m%s\033[0m\n' "$1"; }

code() { curl -s -o /dev/null -w '%{http_code}' --max-time 15 "$1" 2>/dev/null; }

head_ "Finding 2 — source code disclosure via .git"
for p in /.git/HEAD /.git/config /.gitignore; do
    c=$(code "https://$HOST$p")
    if [ "$c" = "200" ]; then red "$p returns 200 — source code is downloadable"
    else green "$p blocked ($c)"; fi
done

head_ "Finding 3 — leaked configuration files"
for p in /includes/config.php /includes/db_connect.php /database_schema.sql /SECURITY_REMEDIATION.md; do
    c=$(code "https://$HOST$p")
    if [ "$c" = "200" ]; then red "$p returns 200"
    else green "$p blocked ($c)"; fi
done

head_ "Unauthenticated maintenance scripts (not in the original report)"
for p in /test.php /check_db.php /db_dump.php /dev-tools/check_db.php /dev-tools/test.php; do
    c=$(code "https://$HOST$p")
    if [ "$c" = "200" ]; then red "$p returns 200 — runs SQL without a login"
    else green "$p blocked ($c)"; fi
done

head_ "Finding 4 — database console"
c=$(code "https://$HOST/phpmyadmin/")
if [ "$c" = "200" ]; then red "/phpmyadmin/ returns 200"; else green "/phpmyadmin/ blocked ($c)"; fi

head_ "Unauthenticated admin endpoints"
c=$(curl -s --max-time 15 -X POST "https://$HOST/admin/campus_life_image_upload.php" \
        -H 'X-Requested-With: XMLHttpRequest' 2>/dev/null)
if printf '%s' "$c" | grep -qi 'Authentication required'; then
    green "campus_life_image_upload.php requires authentication"
else
    red "campus_life_image_upload.php did not demand auth — check the deploy"
fi

c=$(code "https://$HOST/admin/campus_life_editors/edit_sld.php")
if [ "$c" = "302" ] || [ "$c" = "301" ]; then green "campus-life editors redirect to login ($c)"
else red "campus-life editor returned $c, expected a redirect to login"; fi

head_ "Finding 7 — security headers"
H=$(curl -sI --max-time 15 "https://$HOST/" 2>/dev/null)
for h in "Strict-Transport-Security" "X-Frame-Options" "X-Content-Type-Options" "Referrer-Policy" "Permissions-Policy" "Content-Security-Policy"; do
    if printf '%s' "$H" | grep -qi "^$h:"; then green "$h present"; else red "$h missing"; fi
done

head_ "Findings 5 & 6 — directly exposed service ports"
for port in 8080 4000; do
    if curl -s -o /dev/null --max-time 8 "http://$HOST:$port/" 2>/dev/null; then
        red "port $port is reachable from the internet"
    else
        green "port $port not reachable"
    fi
done

head_ "Site still works"
c=$(code "https://$HOST/")
if [ "$c" = "200" ]; then green "homepage returns 200"; else red "homepage returns $c"; fi
c=$(code "https://$HOST/admin/login.php")
if [ "$c" = "200" ]; then green "admin login page returns 200"; else red "admin login returns $c"; fi

head_ "Cannot be checked from here — confirm these by hand"
warn "Finding 1: does admin / password still log in? Try it."
warn "Finding 3: is github.com/eben2468/valley_view_uni still publicly cloneable?"
warn "Finding 3: is the web app still connecting to MySQL as root?"

printf '\n'
if [ "$FAILED" = "0" ]; then
    printf '\033[32mAll automated checks passed.\033[0m Confirm the three manual items above.\n'
else
    printf '\033[31mSome checks failed — see FAIL lines above.\033[0m\n'
fi
exit "$FAILED"
