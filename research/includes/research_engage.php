<?php
/**
 * VVU Scholar — reads, recommendations, ratings, reviews and impact figures.
 *
 * Two kinds of numbers appear next to every publication:
 *
 *  • Counted on this portal: Reads (people who opened the publication page),
 *    Recommendations (one per visitor), Ratings and Reviews (written by
 *    visitors; a review with text waits for the Research Office).
 *  • Fetched from the scholarly record by the scheduled sync: Citations
 *    (OpenAlex / Crossref), Readers (Mendeley, via Altmetric), Altmetric
 *    attention score, FWCI (field-weighted citation impact) and the citation
 *    percentile against papers of the same field and year (OpenAlex).
 *
 * Visitors are identified by a salted hash of address + browser, never stored
 * in the clear. Schema: sql/research_quality_migration.sql
 */

require_once __DIR__ . '/research_helper.php';

if (!defined('VVU_RESEARCH_ENGAGE')) {
    define('VVU_RESEARCH_ENGAGE', 1);

/** True once the metrics columns and tables exist. */
function r_engage_ready(PDO $pdo)
{
    static $ok = null;
    if ($ok === null) {
        try {
            $pdo->query("SELECT views, recommendations, rating_sum, rating_count FROM research_publications LIMIT 1");
            $pdo->query("SELECT 1 FROM research_publication_hits LIMIT 1");
            $pdo->query("SELECT 1 FROM research_publication_reviews LIMIT 1");
            $ok = true;
        } catch (Exception $e) {
            $ok = false;
        }
    }
    return $ok;
}

/** A stable, anonymous id for this visitor. */
function r_visitor()
{
    $salt = (string) (getenv('VVU_VISITOR_SALT') ?: 'vvu-scholar');
    $ip   = (string) ($_SERVER['REMOTE_ADDR'] ?? '');
    $ua   = substr((string) ($_SERVER['HTTP_USER_AGENT'] ?? ''), 0, 200);
    return sha1($salt . '|' . $ip . '|' . $ua);
}

/** Search engines and link previews are not readers. */
function r_is_bot()
{
    $ua = (string) ($_SERVER['HTTP_USER_AGENT'] ?? '');
    return $ua === '' || (bool) preg_match('/bot|crawl|spider|slurp|preview|fetch|curl|wget|python|httpclient|headless|monitor/i', $ua);
}

/** Counts one read per visitor per day. */
function r_record_view(PDO $pdo, $pubId)
{
    if (!r_engage_ready($pdo) || r_is_bot()) {
        return;
    }
    try {
        $st = $pdo->prepare("INSERT IGNORE INTO research_publication_hits (publication_id, visitor, kind, day) VALUES (?,?, 'view', CURDATE())");
        $st->execute([(int) $pubId, r_visitor()]);
        if ($st->rowCount()) {
            $pdo->prepare("UPDATE research_publications SET views = views + 1 WHERE id = ?")->execute([(int) $pubId]);
        }
    } catch (Exception $e) {
        error_log('VVU Scholar: view not recorded — ' . $e->getMessage());
    }
}

/** Counts one profile view per visitor per day. */
function r_record_scholar_view(PDO $pdo, $scholarId)
{
    if (!r_engage_ready($pdo) || r_is_bot()) {
        return;
    }
    try {
        // Profile views share the hits table, keyed by the negated scholar id
        // so they can never collide with a publication id.
        $st = $pdo->prepare("INSERT IGNORE INTO research_publication_hits (publication_id, visitor, kind, day) VALUES (?,?, 'scholar_view', CURDATE())");
        $st->execute([-(int) $scholarId, r_visitor()]);
        if ($st->rowCount()) {
            $pdo->prepare("UPDATE research_scholars SET views = views + 1 WHERE id = ?")->execute([(int) $scholarId]);
        }
    } catch (Exception $e) {
        error_log('VVU Scholar: profile view not recorded — ' . $e->getMessage());
    }
}

/** Has this visitor already recommended the publication? */
function r_has_recommended(PDO $pdo, $pubId)
{
    if (!r_engage_ready($pdo)) {
        return false;
    }
    $st = $pdo->prepare("SELECT 1 FROM research_publication_hits WHERE publication_id = ? AND visitor = ? AND kind = 'recommend'");
    $st->execute([(int) $pubId, r_visitor()]);
    return (bool) $st->fetchColumn();
}

/** Toggles this visitor's recommendation. Returns [recommended?, total]. */
function r_toggle_recommend(PDO $pdo, $pubId)
{
    $pubId = (int) $pubId;
    $v = r_visitor();
    if (r_has_recommended($pdo, $pubId)) {
        $pdo->prepare("DELETE FROM research_publication_hits WHERE publication_id = ? AND visitor = ? AND kind = 'recommend'")->execute([$pubId, $v]);
        $pdo->prepare("UPDATE research_publications SET recommendations = GREATEST(recommendations - 1, 0) WHERE id = ?")->execute([$pubId]);
        $on = false;
    } else {
        $pdo->prepare("INSERT IGNORE INTO research_publication_hits (publication_id, visitor, kind, day) VALUES (?,?, 'recommend', '2000-01-01')")->execute([$pubId, $v]);
        $pdo->prepare("UPDATE research_publications SET recommendations = recommendations + 1 WHERE id = ?")->execute([$pubId]);
        $on = true;
    }
    $st = $pdo->prepare("SELECT recommendations FROM research_publications WHERE id = ?");
    $st->execute([$pubId]);
    return [$on, (int) $st->fetchColumn()];
}

/** Rebuilds a publication's rating totals from its approved reviews. */
function r_recount_ratings(PDO $pdo, $pubId)
{
    $pdo->prepare(
        "UPDATE research_publications p
            SET p.rating_sum   = (SELECT COALESCE(SUM(rating), 0) FROM research_publication_reviews r WHERE r.publication_id = p.id AND r.status = 'approved'),
                p.rating_count = (SELECT COUNT(*)                FROM research_publication_reviews r WHERE r.publication_id = p.id AND r.status = 'approved')
          WHERE p.id = ?"
    )->execute([(int) $pubId]);
}

/**
 * Stores a rating, with an optional written review. A rating on its own
 * counts at once; a written review waits for approval when the setting says
 * so. Returns [ok, message].
 */
function r_submit_review(PDO $pdo, $pubId, $name, $role, $rating, $comment)
{
    $name    = trim(strip_tags((string) $name));
    $role    = trim(strip_tags((string) $role));
    $comment = trim(strip_tags((string) $comment));
    $rating  = (int) $rating;
    if ($rating < 1 || $rating > 5) {
        return [false, 'Choose a rating from 1 to 5 stars.'];
    }
    if ($name === '' || mb_strlen($name) > 120) {
        return [false, 'Enter your name.'];
    }
    if (mb_strlen($comment) > 2000) {
        return [false, 'Please keep the review under 2,000 characters.'];
    }
    if (preg_match_all('#https?://#i', $comment) > 2) {
        return [false, 'Please remove the links from your review.'];
    }
    $needsApproval = $comment !== '' && r_set($pdo, 'reviews_require_approval', '1') === '1';
    try {
        $pdo->prepare(
            "INSERT INTO research_publication_reviews (publication_id, reviewer_name, reviewer_role, rating, comment, status, visitor)
             VALUES (?,?,?,?,?,?,?)"
        )->execute([(int) $pubId, mb_substr($name, 0, 120), mb_substr($role, 0, 120) ?: null, $rating,
                    $comment !== '' ? $comment : null, $needsApproval ? 'pending' : 'approved', r_visitor()]);
    } catch (PDOException $e) {
        if ((int) ($e->errorInfo[1] ?? 0) === 1062) {
            return [false, 'You have already rated this publication. Thank you!'];
        }
        throw $e;
    }
    r_recount_ratings($pdo, $pubId);
    return [true, $needsApproval
        ? 'Thank you! Your rating has been received and your review will appear once the Research Office has checked it.'
        : 'Thank you for rating this publication.'];
}

/** Approved reviews with text, newest first. */
function r_reviews(PDO $pdo, $pubId, $limit = 20)
{
    if (!r_engage_ready($pdo)) {
        return [];
    }
    $st = $pdo->prepare(
        "SELECT reviewer_name, reviewer_role, rating, comment, created_at
           FROM research_publication_reviews
          WHERE publication_id = ? AND status = 'approved' AND comment IS NOT NULL AND comment <> ''
          ORDER BY created_at DESC LIMIT " . (int) $limit
    );
    $st->execute([(int) $pubId]);
    return $st->fetchAll(PDO::FETCH_ASSOC);
}

/** Average rating to one decimal, or null with no ratings. */
function r_rating(array $p)
{
    $n = (int) ($p['rating_count'] ?? 0);
    return $n > 0 ? round((int) $p['rating_sum'] / $n, 1) : null;
}

/** "★★★★☆" as accessible markup. */
function r_stars($value, $size = '')
{
    $value = (float) $value;
    $out = '<span class="vvus-stars' . ($size ? ' vvus-stars--' . $size : '') . '" role="img" aria-label="' . r_e(number_format($value, 1)) . ' out of 5 stars">';
    for ($i = 1; $i <= 5; $i++) {
        $cls = $value >= $i ? 'fa-solid fa-star' : ($value >= $i - 0.5 ? 'fa-solid fa-star-half-stroke' : 'fa-regular fa-star');
        $out .= '<i class="' . $cls . '" aria-hidden="true"></i>';
    }
    return $out . '</span>';
}

/**
 * The impact figures for one publication, in display order, skipping any the
 * record does not have. Each: [label, value, icon, hint].
 */
function r_publication_impact(array $p)
{
    $out = [];
    // Where the citation figure comes from: the highest of the sources checked.
    $src = [];
    foreach (['cites_openalex' => 'OpenAlex', 'cites_crossref' => 'Crossref', 'cites_s2' => 'Semantic Scholar', 'cites_manual' => 'Google Scholar'] as $col => $label) {
        if (isset($p[$col]) && $p[$col] !== null) {
            $src[] = $label . ' ' . number_format((int) $p[$col]);
        }
    }
    $out[] = ['Citations', number_format((int) $p['citations']), 'fa-quote-right',
        $src ? 'Highest count across sources — ' . implode(' · ', $src) . '.' : 'Times other works have cited it.'];
    if (!empty($p['citations_5y'])) {
        $out[] = ['Cites (5 yrs)', number_format((int) $p['citations_5y']), 'fa-calendar-days', 'Citations received in the last five years (OpenAlex).'];
    }
    if (!empty($p['influential_citations'])) {
        $out[] = ['Influential', number_format((int) $p['influential_citations']), 'fa-bolt', 'Citing papers that build substantially on this work (Semantic Scholar).'];
    }
    if (isset($p['views'])) {
        $out[] = ['Reads', number_format((int) $p['views']), 'fa-book-open-reader', 'Visitors who opened this publication on VVU Scholar.'];
    }
    if (isset($p['recommendations'])) {
        $out[] = ['Recommendations', number_format((int) $p['recommendations']), 'fa-thumbs-up', 'Readers who recommended it on VVU Scholar.'];
    }
    if (($r = r_rating($p)) !== null) {
        $out[] = ['Rating (of 5)', number_format($r, 1), 'fa-star', (int) $p['rating_count'] . ' rating(s) from readers.'];
    }
    if (isset($p['readers']) && $p['readers'] !== null && (int) $p['readers'] > 0) {
        $out[] = ['Readers', number_format((int) $p['readers']), 'fa-bookmark', 'Saved to reference libraries (Mendeley readers, via Altmetric).'];
    }
    if (isset($p['altmetric_score']) && $p['altmetric_score'] !== null && (float) $p['altmetric_score'] > 0) {
        $out[] = ['Altmetric', rtrim(rtrim(number_format((float) $p['altmetric_score'], 1), '0'), '.'), 'fa-bullhorn', 'Online attention: news, policy, blogs and social media (Altmetric).'];
    }
    if (isset($p['fwci']) && $p['fwci'] !== null) {
        $out[] = ['FWCI', number_format((float) $p['fwci'], 2), 'fa-scale-balanced', 'Field-weighted citation impact: 1.00 is the world average for papers of the same field, type and year (OpenAlex).'];
    }
    if (isset($p['citation_percentile']) && $p['citation_percentile'] !== null && (float) $p['citation_percentile'] >= 50) {
        $top = max(1, (int) ceil(100 - (float) $p['citation_percentile']));
        $out[] = ['Top ' . $top . '%', 'cited', 'fa-trophy', 'More cited than ' . (int) $p['citation_percentile'] . '% of papers from the same field and year (OpenAlex).'];
    }
    return $out;
}

/** Totals for a researcher's profile. */
function r_scholar_impact(PDO $pdo, $scholarId)
{
    $out = ['reads' => 0, 'recommendations' => 0, 'ratings' => 0, 'rating' => null, 'readers' => 0,
            'altmetric' => 0, 'fwci' => null, 'top10' => 0, 'views' => 0];
    if (!r_engage_ready($pdo)) {
        return $out;
    }
    try {
        $st = $pdo->prepare(
            "SELECT COALESCE(SUM(p.views),0) reads, COALESCE(SUM(p.recommendations),0) recs,
                    COALESCE(SUM(p.rating_sum),0) rs, COALESCE(SUM(p.rating_count),0) rc,
                    COALESCE(SUM(p.readers),0) readers, COALESCE(SUM(p.altmetric_score),0) alt,
                    AVG(p.fwci) fwci, SUM(p.citation_percentile >= 90) top10
               FROM research_publications p
              WHERE p.is_active = 1
                AND (p.scholar_id = :id OR p.id IN (SELECT publication_id FROM research_publication_authors WHERE scholar_id = :id2))"
        );
        $st->execute([':id' => (int) $scholarId, ':id2' => (int) $scholarId]);
        $r = $st->fetch(PDO::FETCH_ASSOC);
        $out['reads'] = (int) $r['reads'];
        $out['recommendations'] = (int) $r['recs'];
        $out['ratings'] = (int) $r['rc'];
        $out['rating'] = (int) $r['rc'] > 0 ? round((int) $r['rs'] / (int) $r['rc'], 1) : null;
        $out['readers'] = (int) $r['readers'];
        $out['altmetric'] = (float) $r['alt'];
        $out['fwci'] = $r['fwci'] !== null ? round((float) $r['fwci'], 2) : null;
        $out['top10'] = (int) $r['top10'];
        $s = $pdo->prepare("SELECT views FROM research_scholars WHERE id = ?");
        $s->execute([(int) $scholarId]);
        $out['views'] = (int) $s->fetchColumn();
    } catch (Exception $e) {
        error_log('VVU Scholar: impact totals — ' . $e->getMessage());
    }
    return $out;
}

/** Portal-wide engagement totals, for the stat tiles. */
function r_engage_totals(PDO $pdo)
{
    $out = ['reads' => 0, 'recommendations' => 0, 'reviews' => 0, 'readers' => 0, 'top10_papers' => 0];
    if (!r_engage_ready($pdo)) {
        return $out;
    }
    try {
        $r = $pdo->query(
            "SELECT COALESCE(SUM(views),0) a, COALESCE(SUM(recommendations),0) b, COALESCE(SUM(rating_count),0) c,
                    COALESCE(SUM(readers),0) d, COALESCE(SUM(citation_percentile >= 90),0) e
               FROM research_publications WHERE is_active = 1"
        )->fetch(PDO::FETCH_NUM);
        $out = ['reads' => (int) $r[0], 'recommendations' => (int) $r[1], 'reviews' => (int) $r[2],
                'readers' => (int) $r[3], 'top10_papers' => (int) $r[4]];
    } catch (Exception $e) {
        // columns not migrated
    }
    return $out;
}

/**
 * Automatic updates without a cron job: when the interval has passed, the
 * next portal visit triggers a short update AFTER its page has been sent
 * (PHP-FPM's fastcgi_finish_request), so the visitor never waits. Hosts
 * without PHP-FPM rely on research/cron.php or the admin button instead.
 */
function r_autosync_tick(PDO $pdo)
{
    if (!function_exists('fastcgi_finish_request') || r_is_bot()) {
        return;
    }
    if (r_set($pdo, 'auto_sync_enabled', '0') !== '1') {
        return;
    }
    $hours = max(1, (int) r_set($pdo, 'auto_sync_hours', '24'));
    $last  = strtotime((string) r_set($pdo, 'last_auto_sync_at', '')) ?: 0;
    if (time() - $last < $hours * 3600) {
        return;
    }
    register_shutdown_function(static function () use ($pdo) {
        @fastcgi_finish_request();
        try {
            require_once __DIR__ . '/../../admin/includes/research_autosync.php';
            vvur_auto_sync($pdo, 'visit', 55);
        } catch (Throwable $e) {
            error_log('VVU Scholar visit-triggered sync: ' . $e->getMessage());
        }
    });
}

} // VVU_RESEARCH_ENGAGE
