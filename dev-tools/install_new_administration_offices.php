<?php
/**
 * Installation Script for New Administration Office Pages
 * Valley View University
 * 
 * This script adds four new administration office pages to the database:
 * 1. Office of the Chief Finance Officer (CFO)
 * 2. Office of Research, Development, and International Relations (RDIR)
 * 3. Office of the Dean of Students' Life and Services (DSLS)
 * 4. Office of the Dean of Spiritual Life and Development (SLS)
 */

require_once 'includes/db_connect.php';

echo "<!DOCTYPE html>
<html>
<head>
    <title>Install New Administration Offices</title>
    <style>
        body { font-family: Arial, sans-serif; max-width: 1200px; margin: 50px auto; padding: 20px; }
        .success { color: green; padding: 10px; background: #e8f5e9; margin: 10px 0; border-radius: 5px; }
        .error { color: red; padding: 10px; background: #ffebee; margin: 10px 0; border-radius: 5px; }
        .info { color: blue; padding: 10px; background: #e3f2fd; margin: 10px 0; border-radius: 5px; }
        h1 { color: #1976d2; }
        h2 { color: #424242; margin-top: 30px; }
    </style>
</head>
<body>
<h1>Installing New Administration Office Pages</h1>";

try {
    $pdo->beginTransaction();
    
    // Step 1: Insert new pages
    echo "<h2>Step 1: Creating Page Records</h2>";
    
    $pages = [
        ['office_of_the_cfo', 'Office of the Chief Finance Officer', 'Office of the Chief Finance Officer'],
        ['office_of_rdir', 'Office of Research, Development, and International Relations', 'Office of Research, Development, and International Relations'],
        ['office_of_dsls', 'Office of the Dean of Students\' Life and Services', 'Office of the Dean of Students\' Life and Services'],
        ['office_of_sls', 'Office of the Dean of Spiritual Life and Development', 'Office of the Dean of Spiritual Life and Development']
    ];
    
    $pageIds = [];
    foreach ($pages as $page) {
        $stmt = $pdo->prepare("INSERT IGNORE INTO administration_pages (page_slug, page_title, page_name) VALUES (?, ?, ?)");
        $stmt->execute($page);
        
        // Get the page ID
        $stmt = $pdo->prepare("SELECT id FROM administration_pages WHERE page_slug = ?");
        $stmt->execute([$page[0]]);
        $pageIds[$page[0]] = $stmt->fetchColumn();
        
        echo "<div class='success'>✓ Created page: {$page[1]} (ID: {$pageIds[$page[0]]})</div>";
    }
    
    // Step 2: Create content sections for each page
    echo "<h2>Step 2: Creating Content Sections</h2>";
    
    $sections = [
        'office_of_the_cfo' => [
            ['hero', 'hero_section', 1],
            ['profile', 'cfo_profile', 2],
            ['section', 'financial_vision', 3],
            ['section', 'contact_section', 4],
            ['section', 'cta_section', 5]
        ],
        'office_of_rdir' => [
            ['hero', 'hero_section', 1],
            ['profile', 'rdir_profile', 2],
            ['section', 'research_vision', 3],
            ['section', 'contact_section', 4],
            ['section', 'cta_section', 5]
        ],
        'office_of_dsls' => [
            ['hero', 'hero_section', 1],
            ['profile', 'dsls_profile', 2],
            ['section', 'student_services', 3],
            ['section', 'contact_section', 4],
            ['section', 'cta_section', 5]
        ],
        'office_of_sls' => [
            ['hero', 'hero_section', 1],
            ['profile', 'sls_profile', 2],
            ['section', 'spiritual_programs', 3],
            ['section', 'contact_section', 4],
            ['section', 'cta_section', 5]
        ]
    ];
    
    $contentIds = [];
    foreach ($sections as $pageSlug => $pageSections) {
        $pageId = $pageIds[$pageSlug];
        $contentIds[$pageSlug] = [];
        
        foreach ($pageSections as $section) {
            $stmt = $pdo->prepare("INSERT INTO administration_content (page_id, section_type, section_key, content_order) VALUES (?, ?, ?, ?)");
            $stmt->execute([$pageId, $section[0], $section[1], $section[2]]);
            $contentIds[$pageSlug][$section[1]] = $pdo->lastInsertId();
            
            echo "<div class='info'>→ Created section: {$section[1]} for {$pageSlug}</div>";
        }
    }
    
    echo "<div class='success'>✓ All content sections created successfully</div>";
    
    // Step 3: Insert field data
    echo "<h2>Step 3: Populating Content Fields</h2>";
    
    // Helper function to insert fields
    function insertFields($pdo, $contentId, $fields) {
        foreach ($fields as $key => $value) {
            $fieldType = (strlen($value) > 100) ? 'textarea' : 'text';
            if (strpos($key, 'image') !== false || strpos($key, 'url') !== false) {
                $fieldType = strpos($key, 'image') !== false ? 'image' : 'url';
            }
            
            $stmt = $pdo->prepare("INSERT INTO administration_content_fields (content_id, field_key, field_value, field_type) VALUES (?, ?, ?, ?)");
            $stmt->execute([$contentId, $key, $value, $fieldType]);
        }
    }
    
    // CFO Content
    $cfoFields = [
        'hero_section' => [
            'badge_text' => 'Financial Leadership',
            'title_main' => 'Office of the',
            'title_highlight' => 'Chief Finance Officer',
            'subtitle' => 'Ensuring financial sustainability and strategic resource management to support the university\'s mission of academic excellence and institutional growth.',
            'background_image' => 'https://lh3.googleusercontent.com/aida-public/AB6AXuBo5kZ6ARGIXa5op7ZfwzuPd_3xc-gFuuNqLtlQhfI9FuPove2RJVSOjvla0bPKFyCQOvwkTsYTIZdrFobxFPda_ADJkaxK8QL0qmmVPAKWk_9tEnOjMndUI5kaG1-10q1H3lzodyVSzIKbkMJ7WqnJu9KTZSW1d6XFiKZSRiTidjPlL62RZcBjVtugVdJVT5ppDqxQJA6zTqKqiuG3IU5tUDZ6EebyhVcSLQd5pruhpjRWsJ4DE2gmxOgB7LP1mLj5zrE5d-hXP6bE'
        ],
        'cfo_profile' => [
            'profile_image' => 'https://via.placeholder.com/400x500/4680ff/ffffff?text=CFO',
            'name' => 'Chief Finance Officer',
            'title' => 'Chief Finance Officer',
            'section_title' => 'Profile & Expertise',
            'bio_paragraph_1' => 'The Chief Finance Officer oversees all financial operations of Valley View University, ensuring fiscal responsibility, transparency, and strategic resource allocation to support the institution\'s academic and operational goals.',
            'bio_paragraph_2' => 'With extensive experience in financial management and higher education administration, the CFO leads initiatives in budget planning, financial reporting, investment management, and compliance with regulatory requirements.',
            'experience_title' => 'Financial Expertise',
            'experience_text' => 'Strategic financial planning and institutional resource management.',
            'impact_title' => 'Fiscal Stewardship',
            'impact_text' => 'Ensuring financial sustainability and accountability.'
        ],
        'financial_vision' => [
            'section_title' => 'Financial Management Pillars',
            'section_description' => 'Our financial strategy is built on transparency, sustainability, and strategic investment in the university\'s future.',
            'pillar_1_title' => 'Budget Management',
            'pillar_1_description' => 'Strategic allocation of resources to support academic programs, infrastructure development, and operational excellence.',
            'pillar_2_title' => 'Financial Reporting',
            'pillar_2_description' => 'Maintaining transparent and accurate financial records that meet international accounting standards and regulatory requirements.',
            'pillar_3_title' => 'Investment Strategy',
            'pillar_3_description' => 'Prudent investment of university funds to generate sustainable returns and support long-term institutional growth.',
            'pillar_4_title' => 'Risk Management',
            'pillar_4_description' => 'Implementing robust financial controls and risk mitigation strategies to protect university assets and ensure compliance.'
        ],
        'contact_section' => [
            'section_title' => 'Contact the Office',
            'section_description' => 'For financial inquiries, budget matters, or administrative questions, please reach out to our finance team.',
            'email' => 'cfo@vvu.edu.gh',
            'phone' => '+233 (0) 302 501 101',
            'office_location' => 'Finance Office, Administration Block',
            'form_title' => 'Financial Inquiry',
            'form_description' => 'Submit your financial questions or requests for information.',
            'form_btn_text' => 'Submit Inquiry'
        ],
        'cta_section' => [
            'cta_title' => 'Financial Transparency &',
            'cta_highlight' => 'Accountability',
            'cta_description' => 'Committed to responsible stewardship of university resources for the benefit of our students and community.',
            'button_1_text' => 'Financial Reports',
            'button_1_url' => '#',
            'button_2_text' => 'Contact Finance',
            'button_2_url' => 'contact_us.php'
        ]
    ];
    
    foreach ($cfoFields as $section => $fields) {
        insertFields($pdo, $contentIds['office_of_the_cfo'][$section], $fields);
    }
    echo "<div class='success'>✓ CFO content populated</div>";
    
    // RDIR Content
    $rdirFields = [
        'hero_section' => [
            'badge_text' => 'Research & Global Engagement',
            'title_main' => 'Office of',
            'title_highlight' => 'Research, Development & International Relations',
            'subtitle' => 'Advancing knowledge through innovative research, fostering international partnerships, and driving institutional development for global impact.',
            'background_image' => 'https://lh3.googleusercontent.com/aida-public/AB6AXuBo5kZ6ARGIXa5op7ZfwzuPd_3xc-gFuuNqLtlQhfI9FuPove2RJVSOjvla0bPKFyCQOvwkTsYTIZdrFobxFPda_ADJkaxK8QL0qmmVPAKWk_9tEnOjMndUI5kaG1-10q1H3lzodyVSzIKbkMJ7WqnJu9KTZSW1d6XFiKZSRiTidjPlL62RZcBjVtugVdJVT5ppDqxQJA6zTqKqiuG3IU5tUDZ6EebyhVcSLQd5pruhpjRWsJ4DE2gmxOgB7LP1mLj5zrE5d-hXP6bE'
        ],
        'rdir_profile' => [
            'profile_image' => 'https://via.placeholder.com/400x500/10b981/ffffff?text=RDIR',
            'name' => 'Director of RDIR',
            'title' => 'Director, Research Development & International Relations',
            'section_title' => 'Leadership & Vision',
            'bio_paragraph_1' => 'The Office of Research, Development, and International Relations (RDIR) serves as the catalyst for academic research, institutional development, and global partnerships at Valley View University. Our mission is to promote excellence in research and foster meaningful international collaborations.',
            'bio_paragraph_2' => 'We support faculty and students in conducting impactful research, securing funding opportunities, and establishing partnerships with institutions worldwide. Through strategic initiatives, we aim to position VVU as a leading research institution in Africa.',
            'experience_title' => 'Research Excellence',
            'experience_text' => 'Promoting cutting-edge research and innovation.',
            'impact_title' => 'Global Partnerships',
            'impact_text' => 'Building international collaborations and exchange programs.'
        ],
        'research_vision' => [
            'section_title' => 'Strategic Focus Areas',
            'section_description' => 'Our office drives excellence through research support, international collaboration, and institutional development initiatives.',
            'pillar_1_title' => 'Research Support',
            'pillar_1_description' => 'Providing resources, funding, and infrastructure to support faculty and student research initiatives across all disciplines.',
            'pillar_2_title' => 'International Relations',
            'pillar_2_description' => 'Establishing and maintaining partnerships with universities and organizations worldwide to enhance academic exchange and collaboration.',
            'pillar_3_title' => 'Institutional Development',
            'pillar_3_description' => 'Driving strategic initiatives for institutional growth, capacity building, and continuous improvement in all areas of university operations.',
            'pillar_4_title' => 'Grant & Funding',
            'pillar_4_description' => 'Assisting researchers in identifying and securing external funding opportunities to support innovative research projects and programs.'
        ],
        'contact_section' => [
            'section_title' => 'Contact the Office',
            'section_description' => 'For research collaboration, partnership opportunities, or international relations inquiries, please contact our office.',
            'email' => 'rdir@vvu.edu.gh',
            'phone' => '+233 (0) 302 501 101',
            'office_location' => 'RDIR Office, Administration Block',
            'form_title' => 'Research Inquiry',
            'form_description' => 'Submit your research or partnership inquiries.',
            'form_btn_text' => 'Submit Inquiry'
        ],
        'cta_section' => [
            'cta_title' => 'Research Excellence &',
            'cta_highlight' => 'Global Impact',
            'cta_description' => 'Join us in advancing knowledge and building partnerships that create lasting impact locally and globally.',
            'button_1_text' => 'Research Opportunities',
            'button_1_url' => '#',
            'button_2_text' => 'Partner With Us',
            'button_2_url' => 'contact_us.php'
        ]
    ];
    
    foreach ($rdirFields as $section => $fields) {
        insertFields($pdo, $contentIds['office_of_rdir'][$section], $fields);
    }
    echo "<div class='success'>✓ RDIR content populated</div>";
    
    // DSLS Content
    $dslsFields = [
        'hero_section' => [
            'badge_text' => 'Student Life & Welfare',
            'title_main' => 'Office of the',
            'title_highlight' => 'Dean of Students\' Life and Services',
            'subtitle' => 'Creating a vibrant, supportive campus environment where every student can thrive academically, socially, and personally.',
            'background_image' => 'https://lh3.googleusercontent.com/aida-public/AB6AXuBo5kZ6ARGIXa5op7ZfwzuPd_3xc-gFuuNqLtlQhfI9FuPove2RJVSOjvla0bPKFyCQOvwkTsYTIZdrFobxFPda_ADJkaxK8QL0qmmVPAKWk_9tEnOjMndUI5kaG1-10q1H3lzodyVSzIKbkMJ7WqnJu9KTZSW1d6XFiKZSRiTidjPlL62RZcBjVtugVdJVT5ppDqxQJA6zTqKqiuG3IU5tUDZ6EebyhVcSLQd5pruhpjRWsJ4DE2gmxOgB7LP1mLj5zrE5d-hXP6bE'
        ],
        'dsls_profile' => [
            'profile_image' => 'https://via.placeholder.com/400x500/f59e0b/ffffff?text=DSLS',
            'name' => 'Dean of Students\' Life and Services',
            'title' => 'Dean, Students\' Life and Services',
            'section_title' => 'Mission & Commitment',
            'bio_paragraph_1' => 'The Office of the Dean of Students\' Life and Services is dedicated to creating a vibrant, supportive, and inclusive campus environment where every student can thrive academically, socially, and personally.',
            'bio_paragraph_2' => 'We provide comprehensive support services including counseling, health services, accommodation, student activities, and welfare programs to ensure holistic student development and well-being throughout their university journey.',
            'experience_title' => 'Student Support',
            'experience_text' => 'Comprehensive services for student welfare and development.',
            'impact_title' => 'Campus Life',
            'impact_text' => 'Creating vibrant and inclusive student experiences.'
        ],
        'student_services' => [
            'section_title' => 'Student Services & Support',
            'section_description' => 'Comprehensive support services designed to enhance student well-being and success.',
            'pillar_1_title' => 'Health & Wellness',
            'pillar_1_description' => 'Comprehensive health services, counseling support, and wellness programs to ensure physical and mental well-being of all students.',
            'pillar_2_title' => 'Accommodation',
            'pillar_2_description' => 'Safe, comfortable, and affordable housing options with modern facilities to create a home away from home for our students.',
            'pillar_3_title' => 'Student Activities',
            'pillar_3_description' => 'Diverse clubs, organizations, and events that foster leadership, creativity, and community engagement among students.',
            'pillar_4_title' => 'Student Welfare',
            'pillar_4_description' => 'Dedicated support for student concerns, advocacy, and resources to ensure every student has access to the help they need.'
        ],
        'contact_section' => [
            'section_title' => 'Contact the Office',
            'section_description' => 'For student support services, accommodation inquiries, or welfare concerns, please reach out to our dedicated team.',
            'email' => 'dsls@vvu.edu.gh',
            'phone' => '+233 (0) 302 501 101',
            'office_location' => 'Student Services Building',
            'form_title' => 'Student Support Request',
            'form_description' => 'Submit your questions or requests for student services.',
            'form_btn_text' => 'Submit Request'
        ],
        'cta_section' => [
            'cta_title' => 'Your Success is',
            'cta_highlight' => 'Our Priority',
            'cta_description' => 'We are committed to providing comprehensive support services that empower every student to achieve their full potential.',
            'button_1_text' => 'Student Resources',
            'button_1_url' => '#',
            'button_2_text' => 'Get Support',
            'button_2_url' => 'contact_us.php'
        ]
    ];
    
    foreach ($dslsFields as $section => $fields) {
        insertFields($pdo, $contentIds['office_of_dsls'][$section], $fields);
    }
    echo "<div class='success'>✓ DSLS content populated</div>";
    
    // SLS Content
    $slsFields = [
        'hero_section' => [
            'badge_text' => 'Spiritual Development',
            'title_main' => 'Office of the',
            'title_highlight' => 'Dean of Spiritual Life and Development',
            'subtitle' => 'Nurturing spiritual growth and character development grounded in Seventh-day Adventist values and Christian principles.',
            'background_image' => 'https://lh3.googleusercontent.com/aida-public/AB6AXuBo5kZ6ARGIXa5op7ZfwzuPd_3xc-gFuuNqLtlQhfI9FuPove2RJVSOjvla0bPKFyCQOvwkTsYTIZdrFobxFPda_ADJkaxK8QL0qmmVPAKWk_9tEnOjMndUI5kaG1-10q1H3lzodyVSzIKbkMJ7WqnJu9KTZSW1d6XFiKZSRiTidjPlL62RZcBjVtugVdJVT5ppDqxQJA6zTqKqiuG3IU5tUDZ6EebyhVcSLQd5pruhpjRWsJ4DE2gmxOgB7LP1mLj5zrE5d-hXP6bE'
        ],
        'sls_profile' => [
            'profile_image' => 'https://via.placeholder.com/400x500/8b5cf6/ffffff?text=SLS',
            'name' => 'Dean of Spiritual Life and Development',
            'title' => 'Dean, Spiritual Life and Development',
            'section_title' => 'Spiritual Leadership',
            'bio_paragraph_1' => 'The Office of the Dean of Spiritual Life and Development nurtures the spiritual growth and character development of the Valley View University community, grounded in Seventh-day Adventist values and Christian principles.',
            'bio_paragraph_2' => 'Through worship services, spiritual programs, counseling, and community outreach, we create opportunities for students and staff to deepen their faith, develop moral character, and live out their Christian calling in service to others.',
            'experience_title' => 'Spiritual Formation',
            'experience_text' => 'Guiding students in their spiritual journey and faith development.',
            'impact_title' => 'Community Service',
            'impact_text' => 'Inspiring service and outreach to the broader community.'
        ],
        'spiritual_programs' => [
            'section_title' => 'Spiritual Programs & Ministries',
            'section_description' => 'Comprehensive spiritual development programs that nurture faith, character, and service.',
            'pillar_1_title' => 'Worship Services',
            'pillar_1_description' => 'Regular chapel services, vespers, and worship gatherings that inspire spiritual growth and community fellowship.',
            'pillar_2_title' => 'Bible Study',
            'pillar_2_description' => 'Small group Bible studies and discipleship programs that deepen understanding of Scripture and Christian living.',
            'pillar_3_title' => 'Outreach Ministry',
            'pillar_3_description' => 'Community service projects and evangelistic initiatives that put faith into action through service to others.',
            'pillar_4_title' => 'Spiritual Counseling',
            'pillar_4_description' => 'Confidential spiritual guidance and pastoral care to support students through life challenges and spiritual questions.'
        ],
        'contact_section' => [
            'section_title' => 'Contact the Office',
            'section_description' => 'For spiritual guidance, pastoral care, or ministry involvement opportunities, please reach out to our chaplaincy team.',
            'email' => 'chaplain@vvu.edu.gh',
            'phone' => '+233 (0) 302 501 101',
            'office_location' => 'Chaplaincy Office, Campus Chapel',
            'form_title' => 'Spiritual Guidance Request',
            'form_description' => 'Submit your questions or requests for spiritual support.',
            'form_btn_text' => 'Submit Request'
        ],
        'cta_section' => [
            'cta_title' => 'Faith, Character &',
            'cta_highlight' => 'Service',
            'cta_description' => 'Growing together in faith and service as we prepare to make a positive impact in the world.',
            'button_1_text' => 'Ministry Programs',
            'button_1_url' => '#',
            'button_2_text' => 'Get Involved',
            'button_2_url' => 'contact_us.php'
        ]
    ];
    
    foreach ($slsFields as $section => $fields) {
        insertFields($pdo, $contentIds['office_of_sls'][$section], $fields);
    }
    echo "<div class='success'>✓ SLS content populated</div>";
    
    // Commit transaction
    $pdo->commit();
    
    echo "<h2>Installation Complete!</h2>";
    echo "<div class='success' style='font-size: 18px; padding: 20px;'>
        <strong>✓ All four administration office pages have been successfully installed!</strong><br><br>
        You can now access the pages at:<br>
        <ul style='margin-top: 10px;'>
            <li><a href='office_of_the_cfo.php' target='_blank'>Office of the Chief Finance Officer</a></li>
            <li><a href='office_of_rdir.php' target='_blank'>Office of Research, Development, and International Relations</a></li>
            <li><a href='office_of_dsls.php' target='_blank'>Office of the Dean of Students' Life and Services</a></li>
            <li><a href='office_of_sls.php' target='_blank'>Office of the Dean of Spiritual Life and Development</a></li>
        </ul>
        <br>
        Manage content at: <a href='admin/manage_administration_pages.php' target='_blank'>Admin Panel</a>
    </div>";
    
} catch (Exception $e) {
    $pdo->rollBack();
    echo "<div class='error'><strong>Error:</strong> " . $e->getMessage() . "</div>";
    echo "<div class='error'>Installation failed. Please check your database connection and try again.</div>";
}

echo "</body></html>";
?>
