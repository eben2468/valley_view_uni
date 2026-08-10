<?php
/**
 * Installer: Strategic Plan page refresh
 * --------------------------------------
 * 1. Extends strategic_plan_president_message so the Vice Chancellor's full
 *    message (five paragraphs + author title) can be stored and edited.
 * 2. Creates strategic_plan_section_headings so the three section headings
 *    that were hard-coded in strategic_plan.php become editable.
 * 3. Replaces the outdated Vision-2025 era content with the current
 *    Vice Chancellor's message and vision.
 *
 * Content source for the message and the pillars:
 *   vvu.edu.gh/.../vice-chancellor-s-office/vice-chancellor-s-message
 *
 * The content replacement runs ONCE. On a second run the schema is verified
 * but existing rows are left alone, so admin edits are never clobbered.
 */
require_once 'includes/db_connect.php';

$log = [];
function say(&$log, $msg, $type = 'ok') { $log[] = [$type, $msg]; }

try {
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    /* ---------------------------------------------------------------
     * 1. Schema: extra columns for the full message
     * --------------------------------------------------------------- */
    $existing = $pdo->query("SHOW COLUMNS FROM strategic_plan_president_message")->fetchAll(PDO::FETCH_COLUMN);
    $wanted = [
        'author_title'        => "VARCHAR(255) NULL",
        'message_paragraph_1' => 'TEXT NULL',
        'message_paragraph_2' => 'TEXT NULL',
        'message_paragraph_3' => 'TEXT NULL',
        'message_paragraph_4' => 'TEXT NULL',
        'message_paragraph_5' => 'TEXT NULL',
    ];
    $added = 0;
    foreach ($wanted as $col => $def) {
        if (!in_array($col, $existing, true)) {
            $pdo->exec("ALTER TABLE strategic_plan_president_message ADD COLUMN `$col` $def");
            $added++;
        }
    }
    say($log, $added > 0 ? "Added <b>$added</b> column(s) to strategic_plan_president_message" : 'Message columns already present', $added > 0 ? 'ok' : 'info');

    /* ---------------------------------------------------------------
     * 2. Schema: editable section headings
     * --------------------------------------------------------------- */
    $pdo->exec("CREATE TABLE IF NOT EXISTS `strategic_plan_section_headings` (
        `id` INT(11) NOT NULL AUTO_INCREMENT,
        `section_key` VARCHAR(50) NOT NULL UNIQUE,
        `heading` VARCHAR(255) NOT NULL,
        `subheading` TEXT NULL,
        `display_order` INT(11) DEFAULT 0,
        `is_active` TINYINT(1) DEFAULT 1,
        `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        PRIMARY KEY (`id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");

    $headings = [
        ['pillars',  'Our Strategic Pillars', 'The commitments that shape every decision of the University as we build on our proud legacy.', 1],
        ['timeline', 'How We Get There',      'The path from a stronger academic foundation to graduates equipped to lead and serve in a rapidly changing world.', 2],
        ['stats',    'Our Foundation',        '', 3],
    ];
    $findHeading = $pdo->prepare("SELECT id FROM strategic_plan_section_headings WHERE section_key = ?");
    $addHeading  = $pdo->prepare("INSERT INTO strategic_plan_section_headings (section_key, heading, subheading, display_order) VALUES (?, ?, ?, ?)");
    $newHeadings = 0;
    foreach ($headings as $h) {
        $findHeading->execute([$h[0]]);
        if (!$findHeading->fetchColumn()) { $addHeading->execute($h); $newHeadings++; }
    }
    say($log, $newHeadings > 0 ? "Created <b>$newHeadings</b> editable section heading(s)" : 'Section headings already present', $newHeadings > 0 ? 'ok' : 'info');

    /* ---------------------------------------------------------------
     * 3. Content replacement - runs once only
     * --------------------------------------------------------------- */
    $already = $pdo->query("SELECT message_paragraph_1 FROM strategic_plan_president_message ORDER BY id DESC LIMIT 1")->fetchColumn();
    if (!empty(trim((string) $already))) {
        say($log, 'Content already refreshed on an earlier run - existing rows left untouched.', 'info');
    } else {

        $VC_PHOTO = 'images/leadership/prof-daniel-ganu.jpg';

        // -- Vice Chancellor's message --------------------------------
        $pdo->prepare("UPDATE strategic_plan_president_message SET
                section_title = ?, president_image_url = ?, message_quote = ?, message_author = ?,
                author_title = ?, message_paragraph_1 = ?, message_paragraph_2 = ?, message_paragraph_3 = ?,
                message_paragraph_4 = ?, message_paragraph_5 = ?
            WHERE id = (SELECT id FROM (SELECT id FROM strategic_plan_president_message ORDER BY id DESC LIMIT 1) t)")
            ->execute([
                "A Message From The Vice Chancellor",
                $VC_PHOTO,
                'The pursuit of knowledge is inseparably linked with faith, service, and character development.',
                'Professor Daniel Ganu, PhD',
                'Vice Chancellor',
                "Welcome to Valley View University (VVU), Ghana's premier chartered private university! Valley View University is a Seventh-day Adventist higher education institution committed to wholistic education, which emphasizes the integrated development of the intellectual, physical, social, and spiritual dimensions of learners.",
                'It is with great honor and a deep sense of responsibility that I assume the role of Vice Chancellor of this esteemed institution. I am delighted to join a vibrant academic community known for its commitment to excellence in teaching, research, and service.',
                'As we look to the future, my vision is to strengthen our academic foundation, foster innovation, and nurture an inclusive environment where students, faculty, and staff can thrive. Together, we will build on our proud legacy while embracing new opportunities that prepare our graduates to lead and serve in a rapidly changing world.',
                'I therefore extend a warm welcome to our students, prospective students, faculty, staff, partners, and visitors to VVU, where the pursuit of knowledge is inseparably linked with faith, service, and character development.',
                "I look forward to working collaboratively with all stakeholders as we advance our shared mission and uphold the values that define VVU. I invite you to consider VVU your institution of choice for both undergraduate and graduate education.",
            ]);
        say($log, "Updated the Vice Chancellor's message (Professor Daniel Ganu)");

        // -- Hero ------------------------------------------------------
        $pdo->prepare("UPDATE strategic_plan_hero SET page_subtitle = ?, hero_title_1 = ?, hero_title_2 = ?, hero_description = ?, download_button_text = ?
                       WHERE id = (SELECT id FROM (SELECT id FROM strategic_plan_hero ORDER BY id DESC LIMIT 1) t)")
            ->execute([
                'Our Strategic Direction',
                'Strategic Plan',
                'One VVU, One Mission',
                'To strengthen our academic foundation, foster innovation, and nurture an inclusive environment where students, faculty, and staff can thrive.',
                'Download the Strategic Plan (PDF)',
            ]);
        say($log, 'Updated the hero text (removed the dated "Vision 2025" wording)');

        // -- Strategic pillars, from the Vice Chancellor's vision ------
        $pdo->exec("DELETE FROM strategic_plan_pillars");
        $ins = $pdo->prepare("INSERT INTO strategic_plan_pillars (icon, title, description, feature_1, feature_2, border_color, display_order, is_active) VALUES (?,?,?,?,?,?,?,1)");
        $pillars = [
            ['foundation', 'A Stronger Academic Foundation', 'Deepening the quality of teaching, learning and scholarship so that every programme meets the highest national and international standards.', 'Quality Teaching & Learning', 'Accredited Programmes', 'blue-600', 1],
            ['lightbulb', 'A Culture of Innovation', 'Encouraging research, enterprise and creative problem solving that responds to the real needs of Ghana and the wider world.', 'Research & Enterprise', 'Creative Problem Solving', 'yellow-500', 2],
            ['diversity_3', 'An Inclusive Community', 'Nurturing an environment where students, faculty and staff of every background are supported, respected and able to thrive.', 'Student & Staff Wellbeing', 'A Place to Thrive', 'green-600', 3],
            ['volunteer_activism', 'Faith, Service and Character', 'Holding fast to the wholistic Adventist philosophy of education that develops the intellect, the body, the social self and the spirit together.', 'Wholistic Education', 'Service-Minded Graduates', 'purple-600', 4],
        ];
        foreach ($pillars as $p) $ins->execute($p);
        say($log, 'Replaced the strategic pillars with the four commitments from the Vice Chancellor\'s vision');

        // -- Timeline --------------------------------------------------
        $pdo->exec("DELETE FROM strategic_plan_timeline");
        $ins = $pdo->prepare("INSERT INTO strategic_plan_timeline (phase_number, phase_badge, phase_title, phase_description, border_color, dot_color, display_order, is_active) VALUES (?,?,?,?,?,?,?,1)");
        $phases = [
            [1, 'Phase One', 'Strengthen the Foundation', 'Consolidate the quality of teaching, learning and scholarship across every school and campus of the University.', 'blue-600', 'blue-600', 1],
            [2, 'Phase Two', 'Foster Innovation', 'Grow research, enterprise and creative problem solving that addresses the real needs of Ghana and the wider world.', 'yellow-500', 'yellow-500', 2],
            [3, 'Phase Three', 'Build on the Legacy', 'Embrace new opportunities that prepare our graduates to lead and serve in a rapidly changing world.', 'green-600', 'green-600', 3],
        ];
        foreach ($phases as $p) $ins->execute($p);
        say($log, 'Replaced the implementation timeline');

        // -- Stats: verifiable institutional milestones ----------------
        $pdo->exec("DELETE FROM strategic_plan_stats");
        $ins = $pdo->prepare("INSERT INTO strategic_plan_stats (stat_value, stat_label, display_order, is_active) VALUES (?,?,?,1)");
        $stats = [
            ['1979', 'Founded as Bekwai Seminary', 1],
            ['1997', 'First Accredited Private University in Ghana', 2],
            ['2006', 'First Presidential Charter in Ghana', 3],
            ['3', 'Campuses: Oyibi, Kumasi & Techiman', 4],
        ];
        foreach ($stats as $s) $ins->execute($s);
        say($log, 'Replaced the unsourced percentage figures with verifiable institutional milestones');

        // -- CTA -------------------------------------------------------
        $pdo->prepare("UPDATE strategic_plan_cta SET cta_title_1 = ?, cta_title_2 = ?, cta_description = ?, button_1_text = ?
                       WHERE id = (SELECT id FROM (SELECT id FROM strategic_plan_cta ORDER BY id DESC LIMIT 1) t)")
            ->execute([
                'Advance Our Shared Mission,',
                'Build the Future With Us',
                'We look forward to working collaboratively with all stakeholders as we advance our shared mission and uphold the values that define VVU.',
                'Download the Strategic Plan (PDF)',
            ]);
        say($log, 'Updated the closing call to action');
    }

    say($log, 'Strategic Plan refresh complete.');

} catch (Exception $e) {
    say($log, 'Error: ' . htmlspecialchars($e->getMessage()), 'error');
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Install - Strategic Plan Refresh</title>
<style>
    body{font-family:system-ui,-apple-system,'Segoe UI',sans-serif;background:#f1f5f9;margin:0;padding:40px 20px;color:#1e293b}
    .box{max-width:780px;margin:0 auto;background:#fff;border-radius:20px;padding:40px;box-shadow:0 10px 40px rgba(0,0,0,.06)}
    h1{margin:0 0 6px;font-size:1.7rem}
    p.sub{margin:0 0 28px;color:#64748b}
    .row{padding:12px 18px;border-radius:12px;margin-bottom:10px;font-size:.95rem;line-height:1.6}
    .ok{background:#ecfdf5;color:#065f46}
    .info{background:#eff6ff;color:#1e40af}
    .warn{background:#fffbeb;color:#92400e}
    .error{background:#fef2f2;color:#991b1b}
    a.btn{display:inline-block;margin-top:22px;margin-right:10px;padding:12px 24px;border-radius:12px;background:#0891b2;color:#fff;text-decoration:none;font-weight:700}
    a.btn.alt{background:#1e293b}
</style>
</head>
<body>
<div class="box">
    <h1>Strategic Plan &mdash; Content Refresh</h1>
    <p class="sub">Installs the Vice Chancellor's message and replaces the Vision-2025 era content.</p>
    <?php foreach ($log as $row): ?>
        <div class="row <?php echo $row[0]; ?>"><?php echo $row[1]; ?></div>
    <?php endforeach; ?>
    <div class="row warn">
        <b>Still to do by hand:</b> the download button points at <code>uploads/VISION 2025.pdf</code>, which is the old plan document. Upload the current plan and update the two URL fields in the admin page.
    </div>
    <a class="btn" href="admin/manage_strategy_history.php">Open Admin Manager</a>
    <a class="btn alt" href="strategic_plan.php">View the Page</a>
</div>
</body>
</html>
