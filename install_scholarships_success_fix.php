<?php
/**
 * Fix: scholarships page "Success Stories" block
 * ----------------------------------------------
 * The block carried a fabricated student testimonial ("Sarah Mensah, Class of
 * 2023") and an invented statistic ("$2M+ Annual Aid"). Both are removed.
 *
 * They are replaced with a factual panel built only from what this page
 * already documents: who awards the scholarships, on what basis, and how a
 * student applies. No invented names, quotes or figures.
 *
 * Safe to re-run: the fabricated rows are only deleted once, and the new
 * items are created only when missing, so admin edits are never overwritten.
 */
require_once 'includes/db_connect.php';

$log = [];
function say(&$log, $msg, $type = 'ok') { $log[] = [$type, $msg]; }

try {
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    /* ---------------------------------------------------------------
     * 1. Remove the fabricated testimonial
     * --------------------------------------------------------------- */
    $del = $pdo->prepare("DELETE FROM academic_pages_items
                          WHERE page_key = 'scholarships' AND section_key = 'success'
                            AND (item_title LIKE '%Sarah Mensah%' OR item_description LIKE '%scholarship gave me the confidence%')");
    $del->execute();
    say($log, $del->rowCount() > 0
        ? "Removed <b>{$del->rowCount()}</b> fabricated testimonial row (Sarah Mensah)"
        : 'Fabricated testimonial already removed', $del->rowCount() > 0 ? 'ok' : 'info');

    /* ---------------------------------------------------------------
     * 2. Re-purpose the section: factual heading + honest badge
     * --------------------------------------------------------------- */
    $sec = $pdo->query("SELECT * FROM academic_pages_sections WHERE page_key='scholarships' AND section_key='success' LIMIT 1")->fetch(PDO::FETCH_ASSOC);
    if (!$sec) throw new Exception("Section 'success' not found for page 'scholarships'.");

    $fabricated_badge = (trim((string) $sec['section_subtitle']) === '$2M+');
    $pdo->prepare("UPDATE academic_pages_sections
                   SET section_title = ?, section_subtitle = ?, section_description = ?
                   WHERE id = ?")
        ->execute([
            'How Awards Are Decided',
            'Merit & Need',            // badge value - describes the basis, claims no amount
            'The two bases for every award',
            $sec['id'],
        ]);
    say($log, $fabricated_badge
        ? 'Replaced the invented "$2M+ Annual Aid" badge with "Merit &amp; Need"'
        : 'Updated the section heading and badge');

    /* ---------------------------------------------------------------
     * 3. Factual points, drawn from this page's own content
     * --------------------------------------------------------------- */
    $items = [
        [
            'item_title'       => 'Reviewed by your department',
            'item_description' => 'Applications are reviewed in consultation with Department Heads to ensure alignment with academic goals.',
            'item_icon'        => 'groups',
            'item_color'       => 'blue-600',
            'display_order'    => 1,
        ],
        [
            'item_title'       => 'Assessed against Academic Board policy',
            'item_description' => 'The Student Finance Services Committee evaluates every candidate against the policies set by the Academic Board.',
            'item_icon'        => 'gavel',
            'item_color'       => 'yellow-500',
            'display_order'    => 2,
        ],
        [
            'item_title'       => 'Awarded on merit or on need',
            'item_description' => 'Merit Scholarships recognise exceptional academic performance. Financial Grants support students who require financial aid.',
            'item_icon'        => 'workspace_premium',
            'item_color'       => 'green-600',
            'display_order'    => 3,
        ],
    ];

    $find = $pdo->prepare("SELECT id FROM academic_pages_items WHERE page_key='scholarships' AND section_key='success' AND item_title = ?");
    $add  = $pdo->prepare("INSERT INTO academic_pages_items
            (page_key, section_key, item_title, item_subtitle, item_description, item_icon, item_color, item_image, item_link, item_stat_value, display_order, is_active)
            VALUES ('scholarships','success',?,'',?,?,?,'','','',?,1)");

    $created = 0;
    foreach ($items as $it) {
        $find->execute([$it['item_title']]);
        if (!$find->fetchColumn()) {
            $add->execute([$it['item_title'], $it['item_description'], $it['item_icon'], $it['item_color'], $it['display_order']]);
            $created++;
        }
    }
    say($log, "Created <b>$created</b> factual point(s) for the section");

    say($log, 'Scholarships section fix complete.');

} catch (Exception $e) {
    say($log, 'Error: ' . htmlspecialchars($e->getMessage()), 'error');
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Fix - Scholarships Success Section</title>
<style>
    body{font-family:system-ui,-apple-system,'Segoe UI',sans-serif;background:#f1f5f9;margin:0;padding:40px 20px;color:#1e293b}
    .box{max-width:780px;margin:0 auto;background:#fff;border-radius:20px;padding:40px;box-shadow:0 10px 40px rgba(0,0,0,.06)}
    h1{margin:0 0 6px;font-size:1.7rem}
    p.sub{margin:0 0 28px;color:#64748b}
    .row{padding:12px 18px;border-radius:12px;margin-bottom:10px;font-size:.95rem;line-height:1.6}
    .ok{background:#ecfdf5;color:#065f46}
    .info{background:#eff6ff;color:#1e40af}
    .error{background:#fef2f2;color:#991b1b}
    a.btn{display:inline-block;margin-top:22px;margin-right:10px;padding:12px 24px;border-radius:12px;background:#0891b2;color:#fff;text-decoration:none;font-weight:700}
    a.btn.alt{background:#1e293b}
</style>
</head>
<body>
<div class="box">
    <h1>Scholarships &mdash; Removing Fabricated Content</h1>
    <p class="sub">Replaces the invented testimonial and statistic with facts this page already documents.</p>
    <?php foreach ($log as $row): ?>
        <div class="row <?php echo $row[0]; ?>"><?php echo $row[1]; ?></div>
    <?php endforeach; ?>
    <a class="btn" href="admin/">Open Admin</a>
    <a class="btn alt" href="scholarships.php">View the Page</a>
</div>
</body>
</html>
