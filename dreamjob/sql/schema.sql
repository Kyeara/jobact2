CREATE TABLE applicant( 
    applicantID INT AUTO_INCREMENT PRIMARY KEY, 
    first_name VARCHAR (32), 
    last_name VARCHAR (32), 
    age INT, 
    gender VARCHAR (32), 
    email VARCHAR (64), 
    contact_info VARCHAR (64), 
    date_added DATETIME DEFAULT CURRENT_TIMESTAMP 
);  

CREATE TABLE users (
    userID INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(32) UNIQUE NOT NULL,
    email VARCHAR(64) NOT NULL,
    password VARCHAR(255) NOT NULL, 
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE deleted_applicants (
    deletedID INT AUTO_INCREMENT PRIMARY KEY,
    applicantID INT NOT NULL,
    first_name VARCHAR(100),
    last_name VARCHAR(100),
    age INT,
    gender VARCHAR(20),
    email VARCHAR(150),
    contact_info VARCHAR(50),
    deleted_by VARCHAR(255),  
    deleted_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
