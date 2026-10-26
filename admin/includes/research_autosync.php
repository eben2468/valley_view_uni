<?php
/**
 * VVU Scholar — automatic updates.
 *
 * One run, vvur_auto_sync(), does in order and within a time budget:
 *
 *   1. OpenAlex: papers by VVU-affiliated authors published since last year
 *      (strict affiliation check — research_quality.php).
 *   2. ORCID: new works on the ORCID record of every researcher who has one,
 *      claimed profiles first, a batch per run in rotation.
 *   3. Google Scholar: researchers with a Scholar profile, best effort —
 *      Google blocks most servers, and the run simply moves on when it does.
 *      (ResearchGate has no data access at all; researchers can import from
 *      it through their dashboard's BibTeX import.)
 *   4. Impact figures: citations, FWCI and citation percentile from OpenAlex;
 *      Mendeley readers and Altmetric attention from Altmetric.
 *   5. Clean-up: duplicate papers collapsed, split profiles merged, totals
 *      recounted.
 *
 * Triggered by: research/cron.php (a real cron job — recommended), the
 * "Run update now" button in Admin → Research Portal → Data Quality & Sync,
 * and — when no cron is set up — a visit to the portal once the interval has
 * passed (the work runs after the page has been sent, so the visitor never
 * waits). Every run is logged in research_sync_log.
 */

if (!defined('VVU_RESEARCH_AUTOSYNC')) {
    define('VVU_RESEARCH_AUTOSYNC', 1);

require_once __DIR__ . '/research_import.php';
require_once __DIR__ . '/research_openalex_sync.php';
require_once __DIR__ . '/research_quality.php';

/** Is a scheduled run due? */
function vvur_sync_due(PDO $pdo)
{
    if (r_set($pdo, 'auto_sync_enabled', '0') !== '1') {
        return false;
    }
    $hours = max(1, (int) r_set($pdo, 'auto_sync_hours', '24'));
    $last  = strtotime((string) r_set($pdo, 'last_auto_sync_at', '')) ?: 0;
    return time() - $last >= $hours * 3600;
}

function vvur_set_setting(PDO $pdo, $key, $value)
{
    $pdo->prepare(
        "INSERT INTO research_settings (setting_key, setting_value) VALUES (?, ?)
         ON DUPLICATE KEY UPDATE setting_value = VALUES(setting_value)"
    )->execute([$key, (string) $value]);
}

/**
 * Adds a work from a researcher's own record (ORCID / Scholar) to their
 * profile: links the existing record when the work is already in the portal,
 * otherwise inserts it. Returns 'added' | 'linked' | 'skipped'.
 */
function vvur_add_work_for(PDO $pdo, array $p, array $scholar)
{
    $p['title'] = trim(strip_tags((string) ($p['title'] ?? '')));
    if ($p['title'] === '' || mb_strlen($p['title']) < 8) {
        return 'skipped';
    }
    $sid = (int) $scholar['id'];
    $dup = vvur_find_duplicate($pdo, $p);
    if ($dup) {
        $ex = $pdo->prepare("SELECT 1 FROM research_author_exclusions WHERE publication_id = ? AND scholar_id = ?");
        $ex->execute([$dup, $sid]);
        if ($ex->fetchColumn()) {
            return 'skipped';                       // they said "Not mine"
        }
        $st = $pdo->prepare("SELECT is_active FROM research_publications WHERE id = ?");
        $st->execute([$dup]);
        if (!(int) $st->fetchColumn()) {
            return 'skipped';                       // withdrawn copy / non-VVU record
        }
        $ins = $pdo->prepare("INSERT IGNORE INTO research_publication_authors (publication_id, scholar_id, author_order) VALUES (?,?,0)");
        $ins->execute([$dup, $sid]);
        if ($ins->rowCount()) {
            $pdo->prepare("UPDATE research_publications SET scholar_id = ? WHERE id = ? AND scholar_id IS NULL")->execute([$sid, $dup]);
            return 'linked';
        }
        return 'skipped';
    }

    $year = (int) ($p['pub_year'] ?? 0);
    $pdo->prepare(
        "INSERT INTO research_publications
            (title, authors, scholar_id, unit_id, pub_type, venue, publisher, pub_year, volume, issue, pages,
             doi, url, abstract, keywords, citations, is_open_access, is_active, source, external_id)
         VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,1,?,?)"
    )->execute([
        mb_substr($p['title'], 0, 600), mb_substr((string) ($p['authors'] ?? '') ?: $scholar['full_name'], 0, 1200),
        $sid, $scholar['unit_id'] ?: null,
        in_array($p['pub_type'] ?? '', array_keys(r_publication_types()), true) ? $p['pub_type'] : 'journal',
        mb_substr((string) ($p['venue'] ?? ''), 0, 400), mb_substr((string) ($p['publisher'] ?? ''), 0, 300),
        ($year >= 1950 && $year <= (int) date('Y') + 1) ? $year : null,
        mb_substr((string) ($p['volume'] ?? ''), 0, 40), mb_substr((string) ($p['issue'] ?? ''), 0, 40),
        mb_substr((string) ($p['pages'] ?? ''), 0, 60),
        ($p['doi'] ?? '') !== '' ? mb_substr(preg_replace('#^https?://(dx\.)?doi\.org/#i', '', $p['doi']), 0, 200) : null,
        ($p['url'] ?? '') !== '' ? mb_substr($p['url'], 0, 600) : null,
        (string) ($p['abstract'] ?? ''), mb_substr((string) ($p['keywords'] ?? ''), 0, 600),
        max(0, (int) ($p['citations'] ?? 0)), !empty($p['is_open_access']) ? 1 : 0,
        in_array($p['source'] ?? '', ['orcid', 'google_scholar', 'crossref', 'bibtex'], true) ? $p['source'] : 'orcid',
        ($p['external_id'] ?? '') !== '' ? mb_substr($p['external_id'], 0, 200) : null,
    ]);
    $pid = (int) $pdo->lastInsertId();
    $pdo->prepare("INSERT IGNORE INTO research_publication_authors (publication_id, scholar_id, author_order) VALUES (?,?,0)")->execute([$pid, $sid]);
    return 'added';
}

/** Researchers to pull from a source, claimed profiles first, least recently synced first. */
function vvur_sync_candidates(PDO $pdo, $column, $limit)
{
    $hasAccounts = vvur_table_exists($pdo, 'research_accounts');
    $owned = $hasAccounts
        ? "(SELECT COUNT(*) FROM research_accounts a WHERE a.scholar_id = s.id AND a.status = 'active')"
        : "0";
    // By default only researchers who claimed their profile (and so linked
    // the identifier themselves) are pulled; identifiers on imported profiles
    // are unverified. "all" pulls everyone who has one.
    $scope = r_set($pdo, 'auto_sync_orcid_scope', 'claimed');
    if ($scope !== 'all') {
        if (!$hasAccounts) {
            return [];
        }
        $owned_only = " AND $owned > 0";
    } else {
        $owned_only = '';
    }
    return $pdo->query(
        "SELECT s.id, s.full_name, s.unit_id, s.$column AS ident
           FROM research_scholars s
          WHERE s.is_active = 1 AND s.merged_into IS NULL AND s.$column IS NOT NULL AND s.$column <> ''$owned_only
          ORDER BY $owned DESC, s.works_synced_at IS NOT NULL, s.works_synced_at ASC
          LIMIT " . (int) $limit
    )->fetchAll(PDO::FETCH_ASSOC);
}

/**
 * Impact figures for the publications refreshed longest ago: OpenAlex for
 * citations / FWCI / percentile (50 per request), then Altmetric for readers
 * and attention (one request per DOI, so fewer per run).
 */
function vvur_refresh_metrics(PDO $pdo, $deadline, $limit = 400, $altLimit = 40)
{
    $s = ['openalex' => 0, 'altmetric' => 0, 'errors' => []];
    $rows = $pdo->query(
        "SELECT id, doi, external_id, source FROM research_publications
          WHERE is_active = 1 AND ((source = 'openalex' AND external_id LIKE 'W%') OR (doi IS NOT NULL AND doi <> ''))
          ORDER BY metrics_synced_at IS NOT NULL, metrics_synced_at ASC
          LIMIT " . (int) $limit
    )->fetchAll(PDO::FETCH_ASSOC);

    $byW = [];
    $byDoi = [];
    foreach ($rows as $r) {
        if ($r['source'] === 'openalex' && preg_match('/^W\d+$/', (string) $r['external_id'])) {
            $byW[$r['external_id']] = (int) $r['id'];
        } elseif ($r['doi']) {
            $byDoi[strtolower(preg_replace('#^https?://(dx\.)?doi\.org/#i', '', $r['doi']))] = (int) $r['id'];
        }
    }

    $upd = $pdo->prepare(
        "UPDATE research_publications
            SET citations = GREATEST(citations, ?), fwci = ?, citation_percentile = ?, metrics_synced_at = NOW()
          WHERE id = ?"
    );
    $apply = function (array $works) use (&$s, $upd, $byW, $byDoi) {
        foreach ($works as $w) {
            $wid = substr(strrchr((string) ($w['id'] ?? ''), '/'), 1);
            $doi = strtolower(preg_replace('#^https?://doi\.org/#i', '', (string) ($w['doi'] ?? '')));
            $pid = $byW[$wid] ?? ($byDoi[$doi] ?? 0);
            if (!$pid) { continue; }
            $pct = $w['citation_normalized_percentile']['value'] ?? null;
            $upd->execute([
                (int) ($w['cited_by_count'] ?? 0),
                isset($w['fwci']) ? round((float) $w['fwci'], 2) : null,
                $pct !== null ? round((float) $pct * 100, 2) : null,
                $pid,
            ]);
            $s['openalex']++;
        }
    };
    $select = 'id,doi,cited_by_count,fwci,citation_normalized_percentile';
    foreach (array_chunk(array_keys($byW), 50) as $chunk) {
        if (microtime(true) > $deadline) { break; }
        $res = vvur_http_get('https://api.openalex.org/works?' . http_build_query(vvur_oa_q([
            'filter' => 'openalex:' . implode('|', $chunk), 'per-page' => 50, 'select' => $select, 'mailto' => 'research@vvu.edu.gh',
        ])), ['Accept: application/json']);
        if (!$res['ok']) { $s['errors'][] = 'OpenAlex metrics: ' . vvur_oa_error($res); break; }
        $apply(json_decode($res['body'], true)['results'] ?? []);
    }
    foreach (array_chunk(array_keys($byDoi), 40) as $chunk) {
        if (microtime(true) > $deadline) { break; }
        $res = vvur_http_get('https://api.openalex.org/works?' . http_build_query(vvur_oa_q([
            'filter' => 'doi:' . implode('|', $chunk), 'per-page' => 50, 'select' => $select, 'mailto' => 'research@vvu.edu.gh',
        ])), ['Accept: application/json']);
        if (!$res['ok']) { $s['errors'][] = 'OpenAlex metrics (DOI): ' . vvur_oa_error($res); break; }
        $apply(json_decode($res['body'], true)['results'] ?? []);
    }

    // Altmetric — one DOI at a time, politely. Altmetric now requires an API
    // key (free for researchers / institutions on request); without one the
    // step is skipped and only the OpenAlex figures are kept fresh.
    $altKey = trim((string) r_set($pdo, 'altmetric_api_key', ''));
    if ($altKey === '') {
        $altLimit = 0;
    }
    $alt = $altLimit <= 0 ? [] : $pdo->query(
        "SELECT id, doi FROM research_publications
          WHERE is_active = 1 AND doi IS NOT NULL AND doi <> ''
          ORDER BY metrics_synced_at IS NOT NULL, metrics_synced_at ASC, id DESC
          LIMIT " . (int) $altLimit
    )->fetchAll(PDO::FETCH_ASSOC);
    $altUpd = $pdo->prepare("UPDATE research_publications SET readers = ?, altmetric_score = ?, metrics_synced_at = NOW() WHERE id = ?");
    $altNone = $pdo->prepare("UPDATE research_publications SET metrics_synced_at = NOW() WHERE id = ?");
    foreach ($alt as $r) {
        if (microtime(true) > $deadline) { break; }
        $doi = preg_replace('#^https?://(dx\.)?doi\.org/#i', '', $r['doi']);
        $res = vvur_http_get('https://api.altmetric.com/v1/doi/' . str_replace('%2F', '/', rawurlencode($doi)) . '?key=' . rawurlencode($altKey), ['Accept: application/json']);
        if (!$res['ok']) {
            if ((int) $res['status'] === 404) { $altNone->execute([$r['id']]); continue; }   // no attention yet
            $s['errors'][] = 'Altmetric: ' . $res['error'];
            break;                                                                     // rate-limited or blocked: stop for this run
        }
        $j = json_decode($res['body'], true) ?: [];
        $readers = isset($j['readers']['mendeley']) ? (int) $j['readers']['mendeley'] : null;
        $altUpd->execute([$readers, isset($j['score']) ? round((float) $j['score'], 2) : null, $r['id']]);
        $s['altmetric']++;
        usleep(250000);
    }
    return $s;
}

/**
 * One automatic update. $trigger: 'cron' | 'admin' | 'visit'. $budget is the
 * number of seconds it may take. Returns ['ok', 'summary'].
 */
function vvur_auto_sync(PDO $pdo, $trigger = 'cron', $budget = 240)
{
    if (!vvur_quality_ready($pdo)) {
        return ['ok' => false, 'summary' => 'Import sql/research_quality_migration.sql first.'];
    }
    // One run at a time, across all PHP workers.
    $lock = (int) $pdo->query("SELECT GET_LOCK('vvu_research_sync', 0)")->fetchColumn();
    if ($lock !== 1) {
        return ['ok' => false, 'summary' => 'An update is already running.'];
    }

    @set_time_limit($budget + 60);
    @ignore_user_abort(true);
    $deadline = microtime(true) + $budget;
    $started  = date('Y-m-d H:i:s');
    vvur_set_setting($pdo, 'last_auto_sync_at', $started);     // claim the slot at once
    $pdo->prepare("INSERT INTO research_sync_log (trigger_by, started_at) VALUES (?, ?)")->execute([$trigger, $started]);
    $logId = (int) $pdo->lastInsertId();

    $parts  = [];
    $errors = [];
    try {
        /* 1. OpenAlex — recent VVU work */
        $fetch = vvur_openalex_fetch([
            'from_year'   => (int) date('Y') - 1,
            'pages'       => 5,
            'time_budget' => max(15, (int) (($deadline - microtime(true)) * 0.3)),
        ]);
        if ($fetch['ok'] && $fetch['works']) {
            $q = vvur_quality_run($pdo, $fetch['works']);
            $sync = $q['sync'];
            $parts[] = sprintf('OpenAlex: %d works read, %d new', $sync['works_seen'] ?? 0, $sync['pub_inserted'] ?? 0);
        } elseif (!$fetch['ok']) {
            $errors[] = $fetch['error'];
        }

        /* 2. ORCID */
        $orcidStats = ['people' => 0, 'added' => 0, 'linked' => 0];
        foreach (vvur_sync_candidates($pdo, 'orcid', 25) as $sch) {
            if (microtime(true) > $deadline - 30) { break; }
            $res = vvur_orcid_works($sch['ident'], false, 200);
            $pdo->prepare("UPDATE research_scholars SET works_synced_at = NOW() WHERE id = ?")->execute([$sch['id']]);
            if (!$res['ok']) { $errors[] = 'ORCID ' . $sch['ident'] . ': ' . $res['error']; continue; }
            $orcidStats['people']++;
            foreach ($res['items'] as $item) {
                $r = vvur_add_work_for($pdo, $item, $sch);
                if (isset($orcidStats[$r])) { $orcidStats[$r]++; }
            }
        }
        $parts[] = sprintf('ORCID: %d researchers checked, %d works added, %d linked', $orcidStats['people'], $orcidStats['added'], $orcidStats['linked']);

        /* 3. Google Scholar (best effort) */
        $gsStats = ['people' => 0, 'added' => 0, 'linked' => 0];
        foreach (vvur_sync_candidates($pdo, 'google_scholar_id', 5) as $sch) {
            if (microtime(true) > $deadline - 25) { break; }
            $res = vvur_google_scholar_profile($sch['ident'], 100);
            if (!$res['ok']) {
                // A CAPTCHA for one profile means a CAPTCHA for all: stop here.
                $errors[] = 'Google Scholar: blocked for this server (researchers can still import via BibTeX)';
                break;
            }
            $gsStats['people']++;
            $pdo->prepare("UPDATE research_scholars SET works_synced_at = NOW() WHERE id = ?")->execute([$sch['id']]);
            foreach ($res['items'] as $item) {
                $item['source'] = 'google_scholar';
                $r = vvur_add_work_for($pdo, $item, $sch);
                if (isset($gsStats[$r])) { $gsStats[$r]++; }
            }
        }
        if ($gsStats['people']) {
            $parts[] = sprintf('Google Scholar: %d checked, %d added, %d linked', $gsStats['people'], $gsStats['added'], $gsStats['linked']);
        }

        /* 4. Impact figures */
        if (microtime(true) < $deadline - 10) {
            $m = vvur_refresh_metrics($pdo, $deadline - 5);
            $parts[] = sprintf('Impact figures: %d from OpenAlex, %d from Altmetric', $m['openalex'], $m['altmetric']);
            $errors = array_merge($errors, $m['errors']);
        }

        /* 5. Clean-up */
        $q = vvur_quality_run($pdo, []);
        $parts[] = sprintf('Clean-up: %d duplicate papers removed, %d profiles merged',
            $q['publications']['hidden'] ?? 0, $q['scholars_merged'] ?? 0);
        r_recount_all($pdo);
        $ok = true;
    } catch (Exception $e) {
        $ok = false;
        $errors[] = $e->getMessage();
        error_log('VVU Scholar auto-sync: ' . $e->getMessage());
    }

    $summary = implode('. ', $parts) . '.';
    if ($errors) {
        $summary .= ' Notes: ' . implode('; ', array_slice(array_unique($errors), 0, 5));
    }
    $pdo->prepare("UPDATE research_sync_log SET finished_at = NOW(), ok = ?, summary = ? WHERE id = ?")
        ->execute([$ok ? 1 : 0, mb_substr($summary, 0, 4000), $logId]);
    $pdo->query("SELECT RELEASE_LOCK('vvu_research_sync')");
    return ['ok' => $ok, 'summary' => $summary];
}

} // VVU_RESEARCH_AUTOSYNC
