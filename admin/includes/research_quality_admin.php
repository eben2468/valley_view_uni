<?php
/**
 * Admin → Research Portal → "Data Quality & Sync" and "Reviews" tabs.
 *
 * Included by admin/manage_research.php for its q_* / rv_* POST actions and
 * for the two tab bodies. The work itself is in research_quality.php
 * (clean-up, merges) and research_autosync.php (automatic updates).
 */

require_once __DIR__ . '/research_autosync.php';
require_once __DIR__ . '/research_citations.php';
require_once __DIR__ . '/../../research/includes/research_engage.php';

if (!function_exists('vvurq_handle_post')) {

/** Handles q_* (quality/sync) and rv_* (reviews) actions, then redirects. */
function vvurq_handle_post(PDO $pdo)
{
    $action = (string) ($_POST['action'] ?? '');
    $tab    = strpos($action, 'rv_') === 0 ? 'reviews' : 'quality';
    if (!vvur_quality_ready($pdo)) {
        vvur_redirect($tab, 'danger', 'Import sql/research_quality_migration.sql in phpMyAdmin first.');
    }

    try {
        switch ($action) {
            /* ---------------------------------------- full clean-up */
            case 'q_cleanup':
                @set_time_limit(600);
                $fetch = vvur_openalex_fetch(['pages' => 30, 'time_budget' => 240]);
                $works = ($fetch['ok'] && $fetch['works']) ? $fetch['works'] : [];
                $r = vvur_quality_run($pdo, $works);
                r_recount_all($pdo);
                $msg = vvur_quality_summary($r);
                if (!$works) {
                    $msg .= ' (OpenAlex could not be reached, so the VVU-affiliation check was skipped this time'
                          . ($fetch['error'] ? ': ' . $fetch['error'] : '') . '.)';
                } elseif (!$fetch['complete']) {
                    $msg .= ' (Only part of OpenAlex was read — ' . ($fetch['error'] ?: 'time ran out') . ' Run it again to check the rest.)';
                }
                vvur_redirect('quality', 'success', 'Clean-up finished. ' . $msg);

            /* ---------------------------------------- citations & publication lists */
            case 'q_refresh_scholar':
                @set_time_limit(400);
                $sid = (int) ($_POST['scholar_id'] ?? 0);
                if (!$sid) { vvur_redirect('quality', 'warning', 'Choose a researcher.'); }
                $r = vvur_pull_researcher_works($pdo, $sid, microtime(true) + 300);
                vvur_redirect('quality', 'success', $r['summary']);

            case 'q_refresh_citations':
                @set_time_limit(400);
                $c = vvur_refresh_citations($pdo, microtime(true) + 300, '1=1', [], 1500);
                r_recount_all($pdo);
                vvur_redirect('quality', 'success', sprintf(
                    'Citations re-checked for %d papers — OpenAlex %d, Crossref %d, Semantic Scholar %d; %d counts changed%s. Researcher totals recalculated.',
                    $c['papers'], $c['openalex'], $c['crossref'], $c['s2'], $c['changed'],
                    $c['errors'] ? ' (' . implode('; ', array_unique($c['errors'])) . ')' : ''));

            /* ---------------------------------------- merges */
            case 'q_merge':
                $keep = (int) ($_POST['keep_id'] ?? 0);
                $ids  = array_map('intval', (array) ($_POST['merge_ids'] ?? []));
                if (!$keep || !array_diff($ids, [$keep])) {
                    vvur_redirect('quality', 'warning', 'Choose the profile to keep and at least one other profile to merge into it.');
                }
                $n = vvur_merge_scholars($pdo, $keep, $ids, 'admin');
                vvur_redirect('quality', 'success', $n . ' profile(s) merged. Their publications now appear on one profile.');

            case 'q_not_same':
                $ids = array_values(array_unique(array_map('intval', (array) ($_POST['ids'] ?? []))));
                $st = $pdo->prepare("INSERT IGNORE INTO research_scholar_not_same (scholar_a, scholar_b) VALUES (?,?)");
                for ($i = 0; $i < count($ids); $i++) {
                    for ($j = $i + 1; $j < count($ids); $j++) {
                        $st->execute([min($ids[$i], $ids[$j]), max($ids[$i], $ids[$j])]);
                    }
                }
                vvur_redirect('quality', 'success', 'Noted — these profiles will not be suggested or merged again.');

            case 'q_unmerge':
                $ok = vvur_unmerge($pdo, (int) ($_POST['merge_id'] ?? 0));
                vvur_redirect('quality', $ok ? 'success' : 'warning', $ok
                    ? 'Merge undone. The profile is back with its own publications, and the pair will not be merged automatically again.'
                    : 'That merge was already undone.');

            /* ---------------------------------------- automatic updates */
            case 'q_sync_now':
                $r = vvur_auto_sync($pdo, 'admin', 240);
                vvur_redirect('quality', $r['ok'] ? 'success' : 'warning', ($r['ok'] ? 'Update finished. ' : 'Update did not finish. ') . $r['summary']);

            case 'q_sync_settings':
                vvur_set_setting($pdo, 'auto_sync_enabled', !empty($_POST['auto_sync_enabled']) ? '1' : '0');
                vvur_set_setting($pdo, 'auto_sync_hours', (string) max(1, min(720, (int) ($_POST['auto_sync_hours'] ?? 24))));
                vvur_set_setting($pdo, 'auto_sync_orcid_scope', ($_POST['auto_sync_orcid_scope'] ?? '') === 'all' ? 'all' : 'claimed');
                if (isset($_POST['s2_api_key'])) {
                    vvur_set_setting($pdo, 's2_api_key', preg_replace('/[^A-Za-z0-9_-]/', '', (string) $_POST['s2_api_key']));
                }
                vvur_set_setting($pdo, 'reviews_require_approval', !empty($_POST['reviews_require_approval']) ? '1' : '0');
                if (isset($_POST['openalex_api_key'])) {
                    vvur_set_setting($pdo, 'openalex_api_key', preg_replace('/[^A-Za-z0-9_-]/', '', (string) $_POST['openalex_api_key']));
                }
                if (isset($_POST['altmetric_api_key'])) {
                    vvur_set_setting($pdo, 'altmetric_api_key', preg_replace('/[^A-Za-z0-9_-]/', '', (string) $_POST['altmetric_api_key']));
                }
                if (!empty($_POST['new_key'])) {
                    vvur_set_setting($pdo, 'auto_sync_key', bin2hex(random_bytes(24)));
                }
                vvur_redirect('quality', 'success', 'Automatic update settings saved.');

            /* ---------------------------------------- reviews */
            case 'rv_approve':
            case 'rv_reject':
            case 'rv_delete':
                $id = (int) ($_POST['review_id'] ?? 0);
                $st = $pdo->prepare("SELECT publication_id FROM research_publication_reviews WHERE id = ?");
                $st->execute([$id]);
                $pub = (int) $st->fetchColumn();
                if (!$pub) {
                    vvur_redirect('reviews', 'warning', 'That review no longer exists.');
                }
                if ($action === 'rv_delete') {
                    $pdo->prepare("DELETE FROM research_publication_reviews WHERE id = ?")->execute([$id]);
                } else {
                    $pdo->prepare("UPDATE research_publication_reviews SET status = ? WHERE id = ?")
                        ->execute([$action === 'rv_approve' ? 'approved' : 'rejected', $id]);
                }
                r_recount_ratings($pdo, $pub);
                vvur_redirect('reviews', 'success', ['rv_approve' => 'Review published.', 'rv_reject' => 'Review hidden.', 'rv_delete' => 'Review deleted.'][$action]);
        }
    } catch (Exception $e) {
        vvur_redirect($tab, 'danger', 'Could not complete that: ' . $e->getMessage());
    }
    vvur_redirect($tab, 'warning', 'Unknown action.');
}

/** Pending reviews, for the tab badge. */
function vvurq_pending_reviews(PDO $pdo)
{
    try {
        return (int) $pdo->query("SELECT COUNT(*) FROM research_publication_reviews WHERE status = 'pending'")->fetchColumn();
    } catch (Exception $e) {
        return 0;
    }
}

/** "Data Quality & Sync" tab. */
function vvurq_render_quality(PDO $pdo)
{
    if (!vvur_quality_ready($pdo)) {
        echo '<div class="alert alert-warning"><strong>One database update is needed.</strong> Import '
           . '<code>sql/research_quality_migration.sql</code> in phpMyAdmin, reload this page, then press “Run clean-up”.</div>';
        return;
    }
    $names = $pdo->query("SELECT id, full_name, publications_count, slug, is_active FROM research_scholars")->fetchAll(PDO::FETCH_UNIQUE | PDO::FETCH_ASSOC);
    $groups = vvur_scholar_duplicate_groups($pdo);
    $merges = $pdo->query(
        "SELECT m.*, k.full_name AS keep_name, k.slug AS keep_slug, d.full_name AS merged_name
           FROM research_scholar_merges m
           JOIN research_scholars k ON k.id = m.keep_id
           JOIN research_scholars d ON d.id = m.merged_id
          ORDER BY m.id DESC LIMIT 60"
    )->fetchAll(PDO::FETCH_ASSOC);
    $log = $pdo->query("SELECT * FROM research_sync_log ORDER BY id DESC LIMIT 12")->fetchAll(PDO::FETCH_ASSOC);
    $counts = $pdo->query(
        "SELECT (SELECT COUNT(*) FROM research_author_exclusions) excl,
                (SELECT COUNT(*) FROM research_scholars WHERE merged_into IS NOT NULL) merged,
                (SELECT COUNT(*) FROM research_publications WHERE is_active = 0) hidden,
                (SELECT COUNT(*) FROM research_publications WHERE is_active = 1) live"
    )->fetch(PDO::FETCH_ASSOC);
    $active = $pdo->query("SELECT id, full_name FROM research_scholars WHERE is_active = 1 ORDER BY full_name")->fetchAll(PDO::FETCH_KEY_PAIR);

    $key  = r_set($pdo, 'auto_sync_key', '');
    $base = ra_base_url_admin();
    $cronUrl = $base . 'research/cron.php?key=' . $key;
    $siteDir = realpath(__DIR__ . '/../../');
    ?>
    <div class="row g-4">
        <!-- Clean-up -->
        <div class="col-lg-7">
            <div class="card h-100">
                <div class="card-header"><h5 class="mb-0"><i class="fas fa-broom me-2"></i>Clean-up</h5></div>
                <div class="card-body">
                    <p class="mb-2">Re-checks the whole portal and fixes three things automatically:</p>
                    <ul class="small mb-3">
                        <li><strong>Not VVU.</strong> OpenAlex tags anyone whose address contains “Valley View” (US streets, schools, companies) as VVU. Each author’s printed affiliation is checked; people and papers that are not Valley View University are removed.</li>
                        <li><strong>One person, several profiles</strong> (e.g. “Evans O. N. D. Ocansey” / “E. O. N. D. Ocansey” / “Evans Ocansey”) are merged into one, with all their papers.</li>
                        <li><strong>One paper, several records</strong> (preprint + journal version, repeated repository versions, the same work from OpenAlex, ORCID, Crossref or Scholar) are reduced to one.</li>
                    </ul>
                    <form method="post" onsubmit="this.querySelector('button').disabled=true;this.querySelector('button').innerHTML='Working… this can take a few minutes';">
                        <?php echo vvu_csrf_field(); ?>
                        <input type="hidden" name="action" value="q_cleanup">
                        <button class="btn btn-primary"><i class="fas fa-broom"></i> Run clean-up</button>
                    </form>
                    <div class="row g-2 mt-3 small text-center">
                        <div class="col-3"><div class="border rounded p-2"><div class="fw-bold fs-5"><?php echo number_format($counts['live']); ?></div>papers live</div></div>
                        <div class="col-3"><div class="border rounded p-2"><div class="fw-bold fs-5"><?php echo number_format($counts['hidden']); ?></div>withdrawn</div></div>
                        <div class="col-3"><div class="border rounded p-2"><div class="fw-bold fs-5"><?php echo number_format($counts['merged']); ?></div>profiles merged</div></div>
                        <div class="col-3"><div class="border rounded p-2"><div class="fw-bold fs-5"><?php echo number_format($counts['excl']); ?></div>wrong author links blocked</div></div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Automatic updates -->
        <div class="col-lg-5">
            <div class="card h-100">
                <div class="card-header"><h5 class="mb-0"><i class="fas fa-rotate me-2"></i>Automatic updates</h5></div>
                <div class="card-body">
                    <form method="post" class="mb-3">
                        <?php echo vvu_csrf_field(); ?>
                        <input type="hidden" name="action" value="q_sync_settings">
                        <div class="form-check form-switch mb-2">
                            <input class="form-check-input" type="checkbox" name="auto_sync_enabled" id="qse" value="1" <?php echo r_set($pdo, 'auto_sync_enabled', '0') === '1' ? 'checked' : ''; ?>>
                            <label class="form-check-label" for="qse">Pull new publications and impact figures automatically</label>
                        </div>
                        <div class="input-group input-group-sm mb-2">
                            <span class="input-group-text">Every</span>
                            <input type="number" class="form-control" name="auto_sync_hours" min="1" max="720" value="<?php echo (int) r_set($pdo, 'auto_sync_hours', '24'); ?>">
                            <span class="input-group-text">hours</span>
                        </div>
                        <label class="form-label small fw-semibold mb-1">Pull ORCID &amp; Google Scholar works for</label>
                        <select class="form-select form-select-sm mb-2" name="auto_sync_orcid_scope">
                            <option value="claimed" <?php echo r_set($pdo, 'auto_sync_orcid_scope', 'claimed') !== 'all' ? 'selected' : ''; ?>>Researchers who claimed their profile (recommended)</option>
                            <option value="all" <?php echo r_set($pdo, 'auto_sync_orcid_scope', 'claimed') === 'all' ? 'selected' : ''; ?>>Every researcher with an ORCID / Scholar id</option>
                        </select>
                        <label class="form-label small fw-semibold mb-1">OpenAlex API key <span class="text-muted fw-normal">(free — <a href="https://openalex.org/settings/api" target="_blank" rel="noopener">get one</a>)</span></label>
                        <input type="text" class="form-control form-control-sm mb-2" name="openalex_api_key" autocomplete="off" value="<?php echo h(r_set($pdo, 'openalex_api_key', '')); ?>" placeholder="Without a key OpenAlex limits this server to a small daily allowance">
                        <label class="form-label small fw-semibold mb-1">Semantic Scholar API key <span class="text-muted fw-normal">(optional — avoids its rate limit; <a href="https://www.semanticscholar.org/product/api#api-key-form" target="_blank" rel="noopener">request one</a>)</span></label>
                        <input type="text" class="form-control form-control-sm mb-2" name="s2_api_key" autocomplete="off" value="<?php echo h(r_set($pdo, 's2_api_key', '')); ?>">
                        <label class="form-label small fw-semibold mb-1">Altmetric API key <span class="text-muted fw-normal">(optional — adds readers &amp; online attention; <a href="https://www.altmetric.com/solutions/free-tools/" target="_blank" rel="noopener">request one</a>)</span></label>
                        <input type="text" class="form-control form-control-sm mb-2" name="altmetric_api_key" autocomplete="off" value="<?php echo h(r_set($pdo, 'altmetric_api_key', '')); ?>">
                        <div class="form-check mb-2">
                            <input class="form-check-input" type="checkbox" name="reviews_require_approval" id="qra" value="1" <?php echo r_set($pdo, 'reviews_require_approval', '1') === '1' ? 'checked' : ''; ?>>
                            <label class="form-check-label small" for="qra">Written reviews need approval before they appear</label>
                        </div>
                        <div class="d-flex gap-2 align-items-center">
                            <button class="btn btn-sm btn-primary"><i class="fas fa-save"></i> Save</button>
                            <label class="small text-muted ms-auto"><input type="checkbox" name="new_key" value="1"> new cron key</label>
                        </div>
                    </form>
                    <form method="post" onsubmit="this.querySelector('button').disabled=true;this.querySelector('button').innerHTML='Updating… up to 4 minutes';">
                        <?php echo vvu_csrf_field(); ?>
                        <input type="hidden" name="action" value="q_sync_now">
                        <button class="btn btn-sm btn-success w-100"><i class="fas fa-cloud-arrow-down"></i> Run update now</button>
                    </form>
                    <p class="small text-muted mt-2 mb-1">Last run: <?php echo h(r_set($pdo, 'last_auto_sync_at', '') ?: 'never'); ?></p>
                    <details class="small">
                        <summary class="text-primary" style="cursor:pointer">Set up a cron job (recommended)</summary>
                        <p class="mt-2 mb-1">In your hosting control panel add a cron job that runs every hour:</p>
                        <code class="d-block p-2 bg-light border rounded mb-1" style="word-break:break-all">php <?php echo h($siteDir . DIRECTORY_SEPARATOR . 'research' . DIRECTORY_SEPARATOR . 'cron.php'); ?></code>
                        <p class="mb-1">or, if the host only allows URLs:</p>
                        <code class="d-block p-2 bg-light border rounded" style="word-break:break-all">wget -q -O /dev/null "<?php echo h($cronUrl); ?>"</code>
                        <p class="mt-2 mb-0 text-muted">It only does work when the interval above has passed. Without a cron job, a visit to the portal starts the update in the background on servers running PHP-FPM.</p>
                    </details>
                </div>
            </div>
        </div>
    </div>

    <!-- Citations & publication lists -->
    <div class="card mt-4">
        <div class="card-header"><h5 class="mb-0"><i class="fas fa-quote-right me-2"></i>Citations &amp; publication lists</h5></div>
        <div class="card-body">
            <p class="small mb-3">Citations are checked against <strong>OpenAlex, Crossref and Semantic Scholar</strong> by DOI (and by title for papers without one); each paper shows the highest count, and researchers can add their Google Scholar figure for papers those indexes miss. The automatic update does this continuously; use these to do it now.</p>
            <div class="row g-3">
                <div class="col-lg-7">
                    <form method="post" class="d-flex gap-2" onsubmit="this.querySelector('button').disabled=true;this.querySelector('button').innerHTML='Working… up to 5 minutes';">
                        <?php echo vvu_csrf_field(); ?>
                        <input type="hidden" name="action" value="q_refresh_scholar">
                        <select name="scholar_id" class="form-select form-select-sm" required>
                            <option value="">— Complete one researcher's record —</option>
                            <?php foreach ($active as $id => $nm): ?><option value="<?php echo (int) $id; ?>"><?php echo h($nm); ?></option><?php endforeach; ?>
                        </select>
                        <button class="btn btn-sm btn-primary text-nowrap"><i class="fas fa-user-check"></i> Complete record</button>
                    </form>
                    <small class="text-muted">All their works from every OpenAlex record (including merged duplicates) and their ORCID, duplicates removed, citations re-checked.</small>
                </div>
                <div class="col-lg-5">
                    <form method="post" onsubmit="this.querySelector('button').disabled=true;this.querySelector('button').innerHTML='Checking… up to 5 minutes';">
                        <?php echo vvu_csrf_field(); ?>
                        <input type="hidden" name="action" value="q_refresh_citations">
                        <button class="btn btn-sm btn-outline-primary w-100"><i class="fas fa-rotate"></i> Re-check all citations now</button>
                    </form>
                    <small class="text-muted">Up to 1,500 papers per click, longest-unchecked first.</small>
                </div>
            </div>
        </div>
    </div>

    <!-- Duplicate suggestions -->
    <div class="card mt-4">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0"><i class="fas fa-people-arrows me-2"></i>Possible duplicate researchers <span class="badge bg-secondary"><?php echo count($groups); ?></span></h5>
        </div>
        <div class="card-body">
            <?php if (!$groups): ?>
                <p class="text-muted mb-3">No likely duplicates right now. “Run clean-up” merges new ones automatically; use the form below for anything it cannot recognise.</p>
            <?php endif; ?>
            <?php foreach ($groups as $ids): ?>
                <form method="post" class="border rounded p-2 mb-2 d-flex flex-wrap align-items-center gap-2">
                    <?php echo vvu_csrf_field(); ?>
                    <input type="hidden" name="keep_id" value="<?php echo (int) $ids[0]; ?>">
                    <?php foreach ($ids as $i => $sid): ?>
                        <input type="hidden" name="merge_ids[]" value="<?php echo (int) $sid; ?>">
                        <input type="hidden" name="ids[]" value="<?php echo (int) $sid; ?>">
                        <span class="badge <?php echo $i === 0 ? 'bg-primary' : 'bg-light text-dark border'; ?>">
                            <?php echo h($names[$sid]['full_name'] ?? '#' . $sid); ?> (<?php echo (int) ($names[$sid]['publications_count'] ?? 0); ?>)
                        </span>
                    <?php endforeach; ?>
                    <span class="ms-auto d-flex gap-1">
                        <button class="btn btn-sm btn-success" name="action" value="q_merge"><i class="fas fa-object-group"></i> Merge into first</button>
                        <button class="btn btn-sm btn-outline-secondary" name="action" value="q_not_same">Different people</button>
                    </span>
                </form>
            <?php endforeach; ?>

            <form method="post" class="row g-2 align-items-end mt-2">
                <?php echo vvu_csrf_field(); ?>
                <input type="hidden" name="action" value="q_merge">
                <div class="col-md-5">
                    <label class="form-label small fw-semibold">Keep this profile</label>
                    <select name="keep_id" class="form-select form-select-sm" required>
                        <option value="">— choose —</option>
                        <?php foreach ($active as $id => $n): ?><option value="<?php echo (int) $id; ?>"><?php echo h($n); ?></option><?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-5">
                    <label class="form-label small fw-semibold">…and merge into it (hold Ctrl to pick several)</label>
                    <select name="merge_ids[]" class="form-select form-select-sm" multiple size="4" required>
                        <?php foreach ($active as $id => $n): ?><option value="<?php echo (int) $id; ?>"><?php echo h($n); ?></option><?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-2"><button class="btn btn-sm btn-primary w-100" onclick="return confirm('Merge these profiles? You can undo it below.');">Merge</button></div>
            </form>
        </div>
    </div>

    <div class="row g-4 mt-0">
        <!-- Merge history -->
        <div class="col-lg-6">
            <div class="card h-100">
                <div class="card-header"><h5 class="mb-0"><i class="fas fa-clock-rotate-left me-2"></i>Merged profiles</h5></div>
                <div class="card-body p-0" style="max-height:420px;overflow:auto">
                    <table class="table table-sm mb-0 align-middle">
                        <tbody>
                            <?php if (!$merges): ?><tr><td class="text-muted p-3">No merges yet.</td></tr><?php endif; ?>
                            <?php foreach ($merges as $m): ?>
                                <tr class="<?php echo $m['undone_at'] ? 'text-muted' : ''; ?>">
                                    <td class="small ps-3"><?php echo h($m['merged_name']); ?> <i class="fas fa-arrow-right mx-1"></i>
                                        <a href="../research/scholar.php?p=<?php echo h(rawurlencode($m['keep_slug'])); ?>" target="_blank"><?php echo h($m['keep_name']); ?></a>
                                        <span class="text-muted">· <?php echo h($m['merged_by']); ?>, <?php echo h(date('j M', strtotime($m['created_at']))); ?></span></td>
                                    <td class="text-end pe-3">
                                        <?php if ($m['undone_at']): ?>
                                            <span class="badge bg-light text-muted border">undone</span>
                                        <?php else: ?>
                                            <form method="post" class="d-inline" onsubmit="return confirm('Undo this merge?');">
                                                <?php echo vvu_csrf_field(); ?>
                                                <input type="hidden" name="action" value="q_unmerge">
                                                <input type="hidden" name="merge_id" value="<?php echo (int) $m['id']; ?>">
                                                <button class="btn btn-sm btn-outline-secondary py-0">Undo</button>
                                            </form>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Sync log -->
        <div class="col-lg-6">
            <div class="card h-100">
                <div class="card-header"><h5 class="mb-0"><i class="fas fa-list-check me-2"></i>Update log</h5></div>
                <div class="card-body p-0" style="max-height:420px;overflow:auto">
                    <table class="table table-sm mb-0">
                        <tbody>
                            <?php if (!$log): ?><tr><td class="text-muted p-3">No updates have run yet.</td></tr><?php endif; ?>
                            <?php foreach ($log as $l): ?>
                                <tr>
                                    <td class="small ps-3 text-nowrap"><?php echo h(date('j M H:i', strtotime($l['started_at']))); ?><br>
                                        <span class="badge bg-<?php echo $l['ok'] ? 'success' : ($l['finished_at'] ? 'danger' : 'warning text-dark'); ?>"><?php echo h($l['trigger_by']); ?></span></td>
                                    <td class="small pe-3"><?php echo h($l['summary'] ?: 'Running…'); ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    <?php
}

/** "Reviews" tab. */
function vvurq_render_reviews(PDO $pdo)
{
    if (!vvur_quality_ready($pdo)) {
        echo '<div class="alert alert-warning">Import <code>sql/research_quality_migration.sql</code> first.</div>';
        return;
    }
    $rows = $pdo->query(
        "SELECT r.*, p.title FROM research_publication_reviews r
           JOIN research_publications p ON p.id = r.publication_id
          ORDER BY FIELD(r.status, 'pending', 'approved', 'rejected'), r.created_at DESC
          LIMIT 200"
    )->fetchAll(PDO::FETCH_ASSOC);
    $badge = ['pending' => 'warning text-dark', 'approved' => 'success', 'rejected' => 'secondary'];
    ?>
    <div class="card">
        <div class="card-header"><h5 class="mb-0"><i class="fas fa-star me-2 text-warning"></i>Reader ratings &amp; reviews</h5></div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table align-middle mb-0">
                    <thead class="table-light"><tr><th>Publication</th><th>Reader</th><th>Rating</th><th style="width:40%">Review</th><th>Status</th><th class="text-end">Actions</th></tr></thead>
                    <tbody>
                        <?php if (!$rows): ?><tr><td colspan="6" class="text-center text-muted py-4">No ratings yet.</td></tr><?php endif; ?>
                        <?php foreach ($rows as $r): ?>
                            <tr>
                                <td class="small"><a href="../research/publication.php?id=<?php echo (int) $r['publication_id']; ?>" target="_blank"><?php echo h(mb_substr($r['title'], 0, 80)); ?></a></td>
                                <td class="small"><strong><?php echo h($r['reviewer_name']); ?></strong><br><span class="text-muted"><?php echo h($r['reviewer_role']); ?></span></td>
                                <td class="text-nowrap text-warning"><?php echo str_repeat('★', (int) $r['rating']) . '<span class="text-muted">' . str_repeat('☆', 5 - (int) $r['rating']) . '</span>'; ?></td>
                                <td class="small"><?php echo $r['comment'] ? nl2br(h($r['comment'])) : '<span class="text-muted">— rating only —</span>'; ?></td>
                                <td><span class="badge bg-<?php echo $badge[$r['status']]; ?>"><?php echo h($r['status']); ?></span></td>
                                <td class="text-end text-nowrap">
                                    <form method="post" class="d-inline">
                                        <?php echo vvu_csrf_field(); ?>
                                        <input type="hidden" name="review_id" value="<?php echo (int) $r['id']; ?>">
                                        <?php if ($r['status'] !== 'approved'): ?><button class="btn btn-sm btn-success" name="action" value="rv_approve" title="Publish"><i class="fas fa-check"></i></button><?php endif; ?>
                                        <?php if ($r['status'] !== 'rejected'): ?><button class="btn btn-sm btn-outline-secondary" name="action" value="rv_reject" title="Hide"><i class="fas fa-eye-slash"></i></button><?php endif; ?>
                                        <button class="btn btn-sm btn-outline-danger" name="action" value="rv_delete" title="Delete" onclick="return confirm('Delete this review?');"><i class="fas fa-trash"></i></button>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <?php
}

/** Site base URL from inside admin/ (for the cron URL). */
function ra_base_url_admin()
{
    $https = (!empty($_SERVER['HTTPS']) && strtolower($_SERVER['HTTPS']) !== 'off')
          || (($_SERVER['HTTP_X_FORWARDED_PROTO'] ?? '') === 'https');
    $host  = preg_replace('/[^A-Za-z0-9.:-]/', '', $_SERVER['HTTP_HOST'] ?? 'vvu.edu.gh');
    $root  = rtrim(dirname(dirname(str_replace('\\', '/', (string) ($_SERVER['SCRIPT_NAME'] ?? '/admin/x.php')))), '/') . '/';
    return ($https ? 'https' : 'http') . '://' . $host . $root;
}

} // vvurq_handle_post
