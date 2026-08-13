<?php
/**
 * Installer: Office of the Chief Finance Officer - content refresh
 * ----------------------------------------------------------------
 * Replaces the generic placeholder body content with the profile of
 * Dr. Francis Osei-Kuffour, plus the richer supporting sections used on the
 * Vice-Chancellor and Pro Vice-Chancellor pages.
 *
 * The hero section and the CTA section are left untouched.
 * Legacy sections are dropped once; new sections are only created when
 * missing, so re-running never overwrites admin edits.
 */
require_once 'includes/db_connect.php';

$SLUG = 'office_of_the_cfo';
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
    $legacy = ['cfo_profile', 'financial_vision', 'contact_section'];
    $del = $pdo->prepare("DELETE FROM administration_content WHERE page_id = ? AND section_key = ?");
    foreach ($legacy as $key) {
        $del->execute([$page_id, $key]);
        if ($del->rowCount() > 0) say($log, "Removed legacy section <b>$key</b> and all of its fields");
    }

    /* ---------------------------------------------------------------
     * 2. New sections
     * --------------------------------------------------------------- */
    $PHOTO = 'images/leadership/dr-francis-osei-kuffour.jpg';

    $definitions = [

        // ---- Profile (vvu.edu.gh / Key Officers) ----
        'officer_profile' => ['type' => 'profile', 'order' => 2, 'fields' => [
            ['profile_image',  $PHOTO, 'image'],
            ['name',           'Dr. Francis Osei-Kuffour', 'text'],
            ['title',          'Chief Finance Officer', 'text'],
            ['section_label',  'Meet the Chief Finance Officer', 'text'],
            ['section_title',  'Profile & Biography', 'text'],
            ['bio_paragraph_1', 'Dr. Francis Osei-Kuffour is the Chief Finance Officer and Supportive Lecturer at Valley View University. He holds a Doctor of Philosophy in Accounting & Finance from the Adventist University of the Philippines, a Master of Business Administration in Accounting & Finance from the University of Professional Studies, and a Bachelor\'s Degree in Applied Accounting from Oxford Brookes University, London.', 'textarea'],
            ['bio_paragraph_2', 'Dr. Osei-Kuffour is a Fellow of the Association of Chartered Certified Accountants (ACCA) and holds membership with the Institute of Chartered Accountants, Ghana (ICAG), the Institute of Internal Auditors, Ghana (IIAG), Chartered Certified Accounting Technician (CAT) London, and the Chartered Institute of Taxation, Ghana (CITG). He is an Oxford Brookes University Student-Mentor and supervises Students\' Research Analysis Projects (RAP). He holds a Professional Teachers\' Certificate A.', 'textarea'],
            ['bio_paragraph_3', 'Dr. Osei-Kuffour\'s work experience spans over 27 years. After four years of teaching at the Basic Educational Level, he worked with the Ghana Adventist Health Services, Dominase Hospital, as an Accountant from 2003 to 2007. He became the Head of Finance and Administration from 2008 to 2010, during which he served as the Supervising Accountant for four Hospitals and five Clinics under the Adventist Health Services (AHS) in the Ashanti Region.', 'textarea'],
            ['bio_paragraph_4', 'He was appointed Treasurer of Valley View University from 2011 to 2015, served as Deputy Finance Officer of the University from 2016 to 2017, and later became the Director of Internal Audit from 2018 to 2025. He is currently the Chief Finance Officer of the University.', 'textarea'],
            ['bio_paragraph_5', 'Dr. Osei-Kuffour has authored and published many academic and business articles in prestigious journals and newspapers. His publications focus on the financial sustainability of businesses in both the public and private sectors. He is married to Zipporah and they have four children.', 'textarea'],
            ['highlight_1_title', 'Years of Experience', 'text'],
            ['highlight_1_text',  'Over 27 years across education, health services administration, treasury, internal audit and university finance.', 'textarea'],
            ['highlight_2_title', 'Areas of Expertise', 'text'],
            ['highlight_2_text',  'Accounting, auditing, financial management, budgeting and financial reporting.', 'textarea'],
        ]],

        // ---- Career timeline ----
        'career_timeline' => ['type' => 'section', 'order' => 3, 'fields' => [
            ['section_label',       'A Career in Finance', 'text'],
            ['section_title',       'Professional Journey', 'text'],
            ['section_description', 'More than two and a half decades of service, from the classroom to the stewardship of a chartered university\'s finances.', 'textarea'],

            ['step_1_year',  'Early Career', 'text'],
            ['step_1_title', 'Basic Education Teaching', 'text'],
            ['step_1_text',  'Four years of teaching at the Basic Educational Level before entering professional practice.', 'textarea'],

            ['step_2_year',  '2003 - 2007', 'text'],
            ['step_2_title', 'Accountant, Dominase Hospital', 'text'],
            ['step_2_text',  'Accountant with the Ghana Adventist Health Services at Dominase Hospital.', 'textarea'],

            ['step_3_year',  '2008 - 2010', 'text'],
            ['step_3_title', 'Head of Finance & Administration', 'text'],
            ['step_3_text',  'Supervising Accountant for four hospitals and five clinics under Adventist Health Services in the Ashanti Region.', 'textarea'],

            ['step_4_year',  '2011 - 2015', 'text'],
            ['step_4_title', 'Treasurer, Valley View University', 'text'],
            ['step_4_text',  'Appointed Treasurer of the University, with oversight of treasury operations.', 'textarea'],

            ['step_5_year',  '2016 - 2017', 'text'],
            ['step_5_title', 'Deputy Finance Officer', 'text'],
            ['step_5_text',  'Supported the financial management and reporting functions of the University.', 'textarea'],

            ['step_6_year',  '2018 - 2025', 'text'],
            ['step_6_title', 'Director of Internal Audit', 'text'],
            ['step_6_text',  'Led the internal audit function, strengthening controls, compliance and accountability.', 'textarea'],

            ['step_7_year',  '2025 - Present', 'text'],
            ['step_7_title', 'Chief Finance Officer', 'text'],
            ['step_7_text',  'Currently responsible for the financial leadership and stewardship of Valley View University.', 'textarea'],
        ]],

        // ---- Qualifications & memberships ----
        'credentials' => ['type' => 'section', 'order' => 4, 'fields' => [
            ['section_label',       'Qualifications', 'text'],
            ['section_title',       'Academic & Professional Standing', 'text'],
            ['section_description', 'A grounding in accounting and finance backed by fellowship and membership of Ghana\'s and the United Kingdom\'s leading professional bodies.', 'textarea'],

            ['academic_title',   'Academic Qualifications', 'text'],
            ['academic_1_title', 'PhD, Accounting & Finance', 'text'],
            ['academic_1_text',  'Adventist University of the Philippines', 'text'],
            ['academic_2_title', 'MBA, Accounting & Finance', 'text'],
            ['academic_2_text',  'University of Professional Studies', 'text'],
            ['academic_3_title', 'BSc, Applied Accounting', 'text'],
            ['academic_3_text',  'Oxford Brookes University, London', 'text'],
            ['academic_4_title', "Professional Teachers' Certificate A", 'text'],
            ['academic_4_text',  'Ghana', 'text'],

            ['memberships_title', 'Professional Bodies', 'text'],
            ['membership_1', 'Fellow, Association of Chartered Certified Accountants (ACCA)', 'text'],
            ['membership_2', 'Member, Institute of Chartered Accountants, Ghana (ICAG)', 'text'],
            ['membership_3', 'Member, Institute of Internal Auditors, Ghana (IIAG)', 'text'],
            ['membership_4', 'Chartered Certified Accounting Technician (CAT), London', 'text'],
            ['membership_5', 'Member, Chartered Institute of Taxation, Ghana (CITG)', 'text'],
        ]],

        // ---- Service beyond the office ----
        'professional_service' => ['type' => 'section', 'order' => 5, 'fields' => [
            ['section_label', 'Beyond the Office', 'text'],
            ['section_title', 'Teaching, Mentorship & Service', 'text'],

            ['service_1_icon',  'cast_for_education', 'text'],
            ['service_1_title', 'Supportive Lecturer', 'text'],
            ['service_1_text',  'Teaches at the School of Business, Valley View University.', 'textarea'],

            ['service_2_icon',  'supervisor_account', 'text'],
            ['service_2_title', 'Student Mentor', 'text'],
            ['service_2_text',  'Oxford Brookes University Student-Mentor, supervising Students\' Research Analysis Projects (RAP).', 'textarea'],

            ['service_3_icon',  'savings',            'text'],
            ['service_3_title', 'Treasurer, AHSRAA', 'text'],
            ['service_3_text',  'Treasurer of the Adventist Human Subject Research Association of Africa.', 'textarea'],

            ['service_4_icon',  'receipt_long',       'text'],
            ['service_4_title', 'Financial Secretary, ICAG Adentan', 'text'],
            ['service_4_text',  'Financial Secretary of the Adentan District Society of the Institute of Chartered Accountants, Ghana.', 'textarea'],

            ['service_5_icon',  'menu_book',          'text'],
            ['service_5_title', 'Published Author', 'text'],
            ['service_5_text',  'Academic and business articles in prestigious journals and newspapers, focused on the financial sustainability of businesses in the public and private sectors.', 'textarea'],

            ['service_6_icon',  'record_voice_over',  'text'],
            ['service_6_title', 'Resource Person', 'text'],
            ['service_6_text',  'Facilitates in accounting, auditing, financial management, budgeting and financial reporting.', 'textarea'],
        ]],

        // ---- What the office does ----
        'office_mandate' => ['type' => 'section', 'order' => 6, 'fields' => [
            ['section_label',       'The Office', 'text'],
            ['section_title',       'Mandate of the Office', 'text'],
            ['section_description', 'The Office of the Chief Finance Officer safeguards the financial health of the University and ensures its resources are used prudently in service of the academic mission.', 'textarea'],

            ['item_1_icon',  'account_balance_wallet', 'text'],
            ['item_1_title', 'Budget Management', 'text'],
            ['item_1_text',  'Strategic allocation of resources to support academic programmes, infrastructure and operations.', 'textarea'],

            ['item_2_icon',  'summarize',              'text'],
            ['item_2_title', 'Financial Reporting', 'text'],
            ['item_2_text',  'Transparent and accurate financial records that meet national and international standards.', 'textarea'],

            ['item_3_icon',  'savings',                'text'],
            ['item_3_title', 'Treasury & Cash Flow', 'text'],
            ['item_3_text',  'Management of University funds, receipts, disbursements and working capital.', 'textarea'],

            ['item_4_icon',  'verified_user',          'text'],
            ['item_4_title', 'Controls & Compliance', 'text'],
            ['item_4_text',  'Robust internal controls, statutory compliance and risk mitigation across all financial operations.', 'textarea'],

            ['item_5_icon',  'shopping_cart_checkout', 'text'],
            ['item_5_title', 'Procurement & Payroll', 'text'],
            ['item_5_text',  'Oversight of purchasing, supplier payments and the timely administration of staff emoluments.', 'textarea'],

            ['item_6_icon',  'trending_up',            'text'],
            ['item_6_title', 'Financial Sustainability', 'text'],
            ['item_6_text',  'Long-term planning and prudent investment to secure the future of the institution.', 'textarea'],
        ]],

        // ---- Related offices ----
        'related_offices' => ['type' => 'section', 'order' => 7, 'fields' => [
            ['section_label', 'Explore Further', 'text'],
            ['section_title', 'Related Offices & Resources', 'text'],

            ['link_1_icon',  'account_balance', 'text'],
            ['link_1_title', 'Office of the Vice-Chancellor', 'text'],
            ['link_1_text',  'Executive leadership and the strategic direction of the University.', 'textarea'],
            ['link_1_url',   'office_of_the_vice_chancellor.php', 'text'],

            ['link_2_icon',  'payments',        'text'],
            ['link_2_title', 'Fees Structure', 'text'],
            ['link_2_text',  'Tuition and other charges for all programmes and campuses.', 'textarea'],
            ['link_2_url',   'fees-structure.php', 'text'],

            ['link_3_icon',  'smartphone',      'text'],
            ['link_3_title', 'Mobile Money Fee Payment', 'text'],
            ['link_3_text',  'Pay your fees conveniently using mobile money.', 'textarea'],
            ['link_3_url',   'mobile_money_fee_payment.php', 'text'],

            ['link_4_icon',  'badge',           'text'],
            ['link_4_title', 'Office of the Registrar', 'text'],
            ['link_4_text',  'Admissions, records, examinations and general administration.', 'textarea'],
            ['link_4_url',   'office_of_the_registrar.php', 'text'],
        ]],

        // ---- Contact ----
        'office_contact' => ['type' => 'section', 'order' => 8, 'fields' => [
            ['section_label',       'Get in Touch', 'text'],
            ['section_title',       'Contact the Office', 'text'],
            ['section_description', 'For financial inquiries, budget matters, fee questions or administrative correspondence, the finance team is glad to assist.', 'textarea'],
            ['email',               'cfo@vvu.edu.gh', 'text'],
            ['phone',               '+233 (0) 302 501 101', 'text'],
            ['office_location',     'Finance Office, Administration Block, Oyibi', 'text'],
            ['postal_address',      'P. O. Box AF 595, Adentan, Accra, Ghana', 'text'],
            ['office_hours',        'Monday - Thursday, 8:00am - 5:00pm | Friday, 8:00am - 12:00pm', 'text'],
            ['map_url',             'https://maps.google.com/?q=Valley+View+University+Oyibi', 'text'],
            ['form_title',          'Financial Inquiry', 'text'],
            ['form_description',    'Send your financial question or request for information to the office. You can expect a response within three working days.', 'textarea'],
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
<title>Install - Chief Finance Officer Content</title>
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
    <h1>Chief Finance Officer &mdash; Content Refresh</h1>
    <p class="sub">Installs the profile of Dr. Francis Osei-Kuffour and the supporting sections.</p>
    <?php foreach ($log as $row): ?>
        <div class="row <?php echo $row[0]; ?>"><?php echo $row[1]; ?></div>
    <?php endforeach; ?>
    <a class="btn" href="admin/manage_administration_pages.php">Open Admin Manager</a>
    <a class="btn alt" href="office_of_the_cfo.php">View the Page</a>
</div>
</body>
</html>
