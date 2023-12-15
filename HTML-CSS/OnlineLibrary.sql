-- This SQL script will create a database and tables needed for the Online Library

-- Create database if it doesn't exist
CREATE DATABASE IF NOT EXISTS OnlineLibrary;

-- Switch to the newly created database
USE OnlineLibrary;

-- Create a table for the users
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    cpr VARCHAR(9) NOT NULL UNIQUE,
    email VARCHAR(255) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    is_admin BOOLEAN NOT NULL DEFAULT 0,
    register_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Create a table for the books
CREATE TABLE books (
    isbn VARCHAR(13) PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    author VARCHAR(255),
    publish_year INT,
    available BOOLEAN NOT NULL DEFAULT 1
);

-- Create a table for borrowing records
CREATE TABLE borrow_records (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    isbn VARCHAR(13) NOT NULL,
    borrow_date DATE NOT NULL,
    return_date DATE,
    period ENUM('6 months', '1 month', '1 week') NOT NULL,
    is_returned BOOLEAN NOT NULL DEFAULT 0,
    fine DECIMAL(5, 2) DEFAULT 0.00,
    FOREIGN KEY (user_id) REFERENCES users(id),
    FOREIGN KEY (isbn) REFERENCES books(isbn)
);

-- You can insert an admin user manually
INSERT INTO users (cpr, email, password, is_admin) 
VALUES ('123456789', 'admin@library.com', MD5('adminpassword'), 1);
-- Insert a book into the books table
INSERT INTO books (isbn, title, author, publish_year, available) 
VALUES ('978-3-16-148410-0', 'Example Book Title', 'Author Name', 2021, 1);

-- Assume that the user with ID 1 is borrowing the book we just inserted.
-- The following is an example of inserting a borrow record for that book.
-- Note: Ensure that the user with ID 1 exists in your users table before running this.
INSERT INTO borrow_records (user_id, isbn, borrow_date, period) 
VALUES (1, '978-3-16-148410-0', CURDATE(), '1 month');

