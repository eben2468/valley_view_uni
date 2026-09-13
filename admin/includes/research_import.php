<?php
/**
 * VVU Scholar — data import and metric sync.
 *
 * Five ways to get real records into the portal, in descending order of how
 * reliably they work from a server:
 *
 *   1. Crossref REST API  — open, no key, no rate-limit problems, and it is the
 *      only source here that returns citation counts (is-referenced-by-count).
 *   2. ORCID public API   — open, no key. Authoritative for "what did this
 *      person write", but carries no citation counts; enrich via Crossref.
 *   3. BibTeX paste/upload — what Google Scholar's own "Export" button gives
 *      you. Always works, because the browser did the fetching.
 *   4. CSV paste/upload    — the other Google Scholar export format.
 *   5. Google Scholar scrape — best-effort. Google serves a CAPTCHA to most
 *      datacentre IPs, so this fails on a lot of hosts. When it does, the
 *      caller is told plainly to use (3) or (4) instead rather than being left
 *      with a silent no-op. ResearchGate is the same story, more so.
 *
 * Nothing here writes to the database. Each importer returns a normalised
 * array; admin/manage_research.php decides what to persist.
 */

if (!defined('VVU_RESEARCH_IMPORT')) {
    define('VVU_RESEARCH_IMPORT', 1);

/** How long any single outbound request may take. */
define('VVUR_HTTP_TIMEOUT', 20);

/**
 * One outbound GET. Returns ['ok' => bool, 'body' => string, 'status' => int,
 * 'error' => string].
 *
 * Uses cURL when it is available and falls back to a stream context, because
 * some shared hosts ship one and not the other.
 */
function vvur_http_get($url, array $headers = [])
{
    $out = ['ok' => false, 'body' => '', 'status' => 0, 'error' => ''];

    if (!preg_match('#^https://#i', $url)) {
        $out['error'] = 'Only https:// URLs are fetched.';
        return $out;
    }

    $ua = 'Mozilla/5.0 (compatible; VVUScholarBot/1.0; +https://vvu.edu.gh/research/) '
        . 'PHP/' . PHP_VERSION;

    if (function_exists('curl_init')) {
        $ch = curl_init($url);
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_MAXREDIRS      => 4,
            CURLOPT_TIMEOUT        => VVUR_HTTP_TIMEOUT,
            CURLOPT_CONNECTTIMEOUT => 10,
            CURLOPT_USERAGENT      => $ua,
            CURLOPT_HTTPHEADER     => array_merge(['Accept-Language: en'], $headers),
            CURLOPT_SSL_VERIFYPEER => true,
            CURLOPT_SSL_VERIFYHOST => 2,
            CURLOPT_ENCODING       => '',
        ]);
        $body   = curl_exec($ch);
        $status = (int) curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $err    = curl_error($ch);
        curl_close($ch);

        $out['status'] = $status;
        if ($body === false) {
            // A CA-bundle that has never been configured is the usual cause on
            // XAMPP; say so rather than "SSL error".
            $out['error'] = $err ?: 'The request failed.';
            if (stripos($err, 'certificate') !== false) {
                $out['error'] .= ' Set curl.cainfo in php.ini to a cacert.pem bundle.';
            }
            return $out;
        }
        $out['body'] = (string) $body;
        $out['ok']   = $status >= 200 && $status < 300;
        if (!$out['ok']) {
            $out['error'] = 'The source answered HTTP ' . $status . '.';
        }
        return $out;
    }

    if (!ini_get('allow_url_fopen')) {
        $out['error'] = 'This server has neither the cURL extension nor allow_url_fopen, '
            . 'so it cannot fetch remote data. Use the BibTeX or CSV import instead.';
        return $out;
    }

    $context = stream_context_create(['http' => [
        'method'        => 'GET',
        'timeout'       => VVUR_HTTP_TIMEOUT,
        'header'        => implode("\r\n", array_merge(['User-Agent: ' . $ua, 'Accept-Language: en'], $headers)),
        'ignore_errors' => true,
    ]]);
    $body = @file_get_contents($url, false, $context);
    if ($body === false) {
        $out['error'] = 'The request failed.';
        return $out;
    }
    $status = 0;
    foreach (($http_response_header ?? []) as $line) {
        if (preg_match('#^HTTP/\S+\s+(\d{3})#', $line, $m)) { $status = (int) $m[1]; }
    }
    $out['status'] = $status;
    $out['body']   = $body;
    $out['ok']     = $status >= 200 && $status < 300;
    if (!$out['ok']) {
        $out['error'] = 'The source answered HTTP ' . $status . '.';
    }
    return $out;
}

/** A blank normalised publication, so every importer returns the same shape. */
function vvur_blank_publication()
{
    return [
        'title' => '', 'authors' => '', 'venue' => '', 'publisher' => '',
        'pub_year' => null, 'volume' => '', 'issue' => '', 'pages' => '',
        'doi' => '', 'url' => '', 'abstract' => '', 'keywords' => '',
        'citations' => 0, 'pub_type' => 'journal', 'is_open_access' => 0,
        'source' => 'manual', 'external_id' => '',
    ];
}

/** Crossref and BibTeX type names → our pub_type enum. */
function vvur_map_type($raw)
{
    $raw = strtolower(trim((string) $raw));
    $map = [
        'journal-article' => 'journal', 'article' => 'journal', 'article-journal' => 'journal',
        'proceedings-article' => 'conference', 'inproceedings' => 'conference', 'conference' => 'conference',
        'book' => 'book', 'monograph' => 'book', 'edited-book' => 'book',
        'book-chapter' => 'chapter', 'incollection' => 'chapter', 'chapter' => 'chapter',
        'dissertation' => 'thesis', 'phdthesis' => 'thesis', 'mastersthesis' => 'thesis', 'thesis' => 'thesis',
        'report' => 'report', 'techreport' => 'report', 'posted-content' => 'preprint',
        'preprint' => 'preprint', 'dataset' => 'dataset', 'patent' => 'patent',
    ];
    return $map[$raw] ?? 'other';
}

/* ==========================================================================
   1. Crossref
   ========================================================================== */

/**
 * Search Crossref. $args: author, affiliation, title, doi, rows, from_year.
 *
 * Crossref asks callers to identify themselves; the mailto parameter is what
 * gets this request into their faster "polite" pool.
 */
function vvur_crossref_search(array $args)
{
    $rows = max(1, min(100, (int) ($args['rows'] ?? 40)));

    if (!empty($args['doi'])) {
        $doi = ltrim(trim((string) $args['doi']), '/');
        $doi = preg_replace('#^https?://(dx\.)?doi\.org/#i', '', $doi);
        $res = vvur_http_get('https://api.crossref.org/works/' . rawurlencode($doi)
            . '?mailto=research@vvu.edu.gh', ['Accept: application/json']);
        if (!$res['ok']) {
            return ['ok' => false, 'error' => 'Crossref did not return that DOI. ' . $res['error'], 'items' => []];
        }
        $json = json_decode($res['body'], true);
        $item = $json['message'] ?? null;
        return ['ok' => (bool) $item, 'error' => $item ? '' : 'Crossref returned no record.',
                'items' => $item ? [vvur_crossref_item($item)] : []];
    }

    $params = ['rows' => $rows, 'mailto' => 'research@vvu.edu.gh'];
    if (!empty($args['author']))      { $params['query.author'] = $args['author']; }
    if (!empty($args['affiliation'])) { $params['query.affiliation'] = $args['affiliation']; }
    if (!empty($args['title']))       { $params['query.bibliographic'] = $args['title']; }
    if (!empty($args['from_year']))   { $params['filter'] = 'from-pub-date:' . ((int) $args['from_year']) . '-01-01'; }

    if (!isset($params['query.author'], $params['query.affiliation'], $params['query.bibliographic'])
        && count($params) <= 2) {
        return ['ok' => false, 'error' => 'Give Crossref something to search on — an author, an affiliation, a title or a DOI.', 'items' => []];
    }

    $res = vvur_http_get('https://api.crossref.org/works?' . http_build_query($params),
        ['Accept: application/json']);
    if (!$res['ok']) {
        return ['ok' => false, 'error' => 'Crossref could not be reached. ' . $res['error'], 'items' => []];
    }

    $json  = json_decode($res['body'], true);
    $items = $json['message']['items'] ?? [];
    return ['ok' => true, 'error' => '', 'items' => array_map('vvur_crossref_item', $items)];
}

/** One Crossref record → our normalised shape. */
function vvur_crossref_item(array $item)
{
    $p = vvur_blank_publication();

    $p['title']   = trim((string) ($item['title'][0] ?? ''));
    $p['venue']   = trim((string) ($item['container-title'][0] ?? ''));
    $p['publisher'] = trim((string) ($item['publisher'] ?? ''));
    $p['volume']  = (string) ($item['volume'] ?? '');
    $p['issue']   = (string) ($item['issue'] ?? '');
    $p['pages']   = (string) ($item['page'] ?? '');
    $p['doi']     = (string) ($item['DOI'] ?? '');
    $p['url']     = (string) ($item['URL'] ?? '');
    $p['citations'] = (int) ($item['is-referenced-by-count'] ?? 0);
    $p['pub_type'] = vvur_map_type($item['type'] ?? '');
    $p['source']   = 'crossref';
    $p['external_id'] = $p['doi'];

    // Crossref gives the date as nested parts, newest issued date first.
    $parts = $item['issued']['date-parts'][0]
        ?? $item['published-print']['date-parts'][0]
        ?? $item['published-online']['date-parts'][0]
        ?? [];
    if (!empty($parts[0])) {
        $p['pub_year'] = (int) $parts[0];
    }

    $names = [];
    foreach (($item['author'] ?? []) as $a) {
        $name = trim(($a['given'] ?? '') . ' ' . ($a['family'] ?? ''));
        if ($name === '' && !empty($a['name'])) { $name = $a['name']; }
        if ($name !== '') { $names[] = $name; }
    }
    $p['authors'] = implode(', ', $names);

    // The abstract arrives as JATS XML; strip the tags but keep the prose.
    if (!empty($item['abstract'])) {
        $p['abstract'] = trim(html_entity_decode(strip_tags((string) $item['abstract']), ENT_QUOTES, 'UTF-8'));
    }

    // A Creative Commons licence on the full text is a reliable open-access
    // signal; a publisher's own "tdm" licence is not, so only CC counts.
    foreach (($item['license'] ?? []) as $lic) {
        if (stripos((string) ($lic['URL'] ?? ''), 'creativecommons.org') !== false) {
            $p['is_open_access'] = 1;
            break;
        }
    }
    if (!empty($item['subject'])) {
        $p['keywords'] = implode(', ', array_slice((array) $item['subject'], 0, 8));
    }

    return $p;
}

/* ==========================================================================
   1b. OpenAlex — the whole institutional corpus in one call
   ========================================================================== */

/** Valley View University in OpenAlex. */
define('VVUR_OPENALEX_INSTITUTION', 'I3133169337');

/**
 * Every work carrying a Valley View University affiliation, straight from
 * OpenAlex. This is the source the portal's catalogue was first built from:
 * open, no key, and unlike Crossref it resolves the institution itself, so one
 * request returns the University's whole output rather than whatever an
 * affiliation string happens to match.
 *
 * $args: institution (OpenAlex id), author (OpenAlex author id), from_year,
 *        pages (how many pages of 100 to walk), cursor.
 */
function vvur_openalex_works(array $args = [])
{
    $institution = trim((string) ($args['institution'] ?? VVUR_OPENALEX_INSTITUTION));
    $maxPages    = max(1, min(20, (int) ($args['pages'] ?? 3)));

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
        return ['ok' => false, 'error' => 'Give OpenAlex an institution or an author to search on.', 'items' => []];
    }

    $select = 'id,doi,title,publication_year,type,cited_by_count,authorships,primary_location,biblio,open_access,primary_topic';
    $cursor = (string) ($args['cursor'] ?? '*');
    $items  = [];

    for ($page = 0; $page < $maxPages && $cursor !== ''; $page++) {
        $url = 'https://api.openalex.org/works?' . http_build_query([
            'filter'   => implode(',', $filters),
            'per-page' => 100,
            'select'   => $select,
            'cursor'   => $cursor,
            'mailto'   => 'research@vvu.edu.gh',
        ]);

        $res = vvur_http_get($url, ['Accept: application/json']);
        if (!$res['ok']) {
            // Anything already collected is still worth offering.
            if ($items) {
                break;
            }
            return ['ok' => false, 'error' => 'OpenAlex could not be reached. ' . $res['error'], 'items' => []];
        }

        $json = json_decode($res['body'], true);
        $rows = $json['results'] ?? [];
        if (!$rows) {
            break;
        }
        foreach ($rows as $work) {
            $item = vvur_openalex_item($work);
            if ($item['title'] !== '') {
                $items[] = $item;
            }
        }
        $cursor = (string) ($json['meta']['next_cursor'] ?? '');
    }

    return $items
        ? ['ok' => true, 'error' => '', 'items' => $items]
        : ['ok' => false, 'error' => 'OpenAlex returned no works for that filter.', 'items' => []];
}

/** One OpenAlex work → our normalised shape. */
function vvur_openalex_item(array $work)
{
    $p = vvur_blank_publication();

    $p['title']     = trim((string) ($work['title'] ?? ''));
    $p['pub_year']  = $work['publication_year'] ?: null;
    $p['citations'] = (int) ($work['cited_by_count'] ?? 0);
    $p['doi']       = preg_replace('#^https?://(dx\.)?doi\.org/#i', '', (string) ($work['doi'] ?? ''));
    $p['url']       = (string) ($work['primary_location']['landing_page_url'] ?? '');
    $p['is_open_access'] = !empty($work['open_access']['is_oa']) ? 1 : 0;
    $p['source']    = 'openalex';
    $p['external_id'] = ($work['id'] ?? '') ? substr(strrchr($work['id'], '/'), 1) : '';

    $source = $work['primary_location']['source'] ?? [];
    $p['venue']     = (string) ($source['display_name'] ?? '');
    $p['publisher'] = (string) ($source['host_organization_name'] ?? '');

    $biblio = $work['biblio'] ?? [];
    $p['volume'] = (string) ($biblio['volume'] ?? '');
    $p['issue']  = (string) ($biblio['issue'] ?? '');
    if (!empty($biblio['first_page'])) {
        $p['pages'] = (string) $biblio['first_page'];
        if (!empty($biblio['last_page']) && $biblio['last_page'] !== $biblio['first_page']) {
            $p['pages'] .= '-' . $biblio['last_page'];
        }
    }

    $names = [];
    foreach (($work['authorships'] ?? []) as $authorship) {
        $name = trim((string) ($authorship['author']['display_name'] ?? ''));
        if ($name !== '') { $names[] = $name; }
    }
    $p['authors'] = implode(', ', $names);

    // OpenAlex types use their own vocabulary; map onto ours.
    $typeMap = [
        'article' => 'journal', 'review' => 'journal', 'editorial' => 'journal',
        'letter' => 'journal', 'erratum' => 'journal', 'retraction' => 'journal',
        'book-review' => 'journal', 'conference-paper' => 'conference',
        'conference-abstract' => 'conference', 'book' => 'book', 'book-chapter' => 'chapter',
        'dissertation' => 'thesis', 'report' => 'report', 'preprint' => 'preprint',
        'dataset' => 'dataset', 'paratext' => 'other', 'peer-review' => 'other',
    ];
    $p['pub_type'] = $typeMap[strtolower((string) ($work['type'] ?? ''))] ?? vvur_map_type($work['type'] ?? '');

    $topic = $work['primary_topic']['display_name'] ?? '';
    $field = $work['primary_topic']['field']['display_name'] ?? '';
    $p['keywords'] = trim(implode(', ', array_filter([$topic, $field])));

    return $p;
}

/* ==========================================================================
   2. ORCID
   ========================================================================== */

/**
 * Every work on a public ORCID record. ORCID carries no citation counts, so
 * each DOI it hands back is looked up in Crossref when $enrich is on.
 */
function vvur_orcid_works($orcid, $enrich = true, $max = 60)
{
    $orcid = trim((string) $orcid);
    $orcid = preg_replace('#^https?://orcid\.org/#i', '', $orcid);
    if (!preg_match('/^\d{4}-\d{4}-\d{4}-\d{3}[\dX]$/i', $orcid)) {
        return ['ok' => false, 'error' => 'That is not a valid ORCID iD. It looks like 0000-0002-1825-0097.', 'items' => []];
    }

    $res = vvur_http_get('https://pub.orcid.org/v3.0/' . $orcid . '/works',
        ['Accept: application/json']);
    if (!$res['ok']) {
        return ['ok' => false, 'error' => 'ORCID could not be reached. ' . $res['error'], 'items' => []];
    }

    $json  = json_decode($res['body'], true);
    $items = [];

    foreach (($json['group'] ?? []) as $group) {
        if (count($items) >= $max) { break; }

        $summary = $group['work-summary'][0] ?? null;
        if (!$summary) { continue; }

        $p = vvur_blank_publication();
        $p['title']    = trim((string) ($summary['title']['title']['value'] ?? ''));
        $p['venue']    = trim((string) ($summary['journal-title']['value'] ?? ''));
        $p['pub_type'] = vvur_map_type($summary['type'] ?? '');
        $p['source']   = 'orcid';
        $p['url']      = (string) ($summary['url']['value'] ?? '');

        $year = $summary['publication-date']['year']['value'] ?? null;
        if ($year) { $p['pub_year'] = (int) $year; }

        foreach (($group['external-ids']['external-id'] ?? $summary['external-ids']['external-id'] ?? []) as $ext) {
            if (strtolower((string) ($ext['external-id-type'] ?? '')) === 'doi') {
                $p['doi'] = (string) ($ext['external-id-value'] ?? '');
                break;
            }
        }
        $p['external_id'] = $p['doi'] ?: ((string) ($summary['put-code'] ?? ''));

        if ($p['title'] !== '') { $items[] = $p; }
    }

    // Crossref fills in the byline, the pagination and — the point of the
    // exercise — the citation count. Capped so one import cannot make 60
    // outbound requests and time the page out.
    if ($enrich) {
        $budget = 25;
        foreach ($items as $i => $p) {
            if ($budget <= 0 || $p['doi'] === '') { continue; }
            $budget--;
            $cr = vvur_crossref_search(['doi' => $p['doi']]);
            if ($cr['ok'] && $cr['items']) {
                $rich = $cr['items'][0];
                // ORCID's own title and venue are the author's; keep them.
                $rich['title'] = $p['title'] !== '' ? $p['title'] : $rich['title'];
                $rich['venue'] = $p['venue'] !== '' ? $p['venue'] : $rich['venue'];
                $rich['source'] = 'orcid';
                $items[$i] = $rich;
            }
        }
    }

    return ['ok' => true, 'error' => '', 'items' => $items];
}

/* ==========================================================================
   3. BibTeX  (what Google Scholar's Export button produces)
   ========================================================================== */

/**
 * BibTeX writes a byline as "Mensah, Kwame and Serwaa, Ama" — surname first,
 * entries joined by the word "and". Splitting on the comma alone would turn
 * three authors into six, so split on " and " first, then put each name back
 * into reading order.
 */
function vvur_bibtex_names($raw)
{
    $names = preg_split('/\s+and\s+/i', (string) $raw);
    $out = [];
    foreach ($names as $name) {
        $name = trim($name);
        if ($name === '') { continue; }
        if (substr_count($name, ',') === 1) {
            [$last, $first] = array_map('trim', explode(',', $name));
            $name = trim($first . ' ' . $last);
        }
        $out[] = $name;
    }
    return implode(', ', $out);
}
/** Parse a BibTeX file or paste into normalised publications. */
function vvur_parse_bibtex($text)
{
    $text  = (string) $text;
    $items = [];

    // A regex cannot reliably find where an entry ends: the body is full of
    // braces of its own. Walk the string instead and match them by depth.
    $matches = [];
    $length  = strlen($text);
    $cursor  = 0;
    while (($at = strpos($text, '@', $cursor)) !== false) {
        if (!preg_match('/^@(\w+)\s*\{/', substr($text, $at, 64), $head)) {
            $cursor = $at + 1;
            continue;
        }
        $open  = $at + strlen($head[0]) - 1;   // the entry's opening brace
        $depth = 0;
        $close = -1;
        for ($i = $open; $i < $length; $i++) {
            if ($text[$i] === '{') {
                $depth++;
            } elseif ($text[$i] === '}') {
                if (--$depth === 0) { $close = $i; break; }
            }
        }
        if ($close === -1) { break; }          // unterminated entry — stop here

        $body  = substr($text, $open + 1, $close - $open - 1);
        $comma = strpos($body, ',');
        $matches[] = [
            $head[1],
            $comma === false ? '' : substr($body, 0, $comma),   // cite key
            $comma === false ? $body : substr($body, $comma + 1), // fields
        ];
        $cursor = $close + 1;
    }

    if (!$matches) {
        return ['ok' => false, 'error' => 'No BibTeX entries found. An entry starts with @article{ and ends with a closing brace.', 'items' => []];
    }

    foreach ($matches as $m) {
        $p = vvur_blank_publication();
        $p['pub_type']    = vvur_map_type($m[0]);
        $p['source']      = 'bibtex';
        $p['external_id'] = trim($m[1]);

        // field = {value} | field = "value" | field = value
        preg_match_all('/(\w+)\s*=\s*(\{((?:[^{}]|\{[^{}]*\})*)\}|"([^"]*)"|([^,\n}]+))/s', $m[2], $fields, PREG_SET_ORDER);

        foreach ($fields as $f) {
            $key = strtolower($f[1]);
            $val = trim($f[3] !== '' ? $f[3] : ($f[4] !== '' ? $f[4] : ($f[5] ?? '')));
            $val = trim(preg_replace('/\s+/', ' ', str_replace(['{', '}', '\\'], '', $val)), " ,\t\n");

            switch ($key) {
                case 'title':     $p['title'] = $val; break;
                case 'author':    $p['authors'] = vvur_bibtex_names($val); break;
                case 'journal':
                case 'booktitle':
                case 'series':    if ($p['venue'] === '') { $p['venue'] = $val; } break;
                case 'publisher':
                case 'school':
                case 'institution': $p['publisher'] = $val; break;
                case 'year':      $p['pub_year'] = (int) $val ?: null; break;
                case 'volume':    $p['volume'] = $val; break;
                case 'number':
                case 'issue':     $p['issue'] = $val; break;
                case 'pages':     $p['pages'] = str_replace('--', '–', $val); break;
                case 'doi':       $p['doi'] = preg_replace('#^https?://(dx\.)?doi\.org/#i', '', $val); break;
                case 'url':       $p['url'] = $val; break;
                case 'abstract':  $p['abstract'] = $val; break;
                case 'keywords':  $p['keywords'] = $val; break;
            }
        }

        if ($p['title'] !== '') { $items[] = $p; }
    }

    return $items
        ? ['ok' => true, 'error' => '', 'items' => $items]
        : ['ok' => false, 'error' => 'The entries were found but none of them had a title.', 'items' => []];
}

/* ==========================================================================
   4. CSV  (Google Scholar's other export format)
   ========================================================================== */

/**
 * Parse a CSV export. Column names are matched loosely, so Scholar's own
 * header (Title, Authors, Publication, Volume, Number, Pages, Year, Publisher)
 * and a hand-made spreadsheet both work.
 */
function vvur_parse_csv($text)
{
    $text = trim((string) $text);
    if ($text === '') {
        return ['ok' => false, 'error' => 'Nothing to import.', 'items' => []];
    }

    $handle = fopen('php://temp', 'r+');
    fwrite($handle, $text);
    rewind($handle);

    $header = fgetcsv($handle);
    if (!$header) {
        fclose($handle);
        return ['ok' => false, 'error' => 'The first line could not be read as a CSV header row.', 'items' => []];
    }

    // Normalise the header once, then look every field up by intent.
    $cols = [];
    foreach ($header as $i => $name) {
        $cols[preg_replace('/[^a-z]/', '', strtolower((string) $name))] = $i;
    }
    $pick = static function (array $row, array $names) use ($cols) {
        foreach ($names as $n) {
            if (isset($cols[$n]) && isset($row[$cols[$n]])) {
                $v = trim((string) $row[$cols[$n]]);
                if ($v !== '') { return $v; }
            }
        }
        return '';
    };

    $items = [];
    while (($row = fgetcsv($handle)) !== false) {
        if (count(array_filter($row, static function ($v) { return trim((string) $v) !== ''; })) === 0) {
            continue;
        }

        $p = vvur_blank_publication();
        $p['source']    = 'csv';
        $p['title']     = $pick($row, ['title']);
        $p['authors']   = $pick($row, ['authors', 'author']);
        $p['venue']     = $pick($row, ['publication', 'journal', 'venue', 'booktitle', 'source']);
        $p['publisher'] = $pick($row, ['publisher']);
        $p['volume']    = $pick($row, ['volume']);
        $p['issue']     = $pick($row, ['number', 'issue']);
        $p['pages']     = $pick($row, ['pages']);
        $p['doi']       = preg_replace('#^https?://(dx\.)?doi\.org/#i', '', $pick($row, ['doi']));
        $p['url']       = $pick($row, ['url', 'link']);
        $p['abstract']  = $pick($row, ['abstract']);
        $p['keywords']  = $pick($row, ['keywords', 'tags']);
        $p['citations'] = (int) preg_replace('/\D/', '', $pick($row, ['citations', 'citedby', 'cites']));

        $year = preg_replace('/\D/', '', $pick($row, ['year', 'publicationyear', 'date']));
        if ($year !== '') { $p['pub_year'] = (int) substr($year, 0, 4); }

        $type = $pick($row, ['type', 'publicationtype']);
        if ($type !== '') { $p['pub_type'] = vvur_map_type($type); }

        if ($p['title'] !== '') { $items[] = $p; }
    }
    fclose($handle);

    return $items
        ? ['ok' => true, 'error' => '', 'items' => $items]
        : ['ok' => false, 'error' => 'No rows with a title were found. Check that the file has a header row.', 'items' => []];
}

/* ==========================================================================
   5. Google Scholar  (best effort)
   ========================================================================== */

/**
 * Read a public Google Scholar profile: the three headline metrics and, when
 * the page comes back intact, the publication list.
 *
 * Google serves an interstitial to most server IPs. That is detected here and
 * reported as such, because the failure mode otherwise looks identical to "the
 * profile has no publications".
 */
function vvur_google_scholar_profile($userId, $pageSize = 100)
{
    $userId = trim((string) $userId);
    // Accept a full profile URL as well as the bare id.
    if (preg_match('/[?&]user=([A-Za-z0-9_-]{8,})/', $userId, $m)) {
        $userId = $m[1];
    }
    if (!preg_match('/^[A-Za-z0-9_-]{8,}$/', $userId)) {
        return ['ok' => false, 'error' => 'That does not look like a Google Scholar profile id. Copy the value after "user=" in the profile URL.', 'metrics' => [], 'items' => []];
    }

    $url = 'https://scholar.google.com/citations?hl=en&user=' . rawurlencode($userId)
        . '&view_op=list_works&sortby=pubdate&cstart=0&pagesize=' . max(20, min(100, (int) $pageSize));

    $res = vvur_http_get($url);
    if (!$res['ok']) {
        return ['ok' => false, 'error' => 'Google Scholar could not be read (' . $res['error']
            . ') Use the Export → BibTeX or CSV import instead; it always works.',
            'metrics' => [], 'items' => []];
    }

    $html = $res['body'];

    if (stripos($html, 'gs_captcha') !== false
        || stripos($html, 'unusual traffic') !== false
        || stripos($html, 'id="captcha"') !== false) {
        return ['ok' => false,
            'error' => 'Google Scholar served a CAPTCHA to this server instead of the profile. '
                     . 'That is normal for hosted servers. Open the profile in your own browser, '
                     . 'use Export → BibTeX (or CSV), and paste it into the BibTeX/CSV tab.',
            'metrics' => [], 'items' => []];
    }

    // The metric table: three rows, "All" then "Since 20xx".
    $metrics = [];
    if (preg_match_all('#<td class="gsc_rsb_std">(\d[\d,]*)</td>#', $html, $m)) {
        $nums = array_map(static function ($n) { return (int) str_replace(',', '', $n); }, $m[1]);
        // Order on the page: citations all, citations 5y, h all, h 5y, i10 all, i10 5y.
        $metrics = [
            'citations'    => $nums[0] ?? 0,
            'citations_5y' => $nums[1] ?? 0,
            'h_index'      => $nums[2] ?? 0,
            'h_index_5y'   => $nums[3] ?? 0,
            'i10_index'    => $nums[4] ?? 0,
        ];
    }

    if (preg_match('#<div id="gsc_prf_in"[^>]*>(.*?)</div>#s', $html, $m)) {
        $metrics['name'] = trim(html_entity_decode(strip_tags($m[1]), ENT_QUOTES, 'UTF-8'));
    }
    if (preg_match('#<div class="gsc_prf_il"[^>]*>(.*?)</div>#s', $html, $m)) {
        $metrics['affiliation'] = trim(html_entity_decode(strip_tags($m[1]), ENT_QUOTES, 'UTF-8'));
    }
    if (preg_match_all('#<a class="gsc_prf_inta[^"]*"[^>]*>(.*?)</a>#s', $html, $m)) {
        $metrics['interests'] = implode(', ', array_map(static function ($t) {
            return trim(html_entity_decode(strip_tags($t), ENT_QUOTES, 'UTF-8'));
        }, $m[1]));
    }

    // Publication rows.
    $items = [];
    if (preg_match_all('#<tr class="gsc_a_tr">(.*?)</tr>#s', $html, $rows)) {
        foreach ($rows[1] as $row) {
            $p = vvur_blank_publication();
            $p['source'] = 'google_scholar';

            if (preg_match('#<a[^>]*class="gsc_a_at"[^>]*>(.*?)</a>#s', $row, $m)) {
                $p['title'] = trim(html_entity_decode(strip_tags($m[1]), ENT_QUOTES, 'UTF-8'));
            }
            // Two grey lines under the title: authors, then venue.
            if (preg_match_all('#<div class="gs_gray">(.*?)</div>#s', $row, $m)) {
                $grey = array_map(static function ($t) {
                    return trim(html_entity_decode(strip_tags($t), ENT_QUOTES, 'UTF-8'));
                }, $m[1]);
                $p['authors'] = $grey[0] ?? '';
                // The second line ends in the year and a citation-count span.
                $p['venue'] = preg_replace('/\s*\d{4}\s*$/', '', $grey[1] ?? '');
            }
            if (preg_match('#<span class="gsc_a_h[^"]*">(\d{4})</span>#s', $row, $m)) {
                $p['pub_year'] = (int) $m[1];
            }
            if (preg_match('#<a[^>]*class="gsc_a_ac[^"]*"[^>]*>(\d[\d,]*)</a>#s', $row, $m)) {
                $p['citations'] = (int) str_replace(',', '', $m[1]);
            }
            if (preg_match('#citation_for_view=([^"&]+)#', $row, $m)) {
                $p['external_id'] = $m[1];
            }

            if ($p['title'] !== '') { $items[] = $p; }
        }
    }

    if (!$metrics && !$items) {
        return ['ok' => false,
            'error' => 'The page was fetched but nothing recognisable was in it — Google has probably '
                     . 'changed its markup or returned a consent page. Use the BibTeX or CSV import.',
            'metrics' => [], 'items' => []];
    }

    return ['ok' => true, 'error' => '', 'metrics' => $metrics, 'items' => $items];
}

/* ==========================================================================
   6. ResearchGate  (best effort — usually blocked)
   ========================================================================== */

/**
 * ResearchGate actively blocks automated readers and its terms do not permit
 * scraping. What is safe and useful is to validate the URL, keep it on the
 * profile so visitors can follow it, and read whatever the public page exposes
 * in its own OpenGraph tags.
 */
function vvur_researchgate_profile($url)
{
    $url = trim((string) $url);
    if ($url !== '' && !preg_match('#^https?://#i', $url)) {
        $url = 'https://www.researchgate.net/profile/' . ltrim($url, '/');
    }
    if (!preg_match('#^https://(www\.)?researchgate\.net/#i', $url)) {
        return ['ok' => false, 'error' => 'That is not a ResearchGate profile URL.', 'profile' => []];
    }

    $res = vvur_http_get($url);
    if (!$res['ok']) {
        return ['ok' => false,
            'error' => 'ResearchGate refused the request (' . $res['error'] . '). '
                     . 'The profile link has still been saved and will show on the researcher\'s page; '
                     . 'import their publications from ORCID, Crossref or a BibTeX export.',
            'profile' => ['researchgate_url' => $url]];
    }

    $profile = ['researchgate_url' => $url];
    if (preg_match('#<meta property="og:title" content="([^"]+)"#i', $res['body'], $m)) {
        $profile['name'] = trim(html_entity_decode($m[1], ENT_QUOTES, 'UTF-8'));
    }
    if (preg_match('#<meta property="og:description" content="([^"]+)"#i', $res['body'], $m)) {
        $profile['bio'] = trim(html_entity_decode($m[1], ENT_QUOTES, 'UTF-8'));
    }
    if (preg_match('#<meta property="og:image" content="([^"]+)"#i', $res['body'], $m)) {
        $profile['photo_url'] = $m[1];
    }

    return ['ok' => true, 'error' => '', 'profile' => $profile];
}

/* ==========================================================================
   De-duplication
   ========================================================================== */

/**
 * Does this publication already exist? Matched on DOI first (exact, and the
 * only truly reliable key), then on a normalised title + year, which catches
 * the same paper arriving from Scholar and from Crossref with different
 * punctuation.
 *
 * Returns the existing row id, or 0.
 */
function vvur_find_duplicate(PDO $pdo, array $p)
{
    try {
        // The source's own id is the strongest key there is — an OpenAlex work
        // id or a BibTeX cite key identifies the record even when the DOI is
        // missing and the title has been re-punctuated.
        if (!empty($p['external_id']) && !empty($p['source'])) {
            $stmt = $pdo->prepare(
                "SELECT id FROM research_publications WHERE external_id = ? AND source = ? LIMIT 1"
            );
            $stmt->execute([$p['external_id'], $p['source']]);
            $id = (int) $stmt->fetchColumn();
            if ($id) { return $id; }
        }

        if (!empty($p['doi'])) {
            $stmt = $pdo->prepare("SELECT id FROM research_publications WHERE doi = ? LIMIT 1");
            $stmt->execute([$p['doi']]);
            $id = (int) $stmt->fetchColumn();
            if ($id) { return $id; }
        }

        $key = preg_replace('/[^a-z0-9]/', '', strtolower((string) $p['title']));
        if ($key === '') { return 0; }

        $stmt = $pdo->prepare(
            "SELECT id, title FROM research_publications
              WHERE pub_year <=> ? OR ? IS NULL
              LIMIT 500"
        );
        $stmt->execute([$p['pub_year'], $p['pub_year']]);
        foreach ($stmt->fetchAll(PDO::FETCH_ASSOC) as $row) {
            if (preg_replace('/[^a-z0-9]/', '', strtolower($row['title'])) === $key) {
                return (int) $row['id'];
            }
        }
    } catch (Exception $e) {
        error_log('VVU Scholar: duplicate check failed — ' . $e->getMessage());
    }
    return 0;
}

} // VVU_RESEARCH_IMPORT
