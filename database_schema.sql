-- Valley View University Database Schema

-- Create database
CREATE DATABASE IF NOT EXISTS valley_view_uni;
USE valley_view_uni;

-- Create admin users table
CREATE TABLE IF NOT EXISTS admin_users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(100) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    email VARCHAR(100) NOT NULL,
    full_name VARCHAR(100),
    failed_attempts INT NOT NULL DEFAULT 0,
    locked_until DATETIME NULL DEFAULT NULL,
    last_login_at DATETIME NULL DEFAULT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- NO DEFAULT ADMIN ACCOUNT IS SEEDED HERE, BY DESIGN.
--
-- This file previously inserted `admin` with the bcrypt hash of the string
-- "password" (mis-documented in a comment as "admin123"). Because the repo was
-- public, that was a working login for anyone who read it — the CRITICAL
-- Finding 1 of the August 2026 penetration test.
--
-- Provision the first administrator out-of-band instead, from the server shell:
--
--     php tools/set_admin_password.php admin admin@vvu.edu.gh
--
-- The tool prompts for a password, enforces a minimum length, hashes it with
-- password_hash() and never writes the plaintext to disk or shell history.

-- Table for contact form messages
CREATE TABLE IF NOT EXISTS contact_messages (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL,
    inquiry_type VARCHAR(50),
    message TEXT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Table for news and events
CREATE TABLE IF NOT EXISTS news_events (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    content TEXT,
    category VARCHAR(50),
    image_url VARCHAR(500),
    publish_date DATE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Table for faculty members
CREATE TABLE IF NOT EXISTS faculty (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    title VARCHAR(100),
    department VARCHAR(100),
    email VARCHAR(100),
    phone VARCHAR(20),
    bio TEXT,
    image_url VARCHAR(500),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Table for academic programs
CREATE TABLE IF NOT EXISTS programs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    faculty VARCHAR(100),
    degree_type VARCHAR(50),
    description TEXT,
    duration VARCHAR(50),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Table for student testimonials
CREATE TABLE IF NOT EXISTS testimonials (
    id INT AUTO_INCREMENT PRIMARY KEY,
    student_name VARCHAR(100) NOT NULL,
    program VARCHAR(100),
    graduation_year YEAR,
    testimonial TEXT,
    image_url VARCHAR(500),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Table for campus facilities
CREATE TABLE IF NOT EXISTS facilities (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    category VARCHAR(100),
    description TEXT,
    image_url VARCHAR(500),
    location VARCHAR(255),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Table for alumni
CREATE TABLE IF NOT EXISTS alumni (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    graduation_year YEAR,
    program VARCHAR(100),
    current_position VARCHAR(255),
    company VARCHAR(255),
    bio TEXT,
    image_url VARCHAR(500),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Insert sample data for contact_messages
INSERT INTO contact_messages (name, email, inquiry_type, message) VALUES
('John Doe', 'john.doe@example.com', 'Admissions Question', 'I would like to know more about the application process.'),
('Jane Smith', 'jane.smith@example.com', 'General Inquiry', 'What are the campus visiting hours?');

-- Insert sample data for news_events
INSERT INTO news_events (title, content, category, image_url, publish_date) VALUES
('VVU Researchers Secure $5M Grant', 'VVU researchers have been awarded a $5 million grant for cancer research.', 'Research', 'https://example.com/research.jpg', '2024-10-15'),
('Innovation Fair Showcases Student Creativity', 'The annual innovation fair displayed groundbreaking projects from our students.', 'Campus Life', 'https://example.com/fair.jpg', '2024-11-01');

-- Insert sample data for faculty
INSERT INTO faculty (name, title, department, email, phone, bio) VALUES
('Dr. Alice Johnson', 'Professor of Computer Science', 'Computer Science', 'a.johnson@vvu.edu', '(123) 456-7891', 'Dr. Johnson specializes in artificial intelligence and machine learning.'),
('Dr. Robert Williams', 'Associate Professor of Biology', 'Biology', 'r.williams@vvu.edu', '(123) 456-7892', 'Dr. Williams research focuses on molecular biology and genetics.');

-- Insert sample data for programs
INSERT INTO programs (name, faculty, degree_type, description, duration) VALUES
('Computer Science', 'Faculty of Science & Technology', 'BSc', 'A comprehensive program covering software development, AI, and data structures.', '4 years'),
('Business Administration', 'Faculty of Business', 'BBA', 'Lead and innovate in the world of commerce with specializations in marketing, finance, and management.', '4 years');

-- Insert sample data for testimonials
INSERT INTO testimonials (student_name, program, graduation_year, testimonial, image_url) VALUES
('Alex Martinez', 'Computer Science', 2025, 'Joining the robotics club was the best decision I''ve made. I found my community and discovered a passion I never knew I had.', 'https://example.com/alex.jpg'),
('Maria Garcia', 'Business Administration', 2024, 'Valley View''s vibrant campus life made it feel like home from day one. I''ve made lifelong friends and connections here.', 'https://example.com/maria.jpg');

-- Insert sample data for facilities
INSERT INTO facilities (name, category, description, image_url, location) VALUES
('Science Laboratory', 'Academic', 'State-of-the-art laboratory for chemistry and physics experiments.', 'https://example.com/lab.jpg', 'Building A'),
('Student Union', 'Student Life', 'Central hub for student activities, dining, and socializing.', 'https://example.com/union.jpg', 'Building B');

-- Insert sample data for alumni
INSERT INTO alumni (name, graduation_year, program, current_position, company, bio, image_url) VALUES
('Michael Brown', 2010, 'Computer Science', 'Senior Software Engineer', 'TechCorp Inc.', 'Michael leads a team of developers working on cutting-edge web applications.', 'https://example.com/michael.jpg'),
('Sarah Davis', 2012, 'Business Administration', 'Marketing Director', 'Global Enterprises', 'Sarah manages marketing campaigns for international brands.', 'https://example.com/sarah.jpg');