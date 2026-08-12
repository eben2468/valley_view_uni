<?php
/**
 * Shared gallery page body — everything after the hero.
 *
 * Renders either the album index or one album's photo grid, plus the stats
 * band, the closing CTA and the lightbox. Both gallery.php and
 * src_gallery.php include this after emitting their own hero, so the two
 * pages stay in lockstep while keeping their own hero markup.
 *
 * Expects these to be set by the including page:
 *   $vvu_gallery_key  string  which gallery ('main' | 'src')
 *   $g_content        array   page copy
 *   $g_categories     array   filter pills
 *   $g_stats          array   stat tiles
 *   $active_album     array|null
 *   $album_images     array
 *   $g_albums         array
 *   $album_slug       string  the raw ?album= value
 *   $vvu_gallery_self string  this gallery's own page, for links
 */
if (!isset($vvu_gallery_self)) {
    $vvu_gallery_self = 'gallery.php';
}
?>


<?php if ($active_album): ?>
    <!-- ============================ ALBUM DETAIL VIEW ============================ -->
    <section class="py-10 sm:py-14 bg-white dark:bg-gray-900">
        <div class="container">
            <a href="<?php echo vvu_e($vvu_gallery_self); ?>"
               class="inline-flex items-center gap-2 px-5 py-3 rounded-full bg-gray-100 dark:bg-gray-800 text-gray-800 dark:text-gray-100 font-bold hover:bg-blue-600 hover:text-white transition-all">
                <span class="material-symbols-outlined">arrow_back</span>
                <?php echo vvu_e(vvu_gallery_text($g_content, 'detail_back_label', 'Back to all albums')); ?>
            </a>

            <div class="mt-8 max-w-5xl">
                <?php if (!empty($active_album['category_name'])): ?>
                    <span class="inline-flex items-center gap-2 px-4 py-2 rounded-full bg-blue-600 text-white text-sm font-black uppercase tracking-wider">
                        <span class="material-symbols-outlined text-white text-lg"><?php echo vvu_e($active_album['category_icon'] ?: 'photo_library'); ?></span>
                        <?php echo vvu_e($active_album['category_name']); ?>
                    </span>
                <?php endif; ?>

                <h1 class="mt-5 text-3xl sm:text-4xl md:text-5xl lg:text-6xl font-black text-gray-900 dark:text-white leading-tight tracking-tight">
                    <?php echo vvu_e($active_album['title']); ?>
                </h1>

                <div class="mt-5 flex flex-wrap items-center gap-x-7 gap-y-3 text-gray-600 dark:text-gray-400 text-base sm:text-lg font-semibold">
                    <?php if ($d = vvu_gallery_date($active_album['event_date'])): ?>
                        <span class="inline-flex items-center gap-2">
                            <span class="material-symbols-outlined text-blue-600 dark:text-blue-400">calendar_month</span>
                            <?php echo vvu_e($d); ?>
                        </span>
                    <?php endif; ?>
                    <span class="inline-flex items-center gap-2">
                        <span class="material-symbols-outlined text-blue-600 dark:text-blue-400">photo_library</span>
                        <?php echo count($album_images); ?>
                        <?php echo vvu_e(vvu_gallery_text($g_content, 'detail_photos_label', 'photos in this album')); ?>
                    </span>
                </div>

                <?php if (trim((string) $active_album['description']) !== ''): ?>
                    <p class="mt-6 text-lg sm:text-xl text-gray-600 dark:text-gray-400 leading-relaxed">
                        <?php echo nl2br(vvu_e($active_album['description'])); ?>
                    </p>
                <?php endif; ?>

                <div class="h-1.5 w-28 bg-yellow-400 rounded-full mt-8"></div>
            </div>
        </div>
    </section>

    <section class="pb-20 sm:pb-28 bg-white dark:bg-gray-900">
        <div class="container">
            <?php if (!$album_images): ?>
                <div class="text-center py-20 rounded-3xl bg-gray-50 dark:bg-gray-800">
                    <span class="material-symbols-outlined text-6xl text-gray-400">image_not_supported</span>
                    <p class="mt-4 text-xl font-bold text-gray-600 dark:text-gray-300">No photos have been added to this album yet.</p>
                </div>
            <?php else: ?>
                <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 xl:grid-cols-5 gap-3 sm:gap-4"
                     id="galleryPhotoGrid">
                    <?php foreach ($album_images as $i => $img):
                        $full  = vvu_gallery_src($img['image_path']);
                        $thumb = vvu_gallery_src($img['thumb_path'], $full);
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
                                <span class="material-symbols-outlined text-4xl text-white">zoom_in</span>
                            </span>
                        </button>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
    </section>

<?php else: ?>
    <!-- ============================ ALBUM INDEX VIEW ============================ -->
    <?php if ($album_slug !== ''): ?>
        <!-- An unknown/unpublished ?album= slug falls back to the full index
             rather than a dead end, but says so instead of silently ignoring it. -->
        <section class="pt-10 bg-white dark:bg-gray-900">
            <div class="container">
                <div class="max-w-3xl mx-auto flex items-start gap-3 p-5 rounded-2xl bg-yellow-50 dark:bg-yellow-900/20 border-2 border-yellow-300 dark:border-yellow-700">
                    <span class="material-symbols-outlined text-yellow-600 dark:text-yellow-400">info</span>
                    <p class="text-base sm:text-lg font-semibold text-gray-800 dark:text-gray-100">
                        That album is no longer available. Browse all of our albums below.
                    </p>
                </div>
            </div>
        </section>
    <?php endif; ?>

    <!-- Introduction Section -->
    <section class="py-16 sm:py-20 lg:py-24 bg-white dark:bg-gray-900">
        <div class="container">
            <div class="max-w-5xl mx-auto text-center">
                <h2 class="text-3xl sm:text-4xl md:text-5xl lg:text-6xl font-black text-gray-900 dark:text-white mb-6 tracking-tight">
                    <?php echo vvu_e(vvu_gallery_text($g_content, 'intro_heading', 'Experience VVU Through Our Lens')); ?>
                </h2>
                <div class="h-2 w-40 bg-blue-600 mx-auto rounded-full mb-8"></div>
                <p class="text-lg sm:text-xl md:text-2xl text-gray-600 dark:text-gray-400 font-medium leading-relaxed">
                    <?php echo nl2br(vvu_e(vvu_gallery_text($g_content, 'intro_text'))); ?>
                </p>
            </div>
        </div>
    </section>

    <!-- Filters + Search -->
    <section class="py-8 sm:py-10 bg-gray-50 dark:bg-gray-950 border-y border-gray-200 dark:border-gray-800">
        <div class="container">
            <div class="max-w-4xl mx-auto text-center mb-8">
                <h2 class="text-2xl sm:text-3xl md:text-4xl font-black text-gray-900 dark:text-white">
                    <?php echo vvu_e(vvu_gallery_text($g_content, 'albums_heading', 'Browse Our Albums')); ?>
                </h2>
                <?php if ($sub = vvu_gallery_text($g_content, 'albums_subheading')): ?>
                    <p class="mt-3 text-base sm:text-lg text-gray-600 dark:text-gray-400 font-medium">
                        <?php echo nl2br(vvu_e($sub)); ?>
                    </p>
                <?php endif; ?>
            </div>

            <div class="max-w-xl mx-auto mb-8">
                <label for="gallerySearch" class="sr-only">Search albums</label>
                <div class="relative">
                    <span class="material-symbols-outlined absolute left-4 top-1/2 -translate-y-1/2 text-gray-400" aria-hidden="true">search</span>
                    <input type="search" id="gallerySearch"
                           placeholder="<?php echo vvu_e(vvu_gallery_text($g_content, 'search_placeholder', 'Search albums…')); ?>"
                           class="w-full pl-12 pr-4 py-4 rounded-full border-2 border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-900 text-gray-900 dark:text-white text-lg font-medium focus:outline-none focus:border-blue-600 transition-colors">
                </div>
            </div>

            <div class="flex flex-wrap justify-center gap-3" id="galleryFilters" role="group" aria-label="Filter albums by category">
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
        </div>
    </section>

    <!-- Album Grid -->
    <section class="py-14 sm:py-20 bg-gray-50 dark:bg-gray-950">
        <div class="container">
            <?php if (!$g_albums): ?>
                <div class="text-center py-20 rounded-3xl bg-white dark:bg-gray-900">
                    <span class="material-symbols-outlined text-6xl text-gray-400">photo_library</span>
                    <p class="mt-4 text-xl font-bold text-gray-600 dark:text-gray-300">
                        No albums have been published yet.
                    </p>
                </div>
            <?php else: ?>
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-5 sm:gap-6" id="galleryAlbumGrid">
                    <?php foreach ($g_albums as $album):
                        $cover = vvu_gallery_src($album['cover_image']);
                        $count = (int) $album['photo_count'];
                    ?>
                        <article class="g-card"
                                 data-category="<?php echo vvu_e($album['category_slug'] ?? ''); ?>"
                                 data-title="<?php echo vvu_e(mb_strtolower($album['title'])); ?>">
                            <a href="<?php echo vvu_e($vvu_gallery_self); ?>?album=<?php echo urlencode($album['slug']); ?>" class="block">
                                <div class="g-card__media">
                                    <?php if ($cover !== ''): ?>
                                        <img src="<?php echo vvu_e($cover); ?>"
                                             alt="<?php echo vvu_e($album['title']); ?>"
                                             loading="lazy" decoding="async">
                                    <?php else: ?>
                                        <div class="w-full h-full flex items-center justify-center bg-blue-900">
                                            <span class="material-symbols-outlined text-6xl text-white/40">photo_library</span>
                                        </div>
                                    <?php endif; ?>
                                    <span class="g-card__scrim" aria-hidden="true"></span>

                                    <?php if (!empty($album['category_name'])): ?>
                                        <span class="g-card__chip">
                                            <span class="material-symbols-outlined text-base" aria-hidden="true"><?php echo vvu_e($album['category_icon'] ?: 'photo_library'); ?></span>
                                            <?php echo vvu_e($album['category_name']); ?>
                                        </span>
                                    <?php endif; ?>

                                    <span class="g-card__count">
                                        <span class="material-symbols-outlined text-base" aria-hidden="true">photo_camera</span>
                                        <?php echo $count; ?>
                                    </span>

                                    <div class="g-card__body">
                                        <?php if ($d = vvu_gallery_date($album['event_date'])): ?>
                                            <div class="flex items-center gap-2 mb-2">
                                                <span class="material-symbols-outlined text-yellow-400 text-lg" aria-hidden="true">calendar_month</span>
                                                <span class="text-yellow-400 font-bold text-sm"><?php echo vvu_e($d); ?></span>
                                            </div>
                                        <?php endif; ?>
                                        <h3 class="g-card__title text-lg sm:text-xl font-black text-white">
                                            <?php echo vvu_e($album['title']); ?>
                                        </h3>
                                        <span class="g-card__cta text-sm">
                                            View album
                                            <span class="material-symbols-outlined text-base" aria-hidden="true">arrow_forward</span>
                                        </span>
                                    </div>
                                </div>
                            </a>
                        </article>
                    <?php endforeach; ?>
                </div>

                <p id="galleryEmpty" class="hidden text-center py-20 text-xl font-bold text-gray-600 dark:text-gray-300">
                    <?php echo vvu_e(vvu_gallery_text($g_content, 'empty_state_text', 'No albums match your search yet.')); ?>
                </p>
            <?php endif; ?>
        </div>
    </section>
<?php endif; ?>

    <?php if ($g_stats): ?>
    <!-- Stats Section -->
    <section class="py-16 sm:py-24 bg-blue-900 overflow-hidden relative">
        <div class="absolute top-0 right-0 w-[600px] h-[600px] bg-yellow-500/10 rounded-full blur-[150px] -mr-72 -mt-72"></div>
        <div class="absolute bottom-0 left-0 w-[600px] h-[600px] bg-blue-500/10 rounded-full blur-[150px] -ml-72 -mb-72"></div>

        <div class="container relative z-10">
            <div class="max-w-4xl mx-auto text-center mb-12 sm:mb-16">
                <h2 class="text-3xl sm:text-4xl md:text-5xl font-black text-white mb-5">
                    <?php echo vvu_e(vvu_gallery_text($g_content, 'stats_heading', 'Gallery Highlights')); ?>
                </h2>
                <p class="text-lg sm:text-xl md:text-2xl text-blue-100 font-medium leading-relaxed">
                    <?php echo nl2br(vvu_e(vvu_gallery_text($g_content, 'stats_text'))); ?>
                </p>
            </div>

            <div class="grid grid-cols-2 lg:grid-cols-4 gap-6 sm:gap-10">
                <?php foreach ($g_stats as $stat): ?>
                    <div class="text-center">
                        <div class="w-16 h-16 sm:w-24 sm:h-24 rounded-3xl bg-yellow-400 flex items-center justify-center mx-auto mb-5 shadow-2xl">
                            <span class="material-symbols-outlined text-3xl sm:text-5xl text-blue-900"><?php echo vvu_e($stat['icon'] ?: 'photo_library'); ?></span>
                        </div>
                        <div class="text-3xl sm:text-5xl md:text-6xl font-black text-yellow-400 mb-2"><?php echo vvu_e($stat['value_text']); ?></div>
                        <div class="text-blue-100 uppercase tracking-widest text-sm sm:text-lg font-black"><?php echo vvu_e($stat['label']); ?></div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>
    <?php endif; ?>

    <!-- CTA Section -->
    <section class="relative py-16 sm:py-24 overflow-hidden bg-white dark:bg-gray-900">
        <div class="container relative z-10">
            <div class="max-w-5xl mx-auto text-center">
                <h2 class="text-3xl sm:text-4xl md:text-5xl lg:text-6xl font-black text-gray-900 dark:text-white mb-6 leading-tight tracking-tight">
                    <?php echo vvu_e(vvu_gallery_text($g_content, 'cta_heading', 'Want to See More?')); ?>
                </h2>
                <p class="text-lg sm:text-xl md:text-2xl text-gray-600 dark:text-gray-400 mb-10 max-w-3xl mx-auto leading-relaxed font-medium">
                    <?php echo nl2br(vvu_e(vvu_gallery_text($g_content, 'cta_text'))); ?>
                </p>
                <div class="flex flex-col sm:flex-row gap-4 sm:gap-6 justify-center">
                    <?php if ($t = vvu_gallery_text($g_content, 'cta_1_text')): ?>
                        <a href="<?php echo vvu_e(vvu_url(vvu_gallery_text($g_content, 'cta_1_link', '#'))); ?>"
                           class="px-8 sm:px-10 py-4 sm:py-5 bg-blue-600 hover:bg-blue-700 text-white text-lg sm:text-xl font-bold rounded-2xl transition-all transform hover:scale-105 shadow-lg flex items-center justify-center gap-3">
                            <span class="material-symbols-outlined text-2xl sm:text-3xl text-white"><?php echo vvu_e(vvu_gallery_text($g_content, 'cta_1_icon', 'event')); ?></span>
                            <?php echo vvu_e($t); ?>
                        </a>
                    <?php endif; ?>
                    <?php if ($t = vvu_gallery_text($g_content, 'cta_2_text')): ?>
                        <a href="<?php echo vvu_e(vvu_url(vvu_gallery_text($g_content, 'cta_2_link', '#'))); ?>"
                           class="px-8 sm:px-10 py-4 sm:py-5 bg-white dark:bg-gray-800 hover:bg-gray-100 dark:hover:bg-gray-700 text-gray-900 dark:text-white text-lg sm:text-xl font-bold rounded-2xl transition-all border-2 border-gray-200 dark:border-gray-700 transform hover:scale-105 shadow-lg flex items-center justify-center gap-3">
                            <span class="material-symbols-outlined text-2xl sm:text-3xl"><?php echo vvu_e(vvu_gallery_text($g_content, 'cta_2_icon', 'newspaper')); ?></span>
                            <?php echo vvu_e($t); ?>
                        </a>
                    <?php endif; ?>
                </div>
            </div>
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
