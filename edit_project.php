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

// Fetch the project details from the database
$projectStmt = $pdo->prepare("SELECT * FROM task_projects WHERE id = :id");
$projectStmt->bindParam(':id', $projectId);
$projectStmt->execute();
$project = $projectStmt->fetch(PDO::FETCH_ASSOC);

// If the project is not found, redirect back to projects.php
if (!$project) {
    header("Location: projects.php");
    exit();
}

// Handle form submission for updating the project
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $projectName = $_POST['name'];
    $projectDescription = $_POST['description'];

    // Update the project in the database
    $updateProjectStmt = $pdo->prepare("UPDATE task_projects SET name = :name, description = :description WHERE id = :id");
    $updateProjectStmt->bindParam(':name', $projectName);
    $updateProjectStmt->bindParam(':description', $projectDescription);
    $updateProjectStmt->bindParam(':id', $projectId);

    if ($updateProjectStmt->execute()) {
        header("Location: projects.php");
        exit();
    } else {
        echo "Error updating project.";
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
  <title><?php echo htmlspecialchars($settings['site_title']); ?> - Edit Project</title>
  <link href="../assets/lib/bootstrap/css/bootstrap.min.css" rel="stylesheet">
  <link href="../assets/lib/font-awesome/css/font-awesome.css" rel="stylesheet">
  <link href="../assets/css/style.css" rel="stylesheet">
</head>

<body>
  <section id="container">
    <?php require 'includes/templates/header.php'; ?>
    <?php require 'includes/templates/navbar.php'; ?>

    <section id="main-content">
      <section class="wrapper">
        <h3>Edit Project</h3>
        <div class="form-panel">
          <form action="edit_project.php?id=<?php echo $projectId; ?>" method="POST" class="form-horizontal style-form">
            <div class="form-group">
              <label class="control-label col-md-3">Project Name</label>
              <div class="col-md-4">
                <input type="text" name="name" class="form-control" value="<?php echo htmlspecialchars($project['name']); ?>" required>
              </div>
            </div>
            <div class="form-group">
              <label class="control-label col-md-3">Description</label>
              <div class="col-md-4">
                <textarea name="description" class="form-control" rows="4"><?php echo htmlspecialchars($project['description']); ?></textarea>
              </div>
            </div>
            <div class="form-group">
              <div class="col-md-4 col-md-offset-3">
                <button type="submit" class="btn btn-theme">Update Project</button>
                <a href="projects.php" class="btn btn-default">Cancel</a>
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
  <script src="../assets/lib/jquery/jquery.min.js"></script>
  <script src="../assets/lib/bootstrap/js/bootstrap.min.js"></script>
</body>
</html>
