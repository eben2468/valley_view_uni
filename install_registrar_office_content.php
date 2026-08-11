<?php
/**
 * Installer: Office of the Registrar - content refresh
 * ----------------------------------------------------
 * Replaces the previous Registrar's content with the profile of
 * Dr. Samuel Kwame Amankwah, plus the richer supporting sections used on the
 * Vice Chancellor and Pro-Vice Chancellor pages.
 *
 * The hero section is left untouched.
 * Legacy sections are dropped once; new sections are only created when
 * missing, so re-running never overwrites admin edits.
 *
 * NOTE: the portrait is expected at images/leadership/dr-samuel-kwame-amankwah.jpg.
 * Until that file exists the page falls back to a monogram card - it never
 * shows the previous Registrar's photograph.
 */
require_once 'includes/db_connect.php';

$SLUG = 'office_of_the_registrar';
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
    $legacy = ['registrar_profile', 'services_section', 'quick_links', 'contact_section'];
    $del = $pdo->prepare("DELETE FROM administration_content WHERE page_id = ? AND section_key = ?");
    foreach ($legacy as $key) {
        $del->execute([$page_id, $key]);
        if ($del->rowCount() > 0) say($log, "Removed legacy section <b>$key</b> and all of its fields");
    }

    /* ---------------------------------------------------------------
     * 2. New sections
     * --------------------------------------------------------------- */
    $PHOTO = 'images/leadership/dr-samuel-kwame-amankwah.jpg';

    $definitions = [

        // ---- Profile ----
        'officer_profile' => ['type' => 'profile', 'order' => 2, 'fields' => [
            ['profile_image',  $PHOTO, 'image'],
            ['name',           'Dr. Samuel Kwame Amankwah', 'text'],
            ['title',          'Registrar', 'text'],
            ['section_label',  'Meet the Registrar', 'text'],
            ['section_title',  'Profile & Biography', 'text'],
            ['bio_paragraph_1', 'Dr. Samuel Kwame Amankwah is a Ghanaian educationist, higher education administrator, and pastor who serves as the Registrar of Valley View University, Accra, Ghana. With more than two decades of experience in university administration and academic governance, he has contributed significantly to institutional development in areas such as quality assurance, enrollment management, academic planning, and academic administration.', 'textarea'],
            ['bio_paragraph_2', 'Dr. Amankwah holds a Doctor of Philosophy (PhD) in Education with emphasis in Curriculum and Instruction and a cognate in Higher Education Administration from the Adventist International Institute of Advanced Studies (AIIAS), Silang, Cavite, Philippines. He also earned a Master of Arts in Educational Leadership (Curriculum Studies) and a Bachelor of Education in Management from the University of Education, Winneba, Ghana. In addition, he holds a Postgraduate Diploma in Pastoral Ministry from Valley View University.', 'textarea'],
            ['bio_paragraph_3', 'His professional career at Valley View University spans over twenty years. Prior to his appointment as Registrar in 2026, he served as Acting Director of Quality Assurance and Academic Planning (2024 - 2025), Director of Enrollment Management (2022 - 2024), and Director of Quality Assurance (2020 - 2022). Earlier, he worked as Administrative Officer in the Academic Registry (2001 - 2022), where he gained extensive experience in academic administration and university governance.', 'textarea'],
            ['bio_paragraph_4', 'Dr. Amankwah also serves as an Adjunct Lecturer in the Department of Teacher Education at Valley View University. Beyond academia, he is the Pastor of the Good Shepherd Seventh-day Adventist Church in Teiman in the Meridian Ghana Conference and Chairperson of the Workers Welfare Association of Valley View University.', 'textarea'],
            ['highlight_1_title', 'Years of Service', 'text'],
            ['highlight_1_text',  'More than two decades in university administration and academic governance at Valley View University.', 'textarea'],
            ['highlight_2_title', 'Areas of Contribution', 'text'],
            ['highlight_2_text',  'Quality assurance, enrollment management, academic planning and academic administration.', 'textarea'],
        ]],

        // ---- Career timeline ----
        'career_timeline' => ['type' => 'section', 'order' => 3, 'fields' => [
            ['section_label',       'Two Decades at VVU', 'text'],
            ['section_title',       'Professional Journey', 'text'],
            ['section_description', 'A career built inside the Academic Registry, progressing through quality assurance, enrollment management and academic planning.', 'textarea'],

            ['step_1_year',  '2001 - 2022', 'text'],
            ['step_1_title', 'Administrative Officer, Academic Registry', 'text'],
            ['step_1_text',  'Gained extensive experience in academic administration and university governance.', 'textarea'],

            ['step_2_year',  '2020 - 2022', 'text'],
            ['step_2_title', 'Director of Quality Assurance', 'text'],
            ['step_2_text',  'Led the University\'s quality assurance function and its standards of academic delivery.', 'textarea'],

            ['step_3_year',  '2022 - 2024', 'text'],
            ['step_3_title', 'Director of Enrollment Management', 'text'],
            ['step_3_text',  'Directed recruitment, admissions and enrollment strategy across the University.', 'textarea'],

            ['step_4_year',  '2024 - 2025', 'text'],
            ['step_4_title', 'Acting Director of Quality Assurance and Academic Planning', 'text'],
            ['step_4_text',  'Combined oversight of quality assurance with the University\'s academic planning agenda.', 'textarea'],

            ['step_5_year',  '2026 - Present', 'text'],
            ['step_5_title', 'Registrar', 'text'],
            ['step_5_text',  'Appointed Registrar of Valley View University, with responsibility for the Registry and academic governance.', 'textarea'],
        ]],

        // ---- Qualifications & wider service ----
        'credentials' => ['type' => 'section', 'order' => 4, 'fields' => [
            ['section_label',       'Qualifications', 'text'],
            ['section_title',       'Academic & Professional Standing', 'text'],
            ['section_description', 'A scholar of curriculum and instruction whose service extends from the lecture room to the pulpit.', 'textarea'],

            ['academic_title',   'Academic Qualifications', 'text'],
            ['academic_1_title', 'PhD, Education (Curriculum and Instruction)', 'text'],
            ['academic_1_text',  'Cognate in Higher Education Administration - Adventist International Institute of Advanced Studies (AIIAS), Silang, Cavite, Philippines', 'text'],
            ['academic_2_title', 'MA, Educational Leadership (Curriculum Studies)', 'text'],
            ['academic_2_text',  'University of Education, Winneba, Ghana', 'text'],
            ['academic_3_title', 'BEd, Management', 'text'],
            ['academic_3_text',  'University of Education, Winneba, Ghana', 'text'],
            ['academic_4_title', 'Postgraduate Diploma, Pastoral Ministry', 'text'],
            ['academic_4_text',  'Valley View University', 'text'],

            ['service_title', 'Teaching, Ministry & Service', 'text'],
            ['service_1', 'Adjunct Lecturer, Department of Teacher Education, Valley View University', 'text'],
            ['service_2', 'Pastor, Good Shepherd Seventh-day Adventist Church, Teiman - Meridian Ghana Conference', 'text'],
            ['service_3', 'Chairperson, Workers Welfare Association of Valley View University', 'text'],
        ]],

        // ---- Research interests ----
        'research_focus' => ['type' => 'section', 'order' => 5, 'fields' => [
            ['section_label',       'Scholarship', 'text'],
            ['section_title',       'Research Interests', 'text'],
            ['section_description', 'Research that connects teaching practice, technology and the governance of higher education.', 'textarea'],

            ['focus_1_icon',  'laptop_chromebook', 'text'],
            ['focus_1_title', 'Online Learning', 'text'],
            ['focus_2_icon',  'devices',           'text'],
            ['focus_2_title', 'ICT in Education', 'text'],
            ['focus_3_icon',  'workspace_premium', 'text'],
            ['focus_3_title', 'Educational Leadership', 'text'],
            ['focus_4_icon',  'verified',          'text'],
            ['focus_4_title', 'Quality Assurance in Higher Education', 'text'],
            ['focus_5_icon',  'menu_book',         'text'],
            ['focus_5_title', 'Curriculum and Instruction', 'text'],
        ]],

        // ---- What the Registry does ----
        'office_mandate' => ['type' => 'section', 'order' => 6, 'fields' => [
            ['section_label',       'The Registry', 'text'],
            ['section_title',       'Mandate of the Office', 'text'],
            ['section_description', 'The Registry is the administrative heart of the University, serving students and staff from application through to graduation.', 'textarea'],

            ['item_1_icon',  'how_to_reg',      'text'],
            ['item_1_title', 'Admissions & Enrollment', 'text'],
            ['item_1_text',  'Processing applications, admissions decisions and the registration of students each semester.', 'textarea'],

            ['item_2_icon',  'folder_shared',   'text'],
            ['item_2_title', 'Student Records', 'text'],
            ['item_2_text',  'Custody of academic records, transcripts, certificates and student data.', 'textarea'],

            ['item_3_icon',  'assignment',      'text'],
            ['item_3_title', 'Examinations', 'text'],
            ['item_3_text',  'Coordination of examinations, results processing and the integrity of assessment.', 'textarea'],

            ['item_4_icon',  'verified',        'text'],
            ['item_4_title', 'Quality Assurance', 'text'],
            ['item_4_text',  'Upholding academic standards, accreditation requirements and institutional policy.', 'textarea'],

            ['item_5_icon',  'event_note',      'text'],
            ['item_5_title', 'Academic Planning', 'text'],
            ['item_5_text',  'The academic calendar, curriculum records and planning for programme delivery.', 'textarea'],

            ['item_6_icon',  'school',          'text'],
            ['item_6_title', 'Graduation & Certification', 'text'],
            ['item_6_text',  'Verification of completion, congregation arrangements and the award of certificates.', 'textarea'],
        ]],

        // ---- Related pages ----
        'related_offices' => ['type' => 'section', 'order' => 7, 'fields' => [
            ['section_label', 'Explore Further', 'text'],
            ['section_title', 'Services & Related Offices', 'text'],

            ['link_1_icon',  'how_to_reg',      'text'],
            ['link_1_title', 'Admissions', 'text'],
            ['link_1_text',  'Entry requirements, application steps and admission deadlines.', 'textarea'],
            ['link_1_url',   'admissions.php', 'text'],

            ['link_2_icon',  'calendar_month',  'text'],
            ['link_2_title', 'Academic Calendar', 'text'],
            ['link_2_text',  'Semester dates, registration windows and examination periods.', 'textarea'],
            ['link_2_url',   'academic_calendar.php', 'text'],

            ['link_3_icon',  'account_circle',  'text'],
            ['link_3_title', 'Student Portal', 'text'],
            ['link_3_text',  'Register for courses, check results and manage your records online.', 'textarea'],
            ['link_3_url',   'https://portal.vvu.edu.gh', 'text'],

            ['link_4_icon',  'co_present',      'text'],
            ['link_4_title', 'Office of the Pro-Vice Chancellor', 'text'],
            ['link_4_text',  'Academic leadership, quality assurance and academic planning.', 'textarea'],
            ['link_4_url',   'office_of_the_pro-vice_chancellor.php', 'text'],
        ]],

        // ---- Contact ----
        'office_contact' => ['type' => 'section', 'order' => 8, 'fields' => [
            ['section_label',       'Get in Touch', 'text'],
            ['section_title',       'Contact the Registry', 'text'],
            ['section_description', 'For admissions, records, transcripts, examinations or any academic administration matter, the Registry team is here to assist.', 'textarea'],
            ['email',               'registrar@vvu.edu.gh', 'text'],
            ['phone',               '+233 (0) 307 051 149', 'text'],
            ['office_location',     'Administration Block, Main Campus, Oyibi', 'text'],
            ['postal_address',      'P. O. Box AF 595, Adentan, Accra, Ghana', 'text'],
            ['office_hours',        'Monday - Thursday, 8:00am - 5:00pm | Friday, 8:00am - 12:00pm', 'text'],
            ['map_url',             'https://maps.google.com/?q=Valley+View+University+Oyibi', 'text'],
            ['form_title',          'Registry Enquiry', 'text'],
            ['form_description',    'Send your admissions, records or examinations enquiry to the Registry. You can expect a response within three working days.', 'textarea'],
            ['form_btn_text',       'Send Enquiry', 'text'],
        ]],
    ];

    /* ---------------------------------------------------------------
     * 3. CTA fields the front-end expected but the database never had
     * --------------------------------------------------------------- */
    $cta_fields = [
        ['cta_title',       'Need Administrative', 'text'],
        ['cta_highlight',   'Support?', 'text'],
        ['cta_description', 'The Registry is here to assist you with all your administrative and academic records needs, from registration through to graduation.', 'textarea'],
        ['button_1_text',   'Contact the Registry', 'text'],
        ['button_1_url',    'contact_us.php', 'text'],
        ['button_2_text',   'Student Portal', 'text'],
        ['button_2_url',    'https://portal.vvu.edu.gh', 'text'],
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

    $photo_ok = file_exists(__DIR__ . '/' . $PHOTO);
    say($log, $photo_ok
        ? 'Portrait found at <code>' . $PHOTO . '</code>.'
        : 'Portrait <b>not yet saved</b> at <code>' . $PHOTO . '</code> - the page shows a monogram until you add it.',
        $photo_ok ? 'ok' : 'warn');

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
<title>Install - Registrar Office Content</title>
<style>
    body{font-family:system-ui,-apple-system,'Segoe UI',sans-serif;background:#f1f5f9;margin:0;padding:40px 20px;color:#1e293b}
    .box{max-width:780px;margin:0 auto;background:#fff;border-radius:20px;padding:40px;box-shadow:0 10px 40px rgba(0,0,0,.06)}
    h1{margin:0 0 6px;font-size:1.7rem}
    p.sub{margin:0 0 28px;color:#64748b}
    .row{padding:12px 18px;border-radius:12px;margin-bottom:10px;font-size:.95rem;line-height:1.6}
    code{background:rgba(0,0,0,.06);padding:2px 6px;border-radius:5px}
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
    <h1>Office of the Registrar &mdash; Content Refresh</h1>
    <p class="sub">Installs the profile of Dr. Samuel Kwame Amankwah and the supporting sections.</p>
    <?php foreach ($log as $row): ?>
        <div class="row <?php echo $row[0]; ?>"><?php echo $row[1]; ?></div>
    <?php endforeach; ?>
    <a class="btn" href="admin/manage_administration_pages.php">Open Admin Manager</a>
    <a class="btn alt" href="office_of_the_registrar.php">View the Page</a>
</div>
</body>
</html>
