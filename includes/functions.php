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
        $_SESSION['banned_message'] = $user['ban_reason'] ?? 'Your account has been disabled.';
        return true;
    }
    return false;
}

// Function to log in a user
function loginUser($username, $password) {
    global $pdo;

    if (isUserBanned($username)) {
        return false;
    }

    $stmt = $pdo->prepare("SELECT * FROM users WHERE username = :username");
    $stmt->execute(['username' => $username]);
    $user = $stmt->fetch();

    if ($user && password_verify($password, $user['password'])) {
        createSession($user['id'], $user['username'], $user['role'], $user['first_name'], $user['last_name'], $user['profile_image']);
        $stmt = $pdo->prepare("UPDATE users SET last_login = NOW() WHERE id = :id");
        $stmt->execute(['id' => $user['id']]);
        header("Location: index.php");
        exit();
    }
    return false;
}

// Function to create a user session
function createSession($userId, $username, $role, $firstName, $lastName, $profileImage) {
    $_SESSION['user_id'] = $userId;
    $_SESSION['username'] = $username;
    $_SESSION['role'] = $role;
    $_SESSION['first_name'] = $firstName;
    $_SESSION['last_name'] = $lastName;
    $_SESSION['profile_image'] = $profileImage;
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

// Function to require login and specific role
function requireRole($requiredRole, $redirectPage = 'unauthorized.php') {
    if (!isLoggedIn() || !hasRole($requiredRole)) {
        header("Location: $redirectPage");
        exit();
    }
}

function requireAdmin() {
    requireRole('admin', 'unauthorized.php');
}

// Function to check if the user has a specific permission
function hasPermission($permissionName) {
    global $pdo;
    $userId = $_SESSION['user_id'];
    
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

// Function to require a specific permission
function requirePermission($permissionName, $redirectPage = 'unauthorized.php') {
    if (!isLoggedIn() || !hasPermission($permissionName)) {
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
    if ($file['error'] !== UPLOAD_ERR_OK) {
        return ['error' => 'File upload error.'];
    }

    if ($file['size'] > $maxSize) {
        return ['error' => 'File is too large. Max size is 2MB.'];
    }

    $allowedTypes = ['image/jpeg', 'image/png', 'image/gif'];
    if (!in_array($file['type'], $allowedTypes)) {
        return ['error' => 'Invalid file type. Only JPEG, PNG, and GIF are allowed.'];
    }

    $fileName = uniqid('img_', true) . '.' . pathinfo($file['name'], PATHINFO_EXTENSION);
    $targetFile = $targetDir . $fileName;

    // Ensure the target directory exists
    if (!file_exists($targetDir)) {
        mkdir($targetDir, 0777, true);
    }

    if (move_uploaded_file($file['tmp_name'], $targetFile)) {
        return ['success' => true, 'path' => $targetFile];
    }
    return ['error' => 'Failed to move uploaded file.'];
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
        'site_description' => getSetting('site_description'),
        'time_format' => getSetting('time_format') ?? '24-hour'
    ];
}

// Function to calculate age based on birthday
function calculateAge($birthday) {
    if (empty($birthday)) {
        return 'Unknown';
    }
    $birthDate = new DateTime($birthday);
    $today = new DateTime();
    $age = $today->diff($birthDate)->y;
    return $age;
}

// Function to log activity
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
function getUserTime($timezone, $timeFormat = '24-hour') {
    // Set default timezone if not provided
    $timezone = $timezone ?: 'UTC';
    date_default_timezone_set($timezone);
    
    // Define the time format based on the setting
    $format = $timeFormat === '12-hour' ? 'h:i A' : 'H:i';

    // Return the current time in the specified format
    return date($format);
}
