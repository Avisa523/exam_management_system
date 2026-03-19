CREATE DATABASE exam_mgmt_db;
USE exam_mgmt_db;


CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    role ENUM('admin','teacher','student') NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);


CREATE TABLE students (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT,
    roll_no VARCHAR(50) NOT NULL,
    full_name VARCHAR(100) NOT NULL,
    course VARCHAR(100),
    semester VARCHAR(50),
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);


CREATE TABLE teachers (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT,
    full_name VARCHAR(100) NOT NULL,
    subject VARCHAR(100),
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);


CREATE TABLE subjects (
    id INT AUTO_INCREMENT PRIMARY KEY,
    subject_name VARCHAR(100) NOT NULL,
    subject_code VARCHAR(50) NOT NULL
);


CREATE TABLE question_papers (
    id INT AUTO_INCREMENT PRIMARY KEY,
    subject_id INT,
    teacher_id INT,
    question TEXT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (subject_id) REFERENCES subjects(id) ON DELETE CASCADE,
    FOREIGN KEY (teacher_id) REFERENCES teachers(id) ON DELETE CASCADE
);


CREATE TABLE exam_results (
    id INT AUTO_INCREMENT PRIMARY KEY,
    student_id INT,
    subject_id INT,
    marks INT NOT NULL,
    total_marks INT DEFAULT 100,
    grade VARCHAR(5),
    result_date DATE,
    FOREIGN KEY (student_id) REFERENCES students(id) ON DELETE CASCADE,
    FOREIGN KEY (subject_id) REFERENCES subjects(id) ON DELETE CASCADE
);

CREATE TABLE noticeboard (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(200) NOT NULL,
    message TEXT NOT NULL,
    posted_by INT,
    posted_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (posted_by) REFERENCES users(id) ON DELETE SET NULL
);


INSERT INTO users (username, email, password, role) 
VALUES ('admin', 'admin@gmail.com', 'admin123', 'admin');



ALTER TABLE students 
ADD contact_no VARCHAR(20),
ADD gender VARCHAR(10),
ADD address VARCHAR(255),
ADD status VARCHAR(20) DEFAULT 'pending';


ALTER TABLE teachers
ADD contact_no VARCHAR(20),
ADD subject_id INT,
ADD semester VARCHAR(50);



CREATE TABLE exam_schedule (
    id INT AUTO_INCREMENT PRIMARY KEY,
    exam_name VARCHAR(100),
    subject_id INT,
    semester VARCHAR(50),
    room VARCHAR(50),
    exam_date DATE,
    exam_time TIME,
    FOREIGN KEY (subject_id) REFERENCES subjects(id) ON DELETE CASCADE
);


ALTER TABLE question_papers
ADD title VARCHAR(200),
ADD description TEXT,
ADD approved TINYINT(1) DEFAULT 0;

CREATE TABLE student_profiles (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    full_name VARCHAR(255) NOT NULL,
    dob DATE,
    gender ENUM('Male','Female','Other'),
    father_name VARCHAR(255),
    mother_name VARCHAR(255),
    permanent_address VARCHAR(255),
    current_address VARCHAR(255),
    mobile_no VARCHAR(20),
    course VARCHAR(100),
    semester VARCHAR(50),
    section VARCHAR(50),
    subjects VARCHAR(255),
    profile_photo VARCHAR(255),
    id_proof VARCHAR(255),
    marksheets VARCHAR(255),
    signature VARCHAR(255),
    completed_registration TINYINT(1) DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id)
);