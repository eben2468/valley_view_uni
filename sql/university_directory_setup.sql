-- Create Table for University Directory (High-level Officers)
CREATE TABLE IF NOT EXISTS university_directory (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    title VARCHAR(255) NOT NULL,
    category VARCHAR(100) NOT NULL,
    display_order INT DEFAULT 0,
    image_url VARCHAR(500) DEFAULT NULL,
    email VARCHAR(255) DEFAULT NULL,
    phone VARCHAR(100) DEFAULT NULL,
    is_active TINYINT DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Truncate to start fresh
TRUNCATE TABLE university_directory;

-- Insert Principal Officers
INSERT INTO university_directory (name, title, category, display_order) VALUES
('Pr. Prof. Daniel Ganu', 'Vice Chancellor', 'Principal Officers', 1),
('Pr. Prof. Peter Agyekum Boateng', 'Pro-Vice Chancellor', 'Principal Officers', 2),
('Pr. Dr. Samuel K. Amankwah', 'Registrar', 'Principal Officers', 3),
('Dr. Francis K. Osei-Kuffour', 'Finance Officer', 'Principal Officers', 4),
('Pr. Dr. Charles N.E. Amoah', 'University Librarian', 'Principal Officers', 5);

-- Insert Campus Administration
INSERT INTO university_directory (name, title, category, display_order) VALUES
('Dr. Michael Amponsah Kodom', 'Rector, Techiman Campus', 'Campus Administration', 6),
('Dr. Asare Bediako Ankrah', 'Vice Rector, Techiman Campus', 'Campus Administration', 7),
('Dr. Patricia Nyamekye', 'Rector, Kumasi Campus', 'Campus Administration', 8),
('Dr. Jeanette Owusu', 'Vice Rector, Kumasi Campus', 'Campus Administration', 9);

-- Insert Academic Deans & Research
INSERT INTO university_directory (name, title, category, display_order) VALUES
('Prof. Martin Akotey', 'Dean, School of Graduate Studies', 'Academic Deans & Research', 10),
('Dr. Ebenezer Owusu Yeboah', 'Vice Dean, School of Graduate Studies', 'Academic Deans & Research', 11),
('Prof. Josephine Ganu', 'Dean, Research, Development, and International Relations', 'Academic Deans & Research', 12),
('Dr. Ebenezer Quaye', 'Ag. Dean, Faculty of Arts & Social Sciences', 'Academic Deans & Research', 13),
('Olivia Osei Tutu', 'Ag. Dean, Faculty of Science', 'Academic Deans & Research', 14),
('Ezekiel Oko Annang', 'Ag. Vice Dean, Faculty of Science', 'Academic Deans & Research', 15),
('Dr. Abdulai Issaka', 'Ag. Dean, School of Business', 'Academic Deans & Research', 16),
('Dr. Martha Duah', 'Dean of Student Life and Services', 'Academic Deans & Research', 17),
('Pr. Peter Obeng Manu', 'Dean, Spiritual Life Development', 'Academic Deans & Research', 18);

-- Insert Departmental & Unit Heads
INSERT INTO university_directory (name, title, category, display_order) VALUES
('Dr. Josiah B. Andoh', 'Ag. Head, Theology & Missions', 'Departmental & Unit Heads', 19),
('Dr. Felix Oppong Asamoah', 'Head, Department of Teacher Education', 'Departmental & Unit Heads', 20),
('Dr. Annie Oye', 'Head, Department of Development & Communication Studies', 'Departmental & Unit Heads', 21),
('Dr. Josephine Pepra-Mensah', 'Head, Department of Management Studies', 'Departmental & Unit Heads', 22),
('Dr. Hannah Fosuah Amo', 'Head, Department of Accounting & Finance', 'Departmental & Unit Heads', 23),
('Dorothy Baffuor Awuah', 'Ag. Head, Department of Nursing and Health Sciences', 'Departmental & Unit Heads', 24),
('Dr. Emmanuel Opoku', 'Head, Department of Agribusiness/Agriculture, Techiman', 'Departmental & Unit Heads', 25),
('Adwoa Ansah-Adu', 'Ag. Head, Department of Health Sciences, Kumasi', 'Departmental & Unit Heads', 26),
('Dr. Patience B.A. Yamoah', 'Head, Centre for Adult & Continuing Education (CACE)', 'Departmental & Unit Heads', 27),
('Emmanuel Boahen', 'Coordinator, Pre-Degree Unit', 'Departmental & Unit Heads', 28);

-- Insert University Directors
INSERT INTO university_directory (name, title, category, display_order) VALUES
('James Allotey', 'Director of Academic Affairs (AAD)', 'University Directors', 29),
('Michael Tetteh Asare', 'Director of Information Technology Services (ITS)', 'University Directors', 30),
('Amos Opoku Boateng, Esq.', 'Director of Legal, Consular & General Services', 'University Directors', 31),
('Dr. Sarah Opoku Boateng', 'Director of University Relations (URD)', 'University Directors', 32),
('Pr. Flacus K. A. Amponsah', 'Director of Human Resource Management & Development (HRMDD)', 'University Directors', 33),
('Boakye Anim Amponsah-Kaakyire', 'Ag. Director, Internal Audit (IAD)', 'University Directors', 34),
('Louis Coffie', 'Ag. Director of Physical Development & Estate Management (PDEMD)', 'University Directors', 35),
('Pr. Dr. Ebenezer Danquah', 'Director of Quality Assurance & Academic Planning (QAAP)', 'University Directors', 36),
('Dr. Evans O.N.D. Ocansey', 'Director, CARES', 'University Directors', 37),
('Oheneba Kofi Nti', 'Director of Sports & Recreation', 'University Directors', 38),
('Akua Amponsah', 'Director of Counselling', 'University Directors', 39),
('Ama Foriwaa Nkrumah', 'Director of Career Services', 'University Directors', 40);

-- Insert Associate Officers & Section Heads
INSERT INTO university_directory (name, title, category, display_order) VALUES
('Samuel Yaw Boateng', 'Associate Director, Enrollment Management', 'Associate Officers & Section Heads', 41),
('Emmanuel Ayi', 'Associate Director, Academic Records & Systems Administration', 'Associate Officers & Section Heads', 42),
('Yaw Asa Mensah', 'Associate Director of ITS, Techiman', 'Associate Officers & Section Heads', 43),
('Solomon Addai', 'Associate Director, Ecology and Sanitation Management', 'Associate Officers & Section Heads', 44),
('Albert Amo-Asimeng', 'Associate Director-HRMD, Training & Development', 'Associate Officers & Section Heads', 45),
('Belinda Edem Livingston', 'Associate Director-HRMD, Techiman', 'Associate Officers & Section Heads', 46),
('Daniel Tweboah Koduah', 'Associate Director-HRMD, Kumasi', 'Associate Officers & Section Heads', 47),
('Mrs. Gifty A.Y. Aidoo', 'Associate Director, Academic Planning & Accreditations', 'Associate Officers & Section Heads', 48),
('Mrs. Beatrice Sonful', 'Associate Director, Quality Assurance & Compliance', 'Associate Officers & Section Heads', 49),
('Samuel Kofi Ameyaw', 'Associate Auditor-IA, Techiman', 'Associate Officers & Section Heads', 50),
('Isaac Domptey', 'Associate Auditor-IA, Kumasi', 'Associate Officers & Section Heads', 51),
('Dr. Kwasi Dwira Mensah', 'Associate Dean of Research, Development, and International Relations/CARES, Techiman', 'Associate Officers & Section Heads', 52),
('Pr. Dr. Odomse Akuoko-Nyantakyi', 'Associate Dean of Research Development, and International Relations/CARES, Kumasi', 'Associate Officers & Section Heads', 53),
('Pr. Isaac Afriyie', 'Associate Dean of Spiritual Life Development, & Student Life and Services', 'Associate Officers & Section Heads', 54),
('Pr. Daniel Faakye', 'Associate Dean of Spiritual Life Development', 'Associate Officers & Section Heads', 55),
('Pr. Jallah S. Karbah', 'Associate Dean of Spiritual Life Development', 'Associate Officers & Section Heads', 56),
('Pr Kusi Appiah', 'Associate Dean of Spiritual Life Development', 'Associate Officers & Section Heads', 57),
('Irene Ago', 'Senior Asst. Registrar, Events', 'Associate Officers & Section Heads', 58),
('Joseph Owusu', 'Associate Director-UR, Techiman', 'Associate Officers & Section Heads', 59),
('Agyeman Kofi Boateng', 'Associate Registrar, Techiman', 'Associate Officers & Section Heads', 60),
('Eunice Frimpong', 'Associate Registrar, Kumasi', 'Associate Officers & Section Heads', 61);

-- Insert Financial Officers
INSERT INTO university_directory (name, title, category, display_order) VALUES
('James Owusu', 'Associate Finance Officer, Accounting', 'Financial Officers', 62),
('Enoch Mintah Amoah', 'Associate Finance Officer, Treasury & Budgeting', 'Financial Officers', 63),
('Amos Asante', 'Associate Finance Officer, Diversification, Investments & Endowment', 'Financial Officers', 64),
('Richard Offeh Bediako', 'Associate Finance Officer, Ventures', 'Financial Officers', 65),
('Ophelia Baafi', 'Associate Finance Officer, Techiman', 'Financial Officers', 66),
('Pr. Kwasi Nimako Acheampong', 'Associate Finance Officer, Kumasi', 'Financial Officers', 67);

-- Insert Operations & Services Support
INSERT INTO university_directory (name, title, category, display_order) VALUES
('Dr. Sonny Davis Arthur', 'Manager, Transport & Mechanical Services', 'Operations & Services Support', 68),
('Elesi Adeku', 'Manager, University Ventures & Water Factory', 'Operations & Services Support', 69),
('Mrs. Abigail Amankwah', 'Manager, University Bakery', 'Operations & Services Support', 70),
('Chief Inspector Thomas Napol (Rtd)', 'Head, Safety & Security', 'Operations & Services Support', 71),
('Pr. Dr. Emmanuel Osei', 'Church Pastor', 'Operations & Services Support', 72);
