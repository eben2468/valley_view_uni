<?php
/**
 * SITE SEARCH
 * Backs the search field in the masthead. Looks across three sources:
 * published news/events, active academic programmes, and every page reachable
 * from the navigation tables.
 */

$page_title = 'Search - Valley View University';
include 'includes/header.php';

$q = isset($_GET['q']) ? trim($_GET['q']) : '';
$q = mb_substr($q, 0, 120);

$results = ['pages' => [], 'programs' => [], 'news' => []];
$total   = 0;

if ($q !== '') {
    // Escape the LIKE wildcards so a query of "100%" doesn't match everything.
    $like = '%' . str_replace(['\\', '%', '_'], ['\\\\', '\%', '\_'], $q) . '%';

    /* ---------------------------------------------------------------- pages */
    try {
        $stmt = $pdo->prepare(
            "SELECT nl.title, nl.url, ns.section_title AS context
               FROM navigation_links nl
               JOIN navigation_sections ns ON ns.id = nl.section_id
               JOIN navigation_items ni ON ni.id = ns.navigation_item_id
              WHERE nl.is_active = 1 AND ns.is_active = 1 AND ni.is_active = 1
                AND nl.title LIKE :like
              ORDER BY nl.title ASC
              LIMIT 40"
        );
        $stmt->execute([':like' => $like]);

        $seen = [];
        foreach ($stmt->fetchAll(PDO::FETCH_ASSOC) as $row) {
            $key = strtolower($row['url']);
            if ($key === '' || isset($seen[$key])) {
                continue;
            }
            $seen[$key] = true;
            $results['pages'][] = $row;
        }
    } catch (Exception $e) {
        // Table missing — leave the group empty.
    }

    /* ------------------------------------------------------------ programmes */
    try {
        $stmt = $pdo->prepare(
            "SELECT title, description, link_url, level, duration
               FROM academic_programs
              WHERE is_active = 1
                AND (title LIKE :like1 OR description LIKE :like2)
              ORDER BY (title LIKE :like3) DESC, title ASC
              LIMIT 24"
        );
        $stmt->execute([':like1' => $like, ':like2' => $like, ':like3' => $like]);
        $results['programs'] = $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (Exception $e) {
    }

    /* ------------------------------------------------------------------ news */
    try {
        $stmt = $pdo->prepare(
            "SELECT title, slug, excerpt, category, publish_date
               FROM news_articles
              WHERE status = 'published'
                AND (title LIKE :like1 OR excerpt LIKE :like2 OR content LIKE :like3 OR tags LIKE :like4)
              ORDER BY (title LIKE :like5) DESC, publish_date DESC
              LIMIT 20"
        );
        $stmt->execute([
            ':like1' => $like, ':like2' => $like, ':like3' => $like,
            ':like4' => $like, ':like5' => $like,
        ]);
        $results['news'] = $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (Exception $e) {
    }

    $total = count($results['pages']) + count($results['programs']) + count($results['news']);
}

/** Wraps every occurrence of the query in a <mark>, on already-escaped text. */
function vvu_highlight($text, $needle)
{
    $text = vvu_e($text);
    if ($needle === '') {
        return $text;
    }
    $pattern = '/' . preg_quote(vvu_e($needle), '/') . '/iu';
    return preg_replace($pattern, '<mark class="vvu-mark">$0</mark>', $text);
}

function vvu_trim_words($text, $words = 32)
{
    $text = trim(preg_replace('/\s+/', ' ', strip_tags((string) $text)));
    $parts = explode(' ', $text);
    if (count($parts) <= $words) {
        return $text;
    }
    return implode(' ', array_slice($parts, 0, $words)) . '…';
}
?>

<style>
    /* Scoped to the search page — same tokens as the masthead. */
    .vvu-srch-hero {
        position: relative;
        padding: 62px 0 70px;
        background:
            var(--vvu-weave),
            linear-gradient(115deg, var(--vvu-navy-deep) 0%, var(--vvu-navy) 48%, var(--vvu-navy-soft) 100%);
        color: #fff;
        text-align: center;
    }

    .vvu-srch-shell {
        width: 100%;
        max-width: 860px;
        margin: 0 auto;
        padding: 0 22px;
    }

    .vvu-srch-hero h1 {
        margin: 0 0 10px;
        font-family: 'Cinzel', Georgia, serif;
        font-size: clamp(26px, 4vw, 40px);
        font-weight: 700;
        letter-spacing: .05em;
        text-transform: uppercase;
        color: #fff !important;
    }

    .vvu-srch-hero .vvu-rule {
        max-width: 220px;
        margin: 0 auto 16px;
    }

    .vvu-srch-hero p {
        margin: 0 0 26px;
        font-size: 14.5px;
        color: rgba(255, 255, 255, .74);
    }

    .vvu-srch-hero .vvu-search__field {
        border-color: transparent;
    }

    .vvu-srch-body {
        padding: 54px 0 78px;
        background: var(--vvu-paper-warm);
    }

    .vvu-srch-wrap {
        width: 100%;
        max-width: 1000px;
        margin: 0 auto;
        padding: 0 22px;
    }

    .vvu-srch-count {
        margin: 0 0 30px;
        font-size: 14px;
        color: var(--vvu-ink-soft);
    }

    .vvu-srch-count strong {
        color: var(--vvu-navy);
        font-weight: 700;
    }

    .vvu-srch-group {
        margin-bottom: 44px;
    }

    .vvu-srch-group>h2 {
        display: flex;
        align-items: center;
        gap: 14px;
        margin: 0 0 18px;
        font-family: 'Cinzel', Georgia, serif;
        font-size: 13px !important;
        font-weight: 700 !important;
        letter-spacing: .16em;
        text-transform: uppercase;
        color: var(--vvu-navy) !important;
    }

    .vvu-srch-group>h2::after {
        content: "";
        flex: 1 1 auto;
        height: 1px;
        background: linear-gradient(90deg, var(--vvu-gold), rgba(240, 180, 41, 0));
    }

    .vvu-srch-group>h2 em {
        font-style: normal;
        font-size: 11px;
        letter-spacing: .08em;
        color: var(--vvu-ink-soft);
    }

    .vvu-srch-list {
        display: grid;
        gap: 12px;
        margin: 0;
        padding: 0;
        list-style: none;
    }

    .vvu-srch-list li {
        list-style: none;
    }

    .vvu-srch-card {
        display: block;
        padding: 18px 22px;
        background: #fff;
        border: 1px solid var(--vvu-hair);
        border-left: 3px solid var(--vvu-hair);
        border-radius: 12px;
        text-decoration: none;
        transition: border-color .22s ease, transform .22s ease, box-shadow .22s ease;
    }

    .vvu-srch-card:hover {
        border-color: rgba(240, 180, 41, .55);
        border-left-color: var(--vvu-gold);
        transform: translateY(-2px);
        box-shadow: var(--vvu-shadow-sm);
        text-decoration: none;
    }

    .vvu-srch-card h3 {
        margin: 0 0 6px;
        font-family: 'Open Sans', sans-serif !important;
        font-size: 16px !important;
        font-weight: 700 !important;
        line-height: 1.35;
        color: var(--vvu-navy) !important;
    }

    .vvu-srch-card p {
        margin: 0;
        font-size: 13.5px;
        line-height: 1.6;
        color: var(--vvu-ink-soft);
    }

    .vvu-srch-meta {
        display: flex;
        flex-wrap: wrap;
        gap: 8px;
        margin-top: 10px;
    }

    .vvu-srch-tag {
        padding: 3px 10px;
        font-size: 10.5px;
        font-weight: 700;
        letter-spacing: .1em;
        text-transform: uppercase;
        color: var(--vvu-gold-deep);
        background: rgba(240, 180, 41, .14);
        border-radius: 999px;
    }

    .vvu-mark {
        padding: 0 2px;
        color: var(--vvu-navy);
        background: rgba(240, 180, 41, .38);
        border-radius: 3px;
    }

    .vvu-srch-empty {
        padding: 54px 30px;
        text-align: center;
        background: #fff;
        border: 1px dashed var(--vvu-hair);
        border-radius: 16px;
    }

    .vvu-srch-empty i {
        font-size: 34px;
        color: var(--vvu-gold);
    }

    .vvu-srch-empty h2 {
        margin: 16px 0 8px;
        font-family: 'Cinzel', Georgia, serif;
        font-size: 20px !important;
        color: var(--vvu-navy) !important;
    }

    .vvu-srch-empty p {
        margin: 0 auto 22px;
        max-width: 460px;
        font-size: 14px;
        color: var(--vvu-ink-soft);
    }

    .vvu-srch-empty .vvu-chips {
        justify-content: center;
    }
</style>

<section class="vvu-srch-hero">
    <div class="vvu-srch-shell">
        <h1>Search</h1>
        <span class="vvu-rule" aria-hidden="true"><i></i></span>
        <p>Find programmes, news, admissions information and every page across the university.</p>

        <form class="vvu-search-form" action="search.php" method="get" role="search">
            <div class="vvu-search__field">
                <i class="fa-solid fa-magnifying-glass" aria-hidden="true"></i>
                <input class="vvu-search__input browser-default" type="search" name="q"
                    value="<?php echo vvu_e($q); ?>" placeholder="Search Valley View University&hellip;"
                    aria-label="Search Valley View University" autocomplete="off"
                    <?php echo $q === '' ? 'autofocus' : ''; ?>>
                <button type="submit" class="vvu-search__submit">Search</button>
            </div>
        </form>
    </div>
</section>

<section class="vvu-srch-body">
    <div class="vvu-srch-wrap">

        <?php if ($q === ''): ?>
            <div class="vvu-srch-empty">
                <i class="fa-solid fa-compass" aria-hidden="true"></i>
                <h2>What are you looking for?</h2>
                <p>Enter a keyword above, or jump straight to one of the pages people ask for most.</p>
                <div class="vvu-chips">
                    <?php foreach ($vvu_search_hints as $label => $href): ?>
                        <a href="<?php echo vvu_e($href); ?>"><?php echo vvu_e($label); ?></a>
                    <?php endforeach; ?>
                </div>
            </div>

        <?php elseif ($total === 0): ?>
            <div class="vvu-srch-empty">
                <i class="fa-regular fa-face-frown" aria-hidden="true"></i>
                <h2>No matches for &ldquo;<?php echo vvu_e($q); ?>&rdquo;</h2>
                <p>Try a shorter or more general keyword &mdash; for example &ldquo;nursing&rdquo; instead of
                    &ldquo;BSc Nursing 2026 admission&rdquo;.</p>
                <div class="vvu-chips">
                    <?php foreach ($vvu_search_hints as $label => $href): ?>
                        <a href="<?php echo vvu_e($href); ?>"><?php echo vvu_e($label); ?></a>
                    <?php endforeach; ?>
                </div>
            </div>

        <?php else: ?>
            <p class="vvu-srch-count">
                <strong><?php echo (int) $total; ?></strong>
                result<?php echo $total === 1 ? '' : 's'; ?> for &ldquo;<strong><?php echo vvu_e($q); ?></strong>&rdquo;
            </p>

            <?php if ($results['programs']): ?>
                <div class="vvu-srch-group">
                    <h2>Programmes <em>(<?php echo count($results['programs']); ?>)</em></h2>
                    <ul class="vvu-srch-list">
                        <?php foreach ($results['programs'] as $row): ?>
                            <li>
                                <a class="vvu-srch-card"
                                    href="<?php echo vvu_e(vvu_url($row['link_url'] ?: 'academic_programs_overview.php')); ?>">
                                    <h3><?php echo vvu_highlight($row['title'], $q); ?></h3>
                                    <?php if (!empty($row['description'])): ?>
                                        <p><?php echo vvu_highlight(vvu_trim_words($row['description'], 28), $q); ?></p>
                                    <?php endif; ?>
                                    <div class="vvu-srch-meta">
                                        <?php if (!empty($row['level'])): ?>
                                            <span class="vvu-srch-tag"><?php echo vvu_e($row['level']); ?></span>
                                        <?php endif; ?>
                                        <?php if (!empty($row['duration'])): ?>
                                            <span class="vvu-srch-tag"><?php echo vvu_e($row['duration']); ?></span>
                                        <?php endif; ?>
                                    </div>
                                </a>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            <?php endif; ?>

            <?php if ($results['news']): ?>
                <div class="vvu-srch-group">
                    <h2>News &amp; Events <em>(<?php echo count($results['news']); ?>)</em></h2>
                    <ul class="vvu-srch-list">
                        <?php foreach ($results['news'] as $row): ?>
                            <li>
                                <a class="vvu-srch-card" href="news_detail.php?slug=<?php echo urlencode($row['slug']); ?>">
                                    <h3><?php echo vvu_highlight($row['title'], $q); ?></h3>
                                    <?php if (!empty($row['excerpt'])): ?>
                                        <p><?php echo vvu_highlight(vvu_trim_words($row['excerpt'], 30), $q); ?></p>
                                    <?php endif; ?>
                                    <div class="vvu-srch-meta">
                                        <?php if (!empty($row['category'])): ?>
                                            <span class="vvu-srch-tag"><?php echo vvu_e(str_replace('_', ' ', $row['category'])); ?></span>
                                        <?php endif; ?>
                                        <?php if (!empty($row['publish_date'])): ?>
                                            <span class="vvu-srch-tag"><?php echo vvu_e(date('j M Y', strtotime($row['publish_date']))); ?></span>
                                        <?php endif; ?>
                                    </div>
                                </a>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            <?php endif; ?>

            <?php if ($results['pages']): ?>
                <div class="vvu-srch-group">
                    <h2>Pages <em>(<?php echo count($results['pages']); ?>)</em></h2>
                    <ul class="vvu-srch-list">
                        <?php foreach ($results['pages'] as $row): ?>
                            <li>
                                <a class="vvu-srch-card" href="<?php echo vvu_e(vvu_url($row['url'])); ?>">
                                    <h3><?php echo vvu_highlight($row['title'], $q); ?></h3>
                                    <?php if (!empty($row['context'])): ?>
                                        <div class="vvu-srch-meta">
                                            <span class="vvu-srch-tag"><?php echo vvu_e($row['context']); ?></span>
                                        </div>
                                    <?php endif; ?>
                                </a>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            <?php endif; ?>
        <?php endif; ?>
    </div>
</section>

<?php include 'includes/footer.php'; ?>
