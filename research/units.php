<?php
/**
 * VVU Scholar — faculties and schools.
 *
 * Two views of the same data: a card per faculty for browsing, and a ranked
 * table underneath for comparing them. Both are counted live by r_units(),
 * which credits a publication to a faculty either directly or through its lead
 * author, so records imported without a faculty still land in the right place.
 */

$vvu_root = '../';

require_once __DIR__ . '/../includes/db_connect.php';
require_once __DIR__ . '/includes/research_partials.php';

$units = r_units($pdo);

// Rank by output for the comparison table, without disturbing the editor's
// display order on the cards above it.
$ranked = $units;
usort($ranked, static function ($a, $b) {
    return [(int) $b['publication_count'], (int) $b['citation_count']]
       <=> [(int) $a['publication_count'], (int) $a['citation_count']];
});

$peak = 0;
foreach ($units as $u) {
    $peak = max($peak, (int) $u['publication_count']);
}
$totalPubs = array_sum(array_column($units, 'publication_count'));

$sec = r_section($pdo, 'units');

$page_title  = 'Faculties & Schools — VVU Scholar | Valley View University';
$active_page = 'research';

include __DIR__ . '/../includes/header.php';
?>

<link rel="stylesheet" href="assets/research.css?v=1.0">
<script src="assets/research.js?v=1.0" defer></script>

<main id="main-content" class="vvus">

    <?php r_pagehero(
        'VVU Scholar',
        $sec['section_title'] ?? 'Research by Faculty',
        'Where Valley View University research is produced — with the people, the output and the citations behind each faculty and school.',
        ['Research Portal' => 'index.php', 'Faculties' => null],
        r_set($pdo, 'hero_image', '')
    ); ?>

    <?php r_subnav('units.php'); ?>

    <?php if (!$units): ?>
        <section class="vvus__band">
            <div class="vvus__wrap">
                <?php r_empty(
                    'No faculties have been added yet',
                    'Faculties and schools are created at Admin → Research Portal → Faculties & Units.',
                    'fa-building-columns'
                ); ?>
            </div>
        </section>
    <?php else: ?>

        <section class="vvus__band">
            <div class="vvus__wrap">
                <div class="vvus-units">
                    <?php foreach ($units as $u):
                        $share = $peak > 0 ? round($u['publication_count'] / $peak * 100) : 0;
                    ?>
                        <article class="vvus-unit vvus-reveal" id="<?php echo r_e($u['slug']); ?>"
                                 style="<?php echo r_accent($u['color']); ?>">
                            <div class="vvus-unit__icon">
                                <i class="<?php echo r_e(r_icon($u['icon'], 'fa-building-columns')); ?>" aria-hidden="true"></i>
                            </div>
                            <h3><?php echo r_e($u['name']); ?></h3>
                            <?php if (!empty($u['description'])): ?>
                                <p class="vvus-unit__desc"><?php echo r_e($u['description']); ?></p>
                            <?php endif; ?>

                            <?php if (!empty($u['head_name'])): ?>
                                <p class="vvus-unit__desc" style="margin-top:10px">
                                    <i class="fa-solid fa-user-tie" aria-hidden="true"></i>
                                    <strong><?php echo r_e($u['head_name']); ?></strong>
                                </p>
                            <?php endif; ?>

                            <?php $departments = r_child_units($pdo, $u['id']); ?>
                            <?php if ($departments): ?>
                                <ul class="vvus-unit__depts">
                                    <?php foreach ($departments as $d): ?>
                                        <li>
                                            <a href="authors.php?unit=<?php echo (int) $d['id']; ?>">
                                                <i class="<?php echo r_e(r_icon($d['icon'], 'fa-diagram-project')); ?>" aria-hidden="true"></i>
                                                <span><?php echo r_e($d['name']); ?></span>
                                                <b><?php echo number_format($d['publication_count']); ?></b>
                                            </a>
                                        </li>
                                    <?php endforeach; ?>
                                </ul>
                            <?php endif; ?>

                            <div class="vvus-unit__bar" data-bar="<?php echo (int) $share; ?>"
                                 role="img" aria-label="<?php echo (int) $share; ?>% of the busiest faculty's output">
                                <i></i>
                            </div>
                            <p class="vvus-unit__nums">
                                <span><b><?php echo number_format($u['scholar_count']); ?></b> researchers</span>
                                <span><b><?php echo number_format($u['publication_count']); ?></b> publications</span>
                                <span><b><?php echo r_e(r_compact($u['citation_count'])); ?></b> citations</span>
                            </p>

                            <div class="vvus-actions" style="margin-top:16px">
                                <a class="vvus-btn vvus-btn--ghost vvus-btn--sm" href="authors.php?unit=<?php echo (int) $u['id']; ?>">
                                    <i class="fa-solid fa-user-graduate" aria-hidden="true"></i> Researchers
                                </a>
                                <a class="vvus-btn vvus-btn--ghost vvus-btn--sm" href="publications.php?unit=<?php echo (int) $u['id']; ?>">
                                    <i class="fa-solid fa-book-open" aria-hidden="true"></i> Publications
                                </a>
                                <?php if (!empty($u['website'])): ?>
                                    <a class="vvus-btn vvus-btn--ghost vvus-btn--sm"
                                       href="<?php echo r_e(r_asset($u['website'])); ?>">
                                        <i class="fa-solid fa-arrow-up-right-from-square" aria-hidden="true"></i> Faculty site
                                    </a>
                                <?php endif; ?>
                            </div>
                        </article>
                    <?php endforeach; ?>
                </div>
            </div>
        </section>

        <!-- Ranked comparison ------------------------------------------------ -->
        <section class="vvus__band vvus__band--alt">
            <div class="vvus__wrap">
                <div class="vvus__head">
                    <p class="vvus__eyebrow">Side by side</p>
                    <h2 class="vvus__title">Faculties ranked by output</h2>
                    <p class="vvus__subtitle">
                        Share is each faculty's slice of the
                        <?php echo number_format($totalPubs); ?> publication records currently
                        attributed to a faculty<?php
                        $metrics = r_metrics($pdo);
                        $unattributed = max(0, $metrics['publications'] - $totalPubs);
                        if ($unattributed > 0) {
                            echo ', out of ' . number_format($metrics['publications'])
                               . ' in the catalogue. The remaining ' . number_format($unattributed)
                               . ' are indexed but not yet assigned to one';
                        }
                        ?>. A paper written across two faculties counts towards both.
                    </p>
                </div>

                <div class="vvus-card vvus-reveal">
                    <div style="overflow-x:auto">
                        <table style="width:100%;border-collapse:collapse;min-width:640px">
                            <thead>
                                <tr style="text-align:left">
                                    <th style="padding:14px 20px;font-size:11px;letter-spacing:.12em;text-transform:uppercase;color:var(--ink-faint);border-bottom:1px solid var(--line)">#</th>
                                    <th style="padding:14px 20px;font-size:11px;letter-spacing:.12em;text-transform:uppercase;color:var(--ink-faint);border-bottom:1px solid var(--line)">Faculty or school</th>
                                    <th style="padding:14px 20px;font-size:11px;letter-spacing:.12em;text-transform:uppercase;color:var(--ink-faint);border-bottom:1px solid var(--line);text-align:right">Researchers</th>
                                    <th style="padding:14px 20px;font-size:11px;letter-spacing:.12em;text-transform:uppercase;color:var(--ink-faint);border-bottom:1px solid var(--line);text-align:right">Publications</th>
                                    <th style="padding:14px 20px;font-size:11px;letter-spacing:.12em;text-transform:uppercase;color:var(--ink-faint);border-bottom:1px solid var(--line);text-align:right">Citations</th>
                                    <th style="padding:14px 20px;font-size:11px;letter-spacing:.12em;text-transform:uppercase;color:var(--ink-faint);border-bottom:1px solid var(--line);text-align:right">Share</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($ranked as $i => $u):
                                    $share = $totalPubs > 0 ? $u['publication_count'] / $totalPubs * 100 : 0;
                                ?>
                                    <tr style="<?php echo r_accent($u['color']); ?>">
                                        <td style="padding:14px 20px;border-bottom:1px solid var(--line-soft);font-family:Cinzel,Georgia,serif;font-weight:700;color:var(--ink-faint)"><?php echo $i + 1; ?></td>
                                        <td style="padding:14px 20px;border-bottom:1px solid var(--line-soft)">
                                            <a href="authors.php?unit=<?php echo (int) $u['id']; ?>"
                                               style="font-size:14px;font-weight:700;color:var(--navy)">
                                                <?php echo r_e($u['name']); ?>
                                            </a>
                                        </td>
                                        <td style="padding:14px 20px;border-bottom:1px solid var(--line-soft);text-align:right;font-variant-numeric:tabular-nums;color:var(--ink-soft)"><?php echo number_format($u['scholar_count']); ?></td>
                                        <td style="padding:14px 20px;border-bottom:1px solid var(--line-soft);text-align:right;font-variant-numeric:tabular-nums;color:var(--ink-soft)"><?php echo number_format($u['publication_count']); ?></td>
                                        <td style="padding:14px 20px;border-bottom:1px solid var(--line-soft);text-align:right;font-variant-numeric:tabular-nums;color:var(--ink-soft)"><?php echo number_format($u['citation_count']); ?></td>
                                        <td style="padding:14px 20px;border-bottom:1px solid var(--line-soft);text-align:right">
                                            <span style="display:inline-flex;align-items:center;gap:9px;justify-content:flex-end">
                                                <span style="display:block;width:70px;height:6px;border-radius:999px;background:var(--line-soft);overflow:hidden">
                                                    <span style="display:block;height:100%;width:<?php echo round($share); ?>%;background:var(--acc)"></span>
                                                </span>
                                                <span style="font-size:12.5px;font-weight:700;color:var(--navy);font-variant-numeric:tabular-nums;min-width:42px"><?php echo number_format($share, 1); ?>%</span>
                                            </span>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </section>

    <?php endif; ?>

</main>

<?php include __DIR__ . '/../includes/footer.php'; ?>
