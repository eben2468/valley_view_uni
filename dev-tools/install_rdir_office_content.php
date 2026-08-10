<?php
/**
 * Installer: Office of Research, Development and International Relations
 * ----------------------------------------------------------------------
 * Replaces the generic placeholder body content with the profile of
 * Prof. Josephine Ganu, plus the richer supporting sections used on the
 * Vice Chancellor and Pro-Vice Chancellor pages.
 *
 * NOTE on sources: the officer profile is taken verbatim from
 * vvu.edu.gh/.../profile-of-josephine-ganu-phd. The RDIR office page on
 * vvu.edu.gh currently carries the wrong body text (it repeats the Office of
 * Spiritual Life welcome message under the RDIR heading), so the "About the
 * Office" and "Focus Areas" copy below was written to match the office's
 * actual remit as described in the Dean's own profile. Every word of it is
 * editable in the admin panel and can be swapped for the official text.
 *
 * The hero section and the CTA section are left untouched.
 * Legacy sections are dropped once; new sections are only created when
 * missing, so re-running never overwrites admin edits.
 */
require_once 'includes/db_connect.php';

$SLUG = 'office_of_rdir';
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
    $legacy = ['rdir_profile', 'research_vision', 'contact_section'];
    $del = $pdo->prepare("DELETE FROM administration_content WHERE page_id = ? AND section_key = ?");
    foreach ($legacy as $key) {
        $del->execute([$page_id, $key]);
        if ($del->rowCount() > 0) say($log, "Removed legacy section <b>$key</b> and all of its fields");
    }

    /* ---------------------------------------------------------------
     * 2. New sections
     * --------------------------------------------------------------- */
    $PHOTO = 'images/leadership/prof-josephine-ganu.png';

    $definitions = [

        // ---- Profile (verbatim from vvu.edu.gh / Key Officers) ----
        'officer_profile' => ['type' => 'profile', 'order' => 2, 'fields' => [
            ['profile_image',  $PHOTO, 'image'],
            ['name',           'Prof. Josephine Ganu, PhD', 'text'],
            ['title',          'Dean, Research, Development & International Relations', 'text'],
            ['section_label',  'Meet the Dean', 'text'],
            ['section_title',  'Profile & Biography', 'text'],
            ['bio_paragraph_1', 'Prof. Josephine Ganu is an experienced professor, scholar, and researcher with over 20 years in academia. She is currently the Dean of Research, Development, and International Relations (RDIR) at Valley View University. Her previous roles at the university include faculty member, department Head, and founding Dean of the School of Business.', 'textarea'],
            ['bio_paragraph_2', 'Prior to rejoining Valley View University, she served as the Director of Research and Grants Development and as a Professor of Management at the School of Postgraduate Studies at the Adventist University of Africa (AUA) in Kenya. She also led the Editorial Boards of the Pan-African Journals published by AUA and chaired the Institutional Review and Ethics Board.', 'textarea'],
            ['bio_paragraph_3', 'Prof. Ganu has extensive experience in research, journal publication, grant writing, and strategic partnerships. She is passionate about fostering excellence in teaching, scholarship and service through multi-institutional, interdisciplinary research, grants, and collaborations within higher education. She has authored numerous articles in respected academic journals and secured competitive grants.', 'textarea'],
            ['bio_paragraph_4', 'She earned her PhD from the University of Santo Tomas in the Philippines, an MBA from the Adventist University of the Philippines, and a bachelor\'s degree from Andrews University in the USA. Her main research interests include corporate social responsibility, research ethics, organizational behaviour, followership, and workplace spirituality.', 'textarea'],
            ['bio_paragraph_5', 'Prof. Josephine Ganu is a member of the Management & Organizational Behavior Teaching Society (MOBTS), the International Leadership Association (ILA), and the Adventist Human-Subject Researchers Association (AHSRA).', 'textarea'],
            ['highlight_1_title', 'Years in Academia', 'text'],
            ['highlight_1_text',  'Over 20 years as a professor, scholar and researcher across Ghana, Kenya and the Philippines.', 'textarea'],
            ['highlight_2_title', 'Areas of Expertise', 'text'],
            ['highlight_2_text',  'Research leadership, journal publication, grant writing and strategic partnerships.', 'textarea'],
        ]],

        // ---- Leadership & service roles ----
        'leadership_roles' => ['type' => 'section', 'order' => 3, 'fields' => [
            ['section_label',       'Track Record', 'text'],
            ['section_title',       'Leadership & Service', 'text'],
            ['section_description', 'Senior academic and research leadership roles held at Valley View University and the Adventist University of Africa.', 'textarea'],

            ['role_1_icon',  'travel_explore',   'text'],
            ['role_1_title', 'Dean, RDIR', 'text'],
            ['role_1_text',  'Currently leads research, development and international relations at Valley View University.', 'textarea'],

            ['role_2_icon',  'storefront',       'text'],
            ['role_2_title', 'Founding Dean, School of Business', 'text'],
            ['role_2_text',  'Established and led the School of Business at Valley View University.', 'textarea'],

            ['role_3_icon',  'groups',           'text'],
            ['role_3_title', 'Head of Department & Faculty Member', 'text'],
            ['role_3_text',  'Earlier service at Valley View University in teaching and departmental leadership.', 'textarea'],

            ['role_4_icon',  'payments',         'text'],
            ['role_4_title', 'Director of Research & Grants Development, AUA', 'text'],
            ['role_4_text',  'Led research and grants development at the Adventist University of Africa, Kenya.', 'textarea'],

            ['role_5_icon',  'menu_book',        'text'],
            ['role_5_title', 'Editorial Leadership, Pan-African Journals', 'text'],
            ['role_5_text',  'Led the Editorial Boards of the Pan-African Journals published by AUA.', 'textarea'],

            ['role_6_icon',  'gavel',            'text'],
            ['role_6_title', 'Chair, Institutional Review & Ethics Board', 'text'],
            ['role_6_text',  'Chaired the Institutional Review and Ethics Board at AUA, safeguarding research integrity.', 'textarea'],
        ]],

        // ---- Qualifications, interests, memberships ----
        'credentials' => ['type' => 'section', 'order' => 4, 'fields' => [
            ['section_label',       'Qualifications', 'text'],
            ['section_title',       'Academic & Professional Standing', 'text'],
            ['section_description', 'A scholarly grounding across three continents, with active membership of international academic bodies.', 'textarea'],

            ['academic_title',   'Academic Qualifications', 'text'],
            ['academic_1_title', 'PhD', 'text'],
            ['academic_1_text',  'University of Santo Tomas, Philippines', 'text'],
            ['academic_2_title', 'Master of Business Administration', 'text'],
            ['academic_2_text',  'Adventist University of the Philippines', 'text'],
            ['academic_3_title', "Bachelor's Degree", 'text'],
            ['academic_3_text',  'Andrews University, USA', 'text'],

            ['memberships_title', 'Professional Memberships', 'text'],
            ['membership_1', 'Management & Organizational Behavior Teaching Society (MOBTS)', 'text'],
            ['membership_2', 'International Leadership Association (ILA)', 'text'],
            ['membership_3', 'Adventist Human-Subject Researchers Association (AHSRA)', 'text'],

            ['interests_title',  'Research Interests', 'text'],
            ['interest_1_icon',  'volunteer_activism', 'text'],
            ['interest_1_title', 'Corporate Social Responsibility', 'text'],
            ['interest_2_icon',  'balance',            'text'],
            ['interest_2_title', 'Research Ethics', 'text'],
            ['interest_3_icon',  'psychology',         'text'],
            ['interest_3_title', 'Organizational Behaviour', 'text'],
            ['interest_4_icon',  'diversity_3',        'text'],
            ['interest_4_title', 'Followership', 'text'],
            ['interest_5_icon',  'self_improvement',   'text'],
            ['interest_5_title', 'Workplace Spirituality', 'text'],
        ]],

        // ---- About the office ----
        'office_overview' => ['type' => 'section', 'order' => 5, 'fields' => [
            ['section_label',  'About RDIR', 'text'],
            ['section_title',  'The Office of Research, Development & International Relations', 'text'],
            ['paragraph_1',    'The Office of Research, Development and International Relations (RDIR) coordinates the research enterprise of Valley View University. It exists to grow the quality, volume and visibility of scholarship produced across every school and campus of the University.', 'textarea'],
            ['paragraph_2',    'The Office supports faculty and students through the full research cycle - from proposal development and grant applications, through ethical review and supervision, to publication in reputable peer-reviewed journals. It also administers the University\'s research policies and safeguards the integrity of human-subject research.', 'textarea'],
            ['paragraph_3',    'Beyond the campus, RDIR builds and maintains the University\'s international relationships: memoranda of understanding with peer institutions, staff and student exchange, joint research projects, and multi-institutional, interdisciplinary collaboration that connects Valley View University to the wider academic world.', 'textarea'],
        ]],

        // ---- Focus areas ----
        'focus_areas' => ['type' => 'section', 'order' => 6, 'fields' => [
            ['section_label',       'What We Do', 'text'],
            ['section_title',       'Strategic Focus Areas', 'text'],
            ['section_description', 'Six areas of work through which the Office serves the University community.', 'textarea'],

            ['area_1_icon',  'science',        'text'],
            ['area_1_title', 'Research Support', 'text'],
            ['area_1_text',  'Guidance, resources and infrastructure for faculty and student research across all disciplines.', 'textarea'],

            ['area_2_icon',  'savings',        'text'],
            ['area_2_title', 'Grants & Funding', 'text'],
            ['area_2_text',  'Identifying funding opportunities and supporting the preparation of competitive grant applications.', 'textarea'],

            ['area_3_icon',  'menu_book',      'text'],
            ['area_3_title', 'Journals & Publication', 'text'],
            ['area_3_text',  'Support for publishing in reputable peer-reviewed journals and stewardship of University journals.', 'textarea'],

            ['area_4_icon',  'gavel',          'text'],
            ['area_4_title', 'Research Ethics & Integrity', 'text'],
            ['area_4_text',  'Institutional review of research proposals and protection of human subjects in accordance with policy.', 'textarea'],

            ['area_5_icon',  'public',         'text'],
            ['area_5_title', 'International Relations', 'text'],
            ['area_5_text',  'Partnerships, memoranda of understanding and exchange programmes with institutions worldwide.', 'textarea'],

            ['area_6_icon',  'trending_up',    'text'],
            ['area_6_title', 'Institutional Development', 'text'],
            ['area_6_text',  'Strategic initiatives, capacity building and projects that strengthen the University\'s academic standing.', 'textarea'],
        ]],

        // ---- Related offices ----
        'related_offices' => ['type' => 'section', 'order' => 7, 'fields' => [
            ['section_label', 'Explore Further', 'text'],
            ['section_title', 'Related Offices & Resources', 'text'],

            ['link_1_icon',  'account_balance', 'text'],
            ['link_1_title', 'Office of the Vice Chancellor', 'text'],
            ['link_1_text',  'Executive leadership and the strategic direction of the University.', 'textarea'],
            ['link_1_url',   'office_of_the_vice_chancellor.php', 'text'],

            ['link_2_icon',  'co_present',      'text'],
            ['link_2_title', 'Office of the Pro-Vice Chancellor', 'text'],
            ['link_2_text',  'Academic leadership, quality assurance and academic planning.', 'textarea'],
            ['link_2_url',   'office_of_the_pro-vice_chancellor.php', 'text'],

            ['link_3_icon',  'science',         'text'],
            ['link_3_title', 'Research Opportunities', 'text'],
            ['link_3_text',  'Current research openings and opportunities for staff and students.', 'textarea'],
            ['link_3_url',   'research_opportunities.php', 'text'],

            ['link_4_icon',  'auto_stories',    'text'],
            ['link_4_title', 'Journals', 'text'],
            ['link_4_text',  'Academic journals published and indexed by the University.', 'textarea'],
            ['link_4_url',   'journals.php', 'text'],
        ]],

        // ---- Contact ----
        'office_contact' => ['type' => 'section', 'order' => 8, 'fields' => [
            ['section_label',       'Get in Touch', 'text'],
            ['section_title',       'Contact the Office', 'text'],
            ['section_description', 'For research collaboration, grant support, ethical review or international partnership enquiries, the RDIR team is glad to assist.', 'textarea'],
            ['email',               'rdir@vvu.edu.gh', 'text'],
            ['phone',               '+233 (0) 302 501 101', 'text'],
            ['office_location',     'RDIR Office, Administration Block, Oyibi', 'text'],
            ['postal_address',      'P. O. Box AF 595, Adentan, Accra, Ghana', 'text'],
            ['office_hours',        'Monday - Thursday, 8:00am - 5:00pm | Friday, 8:00am - 12:00pm', 'text'],
            ['map_url',             'https://maps.google.com/?q=Valley+View+University+Oyibi', 'text'],
            ['form_title',          'Research & Partnership Inquiry', 'text'],
            ['form_description',    'Send your research, grant or partnership enquiry to the office. You can expect a response within three working days.', 'textarea'],
            ['form_btn_text',       'Submit Inquiry', 'text'],
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
<title>Install - RDIR Office Content</title>
<style>
    body{font-family:system-ui,-apple-system,'Segoe UI',sans-serif;background:#f1f5f9;margin:0;padding:40px 20px;color:#1e293b}
    .box{max-width:760px;margin:0 auto;background:#fff;border-radius:20px;padding:40px;box-shadow:0 10px 40px rgba(0,0,0,.06)}
    h1{margin:0 0 6px;font-size:1.7rem}
    p.sub{margin:0 0 28px;color:#64748b}
    .row{padding:12px 18px;border-radius:12px;margin-bottom:10px;font-size:.95rem}
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
    <h1>RDIR Office &mdash; Content Refresh</h1>
    <p class="sub">Installs the profile of Prof. Josephine Ganu and the supporting sections.</p>
    <?php foreach ($log as $row): ?>
        <div class="row <?php echo $row[0]; ?>"><?php echo $row[1]; ?></div>
    <?php endforeach; ?>
    <div class="row warn">
        <b>Note:</b> the RDIR page on vvu.edu.gh currently shows the Office of Spiritual Life welcome text under the RDIR heading, so the "About the Office" and "Focus Areas" copy was written for this office rather than copied. Both are fully editable in the admin panel.
    </div>
    <a class="btn" href="admin/manage_administration_pages.php">Open Admin Manager</a>
    <a class="btn alt" href="office_of_rdir.php">View the Page</a>
</div>
</body>
</html>
