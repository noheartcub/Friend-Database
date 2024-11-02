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

// Get the task ID from the URL
$taskId = $_GET['id'];

// Fetch the task details from the database
$stmt = $pdo->prepare("SELECT * FROM tasks WHERE id = :id");
$stmt->bindParam(':id', $taskId);
$stmt->execute();
$task = $stmt->fetch(PDO::FETCH_ASSOC);

// If task not found, redirect back to tasks.php
if (!$task) {
    header("Location: tasks.php");
    exit();
}

// Handle form submission for updating the task
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $taskName = $_POST['task_name'];
    $description = $_POST['description'];
    $priority = $_POST['priority'];

    // Handle file upload for task image if provided
    if (isset($_FILES['task_image']) && $_FILES['task_image']['error'] === UPLOAD_ERR_OK) {
        $uploadDir = 'uploads/task_images/';
        if (!file_exists($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }
        $imagePath = $uploadDir . basename($_FILES['task_image']['name']);
        move_uploaded_file($_FILES['task_image']['tmp_name'], $imagePath);
    } else {
        $imagePath = $task['image_path'];
    }

    // Update the task in the database
    $updateStmt = $pdo->prepare("UPDATE tasks SET task_name = :task_name, description = :description, priority = :priority, image_path = :image_path WHERE id = :id");
    $updateStmt->bindParam(':task_name', $taskName);
    $updateStmt->bindParam(':description', $description);
    $updateStmt->bindParam(':priority', $priority);
    $updateStmt->bindParam(':image_path', $imagePath);
    $updateStmt->bindParam(':id', $taskId);

    if ($updateStmt->execute()) {
        header("Location: tasks.php?project_id=" . $task['project_id']);
        exit();
    } else {
        echo "Error updating task.";
    }
}

// Get site settings
$settings = getSiteSettings();
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?php echo htmlspecialchars($settings['site_title']); ?> - Edit Task</title>
  <link href="assets/lib/bootstrap/css/bootstrap.min.css" rel="stylesheet">
  <link href="assets/lib/font-awesome/css/font-awesome.css" rel="stylesheet">
  <link href="assets/css/style.css" rel="stylesheet">
</head>

<body>
  <section id="container">
    <?php require 'includes/templates/header.php'; ?>
    <?php require 'includes/templates/navbar.php'; ?>

    <section id="main-content">
      <section class="wrapper">
        <h3>Edit Task</h3>
        <div class="form-panel">
          <form action="edit_task.php?id=<?php echo $taskId; ?>" method="POST" enctype="multipart/form-data" class="form-horizontal style-form">
            <div class="form-group">
              <label class="control-label col-md-3">Task Name</label>
              <div class="col-md-4">
                <input type="text" name="task_name" class="form-control" value="<?php echo htmlspecialchars($task['task_name']); ?>" required>
              </div>
            </div>
            <div class="form-group">
              <label class="control-label col-md-3">Description</label>
              <div class="col-md-4">
                <textarea name="description" class="form-control"><?php echo htmlspecialchars($task['description']); ?></textarea>
              </div>
            </div>
            <div class="form-group">
              <label class="control-label col-md-3">Priority</label>
              <div class="col-md-4">
                <select name="priority" class="form-control">
                  <option value="High" <?php echo $task['priority'] === 'High' ? 'selected' : ''; ?>>High</option>
                  <option value="Medium" <?php echo $task['priority'] === 'Medium' ? 'selected' : ''; ?>>Medium</option>
                  <option value="Low" <?php echo $task['priority'] === 'Low' ? 'selected' : ''; ?>>Low</option>
                </select>
              </div>
            </div>
            <div class="form-group">
              <label class="control-label col-md-3">Task Image (optional)</label>
              <div class="col-md-4">
                <input type="file" name="task_image" class="form-control" accept="image/*">
                <?php if ($task['image_path']): ?>
                  <p>Current Image: <img src="<?php echo htmlspecialchars($task['image_path']); ?>" width="50"></p>
                <?php endif; ?>
              </div>
            </div>
            <div class="form-group">
              <div class="col-md-4 col-md-offset-3">
                <button type="submit" class="btn btn-theme">Update Task</button>
              </div>
            </div>
          </form>
        </div>
      </section>
    </section>

    <footer class="site-footer">
      <div class="text-center">
        <p>&copy; <?php echo htmlspecialchars($settings['site_title']); ?>. All Rights Reserved</p>
      </div>
    </footer>
  </section>
  <script src="assets/lib/jquery/jquery.min.js"></script>
  <script src="assets/lib/bootstrap/js/bootstrap.min.js"></script>
</body>
</html>
