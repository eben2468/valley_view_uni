<?php
require_once('includes/db_connect.php');

// 1. Seed Categories
// The School of Nursing and Midwifery now sits under the Faculty of Science, and
// the Department of Teacher Education (formerly the School of Education) under
// the Faculty of Arts & Social Sciences, so neither is seeded as its own category.
$categories = [
    'Professional Courses' => ['workspace_premium', '#f59e0b', '#d97706'],
    'Center for Adult and Continuing Education' => ['school', '#10b981', '#059669'],
    'School of Business' => ['business_center', '#3b82f6', '#2563eb'],
    'Faculty of Science' => ['science', '#8b5cf6', '#7c3aed'],
    'School of Graduate Studies' => ['psychology', '#06b6d4', '#0891b2'],
    'Faculty of Arts & Social Sciences' => ['palette', '#f97316', '#ea580c']
];

$cat_map = [];
foreach ($categories as $name => $data) {
    $stmt = $pdo->prepare("INSERT INTO program_categories (name, icon, color_1, color_2) VALUES (?, ?, ?, ?)");
    $stmt->execute([$name, $data[0], $data[1], $data[2]]);
    $cat_map[$name] = $pdo->lastInsertId();
}

// 2. Seed Programs from JSON
$json = file_get_contents('data/courses.json');
$courses = json_decode($json, true);

foreach ($courses as $course) {
    $cat_id = $cat_map[$course['category']] ?? null;
    
    // Default learning points and career paths
    $learning_points = ["Advanced theoretical frameworks", "Practical problem-solving", "Effective communication", "Research methodologies"];
    $career_paths = ["Professional in " . $course['category'], "Researcher", "Consultant", "Entrepreneur", "Academic Specialist"];
    
    $stmt = $pdo->prepare("INSERT INTO academic_programs (category_id, title, description, full_description, link_url, learning_points, career_paths) VALUES (?, ?, ?, ?, ?, ?, ?)");
    $stmt->execute([
        $cat_id,
        $course['title'],
        $course['description'],
        $course['description'] . " The " . $course['title'] . " at Valley View University is designed to provide students with a deep understanding of the core principles and advanced practices in " . $course['category'] . ".",
        $course['link'] ?? '',
        json_encode($learning_points),
        json_encode($career_paths)
    ]);
}

// 3. Seed Page Content
$pdo->prepare("INSERT INTO academic_programs_page_content (section_key, hero_title, hero_subtitle, hero_badge, hero_image, cta_title, cta_subtitle) VALUES (?, ?, ?, ?, ?, ?, ?)")
    ->execute([
        'overview',
        'Explore Our <br> <span class=\"text-transparent bg-clip-text bg-gradient-to-r from-yellow-400 via-yellow-300 to-orange-400\">Programs</span>',
        'Discover world-class undergraduate and postgraduate programs designed to empower your future',
        'Academic Excellence',
        'vvu_academic_programs_hero_1766877091510.png',
        'Ready to Start Your Journey?',
        'Apply online today and take the first step towards your future with Valley View University'
    ]);

// 4. Seed Stats
$stats = [
    ['count($courses)+', 'Programs'],
    ['count($categories)', 'Faculties'],
    ['100%', 'Accredited'],
    ['1979', 'Established']
];

// Note: Re-calculating counts for accurate initial seeding
$program_count = count($courses);
$faculty_count = count($categories);

$stats = [
    [$program_count . '+', 'Programs'],
    [$faculty_count, 'Faculties'],
    ['100%', 'Accredited'],
    ['1979', 'Established']
];

foreach ($stats as $index => $stat) {
    $pdo->prepare("INSERT INTO academic_programs_stats (stat_value, stat_label, display_order) VALUES (?, ?, ?)")
        ->execute([$stat[0], $stat[1], $index]);
}

echo "Data seeded successfully.\n";
?>
