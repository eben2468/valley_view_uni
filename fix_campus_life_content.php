<?php
/**
 * Fix script to ensure all campus life pages have default content
 */

require_once 'includes/db_connect.php';

echo "<!DOCTYPE html>
<html>
<head>
    <title>Fix Campus Life Content</title>
    <style>
        body { font-family: Arial, sans-serif; padding: 20px; background: #f5f5f5; }
        .container { max-width: 1000px; margin: 0 auto; background: white; padding: 30px; border-radius: 10px; }
        h1 { color: #333; }
        .success { color: #4CAF50; font-weight: bold; }
        .error { color: #f44336; font-weight: bold; }
        .info { background: #e3f2fd; padding: 15px; margin: 10px 0; border-left: 4px solid #2196F3; }
        .btn { display: inline-block; padding: 10px 20px; background: #4CAF50; color: white; text-decoration: none; border-radius: 5px; margin: 10px 5px; }
    </style>
</head>
<body>
<div class='container'>";

echo "<h1>🔧 Fix Campus Life Content</h1>";

// Check and insert/update content for each page
$pages = [
    'accommodation_content' => [
        'hero_title' => 'Accommodation',
        'hero_subtitle' => 'Comfortable and secure housing for students on campus',
        'hero_image' => 'images/accommodation_hero.jpg',
        'intro_heading' => 'Campus Housing',
        'intro_text' => 'Valley View University provides quality accommodation facilities for students who wish to live on campus. Our residence halls offer a safe, comfortable, and conducive environment for academic success and personal growth.',
        'intro_image' => '',
        'facilities_description' => 'Our residence halls offer modern amenities and a safe environment.',
        'room_types_description' => 'Various room types available to suit different needs and budgets.',
        'application_process' => 'Apply through the student portal during registration.',
        'rules_and_regulations' => 'All residents must adhere to university housing policies.',
        'cta_heading' => 'Ready to Apply?',
        'cta_text' => 'Contact the Housing Office for more information about accommodation options.',
        'status' => 'active'
    ],
    'food_services_content' => [
        'hero_title' => 'Nourishing Body & Mind',
        'hero_subtitle' => 'Experience wholesome, vegetarian cuisine prepared with care for our university community',
        'hero_image' => 'images/cafeteria_interior.png',
        'philosophy_heading' => 'A Healthy Mind Starts with a Healthy Plate',
        'philosophy_text' => 'At Valley View University, we believe that physical well-being is the foundation of academic and spiritual growth. Our cafeteria is dedicated to providing balanced, nutritious, and delicious vegetarian meals that fuel your journey.',
        'philosophy_image' => 'images/vegetarian_meal.png',
        'breakfast_time' => '6:30 - 8:30',
        'lunch_time' => '10:00 - 2:00',
        'dinner_time' => '4:00 - 6:00',
        'meal_plans_description' => 'Whether you live on campus or commute, we have a meal plan that fits your lifestyle and budget.',
        'feedback_heading' => 'How are we doing?',
        'feedback_text' => 'Your feedback helps us improve. Tell us about your dining experience or suggest new menu items.',
        'status' => 'active'
    ],
    'work_study_content' => [
        'hero_title' => 'Work Study Program',
        'hero_subtitle' => 'Learn, Work, and Grow - Experience holistic development through meaningful campus employment opportunities',
        'hero_image' => 'https://images.unsplash.com/photo-1522202176988-66273c2fd55f?auto=format&fit=crop&q=80&w=1920',
        'overview_heading' => 'Campus Employment Philosophy',
        'overview_text' => 'In keeping with the Seventh-day Adventist philosophy of education, which emphasizes the development of the physical nature of humanity, Valley View University provides varied opportunities for students to work in campus-related industries.',
        'overview_image' => 'https://images.unsplash.com/photo-1522071820081-009f0129c71c?auto=format&fit=crop&q=80&w=1200',
        'minimum_hours' => 12,
        'spouse_policy_text' => 'If a student\'s spouse is employed in the Work Study Programme at Valley View University to support the student financially, the spouse\'s employment may be terminated once the student has graduated and ceased to be a registered student at the university.',
        'application_process' => 'Visit the Student Employment Office on campus to complete the work study application form.',
        'cta_heading' => 'Ready to Work & Learn?',
        'cta_text' => 'Join our work study program and experience the value of combining academic excellence with practical work experience.',
        'status' => 'active'
    ],
    'sld_content' => [
        'hero_title' => 'Spiritual Life & Development',
        'hero_subtitle' => 'Nurturing faith, character, and purpose in every student\'s journey',
        'hero_image' => 'https://images.unsplash.com/photo-1438232992991-995b7058bbb3?auto=format&fit=crop&q=80&w=1920',
        'welcome_heading' => 'Welcome to SLD Office',
        'welcome_text' => 'The Spiritual Life and Development office is committed to fostering holistic growth through spiritual guidance, counseling, and ministry programs that strengthen faith and character.',
        'welcome_image' => 'https://images.unsplash.com/photo-1507003211169-0a1dd7228f2d?auto=format&fit=crop&q=80&w=1200',
        'mission_statement' => 'To nurture spiritual growth, provide pastoral care, and empower students to live purpose-driven lives rooted in Christian values and service to humanity.',
        'dean_name' => 'Emmanuel H. Takyi, Ph.D, DMin',
        'dean_title' => 'Dean of Spiritual Life And Development Office',
        'dean_description' => 'Leading our vision for holistic spiritual development across the university community.',
        'cta_heading' => 'Need Spiritual Support?',
        'cta_text' => 'Our doors are always open. Whether you need counseling, prayer, or simply someone to talk to, we\'re here for you.',
        'status' => 'active'
    ]
];

foreach ($pages as $table => $data) {
    echo "<h3>Processing: $table</h3>";
    
    try {
        // Check if record exists
        $stmt = $pdo->prepare("SELECT id FROM `$table` WHERE id = 1");
        $stmt->execute();
        $exists = $stmt->fetch();
        
        if ($exists) {
            // Update existing record
            $fields = [];
            $values = [];
            foreach ($data as $key => $value) {
                $fields[] = "`$key` = ?";
                $values[] = $value;
            }
            $values[] = 1; // WHERE id = 1
            
            $sql = "UPDATE `$table` SET " . implode(', ', $fields) . " WHERE id = ?";
            $stmt = $pdo->prepare($sql);
            $stmt->execute($values);
            
            echo "<p class='success'>✓ Updated existing content</p>";
        } else {
            // Insert new record
            $fields = array_keys($data);
            $placeholders = array_fill(0, count($data), '?');
            
            $sql = "INSERT INTO `$table` (`id`, `" . implode('`, `', $fields) . "`) VALUES (1, " . implode(', ', $placeholders) . ")";
            $stmt = $pdo->prepare($sql);
            $stmt->execute(array_values($data));
            
            echo "<p class='success'>✓ Inserted new content</p>";
        }
    } catch (PDOException $e) {
        echo "<p class='error'>✗ Error: " . $e->getMessage() . "</p>";
    }
}

echo "<div class='info'>";
echo "<h3>✅ Content Fix Complete!</h3>";
echo "<p>All pages should now have default content. You can now:</p>";
echo "<a href='admin/manage_campus_life_pages.php' class='btn'>Edit Content in Admin</a>";
echo "<a href='test_campus_life_db.php' class='btn'>Test Database</a>";
echo "<a href='accommodation.php' class='btn'>View Accommodation</a>";
echo "<a href='food_services.php' class='btn'>View Food Services</a>";
echo "<a href='work_study.php' class='btn'>View Work Study</a>";
echo "<a href='sld.php' class='btn'>View SLD</a>";
echo "</div>";

echo "</div>
</body>
</html>";
?>
