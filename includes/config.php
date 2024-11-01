<?php
// includes/config.php

// Database configuration constants
define('DB_HOST', 'localhost');         // Database host (e.g., 'localhost' for local server)
define('DB_NAME', ''); // Name of your database
define('DB_USER', '');      // Database username
define('DB_PASS', '');      // Database password

// Initialize PDO database connection
try {
    $pdo = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME, DB_USER, DB_PASS);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);      // Enable exceptions for errors
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC); // Fetch results as associative arrays
} catch (PDOException $e) {
    // Handle connection error securely (log to a file or display a generic error)
    die("Database connection failed. Please try again later.");
}
