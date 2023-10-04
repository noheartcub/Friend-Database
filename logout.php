<?php
session_start();

// Function to log out the user
function logout() {
    // Unset all session variables
    $_SESSION = array();

    // Destroy the session
    session_destroy();

    // Redirect the user to the login page or any other desired page
    header('Location: login.php'); // Replace "login.php" with the appropriate URL
    exit;
}

// Call the logout function
logout();
?>