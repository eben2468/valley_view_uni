<?php
/**
 * VVU Scholar — shared markup partials.
 *
 * The five public pages under research/ show the same person card, the same
 * publication row and the same pager. They live here so a change to one is a
 * change to all of them, and so each page file stays about its own content.
 *
 * Every function echoes. Every value is escaped at the point of output.
 */

require_once __DIR__ . '/research_helper.php';

if (!defined('VVU_RESEARCH_PARTIALS')) {
    define('VVU_RESEARCH_PARTIALS', 1);

/**
 * The sticky portal sub-navigation. $current is the basename of the open page.
 */
function r_subnav($current = 'index.php')
{
    $links = [
        'index.php'        => ['Overview',     'fa-chart-simple'],
        'authors.php'      => ['Researchers',  'fa-user-graduate'],
        'publications.php' => ['Publications', 'fa-book-open'],
        'units.php'        => ['Faculties',    'fa-building-columns'],
        'areas.php'        => ['Research Areas', 'fa-flask'],
    ];
    ?>
    <nav class="vvus-subnav" aria-label="Research portal sections">
        <div class="vvus__wrap vvus-subnav__inner">
            <a class="vvus-subnav__brand" href="index.php">
                <i class="fa-solid fa-graduation-cap" aria-hidden="true"></i>
                <span>VVU Scholar</span>
            </a>
            <div class="vvus-subnav__links">
                <?php foreach ($links as $file => $meta): ?>
                    <a href="<?php echo r_e($file); ?>"
                       class="<?php echo $current === $file ? 'is-active' : ''; ?>"
                       <?php echo $current === $file ? 'aria-current="page"' : ''; ?>>
                        <?php echo r_e($meta[0]); ?>
                    </a>
                <?php endforeach; ?>
            </div>
        </div>
    </nav>
    <?php
}

/**
 * The search field. It is a real GET form pointed at publications.php, so it
 * works with JavaScript switched off; research.js upgrades it in place.
 */
function r_searchbox($placeholder = 'Search researchers, publications, topics…', $value = '')
{
    ?>
    <form class="vvus-search" data-api="api.php" action="publications.php" method="get" role="search">
        <div class="vvus-search__field">
            <i class="fa-solid fa-magnifying-glass" aria-hidden="true"></i>
            <input class="vvus-search__input browser-default"
                   type="search" name="q" autocomplete="off"
                   value="<?php echo r_e($value); ?>"
                   placeholder="<?php echo r_e($placeholder); ?>"
                   aria-label="Search the research portal">
            <button class="vvus-search__btn" type="submit">Search</button>
        </div>
        <div class="vvus-search__results" role="listbox" aria-label="Search suggestions" hidden></div>
    </form>
    <?php
}

/** A compact page header for the inner pages — same silhouette as the hero. */
function r_pagehero($eyebrow, $title, $lead = '', array $crumbs = [], $image = '')
{
    $image = trim((string) $image);
    ?>
    <header class="vvus-hero" style="padding-block:64px 52px;">
        <?php if ($image !== ''): ?>
            <div class="vvus-hero__bg" style="background-image:url('<?php echo r_e(r_asset($image)); ?>')"></div>
        <?php endif; ?>
        <div class="vvus-hero__veil"></div>
        <div class="vvus__wrap">
            <?php if ($crumbs): ?>
                <nav class="vvus-crumbs" aria-label="Breadcrumb">
                    <?php
                    $last = count($crumbs) - 1;
                    $i = 0;
                    foreach ($crumbs as $label => $href):
                        if ($i > 0): ?>
                            <span class="vvus-crumbs__sep" aria-hidden="true">/</span>
                        <?php endif;
                        if ($i === $last || $href === null): ?>
                            <span><?php echo r_e($label); ?></span>
                        <?php else: ?>
                            <a href="<?php echo r_e($href); ?>"><?php echo r_e($label); ?></a>
                        <?php endif;
                        $i++;
                    endforeach; ?>
                </nav>
            <?php endif; ?>

            <?php if ($eyebrow !== ''): ?>
                <p class="vvus-hero__badge" style="margin-top:<?php echo $crumbs ? '16px' : '0'; ?>">
                    <i class="fa-solid fa-graduation-cap" aria-hidden="true"></i>
                    <?php echo r_e($eyebrow); ?>
                </p>
            <?php endif; ?>

            <h1 style="max-width:24ch"><?php echo r_e($title); ?></h1>
            <?php if ($lead !== ''): ?>
                <p class="vvus-hero__lead"><?php echo r_e($lead); ?></p>
            <?php endif; ?>
        </div>
    </header>
    <?php
}

/** A researcher's photo, or their initials when there isn't one. */
function r_avatar(array $s, $class = 'vvus-avatar')
{
    $photo = trim((string) ($s['photo'] ?? ''));
    if ($photo !== '') {
        printf(
            '<img class="%s" src="%s" alt="%s" loading="lazy" width="56" height="56">',
            r_e($class),
            r_e(r_asset($photo)),
            r_e($s['full_name'] ?? '')
        );
        return;
    }
    printf(
        '<span class="%s" aria-hidden="true">%s</span>',
        r_e($class),
        r_e(r_initials($s['full_name'] ?? ''))
    );
}

/** One researcher, as a card for the directory grid. */
function r_person_card(array $s)
{
    $name  = trim(($s['title'] ? $s['title'] . ' ' : '') . $s['full_name']);
    $href  = 'scholar.php?p=' . rawurlencode($s['slug']);
    $tags  = array_slice(array_filter(array_map('trim', explode(',', (string) ($s['interests'] ?? '')))), 0, 3);
    ?>
    <article class="vvus-person vvus-reveal" style="<?php echo r_accent($s['unit_color'] ?? 'blue'); ?>">
        <div class="vvus-person__top">
            <?php r_avatar($s); ?>
            <div style="min-width:0">
                <a class="vvus-person__name" href="<?php echo r_e($href); ?>"><?php echo r_e($name); ?></a>
                <?php if (!empty($s['position'])): ?>
                    <p class="vvus-person__role"><?php echo r_e($s['position']); ?></p>
                <?php endif; ?>
            </div>
        </div>

        <?php if (!empty($s['unit_name'])): ?>
            <span class="vvus-person__unit">
                <i class="fa-solid fa-building-columns" aria-hidden="true"></i>
                <?php echo r_e($s['unit_name']); ?>
            </span>
        <?php endif; ?>

        <?php if ($tags): ?>
            <div class="vvus-person__tags">
                <?php foreach ($tags as $tag): ?>
                    <span class="vvus-tag"><?php echo r_e($tag); ?></span>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

        <div class="vvus-person__nums">
            <div class="vvus-person__num"><b><?php echo r_e(r_compact($s['citations'])); ?></b><span>Cites</span></div>
            <div class="vvus-person__num"><b><?php echo (int) $s['h_index']; ?></b><span>h-index</span></div>
            <div class="vvus-person__num"><b><?php echo r_e(r_compact($s['publications_count'])); ?></b><span>Papers</span></div>
        </div>
    </article>
    <?php
}

/**
 * One publication row. $opts:
 *   'abstract' => show the toggle (default true)
 *   'accent'   => colour token override
 */
function r_publication_row(array $p, array $opts = [])
{
    $types  = r_publication_types();
    $link   = r_publication_link($p);
    $accent = $opts['accent'] ?? ($p['area_color'] ?? 'blue');
    $apa    = r_cite_apa($p);
    $domId  = 'abs-' . (int) $p['id'];
    $showAbstract = ($opts['abstract'] ?? true) && trim((string) ($p['abstract'] ?? '')) !== '';
    ?>
    <article class="vvus-pub vvus-reveal" id="pub-<?php echo (int) $p['id']; ?>" style="<?php echo r_accent($accent); ?>">
        <div>
            <div class="vvus-pub__meta">
                <span class="vvus-chip"><?php echo r_e($types[$p['pub_type']] ?? 'Publication'); ?></span>
                <?php if (!empty($p['pub_year'])): ?>
                    <span class="vvus-chip vvus-chip--plain"><?php echo (int) $p['pub_year']; ?></span>
                <?php endif; ?>
                <?php if (!empty($p['is_open_access'])): ?>
                    <span class="vvus-chip vvus-chip--oa"><i class="fa-solid fa-unlock" aria-hidden="true"></i> Open Access</span>
                <?php endif; ?>
                <?php if (!empty($p['area_name'])): ?>
                    <span class="vvus-chip vvus-chip--plain"><?php echo r_e($p['area_name']); ?></span>
                <?php endif; ?>
            </div>

            <?php if ($link !== ''): ?>
                <a class="vvus-pub__title" href="<?php echo r_e($link); ?>" target="_blank" rel="noopener noreferrer">
                    <?php echo r_e($p['title']); ?>
                </a>
            <?php else: ?>
                <h3 class="vvus-pub__title"><?php echo r_e($p['title']); ?></h3>
            <?php endif; ?>

            <?php if (!empty($p['authors'])): ?>
                <p class="vvus-pub__authors">
                    <?php echo r_e($p['authors']); ?>
                    <?php if (!empty($p['scholar_slug'])): ?>
                        — <a href="scholar.php?p=<?php echo r_e(rawurlencode($p['scholar_slug'])); ?>"><?php echo r_e($p['scholar_name']); ?></a>
                    <?php endif; ?>
                </p>
            <?php elseif (!empty($p['scholar_slug'])): ?>
                <p class="vvus-pub__authors">
                    <a href="scholar.php?p=<?php echo r_e(rawurlencode($p['scholar_slug'])); ?>"><?php echo r_e($p['scholar_name']); ?></a>
                </p>
            <?php endif; ?>

            <?php if (!empty($p['venue'])): ?>
                <p class="vvus-pub__venue">
                    <?php echo r_e($p['venue']); ?><?php
                    if (!empty($p['volume'])) {
                        echo ' ' . r_e($p['volume']);
                        if (!empty($p['issue'])) { echo '(' . r_e($p['issue']) . ')'; }
                    }
                    if (!empty($p['pages'])) { echo ', ' . r_e($p['pages']); }
                    ?>
                </p>
            <?php endif; ?>

            <?php if ($showAbstract): ?>
                <p class="vvus-pub__abstract" id="<?php echo r_e($domId); ?>" hidden><?php echo r_e($p['abstract']); ?></p>
            <?php endif; ?>

            <div class="vvus-pub__tools">
                <?php if ($showAbstract): ?>
                    <button type="button" class="vvus-btn vvus-btn--ghost vvus-btn--sm"
                            data-toggle-target="#<?php echo r_e($domId); ?>"
                            data-label-closed="Abstract" data-label-open="Hide abstract"
                            aria-expanded="false" aria-controls="<?php echo r_e($domId); ?>">
                        <i class="fa-solid fa-align-left" aria-hidden="true"></i>
                        <span data-toggle-label>Abstract</span>
                    </button>
                <?php endif; ?>

                <button type="button" class="vvus-btn vvus-btn--ghost vvus-btn--sm"
                        data-copy="<?php echo r_e($apa); ?>" data-copied="Citation copied">
                    <i class="fa-solid fa-quote-right" aria-hidden="true"></i> Cite
                </button>

                <?php if (!empty($p['doi'])): ?>
                    <a class="vvus-btn vvus-btn--ghost vvus-btn--sm"
                       href="https://doi.org/<?php echo r_e(ltrim($p['doi'], '/')); ?>"
                       target="_blank" rel="noopener noreferrer">
                        <i class="fa-solid fa-link" aria-hidden="true"></i> DOI
                    </a>
                <?php endif; ?>

                <?php if (!empty($p['pdf_path'])): ?>
                    <a class="vvus-btn vvus-btn--ghost vvus-btn--sm"
                       href="<?php echo r_e(r_asset($p['pdf_path'])); ?>" target="_blank" rel="noopener">
                        <i class="fa-solid fa-file-pdf" aria-hidden="true"></i> PDF
                    </a>
                <?php endif; ?>
            </div>
        </div>

        <div class="vvus-pub__aside">
            <b><?php echo r_e(r_compact($p['citations'])); ?></b>
            <span>Citations</span>
        </div>
    </article>
    <?php
}

/** Empty state, used wherever a filtered list comes back with nothing. */
function r_empty($title, $message, $icon = 'fa-magnifying-glass', $actionHref = '', $actionText = '')
{
    ?>
    <div class="vvus-empty">
        <i class="fa-solid <?php echo r_e(ltrim($icon, ' ')); ?>" aria-hidden="true"></i>
        <h3><?php echo r_e($title); ?></h3>
        <p><?php echo r_e($message); ?></p>
        <?php if ($actionHref !== ''): ?>
            <div class="vvus-actions vvus-actions--center" style="margin-top:20px">
                <a class="vvus-btn vvus-btn--ghost" href="<?php echo r_e($actionHref); ?>"><?php echo r_e($actionText); ?></a>
            </div>
        <?php endif; ?>
    </div>
    <?php
}

/**
 * Pagination. Keeps whatever filters are in the query string, replacing only
 * the page number, so paging never silently drops a filter.
 */
function r_pager($page, $pages, array $baseQuery = [])
{
    $page  = (int) $page;
    $pages = (int) $pages;
    if ($pages < 2) {
        return;
    }

    $url = static function ($n) use ($baseQuery) {
        $q = array_merge($baseQuery, ['page' => $n]);
        unset($q['focus']);
        $q = array_filter($q, static function ($v) { return $v !== '' && $v !== null; });
        return '?' . http_build_query($q);
    };

    // A window of pages around the current one, with the ends always reachable.
    $window = [];
    for ($i = 1; $i <= $pages; $i++) {
        if ($i === 1 || $i === $pages || abs($i - $page) <= 2) {
            $window[] = $i;
        }
    }
    ?>
    <nav class="vvus-pager" aria-label="Pagination">
        <?php if ($page > 1): ?>
            <a href="<?php echo r_e($url($page - 1)); ?>" rel="prev" aria-label="Previous page">
                <i class="fa-solid fa-chevron-left" aria-hidden="true"></i>
            </a>
        <?php endif; ?>

        <?php
        $previous = 0;
        foreach ($window as $n):
            if ($previous && $n - $previous > 1): ?>
                <span class="is-gap">…</span>
            <?php endif;
            $previous = $n;
            if ($n === $page): ?>
                <span class="is-current" aria-current="page"><?php echo $n; ?></span>
            <?php else: ?>
                <a href="<?php echo r_e($url($n)); ?>"><?php echo $n; ?></a>
            <?php endif;
        endforeach; ?>

        <?php if ($page < $pages): ?>
            <a href="<?php echo r_e($url($page + 1)); ?>" rel="next" aria-label="Next page">
                <i class="fa-solid fa-chevron-right" aria-hidden="true"></i>
            </a>
        <?php endif; ?>
    </nav>
    <?php
}

/** Section heading driven by the research_sections row. */
function r_section_head(array $section, $center = false, $align = '')
{
    ?>
    <div class="vvus__head <?php echo $center ? 'vvus__head--center' : ''; ?>" style="<?php echo r_e($align); ?>">
        <?php if (!empty($section['section_subtitle'])): ?>
            <p class="vvus__eyebrow"><?php echo r_e($section['section_subtitle']); ?></p>
        <?php endif; ?>
        <h2 class="vvus__title"><?php echo r_e($section['section_title']); ?></h2>
        <?php if (!empty($section['section_description'])): ?>
            <p class="vvus__subtitle"><?php echo r_rich($section['section_description']); ?></p>
        <?php endif; ?>
    </div>
    <?php
}

} // VVU_RESEARCH_PARTIALS
