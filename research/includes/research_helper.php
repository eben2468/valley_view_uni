<?php
/**
 * VVU Scholar — shared data layer.
 *
 * Every page under research/ and the admin manager (admin/manage_research.php)
 * read through these functions, so the figures quoted on the landing page, on a
 * faculty card and on a researcher's own profile can never drift apart.
 *
 * Nothing here echoes: callers decide the markup. Nothing here trusts input:
 * every query is prepared, and the two places that have to interpolate (the
 * ORDER BY clauses) do it from a fixed whitelist.
 *
 * Schema: sql/research_portal_schema.sql
 */

if (!defined('VVU_RESEARCH_HELPER')) {
    define('VVU_RESEARCH_HELPER', 1);

/* ==========================================================================
   Small presentation helpers
   ========================================================================== */

/** Escape for HTML text and attributes. */
function r_e($value)
{
    return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
}

/**
 * Body copy an editor typed into a textarea. Light formatting survives, script
 * and layout tags do not.
 */
function r_rich($value)
{
    return trim(strip_tags((string) $value, '<strong><b><em><i><br><ul><ol><li><p><a>'));
}

/** 1 234 567 → "1.2M", 45 200 → "45.2K". Used on the stat tiles. */
function r_compact($n)
{
    $n = (float) $n;
    if ($n >= 1000000) {
        return rtrim(rtrim(number_format($n / 1000000, 1), '0'), '.') . 'M';
    }
    if ($n >= 10000) {
        return rtrim(rtrim(number_format($n / 1000, 1), '0'), '.') . 'K';
    }
    return number_format($n);
}

/** "Kwame Osei Mensah" → "KM", for the fallback avatar. */
function r_initials($name)
{
    $parts = preg_split('/\s+/', trim((string) $name));
    $parts = array_values(array_filter($parts, static function ($p) {
        // Skip honorifics so "Prof. Ama Serwaa" initials as AS, not PA.
        return !preg_match('/^(prof|dr|mr|mrs|ms|rev|sr|jr|phd|mphil)\.?$/i', $p);
    }));
    if (!$parts) {
        return '?';
    }
    $first = mb_substr($parts[0], 0, 1);
    $last  = count($parts) > 1 ? mb_substr(end($parts), 0, 1) : '';
    return mb_strtoupper($first . $last);
}

/**
 * Accent pairs: [light-mode ink, dark-mode ink]. Both clear AA on their own
 * surface. Keyed by the base of a stored token such as "amber" or "amber-500",
 * which is the same vocabulary the rest of the site's CMS pages already use.
 */
function r_accents()
{
    return [
        'blue'   => ['#1d4ed8', '#93b4fd'],
        'indigo' => ['#4338ca', '#a5b4fc'],
        'purple' => ['#6d28d9', '#c4b5fd'],
        'green'  => ['#15803d', '#86efac'],
        'teal'   => ['#0f766e', '#5eead4'],
        'amber'  => ['#a16207', '#fcd34d'],
        'yellow' => ['#a16207', '#fcd34d'],
        'orange' => ['#c2410c', '#fdba74'],
        'red'    => ['#b42318', '#fca5a5'],
        'slate'  => ['#334155', '#cbd5e1'],
    ];
}

/** Inline custom-property pair for a stored colour token. */
function r_accent($color)
{
    $map  = r_accents();
    $base = explode('-', (string) ($color ?: 'blue'))[0];
    $pair = $map[$base] ?? $map['blue'];
    return '--acc-l:' . $pair[0] . ';--acc-d:' . $pair[1] . ';';
}

/** Light-mode ink only — for an SVG fill or a chart dataset. */
function r_accent_hex($color)
{
    $map  = r_accents();
    $base = explode('-', (string) ($color ?: 'blue'))[0];
    return ($map[$base] ?? $map['blue'])[0];
}

/**
 * A Font Awesome class from a stored token. Editors type "fa-flask" or
 * sometimes the whole "fas fa-flask"; accept either.
 */
function r_icon($token, $fallback = 'fa-circle-dot')
{
    $token = trim((string) $token);
    if ($token === '') {
        $token = $fallback;
    }
    if (strpos($token, 'fa-') === false) {
        $token = 'fa-' . $token;
    }
    return preg_match('/\b(fas|far|fab|fa-solid|fa-regular|fa-brands)\b/', $token)
        ? $token
        : 'fa-solid ' . $token;
}

/**
 * Resolve a stored relative path for a page that lives one directory down.
 * Absolute URLs and root-relative paths are left alone.
 */
function r_asset($path, $root = '../')
{
    $path = trim((string) $path);
    if ($path === '') {
        return '';
    }
    if (preg_match('#^(https?:)?//#i', $path) || $path[0] === '/' || $path[0] === '#') {
        return $path;
    }
    return $root . $path;
}

/** A URL-safe slug. Falls back to a hash so two blank names never collide. */
function r_slugify($text, $fallbackPrefix = 'item')
{
    $slug = strtolower(trim((string) $text));
    $slug = preg_replace('/[^a-z0-9]+/', '-', $slug);
    $slug = trim((string) $slug, '-');
    return $slug !== '' ? substr($slug, 0, 150) : $fallbackPrefix . '-' . substr(md5((string) $text . microtime()), 0, 8);
}

/** Makes a slug unique within a table, ignoring the row being edited. */
function r_unique_slug(PDO $pdo, $table, $slug, $ignoreId = 0)
{
    $allowed = ['research_units', 'research_areas', 'research_scholars'];
    if (!in_array($table, $allowed, true)) {
        return $slug;
    }
    $base = $slug;
    $n    = 2;
    $sql  = "SELECT COUNT(*) FROM `$table` WHERE slug = ? AND id <> ?";
    $stmt = $pdo->prepare($sql);
    while (true) {
        $stmt->execute([$slug, (int) $ignoreId]);
        if (!$stmt->fetchColumn()) {
            return $slug;
        }
        $slug = $base . '-' . $n++;
    }
}

/* ==========================================================================
   Settings & sections
   ========================================================================== */

/** The whole research_settings table as key => value. */
function r_settings(PDO $pdo)
{
    static $cache = null;
    if ($cache === null) {
        try {
            $cache = $pdo->query("SELECT setting_key, setting_value FROM research_settings")
                ->fetchAll(PDO::FETCH_KEY_PAIR);
        } catch (Exception $e) {
            $cache = [];
        }
    }
    return $cache;
}

/**
 * One setting. A key cleared in the admin is stored as '' rather than NULL, so
 * an empty string falls through to the default the same way a missing row does.
 */
function r_set(PDO $pdo, $key, $default = '')
{
    $all = r_settings($pdo);
    $val = $all[$key] ?? null;
    return ($val === null || trim((string) $val) === '') ? $default : $val;
}

/** Sections keyed by section_key; only the ones left switched on. */
function r_sections(PDO $pdo)
{
    static $cache = null;
    if ($cache === null) {
        $cache = [];
        try {
            $rows = $pdo->query("SELECT * FROM research_sections WHERE is_active = 1 ORDER BY display_order, id")
                ->fetchAll(PDO::FETCH_ASSOC);
            foreach ($rows as $row) {
                $cache[$row['section_key']] = $row;
            }
        } catch (Exception $e) {
            $cache = [];
        }
    }
    return $cache;
}

/** A section row, or null when the editor switched that band off. */
function r_section(PDO $pdo, $key)
{
    $all = r_sections($pdo);
    return $all[$key] ?? null;
}

/* ==========================================================================
   Aggregate metrics
   ========================================================================== */

/**
 * Institutional h-index: the largest h for which h publications have each been
 * cited at least h times. Computed over the live catalogue rather than stored,
 * so it can never contradict the publication list underneath it.
 */
function r_h_index(array $citationCounts)
{
    rsort($citationCounts, SORT_NUMERIC);
    $h = 0;
    foreach ($citationCounts as $i => $c) {
        if ((int) $c >= $i + 1) {
            $h = $i + 1;
        } else {
            break;
        }
    }
    return $h;
}

/**
 * Every headline figure the portal quotes, counted in one pass.
 * Cached per request — the landing page asks for these four times over.
 */
function r_metrics(PDO $pdo)
{
    static $cache = null;
    if ($cache !== null) {
        return $cache;
    }

    $m = [
        'scholars' => 0, 'publications' => 0, 'citations' => 0, 'h_index' => 0,
        'units' => 0, 'departments' => 0, 'areas' => 0, 'open_access' => 0, 'this_year' => 0,
        'collaborations' => 0, 'top_citation' => 0, 'first_year' => null, 'last_year' => null,
    ];

    try {
        $m['scholars'] = (int) $pdo->query("SELECT COUNT(*) FROM research_scholars WHERE is_active = 1")->fetchColumn();
        // Faculties and schools only — departments are counted inside their
        // parent, not as units in their own right.
        $m['units']    = (int) $pdo->query(
            "SELECT COUNT(*) FROM research_units WHERE is_active = 1 AND parent_id IS NULL"
        )->fetchColumn();
        $m['departments'] = (int) $pdo->query(
            "SELECT COUNT(*) FROM research_units WHERE is_active = 1 AND parent_id IS NOT NULL"
        )->fetchColumn();
        $m['areas']    = (int) $pdo->query("SELECT COUNT(*) FROM research_areas WHERE is_active = 1")->fetchColumn();
        $m['collaborations'] = (int) $pdo->query("SELECT COUNT(*) FROM research_partners WHERE is_active = 1")->fetchColumn();

        $row = $pdo->query(
            "SELECT COUNT(*)                AS n,
                    COALESCE(SUM(citations),0) AS cites,
                    COALESCE(MAX(citations),0) AS top,
                    SUM(is_open_access)     AS oa,
                    MIN(pub_year)           AS y0,
                    MAX(pub_year)           AS y1
               FROM research_publications
              WHERE is_active = 1"
        )->fetch(PDO::FETCH_ASSOC) ?: [];

        $m['publications'] = (int) ($row['n'] ?? 0);
        $m['citations']    = (int) ($row['cites'] ?? 0);
        $m['top_citation'] = (int) ($row['top'] ?? 0);
        $m['first_year']   = $row['y0'] !== null ? (int) $row['y0'] : null;
        $m['last_year']    = $row['y1'] !== null ? (int) $row['y1'] : null;
        $m['open_access']  = $m['publications'] > 0
            ? (int) round(((int) ($row['oa'] ?? 0)) / $m['publications'] * 100)
            : 0;

        $stmt = $pdo->prepare("SELECT COUNT(*) FROM research_publications WHERE is_active = 1 AND pub_year = ?");
        $stmt->execute([(int) date('Y')]);
        $m['this_year'] = (int) $stmt->fetchColumn();

        // h-index over the whole catalogue. Only rows that have been cited at
        // all can contribute, which keeps the scan small on a big library.
        $cites = $pdo->query(
            "SELECT citations FROM research_publications
              WHERE is_active = 1 AND citations > 0
              ORDER BY citations DESC"
        )->fetchAll(PDO::FETCH_COLUMN);
        $m['h_index'] = r_h_index(array_map('intval', $cites));

        // A university with no publications loaded yet still has scholars whose
        // Scholar profiles carry totals. Fall back to those so a freshly seeded
        // portal is not all zeros.
        if ($m['publications'] === 0) {
            $s = $pdo->query(
                "SELECT COALESCE(SUM(publications_count),0) AS n,
                        COALESCE(SUM(citations),0)          AS c,
                        COALESCE(MAX(h_index),0)            AS h
                   FROM research_scholars WHERE is_active = 1"
            )->fetch(PDO::FETCH_ASSOC) ?: [];
            $m['publications'] = (int) ($s['n'] ?? 0);
            $m['citations']    = (int) ($s['c'] ?? 0);
            $m['h_index']      = (int) ($s['h'] ?? 0);
        }
    } catch (Exception $e) {
        error_log('VVU Scholar: metric query failed — ' . $e->getMessage());
    }

    return $cache = $m;
}

/** The stat tiles, with auto_key rows resolved against the live metrics. */
function r_stat_tiles(PDO $pdo)
{
    try {
        $rows = $pdo->query("SELECT * FROM research_stats WHERE is_active = 1 ORDER BY display_order, id")
            ->fetchAll(PDO::FETCH_ASSOC);
    } catch (Exception $e) {
        return [];
    }

    $m = r_metrics($pdo);
    foreach ($rows as &$row) {
        $key = trim((string) ($row['auto_key'] ?? ''));
        if ($key !== '' && array_key_exists($key, $m)) {
            $row['resolved_value'] = $m[$key];
            $row['display_value']  = r_compact($m[$key]);
            $row['is_auto']        = true;
        } else {
            $row['resolved_value'] = (float) preg_replace('/[^0-9.]/', '', (string) $row['stat_value']);
            $row['display_value']  = (string) $row['stat_value'];
            $row['is_auto']        = false;
        }
    }
    unset($row);

    return $rows;
}

/**
 * Publications and citations per year, oldest first, for the trend chart.
 * Years with nothing in them are filled in as zeros so the line does not lie
 * about its own spacing.
 */
function r_year_trend(PDO $pdo, $years = 10)
{
    $years = max(3, min(40, (int) $years));
    $to    = (int) date('Y');

    try {
        $rows = $pdo->query(
            "SELECT pub_year AS y, COUNT(*) AS pubs, COALESCE(SUM(citations),0) AS cites
               FROM research_publications
              WHERE is_active = 1 AND pub_year IS NOT NULL AND pub_year > 1900
              GROUP BY pub_year ORDER BY pub_year"
        )->fetchAll(PDO::FETCH_ASSOC);
    } catch (Exception $e) {
        return [];
    }

    if (!$rows) {
        return [];
    }

    $byYear = [];
    foreach ($rows as $r) {
        $byYear[(int) $r['y']] = ['pubs' => (int) $r['pubs'], 'cites' => (int) $r['cites']];
    }

    // Never start the axis after the newest record, and never run past today.
    $maxYear = min($to, max(array_keys($byYear)));
    $from    = max(min(array_keys($byYear)), $maxYear - $years + 1);

    $out = [];
    for ($y = $from; $y <= $maxYear; $y++) {
        $out[] = [
            'year'         => $y,
            'publications' => $byYear[$y]['pubs'] ?? 0,
            'citations'    => $byYear[$y]['cites'] ?? 0,
        ];
    }
    return $out;
}

/* ==========================================================================
   Units, areas
   ========================================================================== */

/**
 * Faculties with their output attached.
 *
 * A publication counts towards a unit either directly (unit_id) or through its
 * lead author's unit, so records imported without a faculty still land in the
 * right column. A faculty also absorbs the totals of its own departments —
 * "Faculty of Science" includes everything filed under Computing Sciences and
 * Nursing — which is what makes the faculty comparison meaningful.
 *
 * $topLevelOnly keeps departments out of the card grids, where they would
 * otherwise sit beside their own parent and double-count its output.
 */
function r_units(PDO $pdo, $activeOnly = true, $topLevelOnly = true)
{
    $clauses = [];
    if ($activeOnly)   { $clauses[] = 'u.is_active = 1'; }
    if ($topLevelOnly) { $clauses[] = 'u.parent_id IS NULL'; }
    $where = $clauses ? 'WHERE ' . implode(' AND ', $clauses) : '';

    // "This unit or one of its departments", used by all three counts below.
    $family = '(SELECT id FROM research_units f WHERE f.id = u.id OR f.parent_id = u.id)';

    $sql = "
        SELECT u.*,
               (SELECT COUNT(*) FROM research_scholars s
                 WHERE s.is_active = 1 AND s.unit_id IN $family)         AS scholar_count,
               (SELECT COUNT(*) FROM research_publications p
                 LEFT JOIN research_scholars ps ON ps.id = p.scholar_id
                 WHERE p.is_active = 1
                   AND (p.unit_id IN $family OR ps.unit_id IN $family))  AS publication_count,
               (SELECT COALESCE(SUM(p.citations),0) FROM research_publications p
                 LEFT JOIN research_scholars ps ON ps.id = p.scholar_id
                 WHERE p.is_active = 1
                   AND (p.unit_id IN $family OR ps.unit_id IN $family))  AS citation_count
          FROM research_units u
          $where
         ORDER BY u.display_order, u.name";
    try {
        return $pdo->query($sql)->fetchAll(PDO::FETCH_ASSOC);
    } catch (Exception $e) {
        error_log('VVU Scholar: unit query failed — ' . $e->getMessage());
        return [];
    }
}

/** The departments inside one faculty, with their own counts. */
function r_child_units(PDO $pdo, $parentId)
{
    $parentId = (int) $parentId;
    $all = r_units($pdo, true, false);
    $out = [];
    foreach ($all as $unit) {
        if ((int) $unit['parent_id'] === $parentId) {
            $out[] = $unit;
        }
    }
    return $out;
}

/** Every unit, faculties and departments alike, for the admin selectors. */
function r_all_units(PDO $pdo)
{
    return r_units($pdo, false, false);
}

/** One unit by slug, with the same counts attached. */
function r_unit_by_slug(PDO $pdo, $slug)
{
    foreach (r_units($pdo, false, false) as $unit) {
        if ($unit['slug'] === $slug) {
            return $unit;
        }
    }
    return null;
}

/** Research areas with the number of publications and people behind each. */
function r_areas(PDO $pdo, $activeOnly = true)
{
    $where = $activeOnly ? 'WHERE a.is_active = 1' : '';
    $sql = "
        SELECT a.*,
               (SELECT COUNT(*) FROM research_publications p
                 WHERE p.area_id = a.id AND p.is_active = 1)          AS publication_count,
               (SELECT COALESCE(SUM(p.citations),0) FROM research_publications p
                 WHERE p.area_id = a.id AND p.is_active = 1)          AS citation_count,
               (SELECT COUNT(*) FROM research_scholar_areas sa
                 JOIN research_scholars s ON s.id = sa.scholar_id AND s.is_active = 1
                 WHERE sa.area_id = a.id)                             AS scholar_count
          FROM research_areas a
          $where
         ORDER BY a.display_order, a.name";
    try {
        return $pdo->query($sql)->fetchAll(PDO::FETCH_ASSOC);
    } catch (Exception $e) {
        return [];
    }
}

/* ==========================================================================
   Scholars
   ========================================================================== */

/** ORDER BY whitelist for the scholar directory. */
function r_scholar_sorts()
{
    return [
        'citations'    => 's.citations DESC, s.h_index DESC',
        'h_index'      => 's.h_index DESC, s.citations DESC',
        'publications' => 's.publications_count DESC, s.citations DESC',
        'name'         => 's.full_name ASC',
        'recent'       => 's.updated_at DESC',
        'featured'     => 's.is_featured DESC, s.display_order ASC, s.citations DESC',
    ];
}

/**
 * The scholar directory, filtered and paginated.
 *
 * $args: q, unit, area, sort, page, per_page, featured (bool), ids (array)
 * Returns ['rows' => [...], 'total' => int, 'pages' => int, 'page' => int].
 */
function r_scholars(PDO $pdo, array $args = [])
{
    $sorts    = r_scholar_sorts();
    $sortKey  = isset($args['sort'], $sorts[$args['sort']]) ? $args['sort'] : 'citations';
    $perPage  = max(1, min(96, (int) ($args['per_page'] ?? 12)));
    $page     = max(1, (int) ($args['page'] ?? 1));

    $where  = ['s.is_active = 1'];
    $params = [];

    $q = trim((string) ($args['q'] ?? ''));
    if ($q !== '') {
        $where[] = '(s.full_name LIKE ? OR s.position LIKE ? OR s.interests LIKE ? OR s.bio LIKE ?)';
        $like    = '%' . $q . '%';
        array_push($params, $like, $like, $like, $like);
    }
    if (!empty($args['unit'])) {
        // Filtering by a faculty includes everyone in its departments.
        $where[]  = 's.unit_id IN (SELECT id FROM research_units WHERE id = ? OR parent_id = ?)';
        $params[] = (int) $args['unit'];
        $params[] = (int) $args['unit'];
    }
    if (!empty($args['area'])) {
        $where[]  = 's.id IN (SELECT scholar_id FROM research_scholar_areas WHERE area_id = ?)';
        $params[] = (int) $args['area'];
    }
    if (!empty($args['featured'])) {
        $where[] = 's.is_featured = 1';
    }
    if (!empty($args['ids']) && is_array($args['ids'])) {
        $ids = array_values(array_filter(array_map('intval', $args['ids'])));
        if (!$ids) {
            return ['rows' => [], 'total' => 0, 'pages' => 0, 'page' => 1];
        }
        $where[] = 's.id IN (' . implode(',', array_fill(0, count($ids), '?')) . ')';
        $params  = array_merge($params, $ids);
    }

    $whereSql = 'WHERE ' . implode(' AND ', $where);

    try {
        $countStmt = $pdo->prepare("SELECT COUNT(*) FROM research_scholars s $whereSql");
        $countStmt->execute($params);
        $total = (int) $countStmt->fetchColumn();

        $offset = ($page - 1) * $perPage;
        // LIMIT/OFFSET are cast to int above, never taken from the request raw.
        $sql = "SELECT s.*, u.name AS unit_name, u.slug AS unit_slug, u.color AS unit_color
                  FROM research_scholars s
                  LEFT JOIN research_units u ON u.id = s.unit_id
                  $whereSql
                 ORDER BY {$sorts[$sortKey]}
                 LIMIT $perPage OFFSET $offset";
        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (Exception $e) {
        error_log('VVU Scholar: scholar query failed — ' . $e->getMessage());
        return ['rows' => [], 'total' => 0, 'pages' => 0, 'page' => 1];
    }

    return [
        'rows'  => $rows,
        'total' => $total,
        'pages' => (int) ceil($total / $perPage),
        'page'  => $page,
    ];
}

/** One researcher by slug (or by id when $slug is numeric-keyed by caller). */
function r_scholar_by_slug(PDO $pdo, $slug)
{
    try {
        $stmt = $pdo->prepare(
            "SELECT s.*, u.name AS unit_name, u.slug AS unit_slug, u.color AS unit_color, u.icon AS unit_icon
               FROM research_scholars s
               LEFT JOIN research_units u ON u.id = s.unit_id
              WHERE s.slug = ? AND s.is_active = 1
              LIMIT 1"
        );
        $stmt->execute([$slug]);
        return $stmt->fetch(PDO::FETCH_ASSOC) ?: null;
    } catch (Exception $e) {
        return null;
    }
}

/** The research areas tagged on one researcher. */
function r_scholar_areas(PDO $pdo, $scholarId)
{
    try {
        $stmt = $pdo->prepare(
            "SELECT a.* FROM research_areas a
               JOIN research_scholar_areas sa ON sa.area_id = a.id
              WHERE sa.scholar_id = ? AND a.is_active = 1
              ORDER BY a.display_order, a.name"
        );
        $stmt->execute([(int) $scholarId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (Exception $e) {
        return [];
    }
}

/**
 * Colleagues this researcher has actually published with, most-shared first.
 * Read from the co-authorship table, so it reflects the catalogue rather than
 * a hand-maintained list.
 */
function r_coauthors(PDO $pdo, $scholarId, $limit = 8)
{
    $limit = max(1, min(30, (int) $limit));
    try {
        $stmt = $pdo->prepare(
            "SELECT s.id, s.full_name, s.slug, s.title, s.photo, s.position, s.citations,
                    u.name AS unit_name, COUNT(*) AS shared
               FROM research_publication_authors a
               JOIN research_publication_authors b ON b.publication_id = a.publication_id AND b.scholar_id <> a.scholar_id
               JOIN research_scholars s ON s.id = b.scholar_id AND s.is_active = 1
               LEFT JOIN research_units u ON u.id = s.unit_id
              WHERE a.scholar_id = ?
              GROUP BY s.id, s.full_name, s.slug, s.title, s.photo, s.position, s.citations, u.name
              ORDER BY shared DESC, s.citations DESC
              LIMIT $limit"
        );
        $stmt->execute([(int) $scholarId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (Exception $e) {
        return [];
    }
}

/* ==========================================================================
   Publications
   ========================================================================== */

/** ORDER BY whitelist for the publication catalogue. */
function r_publication_sorts()
{
    return [
        'recent'    => 'p.pub_year DESC, p.id DESC',
        'oldest'    => 'p.pub_year ASC, p.id ASC',
        'citations' => 'p.citations DESC, p.pub_year DESC',
        'title'     => 'p.title ASC',
        'featured'  => 'p.is_featured DESC, p.citations DESC, p.pub_year DESC',
    ];
}

/** Human labels for the pub_type enum. */
function r_publication_types()
{
    return [
        'journal'    => 'Journal Article',
        'conference' => 'Conference Paper',
        'book'       => 'Book',
        'chapter'    => 'Book Chapter',
        'thesis'     => 'Thesis',
        'report'     => 'Technical Report',
        'preprint'   => 'Preprint',
        'patent'     => 'Patent',
        'dataset'    => 'Dataset',
        'other'      => 'Other',
    ];
}

/**
 * The publication catalogue, filtered and paginated.
 *
 * $args: q, unit, area, scholar, type, year, year_from, year_to, open_access,
 *        featured, sort, page, per_page
 */
function r_publications(PDO $pdo, array $args = [])
{
    $sorts   = r_publication_sorts();
    $sortKey = isset($args['sort'], $sorts[$args['sort']]) ? $args['sort'] : 'recent';
    $perPage = max(1, min(100, (int) ($args['per_page'] ?? 10)));
    $page    = max(1, (int) ($args['page'] ?? 1));

    $where  = ['p.is_active = 1'];
    $params = [];

    $q = trim((string) ($args['q'] ?? ''));
    if ($q !== '') {
        $where[] = '(p.title LIKE ? OR p.authors LIKE ? OR p.venue LIKE ? OR p.keywords LIKE ? OR p.abstract LIKE ? OR p.doi LIKE ?)';
        $like    = '%' . $q . '%';
        array_push($params, $like, $like, $like, $like, $like, $like);
    }
    if (!empty($args['unit'])) {
        // As above: a faculty filter covers its departments too.
        $family   = '(SELECT id FROM research_units WHERE id = ? OR parent_id = ?)';
        $where[]  = "(p.unit_id IN $family OR p.scholar_id IN (SELECT id FROM research_scholars WHERE unit_id IN $family))";
        $params[] = (int) $args['unit'];
        $params[] = (int) $args['unit'];
        $params[] = (int) $args['unit'];
        $params[] = (int) $args['unit'];
    }
    if (!empty($args['area'])) {
        $where[]  = 'p.area_id = ?';
        $params[] = (int) $args['area'];
    }
    if (!empty($args['scholar'])) {
        // Lead author OR credited co-author, so a profile lists everything.
        $where[]  = '(p.scholar_id = ? OR p.id IN (SELECT publication_id FROM research_publication_authors WHERE scholar_id = ?))';
        $params[] = (int) $args['scholar'];
        $params[] = (int) $args['scholar'];
    }
    if (!empty($args['type']) && isset(r_publication_types()[$args['type']])) {
        $where[]  = 'p.pub_type = ?';
        $params[] = $args['type'];
    }
    if (!empty($args['year'])) {
        $where[]  = 'p.pub_year = ?';
        $params[] = (int) $args['year'];
    }
    if (!empty($args['year_from'])) {
        $where[]  = 'p.pub_year >= ?';
        $params[] = (int) $args['year_from'];
    }
    if (!empty($args['year_to'])) {
        $where[]  = 'p.pub_year <= ?';
        $params[] = (int) $args['year_to'];
    }
    if (!empty($args['open_access'])) {
        $where[] = 'p.is_open_access = 1';
    }
    if (!empty($args['featured'])) {
        $where[] = 'p.is_featured = 1';
    }

    $whereSql = 'WHERE ' . implode(' AND ', $where);

    try {
        $countStmt = $pdo->prepare("SELECT COUNT(*) FROM research_publications p $whereSql");
        $countStmt->execute($params);
        $total = (int) $countStmt->fetchColumn();

        $offset = ($page - 1) * $perPage;
        $sql = "SELECT p.*,
                       s.full_name AS scholar_name, s.slug AS scholar_slug,
                       u.name AS unit_name, u.slug AS unit_slug,
                       a.name AS area_name, a.color AS area_color, a.slug AS area_slug
                  FROM research_publications p
                  LEFT JOIN research_scholars s ON s.id = p.scholar_id
                  LEFT JOIN research_units    u ON u.id = p.unit_id
                  LEFT JOIN research_areas    a ON a.id = p.area_id
                  $whereSql
                 ORDER BY {$sorts[$sortKey]}
                 LIMIT $perPage OFFSET $offset";
        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (Exception $e) {
        error_log('VVU Scholar: publication query failed — ' . $e->getMessage());
        return ['rows' => [], 'total' => 0, 'pages' => 0, 'page' => 1];
    }

    return [
        'rows'  => $rows,
        'total' => $total,
        'pages' => (int) ceil($total / $perPage),
        'page'  => $page,
    ];
}

/** Distinct publication years present in the catalogue, newest first. */
function r_publication_years(PDO $pdo)
{
    try {
        return array_map('intval', $pdo->query(
            "SELECT DISTINCT pub_year FROM research_publications
              WHERE is_active = 1 AND pub_year IS NOT NULL AND pub_year > 1900
              ORDER BY pub_year DESC"
        )->fetchAll(PDO::FETCH_COLUMN));
    } catch (Exception $e) {
        return [];
    }
}

/**
 * An APA-ish reference string. Good enough to paste into a reference list and
 * to give the "Copy citation" button something worth copying.
 */
function r_cite_apa(array $p)
{
    $out = trim((string) ($p['authors'] ?? $p['scholar_name'] ?? ''));
    if ($out !== '') {
        $out = rtrim($out, '.') . ' ';
    }
    if (!empty($p['pub_year'])) {
        $out .= '(' . (int) $p['pub_year'] . '). ';
    }
    $out .= rtrim((string) $p['title'], '.') . '. ';
    if (!empty($p['venue'])) {
        $out .= $p['venue'];
        if (!empty($p['volume'])) {
            $out .= ', ' . $p['volume'];
            if (!empty($p['issue'])) {
                $out .= '(' . $p['issue'] . ')';
            }
        }
        if (!empty($p['pages'])) {
            $out .= ', ' . $p['pages'];
        }
        $out .= '. ';
    } elseif (!empty($p['publisher'])) {
        $out .= $p['publisher'] . '. ';
    }
    if (!empty($p['doi'])) {
        $out .= 'https://doi.org/' . ltrim((string) $p['doi'], '/');
    }
    return trim($out);
}

/** A BibTeX entry, for the per-record export button. */
function r_cite_bibtex(array $p)
{
    $typeMap = [
        'journal' => 'article', 'conference' => 'inproceedings', 'book' => 'book',
        'chapter' => 'incollection', 'thesis' => 'phdthesis', 'report' => 'techreport',
        'preprint' => 'misc', 'patent' => 'misc', 'dataset' => 'misc', 'other' => 'misc',
    ];
    $type = $typeMap[$p['pub_type'] ?? 'journal'] ?? 'article';

    $firstAuthor = preg_split('/\s*(,|;| and )\s*/i', (string) ($p['authors'] ?? 'vvu'))[0];
    $key = r_slugify($firstAuthor . '-' . ($p['pub_year'] ?? '') . '-' . substr((string) $p['title'], 0, 20), 'vvu');

    $fields = array_filter([
        'title'     => $p['title'] ?? '',
        'author'    => str_replace(';', ' and ', (string) ($p['authors'] ?? '')),
        'year'      => $p['pub_year'] ?? '',
        'journal'   => $type === 'article' ? ($p['venue'] ?? '') : '',
        'booktitle' => in_array($type, ['inproceedings', 'incollection'], true) ? ($p['venue'] ?? '') : '',
        'publisher' => $p['publisher'] ?? '',
        'volume'    => $p['volume'] ?? '',
        'number'    => $p['issue'] ?? '',
        'pages'     => $p['pages'] ?? '',
        'doi'       => $p['doi'] ?? '',
        'url'       => $p['url'] ?? '',
    ], static function ($v) {
        return trim((string) $v) !== '';
    });

    $out = '@' . $type . '{' . $key . ",\n";
    foreach ($fields as $k => $v) {
        $out .= '  ' . $k . ' = {' . str_replace(['{', '}'], '', (string) $v) . "},\n";
    }
    return rtrim($out, ",\n") . "\n}";
}

/** Canonical outbound link for a publication: DOI first, then stored URL. */
function r_publication_link(array $p)
{
    if (!empty($p['doi'])) {
        return 'https://doi.org/' . ltrim((string) $p['doi'], '/');
    }
    return trim((string) ($p['url'] ?? ''));
}

/* ==========================================================================
   Profile links
   ========================================================================== */

/** External scholarly profiles a researcher has linked, ready to render. */
function r_scholar_links(array $s)
{
    $links = [];
    if (!empty($s['google_scholar_id'])) {
        $links[] = ['label' => 'Google Scholar', 'icon' => 'fa-brands fa-google', 'color' => 'blue',
            'url' => 'https://scholar.google.com/citations?user=' . rawurlencode($s['google_scholar_id'])];
    }
    if (!empty($s['orcid'])) {
        $links[] = ['label' => 'ORCID', 'icon' => 'fa-brands fa-orcid', 'color' => 'green',
            'url' => 'https://orcid.org/' . rawurlencode(trim($s['orcid']))];
    }
    if (!empty($s['researchgate_url'])) {
        $links[] = ['label' => 'ResearchGate', 'icon' => 'fa-brands fa-researchgate', 'color' => 'teal',
            'url' => $s['researchgate_url']];
    }
    if (!empty($s['scopus_id'])) {
        $links[] = ['label' => 'Scopus', 'icon' => 'fa-solid fa-database', 'color' => 'orange',
            'url' => 'https://www.scopus.com/authid/detail.uri?authorId=' . rawurlencode($s['scopus_id'])];
    }
    if (!empty($s['linkedin_url'])) {
        $links[] = ['label' => 'LinkedIn', 'icon' => 'fa-brands fa-linkedin', 'color' => 'indigo',
            'url' => $s['linkedin_url']];
    }
    if (!empty($s['website'])) {
        $links[] = ['label' => 'Website', 'icon' => 'fa-solid fa-globe', 'color' => 'slate',
            'url' => $s['website']];
    }
    return $links;
}

/** Highlights of one kind (or all of them), in display order. */
function r_highlights(PDO $pdo, $type = null, $limit = 12)
{
    $limit = max(1, min(60, (int) $limit));
    try {
        if ($type) {
            $stmt = $pdo->prepare(
                "SELECT h.*, s.full_name AS scholar_name, s.slug AS scholar_slug, u.name AS unit_name
                   FROM research_highlights h
                   LEFT JOIN research_scholars s ON s.id = h.scholar_id
                   LEFT JOIN research_units u ON u.id = h.unit_id
                  WHERE h.is_active = 1 AND h.highlight_type = ?
                  ORDER BY h.display_order, h.id DESC LIMIT $limit"
            );
            $stmt->execute([$type]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        }
        return $pdo->query(
            "SELECT h.*, s.full_name AS scholar_name, s.slug AS scholar_slug, u.name AS unit_name
               FROM research_highlights h
               LEFT JOIN research_scholars s ON s.id = h.scholar_id
               LEFT JOIN research_units u ON u.id = h.unit_id
              WHERE h.is_active = 1
              ORDER BY h.display_order, h.id DESC LIMIT $limit"
        )->fetchAll(PDO::FETCH_ASSOC);
    } catch (Exception $e) {
        return [];
    }
}

/** Collaborating institutions and funders. */
function r_partners(PDO $pdo, $limit = 40)
{
    $limit = max(1, min(200, (int) $limit));
    try {
        return $pdo->query(
            "SELECT * FROM research_partners WHERE is_active = 1
              ORDER BY display_order, name LIMIT $limit"
        )->fetchAll(PDO::FETCH_ASSOC);
    } catch (Exception $e) {
        return [];
    }
}

/**
 * Rebuild a scholar's cached publication/citation totals from the catalogue.
 * Called after any publication write in the admin so the directory ranking and
 * the profile page never disagree with the list of papers below them.
 */
function r_recount_scholar(PDO $pdo, $scholarId)
{
    $scholarId = (int) $scholarId;
    if ($scholarId <= 0) {
        return;
    }
    try {
        $stmt = $pdo->prepare(
            "SELECT citations FROM research_publications
              WHERE is_active = 1
                AND (scholar_id = :id
                     OR id IN (SELECT publication_id FROM research_publication_authors WHERE scholar_id = :id2))"
        );
        $stmt->execute([':id' => $scholarId, ':id2' => $scholarId]);
        $cites = array_map('intval', $stmt->fetchAll(PDO::FETCH_COLUMN));

        if (!$cites) {
            return; // Nothing indexed yet — keep whatever was entered by hand.
        }

        $upd = $pdo->prepare(
            "UPDATE research_scholars
                SET publications_count = ?, citations = ?, h_index = ?, i10_index = ?
              WHERE id = ?"
        );
        $i10 = count(array_filter($cites, static function ($c) { return $c >= 10; }));
        $upd->execute([count($cites), array_sum($cites), r_h_index($cites), $i10, $scholarId]);
    } catch (Exception $e) {
        error_log('VVU Scholar: recount failed — ' . $e->getMessage());
    }
}

/** Rebuild the cached totals for every scholar. Used by the admin "Recount". */
function r_recount_all(PDO $pdo)
{
    try {
        $ids = $pdo->query("SELECT id FROM research_scholars")->fetchAll(PDO::FETCH_COLUMN);
    } catch (Exception $e) {
        return 0;
    }
    foreach ($ids as $id) {
        r_recount_scholar($pdo, (int) $id);
    }
    return count($ids);
}

/** Build a query string for the filter bars, dropping empty values. */
function r_qs(array $overrides = [], array $base = null)
{
    $base = $base === null ? $_GET : $base;
    $merged = array_merge($base, $overrides);
    $merged = array_filter($merged, static function ($v) {
        return $v !== '' && $v !== null && $v !== [];
    });
    return $merged ? '?' . http_build_query($merged) : '';
}

} // VVU_RESEARCH_HELPER
