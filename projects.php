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

// Get site settings
$settings = getSiteSettings();

// Fetch all projects
$projectsStmt = $pdo->query("SELECT * FROM task_projects ORDER BY created_at DESC");
$projects = $projectsStmt->fetchAll(PDO::FETCH_ASSOC);

// Handle form submission for adding a new project (AJAX check)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['ajax'])) {
    $projectName = $_POST['name'];
    $projectDescription = $_POST['description'];

    $addProjectStmt = $pdo->prepare("INSERT INTO task_projects (name, description) VALUES (:name, :description)");
    $addProjectStmt->bindParam(':name', $projectName);
    $addProjectStmt->bindParam(':description', $projectDescription);

    if ($addProjectStmt->execute()) {
        $newProjectId = $pdo->lastInsertId();
        echo json_encode(['success' => true, 'id' => $newProjectId, 'name' => $projectName, 'description' => $projectDescription]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Error adding project.']);
    }
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="description" content="">
  <meta name="author" content="Dashboard">
  <meta name="keyword" content="Dashboard, Bootstrap, Admin, Template, Theme, Responsive, Fluid, Retina">
  <title><?php echo htmlspecialchars($settings['site_title']); ?> - Projects</title>

  <!-- Favicons -->
  <link href="../assets/img/favicon.png" rel="icon">
  <link href="../assets/img/apple-touch-icon.png" rel="apple-touch-icon">

  <!-- Bootstrap core CSS -->
  <link href="../assets/lib/bootstrap/css/bootstrap.min.css" rel="stylesheet">
  <!--external css-->
  <link href="../assets/lib/font-awesome/css/font-awesome.css" rel="stylesheet" />
  <link rel="stylesheet" type="text/css" href="../assets/css/zabuto_calendar.css">
  <link rel="stylesheet" type="text/css" href="../assets/lib/gritter/css/jquery.gritter.css" />
  <!-- Custom styles for this template -->
  <link href="../assets/css/style.css" rel="stylesheet">
  <link href="../assets/css/style-responsive.css" rel="stylesheet">
  <script src="../assets/lib/chart-master/Chart.js"></script>
</head>

<body>
  <section id="container">
    <!-- TOP BAR CONTENT & NOTIFICATIONS -->
    <?php require 'includes/templates/header.php'; ?>
    
    <!-- MAIN SIDEBAR MENU -->
    <?php require 'includes/templates/navbar.php'; ?>
    
    <!-- MAIN CONTENT -->
    <section id="main-content">
      <section class="wrapper">
        <br>
        <h1>Projects</h1>
        <br>

        <!-- Button to Open Modal for Adding New Project -->
        <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#addProjectModal">Add New Project</button>
        <br><br>

        <!-- Project List -->
        <div class="content-panel">
          <h4>Existing Projects</h4>
          <table class="table table-bordered" id="projectTable">
            <thead>
              <tr>
                <th>Project Name</th>
                <th>Description</th>
                <th>Created At</th>
                <th>Actions</th>
              </tr>
            </thead>
            <tbody>
              <?php if (empty($projects)): ?>
                <tr><td colspan="4" class="text-center">No projects available.</td></tr>
              <?php else: ?>
                <?php foreach ($projects as $project): ?>
                  <tr id="project-<?php echo $project['id']; ?>">
                    <td><a href="tasks.php?project_id=<?= htmlspecialchars($project['id']); ?>"><?= htmlspecialchars($project['name']); ?></a></td>
                    <td><?= htmlspecialchars($project['description']); ?></td>
                    <td><?= htmlspecialchars($project['created_at']); ?></td>
                    <td>
                      <a href="edit_project.php?id=<?= htmlspecialchars($project['id']); ?>" class="btn btn-warning">Edit</a>
                      <a href="delete_project.php?id=<?= htmlspecialchars($project['id']); ?>" class="btn btn-danger">Delete</a>
                    </td>
                  </tr>
                <?php endforeach; ?>
              <?php endif; ?>
            </tbody>
          </table>
        </div>
      </section>
    </section>

    <!-- Modal for Adding New Project -->
    <div class="modal fade" id="addProjectModal" tabindex="-1" role="dialog" aria-labelledby="addProjectModalLabel" aria-hidden="true">
      <div class="modal-dialog" role="document">
        <div class="modal-content">
          <div class="modal-header bg-primary text-white">
            <h5 class="modal-title" id="addProjectModalLabel">Add New Project</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
              <span aria-hidden="true">&times;</span>
            </button>
          </div>
          <form id="addProjectForm" class="form-horizontal style-form">
            <div class="modal-body">
              <div class="form-group">
                <label class="control-label col-md-3" for="projectName">Project Name</label>
                <div class="col-md-9">
                  <input type="text" name="name" id="projectName" class="form-control" required>
                </div>
              </div>
              <div class="form-group">
                <label class="control-label col-md-3" for="projectDescription">Description</label>
                <div class="col-md-9">
                  <textarea name="description" id="projectDescription" class="form-control" rows="3"></textarea>
                </div>
              </div>
            </div>
            <div class="modal-footer">
              <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
              <button type="submit" class="btn btn-primary">Add Project</button>
            </div>
          </form>
        </div>
      </div>
    </div>

    <!--footer start-->
    <footer class="site-footer">
      <div class="text-center">
        <p>&copy; Copyrights <strong><?php echo htmlspecialchars($settings['site_title']); ?></strong>. All Rights Reserved</p>
        <a href="index.html#" class="go-top">
          <i class="fa fa-angle-up"></i>
        </a>
      </div>
    </footer>
    <!--footer end-->
  </section>

  <!-- Scripts placed at the end of the document so pages load faster -->
  <script src="../assets/lib/jquery/jquery.min.js"></script>
  <script src="../assets/lib/bootstrap/js/bootstrap.min.js"></script>
  <script src="../assets/lib/jquery.dcjqaccordion.2.7.js"></script>
  <script src="../assets/lib/jquery.scrollTo.min.js"></script>
  <script src="../assets/lib/jquery.nicescroll.js" type="text/javascript"></script>
  <script src="../assets/lib/jquery.sparkline.js"></script>
  <script src="../assets/lib/common-scripts.js"></script>
  <script type="text/javascript" src="../assets/lib/gritter/js/jquery.gritter.js"></script>
  <script type="text/javascript" src="../assets/lib/gritter-conf.js"></script>
  <script src="../assets/lib/sparkline-chart.js"></script>
  <script src="../assets/lib/zabuto_calendar.js"></script>

  <!-- AJAX script for handling form submission without page reload -->
  <script>
    $(document).ready(function () {
      $('#addProjectForm').submit(function (e) {
        e.preventDefault();
        $.ajax({
          url: 'projects.php',
          type: 'POST',
          data: $(this).serialize() + '&ajax=1',
          dataType: 'json',
          success: function (response) {
            if (response.success) {
              // Append new project to the table
              $('#projectTable tbody').append(`
                <tr id="project-${response.id}">
                  <td><a href="tasks.php?project_id=${response.id}">${response.name}</a></td>
                  <td>${response.description}</td>
                  <td>${new Date().toISOString().split('T')[0]}</td>
                  <td>
                    <a href="edit_project.php?id=${response.id}" class="btn btn-warning">Edit</a>
                    <a href="delete_project.php?id=${response.id}" class="btn btn-danger">Delete</a>
                  </td>
                </tr>
              `);
              $('#addProjectModal').modal('hide'); // Hide modal on success
              $('#addProjectForm')[0].reset(); // Clear form inputs
            } else {
              alert(response.message);
            }
          },
          error: function () {
            alert('Error adding project.');
          }
        });
      });
    });
  </script>

</body>
</html>
