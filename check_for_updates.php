<?php
// Check if accessed from CLI; if not, restrict access
if (php_sapi_name() !== 'cli') {
    http_response_code(403);
    exit('Unauthorized access');
}

// Include necessary files for database connection
include_once 'includes/config.php';

// GitHub repository details
$repoOwner = 'noheartcub';
$repoName = 'Friend-Database';

// GitHub API URL for the latest release
$githubApiUrl = "https://api.github.com/repos/$repoOwner/$repoName/releases/latest";

// Make an API request to GitHub
$options = [
    "http" => [
        "header" => "User-Agent: PHP\r\n"
    ]
];
$context = stream_context_create($options);
$response = file_get_contents($githubApiUrl, false, $context);

if ($response === false) {
    die('Error fetching GitHub release information.');
}

// Decode the JSON response
$releaseData = json_decode($response, true);
$latestVersion = $releaseData['tag_name'] ?? null;

// If we didn't get a valid version, exit
if (!$latestVersion) {
    die('Could not fetch latest version information.');
}

// Fetch the current version from the database
$stmt = $pdo->prepare("SELECT setting_value FROM settings WHERE setting_key = 'current_version'");
$stmt->execute();
$currentVersion = $stmt->fetchColumn();

// Compare versions
if (version_compare($latestVersion, $currentVersion, '>')) {
    // New version available, insert notification
    $updateMessage = "A new update (Version $latestVersion) is available!";

    try {
        $stmt = $pdo->prepare("INSERT INTO notifications (message, type, is_read) VALUES (:message, 'update', 0)");
        $stmt->bindParam(':message', $updateMessage);
        $stmt->execute();

        // Update the current version in the database
        $updateStmt = $pdo->prepare("UPDATE settings SET setting_value = :latestVersion WHERE setting_key = 'current_version'");
        $updateStmt->bindParam(':latestVersion', $latestVersion);
        $updateStmt->execute();

        echo "Update notification added successfully!";
    } catch (PDOException $e) {
        error_log("Error adding update notification: " . $e->getMessage());
        die('Database error.');
    }
} else {
    echo "No new updates available.";
}
