<?php
require_once 'dblogin.php';

try {
    // Users table
    $pdo->exec("CREATE TABLE IF NOT EXISTS users (
        id       INT AUTO_INCREMENT PRIMARY KEY,
        username VARCHAR(50)  NOT NULL UNIQUE,
        email    VARCHAR(100) NOT NULL UNIQUE,
        password VARCHAR(255) NOT NULL
    )");

    // Students table
    $pdo->exec("CREATE TABLE IF NOT EXISTS students (
        id              INT AUTO_INCREMENT PRIMARY KEY,
        first_name      VARCHAR(50),
        last_name       VARCHAR(50),
        email           VARCHAR(100),
        major           VARCHAR(50),
        gpa             DECIMAL(3,2),
        enrollment_year INT
    )");

    // Initial data — INSERT IGNORE prevents duplicate on re-run
    $pdo->exec("INSERT IGNORE INTO students 
        (first_name, last_name, email, major, gpa, enrollment_year)
        VALUES
        ('Emory', 'Smith', 'emory@fsc.edu', 'CS', 3.50, 2023)");

    echo "Database setup complete!";

} catch (PDOException $e) {
    die("Setup failed: " . $e->getMessage());
}
?>