<?php
/**
 * Installer: Office of the Pro Vice-Chancellor - content refresh
 * --------------------------------------------------------------
 * Replaces the outdated body content (which still described a previous
 * office holder) with the profile of Prof. Peter Agyekum Boateng, plus the
 * richer supporting sections used on the Vice-Chancellor's page.
 *
 * The hero section is left completely untouched.
 * Legacy sections are dropped once; new sections are only created when
 * missing, so re-running never overwrites admin edits.
 */
require_once 'includes/db_connect.php';

$SLUG = 'office_of_the_pro-vice_chancellor';
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
    $legacy = ['provc_profile', 'career_leadership', 'research_contributions', 'contact_section'];
    $del = $pdo->prepare("DELETE FROM administration_content WHERE page_id = ? AND section_key = ?");
    foreach ($legacy as $key) {
        $del->execute([$page_id, $key]);
        if ($del->rowCount() > 0) say($log, "Removed legacy section <b>$key</b> and all of its fields");
    }

    /* ---------------------------------------------------------------
     * 2. New sections
     * --------------------------------------------------------------- */
    $PHOTO = 'images/leadership/prof-peter-agyekum-boateng.jpg';

    $definitions = [

        // ---- Profile (vvu.edu.gh / Key Officers) ----
        'pvc_profile' => ['type' => 'profile', 'order' => 2, 'fields' => [
            ['profile_image',  $PHOTO, 'image'],
            ['name',           'Prof. Peter Agyekum Boateng, PhD', 'text'],
            ['title',          'Pro Vice-Chancellor', 'text'],
            ['section_label',  'Meet the Pro Vice-Chancellor', 'text'],
            ['section_title',  'Profile & Biography', 'text'],
            ['bio_paragraph_1', 'Prof. Peter Agyekum Boateng is an Associate Professor of Business Administration (Strategic Management) at Valley View University, Ghana. He is an experienced academic leader, researcher, and administrator with over two decades of service in higher education. His work spans teaching, research, governance, and institutional leadership, with a strong focus on connecting theory with practice in both faith-based and secular settings.', 'textarea'],
            ['bio_paragraph_2', 'Since 2006, Prof. Boateng has held several senior leadership positions at Valley View University. These include Acting Vice-Chancellor, Pro Vice-Chancellor, Rector of both the Kumasi and Techiman campuses, and Dean of the School of Graduate Studies. These roles have provided extensive experience in university governance, strategic planning, and organizational development. He has also contributed to national education reform initiatives in Ghana through consultancy, coaching, and facilitation under programmes supported by the Ghana Education Service.', 'textarea'],
            ['bio_paragraph_3', 'Prof. Boateng has taught across undergraduate, graduate, and doctoral levels, engaging students in rigorous and relevant learning. Alongside his academic career, he is an ordained minister of the Seventh-day Adventist Church and remains committed to mentoring future leaders who combine professional competence with service-oriented leadership.', 'textarea'],
            ['highlight_1_title', 'Academic Rank', 'text'],
            ['highlight_1_text',  'Associate Professor of Business Administration (Strategic Management), Valley View University.', 'textarea'],
            ['highlight_2_title', 'Service in Higher Education', 'text'],
            ['highlight_2_text',  'Over two decades spanning teaching, research, governance and institutional leadership.', 'textarea'],
        ]],

        // ---- Senior leadership roles held ----
        'leadership_roles' => ['type' => 'section', 'order' => 3, 'fields' => [
            ['section_label',       'Since 2006', 'text'],
            ['section_title',       'Leadership & Service', 'text'],
            ['section_description', 'Senior positions held at Valley View University and beyond, each contributing to a deep grounding in university governance, strategic planning and organizational development.', 'textarea'],

            ['role_1_icon',  'account_balance',   'text'],
            ['role_1_title', 'Acting Vice-Chancellor', 'text'],
            ['role_1_text',  'Provided executive leadership for the University, representing it before its Council, regulators and partners.', 'textarea'],

            ['role_2_icon',  'co_present',        'text'],
            ['role_2_title', 'Pro Vice-Chancellor', 'text'],
            ['role_2_text',  'Leads the academic division of the University, overseeing quality, planning and academic policy.', 'textarea'],

            ['role_3_icon',  'apartment',         'text'],
            ['role_3_title', 'Rector, Kumasi Campus', 'text'],
            ['role_3_text',  'Chief academic and administrative officer of the Kumasi Campus.', 'textarea'],

            ['role_4_icon',  'domain',            'text'],
            ['role_4_title', 'Rector, Techiman Campus', 'text'],
            ['role_4_text',  'Chief academic and administrative officer of the Techiman Campus.', 'textarea'],

            ['role_5_icon',  'school',            'text'],
            ['role_5_title', 'Dean, School of Graduate Studies', 'text'],
            ['role_5_text',  'Directed postgraduate programmes, supervision and graduate research culture.', 'textarea'],

            ['role_6_icon',  'diversity_3',       'text'],
            ['role_6_title', 'National Education Reform', 'text'],
            ['role_6_text',  'Consultancy, coaching and facilitation under programmes supported by the Ghana Education Service.', 'textarea'],
        ]],

        // ---- Research interests ----
        'research_focus' => ['type' => 'section', 'order' => 4, 'fields' => [
            ['section_label',       'Scholarship', 'text'],
            ['section_title',       'Research Interests', 'text'],
            ['section_description', 'His research connects theory with practice across both faith-based and secular organizations.', 'textarea'],

            ['focus_1_icon',  'strategy',        'text'],
            ['focus_1_title', 'Strategic Management', 'text'],
            ['focus_2_icon',  'gavel',           'text'],
            ['focus_2_title', 'Corporate Governance', 'text'],
            ['focus_3_icon',  'balance',         'text'],
            ['focus_3_title', 'Ethics', 'text'],
            ['focus_4_icon',  'workspace_premium', 'text'],
            ['focus_4_title', 'Leadership', 'text'],
            ['focus_5_icon',  'trending_up',     'text'],
            ['focus_5_title', 'Organizational Performance', 'text'],

            ['teaching_title', 'Teaching Across Every Level', 'text'],
            ['teaching_text',  'Prof. Boateng has taught across undergraduate, graduate and doctoral levels, engaging students in rigorous and relevant learning.', 'textarea'],
            ['ministry_title', 'Ordained Minister', 'text'],
            ['ministry_text',  'An ordained minister of the Seventh-day Adventist Church, committed to mentoring future leaders who combine professional competence with service-oriented leadership.', 'textarea'],
        ]],

        // ---- What the office does ----
        'office_mandate' => ['type' => 'section', 'order' => 5, 'fields' => [
            ['section_label',       'The Office', 'text'],
            ['section_title',       'Mandate of the Office', 'text'],
            ['section_description', 'The Pro Vice-Chancellor is the principal academic officer of the University, deputising for the Vice-Chancellor and leading the academic enterprise.', 'textarea'],

            ['item_1_icon',  'menu_book',   'text'],
            ['item_1_title', 'Academic Leadership', 'text'],
            ['item_1_text',  'Provides direction for teaching, learning and the academic policy of the University.', 'textarea'],

            ['item_2_icon',  'verified',    'text'],
            ['item_2_title', 'Quality Assurance', 'text'],
            ['item_2_text',  'Safeguards programme standards, accreditation requirements and the integrity of assessment.', 'textarea'],

            ['item_3_icon',  'biotech',     'text'],
            ['item_3_title', 'Research & Graduate Studies', 'text'],
            ['item_3_text',  'Promotes research output, postgraduate supervision and scholarly collaboration.', 'textarea'],

            ['item_4_icon',  'groups',      'text'],
            ['item_4_title', 'Faculty Development', 'text'],
            ['item_4_text',  'Supports the recruitment, growth and professional advancement of academic staff.', 'textarea'],

            ['item_5_icon',  'event_note',  'text'],
            ['item_5_title', 'Academic Planning', 'text'],
            ['item_5_text',  'Coordinates the academic calendar, curriculum review and new programme development.', 'textarea'],

            ['item_6_icon',  'handshake',   'text'],
            ['item_6_title', 'Academic Partnerships', 'text'],
            ['item_6_text',  'Builds collaboration with peer institutions, industry and professional bodies.', 'textarea'],
        ]],

        // ---- Related offices ----
        'related_offices' => ['type' => 'section', 'order' => 6, 'fields' => [
            ['section_label', 'Explore Further', 'text'],
            ['section_title', 'Related Offices & Resources', 'text'],

            ['link_1_icon',  'account_balance', 'text'],
            ['link_1_title', 'Office of the Vice-Chancellor', 'text'],
            ['link_1_text',  'Executive leadership and the strategic direction of the University.', 'textarea'],
            ['link_1_url',   'office_of_the_vice_chancellor.php', 'text'],

            ['link_2_icon',  'badge',           'text'],
            ['link_2_title', 'Office of the Registrar', 'text'],
            ['link_2_text',  'Admissions, records, examinations and general administration.', 'textarea'],
            ['link_2_url',   'office_of_the_registrar.php', 'text'],

            ['link_3_icon',  'apartment',       'text'],
            ['link_3_title', 'Campus Rectors', 'text'],
            ['link_3_text',  'Academic leadership at the Kumasi and Techiman campuses.', 'textarea'],
            ['link_3_url',   'rectors.php', 'text'],

            ['link_4_icon',  'biotech',         'text'],
            ['link_4_title', 'Research, Development & International Relations', 'text'],
            ['link_4_text',  'Research support, development projects and global partnerships.', 'textarea'],
            ['link_4_url',   'office_of_rdir.php', 'text'],
        ]],

        // ---- Contact ----
        'office_contact' => ['type' => 'section', 'order' => 7, 'fields' => [
            ['section_label',       'Get in Touch', 'text'],
            ['section_title',       'Contact the Office', 'text'],
            ['section_description', 'For academic inquiries, programme matters or administrative correspondence, the Pro Vice-Chancellor\'s office is glad to assist.', 'textarea'],
            ['email',               'pro.vc@vvu.edu.gh', 'text'],
            ['phone',               '+233 (0) 302 501 101', 'text'],
            ['office_location',     'Registry Building, Main Campus, Oyibi', 'text'],
            ['postal_address',      'P. O. Box AF 595, Adentan, Accra, Ghana', 'text'],
            ['office_hours',        'Monday - Thursday, 8:00am - 5:00pm | Friday, 8:00am - 12:00pm', 'text'],
            ['map_url',             'https://maps.google.com/?q=Valley+View+University+Oyibi', 'text'],
            ['form_title',          'Academic Enquiry', 'text'],
            ['form_description',    'Send an academic enquiry to the office. You can expect a response within three working days.', 'textarea'],
            ['form_btn_text',       'Send Enquiry', 'text'],
        ]],
    ];

    /* ---------------------------------------------------------------
     * 3. CTA fields the front-end expected but the database never had
     * --------------------------------------------------------------- */
    $cta_fields = [
        ['cta_title',       'Join Our Academic', 'text'],
        ['cta_highlight',   'Community Today', 'text'],
        ['cta_description', 'Experience a transformative education grounded in Christian values and academic excellence.', 'textarea'],
        ['button_1_text',   'Apply Now', 'text'],
        ['button_1_url',    'https://admissions.vvu.edu.gh', 'text'],
        ['button_2_text',   'Request Info', 'text'],
        ['button_2_url',    'contact_us.php', 'text'],
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

    // Top up the CTA section
    $findSection->execute([$page_id, 'cta_section']);
    if ($cta_id = $findSection->fetchColumn()) {
        foreach ($cta_fields as $f) {
            list($fkey, $fval, $ftype) = $f;
            $findField->execute([$cta_id, $fkey]);
            if (!$findField->fetchColumn()) {
                $addField->execute([$cta_id, $fkey, $fval, $ftype]);
                $newFields++;
            }
        }
        $pdo->prepare("UPDATE administration_content SET content_order = 100 WHERE id = ?")->execute([$cta_id]);
    }

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
<title>Install - Pro Vice-Chancellor's Office Content</title>
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
    <h1>Pro Vice-Chancellor&rsquo;s Office &mdash; Content Refresh</h1>
    <p class="sub">Installs the profile of Prof. Peter Agyekum Boateng and the supporting sections.</p>
    <?php foreach ($log as $row): ?>
        <div class="row <?php echo $row[0]; ?>"><?php echo $row[1]; ?></div>
    <?php endforeach; ?>
    <a class="btn" href="admin/manage_administration_pages.php">Open Admin Manager</a>
    <a class="btn alt" href="office_of_the_pro-vice_chancellor.php">View the Page</a>
</div>
</body>
</html>
