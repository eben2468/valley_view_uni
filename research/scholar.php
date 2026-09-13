<?php
/**
 * VVU Scholar — researcher profile.
 *
 *   scholar.php?p=<slug>
 *
 * Identity card, citation metrics, a year-by-year chart of that person's own
 * output, their research areas, co-authors, and the full publication list with
 * paging. A slug that does not resolve gets a proper 404 rather than an empty
 * profile, so a stale link does not look like an empty record.
 */

$vvu_root = '../';

require_once __DIR__ . '/../includes/db_connect.php';
require_once __DIR__ . '/includes/research_partials.php';

$slug    = trim((string) ($_GET['p'] ?? ''));
$scholar = $slug !== '' ? r_scholar_by_slug($pdo, $slug) : null;

if (!$scholar) {
    http_response_code(404);
    $page_title  = 'Researcher not found — VVU Scholar';
    $active_page = 'research';
    include __DIR__ . '/../includes/header.php';
    ?>
    <link rel="stylesheet" href="assets/research.css?v=1.0">
    <main id="main-content" class="vvus">
        <?php r_pagehero('VVU Scholar', 'Researcher not found', '', ['Research Portal' => 'index.php', 'Not found' => null]); ?>
        <?php r_subnav('authors.php'); ?>
        <section class="vvus__band">
            <div class="vvus__wrap">
                <?php r_empty(
                    'That profile is not in the portal',
                    'The link may be out of date, or the profile may have been withdrawn. Browse the directory to find the researcher you were looking for.',
                    'fa-user-slash',
                    'authors.php',
                    'Browse all researchers'
                ); ?>
            </div>
        </section>
    </main>
    <?php
    include __DIR__ . '/../includes/footer.php';
    exit;
}

$page          = max(1, (int) ($_GET['page'] ?? 1));
$scholarAreas  = r_scholar_areas($pdo, $scholar['id']);
$coauthors     = r_coauthors($pdo, $scholar['id'], 8);
$links         = r_scholar_links($scholar);
$grants        = [];

$pubs = r_publications($pdo, [
    'scholar'  => $scholar['id'],
    'sort'     => 'citations',
    'page'     => $page,
    'per_page' => 10,
]);

// Grants and awards credited to this person.
try {
    $stmt = $pdo->prepare(
        "SELECT * FROM research_highlights
          WHERE is_active = 1 AND scholar_id = ?
          ORDER BY display_order, id DESC LIMIT 6"
    );
    $stmt->execute([(int) $scholar['id']]);
    $grants = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (Exception $e) {
    $grants = [];
}

// This person's own output by year, for the small profile chart.
$ownTrend = [];
try {
    $stmt = $pdo->prepare(
        "SELECT p.pub_year AS y, COUNT(*) AS pubs, COALESCE(SUM(p.citations),0) AS cites
           FROM research_publications p
          WHERE p.is_active = 1 AND p.pub_year IS NOT NULL AND p.pub_year > 1900
            AND (p.scholar_id = :id
                 OR p.id IN (SELECT publication_id FROM research_publication_authors WHERE scholar_id = :id2))
          GROUP BY p.pub_year ORDER BY p.pub_year"
    );
    $stmt->execute([':id' => (int) $scholar['id'], ':id2' => (int) $scholar['id']]);
    foreach ($stmt->fetchAll(PDO::FETCH_ASSOC) as $row) {
        $ownTrend[] = [
            'year'         => (int) $row['y'],
            'publications' => (int) $row['pubs'],
            'citations'    => (int) $row['cites'],
        ];
    }
} catch (Exception $e) {
    $ownTrend = [];
}

$fullName    = trim(($scholar['title'] ? $scholar['title'] . ' ' : '') . $scholar['full_name']);
$interests   = array_filter(array_map('trim', explode(',', (string) $scholar['interests'])));
$page_title  = $fullName . ' — VVU Scholar | Valley View University';
$active_page = 'research';

include __DIR__ . '/../includes/header.php';
?>

<link rel="stylesheet" href="assets/research.css?v=1.0">
<?php if ($ownTrend): ?>
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js" defer></script>
<?php endif; ?>
<script src="assets/research.js?v=1.0" defer></script>

<main id="main-content" class="vvus" style="<?php echo r_accent($scholar['unit_color'] ?? 'blue'); ?>">

    <header class="vvus-hero" style="padding-block:56px 44px">
        <?php $heroImage = r_set($pdo, 'hero_image', ''); ?>
        <?php if ($heroImage !== ''): ?>
            <div class="vvus-hero__bg" style="background-image:url('<?php echo r_e(r_asset($heroImage)); ?>')"></div>
        <?php endif; ?>
        <div class="vvus-hero__veil"></div>
        <div class="vvus__wrap">
            <nav class="vvus-crumbs" aria-label="Breadcrumb">
                <a href="index.php">Research Portal</a>
                <span class="vvus-crumbs__sep" aria-hidden="true">/</span>
                <a href="authors.php">Researchers</a>
                <?php if (!empty($scholar['unit_name'])): ?>
                    <span class="vvus-crumbs__sep" aria-hidden="true">/</span>
                    <a href="authors.php?unit=<?php echo (int) $scholar['unit_id']; ?>"><?php echo r_e($scholar['unit_name']); ?></a>
                <?php endif; ?>
                <span class="vvus-crumbs__sep" aria-hidden="true">/</span>
                <span><?php echo r_e($scholar['full_name']); ?></span>
            </nav>
            <h1 style="margin-top:16px;max-width:22ch"><?php echo r_e($fullName); ?></h1>
            <?php if (!empty($scholar['position'])): ?>
                <p class="vvus-hero__lead"><?php echo r_e($scholar['position']); ?></p>
            <?php endif; ?>
        </div>
    </header>

    <?php r_subnav('authors.php'); ?>

    <section class="vvus__band vvus__band--tight">
        <div class="vvus__wrap">
            <div class="vvus-profile">

                <!-- ========================== SIDE ========================== -->
                <aside class="vvus-profile__side">
                    <div class="vvus-idcard">
                        <?php r_avatar($scholar, 'vvus-avatar vvus-avatar--lg'); ?>
                        <h1><?php echo r_e($fullName); ?></h1>
                        <?php if (!empty($scholar['position'])): ?>
                            <p class="vvus-idcard__role"><?php echo r_e($scholar['position']); ?></p>
                        <?php endif; ?>
                        <?php if (!empty($scholar['unit_name'])): ?>
                            <a class="vvus-idcard__unit" href="authors.php?unit=<?php echo (int) $scholar['unit_id']; ?>">
                                <i class="<?php echo r_e(r_icon($scholar['unit_icon'] ?? '', 'fa-building-columns')); ?>" aria-hidden="true"></i>
                                <?php echo r_e($scholar['unit_name']); ?>
                            </a>
                        <?php endif; ?>

                        <?php if (!empty($scholar['email']) || !empty($scholar['phone'])): ?>
                            <div class="vvus-idcard__contact">
                                <?php if (!empty($scholar['email'])): ?>
                                    <a href="mailto:<?php echo r_e($scholar['email']); ?>">
                                        <i class="fa-solid fa-envelope" aria-hidden="true"></i>
                                        <?php echo r_e($scholar['email']); ?>
                                    </a>
                                <?php endif; ?>
                                <?php if (!empty($scholar['phone'])): ?>
                                    <a href="tel:<?php echo r_e(preg_replace('/[^0-9+]/', '', $scholar['phone'])); ?>">
                                        <i class="fa-solid fa-phone" aria-hidden="true"></i>
                                        <?php echo r_e($scholar['phone']); ?>
                                    </a>
                                <?php endif; ?>
                            </div>
                        <?php endif; ?>

                        <?php if ($links): ?>
                            <div class="vvus-profilelinks">
                                <?php foreach ($links as $link): ?>
                                    <a class="vvus-profilelink" href="<?php echo r_e($link['url']); ?>"
                                       target="_blank" rel="noopener noreferrer"
                                       style="<?php echo r_accent($link['color']); ?>">
                                        <i class="<?php echo r_e($link['icon']); ?>" aria-hidden="true"></i>
                                        <?php echo r_e($link['label']); ?>
                                        <i class="fa-solid fa-arrow-up-right-from-square" aria-hidden="true"></i>
                                    </a>
                                <?php endforeach; ?>
                            </div>
                        <?php endif; ?>

                        <?php if (!empty($scholar['last_synced_at'])): ?>
                            <p style="margin-top:16px;font-size:11.5px;color:var(--ink-faint)">
                                Metrics last refreshed
                                <?php echo r_e(date('j M Y', strtotime($scholar['last_synced_at']))); ?>
                            </p>
                        <?php endif; ?>
                    </div>

                    <?php if ($interests): ?>
                        <div class="vvus-card">
                            <div class="vvus-card__head"><h3>Research interests</h3></div>
                            <div class="vvus-card__body">
                                <div class="vvus-person__tags" style="margin-top:0">
                                    <?php foreach ($interests as $tag): ?>
                                        <a class="vvus-tag" href="authors.php?q=<?php echo r_e(rawurlencode($tag)); ?>">
                                            <?php echo r_e($tag); ?>
                                        </a>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                        </div>
                    <?php endif; ?>

                    <?php if ($scholarAreas): ?>
                        <div class="vvus-card">
                            <div class="vvus-card__head"><h3>Research areas</h3></div>
                            <div class="vvus-card__body" style="display:grid;gap:8px">
                                <?php foreach ($scholarAreas as $a): ?>
                                    <a class="vvus-profilelink" href="publications.php?area=<?php echo (int) $a['id']; ?>"
                                       style="<?php echo r_accent($a['color']); ?>">
                                        <i class="<?php echo r_e(r_icon($a['icon'], 'fa-flask')); ?>" aria-hidden="true"></i>
                                        <?php echo r_e($a['name']); ?>
                                    </a>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    <?php endif; ?>
                </aside>

                <!-- ========================== MAIN ========================== -->
                <div style="display:grid;gap:20px;min-width:0">

                    <!-- Metrics -->
                    <div class="vvus-metrics">
                        <div class="vvus-metric vvus-reveal">
                            <b data-count="<?php echo (int) $scholar['citations']; ?>"><?php echo number_format($scholar['citations']); ?></b>
                            <span>Citations</span>
                        </div>
                        <div class="vvus-metric vvus-reveal">
                            <b data-count="<?php echo (int) $scholar['h_index']; ?>"><?php echo (int) $scholar['h_index']; ?></b>
                            <span>h-index</span>
                        </div>
                        <div class="vvus-metric vvus-reveal">
                            <b data-count="<?php echo (int) $scholar['i10_index']; ?>"><?php echo (int) $scholar['i10_index']; ?></b>
                            <span>i10-index</span>
                        </div>
                        <div class="vvus-metric vvus-reveal">
                            <b data-count="<?php echo (int) $scholar['publications_count']; ?>"><?php echo number_format($scholar['publications_count']); ?></b>
                            <span>Publications</span>
                        </div>
                        <?php if ((int) $scholar['citations_5y'] > 0): ?>
                            <div class="vvus-metric vvus-reveal">
                                <b data-count="<?php echo (int) $scholar['citations_5y']; ?>"><?php echo number_format($scholar['citations_5y']); ?></b>
                                <span>Cites (5y)</span>
                            </div>
                        <?php endif; ?>
                        <?php if ((int) $scholar['h_index_5y'] > 0): ?>
                            <div class="vvus-metric vvus-reveal">
                                <b data-count="<?php echo (int) $scholar['h_index_5y']; ?>"><?php echo (int) $scholar['h_index_5y']; ?></b>
                                <span>h-index (5y)</span>
                            </div>
                        <?php endif; ?>
                    </div>

                    <!-- Biography -->
                    <?php if (trim((string) $scholar['bio']) !== ''): ?>
                        <div class="vvus-card vvus-reveal">
                            <div class="vvus-card__head"><h3>About</h3></div>
                            <div class="vvus-card__body">
                                <div class="vvus-prose"><?php echo nl2br(r_rich($scholar['bio'])); ?></div>
                            </div>
                        </div>
                    <?php endif; ?>

                    <!-- Output by year -->
                    <?php if ($ownTrend): ?>
                        <div class="vvus-card vvus-reveal">
                            <div class="vvus-card__head">
                                <h3>Output by year</h3>
                                <div class="vvus-legend">
                                    <span><i style="background:#1d4ed8"></i> Publications</span>
                                    <span><i style="background:#a16207"></i> Citations</span>
                                </div>
                            </div>
                            <div class="vvus-card__body">
                                <div class="vvus-chart" style="height:250px">
                                    <canvas id="vvusTrendChart"
                                            data-series='<?php echo r_e(json_encode($ownTrend, JSON_UNESCAPED_SLASHES)); ?>'
                                            role="img"
                                            aria-label="Publications and citations by year for <?php echo r_e($fullName); ?>"></canvas>
                                </div>
                            </div>
                        </div>
                    <?php endif; ?>

                    <!-- Grants and awards -->
                    <?php if ($grants): ?>
                        <div class="vvus-card vvus-reveal">
                            <div class="vvus-card__head"><h3>Grants, awards &amp; projects</h3></div>
                            <div class="vvus-card__body" style="display:grid;gap:12px">
                                <?php foreach ($grants as $g): ?>
                                    <div style="<?php echo r_accent($g['color']); ?>display:flex;gap:14px;align-items:flex-start;padding:14px 16px;border:1px solid var(--line);border-radius:10px">
                                        <span style="flex:0 0 auto;width:38px;height:38px;display:grid;place-items:center;border-radius:10px;background:color-mix(in srgb,var(--acc) 12%,transparent);color:var(--acc)">
                                            <i class="<?php echo r_e(r_icon($g['icon'], 'fa-award')); ?>" aria-hidden="true"></i>
                                        </span>
                                        <div style="min-width:0">
                                            <strong style="display:block;font-size:14.5px;color:var(--navy);line-height:1.4"><?php echo r_e($g['title']); ?></strong>
                                            <?php if (!empty($g['description'])): ?>
                                                <p style="margin-top:6px;font-size:13px;line-height:1.65;color:var(--ink-soft)"><?php echo r_rich($g['description']); ?></p>
                                            <?php endif; ?>
                                            <p style="margin-top:8px;font-size:12.5px;color:var(--ink-muted)">
                                                <?php echo r_e(implode(' · ', array_filter([$g['funder'], $g['amount'], $g['date_label']]))); ?>
                                            </p>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    <?php endif; ?>

                    <!-- Co-authors -->
                    <?php if ($coauthors): ?>
                        <div class="vvus-card vvus-reveal">
                            <div class="vvus-card__head">
                                <h3>Publishes with</h3>
                                <p style="margin-left:auto;font-size:12.5px;color:var(--ink-muted)">
                                    Colleagues on shared papers
                                </p>
                            </div>
                            <div class="vvus-card__body">
                                <div class="vvus-coauthors">
                                    <?php foreach ($coauthors as $c): ?>
                                        <a class="vvus-coauthor" href="scholar.php?p=<?php echo r_e(rawurlencode($c['slug'])); ?>">
                                            <?php r_avatar($c, 'vvus-avatar vvus-avatar--sm'); ?>
                                            <span style="min-width:0">
                                                <b><?php echo r_e(trim(($c['title'] ? $c['title'] . ' ' : '') . $c['full_name'])); ?></b>
                                                <small><?php echo (int) $c['shared']; ?> shared publication<?php echo (int) $c['shared'] === 1 ? '' : 's'; ?></small>
                                            </span>
                                        </a>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                        </div>
                    <?php endif; ?>

                    <!-- Publications -->
                    <div class="vvus-card vvus-reveal">
                        <div class="vvus-card__head">
                            <h3>Publications</h3>
                            <p style="margin-left:auto;font-size:12.5px;color:var(--ink-muted)">
                                <?php echo number_format($pubs['total']); ?> indexed · most cited first
                            </p>
                        </div>
                        <div class="vvus-card__body">
                            <?php if ($pubs['rows']): ?>
                                <div class="vvus-pubs">
                                    <?php foreach ($pubs['rows'] as $p) { r_publication_row($p); } ?>
                                </div>
                                <?php r_pager($pubs['page'], $pubs['pages'], ['p' => $slug]); ?>
                            <?php else: ?>
                                <?php r_empty(
                                    'No publications indexed yet',
                                    'This profile carries citation metrics but its individual papers have not been loaded into the catalogue. Linked Google Scholar or ORCID records above remain the fullest list.',
                                    'fa-book-open'
                                ); ?>
                            <?php endif; ?>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </section>

</main>

<?php include __DIR__ . '/../includes/footer.php'; ?>
