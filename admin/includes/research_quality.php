<?php
/**
 * VVU Scholar — data quality.
 *
 * Three problems an automatic import creates, and the fixes for them:
 *
 *  1. Not VVU at all. OpenAlex tags an author with Valley View University
 *     whenever their address contains "Valley View" — "118 Valley View Drive,
 *     Smyrna, Georgia", "Valley View School District 365U", "Nanofilm Ltd,
 *     Valley View, Ohio". vvur_authorship_is_vvu() reads the affiliation text
 *     itself, and vvur_verify_affiliations() unlinks such people and withdraws
 *     papers left without a genuine VVU author.
 *
 *  2. One person, several profiles. OpenAlex splits authors by how their name
 *     was printed ("Evans O. N. D. Ocansey", "E. O. N. D. Ocansey", "Evans
 *     Ocansey"…). vvur_scholar_duplicate_groups() finds them and
 *     vvur_merge_scholars() folds them into one profile. The merged profiles
 *     are kept (inactive, merged_into set) so future imports map onto the
 *     surviving profile instead of re-creating the duplicate, and old links
 *     redirect.
 *
 *  3. One paper, several records. The same work arrives from OpenAlex, ORCID,
 *     Crossref, Google Scholar/BibTeX or by hand, often as preprint + journal
 *     version or as several repository versions. vvur_dedupe_publications()
 *     keeps the best record and withdraws the copies, carrying their authors
 *     and citations over.
 *
 * vvur_quality_run() does all three, and is called after every import and by
 * the scheduled sync. Schema: sql/research_quality_migration.sql
 */

if (!defined('VVU_RESEARCH_QUALITY')) {
    define('VVU_RESEARCH_QUALITY', 1);

require_once __DIR__ . '/../../research/includes/research_helper.php';

/* ==========================================================================
   Schema
   ========================================================================== */

/** True once sql/research_quality_migration.sql has been imported. */
function vvur_quality_ready(PDO $pdo)
{
    static $ok = null;
    if ($ok === null) {
        try {
            $pdo->query("SELECT merged_into FROM research_scholars LIMIT 1");
            $pdo->query("SELECT 1 FROM research_author_exclusions LIMIT 1");
            $ok = true;
        } catch (Exception $e) {
            $ok = false;
        }
    }
    return $ok;
}

/* ==========================================================================
   1. Is this really Valley View University?
   ========================================================================== */

/** Does an affiliation line name Valley View University (not a US street or school)? */
function vvur_is_vvu_affiliation($line)
{
    $line = preg_replace('/\s+/', ' ', (string) $line);
    if (preg_match('/valley\s*view\s*univ/i', $line)) {
        return true;                                   // "Valley View University", "ValleyView Univ."
    }
    if (preg_match('/\bvvu\b/i', $line) && preg_match('/ghana|accra|oyibi|adentan|techiman|kumasi|tamale/i', $line)) {
        return true;
    }
    if (preg_match('/valley\s*view/i', $line) && preg_match('/ghana|oyibi|adentan|techiman|kumasi|tamale|accra/i', $line)) {
        return true;                                   // "Valley View, Oyibi, Accra"
    }
    // The Techiman campus is often printed without "View".
    return (bool) preg_match('/valley\s+univ\w*[^a-z]{0,6}techiman/i', $line);
}

/**
 * True when an OpenAlex authorship genuinely belongs to VVU.
 *
 * OpenAlex's institution tag is trusted only when the printed affiliation
 * agrees with it, or when no affiliation text was captured at all. An
 * affiliation line that plainly names VVU counts even when OpenAlex failed
 * to tag the institution.
 */
function vvur_authorship_is_vvu(array $authorship)
{
    $lines = array_filter(array_map('trim', (array) ($authorship['raw_affiliation_strings'] ?? [])));
    foreach ($lines as $line) {
        if (vvur_is_vvu_affiliation($line)) {
            return true;
        }
    }
    $tagged = false;
    foreach (($authorship['institutions'] ?? []) as $inst) {
        if (($inst['id'] ?? '') === 'https://openalex.org/' . (defined('VVUR_OPENALEX_INSTITUTION') ? VVUR_OPENALEX_INSTITUTION : 'I3133169337')) {
            $tagged = true;
        }
    }
    return $tagged && !$lines;
}

/**
 * Re-checks every OpenAlex paper against the affiliation text of its authors.
 *
 * $works: raw OpenAlex works (from vvur_openalex_fetch()). For each paper in
 * the portal that came from OpenAlex, a linked researcher whose authorship is
 * not genuinely VVU is unlinked (and remembered, so a re-import does not link
 * them again). A paper left with no VVU author is withdrawn. Researchers left
 * with no genuine VVU paper at all — and no account, and not added by hand —
 * are withdrawn too.
 */
function vvur_verify_affiliations(PDO $pdo, array $works)
{
    $stats = ['checked' => 0, 'unlinked' => 0, 'pubs_hidden' => 0, 'scholars_hidden' => 0];
    if (!$works || !vvur_quality_ready($pdo)) {
        return $stats;
    }

    $pubByWork = [];
    foreach ($pdo->query("SELECT id, external_id FROM research_publications WHERE source = 'openalex' AND external_id IS NOT NULL") as $r) {
        $pubByWork[$r['external_id']] = (int) $r['id'];
    }
    $scholarByOa = [];
    foreach ($pdo->query("SELECT id, openalex_id, merged_into FROM research_scholars WHERE openalex_id IS NOT NULL") as $r) {
        $scholarByOa[$r['openalex_id']] = (int) ($r['merged_into'] ?: $r['id']);
    }

    $unlink  = $pdo->prepare("DELETE FROM research_publication_authors WHERE publication_id = ? AND scholar_id = ?");
    $exclude = $pdo->prepare("INSERT IGNORE INTO research_author_exclusions (publication_id, scholar_id, reason) VALUES (?,?,?)");
    $verifiedScholars = [];
    $notVvu = [];
    $touched = [];

    foreach ($works as $work) {
        $wid = substr(strrchr((string) ($work['id'] ?? ''), '/'), 1);
        if ($wid === '' || !isset($pubByWork[$wid])) {
            continue;
        }
        $pubId = $pubByWork[$wid];
        $stats['checked']++;

        $good = [];
        $bad  = [];
        foreach (($work['authorships'] ?? []) as $a) {
            $oa = substr(strrchr((string) ($a['author']['id'] ?? ''), '/'), 1);
            if ($oa === '' || !isset($scholarByOa[$oa])) {
                continue;
            }
            $sid = $scholarByOa[$oa];
            if (vvur_authorship_is_vvu($a)) {
                $good[$sid] = true;
            } else {
                $bad[$sid] = $a['raw_affiliation_strings'][0] ?? 'no VVU affiliation';
            }
        }
        foreach ($good as $sid => $_) {
            $verifiedScholars[$sid] = true;
            unset($bad[$sid]);
        }
        // Proven non-VVU only when OpenAlex shows no genuine VVU author at
        // all — a paper whose VVU author simply has no profile yet is not.
        $anyVvu = false;
        foreach (($work['authorships'] ?? []) as $a) {
            if (vvur_authorship_is_vvu($a)) { $anyVvu = true; break; }
        }
        if (!$anyVvu) {
            $notVvu[$pubId] = true;
        }
        foreach ($bad as $sid => $why) {
            $unlink->execute([$pubId, $sid]);
            if ($unlink->rowCount()) {
                $exclude->execute([$pubId, $sid, mb_substr('Not a VVU affiliation: ' . $why, 0, 255)]);
                $stats['unlinked']++;
                $touched[$sid] = true;
            }
        }
        // Lead author pointing at someone who was just unlinked
        $pdo->prepare(
            "UPDATE research_publications p
                SET p.scholar_id = (SELECT a.scholar_id FROM research_publication_authors a
                                     WHERE a.publication_id = p.id ORDER BY a.author_order LIMIT 1)
              WHERE p.id = ? AND (p.scholar_id IS NULL OR p.scholar_id NOT IN
                    (SELECT scholar_id FROM research_publication_authors WHERE publication_id = ?))"
        )->execute([$pubId, $pubId]);
    }

    // Papers with no VVU author leave the catalogue — unless a researcher or
    // editor has linked someone to them by hand since.
    $hide = $pdo->prepare(
        "UPDATE research_publications p
            SET p.is_active = 0
          WHERE p.id = ? AND p.is_active = 1
            AND NOT EXISTS (SELECT 1 FROM research_publication_authors a
                              JOIN research_scholars s ON s.id = a.scholar_id AND s.is_active = 1
                             WHERE a.publication_id = p.id)"
    );
    foreach (array_keys($notVvu) as $pid) {
        $hide->execute([$pid]);
        $stats['pubs_hidden'] += $hide->rowCount();
    }

    // Researchers with no genuine VVU paper, no account and no hand-made
    // papers were only ever here because of a mis-tagged address.
    $cand = $pdo->query(
        "SELECT s.id FROM research_scholars s
          WHERE s.is_active = 1 AND s.merged_into IS NULL AND s.openalex_id IS NOT NULL
            AND NOT EXISTS (SELECT 1 FROM research_publication_authors a
                              JOIN research_publications p ON p.id = a.publication_id AND p.is_active = 1
                             WHERE a.scholar_id = s.id)"
    )->fetchAll(PDO::FETCH_COLUMN);
    $hasAccount = vvur_table_exists($pdo, 'research_accounts');
    foreach ($cand as $sid) {
        $sid = (int) $sid;
        if (isset($verifiedScholars[$sid])) {
            continue;
        }
        if ($hasAccount) {
            $st = $pdo->prepare("SELECT COUNT(*) FROM research_accounts WHERE scholar_id = ?");
            $st->execute([$sid]);
            if ((int) $st->fetchColumn()) {
                continue;
            }
        }
        $pdo->prepare("UPDATE research_scholars SET is_active = 0 WHERE id = ?")->execute([$sid]);
        $stats['scholars_hidden']++;
    }

    foreach (array_keys($touched) as $sid) {
        r_recount_scholar($pdo, $sid);
    }
    return $stats;
}

function vvur_table_exists(PDO $pdo, $table)
{
    try {
        $pdo->query("SELECT 1 FROM `$table` LIMIT 1");
        return true;
    } catch (Exception $e) {
        return false;
    }
}

/* ==========================================================================
   2. One person, several profiles
   ========================================================================== */

/** A name as lower-case ASCII tokens, titles dropped: "Dr. Evans O.N.D. Ocansey" → evans o n d ocansey. */
function vvur_name_tokens($name)
{
    $n = (string) $name;
    if (function_exists('iconv')) {
        $t = @iconv('UTF-8', 'ASCII//TRANSLIT//IGNORE', $n);
        if ($t !== false) { $n = $t; }
    }
    $n = strtolower(preg_replace('/[^A-Za-z\s.\-\x{2010}-\x{2015}]/u', ' ', $n));
    $n = preg_replace('/[.\-\x{2010}-\x{2015}]+/u', ' ', $n);
    $stop = ['dr', 'prof', 'professor', 'mr', 'mrs', 'ms', 'miss', 'rev', 'jr', 'sr', 'phd', 'assoc'];
    $out = [];
    foreach (preg_split('/\s+/', trim($n)) as $t) {
        if ($t !== '' && !in_array($t, $stop, true)) {
            // "JJ" printed without dots is two initials
            if (strlen($t) === 2 && !preg_match('/[aeiouy]/', $t)) {
                $out[] = $t[0];
                $out[] = $t[1];
            } else {
                $out[] = $t;
            }
        }
    }
    return $out;
}

/**
 * How two names relate, from their given names (surnames already equal):
 *   'same'   — every given name agrees (identical, or initials of each other)
 *   'weak'   — compatible, but on too little evidence to merge on its own
 *   'no'     — different people
 */
function vvur_given_names_match(array $a, array $b)
{
    if (!$a || !$b) {
        return 'weak';
    }
    $eq = static function ($x, $y) {
        return $x === $y || (strlen($x) === 1 && $x === $y[0]) || (strlen($y) === 1 && $y === $x[0]);
    };
    if (!$eq($a[0], $b[0])) {
        return 'no';
    }
    // Same length with at most one differing initial ("Evans N. N. D." vs
    // "Evans O. N. D."): a printing slip, provided the first names are written out.
    if (count($a) === count($b) && count($a) >= 3 && strlen($a[0]) > 1 && $a[0] === $b[0]) {
        $diff = 0;
        for ($i = 1; $i < count($a); $i++) {
            if (!$eq($a[$i], $b[$i])) { $diff++; }
        }
        if ($diff <= 1) {
            return 'same';
        }
    }
    // The shorter list must appear, in order, inside the longer one.
    list($s, $l) = count($a) <= count($b) ? [$a, $b] : [$b, $a];
    $j = 1;
    $matched = 1;
    for ($i = 1; $i < count($s); $i++) {
        $hit = false;
        for (; $j < count($l); $j++) {
            if ($eq($s[$i], $l[$j])) { $hit = true; $j++; break; }
        }
        if (!$hit) {
            return 'no';
        }
        $matched++;
    }
    // Enough evidence: the first name is written out the same in both, or at
    // least two given names/initials line up.
    if ((strlen($a[0]) > 1 && $a[0] === $b[0]) || $matched >= 2) {
        return 'same';
    }
    return 'weak';
}

/**
 * Two keys for a name's surname: the last token, and — for double-barrelled
 * names — the last two joined, so "Adjei-Mensah" and "Adjei Mensah" meet.
 */
function vvur_name_parts(array $tokens)
{
    if (count($tokens) < 2) {
        return null;
    }
    return ['surname' => end($tokens), 'given' => array_slice($tokens, 0, -1)];
}

/**
 * Groups of active profiles that are the same person. Each group is a list of
 * scholar ids, the profile to keep first (most publications, then a claimed
 * account, then the fullest name). Pairs listed in research_scholar_not_same
 * are never grouped.
 */
function vvur_scholar_duplicate_groups(PDO $pdo)
{
    $rows = $pdo->query(
        "SELECT s.id, s.full_name, s.orcid, s.unit_id, s.publications_count, s.photo
           FROM research_scholars s
          WHERE s.is_active = 1 AND s.merged_into IS NULL"
    )->fetchAll(PDO::FETCH_ASSOC);

    $owned = [];
    if (vvur_table_exists($pdo, 'research_accounts')) {
        foreach ($pdo->query("SELECT scholar_id FROM research_accounts WHERE scholar_id IS NOT NULL") as $r) {
            $owned[(int) $r['scholar_id']] = true;
        }
    }
    $notSame = [];
    if (vvur_table_exists($pdo, 'research_scholar_not_same')) {
        foreach ($pdo->query("SELECT scholar_a, scholar_b FROM research_scholar_not_same") as $r) {
            $notSame[min($r['scholar_a'], $r['scholar_b']) . '-' . max($r['scholar_a'], $r['scholar_b'])] = true;
        }
    }

    $people = [];
    $buckets = [];
    foreach ($rows as $r) {
        $parts = vvur_name_parts(vvur_name_tokens($r['full_name']));
        if (!$parts || strlen($parts['surname']) < 2) {
            continue;
        }
        $r['given'] = $parts['given'];
        $r['orcid'] = strtoupper(trim((string) $r['orcid']));
        $people[(int) $r['id']] = $r;
        $buckets[$parts['surname']][] = (int) $r['id'];
    }

    $parent = [];
    $find = function ($x) use (&$parent, &$find) {
        if (!isset($parent[$x])) { $parent[$x] = $x; }
        return $parent[$x] === $x ? $x : ($parent[$x] = $find($parent[$x]));
    };

    // Same ORCID iD → same person, whatever the name.
    $byOrcid = [];
    foreach ($people as $id => $p) {
        if ($p['orcid'] !== '') {
            $byOrcid[$p['orcid']][] = $id;
        }
    }

    // Name clusters: a profile joins a cluster only if its name clashes with
    // no member, so "John M. Arthur" and "John Kingsley Arthur" are never
    // chained together through a plain "John Arthur".
    $clusters = [];
    foreach ($buckets as $ids) {
        if (count($ids) < 2) {
            continue;
        }
        usort($ids, function ($a, $b) use ($people) {
            return [count($people[$b]['given']), (int) $people[$b]['publications_count']]
               <=> [count($people[$a]['given']), (int) $people[$a]['publications_count']];
        });
        $local = [];
        foreach ($ids as $id) {
            $placed = false;
            foreach ($local as $k => $members) {
                $ok = true;
                $strong = false;
                foreach ($members as $m) {
                    $pair = min($id, $m) . '-' . max($id, $m);
                    $po = $people[$id]['orcid'];
                    $mo = $people[$m]['orcid'];
                    $rel = vvur_given_names_match($people[$id]['given'], $people[$m]['given']);
                    if (isset($notSame[$pair])
                        || ($po !== '' && $mo !== '' && $po !== $mo)
                        || (isset($owned[$id]) && isset($owned[$m]))
                        || $rel === 'no') {
                        $ok = false;
                        break;
                    }
                    if ($rel === 'same') {
                        $strong = true;
                    }
                }
                // Must clash with nobody in the cluster and clearly match at
                // least one member ("P. B. A. Yamoah" joins via "Patience
                // Boatemaa Antwi-Yamoah", not via a bare "Patience Yamoah").
                if ($ok && $strong) {
                    $local[$k][] = $id;
                    $placed = true;
                    break;
                }
            }
            if (!$placed) {
                $local[] = [$id];
            }
        }
        // Second pass: two clusters whose fullest profiles clearly match are
        // one person whose name was misprinted somewhere ("Evans N. N. D."
        // kept "E. O. N. D. Ocansey" out of the main cluster above).
        $joined = true;
        while ($joined) {
            $joined = false;
            foreach ($local as $x => $cx) {
                foreach ($local as $y => $cy) {
                    if ($y <= $x) { continue; }
                    $a = $cx[0];
                    $b = $cy[0];
                    $pair = min($a, $b) . '-' . max($a, $b);
                    if (!isset($notSame[$pair])
                        && !($people[$a]['orcid'] !== '' && $people[$b]['orcid'] !== '' && $people[$a]['orcid'] !== $people[$b]['orcid'])
                        && !(isset($owned[$a]) && isset($owned[$b]))
                        && vvur_given_names_match($people[$a]['given'], $people[$b]['given']) === 'same'
                        && count($people[$a]['given']) >= 2 && count($people[$b]['given']) >= 2) {
                        $local[$x] = array_merge($cx, $cy);
                        unset($local[$y]);
                        $joined = true;
                        break 2;
                    }
                }
            }
        }
        foreach ($local as $members) {
            if (count($members) > 1) {
                $clusters[] = $members;
            }
        }
    }

    foreach ($clusters as $members) {
        foreach ($members as $m) { $find($m); $parent[$find($m)] = $find($members[0]); }
    }
    foreach ($byOrcid as $ids) {
        if (count($ids) > 1) {
            foreach ($ids as $m) { $find($m); $parent[$find($m)] = $find($ids[0]); }
        }
    }

    $groups = [];
    foreach (array_keys($parent) as $id) {
        $groups[$find($id)][] = $id;
    }
    $out = [];
    foreach ($groups as $ids) {
        if (count($ids) < 2) {
            continue;
        }
        usort($ids, function ($a, $b) use ($people, $owned) {
            return [isset($owned[$b]) ? 1 : 0, (int) $people[$b]['publications_count'], count($people[$b]['given']), $people[$b]['photo'] ? 1 : 0, -$b]
               <=> [isset($owned[$a]) ? 1 : 0, (int) $people[$a]['publications_count'], count($people[$a]['given']), $people[$a]['photo'] ? 1 : 0, -$a];
        });
        $out[] = $ids;
    }
    return $out;
}

/**
 * Folds $mergeIds into $keepId: publications, co-authorships, research areas,
 * missing profile details and any researcher account move to the kept
 * profile; the others become inactive with merged_into = $keepId.
 */
function vvur_merge_scholars(PDO $pdo, $keepId, array $mergeIds, $by = 'auto')
{
    $keepId = (int) $keepId;
    $mergeIds = array_values(array_diff(array_map('intval', $mergeIds), [$keepId]));
    if (!$keepId || !$mergeIds) {
        return 0;
    }

    $fields = ['title', 'position', 'unit_id', 'email', 'phone', 'photo', 'bio', 'interests', 'orcid',
               'google_scholar_id', 'researchgate_url', 'scopus_id', 'linkedin_url', 'website'];
    $own = $pdo->inTransaction() ? false : $pdo->beginTransaction();
    try {
        $keep = $pdo->query("SELECT * FROM research_scholars WHERE id = " . $keepId)->fetch(PDO::FETCH_ASSOC);
        if (!$keep) {
            throw new RuntimeException('The profile to keep no longer exists.');
        }
        foreach ($mergeIds as $mid) {
            $m = $pdo->query("SELECT * FROM research_scholars WHERE id = " . $mid)->fetch(PDO::FETCH_ASSOC);
            if (!$m) {
                continue;
            }
            // Fill blanks on the kept profile; never overwrite.
            $set = [];
            $vals = [];
            foreach ($fields as $f) {
                if ((string) ($keep[$f] ?? '') === '' && (string) ($m[$f] ?? '') !== '') {
                    $set[] = "`$f` = ?";
                    $vals[] = $m[$f];
                    $keep[$f] = $m[$f];
                }
            }
            // A fuller printed name ("Evans Osborn Nene Daker Ocansey") is not
            // preferred automatically: the kept profile's name is the one most
            // papers use. Editors can change it.
            if ($set) {
                $vals[] = $keepId;
                $pdo->prepare("UPDATE research_scholars SET " . implode(', ', $set) . " WHERE id = ?")->execute($vals);
            }

            // Remember exactly what moves, so the merge can be undone.
            $st = $pdo->prepare(
                "SELECT a.publication_id FROM research_publication_authors a
                  WHERE a.scholar_id = ? AND NOT EXISTS
                        (SELECT 1 FROM research_publication_authors k WHERE k.publication_id = a.publication_id AND k.scholar_id = ?)"
            );
            $st->execute([$mid, $keepId]);
            $movedLinks = $st->fetchAll(PDO::FETCH_COLUMN);
            $st = $pdo->prepare("SELECT id FROM research_publications WHERE scholar_id = ?");
            $st->execute([$mid]);
            $movedLeads = $st->fetchAll(PDO::FETCH_COLUMN);
            $pdo->prepare(
                "INSERT INTO research_scholar_merges (keep_id, merged_id, moved_links, moved_leads, merged_by) VALUES (?,?,?,?,?)"
            )->execute([$keepId, $mid, implode(',', $movedLinks), implode(',', $movedLeads), $by]);

            $pdo->prepare(
                "INSERT IGNORE INTO research_publication_authors (publication_id, scholar_id, author_order)
                 SELECT publication_id, ?, author_order FROM research_publication_authors WHERE scholar_id = ?"
            )->execute([$keepId, $mid]);
            $pdo->prepare("DELETE FROM research_publication_authors WHERE scholar_id = ?")->execute([$mid]);
            $pdo->prepare("UPDATE research_publications SET scholar_id = ? WHERE scholar_id = ?")->execute([$keepId, $mid]);
            $pdo->prepare("INSERT IGNORE INTO research_scholar_areas (scholar_id, area_id) SELECT ?, area_id FROM research_scholar_areas WHERE scholar_id = ?")
                ->execute([$keepId, $mid]);
            $pdo->prepare("DELETE FROM research_scholar_areas WHERE scholar_id = ?")->execute([$mid]);
            $pdo->prepare("INSERT IGNORE INTO research_author_exclusions (publication_id, scholar_id, reason, created_at)
                           SELECT publication_id, ?, reason, created_at FROM research_author_exclusions WHERE scholar_id = ?")
                ->execute([$keepId, $mid]);
            if (vvur_table_exists($pdo, 'research_accounts')) {
                $pdo->prepare("UPDATE research_accounts SET scholar_id = ? WHERE scholar_id = ?")->execute([$keepId, $mid]);
                $pdo->prepare("UPDATE research_claims SET scholar_id = ? WHERE scholar_id = ?")->execute([$keepId, $mid]);
            }
            $pdo->prepare("UPDATE research_scholars SET merged_into = ? WHERE merged_into = ?")->execute([$keepId, $mid]);
            $pdo->prepare("UPDATE research_scholars SET is_active = 0, is_featured = 0, merged_into = ? WHERE id = ?")
                ->execute([$keepId, $mid]);
        }
        if ($own) {
            $pdo->commit();
        }
    } catch (Exception $e) {
        if ($own && $pdo->inTransaction()) {
            $pdo->rollBack();
        }
        throw $e;
    }
    r_recount_scholar($pdo, $keepId);
    return count($mergeIds);
}

/**
 * Undoes one merge: the merged profile comes back with the papers that moved,
 * and the pair is remembered as "not the same person" so it is never merged
 * automatically again.
 */
function vvur_unmerge(PDO $pdo, $mergeId)
{
    $st = $pdo->prepare("SELECT * FROM research_scholar_merges WHERE id = ? AND undone_at IS NULL");
    $st->execute([(int) $mergeId]);
    $m = $st->fetch(PDO::FETCH_ASSOC);
    if (!$m) {
        return false;
    }
    $keep  = (int) $m['keep_id'];
    $back  = (int) $m['merged_id'];
    $links = array_filter(array_map('intval', explode(',', (string) $m['moved_links'])));
    $leads = array_filter(array_map('intval', explode(',', (string) $m['moved_leads'])));

    $pdo->beginTransaction();
    try {
        $pdo->prepare("UPDATE research_scholars SET is_active = 1, merged_into = NULL WHERE id = ?")->execute([$back]);
        $add = $pdo->prepare("INSERT IGNORE INTO research_publication_authors (publication_id, scholar_id, author_order) VALUES (?,?,0)");
        $del = $pdo->prepare("DELETE FROM research_publication_authors WHERE publication_id = ? AND scholar_id = ?");
        foreach ($links as $pid) {
            $add->execute([$pid, $back]);
            $del->execute([$pid, $keep]);
        }
        $lead = $pdo->prepare("UPDATE research_publications SET scholar_id = ? WHERE id = ? AND scholar_id = ?");
        foreach ($leads as $pid) {
            $lead->execute([$back, $pid, $keep]);
        }
        $pdo->prepare("INSERT IGNORE INTO research_scholar_not_same (scholar_a, scholar_b) VALUES (?,?)")
            ->execute([min($keep, $back), max($keep, $back)]);
        $pdo->prepare("UPDATE research_scholar_merges SET undone_at = NOW() WHERE id = ?")->execute([(int) $mergeId]);
        $pdo->commit();
    } catch (Exception $e) {
        $pdo->rollBack();
        throw $e;
    }
    r_recount_scholar($pdo, $keep);
    r_recount_scholar($pdo, $back);
    return true;
}

/** Merges every automatically detected group. Returns [groups, profiles merged]. */
function vvur_merge_all_duplicates(PDO $pdo)
{
    $groups = vvur_scholar_duplicate_groups($pdo);
    $merged = 0;
    foreach ($groups as $ids) {
        $merged += vvur_merge_scholars($pdo, $ids[0], array_slice($ids, 1));
    }
    return [count($groups), $merged];
}

/** The live profile for an id, following merged_into. */
function vvur_live_scholar_id(PDO $pdo, $id)
{
    $id = (int) $id;
    for ($i = 0; $i < 5 && $id; $i++) {
        $st = $pdo->prepare("SELECT merged_into FROM research_scholars WHERE id = ?");
        $st->execute([$id]);
        $next = (int) $st->fetchColumn();
        if (!$next) {
            break;
        }
        $id = $next;
    }
    return $id;
}

/* ==========================================================================
   3. One paper, several records
   ========================================================================== */

/** A DOI with prefix, case and version suffixes removed — the key versions share. */
function vvur_doi_key($doi)
{
    $d = strtolower(trim((string) $doi));
    $d = preg_replace('#^(https?://)?(dx\.)?doi\.org/#', '', $d);
    $d = preg_replace('#^doi:\s*#', '', $d);
    if ($d === '') {
        return '';
    }
    if (preg_match('#^(10\.21203/rs\.3\.rs-\d+)/v\d+$#', $d, $m)) { return $m[1]; }   // Research Square
    if (preg_match('#^(10\.17632/[a-z0-9]+)(\.\d+)?$#', $d, $m))   { return $m[1]; }   // Mendeley Data
    if (preg_match('#^(10\.22541/au\.\d+\.\d+)/v\d+$#', $d, $m))   { return $m[1]; }   // Authorea
    if (preg_match('#^(10\.1101/[\d.]+)v?\d*$#', $d, $m))          { return $m[1]; }   // bioRxiv/medRxiv
    return $d;
}

/** A title reduced to letters and digits, for comparing across sources. */
function vvur_title_key($title)
{
    $t = html_entity_decode(strip_tags((string) $title), ENT_QUOTES, 'UTF-8');
    if (function_exists('iconv')) {
        $x = @iconv('UTF-8', 'ASCII//TRANSLIT//IGNORE', $t);
        if ($x !== false) { $t = $x; }
    }
    return substr(strtolower(preg_replace('/[^A-Za-z0-9]/', '', $t)), 0, 120);
}

/** Content words of a title, crudely stemmed: "Managing" and "management" both → "manag". */
function vvur_title_stems($title)
{
    $t = strtolower(html_entity_decode(strip_tags((string) $title), ENT_QUOTES, 'UTF-8'));
    $stop = ['the', 'a', 'an', 'of', 'in', 'on', 'and', 'for', 'to', 'with', 'within', 'by', 'at', 'from', 'as', 'its', 'their', 'into', 'among', 'case', 'study'];
    $out = [];
    foreach (preg_split('/[^a-z0-9]+/', $t) as $w) {
        if ($w === '' || in_array($w, $stop, true)) { continue; }
        $w = preg_replace('/(ments|ment|ings|ing|ions|ion|ies|ed|es|s)$/', '', $w);
        $w = rtrim($w, 'e');
        if ($w !== '') { $out[$w] = true; }
    }
    ksort($out);
    return array_keys($out);
}

/**
 * Are two records the same work despite differently typed titles? Yes when
 * the titles are at least 93% alike letter-for-letter, or have exactly the
 * same stemmed words — and they do not carry two different published DOIs.
 * ("…audit pressure and audit quality" vs "…auditor motivation and audit
 * quality" is 88% alike with different words: two papers.)
 */
function vvur_same_work(array $a, array $b)
{
    $da = vvur_doi_key($a['doi'] ?? '');
    $db = vvur_doi_key($b['doi'] ?? '');
    if ($da !== '' && $db !== '' && $da !== $db && !vvur_is_preprint_doi($da) && !vvur_is_preprint_doi($db)) {
        return false;
    }
    $x = vvur_title_key($a['title']);
    $y = vvur_title_key($b['title']);
    if ($x === '' || $y === '') {
        return false;
    }
    $max = max(strlen($x), strlen($y));
    if (abs(strlen($x) - strlen($y)) / $max <= 0.1 && 1 - levenshtein(substr($x, 0, 255), substr($y, 0, 255)) / min($max, 255) >= 0.93) {
        return true;
    }
    $sa = vvur_title_stems($a['title']);
    $sb = vvur_title_stems($b['title']);
    return count($sa) >= 4 && $sa === $sb;
}

/** Titles too generic to identify a work ("Preface", "Index"…). */
function vvur_generic_title($key)
{
    return strlen($key) < 12 || in_array($key, ['preface', 'subjectindex', 'index', 'contents', 'frontmatter',
        'backmatter', 'introduction', 'editorial', 'conclusion', 'foreword', 'acknowledgements', 'references'], true);
}

/** Preprint and repository DOIs: a journal version always wins over these. */
function vvur_is_preprint_doi($doi)
{
    return (bool) preg_match('#^10\.(21203|1101|22541|2139|5281|17632|21428|31219|20944|31234|31235|36227|48550)/#', strtolower((string) $doi));
}

/**
 * Groups of active publications that are the same work: same DOI family, or
 * same title. Each group lists ids, the record to keep first.
 */
function vvur_publication_duplicate_groups(PDO $pdo)
{
    $rows = $pdo->query(
        "SELECT id, title, doi, citations, abstract, pub_year, source, pub_type
           FROM research_publications WHERE is_active = 1"
    )->fetchAll(PDO::FETCH_ASSOC);
    $byId = [];
    $parent = [];
    $find = function ($x) use (&$parent, &$find) {
        if (!isset($parent[$x])) { $parent[$x] = $x; }
        return $parent[$x] === $x ? $x : ($parent[$x] = $find($parent[$x]));
    };
    $seenTitle = [];
    $seenDoi = [];
    foreach ($rows as $r) {
        $id = (int) $r['id'];
        $byId[$id] = $r;
        $find($id);
        $tk = vvur_title_key($r['title']);
        if (!vvur_generic_title($tk)) {
            if (isset($seenTitle[$tk])) { $parent[$find($id)] = $find($seenTitle[$tk]); } else { $seenTitle[$tk] = $id; }
        }
        $dk = vvur_doi_key($r['doi']);
        if ($dk !== '') {
            if (isset($seenDoi[$dk])) { $parent[$find($id)] = $find($seenDoi[$dk]); } else { $seenDoi[$dk] = $id; }
        }
    }

    // Near-identical titles: the same work typed slightly differently by two
    // sources ("…on Forex Market…" / "…on the forex market…", "Managing" /
    // "the management of"). Compared within the same or adjacent year only.
    $byYear = [];
    foreach ($byId as $id => $r) {
        $tk = vvur_title_key($r['title']);
        if (vvur_generic_title($tk) || $r['pub_year'] === null) {
            continue;
        }
        $byYear[(int) $r['pub_year']][] = $id;
    }
    foreach ($byYear as $year => $ids) {
        $pool = array_merge($ids, $byYear[$year - 1] ?? []);
        foreach ($ids as $a) {
            foreach ($pool as $b) {
                if ($b === $a || ($b > $a && in_array($b, $ids, true)) || $find($a) === $find($b)) {
                    continue;
                }
                if (vvur_same_work($byId[$a], $byId[$b])) {
                    $parent[$find($a)] = $find($b);
                }
            }
        }
    }

    $groups = [];
    foreach (array_keys($byId) as $id) {
        $groups[$find($id)][] = $id;
    }
    $out = [];
    foreach ($groups as $ids) {
        if (count($ids) < 2) {
            continue;
        }
        usort($ids, function ($a, $b) use ($byId) {
            $score = function ($r) {
                return [
                    ($r['doi'] && !vvur_is_preprint_doi($r['doi'])) ? 1 : 0,   // published version
                    $r['pub_type'] === 'preprint' ? 0 : 1,
                    $r['source'] === 'openalex' ? 1 : 0,                     // keeps receiving updates
                    (int) $r['citations'],
                    trim((string) $r['abstract']) !== '' ? 1 : 0,
                    $r['doi'] ? 1 : 0,
                    -(int) $r['id'],
                ];
            };
            return $score($byId[$b]) <=> $score($byId[$a]);
        });
        $out[] = $ids;
    }
    return $out;
}

/**
 * Keeps one record per work: the copies are withdrawn (is_active = 0, never
 * deleted, so a re-import recognises them) after their authors, citations,
 * abstract and open-access flag are carried over to the kept record.
 */
function vvur_dedupe_publications(PDO $pdo)
{
    $groups = vvur_publication_duplicate_groups($pdo);
    $hidden = 0;
    $touched = [];
    foreach ($groups as $ids) {
        $keep = array_shift($ids);
        $in = implode(',', array_map('intval', $ids));
        $pdo->exec(
            "INSERT IGNORE INTO research_publication_authors (publication_id, scholar_id, author_order)
             SELECT $keep, scholar_id, author_order FROM research_publication_authors WHERE publication_id IN ($in)"
        );
        $pdo->exec(
            "UPDATE research_publications k
               JOIN (SELECT MAX(citations) c, MAX(is_open_access) oa,
                            MAX(NULLIF(abstract, '')) abs, MAX(scholar_id) sid
                       FROM research_publications WHERE id IN ($in)) d
                SET k.citations = GREATEST(k.citations, d.c),
                    k.is_open_access = GREATEST(k.is_open_access, d.oa),
                    k.abstract = COALESCE(NULLIF(k.abstract, ''), d.abs),
                    k.scholar_id = COALESCE(k.scholar_id, d.sid)
              WHERE k.id = $keep"
        );
        try {
            // VVU output if any copy was (a hand-entered record may know better than OpenAlex).
            $pdo->exec(
                "UPDATE research_publications k
                    JOIN (SELECT MAX(vvu_output) v FROM research_publications WHERE id IN ($in)) d
                     SET k.vvu_output = GREATEST(k.vvu_output, d.v)
                  WHERE k.id = $keep"
            );
        } catch (Exception $e) {
            // vvu_output not migrated yet
        }
        $pdo->exec("UPDATE research_publications SET is_active = 0 WHERE id IN ($in)");
        $hidden += count($ids);
        foreach ($pdo->query("SELECT scholar_id FROM research_publication_authors WHERE publication_id = $keep") as $r) {
            $touched[(int) $r['scholar_id']] = true;
        }
    }
    foreach (array_keys($touched) as $sid) {
        r_recount_scholar($pdo, $sid);
    }
    return ['groups' => count($groups), 'hidden' => $hidden];
}

/**
 * A record that is not a paper: an ORCID / Scholar entry whose "title" is the
 * journal itself ("Journal of Economics, Finance and Accounting Studies"),
 * usually with a journal-level DOI and no year.
 */
function vvur_is_junk_work(array $p)
{
    $t = trim((string) ($p['title'] ?? ''));
    if ($t === '') {
        return true;
    }
    $journalName = preg_match('/^(the\s+)?(international\s+|african\s+|global\s+|asian\s+|european\s+|american\s+)*(journal|proceedings|transactions|bulletin|annals)\s+(of|on|for)\b/i', $t)
        && !preg_match('/[:?]/', $t) && str_word_count($t) <= 10;
    $sameAsVenue = !empty($p['venue']) && strcasecmp(trim($p['venue']), $t) === 0;
    return ($journalName && empty($p['pub_year'])) || $sameAsVenue;
}

/** Withdraws imported records that are journals rather than papers. */
function vvur_withdraw_junk(PDO $pdo)
{
    $n = 0;
    $rows = $pdo->query("SELECT id, title, venue, pub_year FROM research_publications WHERE is_active = 1 AND source IN ('orcid', 'google_scholar', 'bibtex', 'csv')")->fetchAll(PDO::FETCH_ASSOC);
    $hide = $pdo->prepare("UPDATE research_publications SET is_active = 0 WHERE id = ?");
    foreach ($rows as $r) {
        if (vvur_is_junk_work($r)) {
            $hide->execute([$r['id']]);
            $n++;
        }
    }
    return $n;
}

/* ==========================================================================
   All of it
   ========================================================================== */

/**
 * The full clean-up. $works (optional) are raw OpenAlex works used to check
 * affiliations; without them that step is skipped.
 */
function vvur_quality_run(PDO $pdo, array $works = [], array $syncOpts = [])
{
    if (!vvur_quality_ready($pdo)) {
        return ['ok' => false, 'error' => 'Import sql/research_quality_migration.sql first.'];
    }
    $out = ['ok' => true, 'error' => '', 'sync' => null];
    // Re-import with the strict affiliation rule first, so genuine VVU authors
    // who had no profile (or no link) are added before anything is judged.
    if ($works && function_exists('vvur_openalex_sync')) {
        $out['sync'] = vvur_openalex_sync($pdo, $works, $syncOpts);
    }
    $out['affiliations'] = $works ? vvur_verify_affiliations($pdo, $works) : null;
    list($out['scholar_groups'], $out['scholars_merged']) = vvur_merge_all_duplicates($pdo);
    $out['junk'] = vvur_withdraw_junk($pdo);
    $out['publications'] = vvur_dedupe_publications($pdo);
    return $out;
}

/** One-line summary of vvur_quality_run(). */
function vvur_quality_summary(array $r)
{
    if (empty($r['ok'])) {
        return $r['error'] ?? 'Clean-up did not run.';
    }
    $parts = [];
    if ($r['affiliations']) {
        $a = $r['affiliations'];
        $parts[] = sprintf('%d papers checked: %d non-VVU author links removed, %d papers and %d researchers withdrawn',
            $a['checked'], $a['unlinked'], $a['pubs_hidden'], $a['scholars_hidden']);
    }
    $parts[] = sprintf('%d duplicate profiles merged into %d researchers', $r['scholars_merged'], $r['scholar_groups']);
    $parts[] = sprintf('%d duplicate paper records removed', $r['publications']['hidden']);
    return implode('; ', $parts) . '.';
}

} // VVU_RESEARCH_QUALITY
