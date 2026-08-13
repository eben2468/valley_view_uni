-- =====================================================
-- Valley View University - News Articles Schema
-- Created: 2026-01-30
-- =====================================================

-- Create the news_articles table for storing full news articles
CREATE TABLE IF NOT EXISTS news_articles (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(500) NOT NULL,
    slug VARCHAR(500) NOT NULL UNIQUE,
    excerpt TEXT,
    content LONGTEXT,
    featured_image VARCHAR(1000),
    category ENUM('news', 'events', 'announcements', 'press_releases', 'academic') DEFAULT 'news',
    author VARCHAR(255) DEFAULT 'VVU Communications',
    author_image VARCHAR(500),
    tags VARCHAR(500),
    status ENUM('published', 'draft', 'archived') DEFAULT 'draft',
    is_featured TINYINT(1) DEFAULT 0,
    views_count INT DEFAULT 0,
    meta_title VARCHAR(500),
    meta_description TEXT,
    publish_date DATETIME DEFAULT CURRENT_TIMESTAMP,
    event_date DATE NULL,
    event_time TIME NULL,
    event_location VARCHAR(255),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_status (status),
    INDEX idx_category (category),
    INDEX idx_publish_date (publish_date),
    INDEX idx_featured (is_featured),
    INDEX idx_slug (slug)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Insert sample news articles
INSERT INTO news_articles (title, slug, excerpt, content, featured_image, category, author, status, is_featured, publish_date, event_date) VALUES
(
    'Research Breakthrough in the Science Department',
    'research-breakthrough-science-department',
    'Valley View University researchers have made a significant breakthrough in understanding protein folding mechanisms, which could lead to new treatments for neurodegenerative diseases.',
    '<p>Dr. Emily Rodriguez and her team in the Department of Biochemistry have published their findings in the prestigious journal Nature Structural Biology. Their research focuses on the complex process by which proteins fold into their functional three-dimensional structures.</p>

<p><strong>"Protein misfolding is at the root of many devastating diseases including Alzheimer''s, Parkinson''s, and Huntington''s disease,"</strong> said Dr. Rodriguez. "Our discovery provides new insights into how cells ensure proteins fold correctly and how this process can go wrong."</p>

<p>The team used advanced cryo-electron microscopy techniques to visualize the folding process in real-time. This allowed them to identify a critical checkpoint where cells can detect and correct misfolded proteins before they cause damage.</p>

<h3>Key Findings</h3>
<ul>
    <li>Identification of a new protein quality control checkpoint</li>
    <li>Novel molecular markers for early disease detection</li>
    <li>Potential therapeutic targets for drug development</li>
</ul>

<p>"This research opens up exciting possibilities for therapeutic interventions," explained Dr. Michael Chen, Dean of the Faculty of Science & Technology. "By understanding these quality control mechanisms, we may be able to develop drugs that enhance the cell''s natural ability to prevent protein aggregation."</p>

<p>The study was funded by a $2.3 million grant from the National Institutes of Health and involved collaboration with researchers from three other institutions. The team is now working on identifying small molecules that can modulate the newly discovered checkpoint.</p>',
    'uploads/news/research-science.jpg',
    'news',
    'Dr. Michael Chen',
    'published',
    1,
    '2026-01-28 10:00:00',
    NULL
),
(
    'Upcoming Guest Lecture Series: Innovation in Healthcare',
    'guest-lecture-series-innovation-healthcare',
    'Join us for a week of inspiring lectures featuring leading healthcare innovators from around the world sharing their insights on the future of medicine.',
    '<p>Valley View University is proud to announce our Spring 2026 Guest Lecture Series focusing on <em>Innovation in Healthcare</em>. This exciting event brings together world-renowned experts, researchers, and industry leaders to share their insights on the future of medicine and healthcare technology.</p>

<h3>Featured Speakers</h3>
<ul>
    <li><strong>Dr. Sarah Thompson</strong> - Director of Digital Health, WHO</li>
    <li><strong>Prof. James Okonkwo</strong> - AI in Medicine Pioneer, MIT</li>
    <li><strong>Dr. Amina Mensah</strong> - Telemedicine Expert, Ghana Health Service</li>
</ul>

<h3>Event Details</h3>
<p>The lecture series will be held at the University Auditorium from <strong>February 5-9, 2026</strong>. Each day features a keynote address followed by panel discussions and Q&A sessions.</p>

<blockquote>
    <p>"This is an unprecedented opportunity for our students and faculty to engage with leaders who are shaping the future of healthcare globally." - Vice-Chancellor, Prof. Daniel Owusu</p>
</blockquote>

<p>Registration is open to all students, faculty, staff, and members of the public. Seats are limited, so early registration is encouraged.</p>',
    'uploads/news/lecture-series.jpg',
    'events',
    'VVU Communications',
    'published',
    1,
    '2026-01-27 14:00:00',
    '2026-02-05'
),
(
    'Valley View University Receives National Accreditation Excellence Award',
    'vvu-receives-national-accreditation-excellence-award',
    'The National Accreditation Board has recognized Valley View University with the Excellence Award for outstanding commitment to quality education and continuous improvement.',
    '<p>Valley View University has been honored with the prestigious <strong>National Accreditation Excellence Award</strong> by the Ghana National Accreditation Board (NAB). This recognition celebrates the university''s unwavering commitment to maintaining the highest standards of academic excellence.</p>

<p>The award was presented during the Annual Higher Education Quality Assurance Conference held in Accra, where VVU was acknowledged for its innovative teaching methodologies, robust quality assurance systems, and exceptional student outcomes.</p>

<h3>Key Areas of Recognition</h3>
<ol>
    <li>Comprehensive quality assurance framework</li>
    <li>Outstanding faculty development programs</li>
    <li>Innovative curriculum design</li>
    <li>Strong industry partnerships</li>
    <li>Excellent student support services</li>
</ol>

<p>Vice-Chancellor Prof. Daniel Owusu expressed gratitude to the entire VVU community, stating: <em>"This award belongs to every member of our university family - our dedicated faculty, hardworking staff, and ambitious students who make VVU a center of excellence."</em></p>

<p>The award comes as VVU celebrates its 45th anniversary as Ghana''s first chartered private university, reaffirming its position as a leader in higher education on the continent.</p>',
    'uploads/news/accreditation-award.jpg',
    'announcements',
    'Office of the Vice-Chancellor',
    'published',
    0,
    '2026-01-25 09:00:00',
    NULL
),
(
    'Alumni Spotlight: Sarah Mensah Named CEO of TechGhana',
    'alumni-spotlight-sarah-mensah-ceo-techghana',
    'VVU Computer Science alumna Sarah Mensah has been appointed as the new CEO of TechGhana, one of Africa''s fastest-growing technology companies.',
    '<p>Valley View University is proud to celebrate the remarkable achievement of alumna <strong>Sarah Mensah (BSc Computer Science, 2015)</strong>, who has been appointed as the new Chief Executive Officer of TechGhana, one of Africa''s leading technology companies.</p>

<p>Sarah''s journey from a VVU computer science student to a tech industry leader is an inspiring story of dedication, innovation, and perseverance.</p>

<h3>Career Journey</h3>
<p>After graduating from VVU with First Class Honours, Sarah joined a small startup in Accra. Her exceptional skills in software development and leadership quickly caught the attention of industry leaders. Within eight years, she rose through the ranks at various technology companies before being headhunted by TechGhana.</p>

<blockquote>
    <p>"My time at Valley View University laid the foundation for everything I''ve achieved. The rigorous academic program, combined with the values of integrity and service, shaped who I am today." - Sarah Mensah</p>
</blockquote>

<h3>Giving Back</h3>
<p>Sarah remains connected to her alma mater, mentoring current students and supporting the university''s entrepreneurship programs. She has also established a scholarship fund for female students pursuing degrees in technology.</p>

<p>We congratulate Sarah on this tremendous achievement and look forward to seeing her continued success!</p>',
    'uploads/news/alumni-sarah.jpg',
    'news',
    'Alumni Relations Office',
    'published',
    1,
    '2026-01-24 11:00:00',
    NULL
),
(
    'Semester Registration Dates Announced for 2026',
    'semester-registration-dates-2026',
    'The Office of the Registrar has released the official registration schedule for the 2026 academic year. All students are encouraged to take note of important deadlines.',
    '<p>The Office of the Registrar is pleased to announce the registration dates for the 2026 academic year. All current and prospective students are advised to carefully note the following important dates and deadlines.</p>

<h3>Spring Semester 2026</h3>
<table style="width:100%; border-collapse: collapse;">
    <tr>
        <td style="padding: 8px; border: 1px solid #ddd;"><strong>Early Registration</strong></td>
        <td style="padding: 8px; border: 1px solid #ddd;">January 15-25, 2026</td>
    </tr>
    <tr>
        <td style="padding: 8px; border: 1px solid #ddd;"><strong>Regular Registration</strong></td>
        <td style="padding: 8px; border: 1px solid #ddd;">January 26 - February 7, 2026</td>
    </tr>
    <tr>
        <td style="padding: 8px; border: 1px solid #ddd;"><strong>Late Registration (with fee)</strong></td>
        <td style="padding: 8px; border: 1px solid #ddd;">February 8-14, 2026</td>
    </tr>
    <tr>
        <td style="padding: 8px; border: 1px solid #ddd;"><strong>Classes Begin</strong></td>
        <td style="padding: 8px; border: 1px solid #ddd;">February 17, 2026</td>
    </tr>
</table>

<h3>Required Documents</h3>
<ul>
    <li>Valid student ID or admission letter</li>
    <li>Proof of fee payment or financial clearance</li>
    <li>Course registration form (available on iSchool)</li>
</ul>

<h3>How to Register</h3>
<p>Students can complete their registration online through the <a href="https://ischool.vvu.edu.gh">iSchool portal</a>. For assistance, please contact the Registrar''s Office at <a href="mailto:registrar@vvu.edu.gh">registrar@vvu.edu.gh</a>.</p>

<p><strong>Note:</strong> Failure to register by the late registration deadline will result in exclusion from classes for the semester.</p>',
    'uploads/news/registration.jpg',
    'announcements',
    'Office of the Registrar',
    'published',
    0,
    '2026-01-22 08:00:00',
    NULL
),
(
    'New Campus Wing Inauguration Ceremony',
    'new-campus-wing-inauguration-ceremony',
    'Valley View University is excited to announce the grand opening of the new Science and Technology Complex, a state-of-the-art facility designed to enhance research and learning.',
    '<p>Valley View University invites all members of our community to the grand inauguration of the <strong>Dr. Kofi Addo Science and Technology Complex</strong>, a landmark addition to our campus infrastructure.</p>

<h3>Ceremony Details</h3>
<ul>
    <li><strong>Date:</strong> February 20, 2026</li>
    <li><strong>Time:</strong> 10:00 AM</li>
    <li><strong>Venue:</strong> New Science Complex, Main Campus</li>
</ul>

<h3>About the New Complex</h3>
<p>The 50,000 square foot facility features:</p>
<ul>
    <li>15 state-of-the-art laboratories</li>
    <li>Advanced research centers for biotechnology and AI</li>
    <li>Collaborative learning spaces</li>
    <li>Green building certification with solar power integration</li>
    <li>High-speed network infrastructure</li>
</ul>

<p>This $15 million investment represents the university''s commitment to providing world-class facilities for scientific research and innovation.</p>

<h3>Guest of Honor</h3>
<p>The ceremony will be graced by Hon. Dr. Matthew Opoku Prempeh, Minister of Education, who will officially commission the building.</p>

<p>All students, staff, alumni, and friends of the university are cordially invited to attend this historic event.</p>',
    'uploads/news/new-campus-wing.jpg',
    'press_releases',
    'VVU Communications',
    'published',
    1,
    '2026-01-20 15:00:00',
    '2026-02-20'
),
(
    'VVU Students Win National Debate Championship',
    'vvu-students-win-national-debate-championship',
    'The VVU Debate Team has emerged victorious at the 2026 National Universities Debate Championship, bringing home the gold trophy for the third consecutive year.',
    '<p>Valley View University''s Debate Team has once again demonstrated excellence by winning the <strong>2026 National Universities Debate Championship</strong> held at the University of Ghana.</p>

<p>The team, consisting of final year students Emmanuel Asante, Grace Ofori, and second year student David Tetteh, beat teams from 24 universities across Ghana to claim the championship title.</p>

<h3>Championship Journey</h3>
<p>The team progressed through five rounds of intense debate on topics ranging from environmental policy to digital rights. In the finals, they argued persuasively on the motion: <em>"Artificial Intelligence poses more opportunities than threats to developing nations."</em></p>

<h3>Team Captain''s Remarks</h3>
<blockquote>
    <p>"This victory is dedicated to our coaches, the university, and everyone who believed in us. VVU has cultivated a culture of critical thinking and articulate expression that shines through in competitions like this." - Emmanuel Asante, Team Captain</p>
</blockquote>

<p>Coach Dr. Patience Agyemang attributed the success to rigorous preparation and the university''s strong liberal arts foundation. The team will now represent Ghana at the Pan-African Universities Debate Championship in Kenya this March.</p>

<p>Congratulations to our debate champions!</p>',
    'uploads/news/debate-team.jpg',
    'news',
    'Student Affairs Office',
    'published',
    0,
    '2026-01-18 16:00:00',
    NULL
),
(
    'Academic Workshop: Mastering Research Methodology',
    'workshop-mastering-research-methodology',
    'The Graduate School is organizing a comprehensive workshop on research methodology for all graduate students and interested faculty members.',
    '<p>The School of Graduate Studies cordially invites all graduate students, research assistants, and faculty members to participate in an intensive <strong>Research Methodology Workshop</strong>.</p>

<h3>Workshop Overview</h3>
<p>This two-day workshop will cover essential research skills including:</p>
<ul>
    <li>Formulating research questions and hypotheses</li>
    <li>Quantitative and qualitative research design</li>
    <li>Data collection methods and tools</li>
    <li>Statistical analysis using SPSS and R</li>
    <li>Academic writing and publication strategies</li>
    <li>Research ethics and integrity</li>
</ul>

<h3>Event Details</h3>
<table style="width:100%; border-collapse: collapse;">
    <tr>
        <td style="padding: 8px; border: 1px solid #ddd;"><strong>Dates</strong></td>
        <td style="padding: 8px; border: 1px solid #ddd;">February 12-13, 2026</td>
    </tr>
    <tr>
        <td style="padding: 8px; border: 1px solid #ddd;"><strong>Time</strong></td>
        <td style="padding: 8px; border: 1px solid #ddd;">9:00 AM - 4:00 PM daily</td>
    </tr>
    <tr>
        <td style="padding: 8px; border: 1px solid #ddd;"><strong>Venue</strong></td>
        <td style="padding: 8px; border: 1px solid #ddd;">Graduate Studies Building, Room 201</td>
    </tr>
    <tr>
        <td style="padding: 8px; border: 1px solid #ddd;"><strong>Fee</strong></td>
        <td style="padding: 8px; border: 1px solid #ddd;">Free for VVU students and staff</td>
    </tr>
</table>

<h3>Registration</h3>
<p>Registration is required. Please sign up through the Graduate School office or email <a href="mailto:gradschool@vvu.edu.gh">gradschool@vvu.edu.gh</a> by February 8, 2026.</p>

<p>Certificates will be awarded to all participants upon successful completion of the workshop.</p>',
    'uploads/news/research-workshop.jpg',
    'academic',
    'School of Graduate Studies',
    'published',
    0,
    '2026-01-15 10:00:00',
    '2026-02-12'
);

-- Create an index on category and status for faster filtering
CREATE INDEX IF NOT EXISTS idx_cat_status ON news_articles(category, status);
