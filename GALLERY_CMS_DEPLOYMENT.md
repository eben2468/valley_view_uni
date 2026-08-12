# Photo Gallery CMS — what changed and how to deploy it

Both gallery pages are now fully database-driven. Every album, photo, filter,
heading and counter is editable from the admin panel; nothing below the hero on
either page is hard-coded any more.

One set of tables serves both pages. Each row carries a **gallery key** saying
which page it belongs to:

| Gallery key | Page | Content |
| --- | --- | --- |
| `main` | `gallery.php` | 40 albums, 667 photos |
| `src` | `src_gallery.php` | 4 albums, 85 photos |

Adding a third gallery page later needs a new key and a thin template — not
another copy of the code.

## Content that was imported

**Main gallery** — all **40 albums** and all **667 photos** from
<https://vvu.edu.gh/index.php/life-vvu/gallery>, including every photo behind
every album link on that page. Sorted newest-first into seven categories:
Graduation & Pinning, Ceremonies & Worship, Sports & Wellness, Alumni &
Achievements, Partnerships & Outreach, Academics, and Campus Life & Staff.

**SRC gallery** — all **4 albums** and all **85 photos** from
<https://vvu.edu.gh/index.php/life-vvu/gallery/src-gallery> (SRC
Entrepreneurship Awards, SRC Debate, SRC Float, Move Seminar), in three
categories: Awards & Competitions, Parades & Floats, Seminars & Training.

Both sets of image files were downloaded into `uploads/gallery/`, so the site
does not depend on vvu.edu.gh being reachable. All of the above is editable.

> `src_gallery.php` was already linked from the site menu ("Life @ VVU →
> Gallery → SRC Gallery") but the file did not exist, so that menu item was a
> dead link. It now resolves.

## Files

| File | What it is |
| --- | --- |
| `gallery.php` | Rewritten. Main gallery; keeps its own hero, includes the shared body. |
| `src_gallery.php` | **New.** SRC gallery; same shared body, its own hero. |
| `includes/gallery_helper.php` | **New.** Read helpers, all scoped by gallery key. |
| `includes/gallery_styles.php` | **New.** Shared gallery CSS (included before the hero). |
| `includes/gallery_body.php` | **New.** Shared page body: album index, album detail, stats, CTA, lightbox. |
| `admin/manage_gallery.php` | **New.** Albums, page copy, categories, stat tiles — with a gallery switcher. |
| `admin/edit_gallery_album.php` | **New.** One album's details and its photos. |
| `admin/sidebar.php` | Added the "Photo Gallery" menu item. |
| `sql/gallery_page_schema.sql` | **New.** Creates the five `gallery_*` tables. |
| `sql/gallery_multi_migration.sql` | **New.** Adds the gallery-key columns; seeds SRC page copy + stats. |
| `sql/gallery_page_data.sql` | **New.** The 40 main albums + 667 photo rows. |
| `sql/src_gallery_data.sql` | **New.** The 4 SRC albums + 85 photo rows. |
| `uploads/gallery/` | **New.** 1,548 downloaded image files (≈677 MB). |

### A note on long filenames

Nine source photos have filenames over 80 characters — one is 190 — which push
the full path past Windows' 260-character limit and make the file impossible to
save on a Windows machine. Both seed generators truncate any filename stem to 80
characters, and the files on disk are named to match. Linux servers have no such
limit, so this only ever mattered for local development, but the SQL and the
files agree either way.

## Deploying to the live server

1. **Upload the code**: `gallery.php`, `src_gallery.php`, the three files in
   `includes/`, `admin/manage_gallery.php`, `admin/edit_gallery_album.php`,
   `admin/sidebar.php`, and the four files in `sql/`.

2. **Import the database in this order** — the order matters:

   ```sql
   SOURCE sql/gallery_page_schema.sql;      -- 1. create the tables
   SOURCE sql/gallery_multi_migration.sql;  -- 2. add gallery keys + SRC page copy
   SOURCE sql/gallery_page_data.sql;        -- 3. main gallery content
   SOURCE sql/src_gallery_data.sql;         -- 4. SRC gallery content
   ```

   Or import the same four files in that order through phpMyAdmin.

   **All four are safe to re-run**, in any combination. Files 3 and 4 each
   clear and reload only *their own* gallery (`WHERE gallery_key = 'main'` /
   `'src'`), so re-importing one never disturbs the other. They do discard
   album edits made in the admin panel for that gallery, so treat them as
   "reset this gallery to the imported content".

   All four files are **portable across stock MySQL 5.7/8.x and MariaDB**.
   They deliberately avoid MariaDB-only syntax such as
   `ALTER TABLE ... ADD COLUMN IF NOT EXISTS` (which fails on MySQL with error
   #1064) and never `SELECT` from the table an `INSERT` is writing to (MySQL
   error #1093). The migration checks `information_schema` and runs each change
   through a prepared statement instead — no `DELIMITER` and no stored
   procedures, so it imports cleanly through phpMyAdmin.

3. **Upload `uploads/gallery/`** — 1,572 files, ~677 MB. See
   [Shipping the images](#shipping-the-images) below; there is a script for it.

4. Visit `/admin/manage_gallery.php`. The Main Photo Gallery should read 40
   albums / 667 photos, and switching to SRC Gallery should read 4 albums /
   85 photos.

## Shipping the images

`uploads/gallery/` is ~677 MB. There are two ways to get it onto the server;
**pick one**.

| | Method A — server pulls | Method B — you push |
| --- | --- | --- |
| Script | `dev-tools/fetch_gallery_uploads.sh` | `dev-tools/deploy_gallery_uploads.sh` |
| Runs on | the **live server** | your **PC** (WSL) |
| Source | vvu.edu.gh | your local `uploads/gallery/` |
| Your upload | nothing | 677 MB |
| Speed | fast (server-to-server) | limited by your home upload |
| Bytes | Cloudflare-re-encoded (see below) | exactly your local files |

**Method A is the practical choice** if you have shell access to the server —
nothing has to cross your own connection. Use Method B if the server cannot
reach vvu.edu.gh, or if you want files byte-identical to what was verified
locally.

### Method A — pull from vvu.edu.gh, on the server

Deploy the code first (the script and its manifest live in `dev-tools/`, so
this one time do **not** exclude `dev-tools` from the upload). Then:

```bash
ssh youruser@alpha.vvu.edu.gh
cd /path/to/project/dev-tools

bash fetch_gallery_uploads.sh --dry-run   # downloads nothing
bash fetch_gallery_uploads.sh             # ~1,548 files
```

It skips anything already downloaded, so if it stops just run it again. At the
end it checks every file really is an image and prints the ownership commands
to run next.

> **These bytes will not match `gallery_uploads_manifest.sha256`, and that is
> expected.** vvu.edu.gh is behind Cloudflare with Polish (image optimisation)
> switched on — its responses carry a `cf-polished` header — so Cloudflare
> re-encodes the JPEGs and two downloads of the same photo differ byte-for-byte.
> Verified: same photo, same 1170×800 resolution, file about 5% smaller. The
> images are correct; only the checksums differ. Verify this route by file
> count and the script's own image check, not with the SHA-256 manifest.

### Method B — push from your PC

**rsync is not in Git Bash — run this from WSL**, which already has it:

```bash
wsl
cd /mnt/c/xampp/htdocs/valley_view_uni

# 1. check SSH works at all
ssh youruser@alpha.vvu.edu.gh 'echo connected; ls -d /var/www/vvu'

# 2. see what would be sent — writes nothing
SSH_USER=youruser bash dev-tools/deploy_gallery_uploads.sh --dry-run

# 3. send it (30-60 min on a typical link; just re-run if it drops)
SSH_USER=youruser bash dev-tools/deploy_gallery_uploads.sh
```

Set `SSH_HOST` / `REMOTE_ROOT` too if they differ from `alpha.vvu.edu.gh` and
`/var/www/vvu`.

### Then fix ownership — this step is easy to miss

rsync writes the files as **your SSH user**. PHP runs as the web-server user
(`www-data` on nginx/Debian), and it needs to *write* into
`uploads/gallery/albums/<id>/` whenever someone adds photos through the admin
panel. Without this, the gallery displays fine but new uploads fail:

```bash
ssh youruser@alpha.vvu.edu.gh
sudo chown -R www-data:www-data /var/www/vvu/uploads
sudo find /var/www/vvu/uploads -type d -exec chmod 755 {} \;
sudo find /var/www/vvu/uploads -type f -exec chmod 644 {} \;
```

(Check the user first if you are unsure: `ps -o user= -C php-fpm8.2 | head -1`.)

### Verify

```bash
ssh youruser@alpha.vvu.edu.gh \
  "find /var/www/vvu/uploads/gallery -type f | wc -l; du -sh /var/www/vvu/uploads/gallery"
```

Expect **1572** files and about **677M**. Note the live figure can legitimately
be *higher*: `uploads/gallery/` also holds the homepage gallery's images and
anything added through the admin panel since.

**Method B only:** for a byte-exact check,
`dev-tools/gallery_uploads_manifest.sha256` lists the SHA-256 of every file.
Copy it to the project root on the server and run:

```bash
cd /var/www/vvu && sha256sum -c gallery_uploads_manifest.sha256 | grep -v ': OK$'
```

Silence means every file matches. Delete the manifest afterwards. Do **not**
run this after Method A — Cloudflare re-encodes the images in transit, so
mismatches there are expected and mean nothing is wrong.

> **The script deliberately does not use `--delete`.** The live
> `uploads/gallery/` also contains the homepage gallery's own images and
> anything uploaded through the admin panel since; `--delete` would erase every
> live file that is not in your local folder.

## Using the admin panel

**Photo Gallery** in the sidebar opens the CMS. At the top is a **Gallery page**
switcher — *Main Photo Gallery* / *SRC Gallery*. Everything below it applies
only to the gallery you have selected: each keeps its own albums, categories,
page copy and counters, and they cannot affect each other.

Below the switcher are four tabs:

- **Albums & Photos** — the album list. Create albums, reorder them with the
  order boxes plus *Save order*, publish/hide with the eye button, delete, or
  click *Edit* to open one album.
- **Page Content** — every heading, paragraph, button and label on the page.
- **Categories** — the filter pills. Deleting one keeps its albums; they just
  become uncategorised. Pills with no albums are hidden automatically.
- **Stat Counters** — the four tiles in the blue band. Set *Auto count* to
  Photos or Albums and the number keeps itself accurate; leave it on Manual to
  type your own text like `50K+`.

Inside an album (**Edit**) you can change its title, date, description,
category, cover and visibility, and manage its photos: upload many files at
once, paste paths one per line, edit captions, reorder, hide, bulk-delete, or
tick a photo and press *Set as album cover*.

> Uploading a very large batch of photos at once can exceed the server's
> `upload_max_filesize` / `post_max_size` limits. If a batch fails, add them in
> smaller groups — see `UPLOAD_LIMITS.md`.

## Note on the hero sections

The hero banner at the top of `gallery.php` was left exactly as it was, byte for
byte, because it is the shared default hero used across every page. `src_gallery.php`
uses that same hero markup with SRC wording. Both are deliberately outside the
CMS; everything below them is inside it.

Because the hero has to come between the shared CSS and the shared body, each
page is laid out the same way:

```php
include 'includes/gallery_styles.php';   // shared CSS
// ... this page's own hero ...
include 'includes/gallery_body.php';     // shared everything-else
```

## The front-end pages

- Album cards in a responsive 1 → 2 → 3 → 4 column grid, with category badge,
  photo count, date and hover reveal.
- Category filter pills and a live search box that work together.
- Clicking an album opens `<page>?album=<slug>` — the same page, showing that
  album's full photo grid.
- Clicking a photo opens a lightbox with arrow-key navigation, swipe on touch
  devices, Escape to close, neighbour preloading and a photo counter.
- Thumbnails are lazy-loaded, so a 71-photo album stays fast.
- Dark mode and `prefers-reduced-motion` are both respected.
- An unknown `?album=` slug falls back to the album list with a short notice,
  and a slug from the *other* gallery is not found — the two are fully isolated.
