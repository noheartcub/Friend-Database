<?php
// Start the session and include necessary files
session_start();
include_once 'includes/config.php';
include_once 'includes/functions.php';

// Check if the user is logged in and has admin role
requireAdmin();

// Check if the ID parameter is provided
if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $userId = intval($_GET['id']); // Validate the input

    // Fetch user profile data to get the profile image path
    $stmt = $pdo->prepare("SELECT profile_image FROM people WHERE id = :id");
    $stmt->execute(['id' => $userId]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user) {
        // Delete the user from the database
        $deleteStmt = $pdo->prepare("DELETE FROM people WHERE id = :id");
        $deleteStmt->execute(['id' => $userId]);

        // Check if the image exists and is not the placeholder image
        if ($user['profile_image'] && file_exists($user['profile_image']) && basename($user['profile_image']) !== 'placeholder.png') {
            unlink($user['profile_image']); // Delete the image file
        }

        // Redirect to the list of profiles after deletion
        header("Location: list_profile.php?message=Profile deleted successfully.");
        exit();
    } else {
        header("Location: list_profile.php?error=Profile not found.");
        exit();
    }
} else {
    header("Location: list_profile.php?error=Invalid ID.");
    exit();
}
?>
