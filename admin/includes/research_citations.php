<?php
/**
 * VVU Scholar — verified citations and complete publication lists.
 *
 * Citations. No single index sees every citation, so each paper is checked
 * against three open sources by DOI (and by OpenAlex id):
 *   OpenAlex          cited_by_count, FWCI, percentile, citations per year
 *   Crossref          is-referenced-by-count
 *   Semantic Scholar  citationCount, influentialCitationCount
 * Each count is stored separately and "citations" is the highest of them —
 * the closest open equivalent to the count Google Scholar shows (Google
 * Scholar itself cannot be read by a server).
 *
 * Publication lists. vvur_pull_researcher_works() gathers everything a
 * researcher has published: every OpenAlex author record of theirs (the main
 * one and the duplicates merged into it) plus their ORCID record. Works they
 * published elsewhere are added with vvu_output = 0: they appear on the
 * profile but do not count towards the University's own totals.
 *
 * Schema: sql/research_citations_migration.sql
 */

if (!defined('VVU_RESEARCH_CITATIONS')) {
    define('VVU_RESEARCH_CITATIONS', 1);

require_once __DIR__ . '/research_import.php';
require_once __DIR__ . '/research_openalex_sync.php';
require_once __DIR__ . '/research_quality.php';
require_once __DIR__ . '/research_autosync.php';   // vvur_add_work_for()

function vvur_citations_ready(PDO $pdo)
{
    static $ok = null;
    if ($ok === null) {
        try {
            $pdo->query("SELECT cites_openalex, cites_crossref, cites_s2, citations_5y, vvu_output FROM research_publications LIMIT 1");
            $pdo->query("SELECT 1 FROM research_citations_by_year LIMIT 1");
            $ok = true;
        } catch (Exception $e) {
            $ok = false;
        }
    }
    return $ok;
}

/** JSON POST (Semantic Scholar's batch endpoint). */
function vvur_http_post_json($url, array $data, array $headers = [])
{
    $out = ['ok' => false, 'body' => '', 'status' => 0, 'error' => ''];
    if (!function_exists('curl_init')) {
        $out['error'] = 'cURL is not available on this server.';
        return $out;
    }
    $ch = curl_init($url);
    curl_setopt_array($ch, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_POST           => true,
        CURLOPT_POSTFIELDS     => json_encode($data),
        CURLOPT_TIMEOUT        => 40,
        CURLOPT_CONNECTTIMEOUT => 10,
        CURLOPT_USERAGENT      => 'Mozilla/5.0 (compatible; VVUScholarBot/1.0; +https://vvu.edu.gh/research/)',
        CURLOPT_HTTPHEADER     => array_merge(['Content-Type: application/json', 'Accept: application/json'], $headers),
    ]);
    $body = curl_exec($ch);
    $out['status'] = (int) curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $err = curl_error($ch);
    curl_close($ch);
    if ($body === false) {
        $out['error'] = $err ?: 'The request failed.';
        return $out;
    }
    $out['body'] = $body;
    $out['ok'] = $out['status'] >= 200 && $out['status'] < 300;
    if (!$out['ok']) {
        $out['error'] = 'The source answered HTTP ' . $out['status'] . '.';
    }
    return $out;
}

function vvur_bare_doi($doi)
{
    $d = strtolower(trim((string) $doi));
    $d = preg_replace('#^(https?://)?(dx\.)?doi\.org/#', '', $d);
    return preg_replace('#^doi:\s*#', '', $d);
}

/**
 * Re-checks citation counts for publications, oldest check first.
 * $where narrows the set (SQL on alias p), e.g. one researcher's papers.
 * Returns counts per source and any errors.
 */
function vvur_refresh_citations(PDO $pdo, $deadline, $where = '1=1', array $params = [], $limit = 500)
{
    $s = ['papers' => 0, 'openalex' => 0, 'crossref' => 0, 's2' => 0, 'changed' => 0, 'errors' => []];
    if (!vvur_citations_ready($pdo)) {
        $s['errors'][] = 'Import sql/research_citations_migration.sql first.';
        return $s;
    }
    $st = $pdo->prepare(
        "SELECT p.id, p.doi, p.external_id, p.source, p.citations FROM research_publications p
          WHERE p.is_active = 1 AND ($where)
          ORDER BY p.citations_synced_at IS NOT NULL, p.citations_synced_at ASC
          LIMIT " . (int) $limit
    );
    $st->execute($params);
    $rows = $st->fetchAll(PDO::FETCH_ASSOC);
    $s['papers'] = count($rows);
    if (!$rows) {
        return $s;
    }

    // Records with neither a DOI nor an OpenAlex id (typed in by hand, or a
    // ORCID entry without identifiers): find the work on OpenAlex by its
    // title, and adopt its DOI when the title is unmistakably the same.
    $lookups = 0;
    foreach ($rows as &$r) {
        if ($lookups >= 40 || microtime(true) > $deadline - 20) { break; }
        $hasW = $r['source'] === 'openalex' && preg_match('/^W\d+$/', (string) $r['external_id']);
        if ($hasW || vvur_bare_doi($r['doi']) !== '') { continue; }
        $t = $pdo->prepare("SELECT title, pub_year FROM research_publications WHERE id = ?");
        $t->execute([$r['id']]);
        $me = $t->fetch(PDO::FETCH_ASSOC);
        if (!$me || mb_strlen($me['title']) < 20) { continue; }
        $lookups++;
        $res = vvur_http_get('https://api.openalex.org/works?' . http_build_query(vvur_oa_q([
            // Words only: OpenAlex rejects some punctuation in a search.
            'search' => implode(' ', array_slice(preg_split('/[^\p{L}\p{N}]+/u', $me['title'], -1, PREG_SPLIT_NO_EMPTY), 0, 18)), 'per-page' => 5, 'select' => 'id,doi,title,publication_year', 'mailto' => 'research@vvu.edu.gh',
        ])), ['Accept: application/json']);
        if (!$res['ok']) { $s['errors'][] = 'OpenAlex title search: ' . vvur_oa_error($res); break; }
        foreach ((json_decode($res['body'], true)['results'] ?? []) as $w) {
            $cand = ['title' => (string) ($w['title'] ?? ''), 'doi' => ''];
            if (vvur_same_work(['title' => $me['title'], 'doi' => ''], $cand)
                && (!$me['pub_year'] || !$w['publication_year'] || abs((int) $me['pub_year'] - (int) $w['publication_year']) <= 1)) {
                $doi = vvur_bare_doi($w['doi'] ?? '');
                if ($doi !== '') {
                    $pdo->prepare("UPDATE research_publications SET doi = ? WHERE id = ? AND (doi IS NULL OR doi = '')")->execute([$doi, $r['id']]);
                    $r['doi'] = $doi;
                    $s['matched'] = ($s['matched'] ?? 0) + 1;
                }
                break;
            }
        }
    }
    unset($r);

    $byW = [];
    $byDoi = [];
    $found = [];       // pid => ['oa'=>, 'cr'=>, 's2'=>, 'inf'=>, 'fwci'=>, 'pct'=>, 'years'=>[]]
    foreach ($rows as $r) {
        $pid = (int) $r['id'];
        $found[$pid] = [];
        if ($r['source'] === 'openalex' && preg_match('/^W\d+$/', (string) $r['external_id'])) {
            $byW[$r['external_id']] = $pid;
        }
        $d = vvur_bare_doi($r['doi']);
        if ($d !== '' && strpos($d, '10.') === 0) {
            $byDoi[$d] = $pid;
        }
    }

    /* ---- OpenAlex: by work id, then by DOI for the rest -------------- */
    $select = 'id,doi,cited_by_count,fwci,citation_normalized_percentile,counts_by_year';
    $takeOa = function (array $works) use (&$found, &$s, $byW, $byDoi) {
        foreach ($works as $w) {
            $wid = substr(strrchr((string) ($w['id'] ?? ''), '/'), 1);
            $pid = $byW[$wid] ?? ($byDoi[vvur_bare_doi($w['doi'] ?? '')] ?? 0);
            if (!$pid || isset($found[$pid]['oa'])) { continue; }
            $years = [];
            foreach (($w['counts_by_year'] ?? []) as $y) {
                $years[(int) $y['year']] = (int) $y['cited_by_count'];
            }
            $pct = $w['citation_normalized_percentile']['value'] ?? null;
            $found[$pid]['oa']    = (int) ($w['cited_by_count'] ?? 0);
            $found[$pid]['fwci']  = isset($w['fwci']) ? round((float) $w['fwci'], 2) : null;
            $found[$pid]['pct']   = $pct !== null ? round((float) $pct * 100, 2) : null;
            $found[$pid]['years'] = $years;
            $s['openalex']++;
        }
    };
    foreach (array_chunk(array_keys($byW), 50) as $chunk) {
        if (microtime(true) > $deadline) { break; }
        $res = vvur_http_get('https://api.openalex.org/works?' . http_build_query(vvur_oa_q([
            'filter' => 'openalex:' . implode('|', $chunk), 'per-page' => 50, 'select' => $select, 'mailto' => 'research@vvu.edu.gh',
        ])), ['Accept: application/json']);
        if (!$res['ok']) { $s['errors'][] = 'OpenAlex: ' . vvur_oa_error($res); break; }
        $takeOa(json_decode($res['body'], true)['results'] ?? []);
    }
    $needDoi = [];
    foreach ($byDoi as $d => $pid) {
        if (!isset($found[$pid]['oa'])) { $needDoi[] = $d; }
    }
    foreach (array_chunk($needDoi, 40) as $chunk) {
        if (microtime(true) > $deadline) { break; }
        $res = vvur_http_get('https://api.openalex.org/works?' . http_build_query(vvur_oa_q([
            'filter' => 'doi:' . implode('|', $chunk), 'per-page' => 50, 'select' => $select, 'mailto' => 'research@vvu.edu.gh',
        ])), ['Accept: application/json']);
        if (!$res['ok']) { $s['errors'][] = 'OpenAlex (DOI): ' . vvur_oa_error($res); break; }
        $takeOa(json_decode($res['body'], true)['results'] ?? []);
    }

    /* ---- Crossref: is-referenced-by-count, 40 DOIs per request -------- */
    foreach (array_chunk(array_keys($byDoi), 40) as $chunk) {
        if (microtime(true) > $deadline) { break; }
        $filter = implode(',', array_map(static function ($d) { return 'doi:' . $d; }, $chunk));
        $res = vvur_http_get('https://api.crossref.org/works?' . http_build_query([
            'filter' => $filter, 'rows' => 100, 'select' => 'DOI,is-referenced-by-count', 'mailto' => 'research@vvu.edu.gh',
        ]), ['Accept: application/json']);
        if (!$res['ok']) { $s['errors'][] = 'Crossref: ' . $res['error']; break; }
        foreach ((json_decode($res['body'], true)['message']['items'] ?? []) as $it) {
            $pid = $byDoi[vvur_bare_doi($it['DOI'] ?? '')] ?? 0;
            if ($pid) {
                $found[$pid]['cr'] = (int) ($it['is-referenced-by-count'] ?? 0);
                $s['crossref']++;
            }
        }
    }

    /* ---- Semantic Scholar: batch of up to 400 DOIs -------------------- */
    $s2key = trim((string) r_set($pdo, 's2_api_key', ''));
    foreach (array_chunk(array_keys($byDoi), 400) as $chunk) {
        if (microtime(true) > $deadline) { break; }
        $res = vvur_http_post_json(
            'https://api.semanticscholar.org/graph/v1/paper/batch?fields=citationCount,influentialCitationCount',
            ['ids' => array_map(static function ($d) { return 'DOI:' . $d; }, $chunk)],
            $s2key !== '' ? ['x-api-key: ' . $s2key] : []
        );
        if (!$res['ok']) {
            $s['errors'][] = 'Semantic Scholar: ' . ($res['status'] === 429 ? 'busy (rate limit) — will retry next run' : $res['error']);
            break;
        }
        foreach ((json_decode($res['body'], true) ?: []) as $i => $paper) {
            if (!is_array($paper) || !isset($chunk[$i])) { continue; }
            $pid = $byDoi[$chunk[$i]] ?? 0;
            if ($pid) {
                $found[$pid]['s2']  = (int) ($paper['citationCount'] ?? 0);
                $found[$pid]['inf'] = (int) ($paper['influentialCitationCount'] ?? 0);
                $s['s2']++;
            }
        }
    }

    /* ---- Write ---------------------------------------------------------- */
    $year5 = (int) date('Y') - 4;
    $upd = $pdo->prepare(
        "UPDATE research_publications
            SET cites_openalex = COALESCE(?, cites_openalex),
                cites_crossref = COALESCE(?, cites_crossref),
                cites_s2 = COALESCE(?, cites_s2),
                influential_citations = COALESCE(?, influential_citations),
                fwci = COALESCE(?, fwci),
                citation_percentile = COALESCE(?, citation_percentile),
                citations_5y = COALESCE(?, citations_5y),
                citations_synced_at = NOW(), metrics_synced_at = NOW()
          WHERE id = ?"
    );
    $best = $pdo->prepare(r_best_citations_sql());
    $delYears = $pdo->prepare("DELETE FROM research_citations_by_year WHERE publication_id = ?");
    $insYear  = $pdo->prepare("INSERT INTO research_citations_by_year (publication_id, year, citations) VALUES (?,?,?)");
    $before = [];
    foreach ($rows as $r) { $before[(int) $r['id']] = (int) $r['citations']; }

    foreach ($found as $pid => $f) {
        $five = null;
        if (isset($f['years'])) {
            $five = 0;
            foreach ($f['years'] as $y => $c) { if ($y >= $year5) { $five += $c; } }
            $delYears->execute([$pid]);
            foreach ($f['years'] as $y => $c) { if ($c > 0) { $insYear->execute([$pid, $y, $c]); } }
        }
        $upd->execute([$f['oa'] ?? null, $f['cr'] ?? null, $f['s2'] ?? null, $f['inf'] ?? null,
                       $f['fwci'] ?? null, $f['pct'] ?? null, $five, $pid]);
        $best->execute([$pid]);
    }
    if ($found) {
        $in = implode(',', array_keys($found));
        foreach ($pdo->query("SELECT id, citations FROM research_publications WHERE id IN ($in)") as $r) {
            if ((int) $r['citations'] !== ($before[(int) $r['id']] ?? -1)) { $s['changed']++; }
        }
    }
    return $s;
}

/** Every OpenAlex author id belonging to a profile: its own and those merged into it. */
function vvur_openalex_ids_for(PDO $pdo, $sid)
{
    $st = $pdo->prepare(
        "SELECT openalex_id FROM research_scholars
          WHERE (id = ? OR merged_into = ?) AND openalex_id IS NOT NULL AND openalex_id <> ''"
    );
    $st->execute([(int) $sid, (int) $sid]);
    return array_values(array_unique(array_filter($st->fetchAll(PDO::FETCH_COLUMN))));
}

/**
 * Completes one researcher's publication list from OpenAlex (all of their
 * author records) and ORCID, then refreshes those papers' citations and the
 * profile totals. Returns a stats array with a 'summary' line.
 */
function vvur_pull_researcher_works(PDO $pdo, $sid, $deadline = null)
{
    $deadline = $deadline ?: microtime(true) + 150;
    $out = ['added' => 0, 'linked' => 0, 'skipped' => 0, 'outside' => 0, 'errors' => [], 'summary' => ''];
    $st = $pdo->prepare("SELECT * FROM research_scholars WHERE id = ?");
    $st->execute([(int) $sid]);
    $sch = $st->fetch(PDO::FETCH_ASSOC);
    if (!$sch) {
        $out['summary'] = 'Researcher not found.';
        return $out;
    }
    $sid = (int) $sch['id'];

    // 1. OpenAlex — every author record of this person.
    $ids = vvur_openalex_ids_for($pdo, $sid);
    if ($ids) {
        $fetch = vvur_openalex_fetch([
            'institution' => '',
            'author'      => implode('|', $ids),
            'pages'       => 10,
            'time_budget' => max(20, (int) (($deadline - microtime(true)) * 0.5)),
        ]);
        if (!$fetch['ok']) {
            $out['errors'][] = $fetch['error'];
        }
        foreach ($fetch['works'] as $w) {
            $item = vvur_openalex_item($w);
            // Was this person writing as VVU on this paper?
            $atVvu = false;
            foreach (($w['authorships'] ?? []) as $a) {
                if (in_array(vvur_oa_id($a['author']['id'] ?? ''), $ids, true) && vvur_authorship_is_vvu($a)) {
                    $atVvu = true;
                    break;
                }
            }
            $r = vvur_add_work_for($pdo, $item, $sch, $atVvu ? [] : ['vvu_output' => 0]);
            $out[$r] = ($out[$r] ?? 0) + 1;
            if ($r === 'added' && !$atVvu) {
                $out['outside']++;
            }
        }
        if (!empty($fetch['error']) && $fetch['ok']) {
            $out['errors'][] = $fetch['error'];
        }
    }

    // 2. ORCID — the researcher's own list.
    if (!empty($sch['orcid']) && microtime(true) < $deadline - 20) {
        $res = vvur_orcid_works($sch['orcid'], false, 300);
        if ($res['ok']) {
            foreach ($res['items'] as $item) {
                $r = vvur_add_work_for($pdo, $item, $sch);
                $out[$r] = ($out[$r] ?? 0) + 1;
            }
        } else {
            $out['errors'][] = 'ORCID: ' . $res['error'];
        }
    }
    $pdo->prepare("UPDATE research_scholars SET works_synced_at = NOW() WHERE id = ?")->execute([$sid]);

    // 3. One record per work, then fresh citations for this person's papers.
    vvur_dedupe_publications($pdo);
    $c = vvur_refresh_citations($pdo, max($deadline, microtime(true) + 40),
        "p.scholar_id = :a OR p.id IN (SELECT publication_id FROM research_publication_authors WHERE scholar_id = :b)",
        [':a' => $sid, ':b' => $sid], 1000);
    $out['errors'] = array_merge($out['errors'], $c['errors']);
    r_recount_scholar($pdo, $sid);

    $st = $pdo->prepare("SELECT publications_count, citations, h_index, i10_index, citations_5y, h_index_5y FROM research_scholars WHERE id = ?");
    $st->execute([$sid]);
    $now = $st->fetch(PDO::FETCH_ASSOC);
    $out['summary'] = sprintf(
        '%s: %d works added (%d published outside VVU), %d already in the portal linked; citations checked for %d papers '
        . '(OpenAlex %d, Crossref %d, Semantic Scholar %d). Profile now: %d publications, %d citations, h-index %d, i10 %d, %d citations since %d.',
        $sch['full_name'], $out['added'], $out['outside'], $out['linked'], $c['papers'], $c['openalex'], $c['crossref'], $c['s2'],
        $now['publications_count'], $now['citations'], $now['h_index'], $now['i10_index'], $now['citations_5y'], (int) date('Y') - 4
    );
    if ($out['errors']) {
        $out['summary'] .= ' Notes: ' . implode('; ', array_slice(array_unique($out['errors']), 0, 4));
    }
    return $out;
}

} // VVU_RESEARCH_CITATIONS
