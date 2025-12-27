-- Xlerion Database Schema
-- MariaDB / MySQL

-- Create database
CREATE DATABASE IF NOT EXISTS xlerion_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE xlerion_db;

-- Contacts table for contact form submissions
CREATE TABLE IF NOT EXISTS contacts (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    email VARCHAR(255) NOT NULL,
    message TEXT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_created_at (created_at),
    INDEX idx_email (email)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Content sections table (optional - for dynamic content)
CREATE TABLE IF NOT EXISTS content_sections (
    id INT AUTO_INCREMENT PRIMARY KEY,
    section_name VARCHAR(100) NOT NULL UNIQUE,
    title VARCHAR(255),
    content TEXT,
    is_active BOOLEAN DEFAULT TRUE,
    display_order INT DEFAULT 0,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_section_name (section_name),
    INDEX idx_display_order (display_order)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Insert default content sections
INSERT INTO content_sections (section_name, title, content, display_order) VALUES
('hero', 'Welcome to Xlerion', 'Your gateway to innovative solutions', 1),
('about', 'About Us', 'We are dedicated to providing excellent services', 2),
('services', 'Our Services', 'Discover what we can do for you', 3),
('contact', 'Contact Us', 'Get in touch with our team', 4);
