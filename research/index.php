<?php
/**
 * VVU Scholar — research portal landing page.
 *
 * Every word, figure, colour and section heading on this page comes out of the
 * research_* tables and is edited at
 *     Admin → Research Portal (VVU Scholar)
 * A section switched off in the admin simply does not render, so the page stays
 * coherent however much of it is turned on.
 *
 * Data layer:  research/includes/research_helper.php
 * Markup bits: research/includes/research_partials.php
 * Schema:      sql/research_portal_schema.sql
 */

// The masthead and the stylesheets live one directory up. $vvu_root is read by
// includes/header.php and includes/footer.php to resolve every link and asset.
$vvu_root = '../';

require_once __DIR__ . '/../includes/db_connect.php';
require_once __DIR__ . '/includes/research_partials.php';

$page_title  = r_set($pdo, 'meta_title', 'VVU Scholar — Research Portal | Valley View University');
$active_page = 'research';

$metrics  = r_metrics($pdo);
$tiles    = r_stat_tiles($pdo);
$trend    = r_set($pdo, 'show_trend_chart', '1') === '1'
    ? r_year_trend($pdo, (int) r_set($pdo, 'trend_years', '10'))
    : [];
$units    = r_units($pdo);
$areas    = r_areas($pdo);
$partners = r_partners($pdo, 18);
$highlights = r_highlights($pdo, null, 6);

$topScholars = r_scholars($pdo, [
    'sort'     => 'citations',
    'per_page' => max(3, min(20, (int) r_set($pdo, 'leaderboard_size', '8'))),
])['rows'];

// Featured first; if nobody has flagged anything yet, fall back to the most
// cited work so the band is never an empty box on a freshly seeded portal.
$featuredPubs = r_publications($pdo, [
    'featured' => 1,
    'sort'     => 'featured',
    'per_page' => (int) r_set($pdo, 'featured_pub_count', '6'),
])['rows'];
if (!$featuredPubs) {
    $featuredPubs = r_publications($pdo, [
        'sort'     => 'citations',
        'per_page' => (int) r_set($pdo, 'featured_pub_count', '6'),
    ])['rows'];
}

// The faculty bars are drawn as a share of the busiest faculty, not of the
// total — otherwise eight faculties each get a 12% sliver and the chart says
// nothing about which of them is actually publishing.
$unitPeak = 0;
foreach ($units as $u) {
    $unitPeak = max($unitPeak, (int) $u['publication_count']);
}

$hasData = $metrics['scholars'] > 0 || $metrics['publications'] > 0;

// The hero panel leads with whoever currently tops the leaderboard.
$heroLead = $topScholars[0] ?? null;

include __DIR__ . '/../includes/header.php';
?>

<link rel="stylesheet" href="assets/research.css?v=1.0">
<?php if ($trend): ?>
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js" defer></script>
<?php endif; ?>
<script src="assets/research.js?v=1.0" defer></script>

<main id="main-content" class="vvus">

    <!-- ==================================================================
         HERO
         ================================================================== -->
    <?php
    $heroImage = r_set($pdo, 'hero_image', '');
    ?>
    <header class="vvus-hero">
        <?php if ($heroImage !== ''): ?>
            <div class="vvus-hero__bg" style="background-image:url('<?php echo r_e(r_asset($heroImage)); ?>')"></div>
        <?php endif; ?>
        <div class="vvus-hero__veil"></div>

        <div class="vvus__wrap vvus-hero__grid">
          <div class="vvus-hero__main">
            <p class="vvus-hero__badge">
                <i class="fa-solid fa-graduation-cap" aria-hidden="true"></i>
                <?php echo r_e(r_set($pdo, 'hero_badge', 'VVU Scholar')); ?>
            </p>

            <h1><?php echo r_e(r_set($pdo, 'hero_title', 'Valley View University Research Portal')); ?></h1>

            <?php $lead = r_set($pdo, 'hero_subtitle', ''); ?>
            <?php if ($lead !== ''): ?>
                <p class="vvus-hero__lead"><?php echo r_e($lead); ?></p>
            <?php endif; ?>

            <?php $desc = r_set($pdo, 'hero_description', ''); ?>
            <?php if ($desc !== ''): ?>
                <p class="vvus-hero__desc"><?php echo r_rich($desc); ?></p>
            <?php endif; ?>

            <?php r_searchbox(r_set($pdo, 'hero_search_placeholder', 'Search researchers, publications, topics…')); ?>

            <div class="vvus-hero__quick">
                <span>Browse</span>
                <a href="authors.php"><i class="fa-solid fa-user-graduate" aria-hidden="true"></i> Researchers</a>
                <a href="publications.php"><i class="fa-solid fa-book-open" aria-hidden="true"></i> Publications</a>
                <a href="units.php"><i class="fa-solid fa-building-columns" aria-hidden="true"></i> Faculties</a>
                <a href="areas.php"><i class="fa-solid fa-flask" aria-hidden="true"></i> Research Areas</a>
                <a href="publications.php?open_access=1"><i class="fa-solid fa-unlock" aria-hidden="true"></i> Open Access</a>
            </div>

          </div><!-- /.vvus-hero__main -->

            <?php if ($hasData): ?>
                <aside class="vvus-hero__panel">
                    <h2>The portal at a glance</h2>
                    <dl class="vvus-hero__rows">
                        <div class="vvus-hero__row">
                            <dt>Researchers indexed</dt>
                            <dd><?php echo r_e(r_compact($metrics['scholars'])); ?></dd>
                        </div>
                        <div class="vvus-hero__row">
                            <dt>Publications catalogued</dt>
                            <dd><?php echo r_e(r_compact($metrics['publications'])); ?></dd>
                        </div>
                        <div class="vvus-hero__row">
                            <dt>Citations to that work</dt>
                            <dd><?php echo r_e(r_compact($metrics['citations'])); ?></dd>
                        </div>
                        <div class="vvus-hero__row">
                            <dt>Institutional h-index</dt>
                            <dd><?php echo (int) $metrics['h_index']; ?></dd>
                        </div>
                        <?php if ($metrics['first_year'] && $metrics['last_year']): ?>
                            <div class="vvus-hero__row">
                                <dt>Years covered</dt>
                                <dd style="font-size:19px"><?php echo (int) $metrics['first_year']; ?>&ndash;<?php echo (int) $metrics['last_year']; ?></dd>
                            </div>
                        <?php endif; ?>
                    </dl>

                    <?php if ($heroLead): ?>
                        <a class="vvus-hero__lede" href="scholar.php?p=<?php echo r_e(rawurlencode($heroLead['slug'])); ?>">
                            <?php r_avatar($heroLead); ?>
                            <span class="vvus-hero__lede-text">
                                <small>Most cited right now</small>
                                <b><?php echo r_e(trim(($heroLead['title'] ? $heroLead['title'] . ' ' : '') . $heroLead['full_name'])); ?></b>
                                <span><?php echo r_e(r_compact($heroLead['citations'])); ?> citations · h-index <?php echo (int) $heroLead['h_index']; ?></span>
                            </span>
                        </a>
                    <?php endif; ?>
                </aside>
            <?php else: ?>
              <div></div>
            <?php endif; ?>
        </div>
    </header>

    <?php r_subnav('index.php'); ?>

    <!-- ==================================================================
         STAT TILES
         ================================================================== -->
    <?php if ($tiles && ($sec = r_section($pdo, 'stats'))): ?>
        <section class="vvus__band vvus__band--alt" id="stats" aria-labelledby="stats-h">
            <div class="vvus__wrap">
                <div class="vvus__head">
                    <?php if (!empty($sec['section_subtitle'])): ?>
                        <p class="vvus__eyebrow"><?php echo r_e($sec['section_subtitle']); ?></p>
                    <?php endif; ?>
                    <h2 class="vvus__title" id="stats-h"><?php echo r_e($sec['section_title']); ?></h2>
                    <?php if (!empty($sec['section_description'])): ?>
                        <p class="vvus__subtitle"><?php echo r_rich($sec['section_description']); ?></p>
                    <?php endif; ?>
                </div>

                <div class="vvus-stats">
                    <?php foreach ($tiles as $tile): ?>
                        <article class="vvus-stat vvus-reveal" style="<?php echo r_accent($tile['stat_color']); ?>"
                                 <?php if (!empty($tile['is_auto'])): ?>title="Counted from the catalogue each time the page loads"<?php endif; ?>>
                            <div class="vvus-stat__icon">
                                <i class="<?php echo r_e(r_icon($tile['stat_icon'], 'fa-chart-line')); ?>" aria-hidden="true"></i>
                            </div>
                            <div class="vvus-stat__text">
                                <p class="vvus-stat__value"
                                   <?php if (is_numeric($tile['resolved_value'])): ?>
                                       data-count="<?php echo r_e($tile['resolved_value']); ?>"
                                       data-suffix="<?php echo r_e($tile['stat_suffix']); ?>"
                                   <?php endif; ?>><?php
                                    echo r_e($tile['display_value'] . $tile['stat_suffix']);
                                ?></p>
                                <p class="vvus-stat__label"><?php echo r_e($tile['stat_label']); ?></p>
                            </div>
                        </article>
                    <?php endforeach; ?>
                </div>

                <?php $note = r_set($pdo, 'metrics_note', ''); ?>
                <?php if ($note !== ''): ?>
                    <p class="vvus__note" style="max-width:80ch">
                        <i class="fa-solid fa-circle-info" aria-hidden="true"></i>
                        <?php echo r_rich($note); ?>
                    </p>
                <?php endif; ?>
            </div>
        </section>
    <?php endif; ?>

    <!-- ==================================================================
         OUTPUT & IMPACT OVER TIME
         ================================================================== -->
    <?php if ($trend && ($sec = r_section($pdo, 'trends'))):
        $totalPubs  = array_sum(array_column($trend, 'publications'));
        $totalCites = array_sum(array_column($trend, 'citations'));
        $peak       = $trend[0];
        foreach ($trend as $t) {
            if ($t['publications'] >= $peak['publications']) { $peak = $t; }
        }
    ?>
        <section class="vvus__band" id="trends" aria-labelledby="trends-h">
            <div class="vvus__wrap">
                <div class="vvus__head">
                    <?php if (!empty($sec['section_subtitle'])): ?>
                        <p class="vvus__eyebrow"><?php echo r_e($sec['section_subtitle']); ?></p>
                    <?php endif; ?>
                    <h2 class="vvus__title" id="trends-h"><?php echo r_e($sec['section_title']); ?></h2>
                    <?php if (!empty($sec['section_description'])): ?>
                        <p class="vvus__subtitle"><?php echo r_rich($sec['section_description']); ?></p>
                    <?php endif; ?>
                </div>

                <div class="vvus-trend">
                    <div class="vvus-card vvus-reveal">
                        <div class="vvus-card__head">
                            <h3><?php echo (int) $trend[0]['year']; ?>&ndash;<?php echo (int) end($trend)['year']; ?></h3>
                            <div class="vvus-legend">
                                <span><i style="background:#1d4ed8"></i> Publications</span>
                                <span><i style="background:#a16207"></i> Citations</span>
                            </div>
                        </div>
                        <div class="vvus-card__body">
                            <div class="vvus-chart">
                                <canvas id="vvusTrendChart"
                                        data-series='<?php echo r_e(json_encode($trend, JSON_UNESCAPED_SLASHES)); ?>'
                                        aria-label="Publications and citations by year"
                                        role="img"></canvas>
                            </div>
                        </div>
                    </div>

                    <dl class="vvus-facts vvus-reveal">
                        <div class="vvus-fact">
                            <dt>Publications in this window</dt>
                            <dd><?php echo number_format($totalPubs); ?></dd>
                        </div>
                        <div class="vvus-fact">
                            <dt>Citations to that work</dt>
                            <dd><?php echo number_format($totalCites); ?></dd>
                        </div>
                        <div class="vvus-fact">
                            <dt>Busiest year</dt>
                            <dd><?php echo (int) $peak['year']; ?></dd>
                        </div>
                        <div class="vvus-fact">
                            <dt>Average citations per paper</dt>
                            <dd><?php echo $totalPubs > 0 ? number_format($totalCites / $totalPubs, 1) : '—'; ?></dd>
                        </div>
                        <div class="vvus-fact">
                            <dt>Published this year</dt>
                            <dd><?php echo number_format($metrics['this_year']); ?></dd>
                        </div>
                        <div class="vvus-fact">
                            <dt>Open access share</dt>
                            <dd><?php echo (int) $metrics['open_access']; ?>%</dd>
                        </div>
                        <div class="vvus-fact">
                            <dt>Most cited single work</dt>
                            <dd><?php echo number_format($metrics['top_citation']); ?></dd>
                        </div>
                    </dl>
                </div>
            </div>
        </section>
    <?php endif; ?>

    <!-- ==================================================================
         MOST CITED RESEARCHERS
         ================================================================== -->
    <?php if ($topScholars && ($sec = r_section($pdo, 'leaderboard'))): ?>
        <section class="vvus__band vvus__band--alt" id="leaderboard" aria-labelledby="board-h">
            <div class="vvus__wrap">
                <div class="vvus__head" style="display:flex;flex-wrap:wrap;align-items:flex-end;gap:16px;max-width:none">
                    <div style="flex:1 1 420px;min-width:0">
                        <?php if (!empty($sec['section_subtitle'])): ?>
                            <p class="vvus__eyebrow"><?php echo r_e($sec['section_subtitle']); ?></p>
                        <?php endif; ?>
                        <h2 class="vvus__title" id="board-h"><?php echo r_e($sec['section_title']); ?></h2>
                        <?php if (!empty($sec['section_description'])): ?>
                            <p class="vvus__subtitle"><?php echo r_rich($sec['section_description']); ?></p>
                        <?php endif; ?>
                    </div>
                    <a class="vvus-btn vvus-btn--ghost" href="authors.php">
                        All researchers <i class="fa-solid fa-arrow-right" aria-hidden="true"></i>
                    </a>
                </div>

                <div class="vvus-board">
                    <?php foreach ($topScholars as $i => $s): ?>
                        <article class="vvus-rank vvus-reveal">
                            <span class="vvus-rank__pos"><?php echo $i + 1; ?></span>
                            <?php r_avatar($s); ?>
                            <div class="vvus-rank__who">
                                <a class="vvus-rank__name" href="scholar.php?p=<?php echo r_e(rawurlencode($s['slug'])); ?>">
                                    <?php echo r_e(trim(($s['title'] ? $s['title'] . ' ' : '') . $s['full_name'])); ?>
                                </a>
                                <p class="vvus-rank__meta">
                                    <?php echo r_e(implode(' · ', array_filter([$s['position'], $s['unit_name']]))); ?>
                                </p>
                            </div>
                            <div class="vvus-rank__nums">
                                <div class="vvus-rank__num">
                                    <b><?php echo r_e(r_compact($s['citations'])); ?></b><span>Cites</span>
                                </div>
                                <div class="vvus-rank__num">
                                    <b><?php echo (int) $s['h_index']; ?></b><span>h-index</span>
                                </div>
                                <div class="vvus-rank__num">
                                    <b><?php echo r_e(r_compact($s['publications_count'])); ?></b><span>Papers</span>
                                </div>
                            </div>
                        </article>
                    <?php endforeach; ?>
                </div>
            </div>
        </section>
    <?php endif; ?>

    <!-- ==================================================================
         RESEARCH BY FACULTY
         ================================================================== -->
    <?php if ($units && ($sec = r_section($pdo, 'units'))): ?>
        <section class="vvus__band" id="units" aria-labelledby="units-h">
            <div class="vvus__wrap">
                <div class="vvus__head">
                    <?php if (!empty($sec['section_subtitle'])): ?>
                        <p class="vvus__eyebrow"><?php echo r_e($sec['section_subtitle']); ?></p>
                    <?php endif; ?>
                    <h2 class="vvus__title" id="units-h"><?php echo r_e($sec['section_title']); ?></h2>
                    <?php if (!empty($sec['section_description'])): ?>
                        <p class="vvus__subtitle"><?php echo r_rich($sec['section_description']); ?></p>
                    <?php endif; ?>
                </div>

                <div class="vvus-units">
                    <?php foreach ($units as $u):
                        $share = $unitPeak > 0 ? round($u['publication_count'] / $unitPeak * 100) : 0;
                    ?>
                        <article class="vvus-unit vvus-reveal" style="<?php echo r_accent($u['color']); ?>" id="<?php echo r_e($u['slug']); ?>">
                            <div class="vvus-unit__icon">
                                <i class="<?php echo r_e(r_icon($u['icon'], 'fa-building-columns')); ?>" aria-hidden="true"></i>
                            </div>
                            <h3><?php echo r_e($u['name']); ?></h3>
                            <?php if (!empty($u['description'])): ?>
                                <p class="vvus-unit__desc"><?php echo r_e($u['description']); ?></p>
                            <?php endif; ?>

                            <div class="vvus-unit__bar" data-bar="<?php echo (int) $share; ?>"
                                 role="img"
                                 aria-label="<?php echo (int) $share; ?>% of the busiest faculty's output">
                                <i></i>
                            </div>
                            <p class="vvus-unit__nums">
                                <span><b><?php echo number_format($u['scholar_count']); ?></b> researchers</span>
                                <span><b><?php echo number_format($u['publication_count']); ?></b> publications</span>
                                <span><b><?php echo r_e(r_compact($u['citation_count'])); ?></b> citations</span>
                            </p>
                            <a class="vvus-unit__cta" href="authors.php?unit=<?php echo (int) $u['id']; ?>">
                                Browse researchers <i class="fa-solid fa-arrow-right" aria-hidden="true"></i>
                            </a>
                        </article>
                    <?php endforeach; ?>
                </div>
            </div>
        </section>
    <?php endif; ?>

    <!-- ==================================================================
         RESEARCH AREAS
         ================================================================== -->
    <?php if ($areas && ($sec = r_section($pdo, 'areas'))): ?>
        <section class="vvus__band vvus__band--alt" id="areas" aria-labelledby="areas-h">
            <div class="vvus__wrap">
                <div class="vvus__head vvus__head--center">
                    <?php if (!empty($sec['section_subtitle'])): ?>
                        <p class="vvus__eyebrow"><?php echo r_e($sec['section_subtitle']); ?></p>
                    <?php endif; ?>
                    <h2 class="vvus__title" id="areas-h"><?php echo r_e($sec['section_title']); ?></h2>
                    <?php if (!empty($sec['section_description'])): ?>
                        <p class="vvus__subtitle"><?php echo r_rich($sec['section_description']); ?></p>
                    <?php endif; ?>
                </div>

                <div class="vvus-areas">
                    <?php foreach ($areas as $a):
                        $sdgs = array_filter(array_map('trim', explode(',', (string) $a['sdg_goals'])));
                    ?>
                        <article class="vvus-area vvus-reveal" style="<?php echo r_accent($a['color']); ?>">
                            <div class="vvus-area__icon">
                                <i class="<?php echo r_e(r_icon($a['icon'], 'fa-flask')); ?>" aria-hidden="true"></i>
                            </div>
                            <h3><?php echo r_e($a['name']); ?></h3>
                            <?php if (!empty($a['description'])): ?>
                                <p><?php echo r_e($a['description']); ?></p>
                            <?php endif; ?>
                            <?php if ($sdgs): ?>
                                <p class="vvus-area__sdg" title="Aligned UN Sustainable Development Goals">
                                    <?php foreach ($sdgs as $g): ?>
                                        <b>SDG&nbsp;<?php echo (int) $g; ?></b>
                                    <?php endforeach; ?>
                                </p>
                            <?php endif; ?>
                            <a class="vvus-area__count" href="publications.php?area=<?php echo (int) $a['id']; ?>">
                                <?php echo number_format($a['publication_count']); ?> publications
                                <i class="fa-solid fa-arrow-right" aria-hidden="true"></i>
                            </a>
                        </article>
                    <?php endforeach; ?>
                </div>
            </div>
        </section>
    <?php endif; ?>

    <!-- ==================================================================
         FEATURED PUBLICATIONS
         ================================================================== -->
    <?php if ($featuredPubs && ($sec = r_section($pdo, 'publications'))): ?>
        <section class="vvus__band" id="publications" aria-labelledby="pubs-h">
            <div class="vvus__wrap">
                <div class="vvus__head" style="display:flex;flex-wrap:wrap;align-items:flex-end;gap:16px;max-width:none">
                    <div style="flex:1 1 420px;min-width:0">
                        <?php if (!empty($sec['section_subtitle'])): ?>
                            <p class="vvus__eyebrow"><?php echo r_e($sec['section_subtitle']); ?></p>
                        <?php endif; ?>
                        <h2 class="vvus__title" id="pubs-h"><?php echo r_e($sec['section_title']); ?></h2>
                        <?php if (!empty($sec['section_description'])): ?>
                            <p class="vvus__subtitle"><?php echo r_rich($sec['section_description']); ?></p>
                        <?php endif; ?>
                    </div>
                    <a class="vvus-btn vvus-btn--ghost" href="publications.php">
                        Full catalogue <i class="fa-solid fa-arrow-right" aria-hidden="true"></i>
                    </a>
                </div>

                <div class="vvus-pubs">
                    <?php foreach ($featuredPubs as $p) { r_publication_row($p); } ?>
                </div>
            </div>
        </section>
    <?php endif; ?>

    <!-- ==================================================================
         GRANTS, AWARDS & PROJECTS
         ================================================================== -->
    <?php if ($highlights && ($sec = r_section($pdo, 'highlights'))): ?>
        <section class="vvus__band vvus__band--alt" id="highlights" aria-labelledby="high-h">
            <div class="vvus__wrap">
                <div class="vvus__head">
                    <?php if (!empty($sec['section_subtitle'])): ?>
                        <p class="vvus__eyebrow"><?php echo r_e($sec['section_subtitle']); ?></p>
                    <?php endif; ?>
                    <h2 class="vvus__title" id="high-h"><?php echo r_e($sec['section_title']); ?></h2>
                    <?php if (!empty($sec['section_description'])): ?>
                        <p class="vvus__subtitle"><?php echo r_rich($sec['section_description']); ?></p>
                    <?php endif; ?>
                </div>

                <div class="vvus-highlights">
                    <?php foreach ($highlights as $h): ?>
                        <article class="vvus-highlight vvus-reveal" style="<?php echo r_accent($h['color']); ?>">
                            <?php if (!empty($h['image'])): ?>
                                <img class="vvus-highlight__img" src="<?php echo r_e(r_asset($h['image'])); ?>"
                                     alt="" loading="lazy">
                            <?php endif; ?>
                            <div class="vvus-highlight__body">
                                <span class="vvus-chip">
                                    <i class="<?php echo r_e(r_icon($h['icon'], 'fa-award')); ?>" aria-hidden="true"></i>
                                    <?php echo r_e(ucfirst($h['highlight_type'])); ?>
                                </span>
                                <h3><?php echo r_e($h['title']); ?></h3>
                                <?php if (!empty($h['subtitle'])): ?>
                                    <p class="vvus-highlight__sub"><?php echo r_e($h['subtitle']); ?></p>
                                <?php endif; ?>
                                <?php if (!empty($h['description'])): ?>
                                    <p><?php echo r_rich($h['description']); ?></p>
                                <?php endif; ?>

                                <div class="vvus-highlight__foot">
                                    <?php if (!empty($h['amount'])): ?>
                                        <span class="vvus-highlight__amount"><?php echo r_e($h['amount']); ?></span>
                                    <?php endif; ?>
                                    <?php if (!empty($h['funder'])): ?>
                                        <span><i class="fa-solid fa-hand-holding-dollar" aria-hidden="true"></i> <?php echo r_e($h['funder']); ?></span>
                                    <?php endif; ?>
                                    <?php if (!empty($h['date_label'])): ?>
                                        <span><i class="fa-solid fa-calendar" aria-hidden="true"></i> <?php echo r_e($h['date_label']); ?></span>
                                    <?php endif; ?>
                                    <?php if (!empty($h['scholar_slug'])): ?>
                                        <a href="scholar.php?p=<?php echo r_e(rawurlencode($h['scholar_slug'])); ?>"
                                           style="color:var(--acc);font-weight:700">
                                            <?php echo r_e($h['scholar_name']); ?>
                                        </a>
                                    <?php endif; ?>
                                    <?php if (!empty($h['link'])): ?>
                                        <a href="<?php echo r_e(r_asset($h['link'])); ?>" style="color:var(--acc);font-weight:700">
                                            <?php echo r_e($h['link_text'] ?: 'Read more'); ?>
                                            <i class="fa-solid fa-arrow-right" aria-hidden="true"></i>
                                        </a>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </article>
                    <?php endforeach; ?>
                </div>
            </div>
        </section>
    <?php endif; ?>

    <!-- ==================================================================
         COLLABORATIONS & FUNDERS
         ================================================================== -->
    <?php if ($partners && ($sec = r_section($pdo, 'partners'))): ?>
        <section class="vvus__band" id="partners" aria-labelledby="partners-h">
            <div class="vvus__wrap">
                <div class="vvus__head vvus__head--center">
                    <?php if (!empty($sec['section_subtitle'])): ?>
                        <p class="vvus__eyebrow"><?php echo r_e($sec['section_subtitle']); ?></p>
                    <?php endif; ?>
                    <h2 class="vvus__title" id="partners-h"><?php echo r_e($sec['section_title']); ?></h2>
                    <?php if (!empty($sec['section_description'])): ?>
                        <p class="vvus__subtitle"><?php echo r_rich($sec['section_description']); ?></p>
                    <?php endif; ?>
                </div>

                <div class="vvus-partners">
                    <?php foreach ($partners as $partner):
                        $tag  = $partner['url'] ? 'a' : 'div';
                        $attr = $partner['url']
                            ? ' href="' . r_e($partner['url']) . '" target="_blank" rel="noopener noreferrer"'
                            : '';
                    ?>
                        <<?php echo $tag; ?> class="vvus-partner vvus-reveal"<?php echo $attr; ?>>
                            <?php if (!empty($partner['logo'])): ?>
                                <img src="<?php echo r_e(r_asset($partner['logo'])); ?>"
                                     alt="<?php echo r_e($partner['name']); ?>" loading="lazy">
                            <?php else: ?>
                                <i class="fa-solid fa-handshake-angle" aria-hidden="true"
                                   style="font-size:22px;color:var(--ink-faint)"></i>
                            <?php endif; ?>
                            <span class="vvus-partner__name"><?php echo r_e($partner['name']); ?></span>
                            <?php if (!empty($partner['country'])): ?>
                                <span class="vvus-partner__where"><?php echo r_e($partner['country']); ?></span>
                            <?php endif; ?>
                        </<?php echo $tag; ?>>
                    <?php endforeach; ?>
                </div>
            </div>
        </section>
    <?php endif; ?>

    <!-- ==================================================================
         CALL TO ACTION
         ================================================================== -->
    <?php if ($sec = r_section($pdo, 'cta')): ?>
        <section class="vvus__band vvus__band--tight" id="join">
            <div class="vvus__wrap">
                <div class="vvus-cta vvus-reveal">
                    <h2><?php echo r_e(r_set($pdo, 'cta_title', $sec['section_title'])); ?></h2>
                    <?php $ctaSub = r_set($pdo, 'cta_subtitle', ''); ?>
                    <?php if ($ctaSub !== ''): ?>
                        <p><?php echo r_rich($ctaSub); ?></p>
                    <?php endif; ?>

                    <div class="vvus-actions vvus-actions--center">
                        <?php $b1 = r_set($pdo, 'cta_button_text', ''); ?>
                        <?php if ($b1 !== ''): ?>
                            <a class="vvus-btn vvus-btn--gold" href="<?php echo r_e(r_asset(r_set($pdo, 'cta_button_link', '#'))); ?>">
                                <i class="fa-solid fa-id-badge" aria-hidden="true"></i> <?php echo r_e($b1); ?>
                            </a>
                        <?php endif; ?>
                        <?php $b2 = r_set($pdo, 'cta_button_text_2', ''); ?>
                        <?php if ($b2 !== ''): ?>
                            <a class="vvus-btn vvus-btn--light" href="<?php echo r_e(r_asset(r_set($pdo, 'cta_button_link_2', '#'))); ?>">
                                <?php echo r_e($b2); ?> <i class="fa-solid fa-arrow-right" aria-hidden="true"></i>
                            </a>
                        <?php endif; ?>
                    </div>

                    <?php
                    $email = r_set($pdo, 'contact_email', '');
                    $phone = r_set($pdo, 'contact_phone', '');
                    ?>
                    <?php if ($email !== '' || $phone !== ''): ?>
                        <p style="margin-top:22px;font-size:13.5px;color:rgba(232,238,252,.72)">
                            <?php if ($email !== ''): ?>
                                <i class="fa-solid fa-envelope" aria-hidden="true"></i>
                                <a href="mailto:<?php echo r_e($email); ?>" style="color:#f7dc95"><?php echo r_e($email); ?></a>
                            <?php endif; ?>
                            <?php if ($email !== '' && $phone !== ''): ?> &nbsp;·&nbsp; <?php endif; ?>
                            <?php if ($phone !== ''): ?>
                                <i class="fa-solid fa-phone" aria-hidden="true"></i>
                                <a href="tel:<?php echo r_e(preg_replace('/[^0-9+]/', '', $phone)); ?>" style="color:#f7dc95"><?php echo r_e($phone); ?></a>
                            <?php endif; ?>
                        </p>
                    <?php endif; ?>
                </div>
            </div>
        </section>
    <?php endif; ?>

    <!-- A brand-new portal with nothing loaded yet should say so rather than
         render a page of zeros with no explanation. -->
    <?php if (!$hasData): ?>
        <section class="vvus__band vvus__band--tight">
            <div class="vvus__wrap">
                <?php r_empty(
                    'The catalogue is still being populated',
                    'Researcher profiles and publications are added at Admin → Research Portal. Once records are in, every figure on this page fills in automatically.',
                    'fa-database'
                ); ?>
            </div>
        </section>
    <?php endif; ?>

</main>

<?php include __DIR__ . '/../includes/footer.php'; ?>
