<?php
// includes/functions.php

// Start the session if it hasn't been started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Include the database configuration
include_once __DIR__ . '/config.php';

// Function to check if a user is banned
function isUserBanned($username) {
    global $pdo;
    $stmt = $pdo->prepare("SELECT banned, ban_reason FROM users WHERE username = :username");
    $stmt->execute(['username' => $username]);
    $user = $stmt->fetch();

    if ($user && $user['banned']) {
        // Store the ban reason in the session
        $_SESSION['banned_message'] = $user['ban_reason'] ?? 'Your account has been disabled.';
        return true; // User is banned
    }
    return false; // User is not banned
}

// Function to log in a user
function loginUser($username, $password) {
    global $pdo;

    // Check if the user is banned before proceeding
    if (isUserBanned($username)) {
        return false; // User is banned, do not proceed
    }

    // Fetch user data including profile image
    $stmt = $pdo->prepare("SELECT * FROM users WHERE username = :username");
    $stmt->execute(['username' => $username]);
    $user = $stmt->fetch();

    if ($user) {
        // Check password and proceed if correct
        if (password_verify($password, $user['password'])) {
            // Include the profile_image in the session
            createSession($user['id'], $user['username'], $user['role'], $user['first_name'], $user['last_name'], $user['profile_image']);
            
            // Update the last_login timestamp
            $stmt = $pdo->prepare("UPDATE users SET last_login = NOW() WHERE id = :id");
            $stmt->execute(['id' => $user['id']]);

            // Redirect to index.php after successful login
            header("Location: index.php");
            exit();
        }
    }
    
    // Return false if login fails
    return false;
}

// Function to create a user session
function createSession($userId, $username, $role, $firstName, $lastName, $profileImage) {
    $_SESSION['user_id'] = $userId;
    $_SESSION['username'] = $username; // Optional, if you still want the username
    $_SESSION['role'] = $role;
    $_SESSION['first_name'] = $firstName; // Store first name
    $_SESSION['last_name'] = $lastName;   // Store last name
    $_SESSION['profile_image'] = $profileImage; // Store profile image
    $_SESSION['logged_in'] = true;
}

// Function to check if the user is logged in
function isLoggedIn() {
    return isset($_SESSION['logged_in']) && $_SESSION['logged_in'] === true;
}

// Function to check if the user has a specific role
function hasRole($requiredRole) {
    return isset($_SESSION['role']) && $_SESSION['role'] === $requiredRole;
}

// Function to require login and specific role, with customizable redirect for unauthorized access
function requireRole($requiredRole, $redirectPage = 'unauthorized.php') {
    if (!isLoggedIn() || !hasRole($requiredRole)) {
        header("Location: $redirectPage");
        exit();
    }
}

function requireAdmin() {
    if (!isLoggedIn() || !hasRole('admin')) {
        header("Location: unauthorized.php"); // Redirect to an unauthorized page
        exit();
    }
}


// Function to check if the user has a specific permission
function hasPermission($permissionName) {
    global $pdo;
    $userId = $_SESSION['user_id'];
    
    // Query to check if the user's role has the specified permission
    $stmt = $pdo->prepare("
        SELECT COUNT(*) FROM role_permissions
        JOIN permissions ON role_permissions.permission_id = permissions.id
        JOIN roles ON role_permissions.role_id = roles.id
        JOIN users ON users.role = roles.role_name
        WHERE users.id = :user_id AND permissions.permission_name = :permission_name
    ");
    $stmt->execute(['user_id' => $userId, 'permission_name' => $permissionName]);
    
    return $stmt->fetchColumn() > 0;
}

// Function to require a specific permission, with a redirect for unauthorized access
function requirePermission($permissionName, $redirectPage = 'unauthorized.php') {
    if (!isLoggedIn() || !hasPermission($permissionName)) {
        header("Location: $redirectPage");
        exit();
    }
}

// Enhanced requireRole function to work with permissions
function requireRoleOrPermission($role, $permission, $redirectPage = 'unauthorized.php') {
    if (!isLoggedIn() || (!hasRole($role) && !hasPermission($permission))) {
        header("Location: $redirectPage");
        exit();
    }
}

// Function to log out the user
function logoutUser() {
    $_SESSION = [];
    session_destroy();
}

// Function to upload an image
function uploadImage($file, $targetDir = 'uploads/', $maxSize = 2 * 1024 * 1024) {
    // Check if the file was uploaded without errors
    if ($file['error'] !== UPLOAD_ERR_OK) {
        return ['error' => 'File upload error.'];
    }

    // Validate file size
    if ($file['size'] > $maxSize) {
        return ['error' => 'File is too large. Max size is 2MB.'];
    }

    // Validate file type
    $allowedTypes = ['image/jpeg', 'image/png', 'image/gif'];
    if (!in_array($file['type'], $allowedTypes)) {
        return ['error' => 'Invalid file type. Only JPEG, PNG, and GIF are allowed.'];
    }

    // Generate a unique file name to prevent overwriting
    $fileName = uniqid('img_', true) . '.' . pathinfo($file['name'], PATHINFO_EXTENSION);
    $targetFile = $targetDir . $fileName;

    // Move the file to the target directory
    if (move_uploaded_file($file['tmp_name'], $targetFile)) {
        return ['success' => true, 'path' => $targetFile];
    } else {
        return ['error' => 'Failed to move uploaded file.'];
    }
}

// Function to get a single setting from the database
function getSetting($key) {
    global $pdo;
    $stmt = $pdo->prepare("SELECT setting_value FROM settings WHERE setting_key = :key");
    $stmt->execute(['key' => $key]);
    $result = $stmt->fetch();
    return $result ? $result['setting_value'] : null;
}

// Function to retrieve core site settings
function getSiteSettings() {
    return [
        'site_url' => getSetting('site_url'),
        'site_title' => getSetting('site_title'),
        'site_description' => getSetting('site_description')
    ];
}

// Function to log activity (optional)
function logActivity($action, $details = null) {
    global $pdo;
    $userId = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : null;

    $stmt = $pdo->prepare("INSERT INTO activity_logs (user_id, action, details) VALUES (:user_id, :action, :details)");
    $stmt->execute([
        'user_id' => $userId,
        'action' => $action,
        'details' => $details
    ]);
}
