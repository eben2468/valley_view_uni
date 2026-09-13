<?php
/**
 * VVU Scholar — the OpenAlex synchroniser.
 *
 * One implementation of "pull Valley View University's output from OpenAlex and
 * write it into the portal", used by two front doors:
 *
 *   • Admin → Research Portal → Import Data → "Refresh from OpenAlex"
 *   • php sql/import_openalex.php <dir-of-saved-pages>
 *
 * They must not drift apart, because a refresh does considerably more than
 * insert publications: it creates researcher records for authors the portal has
 * not seen, links co-authors, maps each paper to a research area, places people
 * in faculties from the affiliation line printed on their own papers, and
 * rebuilds the cached citation totals the rankings read.
 *
 * Everything is keyed on OpenAlex ids, so a refresh is idempotent: run it as
 * often as you like and it updates what is there and adds what is new.
 */

require_once __DIR__ . '/research_import.php';
require_once __DIR__ . '/../../research/includes/research_helper.php';

if (!defined('VVU_RESEARCH_OPENALEX_SYNC')) {
    define('VVU_RESEARCH_OPENALEX_SYNC', 1);

/** Valley View University's OpenAlex institution id. */
if (!defined('VVUR_OPENALEX_INSTITUTION')) {
    define('VVUR_OPENALEX_INSTITUTION', 'I3133169337');
}
define('VVUR_OPENALEX_INSTITUTION_URL', 'https://openalex.org/' . VVUR_OPENALEX_INSTITUTION);

/**
 * Research areas, and the OpenAlex subject fields that feed each one.
 *
 * OpenAlex classifies every work into one of ~26 fields. Mapping them into nine
 * areas keeps the long tail (three Chemistry papers, one Dentistry paper) out of
 * areas nobody would click on, while still being derived from the real data
 * rather than invented.
 */
function vvur_openalex_areas()
{
    return [
        'social-sciences-development' => [
            'name' => 'Social Sciences & Development', 'icon' => 'fa-people-group', 'color' => 'blue',
            'description' => 'Development studies, sociology, public policy, education policy and the social sciences.',
            'sdg' => '1,10,16',
            'fields' => ['Social Sciences'],
        ],
        'business-management-accounting' => [
            'name' => 'Business, Management & Accounting', 'icon' => 'fa-briefcase', 'color' => 'indigo',
            'description' => 'Management, marketing, accounting, auditing, corporate governance and organisational research.',
            'sdg' => '8,9',
            'fields' => ['Business, Management and Accounting', 'Decision Sciences'],
        ],
        'health-medicine' => [
            'name' => 'Health & Medicine', 'icon' => 'fa-heart-pulse', 'color' => 'red',
            'description' => 'Clinical medicine, public health, nursing, midwifery and the health professions.',
            'sdg' => '3',
            'fields' => ['Medicine', 'Health Professions', 'Nursing', 'Dentistry',
                         'Immunology and Microbiology', 'Neuroscience',
                         'Pharmacology, Toxicology and Pharmaceutics'],
        ],
        'economics-finance' => [
            'name' => 'Economics & Finance', 'icon' => 'fa-chart-line', 'color' => 'teal',
            'description' => 'Banking, financial inclusion, development economics, econometrics and financial markets.',
            'sdg' => '8,10',
            'fields' => ['Economics, Econometrics and Finance'],
        ],
        'computing-information-systems' => [
            'name' => 'Computing & Information Systems', 'icon' => 'fa-laptop-code', 'color' => 'purple',
            'description' => 'Computer science, information systems, data science, cybersecurity and applied mathematics.',
            'sdg' => '9',
            'fields' => ['Computer Science', 'Mathematics'],
        ],
        'arts-humanities' => [
            'name' => 'Arts & Humanities', 'icon' => 'fa-book-open', 'color' => 'amber',
            'description' => 'Theology, religious studies, philosophy, history, literature, languages and communication.',
            'sdg' => '4,16',
            'fields' => ['Arts and Humanities'],
        ],
        'psychology-behaviour' => [
            'name' => 'Psychology & Behaviour', 'icon' => 'fa-brain', 'color' => 'orange',
            'description' => 'Psychology, behavioural science, counselling and mental health research.',
            'sdg' => '3,4',
            'fields' => ['Psychology'],
        ],
        'engineering-technology' => [
            'name' => 'Engineering & Technology', 'icon' => 'fa-gears', 'color' => 'slate',
            'description' => 'Engineering, energy systems, materials and applied technology.',
            'sdg' => '7,9',
            'fields' => ['Engineering', 'Energy', 'Materials Science'],
        ],
        'environment-agriculture-life-sciences' => [
            'name' => 'Environment, Agriculture & Life Sciences', 'icon' => 'fa-leaf', 'color' => 'green',
            'description' => 'Environmental science, agriculture, biological sciences, chemistry and the earth sciences.',
            'sdg' => '2,13,15',
            'fields' => ['Environmental Science', 'Agricultural and Biological Sciences',
                         'Earth and Planetary Sciences', 'Biochemistry, Genetics and Molecular Biology',
                         'Chemistry', 'Chemical Engineering', 'Physics and Astronomy', 'Veterinary'],
        ],
    ];
}

/**
 * Affiliation-line rules, most specific first — the first match wins. The value
 * is a research_units.slug.
 *
 * OpenAlex does not record which faculty someone belongs to, but their papers
 * do: the affiliation line is printed exactly as the author wrote it, and most
 * VVU lines name the department. Ordering matters — "School of Graduate
 * Studies" has to be tested before "Studies", "Accounting" before "Business".
 */
function vvur_openalex_unit_rules()
{
    return [
        ['/graduate\s+stud/i',                                   'school-of-graduate-studies'],
        ['/account|finance|banking/i',                           'department-of-accounting-and-finance'],
        ['/nursing|midwifery|health\s+scien|public\s+health/i',  'department-of-nursing-and-health-sciences'],
        ['/computer|computing|information\s+technology|information\s+system|engineering|informatics/i',
                                                                 'department-of-computing-sciences-and-engineering'],
        ['/theolog|mission|religio|divinity|biblical/i',         'department-of-theological-studies-and-mission'],
        ['/teacher\s+educat|education|pedagog|curriculum/i',     'department-of-teacher-education'],
        ['/development\s+stud|communicat|journalis|media/i',     'department-of-development-and-communication-studies'],
        ['/management|marketing|human\s+resource|entrepreneur/i','department-of-management-studies'],
        ['/school\s+of\s+business|business\s+school|faculty\s+of\s+business/i', 'school-of-business'],
        ['/faculty\s+of\s+science|school\s+of\s+science/i',      'faculty-of-science'],
        ['/arts\s*(&|and)?\s*social|social\s+scien|humanities/i','faculty-of-arts-social-sciences'],
    ];
}

/** An OpenAlex id URL reduced to its bare id (W2922319819, A5012345678). */
function vvur_oa_id($url)
{
    return $url ? substr(strrchr($url, '/'), 1) : '';
}

/** True when this authorship credits Valley View University. */
function vvur_oa_is_vvu(array $authorship)
{
    foreach (($authorship['institutions'] ?? []) as $inst) {
        if (($inst['id'] ?? '') === VVUR_OPENALEX_INSTITUTION_URL) {
            return true;
        }
    }
    return false;
}

/* ==========================================================================
   Fetch
   ========================================================================== */

/**
 * Page through OpenAlex and return the raw `results` entries.
 *
 * $args: institution, author, from_year, pages, cursor, time_budget.
 * Returns ['ok', 'error', 'works', 'next_cursor', 'complete'].
 *
 * `next_cursor` is non-empty when there was more to fetch than the page budget
 * allowed, so a caller can resume rather than starting over.
 */
function vvur_openalex_fetch(array $args = [])
{
    $institution = trim((string) ($args['institution'] ?? VVUR_OPENALEX_INSTITUTION));
    $maxPages    = max(1, min(30, (int) ($args['pages'] ?? 3)));
    $deadline    = microtime(true) + max(10, (int) ($args['time_budget'] ?? 60));

    $filters = [];
    if ($institution !== '') {
        $filters[] = 'institutions.id:' . $institution;
    }
    if (!empty($args['author'])) {
        $filters[] = 'author.id:' . trim((string) $args['author']);
    }
    if (!empty($args['from_year'])) {
        $filters[] = 'from_publication_date:' . ((int) $args['from_year']) . '-01-01';
    }
    if (!$filters) {
        return ['ok' => false, 'error' => 'Give OpenAlex an institution or an author to search on.',
                'works' => [], 'next_cursor' => '', 'complete' => true];
    }

    // raw_affiliation_strings is what the faculty assignment reads; without it
    // a refresh could not place anybody.
    $select = 'id,doi,title,publication_year,type,cited_by_count,authorships,'
            . 'primary_location,biblio,open_access,primary_topic';

    $cursor = (string) ($args['cursor'] ?? '*');
    $works  = [];

    for ($page = 0; $page < $maxPages && $cursor !== ''; $page++) {
        if (microtime(true) > $deadline) {
            return ['ok' => true, 'error' => '', 'works' => $works,
                    'next_cursor' => $cursor, 'complete' => false];
        }

        $url = 'https://api.openalex.org/works?' . http_build_query([
            'filter'   => implode(',', $filters),
            'per-page' => 100,
            'select'   => $select,
            'cursor'   => $cursor,
            'mailto'   => 'research@vvu.edu.gh',
        ]);

        $res = vvur_http_get($url, ['Accept: application/json']);
        if (!$res['ok']) {
            if ($works) {
                // Keep what arrived; report where to resume from.
                return ['ok' => true, 'error' => 'Stopped early: ' . $res['error'],
                        'works' => $works, 'next_cursor' => $cursor, 'complete' => false];
            }
            return ['ok' => false, 'error' => 'OpenAlex could not be reached. ' . $res['error'],
                    'works' => [], 'next_cursor' => '', 'complete' => true];
        }

        $json = json_decode($res['body'], true);
        $rows = $json['results'] ?? [];
        if (!$rows) {
            $cursor = '';
            break;
        }
        foreach ($rows as $work) {
            $works[] = $work;
        }
        $cursor = (string) ($json['meta']['next_cursor'] ?? '');
    }

    return ['ok' => true, 'error' => '', 'works' => $works,
            'next_cursor' => $cursor, 'complete' => ($cursor === '')];
}

/* ==========================================================================
   Sync
   ========================================================================== */

/**
 * Write a batch of raw OpenAlex works into the portal.
 *
 * $opts:
 *   'assign_units'   (bool, default true)  place people from affiliation lines
 *   'create_areas'   (bool, default true)  ensure the nine areas exist
 *   'recount'        (bool, default true)  rebuild cached citation totals
 *   'overwrite_units'(bool, default false) replace an editor's faculty choice
 *
 * Returns a stats array. Never throws for data reasons; a caller gets counts.
 */
function vvur_openalex_sync(PDO $pdo, array $works, array $opts = [])
{
    $assignUnits    = $opts['assign_units']    ?? true;
    $createAreas    = $opts['create_areas']    ?? true;
    $recount        = $opts['recount']         ?? true;
    $overwriteUnits = $opts['overwrite_units'] ?? false;

    $stats = [
        'works_seen' => 0, 'pub_inserted' => 0, 'pub_updated' => 0,
        'scholars_new' => 0, 'scholars_seen' => 0, 'links' => 0,
        'units_assigned' => 0, 'no_area' => 0, 'skipped' => 0,
    ];

    // De-duplicate by OpenAlex id: overlapping pages must not double-count.
    $byId = [];
    foreach ($works as $work) {
        if (!empty($work['id'])) {
            $byId[$work['id']] = $work;
        }
    }
    $stats['works_seen'] = count($byId);
    if (!$byId) {
        return $stats;
    }

    /* --- 1. Research areas ------------------------------------------------ */
    $areaDefs    = vvur_openalex_areas();
    $fieldToArea = [];
    foreach ($areaDefs as $slug => $def) {
        foreach ($def['fields'] as $field) {
            $fieldToArea[$field] = $slug;
        }
    }
    if ($createAreas) {
        $ins = $pdo->prepare(
            "INSERT INTO research_areas (name, slug, description, icon, color, sdg_goals, display_order, is_active)
             VALUES (?,?,?,?,?,?,?,1)
             ON DUPLICATE KEY UPDATE name = VALUES(name)"
        );
        $order = 1;
        foreach ($areaDefs as $slug => $def) {
            $ins->execute([$def['name'], $slug, $def['description'], $def['icon'],
                           $def['color'], $def['sdg'], $order++]);
        }
    }
    $areaIds = [];
    foreach ($pdo->query("SELECT id, slug FROM research_areas") as $row) {
        $areaIds[$row['slug']] = (int) $row['id'];
    }

    /* --- 2. Scholars ------------------------------------------------------ */
    $authors = [];   // openalex author id => ['name','orcid','lines'=>[line=>n]]
    foreach ($byId as $work) {
        foreach (($work['authorships'] ?? []) as $authorship) {
            if (!vvur_oa_is_vvu($authorship)) {
                continue;
            }
            $oaId = vvur_oa_id($authorship['author']['id'] ?? '');
            $name = trim((string) ($authorship['author']['display_name'] ?? ''));
            if ($oaId === '' || $name === '') {
                continue;
            }
            if (!isset($authors[$oaId])) {
                $authors[$oaId] = ['name' => $name, 'orcid' => '', 'lines' => []];
            }
            $orcid = $authorship['author']['orcid'] ?? '';
            if ($orcid && !$authors[$oaId]['orcid']) {
                $authors[$oaId]['orcid'] = preg_replace('#^https?://orcid\.org/#i', '', $orcid);
            }
            foreach (($authorship['raw_affiliation_strings'] ?? []) as $line) {
                if (stripos($line, 'valley view') === false) {
                    continue;   // a co-author's own institution says nothing about VVU
                }
                $line = trim(preg_replace('/\s+/', ' ', $line));
                $authors[$oaId]['lines'][$line] = ($authors[$oaId]['lines'][$line] ?? 0) + 1;
            }
        }
    }

    $unitIdBySlug = [];
    foreach ($pdo->query("SELECT id, slug FROM research_units") as $row) {
        $unitIdBySlug[$row['slug']] = (int) $row['id'];
    }
    $unitRules = vvur_openalex_unit_rules();

    $scholarIds = [];
    $find   = $pdo->prepare("SELECT id, unit_id FROM research_scholars WHERE openalex_id = ?");
    $insert = $pdo->prepare(
        "INSERT INTO research_scholars (full_name, slug, openalex_id, orcid, unit_id, is_active, last_synced_at)
         VALUES (?,?,?,?,?,1,NOW())"
    );
    $update = $pdo->prepare(
        "UPDATE research_scholars
            SET full_name = ?,
                orcid = CASE WHEN orcid = '' OR orcid IS NULL THEN ? ELSE orcid END,
                last_synced_at = NOW()
          WHERE id = ?"
    );
    $setUnit = $pdo->prepare("UPDATE research_scholars SET unit_id = ? WHERE id = ?");

    foreach ($authors as $oaId => $author) {
        // Which unit do this person's own affiliation lines name? Weighted by
        // how often each line appears, so their usual department wins.
        $unitId = null;
        if ($assignUnits && $author['lines']) {
            arsort($author['lines']);
            foreach (array_keys($author['lines']) as $line) {
                foreach ($unitRules as [$pattern, $slug]) {
                    if (preg_match($pattern, $line) && isset($unitIdBySlug[$slug])) {
                        $unitId = $unitIdBySlug[$slug];
                        break 2;
                    }
                }
            }
        }

        $find->execute([$oaId]);
        $existing = $find->fetch(PDO::FETCH_ASSOC);

        if ($existing) {
            $id = (int) $existing['id'];
            $update->execute([$author['name'], $author['orcid'], $id]);
            // Never overwrite a faculty an editor has set, unless asked to.
            if ($unitId !== null && ($overwriteUnits || $existing['unit_id'] === null)) {
                $setUnit->execute([$unitId, $id]);
                $stats['units_assigned']++;
            }
            $stats['scholars_seen']++;
        } else {
            $slug = r_unique_slug($pdo, 'research_scholars', r_slugify($author['name'], 'researcher'), 0);
            $insert->execute([$author['name'], $slug, $oaId, $author['orcid'], $unitId]);
            $id = (int) $pdo->lastInsertId();
            if ($unitId !== null) {
                $stats['units_assigned']++;
            }
            $stats['scholars_new']++;
        }
        $scholarIds[$oaId] = $id;
    }

    /* --- 3. Publications -------------------------------------------------- */
    $findPub = $pdo->prepare(
        "SELECT id, unit_id FROM research_publications WHERE external_id = ? AND source = 'openalex'"
    );
    $insertPub = $pdo->prepare(
        "INSERT INTO research_publications
            (title, authors, scholar_id, unit_id, area_id, pub_type, venue, publisher, pub_year,
             volume, issue, pages, doi, url, keywords, citations, is_open_access, is_active,
             source, external_id)
         VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,1,'openalex',?)"
    );
    // Citation counts and metadata refresh; an editor's own area, featured flag
    // and published flag are left exactly as they set them.
    $updatePub = $pdo->prepare(
        "UPDATE research_publications
            SET title = ?, authors = ?, venue = ?, publisher = ?, pub_year = ?,
                volume = ?, issue = ?, pages = ?,
                doi = COALESCE(NULLIF(doi, ''), ?),
                url = COALESCE(NULLIF(url, ''), ?),
                citations = ?, is_open_access = ?,
                area_id = COALESCE(area_id, ?),
                unit_id = COALESCE(unit_id, ?)
          WHERE id = ?"
    );
    $linkStmt = $pdo->prepare(
        "INSERT IGNORE INTO research_publication_authors (publication_id, scholar_id, author_order)
         VALUES (?,?,?)"
    );
    $leadUnitStmt = $pdo->prepare("SELECT unit_id FROM research_scholars WHERE id = ?");

    $touchedScholars = [];

    foreach ($byId as $work) {
        $mapped = vvur_openalex_item($work);
        if ($mapped['title'] === '') {
            $stats['skipped']++;
            continue;
        }

        // VVU authors on this paper, in printed order.
        $vvuOnThis = [];
        foreach (($work['authorships'] ?? []) as $authorship) {
            if (!vvur_oa_is_vvu($authorship)) {
                continue;
            }
            $oaId = vvur_oa_id($authorship['author']['id'] ?? '');
            if ($oaId !== '' && isset($scholarIds[$oaId]) && !in_array($scholarIds[$oaId], $vvuOnThis, true)) {
                $vvuOnThis[] = $scholarIds[$oaId];
            }
        }

        $field  = $work['primary_topic']['field']['display_name'] ?? '';
        $areaId = null;
        if ($field !== '' && isset($fieldToArea[$field], $areaIds[$fieldToArea[$field]])) {
            $areaId = $areaIds[$fieldToArea[$field]];
        } else {
            $stats['no_area']++;
        }

        // A paper inherits the faculty of its lead VVU author, so faculty pages
        // count work filed under a department even when the record itself
        // carries no unit.
        $leadScholar = $vvuOnThis[0] ?? null;
        $leadUnit    = null;
        if ($leadScholar) {
            $leadUnitStmt->execute([$leadScholar]);
            $leadUnit = $leadUnitStmt->fetchColumn() ?: null;
        }

        $findPub->execute([$mapped['external_id']]);
        $existing = $findPub->fetch(PDO::FETCH_ASSOC);

        if ($existing) {
            $updatePub->execute([
                $mapped['title'], $mapped['authors'], $mapped['venue'], $mapped['publisher'],
                $mapped['pub_year'], $mapped['volume'], $mapped['issue'], $mapped['pages'],
                $mapped['doi'], $mapped['url'], $mapped['citations'], $mapped['is_open_access'],
                $areaId, $leadUnit, (int) $existing['id'],
            ]);
            $pubId = (int) $existing['id'];
            $stats['pub_updated']++;
        } else {
            $insertPub->execute([
                $mapped['title'], $mapped['authors'], $leadScholar, $leadUnit, $areaId,
                $mapped['pub_type'], $mapped['venue'], $mapped['publisher'], $mapped['pub_year'],
                $mapped['volume'], $mapped['issue'], $mapped['pages'], $mapped['doi'],
                $mapped['url'], $mapped['keywords'], $mapped['citations'],
                $mapped['is_open_access'], $mapped['external_id'],
            ]);
            $pubId = (int) $pdo->lastInsertId();
            $stats['pub_inserted']++;
        }

        foreach ($vvuOnThis as $order => $scholarId) {
            $linkStmt->execute([$pubId, $scholarId, $order]);
            $stats['links']++;
            $touchedScholars[$scholarId] = true;
        }
    }

    /* --- 4. Subject tags and cached totals -------------------------------- */
    $pdo->exec(
        "INSERT IGNORE INTO research_scholar_areas (scholar_id, area_id)
         SELECT DISTINCT a.scholar_id, p.area_id
           FROM research_publication_authors a
           JOIN research_publications p ON p.id = a.publication_id
          WHERE p.area_id IS NOT NULL AND p.is_active = 1"
    );

    if ($recount) {
        foreach (array_keys($touchedScholars) as $scholarId) {
            r_recount_scholar($pdo, $scholarId);
        }
    }

    return $stats;
}

/** A one-line summary of a sync, for a flash message or the console. */
function vvur_openalex_summary(array $s)
{
    return sprintf(
        '%d works read — %d new publications, %d refreshed; %d new researchers, %d already known; '
        . '%d author links, %d faculty assignments.',
        $s['works_seen'], $s['pub_inserted'], $s['pub_updated'],
        $s['scholars_new'], $s['scholars_seen'], $s['links'], $s['units_assigned']
    );
}

} // VVU_RESEARCH_OPENALEX_SYNC
