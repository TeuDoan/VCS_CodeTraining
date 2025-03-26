CREATE DATABASE IF NOT EXISTS student_management;

USE student_management;

CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(100) NOT NULL UNIQUE,
    full_name VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    phonenumber VARCHAR(20) DEFAULT NULL,
    avatar_url VARCHAR(255) DEFAULT NULL,
    is_teacher TINYINT(1) NOT NULL DEFAULT 0;
    uuid char(36) NOT NULL DEFAULT (UUID()) UNIQUE;
);


CREATE TABLE if not EXISTS messages (
    id INT AUTO_INCREMENT PRIMARY KEY,  -- Primary key for performance
    sender_id CHAR(36) NOT NULL,        -- References users.uuid
    receiver_id CHAR(36) NOT NULL,      -- References users.uuid
    message TEXT NOT NULL,
    timestamp DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (sender_id) REFERENCES users(uuid) ON DELETE CASCADE,
    FOREIGN KEY (receiver_id) REFERENCES users(uuid) ON DELETE CASCADE
);

CREATE TABLE if not EXISTS homeworks (
    id INT AUTO_INCREMENT PRIMARY KEY,  -- Primary key for performance
    teacher_id CHAR(36) NOT NULL,       -- References users.uuid
    title VARCHAR(255) NOT NULL,
    description TEXT NOT NULL,
    homework_file VARCHAR(255) NOT NULL
)
