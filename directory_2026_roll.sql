-- =====================================================================
-- Valley View University — Staff & Faculty Directory (2026 ITS data)
-- Generated 2026-08-12
--
-- Faculty ............................ 81
-- Non-Teaching Senior Members ........ 40
-- Senior Staff ....................... 57
-- Junior Staff ....................... 83
-- TOTAL .............................. 261
--
-- Safe to run more than once. Existing photos/bios are preserved:
-- rows are matched on (type, name) and updated rather than duplicated.
-- =====================================================================

SET NAMES utf8mb4;

-- ---------------------------------------------------------------------
-- 1. Schema upgrade — adds the columns the new pages rely on.
-- ---------------------------------------------------------------------
SET @ddl = (SELECT IF(
    (SELECT COUNT(*) FROM information_schema.COLUMNS
      WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'directory' AND COLUMN_NAME = 'staff_category') > 0,
    'SELECT 1', 'ALTER TABLE directory ADD COLUMN staff_category VARCHAR(50) NULL DEFAULT NULL AFTER faculty_group'));
PREPARE s FROM @ddl; EXECUTE s; DEALLOCATE PREPARE s;

SET @ddl = (SELECT IF(
    (SELECT COUNT(*) FROM information_schema.COLUMNS
      WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'directory' AND COLUMN_NAME = 'sort_order') > 0,
    'SELECT 1', 'ALTER TABLE directory ADD COLUMN sort_order INT NOT NULL DEFAULT 0 AFTER staff_category'));
PREPARE s FROM @ddl; EXECUTE s; DEALLOCATE PREPARE s;

SET @ddl = (SELECT IF(
    (SELECT COUNT(*) FROM information_schema.COLUMNS
      WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'directory' AND COLUMN_NAME = 'is_active') > 0,
    'SELECT 1', 'ALTER TABLE directory ADD COLUMN is_active TINYINT(1) NOT NULL DEFAULT 1 AFTER sort_order'));
PREPARE s FROM @ddl; EXECUTE s; DEALLOCATE PREPARE s;

SET @ddl = (SELECT IF(
    (SELECT COUNT(*) FROM information_schema.COLUMNS
      WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'directory' AND COLUMN_NAME = 'is_featured') > 0,
    'SELECT 1', 'ALTER TABLE directory ADD COLUMN is_featured TINYINT(1) NOT NULL DEFAULT 0 AFTER is_active'));
PREPARE s FROM @ddl; EXECUTE s; DEALLOCATE PREPARE s;

-- Unique key on (type, name) so this script can be re-run safely.
SET @ddl = (SELECT IF(
    (SELECT COUNT(*) FROM information_schema.STATISTICS
      WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'directory' AND INDEX_NAME = 'uniq_type_name') > 0,
    'SELECT 1', 'ALTER TABLE directory ADD UNIQUE KEY uniq_type_name (type, name)'));
PREPARE s FROM @ddl; EXECUTE s; DEALLOCATE PREPARE s;

-- ---------------------------------------------------------------------
-- 2. Clear the old placeholder directory rows.
--    Comment the next line out if you want to keep everything already
--    in the table and only add/refresh the 2026 people.
-- ---------------------------------------------------------------------
DELETE FROM directory;

-- ---------------------------------------------------------------------
-- 3. The 2026 roll.
-- ---------------------------------------------------------------------
-- FACULTY — School of Theology & Missions
INSERT INTO directory
    (type, name, title, job_title, department, faculty_group, staff_category, sort_order, is_active, employment_status)
VALUES
    ('faculty', 'Ebenezer Quaye', '', 'Senior Lecturer/Dean FAAS', 'School of Theology & Missions', 'Faculty of Arts & Social Sciences (FASS)', NULL, 10, 1, 'Full-time'),
    ('faculty', 'Isaac Kyere', '', 'Assistant Lecturer', 'School of Theology & Missions', 'Faculty of Arts & Social Sciences (FASS)', NULL, 20, 1, 'Full-time'),
    ('faculty', 'Josiah B. Andoh', '', 'Senior Lecturer/HOD', 'School of Theology & Missions', 'Faculty of Arts & Social Sciences (FASS)', NULL, 30, 1, 'Full-time'),
    ('faculty', 'Martha Duah', '', 'Senior Lecturer/Dean SLS', 'School of Theology & Missions', 'Faculty of Arts & Social Sciences (FASS)', NULL, 40, 1, 'Full-time'),
    ('faculty', 'Solomon Appiah', '', 'Lecturer', 'School of Theology & Missions', 'Faculty of Arts & Social Sciences (FASS)', NULL, 50, 1, 'Full-time'),
    ('faculty', 'Tenortey Francis K.', '', 'Senior Lecturer', 'School of Theology & Missions', 'Faculty of Arts & Social Sciences (FASS)', NULL, 60, 1, 'Full-time')
ON DUPLICATE KEY UPDATE
    title = VALUES(title), job_title = VALUES(job_title), department = VALUES(department),
    faculty_group = VALUES(faculty_group), staff_category = VALUES(staff_category),
    sort_order = VALUES(sort_order), is_active = 1;

-- FACULTY — General Education
INSERT INTO directory
    (type, name, title, job_title, department, faculty_group, staff_category, sort_order, is_active, employment_status)
VALUES
    ('faculty', 'Akua Amponsah Kusi', '', 'Lecturer/Counselor', 'General Education', 'Faculty of Arts & Social Sciences (FASS)', NULL, 70, 1, 'Full-time'),
    ('faculty', 'Christiana Pokua', '', 'Lecturer', 'General Education', 'Faculty of Arts & Social Sciences (FASS)', NULL, 80, 1, 'Full-time'),
    ('faculty', 'Emmanuel Boahen', '', 'Lecturer/Coordinator', 'General Education', 'Faculty of Arts & Social Sciences (FASS)', NULL, 90, 1, 'Full-time'),
    ('faculty', 'Jean Elom Doufodji', '', 'Lecturer', 'General Education', 'Faculty of Arts & Social Sciences (FASS)', NULL, 100, 1, 'Full-time'),
    ('faculty', 'Susana Adjei-Mensah', '', 'Lecturer/Coordinator', 'General Education', 'Faculty of Arts & Social Sciences (FASS)', NULL, 110, 1, 'Full-time'),
    ('faculty', 'Annie Oye', '', 'Lecturer/HOD, Dev. & Communication Studies', 'General Education', 'Faculty of Arts & Social Sciences (FASS)', NULL, 120, 1, 'Full-time'),
    ('faculty', 'Oheneba Kofi Nti', '', 'Lecturer', 'General Education', 'Faculty of Arts & Social Sciences (FASS)', NULL, 130, 1, 'Full-time'),
    ('faculty', 'John Rhule', '', 'Lecturer', 'General Education', 'Faculty of Arts & Social Sciences (FASS)', NULL, 140, 1, 'Full-time')
ON DUPLICATE KEY UPDATE
    title = VALUES(title), job_title = VALUES(job_title), department = VALUES(department),
    faculty_group = VALUES(faculty_group), staff_category = VALUES(staff_category),
    sort_order = VALUES(sort_order), is_active = 1;

-- FACULTY — Development Studies
INSERT INTO directory
    (type, name, title, job_title, department, faculty_group, staff_category, sort_order, is_active, employment_status)
VALUES
    ('faculty', 'Adelaide Gyasi', '', 'Lecturer', 'Development Studies', 'Faculty of Arts & Social Sciences (FASS)', NULL, 150, 1, 'Full-time'),
    ('faculty', 'Michael Amponsah Kodom', '', 'Lecturer/Rector, Techiman Campus', 'Development Studies', 'Faculty of Arts & Social Sciences (FASS)', NULL, 160, 1, 'Full-time'),
    ('faculty', 'Samuel Elvis Addo', '', 'Lecturer', 'Development Studies', 'Faculty of Arts & Social Sciences (FASS)', NULL, 170, 1, 'Full-time')
ON DUPLICATE KEY UPDATE
    title = VALUES(title), job_title = VALUES(job_title), department = VALUES(department),
    faculty_group = VALUES(faculty_group), staff_category = VALUES(staff_category),
    sort_order = VALUES(sort_order), is_active = 1;

-- FACULTY — School of Education
INSERT INTO directory
    (type, name, title, job_title, department, faculty_group, staff_category, sort_order, is_active, employment_status)
VALUES
    ('faculty', 'Charles Kwaku Fobi', '', 'Lecturer', 'School of Education', 'School of Education', NULL, 180, 1, 'Full-time'),
    ('faculty', 'Ebenezer Danquah', '', 'Lecturer', 'School of Education', 'School of Education', NULL, 190, 1, 'Full-time'),
    ('faculty', 'Ellen Osei', '', 'Lecturer', 'School of Education', 'School of Education', NULL, 200, 1, 'Full-time'),
    ('faculty', 'Emmanuel Ayisi Asare', '', 'Lecturer', 'School of Education', 'School of Education', NULL, 210, 1, 'Full-time'),
    ('faculty', 'Emmanuel Duncan', '', 'Lecturer', 'School of Education', 'School of Education', NULL, 220, 1, 'Full-time'),
    ('faculty', 'Emmanuel Koomson', '', 'Lecturer', 'School of Education', 'School of Education', NULL, 230, 1, 'Full-time'),
    ('faculty', 'Felix Awutey', '', 'Assistant Lecturer', 'School of Education', 'School of Education', NULL, 240, 1, 'Full-time'),
    ('faculty', 'Felix Oppong Asamoah', '', 'Senior Lecturer/HOD, Teacher Education', 'School of Education', 'School of Education', NULL, 250, 1, 'Full-time'),
    ('faculty', 'James Ussher', '', 'Lecturer', 'School of Education', 'School of Education', NULL, 260, 1, 'Full-time'),
    ('faculty', 'Joel Nunana Atieku', '', 'Lecturer', 'School of Education', 'School of Education', NULL, 270, 1, 'Full-time'),
    ('faculty', 'Mohammed Abubakar', '', 'Lecturer', 'School of Education', 'School of Education', NULL, 280, 1, 'Full-time'),
    ('faculty', 'Sanderson Kyeraa Yeboah', '', 'Lecturer/Vice Dean', 'School of Education', 'School of Education', NULL, 290, 1, 'Full-time')
ON DUPLICATE KEY UPDATE
    title = VALUES(title), job_title = VALUES(job_title), department = VALUES(department),
    faculty_group = VALUES(faculty_group), staff_category = VALUES(staff_category),
    sort_order = VALUES(sort_order), is_active = 1;

-- FACULTY — School of Business
INSERT INTO directory
    (type, name, title, job_title, department, faculty_group, staff_category, sort_order, is_active, employment_status)
VALUES
    ('faculty', 'Abdulai Issakah', '', 'Senior Lecturer/Dean SOB', 'School of Business', 'School of Business', NULL, 300, 1, 'Full-time'),
    ('faculty', 'Ama Oforiwaa Nkrumah', '', 'Senior Lecturer', 'School of Business', 'School of Business', NULL, 310, 1, 'Full-time'),
    ('faculty', 'Dr. Ebenezer O. Yeboah', 'Dr.', 'Lecturer/Vice Dean, School of Graduate Studies', 'School of Business', 'School of Business', NULL, 320, 1, 'Full-time'),
    ('faculty', 'Dr. Nicholas Andoh', 'Dr.', 'Lecturer', 'School of Business', 'School of Business', NULL, 330, 1, 'Full-time'),
    ('faculty', 'Emmanuel Kyereda Otabil', '', 'Lecturer', 'School of Business', 'School of Business', NULL, 340, 1, 'Full-time'),
    ('faculty', 'Eric Arhin', '', 'Assistant Lecturer', 'School of Business', 'School of Business', NULL, 350, 1, 'Full-time'),
    ('faculty', 'Evans O.N.D. Ocansey', '', 'Senior Lecturer', 'School of Business', 'School of Business', NULL, 360, 1, 'Full-time'),
    ('faculty', 'Evans Owusu Acheampong', '', 'Assistant Lecturer', 'School of Business', 'School of Business', NULL, 370, 1, 'Full-time'),
    ('faculty', 'Faustina Oduro-Twum', '', 'Lecturer/Hall Dean', 'School of Business', 'School of Business', NULL, 380, 1, 'Full-time'),
    ('faculty', 'Gerald Dapaah Gyamfi', '', 'Senior Lecturer', 'School of Business', 'School of Business', NULL, 390, 1, 'Full-time'),
    ('faculty', 'Godfred Mawutor', '', 'Senior Lecturer', 'School of Business', 'School of Business', NULL, 400, 1, 'Full-time'),
    ('faculty', 'Hannah Fosuaa Amo', '', 'Senior Lecturer/HOD, Accounting and Finance', 'School of Business', 'School of Business', NULL, 410, 1, 'Full-time'),
    ('faculty', 'Irene Akuamoah-Boateng', '', 'Asso. Professor', 'School of Business', 'School of Business', NULL, 420, 1, 'Full-time'),
    ('faculty', 'James Narh Ayertey', '', 'Assistant Lecturer', 'School of Business', 'School of Business', NULL, 430, 1, 'Full-time'),
    ('faculty', 'Jeanette Owusu', '', 'Lecturer/Vice Rector, Kumasi Campus', 'School of Business', 'School of Business', NULL, 440, 1, 'Full-time'),
    ('faculty', 'Josephine Pepra-Mensah', '', 'Senior Lecturer/HOD, Management', 'School of Business', 'School of Business', NULL, 450, 1, 'Full-time'),
    ('faculty', 'Kwaku Opoku Ababio', '', 'Lecturer', 'School of Business', 'School of Business', NULL, 460, 1, 'Full-time'),
    ('faculty', 'Mary Sassah', '', 'Assistant Lecturer', 'School of Business', 'School of Business', NULL, 470, 1, 'Full-time'),
    ('faculty', 'Nneoma Benita Amos-Fidelis', '', 'Senior Lecturer', 'School of Business', 'School of Business', NULL, 480, 1, 'Full-time'),
    ('faculty', 'Patience Boatemaa Yamoah', '', 'Lecturer/Director, Centre for Adult and Continuing Education', 'School of Business', 'School of Business', NULL, 490, 1, 'Full-time')
ON DUPLICATE KEY UPDATE
    title = VALUES(title), job_title = VALUES(job_title), department = VALUES(department),
    faculty_group = VALUES(faculty_group), staff_category = VALUES(staff_category),
    sort_order = VALUES(sort_order), is_active = 1;

-- FACULTY — Computer Science & Information Technology
INSERT INTO directory
    (type, name, title, job_title, department, faculty_group, staff_category, sort_order, is_active, employment_status)
VALUES
    ('faculty', 'Asare Michael Tetteh', '', 'Lecturer', 'Computer Science & Information Technology', 'Faculty of Science (FOS)', NULL, 500, 1, 'Full-time'),
    ('faculty', 'Dominic D. Damoah', '', 'Asso. Professor', 'Computer Science & Information Technology', 'Faculty of Science (FOS)', NULL, 510, 1, 'Full-time'),
    ('faculty', 'Ezekiel Oko Annan', '', 'Lecturer/Vice Dean FOS, HOD Computing Sciences and Engineering', 'Computer Science & Information Technology', 'Faculty of Science (FOS)', NULL, 520, 1, 'Full-time'),
    ('faculty', 'Martin Doe', '', 'Assistant Lecturer', 'Computer Science & Information Technology', 'Faculty of Science (FOS)', NULL, 530, 1, 'Full-time'),
    ('faculty', 'Prince Yaw Amoako', '', 'Senior Lecturer', 'Computer Science & Information Technology', 'Faculty of Science (FOS)', NULL, 540, 1, 'Full-time'),
    ('faculty', 'Prof. William Walter Oblitey', 'Prof.', 'Professor Emeritus', 'Computer Science & Information Technology', 'Faculty of Science (FOS)', NULL, 550, 1, 'Full-time'),
    ('faculty', 'Rebecca Adwoa Amponsah', '', 'Assistant Lecturer', 'Computer Science & Information Technology', 'Faculty of Science (FOS)', NULL, 560, 1, 'Full-time'),
    ('faculty', 'Samuel Yao Sebuabe', '', 'Assistant Lecturer', 'Computer Science & Information Technology', 'Faculty of Science (FOS)', NULL, 570, 1, 'Full-time'),
    ('faculty', 'Stephen Ganu', '', 'Assistant Lecturer', 'Computer Science & Information Technology', 'Faculty of Science (FOS)', NULL, 580, 1, 'Full-time'),
    ('faculty', 'Wrancis Ronky Amber-Doe', '', 'Senior Lecturer', 'Computer Science & Information Technology', 'Faculty of Science (FOS)', NULL, 590, 1, 'Full-time')
ON DUPLICATE KEY UPDATE
    title = VALUES(title), job_title = VALUES(job_title), department = VALUES(department),
    faculty_group = VALUES(faculty_group), staff_category = VALUES(staff_category),
    sort_order = VALUES(sort_order), is_active = 1;

-- FACULTY — Mathematics
INSERT INTO directory
    (type, name, title, job_title, department, faculty_group, staff_category, sort_order, is_active, employment_status)
VALUES
    ('faculty', 'Olivia Osei-Tutu', '', 'Lecturer/Ag. Dean, Faculty of Science', 'Mathematics', 'Faculty of Science (FOS)', NULL, 600, 1, 'Full-time'),
    ('faculty', 'Gilbert Biney', '', 'Lecturer', 'Mathematics', 'Faculty of Science (FOS)', NULL, 610, 1, 'Full-time')
ON DUPLICATE KEY UPDATE
    title = VALUES(title), job_title = VALUES(job_title), department = VALUES(department),
    faculty_group = VALUES(faculty_group), staff_category = VALUES(staff_category),
    sort_order = VALUES(sort_order), is_active = 1;

-- FACULTY — Biomedical Sciences
INSERT INTO directory
    (type, name, title, job_title, department, faculty_group, staff_category, sort_order, is_active, employment_status)
VALUES
    ('faculty', 'Emmanuel Prah', '', 'Lecturer', 'Biomedical Sciences', 'Faculty of Science (FOS)', NULL, 620, 1, 'Full-time'),
    ('faculty', 'John Kwaku Kutor', '', 'Senior Lecturer', 'Biomedical Sciences', 'Faculty of Science (FOS)', NULL, 630, 1, 'Full-time'),
    ('faculty', 'John Okyere Asirifi', '', 'Lecturer', 'Biomedical Sciences', 'Faculty of Science (FOS)', NULL, 640, 1, 'Full-time')
ON DUPLICATE KEY UPDATE
    title = VALUES(title), job_title = VALUES(job_title), department = VALUES(department),
    faculty_group = VALUES(faculty_group), staff_category = VALUES(staff_category),
    sort_order = VALUES(sort_order), is_active = 1;

-- FACULTY — Agribusiness & Agricultural Science
INSERT INTO directory
    (type, name, title, job_title, department, faculty_group, staff_category, sort_order, is_active, employment_status)
VALUES
    ('faculty', 'Dr. Emmanuel Opoku', 'Dr.', 'HOD, Agribusiness/Agric Science', 'Agribusiness & Agricultural Science', 'Faculty of Science (FOS)', NULL, 650, 1, 'Full-time')
ON DUPLICATE KEY UPDATE
    title = VALUES(title), job_title = VALUES(job_title), department = VALUES(department),
    faculty_group = VALUES(faculty_group), staff_category = VALUES(staff_category),
    sort_order = VALUES(sort_order), is_active = 1;

-- FACULTY — Nursing and Midwifery
INSERT INTO directory
    (type, name, title, job_title, department, faculty_group, staff_category, sort_order, is_active, employment_status)
VALUES
    ('faculty', 'Alfred Yanful Ahenkorah', '', 'Lecturer', 'Nursing and Midwifery', 'Faculty of Science (FOS)', NULL, 660, 1, 'Full-time'),
    ('faculty', 'Awube Menlah', '', 'Lecturer', 'Nursing and Midwifery', 'Faculty of Science (FOS)', NULL, 670, 1, 'Full-time'),
    ('faculty', 'Bernadette B. Bortey', '', 'Assistant Lecturer', 'Nursing and Midwifery', 'Faculty of Science (FOS)', NULL, 680, 1, 'Full-time'),
    ('faculty', 'Cynthia Essel', '', 'Assistant Lecturer', 'Nursing and Midwifery', 'Faculty of Science (FOS)', NULL, 690, 1, 'Full-time'),
    ('faculty', 'Deborah Olayinka', '', 'Assistant Lecturer', 'Nursing and Midwifery', 'Faculty of Science (FOS)', NULL, 700, 1, 'Full-time'),
    ('faculty', 'Doris Grace Kpongboe', '', 'Lecturer', 'Nursing and Midwifery', 'Faculty of Science (FOS)', NULL, 710, 1, 'Full-time'),
    ('faculty', 'Dorothy Baffour Awuah', '', 'Lecturer/Ag. HOD, Nursing and Health Sciences', 'Nursing and Midwifery', 'Faculty of Science (FOS)', NULL, 720, 1, 'Full-time'),
    ('faculty', 'Isaac Dornu Abayateye', '', 'Assistant Lecturer', 'Nursing and Midwifery', 'Faculty of Science (FOS)', NULL, 730, 1, 'Full-time'),
    ('faculty', 'Millicent Dzifa Cudjoe', '', 'Lecturer', 'Nursing and Midwifery', 'Faculty of Science (FOS)', NULL, 740, 1, 'Full-time'),
    ('faculty', 'Nana Betse Morson', '', 'Assistant Lecturer', 'Nursing and Midwifery', 'Faculty of Science (FOS)', NULL, 750, 1, 'Full-time'),
    ('faculty', 'Patience Norko Nortey', '', 'Assistant Lecturer', 'Nursing and Midwifery', 'Faculty of Science (FOS)', NULL, 760, 1, 'Full-time'),
    ('faculty', 'William Menkah', '', 'Lecturer', 'Nursing and Midwifery', 'Faculty of Science (FOS)', NULL, 770, 1, 'Full-time'),
    ('faculty', 'Wisdom Yaw Kpolar Okpowura', '', 'Assistant Lecturer', 'Nursing and Midwifery', 'Faculty of Science (FOS)', NULL, 780, 1, 'Full-time'),
    ('faculty', 'Christian Benedict Adjei-Afam', '', 'Assistant Lecturer', 'Nursing and Midwifery', 'Faculty of Science (FOS)', NULL, 790, 1, 'Full-time')
ON DUPLICATE KEY UPDATE
    title = VALUES(title), job_title = VALUES(job_title), department = VALUES(department),
    faculty_group = VALUES(faculty_group), staff_category = VALUES(staff_category),
    sort_order = VALUES(sort_order), is_active = 1;

-- FACULTY — Techiman Campus
INSERT INTO directory
    (type, name, title, job_title, department, faculty_group, staff_category, sort_order, is_active, employment_status)
VALUES
    ('faculty', 'Dr. Asare Bediako Ankrah', 'Dr.', 'Vice Rector, Techiman Campus', 'Techiman Campus', 'Campus Leadership', NULL, 800, 1, 'Full-time')
ON DUPLICATE KEY UPDATE
    title = VALUES(title), job_title = VALUES(job_title), department = VALUES(department),
    faculty_group = VALUES(faculty_group), staff_category = VALUES(staff_category),
    sort_order = VALUES(sort_order), is_active = 1;

-- FACULTY — Kumasi Campus
INSERT INTO directory
    (type, name, title, job_title, department, faculty_group, staff_category, sort_order, is_active, employment_status)
VALUES
    ('faculty', 'Dr. Patricia Nyamekye', 'Dr.', 'Rector, Kumasi Campus', 'Kumasi Campus', 'Campus Leadership', NULL, 810, 1, 'Full-time')
ON DUPLICATE KEY UPDATE
    title = VALUES(title), job_title = VALUES(job_title), department = VALUES(department),
    faculty_group = VALUES(faculty_group), staff_category = VALUES(staff_category),
    sort_order = VALUES(sort_order), is_active = 1;

-- SENIOR MEMBER — Vice Chancellery
INSERT INTO directory
    (type, name, title, job_title, department, faculty_group, staff_category, sort_order, is_active, employment_status)
VALUES
    ('staff', 'Prof. Daniel Ganu', 'Prof.', 'Professor/Vice Chancellor', 'Vice Chancellery', 'Non-Teaching Senior Members', 'senior_member', 10, 1, 'Full-time'),
    ('staff', 'Prof. Peter Agyekum Boateng', 'Prof.', 'Pro-Vice Chancellor', 'Vice Chancellery', 'Non-Teaching Senior Members', 'senior_member', 20, 1, 'Full-time')
ON DUPLICATE KEY UPDATE
    title = VALUES(title), job_title = VALUES(job_title), department = VALUES(department),
    faculty_group = VALUES(faculty_group), staff_category = VALUES(staff_category),
    sort_order = VALUES(sort_order), is_active = 1;

-- SENIOR MEMBER — Chaplaincy
INSERT INTO directory
    (type, name, title, job_title, department, faculty_group, staff_category, sort_order, is_active, employment_status)
VALUES
    ('staff', 'Pastor Peter Obeng Manu', 'Pastor', 'Ag. Dean, Spiritual Development', 'Chaplaincy', 'Non-Teaching Senior Members', 'senior_member', 30, 1, 'Full-time'),
    ('staff', 'Pastor Kusi Appiah', 'Pastor', 'Associate Chaplain', 'Chaplaincy', 'Non-Teaching Senior Members', 'senior_member', 40, 1, 'Full-time'),
    ('staff', 'Pastor Emmanuel Osei', 'Pastor', 'Pastor, VVU', 'Chaplaincy', 'Non-Teaching Senior Members', 'senior_member', 50, 1, 'Full-time')
ON DUPLICATE KEY UPDATE
    title = VALUES(title), job_title = VALUES(job_title), department = VALUES(department),
    faculty_group = VALUES(faculty_group), staff_category = VALUES(staff_category),
    sort_order = VALUES(sort_order), is_active = 1;

-- SENIOR MEMBER — Information and Library Services
INSERT INTO directory
    (type, name, title, job_title, department, faculty_group, staff_category, sort_order, is_active, employment_status)
VALUES
    ('staff', 'Samuel Ameyaw', '', 'Senior Assist. Librarian', 'Information and Library Services', 'Non-Teaching Senior Members', 'senior_member', 60, 1, 'Full-time'),
    ('staff', 'Charles N. Amoah', '', 'Librarian', 'Information and Library Services', 'Non-Teaching Senior Members', 'senior_member', 70, 1, 'Full-time')
ON DUPLICATE KEY UPDATE
    title = VALUES(title), job_title = VALUES(job_title), department = VALUES(department),
    faculty_group = VALUES(faculty_group), staff_category = VALUES(staff_category),
    sort_order = VALUES(sort_order), is_active = 1;

-- SENIOR MEMBER — Information Technology
INSERT INTO directory
    (type, name, title, job_title, department, faculty_group, staff_category, sort_order, is_active, employment_status)
VALUES
    ('staff', 'Edmund Tordan', '', 'Head of Infrastructure', 'Information Technology', 'Non-Teaching Senior Members', 'senior_member', 80, 1, 'Full-time')
ON DUPLICATE KEY UPDATE
    title = VALUES(title), job_title = VALUES(job_title), department = VALUES(department),
    faculty_group = VALUES(faculty_group), staff_category = VALUES(staff_category),
    sort_order = VALUES(sort_order), is_active = 1;

-- SENIOR MEMBER — Financial Administration
INSERT INTO directory
    (type, name, title, job_title, department, faculty_group, staff_category, sort_order, is_active, employment_status)
VALUES
    ('staff', 'Francis Osei-Kuffour', '', 'Chief Financial Officer', 'Financial Administration', 'Non-Teaching Senior Members', 'senior_member', 90, 1, 'Full-time'),
    ('staff', 'Amos Asante', '', 'Senior Accountant', 'Financial Administration', 'Non-Teaching Senior Members', 'senior_member', 100, 1, 'Full-time'),
    ('staff', 'Enoch Amoah Mintah', '', 'Senior Accountant', 'Financial Administration', 'Non-Teaching Senior Members', 'senior_member', 110, 1, 'Full-time'),
    ('staff', 'Faustina Brefo Darkwa', '', 'Accountant', 'Financial Administration', 'Non-Teaching Senior Members', 'senior_member', 120, 1, 'Full-time'),
    ('staff', 'James Owusu', '', 'Accountant', 'Financial Administration', 'Non-Teaching Senior Members', 'senior_member', 130, 1, 'Full-time'),
    ('staff', 'John Adjei-Gbenda', '', 'Deputy Auditor', 'Financial Administration', 'Non-Teaching Senior Members', 'senior_member', 140, 1, 'Full-time'),
    ('staff', 'Richard Offeh Bediako', '', 'Assistant Accountant', 'Financial Administration', 'Non-Teaching Senior Members', 'senior_member', 150, 1, 'Full-time'),
    ('staff', 'Samuel Anang Sowah', '', 'Senior Accountant', 'Financial Administration', 'Non-Teaching Senior Members', 'senior_member', 160, 1, 'Full-time')
ON DUPLICATE KEY UPDATE
    title = VALUES(title), job_title = VALUES(job_title), department = VALUES(department),
    faculty_group = VALUES(faculty_group), staff_category = VALUES(staff_category),
    sort_order = VALUES(sort_order), is_active = 1;

-- SENIOR MEMBER — Audit
INSERT INTO directory
    (type, name, title, job_title, department, faculty_group, staff_category, sort_order, is_active, employment_status)
VALUES
    ('staff', 'Boakye Kaakyire Amponsah', '', 'Auditor/Internal Auditor', 'Audit', 'Non-Teaching Senior Members', 'senior_member', 170, 1, 'Full-time')
ON DUPLICATE KEY UPDATE
    title = VALUES(title), job_title = VALUES(job_title), department = VALUES(department),
    faculty_group = VALUES(faculty_group), staff_category = VALUES(staff_category),
    sort_order = VALUES(sort_order), is_active = 1;

-- SENIOR MEMBER — Registrar's Department
INSERT INTO directory
    (type, name, title, job_title, department, faculty_group, staff_category, sort_order, is_active, employment_status)
VALUES
    ('staff', 'Beatrice Sonful', '', 'Assistant Registrar', 'Registrar''s Department', 'Non-Teaching Senior Members', 'senior_member', 180, 1, 'Full-time'),
    ('staff', 'Emmanuel Ayi', '', 'Systems Administrator', 'Registrar''s Department', 'Non-Teaching Senior Members', 'senior_member', 190, 1, 'Full-time'),
    ('staff', 'Florence Adei Kotei', '', 'Snr. Asst. Registrar', 'Registrar''s Department', 'Non-Teaching Senior Members', 'senior_member', 200, 1, 'Full-time'),
    ('staff', 'Gift O. Amoah', '', 'Assistant Registrar/Hall Dean', 'Registrar''s Department', 'Non-Teaching Senior Members', 'senior_member', 210, 1, 'Full-time'),
    ('staff', 'Gifty A.Y. Aidoo', '', 'Snr. Asst. Registrar/QA', 'Registrar''s Department', 'Non-Teaching Senior Members', 'senior_member', 220, 1, 'Full-time'),
    ('staff', 'Harriet Narkie Asare', '', 'Asst. Registrar/Admin SGS', 'Registrar''s Department', 'Non-Teaching Senior Members', 'senior_member', 230, 1, 'Full-time'),
    ('staff', 'Irene L. Ago', '', 'Snr. Asst. Registrar', 'Registrar''s Department', 'Non-Teaching Senior Members', 'senior_member', 240, 1, 'Full-time'),
    ('staff', 'James Adotey Allotey', '', 'Asst. Registrar/Director, Academic Affairs', 'Registrar''s Department', 'Non-Teaching Senior Members', 'senior_member', 250, 1, 'Full-time'),
    ('staff', 'Kofi Agyemang Boateng', '', 'Assistant Registrar', 'Registrar''s Department', 'Non-Teaching Senior Members', 'senior_member', 260, 1, 'Full-time'),
    ('staff', 'Louis Kwame Coffie', '', 'Director, Works', 'Registrar''s Department', 'Non-Teaching Senior Members', 'senior_member', 270, 1, 'Full-time'),
    ('staff', 'Martin Kudjo Akotey', '', 'Asso. Prof./Dean, School of Graduate Studies', 'Registrar''s Department', 'Non-Teaching Senior Members', 'senior_member', 280, 1, 'Full-time'),
    ('staff', 'Mary Agyeman', '', 'Asst. Registrar', 'Registrar''s Department', 'Non-Teaching Senior Members', 'senior_member', 290, 1, 'Full-time'),
    ('staff', 'Richael Amoah', '', 'Asst. Research Fellow', 'Registrar''s Department', 'Non-Teaching Senior Members', 'senior_member', 300, 1, 'Full-time'),
    ('staff', 'Samuel K. Amankwah', '', 'Deputy Registrar/Registrar', 'Registrar''s Department', 'Non-Teaching Senior Members', 'senior_member', 310, 1, 'Full-time'),
    ('staff', 'Samuel Yaw Boateng', '', 'Asst. Registrar', 'Registrar''s Department', 'Non-Teaching Senior Members', 'senior_member', 320, 1, 'Full-time'),
    ('staff', 'Sarah Opoku-Boateng', '', 'Senior Asst. Registrar/Director, URO', 'Registrar''s Department', 'Non-Teaching Senior Members', 'senior_member', 330, 1, 'Full-time'),
    ('staff', 'Solomon Addai', '', 'Assoc. Director, Ecological Management - Physical Development & Estate Mgt. Directorate', 'Registrar''s Department', 'Non-Teaching Senior Members', 'senior_member', 340, 1, 'Full-time'),
    ('staff', 'Sonny Arthur Davis', '', 'Asst. Registrar', 'Registrar''s Department', 'Non-Teaching Senior Members', 'senior_member', 350, 1, 'Full-time'),
    ('staff', 'Abigail Amankwah', '', 'Prin. Accounting Assistant/Manageress, Bakery', 'Registrar''s Department', 'Non-Teaching Senior Members', 'senior_member', 360, 1, 'Full-time'),
    ('staff', 'Flacus Kofi Afriyie Amponsah', '', 'Senior Asst. Registrar/Director, Human Resource Management & Development Directorate', 'Registrar''s Department', 'Non-Teaching Senior Members', 'senior_member', 370, 1, 'Full-time'),
    ('staff', 'Albert Amo-Asimeng', '', 'Senior Asst. Registrar/Associate Director, Training & Development', 'Registrar''s Department', 'Non-Teaching Senior Members', 'senior_member', 380, 1, 'Full-time'),
    ('staff', 'Amos Opoku-Boateng', '', 'Senior Asst. Registrar/Director, Administration and Legal Affairs', 'Registrar''s Department', 'Non-Teaching Senior Members', 'senior_member', 390, 1, 'Full-time')
ON DUPLICATE KEY UPDATE
    title = VALUES(title), job_title = VALUES(job_title), department = VALUES(department),
    faculty_group = VALUES(faculty_group), staff_category = VALUES(staff_category),
    sort_order = VALUES(sort_order), is_active = 1;

-- SENIOR MEMBER — Ventures
INSERT INTO directory
    (type, name, title, job_title, department, faculty_group, staff_category, sort_order, is_active, employment_status)
VALUES
    ('staff', 'Elesi Adwoa Adeku', '', 'Snr. Technologist Asst./General Manager, University Enterprises & Water Factory', 'Ventures', 'Non-Teaching Senior Members', 'senior_member', 400, 1, 'Full-time')
ON DUPLICATE KEY UPDATE
    title = VALUES(title), job_title = VALUES(job_title), department = VALUES(department),
    faculty_group = VALUES(faculty_group), staff_category = VALUES(staff_category),
    sort_order = VALUES(sort_order), is_active = 1;

-- SENIOR STAFF
INSERT INTO directory
    (type, name, title, job_title, department, faculty_group, staff_category, sort_order, is_active, employment_status)
VALUES
    ('staff', 'Abeiku Dadzie-Nyan', '', 'Senior Admin. Assistant', 'Administration', 'Senior Staff', 'senior_staff', 10, 1, 'Full-time'),
    ('staff', 'Abochie Gaison', '', 'Senior Admin. Assistant', 'Administration', 'Senior Staff', 'senior_staff', 20, 1, 'Full-time'),
    ('staff', 'Abraham Korley', '', 'Chief Admin. Assistant', 'Administration', 'Senior Staff', 'senior_staff', 30, 1, 'Full-time'),
    ('staff', 'Benjamin Agyei Darko', '', 'Senior Admin. Assistant', 'Administration', 'Senior Staff', 'senior_staff', 40, 1, 'Full-time'),
    ('staff', 'Benjamin K. Manu Arekenya', '', 'Senior Admin. Assistant', 'Administration', 'Senior Staff', 'senior_staff', 50, 1, 'Full-time'),
    ('staff', 'Chief Inspec. Thomas Napol', 'Chief Insp.', 'Head of Security', 'Security Services', 'Senior Staff', 'senior_staff', 60, 1, 'Full-time'),
    ('staff', 'Clara Araba B. Taylor', '', 'Chief Admin. Assistant, HRMDD', 'Human Resource Management & Development', 'Senior Staff', 'senior_staff', 70, 1, 'Full-time'),
    ('staff', 'Comfort Asadina', '', 'Principal Admin. Assistant', 'Administration', 'Senior Staff', 'senior_staff', 80, 1, 'Full-time'),
    ('staff', 'Daniel Okoe Okai', '', 'Senior Admin. Assistant', 'Administration', 'Senior Staff', 'senior_staff', 90, 1, 'Full-time'),
    ('staff', 'Derrick Azamalah', '', 'Senior Admin. Assistant', 'Administration', 'Senior Staff', 'senior_staff', 100, 1, 'Full-time'),
    ('staff', 'Dorah Apraku', '', 'Senior Hall Assistant', 'Halls of Residence', 'Senior Staff', 'senior_staff', 110, 1, 'Full-time'),
    ('staff', 'Douglas Owusu', '', 'Prin. Estate Assistant', 'Physical Development & Estate Management', 'Senior Staff', 'senior_staff', 120, 1, 'Full-time'),
    ('staff', 'Edward Twum Antwi Baah', '', 'Teaching Assistant', 'Academic Support', 'Senior Staff', 'senior_staff', 130, 1, 'Full-time'),
    ('staff', 'Elizabeth Takyi', '', 'Principal Library Assistant', 'Library Services', 'Senior Staff', 'senior_staff', 140, 1, 'Full-time'),
    ('staff', 'Ellen Amponsah', '', 'Principal Library Assistant', 'Library Services', 'Senior Staff', 'senior_staff', 150, 1, 'Full-time'),
    ('staff', 'Emma Serwaa Osei', '', 'Senior Admin. Assistant', 'Administration', 'Senior Staff', 'senior_staff', 160, 1, 'Full-time'),
    ('staff', 'Enoch Adjei', '', 'Senior Admin. Assistant', 'Administration', 'Senior Staff', 'senior_staff', 170, 1, 'Full-time'),
    ('staff', 'Eric Kyeremeh', '', 'Principal Admin. Assistant', 'Administration', 'Senior Staff', 'senior_staff', 180, 1, 'Full-time'),
    ('staff', 'Ernestina Odame', '', 'Chief Admin. Assistant', 'Administration', 'Senior Staff', 'senior_staff', 190, 1, 'Full-time'),
    ('staff', 'Felicity Amoako Ansong', '', 'Chief Admin. Assistant', 'Administration', 'Senior Staff', 'senior_staff', 200, 1, 'Full-time'),
    ('staff', 'Florence Dellor', '', 'Prin. Accounting Assistant', 'Financial Administration', 'Senior Staff', 'senior_staff', 210, 1, 'Full-time'),
    ('staff', 'Frank Darko', '', 'Admin. Assistant', 'Administration', 'Senior Staff', 'senior_staff', 220, 1, 'Full-time'),
    ('staff', 'Frank Sackey', '', 'Senior IT Assistant', 'Information Technology', 'Senior Staff', 'senior_staff', 230, 1, 'Full-time'),
    ('staff', 'Gabriel Adu Ameyaw', '', 'Chief Works Assistant', 'Works & Maintenance', 'Senior Staff', 'senior_staff', 240, 1, 'Full-time'),
    ('staff', 'Gift Tanoah Peprah', '', 'Teaching Assistant', 'Academic Support', 'Senior Staff', 'senior_staff', 250, 1, 'Full-time'),
    ('staff', 'Godfred Kwesi Ackah', '', 'Senior Admin. Assistant', 'Administration', 'Senior Staff', 'senior_staff', 260, 1, 'Full-time'),
    ('staff', 'Jacob Hokey', '', 'Senior Library Assistant', 'Library Services', 'Senior Staff', 'senior_staff', 270, 1, 'Full-time'),
    ('staff', 'Japhter Darkwah Agyei', '', 'Skills Lab Instructor', 'Skills Laboratory', 'Senior Staff', 'senior_staff', 280, 1, 'Full-time'),
    ('staff', 'Magdalene Attoh', '', 'Chief Admin. Assistant', 'Administration', 'Senior Staff', 'senior_staff', 290, 1, 'Full-time'),
    ('staff', 'Margaret A. Anang', '', 'Snr. Procurement Assistant', 'Procurement', 'Senior Staff', 'senior_staff', 300, 1, 'Full-time'),
    ('staff', 'Mariam Amidu', '', 'Senior Hall Assistant', 'Halls of Residence', 'Senior Staff', 'senior_staff', 310, 1, 'Full-time'),
    ('staff', 'Martin K. Amenyaglo', '', 'Senior Admin. Assistant', 'Administration', 'Senior Staff', 'senior_staff', 320, 1, 'Full-time'),
    ('staff', 'Mavis Addai', '', 'Senior Accounting Asst.', 'Financial Administration', 'Senior Staff', 'senior_staff', 330, 1, 'Full-time'),
    ('staff', 'Nana Yaa Asante Asirifi', '', 'Principal Admin. Assistant', 'Administration', 'Senior Staff', 'senior_staff', 340, 1, 'Full-time'),
    ('staff', 'Naomi Faakye', '', 'Director, Food Services', 'Food Services', 'Senior Staff', 'senior_staff', 350, 1, 'Full-time'),
    ('staff', 'Naomi Obeng', '', 'Prin. Accounting Assistant', 'Financial Administration', 'Senior Staff', 'senior_staff', 360, 1, 'Full-time'),
    ('staff', 'Nkechuku K. E. Amoah', '', 'Teaching Assistant', 'Academic Support', 'Senior Staff', 'senior_staff', 370, 1, 'Full-time'),
    ('staff', 'Obed Bour', '', 'Senior Admin. Assistant', 'Administration', 'Senior Staff', 'senior_staff', 380, 1, 'Full-time'),
    ('staff', 'Ophelia Dankwah', '', 'Production Officer', 'University Enterprises', 'Senior Staff', 'senior_staff', 390, 1, 'Full-time'),
    ('staff', 'Patricia Kwawukume', '', 'Chief Admin. Assistant', 'Administration', 'Senior Staff', 'senior_staff', 400, 1, 'Full-time'),
    ('staff', 'Philomena Tamakloe', '', 'Chief Admin. Assistant', 'Administration', 'Senior Staff', 'senior_staff', 410, 1, 'Full-time'),
    ('staff', 'Princess Quartey', '', 'Principal Library Assistant', 'Library Services', 'Senior Staff', 'senior_staff', 420, 1, 'Full-time'),
    ('staff', 'Raymond Kwaku Boateng', '', 'Teaching Assistant', 'Academic Support', 'Senior Staff', 'senior_staff', 430, 1, 'Full-time'),
    ('staff', 'Rebecca Ayi Narteh', '', 'Senior Accounting Asst.', 'Financial Administration', 'Senior Staff', 'senior_staff', 440, 1, 'Full-time'),
    ('staff', 'Richard Bortey', '', 'Auditing Assistant', 'Internal Audit', 'Senior Staff', 'senior_staff', 450, 1, 'Full-time'),
    ('staff', 'Richard Etwire', '', 'Senior Accounting Asst.', 'Financial Administration', 'Senior Staff', 'senior_staff', 460, 1, 'Full-time'),
    ('staff', 'Rockson Nai Boadu', '', 'Senior Admin. Assistant', 'Administration', 'Senior Staff', 'senior_staff', 470, 1, 'Full-time'),
    ('staff', 'Samuel Agyei Gyekye', '', 'Senior Hall Assistant', 'Halls of Residence', 'Senior Staff', 'senior_staff', 480, 1, 'Full-time'),
    ('staff', 'Sethina Efua Arthur', '', 'Prin. Accounting Assistant', 'Financial Administration', 'Senior Staff', 'senior_staff', 490, 1, 'Full-time'),
    ('staff', 'Shadrack Koduah Amoateng', '', 'Teaching Assistant', 'Academic Support', 'Senior Staff', 'senior_staff', 500, 1, 'Full-time'),
    ('staff', 'Spendylove Amponsah Opoku', '', 'Skills Lab Instructor', 'Skills Laboratory', 'Senior Staff', 'senior_staff', 510, 1, 'Full-time'),
    ('staff', 'Stephen Cudjoe', '', 'Senior Administrative Asst.', 'Administration', 'Senior Staff', 'senior_staff', 520, 1, 'Full-time'),
    ('staff', 'Stephen Donkor', '', 'Prin. Library Assistant', 'Library Services', 'Senior Staff', 'senior_staff', 530, 1, 'Full-time'),
    ('staff', 'Sylvia Adu-Boahen', '', 'Chief Admin. Assistant', 'Administration', 'Senior Staff', 'senior_staff', 540, 1, 'Full-time'),
    ('staff', 'Thelma Peace Barnor', '', 'Skills Lab Instructor', 'Skills Laboratory', 'Senior Staff', 'senior_staff', 550, 1, 'Full-time'),
    ('staff', 'Wilhelmina Asigbee', '', 'Prin. Library Assistant', 'Library Services', 'Senior Staff', 'senior_staff', 560, 1, 'Full-time'),
    ('staff', 'Yunis Kanyandewe', '', 'Chief Accounting Assistant', 'Financial Administration', 'Senior Staff', 'senior_staff', 570, 1, 'Full-time')
ON DUPLICATE KEY UPDATE
    title = VALUES(title), job_title = VALUES(job_title), department = VALUES(department),
    faculty_group = VALUES(faculty_group), staff_category = VALUES(staff_category),
    sort_order = VALUES(sort_order), is_active = 1;

-- JUNIOR STAFF
INSERT INTO directory
    (type, name, title, job_title, department, faculty_group, staff_category, sort_order, is_active, employment_status)
VALUES
    ('staff', 'Abubakar Tahiru', '', 'Driver/Mechanic', 'Transport', 'Junior Staff', 'junior_staff', 10, 1, 'Full-time'),
    ('staff', 'Ahiadu Veronica Esinam Akosua', '', 'Security Guard', 'Security Services', 'Junior Staff', 'junior_staff', 20, 1, 'Full-time'),
    ('staff', 'Ahuma Moses', '', 'Security Guard', 'Security Services', 'Junior Staff', 'junior_staff', 30, 1, 'Full-time'),
    ('staff', 'Alex Asante Ampadu', '', 'Driver', 'Transport', 'Junior Staff', 'junior_staff', 40, 1, 'Full-time'),
    ('staff', 'Alexander Adu-Baah', '', 'Farm Assistant', 'University Farm', 'Junior Staff', 'junior_staff', 50, 1, 'Full-time'),
    ('staff', 'Apau Domena', '', 'Security Guard II', 'Security Services', 'Junior Staff', 'junior_staff', 60, 1, 'Full-time'),
    ('staff', 'Augustine Oppong', '', 'Works Assistant', 'Works & Maintenance', 'Junior Staff', 'junior_staff', 70, 1, 'Full-time'),
    ('staff', 'Bernard Kofi Anane', '', 'Security Guard II', 'Security Services', 'Junior Staff', 'junior_staff', 80, 1, 'Full-time'),
    ('staff', 'Bismark Adjanor', '', 'Electrician', 'Works & Maintenance', 'Junior Staff', 'junior_staff', 90, 1, 'Full-time'),
    ('staff', 'Celestin Asante', '', 'Kitchen Assistant', 'Food Services', 'Junior Staff', 'junior_staff', 100, 1, 'Full-time'),
    ('staff', 'Christian Agbanyo', '', 'Cleaner', 'Sanitation & Custodial', 'Junior Staff', 'junior_staff', 110, 1, 'Full-time'),
    ('staff', 'Christopher Cudjoe', '', 'Carpenter', 'Works & Maintenance', 'Junior Staff', 'junior_staff', 120, 1, 'Full-time'),
    ('staff', 'Cynthia Brown', '', 'Senior Cook', 'Food Services', 'Junior Staff', 'junior_staff', 130, 1, 'Full-time'),
    ('staff', 'Cynthia Eshun', '', 'Cleaner', 'Sanitation & Custodial', 'Junior Staff', 'junior_staff', 140, 1, 'Full-time'),
    ('staff', 'David Kwadzo Dakpui', '', 'Farm Hand', 'University Farm', 'Junior Staff', 'junior_staff', 150, 1, 'Full-time'),
    ('staff', 'Derek Owusu', '', 'Security Guard II', 'Security Services', 'Junior Staff', 'junior_staff', 160, 1, 'Full-time'),
    ('staff', 'Dickson Banahene', '', 'Driver', 'Transport', 'Junior Staff', 'junior_staff', 170, 1, 'Full-time'),
    ('staff', 'Dogbeda Dakpui', '', 'Groundsman', 'Grounds & Landscaping', 'Junior Staff', 'junior_staff', 180, 1, 'Full-time'),
    ('staff', 'Ebenezer Adupreh', '', 'Security Guard', 'Security Services', 'Junior Staff', 'junior_staff', 190, 1, 'Full-time'),
    ('staff', 'Ebenezer Mante', '', 'Sales Person - CAF', 'University Enterprises', 'Junior Staff', 'junior_staff', 200, 1, 'Full-time'),
    ('staff', 'Eddison Djotepeh', '', 'Carpenter', 'Works & Maintenance', 'Junior Staff', 'junior_staff', 210, 1, 'Full-time'),
    ('staff', 'Elias Abradu', '', 'Security Guard II', 'Security Services', 'Junior Staff', 'junior_staff', 220, 1, 'Full-time'),
    ('staff', 'Elijah Awadzu', '', 'Groundsman', 'Grounds & Landscaping', 'Junior Staff', 'junior_staff', 230, 1, 'Full-time'),
    ('staff', 'Emmanuel Cobby Apreku', '', 'Security Guard', 'Security Services', 'Junior Staff', 'junior_staff', 240, 1, 'Full-time'),
    ('staff', 'Emmanuel Addo', '', 'Library Assistant', 'Library Services', 'Junior Staff', 'junior_staff', 250, 1, 'Full-time'),
    ('staff', 'Emmanuel Kwasi Adofo', '', 'Driver', 'Transport', 'Junior Staff', 'junior_staff', 260, 1, 'Full-time'),
    ('staff', 'Enoch Nsoah', '', 'Groundsman', 'Grounds & Landscaping', 'Junior Staff', 'junior_staff', 270, 1, 'Full-time'),
    ('staff', 'Eric Quaye', '', 'Driver', 'Transport', 'Junior Staff', 'junior_staff', 280, 1, 'Full-time'),
    ('staff', 'Esther Abetiah', '', 'Cleaner', 'Sanitation & Custodial', 'Junior Staff', 'junior_staff', 290, 1, 'Full-time'),
    ('staff', 'Esther Otoo', '', 'Senior Cook', 'Food Services', 'Junior Staff', 'junior_staff', 300, 1, 'Full-time'),
    ('staff', 'Eunice Mawufemor Sunu', '', 'Cleaner', 'Sanitation & Custodial', 'Junior Staff', 'junior_staff', 310, 1, 'Full-time'),
    ('staff', 'Felicia Yayra Doe', '', 'Security Guard', 'Security Services', 'Junior Staff', 'junior_staff', 320, 1, 'Full-time'),
    ('staff', 'Fred Apraku', '', 'Cleaner', 'Sanitation & Custodial', 'Junior Staff', 'junior_staff', 330, 1, 'Full-time'),
    ('staff', 'George Abbey', '', 'Driver', 'Transport', 'Junior Staff', 'junior_staff', 340, 1, 'Full-time'),
    ('staff', 'Gideon Agbayisah', '', 'Cleaner', 'Sanitation & Custodial', 'Junior Staff', 'junior_staff', 350, 1, 'Full-time'),
    ('staff', 'Gladys Asare', '', 'Kitchen Assistant', 'Food Services', 'Junior Staff', 'junior_staff', 360, 1, 'Full-time'),
    ('staff', 'Grace Ohene', '', 'Snr. Cook', 'Food Services', 'Junior Staff', 'junior_staff', 370, 1, 'Full-time'),
    ('staff', 'Grace Vifah', '', 'Security Guard', 'Security Services', 'Junior Staff', 'junior_staff', 380, 1, 'Full-time'),
    ('staff', 'Hannah Quayson', '', 'Cleaner/Messenger', 'Sanitation & Custodial', 'Junior Staff', 'junior_staff', 390, 1, 'Full-time'),
    ('staff', 'Isaac Aboagye', '', 'Cleaner/Messenger', 'Sanitation & Custodial', 'Junior Staff', 'junior_staff', 400, 1, 'Full-time'),
    ('staff', 'Isaac Azaglo', '', 'Security Guard II', 'Security Services', 'Junior Staff', 'junior_staff', 410, 1, 'Full-time'),
    ('staff', 'Isaac Mensah', '', 'Farm Hand', 'University Farm', 'Junior Staff', 'junior_staff', 420, 1, 'Full-time'),
    ('staff', 'Isaac Norgbedzi', '', 'Farm Hand', 'University Farm', 'Junior Staff', 'junior_staff', 430, 1, 'Full-time'),
    ('staff', 'Joseph Darko', '', 'Pantry', 'Food Services', 'Junior Staff', 'junior_staff', 440, 1, 'Full-time'),
    ('staff', 'Justice Adjei Laryea', '', 'Security Guard II', 'Security Services', 'Junior Staff', 'junior_staff', 450, 1, 'Full-time'),
    ('staff', 'Kennedy Gblorkpor', '', 'Kitchen Assistant', 'Food Services', 'Junior Staff', 'junior_staff', 460, 1, 'Full-time'),
    ('staff', 'Kenneth Fosu', '', 'Chief Driver', 'Transport', 'Junior Staff', 'junior_staff', 470, 1, 'Full-time'),
    ('staff', 'Kossi Awoume', '', 'Groundsman', 'Grounds & Landscaping', 'Junior Staff', 'junior_staff', 480, 1, 'Full-time'),
    ('staff', 'Linda Adamu Musah', '', 'Security Guard', 'Security Services', 'Junior Staff', 'junior_staff', 490, 1, 'Full-time'),
    ('staff', 'Linda Esi Amponsah', '', 'Security Guard', 'Security Services', 'Junior Staff', 'junior_staff', 500, 1, 'Full-time'),
    ('staff', 'Livingstone Kofitia', '', 'Security Guard', 'Security Services', 'Junior Staff', 'junior_staff', 510, 1, 'Full-time'),
    ('staff', 'Lydia Koranteng', '', 'Kitchen Assistant', 'Food Services', 'Junior Staff', 'junior_staff', 520, 1, 'Full-time'),
    ('staff', 'Margaret Boateng', '', 'Kitchen Assistant', 'Food Services', 'Junior Staff', 'junior_staff', 530, 1, 'Full-time'),
    ('staff', 'Mary Nkrumah', '', 'Security Guard II', 'Security Services', 'Junior Staff', 'junior_staff', 540, 1, 'Full-time'),
    ('staff', 'Matilda Asare', '', 'Kitchen Assistant', 'Food Services', 'Junior Staff', 'junior_staff', 550, 1, 'Full-time'),
    ('staff', 'Monica Owusu Afriyie', '', 'Security Guard II', 'Security Services', 'Junior Staff', 'junior_staff', 560, 1, 'Full-time'),
    ('staff', 'Moses Vakpo', '', 'Cleaner', 'Sanitation & Custodial', 'Junior Staff', 'junior_staff', 570, 1, 'Full-time'),
    ('staff', 'Obeng Darko', '', 'Security Guard II', 'Security Services', 'Junior Staff', 'junior_staff', 580, 1, 'Full-time'),
    ('staff', 'Osei Otiwaa', '', 'Cleaner', 'Sanitation & Custodial', 'Junior Staff', 'junior_staff', 590, 1, 'Full-time'),
    ('staff', 'Patience Mawufemor Avor', '', 'Security Guard', 'Security Services', 'Junior Staff', 'junior_staff', 600, 1, 'Full-time'),
    ('staff', 'Paul Tawiah', '', 'Cleaner', 'Sanitation & Custodial', 'Junior Staff', 'junior_staff', 610, 1, 'Full-time'),
    ('staff', 'Peter Katere', '', 'Farm Hand', 'University Farm', 'Junior Staff', 'junior_staff', 620, 1, 'Full-time'),
    ('staff', 'Peter N. Kpogoh', '', 'Security Guard', 'Security Services', 'Junior Staff', 'junior_staff', 630, 1, 'Full-time'),
    ('staff', 'Philip Osei Bonsu', '', 'Security Guard', 'Security Services', 'Junior Staff', 'junior_staff', 640, 1, 'Full-time'),
    ('staff', 'Prosper Abebu', '', 'Security Guard', 'Security Services', 'Junior Staff', 'junior_staff', 650, 1, 'Full-time'),
    ('staff', 'Prosper E. K. Amu', '', 'Security Guard II', 'Security Services', 'Junior Staff', 'junior_staff', 660, 1, 'Full-time'),
    ('staff', 'Prosper Galli', '', 'Security Guard II', 'Security Services', 'Junior Staff', 'junior_staff', 670, 1, 'Full-time'),
    ('staff', 'Rabiatu Akabasib', '', 'Cleaner', 'Sanitation & Custodial', 'Junior Staff', 'junior_staff', 680, 1, 'Full-time'),
    ('staff', 'Reggie Seyram Sunu', '', 'Security', 'Security Services', 'Junior Staff', 'junior_staff', 690, 1, 'Full-time'),
    ('staff', 'Richard Chartey', '', 'Electrician', 'Works & Maintenance', 'Junior Staff', 'junior_staff', 700, 1, 'Full-time'),
    ('staff', 'Rudolf Kwaku Adu', '', 'Security Guard II', 'Security Services', 'Junior Staff', 'junior_staff', 710, 1, 'Full-time'),
    ('staff', 'Saviour Fiati', '', 'Cleaner', 'Sanitation & Custodial', 'Junior Staff', 'junior_staff', 720, 1, 'Full-time'),
    ('staff', 'Simon Kwaku Afum', '', 'Security Guard II', 'Security Services', 'Junior Staff', 'junior_staff', 730, 1, 'Full-time'),
    ('staff', 'Stephen Amevor', '', 'Security Guard', 'Security Services', 'Junior Staff', 'junior_staff', 740, 1, 'Full-time'),
    ('staff', 'Stephen Boakye Bediako', '', 'Security Guard II', 'Security Services', 'Junior Staff', 'junior_staff', 750, 1, 'Full-time'),
    ('staff', 'Stephen Gyan', '', 'Security Guard', 'Security Services', 'Junior Staff', 'junior_staff', 760, 1, 'Full-time'),
    ('staff', 'Stephen Owusu', '', 'Carpenter', 'Works & Maintenance', 'Junior Staff', 'junior_staff', 770, 1, 'Full-time'),
    ('staff', 'Susana Nsoah', '', 'Cleaner', 'Sanitation & Custodial', 'Junior Staff', 'junior_staff', 780, 1, 'Full-time'),
    ('staff', 'Timothy Arhinful', '', 'Farm Hand', 'University Farm', 'Junior Staff', 'junior_staff', 790, 1, 'Full-time'),
    ('staff', 'Tsikata Yao Julius', '', 'Groundsman', 'Grounds & Landscaping', 'Junior Staff', 'junior_staff', 800, 1, 'Full-time'),
    ('staff', 'William Abetiah', '', 'Principal Driver', 'Transport', 'Junior Staff', 'junior_staff', 810, 1, 'Full-time'),
    ('staff', 'William Sefah', '', 'Security Guard II', 'Security Services', 'Junior Staff', 'junior_staff', 820, 1, 'Full-time'),
    ('staff', 'Yaa Midza', '', 'Cleaner', 'Sanitation & Custodial', 'Junior Staff', 'junior_staff', 830, 1, 'Full-time')
ON DUPLICATE KEY UPDATE
    title = VALUES(title), job_title = VALUES(job_title), department = VALUES(department),
    faculty_group = VALUES(faculty_group), staff_category = VALUES(staff_category),
    sort_order = VALUES(sort_order), is_active = 1;

-- ---------------------------------------------------------------------
-- 4. Encyclopedia page copy (hero + CTA) — editable in the admin panel.
-- ---------------------------------------------------------------------
INSERT INTO encyclopedia_content (page_key, hero_title, hero_subtitle, cta_title, cta_subtitle)
VALUES ('faculty', 'Faculty Encyclopedia',
        'Discover our distinguished team of academic professionals shaping the future.',
        'Join Our Academic Community',
        'Are you passionate about education and research? Explore careers at Valley View University.')
ON DUPLICATE KEY UPDATE page_key = page_key;

INSERT INTO encyclopedia_content (page_key, hero_title, hero_subtitle, cta_title, cta_subtitle)
VALUES ('staff', 'Staff Encyclopedia',
        'Meet the dedicated administrative professionals who keep our university running smoothly.',
        'Join Our Administrative Team',
        'Help us build a better university environment for our students and staff.')
ON DUPLICATE KEY UPDATE page_key = page_key;
