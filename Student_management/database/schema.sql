CREATE TABLE `users` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `username` VARCHAR(50) NOT NULL UNIQUE,
  `email` VARCHAR(100) NOT NULL UNIQUE,
  `password` VARCHAR(255) NOT NULL,
  `fullname` VARCHAR(100) DEFAULT NULL,
  `phonenumber` VARCHAR(20) DEFAULT NULL,
  `avatar_url` VARCHAR(255) DEFAULT NULL,
  `is_teacher` TINYINT(1) NOT NULL DEFAULT 0,
  `uuid` CHAR(36) NOT NULL UNIQUE DEFAULT (UUID()),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE `homeworks` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `teacher_uuid` CHAR(36) DEFAULT NULL,
  `title` VARCHAR(255) NOT NULL,
  `description` TEXT NOT NULL,
  `homework_file` VARCHAR(255) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_teacher_uuid` (`teacher_uuid`),
  CONSTRAINT `fk_teacher_uuid` FOREIGN KEY (`teacher_uuid`) REFERENCES `users` (`uuid`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE `messages` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `sender_uuid` CHAR(36) NOT NULL,
  `receiver_uuid` CHAR(36) NOT NULL,
  `message` TEXT NOT NULL,
  `timestamp` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `messages_ibfk_1` (`sender_uuid`),
  KEY `messages_ibfk_2` (`receiver_uuid`),
  CONSTRAINT `messages_ibfk_1` FOREIGN KEY (`sender_uuid`) REFERENCES `users` (`uuid`) ON DELETE CASCADE,
  CONSTRAINT `messages_ibfk_2` FOREIGN KEY (`receiver_uuid`) REFERENCES `users` (`uuid`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE `submissions` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `homework_id` INT(11) NOT NULL,
  `student_uuid` CHAR(36) NOT NULL,
  `file_path` VARCHAR(255) NOT NULL,
  `submission_time` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `submissions_ibfk_1` (`homework_id`),
  KEY `submissions_ibfk_2` (`student_uuid`),
  CONSTRAINT `submissions_ibfk_1` FOREIGN KEY (`homework_id`) REFERENCES `homeworks` (`id`) ON DELETE CASCADE,
  CONSTRAINT `submissions_ibfk_2` FOREIGN KEY (`student_uuid`) REFERENCES `users` (`uuid`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO users (username, fullname, email, phonenumber, is_teacher, password) VALUES
('teacher1', 'Alice Johnson', 'alice.johnson@example.com', '1234567890', 1, '$2a$10$02Xj.yUOED4.ZCHHrg8pr.ARijODXlJP/9.5CVSPEMBD3tyKa6HCC'),
('teacher2', 'Bob Smith', 'bob.smith@example.com', '1234567891', 1, '$2a$10$02Xj.yUOED4.ZCHHrg8pr.ARijODXlJP/9.5CVSPEMBD3tyKa6HCC'),
('student1', 'Ethan Clark', 'ethan.clark@example.com', '1234567894', 0, '$2a$10$02Xj.yUOED4.ZCHHrg8pr.ARijODXlJP/9.5CVSPEMBD3tyKa6HCC'),
('student2', 'Fiona Lewis', 'fiona.lewis@example.com', '1234567895', 0, '$2a$10$02Xj.yUOED4.ZCHHrg8pr.ARijODXlJP/9.5CVSPEMBD3tyKa6HCC')
COMMIT;
