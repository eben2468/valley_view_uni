<?php
/**
 * Shared gallery page body — everything after the hero.
 *
 * Renders either the album index or one album's photo grid, plus the closing
 * band (copy, buttons, counters) and the lightbox. gallery.php,
 * src_gallery.php and graduation_gallery.php include this after emitting
 * their own hero, so the pages stay in lockstep while keeping their own hero.
 *
 * Styled on css/vvu-modern.css (the Mission & Vision design) plus the g-
 * classes in includes/gallery_styles.php.
 *
 * Expects these to be set by the including page:
 *   $vvu_gallery_key    string  which gallery ('main' | 'src' | 'graduation')
 *   $g_content          array   page copy
 *   $g_categories       array   filter pills
 *   $g_stats            array   stat tiles
 *   $active_album       array|null
 *   $album_images       array
 *   $g_albums           array
 *   $album_slug         string  the raw ?album= value
 *   $vvu_gallery_self   string  this gallery's own page, for links
 *   $vvu_gallery_kicker string  small gold label above headings (optional)
 */
if (!isset($vvu_gallery_self)) {
    $vvu_gallery_self = 'gallery.php';
}
if (!isset($vvu_gallery_kicker)) {
    $vvu_gallery_kicker = 'Photo Gallery';
}

/** Resized copy for a card/tile when the server can make one, else the original. */
$g_thumb = function ($path, $w, $h) {
    $path = vvu_gallery_src($path);
    return ($path !== '' && function_exists('vvu_thumb')) ? vvu_thumb($path, $w, $h) : $path;
};

/** "Google Photos" when the link points there, so the button says where it goes. */
$g_host_label = function ($url) {
    return preg_match('#^https?://(photos\.google\.com|photos\.app\.goo\.gl|goo\.gl/photos)#i', $url)
        ? 'Google Photos' : 'the full album';
};
?>


<?php if ($active_album):
    $g_ext = vvu_gallery_external_url($active_album);
?>
    <!-- ============================ ALBUM DETAIL VIEW ============================ -->
    <section class="vm-section vm-section--white g-detail">
        <div class="container">
            <div class="vm-wrap">
                <a href="<?php echo vvu_e($vvu_gallery_self); ?>" class="g-back">
                    <span class="material-symbols-outlined" aria-hidden="true">arrow_back</span>
                    <?php echo vvu_e(vvu_gallery_text($g_content, 'detail_back_label', 'Back to all albums')); ?>
                </a>

                <div class="g-detail__grid<?php echo $g_ext ? '' : ' g-detail__grid--solo'; ?>">
                    <div>
                        <div class="vm-kicker">
                            <span class="material-symbols-outlined" aria-hidden="true"><?php echo vvu_e($active_album['category_icon'] ?: 'photo_library'); ?></span>
                            <?php echo vvu_e(!empty($active_album['category_name']) ? $active_album['category_name'] : $vvu_gallery_kicker); ?>
                        </div>
                        <div class="vm-heading vm-heading--left g-detail__title" role="heading" aria-level="1">
                            <?php echo vvu_e($active_album['title']); ?>
                        </div>

                        <ul class="g-meta">
                            <?php if ($d = vvu_gallery_date($active_album['event_date'])): ?>
                                <li>
                                    <span class="material-symbols-outlined" aria-hidden="true">calendar_month</span>
                                    <span class="g-meta__text"><?php echo vvu_e($d); ?></span>
                                </li>
                            <?php endif; ?>
                            <li>
                                <span class="material-symbols-outlined" aria-hidden="true">photo_library</span>
                                <span class="g-meta__text">
                                    <?php echo count($album_images); ?>
                                    <?php echo vvu_e(vvu_gallery_text($g_content, 'detail_photos_label', 'photos in this album')); ?>
                                </span>
                            </li>
                        </ul>

                        <?php if (trim((string) $active_album['description']) !== ''): ?>
                            <p class="vm-lead g-detail__lead"><?php echo nl2br(vvu_e($active_album['description'])); ?></p>
                        <?php endif; ?>
                    </div>

                    <?php if ($g_ext): ?>
                        <aside class="g-full">
                            <span class="g-full__icon" aria-hidden="true">
                                <span class="material-symbols-outlined">collections</span>
                            </span>
                            <div class="g-full__title" role="heading" aria-level="2">See every photo</div>
                            <p class="g-full__text">
                                This page shows a selection of highlights. The complete album, with every
                                photo in full resolution, is available on <?php echo vvu_e($g_host_label($g_ext)); ?>.
                            </p>
                            <a href="<?php echo vvu_e($g_ext); ?>" target="_blank" rel="noopener noreferrer" class="vm-btn vm-btn--gold">
                                <span class="material-symbols-outlined" aria-hidden="true">open_in_new</span>
                                View all photos
                            </a>
                        </aside>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </section>

    <section class="vm-section vm-section--tint g-photos-section">
        <div class="container">
            <div class="vm-wrap">
                <?php if (!$album_images): ?>
                    <div class="vm-empty">
                        <span class="material-symbols-outlined" aria-hidden="true"><?php echo $g_ext ? 'collections' : 'image_not_supported'; ?></span>
                        <div class="vm-empty-title">
                            <?php echo $g_ext ? 'Highlights coming soon' : 'No photos have been added to this album yet.'; ?>
                        </div>
                        <?php if ($g_ext): ?>
                            <p class="vm-empty-text">In the meantime, every photo from this album is available online.</p>
                            <div class="vm-actions">
                                <a href="<?php echo vvu_e($g_ext); ?>" target="_blank" rel="noopener noreferrer" class="vm-btn vm-btn--navy">
                                    <span class="material-symbols-outlined" aria-hidden="true">open_in_new</span>
                                    View all photos
                                </a>
                            </div>
                        <?php endif; ?>
                    </div>
                <?php else: ?>
                    <div class="g-photos" id="galleryPhotoGrid">
                        <?php foreach ($album_images as $i => $img):
                            $full  = vvu_gallery_src($img['image_path']);
                            $thumb = trim((string) $img['thumb_path']) !== '' ? vvu_gallery_src($img['thumb_path']) : $g_thumb($full, 480, 480);
                            $cap   = trim((string) $img['title']);
                            $alt   = $cap !== '' ? $cap : $active_album['title'] . ' — photo ' . ($i + 1);
                        ?>
                            <button type="button" class="g-photo"
                                    data-full="<?php echo vvu_e($full); ?>"
                                    data-caption="<?php echo vvu_e($cap); ?>"
                                    data-index="<?php echo $i; ?>"
                                    aria-label="Open photo <?php echo $i + 1; ?> of <?php echo count($album_images); ?>">
                                <img src="<?php echo vvu_e($thumb); ?>"
                                     alt="<?php echo vvu_e($alt); ?>"
                                     loading="lazy" decoding="async">
                                <span class="g-photo__zoom" aria-hidden="true">
                                    <span class="material-symbols-outlined">zoom_in</span>
                                </span>
                            </button>
                        <?php endforeach; ?>
                    </div>

                    <?php if ($g_ext): ?>
                        <div class="g-more">
                            <p class="g-more__text">Want to see more? The full album has every photo from this event.</p>
                            <a href="<?php echo vvu_e($g_ext); ?>" target="_blank" rel="noopener noreferrer" class="vm-btn vm-btn--navy">
                                <span class="material-symbols-outlined" aria-hidden="true">open_in_new</span>
                                View all photos
                            </a>
                        </div>
                    <?php endif; ?>
                <?php endif; ?>
            </div>
        </div>
    </section>

<?php else: ?>
    <!-- ============================ ALBUM INDEX VIEW ============================ -->
    <?php if ($album_slug !== ''): ?>
        <!-- An unknown/unpublished ?album= slug falls back to the full index
             rather than a dead end, but says so instead of silently ignoring it. -->
        <div class="container">
            <div class="g-notice">
                <span class="material-symbols-outlined" aria-hidden="true">info</span>
                <span class="g-notice__text">That album is no longer available. Browse all of our albums below.</span>
            </div>
        </div>
    <?php endif; ?>

    <!-- Introduction -->
    <section class="vm-section vm-section--white g-intro">
        <div class="container">
            <div class="vm-head">
                <div class="vm-kicker">
                    <span class="material-symbols-outlined" aria-hidden="true">photo_camera</span>
                    <?php echo vvu_e($vvu_gallery_kicker); ?>
                </div>
                <div class="vm-heading" role="heading" aria-level="2">
                    <?php echo vvu_e(vvu_gallery_text($g_content, 'intro_heading', 'Experience VVU Through Our Lens')); ?>
                </div>
                <?php if ($intro = vvu_gallery_text($g_content, 'intro_text')): ?>
                    <p class="vm-lead"><?php echo nl2br(vvu_e($intro)); ?></p>
                <?php endif; ?>
            </div>
        </div>
    </section>

    <!-- Albums: search, filters, grid -->
    <section class="vm-section vm-section--tint g-albums-section">
        <div class="container">
            <div class="vm-wrap">
                <div class="vm-head">
                    <div class="vm-kicker">
                        <span class="material-symbols-outlined" aria-hidden="true">collections</span>
                        Albums
                    </div>
                    <div class="vm-heading" role="heading" aria-level="2">
                        <?php echo vvu_e(vvu_gallery_text($g_content, 'albums_heading', 'Browse Our Albums')); ?>
                    </div>
                    <?php if ($sub = vvu_gallery_text($g_content, 'albums_subheading')): ?>
                        <p class="vm-lead"><?php echo nl2br(vvu_e($sub)); ?></p>
                    <?php endif; ?>
                </div>

                <?php if ($g_albums): ?>
                    <div class="vm-search g-search">
                        <span class="material-symbols-outlined" aria-hidden="true">search</span>
                        <label for="gallerySearch" class="sr-only">Search albums</label>
                        <input type="search" id="gallerySearch" autocomplete="off"
                               placeholder="<?php echo vvu_e(vvu_gallery_text($g_content, 'search_placeholder', 'Search albums…')); ?>">
                    </div>

                    <?php if ($g_categories): ?>
                        <div class="g-pills" id="galleryFilters" role="group" aria-label="Filter albums by category">
                            <button type="button" class="g-pill" data-filter="all" aria-pressed="true">
                                <span class="material-symbols-outlined" aria-hidden="true">grid_view</span>
                                <?php echo vvu_e(vvu_gallery_text($g_content, 'all_filter_label', 'All Albums')); ?>
                                <span class="g-pill__n"><?php echo count($g_albums); ?></span>
                            </button>
                            <?php foreach ($g_categories as $cat): ?>
                                <button type="button" class="g-pill" data-filter="<?php echo vvu_e($cat['slug']); ?>" aria-pressed="false">
                                    <span class="material-symbols-outlined" aria-hidden="true"><?php echo vvu_e($cat['icon'] ?: 'photo_library'); ?></span>
                                    <?php echo vvu_e($cat['name']); ?>
                                    <span class="g-pill__n"><?php echo (int) $cat['album_count']; ?></span>
                                </button>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                <?php endif; ?>

                <?php if (!$g_albums): ?>
                    <div class="vm-empty">
                        <span class="material-symbols-outlined" aria-hidden="true">photo_library</span>
                        <div class="vm-empty-title">Albums are on their way</div>
                        <p class="vm-empty-text">No albums have been published yet. Please check back soon.</p>
                    </div>
                <?php else: ?>
                    <div class="g-albums" id="galleryAlbumGrid">
                        <?php foreach ($g_albums as $album):
                            $cover = $g_thumb($album['cover_image'], 720, 450);
                            $count = (int) $album['photo_count'];
                            $ext   = vvu_gallery_external_url($album);
                            $href  = $vvu_gallery_self . '?album=' . urlencode($album['slug']);
                            $desc  = trim((string) $album['description']);
                        ?>
                            <article class="g-card"
                                     data-category="<?php echo vvu_e($album['category_slug'] ?? ''); ?>"
                                     data-title="<?php echo vvu_e(mb_strtolower($album['title'] . ' ' . ($album['category_name'] ?? ''))); ?>">
                                <a href="<?php echo vvu_e($href); ?>" class="g-card__media" tabindex="-1" aria-hidden="true">
                                    <?php if ($cover !== ''): ?>
                                        <img src="<?php echo vvu_e($cover); ?>" alt="" loading="lazy" decoding="async">
                                    <?php else: ?>
                                        <span class="g-card__placeholder">
                                            <span class="material-symbols-outlined">photo_library</span>
                                        </span>
                                    <?php endif; ?>
                                    <?php if (!empty($album['category_name'])): ?>
                                        <span class="g-card__chip"><?php echo vvu_e($album['category_name']); ?></span>
                                    <?php endif; ?>
                                    <?php if ($count > 0): ?>
                                    <span class="g-card__count">
                                        <span class="material-symbols-outlined">photo_camera</span>
                                        <span class="g-card__count-n"><?php echo $count; ?></span>
                                    </span>
                                    <?php endif; ?>
                                    <span class="g-card__hover">
                                        <span class="material-symbols-outlined">visibility</span>
                                    </span>
                                </a>

                                <div class="g-card__body">
                                    <?php if ($d = vvu_gallery_date($album['event_date'])): ?>
                                        <span class="g-card__date">
                                            <span class="material-symbols-outlined" aria-hidden="true">calendar_month</span>
                                            <?php echo vvu_e($d); ?>
                                        </span>
                                    <?php endif; ?>
                                    <div class="g-card__title" role="heading" aria-level="3">
                                        <a href="<?php echo vvu_e($href); ?>"><?php echo vvu_e($album['title']); ?></a>
                                    </div>
                                    <?php if ($desc !== ''): ?>
                                        <p class="g-card__text"><?php echo vvu_e($desc); ?></p>
                                    <?php endif; ?>

                                    <div class="g-card__foot">
                                        <a href="<?php echo vvu_e($href); ?>" class="g-card__link">
                                            View album
                                            <span class="material-symbols-outlined" aria-hidden="true">arrow_forward</span>
                                        </a>
                                        <?php if ($ext): ?>
                                            <a href="<?php echo vvu_e($ext); ?>" target="_blank" rel="noopener noreferrer" class="g-card__all"
                                               aria-label="View all photos of <?php echo vvu_e($album['title']); ?> (opens in a new tab)">
                                                <span class="material-symbols-outlined" aria-hidden="true">open_in_new</span>
                                                All photos
                                            </a>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </article>
                        <?php endforeach; ?>
                    </div>

                    <div id="galleryEmpty" class="vm-empty hidden">
                        <span class="material-symbols-outlined" aria-hidden="true">search_off</span>
                        <div class="vm-empty-title">
                            <?php echo vvu_e(vvu_gallery_text($g_content, 'empty_state_text', 'No albums match your search yet.')); ?>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </section>
<?php endif; ?>

    <!-- Closing band: copy, buttons and counters -->
    <?php
        $g_cta_1 = vvu_gallery_text($g_content, 'cta_1_text');
        $g_cta_2 = vvu_gallery_text($g_content, 'cta_2_text');
        $g_lead  = vvu_gallery_text($g_content, 'cta_text', vvu_gallery_text($g_content, 'stats_text'));
    ?>
    <section class="vm-cta g-cta">
        <div class="container">
            <div class="vm-cta-head">
                <div class="vm-kicker">
                    <span class="material-symbols-outlined" aria-hidden="true">auto_awesome</span>
                    <?php echo vvu_e(vvu_gallery_text($g_content, 'stats_heading', 'Gallery Highlights')); ?>
                </div>
                <div class="vm-cta-heading" role="heading" aria-level="2">
                    <?php echo vvu_e(vvu_gallery_text($g_content, 'cta_heading', 'Want to See More?')); ?>
                </div>
                <?php if ($g_lead !== ''): ?>
                    <p class="vm-cta-lead"><?php echo nl2br(vvu_e($g_lead)); ?></p>
                <?php endif; ?>

                <?php if ($g_cta_1 !== '' || $g_cta_2 !== ''): ?>
                    <div class="vm-actions">
                        <?php if ($g_cta_1 !== ''): ?>
                            <a href="<?php echo vvu_e(vvu_url(vvu_gallery_text($g_content, 'cta_1_link', '#'))); ?>" class="vm-btn vm-btn--gold">
                                <span class="material-symbols-outlined" aria-hidden="true"><?php echo vvu_e(vvu_gallery_text($g_content, 'cta_1_icon', 'event')); ?></span>
                                <?php echo vvu_e($g_cta_1); ?>
                            </a>
                        <?php endif; ?>
                        <?php if ($g_cta_2 !== ''): ?>
                            <a href="<?php echo vvu_e(vvu_url(vvu_gallery_text($g_content, 'cta_2_link', '#'))); ?>" class="vm-btn vm-btn--ghost">
                                <span class="material-symbols-outlined" aria-hidden="true"><?php echo vvu_e(vvu_gallery_text($g_content, 'cta_2_icon', 'newspaper')); ?></span>
                                <?php echo vvu_e($g_cta_2); ?>
                            </a>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>
            </div>

            <?php if ($g_stats): ?>
                <div class="vm-stats" style="--vm-cols: <?php echo min(4, count($g_stats)); ?>">
                    <?php foreach ($g_stats as $stat): ?>
                        <div class="vm-stat">
                            <span class="vm-stat-value"><?php echo vvu_e($stat['value_text']); ?></span>
                            <span class="vm-stat-label"><?php echo vvu_e($stat['label']); ?></span>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
    </section>
</main>

<!-- Lightbox -->
<div class="g-lb" id="galleryLightbox" role="dialog" aria-modal="true" aria-label="Photo viewer">
    <div class="g-lb__bar">
        <span class="text-sm sm:text-base font-bold tracking-wide" id="galleryLbCounter">1 / 1</span>
        <button type="button" class="g-lb__close" id="galleryLbClose" aria-label="Close photo viewer">
            <span class="material-symbols-outlined text-white">close</span>
        </button>
    </div>
    <div class="g-lb__stage">
        <button type="button" class="g-lb__nav g-lb__nav--prev" id="galleryLbPrev" aria-label="Previous photo">
            <span class="material-symbols-outlined text-white">chevron_left</span>
        </button>
        <span class="g-lb__spin" aria-hidden="true"></span>
        <img class="g-lb__img" id="galleryLbImg" src="" alt="">
        <button type="button" class="g-lb__nav g-lb__nav--next" id="galleryLbNext" aria-label="Next photo">
            <span class="material-symbols-outlined text-white">chevron_right</span>
        </button>
    </div>
    <p class="g-lb__cap" id="galleryLbCaption"></p>
</div>

<script>
(function () {
    'use strict';

    /* ---------------------------------------------------------------
       Album index: category pills + live search.
       Both filters are applied together, so picking "Sports" and then
       typing still narrows within Sports rather than resetting it.
    --------------------------------------------------------------- */
    var grid = document.getElementById('galleryAlbumGrid');
    if (grid) {
        var cards   = Array.prototype.slice.call(grid.querySelectorAll('.g-card'));
        var pills   = Array.prototype.slice.call(document.querySelectorAll('#galleryFilters .g-pill'));
        var search  = document.getElementById('gallerySearch');
        var empty   = document.getElementById('galleryEmpty');
        var current = 'all';

        function apply() {
            var q = (search && search.value ? search.value : '').trim().toLowerCase();
            var shown = 0;
            cards.forEach(function (card) {
                var okCat  = current === 'all' || card.getAttribute('data-category') === current;
                var okText = q === '' || (card.getAttribute('data-title') || '').indexOf(q) !== -1;
                var show   = okCat && okText;
                card.style.display = show ? '' : 'none';
                if (show) shown++;
            });
            if (empty) empty.classList.toggle('hidden', shown !== 0);
        }

        pills.forEach(function (pill) {
            pill.addEventListener('click', function () {
                current = pill.getAttribute('data-filter');
                pills.forEach(function (p) { p.setAttribute('aria-pressed', String(p === pill)); });
                apply();
            });
        });

        if (search) {
            search.addEventListener('input', apply);
        }
    }

    /* ---------------------------------------------------------------
       Album detail: lightbox with keyboard, swipe and preloading.
    --------------------------------------------------------------- */
    var photoGrid = document.getElementById('galleryPhotoGrid');
    var lb        = document.getElementById('galleryLightbox');
    if (!photoGrid || !lb) return;

    var photos    = Array.prototype.slice.call(photoGrid.querySelectorAll('.g-photo'));
    var lbImg     = document.getElementById('galleryLbImg');
    var lbCap     = document.getElementById('galleryLbCaption');
    var lbCount   = document.getElementById('galleryLbCounter');
    var btnPrev   = document.getElementById('galleryLbPrev');
    var btnNext   = document.getElementById('galleryLbNext');
    var btnClose  = document.getElementById('galleryLbClose');
    var index     = 0;
    var lastFocus = null;

    if (photos.length < 2) {
        btnPrev.style.display = 'none';
        btnNext.style.display = 'none';
    }

    function preload(i) {
        if (i < 0 || i >= photos.length) return;
        var img = new Image();
        img.src = photos[i].getAttribute('data-full');
    }

    function show(i) {
        // Wrap around so the last photo's "next" returns to the first.
        index = (i + photos.length) % photos.length;
        var el  = photos[index];
        var cap = el.getAttribute('data-caption') || '';

        lb.classList.add('is-loading');
        lbImg.style.visibility = 'hidden';
        lbImg.src = el.getAttribute('data-full');
        lbImg.alt = cap || ('Photo ' + (index + 1) + ' of ' + photos.length);

        lbCap.textContent = cap;
        lbCount.textContent = (index + 1) + ' / ' + photos.length;

        preload(index + 1);
        preload(index - 1);
    }

    lbImg.addEventListener('load', function () {
        lb.classList.remove('is-loading');
        lbImg.style.visibility = 'visible';
    });
    lbImg.addEventListener('error', function () {
        lb.classList.remove('is-loading');
        lbImg.style.visibility = 'visible';
    });

    function open(i) {
        lastFocus = document.activeElement;
        show(i);
        lb.classList.add('is-open');
        document.body.classList.add('g-lb-open');
        btnClose.focus();
    }

    function close() {
        lb.classList.remove('is-open', 'is-loading');
        document.body.classList.remove('g-lb-open');
        lbImg.src = '';
        if (lastFocus && lastFocus.focus) lastFocus.focus();
    }

    photos.forEach(function (el, i) {
        el.addEventListener('click', function () { open(i); });
    });

    btnPrev.addEventListener('click', function () { show(index - 1); });
    btnNext.addEventListener('click', function () { show(index + 1); });
    btnClose.addEventListener('click', close);

    // Click the backdrop (but not the image or a control) to dismiss.
    lb.addEventListener('click', function (e) {
        if (e.target === lb || e.target.classList.contains('g-lb__stage')) close();
    });

    document.addEventListener('keydown', function (e) {
        if (!lb.classList.contains('is-open')) return;
        if (e.key === 'Escape')     { close(); }
        else if (e.key === 'ArrowLeft')  { show(index - 1); }
        else if (e.key === 'ArrowRight') { show(index + 1); }
        else if (e.key === 'Tab') {
            // Keep focus inside the dialog while it is open.
            var focusables = [btnClose, btnPrev, btnNext].filter(function (b) {
                return b && b.style.display !== 'none';
            });
            var first = focusables[0], last = focusables[focusables.length - 1];
            if (e.shiftKey && document.activeElement === first) { e.preventDefault(); last.focus(); }
            else if (!e.shiftKey && document.activeElement === last) { e.preventDefault(); first.focus(); }
        }
    });

    // Horizontal swipe on touch devices.
    var startX = 0, startY = 0, tracking = false;
    lb.addEventListener('touchstart', function (e) {
        if (e.touches.length !== 1) { tracking = false; return; }
        startX = e.touches[0].clientX;
        startY = e.touches[0].clientY;
        tracking = true;
    }, { passive: true });

    lb.addEventListener('touchend', function (e) {
        if (!tracking) return;
        tracking = false;
        var dx = e.changedTouches[0].clientX - startX;
        var dy = e.changedTouches[0].clientY - startY;
        // Ignore mostly-vertical drags so scrolling gestures do not flip photos.
        if (Math.abs(dx) > 50 && Math.abs(dx) > Math.abs(dy)) {
            show(dx < 0 ? index + 1 : index - 1);
        }
    }, { passive: true });
})();
</script>
