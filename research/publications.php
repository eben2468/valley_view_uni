<?php
/**
 * VVU Scholar — publication catalogue.
 *
 * Full-text search across title, byline, venue, keywords, abstract and DOI,
 * narrowed by faculty, area, type, year and open-access status. Each filter is
 * a GET parameter so a narrowed view is a URL a lecturer can paste into a
 * departmental report.
 */

$vvu_root = '../';

require_once __DIR__ . '/../includes/db_connect.php';
require_once __DIR__ . '/includes/research_partials.php';

$q        = trim((string) ($_GET['q'] ?? ''));
$unitId   = (int) ($_GET['unit'] ?? 0);
$areaId   = (int) ($_GET['area'] ?? 0);
$type     = (string) ($_GET['type'] ?? '');
$year     = (int) ($_GET['year'] ?? 0);
$openOnly = !empty($_GET['open_access']);
$sort     = (string) ($_GET['sort'] ?? 'recent');
$page     = max(1, (int) ($_GET['page'] ?? 1));
$perPage  = 10;

$units = r_units($pdo);
$areas = r_areas($pdo);
$years = r_publication_years($pdo);
$types = r_publication_types();

// A search suggestion links straight to one record; honour it by narrowing to
// that row rather than dumping the visitor into an unfiltered list.
$focus = (int) ($_GET['focus'] ?? 0);
if ($focus > 0) {
    $stmt = $pdo->prepare("SELECT title FROM research_publications WHERE id = ? AND is_active = 1");
    $stmt->execute([$focus]);
    $focusTitle = $stmt->fetchColumn();
    if ($focusTitle) {
        $q = (string) $focusTitle;
    }
}

// An area can also arrive by slug, which is what the type-ahead emits.
if (!$areaId && !empty($_GET['area_slug'])) {
    foreach ($areas as $a) {
        if ($a['slug'] === $_GET['area_slug']) { $areaId = (int) $a['id']; }
    }
}

$result = r_publications($pdo, [
    'q'           => $q,
    'unit'        => $unitId,
    'area'        => $areaId,
    'type'        => $type,
    'year'        => $year,
    'open_access' => $openOnly ? 1 : 0,
    'sort'        => $sort,
    'page'        => $page,
    'per_page'    => $perPage,
]);

$activeUnit = null;
foreach ($units as $u) { if ((int) $u['id'] === $unitId) { $activeUnit = $u; } }
$activeArea = null;
foreach ($areas as $a) { if ((int) $a['id'] === $areaId) { $activeArea = $a; } }

$heading = 'Publications';
if ($activeArea) { $heading = $activeArea['name']; }
elseif ($activeUnit) { $heading = $activeUnit['name']; }

$page_title  = $heading . ' — VVU Scholar | Valley View University';
$active_page = 'research';

$sortLabels = [
    'recent'    => 'Newest first',
    'citations' => 'Most cited',
    'oldest'    => 'Oldest first',
    'title'     => 'Title (A–Z)',
];

$hasFilter = $q !== '' || $unitId || $areaId || $type !== '' || $year || $openOnly || $sort !== 'recent';

include __DIR__ . '/../includes/header.php';
?>

<link rel="stylesheet" href="assets/research.css?v=1.0">
<script src="assets/research.js?v=1.0" defer></script>

<main id="main-content" class="vvus">

    <?php r_pagehero(
        'VVU Scholar',
        $heading,
        $activeArea
            ? ($activeArea['description'] ?: 'Work published in this research area.')
            : ($activeUnit
                ? ($activeUnit['description'] ?: 'Work published by this faculty.')
                : 'Journal articles, conference papers, books, chapters and theses indexed for Valley View University.'),
        ['Research Portal' => 'index.php', $heading => null],
        r_set($pdo, 'hero_image', '')
    ); ?>

    <?php r_subnav('publications.php'); ?>

    <section class="vvus__band vvus__band--tight">
        <div class="vvus__wrap">

            <form class="vvus-filters" method="get" action="publications.php" data-autosubmit>
                <div class="vvus-field">
                    <label for="p-q">Search</label>
                    <input class="vvus-input browser-default" id="p-q" type="search" name="q"
                           value="<?php echo r_e($q); ?>"
                           placeholder="Title, author, journal, keyword or DOI">
                </div>

                <div class="vvus-field">
                    <label for="p-unit">Faculty</label>
                    <select class="vvus-select browser-default" id="p-unit" name="unit">
                        <option value="">All faculties</option>
                        <?php foreach ($units as $u): ?>
                            <option value="<?php echo (int) $u['id']; ?>" <?php echo $unitId === (int) $u['id'] ? 'selected' : ''; ?>>
                                <?php echo r_e($u['name']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="vvus-field">
                    <label for="p-area">Area</label>
                    <select class="vvus-select browser-default" id="p-area" name="area">
                        <option value="">All areas</option>
                        <?php foreach ($areas as $a): ?>
                            <option value="<?php echo (int) $a['id']; ?>" <?php echo $areaId === (int) $a['id'] ? 'selected' : ''; ?>>
                                <?php echo r_e($a['name']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="vvus-field">
                    <label for="p-type">Type</label>
                    <select class="vvus-select browser-default" id="p-type" name="type">
                        <option value="">All types</option>
                        <?php foreach ($types as $key => $label): ?>
                            <option value="<?php echo r_e($key); ?>" <?php echo $type === $key ? 'selected' : ''; ?>>
                                <?php echo r_e($label); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="vvus-field">
                    <label for="p-year">Year</label>
                    <select class="vvus-select browser-default" id="p-year" name="year">
                        <option value="">Any year</option>
                        <?php foreach ($years as $y): ?>
                            <option value="<?php echo (int) $y; ?>" <?php echo $year === (int) $y ? 'selected' : ''; ?>>
                                <?php echo (int) $y; ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="vvus-field">
                    <label for="p-sort">Sort by</label>
                    <select class="vvus-select browser-default" id="p-sort" name="sort">
                        <?php foreach ($sortLabels as $key => $label): ?>
                            <option value="<?php echo r_e($key); ?>" <?php echo $sort === $key ? 'selected' : ''; ?>>
                                <?php echo r_e($label); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="vvus-field vvus-field--go">
                    <button class="vvus-btn" type="submit">
                        <i class="fa-solid fa-filter" aria-hidden="true"></i> Apply
                    </button>
                </div>
                <?php if ($openOnly): ?>
                    <input type="hidden" name="open_access" value="1">
                <?php endif; ?>
            </form>

            <div class="vvus-resultbar">
                <p class="vvus-resultbar__count">
                    <b><?php echo number_format($result['total']); ?></b>
                    publication<?php echo $result['total'] === 1 ? '' : 's'; ?>
                    <?php if ($q !== ''): ?> matching &ldquo;<?php echo r_e(mb_substr($q, 0, 60)); ?><?php echo mb_strlen($q) > 60 ? '…' : ''; ?>&rdquo;<?php endif; ?>
                </p>
                <div class="vvus-resultbar__right">
                    <a class="vvus-btn <?php echo $openOnly ? '' : 'vvus-btn--ghost'; ?> vvus-btn--sm"
                       href="<?php echo r_e(r_qs(['open_access' => $openOnly ? '' : 1, 'page' => ''])); ?>">
                        <i class="fa-solid fa-unlock" aria-hidden="true"></i>
                        <?php echo $openOnly ? 'Open access only' : 'Open access'; ?>
                    </a>
                    <?php if ($hasFilter): ?>
                        <a class="vvus-btn vvus-btn--ghost vvus-btn--sm" href="publications.php">
                            <i class="fa-solid fa-xmark" aria-hidden="true"></i> Clear
                        </a>
                    <?php endif; ?>
                </div>
            </div>

            <?php if ($result['rows']): ?>
                <div class="vvus-pubs">
                    <?php foreach ($result['rows'] as $p) { r_publication_row($p); } ?>
                </div>
                <?php r_pager($result['page'], $result['pages'], [
                    'q' => $q, 'unit' => $unitId ?: '', 'area' => $areaId ?: '',
                    'type' => $type, 'year' => $year ?: '', 'sort' => $sort,
                    'open_access' => $openOnly ? 1 : '',
                ]); ?>
            <?php else: ?>
                <?php r_empty(
                    'Nothing in the catalogue matched',
                    'Try fewer words, drop the year filter, or search on a DOI. Newly published work appears here once it has been indexed.',
                    'fa-book-open',
                    'publications.php',
                    'Show the full catalogue'
                ); ?>
            <?php endif; ?>

        </div>
    </section>

</main>

<?php include __DIR__ . '/../includes/footer.php'; ?>
