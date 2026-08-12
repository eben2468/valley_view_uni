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
| `sql/gallery_page_data.sql` | **New.** The 40 main albums + 667 photo rows. |
| `sql/gallery_multi_migration.sql` | **New.** Adds the gallery-key columns; seeds SRC page copy + stats. |
| `sql/src_gallery_data.sql` | **New.** The 4 SRC albums + 85 photo rows. |
| `uploads/gallery/` | **New.** 1,548 downloaded image files (≈677 MB). |

## Deploying to the live server

1. **Upload the code**: `gallery.php`, `src_gallery.php`, the three files in
   `includes/`, `admin/manage_gallery.php`, `admin/edit_gallery_album.php`,
   `admin/sidebar.php`, and the four files in `sql/`.

2. **Import the database in this order** — the order matters:

   ```sql
   SOURCE sql/gallery_page_schema.sql;      -- 1. create the tables
   SOURCE sql/gallery_page_data.sql;        -- 2. main gallery content
   SOURCE sql/gallery_multi_migration.sql;  -- 3. add gallery keys + SRC page copy
   SOURCE sql/src_gallery_data.sql;         -- 4. SRC gallery content
   ```

   Or import the same four files in that order through phpMyAdmin.

   Steps 1 and 3 are safe to re-run. Step 2 **replaces** the main gallery's
   albums, photos and categories; step 4 **replaces** the SRC gallery's — so do
   not re-import those after you have started editing albums in the admin
   panel. Step 4 only touches `src` rows and never disturbs the main gallery.

   `gallery_multi_migration.sql` uses MariaDB's `ADD COLUMN IF NOT EXISTS`
   syntax (the same syntax `sql/slider_timing_migration.sql` already uses). If
   your host runs stock MySQL rather than MariaDB, drop the `IF NOT EXISTS` /
   `IF EXISTS` clauses and run it exactly once.

3. **Upload `uploads/gallery/`**. This is the big one (~677 MB). FTP or the
   host's file manager both work; a zip uploaded and extracted server-side is
   much faster if your host allows it. Make sure `uploads/` stays writable
   (755) so new photo uploads keep working.

4. Visit `/admin/manage_gallery.php`. The Main Photo Gallery should read 40
   albums / 667 photos, and switching to SRC Gallery should read 4 albums /
   85 photos.

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
