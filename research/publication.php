<?php
/**
 * VVU Scholar — one publication.
 *
 *   publication.php?id=<id>
 *
 * The full record (abstract, authors, venue, identifiers), its impact panel
 * (citations, reads, recommendations, rating, readers, Altmetric, FWCI,
 * citation percentile), recommend / rate & review, citation export and more
 * work by the same VVU authors. Opening the page counts as one read per
 * visitor per day.
 */

$vvu_root = '../';

require_once __DIR__ . '/../includes/session_bootstrap.php';
require_once __DIR__ . '/../includes/db_connect.php';
require_once __DIR__ . '/includes/research_partials.php';
require_once __DIR__ . '/includes/research_engage.php';

$id  = (int) ($_GET['id'] ?? 0);
$pub = null;
if ($id > 0) {
    try {
        $st = $pdo->prepare(
            "SELECT p.*, u.name AS unit_name, u.slug AS unit_slug,
                    a.name AS area_name, a.color AS area_color, a.id AS area_ref,
                    s.full_name AS scholar_name, s.slug AS scholar_slug
               FROM research_publications p
               LEFT JOIN research_units u ON u.id = p.unit_id
               LEFT JOIN research_areas a ON a.id = p.area_id
               LEFT JOIN research_scholars s ON s.id = p.scholar_id
              WHERE p.id = ? AND p.is_active = 1"
        );
        $st->execute([$id]);
        $pub = $st->fetch(PDO::FETCH_ASSOC) ?: null;
    } catch (Exception $e) {
        $pub = null;
    }
}

$active_page = 'research';

if (!$pub) {
    http_response_code(404);
    $page_title = 'Publication not found — VVU Scholar';
    include __DIR__ . '/../includes/header.php';
    ?>
    <link rel="stylesheet" href="assets/research.css?v=1.2">
    <main id="main-content" class="vvus">
        <?php r_pagehero('VVU Scholar', 'Publication not found', '', ['Research Portal' => 'index.php', 'Not found' => null]); ?>
        <?php r_subnav('publications.php'); ?>
        <section class="vvus__band"><div class="vvus__wrap">
            <?php r_empty('That publication is not in the portal',
                'It may have been withdrawn, or merged with another record of the same work.',
                'fa-book-open', 'publications.php', 'Browse all publications'); ?>
        </div></section>
    </main>
    <?php
    include __DIR__ . '/../includes/footer.php';
    exit;
}

r_record_view($pdo, $pub['id']);
r_autosync_tick($pdo);
// Re-read the counters so this visit is included.
try {
    $st = $pdo->prepare("SELECT * FROM research_publications WHERE id = ?");
    $st->execute([$pub['id']]);
    $pub = array_merge($pub, $st->fetch(PDO::FETCH_ASSOC) ?: []);
} catch (Exception $e) {
}

// VVU authors of this work.
$vvuAuthors = [];
try {
    $st = $pdo->prepare(
        "SELECT s.id, s.full_name, s.slug, s.title, s.photo, s.position, un.name AS unit_name
           FROM research_publication_authors a
           JOIN research_scholars s ON s.id = a.scholar_id AND s.is_active = 1
           LEFT JOIN research_units un ON un.id = s.unit_id
          WHERE a.publication_id = ?
          ORDER BY a.author_order, s.full_name"
    );
    $st->execute([$pub['id']]);
    $vvuAuthors = $st->fetchAll(PDO::FETCH_ASSOC);
} catch (Exception $e) {
}

// More from the same VVU authors, then from the same research area.
$related = [];
try {
    $ids = array_map(static function ($a) { return (int) $a['id']; }, $vvuAuthors);
    if ($ids) {
        $in = implode(',', $ids);
        $related = $pdo->query(
            "SELECT DISTINCT p.*, s.full_name AS scholar_name, s.slug AS scholar_slug, ar.name AS area_name, ar.color AS area_color
               FROM research_publications p
               JOIN research_publication_authors x ON x.publication_id = p.id AND x.scholar_id IN ($in)
               LEFT JOIN research_scholars s ON s.id = p.scholar_id
               LEFT JOIN research_areas ar ON ar.id = p.area_id
              WHERE p.is_active = 1 AND p.id <> " . (int) $pub['id'] . "
              ORDER BY p.citations DESC, p.pub_year DESC LIMIT 4"
        )->fetchAll(PDO::FETCH_ASSOC);
    }
    if (count($related) < 4 && !empty($pub['area_id'])) {
        $have = array_merge([(int) $pub['id']], array_map(static function ($r) { return (int) $r['id']; }, $related));
        $more = $pdo->query(
            "SELECT p.*, s.full_name AS scholar_name, s.slug AS scholar_slug, ar.name AS area_name, ar.color AS area_color
               FROM research_publications p
               LEFT JOIN research_scholars s ON s.id = p.scholar_id
               LEFT JOIN research_areas ar ON ar.id = p.area_id
              WHERE p.is_active = 1 AND p.area_id = " . (int) $pub['area_id'] . "
                AND p.id NOT IN (" . implode(',', $have) . ")
              ORDER BY p.citations DESC LIMIT " . (4 - count($related))
        )->fetchAll(PDO::FETCH_ASSOC);
        $related = array_merge($related, $more);
    }
} catch (Exception $e) {
    $related = [];
}

$types     = r_publication_types();
$pubLink      = r_publication_link($pub);
$impact    = r_publication_impact($pub);
$rating    = r_rating($pub);
$reviews   = r_reviews($pdo, $pub['id']);
$recommended = r_has_recommended($pdo, $pub['id']);
$ready     = r_engage_ready($pdo);
$flash     = $_SESSION['r_engage_flash'] ?? null;
unset($_SESSION['r_engage_flash']);
$keywords  = array_filter(array_map('trim', explode(',', (string) $pub['keywords'])));

$page_title = mb_substr($pub['title'], 0, 120) . ' — VVU Scholar';
include __DIR__ . '/../includes/header.php';
?>
<link rel="stylesheet" href="assets/research.css?v=1.2">
<script src="assets/research.js?v=1.1" defer></script>

<main id="main-content" class="vvus" style="<?php echo r_accent($pub['area_color'] ?? 'blue'); ?>">
    <header class="vvus-hero" style="padding-block:56px 44px">
        <div class="vvus-hero__veil"></div>
        <div class="vvus__wrap">
            <nav class="vvus-crumbs" aria-label="Breadcrumb">
                <a href="index.php">Research Portal</a>
                <span class="vvus-crumbs__sep" aria-hidden="true">/</span>
                <a href="publications.php">Publications</a>
                <?php if (!empty($pub['area_name'])): ?>
                    <span class="vvus-crumbs__sep" aria-hidden="true">/</span>
                    <a href="publications.php?area=<?php echo (int) $pub['area_ref']; ?>"><?php echo r_e($pub['area_name']); ?></a>
                <?php endif; ?>
            </nav>
            <div class="vvus-pub__meta" style="margin-top:18px">
                <span class="vvus-chip"><?php echo r_e($types[$pub['pub_type']] ?? 'Publication'); ?></span>
                <?php if (!empty($pub['pub_year'])): ?><span class="vvus-chip vvus-chip--plain"><?php echo (int) $pub['pub_year']; ?></span><?php endif; ?>
                <?php if (!empty($pub['is_open_access'])): ?><span class="vvus-chip vvus-chip--oa"><i class="fa-solid fa-unlock" aria-hidden="true"></i> Open Access</span><?php endif; ?>
            </div>
            <h1 class="vvus-pubpage__title"><?php echo r_e($pub['title']); ?></h1>
            <?php if (!empty($pub['authors'])): ?>
                <p class="vvus-hero__lead"><?php echo r_e($pub['authors']); ?></p>
            <?php endif; ?>
        </div>
    </header>

    <?php r_subnav('publications.php'); ?>

    <section class="vvus__band vvus__band--tight">
        <div class="vvus__wrap">
            <?php if ($flash): ?>
                <div class="vvus-notice vvus-notice--<?php echo $flash[0] ? 'ok' : 'error'; ?>" role="status"><?php echo r_e($flash[1]); ?></div>
            <?php endif; ?>

            <div class="vvus-pubpage">
                <!-- ======================= MAIN ======================= -->
                <div class="vvus-pubpage__main">
                    <div class="vvus-card">
                        <div class="vvus-card__body">
                            <?php if (!empty($pub['venue'])): ?>
                                <p class="vvus-pubpage__venue">
                                    <i class="fa-solid fa-book" aria-hidden="true"></i>
                                    <span>
                                        <strong><?php echo r_e($pub['venue']); ?></strong><?php
                                        if (!empty($pub['volume'])) { echo ', ' . r_e($pub['volume']); if (!empty($pub['issue'])) { echo '(' . r_e($pub['issue']) . ')'; } }
                                        if (!empty($pub['pages'])) { echo ', ' . r_e($pub['pages']); }
                                        ?><?php if (!empty($pub['publisher'])): ?> · <?php echo r_e($pub['publisher']); ?><?php endif; ?>
                                    </span>
                                </p>
                            <?php endif; ?>

                            <div class="vvus-pubpage__actions">
                                <?php if ($pubLink !== ''): ?>
                                    <a class="vvus-btn vvus-btn--gold" href="<?php echo r_e($pubLink); ?>" target="_blank" rel="noopener noreferrer">
                                        <i class="fa-solid fa-arrow-up-right-from-square" aria-hidden="true"></i> Read the full text</a>
                                <?php endif; ?>
                                <?php if (!empty($pub['pdf_path'])): ?>
                                    <a class="vvus-btn vvus-btn--ghost" href="<?php echo r_e(r_asset($pub['pdf_path'])); ?>" target="_blank" rel="noopener">
                                        <i class="fa-solid fa-file-pdf" aria-hidden="true"></i> PDF</a>
                                <?php endif; ?>
                                <?php if ($ready): ?>
                                    <form method="post" action="engage.php" class="vvus-recommend" data-recommend>
                                        <?php echo vvu_csrf_field(); ?>
                                        <input type="hidden" name="action" value="recommend">
                                        <input type="hidden" name="id" value="<?php echo (int) $pub['id']; ?>">
                                        <button type="submit" class="vvus-btn vvus-btn--ghost<?php echo $recommended ? ' is-on' : ''; ?>" aria-pressed="<?php echo $recommended ? 'true' : 'false'; ?>">
                                            <i class="fa-solid fa-thumbs-up" aria-hidden="true"></i>
                                            <span data-recommend-label><?php echo $recommended ? 'Recommended' : 'Recommend'; ?></span>
                                            <b data-recommend-count><?php echo (int) $pub['recommendations']; ?></b>
                                        </button>
                                    </form>
                                <?php endif; ?>
                                <button type="button" class="vvus-btn vvus-btn--ghost" data-copy="<?php echo r_e(r_cite_apa($pub)); ?>" data-copied="Citation copied">
                                    <i class="fa-solid fa-quote-right" aria-hidden="true"></i> Cite (APA)</button>
                                <button type="button" class="vvus-btn vvus-btn--ghost" data-copy="<?php echo r_e(r_cite_bibtex($pub)); ?>" data-copied="BibTeX copied">
                                    <i class="fa-solid fa-code" aria-hidden="true"></i> BibTeX</button>
                            </div>

                            <?php if (trim((string) $pub['abstract']) !== ''): ?>
                                <h2 class="vvus-pubpage__h">Abstract</h2>
                                <div class="vvus-prose"><p><?php echo nl2br(r_e($pub['abstract'])); ?></p></div>
                            <?php endif; ?>

                            <?php if ($keywords): ?>
                                <h2 class="vvus-pubpage__h">Keywords</h2>
                                <div class="vvus-person__tags" style="margin-top:0">
                                    <?php foreach ($keywords as $k): ?>
                                        <a class="vvus-tag" href="publications.php?q=<?php echo r_e(rawurlencode($k)); ?>"><?php echo r_e($k); ?></a>
                                    <?php endforeach; ?>
                                </div>
                            <?php endif; ?>

                            <dl class="vvus-pubpage__ids">
                                <?php if (!empty($pub['doi'])): ?>
                                    <div><dt>DOI</dt><dd><a href="https://doi.org/<?php echo r_e(ltrim($pub['doi'], '/')); ?>" target="_blank" rel="noopener noreferrer"><?php echo r_e($pub['doi']); ?></a></dd></div>
                                <?php endif; ?>
                                <?php if (!empty($pub['unit_name'])): ?>
                                    <div><dt>Faculty</dt><dd><a href="publications.php?unit=<?php echo (int) $pub['unit_id']; ?>"><?php echo r_e($pub['unit_name']); ?></a></dd></div>
                                <?php endif; ?>
                                <?php if (!empty($pub['area_name'])): ?>
                                    <div><dt>Research area</dt><dd><a href="publications.php?area=<?php echo (int) $pub['area_ref']; ?>"><?php echo r_e($pub['area_name']); ?></a></dd></div>
                                <?php endif; ?>
                            </dl>
                        </div>
                    </div>

                    <?php if ($vvuAuthors): ?>
                        <div class="vvus-card">
                            <div class="vvus-card__head"><h3>VVU authors</h3></div>
                            <div class="vvus-card__body vvus-pubpage__authors">
                                <?php foreach ($vvuAuthors as $au): ?>
                                    <a class="vvus-pubpage__author" href="scholar.php?p=<?php echo r_e(rawurlencode($au['slug'])); ?>">
                                        <?php r_avatar($au, 'vvus-avatar vvus-avatar--sm'); ?>
                                        <span>
                                            <strong><?php echo r_e(trim(($au['title'] ? $au['title'] . ' ' : '') . $au['full_name'])); ?></strong>
                                            <small><?php echo r_e($au['unit_name'] ?: ($au['position'] ?: 'Valley View University')); ?></small>
                                        </span>
                                    </a>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    <?php endif; ?>

                    <!-- Reviews -->
                    <div class="vvus-card" id="reviews">
                        <div class="vvus-card__head">
                            <h3>Ratings &amp; reviews</h3>
                            <?php if ($rating !== null): ?>
                                <span class="vvus-ratingline"><?php echo r_stars($rating); ?> <b><?php echo number_format($rating, 1); ?></b> from <?php echo (int) $pub['rating_count']; ?> reader<?php echo (int) $pub['rating_count'] === 1 ? '' : 's'; ?></span>
                            <?php endif; ?>
                        </div>
                        <div class="vvus-card__body">
                            <?php if ($reviews): ?>
                                <ul class="vvus-reviews">
                                    <?php foreach ($reviews as $rv): ?>
                                        <li>
                                            <div class="vvus-reviews__head">
                                                <?php echo r_stars($rv['rating'], 'sm'); ?>
                                                <strong><?php echo r_e($rv['reviewer_name']); ?></strong>
                                                <?php if ($rv['reviewer_role']): ?><small><?php echo r_e($rv['reviewer_role']); ?></small><?php endif; ?>
                                                <time><?php echo r_e(date('j M Y', strtotime($rv['created_at']))); ?></time>
                                            </div>
                                            <p><?php echo nl2br(r_e($rv['comment'])); ?></p>
                                        </li>
                                    <?php endforeach; ?>
                                </ul>
                            <?php elseif ($rating === null): ?>
                                <p class="vvus-muted">No ratings yet. Have you read it? Be the first to rate it.</p>
                            <?php endif; ?>

                            <?php if ($ready): ?>
                                <details class="vvus-reviewform" <?php echo !$reviews && $rating === null ? 'open' : ''; ?>>
                                    <summary><i class="fa-solid fa-star" aria-hidden="true"></i> Rate &amp; review this publication</summary>
                                    <form method="post" action="engage.php#reviews">
                                        <?php echo vvu_csrf_field(); ?>
                                        <input type="hidden" name="action" value="review">
                                        <input type="hidden" name="id" value="<?php echo (int) $pub['id']; ?>">
                                        <fieldset class="vvus-starpick">
                                            <legend>Your rating</legend>
                                            <?php for ($i = 5; $i >= 1; $i--): ?>
                                                <input type="radio" id="star<?php echo $i; ?>" name="rating" value="<?php echo $i; ?>" required>
                                                <label for="star<?php echo $i; ?>" title="<?php echo $i; ?> star<?php echo $i > 1 ? 's' : ''; ?>"><i class="fa-solid fa-star" aria-hidden="true"></i><span class="sr-only"><?php echo $i; ?> stars</span></label>
                                            <?php endfor; ?>
                                        </fieldset>
                                        <div class="vvus-reviewform__grid">
                                            <div class="vvus-field">
                                                <label for="rv-name">Your name</label>
                                                <input id="rv-name" class="vvus-input browser-default" name="reviewer_name" maxlength="120" required>
                                            </div>
                                            <div class="vvus-field">
                                                <label for="rv-role">Role / institution <span style="text-transform:none;letter-spacing:0">(optional)</span></label>
                                                <input id="rv-role" class="vvus-input browser-default" name="reviewer_role" maxlength="120" placeholder="e.g. Lecturer, University of Ghana">
                                            </div>
                                        </div>
                                        <div class="vvus-field">
                                            <label for="rv-text">Review <span style="text-transform:none;letter-spacing:0">(optional)</span></label>
                                            <textarea id="rv-text" class="vvus-input browser-default" name="comment" rows="4" maxlength="2000"
                                                      style="height:auto;padding:10px 14px;line-height:1.55"
                                                      placeholder="What did you find useful? How did you use this work?"></textarea>
                                        </div>
                                        <div class="vvus-hp" aria-hidden="true"><label>Leave empty <input type="text" name="website_url" tabindex="-1" autocomplete="off"></label></div>
                                        <button class="vvus-btn vvus-btn--gold" type="submit"><i class="fa-solid fa-paper-plane" aria-hidden="true"></i> Submit</button>
                                        <p class="vvus-muted" style="margin-top:8px;font-size:12.5px">Written reviews are checked by the Research Office before they appear.</p>
                                    </form>
                                </details>
                            <?php endif; ?>
                        </div>
                    </div>

                    <?php if ($related): ?>
                        <div class="vvus-card">
                            <div class="vvus-card__head"><h3>Related publications</h3></div>
                            <div class="vvus-card__body" style="display:grid;gap:12px">
                                <?php foreach ($related as $rp) { r_publication_row($rp, ['abstract' => false]); } ?>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>

                <!-- ======================= SIDE ======================= -->
                <aside class="vvus-pubpage__side">
                    <div class="vvus-card">
                        <div class="vvus-card__head"><h3>Impact</h3></div>
                        <div class="vvus-card__body">
                            <ul class="vvus-impact">
                                <?php foreach ($impact as $m): ?>
                                    <li title="<?php echo r_e($m[3]); ?>">
                                        <i class="fa-solid <?php echo r_e($m[2]); ?>" aria-hidden="true"></i>
                                        <b><?php echo r_e($m[1]); ?></b>
                                        <span><?php echo r_e($m[0]); ?></span>
                                        <small><?php echo r_e($m[3]); ?></small>
                                    </li>
                                <?php endforeach; ?>
                            </ul>
                            <?php if (!empty($pub['doi'])): ?>
                                <a class="vvus-impact__more" href="https://www.altmetric.com/details/doi/<?php echo r_e(ltrim($pub['doi'], '/')); ?>" target="_blank" rel="noopener noreferrer">
                                    Online attention on Altmetric <i class="fa-solid fa-arrow-up-right-from-square" aria-hidden="true"></i></a>
                            <?php endif; ?>
                            <?php if (!empty($pub['metrics_synced_at'])): ?>
                                <p class="vvus-muted" style="margin-top:10px;font-size:11.5px">External figures updated <?php echo r_e(date('j M Y', strtotime($pub['metrics_synced_at']))); ?>.</p>
                            <?php endif; ?>
                        </div>
                    </div>
                </aside>
            </div>
        </div>
    </section>
</main>
<script src="assets/research-engage.js?v=1.0" defer></script>
<?php include __DIR__ . '/../includes/footer.php'; ?>
