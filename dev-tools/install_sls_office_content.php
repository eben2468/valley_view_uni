<?php
/**
 * Installer: Office of the Dean of Spiritual Life and Development
 * ---------------------------------------------------------------
 * Replaces the generic placeholder body content with the profile of
 * Pr. Peter Obeng Manu and the Office's own welcome message, plus the
 * supporting sections used on the Vice Chancellor and Pro-Vice Chancellor
 * pages.
 *
 * Sources: the officer profile comes from
 *   vvu.edu.gh/.../key-officers/profile-of-pr-peter-obeng-manu
 * and the welcome message from the article currently published at
 *   vvu.edu.gh/.../office-of-research-development-and-international-relations-rdir
 * which carries the Office of Spiritual Life welcome text (a mislabelled
 * article on vvu.edu.gh - the text itself belongs to this office).
 *
 * The hero section and the CTA section are left untouched.
 * Legacy sections are dropped once; new sections are only created when
 * missing, so re-running never overwrites admin edits.
 */
require_once 'includes/db_connect.php';

$SLUG = 'office_of_sls';
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
    $legacy = ['sls_profile', 'spiritual_programs', 'contact_section'];
    $del = $pdo->prepare("DELETE FROM administration_content WHERE page_id = ? AND section_key = ?");
    foreach ($legacy as $key) {
        $del->execute([$page_id, $key]);
        if ($del->rowCount() > 0) say($log, "Removed legacy section <b>$key</b> and all of its fields");
    }

    /* ---------------------------------------------------------------
     * 2. New sections
     * --------------------------------------------------------------- */
    $PHOTO = 'images/leadership/pr-peter-obeng-manu.jpg';

    $definitions = [

        // ---- Profile (verbatim from vvu.edu.gh / Key Officers) ----
        'officer_profile' => ['type' => 'profile', 'order' => 2, 'fields' => [
            ['profile_image',  $PHOTO, 'image'],
            ['name',           'Pr. Peter Obeng Manu', 'text'],
            ['title',          'Dean of Spiritual Life and Development', 'text'],
            ['section_label',  'Meet the Dean', 'text'],
            ['section_title',  'Profile & Biography', 'text'],
            ['bio_paragraph_1', 'Pr. Peter Obeng Manu is an ordained minister of the Seventh-day Adventist Church with extensive experience in pastoral ministry, university administration, chaplaincy, and teaching. Over the years, he has served in several capacities, including District Pastor, Church Pastor, Departmental Director for Chaplaincy and Communication, Lecturer at Valley View University, Associate Dean of Student Life and Services, Associate Chaplain, and Dean of Spiritual Life Development.', 'textarea'],
            ['bio_paragraph_2', 'His academic background includes a Bachelor of Arts in Pastoral Theology and a Master of Arts in Pastoral Theology. He is currently a PhD candidate at the Adventist University of Africa, Kenya.', 'textarea'],
            ['bio_paragraph_3', 'He has published scholarly articles in peer-reviewed journals and presented papers at academic conferences. His key competencies include strong communication and writing skills, excellent interpersonal relations, and the ability to work effectively within multidisciplinary teams. He is known as a hardworking, self-motivated, and passionate individual, deeply committed to advancing the mission and kingdom of God.', 'textarea'],
            ['bio_paragraph_4', 'His personal interests include reading, preaching, listening to gospel music, and playing table tennis.', 'textarea'],
            ['highlight_1_title', 'Ordained Minister', 'text'],
            ['highlight_1_text',  'An ordained minister of the Seventh-day Adventist Church with experience across pastoral ministry, chaplaincy, teaching and university administration.', 'textarea'],
            ['highlight_2_title', 'Personal Interests', 'text'],
            ['highlight_2_text',  'Reading, preaching, listening to gospel music, and playing table tennis.', 'textarea'],
        ]],

        // ---- Welcome message from the Office ----
        'welcome_message' => ['type' => 'section', 'order' => 3, 'fields' => [
            ['section_label',   'From the Office', 'text'],
            ['section_title',   'A Word of Welcome', 'text'],
            ['greeting',        'Welcome to the Office of Spiritual Life and Development, Valley View University', 'text'],
            ['paragraph_1',     'The Office of the Spiritual Life at Valley View University exists to nurture faith, character, and service in harmony with the University\'s mission and Seventh-day Adventist philosophy of education.', 'textarea'],
            ['paragraph_2',     'We are committed to fostering a vibrant spiritual environment where students, faculty, and staff grow in their relationship with God and with one another. Through worship services, weeks of spiritual emphasis, prayer initiatives, counseling, mentoring, outreach, and campus ministry programs, we seek to integrate faith into every aspect of university life.', 'textarea'],
            ['paragraph_3',     'Our goal is to support the holistic development of the Valley View University community - spiritually, morally, socially, and intellectually - preparing graduates who are grounded in Christian values, committed to service, and equipped to lead with integrity and purpose in their chosen fields.', 'textarea'],
            ['paragraph_4',     'As an office, we encourage a culture of worship, discipleship, and compassionate service, guiding our community to live out Christ-centered lives that positively impact the Church, society, and the world.', 'textarea'],
            ['paragraph_5',     'May the Lord continue to bless Valley View University, its leadership, faculty, staff, students, and alumni. We pray that every member of the University Community will enjoy a fruitful, transformative, and spiritually enriching experience as we journey together in unity, purpose, and mission.', 'textarea'],
            ['pull_quote',      'We encourage a culture of worship, discipleship, and compassionate service, guiding our community to live out Christ-centered lives.', 'textarea'],
            ['signature_name',  'Pr. Peter Obeng Manu', 'text'],
            ['signature_title', 'Dean of Spiritual Life and Development', 'text'],
            ['signature_image', $PHOTO, 'image'],
        ]],

        // ---- Ministries drawn from the welcome message ----
        'ministries' => ['type' => 'section', 'order' => 4, 'fields' => [
            ['section_label',       'Our Ministries', 'text'],
            ['section_title',       'Programmes & Ministries', 'text'],
            ['section_description', 'The ways in which the Office seeks to integrate faith into every aspect of university life.', 'textarea'],

            ['item_1_icon',  'church',             'text'],
            ['item_1_title', 'Worship Services', 'text'],
            ['item_1_text',  'Chapel services, vespers and worship gatherings that draw the University community together before God.', 'textarea'],

            ['item_2_icon',  'auto_awesome',       'text'],
            ['item_2_title', 'Weeks of Spiritual Emphasis', 'text'],
            ['item_2_text',  'Dedicated weeks of preaching, reflection and renewal set apart in the University calendar.', 'textarea'],

            ['item_3_icon',  'volunteer_activism', 'text'],
            ['item_3_title', 'Prayer Initiatives', 'text'],
            ['item_3_text',  'Prayer bands, intercession and seasons of prayer for the campus, the Church and the nation.', 'textarea'],

            ['item_4_icon',  'support_agent',      'text'],
            ['item_4_title', 'Counseling', 'text'],
            ['item_4_text',  'Confidential pastoral care and spiritual guidance for students, faculty and staff.', 'textarea'],

            ['item_5_icon',  'diversity_3',        'text'],
            ['item_5_title', 'Mentoring', 'text'],
            ['item_5_text',  'Discipleship and one-to-one mentoring that helps young people grow in faith and character.', 'textarea'],

            ['item_6_icon',  'campaign',           'text'],
            ['item_6_title', 'Outreach & Campus Ministry', 'text'],
            ['item_6_text',  'Evangelistic and community service programmes that put faith into compassionate action.', 'textarea'],
        ]],

        // ---- Service record ----
        'leadership_roles' => ['type' => 'section', 'order' => 5, 'fields' => [
            ['section_label',       'Track Record', 'text'],
            ['section_title',       'Ministry & Service', 'text'],
            ['section_description', 'Roles held across pastoral ministry, chaplaincy, teaching and university administration.', 'textarea'],

            ['role_1_icon',  'record_voice_over', 'text'],
            ['role_1_title', 'District & Church Pastor', 'text'],
            ['role_1_text',  'Pastoral leadership of congregations within the Seventh-day Adventist Church.', 'textarea'],

            ['role_2_icon',  'connect_without_contact', 'text'],
            ['role_2_title', 'Director for Chaplaincy & Communication', 'text'],
            ['role_2_text',  'Departmental Director with responsibility for chaplaincy and communication ministries.', 'textarea'],

            ['role_3_icon',  'cast_for_education', 'text'],
            ['role_3_title', 'Lecturer, Valley View University', 'text'],
            ['role_3_text',  'Teaching and mentoring students within the University.', 'textarea'],

            ['role_4_icon',  'groups',             'text'],
            ['role_4_title', "Associate Dean of Student Life & Services", 'text'],
            ['role_4_text',  'Supported the welfare, discipline and development of the student body.', 'textarea'],

            ['role_5_icon',  'church',             'text'],
            ['role_5_title', 'Associate Chaplain', 'text'],
            ['role_5_text',  'Served the spiritual needs of the campus community alongside the Chaplaincy team.', 'textarea'],

            ['role_6_icon',  'workspace_premium',  'text'],
            ['role_6_title', 'Dean of Spiritual Life Development', 'text'],
            ['role_6_text',  'Currently leads the Office of Spiritual Life and Development at Valley View University.', 'textarea'],
        ]],

        // ---- Qualifications & competencies ----
        'credentials' => ['type' => 'section', 'order' => 6, 'fields' => [
            ['section_label',       'Qualifications', 'text'],
            ['section_title',       'Academic & Professional Standing', 'text'],
            ['section_description', 'A grounding in pastoral theology, supported by scholarly publication and ongoing doctoral study.', 'textarea'],

            ['academic_title',   'Academic Background', 'text'],
            ['academic_1_title', 'PhD Candidate', 'text'],
            ['academic_1_text',  'Adventist University of Africa, Kenya', 'text'],
            ['academic_2_title', 'Master of Arts, Pastoral Theology', 'text'],
            ['academic_2_text',  'Postgraduate study in pastoral theology', 'text'],
            ['academic_3_title', 'Bachelor of Arts, Pastoral Theology', 'text'],
            ['academic_3_text',  'Undergraduate study in pastoral theology', 'text'],

            ['competencies_title', 'Key Competencies', 'text'],
            ['competency_1', 'Strong communication and writing skills', 'text'],
            ['competency_2', 'Excellent interpersonal relations', 'text'],
            ['competency_3', 'Effective work within multidisciplinary teams', 'text'],
            ['competency_4', 'Scholarly articles published in peer-reviewed journals', 'text'],
            ['competency_5', 'Papers presented at academic conferences', 'text'],
        ]],

        // ---- Related pages ----
        'related_offices' => ['type' => 'section', 'order' => 7, 'fields' => [
            ['section_label', 'Explore Further', 'text'],
            ['section_title', 'Related Offices & Resources', 'text'],

            ['link_1_icon',  'self_improvement', 'text'],
            ['link_1_title', 'Spiritual Life & Development', 'text'],
            ['link_1_text',  'Worship, discipleship and the spiritual programmes of the University.', 'textarea'],
            ['link_1_url',   'sld.php', 'text'],

            ['link_2_icon',  'groups',           'text'],
            ['link_2_title', "Office of the Dean of Students' Life & Services", 'text'],
            ['link_2_text',  'Student welfare, residence life and campus services.', 'textarea'],
            ['link_2_url',   'office_of_dsls.php', 'text'],

            ['link_3_icon',  'diversity_3',      'text'],
            ['link_3_title', 'Activities & Clubs', 'text'],
            ['link_3_text',  'Student associations, clubs and campus organisations.', 'textarea'],
            ['link_3_url',   'activities_and_clubs.php', 'text'],

            ['link_4_icon',  'account_balance',  'text'],
            ['link_4_title', 'Office of the Vice Chancellor', 'text'],
            ['link_4_text',  'Executive leadership and the strategic direction of the University.', 'textarea'],
            ['link_4_url',   'office_of_the_vice_chancellor.php', 'text'],
        ]],

        // ---- Contact ----
        'office_contact' => ['type' => 'section', 'order' => 8, 'fields' => [
            ['section_label',       'Get in Touch', 'text'],
            ['section_title',       'Contact the Office', 'text'],
            ['section_description', 'For spiritual guidance, pastoral care, prayer requests or ministry involvement, the Chaplaincy team is here for you.', 'textarea'],
            ['email',               'chaplain@vvu.edu.gh', 'text'],
            ['phone',               '+233 (0) 302 501 101', 'text'],
            ['office_location',     'Chaplaincy Office, Campus Chapel, Oyibi', 'text'],
            ['postal_address',      'P. O. Box AF 595, Adentan, Accra, Ghana', 'text'],
            ['office_hours',        'Monday - Thursday, 8:00am - 5:00pm | Friday, 8:00am - 12:00pm', 'text'],
            ['map_url',             'https://maps.google.com/?q=Valley+View+University+Oyibi', 'text'],
            ['form_title',          'Spiritual Guidance Request', 'text'],
            ['form_description',    'Share a prayer request, ask for pastoral support, or tell us how you would like to serve. Every request is treated in confidence.', 'textarea'],
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
<title>Install - Spiritual Life Office Content</title>
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
    <h1>Spiritual Life &amp; Development &mdash; Content Refresh</h1>
    <p class="sub">Installs the profile of Pr. Peter Obeng Manu and the Office's welcome message.</p>
    <?php foreach ($log as $row): ?>
        <div class="row <?php echo $row[0]; ?>"><?php echo $row[1]; ?></div>
    <?php endforeach; ?>
    <a class="btn" href="admin/manage_administration_pages.php">Open Admin Manager</a>
    <a class="btn alt" href="office_of_sls.php">View the Page</a>
</div>
</body>
</html>
