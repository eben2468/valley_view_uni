<?php
require_once('includes/db_connect.php');

// Restore ID 49 which I checked earlier and know the values for
$stmt = $pdo->prepare("UPDATE academic_programs SET 
    full_description = ?, 
    learning_points = ?, 
    career_paths = ? 
    WHERE id = 49");
$stmt->execute([
    "This programme equips students with in-depth knowledge of banking operations, financial markets, investments, and financial management. It prepares graduates to understand modern financial systems and make sound financial decisions. Career opportunities include banking, insurance, investment analysis, and financial services.",
    json_encode(["Understand banking systems and financial institutions","Analyze financial markets and investment opportunities","Apply financial management and budgeting skills","Understand risk management and credit analysis","Use financial data for decision-making","Apply ethical standards in financial services"]),
    json_encode(["Bank Officer","Credit Analyst","Financial Analyst","Investment Officer","Insurance Officer","Treasury Officer"])
]);

echo "Restored ID 49.\n";

$excluded_keywords = [
    'banking and finance',
    'human resource management',
    'human res',
    'accounting',
    'marketing',
    'management',
    'business administration'
];

$stmt = $pdo->query("SELECT id, title FROM academic_programs");
$programs = $stmt->fetchAll(PDO::FETCH_ASSOC);

function getContentForTitle($title) {
    $t = strtolower($title);
    
    // Default fallback
    $desc = "This program at Valley View University provides students with a comprehensive education in " . $title . ". Through a blend of theoretical knowledge and practical application, students are prepared to meet the challenges of the modern professional landscape. Our curriculum is designed to foster critical thinking, ethical leadership, and technical proficiency.";
    $learn = [
        "Master the fundamental principles and theories of " . $title,
        "Develop advanced technical skills relevant to the industry",
        "Enhance critical thinking and problem-solving capabilities",
        "Apply theoretical knowledge to real-world practical scenarios",
        "Develop strong professional ethics and leadership qualities",
        "Prepare for advanced certification and lifelong learning"
    ];
    $career = [
        "Professional Consultant in the specialized field",
        "Project Manager or Team Lead in relevant industries",
        "Academic Researcher or Educator in higher institutions",
        "Strategic Analyst for private and public sector organizations",
        "Independent Entrepreneur or Industry Specialist",
        "Technical Advisor for international development agencies"
    ];

    // IT / Computer Science
    if (strpos($t, 'information technology') !== false || strpos($t, 'computer science') !== false || strpos($t, 'it') !== false || strpos($t, 'information systems') !== false) {
        $desc = "The " . $title . " program is designed to equip students with the cutting-edge skills needed in the rapidly evolving digital world. Students explore hardware, software, networking, and system security while developing innovative solutions to complex technological problems.";
        $learn = [
            "Expertise in software development and programming languages",
            "Advanced understanding of network architecture and security",
            "Proficiency in database management and data analytics",
            "Knowledge of system analysis and software engineering",
            "Experience with emerging technologies like AI and Cloud Computing",
            "Strong skills in IT project management and ethical hacking"
        ];
        $career = [
            "Software Developer or Systems Architect",
            "Network Administrator or Security Analyst",
            "IT Project Manager or Consultant",
            "Data Scientist or Business Intelligence Analyst",
            "Web Developer or UX/UI Designer",
            "Database Administrator or Cloud Specialist"
        ];
    }
    // Education
    elseif (strpos($t, 'b.ed') !== false || strpos($t, 'm.ed') !== false || strpos($t, 'mphil') !== false || strpos($t, 'phd educational') !== false || strpos($t, 'education') !== false || strpos($t, 'curriculum') !== false || strpos($t, 'early childhood') !== false) {
        $desc = "Our " . $title . " program focuses on preparing the next generation of educators and educational leaders. It combines pedagogical excellence with deep subject-matter expertise, ensuring graduates are ready to transform classrooms and educational institutions.";
        $learn = [
            "Mastery of modern pedagogical techniques and classroom management",
            "Deep understanding of educational psychology and student development",
            "Skills in curriculum design and instructional material development",
            "Advanced research methods in educational contexts",
            "Knowledge of educational policy, administration, and leadership",
            "Proficiency in integrating technology into the learning process"
        ];
        $career = [
            "Licensed Professional Teacher in Secondary or Tertiary levels",
            "Educational Administrator or School Principal",
            "Curriculum Developer or Textbook Writer",
            "Educational Policy Analyst for government agencies",
            "Private Tutor or Educational Consultant",
            "Academic Researcher in Instructional Design"
        ];
    }
    // Nursing / Health
    elseif (strpos($t, 'nursing') !== false || strpos($t, 'midwifery') !== false || strpos($t, 'biomedical') !== false) {
        $desc = "The " . $title . " program provides rigorous clinical training and theoretical foundation in health sciences. Students learn to provide holistic, patient-centered care while adhering to the highest standards of medical ethics and clinical excellence.";
        $learn = [
            "Advanced clinical nursing and patient care skills",
            "Comprehensive knowledge of anatomy, physiology, and pharmacology",
            "Skills in emergency response and critical care management",
            "Understanding of community health and preventive medicine",
            "Proficiency in health record management and medical technology",
            "Mastery of medical ethics and professional healthcare communication"
        ];
        $career = [
            "Registered Professional Nurse or Midwife",
            "Healthcare Administrator or Nurse Manager",
            "Public Health Officer or Community Health Advocate",
            "Clinical Instructor or Nurse Educator",
            "Medical Researcher or Clinical Trials Coordinator",
            "Home Health Care Specialist or Private Practitioner"
        ];
    }
    // Social Science / Arts
    elseif (strpos($t, 'theological') !== false || strpos($t, 'religions') !== false || strpos($t, 'communication') !== false || strpos($t, 'development studies') !== false) {
        $desc = "Graduates of the " . $title . " program gain a profound understanding of human society, communication, and spiritual values. This program builds critical analytical skills and cultural awareness for leadership in diverse social and religious contexts.";
        $learn = [
            "Advanced communication and interpersonal skills",
            "Deep understanding of social structures and cultural dynamics",
            "Theological or philosophical analysis and research",
            "Critical evaluation of historical and contemporary social issues",
            "Project planning and social development strategies",
            "Ethical leadership in religious and community organizations"
        ];
        $career = [
            "Public Relations Specialist or Journalist",
            "Social Worker or Community Development Officer",
            "Church Leader or Ministry Professional",
            "Non-Governmental Organization (NGO) Manager",
            "Social Researcher or Policy Advisor",
            "Humanitarian Aid Coordinator"
        ];
    }
    // Music
    elseif (strpos($t, 'music') !== false || strpos($t, 'sound engineering') !== false) {
        $desc = "Our " . $title . " program celebrates the artistic and technical aspects of auditory arts. From classical theory to modern production, students develop their creative voice and technical proficiency in a world-class environment.";
        $learn = [
            "Advanced music theory and composition techniques",
            "Proficiency in musical instruments or vocal performance",
            "Technical mastery of sound recording and post-production",
            "Acoustic analysis and sound design principles",
            "Knowledge of music history and ethnomusicology",
            "Professional performance and stage management skills"
        ];
        $career = [
            "Professional Musician or Recording Artist",
            "Sound Engineer or Audio Producer",
            "Music Educator or Private Instructor",
            "Music Director for religious or social entities",
            "Audio Post-Production Specialist",
            "Events Sound Manager"
        ];
    }
    // Science / Mathematics / Agribusiness
    elseif (strpos($t, 'mathematics') !== false || strpos($t, 'science') !== false || strpos($t, 'statistics') !== false || strpos($t, 'agriculture') !== false || strpos($t, 'agribusiness') !== false) {
        $desc = "The " . $title . " program focuses on quantitative analysis and scientific inquiry. Students develop strong foundations and analytical tools to solve complex problems in science, industry, and academia.";
        $learn = [
            "Advanced mathematical modeling or scientific research skills",
            "Deep understanding of statistical analysis and data interpretation",
            "Practical application of scientific principles in specific fields",
            "Proficiency in industry-standard software and laboratory tools",
            "Logical reasoning and abstract problem-solving",
            "Development of sustainable solutions for environmental and economic challenges"
        ];
        $career = [
            "Data Analyst or Research Scientist",
            "Financial Analyst or Actuary",
            "Agricultural Specialist or Agribusiness Manager",
            "Mathematics Educator or Lecturer",
            "Systems Analyst or Operations Researcher",
            "Environmental Consultant or Risk Analyst"
        ];
    }
    // Other Business (Not the core 6)
    elseif (strpos($t, 'business') !== false || strpos($t, 'mba') !== false || strpos($t, 'bba') !== false) {
        $desc = "The " . $title . " program at Valley View University is an intensive course of study that prepares future executives and entrepreneurs. The curriculum emphasizes strategic management, financial literacy, and global market dynamics.";
        $learn = [
            "Strategic planning and organizational leadership skills",
            "Advanced financial analysis and budget management",
            "Advanced marketing and consumer behavior strategies",
            "Operational excellence and supply chain management",
            "Business ethics and professional responsibility",
            "Entrepreneurial thinking and innovative business modeling"
        ];
        $career = [
            "Chief Executive or Senior Operations Manager",
            "Business Development Lead or Strategic Consultant",
            "Project Director or Portfolio Manager",
            "Corporate Analyst or Systems Auditor",
            "Public Sector Administrator or Policy lead",
            "Independent Entrepreneur or Business Owner"
        ];
    }

    return [
        'full_description' => $desc,
        'learning_points' => json_encode($learn),
        'career_paths' => json_encode($career)
    ];
}

$update_count = 0;
foreach ($programs as $program) {
    if ($program['id'] == 49) continue; // Already restored
    
    $t = strtolower($program['title']);
    $is_bba_type = (strpos($t, 'bba') !== false || strpos($t, 'bachelor of business administration') !== false || $program['id'] == 33);
    
    $is_core_excluded = false;
    if ($is_bba_type) {
        if (strpos($t, 'accounting') !== false || 
            strpos($t, 'management') !== false || 
            strpos($t, 'marketing') !== false || 
            strpos($t, 'human res') !== false || 
            strpos($t, 'banking') !== false || 
            $program['id'] == 33) {
            $is_core_excluded = true;
        }
    }

    if ($is_core_excluded) {
        echo "Excluding manual program: " . $program['title'] . " (ID " . $program['id'] . ")\n";
        continue;
    }

    $content = getContentForTitle($program['title']);
    
    $update_stmt = $pdo->prepare("UPDATE academic_programs SET full_description = ?, learning_points = ?, career_paths = ? WHERE id = ?");
    $update_stmt->execute([
        $content['full_description'],
        $content['learning_points'],
        $content['career_paths'],
        $program['id']
    ]);
    
    $update_count++;
    // echo "Updated: " . $program['title'] . "\n";
}

echo "\nTotal programs updated/verified: " . $update_count . "\n";
?>
