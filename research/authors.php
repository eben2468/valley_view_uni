<?php
/**
 * VVU Scholar — researcher directory.
 *
 * Search, filter by faculty and research area, sort by any of the four
 * metrics, page through the result. Every filter is a plain GET parameter, so
 * a filtered view is a shareable URL and the whole page works without
 * JavaScript; research.js only adds submit-on-change to the selects.
 */

$vvu_root = '../';

require_once __DIR__ . '/../includes/db_connect.php';
require_once __DIR__ . '/includes/research_partials.php';

$q        = trim((string) ($_GET['q'] ?? ''));
$unitId   = (int) ($_GET['unit'] ?? 0);
$areaId   = (int) ($_GET['area'] ?? 0);
$sort     = (string) ($_GET['sort'] ?? 'citations');
$page     = max(1, (int) ($_GET['page'] ?? 1));
$perPage  = 12;

$units = r_units($pdo);
$areas = r_areas($pdo);

$result = r_scholars($pdo, [
    'q'        => $q,
    'unit'     => $unitId,
    'area'     => $areaId,
    'sort'     => $sort,
    'page'     => $page,
    'per_page' => $perPage,
]);

// Name the current filter in the heading so the page never just says
// "Researchers" over a list that is actually one faculty.
$activeUnit = null;
foreach ($units as $u) {
    if ((int) $u['id'] === $unitId) { $activeUnit = $u; }
}
$activeArea = null;
foreach ($areas as $a) {
    if ((int) $a['id'] === $areaId) { $activeArea = $a; }
}

$heading = 'Researchers';
if ($activeUnit) { $heading = $activeUnit['name']; }
elseif ($activeArea) { $heading = $activeArea['name']; }

$page_title  = $heading . ' — VVU Scholar | Valley View University';
$active_page = 'research';

$sortLabels = [
    'citations'    => 'Most cited',
    'h_index'      => 'Highest h-index',
    'publications' => 'Most publications',
    'name'         => 'Name (A–Z)',
    'recent'       => 'Recently updated',
];

include __DIR__ . '/../includes/header.php';
?>

<link rel="stylesheet" href="assets/research.css?v=1.0">
<script src="assets/research.js?v=1.0" defer></script>

<main id="main-content" class="vvus">

    <?php r_pagehero(
        'VVU Scholar',
        $heading,
        $activeUnit
            ? ($activeUnit['description'] ?: 'Researchers attached to this faculty.')
            : ($activeArea
                ? ($activeArea['description'] ?: 'Researchers working in this area.')
                : 'Every researcher indexed in the portal, with their citation record and the work behind it.'),
        ['Research Portal' => 'index.php', $heading => null],
        r_set($pdo, 'hero_image', '')
    ); ?>

    <?php r_subnav('authors.php'); ?>

    <section class="vvus__band vvus__band--tight">
        <div class="vvus__wrap">

            <form class="vvus-filters" method="get" action="authors.php" data-autosubmit>
                <div class="vvus-field">
                    <label for="f-q">Search</label>
                    <input class="vvus-input browser-default" id="f-q" type="search" name="q"
                           value="<?php echo r_e($q); ?>"
                           placeholder="Name, position or research interest">
                </div>

                <div class="vvus-field">
                    <label for="f-unit">Faculty / School</label>
                    <select class="vvus-select browser-default" id="f-unit" name="unit">
                        <option value="">All faculties</option>
                        <?php foreach ($units as $u): ?>
                            <option value="<?php echo (int) $u['id']; ?>" <?php echo $unitId === (int) $u['id'] ? 'selected' : ''; ?>>
                                <?php echo r_e($u['name']); ?> (<?php echo (int) $u['scholar_count']; ?>)
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="vvus-field">
                    <label for="f-area">Research area</label>
                    <select class="vvus-select browser-default" id="f-area" name="area">
                        <option value="">All areas</option>
                        <?php foreach ($areas as $a): ?>
                            <option value="<?php echo (int) $a['id']; ?>" <?php echo $areaId === (int) $a['id'] ? 'selected' : ''; ?>>
                                <?php echo r_e($a['name']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="vvus-field">
                    <label for="f-sort">Sort by</label>
                    <select class="vvus-select browser-default" id="f-sort" name="sort">
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
            </form>

            <div class="vvus-resultbar">
                <p class="vvus-resultbar__count">
                    <b><?php echo number_format($result['total']); ?></b>
                    researcher<?php echo $result['total'] === 1 ? '' : 's'; ?>
                    <?php if ($q !== ''): ?> matching &ldquo;<?php echo r_e($q); ?>&rdquo;<?php endif; ?>
                </p>
                <div class="vvus-resultbar__right">
                    <?php if ($q !== '' || $unitId || $areaId || $sort !== 'citations'): ?>
                        <a class="vvus-btn vvus-btn--ghost vvus-btn--sm" href="authors.php">
                            <i class="fa-solid fa-xmark" aria-hidden="true"></i> Clear filters
                        </a>
                    <?php endif; ?>
                    <a class="vvus-btn vvus-btn--ghost vvus-btn--sm" href="publications.php<?php
                        echo $unitId ? '?unit=' . $unitId : ($areaId ? '?area=' . $areaId : '');
                    ?>">
                        <i class="fa-solid fa-book-open" aria-hidden="true"></i> See publications
                    </a>
                </div>
            </div>

            <?php if ($result['rows']): ?>
                <div class="vvus-grid">
                    <?php foreach ($result['rows'] as $s) { r_person_card($s); } ?>
                </div>
                <?php r_pager($result['page'], $result['pages'], [
                    'q' => $q, 'unit' => $unitId ?: '', 'area' => $areaId ?: '', 'sort' => $sort,
                ]); ?>
            <?php else: ?>
                <?php r_empty(
                    'No researcher matched that',
                    'Try a surname on its own, widen the faculty filter, or clear the search and browse the full list.',
                    'fa-user-slash',
                    'authors.php',
                    'Show all researchers'
                ); ?>
            <?php endif; ?>

        </div>
    </section>

</main>

<?php include __DIR__ . '/../includes/footer.php'; ?>
