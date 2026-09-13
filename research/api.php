<?php
/**
 * VVU Scholar — public read-only JSON endpoint.
 *
 * Powers the live search in the portal hero and anything else that wants the
 * catalogue as data. Read-only by construction: there is no write path in this
 * file, and every query runs through the same prepared helpers the pages use.
 *
 *   api.php?action=suggest&q=…    grouped type-ahead results
 *   api.php?action=scholars&q=…   the researcher directory as JSON
 *   api.php?action=publications   the publication catalogue as JSON
 *   api.php?action=stats          the headline metrics
 */

require_once __DIR__ . '/../includes/db_connect.php';
require_once __DIR__ . '/includes/research_helper.php';

header('Content-Type: application/json; charset=utf-8');
header('X-Content-Type-Options: nosniff');
// The portal is public, but nothing here should sit in a shared cache long
// enough to go stale against an admin edit.
header('Cache-Control: public, max-age=60');

$action = $_GET['action'] ?? 'suggest';
$q      = trim((string) ($_GET['q'] ?? ''));

/** Everything leaves through here, so the shape is consistent. */
function r_json($payload, $status = 200)
{
    http_response_code($status);
    echo json_encode($payload, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
    exit;
}

try {
    switch ($action) {

        /* -------------------------------------------------------------------
           Type-ahead. Three small queries beat one big UNION here: each is
           individually indexed and none of them has to be re-sorted.
           ------------------------------------------------------------------- */
        case 'suggest':
            if (mb_strlen($q) < 2) {
                r_json(['groups' => []]);
            }
            $like = '%' . $q . '%';

            $stmt = $pdo->prepare(
                "SELECT s.full_name, s.slug, s.title, s.position, s.citations, u.name AS unit_name
                   FROM research_scholars s
                   LEFT JOIN research_units u ON u.id = s.unit_id
                  WHERE s.is_active = 1
                    AND (s.full_name LIKE ? OR s.interests LIKE ? OR s.position LIKE ?)
                  ORDER BY s.citations DESC
                  LIMIT 5"
            );
            $stmt->execute([$like, $like, $like]);
            $people = array_map(static function ($r) {
                $meta = array_filter([$r['position'], $r['unit_name']]);
                $meta[] = (int) $r['citations'] . ' citations';
                return [
                    'title' => trim(($r['title'] ? $r['title'] . ' ' : '') . $r['full_name']),
                    'meta'  => implode(' · ', $meta),
                    'url'   => 'scholar.php?p=' . rawurlencode($r['slug']),
                    'icon'  => 'fa-solid fa-user-graduate',
                ];
            }, $stmt->fetchAll(PDO::FETCH_ASSOC));

            $stmt = $pdo->prepare(
                "SELECT p.id, p.title, p.authors, p.venue, p.pub_year, p.citations
                   FROM research_publications p
                  WHERE p.is_active = 1
                    AND (p.title LIKE ? OR p.authors LIKE ? OR p.keywords LIKE ?)
                  ORDER BY p.citations DESC, p.pub_year DESC
                  LIMIT 5"
            );
            $stmt->execute([$like, $like, $like]);
            $papers = array_map(static function ($r) {
                $meta = array_filter([$r['authors'], $r['venue'], $r['pub_year']]);
                return [
                    'title' => $r['title'],
                    'meta'  => mb_substr(implode(' · ', $meta), 0, 120),
                    'url'   => 'publications.php?focus=' . (int) $r['id'],
                    'icon'  => 'fa-solid fa-book-open',
                ];
            }, $stmt->fetchAll(PDO::FETCH_ASSOC));

            $stmt = $pdo->prepare(
                "SELECT name, slug, 'unit' AS kind FROM research_units WHERE is_active = 1 AND name LIKE ?
                  UNION ALL
                 SELECT name, slug, 'area' AS kind FROM research_areas WHERE is_active = 1 AND name LIKE ?
                  LIMIT 4"
            );
            $stmt->execute([$like, $like]);
            $places = array_map(static function ($r) {
                return [
                    'title' => $r['name'],
                    'meta'  => $r['kind'] === 'unit' ? 'Faculty or school' : 'Research area',
                    'url'   => $r['kind'] === 'unit'
                        ? 'units.php#' . rawurlencode($r['slug'])
                        : 'publications.php?area_slug=' . rawurlencode($r['slug']),
                    'icon'  => $r['kind'] === 'unit' ? 'fa-solid fa-building-columns' : 'fa-solid fa-flask',
                ];
            }, $stmt->fetchAll(PDO::FETCH_ASSOC));

            r_json(['groups' => [
                ['label' => 'Researchers',   'items' => $people],
                ['label' => 'Publications',  'items' => $papers],
                ['label' => 'Browse',        'items' => $places],
            ]]);
            break;

        /* ----------------------------------------------------------------- */
        case 'scholars':
            $result = r_scholars($pdo, [
                'q'        => $q,
                'unit'     => $_GET['unit'] ?? null,
                'area'     => $_GET['area'] ?? null,
                'sort'     => $_GET['sort'] ?? 'citations',
                'page'     => $_GET['page'] ?? 1,
                'per_page' => $_GET['per_page'] ?? 12,
            ]);
            $result['rows'] = array_map(static function ($r) {
                return [
                    'name'         => trim(($r['title'] ? $r['title'] . ' ' : '') . $r['full_name']),
                    'slug'         => $r['slug'],
                    'position'     => $r['position'],
                    'unit'         => $r['unit_name'],
                    'citations'    => (int) $r['citations'],
                    'h_index'      => (int) $r['h_index'],
                    'publications' => (int) $r['publications_count'],
                    'url'          => 'scholar.php?p=' . rawurlencode($r['slug']),
                ];
            }, $result['rows']);
            r_json($result);
            break;

        /* ----------------------------------------------------------------- */
        case 'publications':
            $result = r_publications($pdo, [
                'q'        => $q,
                'unit'     => $_GET['unit'] ?? null,
                'area'     => $_GET['area'] ?? null,
                'scholar'  => $_GET['scholar'] ?? null,
                'type'     => $_GET['type'] ?? null,
                'year'     => $_GET['year'] ?? null,
                'sort'     => $_GET['sort'] ?? 'recent',
                'page'     => $_GET['page'] ?? 1,
                'per_page' => $_GET['per_page'] ?? 10,
            ]);
            $result['rows'] = array_map(static function ($r) {
                return [
                    'id'        => (int) $r['id'],
                    'title'     => $r['title'],
                    'authors'   => $r['authors'],
                    'venue'     => $r['venue'],
                    'year'      => $r['pub_year'] !== null ? (int) $r['pub_year'] : null,
                    'type'      => $r['pub_type'],
                    'citations' => (int) $r['citations'],
                    'doi'       => $r['doi'],
                    'link'      => r_publication_link($r),
                    'citation'  => r_cite_apa($r),
                ];
            }, $result['rows']);
            r_json($result);
            break;

        /* ----------------------------------------------------------------- */
        case 'stats':
            r_json(['metrics' => r_metrics($pdo), 'trend' => r_year_trend($pdo, $_GET['years'] ?? 10)]);
            break;

        default:
            r_json(['error' => 'Unknown action.'], 400);
    }
} catch (Exception $e) {
    // Never echo the driver's message — it names the schema and the host.
    error_log('VVU Scholar API: ' . $e->getMessage());
    r_json(['error' => 'The request could not be completed.'], 500);
}
