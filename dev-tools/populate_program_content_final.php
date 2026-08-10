<?php
require_once('includes/db_connect.php');

$excluded_ids = [7, 8, 9, 10, 33, 43, 44, 45, 46, 49, 52];

echo "Skipping/Excluding IDs: " . implode(', ', $excluded_ids) . "\n";

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
    // Science / Mathematics
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
    // Business (Remaining)
    else {
        $desc = "The " . $title . " program at Valley View University is an intensive course of study that prepares future leaders. The curriculum emphasizes strategic growth, financial literacy, and professional excellence in the " . $title . " field.";
        $learn = [
            "Strategic planning and organizational leadership skills",
            "Advanced analysis and practical management techniques",
            "Expertise in specialized business operations",
            "Professional ethics and corporate responsibility",
            "Applied problem-solving in complex industries",
            "Innovative thinking and modern professional practice"
        ];
        $career = [
            "Senior Executive or Specialized Manager",
            "Strategic Consultant or Industry Advisor",
            "Project Lead or Professional Practitioner",
            "Corporate Analyst or Technical Specialist",
            "Policy Lead or Higher Education Educator",
            "Independent Entrepreneur or Business Consultant"
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
    if (in_array($program['id'], $excluded_ids)) {
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
}

echo "Final Update Complete. Total programs updated: " . $update_count . "\n";
?>
