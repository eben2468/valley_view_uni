<?php
/**
 * Installer: Office of the Dean of Students' Life and Services
 * ------------------------------------------------------------
 * Replaces the generic placeholder body content with the profile of
 * Dr. Martha Duah, plus the richer supporting sections used on the
 * Vice-Chancellor and Pro Vice-Chancellor pages.
 *
 * Source: vvu.edu.gh/.../key-officers/profile-of-dr-martha-duah
 *
 * The hero section and the CTA section are left untouched.
 * Legacy sections are dropped once; new sections are only created when
 * missing, so re-running never overwrites admin edits.
 */
require_once 'includes/db_connect.php';

$SLUG = 'office_of_dsls';
$log  = [];
function say(&$log, $msg, $type = 'ok') { $log[] = [$type, $msg]; }

try {
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $stmt = $pdo->prepare("SELECT id FROM administration_pages WHERE page_slug = ?");
    $stmt->execute([$SLUG]);
    $page_id = $stmt->fetchColumn();
    if (!$page_id) throw new Exception("Page '$SLUG' not found in administration_pages.");
    say($log, "Target page: <b>$SLUG</b> (ID: $page_id)", 'info');

    /* ---------------------------------------------------------------
     * 1. Drop the outdated sections (fields cascade automatically)
     * --------------------------------------------------------------- */
    $legacy = ['dsls_profile', 'student_services', 'contact_section'];
    $del = $pdo->prepare("DELETE FROM administration_content WHERE page_id = ? AND section_key = ?");
    foreach ($legacy as $key) {
        $del->execute([$page_id, $key]);
        if ($del->rowCount() > 0) say($log, "Removed legacy section <b>$key</b> and all of its fields");
    }

    /* ---------------------------------------------------------------
     * 2. New sections
     * --------------------------------------------------------------- */
    $PHOTO = 'images/leadership/dr-martha-duah.jpg';

    $definitions = [

        // ---- Profile (verbatim from vvu.edu.gh / Key Officers) ----
        'officer_profile' => ['type' => 'profile', 'order' => 2, 'fields' => [
            ['profile_image',  $PHOTO, 'image'],
            ['name',           'Dr. Martha Duah', 'text'],
            ['title',          "Dean of Student Life and Service", 'text'],
            ['section_label',  'Meet the Dean', 'text'],
            ['section_title',  'Profile & Biography', 'text'],
            ['bio_paragraph_1', 'The University Council appointed Martha Duah, PhD, as Dean of Student Life and Service. Key stakeholders of the University and the West-Central Division of the Seventh-day Adventist Church confirmed her appointment for the 2020-2025 term at the session. She was reappointed and confirmed to serve in the same capacity for the current quinquennium, 2026 to 2030.', 'textarea'],
            ['bio_paragraph_2', 'Prior to this role, she served in the Theological Studies Department, teaching both undergraduate and graduate courses and serving on University committees to streamline campus services.', 'textarea'],
            ['bio_paragraph_3', 'Before joining Valley View as full-time staff, she served as a part-time teacher in Valley View University\'s theology department before moving to Andrews University to pursue her PhD program. Upon completing her program, she was appointed as a contract lecturer at Andrews University Seminary and later moved to Griggs University as an adjunct lecturer. She finally accepted a full-time lecturer position at Valley View University.', 'textarea'],
            ['bio_paragraph_4', 'Dr. Duah, as a senior lecturer and student adviser, has published numerous articles, has supervised many research projects, and has served on the Biblical Research Institute Committee of the General Conference of the Seventh-day Adventist Church. She is also a member of the Biblical Research Committee of the West-Central African Division (BRC).', 'textarea'],
            ['bio_paragraph_5', 'Dr. Duah holds a BA in Religion from Griggs University through Valley View University. She received her MA and PhD in Systematic Theology from Andrews University.', 'textarea'],
            ['highlight_1_title', 'Current Term', 'text'],
            ['highlight_1_text',  'Reappointed and confirmed for the quinquennium 2026 to 2030, having first served the 2020-2025 term.', 'textarea'],
            ['highlight_2_title', 'Scholarship & Service', 'text'],
            ['highlight_2_text',  'Senior lecturer and student adviser with numerous published articles and many supervised research projects.', 'textarea'],
        ]],

        // ---- Appointment & academic journey ----
        'career_timeline' => ['type' => 'section', 'order' => 3, 'fields' => [
            ['section_label',       'The Path Here', 'text'],
            ['section_title',       'Academic & Professional Journey', 'text'],
            ['section_description', 'From part-time teaching in the theology department to leading student life across the University.', 'textarea'],

            ['step_1_year',  'Early Service', 'text'],
            ['step_1_title', 'Part-Time Teacher, Theology Department', 'text'],
            ['step_1_text',  'Began at Valley View University as a part-time teacher in the theology department.', 'textarea'],

            ['step_2_year',  'Doctoral Study', 'text'],
            ['step_2_title', 'PhD Programme, Andrews University', 'text'],
            ['step_2_text',  'Moved to Andrews University in the United States to pursue her doctoral studies.', 'textarea'],

            ['step_3_year',  'After the PhD', 'text'],
            ['step_3_title', 'Contract Lecturer, Andrews University Seminary', 'text'],
            ['step_3_text',  'Appointed as a contract lecturer at the Andrews University Seminary on completing her programme.', 'textarea'],

            ['step_4_year',  'Adjunct Role', 'text'],
            ['step_4_title', 'Adjunct Lecturer, Griggs University', 'text'],
            ['step_4_text',  'Served as an adjunct lecturer at Griggs University before returning to Ghana.', 'textarea'],

            ['step_5_year',  'Return to VVU', 'text'],
            ['step_5_title', 'Lecturer, Theological Studies Department', 'text'],
            ['step_5_text',  'Accepted a full-time lecturer position at Valley View University, teaching undergraduate and graduate courses and serving on University committees to streamline campus services.', 'textarea'],

            ['step_6_year',  '2020 - 2025', 'text'],
            ['step_6_title', 'Dean of Student Life and Service', 'text'],
            ['step_6_text',  'Appointed by the University Council and confirmed by stakeholders of the University and the West-Central Division.', 'textarea'],

            ['step_7_year',  '2026 - 2030', 'text'],
            ['step_7_title', 'Reappointed, Current Quinquennium', 'text'],
            ['step_7_text',  'Reappointed and confirmed to serve in the same capacity for the current quinquennium.', 'textarea'],
        ]],

        // ---- Qualifications & committee service ----
        'credentials' => ['type' => 'section', 'order' => 4, 'fields' => [
            ['section_label',       'Qualifications', 'text'],
            ['section_title',       'Academic & Professional Standing', 'text'],
            ['section_description', 'A scholar of Systematic Theology serving the wider Seventh-day Adventist Church through its research committees.', 'textarea'],

            ['academic_title',   'Academic Qualifications', 'text'],
            ['academic_1_title', 'PhD, Systematic Theology', 'text'],
            ['academic_1_text',  'Andrews University', 'text'],
            ['academic_2_title', 'MA, Systematic Theology', 'text'],
            ['academic_2_text',  'Andrews University', 'text'],
            ['academic_3_title', 'BA, Religion', 'text'],
            ['academic_3_text',  'Griggs University, through Valley View University', 'text'],

            ['service_title', 'Committee & Scholarly Service', 'text'],
            ['service_1', 'Biblical Research Institute Committee, General Conference of the Seventh-day Adventist Church', 'text'],
            ['service_2', 'Member, Biblical Research Committee of the West-Central African Division (BRC)', 'text'],
            ['service_3', 'Numerous articles published in academic outlets', 'text'],
            ['service_4', 'Supervisor of many student research projects', 'text'],
            ['service_5', 'Senior lecturer and student adviser', 'text'],
        ]],

        // ---- What the office does ----
        'office_mandate' => ['type' => 'section', 'order' => 5, 'fields' => [
            ['section_label',       'The Office', 'text'],
            ['section_title',       'Mandate of the Office', 'text'],
            ['section_description', 'The Office of the Dean of Students\' Life and Services exists so that every student is supported, safe and able to thrive outside the lecture hall as well as within it.', 'textarea'],

            ['item_1_icon',  'health_and_safety', 'text'],
            ['item_1_title', 'Health & Wellness', 'text'],
            ['item_1_text',  'Health services, counselling support and wellness programmes that care for the whole student.', 'textarea'],

            ['item_2_icon',  'apartment',         'text'],
            ['item_2_title', 'Accommodation', 'text'],
            ['item_2_text',  'Safe, comfortable and affordable housing, and the residential life that goes with it.', 'textarea'],

            ['item_3_icon',  'diversity_3',       'text'],
            ['item_3_title', 'Student Activities', 'text'],
            ['item_3_text',  'Clubs, associations and events that build leadership, friendship and a sense of belonging.', 'textarea'],

            ['item_4_icon',  'support_agent',     'text'],
            ['item_4_title', 'Welfare & Advocacy', 'text'],
            ['item_4_text',  'A first point of call for student concerns, with advocacy and referral where it is needed.', 'textarea'],

            ['item_5_icon',  'gavel',             'text'],
            ['item_5_title', 'Discipline & Conduct', 'text'],
            ['item_5_text',  'Fair and consistent administration of the student code of conduct and University regulations.', 'textarea'],

            ['item_6_icon',  'school',            'text'],
            ['item_6_title', 'Orientation & Advising', 'text'],
            ['item_6_text',  'Welcoming new students and guiding them through university life from first day to graduation.', 'textarea'],
        ]],

        // ---- Related pages ----
        'related_offices' => ['type' => 'section', 'order' => 6, 'fields' => [
            ['section_label', 'Explore Further', 'text'],
            ['section_title', 'Student Services & Resources', 'text'],

            ['link_1_icon',  'bed',              'text'],
            ['link_1_title', 'Accommodation', 'text'],
            ['link_1_text',  'Halls of residence, room options and application details.', 'textarea'],
            ['link_1_url',   'accommodation.php', 'text'],

            ['link_2_icon',  'restaurant',       'text'],
            ['link_2_title', 'Food Services', 'text'],
            ['link_2_text',  'Cafeteria, meal plans and dining across the campus.', 'textarea'],
            ['link_2_url',   'food_services.php', 'text'],

            ['link_3_icon',  'groups',           'text'],
            ['link_3_title', 'Activities & Clubs', 'text'],
            ['link_3_text',  'Student associations, clubs and campus organisations.', 'textarea'],
            ['link_3_url',   'activities_and_clubs.php', 'text'],

            ['link_4_icon',  'self_improvement', 'text'],
            ['link_4_title', 'Office of the Dean of Spiritual Life', 'text'],
            ['link_4_text',  'Chaplaincy, worship and the spiritual life of the campus.', 'textarea'],
            ['link_4_url',   'office_of_sls.php', 'text'],
        ]],

        // ---- Contact ----
        'office_contact' => ['type' => 'section', 'order' => 7, 'fields' => [
            ['section_label',       'Get in Touch', 'text'],
            ['section_title',       'Contact the Office', 'text'],
            ['section_description', 'For student support, accommodation enquiries, welfare concerns or anything else about life on campus, the team is here to help.', 'textarea'],
            ['email',               'dsls@vvu.edu.gh', 'text'],
            ['phone',               '+233 (0) 302 501 101', 'text'],
            ['office_location',     'Student Services Building, Main Campus, Oyibi', 'text'],
            ['postal_address',      'P. O. Box AF 595, Adentan, Accra, Ghana', 'text'],
            ['office_hours',        'Monday - Thursday, 8:00am - 5:00pm | Friday, 8:00am - 12:00pm', 'text'],
            ['map_url',             'https://maps.google.com/?q=Valley+View+University+Oyibi', 'text'],
            ['form_title',          'Student Support Request', 'text'],
            ['form_description',    'Tell us how we can help. Share your question, concern or request and the office will respond within three working days.', 'textarea'],
            ['form_btn_text',       'Submit Request', 'text'],
        ]],
    ];

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

    // Keep the CTA last in the admin listing
    $pdo->prepare("UPDATE administration_content SET content_order = 100 WHERE page_id = ? AND section_key = 'cta_section'")
        ->execute([$page_id]);

    say($log, "Sections created: <b>$newSections</b> &nbsp;|&nbsp; Fields created: <b>$newFields</b>");
    say($log, 'Content refresh complete.');

} catch (Exception $e) {
    say($log, 'Error: ' . htmlspecialchars($e->getMessage()), 'error');
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Install - Dean of Students' Life &amp; Services Content</title>
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
    <h1>Dean of Students&rsquo; Life &amp; Services &mdash; Content Refresh</h1>
    <p class="sub">Installs the profile of Dr. Martha Duah and the supporting sections.</p>
    <?php foreach ($log as $row): ?>
        <div class="row <?php echo $row[0]; ?>"><?php echo $row[1]; ?></div>
    <?php endforeach; ?>
    <a class="btn" href="admin/manage_administration_pages.php">Open Admin Manager</a>
    <a class="btn alt" href="office_of_dsls.php">View the Page</a>
</div>
</body>
</html>
