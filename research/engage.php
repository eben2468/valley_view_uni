<?php
/**
 * VVU Scholar — reader actions on a publication: recommend, rate & review.
 *
 * POST only, CSRF-checked. Answers JSON to research-engage.js and redirects
 * back to the publication page for browsers without JavaScript.
 */

require_once __DIR__ . '/../includes/session_bootstrap.php';
require_once __DIR__ . '/../includes/security_headers.php';
require_once __DIR__ . '/../includes/db_connect.php';
require_once __DIR__ . '/includes/research_engage.php';

$wantsJson = (($_SERVER['HTTP_X_REQUESTED_WITH'] ?? '') === 'XMLHttpRequest')
    || strpos((string) ($_SERVER['HTTP_ACCEPT'] ?? ''), 'application/json') !== false;

function engage_reply($ok, $message, array $extra, $pubId, $wantsJson)
{
    if ($wantsJson) {
        header('Content-Type: application/json');
        echo json_encode(array_merge(['ok' => $ok, 'message' => $message], $extra));
        exit;
    }
    $_SESSION['r_engage_flash'] = [$ok, $message];
    header('Location: publication.php?id=' . (int) $pubId . (($_POST['action'] ?? '') === 'review' ? '#reviews' : ''));
    exit;
}

$pubId = (int) ($_POST['id'] ?? 0);

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: publications.php');
    exit;
}
if (!vvu_csrf_valid($_POST['csrf_token'] ?? null)) {
    http_response_code(419);
    engage_reply(false, 'Your session expired. Reload the page and try again.', [], $pubId, $wantsJson);
}
if (!r_engage_ready($pdo)) {
    engage_reply(false, 'This feature is not switched on yet.', [], $pubId, $wantsJson);
}

$st = $pdo->prepare("SELECT id FROM research_publications WHERE id = ? AND is_active = 1");
$st->execute([$pubId]);
if (!$st->fetchColumn()) {
    http_response_code(404);
    engage_reply(false, 'That publication is not in the portal.', [], $pubId, $wantsJson);
}
if (r_is_bot()) {
    engage_reply(false, 'Not available.', [], $pubId, $wantsJson);
}

try {
    switch ($_POST['action'] ?? '') {
        case 'recommend':
            list($on, $count) = r_toggle_recommend($pdo, $pubId);
            engage_reply(true, $on ? 'Thanks for recommending this publication.' : 'Recommendation removed.',
                ['recommended' => $on, 'count' => $count], $pubId, $wantsJson);
            break;

        case 'review':
            if (($_POST['website_url'] ?? '') !== '') {      // honeypot
                engage_reply(false, 'Your review could not be submitted.', [], $pubId, $wantsJson);
            }
            // At most five reviews an hour from one visitor, across all papers.
            $c = $pdo->prepare("SELECT COUNT(*) FROM research_publication_reviews WHERE visitor = ? AND created_at > NOW() - INTERVAL 1 HOUR");
            $c->execute([r_visitor()]);
            if ((int) $c->fetchColumn() >= 5) {
                engage_reply(false, 'You have submitted several reviews just now. Please try again later.', [], $pubId, $wantsJson);
            }
            list($ok, $msg) = r_submit_review($pdo, $pubId, $_POST['reviewer_name'] ?? '', $_POST['reviewer_role'] ?? '',
                $_POST['rating'] ?? 0, $_POST['comment'] ?? '');
            engage_reply($ok, $msg, [], $pubId, $wantsJson);
            break;
    }
} catch (Exception $e) {
    error_log('VVU Scholar engage: ' . $e->getMessage());
    engage_reply(false, 'Something went wrong. Please try again.', [], $pubId, $wantsJson);
}
engage_reply(false, 'Unknown action.', [], $pubId, $wantsJson);
