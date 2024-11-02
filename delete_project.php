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

// Get the project ID from the URL
$projectId = $_GET['id'];

// Fetch all tasks associated with this project to delete related images
$taskStmt = $pdo->prepare("SELECT image_path FROM tasks WHERE project_id = :project_id");
$taskStmt->bindParam(':project_id', $projectId);
$taskStmt->execute();
$tasks = $taskStmt->fetchAll(PDO::FETCH_ASSOC);

// Delete associated task images if they exist
foreach ($tasks as $task) {
    if ($task['image_path'] && file_exists($task['image_path'])) {
        unlink($task['image_path']);
    }
}

// Delete all tasks related to this project
$deleteTasksStmt = $pdo->prepare("DELETE FROM tasks WHERE project_id = :project_id");
$deleteTasksStmt->bindParam(':project_id', $projectId);
$deleteTasksStmt->execute();

// Delete the project itself
$deleteProjectStmt = $pdo->prepare("DELETE FROM task_projects WHERE id = :id");
$deleteProjectStmt->bindParam(':id', $projectId);
$deleteProjectStmt->execute();

// Redirect back to the projects page
header("Location: projects.php");
exit();
?>
