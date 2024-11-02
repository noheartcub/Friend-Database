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

// Handle form submission for adding a new task
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $taskName = $_POST['task_name'];
    $description = $_POST['description'];
    $priority = $_POST['priority'];
    $projectId = $_GET['project_id']; // Ensure project_id is passed in URL

    // Handle file upload for task image
    $imagePath = null;
    if (isset($_FILES['task_image']) && $_FILES['task_image']['error'] === UPLOAD_ERR_OK) {
        $uploadDir = 'uploads/task_images/';
        if (!file_exists($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }
        $imagePath = $uploadDir . basename($_FILES['task_image']['name']);
        move_uploaded_file($_FILES['task_image']['tmp_name'], $imagePath);
    }

    // Insert the new task into the database
    $stmt = $pdo->prepare("INSERT INTO tasks (project_id, task_name, description, priority, image_path) VALUES (:project_id, :task_name, :description, :priority, :image_path)");
    $stmt->bindParam(':project_id', $projectId);
    $stmt->bindParam(':task_name', $taskName);
    $stmt->bindParam(':description', $description);
    $stmt->bindParam(':priority', $priority);
    $stmt->bindParam(':image_path', $imagePath);

    if ($stmt->execute()) {
        header("Location: tasks.php?project_id=$projectId");
        exit();
    } else {
        echo "Error adding task.";
    }
}

// Fetch tasks for the specific project
$projectId = $_GET['project_id'];
$tasksStmt = $pdo->prepare("SELECT * FROM tasks WHERE project_id = :project_id");
$tasksStmt->bindParam(':project_id', $projectId);
$tasksStmt->execute();
$tasks = $tasksStmt->fetchAll(PDO::FETCH_ASSOC);

?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="description" content="">
  <meta name="author" content="Dashboard">
  <meta name="keyword" content="Dashboard, Bootstrap, Admin, Template, Theme, Responsive, Fluid, Retina">
  <title><?php echo htmlspecialchars($settings['site_title']); ?> - Tasks</title>

  <!-- Favicons -->
  <link href="assets/img/favicon.png" rel="icon">
  <link href="assets/img/apple-touch-icon.png" rel="apple-touch-icon">

  <!-- Bootstrap core CSS -->
  <link href="assets/lib/bootstrap/css/bootstrap.min.css" rel="stylesheet">
  <!--external css-->
  <link href="assets/lib/font-awesome/css/font-awesome.css" rel="stylesheet" />
  <link rel="stylesheet" type="text/css" href="assets/css/zabuto_calendar.css">
  <link rel="stylesheet" type="text/css" href="assets/lib/gritter/css/jquery.gritter.css" />
  <!-- Custom styles for this template -->
  <link href="assets/css/style.css" rel="stylesheet">
  <link href="assets/css/style-responsive.css" rel="stylesheet">
  <script src="assets/lib/chart-master/Chart.js"></script>
</head>

<body>
  <section id="container">
    <!-- **********************************************************************************************************************************************************
        TOP BAR CONTENT & NOTIFICATIONS
        *********************************************************************************************************************************************************** -->
    <!--header start-->
    <?php require 'includes/templates/header.php'; ?>
    <!--header end-->

    <!-- **********************************************************************************************************************************************************
        MAIN SIDEBAR MENU
        *********************************************************************************************************************************************************** -->
    <!--sidebar start-->
    <?php require 'includes/templates/navbar.php'; ?>
    <!--sidebar end-->
    
    <!-- **********************************************************************************************************************************************************
        MAIN CONTENT
        *********************************************************************************************************************************************************** -->
    <section id="main-content">
      <section class="wrapper">
        <h3>Tasks for Project</h3>
        <!-- Go Back Button -->
        <a href="projects.php" class="btn btn-secondary">Go Back to Projects</a>

        <!-- Button to Open Modal for Adding New Task -->
        <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#addTaskModal">Add New Task</button>
        <br><br>

        <!-- Tasks Table -->
        <div class="content-panel">
          <h4>Existing Tasks</h4>
          <table class="table table-bordered">
            <thead>
              <tr>
                <th>Task Name</th>
                <th>Description</th>
                <th>Priority</th>
                <th>Image</th>
                <th>Actions</th>
              </tr>
            </thead>
            <tbody>
              <?php if (empty($tasks)): ?>
                <tr><td colspan="5" class="text-center">No tasks available for this project.</td></tr>
              <?php else: ?>
                <?php foreach ($tasks as $task): ?>
                  <tr>
                    <td><?php echo htmlspecialchars($task['task_name']); ?></td>
                    <td><?php echo htmlspecialchars($task['description']); ?></td>
                    <td><?php echo htmlspecialchars($task['priority']); ?></td>
                    <td>
                      <?php if ($task['image_path']): ?>
                        <img src="<?php echo htmlspecialchars($task['image_path']); ?>" style="width: 50px;">
                      <?php else: ?>
                        No Image
                      <?php endif; ?>
                    </td>
                    <td>
                      <!-- Actions (Edit/Delete) -->
                      <a href="edit_task.php?id=<?php echo $task['id']; ?>" class="btn btn-warning">Edit</a>
                      <a href="delete_task.php?id=<?php echo $task['id']; ?>" class="btn btn-danger">Delete</a>
                    </td>
                  </tr>
                <?php endforeach; ?>
              <?php endif; ?>
            </tbody>
          </table>
        </div>
      </section>
    </section>

    <!-- Modal for Adding New Task -->
    <div class="modal fade" id="addTaskModal" tabindex="-1" role="dialog" aria-labelledby="addTaskModalLabel" aria-hidden="true">
      <div class="modal-dialog" role="document">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title" id="addTaskModalLabel">Add New Task</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
              <span aria-hidden="true">&times;</span>
            </button>
          </div>
          <form action="tasks.php?project_id=<?php echo $projectId; ?>" method="POST" enctype="multipart/form-data">
            <div class="modal-body">
              <div class="form-group">
                <label for="taskName">Task Name</label>
                <input type="text" class="form-control" id="taskName" name="task_name" required>
              </div>
              <div class="form-group">
                <label for="description">Description</label>
                <textarea class="form-control" id="description" name="description"></textarea>
              </div>
              <div class="form-group">
                <label for="priority">Priority</label>
                <select class="form-control" id="priority" name="priority" required>
                  <option value="High">High</option>
                  <option value="Medium">Medium</option>
                  <option value="Low">Low</option>
                </select>
              </div>
              <div class="form-group">
                <label for="taskImage">Task Image (optional)</label>
                <input type="file" class="form-control" id="taskImage" name="task_image" accept="image/*">
              </div>
            </div>
            <div class="modal-footer">
              <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
              <button type="submit" class="btn btn-primary">Add Task</button>
            </div>
          </form>
        </div>
      </div>
    </div>

    <!--footer start-->
    <footer class="site-footer">
      <div class="text-center">
        <p>&copy; Copyrights <strong><?php echo htmlspecialchars($settings['site_title']); ?></strong>. All Rights Reserved</p>
        <a href="index.html#" class="go-top"><i class="fa fa-angle-up"></i></a>
      </div>
    </footer>
    <!--footer end-->
  </section>

  <!-- JS Scripts placed at the end of the document so pages load faster -->
  <script src="assets/lib/jquery/jquery.min.js"></script>
  <script src="assets/lib/bootstrap/js/bootstrap.min.js"></script>
  <script class="include" type="text/javascript" src="assets/lib/jquery.dcjqaccordion.2.7.js"></script>
  <script src="assets/lib/jquery.scrollTo.min.js"></script>
  <script src="assets/lib/jquery.nicescroll.js" type="text/javascript"></script>
  <script src="assets/lib/jquery.sparkline.js"></script>
  <!--common script for all pages-->
  <script src="assets/lib/common-scripts.js"></script>
  <script type="text/javascript" src="assets/lib/gritter/js/jquery.gritter.js"></script>
  <script type="text/javascript" src="assets/lib/gritter-conf.js"></script>
  <script src="assets/lib/sparkline-chart.js"></script>
  <script src="assets/lib/zabuto_calendar.js"></script>

</body>
</html>
