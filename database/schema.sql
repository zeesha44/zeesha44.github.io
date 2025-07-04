-- Create the database
CREATE DATABASE IF NOT EXISTS amis_db;
USE amis_db;

-- Create states table
CREATE TABLE IF NOT EXISTS states (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL UNIQUE
);

-- Insert all Nigerian states
INSERT INTO states (name) VALUES 
('Abia'), ('Adamawa'), ('Akwa Ibom'), ('Anambra'), ('Bauchi'),
('Bayelsa'), ('Benue'), ('Borno'), ('Cross River'), ('Delta'),
('Ebonyi'), ('Edo'), ('Ekiti'), ('Enugu'), ('Federal Capital Territory'),
('Gombe'), ('Imo'), ('Jigawa'), ('Kaduna'), ('Kano'),
('Katsina'), ('Kebbi'), ('Kogi'), ('Kwara'), ('Lagos'),
('Nasarawa'), ('Niger'), ('Ogun'), ('Ondo'), ('Osun'),
('Oyo'), ('Plateau'), ('Rivers'), ('Sokoto'), ('Taraba'),
('Yobe'), ('Zamfara');

-- Create schools table with foreign key to states
CREATE TABLE IF NOT EXISTS schools (
    id INT AUTO_INCREMENT PRIMARY KEY,
    state_id INT NOT NULL,
    listing_type ENUM('free', 'premium') NOT NULL,
    school_name VARCHAR(255) NOT NULL,
    address TEXT NOT NULL,
    phone VARCHAR(20) NOT NULL,
    email VARCHAR(255) NOT NULL,
    username VARCHAR(100) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    logo_path VARCHAR(255),
    status ENUM('pending', 'approved', 'rejected') DEFAULT 'pending',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (state_id) REFERENCES states(id)
);

-- Create school_images table for multiple images
CREATE TABLE IF NOT EXISTS school_images (
    id INT AUTO_INCREMENT PRIMARY KEY,
    school_id INT NOT NULL,
    image_path VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (school_id) REFERENCES schools(id) ON DELETE CASCADE
);

-- Create directory for uploads if it doesn't exist
-- Note: This needs to be executed manually:
-- mkdir -p unitis/uploads
-- chmod 777 unitis/uploads
