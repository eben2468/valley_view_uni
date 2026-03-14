-- Directory Table for Staff and Faculty
CREATE TABLE IF NOT EXISTS directory (
    id INT AUTO_INCREMENT PRIMARY KEY,
    type ENUM('staff', 'faculty') NOT NULL,
    name VARCHAR(255) NOT NULL,
    title VARCHAR(50), -- Academic title (e.g., Dr., Prof.)
    job_title VARCHAR(255), -- Position (e.g., Lecturer, Registrar)
    department VARCHAR(255),
    faculty_group VARCHAR(255), -- Which faculty they belong to
    employment_status VARCHAR(100), -- Full-time, etc.
    email VARCHAR(255),
    phone VARCHAR(100),
    office_location VARCHAR(255),
    image_url VARCHAR(500),
    bio TEXT,
    education TEXT,
    research_interests TEXT,
    publications TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Insert some dummy data for Faculty
INSERT IGNORE INTO directory (type, name, title, job_title, department, faculty_group, employment_status, email, image_url, bio) VALUES
('faculty', 'Dr. Benette Yaw Osei', 'Dr.', 'Lecturer', 'Computer Science', 'Science & Technology', 'Full-time', 'b.osei@vvu.edu', 'https://ui-avatars.com/api/?name=Benette+Yaw+Osei&background=800000&color=fff&size=512', 'Expert in Computer Science...'),
('faculty', 'Donald Yeboah', '', 'Snr. IT Asst.', 'Information Technology', 'Science & Technology', 'Full-time', 'd.yeboah@vvu.edu', 'https://ui-avatars.com/api/?name=Donald+Yeboah&background=002147&color=fff&size=512', 'Technology specialist...'),
('faculty', 'Dr. Adasa Nkrumah Kofi Frimpong', 'Dr.', 'Ag. Head, Academic and Administrative Computing', 'Computing Sciences', 'Science & Technology', 'Full-time', 'a.frimpong@vvu.edu', 'https://ui-avatars.com/api/?name=Adasa+Frimpong&background=006400&color=fff&size=512', 'Leadership in academic computing...'),
('staff', 'Ms. Beatrice Acheampong', 'Ms.', 'Assistant Registrar', 'Administration', 'Registry', 'Full-time', 'b.acheampong@vvu.edu', 'https://ui-avatars.com/api/?name=Beatrice+Acheampong&background=800080&color=fff&size=512', 'Administrative support...'),
('staff', 'Mrs. Gertrude Effeh Brew', 'Mrs.', 'University Counsellor', 'Student Affairs', 'Administration', 'Full-time', 'g.brew@vvu.edu', 'https://ui-avatars.com/api/?name=Gertrude+Brew&background=000000&color=fff&size=512', 'Supportive counselling...');
