<?php
require_once 'includes/db_connect.php';

try {
    $pdo->beginTransaction();

    // 1. Philosophy Principles
    $principles = [
        ['Modesty', 'Appropriate covering of the body parts, avoiding contemporary styles that are revealing or suggestive. Dressing that respects oneself and others.', 'shield', 'purple-600', 1],
        ['Chastity', 'Clothing that reflects purity of heart and mind. Our attire should not draw inappropriate attention but rather reflect inner virtue.', 'favorite', 'blue-600', 2],
        ['Simplicity', 'Avoiding extravagance and ostentation. Our dress accentuates natural beauty rather than looks encouraged by fleeting fashion trends.', 'spa', 'green-600', 3],
        ['Propriety', 'Dressing that is appropriate for every occasion. Understanding context and choosing attire that fits the setting and activity.', 'balance', 'yellow-500', 4],
        ['Neatness', 'Clean, well-maintained clothing that reflects self-respect and care. Attention to personal grooming and presentation.', 'dry_cleaning', 'cyan-600', 5],
        ['Comeliness', 'Pleasant and becoming appearance that reflects the beauty of character. Dressing in a way that is attractive yet dignified.', 'diamond', 'pink-500', 6]
    ];
    $stmt = $pdo->prepare("INSERT IGNORE INTO philosophy_dress_principles (title, description, icon, border_color, display_order) VALUES (?, ?, ?, ?, ?)");
    foreach ($principles as $p) $stmt->execute($p);

    // 1b. Philosophy Benefits
    $benefits = [
        ['Unity', 'Creates a sense of community and shared values among all campus members.', 'groups', 'purple-500', 'purple-700', 1],
        ['Focus', 'Minimizes distractions and helps students focus on academic excellence.', 'psychology_alt', 'blue-500', 'blue-700', 2],
        ['Professionalism', 'Prepares students for professional environments and career success.', 'workspace_premium', 'green-500', 'green-700', 3],
        ['Character', 'Develops discipline, self-respect, and consideration for others.', 'self_improvement', 'yellow-400', 'orange-500', 4]
    ];
    $stmt = $pdo->prepare("INSERT IGNORE INTO philosophy_dress_benefits (title, description, icon, gradient_start, gradient_end, display_order) VALUES (?, ?, ?, ?, ?, ?)");
    foreach ($benefits as $b) $stmt->execute($b);

    // 2. Accommodation Halls
    $halls = [
        ['male', 'Male Halls', 'Comfortable living spaces for our gentlemen.', "J.J. Nortey Hall\nM.A Bediako Hall", 'https://lh3.googleusercontent.com/aida-public/AB6AXuAHALqB0afo0KfhqsLQBo3u8VWgKXYaXbaVGRedfHLcTBhWNsZpS5Zz4aHkx05ojLmrZdCb3w649d92xutlHHxTSCzPqwv4iEaj8fzjo6BBdpxuPqGYPtKp01mFptUOw26QScAlk7IYbFzE2t3i5fiSdMqVy0s0CQ6KINmpAF8DZZuVtoJzFHwGJccZVXQiC6frOFlugPBj6s_SL_c8K8taNzKXwSizGFIGnEq2HrEIHoYb20ZA_tymbifDtpWtPrG1FcF8FVG71Q-D', 'blue-600', 'blue-800', 'man', 1],
        ['female', 'Female Halls', 'Safe and supportive environment for our ladies.', "NAGSDA Hall\nEllen White Hall", 'https://lh3.googleusercontent.com/aida-public/AB6AXuBtjpRlr4aloKrqjsmf_32MOdOavvqyT_jmcmaSE9tUGxoJve6ujzMyRtLlTmHOTYdWucGAmoOGF4XyMED609Zuiud4u-87DeQNRO3mhc6cU1uSDSQPJZbNxct0jPHSqIx6tBgiqpbMUbsouq6ASiDib2bVUMu7jX2Ea-OUaRHSHR1CbXuRGiOQjLVmxs-za9LMh_2Vnu1uVK_jSvtpzfz2rYemkzX82PpUfrwE6ar3B1DmSieaUjjFBOf56rzI_nCjIy7QjYp6g2Ob', 'pink-600', 'purple-800', 'woman', 2]
    ];
    $stmt = $pdo->prepare("INSERT IGNORE INTO accommodation_halls (type, title, description, halls_list, image, gradient_start, gradient_end, icon, display_order) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
    foreach ($halls as $h) $stmt->execute($h);

    // 3. Work Study Steps
    $steps = [
        [1, 'Register', 'Complete your student registration for the academic year', 'blue'],
        [2, 'Visit Office', 'Go to the Student Employment Office on campus', 'green'],
        [3, 'Fill Form', 'Complete work study application and preference form', 'purple'],
        [4, 'Get Assigned', 'Receive your work assignment and begin employment', 'yellow']
    ];
    $stmt = $pdo->prepare("INSERT IGNORE INTO work_study_steps (step_number, title, description, color) VALUES (?, ?, ?, ?)");
    foreach ($steps as $s) $stmt->execute($s);

    // 4. SLD Locations
    $locs = [
        ['Oyibi Campus', 'Main campus spiritual services and counseling center', 'location_on', 1],
        ['Kumasi Campus', 'Full chaplaincy and ministry services available', 'location_on', 2],
        ['Techiman Campus', 'Dedicated chaplains for pastoral care and support', 'location_on', 3]
    ];
    $stmt = $pdo->prepare("INSERT IGNORE INTO sld_locations (title, description, icon, display_order) VALUES (?, ?, ?, ?)");
    foreach ($locs as $l) $stmt->execute($l);

    // Update main content tables with initial text for new columns
    $pdo->exec("UPDATE philosophy_on_dress_content SET 
        adventist_values_text = 'As a Seventh-day Adventist institution, our understanding of biblical ideals is influenced by our faith tradition.',
        universal_respect_text = 'While we understand that others may have different meanings of dressing, we require all students, workers, and visitors to abide by our dress code.',
        total_person_text = 'Ours is a value-based and holistic education that develops the \'total person\' for service to God and humanity.',
        quote_text = 'Valley View University dress ideal seeks for appropriate covering of the body parts and avoids contemporary styles that are revealing or suggestive. It accentuates natural beauty rather than looks encouraged by fashion trends.',
        guidelines_heading = 'What We Encourage',
        guidelines_subtitle = 'Practical guidelines to help you dress appropriately on campus'
        WHERE id = 1");

    $pdo->exec("UPDATE accommodation_content SET 
        off_campus_text = 'Students wishing to live off-campus must apply to the Administration through the Dean for Student Life and Services at the beginning of each semester. Transfers during the semester are not permitted. Off-campus students are expected to uphold VVU standards at all times.',
        dining_text = 'The University cafeteria serves wholesome and balanced vegetarian meals. We believe in nurturing the body as well as the mind.',
        dining_list = \"All entrees served in the cafeteria are vegetarian.\nNon-vegetarian entrees are not permitted in the cafeteria.\nExternal food vendors are available outside university premises for other meal options.\",
        dining_image = 'https://lh3.googleusercontent.com/aida-public/AB6AXuDSeqmrwPG3sf-gWxQkWIyTW3i2UysmfBYSG33eWEfWiOAl8dOngxb2QfZdI5HgNXDHEHDeX-0uo_FW6WNFLqQvaOCBKnr98FysYM98VrEM3NLbjQGogw3wGDoTF3QmXzDNzfVbX6nU1axpNdLKxbG87VK69YVekVdavfIV2HeF3PQApKddWlfKLiCspAWSe45WT6WZGhniZPeAW73UZdjihyr-q58X7Dk45_Bw6EsL5R399E9Qe--xFI0yYay1kNVtWy8Hjg_aHfjL',
        cta_heading = 'Ready to Join Our Community?',
        cta_text = 'Secure your accommodation early. Spaces are limited and allocated on a first-come, first-served basis.'
        WHERE id = 1");

    $pdo->exec("UPDATE food_services_content SET 
        breakfast_desc = 'Start your day with fresh fruits, cereals, and warm entrees.',
        lunch_desc = 'Our main meal of the day featuring a variety of local and international dishes.',
        dinner_desc = 'A lighter evening meal to wind down and socialize with peers.',
        meal_plans_text = 'Whether you live on campus or commute, we have a meal plan that fits your lifestyle and budget. Enjoy the convenience of pre-paid dining.',
        meal_plans_reg_info = 'Visit the Student Finance office or use the student portal to select your preferred meal plan for the semester.'
        WHERE id = 1");

    $pdo->commit();
    echo "Seeding successful!";
} catch (Exception $e) {
    if ($pdo->inTransaction()) $pdo->rollBack();
    echo "Seeding failed: " . $e->getMessage();
}
