<?php
// Start the session and include necessary files
session_start();
include_once 'includes/config.php';
include_once 'includes/functions.php';

// Check if the user is logged in
if (!isLoggedIn()) {
    header("Location: login.php");
    exit();
}

// Get task ID from the URL
$taskId = $_GET['id'];

// Fetch task to get the project_id before deletion
$taskStmt = $pdo->prepare("SELECT project_id, image_path FROM tasks WHERE id = :id");
$taskStmt->bindParam(':id', $taskId);
$taskStmt->execute();
$task = $taskStmt->fetch(PDO::FETCH_ASSOC);

if ($task) {
    $projectId = $task['project_id'];

    // Delete the task image if it exists
    if ($task['image_path'] && file_exists($task['image_path'])) {
        unlink($task['image_path']);
    }

    // Delete the task from the database
    $deleteStmt = $pdo->prepare("DELETE FROM tasks WHERE id = :id");
    $deleteStmt->bindParam(':id', $taskId);
    $deleteStmt->execute();
}

// Redirect back to tasks.php with the project_id
header("Location: tasks.php?project_id=$projectId");
exit();
?>
