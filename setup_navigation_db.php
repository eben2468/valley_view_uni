<?php
require_once('includes/db_connect.php');

/**
 * Navigation Setup Script
 * This script creates the required database tables and seeds them with the current 
 * navigation content from includes/header.php.
 */

try {
    // 1. Create Tables
    $sql = file_get_contents('sql/navigation_tables.sql');
    $pdo->exec($sql);
    echo "Tables created successfully.<br>";

    // 2. Clear existing entries to prevent duplicates during testing
    $pdo->exec("SET FOREIGN_KEY_CHECKS = 0;");
    $pdo->exec("TRUNCATE TABLE navigation_links;");
    $pdo->exec("TRUNCATE TABLE navigation_sections;");
    $pdo->exec("TRUNCATE TABLE navigation_items;");
    $pdo->exec("SET FOREIGN_KEY_CHECKS = 1;");

    // 3. Seed Main Navigation (Desktop Menu)
    // Looking at header.php, the main structure is:
    // HOME, ABOUT, ACADEMICS, ADMISSIONS, LIFE @ VVU, STORIES, RESOURCES, VENTURES
    
    $main_menu = [
        [
            'title' => 'HOME',
            'url' => 'index.php',
            'has_megamenu' => 0
        ],
        [
            'title' => 'ABOUT',
            'url' => 'javascript:void(0)',
            'has_megamenu' => 1,
            'megamenu_type' => 'about-mm',
            'sections' => [
                [
                    'title' => 'About VVU',
                    'column' => 3,
                    'links' => [
                        ['Mission and Vision', 'mission_and_vision.php'],
                        ['Core Values', 'core_values.php'],
                        ['VVU Anthem', 'vvu_anthem.php'],
                        ['Ecology', 'ecology.php'],
                        ['The VVU (The Campus)', 'the_campus.php']
                    ]
                ],
                [
                    'title' => 'Strategy & History',
                    'column' => 3,
                    'links' => [
                        ['Strategic Plan', 'strategic_plan.php'],
                        ['Policies', 'policies.php'],
                        ['Our History', 'history.php'],
                        ['Accreditation and Charter', 'accreditation_and_charter.php']
                    ]
                ],
                [
                    'title' => 'Administration',
                    'column' => 4,
                    'links' => [
                        ['Office of the Vice Chancellor', 'office_of_the_vice_chancellor.php'],
                        ['Office of the Pro-Vice Chancellor', 'office_of_the_pro-vice_chancellor.php'],
                        ['Office of the Registrar', 'office_of_the_registrar.php'],
                        ['Rectors', 'rectors.php'],
                        ['Recorders', 'recorders.php']
                    ]
                ],
                [
                    'title' => 'Directories & Contact',
                    'column' => 4,
                    'links' => [
                        ['Faculty Encyclopedia', 'faculty_encyclopedia.php'],
                        ['Staff Encyclopedia', 'staff_encyclopedia.php'],
                        ['FAQs about VVU', 'faqs_about_vvu.php']
                    ]
                ],
                [
                    'title' => 'Sidebar Info',
                    'column' => 2,
                    'type' => 'description',
                    'description' => "Valley View University - Ghana's first chartered private university.",
                    'button_text' => 'Learn More',
                    'button_link' => ''
                ],
                 [
                    'title' => 'Featured Image',
                    'column' => 1,
                    'type' => 'featured',
                    'featured_image' => 'Education-Website-and-AdminPanel/images/h-about.jpg',
                    'featured_link' => 'the_campus.php',
                    'featured_text' => 'The VVU'
                ]
            ]
        ],
        [
            'title' => 'ACADEMICS',
            'url' => 'javascript:void(0)',
            'has_megamenu' => 1,
            'megamenu_type' => 'admi-mm',
            'sections' => [
                [
                    'title' => 'Academic Overview',
                    'column' => 3,
                    'links' => [
                        ['Get Started', 'admissions.php'],
                        ['Academic Programs', 'academic_programs_overview.php'],
                        ['Centres and Campuses', 'the_campus.php'],
                        ['Learning Outcomes', 'learning_outcomes.php']
                    ]
                ],
                [
                    'title' => 'Resources',
                    'column' => 3,
                    'links' => [
                        ['VVU Library', 'library_resources.php'],
                        ['Journals', 'journals.php'],
                        ['Student Handbook', 'student_handbook.php'],
                        ['Academic Bulletin', 'academic_bulletin.php'],
                        ['Academic Calendar', 'academic_calendar.php'],
                        ['Sandwich Calendar', 'sandwich_calendar.php']
                    ]
                ],
                [
                    'title' => 'Schools & Faculties',
                    'column' => 4,
                    'links' => [
                        ['Faculty of Science', 'faculty_of_science.php'],
                        ['School of Business', 'academic_programs_overview.php'],
                        ['Faculty of Arts & Social Science', 'academic_programs_overview.php'],
                        ['School of Nursing and Midwifery', 'academic_programs_overview.php'],
                        ['School of Graduate Studies', 'academic_programs_overview.php'],
                        ['School of Theology & Missions', 'academic_programs_overview.php']
                    ]
                ],
                [
                    'title' => 'Sidebar Info',
                    'column' => 2,
                    'type' => 'description',
                    'description' => "Explore our diverse range of academic schools and faculties offering world-class education.",
                    'button_text' => 'View All',
                    'button_link' => 'academic_programs_overview.php'
                ],
                [
                    'title' => 'Featured Image',
                    'column' => 1,
                    'type' => 'featured',
                    'featured_image' => 'Education-Website-and-AdminPanel/images/h-about1.jpg',
                    'featured_link' => '',
                    'featured_text' => 'Academics'
                ]
            ]
        ],
        [
            'title' => 'ADMISSIONS',
            'url' => 'javascript:void(0)',
            'has_megamenu' => 1,
            'megamenu_type' => 'admi-mm',
            'sections' => [
                [
                    'title' => 'Admissions Info',
                    'column' => 3,
                    'links' => [
                        ['Provisional Admissions List', '#'],
                        ['Entry Requirements', 'entry-requirement.php'],
                        ['Caution to Applicants', 'caution-to-applicants.php'],
                        ['Scholarships', 'scholarships.php'],
                        ['Scholarship Forms', 'scholarships-forms.php']
                    ]
                ],
                [
                    'title' => 'Application & Resources',
                    'column' => 4,
                    'links' => [
                        ['Fee Structure', 'fees-structure.php'],
                        ['Why Choose VVU', 'why_choose_vvu.php'],
                        ['Apply Online', 'https://admissions.vvu.edu.gh/'],
                        ['Downloads Forms', 'download-forms.php'],
                        ['Mature Entrance', 'mature-entrance.php'],
                        ['Degree and Diploma in Music', 'degree_and_diploma_in_music.php']
                    ]
                ],
                [
                    'title' => 'Sidebar Info',
                    'column' => 2,
                    'type' => 'description',
                    'description' => "Begin your journey with our undergraduate and postgraduate programs. Discover entry requirements.",
                    'button_text' => 'Apply Now',
                    'button_link' => 'admissions.php'
                ],
                [
                    'title' => 'Featured Image',
                    'column' => 1,
                    'type' => 'featured',
                    'featured_image' => 'Education-Website-and-AdminPanel/images/h-adm1.jpg',
                    'featured_link' => '',
                    'featured_text' => 'Admissions'
                ]
            ]
        ],
        [
            'title' => 'LIFE @ VVU',
            'url' => 'javascript:void(0)',
            'has_megamenu' => 1,
            'megamenu_type' => 'admi-mm',
            'sections' => [
                [
                    'title' => 'Campus Life',
                    'column' => 3,
                    'links' => [
                        ['Philosophy on Dress', 'philosophy_on_dress.php'],
                        ['SRC', 'https://src.vvu.edu.gh/'],
                        ['Accommodation', 'accommodation.php'],
                        ['Food Services', 'food_services.php'],
                        ['Work Study', 'work_study.php'],
                        ['Spiritual Life and Development', 'sld.php']
                    ]
                ],
                [
                    'title' => 'Gallery',
                    'column' => 3,
                    'links' => [
                        ['Gallery', 'gallery.php'],
                        ['News Gallery', 'news_gallery.php'],
                        ['Strategic Planning - 2025', 'strategic_planning.php'],
                        ['SRC Gallery', 'src_gallery.php']
                    ]
                ],
                [
                    'title' => 'Student Associations',
                    'column' => 4,
                    'links' => [
                        ['COSSA', '#'],
                        ['THEMSA', '#'],
                        ['NURSA', '#'],
                        ['SOBSA', '#'],
                        ['EDSA', '#'],
                        ['BMEDSA', '#'],
                        ['ISA', '#'],
                        ['DESSA', '#']
                    ]
                ],
                [
                    'title' => 'Sidebar Info',
                    'column' => 2,
                    'type' => 'description',
                    'description' => "Experience vibrant campus life with activities, clubs, and a supportive community.",
                    'button_text' => 'Explore',
                    'button_link' => 'student_life.php'
                ],
                [
                    'title' => 'Featured Image',
                    'column' => 1,
                    'type' => 'featured',
                    'featured_image' => 'Education-Website-and-AdminPanel/images/h-cam.jpg',
                    'featured_link' => '',
                    'featured_text' => 'Student Life'
                ]
            ]
        ],
        [
            'title' => 'STORIES',
            'url' => 'javascript:void(0)',
            'has_megamenu' => 1,
            'megamenu_type' => 'about-mm',
            'sections' => [
                [
                    'title' => 'Stories & Updates',
                    'column' => 3,
                    'links' => [
                        ['Events', 'events.php'],
                        ['Notices', 'notices.php'],
                        ['News', 'news_&_events.php'],
                        ['Valley View Radio', 'vvu_radio.php']
                    ]
                ],
                 [
                    'title' => 'Gallery',
                    'column' => 4,
                    'links' => [
                        ['Photo Gallery', 'gallery.php'],
                        ['News Gallery', 'news_gallery.php']
                    ]
                ],
                [
                    'title' => 'Sidebar Info',
                    'column' => 2,
                    'type' => 'description',
                    'description' => "Stay updated with the latest news, events and announcements from Valley View University community.",
                    'button_text' => 'Read more',
                    'button_link' => 'news_&_events.php'
                ],
                [
                    'title' => 'Featured Image',
                    'column' => 1,
                    'type' => 'featured',
                    'featured_image' => 'Education-Website-and-AdminPanel/images/h-res1.jpg',
                    'featured_link' => 'news_&_events.php',
                    'featured_text' => 'Latest News'
                ]
            ]
        ],
        [
            'title' => 'RESOURCES',
            'url' => 'javascript:void(0)',
            'has_megamenu' => 1,
            'megamenu_type' => 'admi-mm',
            'sections' => [
                [
                    'title' => 'Information For',
                    'column' => 3,
                    'links' => [
                        ['Freshmen Info', 'freshmen_info.php'],
                        ['New To VVU', 'new_to_vvu.php'],
                        ['Take A Tour', 'take_a_tour.php'],
                        ['Download Our Forms', 'download-forms.php']
                    ]
                ],
                [
                    'title' => 'Current Students',
                    'column' => 3,
                    'links' => [
                        ['Mobile Money Fee Payment', 'mobile_money_fee_payment.php'],
                        ['Student Email', 'student-email.php'],
                        ['iSchool', 'iSchool.php'],
                        ['E-Learning', 'https://elearning.vvu.edu.gh/']
                    ]
                ],
                [
                    'title' => 'Faculty & Staff',
                    'column' => 4,
                    'links' => [
                        ['University Policies', 'policies.php'],
                        ['iSchool', 'https://ischool.vvu.edu.gh/Default.aspx'],
                        ['E-Learning', 'https://elearning.vvu.edu.gh/'],
                        ['Faculty And Staff Forms', 'faculty_and_staff_forms.php']
                    ]
                ],
                 [
                    'title' => 'Employment & General',
                    'column' => 4,
                    'links' => [
                        ['Employment Opportunity', 'employment_opportunity.php'],
                        ['Employment Application Form', 'uploads/employment-forma.pdf'],
                        ['Downloads', '#'],
                        ['IBC Abstracts', '#'],
                        ['E-Learning Materials', '#']
                    ]
                ],
                [
                    'title' => 'Sidebar Info',
                    'column' => 2,
                    'type' => 'description',
                    'description' => "Access student portals, e-learning platforms, handbooks and fee payment systems.",
                    'button_text' => 'View Resources',
                    'button_link' => 'freshmen_info.php'
                ],
                [
                    'title' => 'Featured Image',
                    'column' => 1,
                    'type' => 'featured',
                    'featured_image' => 'Education-Website-and-AdminPanel/images/h-cam.jpg',
                    'featured_link' => 'freshmen_info.php',
                    'featured_text' => 'Resources'
                ]
            ]
        ],
        [
            'title' => 'VENTURES',
            'url' => 'javascript:void(0)',
            'has_megamenu' => 1,
            'megamenu_type' => 'admi-mm',
            'sections' => [
                [
                    'title' => 'Production',
                    'column' => 3,
                    'links' => [
                        ['Bakery Factory', 'bakery_factory_page.php'],
                        ['Water Factory', 'water_factory.php'],
                        ['Grocery', 'grocery.php']
                    ]
                ],
                 [
                    'title' => 'Services',
                    'column' => 4,
                    'links' => [
                        ['VVU Hospital', 'hospital.php'],
                        ['Eye Clinic', 'vvu_eye_clinic.php'],
                        ['Guest House', 'guest_house.php'],
                        ['Basic Schools', 'basic_schools_page.php'],
                        ['Radio Station', 'vvu_radio.php']
                    ]
                ],
                [
                    'title' => 'Sidebar Info',
                    'column' => 2,
                    'type' => 'description',
                    'description' => "Discover our commercial ventures providing quality services to the community.",
                    'button_text' => 'Learn More',
                    'button_link' => 'bakery_factory_page.php'
                ],
                [
                    'title' => 'Featured Image',
                    'column' => 1,
                    'type' => 'featured',
                    'featured_image' => 'Education-Website-and-AdminPanel/images/h-about1.jpg',
                    'featured_link' => 'bakery_factory_page.php',
                    'featured_text' => 'Ventures'
                ]
            ]
        ],
    ];

    $item_stmt = $pdo->prepare("INSERT INTO navigation_items (title, url, has_megamenu, megamenu_type, sort_order) VALUES (?, ?, ?, ?, ?)");
    $section_stmt = $pdo->prepare("INSERT INTO navigation_sections (navigation_item_id, section_title, section_type, column_position, featured_image, featured_link, featured_text, description_text, button_text, button_link, sort_order) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
    $link_stmt = $pdo->prepare("INSERT INTO navigation_links (section_id, title, url, sort_order) VALUES (?, ?, ?, ?)");

    foreach ($main_menu as $idx => $item) {
        $item_stmt->execute([$item['title'], $item['url'], $item['has_megamenu'], $item['megamenu_type'] ?? null, $idx]);
        $item_id = $pdo->lastInsertId();

        if (isset($item['sections'])) {
            foreach ($item['sections'] as $s_idx => $section) {
                $section_stmt->execute([
                    $item_id,
                    $section['title'],
                    $section['type'] ?? 'links',
                    $section['column'],
                    $section['featured_image'] ?? null,
                    $section['featured_link'] ?? null,
                    $section['featured_text'] ?? null,
                    $section['description'] ?? null,
                    $section['button_text'] ?? null,
                    $section['button_link'] ?? null,
                    $s_idx
                ]);
                $section_id = $pdo->lastInsertId();

                if (isset($section['links'])) {
                    foreach ($section['links'] as $l_idx => $link) {
                        $link_stmt->execute([$section_id, $link[0], $link[1], $l_idx]);
                    }
                }
            }
        }
    }

    echo "Main navigation seeded successfully.<br>";

    // 4. Seed Mobile & Quick Access Menu (Briefly)
    // For now, focusing on the main navigation as requested, but we can expand this.

    echo "Database setup and seeding complete.";

} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}
?>
