<?php
// Start the session and include necessary files
session_start();
include_once 'includes/config.php';
include_once 'includes/functions.php';

// Check if the user is logged in and has admin role
requireAdmin();

// Fetch user ID from the URL
if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $userId = intval($_GET['id']); // Validate the input

    // Remove the warning message by updating it to NULL
    $stmt = $pdo->prepare("UPDATE people SET warning_message = NULL, warning_level = NULL WHERE id = :id");
    $stmt->execute(['id' => $userId]);

    // Redirect back to the profile page
    header("Location: profile.php?id=" . $userId);
    exit();
} else {
    header("Location: 404.php"); // Redirect to a 404 error page for invalid ID
    exit();
}
