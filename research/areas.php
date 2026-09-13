<?php
/**
 * VVU Scholar — research areas.
 *
 * The thematic clusters VVU scholarship groups into, each with the volume and
 * impact behind it and the researchers who work in it. Areas are created and
 * ordered at Admin → Research Portal → Research Areas; the counts are live.
 */

$vvu_root = '../';

require_once __DIR__ . '/../includes/db_connect.php';
require_once __DIR__ . '/includes/research_partials.php';

$areas = r_areas($pdo);
$sec   = r_section($pdo, 'areas');

$peak = 0;
foreach ($areas as $a) {
    $peak = max($peak, (int) $a['publication_count']);
}

$page_title  = 'Research Areas — VVU Scholar | Valley View University';
$active_page = 'research';

include __DIR__ . '/../includes/header.php';
?>

<link rel="stylesheet" href="assets/research.css?v=1.0">
<script src="assets/research.js?v=1.0" defer></script>

<main id="main-content" class="vvus">

    <?php r_pagehero(
        'VVU Scholar',
        $sec['section_title'] ?? 'Research Areas',
        'The themes Valley View University scholarship clusters around, and how they map onto the UN Sustainable Development Goals.',
        ['Research Portal' => 'index.php', 'Research Areas' => null],
        r_set($pdo, 'hero_image', '')
    ); ?>

    <?php r_subnav('areas.php'); ?>

    <section class="vvus__band">
        <div class="vvus__wrap">
            <?php if (!$areas): ?>
                <?php r_empty(
                    'No research areas defined yet',
                    'Areas are created at Admin → Research Portal → Research Areas, then tagged onto publications and researchers.',
                    'fa-flask'
                ); ?>
            <?php else: ?>
                <div class="vvus-areas">
                    <?php foreach ($areas as $a):
                        $sdgs  = array_filter(array_map('trim', explode(',', (string) $a['sdg_goals'])));
                        $share = $peak > 0 ? round($a['publication_count'] / $peak * 100) : 0;
                    ?>
                        <article class="vvus-area vvus-reveal" id="<?php echo r_e($a['slug']); ?>"
                                 style="<?php echo r_accent($a['color']); ?>">
                            <div class="vvus-area__icon">
                                <i class="<?php echo r_e(r_icon($a['icon'], 'fa-flask')); ?>" aria-hidden="true"></i>
                            </div>
                            <h3><?php echo r_e($a['name']); ?></h3>
                            <?php if (!empty($a['description'])): ?>
                                <p><?php echo r_e($a['description']); ?></p>
                            <?php endif; ?>

                            <div class="vvus-unit__bar" data-bar="<?php echo (int) $share; ?>"
                                 style="position:relative"
                                 role="img" aria-label="<?php echo (int) $share; ?>% of the largest area's output">
                                <i></i>
                            </div>
                            <p class="vvus-unit__nums" style="position:relative">
                                <span><b><?php echo number_format($a['publication_count']); ?></b> publications</span>
                                <span><b><?php echo r_e(r_compact($a['citation_count'])); ?></b> citations</span>
                                <span><b><?php echo number_format($a['scholar_count']); ?></b> researchers</span>
                            </p>

                            <?php if ($sdgs): ?>
                                <p class="vvus-area__sdg" title="Aligned UN Sustainable Development Goals">
                                    <?php foreach ($sdgs as $g): ?>
                                        <b>SDG&nbsp;<?php echo (int) $g; ?></b>
                                    <?php endforeach; ?>
                                </p>
                            <?php endif; ?>

                            <a class="vvus-area__count" href="publications.php?area=<?php echo (int) $a['id']; ?>">
                                Browse this area <i class="fa-solid fa-arrow-right" aria-hidden="true"></i>
                            </a>
                        </article>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
    </section>

</main>

<?php include __DIR__ . '/../includes/footer.php'; ?>
