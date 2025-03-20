CREATE DATABASE IF NOT EXISTS student_management;

USE student_management;

CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    full_name VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    phonenumber VARCHAR(20) DEFAULT NULL,
    profile_pic VARCHAR(255) DEFAULT NULL,
    role ENUM('admin', 'teacher', 'student') NOT NULL DEFAULT 'student',
);
