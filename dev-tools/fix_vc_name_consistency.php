<?php
/**
 * Fix: naming consistency on the Office of the Vice Chancellor page
 * ------------------------------------------------------------------
 * The profile section referred to the Vice Chancellor as "Daniel Ganu, PhD"
 * while the message below it was signed "Professor Daniel Ganu" - the same
 * person named two different ways on one page. Both now read
 * "Professor Daniel Ganu, PhD".
 *
 * Also clears two leftover statements in the profile side-boxes that still
 * described the previous Vice Chancellor.
 *
 * Idempotent: re-running only rewrites values that still hold the old text.
 */
require_once 'includes/db_connect.php';

$log = [];
function say(&$log, $msg, $type = 'ok') { $log[] = [$type, $msg]; }

try {
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $page_id = $pdo->query("SELECT id FROM administration_pages WHERE page_slug = 'office_of_the_vice_chancellor'")->fetchColumn();
    if (!$page_id) throw new Exception('Vice Chancellor page not found.');

    $VC_NAME = 'Professor Daniel Ganu, PhD';

    // section_key => [field_key => new value]
    $updates = [
        'vc_profile' => [
            'name'            => $VC_NAME,
            'title'           => 'Vice Chancellor',
            // side-boxes still carried the previous Vice Chancellor's details
            'experience_title'=> 'Experience',
            'experience_text' => 'Over two decades of university teaching, mentoring and academic administration at Valley View University (2002-2012) and the Adventist University of Africa, Kenya.',
            'impact_title'    => 'Research & Scholarship',
            'impact_text'     => 'Principal Investigator of the African Health Study and Editor-in-Chief of the Pan-African Journal of Health and Environmental Sciences.',
        ],
        'vc_message' => [
            'signature_name'  => $VC_NAME,
            'signature_title' => 'Vice Chancellor',
        ],
    ];

    $findSection = $pdo->prepare("SELECT id FROM administration_content WHERE page_id = ? AND section_key = ?");
    $getField    = $pdo->prepare("SELECT id, field_value FROM administration_content_fields WHERE content_id = ? AND field_key = ?");
    $setField    = $pdo->prepare("UPDATE administration_content_fields SET field_value = ? WHERE id = ?");

    $changed = 0;

    foreach ($updates as $section_key => $fields) {
        $findSection->execute([$page_id, $section_key]);
        $content_id = $findSection->fetchColumn();
        if (!$content_id) { say($log, "Section <b>$section_key</b> not found - skipped", 'info'); continue; }

        foreach ($fields as $fkey => $new) {
            $getField->execute([$content_id, $fkey]);
            $row = $getField->fetch(PDO::FETCH_ASSOC);
            if (!$row) { say($log, "Field <b>$section_key.$fkey</b> not found - skipped", 'info'); continue; }

            $old = AdministrationContentPlainValue($row['field_value']);
            if ($old === $new) continue;

            $setField->execute([$new, $row['id']]);
            $changed++;
            say($log, "<b>$section_key.$fkey</b><br><span style='opacity:.65'>was:</span> " . htmlspecialchars($old) . "<br><span style='opacity:.65'>now:</span> " . htmlspecialchars($new));
        }
    }

    say($log, $changed > 0 ? "Updated <b>$changed</b> field(s)." : 'Everything already consistent - nothing to change.', 'info');

} catch (Exception $e) {
    say($log, 'Error: ' . htmlspecialchars($e->getMessage()), 'error');
}

// Strip the <p> wrappers CKEditor may have added, for comparison/display
function AdministrationContentPlainValue($v) {
    $v = html_entity_decode((string) $v, ENT_QUOTES | ENT_HTML5, 'UTF-8');
    return trim(preg_replace('/\s+/u', ' ', strip_tags($v)));
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Fix - Vice Chancellor Name Consistency</title>
<style>
    body{font-family:system-ui,-apple-system,'Segoe UI',sans-serif;background:#f1f5f9;margin:0;padding:40px 20px;color:#1e293b}
    .box{max-width:760px;margin:0 auto;background:#fff;border-radius:20px;padding:40px;box-shadow:0 10px 40px rgba(0,0,0,.06)}
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
    <h1>Vice Chancellor &mdash; Name Consistency</h1>
    <p class="sub">Harmonises how the Vice Chancellor is named across the page.</p>
    <?php foreach ($log as $row): ?>
        <div class="row <?php echo $row[0]; ?>"><?php echo $row[1]; ?></div>
    <?php endforeach; ?>
    <a class="btn" href="admin/manage_administration_pages.php">Open Admin Manager</a>
    <a class="btn alt" href="office_of_the_vice_chancellor.php">View the Page</a>
</div>
</body>
</html>
