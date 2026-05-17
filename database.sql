CREATE DATABASE IF NOT EXISTS elsa_university;
USE elsa_university;

SET FOREIGN_KEY_CHECKS = 0;

/* =========================
   DROP TABLES (SAFE RESET)
========================= */
DROP TABLE IF EXISTS notifications;
DROP TABLE IF EXISTS submissions;
DROP TABLE IF EXISTS join_requests;
DROP TABLE IF EXISTS grades;
DROP TABLE IF EXISTS classworks;
DROP TABLE IF EXISTS enrollments;
DROP TABLE IF EXISTS messages;
DROP TABLE IF EXISTS classes;
DROP TABLE IF EXISTS users;

SET FOREIGN_KEY_CHECKS = 1;

/* =========================
   USERS
========================= */
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    fname VARCHAR(100),
    mname VARCHAR(100),
    lname VARCHAR(100),
    email VARCHAR(150),
    username VARCHAR(100),
    password VARCHAR(255),
    role VARCHAR(50),
    profile_pic VARCHAR(255),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

/* =========================
   CLASSES
========================= */
CREATE TABLE classes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    class_name VARCHAR(255),
    block VARCHAR(100),
    program VARCHAR(100),
    class_code VARCHAR(50) UNIQUE,
    professor_id INT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,

    FOREIGN KEY (professor_id) REFERENCES users(id)
    ON DELETE SET NULL
);

/* =========================
   ENROLLMENTS
========================= */
CREATE TABLE enrollments (
    id INT AUTO_INCREMENT PRIMARY KEY,
    class_id INT,
    student_id INT,
    status VARCHAR(50) DEFAULT 'active',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,

    FOREIGN KEY (class_id) REFERENCES classes(id)
    ON DELETE CASCADE,

    FOREIGN KEY (student_id) REFERENCES users(id)
    ON DELETE CASCADE
);

/* =========================
   JOIN REQUESTS
========================= */
CREATE TABLE join_requests (
    id INT AUTO_INCREMENT PRIMARY KEY,
    class_id INT,
    student_id INT,
    status ENUM('pending','accepted','rejected','left') DEFAULT 'pending',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,

    FOREIGN KEY (class_id) REFERENCES classes(id)
    ON DELETE CASCADE,

    FOREIGN KEY (student_id) REFERENCES users(id)
    ON DELETE CASCADE
);

/* =========================
   CLASSWORKS
========================= */
CREATE TABLE classworks (
    id INT AUTO_INCREMENT PRIMARY KEY,
    class_id INT,
    title VARCHAR(255),
    type ENUM('lesson','lab','quiz'),
    description TEXT,
    max_points INT DEFAULT 100,
    file_path VARCHAR(255),
    due_date DATETIME,
    allow_submission TINYINT DEFAULT 1,
    attachment_required TINYINT DEFAULT 0,
    allow_late TINYINT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,

    FOREIGN KEY (class_id) REFERENCES classes(id)
    ON DELETE CASCADE
);

/* =========================
   SUBMISSIONS (IMPORTANT FIX)
========================= */
CREATE TABLE submissions (
    id INT AUTO_INCREMENT PRIMARY KEY,
    classwork_id INT,
    class_id INT,
    student_id INT,
    file_path VARCHAR(255),
    answer_text TEXT,
    status VARCHAR(50) DEFAULT 'submitted',
    submitted_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,

    FOREIGN KEY (classwork_id) REFERENCES classworks(id)
    ON DELETE CASCADE,

    FOREIGN KEY (class_id) REFERENCES classes(id)
    ON DELETE CASCADE,

    FOREIGN KEY (student_id) REFERENCES users(id)
    ON DELETE CASCADE
);

/* =========================
   GRADES (FIXED RELATIONS)
========================= */
CREATE TABLE grades (
    id INT AUTO_INCREMENT PRIMARY KEY,
    classwork_id INT,
    class_id INT,
    student_id INT,
    grade DECIMAL(5,2),
    remarks VARCHAR(255),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,

    FOREIGN KEY (classwork_id) REFERENCES classworks(id)
    ON DELETE CASCADE,

    FOREIGN KEY (class_id) REFERENCES classes(id)
    ON DELETE CASCADE,

    FOREIGN KEY (student_id) REFERENCES users(id)
    ON DELETE CASCADE
);

/* =========================
   MESSAGES
========================= */
CREATE TABLE messages (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT,
    message TEXT,
    code VARCHAR(50),
    is_read TINYINT DEFAULT 0,
    user_type VARCHAR(50),
    receiver_role VARCHAR(50),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,

    FOREIGN KEY (user_id) REFERENCES users(id)
    ON DELETE CASCADE
);

/* =========================
   NOTIFICATIONS
========================= */
CREATE TABLE notifications (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT,
    message TEXT,
    type VARCHAR(50),
    is_read TINYINT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,

    FOREIGN KEY (user_id) REFERENCES users(id)
    ON DELETE CASCADE
);

ALTER TABLE classworks
ADD FOREIGN KEY (class_id) REFERENCES classes(id) ON DELETE CASCADE;

ALTER TABLE grades
ADD FOREIGN KEY (classwork_id) REFERENCES classworks(id) ON DELETE CASCADE;

ALTER TABLE grades
ADD FOREIGN KEY (student_id) REFERENCES users(id) ON DELETE CASCADE;
