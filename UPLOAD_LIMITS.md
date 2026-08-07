# Fixing "413 Request Entity Too Large" on uploads

## What the error actually means

`413` comes from the **web server**, not from PHP. The server measures the size
of the incoming request, decides it is too big, and rejects it *before PHP is
started*. That is why the admin panel shows a bare error page instead of a
friendly message — none of our code ever ran.

Because of that, raising `upload_max_filesize` alone will **not** fix it if the
limit being hit is the web server's.

It works locally because XAMPP allows 40MB by default.

## Three limits, in the order the request meets them

| # | Limit | Where it lives | Symptom when exceeded |
|---|-------|----------------|----------------------|
| 1 | `client_max_body_size` (nginx) or `LimitRequestBody` (Apache) | Web server config | **413 Request Entity Too Large** |
| 2 | `post_max_size` | PHP | Empty `$_POST`, form silently does nothing |
| 3 | `upload_max_filesize` | PHP | File rejected, now reported in the admin panel |

Rule of thumb: `client_max_body_size` ≥ `post_max_size` > `upload_max_filesize`.

## What is already handled in this repo

**Browser-side shrinking (`admin/js/upload-guard.js`)** — loaded on every admin
page. Any image over ~1.2MB is resized to a maximum of 2000px on its longest
edge and re-encoded before the upload starts. A 23MB camera photo leaves the
browser at about 1.2MB, which is under the default limit of essentially every
host. **This alone resolves the error for normal photo uploads**, with no server
configuration required.

It deliberately leaves alone: PDFs and documents, GIFs (to keep animation),
images already small enough, and transparency (it uses WebP, falling back to PNG,
so logos do not get a black background).

**`.user.ini`** — raises the PHP limits on hosts running PHP as FastCGI/PHP-FPM,
which covers most shared hosting and cPanel.

**`.htaccess`** — the same limits for hosts running PHP as an Apache module. The
`php_value` lines are wrapped in `<IfModule>` guards because an unguarded
`php_value` causes an instant HTTP 500 on CGI/FastCGI hosts.

> Both files start with a dot. Some FTP clients hide dotfiles by default — make
> sure they actually reached the server.

## If the error persists

That means limit #1 is still in the way, and it can only be changed on the
server.

**Nginx** — needs a line in the server or http block, then a reload:

```nginx
client_max_body_size 64M;
```

`.htaccess` does nothing on nginx. This must be done by whoever controls the
server config.

**cPanel** — *Software → Select PHP Version → Options*, then set
`upload_max_filesize` and `post_max_size`. This overrides `.user.ini`.

**LiteSpeed** — respects `.htaccess`, but also has its own request-body cap in
the server admin console.

**What to ask your host**, if you cannot reach these yourself:

> Our site returns "413 Request Entity Too Large" when uploading images.
> Please raise the maximum request body size to 64MB
> (`client_max_body_size` on nginx, or `LimitRequestBody` on Apache),
> and confirm PHP's `upload_max_filesize` and `post_max_size` are at least
> 64M and 72M.

## Checking what the server currently allows

Upload `phpinfo.php` containing `<?php phpinfo();` and look at
`upload_max_filesize` and `post_max_size` under **Core**. **Delete the file
immediately afterwards** — it exposes server details publicly.
