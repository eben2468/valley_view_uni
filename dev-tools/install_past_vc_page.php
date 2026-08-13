<?php
/**
 * Installer: "Past Vice-Chancellors" page (past-vc.php)
 * ----------------------------------------------------
 * Registers the page in the administration_pages CMS tables so that all of its
 * content becomes editable from /admin/manage_administration_pages.php
 *
 * Safe to run multiple times - existing sections/fields are never overwritten,
 * only missing ones are created.
 */
require_once 'includes/db_connect.php';

header('Content-Type: text/html; charset=utf-8');

$SLUG  = 'past-vc';
$TITLE = 'Past Vice-Chancellors';

$log = [];

function say(&$log, $msg, $type = 'ok') { $log[] = [$type, $msg]; }

try {
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    /* ---------------------------------------------------------------
     * 1. Page record
     * --------------------------------------------------------------- */
    $stmt = $pdo->prepare("SELECT id FROM administration_pages WHERE page_slug = ?");
    $stmt->execute([$SLUG]);
    $page_id = $stmt->fetchColumn();

    if (!$page_id) {
        $pdo->prepare("INSERT INTO administration_pages (page_slug, page_title, page_name, is_active) VALUES (?, ?, ?, 1)")
            ->execute([$SLUG, $TITLE, $TITLE]);
        $page_id = $pdo->lastInsertId();
        say($log, "Created page record <b>$SLUG</b> (ID: $page_id)");
    } else {
        say($log, "Page record <b>$SLUG</b> already exists (ID: $page_id)", 'info');
    }

    /* ---------------------------------------------------------------
     * 2. Section + field definitions
     * --------------------------------------------------------------- */
    $IMG = 'images/past-vice-chancellors/';

    // Chronological roll of leaders taken from the university archive.
    // Each entry: [name, title, tenure, image, note]
    $leaders = [
        ['Walton S. Whaley',      'Director / President',        '1980 / 1983 - 1987', $IMG . 'whailey2.jpg',      'Pioneer leader of the Adventist Missionary College, guiding the institution through its formative Bekwai and Adenta years.'],
        ['Emmanuel Osei',         'Acting Director',             '1981 - 1983',        $IMG . 'dummy.jpg',         'Steered the young seminary during its transitional period before the move to Adenta near Accra.'],
        ['Arlyn C. Sundsted',     'Acting President',            '1987 - 1988',        $IMG . 'sunsted1.jpg',      'Provided continuity of academic leadership during a critical year of institutional growth.'],
        ['Christus A. Mensah',    'Acting President',            '1988 - 1989',        $IMG . 'c-a-mensah.jpg',    'Oversaw preparations for the historic relocation of the institution to Oyibi.'],
        ['Isreal T. Agboka',      'Acting President',            '1989 - 1990',        $IMG . 'agboka.jpg',        'Led the college through its first year at the Oyibi campus as Valley View College.'],
        ['Donald Eichner',        'President',                   '1989 / 1990 - 1991', $IMG . 'dummy.jpg',         'Championed early campus development and the expansion of academic programmes at Oyibi.'],
        ['Francis Chase',         'Acting President',            '1991 - 1992',        $IMG . 'dummy.jpg',         'Maintained institutional stability and mission focus during a season of transition.'],
        ['Roland L. Joachim',     'President',                   '1992 - 1994',        $IMG . 'joachim.jpg',       'Strengthened the academic foundations that prepared the college for national accreditation.'],
        ['Seth A. Laryea',        'President / Vice-Chancellor', '1995 - 2010',        $IMG . 'laryea.jpg',        'The longest serving head of the institution. Under his leadership Valley View became the first accredited private tertiary institution in Ghana (1997) and the first to receive a Presidential Charter (2006).'],
        ['Prof. Daniel Buor',     'Vice-Chancellor',             '2010 - 2015',        $IMG . 'buor.jpg',          'Expanded graduate education and research output while consolidating the university\'s chartered status.'],
        ['Prof. Daniel K. Bediako', 'Vice-Chancellor',           '2015 - 2022',        $IMG . 'prof-bediako.jpg',  'Advanced scholarship, faculty development and the international profile of the university.'],
        ['Prof. William K. Koomson', 'President / Vice-Chancellor', '2022 - 2025',     $IMG . 'prof-koomson.jpg',  'Brought three decades of international service to bear on digital transformation and community outreach.'],
        ['Prof. Daniel Ganu',     'Vice-Chancellor',             '2026 - Present',     $IMG . 'prof-ganu.jpeg',    'The current Vice-Chancellor, leading Valley View University into its next era of academic and spiritual excellence.'],
    ];

    $definitions = [
        'hero_section' => ['type' => 'hero', 'order' => 1, 'fields' => [
            ['badge_text',       'Our Heritage', 'text'],
            ['title_main',       'Past Vice', 'text'],
            ['title_highlight',  'Chancellors', 'text'],
            ['subtitle',         'From a modest seminary in Bekwai to Ghana\'s first chartered private university - the men who carried the vision forward.', 'textarea'],
            ['background_image', 'https://lh3.googleusercontent.com/aida-public/AB6AXuBo5kZ6ARGIXa5op7ZfwzuPd_3xc-gFuuNqLtlQhfI9FuPove2RJVSOjvla0bPKFyCQOvwkTsYTIZdrFobxFPda_ADJkaxK8QL0qmmVPAKWk_9tEnOjMndUI5kaG1-10q1H3lzodyVSzIKbkMJ7WqnJu9KTZSW1d6XFiKZSRiTidjPlL62RZcBjVtugVdJVT5ppDqxQJA6zTqKqiuG3IU5tUDZ6EebyhVcSLQd5pruhpjRWsJ4DE2gmxOgB7LP1mLj5zrE5d-hXP6bE', 'image'],
        ]],

        'introduction' => ['type' => 'section', 'order' => 2, 'fields' => [
            ['section_label',  'The Story So Far', 'text'],
            ['section_title',  'A Legacy Built by Many Hands', 'text'],
            ['paragraph_1',    'Initially, casually christened "Bekwai Seminary," the Adventist Missionary College (renamed Adventist Ministerial College in 1981) was hosted by the Bekwai Secondary School from September 1979 to September 1983, when it collapsed to give way to the Adventist Missionary College that started at Adenta near Accra (1983-1989).', 'textarea'],
            ['paragraph_2',    'In January 1989, the institution was moved to Oyibi and renamed Valley View College.', 'textarea'],
            ['paragraph_3',    'When the National Accreditation Board granted an institutional accreditation to the College in 1997 - becoming the first private tertiary institution to be accredited in Ghana - the name Valley View University was adopted. In 2006, a Presidential Charter placed the institution on a full university status, again becoming the first private tertiary educational institution to be chartered in Ghana.', 'textarea'],
            ['paragraph_4',    'Since its establishment, there have been five acting presidents and six substantive presidents, three of them bearing the title "Vice-Chancellor" - a title reserved for the head of a chartered university in Ghana.', 'textarea'],
        ]],

        'stats_section' => ['type' => 'section', 'order' => 3, 'fields' => [
            ['stat_1_val',   '1979',  'text'],
            ['stat_1_label', 'Founded as Bekwai Seminary', 'text'],
            ['stat_2_val',   '1997',  'text'],
            ['stat_2_label', 'First Accredited Private University', 'text'],
            ['stat_3_val',   '2006',  'text'],
            ['stat_3_label', 'First Presidential Charter', 'text'],
            ['stat_4_val',   '13',    'text'],
            ['stat_4_label', 'Leaders Since Inception', 'text'],
        ]],

        'timeline_section' => ['type' => 'section', 'order' => 4, 'fields' => [
            ['section_label',    'Leadership Timeline', 'text'],
            ['section_title',    'Those Who Led the Way', 'text'],
            ['section_subtitle', 'Five acting presidents and six substantive presidents have shaped Valley View University across four decades of service, scholarship and faith.', 'textarea'],
        ]],

        'cta_section' => ['type' => 'section', 'order' => 100, 'fields' => [
            ['cta_title',       'Continuing a Legacy of', 'text'],
            ['cta_highlight',   'Excellence and Service', 'text'],
            ['cta_description', 'Discover the office that carries this legacy forward today, and the story of how Valley View University came to be.', 'textarea'],
            ['button_1_text',   'Office of the Vice-Chancellor', 'text'],
            ['button_1_url',    'office_of_the_vice_chancellor.php', 'text'],
            ['button_2_text',   'Our History', 'text'],
            ['button_2_url',    'history.php', 'text'],
        ]],
    ];

    // Build one section per leader (order 10..) so the admin can edit each entry.
    foreach ($leaders as $i => $l) {
        $definitions['leader_' . ($i + 1)] = [
            'type'  => 'card',
            'order' => 10 + $i,
            'fields' => [
                ['name',   $l[0], 'text'],
                ['title',  $l[1], 'text'],
                ['tenure', $l[2], 'text'],
                ['photo',  $l[3], 'image'],
                ['note',   $l[4], 'textarea'],
            ],
        ];
    }

    /* ---------------------------------------------------------------
     * 3. Create missing sections & fields
     * --------------------------------------------------------------- */
    $findSection = $pdo->prepare("SELECT id FROM administration_content WHERE page_id = ? AND section_key = ?");
    $addSection  = $pdo->prepare("INSERT INTO administration_content (page_id, section_type, section_key, content_order, is_active) VALUES (?, ?, ?, ?, 1)");
    $findField   = $pdo->prepare("SELECT id FROM administration_content_fields WHERE content_id = ? AND field_key = ?");
    $addField    = $pdo->prepare("INSERT INTO administration_content_fields (content_id, field_key, field_value, field_type) VALUES (?, ?, ?, ?)");

    $newSections = 0; $newFields = 0;

    foreach ($definitions as $key => $def) {
        $findSection->execute([$page_id, $key]);
        $content_id = $findSection->fetchColumn();

        if (!$content_id) {
            $addSection->execute([$page_id, $def['type'], $key, $def['order']]);
            $content_id = $pdo->lastInsertId();
            $newSections++;
        }

        foreach ($def['fields'] as $f) {
            list($fkey, $fval, $ftype) = $f;
            $findField->execute([$content_id, $fkey]);
            if (!$findField->fetchColumn()) {
                $addField->execute([$content_id, $fkey, $fval, $ftype]);
                $newFields++;
            }
        }
    }

    say($log, "Sections created: <b>$newSections</b> &nbsp;|&nbsp; Fields created: <b>$newFields</b>");
    say($log, 'Installation complete.');

} catch (Exception $e) {
    say($log, 'Error: ' . htmlspecialchars($e->getMessage()), 'error');
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Install - Past Vice-Chancellors Page</title>
<style>
    body{font-family:system-ui,-apple-system,'Segoe UI',sans-serif;background:#f1f5f9;margin:0;padding:40px 20px;color:#1e293b}
    .box{max-width:760px;margin:0 auto;background:#fff;border-radius:20px;padding:40px;box-shadow:0 10px 40px rgba(0,0,0,.06)}
    h1{margin:0 0 6px;font-size:1.7rem}
    p.sub{margin:0 0 28px;color:#64748b}
    .row{padding:12px 18px;border-radius:12px;margin-bottom:10px;font-size:.95rem}
    .ok{background:#ecfdf5;color:#065f46}
    .info{background:#eff6ff;color:#1e40af}
    .error{background:#fef2f2;color:#991b1b}
    a.btn{display:inline-block;margin-top:22px;margin-right:10px;padding:12px 24px;border-radius:12px;background:#0891b2;color:#fff;text-decoration:none;font-weight:700}
    a.btn.alt{background:#1e293b}
</style>
</head>
<body>
<div class="box">
    <h1>Past Vice-Chancellors &mdash; CMS Installer</h1>
    <p class="sub">Registers <code>past-vc.php</code> with the administration pages CMS.</p>
    <?php foreach ($log as $row): ?>
        <div class="row <?php echo $row[0]; ?>"><?php echo $row[1]; ?></div>
    <?php endforeach; ?>
    <a class="btn" href="admin/manage_administration_pages.php">Open Admin Manager</a>
    <a class="btn alt" href="past-vc.php">View the Page</a>
</div>
</body>
</html>
