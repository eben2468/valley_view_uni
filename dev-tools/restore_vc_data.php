<?php
require_once('includes/db_connect.php');

try {
    $pdo->beginTransaction();

    // Mapping of section_key to its fields for Page 1 (Vice-Chancellor)
    $data = [
        'hero_section' => [
            ['badge_text', 'University Leadership', 'text'],
            ['title_main', 'Office of the', 'text'],
            ['title_highlight', 'Vice-Chancellor', 'text'],
            ['subtitle', 'Leading Valley View University towards a future of academic excellence, spiritual growth, and societal impact through dedicated service and visionary leadership.', 'textarea'],
            ['background_image', 'https://lh3.googleusercontent.com/aida-public/AB6AXuBo5kZ6ARGIXa5op7ZfwzuPd_3xc-gFuuNqLtlQhfI9FuPove2RJVSOjvla0bPKFyCQOvwkTsYTIZdrFobxFPda_ADJkaxK8QL0qmmVPAKWk_9tEnOjMndUI5kaG1-10q1H3lzodyVSzIKbkMJ7WqnJu9KTZSW1d6XFiKZSRiTidjPlL62RZcBjVtugVdJVT5ppDqxQJA6zTqKqiuG3IU5tUDZ6EebyhVcSLQd5pruhpjRWsJ4DE2gmxOgB7LP1mLj5zrE5d-hXP6bE', 'image']
        ],
        'vc_profile' => [
            ['profile_image', 'https://lh3.googleusercontent.com/aida-public/AB6AXuCLxwRQaStcMjctmdRSUlFrbTzHrHZ4QmQ7_w-SjNu0YbuDefwcI5HThsfCdLLv2t2buwPecrNFBE0YG9eouPiuXF_v0W_iZyuf-LqyyZDM_LGTDg50yAveRJO1xUoJWTArmE9HlG_NBbpBogj3YzigfkiFnlpHCvldhseWxVTj3HaJpdFaTDwR34NqL0UJmX8pZa6aANMldz55PZSL0ZrzavkAeMjQv_pYGZbL4ObyJK-1ZZU9mW2rBo-Z6I1hzq_bCvv7QBKpvQy0', 'image'],
            ['name', 'William Kofi Koomson, PhD', 'text'],
            ['title', 'Vice-Chancellor', 'text'],
            ['bio_paragraph_1', 'William Kofi Koomson, a Ghanaian by birth, lived and worked in the Americas, including Jamaica, Trinidad and Tobago, Canada, and the United States of America for the past 30 years, after acquiring his initial secondary education in Ghana. He is married with four adult children.', 'textarea'],
            ['bio_paragraph_2', 'He has worked for the Seventh-day Adventist Church for the past 35 years as an Administrator and Departmental Director at the local Conference, Union and General Conference (Review and Herald Publishing Association) levels, Vice Principal for the Literature Ministry Seminary, University Professor, College Principal/Rector and Pastor.', 'textarea'],
            ['bio_paragraph_3', 'As part of his evangelistic outreach, he spearheaded a Community Sharing Ministry program in the USA, Canada and Europe to distribute within seven years over 250,000 copies of the book, Steps to Christ. He led in establishing two Churches in North America and through his evangelistic efforts, more than 1510 souls have been baptized into the Seventh-day Adventist Church.', 'textarea'],
            ['experience_text', '35+ Years of Service in the Seventh-day Adventist Church.', 'text'],
            ['impact_text', 'Extensive international experience across the Americas and Europe.', 'text']
        ],
        'strategic_vision' => [
            ['section_title', 'Strategic Vision', 'text'],
            ['section_subtitle', 'Under the leadership of Dr. William Kofi Koomson, the Office of the Vice-Chancellor is focused on four key pillars of transformation.', 'textarea'],
            ['pillar_1_title', 'Academic Excellence', 'text'],
            ['pillar_1_description', 'Enhancing the quality of teaching and learning through innovative curricula and world-class faculty development.', 'textarea'],
            ['pillar_2_title', 'Research & Innovation', 'text'],
            ['pillar_2_description', 'Fostering a culture of research that addresses local and global challenges through interdisciplinary collaboration.', 'textarea'],
            ['pillar_3_title', 'Community Engagement', 'text'],
            ['pillar_3_description', 'Strengthening our impact on society through meaningful service, outreach, and strategic partnerships.', 'textarea'],
            ['pillar_4_title', 'Spiritual Growth', 'text'],
            ['pillar_4_description', 'Nurturing the spiritual well-being of our community, grounded in the values of the Seventh-day Adventist Church.', 'textarea']
        ],
        'contact_section' => [
            ['section_title', 'Contact the Office', 'text'],
            ['section_description', 'For official inquiries, scheduling, or administrative matters, please reach out to our dedicated team.', 'textarea'],
            ['email', 'vc@vvu.edu.gh', 'text'],
            ['phone', '+233 (0) 302 501 101', 'text'],
            ['office_location', 'Administration Block, Oyibi Campus', 'text']
        ],
        'cta_section' => [
            ['cta_title', 'Building a Legacy of', 'text'],
            ['cta_highlight', 'Excellence Together', 'text'],
            ['cta_description', 'Join us in our mission to transform lives through quality Christian education and dedicated service to humanity.', 'textarea'],
            ['button_1_text', 'About the University', 'text'],
            ['button_1_url', 'about_us.php', 'text'],
            ['button_2_text', 'Get in Touch', 'text'],
            ['button_2_url', 'contact_us.php', 'text']
        ]
    ];

    foreach ($data as $section_key => $fields) {
        // Find the section ID for page 1
        $stmt = $pdo->prepare("SELECT id FROM administration_content WHERE page_id = 1 AND section_key = ?");
        $stmt->execute([$section_key]);
        $section_id = $stmt->fetchColumn();

        if ($section_id) {
            echo "Processing section: $section_key (ID: $section_id)\n";
            foreach ($fields as $field) {
                list($field_key, $field_value, $field_type) = $field;
                
                // Use INSERT ... ON DUPLICATE KEY UPDATE since I added the unique constraint
                $stmt2 = $pdo->prepare("INSERT INTO administration_content_fields (content_id, field_key, field_value, field_type) 
                                        VALUES (?, ?, ?, ?) 
                                        ON DUPLICATE KEY UPDATE field_value = VALUES(field_value), field_type = VALUES(field_type)");
                $stmt2->execute([$section_id, $field_key, $field_value, $field_type]);
            }
        } else {
            echo "Warning: Section $section_key not found for page 1\n";
        }
    }

    $pdo->commit();
    echo "Restoration complete!\n";

} catch (Exception $e) {
    if ($pdo->inTransaction()) $pdo->rollBack();
    echo "Error: " . $e->getMessage() . "\n";
}
