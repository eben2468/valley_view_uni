<?php
/**
 * Installer: Office of the Vice-Chancellor - content refresh
 * ----------------------------------------------------------
 * Removes the legacy "Strategic Vision" and "Contact the Office" sections and
 * installs the new, richer set of sections (Vice-Chancellor's Message,
 * Strategic Priorities, Mandate of the Office, Related Offices, Contact).
 *
 * The hero section, the VC profile and the CTA section are left untouched.
 * Safe to re-run: legacy sections are only removed once and the new sections
 * are created only when missing, so admin edits are never overwritten.
 */
require_once 'includes/db_connect.php';

$SLUG = 'office_of_the_vice_chancellor';
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
    $legacy = ['strategic_vision', 'contact_section'];
    $del = $pdo->prepare("DELETE FROM administration_content WHERE page_id = ? AND section_key = ?");
    foreach ($legacy as $key) {
        $del->execute([$page_id, $key]);
        if ($del->rowCount() > 0) say($log, "Removed legacy section <b>$key</b> and all of its fields");
    }

    /* ---------------------------------------------------------------
     * 2. New / refreshed sections
     * --------------------------------------------------------------- */
    $GANU = 'images/leadership/prof-daniel-ganu.jpg';

    $definitions = [

        // ---- The Vice-Chancellor's Message (vvu.edu.gh) ----
        'vc_message' => ['type' => 'section', 'order' => 3, 'fields' => [
            ['section_label',    'From the Vice-Chancellor', 'text'],
            ['section_title',    "The Vice-Chancellor's Message", 'text'],
            ['greeting',         'Welcome to Valley View University', 'text'],
            ['paragraph_1',      "Welcome to Valley View University (VVU), Ghana's premier chartered private university! Valley View University is a Seventh-day Adventist higher education institution committed to wholistic education, which emphasizes the integrated development of the intellectual, physical, social, and spiritual dimensions of learners.", 'textarea'],
            ['paragraph_2',      'It is with great honor and a deep sense of responsibility that I assume the role of Vice-Chancellor of this esteemed institution. I am delighted to join a vibrant academic community known for its commitment to excellence in teaching, research, and service.', 'textarea'],
            ['paragraph_3',      'As we look to the future, my vision is to strengthen our academic foundation, foster innovation, and nurture an inclusive environment where students, faculty, and staff can thrive. Together, we will build on our proud legacy while embracing new opportunities that prepare our graduates to lead and serve in a rapidly changing world.', 'textarea'],
            ['paragraph_4',      'I therefore extend a warm welcome to our students, prospective students, faculty, staff, partners, and visitors to VVU, where the pursuit of knowledge is inseparably linked with faith, service, and character development.', 'textarea'],
            ['paragraph_5',      "I look forward to working collaboratively with all stakeholders as we advance our shared mission and uphold the values that define VVU. I invite you to consider VVU your institution of choice for both undergraduate and graduate education. Explore our website to learn how you can benefit from VVU's academic programs.", 'textarea'],
            ['pull_quote',       'The pursuit of knowledge is inseparably linked with faith, service, and character development.', 'textarea'],
            ['signature_name',   'Professor Daniel Ganu', 'text'],
            ['signature_title',  'Vice-Chancellor', 'text'],
            ['signature_image',  $GANU, 'image'],
        ]],

        // ---- Strategic priorities drawn from the message ----
        'vision_pillars' => ['type' => 'section', 'order' => 4, 'fields' => [
            ['section_label',       'Looking Ahead', 'text'],
            ['section_title',       'Strategic Priorities', 'text'],
            ['section_description', "The Vice-Chancellor's vision for Valley View University rests on four commitments that shape every decision of the office.", 'textarea'],

            ['pillar_1_icon',        'foundation', 'text'],
            ['pillar_1_title',       'A Stronger Academic Foundation', 'text'],
            ['pillar_1_description', 'Deepening the quality of teaching, learning and scholarship so that every programme meets the highest national and international standards.', 'textarea'],

            ['pillar_2_icon',        'lightbulb', 'text'],
            ['pillar_2_title',       'A Culture of Innovation', 'text'],
            ['pillar_2_description', 'Encouraging research, enterprise and creative problem solving that responds to the real needs of Ghana and the wider world.', 'textarea'],

            ['pillar_3_icon',        'diversity_3', 'text'],
            ['pillar_3_title',       'An Inclusive Community', 'text'],
            ['pillar_3_description'  , 'Nurturing an environment where students, faculty and staff of every background are supported, respected and able to thrive.', 'textarea'],

            ['pillar_4_icon',        'volunteer_activism', 'text'],
            ['pillar_4_title',       'Faith, Service and Character', 'text'],
            ['pillar_4_description', 'Holding fast to the wholistic Adventist philosophy of education that develops the intellect, the body, the social self and the spirit together.', 'textarea'],
        ]],

        // ---- What the office actually does ----
        'office_mandate' => ['type' => 'section', 'order' => 5, 'fields' => [
            ['section_label',       'The Office', 'text'],
            ['section_title',       'Mandate of the Office', 'text'],
            ['section_description', 'As the chief executive officer of the University, the Vice-Chancellor carries responsibility for the academic, administrative and financial life of the institution.', 'textarea'],

            ['item_1_icon',  'account_balance', 'text'],
            ['item_1_title', 'Executive Leadership', 'text'],
            ['item_1_text',  'Provides overall direction for the University and represents it before the Governing Council, regulators and the public.', 'textarea'],

            ['item_2_icon',  'school', 'text'],
            ['item_2_title', 'Academic Oversight', 'text'],
            ['item_2_text',  'Safeguards the quality of programmes, accreditation standards and the integrity of teaching, learning and assessment.', 'textarea'],

            ['item_3_icon',  'trending_up', 'text'],
            ['item_3_title', 'Strategy & Growth', 'text'],
            ['item_3_text',  'Drives implementation of the strategic plan, institutional development and prudent stewardship of University resources.', 'textarea'],

            ['item_4_icon',  'handshake', 'text'],
            ['item_4_title', 'Partnerships', 'text'],
            ['item_4_text',  'Builds relationships with industry, government, alumni, the Church and international institutions of higher learning.', 'textarea'],

            ['item_5_icon',  'groups', 'text'],
            ['item_5_title', 'Staff & Student Welfare', 'text'],
            ['item_5_text',  'Champions the wellbeing, development and fair treatment of every member of the University community.', 'textarea'],

            ['item_6_icon',  'church', 'text'],
            ['item_6_title', 'Mission & Values', 'text'],
            ['item_6_text',  'Keeps the University anchored in the Seventh-day Adventist philosophy of wholistic, Christ-centred education.', 'textarea'],
        ]],

        // ---- Related offices / useful links ----
        'related_offices' => ['type' => 'section', 'order' => 6, 'fields' => [
            ['section_label', 'Explore Further', 'text'],
            ['section_title', 'Related Offices & Resources', 'text'],

            ['link_1_icon',  'co_present',   'text'],
            ['link_1_title', 'Office of the Pro Vice-Chancellor', 'text'],
            ['link_1_text',  'Academic leadership, quality assurance and digital transformation.', 'textarea'],
            ['link_1_url',   'office_of_the_pro-vice_chancellor.php', 'text'],

            ['link_2_icon',  'badge',        'text'],
            ['link_2_title', 'Office of the Registrar', 'text'],
            ['link_2_text',  'Admissions, records, examinations and general administration.', 'textarea'],
            ['link_2_url',   'office_of_the_registrar.php', 'text'],

            ['link_3_icon',  'history_edu',  'text'],
            ['link_3_title', 'Past Vice-Chancellors', 'text'],
            ['link_3_text',  'The leaders who have shaped Valley View University since 1979.', 'textarea'],
            ['link_3_url',   'past-vc.php', 'text'],

            ['link_4_icon',  'map',          'text'],
            ['link_4_title', 'Strategic Plan', 'text'],
            ['link_4_text',  'The roadmap guiding the next phase of the University\'s growth.', 'textarea'],
            ['link_4_url',   'strategic_plan.php', 'text'],
        ]],

        // ---- Rebuilt contact block (replaces legacy contact_section) ----
        'office_contact' => ['type' => 'section', 'order' => 7, 'fields' => [
            ['section_label',     'Get in Touch', 'text'],
            ['section_title',     'Contact the Office', 'text'],
            ['section_description', 'For official inquiries, invitations, scheduling or administrative matters, the team in the Vice-Chancellor\'s office is glad to assist.', 'textarea'],
            ['email',             'vc@vvu.edu.gh', 'text'],
            ['phone',             '+233 (0) 302 501 101', 'text'],
            ['office_location',   'Administration Block, Main Campus, Oyibi', 'text'],
            ['postal_address',    'P. O. Box AF 595, Adentan, Accra, Ghana', 'text'],
            ['office_hours',      'Monday - Thursday, 8:00am - 5:00pm | Friday, 8:00am - 12:00pm', 'text'],
            ['map_url',           'https://maps.google.com/?q=Valley+View+University+Oyibi', 'text'],
            ['form_title',        'Request an Appointment', 'text'],
            ['form_description',  'To request a meeting with the Vice-Chancellor, share your details and the purpose of your visit. The office will respond within three working days.', 'textarea'],
            ['form_btn_text',     'Submit Request', 'text'],
        ]],
    ];

    /* ---------------------------------------------------------------
     * 3. Fields the front-end used to hard-code - now editable
     * --------------------------------------------------------------- */
    $extra_profile_fields = [
        ['section_title',    'Profile & Biography', 'text'],
        ['experience_title', 'Experience', 'text'],
        ['impact_title',     'Global Impact', 'text'],
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

    // Add the missing vc_profile labels
    $findSection->execute([$page_id, 'vc_profile']);
    if ($profile_id = $findSection->fetchColumn()) {
        foreach ($extra_profile_fields as $f) {
            list($fkey, $fval, $ftype) = $f;
            $findField->execute([$profile_id, $fkey]);
            if (!$findField->fetchColumn()) {
                $addField->execute([$profile_id, $fkey, $fval, $ftype]);
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
<title>Install - Vice-Chancellor's Office Content</title>
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
    <h1>Vice-Chancellor&rsquo;s Office &mdash; Content Refresh</h1>
    <p class="sub">Replaces the outdated Strategic Vision &amp; Contact sections with the new message-led content.</p>
    <?php foreach ($log as $row): ?>
        <div class="row <?php echo $row[0]; ?>"><?php echo $row[1]; ?></div>
    <?php endforeach; ?>
    <a class="btn" href="admin/manage_administration_pages.php">Open Admin Manager</a>
    <a class="btn alt" href="office_of_the_vice_chancellor.php">View the Page</a>
</div>
</body>
</html>
